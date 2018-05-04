<?php

namespace Wulkanowy\BitriseRedirector\Service;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class BuildsService
{
    /**
     * @var string Desired type of build
     */
    private $status = 'success';

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
            if ($branch === $value->branch && $this->status === $value->status_text) {
                return $value->slug;
            }
        }

        throw new RequestFailedException('Build on branch '.$branch.
            ' and status '.$this->status.' not exist in last 50 builds.');
    }
}
