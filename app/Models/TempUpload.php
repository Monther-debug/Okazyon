<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TempUpload extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'type',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('temp_images')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/jpg', 'image/gif']);
    }
}