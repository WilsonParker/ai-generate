<?php

namespace App\Services\Image\Models;

use App\Services\Image\Contracts\ImageModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model implements ImageModel
{
    protected $fillable = [
        'path',
        'name',
        'origin_name',
        'size',
        'mime',
        'user_id',
        'imageable_type',
        'imageable_id',
    ];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getLink(): string
    {
        $url = Storage::disk($this->disk)->url($this->path . '/' . $this->name);
        $cdn = config('filesystems.disks.s3.cdn');
        if (isset($cdn)) {
            $bucket = config('filesystems.disks.s3.bucket');
            $region = config('filesystems.disks.s3.region');
            return str_replace(
                'https://' . $bucket . '.s3.' . $region . '.amazonaws.com',
                $cdn,
                $url
            );
        } else {
            return $url;
        }
    }

}
