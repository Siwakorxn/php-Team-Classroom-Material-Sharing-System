<?php
$team = "What is it?";
$topic = "ระบบแบ่งปันและยืมอุปกรณ์ระหว่างห้องเรียน (Classroom Material Sharing System) ";
$problem = "ลดค่าใช้จ่ายและใช้ทรัพยากรเหลือใช้ให้เกิดประโยชน์ ";
$members = ["นายศิวกร คำเจริญ(Developer A)", "น.ส.พรนัชชา คงยิ้งหาญ (Developer B)"];
?>
<!DOCTYPE html>
<html lang="th">
<head><meta charset="UTF-8"><title><?php echo $topic; ?></title></head>
<body>
  <h1><?php echo $topic; ?></h1>
  <p>ทีม: <?php echo $team; ?></p>
  <p>ปัญหาที่แก้: <?php echo $problem; ?></p>
  <h2>สมาชิก</h2>
  <ul>
    <?php foreach ($members as $m): ?>
      <li><?php echo $m; ?></li>
    <?php endforeach; ?>
  </ul>
  <p>อัปเดตล่าสุด: <?php echo date('d/m/Y H:i'); ?></p>
</body>
</html>
