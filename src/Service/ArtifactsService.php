<?php

namespace Wulkanowy\BitriseRedirector\Service;

use GuzzleHttp\Client;

class ArtifactsService
{
    /**
     * @var BuildsService
     */
    private $builds;

    public function __construct(BuildsService $builds)
    {
        $this->builds = $builds;
    }

    /**
     * @param Client $client
     * @param string $slug
     * @param string $branch
     *
     * @return array
     * @throws \RuntimeException
     * @throws RequestFailedException
     */
    public function getArtifactsListByBranch(Client $client, string $branch, string $slug) : array
    {
        $lastBuildSlug = $this->builds->getLastBuildSlugByBranch($client, $branch, $slug);

        return json_decode($client->get('apps/'.$slug.'/builds/'.$lastBuildSlug.'/artifacts')
            ->getBody()->getContents())->data;
    }
}
