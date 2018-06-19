<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';
require_once 'vendor/autoload.php';

use \Firebase\JWT\JWT;

//make sure they are posting this endpoint
$httpMethods = ["POST"];
enforceHttpMethods($httpMethods);

//get the JSON data from the post body
$data = getJsonFromHttpBody();

//make sure that they sent us a username and password
$requiredKeys = ['username', 'password'];
$optionalKeys = [];
enforceKeys($data, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($data, $requiredKeys);

$username  = $data['username'];
$password  = $data['password'];

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

//we need the password to validate the given username
$query = "CALL getUserCredentials(:username)";
$statement = $pdo->prepare($query);
$statement->bindValue(':username', $username, PDO::PARAM_STR);
$statement->execute();
$results = $statement->fetchAll();
if(count($results) != 1)
{
	//unknown users are unauthorized
	http_response_code(401);
	exit();
}

//get the hashed password from the resultset
$hashedPassword = $results[0]['password'];

//if the password was incorrect
if(!password_verify($password, $hashedPassword))
{
	//bad password is unauthorized
	http_response_code(401);
	exit();
}

//make the refreshToken
$tokenData = array(
	'iss' => $config['refresh_iss'],
	'aud' => $config['refresh_aud'],
	'jti' => generateRandomSalt(16),
	'iat' => time(),
	'exp' => time() + $config['refresh_exp'],
	'sub' => 'refresh',
	'uid' => $username
);

//by default it is HS256
$tokenString = JWT::encode($tokenData, $config['refresh_key'], 'HS256');

$json = [ 'refreshToken' => $tokenString ];
printf("%s", json_encode($json));

?>
