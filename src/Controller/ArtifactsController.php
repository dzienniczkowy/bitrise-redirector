<?php

namespace Wulkanowy\BitriseRedirector\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Wulkanowy\BitriseRedirector\Service\ArtifactsService;
use Wulkanowy\BitriseRedirector\Service\RequestFailedException;

class ArtifactsController extends AbstractController
{
    /**
     * @var string
     */
    private $corsRule;

    /**
     * @var ArtifactsService
     */
    private $artifacts;

    public function __construct($corsRule, ArtifactsService $artifacts)
    {
        $this->corsRule = $corsRule;
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
     * @param string $filename
     *
     * @throws \RuntimeException
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return RedirectResponse
     */
    public function artifactAction(string $slug, string $branch, string $filename): RedirectResponse
    {
        return $this->getArtifact($slug, $branch, $filename);
    }

    /**
     * @param string $slug
     * @param string $branch
     * @param int $index
     *
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return RedirectResponse
     */
    public function artifactIndexAction(string $slug, string $branch, int $index): RedirectResponse
    {
        return $this->getArtifact($slug, $branch, $index);
    }

    /**
     * @param string $slug
     * @param string $branch
     * @param        $key
     *
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return RedirectResponse
     */
    private function getArtifact(string $slug, string $branch, $key): RedirectResponse
    {
        $artifacts = $this->artifacts->getArtifactsListByBranch($branch, $slug);
        $artifact = $this->artifacts->getArtifact($artifacts, $key);
        $res = $this->artifacts->getArtifactInfo($slug, $branch, $artifact);

        return $this->redirect($res->public_install_page_url ?: $res->expiring_download_url);
    }

    /**
     * @param string $slug
     * @param string $branch
     * @param string $filename
     *
     * @throws \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return JsonResponse
     */
    public function artifactInfoAction(string $slug, string $branch, string $filename): JsonResponse
    {
        return $this->getArtifactsInfoResponse($slug, $branch, $filename);
    }

    /**
     * @param string $slug
     * @param string $branch
     * @param int $index
     *
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return JsonResponse
     */
    public function artifactInfoIndexAction(string $slug, string $branch, int $index): JsonResponse
    {
        return $this->getArtifactsInfoResponse($slug, $branch, $index);
    }

    /**
     * @param string $slug
     * @param string $branch
     * @param        $key
     *
     * @throws RequestFailedException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     *
     * @return JsonResponse
     */
    private function getArtifactsInfoResponse(string $slug, string $branch, $key): JsonResponse
    {
        $artifacts = $this->artifacts->getArtifactsListByBranch($branch, $slug);
        $artifact = $this->artifacts->getArtifact($artifacts, $key);
        $info = $this->artifacts->getArtifactInfo($slug, $branch, $artifact);

        return $this->json(
            [
                'build_number' => $info->build_number,
                'commit_view_url' => $info->commit_view_url,
                'expiring_download_url' => $info->expiring_download_url,
                'file_size_bytes' => $info->file_size_bytes,
                'finished_at' => $info->finished_at,
                'public_install_page_url' => $info->public_install_page_url,
            ],
            200,
            [
                'Access-Control-Allow-Origin' => $this->corsRule,
            ]
        );
    }
}
