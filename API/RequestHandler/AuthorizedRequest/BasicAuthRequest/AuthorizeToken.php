<?php
namespace API\RequestHandler\AuthorizedRequest\BasicAuthRequest;

require_once 'phpUtilities.php';
require_once 'vendor/autoload.php';

use \PDO;

class AuthorizeToken extends BasicAuthRequest
{
	public function processAuthorizedRequest($request, $response, $input)
	{
		$pdo = connectToDatabase();
		$query = 'SELECT getUserCredentials(:username) as hashedPassword';
		$statement = $pdo->prepare($query);
		$statement->bindValue(':username', $this->username);
		$statement->execute();
		$hashedPassword = $statement->fetch(PDO::FETCH_ASSOC)['hashedPassword'];

		//if the password was incorrect
		if(!password_verify($this->password, $hashedPassword))
			return $response->withStatus(401);

		//make the tokens
		$encodedRefreshToken = \API\TokenHandler::encodeRefreshToken($this->username);
		$encodedAuthToken = \API\TokenHandler::encodeAuthToken($this->username);

		$json =
		[
			'refreshToken' => $encodedRefreshToken,
			'authToken' => $encodedAuthToken
		];

		return $response->withJson($json);
	}
}

?>
