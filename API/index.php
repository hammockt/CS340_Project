<?php
namespace API;

//ini_set("log_errors", 1);
//ini_set('error_log','demo_errors.log');
//ini_set('display_errors',1);
//error_reporting(E_ALL);

require_once 'vendor/autoload.php';

$app = new \Slim\App();

//middleware to automatically handle generic headers
$app->add(function($request, $response, $next)
{
	$method = $request->getMethod();

	$response = $response->withAddedHeader('Access-Control-Allow-Origin', '*')
	                     ->withAddedHeader('Access-Control-Allow-Methods', $request->getMethod());

	if($method !== 'GET')
	{
		$supportedMedia = ['application/json', 'application/x-www-form-urlencoded', 'application/xml'];
		$response = $response->withAddedHeader('Accept', implode(', ', $supportedMedia));

		if(!$request->hasHeader('Content-Type'))
		{
			return $response->write('Content-Type is required')
			                ->withStatus(403);
		}

		if(!in_array($request->getContentType(), $supportedMedia))
		{
			return $response->withStatus(415);
		}
			
	}

	$response = $next($request, $response);

	return $response;
});

$app->get('/cpus', '\API\CPUFilter');
$app->post('/token', '\API\TokenHandler');
$app->get('/build', '\API\BuildHandler');
//build/{buildID} gets all of the parts in a build
//build/{buildID}/cpus gets all of the cpu parts

$app->run();

?>
