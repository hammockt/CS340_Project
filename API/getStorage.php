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
$optionalKeys = ['partID', 'formFactor', 'manufacturer', 'minSize', 'maxSize', 'isSSD', 'isHDD'];
enforceKeys($_GET, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($_GET, $requiredKeys);

//optional fields
$partID       = $_GET['partID'];
$formFactor   = $_GET['formFactor'];
$manufacturer = $_GET['manufacturer'];
$minSize      = $_GET['minSize'];
$maxSize      = $_GET['maxSize'];
$isSSD        = $_GET['isSSD'];
$isHDD        = $_GET['isHDD'];

validateString($partID);
validateString($formFactor);
validateString($manufacturer);
validateInteger($minSize);
validateInteger($maxSize);
validateBoolean($isSSD);
validateBoolean($isHDD);

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = 'CALL getStorage(:partID, :formFactor, :manufacturer, :minSize, :maxSize, :isSSD, :isHDD)';
$statement = $pdo->prepare($query);
$statement->bindValue(':partID',        $partID,       PDO::PARAM_STR);
$statement->bindValue(':formFactor',    $formFactor,   PDO::PARAM_STR);
$statement->bindValue(':manufacturer',  $manufacturer, PDO::PARAM_STR);
$statement->bindValue(':minSize',       $minSize,      PDO::PARAM_INT);
$statement->bindValue(':maxSize',       $maxSize,      PDO::PARAM_INT);
$statement->bindValue(':isSSD',         $isSSD,        PDO::PARAM_BOOL);
$statement->bindValue(':isHDD',         $isHDD,        PDO::PARAM_BOOL);
$statement->execute();

$jsonArray = array();
while($rowObject = $statement->fetchObject())
{
	$rowObject = (array)$rowObject;
	$rowObject['name'] = "${rowObject['manufacturer']} ${rowObject['series']}";
	$rowObject = (object)$rowObject;

	array_push($jsonArray, $rowObject);
}

ob_start("ob_gzhandler");
printf("%s", json_encode($jsonArray));
ob_end_flush();

?>
