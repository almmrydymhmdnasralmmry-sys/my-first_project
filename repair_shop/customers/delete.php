<?php require '../config/database.php';auth();$id=(int)($_GET['id']??0);$s=$pdo->prepare('DELETE FROM customers WHERE id=?');$s->execute([$id]);header('Location:index.php');exit;
