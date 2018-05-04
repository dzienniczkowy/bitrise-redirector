<?php

namespace Wulkanowy\BitriseRedirector\Controller;

use GuzzleHttp\Client;
use IvoPetkov\HTML5DOMDocument;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
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

    public function __construct(Client $client, BuildsService $builds, ArtifactsService $artifacts)
    {
        $this->builds = $builds;
        $this->artifacts = $artifacts;
        $this->client = $client;
    }

    /**
     * @param string $slug
     * @param string $branch
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
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
     *
     * @return RedirectResponse
     */
    public function artifactAction(string $slug, string $branch, string $artifact): RedirectResponse
    {
        $res = json_decode($this->getArtifactJson($slug, $branch, $artifact), true)['data'];

        return $this->redirect($res['public_install_page_url'] ?: $res['expiring_download_url']);
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
     *
     * @return JsonResponse
     */
    public function artifactInfoAction(string $slug, string $branch, string $artifact): JsonResponse
    {
        $info = json_decode($this->getArtifactJson($slug, $branch, $artifact), true)['data'];

        /** @var Client $downloadPage */
        $dom = new HTML5DOMDocument();
        $dom->loadHTML($this->client->get($info['public_install_page_url'])->getBody()->getContents());

        return $this->json(
            [
                'latestVersion'     => $dom->querySelectorAll('h1')[2]->innerHTML,
                'latestVersionCode' => $dom->querySelectorAll('.size')[2]->innerHTML,
                'url'               => $info['expiring_download_url'],
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
     *
     * @return string
     */
    public function getArtifactJson(string $slug, string $branch, string $artifact): string
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

        $res = $this->client->get('apps/'.$slug.'/builds/'.$lastBuildSlug.'/artifacts/'.$build->slug);

        return $res->getBody()->getContents();
    }
}
