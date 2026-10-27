<?php
require_once 'config.php';

// ============================================================
// ส่วนที่ 1) ภาพรวมของห้องแล็บ (ข้อมูลตัวอย่าง ยังไม่ต่อฐานข้อมูล)
// สัปดาห์ที่ 3 จะเปลี่ยนเป็นนับจากข้อมูลจริง
// ============================================================
$labName    = 'ห้องแล็บเครือข่าย อาคาร 4'; // string
$totalItems = 40;                          // int
$borrowed   = 33;                          // int
$isOpen     = true;                        // bool

// ตรวจชนิดข้อมูลชั่วคราว: เอา // ออกเพื่อทดสอบ แล้วใส่กลับก่อนส่งงาน
// var_dump($labName, $totalItems, $borrowed, $isOpen); exit;

$available   = $totalItems - $borrowed;                                         // int
// กันการหารด้วยศูนย์ (กรณีทดสอบ T4)
$usedPercent = $totalItems > 0 ? round($borrowed / $totalItems * 100, 1) : 0;   // float

// ============================================================
// ส่วนที่ 2) รายการยืมล่าสุด 1 รายการ
// ============================================================
$borrower   = 'สมชาย ใจดี';              // string
$itemName   = 'สาย LAN Cat6 3 เมตร';     // string
$qty        = 2;                          // int
$borrowDate = '2026-10-20';               // string รูปแบบ ปี-เดือน-วัน
$dueDate    = date('Y-m-d', strtotime($borrowDate . ' +' . LOAN_DAYS . ' days'));
$today      = date('Y-m-d');              // ตอนทดสอบเปลี่ยนเป็น '2026-10-27' ฯลฯ
$daysLate   = intdiv(strtotime($today) - strtotime($dueDate), 86400); // ติดลบได้ถ้ายังไม่ถึงกำหนด

$isLate = $daysLate > 0;                                          // bool
$fine   = $isLate ? $daysLate * FINE_PER_DAY * $qty : 0;          // ค่าปรับรวม (บาท)

// ============================================================
// ส่วนที่ 3) เงื่อนไขการเตือน
// ============================================================
$needAlert   = $usedPercent >= ALERT_PERCENT || $isLate;
$statusText  = $isLate ? "เกินกำหนด $daysLate วัน" : 'ยังไม่เกินกำหนด';
$lastMessage = $borrower . ' ยืม ' . $itemName . ' จำนวน ' . $qty . ' ชิ้น';

// ส่วนช่วยแสดงผล: ยืมมาแล้วกี่วัน และเปอร์เซ็นต์ของเวลาที่ใช้ไป (0–100) สำหรับแถบเส้นเวลา
$daysBorrowed = intdiv(strtotime($today) - strtotime($borrowDate), 86400);       // int
$loanProgress = min(100, max(0, round($daysBorrowed / LOAN_DAYS * 100)));        // 0–100

$menu = [
  ['icon' => 'bi-grid-1x2', 'label' => 'แดชบอร์ด', 'href' => 'index.php', 'active' => true],
  ['icon' => 'bi-megaphone', 'label' => 'ประกาศแบ่งปัน', 'href' => '#'],
  ['icon' => 'bi-arrow-left-right', 'label' => 'ยืม–คืน', 'href' => '#'],
  ['icon' => 'bi-building', 'label' => 'ห้องเรียน', 'href' => '#'],
  ['icon' => 'bi-bar-chart-line', 'label' => 'รายงาน', 'href' => '#'],
  ['icon' => 'bi-info-circle', 'label' => 'เกี่ยวกับโครงงาน', 'href' => 'about.php'],
];

