<?php

namespace App\Support;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Storage;

/**
 * Resolves receipt paths on the private disk, with fallback to the legacy public disk if present.
 */
final class TransactionReceiptFilesystem
{
    /** Relative path segments stored on the private (local) disk. */
    public const PRIVATE_DIRECTORY = 'receipts';

    public static function privateDisk(): Filesystem
    {
        return Storage::disk('local');
    }

    public static function legacyPublicDisk(): Filesystem
    {
        return Storage::disk('public');
    }

    /**
     * @return array{disk: Filesystem, path: string}|null
     */
    public static function resolve(?string $relativePath): ?array
    {
        if ($relativePath === null || $relativePath === '') {
            return null;
        }

        $normalized = ltrim($relativePath, '/');

        // New default: receipts/… under storage/app/private (local disk root)
        if (self::privateDisk()->exists($normalized)) {
            return ['disk' => self::privateDisk(), 'path' => $normalized];
        }

        // Preferred private layout if only filename was persisted
        if (! str_contains($normalized, '/') && self::privateDisk()->exists(self::PRIVATE_DIRECTORY.'/'.$normalized)) {
            return [
                'disk' => self::privateDisk(),
                'path' => self::PRIVATE_DIRECTORY.'/'.$normalized,
            ];
        }

        // Legacy: storage/app/public/… served via symlink
        if (self::legacyPublicDisk()->exists($normalized)) {
            return ['disk' => self::legacyPublicDisk(), 'path' => $normalized];
        }

        return null;
    }

    public static function absolutePathForLocalProcessing(?string $relativePath): ?string
    {
        $resolved = self::resolve($relativePath);
        if ($resolved === null) {
            return null;
        }

        return $resolved['disk']->path($resolved['path']);
    }
}
