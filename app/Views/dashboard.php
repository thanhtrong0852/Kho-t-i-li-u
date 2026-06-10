<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin — RoomManager</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }
:root {
  color-scheme: dark;
  --bg:      #111318;
  --bg2:     #1a1d26;
  --bg3:     #22263a;
  --border:  rgba(255,255,255,0.08);
  --text:    #f0f2ff;
  --text2:   #8b90b0;
  --accent:  #667eea;
  --accent2: #764ba2;
  --sidebar: 224px;
}
body { font-family:'Nunito',sans-serif; background:var(--bg); color:var(--text); display:flex; min-height:100vh; }

/* SIDEBAR */
.sidebar { width:var(--sidebar); background:var(--bg2); border-right:1px solid var(--border); display:flex; flex-direction:column; position:fixed; top:0; left:0; height:100vh; z-index:50; }
.sidebar-logo { padding:18px 16px; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:10px; }
.logo-icon { width:34px; height:34px; background:linear-gradient(135deg,var(--accent),var(--accent2)); border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:16px; }
.logo-text { font-size:15px; font-weight:800; color:var(--text); }
.logo-ver { font-size:10px; font-weight:700; color:var(--text2); background:var(--bg3); padding:2px 6px; border-radius:5px; margin-left:2px; }
.sidebar-label { font-size:10px; font-weight:700; color:#555a72; text-transform:uppercase; letter-spacing:0.8px; padding:14px 16px 5px; }
.sidebar-nav { flex:1; padding:5px 8px; overflow-y:auto; }
.nav-item { display:flex; align-items:center; gap:10px; padding:9px 10px; border-radius:9px; font-size:13px; font-weight:600; color:var(--text2); text-decoration:none; transition:all 0.15s; margin-bottom:2px; }
.nav-item:hover { background:rgba(102,126,234,0.1); color:var(--accent); }
.nav-item.active { background:rgba(102,126,234,0.15); color:var(--accent); }
.nav-item .ni { font-size:17px; flex-shrink:0; }
.nav-badge { margin-left:auto; background:var(--accent); color:#fff; font-size:10px; font-weight:800; padding:1px 7px; border-radius:20px; }
.sidebar-footer { padding:12px 8px; border-top:1px solid var(--border); }
.user-mini { display:flex; align-items:center; gap:10px; padding:9px 10px; border-radius:9px; cursor:pointer; transition:background 0.15s; }
.user-mini:hover { background:rgba(255,255,255,0.04); }
.user-avatar { width:32px; height:32px; background:linear-gradient(135deg,var(--accent),var(--accent2)); border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff; flex-shrink:0; }
.user-name { font-size:13px; font-weight:700; color:var(--text); }
.user-role { font-size:11px; color:var(--text2); }
.logout-btn { display:flex; align-items:center; gap:8px; padding:9px 10px; border-radius:9px; font-size:13px; font-weight:600; color:#f87171; text-decoration:none; transition:background 0.15s; margin-top:3px; }
.logout-btn:hover { background:rgba(248,113,113,0.1); }

/* CONTENT */
.content { margin-left:var(--sidebar); flex:1; display:flex; flex-direction:column; min-height:100vh; }

/* TOPBAR */
.topbar { background:var(--bg2); border-bottom:1px solid var(--border); padding:0 26px; height:56px; display:flex; align-items:center; justify-content:space-between; position:sticky; top:0; z-index:40; }
.topbar-title { font-size:15px; font-weight:800; color:var(--text); }
.topbar-right { display:flex; align-items:center; gap:10px; }
.topbar-date { font-size:12px; color:var(--text2); font-weight:600; }
.btn-outline { padding:7px 14px; border:1.5px solid var(--border); border-radius:9px; font-size:12px; font-weight:700; color:var(--text2); text-decoration:none; background:transparent; transition:all 0.15s; cursor:pointer; font-family:'Nunito',sans-serif; }
.btn-outline:hover { border-color:var(--accent); color:var(--accent); }
.btn-primary { padding:7px 14px; background:linear-gradient(135deg,var(--accent),var(--accent2)); border:none; border-radius:9px; font-size:12px; font-weight:700; color:#fff; text-decoration:none; transition:opacity 0.15s; cursor:pointer; font-family:'Nunito',sans-serif; }
.btn-primary:hover { opacity:0.88; }

/* PAGE */
.page { padding:22px 26px; }

/* STATS */
.stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:20px; }
.stat-card { background:var(--bg2); border-radius:14px; border:1px solid var(--border); padding:18px; cursor:pointer; transition:border-color 0.2s, transform 0.2s; }
.stat-card:hover { border-color:rgba(102,126,234,0.35); transform:translateY(-2px); }
.stat-row-top { display:flex; align-items:center; justify-content:space-between; margin-bottom:10px; }
.stat-ico { width:40px; height:40px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:19px; }
.si-blue   { background:rgba(102,126,234,0.15); }
.si-green  { background:rgba(74,222,128,0.12); }
.si-amber  { background:rgba(251,146,60,0.12); }
.si-purple { background:rgba(167,139,250,0.12); }
.stat-change { display:flex; align-items:center; gap:3px; font-size:11px; font-weight:700; padding:3px 8px; border-radius:6px; }
.chg-up   { background:rgba(74,222,128,0.12); color:#4ade80; }
.chg-dn   { background:rgba(251,146,60,0.12); color:#fb923c; }
.chg-flat { background:rgba(255,255,255,0.05); color:#555a72; }
.stat-val { font-size:24px; font-weight:800; color:var(--text); line-height:1; }
.stat-lbl { font-size:12px; color:var(--text2); font-weight:600; margin-top:4px; }

/* MAIN GRID */
.main-grid { display:grid; grid-template-columns:1fr 300px; gap:16px; }
.card { background:var(--bg2); border-radius:14px; border:1px solid var(--border); overflow:hidden; }
.card-header { padding:14px 18px; display:flex; align-items:center; justify-content:space-between; border-bottom:1px solid var(--border); }
.card-title { font-size:13px; font-weight:700; color:var(--text); }
.card-sub   { font-size:11px; color:var(--text2); margin-top:2px; }
.card-link  { font-size:12px; color:var(--accent); font-weight:600; text-decoration:none; }
.card-link:hover { text-decoration:underline; }

/* TABLE */
.tbl { width:100%; border-collapse:collapse; }
.tbl thead th { padding:9px 18px; text-align:left; font-size:11px; font-weight:700; color:#555a72; text-transform:uppercase; letter-spacing:0.4px; background:rgba(255,255,255,0.02); border-bottom:1px solid var(--border); }
.tbl tbody tr { border-bottom:1px solid rgba(255,255,255,0.04); cursor:pointer; transition:background 0.12s; }
.tbl tbody tr:last-child { border-bottom:none; }
.tbl tbody tr:hover { background:rgba(255,255,255,0.03); }
.tbl tbody td { padding:10px 18px; font-size:13px; color:var(--text); }
.td-name { font-weight:700; }

.pill { display:inline-block; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:700; }
.p-blue   { background:rgba(102,126,234,0.15); color:#818cf8; }
.p-green  { background:rgba(74,222,128,0.12);  color:#4ade80; }
.p-amber  { background:rgba(251,146,60,0.12);  color:#fb923c; }
.p-red    { background:rgba(248,113,113,0.12); color:#f87171; }

/* EXPIRY LIST */
.expiry-item { display:flex; align-items:center; gap:12px; padding:10px 18px; border-bottom:1px solid rgba(255,255,255,0.04); }
.expiry-item:last-child { border-bottom:none; }
.expiry-date { width:38px; height:38px; border:1px solid var(--border); border-radius:9px; display:flex; flex-direction:column; align-items:center; justify-content:center; flex-shrink:0; background:var(--bg3); }
.expiry-day  { font-size:14px; font-weight:800; color:var(--text); line-height:1; }
.expiry-mon  { font-size:9px; color:var(--text2); text-transform:uppercase; }
.expiry-room { font-size:13px; font-weight:700; color:var(--text); }
.expiry-name { font-size:11px; color:var(--text2); }

/* MINI CHART */
.revenue-row { display:flex; align-items:flex-end; gap:5px; height:56px; padding:0 18px 14px; }
.rev-bar-wrap { flex:1; display:flex; flex-direction:column; align-items:center; gap:4px; }
.rev-bar { width:100%; border-radius:3px 3px 0 0; background:linear-gradient(180deg,var(--accent),var(--accent2)); opacity:0.7; min-height:3px; transition:opacity 0.2s; }
.rev-bar:hover { opacity:1; }
.rev-month { font-size:9px; color:var(--text2); }

.empty-txt { text-align:center; padding:22px; color:#555a72; font-size:13px; }

@media (max-width:900px) { .sidebar{transform:translateX(-100%);} .content{margin-left:0;} .stats-grid{grid-template-columns:1fr 1fr;} .main-grid{grid-template-columns:1fr;} }
</style>
</head>
<body>

<aside class="sidebar">
  <div class="sidebar-logo">
    <div class="logo-icon">🏠</div>
    <span class="logo-text">RoomManager</span>
    <span class="logo-ver">v2.0</span>
  </div>
  <div class="sidebar-nav">
    <div class="sidebar-label">Quản lý</div>
    <a href="index.php?controller=dashboard&action=index" class="nav-item active"><span class="ni">📊</span> Tổng quan</a>
    <a href="index.php?controller=phong&action=index"     class="nav-item"><span class="ni">🏠</span> Phòng trọ</a>
    <a href="index.php?controller=nguoithue&action=index" class="nav-item"><span class="ni">👥</span> Người thuê</a>
    <a href="index.php?controller=hopdong&action=index"   class="nav-item"><span class="ni">📋</span> Hợp đồng</a>
    <a href="index.php?controller=hoadon&action=index"    class="nav-item">
      <span class="ni">🧾</span> Hóa đơn
      <?php if (!empty($soHoaDonChua)): ?><span class="nav-badge"><?= $soHoaDonChua ?></span><?php endif; ?>
    </a>
    <div class="sidebar-label">Hệ thống</div>
    <a href="index.php?controller=khutro&action=index"    class="nav-item"><span class="ni">🏘️</span> Khu trọ</a>
    <a href="index.php?controller=thongbao&action=index"  class="nav-item"><span class="ni">🔔</span> Thông báo</a>
    <a href="index.php?controller=baocao&action=index"    class="nav-item"><span class="ni">📈</span> Báo cáo</a>
  </div>
  <div class="sidebar-footer">
    <div class="user-mini">
      <div class="user-avatar"><?= strtoupper(substr($_SESSION['user'] ?? 'A', 0, 1)) ?></div>
      <div><div class="user-name"><?= htmlspecialchars($_SESSION['user'] ?? 'Admin') ?></div><div class="user-role">Quản trị viên</div></div>
    </div>
    <a href="index.php?controller=auth&action=logout" class="logout-btn">🚪 Đăng xuất</a>
  </div>
</aside>

<div class="content">
  <div class="topbar">
    <div class="topbar-title">📊 Tổng quan hệ thống</div>
    <div class="topbar-right">
      <span class="topbar-date">📅 <?= date('d/m/Y') ?></span>
      <a href="index.php?controller=hoadon&action=create" class="btn-outline">⚡ Tạo hóa đơn</a>
      <a href="index.php?controller=phong&action=create"  class="btn-primary">＋ Thêm phòng</a>
    </div>
  </div>

  <div class="page">
    <div class="stats-grid">
      <div class="stat-card" onclick="location.href='index.php?controller=phong&action=index'">
        <div class="stat-row-top"><div class="stat-ico si-blue">🏠</div><div class="stat-change chg-flat">— Ổn định</div></div>
        <div class="stat-val"><?= $tongPhong ?? 0 ?></div><div class="stat-lbl">Tổng số phòng</div>
      </div>
      <div class="stat-card" onclick="location.href='index.php?controller=phong&action=index'">
        <div class="stat-row-top"><div class="stat-ico si-green">✅</div><div class="stat-change chg-up">↑ Tốt</div></div>
        <div class="stat-val"><?= $phongDangThue ?? 0 ?></div><div class="stat-lbl">Đang cho thuê</div>
      </div>
      <div class="stat-card" onclick="location.href='index.php?controller=phong&action=index'">
        <div class="stat-row-top"><div class="stat-ico si-amber">🔑</div><div class="stat-change chg-dn">Còn trống</div></div>
        <div class="stat-val"><?= $phongTrong ?? 0 ?></div><div class="stat-lbl">Phòng trống</div>
      </div>
      <div class="stat-card" onclick="location.href='index.php?controller=baocao&action=index'">
        <div class="stat-row-top"><div class="stat-ico si-purple">💰</div><div class="stat-change chg-up">↑ Thu</div></div>
        <div class="stat-val"><?= number_format(($doanhThuThang??0)/1000000, 1) ?>M</div><div class="stat-lbl">Doanh thu tháng <?= date('n') ?></div>
      </div>
    </div>

    <div class="main-grid">
      <div class="card">
        <div class="card-header">
          <div><div class="card-title">Danh sách phòng</div><div class="card-sub"><?= $tongPhong??0 ?> phòng · <?= $phongDangThue??0 ?> đang thuê</div></div>
          <a href="index.php?controller=phong&action=index" class="card-link">Xem tất cả →</a>
        </div>
        <?php if (!empty($phongs)): ?>
        <table class="tbl">
          <thead><tr><th>Phòng</th><th>Giá thuê</th><th>Trạng thái</th><th></th></tr></thead>
          <tbody>
          <?php foreach (array_slice($phongs,0,7) as $p): ?>
          <tr onclick="location.href='index.php?controller=phong&action=chiTiet&id=<?= $p['id'] ?>'">
            <td><span class="td-name"><?= htmlspecialchars($p['so_phong']) ?></span></td>
            <td><strong><?= number_format($p['gia']) ?>đ</strong></td>
            <td>
              <?php if($p['trang_thai']==='dang_thue'): ?><span class="pill p-blue">Đang thuê</span>
              <?php elseif($p['trang_thai']==='trong'): ?><span class="pill p-green">Còn trống</span>
              <?php else: ?><span class="pill p-amber">Bảo trì</span><?php endif; ?>
            </td>
            <td><a href="index.php?controller=phong&action=chiTiet&id=<?= $p['id'] ?>" class="btn-outline" style="padding:4px 10px;font-size:11px;" onclick="event.stopPropagation()">Chi tiết</a></td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php else: ?>
        <div class="empty-txt">Chưa có phòng nào. <a href="index.php?controller=phong&action=create" style="color:var(--accent);font-weight:700;">Thêm ngay</a></div>
        <?php endif; ?>
        <?php
          $months=['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'];
          $revData=$doanhThuTheoThang??array_fill(0,12,0);
          $maxRev=max($revData)?:1;
        ?>
        <div style="padding:14px 18px 0;border-top:1px solid var(--border);"><div style="font-size:12px;font-weight:700;color:var(--text2);margin-bottom:8px;">Doanh thu theo tháng</div></div>
        <div class="revenue-row">
          <?php foreach($months as $i=>$m): ?>
          <?php $h=max(3,round(($revData[$i]/$maxRev)*48)); ?>
          <div class="rev-bar-wrap">
            <div class="rev-bar" style="height:<?=$h?>px" title="Tháng <?=$i+1?>: <?=number_format($revData[$i])?>đ"></div>
            <div class="rev-month"><?=$m?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <div style="display:flex;flex-direction:column;gap:14px;">
        <div class="card">
          <div class="card-header">
            <div><div class="card-title">Hợp đồng sắp hết hạn</div><div class="card-sub">Trong 30 ngày tới</div></div>
            <a href="index.php?controller=hopdong&action=index" class="card-link">Xem →</a>
          </div>
          <?php if (!empty($sapHetHan)): ?>
            <?php foreach(array_slice($sapHetHan,0,5) as $hd): ?>
            <div class="expiry-item">
              <div class="expiry-date">
                <div class="expiry-day"><?=date('d',strtotime($hd['ngay_ket_thuc']))?></div>
                <div class="expiry-mon"><?=date('M',strtotime($hd['ngay_ket_thuc']))?></div>
              </div>
              <div style="flex:1">
                <div class="expiry-room">P.<?=htmlspecialchars($hd['so_phong'])?></div>
                <div class="expiry-name"><?=htmlspecialchars($hd['ho_ten'])?></div>
              </div>
              <span class="pill p-amber">Sắp HH</span>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-txt">Không có hợp đồng sắp hết hạn</div>
          <?php endif; ?>
        </div>

        <div class="card">
          <div class="card-header">
            <div><div class="card-title">Hóa đơn chưa thu</div><div class="card-sub">Tháng <?=date('n/Y')?></div></div>
            <a href="index.php?controller=hoadon&action=index" class="card-link">Xem →</a>
          </div>
          <?php if (!empty($hoaDonChuaThu)): ?>
            <?php foreach(array_slice($hoaDonChuaThu,0,5) as $hd): ?>
            <div style="display:flex;align-items:center;justify-content:space-between;padding:9px 18px;border-bottom:1px solid rgba(255,255,255,0.04);">
              <div>
                <div style="font-size:13px;font-weight:700;">P.<?=htmlspecialchars($hd['so_phong'])?></div>
                <div style="font-size:11px;color:var(--text2);"><?=htmlspecialchars($hd['ho_ten'])?></div>
              </div>
              <div style="font-size:13px;font-weight:800;color:#f87171;"><?=number_format($hd['tong_tien'])?>đ</div>
            </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="empty-txt">Tất cả đã thanh toán 🎉</div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

</body>
</html>