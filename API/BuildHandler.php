<?php
namespace API;

require_once 'phpUtilities.php';
require_once 'vendor/autoload.php';

use \Resources\TypedInput;
use \PDO;

class BuildHandler extends RestrictedRequestHandler
{
	private static $inputKeys = [];

	public static function initClass()
	{
		self::$inputKeys['username'] = new TypedInput('string', true);
	}

	public function processRequest($request, $response, $input)
	{
		enforceKeys($input, self::$inputKeys);

		$pdo = connectToDatabase();

		$query = 'CALL getUsersBuilds(:username)';
		$statement = $pdo->prepare($query);
		$statement->bindValue(':username', $input['username'], PDO::PARAM_STR);
		$statement->execute();

		$jsonArray = DTO\Build::fetch($statement);
		return $response->withJson($jsonArray);
	}	
}

BuildHandler::initClass();

?>
