<?php

namespace Wulkanowy\BitriseRedirector\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Wulkanowy\BitriseRedirector\Service\BuildsService;

class BuildsController extends Controller
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @param BuildsService $builds
     * @param string        $slug
     * @param string        $branch
     *
     * @return RedirectResponse
     * @throws \RuntimeException
     * @throws \Wulkanowy\BitriseRedirector\Service\RequestFailedException
     */
    public function latestAction(BuildsService $builds, string $slug, string $branch) : RedirectResponse
    {
        $lastBuildSlug = $builds->getLastBuildSlugByBranch($this->client, $branch, $slug);

        return $this->redirect('https://www.bitrise.io/build/'.$lastBuildSlug);
    }
}
