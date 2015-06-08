<?php
require_once 'HTTP/Request2.php';
require_once '/opt/airpippi/config.php';

try {
	$req = new HTTP_Request2('http://'.$webiopi_host.'/room/temperature', HTTP_Request2::METHOD_GET);
	$req->setAuth($webiopi_user, $webiopi_passwd, HTTP_Request2::AUTH_BASIC);
	$res = $req->send();
	if ($res->getStatus() == 200) {
		$temp = $res->getBody();
	} else { exit(1); }
} catch (Exception $e) {
	exit(1);
}

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