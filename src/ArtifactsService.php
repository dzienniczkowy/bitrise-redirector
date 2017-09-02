<?php

namespace Wulkanowy\BitriseRedirector;

use GuzzleHttp\Client;

class ArtifactsService
{
    /**
     * @param Client $client
     * @param string $appSlug
     * @param string $buildSLug
     *
     * @throws \RuntimeException
     *
     * @return array
     */
    public function getArtifactsListByBranch(Client $client, string $appSlug, string $buildSLug) : array
    {
        return json_decode(
            $client->get('apps/'.$appSlug.'/builds/'.$buildSLug.'/artifacts')->getBody()->getContents()
        )->data;
    }
}
