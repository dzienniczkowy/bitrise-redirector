<?php

namespace Wulkanowy\BitriseRedirector\Controller;

use GuzzleHttp\Client;
use IvoPetkov\HTML5DOMDocument;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Wulkanowy\BitriseRedirector\Service\ArtifactsService;
use Wulkanowy\BitriseRedirector\Service\BuildsService;
use Wulkanowy\BitriseRedirector\Service\RequestFailedException;

class ArtifactsController extends Controller
{
    /**
     * @var BuildsService
     */
    private $builds;

    /**
     * @var ArtifactsService
     */
    private $artifacts;
    /**
     * @var Client
     */
    private $client;

    /**
     * @var FilesystemCache
     */
    private $cache;

    public function __construct(Client $client, BuildsService $builds, ArtifactsService $artifacts)
    {
        $this->builds = $builds;
        $this->artifacts = $artifacts;
        $this->client = $client;
        $this->cache = new FilesystemCache();
    }

    /**
     * @param string $slug
     * @param string $branch
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return JsonResponse
     */
    public function listAction(string $slug, string $branch): JsonResponse
    {
        return $this->json($this->artifacts->getArtifactsListByBranch($this->client, $branch, $slug));
    }

    /**
     * @param string $slug
     * @param string $branch
     * @param string $artifact
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return RedirectResponse
     */
    public function artifactAction(string $slug, string $branch, string $artifact): RedirectResponse
    {
        $res = $this->getArtifactJson($slug, $branch, $artifact)->data;

        return $this->redirect($res->public_install_page_url ?: $res->expiring_download_url);
    }

    /**
     * @param string $slug
     * @param string $branch
     * @param string $artifact
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return JsonResponse
     */
    public function artifactInfoAction(string $slug, string $branch, string $artifact): JsonResponse
    {
        $info = $this->getArtifactJson($slug, $branch, $artifact)->data;

        $tag = 'artifact.'.$branch.'.'.$artifact.'.html';
        if ($this->cache->has($tag)) {
            $response = $this->cache->get($tag);
        } else {
            $response = $this->client->get($info->public_install_page_url)->getBody()->getContents();
            $this->cache->set($tag, $response, 60);
        }

        /** @var Client $downloadPage */
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($response);

        return $this->json(
            [
                'latestVersion'     => $dom->querySelectorAll('h1')[2]->innerHTML,
                'latestVersionCode' => $dom->querySelectorAll('.size')[2]->innerHTML,
                'url'               => $info->expiring_download_url,
            ],
            200,
            [
                'Access-Control-Allow-Origin' => $this->container->getParameter('cors_origin'),
            ]
        );
    }

    /**
     * @param string $slug
     * @param string $branch
     * @param string $artifact
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return \stdClass
     */
    public function getArtifactJson(string $slug, string $branch, string $artifact): \stdClass
    {
        $lastBuildSlug = $this->builds->getLastBuildSlugByBranch($this->client, $branch, $slug);
        $artifactsArray = $this->artifacts->getArtifactsListByBranch($this->client, $branch, $slug);

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

        $tag = 'artifact.'.$branch.'.'.$artifact.'.json';

        if ($this->cache->has($tag)) {
            $response = $this->cache->get($tag);
        } else {
            $response = $this->client->get('apps/'.$slug.'/builds/'.$lastBuildSlug.'/artifacts/'.$build->slug)
                ->getBody()->getContents();
            $this->cache->set($tag, $response, 3600);
        }

        return \json_decode($response);
    }
}
