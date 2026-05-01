<?php

namespace App\Support;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Swis\Filament\Backgrounds\Contracts\ProvidesImages;
use Swis\Filament\Backgrounds\Image;
use Swis\Filament\Backgrounds\ImageProviders\CuratedBySwis;

/**
 * Like swisnl MyImages but never throws when the public directory is missing or empty.
 */
final class SafeFilamentPublicBackground implements ProvidesImages
{
    private function __construct(
        private string $relativeDirectory,
    ) {}

    public static function make(string $relativeDirectory = 'images/background'): self
    {
        return new self($relativeDirectory);
    }

    public function getImage(): Image
    {
        try {
            $dir = public_path($this->relativeDirectory);
            if (! is_dir($dir)) {
                return CuratedBySwis::make()->getImage();
            }

            $files = app(Filesystem::class)->files($dir);
            if ($files === []) {
                return CuratedBySwis::make()->getImage();
            }

            $path = Str::of($files[array_rand($files)]->getPathname())
                ->replaceStart(public_path(), '')
                ->replace(DIRECTORY_SEPARATOR, '/')
                ->toString();

            return new Image('url("'.asset($path).'")');
        } catch (\Throwable) {
            return CuratedBySwis::make()->getImage();
        }
    }
}
