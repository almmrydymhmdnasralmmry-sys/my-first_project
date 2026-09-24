<?php
require 'config.php'; require 'auth.php';
$id=(int)($_GET['id']??0);
$s=$pdo->prepare("SELECT r.*,c.full_name customer,c.phone,c.address,d.device_type,d.brand,d.model,d.imei_serial,d.color,d.accessories,t.full_name technician
FROM repair_orders r JOIN devices d ON d.id=r.device_id JOIN customers c ON c.id=d.customer_id
LEFT JOIN technicians t ON t.id=r.technician_id WHERE r.id=?");
$s->execute([$id]); $o=$s->fetch();
if(!$o) die('طلب الصيانة غير موجود');

$p=$pdo->prepare("SELECT p.part_name,rp.quantity,rp.unit_price FROM repair_parts rp JOIN parts p ON p.id=rp.part_id WHERE rp.repair_order_id=?");
$p->execute([$id]); $parts=$p->fetchAll();
$pa=$pdo->prepare("SELECT * FROM payments WHERE repair_order_id=? ORDER BY id");
$pa->execute([$id]); $payments=$pa->fetchAll();
$paid=array_sum(array_column($payments,'amount'));
$remaining=max(0,$o['total_cost']-$paid);
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>فاتورة صيانة - <?=htmlspecialchars($o['order_no'])?></title>
<style>
*{box-sizing:border-box}body{margin:0;background:#eef1f5;font-family:"Tahoma","Arial",sans-serif;color:#202733}
.invoice{width:210mm;min-height:297mm;margin:18px auto;background:#fff;padding:14mm;box-shadow:0 5px 25px #0001}
.header{display:flex;justify-content:space-between;gap:20px;border-bottom:3px solid #172033;padding-bottom:18px}
.brand{display:flex;gap:12px;align-items:center}.logo{width:58px;height:58px;border-radius:14px;background:#172033;color:
#fff;display:grid;place-items:center;font-size:28px}
h1{margin:0;font-size:25px}.muted{color:#6b7280;font-size:12px}.invoice-title{text-align:left}.invoice-title h2{margin:0 0 8px;font-size:24px}
.meta{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin:18px 0}.box{border:1px solid #dce1e8;border-radius:8px;padding:11px}.box b{display:inline-block;min-width:95px}
table{width:100%;border-collapse:collapse;margin-top:15px}th{background:#172033;color:#fff;padding:10px;font-size:13px}td{border:1px solid #dce1e8;padding:9px;font-size:13px}
.num{text-align:center}.money{text-align:left;direction:ltr}
.summary{margin-top:15px;margin-right:auto;width:360px}.summary div{display:flex;justify-content:space-between;padding:7px 10px;border-bottom:1px solid #e5e7eb}.summary .total{font-size:18px;font-weight:bold;border:2px solid #172033;margin-top:5px;border-radius:7px}
.notes{margin-top:20px;border:1px dashed #cbd5e1;padding:12px;border-radius:8px;min-height:65px}.sign{display:grid;grid-template-columns:1fr 1fr;gap:50px;margin-top:55px;text-align:center}.line{border-top:1px solid #555;padding-top:8px}
.footer{border-top:1px solid #e5e7eb;margin-top:35px;padding-top:12px;text-align:center;font-size:11px;color:#6b7280}
.actions{text-align:center;margin:15px}.btn{padding:10px 18px;border:0;border-radius:7px;cursor:pointer;background:#172033;color:#fff}
@media print{body{background:#fff}.invoice{margin:0;box-shadow:none;width:210mm;min-height:297mm}.actions{display:none}@page{size:A4;margin:0}}
</style>
</head>
<body>
<div class="actions"><button class="btn" onclick="window.print()">🖨 طباعة الفاتورة</button></div>
<div class="invoice">
<div class="header">
<div class="brand">
<div class="logo">🔧</div>
<div><h1>المعمري للصيانة</h1><div class="muted">صيانة الجوالات والأجهزة الإلكترونية</div><div class="muted">هاتف: 779210179</div></div>
</div>
<div class="invoice-title"><h2>فاتورة صيانة</h2><div><b>رقم الطلب:</b> <?=htmlspecialchars($o['order_no'])?></div><div><b>تاريخ الاستلام:</b> <?=htmlspecialchars($o['opened_at'])?></div></div>
</div>

<div class="meta">
<div class="box"><b>العميل:</b> <?=htmlspecialchars($o['customer'])?><br><b>الهاتف:</b> <?=htmlspecialchars($o['phone'])?><br><b>العنوان:</b> <?=htmlspecialchars($o['address'])?></div>
<div class="box"><b>الجهاز:</b> <?=htmlspecialchars($o['device_type'].' - '.$o['brand'].' '.$o['model'])?><br><b>IMEI/Serial:</b> <?=htmlspecialchars($o['imei_serial'])?><br><b>اللون:</b> <?=htmlspecialchars($o['color'])?></div>
</div>

<table>
<thead><tr><th>#</th><th>البيان</th><th>الكمية</th><th>سعر الوحدة</th><th>الإجمالي</th></tr></thead>
<tbody>
<?php $i=1; foreach($parts as $p): $line=$p['quantity']*$p['unit_price']; ?>
<tr><td class="num"><?=$i++?></td><td><?=htmlspecialchars($p['part_name'])?></td><td class="num"><?=$p['quantity']?></td><td class="money"><?=number_format($p['unit_price'],2)?> $</td><td class="money"><?=number_format($line,2)?> $</td></tr>
<?php endforeach; ?>
<tr><td class="num"><?=$i?></td><td>أجرة الصيانة والعمل</td><td class="num">1</td><td class="money"><?=number_format($o['labor_cost'],2)?> $</td><td class="money"><?=number_format($o['labor_cost'],2)?> $</td></tr>
</tbody>
</table>

<div class="summary">
<div><span>قطع الغيار</span><b><?=number_format($o['parts_total'],2)?> $</b></div>
<div><span>أجرة العمل</span><b><?=number_format($o['labor_cost'],2)?> $</b></div>
<div><span>الخصم</span><b><?=number_format($o['discount'],2)?> $</b></div>
<div class="total"><span>الإجمالي</span><b><?=number_format($o['total_cost'],2)?> $</b></div>
<div><span>المدفوع</span><b><?=number_format($paid,2)?> $</b></div>
<div><span>المتبقي</span><b><?=number_format($remaining,2)?> $</b></div>
</div>

<div class="notes"><b>المشكلة:</b> <?=htmlspecialchars($o['reported_problem'])?><br><b>التشخيص:</b> <?=htmlspecialchars($o['diagnosis']??'غير مسجل')?><br><b>الفني:</b> <?=htmlspecialchars($o['technician']??'غير معين')?></div>

<div class="sign"><div><div class="line">توقيع الموظف</div></div><div><div class="line">توقيع العميل / الاستلام</div></div></div>
<div class="footer">شكرًا لثقتكم — يرجى الاحتفاظ بالفاتورة عند استلام الجهاز. هذه الفاتورة توضح بيانات طلب الصيانة والتكاليف المسجلة في النظام.</div>
</div>
</body>
</html>
