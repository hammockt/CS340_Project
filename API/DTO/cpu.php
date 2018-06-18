<?php

class CPU implements JsonSerializable
{
	private $partID;
	private $cores;
	private $threads;
	private $socket;
	private $clockSpeed;
	private $manufacturer;
	private $family;
	private $supportedChipsets;
	private $name;

	public function __construct($array)
	{
		$this->partID = $array['partID'];
		$this->cores = (integer)$array['cores'];
		$this->threads = (integer)$array['threads'];
		$this->socket = $array['socket'];
		$this->clockSpeed = (float)$array['clockSpeed'];
		$this->manufacturer = $array['manufacturer'];
		$this->family = $array['family'];
		$this->supportedChipsets = explode(',', $array['supportedChipsets']);

		$this->name = $array['name'];
	}

	public function jsonSerialize()
	{
		return get_object_vars($this);
	}
}

?>
