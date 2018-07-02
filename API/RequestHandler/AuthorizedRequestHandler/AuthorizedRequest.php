<?php
namespace API\RequestHandler\AuthorizedRequest;

require_once 'vendor/autoload.php';

use \API\RequestHandler\RequestHandler;

abstract class AuthorizedRequest extends RequestHandler
{
	abstract public function authorize($request, $response);
	abstract public function processAuthorizedRequest($request, $response, $input);

	public function processRequest($request, $response, $input)
	{
		$result = $this->authorize($request, $response);
		if($result !== null)
			return $result;

		return $this->processAuthorizedRequest($request, $response, $input);
	}
}

?>
