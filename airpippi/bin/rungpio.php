<?php

require_once '/opt/airpippi/config.php';

function runGPIO() {
	global $gpio;

	exec("gpio -g mode " . $gpio . " out");
	exec("gpio -g write " . $gpio . " 1");
	usleep(200000);
	exec("gpio -g write " . $gpio . " 0");
	return 0;
}

/* for AT command. */
if (isset($argv[1]) && $argv[1] == 'rungpio') {
	runGPIO();
	exit();
}

?>