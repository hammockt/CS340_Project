<?php
namespace API\DTO;

class Build implements \JsonSerializable
{
	private $buildID;
	private $name;
	private $shared;

	public function __construct()
	{
		settype($this->buildID, "integer");
		settype($this->shared, "boolean");
	}

	public function jsonSerialize()
	{
		return get_object_vars($this);
	}

	public static function fetch($statement)
	{
		$statement->setFetchMode(\PDO::FETCH_CLASS, static::class);

		$jsonArray = array();
		while($build = $statement->fetch())
			$jsonArray[] = $build;

		return $jsonArray;
	}
}

?>
