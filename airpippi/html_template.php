<?php
$pages = array(
  '/' => "メイン",
  'temp.php' => "室温ログ",
  'job.php' => "ジョブ管理",
  'settings.php' => "設定",
);

function generate_menu($page) {
  global $pages;
  foreach($pages as $link => $title) {
    $active = ($page == $link) ? ' class="active"' : '';
    $menu .= '            <li' . $active . '>';
    $menu .= '<a href="' .$link. '">' . $title . "</a></li>\n";
  }
  $html = <<<EOM
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
{$menu}
          </ul>
        </div><!--/.nav-collapse -->
EOM;
    print $html;
}

function htmlhead($page) {
  global $pages;
  $title = ($page) ? $pages[$page]." - " : "";
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title><?php print $title; ?>エアぴっぴ</title>

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
<?php generate_menu($page); ?>
      </div>
    </nav>
<?php
}
function htmlfoot() {
?>
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-2.2.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
<?php
}
