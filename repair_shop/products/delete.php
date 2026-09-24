<?php require '../config/database.php';auth();$s=$pdo->prepare('DELETE FROM products WHERE id=?');$s->execute([(int)$_GET['id']]);header('Location:index.php');exit;
