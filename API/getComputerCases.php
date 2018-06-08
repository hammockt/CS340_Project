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
$optionalKeys = ['partID', 'manufacturer', 'type', 'minCpuClearance', 'maxCpuClearance'];
enforceKeys($_GET, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($_GET, $requiredKeys);

//optional fields
$partID          = $_GET['partID'];
$manufacturer    = $_GET['manufacturer'];
$type            = $_GET['type'];
$minCpuClearance = $_GET['minCpuClearance'];
$maxCpuClearance = $_GET['maxCpuClearance'];

validateString($partID);
validateString($manufacturer);
validateString($type);
validateInteger($minCpuClearance);
validateInteger($maxCpuClearance);

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = 'CALL getComputerCases(:partID, :manufacturer, :type, :minCpuClearance, :maxCpuClearance)';
$statement = $pdo->prepare($query);
$statement->bindValue(':partID',          $partID,          PDO::PARAM_STR);
$statement->bindValue(':manufacturer',    $manufacturer,    PDO::PARAM_STR);
$statement->bindValue(':type',            $type,            PDO::PARAM_STR);
$statement->bindValue(':minCpuClearance', $minCpuClearance, PDO::PARAM_INT);
$statement->bindValue(':maxCpuClearance', $maxCpuClearance, PDO::PARAM_INT);
$statement->execute();

$jsonArray = array();
while($rowObject = $statement->fetchObject())
{
	$nameID = ($rowObject->model !== null)? $rowObject->model: $rowObject->partID;
	/*$lengths = explode(',', $rowObject->gpuClearance);
	$notes = explode(',', $rowObject->note);
	$array = [];
	for($i = 0; $i < count($lengths); $i++)
	{
		array_push($array, (object) [ 'length' => $lengths[$i], 'note' => $notes[$i] ]);
	}*/
	//$rowObject->formFactor = explode(',', $rowObject->formFactor);

	$rowObject = (array)$rowObject;
	$rowObject['name'] = "${rowObject['manufacturer']} $nameID";
	//$rowObject['gpuClearances'] = $array;
	$rowObject = (object)$rowObject;

	array_push($jsonArray, $rowObject);
}

printf("%s", json_encode($jsonArray));

?>
