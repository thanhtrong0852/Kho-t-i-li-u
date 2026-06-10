<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đặt lại mật khẩu — RoomManager</title>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
* { margin:0; padding:0; box-sizing:border-box; }
body {
  font-family: 'Be Vietnam Pro', sans-serif;
  min-height: 100vh;
  background: #0a0c14;
  display: flex;
  align-items: center;
  justify-content: center;
}
.corner-glow { position:fixed; border-radius:50%; pointer-events:none; z-index:1; }
.glow-tl { width:400px; height:400px; top:-150px; left:-100px; background:radial-gradient(circle, rgba(34,201,147,0.12) 0%, transparent 70%); }
.glow-br { width:500px; height:500px; bottom:-200px; right:-100px; background:radial-gradient(circle, rgba(124,92,252,0.1) 0%, transparent 70%); }

.wrap {
  position:relative; z-index:10;
  width:100%; max-width:420px; padding:20px;
  animation: fadeUp 0.6s ease both;
}
@keyframes fadeUp { from{opacity:0;transform:translateY(24px)} to{opacity:1;transform:translateY(0)} }

.card {
  background: rgba(22, 25, 34, 0.9);
  backdrop-filter: blur(20px);
  border: 1px solid rgba(34,201,147,0.2);
  border-radius: 20px;
  padding: 40px 36px;
  box-shadow: 0 32px 64px rgba(0,0,0,0.5), 0 0 80px rgba(34,201,147,0.08);
}

