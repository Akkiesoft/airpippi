<?php
// Air Conditioner Remote Script [Server]
// for WebIOPi (Raspberry Pi GPIO)
// (c) 2013,2015 Akkiesoft.

require_once '/opt/airpippi/config.php';
require_once '/opt/airpippi/bin/rungpio.php';

if (! isset($_POST['command']) ) {
	http_response_code(400);
	exit(1);
}

/* Power(0 = Success, not 0 = Failed) */
if ($_POST['command'] == "power") {
	if (runGPIO()) {
		http_response_code(500);
		exit(1);
	}
	exit(0);
}

/* Timer */
if ($_POST['command'] == "timer") {
	$time = intval($_POST['time']);
	if ($time) {
		exec("echo '/usr/bin/php /opt/airpippi/bin/rungpio.php rungpio' | /usr/bin/at now +" . $time . "minute", $ret2, $status);
		if ($status < 0) {
			http_response_code(400);
			exit(1);
		}
		echo exec('/usr/bin/atq');
		exit(0);
	}
	http_response_code(400);
	exit(1);
}

/* Temperature */
if ($_POST['command'] == "temp") {
	$raw = file_get_contents("/opt/airpippi/temp.json");
	$json = json_decode($raw, true);
	if (! isset($json["data"][0]["temp"])) { exit(1); }
	echo $json["data"][0]["temp"];
	exit(0);
}

http_response_code(400);
exit(1);
?>
