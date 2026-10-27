<?php
$goals = [
    'ให้นักศึกษาประกาศแจกหรือให้ยืมอุปกรณ์เหลือใช้ได้',
    'ให้ผู้ที่ต้องการค้นหาอุปกรณ์ได้ง่าย',
    'ลดการซื้ออุปกรณ์ซ้ำและลดขยะในวิทยาลัย',
];
?>
<!DOCTYPE html>
<html lang="th">
<head><meta charset="UTF-8"><title>เกี่ยวกับระบบ</title></head>
<body>
<nav>
  <a href="index.php">หน้าแรก</a> |
  <a href="about.php">เกี่ยวกับ</a> |
  <a href="server.php">เซิร์ฟเวอร์</a>
</nav>
<h1>เป้าหมายของระบบ (<?php echo count($goals); ?> ข้อ)</h1>
<ol>
<?php foreach ($goals as $g): ?>
<li><?php echo $g; ?></li>
<?php endforeach; ?>
</ol>
</body>
</html>