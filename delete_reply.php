<?php 
session_start();
require('dbconnect.php');

if(!isset($_SESSION['id']) || $_SESSION['time'] + 3600 < time()) {
    header('Location: login.php');
    exit();
}
$_SESSION['time'] = time();

if (isset($_GET['id'])) {
    $reply_id = intval($_GET['id']);

    $stmt = $db->prepare('SELECT * FROM replies WHERE id=?');
    $stmt->execute(array($reply_id));
    $reply = $stmt->fetch();

    if (!$reply || $reply['member_id'] != $_SESSION['id']) {
        header('Location: threads.php');
        exit();
    }
} else {
    header('Location: threads.php');
    exit();
}

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $del = $db->prepare('DELETE FROM replies WHERE id=?');
    $del->execute(array($reply_id));
    header('Location: replies.php?thread_id=' . $reply['thread_id']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head style="text-align: center; padding: 20px;"> 
        <title>
            返信削除
        </title>
    </head>
    <body style="text-align: center; padding: 20px;">
        <h1>返信削除</h1>
        <p>本当にこの返信を削除してもよろしいですか？</p>
        <blockquote style = "border: 1px solid #ccc; padding: 10px; margin: 20px;"><?php echo htmlspecialchars($reply['message'], ENT_QUOTES); ?></blockquote>
        <form action="" method = "post">
            <input type="submit" value = "削除する">
            <a href="replies.php?thread_id=<?php echo $reply['thread_id']; ?>">キャンセル</a>
        </form>
    </body>
</html>

    