.logo-row { display:flex; align-items:center; gap:12px; margin-bottom:28px; justify-content:center; }
.logo-icon { width:44px; height:44px; background:linear-gradient(135deg,#22c993,#2dd4bf); border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:20px; box-shadow:0 8px 24px rgba(34,201,147,0.35); }
.logo-text { font-size:20px; font-weight:800; background:linear-gradient(135deg,#fff,#aab4d4); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }

.card-title { font-size:22px; font-weight:700; color:#e8eaf0; text-align:center; margin-bottom:6px; }
.card-sub { font-size:13px; color:#555b6e; text-align:center; margin-bottom:28px; }

.alert-error { background:rgba(247,92,92,0.12); border:1px solid rgba(247,92,92,0.3); border-radius:10px; padding:10px 14px; font-size:13px; color:#f75c5c; margin-bottom:18px; display:flex; align-items:center; gap:8px; }
.alert-success { background:rgba(34,201,147,0.12); border:1px solid rgba(34,201,147,0.3); border-radius:10px; padding:12px 14px; font-size:13px; color:#22c993; margin-bottom:18px; display:flex; align-items:center; gap:8px; line-height:1.5; }

.field { margin-bottom:18px; }
.field label { display:block; font-size:12px; font-weight:600; color:#8b90a0; margin-bottom:8px; text-transform:uppercase; letter-spacing:.3px; }
.input-wrap { position:relative; }
.input-wrap .ic { position:absolute; left:14px; top:50%; transform:translateY(-50%); font-size:16px; opacity:.4; pointer-events:none; }
.input-wrap input {
  width:100%; background:rgba(255,255,255,0.04); border:1.5px solid rgba(255,255,255,0.08);
  border-radius:12px; padding:13px 14px 13px 44px; font-size:14px; font-family:'Be Vietnam Pro',sans-serif;
  color:#e8eaf0; outline:none; transition:border-color .2s, background .2s, box-shadow .2s;
}
.input-wrap input::placeholder { color:#555b6e; }
.input-wrap input:focus { border-color:rgba(34,201,147,0.6); background:rgba(34,201,147,0.05); box-shadow:0 0 0 4px rgba(34,201,147,0.08); }

.btn-submit {
  width:100%; padding:14px; background:linear-gradient(135deg,#22c993,#2dd4bf);
  border:none; border-radius:12px; font-size:15px; font-weight:700; color:white;
  cursor:pointer; font-family:'Be Vietnam Pro',sans-serif;
  box-shadow:0 8px 24px rgba(34,201,147,0.3); transition:opacity .2s, transform .15s;
  display:flex; align-items:center; justify-content:center; gap:8px;
}
.btn-submit:hover { opacity:.92; transform:translateY(-1px); }

.back-link { display:block; text-align:center; margin-top:20px; font-size:13px; color:#4f8ef7; text-decoration:none; font-weight:500; }
.back-link:hover { text-decoration:underline; }

.pw-strength { height:4px; border-radius:2px; margin-top:8px; background:rgba(255,255,255,0.06); overflow:hidden; }
.pw-bar { height:100%; border-radius:2px; transition:width .3s, background .3s; width:0; }

.toggle-pw {
  position:absolute; right:12px; top:50%; transform:translateY(-50%);
  background:none; border:none; cursor:pointer; font-size:16px; color:#555b6e; padding:4px;
}
</style>
</head>
<body>

<div class="corner-glow glow-tl"></div>
<div class="corner-glow glow-br"></div>

<div class="wrap">
  <div class="card">

    <div class="logo-row">
      <div class="logo-icon">🔐</div>
      <span class="logo-text">RoomManager</span>
    </div>

    <?php if (!empty($success)): ?>
      <div class="card-title">✅ Thành công!</div>
      <div class="card-sub">&nbsp;</div>
      <div class="alert-success">
        <span>✓</span>
        <div><?= htmlspecialchars($success) ?></div>
      </div>
      <a href="index.php?controller=auth&action=login" class="btn-submit" style="text-decoration:none;margin-top:10px;">
        🔑 Đăng nhập ngay
      </a>

    <?php elseif (!empty($error) && empty($token)): ?>
      <div class="card-title">⚠ Lỗi</div>
      <div class="card-sub">&nbsp;</div>
      <div class="alert-error"><span>⚠</span> <?= htmlspecialchars($error) ?></div>
      <a href="index.php?controller=auth&action=forgotPassword" class="btn-submit" style="text-decoration:none;background:linear-gradient(135deg,#4f8ef7,#7c5cfc);">
        ← Thử lại
      </a>

    <?php else: ?>
      <div class="card-title">Đặt lại mật khẩu</div>
      <div class="card-sub">Nhập mật khẩu mới cho tài khoản của bạn</div>

      <?php if (!empty($error)): ?>
      <div class="alert-error"><span>⚠</span> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="POST" action="index.php?controller=auth&action=resetPassword&token=<?= htmlspecialchars($token) ?>">

        <div class="field">
          <label>Mật khẩu mới <span style="color:#f75c5c">*</span></label>
          <div class="input-wrap">
            <input type="password" name="password" id="pw1" placeholder="Tối thiểu 8 ký tự..." required minlength="8" oninput="checkStrength(this.value)"/>
            <span class="ic">🔒</span>
            <button type="button" class="toggle-pw" onclick="togglePw('pw1',this)">👁</button>
          </div>
          <div class="pw-strength"><div class="pw-bar" id="pwBar"></div></div>
          <div style="font-size:11px;color:#555b6e;margin-top:4px;" id="pwHint">Tối thiểu 8 ký tự</div>
        </div>

        <div class="field">
          <label>Xác nhận mật khẩu <span style="color:#f75c5c">*</span></label>
          <div class="input-wrap">
            <input type="password" name="confirm_password" id="pw2" placeholder="Nhập lại mật khẩu..." required minlength="8" oninput="checkMatch()"/>
            <span class="ic">🔒</span>
            <button type="button" class="toggle-pw" onclick="togglePw('pw2',this)">👁</button>
          </div>
          <div style="font-size:11px;margin-top:4px;display:none;" id="matchHint"></div>
        </div>

        <button type="submit" class="btn-submit">✓ Đặt lại mật khẩu</button>

      </form>
    <?php endif; ?>

    <a href="index.php?controller=auth&action=login" class="back-link">← Quay lại đăng nhập</a>

  </div>
</div>

<script>
function togglePw(id, btn) {
  const inp = document.getElementById(id);
  if (inp.type === 'password') { inp.type = 'text'; btn.textContent = '🙈'; }
  else { inp.type = 'password'; btn.textContent = '👁'; }
}

function checkStrength(pw) {
  const bar  = document.getElementById('pwBar');
  const hint = document.getElementById('pwHint');
  let score = 0;
  if (pw.length >= 8) score++;
  if (pw.length >= 12) score++;
  if (/[A-Z]/.test(pw)) score++;
  if (/[0-9]/.test(pw)) score++;
  if (/[^A-Za-z0-9]/.test(pw)) score++;

  const pct    = Math.min(score / 5 * 100, 100);
  const colors = ['#f75c5c','#f7a94f','#f7a94f','#22c993','#22c993'];
  const labels = ['Yếu','Trung bình','Trung bình','Mạnh','Rất mạnh'];

  bar.style.width = pct + '%';
  bar.style.background = colors[Math.min(score-1, 4)] || '#f75c5c';
  hint.textContent = pw.length > 0 ? ('Độ mạnh: ' + (labels[Math.min(score-1, 4)] || 'Yếu')) : 'Tối thiểu 8 ký tự';
  hint.style.color = colors[Math.min(score-1, 4)] || '#555b6e';
}

function checkMatch() {
  const pw1 = document.getElementById('pw1').value;
  const pw2 = document.getElementById('pw2').value;
  const hint = document.getElementById('matchHint');
  if (pw2.length === 0) { hint.style.display = 'none'; return; }
  hint.style.display = 'block';
  if (pw1 === pw2) {
    hint.textContent = '✓ Mật khẩu khớp';
    hint.style.color = '#22c993';
  } else {
    hint.textContent = '✗ Mật khẩu không khớp';
    hint.style.color = '#f75c5c';
  }
}
</script>

</body>
</html>
