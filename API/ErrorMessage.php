<?php
namespace API;

class ErrorMessage implements \JsonSerializable
{
	private $message;

	public function __construct($message)
	{
		$this->message = $message;
	}

	public function jsonSerialize()
	{
		return get_object_vars($this);
	}
}

?>
