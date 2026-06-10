<?php
class HopDongController {
    private HopDongModel        $model;
    private PhongModel          $phongModel;
    private NguoiThueModel      $ntModel;
    private NguoiThuePhongModel $ntPhongModel;
    private XeModel             $xeModel;
    private KhuTroModel         $khuModel;

    public function __construct() {
        $this->model        = new HopDongModel();
        $this->phongModel   = new PhongModel();
        $this->ntModel      = new NguoiThueModel();
        $this->ntPhongModel = new NguoiThuePhongModel();
        $this->xeModel      = new XeModel();
        $this->khuModel     = new KhuTroModel();
    }

    public function index() {
        $filter = $_GET['filter'] ?? 'all';
        $trangThai = ($filter !== 'all') ? $filter : null;
        $list = $this->model->getAll($trangThai);
        foreach ($list as &$hd) {
            $hd['nguoi_o_cung'] = $this->ntPhongModel->getByHopDong($hd['id']);
            $hd['xe_list']      = $this->xeModel->getByHopDong($hd['id']);
        }
        unset($hd);
        $title = 'Quản lý hợp đồng';
        require 'app/Views/HopDong/index.php';
    }

    // Tạo HĐ: chọn phòng → nhập thông tin người thuê → tạo luôn
    public function create() {
        $title  = 'Tạo hợp đồng mới';
        $error  = '';
        // Chỉ lấy phòng ĐANG TRỐNG
        $phongs = $this->phongModel->getTrong();
        $khuModel = new KhuTroModel();
        $khus   = $khuModel->getAll();
        // Danh sách người thuê đã đăng ký (chưa có HĐ hiệu lực)
        $db = \Database::getInstance();
        $stmtNT = $db->query(
            "SELECT nt.* FROM nguoi_thue nt
             LEFT JOIN hop_dong hd ON hd.nguoi_thue_id = nt.id AND hd.trang_thai = 'hieu_luc'
             WHERE hd.id IS NULL
             ORDER BY nt.ho_ten"
        );
        $nguoiThueList = $stmtNT->fetchAll();

        // Danh sách tài khoản user để admin chọn liên kết
        $stmtAcc = $db->query(
            "SELECT id, ho_ten, username, sdt, email FROM account WHERE vai_tro='user' ORDER BY ho_ten"
        );
        $accountList = $stmtAcc->fetchAll();

        // Thông tin admin (Bên A hợp đồng)
        $adminInfo = $db->prepare(
            "SELECT a.ho_ten, a.sdt, a.email FROM account a WHERE a.id=? LIMIT 1"
        );
        $adminInfo->execute([(int)($_SESSION['user_id'] ?? 0)]);
        $adminInfo = $adminInfo->fetch() ?: [];

        // AJAX: lấy thông tin user theo account_id
        if (isset($_GET['action']) && $_GET['action'] === 'getAccountInfo') {
            header('Content-Type: application/json');
            $acc_id = (int)($_GET['acc_id'] ?? 0);
            $s = $db->prepare(
                "SELECT a.ho_ten, a.sdt, a.email,
                        nt.cccd, nt.dia_chi, nt.ngay_sinh, nt.gioi_tinh
                 FROM account a
                 LEFT JOIN nguoi_thue nt ON nt.account_id = a.id
                 WHERE a.id = ? LIMIT 1"
            );
            $s->execute([$acc_id]);
            echo json_encode($s->fetch() ?: []);
            exit;
        }

        // AJAX: lấy thông tin phòng + khu
        if (isset($_GET['action']) && $_GET['action'] === 'getPhongInfo') {
            header('Content-Type: application/json');
            $phong_id = (int)($_GET['phong_id'] ?? 0);
            $s = $db->prepare(
                "SELECT p.so_phong, p.gia, p.dien_tich, p.so_nguoi,
                        k.ten_khu, k.dia_chi, k.ma_khu
                 FROM phong p
                 LEFT JOIN khu_tro k ON p.khu_id = k.id
                 WHERE p.id = ? LIMIT 1"
            );
            $s->execute([$phong_id]);
            echo json_encode($s->fetch() ?: []);
            exit;
        }


        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $phong_id  = (int)($_POST['phong_id']   ?? 0);
            $ngay_bd   = $_POST['ngay_bat_dau']     ?? '';
            $ngay_kt   = $_POST['ngay_ket_thuc']    ?? '';
            $tien_coc  = (float)($_POST['tien_coc'] ?? 0);
            $ghi_chu   = trim($_POST['ghi_chu']     ?? '');
            $account_id_chon = (int)($_POST['account_id'] ?? 0);
            // Thông tin người thuê (tạo luôn trong form)
            $ho_ten    = trim($_POST['ho_ten']      ?? '');
            $cccd      = trim($_POST['cccd']        ?? '');
            $sdt       = trim($_POST['sdt']         ?? '');
            $dia_chi   = trim($_POST['dia_chi']     ?? '');

            if (!$phong_id || !$ngay_bd || !$ngay_kt || !$ho_ten) {
                $error = 'Vui lòng điền đầy đủ: phòng, ngày và họ tên người thuê!';
            } else {
                // 1. Tạo người thuê mới
                $this->ntModel->create($ho_ten, $cccd, $sdt, $dia_chi);
                $nguoi_thue_id = $this->ntModel->getLastId();

                // Link account: ưu tiên admin chọn tay, fallback theo SĐT/tên
                $db = Database::getInstance();
                $linkedAccount = $account_id_chon ?: null;
                if (!$linkedAccount) {
                    $s = $db->prepare("SELECT id FROM account WHERE vai_tro='user' AND (sdt=? OR ho_ten=?) LIMIT 1");
                    $s->execute([$sdt, $ho_ten]);
                    $linkedAccount = $s->fetchColumn() ?: null;
                }
                if ($linkedAccount) {
                    $db->prepare("UPDATE nguoi_thue SET account_id=? WHERE id=?")
                       ->execute([$linkedAccount, $nguoi_thue_id]);
                }

                // Lấy thông tin phòng + khu để render nội dung HĐ
                $phongInfo = $db->prepare(
                    "SELECT p.so_phong, p.gia, p.dien_tich, p.so_nguoi, k.ten_khu, k.dia_chi AS dia_chi_khu
                     FROM phong p LEFT JOIN khu_tro k ON p.khu_id=k.id WHERE p.id=? LIMIT 1"
                );
                $phongInfo->execute([$phong_id]);
                $pi = $phongInfo->fetch();

                // Lấy thông tin admin
                $adm = $db->prepare("SELECT ho_ten, sdt FROM account WHERE id=? LIMIT 1");
                $adm->execute([(int)($_SESSION['user_id'] ?? 0)]);
                $ad = $adm->fetch();

                // Tính thời hạn (tháng)
                $thang = (int)round((strtotime($ngay_kt) - strtotime($ngay_bd)) / (30*86400));

                // Render nội dung hợp đồng: thay thế biến trong mẫu
                $template = trim($_POST['dieu_khoan'] ?? '');
                $so_tien_chu = function_exists('soThanhChu') ? soThanhChu((int)($pi['gia'] ?? 0)) : number_format($pi['gia'] ?? 0);
                $coc_chu     = function_exists('soThanhChu') ? soThanhChu((int)$tien_coc) : number_format($tien_coc);
                $vars = [
                    '{ngay_ky}'       => date('d/m/Y'),
                    '{ten_chu_tro}'   => $ad['ho_ten'] ?? '',
                    '{sdt_chu_tro}'   => $ad['sdt']    ?? '',
                    '{dia_chi_phong}' => ($pi['dia_chi_khu'] ?? '') . ' — ' . ($pi['ten_khu'] ?? ''),
                    '{ho_ten}'        => $ho_ten,
                    '{cccd}'          => $cccd,
                    '{sdt}'           => $sdt,
                    '{dia_chi}'       => $dia_chi,
                    '{so_phong}'      => $pi['so_phong']  ?? '',
                    '{ten_khu}'       => $pi['ten_khu']   ?? '',
                    '{ngay_bat_dau}'  => date('d/m/Y', strtotime($ngay_bd)),
                    '{ngay_ket_thuc}' => date('d/m/Y', strtotime($ngay_kt)),
                    '{thoi_han}'      => $thang . ' tháng',
                    '{so_nguoi}'      => $pi['so_nguoi']  ?? '',
                    '{gia_thue}'      => number_format($pi['gia'] ?? 0),
                    '{gia_thue_chu}'  => $so_tien_chu,
                    '{tien_coc}'      => number_format($tien_coc),
                    '{tien_coc_chu}'  => $coc_chu,
                ];
                $noi_dung = str_replace(array_keys($vars), array_values($vars), $template);

                // 2. Tạo hợp đồng
                $this->model->create($phong_id, $nguoi_thue_id, $ngay_bd, $ngay_kt, $tien_coc, $ghi_chu, [], $noi_dung);
                $hdId = $this->model->getLastInsertId();

                // 3. Lưu chữ ký (nếu có)
                $sig_path = '';
                $sig_data = $_POST['chu_ky'] ?? '';
                if ($sig_data && str_starts_with($sig_data, 'data:image/png;base64,')) {
                    $dir = 'public/uploads/signatures/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $img  = base64_decode(str_replace('data:image/png;base64,', '', $sig_data));
                    $name = 'sig_' . $hdId . '_' . time() . '.png';
                    if (file_put_contents($dir . $name, $img)) {
                        $sig_path = $dir . $name;
                    }
                }

                // 4. Lưu vào nguoi_thue_phong (chủ HĐ)
                $this->ntPhongModel->create([
                    'hop_dong_id'     => $hdId,
                    'phong_id'        => $phong_id,
                    'ho_ten'          => $ho_ten,
                    'cccd'            => $cccd,
                    'sdt'             => $sdt,
                    'ngay_sinh'       => $_POST['ngay_sinh'] ?? '',
                    'gioi_tinh'       => $_POST['gioi_tinh'] ?? 'nam',
                    'que_quan'        => $dia_chi,
                    'avatar'          => $sig_path,
                    'la_chu_hop_dong' => 1,
                ]);

                // 5. Lưu người ở cùng + xe của họ (nếu có)
                foreach ($_POST['nguoi_o_cung'] ?? [] as $ocIdx => $nt) {
                    $ht = trim($nt['ho_ten'] ?? '');
                    if (!$ht) continue;
                    $this->ntPhongModel->create([
                        'hop_dong_id'     => $hdId,
                        'phong_id'        => $phong_id,
                        'ho_ten'          => $ht,
                        'cccd'            => trim($nt['cccd'] ?? ''),
                        'sdt'             => trim($nt['sdt']  ?? ''),
                        'ngay_sinh'       => $nt['ngay_sinh'] ?? '',
                        'gioi_tinh'       => $nt['gioi_tinh'] ?? 'nam',
                        'que_quan'        => '',
                        'avatar'          => '',
                        'la_chu_hop_dong' => 0,
                    ]);
                    // Xe của người ở cùng này
                    foreach ($nt['xe'] ?? [] as $xe) {
                        $bs = trim($xe['bien_so'] ?? '');
                        if (!$bs) continue;
                        $this->xeModel->create([
                            'hop_dong_id' => $hdId,
                            'phong_id'    => $phong_id,
                            'bien_so'     => strtoupper($bs),
                            'loai_xe'     => $xe['loai_xe'] ?? 'xe_may',
                            'mau_sac'     => trim($xe['mau_sac'] ?? ''),
                            'ghi_chu'     => 'Xe của: ' . $ht,
                        ]);
                    }
                }

                // 6. Lưu xe (nếu có)
                foreach ($_POST['xe_list'] ?? [] as $xe) {
                    $bs = trim($xe['bien_so'] ?? '');
                    if (!$bs) continue;
                    $this->xeModel->create([
                        'hop_dong_id' => $hdId,
                        'phong_id'    => $phong_id,
                        'bien_so'     => strtoupper($bs),
                        'loai_xe'     => $xe['loai_xe'] ?? 'xe_may',
                        'mau_sac'     => trim($xe['mau_sac'] ?? ''),
                        'ghi_chu'     => '',
                    ]);
                }

                // 7. Cập nhật phòng → đang thuê
                $this->phongModel->updateTrangThai($phong_id, 'dang_thue');

                header('Location: index.php?controller=hopdong&action=index&msg=created');
                exit;
            }
        }
        require 'app/Views/HopDong/create.php';
    }

    // Thêm người ở cùng (không phải chủ HĐ)
    public function themNguoi() {
        $hd_id = (int)($_GET['id'] ?? 0);
        $hd    = $this->model->getById($hd_id);
        if (!$hd) { header('Location: index.php?controller=hopdong&action=index'); exit; }
        $soNguoi = $this->ntPhongModel->countByHopDong($hd_id);
        $title   = 'Thêm người thuê chung';
        $error   = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ho_ten    = trim($_POST['ho_ten']   ?? '');
            $cccd      = trim($_POST['cccd']     ?? '');
            $sdt       = trim($_POST['sdt']      ?? '');
            $ngay_sinh = $_POST['ngay_sinh']     ?? '';
            $gioi_tinh = $_POST['gioi_tinh']     ?? 'nam';
            $que_quan  = trim($_POST['que_quan'] ?? '');
            $avatar    = '';
            $soNguoi   = $this->ntPhongModel->countByHopDong($hd_id);
            if (!$ho_ten) {
                $error = 'Vui lòng nhập họ tên!';
            } elseif ($soNguoi >= 4) {
                $error = 'Phòng này đã đủ 4 người, không thể thêm người ở chung!';
            } else {
                if (!empty($_FILES['avatar']['tmp_name'])) {
                    $dir = 'public/uploads/avatars/';
                    if (!is_dir($dir)) mkdir($dir, 0755, true);
                    $ext  = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                    $name = 'nt_' . time() . '_' . rand(100,999) . '.' . $ext;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $dir.$name))
                        $avatar = $dir.$name;
                }
                $this->ntPhongModel->create([
                    'hop_dong_id'     => $hd_id,
                    'phong_id'        => $hd['phong_id'],
                    'ho_ten'          => $ho_ten,
                    'cccd'            => $cccd,
                    'sdt'             => $sdt,
                    'ngay_sinh'       => $ngay_sinh,
                    'gioi_tinh'       => $gioi_tinh,
                    'que_quan'        => $que_quan,
                    'avatar'          => $avatar,
                    'la_chu_hop_dong' => 0,
                ]);
                header('Location: index.php?controller=hopdong&action=index&msg=created');
                exit;
            }
        }
        require 'app/Views/HopDong/them_nguoi.php';
    }

    public function xoaNguoi() {
        $id = (int)($_GET['id'] ?? 0);
        $nt = $this->ntPhongModel->getById($id);
        if ($nt && $nt['la_chu_hop_dong'] == 1) {
            header('Location: index.php?controller=hopdong&action=index');
            exit;
        }
        $this->ntPhongModel->delete($id);
        header('Location: index.php?controller=hopdong&action=index&msg=deleted');
        exit;
    }

    public function ketThuc() {
        $id = (int)($_GET['id'] ?? 0);
        $hd = $this->model->getById($id);
        if ($hd) {
            // Kết thúc HĐ → phòng về trống
            $this->model->updateTrangThai($id, 'het_han');
            $this->phongModel->updateTrangThai($hd['phong_id'], 'trong');
        }
        header('Location: index.php?controller=hopdong&action=index&msg=ended');
        exit;
    }

    public function delete() {
        $id = (int)($_GET['id'] ?? 0);
        $hd = $this->model->getById($id);
        if ($hd) {
            if ($hd['trang_thai'] === 'hieu_luc') {
                // Xóa HĐ hiệu lực → phòng về trống
                $this->phongModel->updateTrangThai($hd['phong_id'], 'trong');
            }
            // Xóa luôn người thuê gắn với HĐ này (nguoi_thue_phong cascade)
            // Xóa người thuê chính nếu không còn HĐ nào khác
            $nguoi_thue_id = $hd['nguoi_thue_id'];
            $this->model->delete($id);
            // Kiểm tra còn HĐ không, nếu không → xóa người thuê
            $conHD = $this->model->countByNguoiThue($nguoi_thue_id);
            if ($conHD === 0) {
                $this->ntModel->forceDelete($nguoi_thue_id);
            }
        }
        header('Location: index.php?controller=hopdong&action=index&msg=deleted');
        exit;
    }
}