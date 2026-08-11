<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteVisit;
use App\Support\DonutChart;
use App\Support\UserAgentParser;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
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
        $activeNow = $this->countActiveVisitors();

        return view('admin.analytics.index', compact('visits', 'charts', 'range', 'ranges', 'activeNow'));
    }

    /**
     * Distinct visitors with a hit in the last 2 minutes.
     * Logged-in users count by user_id; guests by IP address.
     *
     * @return array{total: int, users: int, guests: int}
     */
    private function countActiveVisitors(): array
    {
        $since = Carbon::now()->subMinutes(2);

        $users = (int) SiteVisit::query()
            ->where('created_at', '>=', $since)
            ->whereNotNull('user_id')
            ->selectRaw('count(distinct user_id) as aggregate')
            ->value('aggregate');

        $guests = (int) SiteVisit::query()
            ->where('created_at', '>=', $since)
            ->whereNull('user_id')
            ->whereNotNull('ip_address')
            ->selectRaw('count(distinct ip_address) as aggregate')
            ->value('aggregate');

        return [
            'total' => $users + $guests,
            'users' => $users,
            'guests' => $guests,
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
        $query = SiteVisit::query()->select(['user_id', 'user_agent']);
        $this->applyRange($query, $since);
        $rows = $query->get();

        if ($rows->isEmpty()) {
            return [];
        }

        $authCounts = ['Users' => 0, 'Guests' => 0];
        $osCounts = [];
        $deviceCounts = [];
        $browserCounts = [];
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

            $osCounts[$os] = ($osCounts[$os] ?? 0) + 1;
            $deviceCounts[$device] = ($deviceCounts[$device] ?? 0) + 1;
            $browserCounts[$browser] = ($browserCounts[$browser] ?? 0) + 1;

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
