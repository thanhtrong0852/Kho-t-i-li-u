</div><!-- end .page-content -->
</div><!-- end .main -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script>
/* ── Format tiền VNĐ toàn cục ── */
(function() {
  const VND_NAMES = ['gia','tien_coc','tien_phong','tien_dien','tien_nuoc'];
  const sel       = VND_NAMES.map(n => `input[name="${n}"]`).join(',');

  function toNum(v)    { return parseInt(String(v).replace(/\D/g,''), 10); }
  function fmtDot(v)   { const n=toNum(v); return isNaN(n)?'':(n===0?'0':n.toLocaleString('vi-VN')); }
  function fmtVND(v)   { const d=fmtDot(v); return d ? d+' VNĐ' : ''; }

  document.addEventListener('DOMContentLoaded', function() {
    const forms = new Set();

    document.querySelectorAll(sel).forEach(inp => {
      inp.type        = 'text';
      inp.inputMode   = 'numeric';
      inp.placeholder = inp.placeholder || 'Nhập số tiền...';

      /* hiển thị ban đầu */
      if (inp.value !== '') inp.value = fmtVND(inp.value);

      inp.addEventListener('focus', function() {
        const raw = toNum(this.value);
        this.value = isNaN(raw) || raw === 0 ? '' : String(raw);
      });

      inp.addEventListener('input', function() {
        const raw = this.value.replace(/\D/g,'');
        if (!raw) { this.value = ''; return; }
        this.value = fmtDot(raw);
        /* luôn đặt cursor về cuối — người gõ số không cần giữ giữa */
        const len = this.value.length;
        this.setSelectionRange(len, len);
      });

      inp.addEventListener('blur', function() {
        const raw = toNum(this.value);
        this.value = (!isNaN(raw) && raw >= 0) ? fmtVND(raw) : '';
      });

      /* strip VNĐ trước khi submit để PHP nhận số sạch */
      const form = inp.closest('form');
      if (form && !forms.has(form)) {
        forms.add(form);
        form.addEventListener('submit', function() {
          this.querySelectorAll(sel).forEach(i => {
            i.value = String(toNum(i.value));
          });
        });
      }
    });
  });
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
  document.querySelectorAll('input[type=date]').forEach(function(inp) {
    const existing = inp.value;
    flatpickr(inp, {
      locale      : 'vn',
      dateFormat  : 'Y-m-d',
      altInput    : true,
      altFormat   : 'd/m/Y',
      allowInput  : true,
      defaultDate : existing || null,
    });
  });
});
</script>
<script>
function toggleSidebar(){
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').style.display =
    document.getElementById('sidebar').classList.contains('open') ? 'block' : 'none';
}
function closeSidebar(){
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('overlay').style.display='none';
}
</script>

