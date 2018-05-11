<?php
require_once 'phpUtilities.php';
require_once 'vendor/autoload.php';

use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\ValidationData;

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

		$token = (new Parser())->parse($refreshToken);
		$this->assertTrue($token->verify(new Sha256(), $config['refresh_key']));

		$tokenValidater = new ValidationData();
		$tokenValidater->setIssuer($config['refresh_iss']);
		$tokenValidater->setAudience($config['refresh_aud']);
		$tokenValidater->setSubject('refresh');
		$this->assertTrue($token->validate($tokenValidater));

		$this->assertTrue($token->hasClaim('jti'));
		$this->assertTrue($token->hasClaim('uid'));
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
}

?>
