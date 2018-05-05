<?php

namespace Wulkanowy\BitriseRedirector\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Wulkanowy\BitriseRedirector\Service\ArtifactsService;
use Wulkanowy\BitriseRedirector\Service\RequestFailedException;

class ArtifactsController extends Controller
{
    /**
     * @var ArtifactsService
     */
    private $artifacts;

    public function __construct(ArtifactsService $artifacts)
    {
        $this->artifacts = $artifacts;
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
        return $this->json($this->artifacts->getArtifactsListByBranch($branch, $slug));
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
        $res = $this->artifacts->getArtifactInfo($slug, $branch, $artifact);

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
        $info = $this->artifacts->getArtifactInfo($slug, $branch, $artifact);

        return $this->json(
            [
                'build_number'            => $info->build_number,
                'commit_view_url'         => $info->commit_view_url,
                'expiring_download_url'   => $info->expiring_download_url,
                'file_size_bytes'         => $info->file_size_bytes,
                'finished_at'             => $info->finished_at,
                'public_install_page_url' => $info->public_install_page_url,
            ],
            200,
            [
                'Access-Control-Allow-Origin' => $this->container->getParameter('cors_origin'),
            ]
        );
    }
}
