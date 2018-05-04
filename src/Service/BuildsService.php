<?php

namespace Wulkanowy\BitriseRedirector\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Cache\Simple\FilesystemCache;

class BuildsService
{
    /**
     * @var string Desired type of build
     */
    private $status = 'success';

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
     * @return string
     */
    public function getLastBuildSlugByBranch(Client $client, string $branch, string $slug) : string
    {
        $tag = 'builds.'.$branch.'.'.$slug.'.last';

        if ($this->cache->has($tag)) {
            $response = $this->cache->get($tag);
        } else {
            try {
                $res = $client->get('apps/'.$slug.'/builds');
            } catch (ClientException $e) {
                throw new RequestFailedException('App not exist.');
            }

            $response = $res->getBody()->getContents();
            $this->cache->set($tag, $response, 3600);
        }

        $json = json_decode($response);

        foreach ($json->data as $key => $value) {
            if ($branch === $value->branch && $this->status === $value->status_text) {
                return $value->slug;
            }
        }

        throw new RequestFailedException('Build on branch '.$branch.
            ' and status '.$this->status.' not exist in last 50 builds.');
    }
}
