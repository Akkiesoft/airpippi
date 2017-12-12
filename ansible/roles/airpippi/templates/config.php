<?php
// Air Conditioner Remote Script [Server]
// (c) 2013,2015,2017 Akkiesoft.

$redirectTo     = '/';
$gpio           = '{{ gpio }}';
$ds18b20        = '{{ ds18b20_id }}';

$temp_json_path = '/opt/airpippi/temp.json';
$joblist_json_path = '/opt/airpippi/joblist.json';
$twitter_auth_file = '/opt/airpippi/twitterauth.json';

$raw = file_get_contents('/opt/airpippi/twitterapp.json');
$twapp = json_decode($raw);
$consumer_key    = $twapp->consumer_key;
$consumer_secret = $twapp->consumer_secret;

?>
