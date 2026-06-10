<?php
class NguoiThueController {
    private NguoiThueModel $model;
    public function __construct() { $this->model = new NguoiThueModel(); }

    public function index() {
        $kw    = trim($_GET['kw'] ?? '');
        $list  = $this->model->getAll($kw);
        $title = 'Danh sách người thuê';
        require 'app/Views/NguoiThue/index.php';
    }

    public function searchQuick() {
        header('Content-Type: application/json; charset=utf-8');

        if (!in_array($_SESSION['vai_tro'] ?? '', ['quan_ly', 'chu_tro'], true)) {
            echo json_encode(['ok' => false, 'results' => []]);
            exit;
        }

        $kw = trim($_GET['q'] ?? '');
        if (mb_strlen($kw, 'UTF-8') < 2) {
            echo json_encode(['ok' => true, 'results' => []]);
            exit;
        }

        $rows = $this->model->quickSearch($kw, 8);
        $results = array_map(function($r) {
            $type = $r['result_type'] ?? 'tenant';
            $hopDongId = (int)($r['hop_dong_id'] ?? 0);
            return [
                'id'       => (int)$r['id'],
                'type'     => $type,
                'typeLabel'=> $type === 'roommate' ? 'Người ở cùng' : 'Người thuê chính',
                'name'     => $r['ho_ten'] ?? '',
                'phone'    => $r['sdt'] ?? '',
                'cccd'     => $r['cccd'] ?? '',
                'room'     => $r['so_phong'] ?? '',
                'area'     => $r['ten_khu'] ?? '',
                'email'    => $r['account_email'] ?? '',
                'username' => $r['username'] ?? '',
                'active'   => !empty($r['hop_dong_id']),
                'url'      => $type === 'roommate' && $hopDongId > 0
                    ? 'index.php?controller=hopdong&action=index&focus_hd=' . $hopDongId
                    : 'index.php?controller=nguoithue&action=edit&id=' . (int)$r['nguoi_thue_id'],
            ];
        }, $rows);

        echo json_encode(['ok' => true, 'results' => $results], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Chỉ cho sửa thông tin, KHÔNG cho tạo mới độc lập
    public function edit() {
        $id   = (int)($_GET['id'] ?? 0);
        $data = $this->model->getById($id);
        if (!$data) { header('Location: index.php?controller=nguoithue&action=index'); exit; }
        $title = 'Chỉnh sửa người thuê';
        $error = '';

        $db       = Database::getInstance();
        $accounts = $db->query(
            "SELECT id, username, ho_ten FROM account WHERE vai_tro='user' ORDER BY ho_ten"
        )->fetchAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ho_ten     = trim($_POST['ho_ten']  ?? '');
            $cccd       = trim($_POST['cccd']    ?? '');
            $sdt        = trim($_POST['sdt']     ?? '');
            $dia_chi    = trim($_POST['dia_chi'] ?? '');
            $account_id = $_POST['account_id'] !== '' ? (int)$_POST['account_id'] : null;

            if (!$ho_ten) {
                $error = 'Vui lòng nhập họ tên!';
            } else {
                $avatar = $data['avatar'] ?? '';
                if (!empty($_FILES['avatar']['tmp_name'])) {
                    $uploadDir = 'public/uploads/avatars/';
                    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                    $ext  = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                    $name = 'avatar_' . $id . '_' . time() . '.' . $ext;
                    $dest = $uploadDir . $name;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dest))
                        $avatar = $dest;
                }
                $this->model->update($id, $ho_ten, $cccd, $sdt, $dia_chi, $avatar);
                $this->model->linkAccount($id, $account_id);
                header('Location: index.php?controller=nguoithue&action=index&msg=updated');
                exit;
            }
        }
        require 'app/Views/NguoiThue/edit.php';
    }

    // Chỉ cho xóa khi KHÔNG còn HĐ hiệu lực
    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $ok = $this->model->delete($id);
        if (!$ok) {
            header('Location: index.php?controller=nguoithue&action=index&msg=cannot_delete');
        } else {
            header('Location: index.php?controller=nguoithue&action=index&msg=deleted');
        }
        exit;
    }
}
