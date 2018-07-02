<?php
namespace API\RequestHandler\AuthorizedRequest\BearerAuthRequest;

require_once 'vendor/autoload.php';

class RefreshToken extends BearerAuthRequest
{
	public function processAuthorizedRequest($request, $response, $input)
	{
		$refreshToken = \API\TokenHandler::verifyToken($this->bearerField, 'refresh');
		if($refreshToken === null)
			return $response->withStatus(401);

		$encodedAuthToken = \API\TokenHandler::encodeAuthToken($refreshToken->uid);

		$json = [ 'authToken' => $encodedAuthToken ];
		return $response->withJson($json);
	}
}

?>
