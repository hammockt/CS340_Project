<?php

function getCaseName($data)
{
	$nameID = ($data['model'] !== null)? $data['model']: $data['partID'];

	return "${data['manufacturer']} $nameID";
}

function getCPUName($data)
{
	return $data['name'];
}

function getCPUCoolerName($data)
{
	$nameID = ($data['model'] !== null)? $data['model']: $data['partID'];

	return "${data['manufacturer']} $nameID";
}

function getGraphicsCardName($data)
{
	$nameID = ($data['series'] !== null)? $data['series']: $data['gpuChipset'];

	return "${data['manufacturer']} $nameID";
}

function getMotherboardName($data)
{
	return "${data['manufacturer']} ${data['partID']}";
}

function getPowerSupplyName($data)
{
	$nameID = ($data['series'] !== null)? $data['series']: $data['partID'];

	return "${data['manufacturer']} $nameID";
}

function getRAMName($data)
{
	$nameID = $data['manufacturer'];
	if($data['series'] !== null)
	{
		$nameID .= " ${data['series']}";
	}

	return $nameID;
}

function getStorageName($data)
{
	return "${data['manufacturer']} ${data['series']}";
}

?>
