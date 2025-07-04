<?php
session_start();
require('dbconnect.php');
if (!isset($_SESSION['join'])) {
    header('Location: register.php');
    exit();
}

$hash = password_hash($_SESSION['join']['password'], PASSWORD_BCRYPT);

if (!empty($_POST)) {
    $statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, created=NOW()');
    $statement->execute(array(
        $_SESSION['join']['name'],
        $_SESSION['join']['email'],
        $hash));
    unset($_SESSION['join']);
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head style="text-align: center; padding: 20px;">
        <title>
            ユーザー登録確認画面
        </title>
    </head>
    <body style="text-align: center; padding: 20px;">
        <h1>ユーザー登録確認画面</h1>
    <form action="" method="post">
        <input type="hidden" name="action" value="submit">

        <p>
            <span style="display: inline-block; width: 100px; text-align: right;">名前:</span>
            <span class="check" style="display: inline-block; border: 1px solid #ccc; padding: 2px 10px;">
                <?php echo htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES); ?>
            </span>
        </p>

        <p>
            <span style="display: inline-block; width: 100px; text-align: right;">Email:</span>
            <span class="check" style="display: inline-block; border: 1px solid #ccc; padding: 2px 10px;">
                <?php echo htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES); ?>
            </span>
        </p>

        <p>
            <span style="display: inline-block; width: 100px; text-align: right;">パスワード:</span>
            <span class="check">
                [セキュリティのため非表示]
            </span>
        </p>

        <p style="margin-top: 20px;">
            <input type="button" onclick="event.preventDefault();location.href='register.php?action=rewrite'" value="修正する" name="rewrite" class="button02">
            <input type="submit" value="登録する" name="registration" class="button">
        </p>
    </form>

    </body>





