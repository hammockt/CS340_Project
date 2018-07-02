<?php
require_once 'vendor/autoload.php';

class Testcpus extends PHPUnit_Framework_TestCase
{
	private static $client;
	private static $request;
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
		self::$client = new GuzzleHttp\Client([ 'http_errors' => false ]);
		self::$request = new GuzzleHttp\Psr7\Request('GET', 'https://web.engr.oregonstate.edu/~hammockt/cs340/Project/Dev/API/cpus');
	}

	public function testPlainCall()
	{
		$res = self::$client->send(self::$request);

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
		$tempRequest = GuzzleHttp\Psr7\modify_request(self::$request, ['method' => 'POST']);
		$res = self::$client->send($tempRequest);

		$this->assertEquals(405, $res->getStatusCode());
	}

	public function testBadKey()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['thisIsABad' => 'key'] ]);

		$this->assertEquals(403, $res->getStatusCode());
	}

	public function testInvalidInput()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['minCores' => 'bad'] ]);

		$this->assertEquals(403, $res->getStatusCode());
	}

	public function testSearchPartID()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['partID' => self::$partID] ]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertEquals(self::$partID, $jsonArray[0]->partID);
	}

	public function testSearchName()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['name' => self::$name] ]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertEquals(self::$name, $jsonArray[0]->name);
	}

	public function testSearchManufacturer()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['manufacturer' => self::$manufacturer] ]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertEquals(self::$manufacturer, $jsonArray[0]->manufacturer);
	}

	public function testSearchSocket()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['socket' => self::$socket] ]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertEquals(self::$socket, $jsonArray[0]->socket);
	}

	public function testSearchFamily()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['family' => self::$family] ]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		$this->assertEquals(self::$family, $jsonArray[0]->family);
	}

	public function testMinCores()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['minCores' => self::$cores]	]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		foreach($jsonArray as $cpu)
		{
			$this->assertGreaterThanOrEqual(self::$cores, $cpu->cores);
		}
	}

	public function testMaxCores()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['maxCores' => self::$cores]	]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		foreach($jsonArray as $cpu)
			$this->assertLessThanOrEqual(self::$cores, $cpu->cores);
	}

	public function testMinThreads()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['minThreads' => self::$threads]	]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		foreach($jsonArray as $cpu)
			$this->assertGreaterThanOrEqual(self::$threads, $cpu->threads);
	}

	public function testMaxThreads()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['maxThreads' => self::$threads] ]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		foreach($jsonArray as $cpu)
			$this->assertLessThanOrEqual(self::$threads, $cpu->threads);
	}

	public function testMinClockSpeed()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['minClockSpeed' => self::$clockSpeed] ]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		foreach($jsonArray as $cpu)
			$this->assertGreaterThanOrEqual(self::$clockSpeed, $cpu->clockSpeed);
	}

	public function testMaxClockSpeed()
	{
		$res = self::$client->send(self::$request, [ 'query' => ['maxClockSpeed' => self::$clockSpeed] ]);

		$this->assertEquals(200, $res->getStatusCode());

		$jsonArray = json_decode($res->getBody());
		foreach($jsonArray as $cpu)
			$this->assertLessThanOrEqual(self::$clockSpeed, $cpu->clockSpeed);
	}
}
?>
