<?php
require_once 'vendor/autoload.php';

class TestAuthorize extends PHPUnit_Framework_TestCase
{
	private static $client;
	private static $request;

	public static function setUpBeforeClass()
	{
		$requestOptions =
		[
			'Authorization' => "Basic " . base64_encode('test@testing.com:test'),
			'Content-Type' => 'application/x-www-form-urlencoded'
		];

		self::$client = new GuzzleHttp\Client([ 'http_errors' => false ]);
		self::$request = new GuzzleHttp\Psr7\Request('POST', 'https://web.engr.oregonstate.edu/~hammockt/cs340/Project/Dev/API/token/authorize', $requestOptions);
	}

	public function testNoContentType()
	{
		$tempRequest = GuzzleHttp\Psr7\modify_request(self::$request, ['remove_headers' => ['Content-Type']]);
		$res = self::$client->send($tempRequest);

		$this->assertEquals(403, $res->getStatusCode());
	}

	public function testBadContentType()
	{
		$tempRequest = GuzzleHttp\Psr7\modify_request(self::$request, ['set_headers' => ['Content-Type' => 'bad']]);
		$res = self::$client->send($tempRequest);

		$this->assertEquals(415, $res->getStatusCode());
	}

	public function testUnknownInput()
	{
		$res = self::$client->send(self::$request, [ 'form_params' => ['thisIsABad' => 'key'] ]);

		$this->assertEquals(403, $res->getStatusCode());
	}

	public function testGoodCall()
	{
		var_dump(self::$request->getHeaders());
		$res = self::$client->send(self::$request);

		echo $res->getBody();
		$this->assertEquals(200, $res->getStatusCode());

		$json = json_decode($res->getBody());
		$this->assertTrue(is_object($json));

		$this->assertTrue(property_exists($json, 'refreshToken'));
		$this->assertTrue(property_exists($json, 'authToken'));

		$this->assertStringMatchesFormat('%s.%s.%s', $json->refreshToken);
		$this->assertStringMatchesFormat('%s.%s.%s', $json->authToken);

		$this->assertNotNull(API\TokenHandler::verifyToken($json->refreshToken, 'refresh'));
		$this->assertNotNull(API\TokenHandler::verifyToken($json->authToken, 'auth'));
	}
}

?>
