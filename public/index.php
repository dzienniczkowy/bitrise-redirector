<?php

use Silex\ControllerCollection;
use SilexGuzzle\GuzzleServiceProvider;
use Symfony\Component\HttpFoundation\Response;
use Wulkanowy\BitriseRedirector\ArtifactsService;
use Wulkanowy\BitriseRedirector\BuildsService;
use Wulkanowy\BitriseRedirector\RequestFailedException;

require_once __DIR__.'/../vendor/autoload.php';

$app = new Silex\Application();
$app['debug'] = getenv('DEBUG');

$app->register(new GuzzleServiceProvider(), [
    'guzzle.base_uri'        => 'https://api.bitrise.io/v0.1/',
    'guzzle.request_options' => [
        'headers' => [
            'Authorization' => 'token '.getenv('API_KEY'),
        ],
    ],
]);

$app['builds'] = function () {
    return new BuildsService();
};
$app['artifacts'] = function () {
    return new ArtifactsService();
};

$app->get('/', function () {
    return new Response(<<<'HTML'
<!DOCTYPE html>
<title>Bitrise redirector</title>
<h1>Bitrise redirector</h1>
For more info go to
<a href="https://github.com/wulkanowy/bitrise-redirector#bitrise-redirector">github page</a>.
HTML
);
});

/**
 * @var ControllerCollection
 */
$builds = $app['controllers_factory'];

// Redirect to the latest build on a specific branch
$builds->get('/{branch}', 'Wulkanowy\\BitriseRedirector\\BuildsController::latestAction');

// Get latest build artifacts on a specific branch
$builds->get('/{branch}/artifacts', 'Wulkanowy\\BitriseRedirector\\ArtifactsController::listAction');

// Redirect to specific latest build artifact on a specific branch
$builds->get('/{branch}/artifacts/{artifact}', 'Wulkanowy\\BitriseRedirector\\ArtifactsController::artifactAction');

$app->mount('/v0.1/apps/{slug}/builds', $builds);

$app->error(function (RequestFailedException $e) {
    return new Response($e->getMessage(), 404);
});

$app->run();
