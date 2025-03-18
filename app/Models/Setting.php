<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $guarded = ['id'];
    protected static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->renameLogoFile();
            if ($model->isDirty('logo')) {
                $model->save();
            }
        });

        static::saving(function ($model) {
            if ($model->isDirty('logo') && $model->exists) {
                $model->renameLogoFile();
            }
        });
    }
    private function renameLogoFile()
    {
        if ($this->isDirty('logo') && $this->logo !== $this->getOriginal('logo')) {
            $extension = pathinfo($this->logo, PATHINFO_EXTENSION);
            $newFileName = 'logo/logo.' . $extension;
        
            if (Storage::disk('public')->exists($this->logo)) {
                $oldLogo = $this->getOriginal('logo'); // Ambil logo lama
                if ($oldLogo !== null && Storage::disk('public')->exists($oldLogo)) {
                    Storage::disk('public')->delete($oldLogo);
                }
                Storage::disk('public')->move($this->logo, $newFileName);
                $this->logo = $newFileName;
                $this->saveQuietly();
            }
        }
        
    }
}
