<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json");

require_once 'phpUtilities.php';
require_once 'restUtilities.php';

//make sure they are using this endpoint
$httpMethods = ["GET"];
enforceHttpMethods($httpMethods);

$data = getHttpData();

$requiredKeys = ['Case', 'CPU', 'CPUCooler', 'GraphicsCard', 'Motherboard', 'PowerSupply', 'RAM', 'Storage'];
$optionalKeys = [];
enforceKeys($data, $requiredKeys, $optionalKeys);
enforceNonEmptyKeys($data, $requiredKeys);

$Case         = $data['Case'];
$CPU          = $data['CPU'];
$CPUCooler    = $data['CPUCooler'];
$GraphicsCard = $data['GraphicsCard'];
$Motherboard  = $data['Motherboard'];
$PowerSupply  = $data['PowerSupply'];
$RAM          = $data['RAM'];
$Storage      = $data['Storage'];

//validateString($partID);

$jsonArray = array();

if(count($Case) > 1)
	array_push($jsonArray, '<div class="alert alert-danger">You have one Case for the build</div>');

if(count($Case) == 1 && count($Motherboard) > 1)
	array_push($jsonArray, '<div class="alert alert-danger">Your Case only supports one Motherboard</div>');

if(strpos($Case[0]['formFactor'], $Motherboard[0]['formFactor']) === false)
	array_push($jsonArray, '<div class="alert alert-danger">Your Case does not support your Motherboard\'s form factor</div>');

if(count($CPU) > 1)
	array_push($jsonArray, '<div class="alert alert-danger">Your Motherboard only supports one CPU</div>');

if($CPU[0]['socket'] !== $Motherboard[0]['socket'])
	array_push($jsonArray, '<div class="alert alert-danger">Your CPU uses a different socket than your Motherboard</div>');

if(strpos($CPU[0]['supportedChipsets'], $Motherboard[0]['chipset']) === false)
	array_push($jsonArray, '<div class="alert alert-danger">Your CPU doesn\'t support your Motherboard\'s chipset</div>');

if(count($CPUCooler) > 1)
	array_push($jsonArray, '<div class="alert alert-danger">Your Motherboard doesn\'t support multiple CPU Coolers</div>');

if(strpos($CPUCooler[0]['supportedSockets'], $Motherboard[0]['socket']) === false)
	array_push($jsonArray, '<div class="alert alert-danger">Your CPU Cooler doesn\'t support your Motherboard\'s socket</div>');

$totalRAM = 0;
$totalSlots = 0;
foreach($RAM as $ram)
{
	$totalRAM += $ram['size'];
	$totalSlots += $ram['sticks'];
	if($Motherboard[0]['ramType'] !== $ram['type'] || $Motherboard[0]['ramModuleType'] !== $ram['moduleType'])
		array_push($jsonArray, '<div class="alert alert-danger">Your RAM is uncompatible with your Motherboard</div>');
}

if($totalRAM > $Motherboard[0]['maxRam'])
	array_push($jsonArray, '<div class="alert alert-danger">Your have more RAM than your system can support</div>');

if($totalSlots > $Motherboard[0]['ramSlots'])
	array_push($jsonArray, '<div class="alert alert-danger">Your have more RAM sticks than Motherboard slots</div>');

for($i = 1; $i < count($RAM); $i++)
{
	if($RAM[$i]['partID'] !== $RAM[$i-1]['partID'])
		array_push($jsonArray, '<div class="alert alert-warning">Mixing different RAM can cause system instability</div>');
}

if(count($PowerSupply) > 1)
	array_push($jsonArray, '<div class="alert alert-danger">Your Case does not support more than one Power Supply</div>');

if($CPUCooler[0]['height'] > $Case[0]['cpuCoolerClearance'])
	array_push($jsonArray, '<div class="alert alert-danger">Your CPU Cooler is too tall for your Case</div>');

printf("%s", json_encode($jsonArray));

?>
