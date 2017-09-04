<?php
// Air Conditioner Remote Script [Server]
// for WebIOPi (Raspberry Pi GPIO)
// (c) 2013,2015 Akkiesoft.

$redirectTo     = '/';
$webiopi_host   = 'localhost:8000';
$webiopi_user   = 'AirPippi';
$webiopi_passwd = 'A1rp1pp1';
$webiopi_gpio   = '17';

$temp_json_path = '/opt/airpippi/temp.json';
$joblist_json_path = '/opt/airpippi/joblist.json';
$twitter_auth_file = '/opt/airpippi/twitterauth.json';

$raw = file_get_contents('/opt/airpippi/twitterapp.json');
$twapp = json_decode($raw);
$consumer_key    = $twapp->consumer_key;
$consumer_secret = $twapp->consumer_secret;

?>