<?php

namespace Wulkanowy\BitriseRedirector\Tests\Service;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Wulkanowy\BitriseRedirector\Service\ArtifactsService;
use Wulkanowy\BitriseRedirector\Service\BuildsService;

class ArtifactsServiceTest extends TestCase
{
    public function testGetArtifactByFilename(): void
    {
        $artifacts = new ArtifactsService(
            $this->getMockBuilder(Client::class)->getMock(),
            $this->getMockBuilder(BuildsService::class)->disableOriginalConstructor()->getMock()
        );

        $artifact1 = new \stdClass();
        $artifact1->title = 'test-name';

        $array = [$artifact1];

        $this->assertEquals($artifact1, $artifacts->getArtifactByFilename($array, 'test-name'));
        $this->assertEquals(null, $artifacts->getArtifactByFilename($array, 'invalid'));
    }
}
