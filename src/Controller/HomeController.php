<?php

namespace Wulkanowy\BitriseRedirector\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
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
        <a href="{$this->generateUrl('branch', ['slug' => 'daeff1893f3c8128'])}">/v0.1/apps/{slug}/builds/{branch}</a></code>
        – redirect to the latest build on a specific branch</li>
    <li><code>GET 
        <a href="{$this->generateUrl('artifacts', ['slug' => 'daeff1893f3c8128', 'branch' => 'master'])}">/v0.1/apps/{slug}/builds/{branch}/artifacts</a></code>
        – json list of build artifacts for the latest build on a specific branch</li>
    <li><code>GET
        <a href="{$this->generateUrl('artifact', ['slug' => 'daeff1893f3c8128', 'branch' => 'master', 'artifact' => 'app-debug-bitrise-signed.apk'])}">/v0.1/apps/{slug}/builds/{branch}/artifacts/{artifact}</a></code>
        – redirect to the download link of a specific build artifact </li>
    <li><code>GET
        <a href="{$this->generateUrl('info', ['slug' => 'daeff1893f3c8128', 'branch' => 'master', 'artifact' => 'app-debug-bitrise-signed.apk'])}">/v0.1/apps/{slug}/builds/{branch}/artifacts/{artifact}/info</a></code>
        – info of last artifact on specific branch </li>
</ul>

HTML
);
    }
}
