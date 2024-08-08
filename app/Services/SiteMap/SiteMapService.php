<?php

namespace App\Services\SiteMap;

use App\Models\Blog\Blog;
use App\Models\Enterprise\Enterprise;
use App\Models\Stock\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\Tags\Url;

class SiteMapService
{
    private int $chunkSize = 40000;
    private int $divideSize = 40000;

    public function generate(): void
    {
        $stock = new Stock();
        $blog = new Blog();
        $enterprise = new Enterprise();

        $links = [];
        SitemapGenerator::create(config('constant.sitemap'))
            ->getSitemap()
            ->add($this->getUrl('/'))
            ->add($this->getUrl('/ai-stock-image/index'))
            ->add($this->getUrl('/ai-stock-image/search'))
            ->add($this->getUrl('/notice/faq'))
            ->add($this->getUrl('/looking-for-ai-images-for-project'))
            ->add($this->getUrl('/need-images-without-legal-or-license-issues'))
            ->add($this->getUrl('/blog'))
            ->add($this->getUrl('/enterprise '))
            ->writeToDisk('sitemap', 'sitemap-main.xml');
        $links = [...$links, $this->convertLink('sitemap-main.xml')];
        $links = [
            ...$links,
            ...$this->divideSiteMap($blog->without($blog->getWith()), 'sitemap-blog'),
            ...$this->divideSiteMap($enterprise->without($enterprise->getWith()), 'sitemap-enterprise'),
            ...$this->divideSiteMap($stock->without($stock->getWith())
                ->with(['images'])
                ->enabled(), 'sitemap-stock'),
        ];

        $output = View::make('sitemap')->with(['sitemaps' => $links])->render();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . $output;
        Storage::disk('sitemap')->put('sitemap.xml', $xml);
    }

    protected function getUrl(
        string $url,
               $priority = 0.5,
               $changeFrequency = Url::CHANGE_FREQUENCY_WEEKLY,
               $lastModificationDate = null,
    )
    {
        return Url::create(config('constant.sitemap') . $url)
            ->setLastModificationDate($lastModificationDate ?? now())
            ->setChangeFrequency($changeFrequency)
            ->setPriority($priority);
    }

    protected function convertLink(string $sitemap): string
    {
        return config('constant.sitemap') . '/' . $sitemap;
    }

    protected function divideSiteMap(Builder $query, string $fileName): array
    {
        $idx = 1;
        $count = 0;
        $links = [];

        $initGenerator = function () use (&$count) {
            $count = 0;
            return SitemapGenerator::create(config('constant.sitemap'))
                ->getSitemap();
        };

        $createSitemapName = function ($fileName, $idx) {
            return $fileName . "-$idx.xml";
        };

        $generator = $initGenerator();

        $query->chunk($this->chunkSize, function ($items) use (
            &$count,
            &$generator,
            &$idx,
            &$links,
            $fileName,
            $createSitemapName,
            $initGenerator
        ) {
            if ($count >= $this->divideSize) {
                $generator->writeToDisk('sitemap', $createSitemapName($fileName, $idx));
                $links[] = $this->convertLink($createSitemapName($fileName, $idx));
                $generator = $initGenerator();
                $idx++;
            } else {
                $generator->add($items);
                $count += $this->chunkSize;
            }
        });

        if ($count > 0) {
            $generator->writeToDisk('sitemap', $createSitemapName($fileName, $idx));
            $links[] = $this->convertLink($createSitemapName($fileName, $idx));
        }

        return $links;
    }

}
