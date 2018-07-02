<?php
namespace API\RequestHandler\AuthorizedRequest\BearerAuthRequest;

require_once 'vendor/autoload.php';

use \API\RequestHandler\AuthorizedRequest\AuthorizedRequest;
use \API\ErrorMessage;

abstract class BearerAuthRequest extends AuthorizedRequest
{
	protected $bearerField;

	public function authorize($request, $response)
	{
		$authHeader = $request->getHeader('Authorization')[0];
		if($authHeader === null || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches))
		{
			$jsonArray = array();
			$jsonArray[] = new ErrorMessage('Bearer Authorization is required');
			return $response->withStatus(403)
			                ->withJson($jsonArray);
		}

		$this->bearerField = $matches[1];

		return null;
	}
}

?>

