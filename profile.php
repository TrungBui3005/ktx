<?php 
include 'header.php'; 

// 1. Kiểm tra đăng nhập (Bảo vệ trang)
if (!isset($_SESSION['ten_dang_nhap'])) {
    header("Location: login.php");
    exit();
}

$ten_dang_nhap = $_SESSION['ten_dang_nhap'];

// 2. Truy vấn đọc dữ liệu từ bảng sinh_vien JOIN với bảng phong
// Sử dụng LEFT JOIN để nếu sinh viên chưa có phòng thì vẫn hiện được thông tin cá nhân
$sql = "SELECT sv.*, p.gia_phong, p.so_giuong_toi_da 
        FROM sinh_vien sv 
        LEFT JOIN phong p ON sv.ma_phong = p.ma_phong 
        WHERE sv.ten_dang_nhap = '$ten_dang_nhap'";

$result = mysqli_query($conn, $sql);
$data = mysqli_fetch_assoc($result);

// 3. Nếu không tìm thấy dữ liệu trong bảng sinh_vien (Dễ xảy ra nếu mới tạo tài khoản tai_khoan mà chưa tạo profile)
if (!$data) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>
            <i class='bi bi-exclamation-triangle'></i> Hồ sơ sinh viên chưa tồn tại. Vui lòng liên hệ Admin để cập nhật thông tin cho tài khoản: <b>$ten_dang_nhap</b>
          </div></div>";
    include 'footer.php';
    exit();
}
?>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0 mt-4">
            <div class="card-header bg-white py-3 border-bottom">
                <h4 class="mb-0 fw-bold text-primary"><i class="bi bi-person-badge"></i> HỒ SƠ SINH VIÊN</h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center border-end">
                        <div class="avatar-wrapper mb-3">
                            <i class="bi bi-person-circle text-secondary" style="font-size: 100px;"></i>
                        </div>
                        <h5 class="fw-bold"><?= $data['ho_ten'] ?></h5>
                        <p class="text-muted small">MSSV: <?= $data['ma_sv'] ?></p>
                        <hr>
                        <div class="text-start ps-3">
                            <p class="small mb-1"><strong>Trạng thái:</strong> 
                                <span class="badge <?= $data['ma_phong'] ? 'bg-success' : 'bg-secondary' ?>">
                                    <?= $data['ma_phong'] ? 'Đang ở nội trú' : 'Chưa xếp phòng' ?>
                                </span>
                            </p>
                            <p class="small"><strong>Phòng:</strong> <?= $data['ma_phong'] ?? 'N/A' ?></p>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <h6 class="text-uppercase text-muted fw-bold small mb-3">Thông tin chi tiết</h6>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <th width="40%">Họ và Tên:</th>
                                <td><?= htmlspecialchars($data['ho_ten']) ?></td>
                            </tr>
                            <tr>
                                <th>Giới tính:</th>
                                <td><?= $data['gioi_tinh'] ?></td>
                            </tr>
                            <tr>
                                <th>Đối tượng ưu tiên:</th>
                                <td>
                                    <?php if($data['doi_tuong_uu_tien'] != "" && $data['doi_tuong_uu_tien'] != "Không"): ?>
                                        <span class="badge bg-warning text-dark"><?= $data['doi_tuong_uu_tien'] ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">Không có</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Tài khoản đăng nhập:</th>
                                <td><code class="text-primary"><?= $data['ten_dang_nhap'] ?></code></td>
                            </tr>
                        </table>

                        <h6 class="text-uppercase text-muted fw-bold small mt-4 mb-3">Thông tin tài chính (Tạm tính)</h6>
                        <div class="bg-light p-3 rounded">
                            <div class="d-flex justify-content-between">
                                <span>Giá phòng niêm yết:</span>
                                <span class="fw-bold text-danger"><?= number_format($data['gia_phong'] ?? 0) ?> VNĐ/tháng</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white text-end py-3">
                <button onclick="window.print()" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-printer"></i> In thẻ SV
                </button>
                <a href="edit_profile.php" class="btn btn-primary btn-sm px-4">
                    <i class="bi bi-pencil-square"></i> Cập nhật hồ sơ
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>