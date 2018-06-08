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
$optionalKeys = ['partID', 'manufacturer', 'isAir', 'isLiquid', 'minHeight', 'maxHeight'];
enforceKeys($_GET, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($_GET, $requiredKeys);

//optional fields
$partID       = $_GET['partID'];
$manufacturer = $_GET['manufacturer'];
$isAir        = $_GET['isAir'];
$isLiquid     = $_GET['isLiquid'];
$minHeight    = $_GET['minHeight'];
$maxHeight    = $_GET['maxHeight'];

validateString($partID);
validateString($manufacturer);
validateBoolean($isAir);
validateBoolean($isLiquid);
validateInteger($minHeight);
validateInteger($maxHeight);

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = 'CALL getCPUCoolers(:partID, :manufacturer, :isAir, :isLiquid, :minHeight, :maxHeight)';
$statement = $pdo->prepare($query);
$statement->bindValue(':partID',        $partID,       PDO::PARAM_STR);
$statement->bindValue(':manufacturer',  $manufacturer, PDO::PARAM_STR);
$statement->bindValue(':isAir',         $isAir,        PDO::PARAM_BOOL);
$statement->bindValue(':isLiquid',      $isLiquid,     PDO::PARAM_BOOL);
$statement->bindValue(':minHeight',     $minHeight,    PDO::PARAM_INT);
$statement->bindValue(':maxHeight',     $maxHeight,    PDO::PARAM_INT);
$statement->execute();

$jsonArray = array();
while($rowObject = $statement->fetchObject())
{
	$nameID = ($rowObject->model !== null)? $rowObject->model: $rowObject->partID;

	$rowObject = (array)$rowObject;
	$rowObject['name'] = "${rowObject['manufacturer']} $nameID";
	$rowObject = (object)$rowObject;

	array_push($jsonArray, $rowObject);
}

printf("%s", json_encode($jsonArray));

?>
