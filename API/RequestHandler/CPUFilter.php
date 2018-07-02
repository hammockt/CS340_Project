<?php
namespace API\RequestHandler;

require_once 'phpUtilities.php';
require_once 'vendor/autoload.php';

use \Resources\TypedInput;
use \PDO;

class CPUFilter extends RequestHandler
{
	public static function initClass()
	{
		parent::$inputKeys['partID']        = new TypedInput('string');
		parent::$inputKeys['name']          = new TypedInput('string');
		parent::$inputKeys['manufacturer']  = new TypedInput('string');
		parent::$inputKeys['socket']        = new TypedInput('string');
		parent::$inputKeys['family']        = new TypedInput('string');
		parent::$inputKeys['minCores']      = new TypedInput('integer');
		parent::$inputKeys['maxCores']      = new TypedInput('integer');
		parent::$inputKeys['minThreads']    = new TypedInput('integer');
		parent::$inputKeys['maxThreads']    = new TypedInput('integer');
		parent::$inputKeys['minClockSpeed'] = new TypedInput('float');
		parent::$inputKeys['maxClockSpeed'] = new TypedInput('float');
	}

	public function processRequest($request, $response, $input)
	{
		$pdo = connectToDatabase();

		$query = 'CALL getCPUs(:partID, :name, :manufacturer, :socket, :family, :minCores, :maxCores, :minThreads, :maxThreads, :minClockSpeed, :maxClockSpeed)';
		$statement = $pdo->prepare($query);
		$statement->execute($input);

		$jsonArray = \API\DTO\CPU::fetch($statement);
		return $response->withJson($jsonArray);
	}
}

CPUFilter::initClass();

?>
