<?php
$host='localhost'; $db='repair_shop'; $user='root'; $pass='';
try {
 $pdo=new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4",$user,$pass,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);
} catch(PDOException $e){ die('فشل الاتصال بقاعدة البيانات: '.htmlspecialchars($e->getMessage())); }
function auth(){ if(session_status()===PHP_SESSION_NONE) session_start(); if(empty($_SESSION['user_id'])){ header('Location: /repair_shop/login.php'); exit; } }
?>
