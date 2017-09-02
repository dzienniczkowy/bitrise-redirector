<?php

namespace Wulkanowy\BitriseRedirector;

use GuzzleHttp\Client;
use Silex\Application;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ArtifactsController
{
    /**
     * @param Application $app
     * @param string      $slug
     * @param string      $branch
     *
     * @throws RequestFailedException
     * @throws \RuntimeException
     *
     * @return JsonResponse
     */
    public function listAction(Application $app, string $slug, string $branch) : JsonResponse
    {
        $lastBuildSlug = $app['builds']->getLastBuildSlugByBranch($app['guzzle'], $branch, $slug);

        return $app->json(
            $app['artifacts']->getArtifactsListByBranch($app['guzzle'], $slug, $lastBuildSlug)
        );
    }

    /**
     * @param Application $app
     * @param string      $slug
     * @param string      $branch
     * @param string      $artifact
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
     *
     * @return RedirectResponse
     */
    public function artifactAction(Application $app, string $slug, string $branch, string $artifact) : RedirectResponse
    {
        /** @var Client $client */
        $client = $app['guzzle'];
        /** @var BuildsService $builds */
        $builds = $app['builds'];
        /** @var ArtifactsService $artifacts */
        $artifacts = $app['artifacts'];

        $lastBuildSlug = $builds->getLastBuildSlugByBranch($client, $branch, $slug);
        $artifactsArray = $artifacts->getArtifactsListByBranch($client, $slug, $lastBuildSlug);

        $build = null;
        foreach ($artifactsArray as $key => $value) {
            if ($artifact === $value->title) {
                $build = $value;
                break;
            }
        }

        if (null === $build) {
            throw new RequestFailedException('Artifact not found.', 404);
        }

        $res = $client->get('apps/'.$slug.'/builds/'.$lastBuildSlug.'/artifacts/'.$build->slug);
        $res = json_decode($res->getBody()->getContents(), true)['data'];

        return $app->redirect($res['public_install_page_url'] ?: $res['expiring_download_url']);
    }
}