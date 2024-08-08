<?php

namespace App\Services\SiteMap;

use Spatie\Sitemap\Tags\Url as BaseUrl;

class Url extends BaseUrl
{
    public string $changeFrequency = '';
    public float $priority = 0;

    public function __construct(string $url)
    {
        $this->url = $url;
    }
}
