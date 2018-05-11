<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once 'restUtilities.php';
require_once 'postUtilities.php';
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

$ini = parse_ini_file('../config/rest.ini');

//make sure they are posting this endpoint
$httpMethods = ["POST"];
enforceHttpMethods($httpMethods);

//get the JSON data from the post body
$data = getJsonFromHttpBody();

//make sure that they sent us a username and password
$keys = ['username', 'password'];
enforceNonEmptyKeys($data, $keys);

$username  = $data['username'];
$password  = $data['password'];

$pdo = new PDO($ini['db_dsn'], $ini['db_user'], $ini['db_password']);

//we need the password to validate the given username
$query = "SELECT password FROM Users WHERE username = ?";
$statement = $pdo->prepare($query);
$statement->bindValue(1, $username, PDO::PARAM_STR);
$statement->execute();
$results = $statement->fetchAll();
//should we also check if the results is greater than 1 and return HTTP(500)?
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

$token = (new Builder())->setIssuer($ini['refresh_iss'])
                        ->setAudience($ini['refresh_aud'])
                        ->setId(generateRandomSalt(16))
                        ->setIssuedAt(time())
                        ->setExpiration(time() + $ini['refresh_exp'])
                        ->setSubject('refresh')
                        ->set('uid', $username)
                        ->sign(new Sha256(), $ini['refresh_key'])
                        ->getToken();

$json = [ 'refreshToken' => "$token" ];
printf("%s", json_encode($json));

?>
