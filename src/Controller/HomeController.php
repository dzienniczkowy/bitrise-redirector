<?php

namespace Wulkanowy\BitriseRedirector\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends AbstractController
{
    /**
     * @throws \InvalidArgumentException
     *
     * @return Response
     */
    public function index(): Response
    {
        return new Response(
            <<<HTML
<!DOCTYPE html>
<title>Bitrise redirector</title>
<h1>Bitrise redirector</h1>
For more info go to
<a href="https://github.com/wulkanowy/bitrise-redirector#bitrise-redirector">github page</a>.

<ul>
    <li><code>GET
        <a href="{$this->generateUrl('branch', ['slug' => 'f841f20d8f8b1dc8'])}">/v0.1/apps/{slug}/builds/{branch}</a></code>
        – redirect to the latest build on a specific branch</li>
    <li><code>GET 
        <a href="{$this->generateUrl('artifacts', ['slug' => 'f841f20d8f8b1dc8', 'branch' => 'master'])}">/v0.1/apps/{slug}/builds/{branch}/artifacts</a></code>
        – json list of build artifacts for the latest build on a specific branch</li>
    <li><code>GET
        <a href="{$this->generateUrl('artifactFilename', ['slug' => 'f841f20d8f8b1dc8', 'branch' => 'master', 'filename' => 'app-debug-bitrise-signed.apk'])}">/v0.1/apps/{slug}/builds/{branch}/artifacts/{filename}</a></code>
        – redirect to the download link of a specific build artifact</li>
    <li><code>GET
        <a href="{$this->generateUrl('artifactIndex', ['slug' => 'f841f20d8f8b1dc8', 'branch' => 'master', 'index' => '0'])}">/v0.1/apps/{slug}/builds/{branch}/artifacts/{index}</a></code>
        – redirect to the download link of a specific build artifact (index-based version)</li>
    <li><code>GET
        <a href="{$this->generateUrl('infoFilename', ['slug' => 'f841f20d8f8b1dc8', 'branch' => 'master', 'filename' => 'app-debug-bitrise-signed.apk'])}">/v0.1/apps/{slug}/builds/{branch}/artifacts/{filename}/info</a></code>
        – info of last artifact on specific branch</li>
    <li><code>GET
        <a href="{$this->generateUrl('infoIndex', ['slug' => 'f841f20d8f8b1dc8', 'branch' => 'master', 'index' => '0'])}">/v0.1/apps/{slug}/builds/{branch}/artifacts/{index}/info</a></code>
        – info of last artifact on specific branch (index-based version)</li>
</ul>

HTML
        );
    }
}
