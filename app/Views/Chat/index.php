<?php
$title = 'Chat nhóm';
require_once 'app/Views/Layouts/header.php';
$currentUserId = (int)($_SESSION['user_id'] ?? 0);
$isAdmin = in_array($_SESSION['vai_tro'] ?? '', ['quan_ly', 'chu_tro']);

// Đếm thành viên online (có account)
$db = Database::getInstance();
$totalMembers = (int)$db->query("SELECT COUNT(*) FROM account")->fetchColumn();
?>

<style>
.chat-wrap{display:flex;flex-direction:column;height:calc(100vh - 130px);max-height:750px;}
@media(max-width:900px){.chat-wrap{height:calc(100vh - 100px);max-height:none;}}
.chat-container{flex:1;display:flex;flex-direction:column;background:var(--card);border:1px solid var(--border);border-radius:18px;overflow:hidden;box-shadow:0 8px 32px rgba(0,0,0,.2);}

/* Header */
.chat-hd{padding:14px 20px;background:linear-gradient(135deg,rgba(79,142,247,.08),rgba(124,92,252,.05));border-bottom:1px solid var(--border);display:flex;align-items:center;gap:14px;flex-shrink:0;}
.chat-hd-icon{width:44px;height:44px;border-radius:14px;background:linear-gradient(135deg,#4f8ef7,#7c5cfc);display:flex;align-items:center;justify-content:center;font-size:20px;box-shadow:0 4px 14px rgba(79,142,247,.3);}
.chat-hd-info{flex:1;}
.chat-hd-name{font-size:15px;font-weight:700;color:var(--text);}
.chat-hd-sub{font-size:11px;color:var(--text3);display:flex;align-items:center;gap:6px;margin-top:2px;}
.online-dot{width:7px;height:7px;border-radius:50%;background:#22c993;animation:pulse 2s infinite;}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.5}}

/* Pinned messages */
.pin-board{background:#f7f8fb;color:#1f2b45;border-bottom:1px solid rgba(15,23,42,.12);display:none;flex-shrink:0;}
.pin-board.show{display:block;}
.pin-head{height:40px;display:flex;align-items:center;justify-content:space-between;padding:0 18px;background:#eef0f5;font-size:13px;font-weight:700;}
.pin-toggle{border:none;background:transparent;color:#1f2b45;font:inherit;cursor:pointer;display:flex;align-items:center;gap:7px;padding:5px 0;}
.pin-toggle:hover{color:#4f8ef7;}
.pin-list{max-height:220px;overflow:auto;}
.pin-board.collapsed .pin-list,.pin-board.collapsed .pin-all{display:none;}
.pin-item{display:flex;gap:12px;padding:10px 18px;border-top:1px solid rgba(15,23,42,.1);cursor:pointer;background:#fff;}
.pin-item:hover{background:#f8fbff;}
.pin-icon{width:26px;height:26px;border:1.5px solid #2f7bff;border-radius:50%;color:#2f7bff;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;margin-top:2px;}
.pin-info{flex:1;min-width:0;}
.pin-title{font-size:13px;font-weight:800;color:#1f2b45;margin-bottom:4px;}
.pin-text{font-size:13px;color:#27364f;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.pin-more{border:none;background:transparent;color:#1f2b45;font-size:20px;line-height:1;cursor:pointer;padding:0 4px;}
.pin-all{padding:9px 18px;border-top:1px solid rgba(15,23,42,.1);text-align:center;background:#fff;color:#27364f;font-size:13px;cursor:pointer;}
.pin-all:hover{color:#2f7bff;background:#f8fbff;}
.msg-row.pin-focus .msg-bubble{box-shadow:0 0 0 3px rgba(79,142,247,.28),0 0 20px rgba(79,142,247,.18);}

/* Body */
.chat-body{flex:1;overflow-y:auto;padding:16px 20px;display:flex;flex-direction:column;gap:4px;background:var(--bg);}
.chat-body::-webkit-scrollbar{width:4px;}.chat-body::-webkit-scrollbar-thumb{background:rgba(255,255,255,.08);border-radius:2px;}

/* Messages */
.msg-row{display:flex;gap:8px;align-items:flex-end;max-width:75%;margin-bottom:4px;animation:msgIn .2s ease both;}
@keyframes msgIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
.msg-row.me{margin-left:auto;flex-direction:row-reverse;}
.msg-row.me + .msg-row.me,.msg-row:not(.me) + .msg-row:not(.me){margin-top:-2px;}
.msg-av{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;color:#fff;flex-shrink:0;margin-bottom:2px;}
.msg-wrap{display:flex;flex-direction:column;gap:1px;}
.msg-row.me .msg-wrap{align-items:flex-end;}
.msg-name{font-size:10px;font-weight:600;color:var(--text3);padding:0 6px;display:flex;align-items:center;gap:4px;}
.msg-name .badge-admin{font-size:8px;padding:1px 5px;border-radius:3px;background:linear-gradient(135deg,#4f8ef7,#7c5cfc);color:#fff;font-weight:700;letter-spacing:.3px;}
.msg-bubble{padding:8px 14px;border-radius:16px;font-size:13px;line-height:1.55;word-break:break-word;white-space:pre-wrap;max-width:100%;}
.msg-row:not(.me) .msg-bubble{background:var(--bg3);border:1px solid var(--border);color:var(--text);border-bottom-left-radius:4px;}
.msg-row.me .msg-bubble{background:linear-gradient(135deg,#4f8ef7,#7c5cfc);color:#fff;border-bottom-right-radius:4px;box-shadow:0 2px 8px rgba(79,142,247,.25);}
.msg-time{font-size:9px;color:var(--text3);padding:0 6px;margin-top:1px;}
.msg-row.me .msg-time{text-align:right;}
.msg-row{position:relative;}
.msg-bubble{cursor:pointer;}
.msg-reply-ref{max-width:260px;margin:0 6px 3px;padding:5px 8px;border-left:3px solid rgba(79,142,247,.75);border-radius:8px;background:rgba(79,142,247,.08);color:var(--text2);font-size:11px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.msg-pin{font-size:10px;color:#f6c453;margin:0 6px 2px;}
.msg-reaction{position:absolute;right:-8px;bottom:14px;min-width:23px;height:23px;padding:0 5px;border-radius:999px;background:var(--card);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:14px;box-shadow:0 4px 10px rgba(0,0,0,.28);}
.msg-row.me .msg-reaction{left:-8px;right:auto;}
.reply-preview{display:none;align-items:center;gap:10px;margin-bottom:8px;padding:9px 12px;border:1px solid rgba(79,142,247,.25);border-radius:12px;background:rgba(79,142,247,.08);}
.reply-preview.show{display:flex;}
.reply-preview-main{flex:1;min-width:0;}
.reply-preview-title{font-size:11px;font-weight:800;color:#6ea1ff;margin-bottom:2px;}
.reply-preview-text{font-size:12px;color:var(--text2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
.reply-close{border:none;background:transparent;color:var(--text3);font-size:18px;cursor:pointer;padding:0 4px;}
.msg-action-layer{position:fixed;z-index:1500;display:none;min-width:230px;}
.msg-action-layer.show{display:block;}
.react-bar{display:flex;gap:8px;align-items:center;padding:9px 12px;margin-bottom:8px;border-radius:999px;background:#242424;box-shadow:0 12px 36px rgba(0,0,0,.45);}
.react-bar button{border:none;background:transparent;font-size:24px;line-height:1;cursor:pointer;transition:.12s;padding:3px;}
.react-bar button:hover{transform:scale(1.25) translateY(-2px);}
.ctx-menu{overflow:hidden;border-radius:16px;background:#242424;border:1px solid rgba(255,255,255,.1);box-shadow:0 18px 48px rgba(0,0,0,.5);}
.ctx-item{width:100%;display:flex;align-items:center;justify-content:space-between;gap:18px;border:none;background:transparent;color:#f4f4f5;font-family:inherit;font-size:15px;text-align:left;padding:13px 16px;cursor:pointer;border-bottom:1px solid rgba(255,255,255,.08);}
.ctx-item:hover{background:rgba(255,255,255,.08);}
.ctx-item:last-child{border-bottom:none;}
.ctx-item.danger{color:#ff4d62;}
.ctx-ico{opacity:.9;}

/* System & Date */
.msg-system{text-align:center;font-size:11px;color:var(--text3);padding:6px 0;font-style:italic;}
.msg-date-sep{text-align:center;padding:10px 0 6px;position:sticky;top:0;z-index:1;}
.msg-date-sep span{background:var(--card);border:1px solid var(--border);padding:4px 14px;border-radius:12px;font-size:10px;font-weight:600;color:var(--text3);letter-spacing:.3px;}

/* Typing indicator */
.typing-indicator{display:none;padding:4px 0;font-size:11px;color:var(--text3);font-style:italic;align-items:center;gap:6px;}
.typing-indicator.show{display:flex;}
.typing-dots{display:flex;gap:3px;}.typing-dots span{width:5px;height:5px;border-radius:50%;background:var(--text3);animation:tdot 1.2s infinite;}
.typing-dots span:nth-child(2){animation-delay:.2s;}.typing-dots span:nth-child(3){animation-delay:.4s;}
@keyframes tdot{0%,80%,100%{transform:scale(.6);opacity:.4}40%{transform:scale(1);opacity:1}}

/* Footer */
.chat-ft{padding:12px 16px;border-top:1px solid var(--border);background:var(--bg2);flex-shrink:0;}
.chat-input-row{display:flex;gap:8px;align-items:flex-end;}
.chat-input{flex:1;resize:none;height:40px;max-height:120px;background:rgba(255,255,255,.04);border:1.5px solid var(--border);color:var(--text);border-radius:14px;padding:10px 14px;font-family:inherit;font-size:13px;outline:none;transition:border-color .2s;}
.chat-input:focus{border-color:rgba(79,142,247,.5);box-shadow:0 0 0 3px rgba(79,142,247,.06);}
.chat-input::placeholder{color:var(--text3);}
.chat-send{width:40px;height:40px;border:none;border-radius:12px;background:linear-gradient(135deg,#4f8ef7,#7c5cfc);color:#fff;font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .15s;box-shadow:0 4px 12px rgba(79,142,247,.3);}
.chat-send:hover{transform:scale(1.05);box-shadow:0 6px 18px rgba(79,142,247,.4);}
.chat-send:disabled{opacity:.4;cursor:not-allowed;transform:none;}
.chat-ft-hint{font-size:10px;color:var(--text3);margin-top:6px;text-align:center;}

/* Scroll to bottom */
.scroll-bottom{position:absolute;bottom:70px;right:24px;width:36px;height:36px;border-radius:50%;background:var(--card);border:1px solid var(--border);display:none;align-items:center;justify-content:center;cursor:pointer;font-size:14px;box-shadow:0 4px 12px rgba(0,0,0,.3);z-index:5;transition:.15s;}
.scroll-bottom:hover{background:rgba(79,142,247,.1);border-color:rgba(79,142,247,.3);}
.scroll-bottom.show{display:flex;}

.sys-modal{position:fixed;inset:0;background:rgba(0,0,0,.68);backdrop-filter:blur(6px);z-index:1400;display:none;align-items:center;justify-content:center;padding:18px;}
.sys-modal.show{display:flex;}
.sys-dialog{width:min(520px,100%);background:var(--card);border:1px solid rgba(79,142,247,.22);border-radius:16px;box-shadow:0 24px 70px rgba(0,0,0,.55);overflow:hidden;animation:sysIn .18s ease both;}
@keyframes sysIn{from{opacity:0;transform:translateY(10px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
.sys-head{display:flex;align-items:center;gap:12px;padding:16px 18px;border-bottom:1px solid var(--border);background:linear-gradient(135deg,rgba(79,142,247,.12),rgba(124,92,252,.08));}
.sys-ico{width:38px;height:38px;border-radius:12px;background:linear-gradient(135deg,#4f8ef7,#7c5cfc);display:flex;align-items:center;justify-content:center;font-size:18px;box-shadow:0 8px 20px rgba(79,142,247,.26);}
.sys-title{font-size:15px;font-weight:800;color:var(--text);}
.sys-sub{font-size:11px;color:var(--text3);margin-top:2px;}
.sys-body{padding:18px;}
.sys-text{width:100%;min-height:120px;resize:vertical;background:rgba(255,255,255,.04);border:1.5px solid var(--border);border-radius:12px;color:var(--text);font-family:inherit;font-size:13px;line-height:1.6;padding:12px 14px;outline:none;}
.sys-text:focus{border-color:rgba(79,142,247,.55);box-shadow:0 0 0 3px rgba(79,142,247,.08);}
.sys-actions{display:flex;gap:10px;justify-content:flex-end;padding:0 18px 18px;}
.sys-btn{border:none;border-radius:10px;padding:10px 16px;font-family:inherit;font-size:13px;font-weight:700;cursor:pointer;transition:.15s;}
.sys-btn.cancel{background:var(--bg3);color:var(--text2);border:1px solid var(--border);}
.sys-btn.cancel:hover{color:var(--text);border-color:var(--border2);}
.sys-btn.send{background:linear-gradient(135deg,#4f8ef7,#7c5cfc);color:#fff;box-shadow:0 8px 20px rgba(79,142,247,.25);}
.sys-btn.send:hover{transform:translateY(-1px);}
.sys-btn:disabled{opacity:.55;cursor:not-allowed;transform:none;}

@media(max-width:600px){.msg-row{max-width:88%;}.chat-body{padding:12px;}}
</style>

<div class="chat-wrap">
  <div class="chat-container" style="position:relative;">
    <!-- Header -->
    <div class="chat-hd">
      <div class="chat-hd-icon">💬</div>
      <div class="chat-hd-info">
        <div class="chat-hd-name">Nhóm Khu Trọ</div>
        <div class="chat-hd-sub">
          <span class="online-dot"></span>
          <?= $totalMembers ?> thành viên · Admin & Người thuê
        </div>
      </div>
      <?php if($isAdmin): ?>
      <button onclick="sendSystemMsg()" class="btn btn-outline btn-sm" title="Gửi thông báo hệ thống">📢</button>
      <?php endif; ?>
    </div>

    <?php $pinnedCount = count($pinnedMessages ?? []); ?>
    <div class="pin-board <?= $pinnedCount > 0 ? 'show' : '' ?>" id="pinBoard">
      <div class="pin-head">
        <span id="pinTitle">Danh sách ghim (<?= $pinnedCount ?>)</span>
        <button type="button" class="pin-toggle" onclick="togglePinBoard()">
          <span id="pinToggleText">Thu gọn</span>
          <span id="pinToggleIcon">⌃</span>
        </button>
      </div>
      <div class="pin-list" id="pinList">
        <?php foreach (($pinnedMessages ?? []) as $pin): ?>
          <?php
            $pinText = trim($pin['noi_dung'] ?? '');
            $pinPreview = mb_strlen($pinText, 'UTF-8') > 120 ? mb_substr($pinText, 0, 120, 'UTF-8') . '...' : $pinText;
          ?>
          <div class="pin-item" data-id="<?= (int)$pin['id'] ?>" onclick="jumpToPinned(<?= (int)$pin['id'] ?>)">
            <div class="pin-icon">≡</div>
            <div class="pin-info">
              <div class="pin-title">Tin nhắn</div>
              <div class="pin-text"><?= htmlspecialchars($pin['ho_ten'] ?? '') ?>: <?= htmlspecialchars($pinPreview) ?></div>
            </div>
            <button type="button" class="pin-more" onclick="event.stopPropagation();jumpToPinned(<?= (int)$pin['id'] ?>)">...</button>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="pin-all" onclick="togglePinBoard()">Xem tất cả ở bảng tin nhóm ›</div>
    </div>

    <!-- Messages -->
    <div class="chat-body" id="chatBody">
      <?php if(empty($messages)): ?>
      <div style="text-align:center;padding:40px;color:var(--text3);">
        <div style="font-size:36px;margin-bottom:8px;">💬</div>
        <div style="font-size:13px;">Chưa có tin nhắn nào. Hãy bắt đầu cuộc trò chuyện!</div>
      </div>
      <?php else:
        $lastDate = '';
        $lastUser = 0;
        foreach ($messages as $msg):
          $msgDate = date('d/m/Y', strtotime($msg['created_at']));
          if ($msgDate !== $lastDate):
            $lastDate = $msgDate;
            $lastUser = 0;
      ?>
      <div class="msg-date-sep"><span><?= $msgDate === date('d/m/Y') ? 'Hôm nay' : ($msgDate === date('d/m/Y', strtotime('-1 day')) ? 'Hôm qua' : $msgDate) ?></span></div>
      <?php endif; ?>

      <?php if ($msg['loai'] === 'system'): ?>
      <div class="msg-system">📢 <?= htmlspecialchars($msg['noi_dung']) ?></div>
      <?php else:
        $isMe = (int)$msg['user_id'] === $currentUserId;
        $isAdminMsg = in_array($msg['vai_tro'], ['quan_ly', 'chu_tro']);
        $colors = ['#4f8ef7,#7c5cfc','#22c993,#2dd4bf','#f7a94f,#f75c5c','#7c5cfc,#f472b6','#2dd4bf,#4f8ef7'];
        $col = $colors[$msg['user_id'] % count($colors)];
        $init = mb_strtoupper(mb_substr($msg['ho_ten'], 0, 1, 'UTF-8'));
        $showName = (!$isMe && (int)$msg['user_id'] !== $lastUser);
        $msgTextAttr = htmlspecialchars($msg['noi_dung'], ENT_QUOTES, 'UTF-8');
        $replyText = trim($msg['reply_text'] ?? '');
        $reaction = trim($msg['reaction'] ?? '');
        $pinned = (int)($msg['pinned'] ?? 0) === 1;
        $lastUser = (int)$msg['user_id'];
      ?>
      <div class="msg-row <?= $isMe ? 'me' : '' ?>" data-id="<?= $msg['id'] ?>" data-user-id="<?= (int)$msg['user_id'] ?>" data-text="<?= $msgTextAttr ?>" oncontextmenu="openMsgMenu(event,this)">
        <?php if (!$isMe): ?>
        <div class="msg-av" style="background:linear-gradient(135deg,<?= $col ?>);<?= !$showName ? 'visibility:hidden;' : '' ?>"><?= $init ?></div>
        <?php endif; ?>
        <div class="msg-wrap">
          <?php if ($showName && !$isMe): ?>
          <div class="msg-name">
            <?= htmlspecialchars($msg['ho_ten']) ?>
            <?php if ($isAdminMsg): ?><span class="badge-admin">ADMIN</span><?php endif; ?>
          </div>
          <?php endif; ?>
          <?php if ($pinned): ?><div class="msg-pin">📌 Đã ghim</div><?php endif; ?>
          <?php if ($replyText !== ''): ?><div class="msg-reply-ref">↩ <?= htmlspecialchars($replyText) ?></div><?php endif; ?>
          <div class="msg-bubble"><?= nl2br(htmlspecialchars($msg['noi_dung'])) ?></div>
          <div class="msg-time"><?= date('H:i', strtotime($msg['created_at'])) ?></div>
        </div>
        <?php if ($reaction !== ''): ?><div class="msg-reaction"><?= htmlspecialchars($reaction) ?></div><?php endif; ?>
      </div>
      <?php endif;
        endforeach;
      endif; ?>
    </div>

    <!-- Scroll to bottom button -->
    <div class="scroll-bottom" id="scrollBtn" onclick="scrollToBottom()">↓</div>

    <!-- Typing indicator -->
    <div style="padding:0 20px 4px;">
      <div class="typing-indicator" id="typingIndicator">
        <div class="typing-dots"><span></span><span></span><span></span></div>
        <span id="typingText">đang nhập...</span>
      </div>
    </div>

    <!-- Footer -->
    <div class="chat-ft">
      <div class="reply-preview" id="replyPreview">
        <div class="reply-preview-main">
          <div class="reply-preview-title">Đang trả lời</div>
          <div class="reply-preview-text" id="replyPreviewText"></div>
        </div>
        <button class="reply-close" type="button" onclick="clearReply()" title="Bỏ trả lời">×</button>
      </div>
      <div class="chat-input-row">
        <textarea class="chat-input" id="chatInput" placeholder="Nhập tin nhắn..." rows="1"></textarea>
        <button class="chat-send" id="chatSendBtn" onclick="sendMessage()" title="Gửi (Enter)">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
        </button>
      </div>
      <div class="chat-ft-hint">Enter gửi · Shift+Enter xuống dòng</div>
    </div>
  </div>
</div>

<?php if($isAdmin): ?>
<div class="sys-modal" id="sysModal" onclick="if(event.target===this)closeSystemModal()">
  <div class="sys-dialog" role="dialog" aria-modal="true" aria-labelledby="sysModalTitle">
    <div class="sys-head">
      <div class="sys-ico">📢</div>
      <div>
        <div class="sys-title" id="sysModalTitle">Gửi thông báo hệ thống</div>
        <div class="sys-sub">Tin này sẽ hiện giữa luồng chat cho toàn bộ nhóm.</div>
      </div>
    </div>
    <div class="sys-body">
      <textarea class="sys-text" id="sysText" maxlength="1000" placeholder="Nhập nội dung thông báo..."></textarea>
      <div style="font-size:10px;color:var(--text3);margin-top:7px;text-align:right;">Ctrl + Enter để gửi</div>
    </div>
    <div class="sys-actions">
      <button type="button" class="sys-btn cancel" onclick="closeSystemModal()">Hủy</button>
      <button type="button" class="sys-btn send" id="sysSendBtn" onclick="submitSystemMsg()">Gửi thông báo</button>
    </div>
  </div>
</div>
<?php endif; ?>

<div class="msg-action-layer" id="msgActionLayer">
  <div class="react-bar">
    <button type="button" onclick="reactToSelected('❤️')">❤️</button>
    <button type="button" onclick="reactToSelected('😆')">😆</button>
    <button type="button" onclick="reactToSelected('😮')">😮</button>
    <button type="button" onclick="reactToSelected('😢')">😢</button>
    <button type="button" onclick="reactToSelected('😡')">😡</button>
    <button type="button" onclick="reactToSelected('👍')">👍</button>
  </div>
  <div class="ctx-menu">
    <button type="button" class="ctx-item" onclick="replySelected()"><span>Trả lời</span><span class="ctx-ico">↩</span></button>
    <button type="button" class="ctx-item" onclick="copySelected()"><span>Sao chép</span><span class="ctx-ico">⧉</span></button>
    <button type="button" class="ctx-item" onclick="forwardSelected()"><span>Chuyển tiếp</span><span class="ctx-ico">➜</span></button>
    <button type="button" class="ctx-item" onclick="pinSelected()"><span>Ghim</span><span class="ctx-ico">📌</span></button>
    <button type="button" class="ctx-item danger" onclick="deleteSelected()"><span>Xóa</span><span class="ctx-ico">🗑</span></button>
  </div>
</div>

<script>
const CURRENT_USER_ID = <?= $currentUserId ?>;
const CURRENT_NAME    = '<?= addslashes($_SESSION['ho_ten'] ?? $_SESSION['user'] ?? '') ?>';
const CURRENT_ROLE    = '<?= $_SESSION['vai_tro'] ?? 'user' ?>';
const IS_ADMIN        = <?= $isAdmin ? 'true' : 'false' ?>;
const COLORS = ['#4f8ef7,#7c5cfc','#22c993,#2dd4bf','#f7a94f,#f75c5c','#7c5cfc,#f472b6','#2dd4bf,#4f8ef7'];

let lastMsgId = <?= !empty($messages) ? end($messages)['id'] : 0 ?>;
let polling = null;
let lastRenderUser = 0;
let selectedMessage = null;
let replyTo = null;
let touchTimer = null;

function esc(s) { return String(s ?? '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
function attr(s) { return esc(s).replace(/"/g,'&quot;'); }

function renderMsg(msg, showAvatar) {
  const isMe = parseInt(msg.user_id) === CURRENT_USER_ID;
  const isAdminMsg = ['quan_ly','chu_tro'].includes(msg.vai_tro);
  const col = COLORS[msg.user_id % COLORS.length];
  const init = msg.ho_ten.charAt(0).toUpperCase();
  const time = msg.created_at ? msg.created_at.substring(11, 16) : '';
  const showName = !isMe && showAvatar;

  if (msg.loai === 'system') {
    return `<div class="msg-system">📢 ${esc(msg.noi_dung)}</div>`;
  }

  const replyText = msg.reply_text ? `<div class="msg-reply-ref">↩ ${esc(msg.reply_text)}</div>` : '';
  const pinMark = parseInt(msg.pinned || 0) === 1 ? `<div class="msg-pin">📌 Đã ghim</div>` : '';
  const reaction = msg.reaction ? `<div class="msg-reaction">${esc(msg.reaction)}</div>` : '';

  let html = `<div class="msg-row ${isMe?'me':''}" data-id="${msg.id}" data-user-id="${msg.user_id}" data-text="${attr(msg.noi_dung)}" oncontextmenu="openMsgMenu(event,this)">`;
  if (!isMe) {
    html += `<div class="msg-av" style="background:linear-gradient(135deg,${col});${!showName?'visibility:hidden;':''}">${init}</div>`;
  }
  html += `<div class="msg-wrap">`;
  if (showName) {
    html += `<div class="msg-name">${esc(msg.ho_ten)}${isAdminMsg?'<span class="badge-admin">ADMIN</span>':''}</div>`;
  }
  html += `${pinMark}${replyText}<div class="msg-bubble">${esc(msg.noi_dung).replace(/\n/g,'<br>')}</div>`;
  html += `<div class="msg-time">${time}</div>`;
  html += `</div>${reaction}</div>`;
  return html;
}

async function sendMessage() {
  const input = document.getElementById('chatInput');
  const btn   = document.getElementById('chatSendBtn');
  const text  = input.value.trim();
  if (!text) return;

  input.value = '';
  input.style.height = '40px';
  btn.disabled = true;

  try {
    const res = await fetch('index.php?controller=chat&action=send', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify({ noi_dung: text, reply_to_id: replyTo ? replyTo.id : 0 })
    });
    const data = await res.json();
    if (data.ok && data.message) {
      const showAv = parseInt(data.message.user_id) !== lastRenderUser;
      appendHtml(renderMsg(data.message, showAv));
      lastMsgId = data.message.id;
      lastRenderUser = parseInt(data.message.user_id);
      clearReply();
    }
  } catch(e) {}

  btn.disabled = false;
  input.focus();
}

function appendHtml(html) {
  const body = document.getElementById('chatBody');
  body.insertAdjacentHTML('beforeend', html);
  scrollToBottom();
}

function scrollToBottom() {
  const body = document.getElementById('chatBody');
  body.scrollTop = body.scrollHeight;
}

function insertEmoji(emoji) {
  const input = document.getElementById('chatInput');
  const start = input.selectionStart;
  input.value = input.value.substring(0, start) + emoji + input.value.substring(input.selectionEnd);
  input.selectionStart = input.selectionEnd = start + emoji.length;
  input.focus();
}

function openMsgMenu(e, row) {
  e.preventDefault();
  selectedMessage = {
    id: parseInt(row.dataset.id),
    userId: parseInt(row.dataset.userId || 0),
    text: row.dataset.text || '',
    row
  };

  const layer = document.getElementById('msgActionLayer');
  const x = e.touches ? e.touches[0].clientX : e.clientX;
  const y = e.touches ? e.touches[0].clientY : e.clientY;
  layer.classList.add('show');
  layer.style.left = Math.min(x, window.innerWidth - layer.offsetWidth - 12) + 'px';
  layer.style.top = Math.max(12, Math.min(y - 58, window.innerHeight - layer.offsetHeight - 12)) + 'px';
}

function closeMsgMenu() {
  const layer = document.getElementById('msgActionLayer');
  if (layer) layer.classList.remove('show');
}

function setReactionBadge(row, reaction) {
  let badge = row.querySelector('.msg-reaction');
  if (!badge) {
    badge = document.createElement('div');
    badge.className = 'msg-reaction';
    row.appendChild(badge);
  }
  badge.textContent = reaction;
}

async function reactToSelected(reaction) {
  if (!selectedMessage) return;
  const msg = selectedMessage;
  setReactionBadge(msg.row, reaction);
  closeMsgMenu();
  await fetch('index.php?controller=chat&action=react', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id: msg.id, reaction })
  }).catch(() => {});
}

function replySelected() {
  if (!selectedMessage) return;
  replyTo = { id: selectedMessage.id, text: selectedMessage.text };
  const box = document.getElementById('replyPreview');
  const text = document.getElementById('replyPreviewText');
  text.textContent = replyTo.text;
  box.classList.add('show');
  closeMsgMenu();
  document.getElementById('chatInput').focus();
}

function clearReply() {
  replyTo = null;
  const box = document.getElementById('replyPreview');
  if (box) box.classList.remove('show');
}

async function copySelected() {
  if (!selectedMessage) return;
  try {
    await navigator.clipboard.writeText(selectedMessage.text);
  } catch (e) {
    const input = document.getElementById('chatInput');
    input.value = selectedMessage.text;
    input.select();
    document.execCommand('copy');
    input.value = '';
  }
  closeMsgMenu();
}

function forwardSelected() {
  if (!selectedMessage) return;
  const input = document.getElementById('chatInput');
  input.value = selectedMessage.text;
  input.dispatchEvent(new Event('input'));
  closeMsgMenu();
  input.focus();
}

async function deleteSelected() {
  if (!selectedMessage) return;
  if (!IS_ADMIN && selectedMessage.userId !== CURRENT_USER_ID) {
    closeMsgMenu();
    return;
  }
  const msg = selectedMessage;
  closeMsgMenu();
  const res = await fetch('index.php?controller=chat&action=delete', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id: msg.id })
  }).catch(() => null);
  if (res) {
    removePinnedItem(msg.id);
    msg.row.remove();
  }
}

async function pinSelected() {
  if (!selectedMessage) return;
  if (!IS_ADMIN && selectedMessage.userId !== CURRENT_USER_ID) {
    closeMsgMenu();
    return;
  }
  const msg = selectedMessage;
  closeMsgMenu();
  const res = await fetch('index.php?controller=chat&action=pin', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ id: msg.id })
  }).catch(() => null);
  if (!res) return;
  const data = await res.json();
  if (!data.ok) return;

  let pin = msg.row.querySelector('.msg-pin');
  if (parseInt(data.pinned || 0) === 1) {
    if (!pin) {
      pin = document.createElement('div');
      pin.className = 'msg-pin';
      pin.textContent = '📌 Đã ghim';
      msg.row.querySelector('.msg-wrap').prepend(pin);
    }
    upsertPinnedItem(data.message || {id: msg.id, ho_ten: CURRENT_NAME, noi_dung: msg.text});
  } else if (pin) {
    pin.remove();
    removePinnedItem(msg.id);
  }
}

function pinCount() {
  return document.querySelectorAll('#pinList .pin-item').length;
}

function updatePinTitle() {
  const board = document.getElementById('pinBoard');
  const title = document.getElementById('pinTitle');
  const count = pinCount();
  if (title) title.textContent = `Danh sách ghim (${count})`;
  if (board) board.classList.toggle('show', count > 0);
}

function pinnedPreview(msg) {
  const text = String(msg.noi_dung || '');
  return text.length > 120 ? text.substring(0, 120) + '...' : text;
}

function upsertPinnedItem(msg) {
  const list = document.getElementById('pinList');
  if (!list || !msg) return;
  removePinnedItem(parseInt(msg.id), false);
  const item = document.createElement('div');
  item.className = 'pin-item';
  item.dataset.id = msg.id;
  item.onclick = () => jumpToPinned(parseInt(msg.id));
  item.innerHTML = `
    <div class="pin-icon">≡</div>
    <div class="pin-info">
      <div class="pin-title">Tin nhắn</div>
      <div class="pin-text">${esc(msg.ho_ten || CURRENT_NAME)}: ${esc(pinnedPreview(msg))}</div>
    </div>
    <button type="button" class="pin-more">...</button>
  `;
  item.querySelector('.pin-more').onclick = (e) => {
    e.stopPropagation();
    jumpToPinned(parseInt(msg.id));
  };
  list.prepend(item);
  updatePinTitle();
}

function removePinnedItem(id, update = true) {
  const item = document.querySelector(`#pinList .pin-item[data-id="${parseInt(id)}"]`);
  if (item) item.remove();
  if (update) updatePinTitle();
}

function renderPinnedBoard(messages) {
  const list = document.getElementById('pinList');
  if (!list) return;
  list.innerHTML = '';
  (messages || []).forEach(msg => upsertPinnedItem(msg));
  updatePinTitle();
}

async function refreshPinnedBoard() {
  try {
    const res = await fetch('index.php?controller=chat&action=pins');
    const data = await res.json();
    if (data.ok) renderPinnedBoard(data.messages || []);
  } catch(e) {}
}

function togglePinBoard() {
  const board = document.getElementById('pinBoard');
  const text = document.getElementById('pinToggleText');
  const icon = document.getElementById('pinToggleIcon');
  if (!board) return;
  board.classList.toggle('collapsed');
  const collapsed = board.classList.contains('collapsed');
  if (text) text.textContent = collapsed ? 'Mở rộng' : 'Thu gọn';
  if (icon) icon.textContent = collapsed ? '⌄' : '⌃';
}

function jumpToPinned(id) {
  const row = document.querySelector(`.msg-row[data-id="${parseInt(id)}"]`);
  if (!row) return;
  row.scrollIntoView({behavior:'smooth', block:'center'});
  row.classList.add('pin-focus');
  setTimeout(() => row.classList.remove('pin-focus'), 1400);
}

function sendSystemMsg() {
  openSystemModal();
}

function openSystemModal() {
  const modal = document.getElementById('sysModal');
  const input = document.getElementById('sysText');
  if (!modal || !input) return;
  modal.classList.add('show');
  document.body.style.overflow = 'hidden';
  input.value = '';
  setTimeout(() => input.focus(), 30);
}

function closeSystemModal() {
  const modal = document.getElementById('sysModal');
  if (!modal) return;
  modal.classList.remove('show');
  document.body.style.overflow = '';
}

function submitSystemMsg() {
  const input = document.getElementById('sysText');
  const btn = document.getElementById('sysSendBtn');
  const text = input ? input.value.trim() : '';
  if (!text) return;
  if (btn) btn.disabled = true;

  fetch('index.php?controller=chat&action=send', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({ noi_dung: text, loai: 'system' })
  }).then(r => r.json()).then(data => {
    if (data.ok) {
      appendHtml(`<div class="msg-system">📢 ${esc(text)}</div>`);
      lastMsgId = data.message.id;
      closeSystemModal();
    }
  }).finally(() => {
    if (btn) btn.disabled = false;
  });
}

// Polling
function startPolling() {
  polling = setInterval(async () => {
    try {
      const res = await fetch('index.php?controller=chat&action=poll&after=' + lastMsgId);
      const data = await res.json();
      if (data.ok && data.messages.length > 0) {
        data.messages.forEach(msg => {
          if (parseInt(msg.user_id) !== CURRENT_USER_ID) {
            const showAv = parseInt(msg.user_id) !== lastRenderUser;
            appendHtml(renderMsg(msg, showAv));
            lastRenderUser = parseInt(msg.user_id);
          }
          lastMsgId = Math.max(lastMsgId, parseInt(msg.id));
        });
        // Sound notification
        if (document.hidden) document.title = '💬 Tin nhắn mới!';
      }
      refreshPinnedBoard();
    } catch(e) {}
  }, 2500);
}

// Scroll button
document.addEventListener('DOMContentLoaded', function() {
  const body = document.getElementById('chatBody');
  const scrollBtn = document.getElementById('scrollBtn');

  body.addEventListener('scroll', () => {
    const atBottom = body.scrollHeight - body.scrollTop - body.clientHeight < 100;
    scrollBtn.classList.toggle('show', !atBottom);
  });

  // Input handlers
  const input = document.getElementById('chatInput');
  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });
  input.addEventListener('input', function() {
    this.style.height = '40px';
    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
  });

  document.addEventListener('click', function(e) {
    const layer = document.getElementById('msgActionLayer');
    if (layer && !layer.contains(e.target)) closeMsgMenu();
  });

  body.addEventListener('touchstart', function(e) {
    const row = e.target.closest('.msg-row');
    if (!row) return;
    touchTimer = setTimeout(() => openMsgMenu(e, row), 520);
  }, {passive:false});

  body.addEventListener('touchend', function() {
    if (touchTimer) clearTimeout(touchTimer);
  });

  body.addEventListener('touchmove', function() {
    if (touchTimer) clearTimeout(touchTimer);
  });

  const sysText = document.getElementById('sysText');
  if (sysText) {
    sysText.addEventListener('keydown', function(e) {
      if (e.key === 'Enter' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault();
        submitSystemMsg();
      }
      if (e.key === 'Escape') {
        closeSystemModal();
      }
    });
  }

  scrollToBottom();
  startPolling();
});

// Reset title on focus
window.addEventListener('focus', () => { document.title = '<?= $title ?> — Quản lý phòng trọ'; });
window.addEventListener('beforeunload', () => { if(polling) clearInterval(polling); });
</script>

<?php require_once 'app/Views/Layouts/footer.php'; ?>
