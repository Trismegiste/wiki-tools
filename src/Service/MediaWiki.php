<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * MediaWiki API
 */
class MediaWiki
{

    protected $client;
    protected $host;

    public function __construct(HttpClientInterface $cl, string $h)
    {
        $this->client = $cl;
        $this->host = $h;
    }

    public function searchPageFromCategory(string $cat, int $lim = 20): array
    {
        $response = $this->client->request('GET', $this->host, ['query' => [
                'action' => 'query',
                'format' => 'json',
                'list' => 'categorymembers',
                'cmtitle' => 'Catégorie:' . $cat,
                'cmlimit' => $lim
        ]]);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('API returned ' . $response->getStatusCode() . ' status code');
        }
        $result = json_decode($response->getContent());


        return $result->query->categorymembers;
    }

}
