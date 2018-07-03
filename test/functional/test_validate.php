<?php
require_once 'vendor/autoload.php';

class TestValidate extends PHPUnit_Framework_TestCase
{
	private static $client;
	private static $request;
	private static $username = 'test@testing.com';

	public static function setUpBeforeClass()
	{
		$authToken = API\TokenHandler::encodeAuthToken(self::$username);

		$requestOptions =
		[
			'Authorization' => "Bearer $authToken",
			'Content-Type' => 'application/x-www-form-urlencoded'
		];

		self::$client = new GuzzleHttp\Client([ 'http_errors' => false ]);
		self::$request = new GuzzleHttp\Psr7\Request('POST', 'https://web.engr.oregonstate.edu/~hammockt/cs340/Project/Dev/API/token/validate', $requestOptions);
	}

	public function testNoBearer()
	{
		$tempRequest = GuzzleHttp\Psr7\modify_request(self::$request, ['set_headers' => ['Authorization' => 'Basic ' . base64_encode(self::$username.':test')]]);
		$res = self::$client->send($tempRequest);

		$this->assertEquals(403, $res->getStatusCode());
	}

	public function testBadBearer()
	{
		$tempRequest = GuzzleHttp\Psr7\modify_request(self::$request, ['set_headers' => ['Authorization' => 'Bearer notAToken']]);
		$res = self::$client->send($tempRequest);

		$this->assertEquals(401, $res->getStatusCode());
	}

	public function testExpiredToken()
	{
		$expiredAuth = API\TokenHandler::encodeAuthToken(self::$username, time() - 10, time() - 5);

		$tempRequest = GuzzleHttp\Psr7\modify_request(self::$request, ['set_headers' => ['Authorization' => "Bearer $expiredAuth"]]);
		$res = self::$client->send($tempRequest);

		$this->assertEquals(401, $res->getStatusCode());
	}

	public function testWrongToken()
	{
		$wrongToken = API\TokenHandler::encodeRefreshToken(self::$username);

		$tempRequest = GuzzleHttp\Psr7\modify_request(self::$request, ['set_headers' => ['Authorization' => "Bearer $wrongToken"]]);
		$res = self::$client->send($tempRequest);

		$this->assertEquals(401, $res->getStatusCode());
	}

	public function testGoodCall()
	{
		$res = self::$client->send(self::$request);

		$this->assertEquals(200, $res->getStatusCode());

		$json = json_decode($res->getBody());
		$this->assertTrue(is_object($json));

		$this->assertTrue(property_exists($json, 'username'));
		$this->assertEquals(self::$username, $json->username);
	}
}

?>
