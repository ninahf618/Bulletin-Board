<?php 
session_start();
require('dbconnect.php');

$returnTo = $_GET['return_to'] ?? 'threads.php';
$allowedPages = ['threads.php', 'all.php'];
if (!in_array($returnTo, $allowedPages)) {
    $returnTo = 'threads.php';
}

if (!isset($_SESSION['id'])) {
    header('Location: login.php');
    exit();
}

$id = $_GET['id'] ?? null;

if ($id === null || !ctype_digit($id)) {
    header('Location: ' . htmlspecialchars($returnTo, ENT_QUOTES));
    exit();
}

$threads = $db->prepare('SELECT * FROM threads WHERE id = ?');
$threads->execute(array($id));
$thread = $threads->fetch();

if (!$thread || $thread['user_id'] != $_SESSION['id']) {
    header('Location: ' . htmlspecialchars($returnTo, ENT_QUOTES));
    exit();
}

// If POST request, proceed to delete
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $delete = $db->prepare('DELETE FROM threads WHERE id = ?');
    $delete->execute(array($id));
    header('Location: ' . htmlspecialchars($returnTo, ENT_QUOTES));
    exit();
}
?>

<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF-8">
        <title>スレッド削除</title>
    </head>
    <body style="text-align: center; padding: 20px;">
        <h1>メモ削除</h1>
        <p>本当にこのメモを削除してもよろしいですか？</p>
        <blockquote style="border: 1px solid #ccc; padding: 10px; margin: 20px;">
            <?php echo htmlspecialchars($thread['body'], ENT_QUOTES); ?>
        </blockquote>
        <form action="" method="post">
            <input type="submit" value="削除する">
            <a href="<?php echo htmlspecialchars($returnTo); ?>">キャンセル</a>
        </form>
    </body>
</html>
