<?php 
session_start();
require('dbconnect.php');

if (isset($_SESSION['id']) && ($_SESSION['time'] + 3600 > time())) {
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member = $members->fetch();
} else {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['thread_id']) || !is_numeric($_GET['thread_id'])) {
    header('Location: threads.php');
    exit();
}

$thread_id = (int)$_GET['thread_id'];
$errors = [];

if (!empty($_POST)) {
    if ($_POST['message'] === '') {
        $errors['message'] = 'blank';
    }

    if (empty($errors)) {
        $reply = $db->prepare('INSERT INTO replies SET thread_id=?, member_id=?, message=?, created=NOW()');
        $reply->execute(array($thread_id, $member['id'], $_POST['message']));
        header('Location: replies.php?thread_id=' . $thread_id);
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="ja">
<head style="text-align: center; padding: 20px;">
    <title>返信投稿</title>
</head>
<body style="text-align: center; padding: 20px;">
    <h1>返信を投稿する</h1>
    <form action="" method="post">
        <div>
            <textarea name="message" cols='50' rows='10'><?php echo htmlspecialchars($_POST['message'] ?? '', ENT_QUOTES); ?></textarea>
            <?php if (isset($errors['message']) && $errors['message'] === 'blank'): ?>
                <p class="error" style="color: red">メッセージを入力してください</p>
            <?php endif; ?>
        </div>

        <div>
            <input type="submit" value="返信する">
        </div>
    </form>

    <p><a href="threads.php">スレッド一覧に戻る</a></p>
</body>
</html>