<!-- ═══ AI CHATBOX ═══ -->
<style>
.ai-fab{position:fixed;right:22px;bottom:22px;z-index:1200;width:56px;height:56px;border:none;border-radius:50%;background:linear-gradient(135deg,#4f8ef7,#7c5cfc);color:#fff;font-size:22px;cursor:pointer;box-shadow:0 8px 28px rgba(79,142,247,.4);transition:transform .2s,box-shadow .2s;display:flex;align-items:center;justify-content:center;}
.ai-fab:hover{transform:scale(1.08);box-shadow:0 12px 36px rgba(79,142,247,.5);}
.ai-fab.open{transform:rotate(45deg) scale(1.05);}
.ai-box{position:fixed;right:22px;bottom:90px;z-index:1200;width:380px;max-width:calc(100vw - 24px);height:520px;max-height:70vh;background:var(--card,#161922);border:1px solid rgba(79,142,247,.2);border-radius:18px;overflow:hidden;display:none;flex-direction:column;box-shadow:0 24px 64px rgba(0,0,0,.5);animation:aiIn .25s ease both;}
.ai-box.open{display:flex;}
@keyframes aiIn{from{opacity:0;transform:translateY(12px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}
.ai-head{padding:14px 18px;background:linear-gradient(135deg,#4f8ef7,#7c5cfc);color:#fff;display:flex;align-items:center;justify-content:space-between;flex-shrink:0;}
.ai-head-title{font-size:14px;font-weight:700;display:flex;align-items:center;gap:8px;}
.ai-head-actions{display:flex;gap:6px;}
.ai-head-actions button{background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);color:#fff;border-radius:7px;padding:5px 9px;cursor:pointer;font-size:11px;font-weight:600;transition:.15s;}
.ai-head-actions button:hover{background:rgba(255,255,255,.25);}
.ai-messages{flex:1;padding:14px;overflow-y:auto;background:var(--bg,#0d0f14);}
.ai-messages::-webkit-scrollbar{width:3px;}
.ai-messages::-webkit-scrollbar-thumb{background:rgba(255,255,255,.1);border-radius:2px;}
.ai-msg{margin-bottom:12px;display:flex;}
.ai-msg.user{justify-content:flex-end;}
.ai-bubble{max-width:82%;padding:10px 14px;border-radius:14px;font-size:13px;line-height:1.6;white-space:pre-wrap;word-break:break-word;}
.ai-msg.bot .ai-bubble{background:rgba(255,255,255,.05);border:1px solid rgba(255,255,255,.08);color:var(--text,#e8eaf0);}
.ai-msg.user .ai-bubble{background:linear-gradient(135deg,#4f8ef7,#7c5cfc);color:#fff;}
.ai-msg.bot .ai-bubble .typing{display:inline-flex;gap:4px;}.typing span{width:6px;height:6px;border-radius:50%;background:rgba(255,255,255,.4);animation:blink 1.2s infinite;}
.typing span:nth-child(2){animation-delay:.2s;}.typing span:nth-child(3){animation-delay:.4s;}
@keyframes blink{0%,80%,100%{opacity:.3}40%{opacity:1}}
.ai-foot{border-top:1px solid rgba(255,255,255,.06);padding:12px;background:var(--bg2,#13161e);flex-shrink:0;}
.ai-row{display:flex;gap:8px;}
.ai-row textarea{flex:1;resize:none;height:42px;max-height:100px;background:rgba(255,255,255,.04);border:1px solid rgba(255,255,255,.08);color:var(--text,#e8eaf0);border-radius:12px;padding:10px 12px;font-family:inherit;font-size:13px;outline:none;transition:border-color .2s;}
.ai-row textarea:focus{border-color:rgba(79,142,247,.5);}
.ai-row textarea::placeholder{color:#555b6e;}
.ai-row button{min-width:70px;border:none;border-radius:12px;background:linear-gradient(135deg,#4f8ef7,#7c5cfc);color:#fff;font-weight:700;font-size:13px;cursor:pointer;transition:opacity .15s;}
.ai-row button:hover{opacity:.85;}
.ai-row button:disabled{opacity:.5;cursor:not-allowed;}
@media(max-width:500px){.ai-box{right:10px;left:10px;width:auto;max-width:none;bottom:80px;height:65vh;}.ai-fab{right:14px;bottom:14px;width:50px;height:50px;font-size:20px;}}
.ai-suggestions{display:flex;flex-wrap:wrap;gap:6px;padding:4px 0 8px;}
.ai-suggestions button{background:rgba(79,142,247,.08);border:1px solid rgba(79,142,247,.2);color:var(--accent,#4f8ef7);border-radius:20px;padding:6px 12px;font-size:11px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .15s;white-space:nowrap;}
.ai-suggestions button:hover{background:rgba(79,142,247,.18);border-color:rgba(79,142,247,.4);transform:translateY(-1px);}
</style>

<?php if (($_GET['controller'] ?? '') !== 'chat'): ?>
<button class="ai-fab" id="aiFab" onclick="toggleAI()" title="Chat AI">🤖</button>
<?php endif; ?>

<div class="ai-box" id="aiBox">
  <div class="ai-head">
    <div class="ai-head-title">🤖 RoomManager AI</div>
    <div class="ai-head-actions">
      <button onclick="clearAI()" title="Xóa lịch sử">🗑</button>
      <button onclick="toggleAI()">✕</button>
    </div>
  </div>

  <div class="ai-messages" id="aiMessages">
    <div class="ai-msg bot">
      <div class="ai-bubble">Xin chào! Tôi là trợ lý AI của RoomManager 🏠<br><br>Bạn có thể hỏi tôi về:<br>• Quản lý phòng, hợp đồng<br>• Tính tiền điện nước<br>• Nội quy khu trọ<br>• Quy trình thuê/trả phòng</div>
    </div>
    <!-- Gợi ý nhanh -->
    <div class="ai-suggestions" id="aiSuggestions">
      <?php if (($_SESSION['vai_tro'] ?? '') === 'user'): ?>
      <button onclick="askSuggestion('Phòng trống')">🏠 Phòng trống</button>
      <button onclick="askSuggestion('Công nợ')">💰 Công nợ</button>
      <button onclick="askSuggestion('Nội quy')">📋 Nội quy</button>
      <button onclick="askSuggestion('Giá phòng')">💵 Giá phòng</button>
      <button onclick="askSuggestion('Đơn giá điện nước')">⚡ Đơn giá</button>
      <button onclick="askSuggestion('Khu trọ')">🏘 Khu trọ</button>
      <?php else: ?>
      <button onclick="askSuggestion('Phòng trống')">🏠 Phòng trống</button>
      <button onclick="askSuggestion('Công nợ')">⚠ Công nợ</button>
      <button onclick="askSuggestion('Doanh thu')">💰 Doanh thu</button>
      <button onclick="askSuggestion('Hợp đồng sắp hết hạn')">📄 HĐ sắp hết</button>
      <button onclick="askSuggestion('Hóa đơn')">⚡ Hóa đơn</button>
      <button onclick="askSuggestion('Người thuê')">👥 Người thuê</button>
      <button onclick="askSuggestion('Giá phòng')">💵 Giá phòng</button>
      <button onclick="askSuggestion('Khu trọ')">🏘 Khu trọ</button>
      <?php endif; ?>
    </div>
  </div>

  <div class="ai-foot">
    <div class="ai-row">
      <textarea id="aiInput" placeholder="Nhập câu hỏi..." rows="1"></textarea>
      <button id="aiSendBtn" onclick="sendAI()">Gửi</button>
    </div>
  </div>
</div>

<script>
function toggleAI() {
  const box = document.getElementById('aiBox');
  const fab = document.getElementById('aiFab');
  box.classList.toggle('open');
  fab.classList.toggle('open');
  if (box.classList.contains('open')) {
    document.getElementById('aiInput').focus();
  }
}

function appendMsg(role, html) {
  const box = document.getElementById('aiMessages');
  const div = document.createElement('div');
  div.className = 'ai-msg ' + role;
  div.innerHTML = '<div class="ai-bubble">' + html + '</div>';
  box.appendChild(div);
  box.scrollTop = box.scrollHeight;
  return div;
}

function showTyping() {
  return appendMsg('bot', '<div class="typing"><span></span><span></span><span></span></div>');
}

async function sendAI() {
  const input = document.getElementById('aiInput');
  const btn   = document.getElementById('aiSendBtn');
  const text  = input.value.trim();
  if (!text) return;

  appendMsg('user', escHtml(text));
  input.value = '';
  input.style.height = '42px';
  btn.disabled = true;

  const typing = showTyping();

  try {
    const res = await fetch('index.php?controller=ai&action=chat', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ message: text })
    });

    const data = await res.json();
    typing.remove();

    if (data.ok) {
      appendMsg('bot', formatAI(data.reply));
    } else {
      appendMsg('bot', '<span style="color:#f75c5c;">⚠ ' + escHtml(data.message || 'Lỗi không xác định') + '</span>');
    }
  } catch (e) {
    typing.remove();
    appendMsg('bot', '<span style="color:#f75c5c;">⚠ Không kết nối được tới AI</span>');
  }

  btn.disabled = false;
  input.focus();
}

async function clearAI() {
  if (!confirm('Xóa lịch sử chat?')) return;
  try { await fetch('index.php?controller=ai&action=clear', {method:'POST'}); } catch(e) {}
  const box = document.getElementById('aiMessages');
  box.innerHTML = '';
  appendMsg('bot', 'Đã xóa lịch sử. Bạn cần hỗ trợ gì? 🏠');
}

function escHtml(s) {
  return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function askSuggestion(text) {
  document.getElementById('aiInput').value = text;
  // Ẩn gợi ý sau khi click
  const sug = document.getElementById('aiSuggestions');
  if (sug) sug.style.display = 'none';
  sendAI();
}

function formatAI(text) {
  // Bold: **text**
  let html = escHtml(text);
  html = html.replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>');
  // Bullet points
  html = html.replace(/^[•\-]\s/gm, '• ');
  // Line breaks
  html = html.replace(/\n/g, '<br>');
  return html;
}

// Enter to send, Shift+Enter for newline
document.addEventListener('DOMContentLoaded', function() {
  const input = document.getElementById('aiInput');
  if (input) {
    input.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault();
        sendAI();
      }
    });
    // Auto-resize
    input.addEventListener('input', function() {
      this.style.height = '42px';
      this.style.height = Math.min(this.scrollHeight, 100) + 'px';
    });
  }
});
</script>

</body>
</html>