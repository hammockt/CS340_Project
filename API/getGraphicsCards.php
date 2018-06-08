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
$optionalKeys = ['partID', 'manufacturer', 'gpuChipset', 'memoryType', 'minClockSpeed', 'maxClockSpeed', 'minLength', 'maxLength', 'minMemory', 'maxMemory'];
enforceKeys($_GET, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($_GET, $requiredKeys);

//optional fields
$partID        = $_GET['partID'];
$manufacturer  = $_GET['manufacturer'];
$gpuChipset    = $_GET['gpuChipset'];
$memoryType    = $_GET['memoryType'];
$minClockSpeed = $_GET['minClockSpeed'];
$maxClockSpeed = $_GET['maxClockSpeed'];
$minLength     = $_GET['minLength'];
$maxLength     = $_GET['maxLength'];
$minMemory     = $_GET['minMemory'];
$maxMemory     = $_GET['maxMemory'];

validateString($partID);
validateString($manufacturer);
validateString($gpuChipset);
validateString($memoryType);
validateFloat($minClockSpeed);
validateFloat($maxClockSpeed);
validateInteger($minLength);
validateInteger($maxLength);
validateFloat($minMemory);
validateFloat($maxMemory);

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = 'CALL getGraphicsCards(:partID, :manufacturer, :gpuChipset, :memoryType, :minClockSpeed, :maxClockSpeed, :minLength, :maxLength, :minMemory, :maxMemory)';
$statement = $pdo->prepare($query);
$statement->bindValue(':partID',        $partID,       PDO::PARAM_STR);
$statement->bindValue(':manufacturer',  $manufacturer, PDO::PARAM_STR);
$statement->bindValue(':gpuChipset',    $gpuChipset,   PDO::PARAM_STR);
$statement->bindValue(':memoryType',    $memoryType,   PDO::PARAM_STR);
$statement->bindValue(':minClockSpeed', $minClockSpeed);
$statement->bindValue(':maxClockSpeed', $maxClockSpeed);
$statement->bindValue(':minLength',     $minLength,    PDO::PARAM_INT);
$statement->bindValue(':maxLength',     $maxLength,    PDO::PARAM_INT);
$statement->bindValue(':minMemory',     $minMemory);
$statement->bindValue(':maxMemory',     $maxMemory);
$statement->execute();

$jsonArray = array();
while($rowObject = $statement->fetchObject())
{
	$nameID = ($rowObject->series !== null)? $rowObject->series: $rowObject->gpuChipset;

	$rowObject = (array)$rowObject;
	$rowObject['name'] = "${rowObject['manufacturer']} $nameID";
	$rowObject = (object)$rowObject;

	array_push($jsonArray, $rowObject);
}

printf("%s", json_encode($jsonArray));

?>
