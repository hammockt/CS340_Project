<?php
namespace API\DTO;

class CPU implements \JsonSerializable
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

	public function __construct()
	{
		$this->supportedChipsets = explode(',', $this->supportedChipsets);

		settype($this->cores, "integer");
		settype($this->threads, "integer");
		settype($this->clockSpeed, "float");
	}

	public function jsonSerialize()
	{
		return get_object_vars($this);
	}

	public static function fetch($statement)
	{
		$statement->setFetchMode(\PDO::FETCH_CLASS, static::class);

		$jsonArray = array();
		while($cpu = $statement->fetch())
			$jsonArray[] = $cpu;

		return $jsonArray;
	}
}

?>
