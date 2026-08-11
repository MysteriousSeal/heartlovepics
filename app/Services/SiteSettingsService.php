<?php

namespace App\Services;

use App\Models\SiteSetting;

class SiteSettingsService
{
    public function showGalleryAuthors(): bool
    {
        return $this->boolean(SiteSetting::SHOW_GALLERY_AUTHORS, true);
    }

    public function setShowGalleryAuthors(bool $show): void
    {
        $this->set(SiteSetting::SHOW_GALLERY_AUTHORS, $show ? '1' : '0');
    }

    public function showGalleryArtists(): bool
    {
        return $this->boolean(SiteSetting::SHOW_GALLERY_ARTISTS, true);
    }

    public function setShowGalleryArtists(bool $show): void
    {
        $this->set(SiteSetting::SHOW_GALLERY_ARTISTS, $show ? '1' : '0');
    }

    private function boolean(string $key, bool $default): bool
    {
        $value = SiteSetting::query()->where('key', $key)->value('value');

        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    private function set(string $key, string $value): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );
    }
}
