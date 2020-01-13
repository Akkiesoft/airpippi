<?php
include("../html_template.php");
htmlhead("/");
?>
    <div class="container" role="main">

        <div class="page-header">
            <h1><span class="glyphicon glyphicon-scale" aria-hidden="true"></span> 現在の室温</h1>
        </div>
        <h2><span id="temp_icon" class="glyphicon glyphicon-hourglass" aria-hidden="true"></span> <span id="temp"></span> </h2>


        <div class="page-header">
            <h1><span class="glyphicon glyphicon-off" aria-hidden="true"></span> 電源操作</h1>
        </div>
        <div id="ret_power" class="hidden" role="alert">電源操作に成功しました</div>
        <p><button name="power" class="btn btn-default" onclick="power();return false">電源を操作する</button></p>


        <div class="page-header">
            <h1><span class="glyphicon glyphicon-time" aria-hidden="true"></span> タイマー</h1>
        </div>
	<div id="ret_timer" class="hidden" role="alert"></div>
        <p><form method="post" action="/">
          <div class="input-group">
            <input type="number" min="1" name="time" id="time" class="form-control" placeholder="分単位で時間を指定...">
            <span class="input-group-btn">
              <button type="submit" name="timerset" class="btn btn-default" onclick="timer();return false">セット</button>
            </span>
          </div>
        </form></p>

    </div>
    <!-- iOSのボタンがニョキッと出てくるのを回避 -->
    <br><br><br>

    <script src="js/airpippi.js"></script>
    <script>airpippi_temp();</script>

<?php htmlfoot(); ?>
