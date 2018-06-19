<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';
require_once 'tokenUtilities.php';
require_once 'vendor/autoload.php';

//make sure they are getting this endpoint
$httpMethods = ["POST"];
enforceHttpMethods($httpMethods);

$data = getJsonFromHttpBody();

//make sure that they sent us a authToken
$requiredKeys = ['authToken', 'buildID', 'partID'];
$optionalKeys = [];
enforceKeys($data, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($data, $requiredKeys);

$authToken = $data['authToken'];
$buildID   = $data['buildID'];
$partID    = $data['partID'];

validateInteger($buildID);
validateString($partID);

$config = loadConfig();
verifyAuthToken($config, $authToken);

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = "INSERT INTO $table (buildID, partID) VALUES (:buildID, :partID)";
$statement = $pdo->prepare($query);
$statement->bindValue(':buildID', $buildID, PDO::PARAM_INT);
$statement->bindValue(':partID', $partID, PDO::PARAM_STR);
$statement->execute();

$output = (object) [ 'affectedRows' => $statement->rowCount() ];
printf("%s", json_encode($output));

?>
