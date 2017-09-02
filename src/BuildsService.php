<?php

namespace Wulkanowy\BitriseRedirector;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class BuildsService
{
    /**
     * @param Client $client
     * @param string $branch
     * @param string $slug
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
     *
     * @return string
     */
    public function getLastBuildSlugByBranch(Client $client, string $branch, string $slug) : string
    {
        try {
            $response = $client->get('apps/'.$slug.'/builds');
        } catch (ClientException $e) {
            throw new RequestFailedException('App not exist.');
        }

        $response = json_decode($response->getBody()->getContents());

        foreach ($response->data as $key => $value) {
            if ($branch === $value->branch) {
                return $value->slug;
            }
        }

        throw new RequestFailedException('Branch not exist in last 50 builds.');
    }
}
