<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($title ?? 'RoomManager') ?> — Quản lý phòng trọ</title>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
:root{--bg:#0d0f14;--bg2:#13161e;--bg3:#1a1e28;--card:#161922;--border:rgba(255,255,255,0.07);--border2:rgba(255,255,255,0.12);--accent:#4f8ef7;--accent2:#7c5cfc;--green:#22c993;--red:#f75c5c;--amber:#f7a94f;--text:#e8eaf0;--text2:#8b90a0;--text3:#555b6e;--sidebar:240px;--radius:14px;--radius-sm:10px;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Be Vietnam Pro',sans-serif;background:var(--bg);color:var(--text);min-height:100vh;display:flex;overflow-x:hidden;}
/* SIDEBAR */
.sidebar{width:var(--sidebar);min-height:100vh;background:var(--bg2);border-right:1px solid var(--border);display:flex;flex-direction:column;position:fixed;left:0;top:0;bottom:0;z-index:100;transition:transform .3s;}
.sidebar-logo{padding:18px 18px 14px;display:flex;align-items:center;gap:10px;border-bottom:1px solid var(--border);text-decoration:none;}
.logo-box{width:36px;height:36px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:17px;box-shadow:0 4px 14px rgba(79,142,247,.3);flex-shrink:0;}
.logo-name{font-size:16px;font-weight:800;background:linear-gradient(135deg,#fff,#aab4d4);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.logo-ver{font-size:10px;color:var(--text3);margin-left:auto;background:var(--bg3);padding:2px 6px;border-radius:4px;border:1px solid var(--border);}
.sidebar-nav{flex:1;padding:10px;overflow-y:auto;}
.sidebar-nav::-webkit-scrollbar{width:3px;}
.sidebar-nav::-webkit-scrollbar-thumb{background:var(--bg3);}
.nav-lbl{font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:1.2px;padding:10px 10px 5px;}
.nav-item{display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:var(--radius-sm);color:var(--text2);font-size:13px;font-weight:500;text-decoration:none;cursor:pointer;transition:all .15s;margin-bottom:1px;position:relative;}
.nav-item:hover{background:rgba(255,255,255,.04);color:var(--text);}
.nav-item.active{background:linear-gradient(135deg,rgba(79,142,247,.14),rgba(124,92,252,.09));color:var(--accent);border:1px solid rgba(79,142,247,.2);}
.nav-item.active::before{content:'';position:absolute;left:0;top:22%;bottom:22%;width:3px;background:var(--accent);border-radius:0 3px 3px 0;}
.nav-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;}
.nav-item.active .nav-icon{background:rgba(79,142,247,.13);}
.nav-badge{font-size:10px;font-weight:700;padding:2px 6px;border-radius:10px;margin-left:auto;}
.nb-red{background:rgba(247,92,92,.2);color:var(--red);}
.nb-green{background:rgba(34,201,147,.2);color:var(--green);}
.nb-amber{background:rgba(247,169,79,.2);color:var(--amber);}
.nav-divider{height:1px;background:var(--border);margin:6px 8px;}
.sidebar-user{padding:10px;border-top:1px solid var(--border);}
.user-card{display:flex;align-items:center;gap:9px;padding:9px 11px;background:var(--bg3);border-radius:var(--radius-sm);border:1px solid var(--border);cursor:pointer;transition:border-color .2s;text-decoration:none;}
.user-card:hover{border-color:rgba(79,142,247,.3);}
.user-av{width:32px;height:32px;background:linear-gradient(135deg,var(--accent),var(--accent2));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;}
.user-name{font-size:13px;font-weight:600;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.user-role{font-size:11px;color:var(--text3);}
/* MAIN */
.main{margin-left:var(--sidebar);flex:1;min-width:0;width:calc(100% - var(--sidebar));min-height:100vh;display:flex;flex-direction:column;}
/* TOPBAR */
.topbar{height:58px;min-width:0;background:var(--bg2);border-bottom:1px solid var(--border);display:flex;align-items:center;padding:0 24px;gap:14px;position:sticky;top:0;z-index:50;}
.topbar-left{display:flex;align-items:center;gap:10px;flex:1;min-width:0;}
.menu-btn{display:none;width:34px;height:34px;background:var(--bg3);border:1px solid var(--border);border-radius:8px;cursor:pointer;align-items:center;justify-content:center;font-size:15px;color:var(--text2);}
.breadcrumb{display:flex;align-items:center;gap:6px;min-width:0;overflow:hidden;white-space:nowrap;font-size:13px;color:var(--text3);}
.breadcrumb-cur{min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text);font-weight:600;}
.tb-search{display:flex;align-items:center;gap:7px;background:var(--bg3);border:1px solid var(--border);border-radius:9px;padding:6px 13px;width:210px;transition:border-color .2s,width .3s;}
.tb-search:focus-within{border-color:rgba(79,142,247,.4);width:270px;}
.tb-search input{background:none;border:none;outline:none;font-size:13px;color:var(--text);font-family:inherit;width:100%;}
.tb-search input::placeholder{color:var(--text3);}
.tb-search{position:relative;}
.global-search-results{position:absolute;top:calc(100% + 8px);right:0;width:430px;max-width:calc(100vw - 40px);background:var(--card);border:1px solid rgba(79,142,247,.22);border-radius:12px;box-shadow:0 18px 50px rgba(0,0,0,.45);overflow:hidden;display:none;z-index:120;}
.global-search-results.show{display:block;}
.gs-head{padding:10px 13px;border-bottom:1px solid var(--border);font-size:11px;color:var(--text3);font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
.gs-list{max-height:360px;overflow:auto;}
.gs-item{display:flex;gap:10px;padding:11px 13px;border-bottom:1px solid rgba(255,255,255,.04);text-decoration:none;color:var(--text);transition:.12s;}
.gs-item:hover,.gs-item.active{background:rgba(79,142,247,.1);}
.gs-item:last-child{border-bottom:none;}
.gs-av{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),var(--accent2));display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;color:#fff;flex-shrink:0;}
.gs-main{min-width:0;flex:1;}
.gs-name{font-size:13px;font-weight:800;color:var(--text);display:flex;gap:7px;align-items:center;margin-bottom:3px;}
.gs-meta{font-size:11px;color:var(--text2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;line-height:1.6;}
.gs-pill{font-size:10px;padding:2px 7px;border-radius:99px;background:rgba(34,201,147,.13);color:var(--green);font-weight:700;}
.gs-pill.off{background:rgba(139,144,160,.13);color:var(--text3);}
.gs-empty{padding:16px;text-align:center;color:var(--text3);font-size:12px;}
.gs-all{display:block;padding:10px 13px;text-align:center;border-top:1px solid var(--border);font-size:12px;color:var(--accent);text-decoration:none;font-weight:700;background:rgba(79,142,247,.04);}
.gs-all:hover{background:rgba(79,142,247,.1);}
.topbar-right{display:flex;align-items:center;gap:7px;flex-shrink:0;}
.icon-btn{width:34px;height:34px;flex-shrink:0;background:var(--bg3);border:1px solid var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:15px;cursor:pointer;transition:all .15s;text-decoration:none;position:relative;}
.icon-btn:hover{background:rgba(255,255,255,.05);border-color:var(--border2);}
.notif-dot{position:absolute;top:6px;right:6px;width:7px;height:7px;background:var(--red);border-radius:50%;border:1.5px solid var(--bg2);animation:pulse 2s infinite;}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.tb-date{font-size:12px;color:var(--text3);padding-left:10px;border-left:1px solid var(--border);margin-left:2px;}
/* CONTENT */
.page-content{flex:1;min-width:0;max-width:100%;padding:26px;overflow-x:hidden;}
/* ALERT MSG */
.msg-alert{padding:10px 14px;border-radius:10px;font-size:13px;display:flex;align-items:center;gap:8px;margin-bottom:18px;animation:fadeIn .3s ease;}
@keyframes fadeIn{from{opacity:0;transform:translateY(-6px)}to{opacity:1;transform:translateY(0)}}
.msg-success{background:rgba(34,201,147,.1);border:1px solid rgba(34,201,147,.25);color:var(--green);}
.msg-error{background:rgba(247,92,92,.1);border:1px solid rgba(247,92,92,.25);color:var(--red);}
.msg-info{background:rgba(79,142,247,.1);border:1px solid rgba(79,142,247,.25);color:var(--accent);}
/* PAGE HEADER */
.page-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px;}
.page-title h1{font-size:21px;font-weight:800;color:var(--text);letter-spacing:-.3px;margin-bottom:2px;}
.page-title p{font-size:13px;color:var(--text2);}
.header-actions{display:flex;gap:9px;align-items:center;}
/* BUTTONS */
.btn{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:var(--radius-sm);font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;transition:all .15s;font-family:inherit;border:none;}
.btn-primary{background:linear-gradient(135deg,var(--accent),var(--accent2));color:#fff;box-shadow:0 5px 18px rgba(79,142,247,.28);}
.btn-primary:hover{opacity:.9;transform:translateY(-1px);}
.btn-outline{background:transparent;color:var(--text2);border:1px solid var(--border);}
.btn-outline:hover{background:rgba(255,255,255,.04);color:var(--text);border-color:var(--border2);}
.btn-danger{background:rgba(247,92,92,.13);color:var(--red);border:1px solid rgba(247,92,92,.22);}
.btn-danger:hover{background:rgba(247,92,92,.22);}
.btn-success{background:rgba(34,201,147,.13);color:var(--green);border:1px solid rgba(34,201,147,.22);}
.btn-success:hover{background:rgba(34,201,147,.22);}
.btn-sm{padding:6px 11px;font-size:12px;}
.btn-xs{padding:4px 9px;font-size:11px;}
/* CARD */
.card{max-width:100%;background:var(--card);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:18px;}
.card-header{display:flex;align-items:center;justify-content:space-between;padding:15px 20px;border-bottom:1px solid var(--border);}
.card-title{font-size:14px;font-weight:700;color:var(--text);}
.card-sub{font-size:12px;color:var(--text3);margin-top:2px;}
.card-link{font-size:12px;color:var(--accent);text-decoration:none;font-weight:500;}
.card-link:hover{text-decoration:underline;}
.card-body{padding:20px;}
/* TABLE */
.tbl{width:100%;border-collapse:collapse;}
.tbl thead tr{background:rgba(255,255,255,.02);}
.tbl th{padding:9px 18px;font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.8px;text-align:left;border-bottom:1px solid var(--border);}
.tbl td{padding:12px 18px;font-size:13px;color:var(--text2);border-bottom:1px solid rgba(255,255,255,.03);vertical-align:middle;}
.tbl tbody tr{transition:background .1s;cursor:pointer;}
.tbl tbody tr:hover{background:rgba(255,255,255,.022);}
.tbl tbody tr:last-child td{border-bottom:none;}
.td-name{font-weight:600;color:var(--text);font-size:14px;}
/* STATUS PILLS */
.pill{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:20px;font-size:11px;font-weight:600;}
.pill::before{content:'';width:5px;height:5px;border-radius:50%;}
.p-blue{background:rgba(79,142,247,.12);color:var(--accent);}
.p-blue::before{background:var(--accent);}
.p-green{background:rgba(34,201,147,.12);color:var(--green);}
.p-green::before{background:var(--green);}
.p-red{background:rgba(247,92,92,.12);color:var(--red);}
.p-red::before{background:var(--red);animation:pulse 1.5s infinite;}
.p-amber{background:rgba(247,169,79,.12);color:var(--amber);}
.p-amber::before{background:var(--amber);}
.p-purple{background:rgba(124,92,252,.12);color:var(--accent2);}
.p-purple::before{background:var(--accent2);}
/* AVATAR */
.av{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;}
.av-row{display:flex;align-items:center;gap:8px;}
/* FORM */
.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.form-group{margin-bottom:16px;}
.form-label{display:block;font-size:11px;font-weight:700;color:var(--text2);text-transform:uppercase;letter-spacing:.5px;margin-bottom:7px;}
.form-control{width:100%;background:rgba(255,255,255,.04);border:1.5px solid var(--border);border-radius:10px;padding:10px 14px;font-size:13px;font-family:inherit;color:var(--text);outline:none;transition:border-color .2s,background .2s,box-shadow .2s;}
.form-control::placeholder{color:var(--text3);}
.form-control:focus{border-color:rgba(79,142,247,.5);background:rgba(79,142,247,.04);box-shadow:0 0 0 3px rgba(79,142,247,.08);}
select.form-control{-webkit-appearance:none;cursor:pointer;}
textarea.form-control{resize:vertical;min-height:80px;}
/* STAT GRID */
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:20px;}
.stat-card{background:var(--card);border:1px solid var(--border);border-radius:var(--radius);padding:18px;cursor:pointer;transition:transform .2s,border-color .2s;}
.stat-card:hover{transform:translateY(-2px);border-color:var(--border2);}
.stat-icon{width:38px;height:38px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:17px;margin-bottom:12px;}
.si-blue{background:rgba(79,142,247,.12);}
.si-green{background:rgba(34,201,147,.12);}
.si-amber{background:rgba(247,169,79,.12);}
.si-purple{background:rgba(124,92,252,.12);}
.stat-val{font-size:26px;font-weight:800;color:var(--text);letter-spacing:-.5px;line-height:1;margin-bottom:3px;}
.stat-lbl{font-size:12px;color:var(--text2);}
.stat-chg{font-size:11px;font-weight:600;margin-top:8px;}
.chg-up{color:var(--green);}
.chg-dn{color:var(--red);}
.chg-flat{color:var(--text3);}
/* RESPONSIVE */
@media(max-width:900px){
  .sidebar{transform:translateX(-100%);}
  .sidebar.open{transform:translateX(0);}
  .main{margin-left:0;width:100%;min-width:0;}
  .menu-btn{display:flex;}
  .tb-search{display:none;}
  .stat-grid{grid-template-columns:1fr 1fr;}
}
@media(max-width:600px){
  .stat-grid{grid-template-columns:1fr 1fr;}
  .page-content{padding:12px;min-width:0;max-width:100%;overflow-x:hidden;}
  .topbar{padding:0 12px;gap:8px;}
  .topbar-left{gap:7px;}
  .breadcrumb{gap:3px;font-size:12px;}
  .breadcrumb > span:first-child,.breadcrumb > span:nth-child(2){display:none;}
  .topbar-right{gap:5px;}
  .tb-date{display:none;}
  .form-grid{grid-template-columns:1fr;}
  /* Page header stack */
  .page-header{flex-direction:column;align-items:flex-start;gap:10px;}
  .page-header h1{font-size:17px;}
  .header-actions{flex-wrap:wrap;}
  /* Responsive table — chuyển thành card dọc */
  .tbl-r thead{display:none;}
  .tbl-r tbody tr{display:block;border:1px solid var(--border);border-radius:var(--radius-sm);margin-bottom:10px;background:var(--bg2);cursor:default;}
  .tbl-r tbody tr:last-child{margin-bottom:0;}
  .tbl-r tbody tr:hover{background:var(--bg2);}
  .tbl-r td{display:flex;justify-content:space-between;align-items:center;padding:9px 14px;border-bottom:1px solid rgba(255,255,255,.04);min-height:38px;text-align:right;}
  .tbl-r td:last-child{border-bottom:none;}
  .tbl-r td::before{content:attr(data-label);font-size:10px;font-weight:700;color:var(--text3);text-transform:uppercase;letter-spacing:.5px;flex-shrink:0;margin-right:10px;min-width:70px;text-align:left;}
  .tbl-r td.mob-hide{display:none;}
  .tbl-r td.mob-actions{justify-content:flex-end;flex-wrap:wrap;gap:5px;padding:10px 14px;}
  .tbl-r td.mob-actions::before{display:none;}
  .tbl-r td.mob-header{background:rgba(79,142,247,.07);padding:10px 14px;font-weight:700;color:var(--text);}
  .tbl-r td.mob-header::before{display:none;}
}
::-webkit-scrollbar{width:4px;}
::-webkit-scrollbar-thumb{background:var(--bg3);border-radius:2px;}
</style>
</head>
<body>

<!-- SIDEBAR OVERLAY -->
<div id="overlay" onclick="closeSidebar()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;"></div>

<!-- ═══ SIDEBAR ═══ -->
<aside class="sidebar" id="sidebar">
  <?php
    $__role = $_SESSION['vai_tro'] ?? 'user';
    $__soChuyenPhongChoDuyet = $__role === 'user'
      ? (new ChuyenPhongModel())->countPendingByUser((int)($_SESSION['user_id'] ?? 0))
      : (new ChuyenPhongModel())->countPending();
    $__soOCungChoDuyet = $__role === 'user'
      ? (new YeuCauNguoiOCungModel())->countPendingByUser((int)($_SESSION['user_id'] ?? 0))
      : (new YeuCauNguoiOCungModel())->countPending();
  ?>
  <a class="sidebar-logo" href="<?= $__role === 'user' ? 'index.php?controller=user&action=index' : 'index.php?controller=dashboard&action=index' ?>">
    <div class="logo-box">🏠</div>
    <span class="logo-name">RoomManager</span>
    <span class="logo-ver">v2.0</span>
  </a>

  <nav class="sidebar-nav">
    <?php if ($__role === 'user'): ?>
    <!-- NAV NGƯỜI DÙNG -->
    <div class="nav-lbl">Tổng quan</div>
    <a class="nav-item <?= (($_GET['controller']??'')==='user' && in_array($_GET['action']??'index', ['index','']))?'active':'' ?>"
       href="index.php?controller=user&action=index">
      <div class="nav-icon">🏠</div>Phòng của tôi
    </a>
    <div class="nav-divider"></div>
    <div class="nav-lbl">Xem</div>
    <a class="nav-item <?= (($_GET['controller']??'')==='user' && in_array($_GET['action']??'', ['khuPhong','phong']))?'active':'' ?>"
       href="index.php?controller=user&action=khuPhong">
      <div class="nav-icon">🏘</div>Khu & Phòng trọ
    </a>
    <a class="nav-item <?= (($_GET['controller']??'')==='hoadon' && ($_GET['action']??'')==='lichSu')?'active':'' ?>"
       href="index.php?controller=hoadon&action=lichSu">
      <div class="nav-icon">📋</div>Lịch sử thanh toán
    </a>
    <a class="nav-item <?= (($_GET['controller']??'')==='user' && ($_GET['action']??'')==='hopDong')?'active':'' ?>"
       href="index.php?controller=user&action=hopDong">
      <div class="nav-icon">📄</div>Hợp đồng
    </a>
    <a class="nav-item <?= (($_GET['controller']??'')==='chuyenphong')?'active':'' ?>"
       href="index.php?controller=chuyenphong&action=index">
      <div class="nav-icon">🔄</div>Chuyển phòng
      <?php if ($__soChuyenPhongChoDuyet > 0): ?><span class="nav-badge nb-amber"><?= $__soChuyenPhongChoDuyet ?></span><?php endif; ?>
    </a>
    <a class="nav-item <?= (($_GET['controller']??'')==='thongbao')?'active':'' ?>"
       href="index.php?controller=thongbao&action=index">
      <div class="nav-icon">📢</div>bảo trì
    </a>
    <a class="nav-item <?= (($_GET['controller']??'')==='chat')?'active':'' ?>"
       href="index.php?controller=chat&action=index">
      <div class="nav-icon">💬</div>Chat nhóm
    </a>
    <?php else: ?>
    <!-- NAV ADMIN/CHỦ TRỌ -->
    <div class="nav-lbl">Tổng quan</div>
    <a class="nav-item <?= (($_GET['controller']??'dashboard')==='dashboard')?'active':'' ?>"
       href="index.php?controller=dashboard&action=index">
      <div class="nav-icon">🏠</div>Home
    </a>

    <div class="nav-divider"></div>
    <div class="nav-lbl">Quản lý</div>

    <a class="nav-item <?= (in_array($_GET['controller']??'', ['khutro','phong']))?'active':'' ?>"
       href="index.php?controller=khutro&action=index">
      <div class="nav-icon">🏘</div>Khu & Phòng trọ
    </a>

    <a class="nav-item <?= (($_GET['controller']??'')==='nguoithue' && ($_GET['action']??'index')!=='yeuCauOCung')?'active':'' ?>"
       href="index.php?controller=nguoithue&action=index">
      <div class="nav-icon">👥</div>Người thuê
      <?php if ($__soOCungChoDuyet > 0): ?><span class="nav-badge nb-amber"><?= $__soOCungChoDuyet ?></span><?php endif; ?>
    </a>

    <a class="nav-item <?= (($_GET['controller']??'')==='nguoithue' && ($_GET['action']??'')==='yeuCauOCung')?'active':'' ?>"
       href="index.php?controller=nguoithue&action=yeuCauOCung">
      <div class="nav-icon">➕</div>Duyệt ở cùng
      <?php if ($__soOCungChoDuyet > 0): ?><span class="nav-badge nb-amber"><?= $__soOCungChoDuyet ?></span><?php endif; ?>
    </a>

    <a class="nav-item <?= (($_GET['controller']??'')==='hopdong')?'active':'' ?>"
       href="index.php?controller=hopdong&action=index">
      <div class="nav-icon">📄</div>Hợp đồng
    </a>

    <a class="nav-item <?= (($_GET['controller']??'')==='chuyenphong')?'active':'' ?>"
       href="index.php?controller=chuyenphong&action=index">
      <div class="nav-icon">🔄</div>Chuyển phòng
      <?php if ($__soChuyenPhongChoDuyet > 0): ?><span class="nav-badge nb-amber"><?= $__soChuyenPhongChoDuyet ?></span><?php endif; ?>
    </a>

    <a class="nav-item <?= (($_GET['controller']??'')==='hoadon')?'active':'' ?>"
       href="index.php?controller=hoadon&action=index">
      <div class="nav-icon">💵</div>Tổng chi phí
    </a>

    <a class="nav-item <?= (($_GET['controller']??'')==='hoadon' && ($_GET['action']??'')==='lichSu')?'active':'' ?>"
       href="index.php?controller=hoadon&action=lichSu">
      <div class="nav-icon">📋</div>Lịch sử thanh toán
    </a>

    <a class="nav-item <?= (($_GET['controller']??'')==='thongbao')?'active':'' ?>"
       href="index.php?controller=thongbao&action=index">
      <div class="nav-icon">🔧</div>Bảo trì
    </a>

    <a class="nav-item <?= (($_GET['controller']??'')==='chat')?'active':'' ?>"
       href="index.php?controller=chat&action=index">
      <div class="nav-icon">💬</div>Chat nhóm
    </a>

    <div class="nav-divider"></div>
    <div class="nav-lbl">Thống kê</div>

    <a class="nav-item <?= (($_GET['controller']??'')==='baocao')?'active':'' ?>"
       href="index.php?controller=baocao&action=index">
      <div class="nav-icon">📈</div>Báo cáo doanh thu
    </a>

    <div class="nav-divider"></div>
    <div class="nav-lbl">Cài đặt</div>

    <a class="nav-item <?= (($_GET['controller']??'')==='dongia')?'active':'' ?>"
       href="index.php?controller=dongia&action=index">
      <div class="nav-icon">⚙️</div>Đơn giá điện nước
    </a>
    <?php endif; ?>
  </nav>

  <div class="sidebar-user">
    <?php if($__role === 'user'): ?>
    <a class="user-card" href="index.php?controller=user&action=profile"
       style="<?= (($_GET['action']??'')==='profile') ? 'border-color:rgba(79,142,247,.4);background:rgba(79,142,247,.08);' : '' ?>">
      <div class="user-av"><?= strtoupper(substr($_SESSION['user']??'A',0,1)) ?></div>
      <div style="flex:1;min-width:0;">
        <div class="user-name"><?= htmlspecialchars($_SESSION['ho_ten'] ?? $_SESSION['user'] ?? 'Người dùng') ?></div>
        <?php $__roleLabel = ['quan_ly'=>'Quản trị viên','chu_tro'=>'Chủ trọ','user'=>'Người thuê']; ?>
        <div class="user-role"><?= $__roleLabel[$__role] ?? 'Người dùng' ?> · Hồ sơ</div>
      </div>
      <a href="index.php?controller=auth&action=logout"
         title="Đăng xuất"
         onclick="event.stopPropagation();"
         style="flex-shrink:0;width:28px;height:28px;border-radius:8px;display:flex;align-items:center;justify-content:center;background:rgba(239,68,68,.12);border:1px solid rgba(239,68,68,.2);color:#ef4444;font-size:14px;text-decoration:none;transition:.2s;"
         onmouseover="this.style.background='rgba(239,68,68,.25)'" onmouseout="this.style.background='rgba(239,68,68,.12)'">⏻</a>
    </a>
    <?php else: ?>
    <a class="user-card" href="index.php?controller=auth&action=logout">
      <div class="user-av"><?= strtoupper(substr($_SESSION['user']??'A',0,1)) ?></div>
      <div>
        <div class="user-name"><?= htmlspecialchars($_SESSION['ho_ten'] ?? $_SESSION['user'] ?? 'Người dùng') ?></div>
        <?php $__roleLabel = ['quan_ly'=>'Quản trị viên','chu_tro'=>'Chủ trọ','user'=>'Người thuê']; ?>
        <div class="user-role"><?= $__roleLabel[$__role] ?? 'Người dùng' ?> · Đăng xuất</div>
      </div>
    </a>
    <?php endif; ?>
  </div>
</aside>

<!-- ═══ MAIN ═══ -->
<div class="main">
  <header class="topbar">
    <div class="topbar-left">
      <button class="menu-btn" onclick="toggleSidebar()">☰</button>
      <div class="breadcrumb">
        <span>RoomManager</span>
        <span style="opacity:.4;margin:0 2px;">/</span>
        <span class="breadcrumb-cur"><?= htmlspecialchars($title??'Dashboard') ?></span>
      </div>
    </div>
    <div class="tb-search" id="globalSearchBox">
      <span style="color:var(--text3);font-size:13px">🔍</span>
      <input type="text" id="globalSearchInput" autocomplete="off" placeholder="<?= $__role === 'user' ? 'Tìm phòng...' : 'Tìm người thuê, phòng, SĐT...' ?>"/>
      <?php if ($__role !== 'user'): ?>
      <div class="global-search-results" id="globalSearchResults">
        <div class="gs-head">Tìm kiếm người thuê</div>
        <div class="gs-list" id="globalSearchList"></div>
        <a class="gs-all" id="globalSearchAll" href="index.php?controller=nguoithue&action=index">Xem tất cả trong danh sách người thuê ›</a>
      </div>
      <?php endif; ?>
    </div>
    <div class="topbar-right">
      <?php if ($__role !== 'user'): ?>
      <a class="icon-btn" href="index.php?controller=nguoithue&action=yeuCauOCung" title="Yêu cầu thêm người ở cùng">➕<?php if ($__soOCungChoDuyet > 0): ?><span class="notif-dot"></span><?php endif; ?></a>
      <a class="icon-btn" href="index.php?controller=chuyenphong&action=index" title="Yêu cầu chuyển phòng">🔄<?php if ($__soChuyenPhongChoDuyet > 0): ?><span class="notif-dot"></span><?php endif; ?></a>
      <a class="icon-btn" href="index.php?controller=hoadon&action=congNo" title="Công nợ">🔔</a>
      <a class="icon-btn" href="index.php?controller=baocao&action=index" title="Báo cáo">📊</a>
      <?php endif; ?>
      <a class="icon-btn" href="index.php?controller=auth&action=logout" title="Đăng xuất">⏻</a>
      <span class="tb-date"><?= date('d/m/Y') ?></span>
    </div>
  </header>

  <div class="page-content">
    <?php if(!empty($_GET['msg'])):
      $map=['created'=>['success','✓ Thêm mới thành công!'],
            'updated'=>['success','✓ Cập nhật thành công!'],
            'deleted'       =>['error','✓ Đã xóa thành công!'],
          'cannot_delete' =>['error','⚠ Không thể xóa! Người thuê đang có hợp đồng hiệu lực.'],
            'paid'   =>['success','✓ Đã đánh dấu thanh toán!'],
            'ended'  =>['success','✓ Đã kết thúc hợp đồng!'],
            'transfer_sent'      =>['success','✓ Đã gửi yêu cầu chuyển phòng cho quản lý!'],
            'transfer_cancelled' =>['success','✓ Đã hủy yêu cầu chuyển phòng!'],
            'transfer_approved'  =>['success','✓ Đã duyệt và cập nhật phòng mới thành công!'],
            'transfer_rejected'  =>['info','Đã từ chối yêu cầu chuyển phòng.'],
            'transfer_error'     =>['error','⚠ Không thể xử lý yêu cầu chuyển phòng.'],
            'roommate_sent'      =>['success','✓ Đã gửi yêu cầu thêm người ở cùng cho quản lý!'],
            'roommate_cancelled' =>['success','✓ Đã hủy yêu cầu thêm người ở cùng!'],
            'roommate_approved'  =>['success','✓ Đã duyệt và thêm người ở cùng vào phòng!'],
            'roommate_rejected'  =>['info','Đã từ chối yêu cầu thêm người ở cùng.'],
            'roommate_error'     =>['error','⚠ Không thể xử lý yêu cầu thêm người ở cùng.']];
      $m=$map[$_GET['msg']]??['info','Thao tác thành công!'];
    ?>
    <div class="msg-alert msg-<?= $m[0] ?>" id="autoMsg">
      <?= $m[1] ?>
    </div>
    <script>setTimeout(()=>{const e=document.getElementById('autoMsg');if(e){e.style.transition='opacity .4s';e.style.opacity='0';setTimeout(()=>e.remove(),400);}},4000);</script>
    <?php endif; ?>
    <?php if($__role !== 'user'): ?>
    <script>
    (function(){
      const input = document.getElementById('globalSearchInput');
      const box = document.getElementById('globalSearchResults');
      const list = document.getElementById('globalSearchList');
      const all = document.getElementById('globalSearchAll');
      if (!input || !box || !list) return;

      let timer = null;
      let activeIndex = -1;

      const esc = (s) => String(s || '').replace(/[&<>"']/g, c => ({
        '&':'&amp;', '<':'&lt;', '>':'&gt;', '"':'&quot;', "'":'&#039;'
      }[c]));

      function showBox() { box.classList.add('show'); }
      function hideBox() { box.classList.remove('show'); activeIndex = -1; }

      function setActive(index) {
        const items = list.querySelectorAll('.gs-item');
        items.forEach(i => i.classList.remove('active'));
        activeIndex = index;
        if (items[activeIndex]) items[activeIndex].classList.add('active');
      }

      function render(results, q) {
        if (all) all.href = 'index.php?controller=nguoithue&action=index&kw=' + encodeURIComponent(q);
        if (!q || q.length < 2) {
          list.innerHTML = '<div class="gs-empty">Nhập ít nhất 2 ký tự để tìm người thuê</div>';
          showBox();
          return;
        }
        if (!results.length) {
          list.innerHTML = '<div class="gs-empty">Không tìm thấy người thuê phù hợp</div>';
          showBox();
          return;
        }
        list.innerHTML = results.map(r => {
          const room = r.room ? `Phòng ${esc(r.room)}${r.area ? ' · ' + esc(r.area) : ''}` : 'Chưa có phòng hiệu lực';
          const contact = [r.phone ? 'SĐT: ' + r.phone : '', r.cccd ? 'CCCD: ' + r.cccd : '', r.email ? 'Email: ' + r.email : ''].filter(Boolean).join(' · ');
          const init = esc((r.name || '?').trim().charAt(0).toUpperCase());
          const typeLabel = r.typeLabel || (r.type === 'roommate' ? 'Người ở cùng' : 'Người thuê chính');
          const statusText = r.active ? 'Đang thuê' : 'Chưa thuê';
          return `<a class="gs-item" href="${esc(r.url)}">
            <div class="gs-av">${init}</div>
            <div class="gs-main">
              <div class="gs-name">${esc(r.name)} <span class="gs-pill ${r.type === 'roommate' ? 'off' : ''}">${esc(typeLabel)}</span> <span class="gs-pill ${r.active ? '' : 'off'}">${statusText}</span></div>
              <div class="gs-meta">${room}</div>
              <div class="gs-meta">${esc(contact || r.username || 'Chưa có thông tin liên hệ')}</div>
            </div>
          </a>`;
        }).join('');
        setActive(-1);
        showBox();
      }

      async function search(q) {
        if (!q) { hideBox(); return; }
        if (q.length < 2) { render([], q); return; }
        list.innerHTML = '<div class="gs-empty">Đang tìm...</div>';
        showBox();
        try {
          const res = await fetch('index.php?controller=nguoithue&action=searchQuick&q=' + encodeURIComponent(q));
          const data = await res.json();
          render(data.ok ? (data.results || []) : [], q);
        } catch(e) {
          list.innerHTML = '<div class="gs-empty">Không tải được kết quả tìm kiếm</div>';
          showBox();
        }
      }

      input.addEventListener('input', () => {
        const q = input.value.trim();
        clearTimeout(timer);
        timer = setTimeout(() => search(q), 220);
      });

      input.addEventListener('keydown', (e) => {
        const items = list.querySelectorAll('.gs-item');
        if (e.key === 'ArrowDown' && items.length) {
          e.preventDefault();
          setActive(Math.min(activeIndex + 1, items.length - 1));
        } else if (e.key === 'ArrowUp' && items.length) {
          e.preventDefault();
          setActive(Math.max(activeIndex - 1, 0));
        } else if (e.key === 'Enter') {
          e.preventDefault();
          if (items[activeIndex]) {
            window.location.href = items[activeIndex].href;
          } else {
            window.location.href = 'index.php?controller=nguoithue&action=index&kw=' + encodeURIComponent(input.value.trim());
          }
        } else if (e.key === 'Escape') {
          hideBox();
        }
      });

      document.addEventListener('click', (e) => {
        const wrap = document.getElementById('globalSearchBox');
        if (wrap && !wrap.contains(e.target)) hideBox();
      });
    })();
    </script>
    <?php endif; ?>
