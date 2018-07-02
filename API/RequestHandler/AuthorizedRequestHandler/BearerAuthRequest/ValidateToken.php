<?php
namespace API\RequestHandler\AuthorizedRequest\BearerAuthRequest;

require_once 'vendor/autoload.php';

class ValidateToken extends BearerAuthRequest
{
	public function processAuthorizedRequest($request, $response, $input)
	{
		$authToken = \API\TokenHandler::verifyToken($this->bearerField, 'auth');
		if($authToken === null)
			return $response->withStatus(401);

		$json = [ 'username' => $authToken->uid ];
		return $response->withJson($json);
	}
}

?>
