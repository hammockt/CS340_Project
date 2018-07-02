<?php
namespace API\RequestHandler\AuthorizedRequest\BearerAuthRequest\JwtAuthRequest;

require_once 'vendor/autoload.php';

use \API\RequestHandler\AuthorizedRequest\BearerAuthRequest\BearerAuthRequest;
use \GuzzleHttp\Client;

abstract class JwtAuthRequest extends BearerAuthRequest
{
	protected $tokenProperties;

	public function authorize($request, $response)
	{
		$result = parent::authorize($request, $response);
		if($result !== null)
			return $result;

		//we should probably put this url in the config file for later
		$client = new Client([ 'http_errors' => false ]);
		$res = $client->post('https://web.engr.oregonstate.edu/~hammockt/cs340/Project/Dev/API/token/validate',
		[
			'headers' =>
			[
				'Content-Type' => 'application/x-www-form-urlencoded',
				'Authorization' => "Bearer $this->bearerField"
			]
		]);

		if($res->getStatusCode() < 200 || $res->getStatusCode() > 299)
		{
			return $response->withStatus($res->getStatusCode())
				            ->withAddedHeader('Content-Type', $res->getHeader('Content-Type')[0])
				            ->write($res->getBody());
		}

		$this->tokenProperties = json_decode($res->getBody());
		return null;
	}
}

?>
