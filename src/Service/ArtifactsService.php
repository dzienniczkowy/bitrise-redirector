<?php

namespace Wulkanowy\BitriseRedirector\Service;

use GuzzleHttp\Client;
use Symfony\Component\Cache\Simple\FilesystemCache;

class ArtifactsService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var BuildsService
     */
    private $builds;

    /**
     * @var FilesystemCache
     */
    private $cache;

    public function __construct(Client $client, BuildsService $builds)
    {
        $this->client = $client;
        $this->builds = $builds;
        $this->cache = new FilesystemCache();
    }

    /**
     * @param string $slug
     * @param string $branch
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return array
     */
    public function getArtifactsListByBranch(string $branch, string $slug) : array
    {
        $tag = 'artifacts.'.$slug.'.'.$branch;

        $lastBuildSlug = $this->builds->getLastBuildInfoByBranch($branch, $slug)['slug'];

        if ($this->cache->has($tag)) {
            $response = $this->cache->get($tag);
        } else {
            $response = $this->client->get('apps/'.$slug.'/builds/'.$lastBuildSlug.'/artifacts')
                ->getBody()->getContents();
            $this->cache->set($tag, $response, 600);
        }

        return json_decode($response)->data;
    }

    /**
     * @param string    $slug
     * @param string    $branch
     * @param \stdClass $artifact
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return \stdClass
     */
    public function getArtifactInfo(string $slug, string $branch, ?\stdClass $artifact): \stdClass
    {
        $buildInfo = $this->builds->getLastBuildInfoByBranch($branch, $slug);

        if (null === $artifact) {
            throw new RequestFailedException('Artifact not found.', 404);
        }

        $infoTag = 'artifact.'.$branch.'.'.$artifact->title.'.json';

        if ($this->cache->has($infoTag)) {
            $response = $this->cache->get($infoTag);
        } else {
            $response = $this->client->get('apps/'.$slug.'/builds/'.$buildInfo['slug'].'/artifacts/'.$artifact->slug)
                ->getBody()->getContents();
            $this->cache->set($infoTag, $response, 60);
        }

        $info = \json_decode($response)->data;

        $info->build_number = $buildInfo['build_number'];
        $info->commit_view_url = $buildInfo['commit_view_url'];
        $info->finished_at = $buildInfo['finished_at'];

        return $info;
    }

    public function getArtifact(array $artifacts, $key): ?\stdClass
    {
        if (\is_int($key)) {
            return $this->getArtifactByIndex($artifacts, $key);
        }

        return $this->getArtifactByFilename($artifacts, $key);
    }

    public function getArtifactByFilename(array $artifacts, string $filename): ?\stdClass
    {
        foreach ($artifacts as $key => $item) {
            if ($filename === $item->title) {
                return $item;
            }
        }

        return null;
    }

    public function getArtifactByIndex(array $artifacts, int $index): ?\stdClass
    {
        return $artifacts[$index] ?? null;
    }
}
