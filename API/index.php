<?php
namespace API;

require_once 'vendor/autoload.php';

$app = new \Slim\App();

//later on should add group middleware for token authorization
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

$app->run();

?>
