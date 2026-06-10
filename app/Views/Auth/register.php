<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Đăng ký — RoomManager</title>
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
}
body {
  font-family: 'Nunito', sans-serif;
  background: var(--bg); color: var(--text);
  min-height: 100vh;
  display: flex; align-items: center; justify-content: center;
  padding: 32px 24px;
}
.page-wrap {
  width: 100%; max-width: 500px;
  background: var(--bg2);
  border-radius: 20px;
  border: 1px solid var(--border);
  padding: 38px 40px;
  animation: fadeUp 0.4s ease both;
}
@keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
.logo-row { display: flex; align-items: center; gap: 10px; margin-bottom: 26px; }
.logo-icon {
  width: 36px; height: 36px;
  background: linear-gradient(135deg, var(--accent), var(--accent2));
  border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 17px;
}
.logo-text { font-size: 16px; font-weight: 800; color: var(--text); }
.steps-bar { display: flex; align-items: center; margin-bottom: 26px; }
.step-item { display: flex; flex-direction: column; align-items: center; gap: 5px; flex: 1; }
.step-circle {
  width: 28px; height: 28px; border-radius: 50%;
  border: 2px solid rgba(255,255,255,0.12);
  background: var(--bg3);
  display: flex; align-items: center; justify-content: center;
  font-size: 11px; font-weight: 700; color: #555a72;
  position: relative; z-index: 1; transition: all 0.3s;
}
.step-circle.done { background: var(--accent); border-color: var(--accent); color: #fff; }
.step-circle.active { border-color: var(--accent); color: var(--accent); background: rgba(102,126,234,0.1); }
.step-lbl { font-size: 10px; font-weight: 600; color: #555a72; text-transform: uppercase; letter-spacing: 0.4px; }
.step-circle.done + .step-lbl, .step-circle.active + .step-lbl { color: var(--accent); }
.step-line { flex: 1; height: 2px; background: rgba(255,255,255,0.08); margin: 0 -2px; position: relative; top: -12px; transition: background 0.3s; }
.step-line.done { background: var(--accent); }
.form-heading { font-size: 20px; font-weight: 800; color: var(--text); margin-bottom: 4px; }
.form-sub { font-size: 13px; color: var(--text2); margin-bottom: 22px; }
.alert { border-radius: 10px; padding: 10px 14px; font-size: 13px; margin-bottom: 14px; display: flex; align-items: center; gap: 8px; }
.alert-error   { background:rgba(239,68,68,0.1);  border:1px solid rgba(239,68,68,0.25);  color:#f87171; }
.alert-success { background:rgba(34,197,94,0.1);  border:1px solid rgba(34,197,94,0.25);  color:#4ade80; }
.field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.field { margin-bottom: 14px; }
.field label { display: block; font-size: 11px; font-weight: 700; color: var(--text2); margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.5px; }
.input-wrap { position: relative; }
.input-wrap input {
  width: 100%; background: var(--bg3); border: 1.5px solid var(--border); border-radius: 10px;
  padding: 11px 14px; font-size: 13px; font-family: 'Nunito', sans-serif;
  color: var(--text); outline: none; transition: border-color 0.2s, box-shadow 0.2s;
}
.input-wrap input::placeholder { color: #555a72; }
.input-wrap input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(102,126,234,0.12); }
.toggle-pw {
  position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
  background: none; border: none; cursor: pointer; font-size: 14px; color: #555a72; padding: 4px; transition: color 0.2s;
}
.toggle-pw:hover { color: var(--accent); }
.strength-wrap { margin-top: 5px; }
.strength-bar { height: 3px; border-radius: 2px; background: rgba(255,255,255,0.08); overflow: hidden; }
.strength-fill { height: 100%; border-radius: 2px; width: 0%; transition: width 0.3s, background 0.3s; }
.strength-lbl { font-size: 10px; color: #555a72; margin-top: 3px; }
.fb { font-size: 11px; margin-top: 4px; }
.fb-ok  { color: #4ade80; }
.fb-err { color: #f87171; }
.terms-row { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 18px; }
.terms-row input[type=checkbox] {
  appearance: none; width: 17px; height: 17px; border: 1.5px solid rgba(255,255,255,0.15);
  border-radius: 5px; background: var(--bg3); cursor: pointer; transition: all 0.2s;
  position: relative; flex-shrink: 0; margin-top: 1px;
}
.terms-row input[type=checkbox]:checked { background: var(--accent); border-color: var(--accent); }
.terms-row input[type=checkbox]:checked::after {
  content:'✓'; position:absolute; top:50%; left:50%;
  transform:translate(-50%,-50%); color:#fff; font-size:10px; font-weight:700;
}
.terms-txt { font-size: 12px; color: var(--text2); line-height: 1.6; }
.terms-txt a { color: var(--accent); text-decoration: none; font-weight: 600; }
.terms-txt a:hover { text-decoration: underline; }
.btn-primary {
  width: 100%; padding: 13px;
  background: linear-gradient(135deg, var(--accent), var(--accent2));
  border: none; border-radius: 11px; font-size: 14px; font-weight: 700; color: #fff;
  cursor: pointer; font-family: 'Nunito', sans-serif;
  transition: opacity 0.2s, transform 0.15s; margin-bottom: 14px;
}
.btn-primary:hover { opacity: 0.9; transform: translateY(-1px); }
.alt-link { text-align: center; font-size: 13px; color: var(--text2); }
.alt-link a { color: var(--accent); font-weight: 700; text-decoration: none; }
.alt-link a:hover { text-decoration: underline; }
@media (max-width: 480px) { .page-wrap { padding: 26px 20px; } .field-row { grid-template-columns: 1fr; } }
</style>
</head>
<body>
<div class="page-wrap">
  <div class="logo-row">
    <div class="logo-icon">🏠</div>
    <span class="logo-text">RoomManager</span>
  </div>
  <div class="steps-bar">
    <div class="step-item"><div class="step-circle done" id="sc1">✓</div><div class="step-lbl">Cơ bản</div></div>
    <div class="step-line done" id="sl1"></div>
    <div class="step-item"><div class="step-circle active" id="sc2">2</div><div class="step-lbl">Bảo mật</div></div>
    <div class="step-line" id="sl2"></div>
    <div class="step-item"><div class="step-circle" id="sc3">3</div><div class="step-lbl">Hoàn tất</div></div>
  </div>
  <div class="form-heading">Tạo tài khoản mới</div>
  <div class="form-sub">Điền thông tin để bắt đầu sử dụng</div>
  <?php if (!empty($error)): ?>
  <div class="alert alert-error">⚠ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>
  <?php if (!empty($success)): ?>
  <div class="alert alert-success">✓ <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>
  <form method="POST" action="index.php?controller=auth&action=register" id="regForm">
    <div class="field-row">
      <div class="field">
        <label>Họ và tên</label>
        <div class="input-wrap"><input type="text" name="ho_ten" placeholder="Nguyễn Văn A" required value="<?= htmlspecialchars($_POST['ho_ten'] ?? '') ?>"/></div>
      </div>
      <div class="field">
        <label>Số điện thoại</label>
        <div class="input-wrap"><input type="tel" name="sdt" placeholder="0901234567" value="<?= htmlspecialchars($_POST['sdt'] ?? '') ?>"/></div>
      </div>
    </div>
    <div class="field">
      <label>Email</label>
      <div class="input-wrap"><input type="email" name="email" placeholder="example@email.com" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"/></div>
    </div>
    <div class="field">
      <label>Tên đăng nhập</label>
      <div class="input-wrap"><input type="text" name="username" placeholder="Tối thiểu 4 ký tự" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" oninput="checkUsername(this.value)"/></div>
      <div class="fb" id="userFb"></div>
    </div>
    <div class="field-row">
      <div class="field">
        <label>Mật khẩu</label>
        <div class="input-wrap">
          <input type="password" name="password" id="pw1" placeholder="Tối thiểu 8 ký tự" required oninput="checkStrength(this.value)"/>
          <button type="button" class="toggle-pw" onclick="togglePw('pw1',this)">👁</button>
        </div>
        <div class="strength-wrap">
          <div class="strength-bar"><div class="strength-fill" id="strFill"></div></div>
          <div class="strength-lbl" id="strLbl">Nhập mật khẩu</div>
        </div>
      </div>
      <div class="field">
        <label>Xác nhận</label>
        <div class="input-wrap">
          <input type="password" name="confirm_password" id="pw2" placeholder="Nhập lại..." required oninput="checkMatch()"/>
          <button type="button" class="toggle-pw" onclick="togglePw('pw2',this)">👁</button>
        </div>
        <div class="fb" id="matchFb"></div>
      </div>
    </div>
    <div class="terms-row">
      <input type="checkbox" id="terms" name="terms" required/>
      <div class="terms-txt">Tôi đồng ý với <a href="#">Điều khoản dịch vụ</a> và <a href="#">Chính sách bảo mật</a></div>
    </div>
    <button type="submit" class="btn-primary" id="regBtn">Tạo tài khoản →</button>
  </form>
  <div class="alt-link">Đã có tài khoản? <a href="index.php?controller=auth&action=login">Đăng nhập ngay</a></div>
</div>
<script>
function togglePw(id,btn){const i=document.getElementById(id);i.type=i.type==='password'?'text':'password';btn.textContent=i.type==='password'?'👁':'🙈';}
function checkStrength(v){
  const f=document.getElementById('strFill'),l=document.getElementById('strLbl');
  let s=0;if(v.length>=8)s++;if(v.length>=10)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;
  const lv=[{w:'0%',bg:'transparent',t:'Nhập mật khẩu',c:'#555a72'},{w:'25%',bg:'#f87171',t:'Yếu',c:'#f87171'},{w:'50%',bg:'#fb923c',t:'Trung bình',c:'#fb923c'},{w:'75%',bg:'#60a5fa',t:'Khá mạnh',c:'#60a5fa'},{w:'100%',bg:'#4ade80',t:'Mạnh',c:'#4ade80'}][Math.min(s,4)];
  f.style.width=lv.w;f.style.background=lv.bg;l.textContent=lv.t;l.style.color=lv.c;checkMatch();
}
function checkMatch(){const p1=document.getElementById('pw1').value,p2=document.getElementById('pw2').value,fb=document.getElementById('matchFb');if(!p2){fb.textContent='';return;}fb.textContent=p1===p2?'✓ Mật khẩu khớp':'✗ Không khớp';fb.className='fb '+(p1===p2?'fb-ok':'fb-err');}
function checkUsername(v){const fb=document.getElementById('userFb');if(!v){fb.textContent='';return;}if(v.length<4){fb.textContent='✗ Tối thiểu 4 ký tự';fb.className='fb fb-err';}else if(/^[a-zA-Z0-9_]+$/.test(v)){fb.textContent='✓ Hợp lệ';fb.className='fb fb-ok';}else{fb.textContent='✗ Chỉ dùng chữ, số và _';fb.className='fb fb-err';}}
document.querySelectorAll('input').forEach(f=>f.addEventListener('focus',()=>{document.getElementById('sc3').className='step-circle active';document.getElementById('sl2').className='step-line done';}));
document.getElementById('regForm').addEventListener('submit',function(e){
  if(document.getElementById('pw1').value!==document.getElementById('pw2').value){e.preventDefault();document.getElementById('matchFb').textContent='✗ Mật khẩu không khớp!';document.getElementById('matchFb').className='fb fb-err';return;}
  const btn=document.getElementById('regBtn');btn.textContent='Đang xử lý...';btn.style.opacity='0.7';btn.style.pointerEvents='none';
});
</script>
</body>
</html>