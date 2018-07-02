<?php
namespace API\RequestHandler\AuthorizedRequest\BasicAuthRequest;

require_once 'vendor/autoload.php';

use \API\RequestHandler\AuthorizedRequest\AuthorizedRequest;
use \API\ErrorMessage;

abstract class BasicAuthRequest extends AuthorizedRequest
{
	protected $username;
	protected $password;

	public function authorize($request, $response)
	{
		$this->username = $request->getHeader('PHP_AUTH_USER')[0];
		$this->password = $request->getHeader('PHP_AUTH_PW')[0];

		if($this->username === null || $this->password === null)
		{
			$jsonArray = array();
			$jsonArray[] = new ErrorMessage('Basic Authorization is required');
			return $response->withStatus(403)
			                ->withJson($jsonArray);
		}

		return null;
	}
}

?>
