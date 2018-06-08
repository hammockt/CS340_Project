<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
//header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';
require_once 'getUtilities.php';
require_once 'vendor/autoload.php';

//make sure they are posting this endpoint
$httpMethods = ["GET"];
enforceHttpMethods($httpMethods);

$requiredKeys = ['refreshToken'];
$optionalKeys = [];
enforceKeys($_GET, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($_GET, $requiredKeys);

$refreshToken = $_GET['refreshToken'];

validateString($refreshToken);

$refreshTokenObject = (object) [ 'refreshToken' => $refreshToken ];

$client = new GuzzleHttp\Client(['base_uri' => 'https://web.engr.oregonstate.edu/~hammockt/cs340/Project/Dev/API/']);

$res = $client->request('POST', 'authToken', [
	GuzzleHttp\RequestOptions::JSON => $refreshTokenObject,
	'http_errors' => false
]);

$authTokenJson = $res->getBody();
$authTokenJsonObject = json_decode($authTokenJson);
printf("%s", $authTokenJsonObject->authToken);

?>
