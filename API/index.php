<?php
namespace API;

ini_set('display_errors',1);
error_reporting(E_ALL);

require_once 'vendor/autoload.php';

$settings =
[
	'settings' =>
	[
		'displayErrorDetails' => true
	],
];

$app = new \Slim\App($settings);

$app->get('/cpus', '\API\RequestHandler\CPUFilter');

//token group?
$app->post('/token/authorize', '\API\RequestHandler\AuthorizedRequest\BasicAuthRequest\AuthorizeToken');
$app->post('/token/refresh', '\API\RequestHandler\AuthorizedRequest\BearerAuthRequest\RefreshToken');
$app->post('/token/validate', '\API\RequestHandler\AuthorizedRequest\BearerAuthRequest\ValidateToken');

$app->get('/build', '\API\RequestHandler\AuthorizedRequest\BearerAuthRequest\JwtAuthRequest\BuildList');

$app->run();

?>
