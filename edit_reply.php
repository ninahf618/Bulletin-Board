<?php 
session_start();
require('dbconnect.php');

if(!isset($_SESSION['id']) || ($_SESSION['time'] + 3600 < time())) {
    header('Location: login.php');
    exit();
}
$_SESSION['time'] = time();

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: threads.php');
    exit();
}

$id = (int) $_GET['id'];

$stmt = $db->prepare('SELECT * FROM replies WHERE id=?');
$stmt->execute(array($id));
$reply = $stmt->fetch();

if(!$reply || $reply['member_id'] != $_SESSION['id']) {
    echo 'この返信は編集できません。';
    exit();
}

if (!empty($_POST)) {
    if ($_POST['message'] !== '') {
        $update = $db->prepare('UPDATE replies SET message=?, modified=NOW() WHERE id=?');
        $update->execute(array($_POST['message'], $id));
        header('Location: replies.php?thread_id=' . $reply['thread_id']);
        exit();
    } else {
        $error['message'] = 'blank';
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head style="text-align: center; padding: 20px;">
        <title>返信編集</title>
    </head>
    <body style="text-align: center; padding: 20px;">
        <h1>返信の編集</h1>
        <form action="" method="post">
            <textarea name="message" cols = "50" rows="5"><?php echo htmlspecialchars($reply['message'], ENT_QUOTES); ?></textarea>
            <br>
            <?php if (!empty($error['message']) && $error['message'] === 'blank'): ?>
                <p class="error" style="color: red;">メッセージを入力してください。</p>
            <?php endif; ?>
            <input type="submit" value="更新する">
        </form>

        <p><a href="replies.php?thread_id=<?php echo $reply['thread_id']; ?>">返信一覧に戻る</a></p>
    </body>
</html>
