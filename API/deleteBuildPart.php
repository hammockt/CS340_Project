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
$requiredKeys = ['authToken', 'id'];
$optionalKeys = [];
enforceKeys($data, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($data, $requiredKeys);

$authToken = $data['authToken'];
$id        = $data['id'];

validateInteger($id);

$config = loadConfig();
verifyAuthToken($config, $authToken);

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = "DELETE FROM $table WHERE id = :id";
$statement = $pdo->prepare($query);
$statement->bindValue(':id', $id, PDO::PARAM_INT);
$statement->execute();

$output = (object) [ 'affectedRows' => $statement->rowCount() ];
printf("%s", json_encode($output));

?>
