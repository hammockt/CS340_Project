<?php
namespace API;

require_once 'phpUtilities.php';
require_once 'vendor/autoload.php';

use \Resources\TypedInput;
use \PDO;
use \Firebase\JWT\JWT;

function encodeRefreshToken($username)
{
	$config = loadConfig();
	$tokenData = array(
		'iss' => $config['refresh_iss'],
		'aud' => $config['refresh_aud'],
		'jti' => generateRandomSalt(16),
		'iat' => time(),
		'exp' => time() + $config['refresh_exp'],
		'sub' => 'refresh',
		'uid' => $username
	);

	return JWT::encode($tokenData, $config['refresh_key'], 'HS256');
}

function encodeAuthToken($username)
{
	$config = loadConfig();
	$authTokenData = array(
		'iss' => $config['auth_iss'],
		'aud' => $config['auth_aud'],
		'jti' => generateRandomSalt(16),
		'iat' => time(),
		'exp' => time() + $config['auth_exp'],
		'sub' => 'auth',
		'uid' => $username
	);

	return JWT::encode($authTokenData, $config['auth_key'], 'HS256');
}

function verifyToken($tokenString, $tokenType)
{
	$config = loadConfig();

	try
	{
		$token = JWT::decode($tokenString, $config["{$tokenType}_key"], ['HS256']);
	}
	catch(\Exception $e)
	{
		//expired tokens are unauthorized
		http_response_code(401);
		exit();
	}

	if(!isset($token->jti) || !isset($token->uid) || $token->iss !== $config["{$tokenType}_iss"] || $token->aud !== $config["{$tokenType}_aud"] || $token->sub !== $tokenType)
	{
		//bad tokens are unauthorized
		http_response_code(401);
		exit();
	}

	return $token;
}

class TokenHandler extends RequestHandler
{
	private static $inputKeys = [];

	public static function initClass()
	{
		self::$inputKeys['grantType'] = new TypedInput('string', true);
	}

	public function processRequest($request, $response, $input)
	{
		enforceKeys($input, self::$inputKeys);

		switch($input['grantType'])
		{
			case 'authorize': return $this->authorize($request, $response, $input);
			case 'refresh': return $this->refresh($request, $response, $input);
			case 'validate': return $this->validate($request, $response, $input);
			default: return $response->withStatus(400);
		}
	}

	public function authorize($request, $response, $input)
	{
		$username = $_SERVER['PHP_AUTH_USER'];
		$password = $_SERVER['PHP_AUTH_PW'];

		if($username === null || $password === null)
			return $response->withStatus(403);

		$pdo = connectToDatabase();
		$query = 'CALL getUserCredentials(:username)';
		$statement = $pdo->prepare($query);
		$statement->bindValue(':username', $username, PDO::PARAM_STR);
		$statement->execute();
		$results = $statement->fetchAll(PDO::FETCH_ASSOC);

		//We should of got 1 result
		if(count($results) != 1)
			return $response->withStatus(401);

		//if the password was incorrect
		$hashedPassword = $results[0]['password'];
		if(!password_verify($password, $hashedPassword))
			return $response->withStatus(401);

		//make the tokens
		$encodedRefreshToken = encodeRefreshToken($username);
		$encodedAuthToken = encodeAuthToken($username);

		$json = [
			'refreshToken' => $encodedRefreshToken,
			'authToken' => $encodedAuthToken
		];

		return $response->withJson($json);
	}

	public function refresh($request, $response, $input)
	{
		$token = explode(' ', $request->getHeader('Authorization')[0]);

		if(count($token) !== 2 || $token[0] !== 'Bearer')
		{
			return $response->write('Bearer authorization is required')
				            ->withStatus(403);
		}

		$refreshToken = verifyToken($token[1], 'refresh');
		$encodedAuthToken = encodeAuthToken($refreshToken->uid);

		$json = [ 'authToken' => $encodedAuthToken ];
		return $response->withJson($json);
	}

	public function validate($request, $response, $input)
	{
		$token = explode(' ', $request->getHeader('Authorization')[0]);

		if(count($token) !== 2 || $token[0] !== 'Bearer')
		{
			return $response->write('Bearer authorization is required')
				            ->withStatus(403);
		}

		$authToken = verifyToken($token[1], 'auth');

		$json = [ 'username' => $authToken->uid ];
		return $response->withJson($json);
	}
}

TokenHandler::initClass();

?>
