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

include("../html_template.php");
htmlhead("temp.php");
?>


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

<?php htmlfoot(); ?>
