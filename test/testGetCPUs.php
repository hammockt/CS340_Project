<?php
require_once 'vendor/autoload.php';

class TestGetCPUs extends PHPUnit_Framework_TestCase
{
	private static $client;
	private static $name = 'i7-8700K';
	private static $manufacturer = 'Intel';
	private static $socket = 'LGA1151';
	private static $cores = 6;
	private static $threads = 12;
	private static $clockSpeed = 3.7;

	public static function setUpBeforeClass()
	{
		self::$client = new GuzzleHttp\Client(['base_uri' => 'https://web.engr.oregonstate.edu/~hammockt/cs340/Project/Dev/API/']);
	}

	public function testPlainCall()
	{
		$res = self::$client->request('GET', 'getCPUs', [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		$this->assertTrue(property_exists($jsonArray[0], 'partID'));
		$this->assertTrue(property_exists($jsonArray[0], 'cores'));
		$this->assertTrue(property_exists($jsonArray[0], 'threads'));
		$this->assertTrue(property_exists($jsonArray[0], 'name'));
		$this->assertTrue(property_exists($jsonArray[0], 'socket'));
		$this->assertTrue(property_exists($jsonArray[0], 'clockSpeed'));
		$this->assertTrue(property_exists($jsonArray[0], 'manufacturer'));
		$this->assertTrue(property_exists($jsonArray[0], 'family'));
	}

	public function testBadHttpMethod()
	{
		$res = self::$client->request('POST', 'getCPUs', [
			'http_errors' => false
		]);

		$this->assertEquals(405, $res->getStatusCode());
	}

	public function testBadOptionalKey()
	{
		$res = self::$client->request('GET', 'getCPUs?thisIsABadKey=test', [
			'http_errors' => false
		]);

		$this->assertEquals(400, $res->getStatusCode());
	}

	public function testSearchName()
	{
		$res = self::$client->request('GET', 'getCPUs?name=' . self::$name, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		$this->assertEquals(self::$name, $jsonArray[0]->name);
	}

	public function testSearchManufacturer()
	{
		$res = self::$client->request('GET', 'getCPUs?manufacturer=' . self::$manufacturer, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		$this->assertEquals(self::$manufacturer, $jsonArray[0]->manufacturer);
	}

	public function testSearchSocket()
	{
		$res = self::$client->request('GET', 'getCPUs?socket=' . self::$socket, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		$this->assertEquals(self::$socket, $jsonArray[0]->socket);
	}

	public function testMinCores()
	{
		$res = self::$client->request('GET', 'getCPUs?minCores=' . self::$cores, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		foreach($jsonArray as $cpu)
		{
			$this->assertGreaterThanOrEqual(self::$cores, $cpu->cores);
		}
	}

	public function testMaxCores()
	{
		$res = self::$client->request('GET', 'getCPUs?maxCores=' . self::$cores, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		foreach($jsonArray as $cpu)
		{
			$this->assertLessThanOrEqual(self::$cores, $cpu->cores);
		}
	}

	public function testMinThreads()
	{
		$res = self::$client->request('GET', 'getCPUs?minThreads=' . self::$threads, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		foreach($jsonArray as $cpu)
		{
			$this->assertGreaterThanOrEqual(self::$threads, $cpu->threads);
		}
	}

	public function testMaxThreads()
	{
		$res = self::$client->request('GET', 'getCPUs?maxThreads=' . self::$threads, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		foreach($jsonArray as $cpu)
		{
			$this->assertLessThanOrEqual(self::$threads, $cpu->threads);
		}
	}

	public function testMinClockSpeed()
	{
		$res = self::$client->request('GET', 'getCPUs?minClockSpeed=' . self::$clockSpeed, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		foreach($jsonArray as $cpu)
		{
			$this->assertGreaterThanOrEqual(self::$clockSpeed, $cpu->clockSpeed);
		}
	}

	public function testMaxClockSpeed()
	{
		$res = self::$client->request('GET', 'getCPUs?maxClockSpeed=' . self::$clockSpeed, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		foreach($jsonArray as $cpu)
		{
			$this->assertLessThanOrEqual(self::$clockSpeed, $cpu->clockSpeed);
		}
	}
}
?>
