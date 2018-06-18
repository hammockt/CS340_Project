<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';

//make sure they are posting this endpoint
$httpMethods = ["POST"];
enforceHttpMethods($httpMethods);

$data = getJsonFromHttpBody();

$requiredKeys = ['username', 'password'];
$optionalKeys = ['nickname'];
enforceKeys($data, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($data, $requiredKeys);

//required fields
$username  = $data['username'];
$password  = $data['password'];

//optional fields
$nickname  = (empty($data['nickname'])? null: $data['nickname']);

//does the password meet the minimum requirements?
//is the username a valid email?
$passwordRegex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[\W\w]{6,72}$/';
$usernameRegex = '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD';
if(!preg_match($passwordRegex, $password) || !preg_match($usernameRegex, $username))
{
	http_response_code(409);
	exit();
}

$config = loadConfig();

$pdo = new PDO($config['db_dsn'], $config['db_user'], $config['db_password'], array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));

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

$output = (object) [ 'affectedRows' => $statement->rowCount() ];
printf("%s", json_encode($output));

?>
