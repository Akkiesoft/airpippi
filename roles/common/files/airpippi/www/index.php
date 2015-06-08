<?php
// Air Conditioner Remote Script [Server]
// for WebIOPi (Raspberry Pi GPIO)
// (c) 2013,2015 Akkiesoft.

require_once '/opt/airpippi/config.php';
require_once '/opt/airpippi/bin/rungpio.php';

session_start();

/* Power */
if (isset($_POST['power'])) {
	$result = runGPIO();
	if ($result == 0) {
		$_SESSION['power_message'] = '<div class="alert alert-success" role="alert">電源操作に成功しました</div>';
	} else {
		$_SESSION['power_message'] = '<div class="alert alert-danger" role="alert">電源操作に失敗しました</div>';
	}
	header('Location:' . $redirectTo);
	exit();
}

/* Timer */
if (isset($_POST['timerset'])) {
	$time = intval($_POST['time']);
	if ($time) {
		exec("echo '/usr/bin/php /opt/airpippi/bin/rungpio.php rungpio' | /usr/bin/at now +" . $time . "minute");
		$result = exec('/usr/bin/atq', $ret2);
		$_SESSION['timer_message'] = '<div class="alert alert-success" role="alert">'.$time.'分後に電源を操作します<br><small>('.$result.')</small></div>';
	} else {
		$result = 'パラメータが不正です。';
	}
	header('Location:' . $redirectTo);
	exit();
}


/* Temperature */
$raw = file_get_contents("/opt/airpippi/temp.json");
$json = json_decode($raw, true);
if (! isset($json["data"][0]["temp"])) {
	$temp_html = '<div class="alert alert-danger" role="alert">現在の室温が取得できませんでした</div>';
} else {
	$temp = $json["data"][0]["temp"];
	$tempicon = "ok";
	if ($temp < 18) { $tempicon = "remove"; }
	if (17 < $temp) { $tempicon = "exclamation";  }
	if (21 < $temp) { $tempicon = "ok"; }
	if (28 < $temp) { $tempicon = "exclamation";  }
	if (30 < $temp) { $tempicon = "remove"; }

	$temp_html = <<<EOM
<h2><span class="glyphicon glyphicon-{$tempicon}-sign" aria-hidden="true"></span> {$temp}℃</h2>
EOM;

}
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>エアぴっぴ</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-theme.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

    <!-- Fixed navbar -->
    <nav class="navbar navbar-inverse navbar-static-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="/">エアぴっぴ</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="/">メイン</a></li>
            <li><a href="temp.php">室温ログ</a></li>
            <li><a href="settings.php">設定</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container" role="main">

        <div class="page-header">
            <h1><span class="glyphicon glyphicon-scale" aria-hidden="true"></span> 現在の室温</h1>
        </div>
        <?php print $temp_html; ?>

        
        <div class="page-header">
            <h1><span class="glyphicon glyphicon-off" aria-hidden="true"></span> 電源操作</h1>
        </div>
        <?php if (isset($_SESSION['power_message'])) { print $_SESSION['power_message']; unset($_SESSION['power_message']); } ?>
        <p><form method="post" action="/">
          <button type="submit" name="power" class="btn btn-default">電源を操作する</button>
        </form></p>


        <div class="page-header">
            <h1><span class="glyphicon glyphicon-time" aria-hidden="true"></span> タイマー</h1>
        </div>
        <?php if (isset($_SESSION['timer_message'])) { print $_SESSION['timer_message']; unset($_SESSION['timer_message']); } ?>
        <p><form method="post" action="/">
          <div class="input-group">
            <input type="text" name="time" class="form-control" placeholder="分単位で時間を指定...">
            <span class="input-group-btn">
              <button type="submit" name="timerset" class="btn btn-default">セット</button>
            </span>
          </div>
        </form></p>

    </div>


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
