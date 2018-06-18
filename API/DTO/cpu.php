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
		$this->cores = $array['cores'];
		$this->threads = $array['threads'];
		$this->socket = $array['socket'];
		$this->clockSpeed = $array['clockSpeed'];
		$this->manufacturer = $array['manufacturer'];
		$this->family = $array['family'];
		$this->supportedChipsets = explode(',', $array['supportedChipsets']);

		$this->name = $array['name'];

		settype($this->cores, "integer");
		settype($this->threads, "integer");
		settype($this->clockSpeed, "float");
	}

	public function jsonSerialize()
	{
		return get_object_vars($this);
	}
}

?>
