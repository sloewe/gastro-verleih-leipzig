<?php

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class StoreUploadedImageAsJpeg
{
    /**
     * Resize the uploaded bitmap to fit inside the given bounds and persist as JPEG on $disk.
     *
     * @return non-empty-string Relative path on the disk (e.g. categories/uuid.jpg)
     */
    public function store(
        UploadedFile $file,
        string $disk,
        string $directory,
        int $maxWidth,
        int $maxHeight,
    ): string {
        if (! extension_loaded('gd')) {
            throw new RuntimeException('The GD extension is required to process uploaded images.');
        }

        $maxWidth = max(1, $maxWidth);
        $maxHeight = max(1, $maxHeight);

        $realPath = $file->getRealPath();
        if ($realPath === false) {
            throw new RuntimeException('Could not read the uploaded image path.');
        }

        $binary = file_get_contents($realPath);
        if ($binary === false || $binary === '') {
            throw new RuntimeException('Could not read the uploaded image.');
        }

        /** @var GdImage|false $source */
        $source = @imagecreatefromstring($binary);
        if ($source === false) {
            throw new RuntimeException('Could not decode the uploaded image.');
        }

        try {
            $srcWidth = imagesx($source);
            $srcHeight = imagesy($source);

            $ratio = min($maxWidth / $srcWidth, $maxHeight / $srcHeight, 1.0);
            $newWidth = max(1, (int) round($srcWidth * $ratio));
            $newHeight = max(1, (int) round($srcHeight * $ratio));

            /** @var GdImage $destination */
            $destination = imagecreatetruecolor($newWidth, $newHeight);
            imagealphablending($destination, false);
            $white = imagecolorallocate($destination, 255, 255, 255);
            imagefilledrectangle($destination, 0, 0, $newWidth, $newHeight, $white);
            imagealphablending($destination, true);

            imagecopyresampled(
                $destination,
                $source,
                0,
                0,
                0,
                0,
                $newWidth,
                $newHeight,
                $srcWidth,
                $srcHeight,
            );

            $quality = max(1, min(100, (int) config('images.jpeg_quality', 85)));

            ob_start();
            imagejpeg($destination, null, $quality);
            $jpegBinary = ob_get_clean();
            if ($jpegBinary === false || $jpegBinary === '') {
                throw new RuntimeException('Could not encode the image as JPEG.');
            }

            $directory = trim($directory, '/');
            $relativePath = ($directory !== '' ? $directory.'/' : '').Str::uuid()->toString().'.jpg';

            if (! Storage::disk($disk)->put($relativePath, $jpegBinary)) {
                throw new RuntimeException('Could not store the processed image.');
            }

            return $relativePath;
        } finally {
            imagedestroy($source);
            if (isset($destination) && $destination instanceof GdImage) {
                imagedestroy($destination);
            }
        }
    }
}
