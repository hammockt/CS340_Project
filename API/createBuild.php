<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';
require_once 'postUtilities.php';
require_once 'tokenUtilities.php';
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Parser;

//make sure they are getting this endpoint
$httpMethods = ["POST"];
enforceHttpMethods($httpMethods);

$data = getJsonFromHttpBody();

//make sure that they sent us a authToken
$requiredKeys = ['authToken'];
$optionalKeys = ['buildName'];
enforceKeys($data, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($data, $requiredKeys);

$authToken = $data['authToken'];
$buildName = $data['buildName'];

//to token string into a token object
$token = (new Parser())->parse($authToken);
$config = loadConfig();
verifyAuthToken($config, $token);

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = 'CALL createBuild(:username, :buildName)';
$statement = $pdo->prepare($query);
$statement->bindValue(':username', $token->getClaim('uid'), PDO::PARAM_STR);
$statement->bindValue(':buildName', $buildName, PDO::PARAM_STR);
$statement->execute();

$affectedRows = $statement->fetchObject()->affectedRows;
$output = (object) [ 'affectedRows' => $affectedRows ];

printf("%s", json_encode($output));

?>
