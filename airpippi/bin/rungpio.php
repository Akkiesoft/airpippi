<?php

require_once 'HTTP/Request2.php';
require_once '/opt/airpippi/config.php';

function runGPIO() {
	global $webiopi_host, $webiopi_user, $webiopi_passwd, $webiopi_gpio;

	try {
		$req = new HTTP_Request2(
			'http://'.$webiopi_host.'/GPIO/'.$webiopi_gpio.'/value/1',
			HTTP_Request2::METHOD_POST
		);
		$req->setAuth($webiopi_user, $webiopi_passwd, HTTP_Request2::AUTH_BASIC);
		$response = $req->send()->getStatus();
		if ($response == 200) {
			usleep(200000);
			$req = new HTTP_Request2(
				'http://'.$webiopi_host.'/GPIO/'.$webiopi_gpio.'/value/0',
				HTTP_Request2::METHOD_POST
			);
			$req->setAuth($webiopi_user, $webiopi_passwd, HTTP_Request2::AUTH_BASIC);
			$response = $req->send()->getStatus();
			return 0;
		}
	} catch (Exception $e) {
		return 1;
	}
	return 1;
}

/* for AT command. */
if (isset($argv[1]) && $argv[1] == 'rungpio') {
	runGPIO();
	exit();
}

?>