function e($s)
{
  return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>แดชบอร์ด · <?php echo e(APP_NAME); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Thai:wght@400;500;600&display=swap" rel="stylesheet">
  <style>
    @property --p {
      syntax: '<number>';
      inherits: false;
      initial-value: 0;
    }

    :root {
      --bg: #f7f4ee;
      --surface: #ffffff;
      --ink: #1f2a37;
      --muted: #6b7280;
      --line: #e8e2d6;
      --teal: #0f766e;
      --teal-soft: #d7efec;
      --amber: #d97706;
      --amber-soft: #fdecc8;
      --red: #dc4c3e;
      --red-soft: #fde3df;
      --sidebar-w: 248px;
    }

    body {
      font-family: 'IBM Plex Sans Thai', system-ui, sans-serif;
      background: var(--bg);
      color: var(--ink);
    }

    /* ---------- Sidebar (สว่าง มีแถบสีด้านซ้ายของเมนูที่เลือก) ---------- */
    .sidebar {
      width: var(--sidebar-w);
      background-color: var(--surface) !important;
      border-right: 1px solid var(--line) !important;
    }

    @media (min-width: 992px) {
      .sidebar { position: fixed; inset: 0 auto 0 0; }
      .main { margin-left: var(--sidebar-w); }
    }

    .brand {
      display: flex;
      align-items: center;
      gap: .7rem;
      padding: 1.4rem 1.25rem 1.1rem;
      color: var(--ink);
      text-decoration: none;
    }

    .brand-logo {
      width: 38px;
      height: 38px;
      border-radius: 10px;
      display: grid;
      place-items: center;
      background: var(--teal);
      color: #fff;
      font-size: 1.1rem;
    }

    .brand-name { font-weight: 600; line-height: 1.2; }
    .brand-name small { display: block; font-weight: 400; color: var(--muted); font-size: .72rem; }

    .menu-title {
      font-size: .7rem;
      letter-spacing: .1em;
      text-transform: uppercase;
      color: var(--muted);
      padding: 1rem 1.4rem .4rem;
    }

    .side-link {
      position: relative;
      display: flex;
      align-items: center;
      gap: .75rem;
      margin: .1rem .7rem;
      padding: .6rem .85rem;
      border-radius: 8px;
      color: #4b5563;
      text-decoration: none;
      font-size: .93rem;
      transition: background .2s, color .2s, padding-left .2s;
    }

    .side-link:hover {
      background: var(--bg);
      color: var(--ink);
      padding-left: 1.1rem;
    }

    .side-link.active {
      background: var(--teal-soft);
      color: var(--teal);
      font-weight: 500;
    }

    .side-link.active::before {
      content: '';
      position: absolute;
      left: -.7rem;
      top: 20%;
      bottom: 20%;
      width: 4px;
      border-radius: 0 4px 4px 0;
      background: var(--teal);
    }

    .room-chip {
      margin: 1.5rem .9rem 1rem;
      padding: .8rem 1rem;
      border-radius: 12px;
      background: var(--bg);
      font-size: .85rem;
    }

    .room-chip .dot {
      display: inline-block;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      margin-right: .4rem;
    }

    .dot-on { background: #16a34a; animation: pulse 2s infinite; }
    .dot-off { background: var(--red); }

    /* ---------- Topbar ---------- */
    .topbar {
      display: flex;
      align-items: center;
      gap: .75rem;
      padding: 1rem 1.5rem;
    }

    .date-chip {
      font-size: .85rem;
      color: var(--muted);
      background: var(--surface);
      border: 1px solid var(--line);
      padding: .35rem .8rem;
      border-radius: 999px;
    }

    .icon-btn {
      position: relative;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      border: 1px solid var(--line);
      background: var(--surface);
      color: var(--muted);
    }

    .icon-btn .dot {
      position: absolute;
      top: 9px;
      right: 10px;
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--red);
      border: 2px solid #fff;
    }

    .avatar {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      display: grid;
      place-items: center;
      background: var(--amber-soft);
      color: var(--amber);
      font-weight: 600;
    }

    /* ---------- Cards ---------- */
    .card-x {
      background: var(--surface);
      border: 1px solid var(--line);
      border-radius: 14px;
      padding: 1.25rem 1.4rem;
      height: 100%;
    }

    .tile {
      transition: transform .2s, box-shadow .2s;
    }

    .tile:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 24px rgba(31, 42, 55, .08);
    }

    .tile-label { color: var(--muted); font-size: .88rem; }
    .tile-value { font-size: 2.1rem; font-weight: 600; line-height: 1.1; font-variant-numeric: tabular-nums; }
    .tile-unit { color: var(--muted); font-size: .85rem; }

    .tile-icon {
      width: 42px;
      height: 42px;
      border-radius: 50%;
      display: grid;
      place-items: center;
      font-size: 1.2rem;
      flex-shrink: 0;
    }

    .t-teal { background: var(--teal-soft); color: var(--teal); }
    .t-amber { background: var(--amber-soft); color: var(--amber); }
    .t-ink { background: #e9ecf1; color: #374151; }

    /* ---------- วงแหวนอัตราการใช้งาน (กล่อง "ใช้งาน") ---------- */
    .ring-card.warn { background: var(--red-soft); border-color: #f3b3ab; }

    .ring {
      --ring-color: var(--teal);
      position: relative;
      width: 170px;
      height: 170px;
      margin: .5rem auto;
      border-radius: 50%;
      background: conic-gradient(var(--ring-color) calc(var(--p) * 1%), #ece7dc 0);
      animation: sweep 1.2s ease-out;
    }

    .ring.is-alert { --ring-color: var(--red); }

    .ring::before {
      content: '';
      position: absolute;
      inset: 16px;
      border-radius: 50%;
      background: var(--surface);
    }

    .ring-card.warn .ring::before { background: #fff6f4; }

    .ring-text {
      position: absolute;
      inset: 0;
      display: grid;
      place-content: center;
      text-align: center;
    }

    .ring-text b { font-size: 2.2rem; line-height: 1; font-variant-numeric: tabular-nums; }
    .ring-text span { font-size: .8rem; color: var(--muted); }

    /* ---------- เส้นเวลารายการยืม ---------- */
    .timeline {
      position: relative;
      height: 8px;
      border-radius: 999px;
      background: #ece7dc;
      margin: 2.2rem 0 .6rem;
    }

    .timeline-fill {
      height: 100%;
      border-radius: 999px;
      background: var(--teal);
      width: <?php echo $loanProgress; ?>%;
      animation: grow 1s ease-out;
    }

    .timeline-fill.is-late { background: var(--red); }

    .marker {
      position: absolute;
      top: 50%;
      transform: translate(-50%, -50%);
      width: 16px;
      height: 16px;
      border-radius: 50%;
      background: #fff;
      border: 3px solid var(--teal);
    }

    .marker.end { border-color: var(--amber); }
    .marker small {
      position: absolute;
      bottom: 150%;
      left: 50%;
      transform: translateX(-50%);
      white-space: nowrap;
      font-size: .75rem;
      color: var(--muted);
    }

    .pill {
      display: inline-block;
      padding: .3em .8em;
      border-radius: 999px;
      font-size: .85rem;
      font-weight: 500;
    }

    .pill-ok { background: var(--teal-soft); color: var(--teal); }
    .pill-late { background: var(--red-soft); color: #b83a2e; }

    .rule {
      display: flex;
      align-items: center;
      gap: .8rem;
    }

    .alert-x {
      background: var(--amber-soft);
      border: 1px solid #f1cf8a;
      color: #92520a;
      border-radius: 12px;
      padding: .9rem 1.1rem;
    }

    /* ---------- ลูกเล่น: ค่อยๆ โผล่ขึ้นมาตอนโหลด ---------- */
    .reveal { animation: rise .55s ease-out both; }
    .reveal:nth-child(2) { animation-delay: .08s; }
    .reveal:nth-child(3) { animation-delay: .16s; }
    .reveal:nth-child(4) { animation-delay: .24s; }

    @keyframes rise { from { opacity: 0; transform: translateY(14px); } }
    @keyframes sweep { from { --p: 0; } }
    @keyframes grow { from { width: 0; } }
    @keyframes pulse { 50% { box-shadow: 0 0 0 5px rgba(22, 163, 74, .18); } }

    @media (prefers-reduced-motion: reduce) {
      * { animation: none !important; transition: none !important; }
    }
  </style>
</head>

<body>
  <!-- Sidebar: ยึดซ้ายบนจอใหญ่ / เลื่อนออกมาเป็น offcanvas บนมือถือ -->
  <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar" aria-labelledby="sidebarLabel">
    <div class="d-flex align-items-start justify-content-between">
      <a class="brand" href="index.php" id="sidebarLabel">
        <span class="brand-logo"><i class="bi bi-share"></i></span>
        <span class="brand-name"><?php echo e(SITE_NAME); ?><small>Sharing System</small></span>
      </a>
      <button type="button" class="btn-close d-lg-none m-3" data-bs-dismiss="offcanvas"
        data-bs-target="#sidebar" aria-label="Close"></button>
    </div>

    <div class="menu-title">เมนู</div>
    <nav>
      <?php foreach ($menu as $item): ?>
        <a class="side-link<?php echo !empty($item['active']) ? ' active' : ''; ?>" href="<?php echo e($item['href']); ?>"
          <?php echo !empty($item['active']) ? 'aria-current="page"' : ''; ?>>
          <i class="bi <?php echo e($item['icon']); ?>"></i>
          <span><?php echo e($item['label']); ?></span>
        </a>
      <?php endforeach; ?>
    </nav>

    <div class="room-chip mt-auto">
      <div class="fw-medium mb-1"><?php echo e($labName); ?></div>
      <span class="dot <?php echo $isOpen ? 'dot-on' : 'dot-off'; ?>"></span><?php echo $isOpen ? 'เปิดให้บริการ' : 'ปิดอยู่'; ?>
    </div>
  </aside>

  <div class="main">
    <!-- Topbar -->
    <header class="topbar">
      <button class="icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar"
        aria-controls="sidebar" aria-label="เปิดเมนู">
        <i class="bi bi-list fs-5"></i>
      </button>

      <div class="d-none d-sm-block">
        <div class="fw-semibold fs-5 lh-sm">แดชบอร์ด</div>
        <small class="text-secondary"><?php echo e(APP_NAME); ?></small>
      </div>

      <div class="ms-auto d-flex align-items-center gap-2">
        <span class="date-chip d-none d-md-inline"><i class="bi bi-calendar3 me-1"></i><?php echo date('d/m/Y'); ?></span>
        <button class="icon-btn" type="button" aria-label="การแจ้งเตือน">
          <i class="bi bi-bell"></i>
          <?php if ($needAlert): ?><span class="dot"></span><?php endif; ?>
        </button>
        <div class="dropdown">
          <button class="btn p-0 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" aria-label="บัญชี">
            <span class="avatar">A</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
            <li><span class="dropdown-item-text small text-secondary">Admin · ผู้ดูแลระบบ</span></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>โปรไฟล์</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-left me-2"></i>ออกจากระบบ</a></li>
          </ul>
        </div>
      </div>
    </header>

    <main class="px-3 px-lg-4 pb-4">
      <!-- ภาพรวม: วงแหวน % ใช้งาน + 3 กล่องตัวเลข -->
      <div class="row g-3 mb-3">
        <div class="col-12 col-lg-4 reveal">
          <div class="card-x ring-card text-center<?php echo $usedPercent >= ALERT_PERCENT ? ' warn' : ''; ?>">
            <div class="tile-label">ใช้งาน (ยืมออกไปเทียบกับทั้งหมด)</div>
            <div class="ring<?php echo $usedPercent >= ALERT_PERCENT ? ' is-alert' : ''; ?>" style="--p: <?php echo $usedPercent; ?>">
              <div class="ring-text">
                <b><span data-count="<?php echo $usedPercent; ?>" data-decimals="1"><?php echo $usedPercent; ?></span>%</b>
                <span>เกณฑ์เตือน <?php echo ALERT_PERCENT; ?>%</span>
              </div>
            </div>
            <small class="text-secondary">
              <?php if ($usedPercent >= ALERT_PERCENT): ?>
                <i class="bi bi-exclamation-triangle me-1"></i>เกินเกณฑ์เตือนแล้ว
              <?php else: ?>
                ยังต่ำกว่าเกณฑ์เตือน
              <?php endif; ?>
            </small>
          </div>
        </div>

        <div class="col-12 col-lg-8">
          <div class="row g-3 h-100">
            <div class="col-12 col-sm-4 reveal">
              <div class="card-x tile">
                <div class="d-flex align-items-center gap-3 mb-3">
                  <span class="tile-icon t-ink"><i class="bi bi-hdd-network"></i></span>
                  <span class="tile-label">อุปกรณ์ทั้งหมด</span>
                </div>
                <div class="tile-value"><span data-count="<?php echo $totalItems; ?>"><?php echo $totalItems; ?></span></div>
                <div class="tile-unit">ชิ้น</div>
              </div>
            </div>
            <div class="col-12 col-sm-4 reveal">
              <div class="card-x tile">
                <div class="d-flex align-items-center gap-3 mb-3">
                  <span class="tile-icon t-amber"><i class="bi bi-box-arrow-up-right"></i></span>
                  <span class="tile-label">ยืมออกไป</span>
                </div>
                <div class="tile-value"><span data-count="<?php echo $borrowed; ?>"><?php echo $borrowed; ?></span></div>
                <div class="tile-unit">ชิ้น</div>
              </div>
            </div>
            <div class="col-12 col-sm-4 reveal">
              <div class="card-x tile">
                <div class="d-flex align-items-center gap-3 mb-3">
                  <span class="tile-icon t-teal"><i class="bi bi-box-seam"></i></span>
                  <span class="tile-label">พร้อมให้ยืม</span>
                </div>
                <div class="tile-value"><span data-count="<?php echo $available; ?>"><?php echo $available; ?></span></div>
                <div class="tile-unit">ชิ้น</div>
              </div>
            </div>

            <!-- กติกาการยืม -->
            <div class="col-12 reveal">
              <div class="card-x d-flex flex-wrap align-items-center gap-4">
                <div class="fw-semibold">กติกาการยืม</div>
                <div class="rule"><span class="tile-icon t-teal"><i class="bi bi-calendar-week"></i></span>
                  <span>ยืมได้สูงสุด <strong><?php echo LOAN_DAYS; ?> วัน</strong></span></div>
                <div class="rule"><span class="tile-icon t-amber"><i class="bi bi-cash-coin"></i></span>
                  <span>ปรับ <strong><?php echo FINE_PER_DAY; ?> บาท</strong> / ชิ้น / วัน</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- รายการยืมล่าสุด -->
      <div class="card-x reveal">
        <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
          <div>
            <h2 class="h6 fw-semibold mb-1">รายการยืมล่าสุด</h2>
            <div class="fs-5"><?php echo e($lastMessage); ?></div>
          </div>
          <span class="pill <?php echo $isLate ? 'pill-late' : 'pill-ok'; ?>"><?php echo e($statusText); ?></span>
        </div>

        <!-- เส้นเวลา: วันที่ยืม → กำหนดคืน -->
        <div class="timeline" aria-label="ยืมมาแล้ว <?php echo e($daysBorrowed); ?> จาก <?php echo LOAN_DAYS; ?> วัน">
          <div class="timeline-fill<?php echo $isLate ? ' is-late' : ''; ?>"></div>
          <span class="marker" style="left: 0"><small>ยืม <?php echo e($borrowDate); ?></small></span>
          <span class="marker end" style="left: 100%"><small>คืน <?php echo e($dueDate); ?></small></span>
        </div>
        <div class="d-flex flex-wrap justify-content-between gap-2 mt-3 pt-3 border-top">
          <span class="text-secondary">ยืมมาแล้ว <strong class="text-body"><?php echo e($daysBorrowed); ?></strong> จาก <?php echo LOAN_DAYS; ?> วัน</span>
          <span>ค่าปรับ <strong><?php echo number_format($fine, 2); ?></strong> บาท</span>
        </div>
      </div>

      <!-- ข้อความเตือน 1 จุด -->
      <?php if ($needAlert): ?>
        <div class="alert-x mt-3 d-flex align-items-center gap-2 reveal">
          <i class="bi bi-exclamation-triangle-fill"></i>
          <span>ต้องติดตาม: อุปกรณ์ถูกยืมออกไปมาก หรือมีรายการเกินกำหนดคืน</span>
        </div>
      <?php endif; ?>
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
  </script>
  <script>
    // ตัวเลขนับขึ้นสั้นๆ ตอนโหลดหน้า (ถ้าปิด JS หรือเลือกลดการเคลื่อนไหว จะเห็นค่าจริงทันที)
    if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
      document.querySelectorAll('[data-count]').forEach(function (el) {
        var target = parseFloat(el.dataset.count);
        var decimals = parseInt(el.dataset.decimals || '0', 10);
        var start = null;
        function step(t) {
          if (start === null) start = t;
          var k = Math.min((t - start) / 700, 1);
          el.textContent = (target * k).toFixed(decimals);
          if (k < 1) requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
      });
    }
  </script>
</body>

</html>