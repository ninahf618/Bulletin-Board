<?php
try {
    $db = new PDO ('mysql:dbname=forum;host=127.0.0.1; charset=utf8', 'root', '');
} catch (PDOException $e) {
        echo 'Connection failed: ' . $e->getMessage();
    }





?>