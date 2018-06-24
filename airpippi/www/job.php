<?php
require_once("../config.php");

$json_raw = file_get_contents($joblist_json_path);
$json = json_decode($json_raw);

$msg_none_name = "ジョブ名を入力してください";
$msg_invalid_hour = "正しい時間を入力してください(時: 0〜23)";
$msg_invalid_min  = "正しい時間を入力してください(分: 0〜59)";
$msg_none_time = "時間を入力してください";
$msg_dow_require_least_one = "曜日は最低一つ選択してください";

$dowlabel_en = array("mon", "tue", "wed", "thu", "fri", "sat", "sun");
$dowlabel = array("月", "火", "水", "木", "金", "土", "日");

$tasklabel = array("電源を操作する");


$changed = "";
if (isset($_POST) && count($_POST)) {
  // 新規タスク
  if (isset($_POST["new"])) {
    $error = "";
    $name  = htmlspecialchars($_POST["new-name"]);
    $hour  = intval($_POST["new-hour"]);
    $min   = intval($_POST["new-min"]);
    foreach($_POST["new-dow"] as $i=>$l) {
      $dow[$i] = ($l) ? true: false;
    }
    // JavaScriptがない人向けチェック
    // なお、ココでチェックにかかる稀有なておくれは救済されないので
    // 再度入力が要求される(手抜き)
    if (! $name) { $error = $msg_none_name; }
    else if ($hour < 0 || 23 < $hour) { $error = $msg_invalid_hour; }
    else if ($min < 0 || 59 < $min) { $error = $msg_invalid_min; }
    else if ($_POST["new-hour"] == "" || $_POST["new-min"] == "") { $error = $msg_none_time; }
    else if (! in_array(true, $dow)) { $error = $msg_dow_require_least_one; }
    else {
      // 追加処理
      $json[] = array(
        'name' => $name,
        'dow'  => $dow,
        'hour' => $hour, 
        'min'  => $min,
        'task' => intval($_POST["new-task"]),
        'enabled' => true
      );
      $changed = "new=" . (count($json) - 1);
    }
  }

  // 既存タスクの有効化
  if (isset($_POST["enable"])) {
    $id = intval($_POST["enable"]);
    $json[$id]->enabled = true;
    $changed = "enable=".$id;
  }
  // 既存タスクの無効化
  else if (isset($_POST["disable"])) {
    $id = intval($_POST["disable"]);
    $json[$id]->enabled = false;
    $changed = "disable=".$id;
  }
  // 既存タスクの削除
  else if (isset($_POST["delete"])) {
    $id = intval($_POST["delete"]);
    $name = urlencode($json[$id]->name);
    unset($json[$id]);
    $json = array_values($json);
    $changed = "delete=".$name;
  }

  if ($changed) {
    $json_raw = json_encode($json, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
    file_put_contents($joblist_json_path, $json_raw);
    header("Location: job.php?".$changed);
    exit(0);
  }
}

$message = "";
if (isset($_GET) && count($_GET)) {
  // 新規タスク作成のメッセージ
  if (isset($_GET['new']) && is_numeric($_GET['new'])) {
    $id = intval($_GET["new"]);
    $message = "ジョブ [{$json[$id]->name}] を作成しました";
  }
  // 既存タスクの有効化のメッセージ
  else if (isset($_GET['enable']) && is_numeric($_GET['enable'])) {
    $id = intval($_GET["enable"]);
    $message = "ジョブ [{$json[$id]->name}] を有効化しました";
  }
  // 既存タスクの無効化のメッセージ
  else if (isset($_GET['disable']) && is_numeric($_GET['disable'])) {
    $id = intval($_GET["disable"]);
    if (isset($json[$id]->name))
      $message = "ジョブ [{$json[$id]->name}] を無効化しました";
  }
  // 既存タスクの削除時のメッセージ
  else if (isset($_GET["delete"])) {
    $name = htmlspecialchars($_GET["delete"]);
    $message = "ジョブ [{$name}] を削除しました";
  }
  // 何らかの不正なパラメータは消えてもらう
  else {
    header("Location: job.php");
    exit(0);
  }
}

// ジョブ一覧のHTML生成
foreach ($json as $id=>$item) {
  $dow = "";
    $i = 0;
    foreach($item->dow as $l) {
        $dow .= ($l === true) ? $dowlabel[$i]." " : "";
        $i++;
    }
    $status = ($item->enabled) ? "success":"default";
    $jobtime = sprintf("%02d:%02d", $item->hour, $item->min);
    $joblist .= <<<EOM
  <div class="panel panel-{$status}">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a data-toggle="collapse" data-parent="#joblist" href="#job{$id}">{$item->name}</a>
      </h4>
    </div>
    <div id="job{$id}" class="panel-collapse collapse">
      <div class="panel-body">
        <form action="job.php" method="post">
        <dl class="dl-horizontal">
          <dt>曜日</dt><dd>{$dow}</dd>
          <dt>時間</dt><dd>{$jobtime}</dd>
          <dt>実行内容</dt><dd>{$tasklabel[$item->task]}</dd>
          <dt>ジョブの有効/無効</dt><dd><div class="btn-group" role="group" aria-label="...">
            <button type="submit" class="btn btn-success btn-sm" name="enable" value="{$id}"><span class="glyphicon glyphicon-play"></span></button>
            <button type="submit" class="btn btn-default btn-sm" name="disable" value="{$id}"><span class="glyphicon glyphicon-stop"></span></button>
            </div></dd>
          <dt>ジョブの削除</dt><dd>
            <button type="submit" class="jobdelete btn btn-danger btn-sm" name="delete" value="{$id}"><span class="glyphicon glyphicon-remove"></span></button></dd>
        </dl>
        </form>
      </div>
    </div>
  </div>


EOM;
}

include("../html_template.php");
htmlhead("job.php");
?>
    <div class="container" role="settings">

      <div class="page-header">
        <h1><span class="glyphicon glyphicon-tasks" aria-hidden="true"></span> ジョブ管理</h1>
      </div>

<?php if ($message || $error) { ?>
      <script>
        function hide_message() {
          document.getElementById("message").className = "hidden";
          history.replaceState("", "", "job.php");
        }
        setTimeout("hide_message();", 5000);
      </script>
<?php } ?>
<?php if ($message) { ?>
      <div id="message" class="alert alert-info" role="alert"><?php print $message; ?></div>
<?php } ?>
<?php if ($error) { ?>
      <div id="message" class="alert alert-danger" role="alert"><?php print $error; ?></div>
<?php } ?>

      <p>
        <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#newjob">新規ジョブ追加</button>
      </p>
      <div id="newjob" class="panel panel-default collapse">
        <form class="panel-body form-horizontal" id="newjobform" method="post" action="job.php">
          <div class="form-group">
            <label for="new-name" class="col-sm-2 control-label">ジョブ名</label>
            <div class="col-sm-10">
              <input id="new-name" name="new-name" class="form-control" placeholder="ジョブ名" required>
            </div>
          </div>
          <div class="form-group">
            <label for="new-dow" class="col-sm-2 control-label">曜日</label>
            <div class="col-sm-10" id="dow-grp">
<?php foreach($dowlabel as $i=>$dow) { ?>
              <input type="hidden" name="new-dow[<?php print $dowlabel_en[$i]; ?>]">
              <label class="checkbox-inline">
                <input type="checkbox" name="new-dow[<?php print $dowlabel_en[$i]; ?>]"><?php print $dow; ?>

              </label>
<?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label for="new-hour" class="col-sm-2 control-label">時間</label>
            <div class="col-sm-2">
              <input id="new-hour" name="new-hour" class="form-control" type="number" min="0" max="23" placeholder="時" required>
              <input id="new-min"  name="new-min"  class="form-control" type="number" min="0" max="59" placeholder="分" required>
            </div>
          </div>
          <div class="form-group">
            <label for="new-task" class="col-sm-2 control-label">実行内容</label>
            <div class="col-sm-10">
              <select name="new-task" class="form-control">
                <option value="0">電源を操作する</option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <button type="submit" class="btn btn-default" name="new" value="^o^"><span class="glyphicon glyphicon-plus" aria-hidden="true">追加</span></button>
            </div>
          </div>
        </form>
      </div>

      <h3>ジョブ一覧</h3>
<div class="panel-group" id="joblist">
<?php print $joblist; ?>
</div>
    </div>
    <!-- iOSのボタンがニョキッと出てくるのを回避 -->
    <br><br><br>

    <script src="js/job.js"></script>
<?php htmlfoot("/"); ?>
