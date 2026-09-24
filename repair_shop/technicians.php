<?php
 require 'config.php';require 'auth.php';$title='الفنيون';

if($_SERVER['REQUEST_METHOD']==='POST'){ $s=$pdo->prepare("INSERT INTO technicians(full_name,phone,specialty)
 VALUES(?,?,?)");$s->execute([$_POST['full_name'],$_POST['phone'],$_POST['specialty']]);header('Location:technicians.php');exit;}
$rows=$pdo->query("SELECT * FROM technicians ORDER BY id DESC")->fetchAll();include 'header.php';?>
<h3>الفنيون</h3><div class="card p-3 my-3"><form method="post" class="row g-2"><div class="col-md-4"><input class="form-control" 
name="full_name" placeholder="اسم الفني" required></div><div class="col-md-3"><input class="form-control" name="phone"
 placeholder="الهاتف"></div><div class="col-md-4"><input class="form-control" name="specialty"
  placeholder="التخصص"></div><div class="col-md-1"><button class="btn btn-primary w-100">حفظ</button></div></form></div><div class="card p-3">
    <table class="table"><tr><th>الفني</th><th>الهاتف</th><th>التخصص</th></tr><?php 
    foreach($rows as $r):?><tr><td><?=h($r['full_name'])?></td><td><?=h($r['phone'])?></td><td><?=h($r['specialty'])?></td></tr><?php 
    endforeach;?></table></div><?php include 'footer.php';