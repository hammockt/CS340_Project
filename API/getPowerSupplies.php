<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';
require_once 'partNameBLL.php';

//make sure they are posting this endpoint
$httpMethods = ["GET"];
enforceHttpMethods($httpMethods);

$requiredKeys = [];
$optionalKeys = ['partID', 'manufacturer', 'modular', 'eightyPlus', 'minWattage', 'maxWattage'];
enforceKeys($_GET, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($_GET, $requiredKeys);

//optional fields
$partID       = $_GET['partID'];
$manufacturer = $_GET['manufacturer'];
$modular      = $_GET['modular'];
$eightyPlus   = $_GET['eightyPlus'];
$minWattage   = $_GET['minWattage'];
$maxWattage   = $_GET['maxWattage'];

validateString($partID);
validateString($manufacturer);
validateString($modular);
validateString($isLiquid);
validateInteger($minWattage);
validateInteger($maxWattage);

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = 'CALL getPowerSupplies(:partID, :manufacturer, :modular, :eightyPlus, :minWattage, :maxWattage)';
$statement = $pdo->prepare($query);
$statement->bindValue(':partID',       $partID,       PDO::PARAM_STR);
$statement->bindValue(':manufacturer', $manufacturer, PDO::PARAM_STR);
$statement->bindValue(':modular',      $modular,      PDO::PARAM_STR);
$statement->bindValue(':eightyPlus',   $eightyPlus,   PDO::PARAM_STR);
$statement->bindValue(':minWattage',   $minWattage,   PDO::PARAM_INT);
$statement->bindValue(':maxWattage',   $maxWattage,   PDO::PARAM_INT);
$statement->execute();

$jsonArray = array();
while($row = $statement->fetch(PDO::FETCH_ASSOC))
{
	$row['name'] = getPowerSupplyName($row);

	array_push($jsonArray, (object)$row);
}

printf("%s", json_encode($jsonArray));

?>
