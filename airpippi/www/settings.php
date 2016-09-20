<?php
require_once 'HTTP/OAuth/Consumer.php';
require_once '/opt/airpippi/config.php';

$consumer = new HTTP_OAuth_Consumer($consumer_key, $consumer_secret);
$http_request = new HTTP_Request2();
$http_request->setConfig('ssl_verify_peer', false);
$consumer_request = new HTTP_OAuth_Consumer_Request;
$consumer_request->accept($http_request);
$consumer->accept($consumer_request);
$pin = "";

/* Input PIN */
if (isset($_POST['pin'])) {
	$pin = $_POST['pin'];
	$oauth_token = $_POST['token'];
	$oauth_secret = $_POST['secret'];

	$consumer->setToken($oauth_token);
	$consumer->setTokenSecret($oauth_secret);
	$consumer->getAccessToken('https://api.twitter.com/oauth/access_token', $pin);

	$data['access_token']  = $consumer->getToken();
	$data['access_secret'] = $consumer->getTokenSecret();

	/* screen_nameの取得(HTTP/OAuthではgetAccessTokenのついでにscreen_nameが取れないのつらい) */
	$consumer->setToken($data['access_token']);
	$consumer->setTokenSecret($data['access_secret']);
	$response = $consumer->sendRequest('https://api.twitter.com/1.1/account/settings.json');
	$body = json_decode($response->getBody());
	$data['airpippi_twit'] = $body->screen_name;

	$json = json_encode($data);
	file_put_contents($twitter_auth_file, $json);
	header('Location: settings.php');
	exit();
}

/* Unregister */
if (isset($_POST['unregister'])) {
	file_put_contents($twitter_auth_file, "");
	header('Location: settings.php');
	exit();
}

$twauth = file_get_contents($twitter_auth_file);
$data = json_decode($twauth, 1);
$tw_stat = "未設定";
if (isset($data['airpippi_twit'])) {
	$tw_stat = "@{$data['airpippi_twit']} と連携設定済み";
}

/* mention accounts */
if (isset($_POST['mention'])) {
	$data['accounts'] = str_replace("\n", " ", $_POST['accounts']);
	$json = json_encode($data);
	file_put_contents($twitter_auth_file, $json);
	header('Location: settings.php');
	exit();
}

/* Before register */
if ($pin == "" && ! isset($data['airpippi_twit'])) {
	$consumer->getRequestToken('https://api.twitter.com/oauth/request_token');
	$token = $consumer->getToken();
	$secret = $consumer->getTokenSecret();
	$url='https://api.twitter.com/oauth/authorize?oauth_token=' . $token;
}

?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>設定 - エアぴっぴ</title>

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
            <li><a href="temp.php">室温ログ</a></li>
            <li class="active"><a href="settings.php">設定</a></li>
          </ul>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container" role="settings">

        <div class="page-header">
            <h1><span class="glyphicon glyphicon-link" aria-hidden="true"></span> Twitter連携</h1>
        </div>
        <p>Twitter連携ステータス: <b><?php print $tw_stat; ?></b></p>

<?php if (! isset($data['airpippi_twit'])) {?>
        <p>Twitter連携のしかた</p>
        <ul>
          <li><a href="<?php print $url; ?>" target="_blank">このリンクをクリックしてTwitterの認証ページに移動します</a></li>
          <li>認証した後に表示されるPINコードを下にあるボックスに入力して、連携ボタンを押します。</li>
        </ul>
          
        <form method="post" action="settings.php">
          <div class="input-group">
            <input type="text" name="pin" class="form-control" placeholder="PINコードを入力...">
            <span class="input-group-btn">
              <button type="submit" class="btn btn-default">連携する</button>
            </span>
          </div>
          <input name="token" value="<?php print $token; ?>" type="hidden">
          <input name="secret" value="<?php print $secret; ?>" type="hidden">
        </form>
<?php } else { ?>
        <h2>メンションを受け付けるアカウント</h2>
        <form method="post" action="settings.php">
          <div class="form-group">
            <textarea class="form-control" rows="3" name="accounts" placeholder="@example (複数の場合はスペースで区切ります)"><?php print $data["accounts"]; ?></textarea>
          </div>
          <div class="form-group">
            <button type="submit" name="mention" class="btn btn-default">設定</button>
          </div>
        </form>

        <h2>Twitter連携の使い方</h2>
        <p>「メンションを受け付けるアカウント」で設定したTwitterアカウントから、Twitter連携を行ったTwitterアカウントに対して、メンションを送ってください。</p>
        <h3>操作のためのキーワード</h3>
        <dl class="dl-horizontal">
          <dt>電源</dt><dd>電源をすぐに操作します</dd>
          <dt>室温</dt><dd>室温を返信してくれます。</dd>
          <dt>タイマー</dt><dd>タイマー実行します。このキーワードには<strong>XX分</strong>といったように時間を合わせて記述してください。</dd>
        </dl>
        <h3>実行例</h3>
        <p>電源を操作するとき:</p>
        <pre>@<?php print $data['airpippi_twit'] ?> 電源を操作</pre>
        <p>電源を操作して、室温を通知してもらうとき:</p>
        <pre>@<?php print $data['airpippi_twit'] ?> 電源を操作して室温を教えて</pre>
        <p>電源を操作して、さらに30分後にタイマー実行してもらうとき:</p>
        <pre>@<?php print $data['airpippi_twit'] ?> 電源を操作して30分後にタイマー</pre>

        <h2>連携情報を削除する</h2>
        <p>Twitter連携情報を削除すると、@<?php print $data['airpippi_twit'] ?>にメンションを送っても操作ができなくなります。違うアカウントと連携したくなった時とかに使ってください。</p>
        <form method="post" action="settings.php">
          <div class="form-group">
            <button type="submit" name="unregister" class="btn btn-danger">連携情報を削除する</button>
          </div>
        </form>

<?php } ?>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-2.2.4.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
  </body>
</html>
