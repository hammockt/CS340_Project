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
$optionalKeys = ['password', 'nickname'];
enforceKeys($data, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($data, $requiredKeys);

$authToken = $data['authToken'];
$password  = $data['password'];
$nickname  = $data['nickname'];

//to token string into a token object
$token = (new Parser())->parse($authToken);
$config = loadConfig();
verifyAuthToken($config, $token);

if($password !== null)
{
	$passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\W\w]{6,72}$/';
	if(!preg_match($passwordRegex, $password))
	{
		http_response_code(409);
		exit();
	}
}

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

$query = 'CALL updateUserProperties(:username, :password, :nickname)';
$statement = $pdo->prepare($query);
$statement->bindValue(':username', $token->getClaim('uid'), PDO::PARAM_STR);
if($password === null)
	$statement->bindValue(':password', $password, PDO::PARAM_STR);
else
{
	$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);
	$statement->bindValue(':password', $hashedPassword, PDO::PARAM_STR);
}
$statement->bindValue(':nickname', $nickname, PDO::PARAM_STR);
$statement->execute();

$affectedRows = $statement->fetchObject()->affectedRows;
$output = (object) [ 'affectedRows' => $affectedRows ];

printf("%s", json_encode($output));

?>
