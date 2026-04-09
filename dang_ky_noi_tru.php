<?php 
include 'header.php'; 

if ($role != 'sinh_vien') {
    echo "<div class='container mt-5'><div class='alert alert-danger'>Bạn không có quyền truy cập trang này.</div></div>";
    include 'footer.php'; exit();
}

$message = "";
$user_id = $_SESSION['ten_dang_nhap'];

// 1. Lấy thông tin sinh viên
$sql_sv = "SELECT * FROM sinh_vien WHERE ten_dang_nhap = '$user_id'";
$res_sv = mysqli_query($conn, $sql_sv);
$sv = mysqli_fetch_assoc($res_sv);

// 2. Logic xử lý Đăng ký
if (isset($_POST['gui_don'])) {
    $ma_phong = mysqli_real_escape_string($conn, $_POST['ma_phong']);
    $ngay_dk = date('Y-m-d');
    $ma_don = "DK" . date('His') . rand(10, 99);

    // Kiểm tra logic: Đã có phòng chưa?
    if ($sv['ma_phong']) {
        $message = "<div class='alert alert-warning shadow-sm'><i class='bi bi-exclamation-triangle-fill me-2'></i>Bạn đã có phòng nội trú, không thể đăng ký thêm.</div>";
    } else {
        // Kiểm tra logic: Còn đơn chờ duyệt nào không?
        $check_pending = mysqli_query($conn, "SELECT * FROM dang_ky_noi_tru WHERE ma_sv = '{$sv['ma_sv']}' AND trang_thai = 'Chờ duyệt'");
        
        // Kiểm tra logic: Phòng đó có còn chỗ thực tế không (tránh race condition)
        $room_check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT so_giuong_trong FROM phong WHERE ma_phong = '$ma_phong'"));

        if (mysqli_num_rows($check_pending) > 0) {
            $message = "<div class='alert alert-info shadow-sm'><i class='bi bi-info-circle-fill me-2'></i>Bạn đang có một đơn đăng ký đang chờ xử lý.</div>";
        } elseif ($room_check['so_giuong_trong'] <= 0) {
            $message = "<div class='alert alert-danger shadow-sm'>Rất tiếc, phòng này vừa hết chỗ. Vui lòng chọn phòng khác.</div>";
        } else {
            $sql = "INSERT INTO dang_ky_noi_tru (ma_don, ma_sv, ma_phong, ngay_dang_ky, trang_thai) 
                    VALUES ('$ma_don', '{$sv['ma_sv']}', '$ma_phong', '$ngay_dk', 'Chờ duyệt')";
            
            if (mysqli_query($conn, $sql)) {
                $message = "<div class='alert alert-success shadow-sm text-center'><i class='bi bi-check-circle-fill me-2'></i>Gửi đơn thành công! Vui lòng theo dõi trạng thái tại Lịch sử.</div>";
            }
        }
    }
}

// 3. Lấy danh sách phòng phù hợp giới tính
$sql_phong = "SELECT * FROM phong WHERE so_giuong_trong > 0 AND gioi_tinh_phong = '{$sv['gioi_tinh']}'";
$res_phong = mysqli_query($conn, $sql_phong);
?>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card card-custom shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="fw-bold text-primary mb-0"><i class="bi bi-pencil-square me-2"></i>ĐĂNG KÝ PHÒNG MỚI</h5>
                </div>
                <div class="card-body p-4">
                    <?= $message ?>
                    
                    <form action="" method="POST">
                        <div class="mb-4">
                            <h6 class="fw-bold mb-3"><span class="step-number">1</span>Xác nhận thông tin cá nhân</h6>
                            <div class="row g-3 bg-light p-3 rounded-3">
                                <div class="col-md-6">
                                    <label class="small text-muted">Họ và tên</label>
                                    <p class="fw-bold mb-0"><?= $sv['ho_ten'] ?></p>
                                </div>
                                <div class="col-md-6">
                                    <label class="small text-muted">Mã sinh viên</label>
                                    <p class="fw-bold mb-0"><?= $sv['ma_sv'] ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3"><span class="step-number">2</span>Chọn phòng trống (Dành cho <?= $sv['gioi_tinh'] ?>)</h6>
                            <div class="row g-3">
                                <?php if(mysqli_num_rows($res_phong) > 0): ?>
                                    <?php while($p = mysqli_fetch_assoc($res_phong)): ?>
                                        <div class="col-md-6">
                                            <input type="radio" name="ma_phong" value="<?= $p['ma_phong'] ?>" class="form-check-input d-none" id="p<?= $p['ma_phong'] ?>" required>
                                            <label class="card card-body room-card-select p-3" for="p<?= $p['ma_phong'] ?>">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="fw-bold fs-5 text-dark">Phòng <?= $p['ma_phong'] ?></span>
                                                        <div class="text-muted small">Còn <?= $p['so_giuong_trong'] ?>/<?= $p['so_giuong_toi_da'] ?> chỗ</div>
                                                    </div>
                                                    <div class="text-end">
                                                        <span class="text-primary fw-bold"><?= number_format($p['gia_phong']) ?> đ</span>
                                                        <div class="small text-muted">/tháng</div>
                                                    </div>
                                                </div>
                                            </label>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="col-12 text-center py-4">
                                        <i class="bi bi-emoji-frown fs-1 text-muted"></i>
                                        <p class="text-muted mt-2">Hiện tại không còn phòng trống phù hợp với bạn.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="text-end pt-3">
                            <button type="submit" name="gui_don" class="btn btn-primary px-5 py-2 rounded-pill fw-bold shadow">
                                <i class="bi bi-send-fill me-2"></i> Gửi đơn đăng ký ngay
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom shadow-sm border-0 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="fw-bold mb-0"><i class="bi bi-clock-history me-2"></i>LỊCH SỬ GẦN ĐÂY</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <?php 
                        $history = mysqli_query($conn, "SELECT * FROM dang_ky_noi_tru WHERE ma_sv = '{$sv['ma_sv']}' ORDER BY ngay_dang_ky DESC LIMIT 5");
                        if(mysqli_num_rows($history) > 0):
                            while($row = mysqli_fetch_assoc($history)):
                                $stt_color = ($row['trang_thai'] == 'Đã duyệt') ? 'text-success' : (($row['trang_thai'] == 'Từ chối') ? 'text-danger' : 'text-warning');
                        ?>
                        <li class="list-group-item p-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span class="fw-bold small"><?= $row['ma_don'] ?></span>
                                <span class="small <?= $stt_color ?> fw-bold"><?= $row['trang_thai'] ?></span>
                            </div>
                            <div class="text-muted small">Phòng: <?= $row['ma_phong'] ?> | <?= date('d/m/Y', strtotime($row['ngay_dang_ky'])) ?></div>
                        </li>
                        <?php endwhile; else: ?>
                            <li class="list-group-item text-center py-4 text-muted small">Chưa có đơn đăng ký nào</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="card card-custom bg-light border-0">
                <div class="card-body p-4">
                    <h6 class="fw-bold"><i class="bi bi-info-circle me-2"></i>Lưu ý xét duyệt</h6>
                    <p class="small text-muted mb-0">Hệ thống sẽ ưu tiên xét duyệt dựa trên <b>Đối tượng ưu tiên</b> của sinh viên. Kết quả sẽ có sau 1-3 ngày làm việc.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>