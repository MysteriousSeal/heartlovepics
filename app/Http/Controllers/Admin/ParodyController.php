<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Image;
use App\Models\Parody;
use App\Services\AvatarService;
use App\Support\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ParodyController extends Controller
{
    public function __construct(private AvatarService $avatars) {}

    public function index(Request $request): View
    {
        $query = Parody::query()->withCount('images');

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }

        $parodies = $query->orderBy('name')->paginate(30)->withQueryString();

        $pendingParodies = $this->pendingParodies();

        return view('admin.parodies.index', compact('parodies', 'pendingParodies'));
    }

    /**
     * Distinct parody names credited on posts that don't have a matching
     * Parody record yet — free text a post author typed in that no admin
     * has reviewed and turned into a real profile.
     *
     * @return \Illuminate\Support\Collection<int, array{name: string, count: int}>
     */
    private function pendingParodies(): \Illuminate\Support\Collection
    {
        $validatedNames = Parody::query()->pluck('name');

        return Image::query()
            ->whereNotNull('parody')
            ->where('parody', '!=', '')
            ->whereNotIn('parody', $validatedNames)
            ->selectRaw('parody, count(*) as posts_count')
            ->groupBy('parody')
            ->orderBy('parody')
            ->get()
            ->map(fn ($row) => ['name' => $row->parody, 'count' => $row->posts_count]);
    }

    public function edit(Parody $parody): View
    {
        return view('admin.parodies.edit', compact('parody'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:parodies,name'],
            'cover' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'description' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('cover')) {
            $validated['cover_path'] = $this->avatars->store($request->file('cover'));
        }
        unset($validated['cover']);

        $validated['description'] = HtmlSanitizer::clean($validated['description'] ?? null);

        $parody = Parody::create($validated);

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_PARODY_CREATED,
            'Added parody "'.$parody->name.'"',
            'parody',
            $parody->id,
            $parody->name,
        );

        return redirect()
            ->route('admin.parodies.index')
            ->with('success', 'Parody "'.$parody->name.'" added.');
    }

    public function update(Request $request, Parody $parody): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:parodies,name,'.$parody->id],
            'cover' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif,webp', 'max:5120'],
            'remove_cover' => ['sometimes', 'boolean'],
            'description' => ['nullable', 'string'],
        ]);

        if ($request->hasFile('cover')) {
            $this->avatars->delete($parody->cover_path);
            $validated['cover_path'] = $this->avatars->store($request->file('cover'));
        } elseif ($request->boolean('remove_cover')) {
            $this->avatars->delete($parody->cover_path);
            $validated['cover_path'] = null;
        }
        unset($validated['cover'], $validated['remove_cover']);

        $validated['description'] = HtmlSanitizer::clean($validated['description'] ?? null);

        $oldName = $parody->name;
        $parody->update($validated);

        // Posts credit a parody by name, not a foreign key — renaming here
        // must carry every post's parody along, or they silently lose their
        // link back to this record. Look up by the old name explicitly:
        // $parody->images() would query against $parody->name, which by this
        // point already holds the new value and would match nothing.
        if ($oldName !== $parody->name) {
            Image::where('parody', $oldName)->update(['parody' => $parody->name]);
        }

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_PARODY_UPDATED,
            'Updated parody "'.$parody->name.'"',
            'parody',
            $parody->id,
            $parody->name,
        );

        return redirect()
            ->route('admin.parodies.index')
            ->with('success', 'Parody "'.$parody->name.'" updated.');
    }

    public function destroy(Parody $parody): RedirectResponse
    {
        $name = $parody->name;
        $this->avatars->delete($parody->cover_path);
        $parody->delete();

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_PARODY_DELETED,
            'Deleted parody "'.$name.'"',
            'parody',
            null,
            $name,
        );

        return redirect()
            ->route('admin.parodies.index')
            ->with('success', 'Parody "'.$name.'" deleted. Posts crediting it keep the name, just without the cover or description.');
    }
}
