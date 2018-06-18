<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';
require_once 'tokenUtilities.php';
require_once 'partNameBLL.php';
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Parser;

//make sure they are getting this endpoint
$httpMethods = ["GET"];
enforceHttpMethods($httpMethods);

//make sure that they sent us a authToken
$requiredKeys = ['authToken', 'buildID'];
$optionalKeys = [];
enforceKeys($_GET, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($_GET, $requiredKeys);

$authToken = $_GET['authToken'];
$buildID = $_GET['buildID'];

//to token string into a token object
$token = (new Parser())->parse($authToken);
$config = loadConfig();
verifyAuthToken($config, $token);

validateInteger($buildID);

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = "CALL $procedure(:buildID)";
$statement = $pdo->prepare($query);
$statement->bindValue(':buildID', $buildID, PDO::PARAM_INT);
$statement->execute();

$jsonArray = array();
while($row = $statement->fetch(PDO::FETCH_ASSOC))
{
	$id = $row['id'];
	unset($row['id']);

	$row['name'] = $nameFunction($row);

	$object = (object) [
		'id' => $id,
		'data' => $row
	];

	array_push($jsonArray, $object);
}

printf("%s", json_encode($jsonArray));

?>
