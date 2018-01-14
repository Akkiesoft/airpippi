$("#newjobform").submit(function(){
  // 曜日以外ブラウザでやってくれているけど一応やる
  if ($("input[name='new-name']").val() == '') {
    alert('<?php print $msg_none_name; ?>');
    return false;
  }
  var hour = $("input[name='new-hour']").val();
  if (hour < 0 && 23 < hour) {
    alert('<?php print $msg_invalid_hour; ?>');
    return false;
  }
  var min = $("input[name='new-min']").val();
  if (min < 0 && 23 < min) {
    alert('<?php print $msg_invalid_min; ?>');
    return false;
  }
  if (hour == "" || min == "") {
    alert("<?php print $msg_none_time; ?>");
    return false;
  }
  if (! $("#dow-grp :checkbox:checked").length) {
    alert("<?php print $msg_dow_require_least_one; ?>");
    return false;
  }
});

$(".jobdelete").click(function(){
  return confirm("ジョブを削除しても良いですか？");
});