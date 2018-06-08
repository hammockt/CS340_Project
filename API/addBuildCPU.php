<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';
require_once 'getUtilities.php';
require_once 'postUtilities.php';
require_once 'tokenUtilities.php';
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Parser;

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

//to token string into a token object
$token = (new Parser())->parse($authToken);
$config = loadConfig();
verifyAuthToken($config, $token);

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = 'CALL addBuildCPU(:buildID, :partID)';
$statement = $pdo->prepare($query);
$statement->bindValue(':buildID', $buildID, PDO::PARAM_INT);
$statement->bindValue(':partID', $partID, PDO::PARAM_STR);
$statement->execute();

$affectedRows = $statement->fetchObject()->affectedRows;
$output = (object) [ 'affectedRows' => $affectedRows ];

printf("%s", json_encode($output));

?>
