<?php
ini_set('display_errors', "Off");
ini_set('error_reporting', E_ALL ); 
require('/Applications/MAMP/htdocs/twitter /dbconnect.php');
session_start();

if (!empty($_POST)) {  #①!empty($_POST)で受け取る項目が空でないかを確認する
    #エラー項目の確認
    if ($_POST['name'] == '') {
        $error['name'] = 'blank';
    }
    if ($_POST['email'] == '') {
        $error['email'] = 'blank';
    }
    if (strlen($_POST['password']) < 4) {
        $error['password'] = 'length';
    }
    if ($_POST['password'] == '') {
        $error['password'] = 'blank';
    }

    $fileName = $_FILES['image']['name'];
    if (!empty($fileName)) {
        $ext = substr($fileName, -3);
        if ($ext != 'jpg' && $ext != 'gif') {
            $error['image'] = 'type';
        }
    }

    //重複アカウントのチェック
    if (empty($error)) {
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
        $member->execute(array($_POST['email']));
        $record = $member->fetch();
        if ($record['cnt'] > 0) {
            $error['email'] = 'duplicate';
        }
    }

    if (empty($error)) {
        //画像をアップロードする
        $image = date('YmdHis') . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], '../member_picture/' . $image);
        $_SESSION['join'] = $_POST;
        $_SESSION['join']['image'] = $image;
        header('Location: check.php');
        exit();
    }
}

//書き直し
if ($_REQUEST['action'] == 'rewrite') {
    $_POST = $_SESSION['join'];
    $error['rewrite'] = true;
}
?>



<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Document</title>
</head>
<body>
    <div class="top">
        <h1>会員登録</h1>
    </div>
    <div class="background">
        <p>次のフォームに必要事項をご記入ください</p>
        <form action="" method="post" enctype="multipart/form-data">
            <dl>
                <dt>ニックネーム<span class="required">必須</span></dt>
                <dd><input type="text" name="name" size="35" maxlength="255"　value="<?php echo htmlspecialchars($_POST['name'], ENT_QUOTES); ?>" />
                <?php if(isset($error['name']) && $error['name'] == 'blank'): ?>
                <p class="error">※ ニックネームを入力してください</p>
                <?php endif; ?>
                </dd>
                <dt>メールアドレス<span class="required">必須</span></dt>
                <dd><input type="text" name="email" size="35" maxlength="255"　value="<?php echo htmlspecialchars($_POST['email'], ENT_QUOTES); ?>" />
                <?php if(isset($error['email']) && $error['email'] == 'blank'): ?>
                <p class="error">※ メールアドレスを入力してください</p>
                <?php endif; ?>
                <?php if($error['email'] == 'duplicate'): ?>
                <p class="error">* 指定されたメールアドレスは既に登録されています</p>
                <?php endif; ?>
                </dd>
                <dt>パスワード<span class="required">必須</span></dt>
                <dd><input type="text" name="password" size="35" maxlength="255"　value="<?php echo htmlspecialchars($_POST['password'], ENT_QUOTES); ?>" />
                <?php if(isset($error['password']) && $error['password'] == 'blank'): ?>
                <p class="error">※ パスワードを入力してください</p>
                <?php endif; ?>
                <?php if(isset($error['password']) && $error['password'] == 'length'): ?>
                <p class="error">※ パスワードを入力してください</p>
                <?php endif; ?>
                </dd>
                <dt>写真など</dt>
                <dd><input type="file" name="image" size="35" />
                <?php if(isset($error['image']) && $error['image'] == 'type'): ?>
                    <p class="error">* 写真などは『.gif』または『.jpg』の画像を指定してください</p>
                <?php endif; ?>
                <?php if(!empty($error)): ?>
                    <p class="error">* 恐れ入りますが、画像を改めて指定してください</p>
                    <?php endif; ?>
                </dd>
            </dl>   
            <input type="submit" value="入力内容を確認する" class="button">
        </form>
    </div>
</body>
</html>

