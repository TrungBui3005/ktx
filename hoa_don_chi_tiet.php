<?php 
include 'header.php'; 

if ($role != 'sinh_vien') {
    echo "<div class='alert alert-danger'>Bạn không có quyền truy cập.</div>";
    include 'footer.php'; exit();
}

$user_id = $_SESSION['ten_dang_nhap'];

// 1. Lấy mã sinh viên và mã phòng của sinh viên đang đăng nhập
$sv_query = "SELECT ma_sv, ma_phong FROM sinh_vien WHERE ten_dang_nhap = '$user_id'";
$sv_res = mysqli_query($conn, $sv_query);
$sv = mysqli_fetch_assoc($sv_res);
$ma_phong = $sv['ma_phong'];

if (!$ma_phong) {
    echo "<div class='container mt-5'><div class='alert alert-warning'>Bạn chưa được xếp phòng nên chưa có hóa đơn.</div></div>";
    include 'footer.php'; exit();
}

// 2. Xử lý lấy thông tin hóa đơn
$id_hd = isset($_GET['id']) ? mysqli_real_escape_string($conn, $_GET['id']) : "";

if ($id_hd) {
    // Truy vấn theo mã hóa đơn và phải thuộc đúng phòng của SV đó (Bảo mật)
    $sql_hd = "SELECT * FROM hoa_don WHERE ma_hd = '$id_hd' AND ma_phong = '$ma_phong'";
} else {
    // Nếu không truyền ID, lấy hóa đơn mới nhất của phòng
    $sql_hd = "SELECT * FROM hoa_don WHERE ma_phong = '$ma_phong' ORDER BY nam DESC, thang DESC LIMIT 1";
}

$res_hd = mysqli_query($conn, $sql_hd);
$hd = mysqli_fetch_assoc($res_hd);

if (!$hd) {
    echo "<div class='container mt-5'><div class='alert alert-info'>Hiện chưa có dữ liệu hóa đơn cho phòng của bạn.</div></div>";
    include 'footer.php'; exit();
}

// 3. Logic tính toán tiền điện nước thực tế (Giả định đơn giá)
$don_gia_dien = 3000;  // 3.000đ / số
$don_gia_nuoc = 15000; // 15.000đ / khối

$so_dien = max(0, $hd['chi_so_dien_moi'] - $hd['chi_so_dien_cu']);
$so_nuoc = max(0, $hd['chi_so_nuoc_moi'] - $hd['chi_so_nuoc_cu']);

$tien_dien = $so_dien * $don_gia_dien;
$tien_nuoc = $so_nuoc * $don_gia_nuoc;
// Tiền phòng = Tổng tiền - (Điện + Nước)
$tien_phong = $hd['tong_tien'] - ($tien_dien + $tien_nuoc);
?>

