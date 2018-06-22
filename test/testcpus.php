<?php
require_once 'vendor/autoload.php';

class Testcpus extends PHPUnit_Framework_TestCase
{
	private static $client;
	private static $partID = 'BX80684I78700K';
	private static $name = 'i7-8700K';
	private static $manufacturer = 'Intel';
	private static $socket = 'LGA1151';
	private static $family = 'Coffee Lake-S';
	private static $cores = 6;
	private static $threads = 12;
	private static $clockSpeed = 3.7;

	public static function setUpBeforeClass()
	{
		self::$client = new GuzzleHttp\Client(['base_uri' => 'https://web.engr.oregonstate.edu/~hammockt/cs340/Project/Dev/API/']);
	}

	public function testPlainCall()
	{
		$res = self::$client->request('GET', 'cpus', [
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
		$this->assertTrue(property_exists($jsonArray[0], 'supportedChipsets'));
	}

	public function testBadHttpMethod()
	{
		$res = self::$client->request('POST', 'cpus', [
			'http_errors' => false
		]);

		$this->assertEquals(405, $res->getStatusCode());
	}

	public function testBadOptionalKey()
	{
		$res = self::$client->request('GET', 'cpus?thisIsABadKey=test', [
			'http_errors' => false
		]);

		$this->assertEquals(400, $res->getStatusCode());
	}

	public function testSearchPartID()
	{
		$res = self::$client->request('GET', 'cpus?partID=' . self::$partID, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		$this->assertEquals(self::$partID, $jsonArray[0]->partID);
	}

	public function testSearchName()
	{
		$res = self::$client->request('GET', 'cpus?name=' . self::$name, [
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
		$res = self::$client->request('GET', 'cpus?manufacturer=' . self::$manufacturer, [
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
		$res = self::$client->request('GET', 'cpus?socket=' . self::$socket, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		$this->assertEquals(self::$socket, $jsonArray[0]->socket);
	}

	public function testSearchFamily()
	{
		$res = self::$client->request('GET', 'cpus?family=' . self::$family, [
			'http_errors' => false
		]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertTrue(is_array($jsonArray));
		$this->assertTrue(count($jsonArray) > 0);

		$this->assertEquals(self::$family, $jsonArray[0]->family);
	}

	public function testMinCores()
	{
		$res = self::$client->request('GET', 'cpus?minCores=' . self::$cores, [
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
		$res = self::$client->request('GET', 'cpus?maxCores=' . self::$cores, [
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
		$res = self::$client->request('GET', 'cpus?minThreads=' . self::$threads, [
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
		$res = self::$client->request('GET', 'cpus?maxThreads=' . self::$threads, [
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
		$res = self::$client->request('GET', 'cpus?minClockSpeed=' . self::$clockSpeed, [
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
		$res = self::$client->request('GET', 'cpus?maxClockSpeed=' . self::$clockSpeed, [
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
