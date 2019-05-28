<?php

namespace Wulkanowy\BitriseRedirector\Controller;

use Psr\SimpleCache\InvalidArgumentException;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Wulkanowy\BitriseRedirector\Service\BuildsService;
use Wulkanowy\BitriseRedirector\Service\RequestFailedException;

class BuildsController extends AbstractController
{
    /**
     * @param BuildsService $builds
     * @param string        $slug
     * @param string        $branch
     *
     * @throws RuntimeException
     * @throws RequestFailedException
     * @throws InvalidArgumentException
     *
     * @return RedirectResponse
     */
    public function latestAction(BuildsService $builds, string $slug, string $branch): RedirectResponse
    {
        $lastBuildSlug = $builds->getLastBuildInfoByBranch($branch, $slug)['slug'];

        return $this->redirect('https://www.bitrise.io/build/'.$lastBuildSlug);
    }
}
