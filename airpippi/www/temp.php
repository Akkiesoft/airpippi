<?php
$raw = file_get_contents("/opt/airpippi/temp.json");
$json = json_decode($raw);
$temp_raw = array();
$time_raw = array();
if (! isset($json->data) || count($json->data) == 0) {
	$error = '<div class="alert alert-danger" role="alert">室温がまだ一つも記録されていないようです。温度センサーの接続を確認して、もうしばらく待ってみてください。</div>';
}
foreach($json->data as $item) {
	$temp_raw[] = $item->temp;
	$time_raw[] = strftime(
		"%m/%d %H:%M",
		strtotime($item->time)
	);
}
$temp_raw = array_reverse($temp_raw);
$temp = json_encode($temp_raw, JSON_NUMERIC_CHECK);
$time_raw = array_reverse($time_raw);
$time = json_encode($time_raw, JSON_NUMERIC_CHECK);

?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>室温ログ - エアぴっぴ</title>

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
            <li><a href="/">メイン</a></li>
            <li class="active"><a href="temp.php">室温ログ</a></li>
            <li><a href="job.php">ジョブ管理</a></li>
            <li><a href="settings.php">設定</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container" role="settings">

        <div class="page-header">
            <h1><span class="glyphicon glyphicon-stats" aria-hidden="true"></span> 室温ログ</h1>
        </div>
<?php if ($error) { print $error; } else { ?>
        <div style="width:100%;height:400px;"><canvas id="graph"></canvas></div>
        <script src="js/Chart.min.js"></script>
        <script>
var ctx = document.getElementById("graph").getContext("2d");
var temp = {
  labels : <?php print $time; ?>,
  datasets : [{
    backgroundColor: "rgba(151,187,205,0.2)",
    borderColor: "rgba(151,187,205,1)",
    pointRadius: 3.5,
    pointBackgroundColor: "rgba(151,187,205,1)",
    pointBorderColor: "#fff",
    pointHoverBackgroundColor: "#fff",
    pointHoverBorderColor: "rgba(151,187,205,1)",
    data: <?php print $temp; ?>
  }]
};
var options = {
  responsive : true,
  maintainAspectRatio: false,
  animation  : { duration : 0 },
  legend     : { display  : false },
  scales     : { yAxes : [{ ticks: { min: 10, max: 40 } }] }
}
var tempChart = new Chart(ctx, {
  type    : 'line',
  data    : temp,
  options : options
});
        </script>
<?php } ?>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-2.2.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
