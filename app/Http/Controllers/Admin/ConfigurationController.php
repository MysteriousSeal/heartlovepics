<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminActivityLog;
use App\Services\SiteSettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ConfigurationController extends Controller
{
    public function __construct(private SiteSettingsService $settings) {}

    public function edit(): View
    {
        return view('admin.configuration.edit', [
            'showGalleryAuthors' => $this->settings->showGalleryAuthors(),
            'showGalleryArtists' => $this->settings->showGalleryArtists(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'show_gallery_authors' => ['sometimes', 'boolean'],
            'show_gallery_artists' => ['sometimes', 'boolean'],
        ]);

        $previousAuthors = $this->settings->showGalleryAuthors();
        $previousArtists = $this->settings->showGalleryArtists();
        $showGalleryAuthors = (bool) ($validated['show_gallery_authors'] ?? false);
        $showGalleryArtists = (bool) ($validated['show_gallery_artists'] ?? false);

        $this->settings->setShowGalleryAuthors($showGalleryAuthors);
        $this->settings->setShowGalleryArtists($showGalleryArtists);

        if ($previousAuthors !== $showGalleryAuthors || $previousArtists !== $showGalleryArtists) {
            AdminActivityLog::record(
                auth()->user(),
                AdminActivityLog::ACTION_CONFIGURATION_UPDATED,
                'Updated configuration: gallery authors are '.($showGalleryAuthors ? 'shown' : 'hidden').', gallery artists are '.($showGalleryArtists ? 'shown' : 'hidden').'.',
                'configuration',
                null,
                'Gallery credits',
            );
        }

        return redirect()
            ->route('admin.configuration.edit')
            ->with('success', 'Configuration updated.');
    }
}
