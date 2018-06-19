<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';
require_once 'tokenUtilities.php';
require_once 'vendor/autoload.php';

use \Firebase\JWT\JWT;

//make sure they are posting this endpoint
$httpMethods = ["POST"];
enforceHttpMethods($httpMethods);

//get the JSON data from the post body
$data = getJsonFromHttpBody();

//make sure that they sent us a refreshToken
$requiredKeys = ['refreshToken'];
$optionalKeys = [];
enforceKeys($data, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($data, $requiredKeys);

$refreshToken = $data['refreshToken'];

$config = loadConfig();

$refreshTokenObject = verifyRefreshToken($config, $refreshToken);

//now create a authToken
$authTokenData = array(
	'iss' => $config['auth_iss'],
	'aud' => $config['auth_aud'],
	'jti' => generateRandomSalt(16),
	'iat' => time(),
	'exp' => time() + $config['auth_exp'],
	'sub' => 'auth',
	'uid' => $refreshTokenObject->uid
);

//by default it is HS256
$authTokenString = JWT::encode($authTokenData, $config['auth_key'], 'HS256');

$json = [ 'authToken' => $authTokenString ];
printf("%s", json_encode($json));

?>
