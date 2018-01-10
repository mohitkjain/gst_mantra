<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';

date_default_timezone_set('Asia/Kolkata');
error_reporting(-1);

$app = new \Slim\App([
    'settings' => [
        // Slim Settings
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => true,
        'addContentLengthHeader' => false,
    ]
]);

//GST Mantra
require_once '../api/gst_mantra/get_chapters.php';
require_once '../api/gst_mantra/get_chapter_details.php';
require_once '../api/gst_mantra/get_service_details.php';

//Wallpapers
require_once '../api/wallpapers/get_category.php';
require_once '../api/wallpapers/get_images_by_category.php';
require_once '../api/wallpapers/increase_decrease_vote.php';
require_once '../api/wallpapers/get_featured_images.php';

$app->get('/hello/{name}/{startAt}', function (Request $request, Response $response) {
    $name = $request->getAttribute('name');
    $startAt = $request->getAttribute('startAt');
    $response->getBody()->write("Hello, $name  Hi $startAt");

    return $response;
});
$app->run();