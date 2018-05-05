<?php

namespace Wulkanowy\BitriseRedirector\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Cache\Simple\FilesystemCache;

class BuildsService
{
    private const STATUS_SUCCESS = 1;

    /**
     * @var FilesystemCache
     */
    private $cache;

    public function __construct()
    {
        $this->cache = new FilesystemCache();
    }

    /**
     * @param Client $client
     * @param string $branch
     * @param string $slug
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return array
     */
    public function getLastBuildInfoByBranch(Client $client, string $branch, string $slug): array
    {
        $tag = 'builds.'.$slug.'.'.$branch.'.last';

        if ($this->cache->has($tag)) {
            $response = $this->cache->get($tag);
        } else {
            try {
                $res = $client->get(
                    'apps/'.$slug.'/builds',
                    [
                        'query' => [
                            'branch' => $branch,
                            'limit'  => 1,
                            'status' => self::STATUS_SUCCESS,
                        ],
                    ]
                );
            } catch (ClientException $e) {
                throw new RequestFailedException('App not exist.');
            }

            $response = $res->getBody()->getContents();
            $this->cache->set($tag, $response, 3600);
        }

        $lastBuild = json_decode($response, true)['data'];

        if (empty($lastBuild)) {
            throw new RequestFailedException('Build on branch '.$branch.' not found.');
        }

        return $lastBuild[0];
    }
}
