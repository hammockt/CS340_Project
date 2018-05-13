<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';
require_once 'postUtilities.php';

//make sure they are posting this endpoint
$httpMethods = ["POST"];
enforceHttpMethods($httpMethods);

$data = getJsonFromHttpBody();

$keys = ['username', 'password'];
enforceNonEmptyKeys($data, $keys);

//required fields
$username  = $data['username'];
$password  = $data['password'];

//optional fields
$nickname  = (empty($data['nickname'])? null: $data['nickname']);

//does the password meet the minimum requirements?
if(!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\W\w]{6,72}$/', $password))
{
	http_response_code(409);
	exit();
}

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password']);

//check if someone already has this username
$query = "SELECT COUNT(username) as totalCount FROM Users WHERE username = ?";
$statement = $pdo->prepare($query);
$statement->bindValue(1, $username, PDO::PARAM_STR);
$statement->execute();
$totalCount = $statement->fetchObject()->totalCount;
if($totalCount > 0)
{
	http_response_code(409);
	exit();
}

//all good so hash password
$hashedPassword = password_hash($password, PASSWORD_BCRYPT, ["cost" => 10]);

//insert them into the Database
$query = "INSERT INTO Users (username, password, nickname) VALUES(?, ?, ?)";
$statement = $pdo->prepare($query);
$statement->bindValue(1, $username,       PDO::PARAM_STR);
$statement->bindValue(2, $hashedPassword, PDO::PARAM_STR);
$statement->bindValue(3, $nickname,       PDO::PARAM_STR);
$statement->execute();

?>
