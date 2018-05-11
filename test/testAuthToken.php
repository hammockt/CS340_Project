<?php
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

class TestAuthToken extends PHPUnit_Framework_TestCase
{
	private static $client;
	private static $refreshTokenJsonObject;
	private static $refreshToken;

	public static function goodInput()
	{
		$data = [
			'username' => 'test@testing.com',
			'password' => 'test'
		];

		return $data;
	}

	public static function setUpBeforeClass()
	{
		self::$client = new GuzzleHttp\Client(['base_uri' => 'https://web.engr.oregonstate.edu/~hammockt/cs340/Project/Dev/API/']);

		$res = self::$client->request('POST', 'refreshToken', [
			GuzzleHttp\RequestOptions::JSON => self::goodInput(),
			'http_errors' => false
		]);

		self::$refreshTokenJsonObject = json_decode($res->getBody());
		self::$refreshToken = (new Parser())->parse(self::$refreshTokenJsonObject->refreshToken);
	}

	public function verifyToken( $authToken )
	{
		$this->assertStringMatchesFormat('%s.%s.%s', $authToken);

		$ini = parse_ini_file('../config/rest.ini');

		$token = (new Parser())->parse($authToken);
		$this->assertTrue($token->verify(new Sha256(), $ini['auth_key']));

		$tokenValidater = new ValidationData();
		$tokenValidater->setIssuer($ini['auth_iss']);
		$tokenValidater->setAudience($ini['auth_aud']);
		$tokenValidater->setSubject('auth');
		$this->assertTrue($token->validate($tokenValidater));

		$this->assertTrue($token->hasClaim('jti'));
		$this->assertTrue($token->hasClaim('uid'));
	}

	public function testGoodInput()
	{
		$res = self::$client->request('POST', 'authToken', [
			GuzzleHttp\RequestOptions::JSON => self::$refreshTokenJsonObject,
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$json = json_decode($res->getBody());
		$this->verifyToken($json->authToken);
	}
}
?>
