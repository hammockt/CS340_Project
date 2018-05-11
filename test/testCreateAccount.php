<?php
require_once 'vendor/autoload.php';

class TestCreateAccount extends PHPUnit_Framework_TestCase
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

	public function testDuplicateCreation()
	{
		$res = self::$client->request('POST', 'createAccount', [
			GuzzleHttp\RequestOptions::JSON => self::goodInput(),
			'http_errors' => false
		]);

		$this->assertEquals(409, $res->getStatusCode());
	}

	public function testBadPassword()
	{
		$data = [
			'username' => 'thisIsProbablyGoingToBeUnique',
			'password' => 'bad'
		];

		$res = self::$client->request('POST', 'createAccount', [
			GuzzleHttp\RequestOptions::JSON => $data,
			'http_errors' => false
		]);

		$this->assertEquals(409, $res->getStatusCode());
	}
}

?>
