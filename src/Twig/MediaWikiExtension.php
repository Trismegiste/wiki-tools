<?php

namespace App\Twig;

use App\Service\MediaWiki;
use Twig\Extension\AbstractExtension;

/**
 * Description of MediaWikiExtension
 */
class MediaWikiExtension extends AbstractExtension
{

    protected $api;

    public function __construct(MediaWiki $api)
    {
        $this->api = $api;
    }

    public function getFunctions()
    {
        return [
            new \Twig\TwigFunction('dump_page', [$this, 'dumpPage']),
            new \Twig\TwigFunction('dump_category', [$this, 'dumpCategory'], ['is_safe' => ['html']])
        ];
    }

    public function dumpCategory(string $category): string
    {
        $page = $this->api->searchPageFromCategory($category, 50);

        $dump = '';
        foreach ($page as $item) {
            $title = $item->title;
            $content = $this->api->getPage($item->pageid);
            $dump .= "<h1>$title</h1>\n";
            $dump .= $content;
            usleep(100000);
        }

        return $dump;
    }

}
