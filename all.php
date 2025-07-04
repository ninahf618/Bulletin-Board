<?php
session_start();
require('dbconnect.php');
?>

<!DOCTYPE html>
<html lang = ja>
        <header style="text-align: center; padding: 20px;">
            <div class = "head">
                <h1>みんなのメモ一覧</h1>
                <span class = "threads"><a href= "threads.php">投稿する</span> |
                <span class = "logout"><a href= "login.php">ログアウト</a></span>
            </div>
        </header>

<?php
$threads = $db->query('SELECT m.name, t.* FROM members m JOIN threads t ON m.id=t.user_id ORDER BY t.created DESC');
?>

<?php foreach($threads as $thread): ?>
    <div class = "thread" style = "border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; margin-top: 20px;">
            <?php echo htmlspecialchars($thread['body'], ENT_QUOTES); ?> 
        <span class = "name">
            <p> 投稿者: <?php echo htmlspecialchars($thread['name'], ENT_QUOTES); ?></p>
            <p> | 投稿日: <?php echo htmlspecialchars($thread['created'], ENT_QUOTES); ?> |</p>

            <?php if ($_SESSION['id'] == $thread['user_id']): ?>
                [<a href="delete.php?id=<?php echo htmlspecialchars($thread['id'], ENT_QUOTES); ?>&return_to=all.php">削除</a>] |
                [<a href="edit.php?id=<?php echo $thread['id']; ?>&return_to=<?php echo basename($_SERVER['PHP_SELF']); ?>">編集</a>] |
            <?php endif; ?>

            <?php $reply_count_stmt = $db->prepare('SELECT COUNT(*) AS cnt FROM replies WHERE thread_id=?');
            $reply_count_stmt->execute(array($thread['id']));
            $reply_count = $reply_count_stmt->fetch(); ?>
            <?php if ($reply_count['cnt'] > 0): ?>
                [<a href = "replies.php?thread_id=<?php echo $thread['id']; ?>"> <?php echo $reply_count['cnt']; ?>件の返信</a>] |
                <?php endif; ?>

                [<a href="reply.php?thread_id=<?php echo $thread['id']; ?>">返信</a>] 
        </span>
    </div>
<?php endforeach; ?>