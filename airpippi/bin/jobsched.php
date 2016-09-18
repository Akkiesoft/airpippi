<?php

require_once '/opt/airpippi/config.php';
require_once '/opt/airpippi/bin/rungpio.php';

$json_raw = file_get_contents($joblist_json_path);
$json = json_decode($json_raw);

foreach ($json as $item) {
	# is enabled?
	if (! $item->enabled) continue;

	# check day of week
	$now_dow = strtolower(date("D"));
	if (! $item->dow->$now_dow) continue;

	# check hour and minute
	$jobtime = array(intval($item->hour), intval($item->min));
	$now = array(intval(date("G")), intval(date("i")));
	if ($jobtime !== $now) continue;

	# run a job
	$job = $item->task;
	switch($job){
	case 0:
		# power
		runGPIO();
		break;
	}
}
