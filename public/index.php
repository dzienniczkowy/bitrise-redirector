<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\ResponseInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Wulkanowy\BitriseRedirector\RequestFailedException;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = getenv('DEBUG');

$client = new Client(['base_uri' => 'https://api.bitrise.io/v0.1/','headers' => [
    'Authorization' => 'token '.getenv('API_KEY'),
]]);

$last = function(string $slug, string $branch) use ($client) {
    try {
        $response = $client->get('apps/'.$slug.'/builds');
    } catch (ClientException $e) {
        throw new RequestFailedException('App not exist.');
    }

    $response = json_decode($response->getBody()->getContents());

    foreach ($response->data as $key => $value) {
        if ($branch === $value->branch) {
            return $value;
        }
    }

    throw new RequestFailedException('Branch not exist in last 50 builds.');
};

$artifacts = function(string $appSlug, stdClass $last) use ($client) : ResponseInterface {
    return $client->get('apps/'.$appSlug.'/builds/'.$last->slug.'/artifacts');
};

$app->get('/', function() use ($app) {
    return $app->redirect('https://github.com/wulkanowy/bitrise-redirector');
});

/**
 * @var $builds ControllerCollection
 */
$builds = $app['controllers_factory'];

// Redirect to the latest build on a specific branch
$builds->get('/latest', function (string $slug, string $branch) use ($app, $last) {
    return $app->redirect('https://www.bitrise.io/build/'.$last($slug, $branch)->slug);
});

// Get latest build artifacts on a specific branch
$builds->get('/latest/artifacts', function (string $slug, string $branch) use ($app, $last, $artifacts) {
    $response = $artifacts($slug, $last($slug, $branch));
    return $app->json(json_decode($response->getBody()->getContents())->data);
});

// Get specific latest build artifact on a specific branch
$builds->get('/latest/artifacts/{artifact}', function (string $slug, string $branch, string $artifact) use ($app, $last, $artifacts, $client) {
    $lastBuildSlug = $last($slug, $branch);
    $response = $artifacts($slug, $lastBuildSlug);
    $response = json_decode($response->getBody()->getContents());

    foreach ($response->data as $key => $value) {
        if ($artifact === $value->title) {
            $build = $value;
            break;
        }
    }

    if (!isset($build)) {
        throw new RequestFailedException('Artifact not found.', 404);
    }

    $res = $client->get('apps/'.$slug.'/builds/'.$lastBuildSlug->slug.'/artifacts/'.$build->slug);
    $res = json_decode($res->getBody()->getContents(), true)['data'];

    return $app->redirect($res['public_install_page_url'] ?: $res['expiring_download_url']);
});

$app->mount('/api/v0.1/apps/{slug}/{branch}/builds', $builds);

$app->error(function (RequestFailedException $e, Request $request, $code) {
    return new Response($e->getMessage(), 404);
});

$app->run();
