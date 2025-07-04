<?php 
session_start();
require('dbconnect.php');

$returnTo = $_GET['return_to'] ?? 'threads.php';
$allowedPages = ['threads.php', 'all.php'];
if (!in_array($returnTo, $allowedPages)) {
    $returnTo = 'threads.php';
}

$id = $_GET['id'] ?? null;

if (!isset($_SESSION['id']) || $id === null || !ctype_digit($id)) {
    header('Location: ' .htmlspecialchars($returnTo, ENT_QUOTES));
    exit();
}

$stmt = $db->prepare('SELECT * FROM threads WHERE id=?');
$stmt->execute([$id]);
$thread = $stmt->fetch();

if (!$thread || $thread['user_id'] != $_SESSION['id']) {
    header('Location: '. htmlspecialchars($returnTo, ENT_QUOTES));
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newContent = $_POST['body'] ?? '';

    if (trim($newContent) !== '') {
        $update = $db->prepare('UPDATE threads SET body = ?, modified = NOW() WHERE id = ?');
        $update->execute([$newContent, $id]);
        header('Location: '. htmlspecialchars($returnTo, ENT_QUOTES));
        exit();
    }
    $error = "内容を入力してください";
}

?>

<!DOCTYPE html>
<html lang = "ja">
    <head style="text-align: center; padding: 20px;">
        <title>スレッド編集</title>
    </head>
    <body style="text-align: center; padding: 20px;">
        <h2>スレッドを編集する</h2>
        <span class = "logout"><a href= "login.php">ログアウト</a></span>
        <?php if (!empty($error)): ?>
            <p style = "color: red;"> <?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <form action="edit.php?id=<?php echo htmlspecialchars($id); ?>&return_to=<?php echo htmlspecialchars($returnTo); ?>" method= "post">
            <textarea name="body" cols='50' rows = '10'><?php echo htmlspecialchars(isset($_POST['body']) ? $_POST['body'] : $thread['body'], ENT_QUOTES); ?></textarea>
            <br>
            <input type="submit" value = "投稿する" class = "button02">
        </form>
        <span class = "all"><a href= "all.php">みんなのメモ一覧へ戻る</a></span> |
        <span class = "threads"><a href= "threads.php">新しい投稿する</a></span> 


    </body>
    </html>


