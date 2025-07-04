<?php
session_start();
require('dbconnect.php');

if (!empty($_POST)) {
    if (($_POST['email'] != '') && ($_POST['password'] != '')) {
        $login = $db->prepare('SELECT * FROM members WHERE email=?');
        $login->execute(array($_POST['email']));
        $member=$login->fetch();

        if ($member != false && password_verify($_POST['password'], $member['password'])) {
            $_SESSION['id'] = $member['id'];
            $_SESSION['time'] = time();
            header('Location: threads.php');
            exit();
        } else {
            $error['login'] = 'failed';
        }
    } else {
        $error['login'] = 'blank';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head style="text-align: center; padding: 20px;">
        <title>ログイン画面</title>
        <style>
            .error {  color: red;font-size: 0.8em;}
        </style>
    </head>
    <body style="text-align: center; padding: 20px;">
        <h1>ログイン画面</h1>
        <form action="" method="post">
            <label style="display: inline-block; width: 200px; padding-bottom: 10px;">
                Email
                <input type="text" name="email" style="width:150px" value="<?php echo htmlspecialchars($_POST['email'] ?? "", ENT_QUOTES); ?>">
                <?php if (isset($error['login']) && ($error['login'] == 'blank')): ?>
                    <p class="error">Eメールとパスワードを入力してください</p>
                <?php endif; ?>

                <?php if (isset($error['login']) && ($error['login'] == 'failed')): ?>
                    <p class="error">Eメールかパスワードが間違っています</p>
                <?php endif; ?>
            </label>
            <br>
            <label style="display: inline-block; width: 200px;">
                パスワード
                <input type="password" name="password" style="width:150px" value = "<?php echo htmlspecialchars($_POST['password']??"", ENT_QUOTES); ?>">
            </label>

            <div class = "login2" style="text-align: center; padding: 10px;">
                <input type="submit" value = "ログインする" class = "button">
            </div>
        </form>
        <a href="register.php">ユーザー登録する</a>
    </body>
