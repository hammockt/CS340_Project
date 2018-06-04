<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
//header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'vendor/autoload.php';

function goodInput()
{
	$data = [
		'username' => 'test@testing.com',
		'password' => 'test'
	];

	return $data;
}

$client = new GuzzleHttp\Client(['base_uri' => 'https://web.engr.oregonstate.edu/~hammockt/cs340/Project/Dev/API/']);

$res = $client->request('POST', 'refreshToken', [
	GuzzleHttp\RequestOptions::JSON => goodInput(),
	'http_errors' => false
]);

$refreshTokenJson = $res->getBody();
$refreshTokenJsonObject = json_decode($refreshTokenJson);
printf("%s", $refreshTokenJsonObject->refreshToken);

?>
