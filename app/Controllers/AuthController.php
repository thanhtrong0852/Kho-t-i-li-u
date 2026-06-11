<?php
class AuthController {

    public function login() {
        if (!empty($_SESSION['user'])) {
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $u = trim($_POST['username'] ?? '');
            $p = $_POST['password']     ?? '';

            $db   = Database::getInstance();
            $stmt = $db->prepare("SELECT * FROM account WHERE username=? LIMIT 1");
            $stmt->execute([$u]);
            $user = $stmt->fetch();

            if ($user && password_verify($p, $user['password'])) {
                $_SESSION['user']    = $user['username'];
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['ho_ten']  = $user['ho_ten'];
                $_SESSION['vai_tro'] = $user['vai_tro'];
                $dest = ($user['vai_tro'] === 'user')
                    ? 'index.php?controller=user&action=index'
                    : 'index.php?controller=dashboard&action=index';
                header('Location: ' . $dest);
                exit;
            }

            $error = 'Sai tài khoản hoặc mật khẩu!';
        }
        require 'app/Views/Auth/login.php';
    }

    public function register() {
        if (!empty($_SESSION['user'])) {
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }
        $error   = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ho_ten   = trim($_POST['ho_ten']          ?? '');
            $username = trim($_POST['username']         ?? '');
            $password = $_POST['password']              ?? '';
            $confirm  = $_POST['confirm_password']      ?? '';
            $email    = trim($_POST['email']            ?? '');
            $sdt      = trim($_POST['sdt']              ?? '');
            $vai_tro  = 'user';
            $terms    = isset($_POST['terms']);

            if (!$ho_ten || !$username || !$password || !$email || !$sdt) {
                $error = 'Vui lòng điền đầy đủ thông tin bắt buộc!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Email không hợp lệ!';
            } elseif (!preg_match('/^[0-9+\-\s]{9,20}$/', $sdt)) {
                $error = 'Số điện thoại không hợp lệ!';
            } elseif (strlen($username) < 4) {
                $error = 'Tên đăng nhập tối thiểu 4 ký tự!';
            } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
                $error = 'Tên đăng nhập chỉ dùng chữ, số và dấu _';
            } elseif (strlen($password) < 8) {
                $error = 'Mật khẩu tối thiểu 8 ký tự!';
            } elseif ($password !== $confirm) {
                $error = 'Mật khẩu xác nhận không khớp!';
            } elseif (!$terms) {
                $error = 'Vui lòng đồng ý với điều khoản!';
            } else {
                $db = Database::getInstance();
                $this->ensureChatJoinColumn($db);

                // Kiểm tra username đã tồn tại chưa
                $check = $db->prepare("SELECT id FROM account WHERE username=?");
                $check->execute([$username]);
                if ($check->fetch()) {
                    $error = 'Tên đăng nhập đã tồn tại!';
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $ins  = $db->prepare(
                        "INSERT INTO account (ho_ten, username, password, email, sdt, vai_tro, chat_joined_at)
                         VALUES (?,?,?,?,?,?,NOW())"
                    );
                    $ins->execute([$ho_ten, $username, $hash, $email, $sdt, $vai_tro]);
                    $newId = $db->lastInsertId();

                    // Tự động tạo người thuê liên kết với tài khoản
                    $insNT = $db->prepare(
                        "INSERT INTO nguoi_thue (account_id, ho_ten, sdt, cccd, dia_chi)
                         VALUES (?, ?, ?, '', '')"
                    );
                    $insNT->execute([$newId, $ho_ten, $sdt]);

                    $_SESSION['user']    = $username;
                    $_SESSION['user_id'] = $newId;
                    $_SESSION['ho_ten']  = $ho_ten;
                    $_SESSION['vai_tro'] = $vai_tro;
                    header('Location: index.php?controller=user&action=index');
                    exit;
                }
            }
        }
        require 'app/Views/Auth/register.php';
    }

    public function terms() {
        require 'app/Views/Auth/terms.php';
    }

    public function privacy() {
        require 'app/Views/Auth/privacy.php';
    }

    private function ensureChatJoinColumn(PDO $db): void
    {
        $s = $db->prepare("SHOW COLUMNS FROM account LIKE ?");
        $s->execute(['chat_joined_at']);
        if (!$s->fetch()) {
            $db->exec("ALTER TABLE account ADD COLUMN chat_joined_at DATETIME NULL");
            $db->exec("UPDATE account SET chat_joined_at = '1970-01-01 00:00:00' WHERE chat_joined_at IS NULL");
        }
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?controller=auth&action=login');
        exit;
    }

    /**
     * Quên mật khẩu — Bước 1: Xác minh tài khoản
     */
    public function forgotPassword() {
        if (!empty($_SESSION['user'])) {
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }

        $error   = '';
        $success = '';
        $step    = 1; // Bước 1: nhập thông tin xác minh

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $email    = trim($_POST['email'] ?? '');
            $sdt      = trim($_POST['sdt'] ?? '');

            if (!$username) {
                $error = 'Vui lòng nhập tên đăng nhập!';
            } elseif (!$email && !$sdt) {
                $error = 'Vui lòng nhập email hoặc số điện thoại để xác minh!';
            } else {
                $db   = Database::getInstance();
                $stmt = $db->prepare("SELECT * FROM account WHERE username = ? LIMIT 1");
                $stmt->execute([$username]);
                $user = $stmt->fetch();

                if (!$user) {
                    $error = 'Tài khoản không tồn tại!';
                } else {
                    // Xác minh bằng email hoặc SĐT
                    $emailMatch = !empty($email) && strtolower($email) === strtolower($user['email'] ?? '');
                    $sdtMatch   = !empty($sdt) && $sdt === ($user['sdt'] ?? '');

                    if (!$emailMatch && !$sdtMatch) {
                        $error = 'Email hoặc số điện thoại không khớp với tài khoản!';
                    } else {
                        // Xác minh thành công → tạo token reset
                        $token = bin2hex(random_bytes(32));
                        $_SESSION['reset_token']   = $token;
                        $_SESSION['reset_user_id'] = $user['id'];
                        $_SESSION['reset_expires'] = time() + 600; // 10 phút

                        header('Location: index.php?controller=auth&action=resetPassword&token=' . $token);
                        exit;
                    }
                }
            }
        }

        require 'app/Views/Auth/forgot_password.php';
    }

    /**
     * Đặt lại mật khẩu — Bước 2: Nhập mật khẩu mới
     */
    public function resetPassword() {
        if (!empty($_SESSION['user'])) {
            header('Location: index.php?controller=dashboard&action=index');
            exit;
        }

        $token = $_GET['token'] ?? '';
        $error   = '';
        $success = '';

        // Kiểm tra token hợp lệ
        if (empty($token) || $token !== ($_SESSION['reset_token'] ?? '')) {
            $error = 'Link đặt lại mật khẩu không hợp lệ hoặc đã hết hạn!';
            require 'app/Views/Auth/reset_password.php';
            return;
        }

        // Kiểm tra hết hạn
        if (time() > ($_SESSION['reset_expires'] ?? 0)) {
            unset($_SESSION['reset_token'], $_SESSION['reset_user_id'], $_SESSION['reset_expires']);
            $error = 'Link đặt lại mật khẩu đã hết hạn (10 phút). Vui lòng thử lại!';
            require 'app/Views/Auth/reset_password.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $password = $_POST['password'] ?? '';
            $confirm  = $_POST['confirm_password'] ?? '';

            if (strlen($password) < 8) {
                $error = 'Mật khẩu mới tối thiểu 8 ký tự!';
            } elseif ($password !== $confirm) {
                $error = 'Mật khẩu xác nhận không khớp!';
            } else {
                $userId = $_SESSION['reset_user_id'] ?? 0;
                $hash   = password_hash($password, PASSWORD_DEFAULT);

                $db = Database::getInstance();
                $stmt = $db->prepare("UPDATE account SET password = ? WHERE id = ?");
                $stmt->execute([$hash, $userId]);

                // Xóa token
                unset($_SESSION['reset_token'], $_SESSION['reset_user_id'], $_SESSION['reset_expires']);

                $success = 'Đặt lại mật khẩu thành công! Bạn có thể đăng nhập ngay.';
            }
        }

        require 'app/Views/Auth/reset_password.php';
    }
}
