<?php

namespace Wulkanowy\BitriseRedirector\Controller;

use GuzzleHttp\Client;
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
        $res = $this->getArtifactJson($slug, $branch, $artifact);

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
        $infoTag = 'artifact.'.$branch.'.'.$artifact.'.json';

        if ($this->cache->has($infoTag)) {
            $info = $this->cache->get($infoTag);
        } else {
            $info = $this->getArtifactJson($slug, $branch, $artifact);
            $this->cache->set($infoTag, $info, 60);
        }

        return $this->json(
            [
                'build_number'            => $info->build_number,
                'commit_view_url'         => $info->commit_view_url,
                'expiring_download_url'   => $info->expiring_download_url,
                'file_size_bytes'         => $info->file_size_bytes,
                'finished_at'             => $info->finished_at,
                'public_install_page_url' => $info->public_install_page_url,
                'latestVersionCode'       => $info->build_number, // depracated
                'url'                     => $info->expiring_download_url, // deprecated
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
        $buildInfo = $this->builds->getLastBuildInfoByBranch($this->client, $branch, $slug);
        $artifacts = $this->artifacts->getArtifactsListByBranch($this->client, $branch, $slug);
        $build = $this->artifacts->getArtifactByFilename($artifacts, $artifact);

        if (null === $build) {
            throw new RequestFailedException('Artifact not found.', 404);
        }

        $response = $this->client->get('apps/'.$slug.'/builds/'.$buildInfo['slug'].'/artifacts/'.$build->slug)
            ->getBody()->getContents();

        $info = \json_decode($response)->data;

        $info->build_number = $buildInfo['build_number'];
        $info->commit_view_url = $buildInfo['commit_view_url'];
        $info->finished_at = $buildInfo['finished_at'];

        return $info;
    }
}
