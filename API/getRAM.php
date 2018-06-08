<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';
require_once 'getUtilities.php';

//make sure they are posting this endpoint
$httpMethods = ["GET"];
enforceHttpMethods($httpMethods);

$requiredKeys = [];
$optionalKeys = ['partID', 'manufacturer', 'type', 'moduleType', 'minSize', 'maxSize', 'minSpeed', 'maxSpeed', 'minSticks', 'maxSticks'];
enforceKeys($_GET, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($_GET, $requiredKeys);

//optional fields
$partID       = $_GET['partID'];
$manufacturer = $_GET['manufacturer'];
$type         = $_GET['type'];
$moduleType   = $_GET['moduleType'];
$minSize      = $_GET['minSize'];
$maxSize      = $_GET['maxSize'];
$minSpeed     = $_GET['minSpeed'];
$maxSpeed     = $_GET['maxSpeed'];
$minSticks    = $_GET['minSticks'];
$maxSticks    = $_GET['maxSticks'];

validateString($partID);
validateString($manufacturer);
validateString($type);
validateString($moduleType);
validateFloat($minSize);
validateFloat($maxSize);
validateFloat($minSpeed);
validateFloat($maxSpeed);
validateInteger($minSticks);
validateInteger($maxSticks);

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = 'CALL getRAM(:partID, :manufacturer, :type, :moduleType, :minSize, :maxSize, :minSpeed, :maxSpeed, :minSticks, :maxSticks)';
$statement = $pdo->prepare($query);
$statement->bindValue(':partID',       $partID,       PDO::PARAM_STR);
$statement->bindValue(':manufacturer', $manufacturer, PDO::PARAM_STR);
$statement->bindValue(':type',         $type,         PDO::PARAM_STR);
$statement->bindValue(':moduleType',   $moduleType,   PDO::PARAM_STR);
$statement->bindValue(':minSize',      $minSize);
$statement->bindValue(':maxSize',      $maxSize);
$statement->bindValue(':minSpeed',     $minSpeed);
$statement->bindValue(':maxSpeed',     $maxSpeed);
$statement->bindValue(':minSticks',    $minSticks,    PDO::PARAM_INT);
$statement->bindValue(':maxSticks',    $maxSticks,    PDO::PARAM_INT);
$statement->execute();

$jsonArray = array();
while($rowObject = $statement->fetchObject())
{
	$nameID = $rowObject->manufacturer;
	if($rowObject->series !== null)
	{
		$nameID = "$nameID $rowObject->series";
	}

	$rowObject = (array)$rowObject;
	$rowObject['name'] = $nameID;
	$rowObject = (object)$rowObject;

	array_push($jsonArray, $rowObject);
}

printf("%s", json_encode($jsonArray));

?>