<div class="row justify-content-center py-4">
    <div class="col-md-9 col-lg-7">
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="phong_cua_toi.php" class="text-decoration-none text-muted small">
                <i class="bi bi-chevron-left"></i> Quay lại trang phòng
            </a>
            <button onclick="window.print()" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-printer me-1"></i> In hóa đơn
            </button>
        </div>

        <div class="card card-custom shadow-lg border-0 overflow-hidden">
            <div class="card-header bg-primary text-white p-4 text-center">
                <h4 class="fw-bold mb-1 text-uppercase">Hóa Đơn Dịch Vụ KTX</h4>
                <p class="mb-0 opacity-75 small">
                    Mã số: #<?= $hd['ma_hd'] ?> | Kỳ thanh toán: Tháng <?= $hd['thang'] ?>/<?= $hd['nam'] ?>
                </p>
            </div>

            <div class="card-body p-4 p-md-5">
                <div class="row mb-4">
                    <div class="col-6">
                        <label class="small text-muted text-uppercase d-block">Đại diện thanh toán</label>
                        <span class="fw-bold"><?= $user_display ?></span>
                        <div class="small text-muted">MSV: <?= $sv['ma_sv'] ?></div>
                    </div>
                    <div class="col-6 text-end">
                        <label class="small text-muted text-uppercase d-block">Phòng ở</label>
                        <span class="fw-bold fs-5"><?= $ma_phong ?></span>
                    </div>
                </div>

                <hr class="my-4 border-dashed">

                <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                        <thead class="text-muted small text-uppercase">
                            <tr class="border-bottom">
                                <th>Khoản mục dịch vụ</th>
                                <th class="text-end">Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold text-dark">Tiền thuê phòng</div>
                                    <div class="small text-muted">Phí lưu trú cố định hàng tháng</div>
                                </td>
                                <td class="text-end fw-bold text-dark"><?= number_format($tien_phong) ?> đ</td>
                            </tr>
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold text-dark">Tiền điện</div>
                                    <div class="small text-muted">
                                        Sử dụng: <?= $so_dien ?> số (<?= $hd['chi_so_dien_cu'] ?> → <?= $hd['chi_so_dien_moi'] ?>)
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-dark"><?= number_format($tien_dien) ?> đ</td>
                            </tr>
                            <tr>
                                <td class="py-3">
                                    <div class="fw-bold text-dark">Tiền nước</div>
                                    <div class="small text-muted">
                                        Sử dụng: <?= $so_nuoc ?> khối (<?= $hd['chi_so_nuoc_cu'] ?> → <?= $hd['chi_so_nuoc_moi'] ?>)
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-dark"><?= number_format($tien_nuoc) ?> đ</td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="border-top">
                                <td class="pt-4 fs-5 fw-bold text-dark">TỔNG CỘNG:</td>
                                <td class="pt-4 fs-4 fw-bold text-danger text-end">
                                    <?= number_format($hd['tong_tien']) ?> <small>VNĐ</small>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="mt-5 p-4 rounded-4 bg-light">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="small text-muted d-block text-uppercase fw-bold">Trạng thái:</span>
                            <span class="fw-bold fs-5 <?= ($hd['trang_thai'] == 'Đã thanh toán') ? 'text-success' : 'text-danger' ?>">
                                <i class="bi <?= ($hd['trang_thai'] == 'Đã thanh toán') ? 'bi-patch-check-fill' : 'bi-exclamation-circle-fill' ?> me-1"></i>
                                <?= strtoupper($hd['trang_thai']) ?>
                            </span>
                        </div>

                        <?php if ($hd['trang_thai'] != 'Đã thanh toán'): ?>
                            <button id="btnShowQR" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                                <i class="bi bi-qr-code-scan me-2"></i>THANH TOÁN NGAY
                            </button>
                        <?php endif; ?>
                    </div>

                    <?php if ($hd['trang_thai'] != 'Đã thanh toán'): 
                        // Cấu hình mã QR thanh toán (VietQR/SePay)
                        $STK = "010501056868"; 
                        $NGAN_HANG = "MBBank"; 
                        $NOI_DUNG = "THANHTOAN KTX" . $hd['ma_hd']; 
                        $SO_TIEN = (int)$hd['tong_tien'];
                        $qr_url = "https://qr.sepay.vn/img?bank=$NGAN_HANG&acc=$STK&template=compact&amount=$SO_TIEN&des=$NOI_DUNG";
                    ?>
                        <div id="qrContainer" class="text-center py-4 border-top mt-4" style="display: none;">
                            <p class="text-muted small mb-3">Sử dụng ứng dụng Ngân hàng hoặc Ví điện tử để quét mã</p>
                            <div class="bg-white p-2 d-inline-block border rounded-3 shadow-sm mb-3">
                                <img src="<?= $qr_url ?>" alt="QR Thanh toán" class="img-fluid" style="max-width: 250px;">
                            </div>
                            <div class="alert alert-warning border-0 small mx-auto" style="max-width: 450px;">
                                <i class="bi bi-info-circle-fill"></i> Hệ thống sẽ tự động cập nhật trạng thái sau khi giao dịch thành công khoảng 1-2 phút.
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('btnShowQR')?.addEventListener('click', function() {
    this.style.display = 'none'; 
    const qrContainer = document.getElementById('qrContainer');
    qrContainer.style.display = 'block';
    qrContainer.scrollIntoView({ behavior: 'smooth' });
});
</script>

<style>
    .border-dashed { border-top: 2px dashed #dee2e6; }
    @media print {
        .btn, #header, #footer, .mb-3 { display: none !important; }
        .card { border: none !important; shadow: none !important; }
        body { background: white !important; }
    }
</style>

<?php include 'footer.php'; ?>