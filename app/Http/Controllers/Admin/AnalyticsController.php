<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteVisit;
use App\Support\DonutChart;
use App\Support\UserAgentParser;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /** @var array<string, string> */
    private const RANGES = [
        'all' => 'All time',
        '30d' => 'Last 30 days',
        '7d' => 'Last 7 days',
        '24h' => 'Last 24 hours',
    ];

    public function index(Request $request): View
    {
        $range = $this->resolveRange($request->input('range'));
        $since = $this->sinceForRange($range);

        $visitsQuery = SiteVisit::query()->with('user')->latest('created_at');
        $this->applyRange($visitsQuery, $since);

        $visits = $visitsQuery->paginate(50)->withQueryString();
        $charts = $this->buildCharts($since);
        $ranges = self::RANGES;
        $activeNow = $this->countDistinctVisitors(Carbon::now()->subMinutes(2));
        $rangeVisitors = $this->countDistinctVisitors($since);

        return view('admin.analytics.index', compact(
            'visits',
            'charts',
            'range',
            'ranges',
            'activeNow',
            'rangeVisitors',
        ));
    }

    /**
     * Polled from the admin nav every few seconds to keep the "active now"
     * chip live without a full page reload.
     */
    public function activeNow(): JsonResponse
    {
        return response()->json($this->countDistinctVisitors(Carbon::now()->subMinutes(2)));
    }

    /**
     * Distinct visitors since $since (or all time when null).
     * Bots are counted separately from human guests via user-agent detection.
     *
     * @return array{total: int, users: int, guests: int, bots: int}
     */
    private function countDistinctVisitors(?CarbonInterface $since): array
    {
        $query = SiteVisit::query()->select(['user_id', 'ip_address', 'user_agent']);
        $this->applyRange($query, $since);

        $userKeys = [];
        $guestKeys = [];
        $botKeys = [];

        foreach ($query->cursor() as $row) {
            $isBot = UserAgentParser::isBot($row->user_agent);
            $ip = $row->ip_address ?: 'unknown';

            if ($isBot) {
                $botKeys['b:'.$ip.'|'.md5((string) $row->user_agent)] = true;

                continue;
            }

            if ($row->user_id) {
                $userKeys['u:'.$row->user_id] = true;
            } else {
                $guestKeys['g:'.$ip] = true;
            }
        }

        $users = count($userKeys);
        $guests = count($guestKeys);
        $bots = count($botKeys);

        return [
            'total' => $users + $guests + $bots,
            'users' => $users,
            'guests' => $guests,
            'bots' => $bots,
        ];
    }

    private function resolveRange(?string $range): string
    {
        return array_key_exists((string) $range, self::RANGES) ? (string) $range : 'all';
    }

    private function sinceForRange(string $range): ?CarbonInterface
    {
        return match ($range) {
            '24h' => Carbon::now()->subDay(),
            '7d' => Carbon::now()->subDays(7),
            '30d' => Carbon::now()->subDays(30),
            default => null,
        };
    }

    /**
     * @param  Builder<SiteVisit>  $query
     */
    private function applyRange(Builder $query, ?CarbonInterface $since): void
    {
        if ($since !== null) {
            $query->where('created_at', '>=', $since);
        }
    }

    /**
     * Aggregate visits for donut charts within the selected range.
     *
     * @return array<string, array{title: string, total: int, gradient: string, slices: list<array{label: string, count: int, percent: float, color: string}>}>
     */
    private function buildCharts(?CarbonInterface $since): array
    {
        $query = SiteVisit::query()->select(['user_id', 'user_agent', 'country']);
        $this->applyRange($query, $since);
        $rows = $query->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $authCounts = ['Users' => 0, 'Guests' => 0];
        $osCounts = [];
        $deviceCounts = [];
        $browserCounts = [];
        $countryCounts = [];
        $botCounts = ['Human' => 0, 'Bot' => 0];

        foreach ($rows as $row) {
            if ($row->user_id) {
                $authCounts['Users']++;
            } else {
                $authCounts['Guests']++;
            }

            $parsed = UserAgentParser::parse($row->user_agent);

            $os = $parsed['os'] === '—' ? 'Unknown' : $parsed['os'];
            $device = $parsed['device'];
            $browser = $parsed['browser'];
            $country = filled($row->country) ? (string) $row->country : 'Unknown';

            $osCounts[$os] = ($osCounts[$os] ?? 0) + 1;
            $deviceCounts[$device] = ($deviceCounts[$device] ?? 0) + 1;
            $browserCounts[$browser] = ($browserCounts[$browser] ?? 0) + 1;
            $countryCounts[$country] = ($countryCounts[$country] ?? 0) + 1;

            if ($parsed['is_bot']) {
                $botCounts['Bot']++;
            } else {
                $botCounts['Human']++;
            }
        }

        return [
            'auth' => array_merge(
                ['title' => 'Users vs guests'],
                DonutChart::fromCounts($authCounts, 2),
            ),
            'country' => array_merge(
                ['title' => 'Country'],
                DonutChart::fromCounts($countryCounts),
            ),
            'os' => array_merge(
                ['title' => 'Operating system'],
                DonutChart::fromCounts($osCounts),
            ),
            'device' => array_merge(
                ['title' => 'Device'],
                DonutChart::fromCounts($deviceCounts),
            ),
            'browser' => array_merge(
                ['title' => 'Browser'],
                DonutChart::fromCounts($browserCounts),
            ),
            'bot' => array_merge(
                ['title' => 'Bot vs human'],
                DonutChart::fromCounts($botCounts, 2),
            ),
        ];
    }
}
