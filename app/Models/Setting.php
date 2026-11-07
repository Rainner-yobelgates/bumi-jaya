<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class Setting extends Model
{
    protected $guarded = ['id'];

    protected static function booted(): void
    {
        static::saving(function (Setting $setting): void {
            if (! $setting->isDirty('logo') || $setting->logo === null) {
                return;
            }

            $path = $setting->logo;
            if (! is_string($path) || ! preg_match('/\Alogo\/[A-Za-z0-9_-]+\.(png|jpe?g|webp)\z/', $path, $matches)) {
                throw ValidationException::withMessages(['data.logo' => 'Lokasi atau ekstensi logo tidak valid.']);
            }

            $disk = Storage::disk('public');
            if (! $disk->exists($path) || $disk->size($path) > 2048 * 1024) {
                throw ValidationException::withMessages(['data.logo' => 'Logo harus tersedia dan berukuran maksimal 2 MB.']);
            }

            $mimeTypes = [
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
            ];
            $image = @getimagesize($disk->path($path));
            if ($image === false || $image['mime'] !== $mimeTypes[$matches[1]] || $disk->mimeType($path) !== $image['mime']) {
                throw ValidationException::withMessages(['data.logo' => 'Logo harus berupa gambar PNG, JPEG, atau WebP yang valid.']);
            }

            // Keep unique filenames and old files so failed or concurrent saves cannot destroy a logo.
        });
    }
}
