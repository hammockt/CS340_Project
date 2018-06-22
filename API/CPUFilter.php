<?php
namespace API;

require_once 'phpUtilities.php';
require_once 'vendor/autoload.php';

use \Resources\TypedInput;
use \PDO;

class CPUFilter extends RequestHandler
{
	private static $inputKeys = [];

	public static function initClass()
	{
		self::$inputKeys['partID']        = new TypedInput('string');
		self::$inputKeys['name']          = new TypedInput('string');
		self::$inputKeys['manufacturer']  = new TypedInput('string');
		self::$inputKeys['socket']        = new TypedInput('string');
		self::$inputKeys['family']        = new TypedInput('string');
		self::$inputKeys['minCores']      = new TypedInput('integer');
		self::$inputKeys['maxCores']      = new TypedInput('integer');
		self::$inputKeys['minThreads']    = new TypedInput('integer');
		self::$inputKeys['maxThreads']    = new TypedInput('integer');
		self::$inputKeys['minClockSpeed'] = new TypedInput('float');
		self::$inputKeys['maxClockSpeed'] = new TypedInput('float');
	}

	public function processRequest($request, $response, $input)
	{
		enforceKeys($input, self::$inputKeys);

		$pdo = connectToDatabase();

		$query = 'CALL getCPUs(:partID, :name, :manufacturer, :socket, :family, :minCores, :maxCores, :minThreads, :maxThreads, :minClockSpeed, :maxClockSpeed)';
		$statement = $pdo->prepare($query);
		passInputKeys($input, self::$inputKeys, $statement);
		$statement->execute();

		$jsonArray = DTO\CPU::fetch($statement);
		return $response->withJson($jsonArray);
	}
}

CPUFilter::initClass();

?>
