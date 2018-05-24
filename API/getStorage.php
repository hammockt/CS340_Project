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
$optionalKeys = ['formFactor', 'manufacturer', 'series', 'minSize', 'maxSize', 'isSSD', 'isHDD'];
enforceKeys($_GET, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($_GET, $requiredKeys);

//optional fields
$formFactor   = $_GET['formFactor'];
$manufacturer = $_GET['manufacturer'];
$series       = $_GET['series'];
$minSize      = $_GET['minSize'];
$maxSize      = $_GET['maxSize'];
$isSSD        = $_GET['isSSD'];
$isHDD        = $_GET['isHDD'];

validateString($formFactor);
validateString($manufacturer);
validateString($series);
validateInt($minSize);
validateInt($maxSize);
validateBoolean($isSSD);
validateBoolean($isHDD);

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password']);

$query = 'CALL getStorage(:formFactor, :manufacturer, :series, :minSize, :maxSize, :isSSD, :isHDD)';
$statement = $pdo->prepare($query);
$statement->bindValue(':formFactor',    $formFactor,   PDO::PARAM_STR);
$statement->bindValue(':manufacturer',  $manufacturer, PDO::PARAM_STR);
$statement->bindValue(':series',        $series,       PDO::PARAM_STR);
$statement->bindValue(':minSize',       $minSize,      PDO::PARAM_INT);
$statement->bindValue(':maxSize',       $maxSize,      PDO::PARAM_INT);
$statement->bindValue(':isSSD',         $isSSD);
$statement->bindValue(':isHDD',         $isHDD);
$statement->execute();

$jsonArray = array();
while($rowObject = $statement->fetchObject())
{
	array_push($jsonArray, $rowObject);
}

printf("%s", json_encode($jsonArray));

?>
