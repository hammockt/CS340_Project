<?php
namespace API;

require_once 'vendor/autoload.php';

use \GuzzleHttp\Client;

abstract class RestrictedRequestHandler extends RequestHandler
{
	public function __invoke($request, $response, $args)
	{
		$authHeader = explode(' ', $request->getHeader('Authorization')[0]);

		if(count($authHeader) !== 2 || $authHeader[0] !== 'Bearer')
		{
			return $response->write('Bearer authorization is required')
				            ->withStatus(403);
		}

		$client = new Client(['base_uri' => 'https://web.engr.oregonstate.edu/~hammockt/cs340/Project/Dev/API/']);

		$res = $client->request('POST', 'token',
		[
			'form_params' =>
			[
				'grantType' => 'validate'
			],
			'headers' =>
			[
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Authorization' => $request->getHeader('Authorization')[0]
			],
			'http_errors' => false
		]);

		if($res->getStatusCode() < 200 || $res->getStatusCode() >= 300)
			return $response->withStatus($res->getStatusCode());

		foreach(json_decode($res->getBody()) as $key => $value)
			$args[$key] = $value;

		return parent::__invoke($request, $response, $args);
	}
}

?>
