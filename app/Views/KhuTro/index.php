<?php
$title = 'Quản lý khu trọ';
require_once 'app/Views/Layouts/header.php';
?>
<style>
.khu-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:18px;}
.khu-card{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden;transition:transform .2s,box-shadow .2s;}
.khu-card:hover{transform:translateY(-3px);box-shadow:0 14px 36px rgba(0,0,0,.3);}
.khu-head{height:80px;position:relative;overflow:hidden;}
.khu-head-bg{position:absolute;inset:0;opacity:.12;background:repeating-linear-gradient(45deg,transparent,transparent 8px,rgba(255,255,255,.5) 8px,rgba(255,255,255,.5) 9px);}
.khu-badge-ma{position:absolute;top:50%;left:18px;transform:translateY(-50%);width:48px;height:48px;border-radius:12px;background:rgba(255,255,255,.2);border:2px solid rgba(255,255,255,.35);display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:900;color:#fff;letter-spacing:-.5px;}
.khu-name{position:absolute;top:50%;left:80px;transform:translateY(-50%);}
.khu-name-txt{font-size:18px;font-weight:800;color:#fff;letter-spacing:-.2px;}
.khu-name-sub{font-size:11px;color:rgba(255,255,255,.7);margin-top:2px;}
.stat3{display:grid;grid-template-columns:repeat(3,1fr);gap:8px;margin-bottom:14px;}
.sbox{background:var(--bg3);border:1px solid var(--border);border-radius:9px;padding:10px 8px;text-align:center;}
.sval{font-size:22px;font-weight:800;line-height:1;margin-bottom:3px;}
.slbl{font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;}
.fill-bar{height:6px;background:rgba(255,255,255,.06);border-radius:3px;overflow:hidden;margin-bottom:14px;}
.fill-inner{height:100%;border-radius:3px;transition:width .5s;}
.addr{display:flex;align-items:flex-start;gap:7px;font-size:12px;color:var(--text2);margin-bottom:14px;line-height:1.4;}
</style>

<div class="page-header">
  <div class="page-title">
    <h1>Quản lý khu trọ</h1>
    <p>Tổng <?=count($list??[])?> khu · Số phòng tự động theo mã khu (A101, B201...)</p>
  </div>
  <a href="index.php?controller=khutro&action=create" class="btn btn-primary">＋ Thêm khu trọ</a>
</div>

<?php if(!empty($list)): ?>
<div class="khu-grid">
<?php
$grads=[
  ['#4f8ef7','#7c5cfc'],['#22c993','#2dd4bf'],['#f7a94f','#f75c5c'],
  ['#7c5cfc','#f472b6'],['#f472b6','#f75c5c'],['#2dd4bf','#22c993'],
];
foreach($list as $i=>$khu):
  $g  = $grads[$i % count($grads)];
  $gc = "linear-gradient(135deg,{$g[0]},{$g[1]})";
  $pct= $khu['so_phong']>0 ? round($khu['dang_thue']/$khu['so_phong']*100) : 0;
?>
<div class="khu-card">
  <!-- Header -->
  <div class="khu-head" style="background:<?=$gc?>;">
    <div class="khu-head-bg"></div>
    <div class="khu-badge-ma"><?=htmlspecialchars($khu['ma_khu'])?></div>
    <div class="khu-name">
      <div class="khu-name-txt"><?=htmlspecialchars($khu['ten_khu'])?></div>
      <div class="khu-name-sub">Phòng: <?=htmlspecialchars($khu['ma_khu'])?>101, <?=htmlspecialchars($khu['ma_khu'])?>102...</div>
    </div>
    <!-- Nút actions top-right -->
    <div style="position:absolute;top:10px;right:12px;display:flex;gap:6px;">
      <a href="index.php?controller=khutro&action=edit&id=<?=$khu['id']?>"
         style="background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);color:#fff;font-size:11px;font-weight:600;padding:4px 10px;border-radius:7px;text-decoration:none;">✏ Sửa</a>
    </div>
  </div>

  <div style="padding:16px;">
    <!-- Địa chỉ -->
    <?php if(!empty($khu['dia_chi'])): ?>
    <div class="addr"><span style="flex-shrink:0;">📍</span><span><?=htmlspecialchars($khu['dia_chi'])?></span></div>
    <?php endif; ?>

    <!-- Stats -->
    <div class="stat3">
      <div class="sbox">
        <div class="sval" style="color:var(--text);"><?=$khu['so_phong']?></div>
        <div class="slbl" style="color:var(--text3);">Tổng phòng</div>
      </div>
      <div class="sbox" style="background:rgba(34,201,147,.07);border-color:rgba(34,201,147,.15);">
        <div class="sval" style="color:var(--green);"><?=$khu['dang_thue']?></div>
        <div class="slbl" style="color:var(--green);">Đang thuê</div>
      </div>
      <div class="sbox" style="background:rgba(247,169,79,.07);border-color:rgba(247,169,79,.15);">
        <div class="sval" style="color:var(--amber);"><?=$khu['phong_trong']?></div>
        <div class="slbl" style="color:var(--amber);">Còn trống</div>
      </div>
    </div>

    <!-- Tỉ lệ lấp đầy -->
    <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--text3);margin-bottom:5px;">
      <span>Tỉ lệ lấp đầy</span>
      <strong style="color:var(--text);"><?=$pct?>%</strong>
    </div>
    <div class="fill-bar">
      <div class="fill-inner" style="width:<?=$pct?>%;background:<?=$gc?>;"></div>
    </div>

    <!-- Actions -->
    <div style="display:flex;gap:8px;">
      <a href="index.php?controller=phong&action=index&khu_id=<?=$khu['id']?>"
         class="btn btn-primary btn-sm" style="flex:1;justify-content:center;">🏠 Xem phòng</a>
      <a href="index.php?controller=phong&action=create&khu_id=<?=$khu['id']?>"
         class="btn btn-success btn-sm">＋ Thêm phòng</a>
      <a href="index.php?controller=khutro&action=delete&id=<?=$khu['id']?>"
         class="btn btn-danger btn-sm"
         onclick="return confirm('Xóa khu <?=htmlspecialchars($khu['ten_khu'])?>? Các phòng sẽ không bị xóa.')">🗑</a>
    </div>
  </div>
</div>
<?php endforeach; ?>
</div>

<?php else: ?>
<div class="card" style="text-align:center;padding:56px;">
  <div style="font-size:48px;margin-bottom:12px">🏘</div>
  <div style="font-size:16px;font-weight:800;color:var(--text2);margin-bottom:6px">Chưa có khu trọ nào</div>
  <div style="font-size:13px;color:var(--text3);margin-bottom:18px">
    Tạo khu trọ để phòng tự động được đặt tên theo khu<br>
    <strong style="color:var(--accent);">Khu A → A101, A102... &nbsp;|&nbsp; Khu B → B101, B102...</strong>
  </div>
  <a href="index.php?controller=khutro&action=create" class="btn btn-primary">＋ Thêm khu trọ đầu tiên</a>
</div>
<?php endif; ?>

<?php require_once 'app/Views/Layouts/footer.php'; ?>