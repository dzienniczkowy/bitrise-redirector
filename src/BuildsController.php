<?php

namespace Wulkanowy\BitriseRedirector;

use Silex\Application;
use Symfony\Component\HttpFoundation\RedirectResponse;

class BuildsController
{
    /**
     * @param Application $app
     * @param string $slug
     * @param string $branch
     *
     * @return RedirectResponse
     */
    public function latestAction(Application $app, string $slug, string $branch) : RedirectResponse
    {
        $lastBuildSlug = $app['builds']->getLastBuildSlugByBranch($app['guzzle'], $branch, $slug);

        return $app->redirect('https://www.bitrise.io/build/'.$lastBuildSlug);
    }
}
