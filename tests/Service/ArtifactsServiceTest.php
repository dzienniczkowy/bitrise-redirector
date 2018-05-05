<?php

namespace Wulkanowy\BitriseRedirector\Tests\Service;

use PHPUnit\Framework\TestCase;
use Wulkanowy\BitriseRedirector\Service\ArtifactsService;
use Wulkanowy\BitriseRedirector\Service\BuildsService;

class ArtifactsServiceTest extends TestCase
{
    public function testGetArtifactByFilename(): void
    {
        $artifacts = new ArtifactsService($this->getMockBuilder(BuildsService::class)->getMock());

        $artifact1 = new \stdClass();
        $artifact1->title = 'test-name';

        $array = [$artifact1];

        $this->assertEquals($artifact1, $artifacts->getArtifactByFilename($array, 'test-name'));
        $this->assertEquals(null, $artifacts->getArtifactByFilename($array, 'invalid'));
    }
}
