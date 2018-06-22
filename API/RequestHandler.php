<?php
namespace API;

require_once 'vendor/autoload.php';

abstract class RequestHandler
{
	abstract protected function processRequest($request, $response, $input);

	public function __invoke($request, $response, $args)
	{
		$input = ($request->getMethod() === 'GET')? $request->getQueryParams(): $request->getParsedBody();

		return $this->processRequest($request, $response, $input);
	}
}

?>
