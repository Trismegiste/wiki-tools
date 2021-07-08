<?php

namespace App\Tests\Service;

use App\Service\MediaWiki;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class MediaWikiTest extends TestCase
{

    protected $sut;
    protected $network;

    private function createFakeResponse()
    {
        $r = $this->createStub(ResponseInterface::class);
        $r->method('getStatusCode')
                ->willReturn(200);

        return $r;
    }

    protected function setUp(): void
    {
        $this->network = $this->createStub(HttpClientInterface::class);
        $this->sut = new MediaWiki($this->network, 'dummy.com');
    }

    /** @covers MediaWiki::searchPageFromCategory */
    public function testSearchPageFromCategory()
    {
        $resp = $this->createFakeResponse();
        $resp->method('getContent')
                ->willReturn(json_encode(['query' => ['categorymembers' => ['entry']]]));
        $this->network->method('request')
                ->willReturn($resp);

        $this->assertEquals(['entry'], $this->sut->searchPageFromCategory('yolo'));
    }

}
