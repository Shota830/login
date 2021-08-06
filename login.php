<?php
ini_set('display_errors', "Off");
ini_set('error_reporting', E_ALL ); 
require('/Applications/MAMP/htdocs/twitter /dbconnect.php');
session_start();

if (!empty($_POST)) {
    //ログインの処理
    if ($_POST['email'] != '' && $_POST['password'] != '') {
        $login = $db->prepare('SELECT * FROM members WHERE email=? AND password=?');
        $login->execute(array(
            $_POST['email'],
            sha1($_POST['password'])
        ));
        $member = $login->fetch();

        if ($member) {
            //ログイン成功
            $_SESSION['id'] = $member['id'];
            $_SESSION['time'] = time();

            header('Location: index.php'); exit();
        } else {
            $error['login'] ='failed';
        }
    } else {
        $error['login'] = 'blank';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
    <title>Document</title>
</head>
<body>
    <div class="top">
    <h1>ログインする</h1>
    </div>
    <div id="lead">
        <p>メールアドレスとパスワードを記入してログインしてください</p>
        <p>入会手続きがまだの方はこちらからどうぞ。</p>
        <p>&raquo;<a href="join/">入会手続きをする</a></p>
    </div>
    <form action="" method="post">
        <dl>
            <dt>メールアドレス</dt>
            <dd>
                <input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'], ENT_QUOTES); ?>"/>
                <?php if (isset($_error['login']) && $error['login'] == 'blank'): ?>
                <p class="error">* メールアドレスとパスワードをご記入ください</p>
                <?php endif; ?>
                <?php if (isset($error['login']) && $error['login'] == 'failed'): ?>
                <p class="error">* ログインに失敗しました。正しくご記入ください</p>
                <?php endif; ?>
            </dd>
            <dt>パスワード</dt>
            <dd>
                <input type="password" name="password" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['password'], ENT_QUOTES); ?>" />
            </dd>
            <dt>ログイン情報の記録</dt>
            <dd>
                <input id="save" type="checkbox" name="save" value="on"><label for="save">次回からは自動的にログインする</label>
            </dd>
        </dl>
        <div><input type="submit" value="ログインする" /></div>
    </form>
</body>
</html>

