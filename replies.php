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

$thread_id = (int) $_GET['thread_id'];

$thread_stmt = $db->prepare('SELECT t.*, m.name FROM threads t LEFT JOIN members m ON t.user_id = m.id WHERE t.id=?');
$thread_stmt->execute(array($thread_id));
$thread = $thread_stmt->fetch();

if (!$thread) {
    echo "スレッドが見つかりません。";
    exit();
}

$replies_stmt = $db->prepare('SELECT r.*, m.name FROM replies r LEFT JOIN members m ON r.member_id = m.id WHERE r.thread_id=? ORDER BY r.created ASC');
$replies_stmt->execute(array($thread_id));
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <title>返信一覧</title>
    </head>
    <body>
        <div style="text-align: center; padding: 20px;">
        <h1>メモ:</h1>
                <span class = "memo"><a href="threads.php">みんなのメモ一覧に戻る</a>|</span>
        <span class = "replier"><a href="reply.php?thread_id=<?php echo $thread_id; ?>">返信を投稿する</a>|</span>
        <span class = "logout"><a href= "login.php">ログアウト</a></span>
        </div>
        <div style = "border: 1px solid #ccc; padding: 10px; margin-bottom: 50px;">
            <p><?php echo nl2br(htmlspecialchars($thread['body'], ENT_QUOTES)); ?></p>
            <p>| 投稿者: <?php echo htmlspecialchars($thread['name'], ENT_QUOTES); ?> |</p>
            <p>| 投稿日: <?php echo htmlspecialchars($thread['created'], ENT_QUOTES); ?> |</p>
        </div>

        <h2 style="text-align: center; padding: 20px;">メモへの返信一覧:</h2>

        <?php while ($reply = $replies_stmt->fetch()): ?>
            <div style = "border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; margin-top: 20px;">
                <p><?php echo nl2br(htmlspecialchars($reply['message'], ENT_QUOTES)); ?></p>
                <p>| 返信者: <?php echo htmlspecialchars($reply['name'], ENT_QUOTES); ?> |</p>
                <p>| 返信日: <?php echo htmlspecialchars($reply['created'], ENT_QUOTES); ?> |</p>

                <?php if ($_SESSION['id'] == $reply['member_id']): ?>
                    [<a href="edit_reply.php?id=<?php echo $reply['id']; ?>">編集</a>] |
                    [<a href="delete_reply.php?id=<?php echo $reply['id']; ?>&thread_id=<?php echo $thread_id; ?>">削除</a>]
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
    </body>
</html>
