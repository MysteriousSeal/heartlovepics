<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Models\Artist;
use App\Models\Image;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArtistController extends Controller
{
    public function index(Request $request): View
    {
        $query = Artist::query()->withCount('images');

        if ($search = $request->string('search')->trim()->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }

        $artists = $query->orderBy('name')->paginate(30)->withQueryString();

        return view('admin.artists.index', compact('artists'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:artists,name'],
            'deviantart_url' => ['nullable', 'url', 'max:255'],
            'furaffinity_url' => ['nullable', 'url', 'max:255'],
            'patreon_url' => ['nullable', 'url', 'max:255'],
        ]);

        $artist = Artist::create($validated);

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_ARTIST_CREATED,
            'Added artist "'.$artist->name.'"',
            'artist',
            $artist->id,
            $artist->name,
        );

        return redirect()
            ->route('admin.artists.index')
            ->with('success', 'Artist "'.$artist->name.'" added.');
    }

    public function update(Request $request, Artist $artist): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:artists,name,'.$artist->id],
            'deviantart_url' => ['nullable', 'url', 'max:255'],
            'furaffinity_url' => ['nullable', 'url', 'max:255'],
            'patreon_url' => ['nullable', 'url', 'max:255'],
        ]);

        $oldName = $artist->name;
        $artist->update($validated);

        // Posts credit an artist by name, not a foreign key — renaming here
        // must carry every post's artist_name along, or they silently lose
        // their link back to this record. Look up by the old name explicitly:
        // $artist->images() would query against $artist->name, which by this
        // point already holds the new value and would match nothing.
        if ($oldName !== $artist->name) {
            Image::where('artist_name', $oldName)->update(['artist_name' => $artist->name]);
        }

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_ARTIST_UPDATED,
            'Updated artist "'.$artist->name.'"',
            'artist',
            $artist->id,
            $artist->name,
        );

        return redirect()
            ->route('admin.artists.index')
            ->with('success', 'Artist "'.$artist->name.'" updated.');
    }

    public function destroy(Artist $artist): RedirectResponse
    {
        $name = $artist->name;
        $artist->delete();

        AdminActivityLog::record(
            auth()->user(),
            AdminActivityLog::ACTION_ARTIST_DELETED,
            'Deleted artist "'.$name.'"',
            'artist',
            null,
            $name,
        );

        return redirect()
            ->route('admin.artists.index')
            ->with('success', 'Artist "'.$name.'" deleted. Posts crediting them keep the name, just without links.');
    }
}
