<?php 
session_start();
require('dbconnect.php');
if (!empty($_POST)) {
    $error = [];
    if ($_POST['name'] == '') {
        $error['name'] = 'blank';
    }
    if ($_POST['email'] == '') {
        $error['email'] = 'blank';
    } else {
        $member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
        $member->execute(array($_POST['email']));
        $record = $member->fetch();
        if ($record['cnt'] > 0) {
            $error['email'] = 'duplicate';
        }
    }
    if ($_POST['password'] == '') {
        $error['password'] = 'blank';
    }
    if ($_POST['password2'] == '') {
        $error['password2'] = 'blank';
    }
    if (strlen($_POST['password']) < 6) {
        $error['password'] = 'length';
    }
    if (($_POST['password'] != $_POST['password2']) && ($_POST['password2'] != "")) {
    $error['password2'] = 'difference';
    }
    if (($_POST['password'] != $_POST['password2']) && ($_POST['password2'] != "")) {
        $error['password2'] = 'difference';
    } if (empty($error)) {
        $_SESSION['join'] = $_POST;
        header('Location: check.php');
        exit();
    } if (isset($_SESSION['join']) && isset($_REQUEST['action']) && ($_REQUEST['action'] == 'rewrite')) {
        $_POST = $_SESSION['join'];
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head style="text-align: center; padding: 20px;">
        <title>会員登録をする</title>
        <style>
            .error { color: red;font-size:0.8em; }
        </style>
    </head>
    <body style="text-align: center; padding: 20px;">
        <h1>会員登録をする</h1>
        <form action="" method = "post" class = "registrationform">
            <label style="display: inline-block; width: 200px; padding-bottom: 10px;">
                名前
                <br>
                <input type="text" name="name" style = "width:150px" value = "<?php echo $_POST['name']??""; ?>">
                <?php if (isset($error['name']) && $error['name'] == 'blank'):  ?>
                    <p class="error">名前を入力してください</p>
                <?php endif; ?>
            </label>
            <br>
            <label style="display: inline-block; width: 200px; padding-bottom: 10px;">
                Email
                <input type="text" name="email" style = "width:150px" value = "<?php echo $_POST['email']??""; ?>">
                <?php if (isset($error['email']) && $error['email'] == 'blank'):  ?>
                    <p class="error">Emailを入力してください</p>
                    <?php endif; ?>
                <?php if (isset($error['email']) && $error['email'] == 'duplicate'):  ?>
                    <p class="error">このEmailはすでに登録されています</p>
                <?php endif; ?>
            </label>
            <br>
            <label style="display: inline-block; width: 200px; padding-bottom: 10px;">
                パスワード
                <input type="password" name="password" style = "width:150px" value = "<?php echo $_POST['password']??""; ?>">
                <?php if (isset($error['password']) && $error['password'] == 'blank'):  ?>
                    <p class="error">パスワードを入力してください</p>
                <?php endif; ?>
                <?php if (isset($error['password']) && $error['password'] == 'length'):  ?>
                    <p class="error">パスワードは6文字以上で入力してください</p>
                <?php endif; ?>
            </label>
            <br>
            <label style="display: inline-block; width: 200px; padding-bottom: 10px;">
                パスワード再入力 <span class = "red"> * </span>
                <input type = "password" name = "password2" style = "width:150px">
                <?php if (isset($error['password2']) && $error['password2'] == 'blank'):  ?>
                    <p class="error">パスワードを入力してください</p>
                <?php endif; ?>
                <?php if (isset($error['password2']) && $error['password2'] == 'difference'):  ?>
                    <p class="error">パスワードが一致しません</p>
                <?php endif; ?>
            </label>
            <br>
            <input type="submit" value="確認する" class = "button">
        </form>
        <a href = "login.php">ログイン画面へ戻る</a>
        </body>
    </html>

