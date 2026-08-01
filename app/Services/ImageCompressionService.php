<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageCompressionService
{
    public function compress(string $disk, string $path): void
    {
        $storage = Storage::disk($disk);

        if (! $storage->exists($path)) {
            return;
        }

        $fullPath = $storage->path($path);

        $manager = new ImageManager(new Driver());

        $image = $manager->read($fullPath);

        // Optional resize
        if ($image->width() > 2000) {
            $image->scale(width: 2000);
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));

        match ($extension) {
            'jpg', 'jpeg' => $image->toJpeg(75)->save($fullPath),
            'png'         => $image->toPng()->save($fullPath),
            'webp'        => $image->toWebp(75)->save($fullPath),
            default       => null,
        };
    }
}