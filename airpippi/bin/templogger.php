<?php
require_once '/opt/airpippi/config.php';

$raw = file('/sys/bus/w1/devices/' . $ds18b20 . '/w1_slave', FILE_IGNORE_NEW_LINES);
$temp = explode('=', $raw[1]);
$temp = round($temp[1] / 1000, 2);

$item = array(
	'time' => date("c"),
	'temp' => $temp
);

$raw = file_get_contents($temp_json_path);
$json = json_decode($raw, 1);
if (count($json["data"])) {
	array_unshift($json["data"], $item);
} else {
	$json["data"][] = $item;
}
array_splice($json["data"], 60);

$raw = json_encode($json);
file_put_contents($temp_json_path, $raw);
?>