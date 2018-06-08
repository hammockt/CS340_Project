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
$optionalKeys = ['partID', 'socket', 'chipset', 'formFactor', 'manufacturer', 'ramType', 'minRAM', 'maxRAM', 'minSlots', 'maxSlots'];
enforceKeys($_GET, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($_GET, $requiredKeys);

//optional fields
$partID       = $_GET['partID'];
$socket       = $_GET['socket'];
$chipset      = $_GET['chipset'];
$formFactor   = $_GET['formFactor'];
$manufacturer = $_GET['manufacturer'];
$ramType      = $_GET['ramType'];
$minRAM       = $_GET['minRAM'];
$maxRAM       = $_GET['maxRAM'];
$minSlots     = $_GET['minSlots'];
$maxSlots     = $_GET['maxSlots'];

validateString($partID);
validateString($socket);
validateString($chipset);
validateString($formFactor);
validateString($manufacturer);
validateString($ramType);
validateFloat($minRAM);
validateFloat($maxRAM);
validateInteger($minSlots);
validateInteger($maxSlots);

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = 'CALL getMotherboards(:partID, :socket, :chipset, :formFactor, :manufacturer, :ramType, :minRAM, :maxRAM, :minSlots, :maxSlots)';
$statement = $pdo->prepare($query);
$statement->bindValue(':partID',       $partID,       PDO::PARAM_STR);
$statement->bindValue(':socket',       $socket,       PDO::PARAM_STR);
$statement->bindValue(':chipset',      $chipset,      PDO::PARAM_STR);
$statement->bindValue(':formFactor',   $formFactor,   PDO::PARAM_STR);
$statement->bindValue(':manufacturer', $manufacturer, PDO::PARAM_STR);
$statement->bindValue(':ramType',      $ramType,      PDO::PARAM_STR);
$statement->bindValue(':minRAM',       $minRAM);
$statement->bindValue(':maxRAM',       $maxRAM);
$statement->bindValue(':minSlots',     $minSlots,     PDO::PARAM_INT);
$statement->bindValue(':maxSlots',     $maxSlots,     PDO::PARAM_INT);
$statement->execute();

$jsonArray = array();
while($rowObject = $statement->fetchObject())
{
	$rowObject = (array)$rowObject;
	$rowObject['name'] = "${rowObject['manufacturer']} ${rowObject['partID']}";
	$rowObject = (object)$rowObject;

	array_push($jsonArray, $rowObject);
}

printf("%s", json_encode($jsonArray));

?>
