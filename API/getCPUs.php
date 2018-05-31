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
$optionalKeys = ['partID', 'name', 'manufacturer', 'socket', 'family', 'minCores', 'maxCores', 'minThreads', 'maxThreads', 'minClockSpeed', 'maxClockSpeed'];
enforceKeys($_GET, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($_GET, $requiredKeys);

//optional fields
$partID        = $_GET['partID'];
$name          = $_GET['name'];
$manufacturer  = $_GET['manufacturer'];
$socket        = $_GET['socket'];
$family        = $_GET['family'];
$minCores      = $_GET['minCores'];
$maxCores      = $_GET['maxCores'];
$minThreads    = $_GET['minThreads'];
$maxThreads    = $_GET['maxThreads'];
$minClockSpeed = $_GET['minClockSpeed'];
$maxClockSpeed = $_GET['maxClockSpeed'];

validateString($partID);
validateString($name);
validateString($manufacturer);
validateString($socket);
validateString($family);
validateInteger($minCores);
validateInteger($maxCores);
validateInteger($minThreads);
validateInteger($maxThreads);
validateFloat($minClockSpeed);
validateFloat($maxClockSpeed);

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password']);

$query = 'CALL getCPUs(:partID, :name, :manufacturer, :socket, :family, :minCores, :maxCores, :minThreads, :maxThreads, :minClockSpeed, :maxClockSpeed)';
$statement = $pdo->prepare($query);
$statement->bindValue(':partID',        $partID,       PDO::PARAM_STR);
$statement->bindValue(':name',          $name,         PDO::PARAM_STR);
$statement->bindValue(':manufacturer',  $manufacturer, PDO::PARAM_STR);
$statement->bindValue(':socket',        $socket,       PDO::PARAM_STR);
$statement->bindValue(':family',        $family,       PDO::PARAM_STR);
$statement->bindValue(':minCores',      $minCores,     PDO::PARAM_INT);
$statement->bindValue(':maxCores',      $maxCores,     PDO::PARAM_INT);
$statement->bindValue(':minThreads',    $minThreads,   PDO::PARAM_INT);
$statement->bindValue(':maxThreads',    $maxThreads,   PDO::PARAM_INT);
$statement->bindValue(':minClockSpeed', $minClockSpeed);
$statement->bindValue(':maxClockSpeed', $maxClockSpeed);
$statement->execute();

$jsonArray = array();
while($rowObject = $statement->fetchObject())
{
	array_push($jsonArray, $rowObject);
}

printf("%s", json_encode($jsonArray));

?>
