<?php

namespace Wulkanowy\BitriseRedirector\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Wulkanowy\BitriseRedirector\Service\BuildsService;

class BuildsController extends Controller
{
    /**
     * @param BuildsService $builds
     * @param string        $slug
     * @param string        $branch
     *
     * @throws \RuntimeException
     * @throws \Wulkanowy\BitriseRedirector\Service\RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return RedirectResponse
     */
    public function latestAction(BuildsService $builds, string $slug, string $branch) : RedirectResponse
    {
        $lastBuildSlug = $builds->getLastBuildInfoByBranch($branch, $slug)['slug'];

        return $this->redirect('https://www.bitrise.io/build/'.$lastBuildSlug);
    }
}
