<?php
namespace API\RequestHandler\AuthorizedRequest\BearerAuthRequest\JwtAuthRequest;

require_once 'phpUtilities.php';
require_once 'vendor/autoload.php';

use \PDO;

class BuildList extends JwtAuthRequest
{
	public function processAuthorizedRequest($request, $response, $input)
	{
		$pdo = connectToDatabase();

		$query = 'CALL getUsersBuilds(:username)';
		$statement = $pdo->prepare($query);
		$statement->bindValue(':username', $this->tokenProperties->username);
		$statement->execute();

		$jsonArray = \API\DTO\Build::fetch($statement);
		return $response->withJson($jsonArray);
	}	
}

?>
