<?php
require_once 'vendor/autoload.php';

class TestRefresh extends PHPUnit_Framework_TestCase
{
	private static $client;
	private static $request;
	private static $username = 'test@testing.com';

	public static function setUpBeforeClass()
	{
		$refreshToken = API\TokenHandler::encodeRefreshToken(self::$username);

		$requestOptions =
		[
			'Authorization' => "Bearer $refreshToken",
			'Content-Type' => 'application/x-www-form-urlencoded'
		];

		self::$client = new GuzzleHttp\Client([ 'http_errors' => false ]);
		self::$request = new GuzzleHttp\Psr7\Request('POST', 'https://web.engr.oregonstate.edu/~hammockt/cs340/Project/Dev/API/token/refresh', $requestOptions);
	}

	public function testExpiredToken()
	{
		$expiredRefresh = API\TokenHandler::encodeRefreshToken(self::$username, time() - 10, time() - 5);

		$tempRequest = GuzzleHttp\Psr7\modify_request(self::$request, ['set_headers' => ['Authorization' => "Bearer $expiredRefresh"]]);
		$res = self::$client->send($tempRequest);

		$this->assertEquals(401, $res->getStatusCode());
	}

	public function testWrongToken()
	{
		$wrongToken = API\TokenHandler::encodeAuthToken(self::$username);

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

		$this->assertTrue(property_exists($json, 'authToken'));

		$this->assertStringMatchesFormat('%s.%s.%s', $json->authToken);

		$this->assertNotNull(API\TokenHandler::verifyToken($json->authToken, 'auth'));
	}
}

?>
