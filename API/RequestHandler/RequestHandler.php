<?php
namespace API\RequestHandler;

require_once 'vendor/autoload.php';

use \Resources\TypedInput;
use \API\ErrorMessage;

abstract class RequestHandler
{
	protected static $inputKeys = [];

	abstract public function processRequest($request, $response, $input);

	public function __invoke($request, $response, $args)
	{
		$method = $request->getMethod();

		$response = $response->withAddedHeader('Access-Control-Allow-Origin', '*')
			                 ->withAddedHeader('Access-Control-Allow-Methods', $request->getMethod());

		if($method !== 'GET')
		{
			$supportedMedia = ['application/json', 'application/x-www-form-urlencoded', 'application/xml'];
			$response = $response->withAddedHeader('Accept', implode(', ', $supportedMedia));

			if(!$request->hasHeader('Content-Type'))
			{
				return $response->write('Content-Type is required')
					            ->withStatus(403);
			}

			if(!in_array($request->getContentType(), $supportedMedia))
			{
				return $response->withStatus(415);
			}
				
		}

		$input = self::getInput($request, $args);

		$result = self::handleUnknownKeys($input);
		if($result !== null)
		{
			//will not do withJson because we make no assumptions about the message type
			//it could be html, json, or xml. We just need a string
			return $response->write($result)
			                ->withHeader('Content-Type', 'application/json')
			                ->withStatus(403);
		}

		$result = self::validateKeys($input);
		if($result !== null)
		{
			return $response->write($result)
			                ->withHeader('Content-Type', 'application/json')
			                ->withStatus(403);
		}

		return $this->processRequest($request, $response, $input);
	}

	public static function getInput($request, $args)
	{
		$input = ($request->getMethod() === 'GET')? $request->getQueryParams(): $request->getParsedBody();
		foreach($args as $key => $value)
			$input[$key] = $value;

		return $input;
	}

	public static function handleUnknownKeys(array $input)
	{
		$diff = array_diff_key($input, self::$inputKeys);
		if(count($diff) > 0)
		{
			$jsonArray = array();
			foreach(array_keys($diff) as $key)
				$jsonArray[] = new ErrorMessage("Unknown Key: $key");

			return json_encode($jsonArray);
		}

		return null;
	}

	//need to inadvertly set defaults here as well
	//this is because we will make the assumption that all of the keys in inputKeys exist within input
	//to adhere to this, optional inputs which may or may not have their keys defined, now need them
	public static function validateKeys(array &$input)
	{
		$jsonArray = array();
		foreach(self::$inputKeys as $inputKey => $typedInput)
		{
			if(!isset($input[$inputKey]))
				$input[$inputKey] = null;

			if($typedInput->isRequired && $input[$inputKey] === null)
			{
				$jsonArray[] = new ErrorMessage("Required Key missing: $inputKey");
				continue;
			}

			if(!$typedInput->validate($input[$inputKey]))
				$jsonArray[] = new ErrorMessage("Invalid input for key: $inputKey");
		}

		return (count($jsonArray) === 0)? null: json_encode($jsonArray);
	}
}

?>
