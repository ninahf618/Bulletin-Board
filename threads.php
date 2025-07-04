<?php
session_start();
require('dbconnect.php');

if (isset($_SESSION['id']) && ($_SESSION['time'] + 3600 > time())) {
    $_SESSION['time'] = time();

    $members = $db->prepare('SELECT * FROM members WHERE id=?');
    $members->execute(array($_SESSION['id']));
    $member=$members->fetch();
} else {
    header('Location: login.php');
    exit();
}

if (!empty($_POST)) {
    if (trim($_POST['body']) === '') {
        $error['body'] = 'blank';
    } elseif (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
        $stmt = $db->prepare('INSERT INTO threads SET user_id=?, body=?, created=NOW()');
        $stmt->execute(array($member['id'], $_POST['body']));
        header('Location: threads.php');
        exit();
    } else {
        header('Location: login.php');
        exit();
    }
}

// if (!empty($_POST)) {
//     if (isset($_POST['token']) && $_POST['token'] === $_SESSION['token']) {
//         $stmt=$db->prepare('INSERT INTO threads SET user_id=?, body=?, created=NOW()');
//         $stmt->execute(array($member['id'] , $_POST['body']));
//         header('Location: threads.php');
//         exit();
//     } else {
//         header('Location: login.php');
//         exit();
//     }
// }



$threads = $db->query('SELECT m.name, t.* FROM members m JOIN threads t ON m.id=t.user_id ORDER BY t.created DESC');

$TOKEN_LENGTH = 32;
$tokenByte = openssl_random_pseudo_bytes($TOKEN_LENGTH);
$token = bin2hex($tokenByte);
$_SESSION['token'] = $token;
?>

<!DOCTYPE html>
<html lang="ja">
        <header style="text-align: center; padding: 20px;">
            <div class = "head">
                <h1>メモ</h1>
                <span class = "all"><a href= "all.php">みんなのメモ一覧</a></span> |
                <span class = "logout"><a href= "login.php">ログアウト</a></span>
            </div>
        </header>
        <body>
        <form action="" method = "post" style="text-align: center; padding: 20px;">
            <input type="hidden" name = "token" value = "<?=$token?>">
            <?php if (isset($error['login']) && ($error['login'] == 'token')): ?>
                <p class= "error"> 不正アクセスです</p>
            <?php endif; ?>
            <div class = "edit">
                <p>
                    <?php echo htmlspecialchars($member['name'], ENT_QUOTES); ?>さん、こんにちは！メモを追加してね！
                </p>
                <textarea name="body" cols='50' rows = '10'><?php echo htmlspecialchars(isset($_POST['body']) ? $_POST['body'] : '', ENT_QUOTES); ?></textarea>
                <?php if (isset($error['body']) && $error['body'] == 'blank'): ?>
                <p class="error" style="color:red;">メモ内容を入力してください。</p>
                <?php endif; ?>

            </div>
            <input type="submit" value = "投稿する" class = "button02">
        </form>

<?php foreach($threads as $thread): ?>
    <div class = "thread" style = "border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; margin-top: 20px;">
        <?php echo htmlspecialchars($thread['body'], ENT_QUOTES); ?> 
        <span class = "name">
            <p> | 投稿者: <?php echo htmlspecialchars($thread['name'], ENT_QUOTES); ?> |</p>
            <p> | 投稿日: <?php echo htmlspecialchars($thread['created'], ENT_QUOTES); ?> |</p>

            <?php if ($_SESSION['id'] == $thread['user_id']): ?>
                [<a href="delete.php?id=<?php echo htmlspecialchars($thread['id'], ENT_QUOTES); ?>&return_to=threads.php">削除</a>] | 
                [<a href="edit.php?id=<?php echo $thread['id']; ?>&return_to=<?php echo basename($_SERVER['PHP_SELF']); ?>">編集</a>] |
            <?php endif; ?>

            <?php $reply_count_stmt = $db->prepare('SELECT COUNT(*) AS cnt FROM replies WHERE thread_id=?');
            $reply_count_stmt->execute(array($thread['id']));
            $reply_count = $reply_count_stmt->fetch(); ?>
            <?php if ($reply_count['cnt'] > 0): ?>
                [<a href = "replies.php?thread_id=<?php echo $thread['id']; ?>"> <?php echo $reply_count['cnt']; ?>件の返信</a>]
                <?php endif; ?>

                [<a href="reply.php?thread_id=<?php echo $thread['id']; ?>">返信</a>]
        </span>
    </div>
<?php endforeach; ?>
</body>
</html>
