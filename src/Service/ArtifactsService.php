<?php

namespace Wulkanowy\BitriseRedirector\Service;

use GuzzleHttp\Client;
use Symfony\Component\Cache\Simple\FilesystemCache;

class ArtifactsService
{
    /**
     * @var BuildsService
     */
    private $builds;

    /**
     * @var FilesystemCache
     */
    private $cache;

    public function __construct(BuildsService $builds)
    {
        $this->builds = $builds;
        $this->cache = new FilesystemCache();
    }

    /**
     * @param Client $client
     * @param string $slug
     * @param string $branch
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return array
     */
    public function getArtifactsListByBranch(Client $client, string $branch, string $slug) : array
    {
        $tag = 'artifacts.'.$slug.'.'.$branch;

        $lastBuildSlug = $this->builds->getLastBuildInfoByBranch($client, $branch, $slug)['slug'];

        if ($this->cache->has($tag)) {
            $response = $this->cache->get($tag);
        } else {
            $response = $client->get('apps/'.$slug.'/builds/'.$lastBuildSlug.'/artifacts')
                ->getBody()->getContents();
            $this->cache->set($tag, $response, 3600);
        }

        return json_decode($response)->data;
    }

    public function getArtifactByFilename(array $artifacts, string $filename): ?\stdClass {
        foreach ($artifacts as $key => $item) {
            if ($filename === $item->title) {
                return $item;
            }
        }

        return null;
    }
}
