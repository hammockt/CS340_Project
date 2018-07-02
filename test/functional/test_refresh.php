<?php
require_once 'phpUtilities.php';
require_once 'vendor/autoload.php';

use \Firebase\JWT\JWT;

class TestRefreshToken extends PHPUnit_Framework_TestCase
{
	private static $client;
	
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
	}

	public function verifyToken( $refreshToken )
	{
		$this->assertStringMatchesFormat('%s.%s.%s', $refreshToken);

		$config = loadConfig();

		$token = JWT::decode($refreshToken, $config['refresh_key'], ['HS256']);
		$this->assertTrue(isset($token->jti));
		$this->assertTrue(isset($token->uid));
		$this->assertTrue($token->iss === $config['refresh_iss']);
		$this->assertTrue($token->aud === $config['refresh_aud']);
		$this->assertTrue($token->sub === 'refresh');
	}

	public function testGoodInput()
	{
		$res = self::$client->request('POST', 'refreshToken', [
			GuzzleHttp\RequestOptions::JSON => self::goodInput(),
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$json = json_decode($res->getBody());
		$this->verifyToken($json->refreshToken);
	}

	public function testBadHttpMethod()
	{
		$res = self::$client->request('GET', 'refreshToken', [
			GuzzleHttp\RequestOptions::JSON => self::goodInput(),
			'http_errors' => false
		]);

		$this->assertEquals(405, $res->getStatusCode());
	}

	public function testBadPassword()
	{
		$data = self::goodInput();
		$data['password'] = 'thisWillClearlyNotWork';

		$res = self::$client->request('POST', 'refreshToken', [
			GuzzleHttp\RequestOptions::JSON => $data,
			'http_errors' => false
		]);

		$this->assertEquals(401, $res->getStatusCode());
	}

	public function testBadUsername()
	{
		$data = self::goodInput();
		$data['username'] = 'thisWillClearlyNotWork';

		$res = self::$client->request('POST', 'refreshToken', [
			GuzzleHttp\RequestOptions::JSON => $data,
			'http_errors' => false
		]);

		$this->assertEquals(401, $res->getStatusCode());
	}

	public function testIncorrectJsonKeys()
	{
		$data = [
			'u' => 'bad',
			'p' => 'especiallyBad'
		];

		$res = self::$client->request('POST', 'refreshToken', [
			GuzzleHttp\RequestOptions::JSON => $data,
			'http_errors' => false
		]);

		$this->assertEquals(400, $res->getStatusCode());
	}

	public function testEmptyJsonKey()
	{
		$data = self::goodInput();
		$data['username'] = '';
		$data['password'] = '';

		$res = self::$client->request('POST', 'refreshToken', [
			GuzzleHttp\RequestOptions::JSON => $data,
			'http_errors' => false
		]);

		$this->assertEquals(400, $res->getStatusCode());
	}

	public function testAdditionalKey()
	{
		$data = self::goodInput();
		$data['thisIsNotAKey'] = 'test';

		$res = self::$client->request('POST', 'refreshToken', [
			GuzzleHttp\RequestOptions::JSON => $data,
			'http_errors' => false
		]);

		$this->assertEquals(400, $res->getStatusCode());
	}
}

?>
