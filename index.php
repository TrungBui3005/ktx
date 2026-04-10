<?php include 'header.php'; ?>

<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center justify-content-between bg-white p-4 rounded-4 shadow-sm border-start border-primary border-5">
                <div>
                    <h3 class="fw-bold mb-1">Xin chào, <?= $user_display ?>!</h3>
                    <p class="text-muted mb-0">Hệ thống đang hoạt động. Vai trò: <span class="badge bg-primary text-uppercase"><?= $role ?></span></p>
                </div>
                <div class="text-end d-none d-md-block">
                    <span class="fw-bold text-primary"><i class="bi bi-calendar3 me-2"></i><?= date('d/m/Y') ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <?php if($role == 'admin'): 
            // 1. Lấy tổng doanh thu
            $revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(tong_tien) as total FROM hoa_don WHERE trang_thai = 'Đã thanh toán'"))['total'];   
            // 2. Đếm số hóa đơn chưa thanh toán
            $pending_bill = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM hoa_don WHERE trang_thai = 'Chưa thanh toán'"))['total'];
            // 3. Đếm số thiết bị đang hỏng
            $sql_hong = "SELECT COUNT(*) as total FROM yeu_cau_sua_chua WHERE trang_thai != 'Đã hoàn thành'";
            $res_hong = mysqli_query($conn, $sql_hong);
            $data_hong = mysqli_fetch_assoc($res_hong);
            $so_luong_hong = $data_hong['total'];
            // 4. Đếm số đơn đăng ký nội trú mới
            $pending_req_query = mysqli_query($conn, "SELECT COUNT(*) as total FROM dang_ky_noi_tru WHERE trang_thai = 'Chờ duyệt'");
            $pending_req = mysqli_fetch_assoc($pending_req_query)['total'];

            // --- XỬ LÝ DỮ LIỆU BIỂU ĐỒ DOANH THU ---
            $revenue_months = array_fill(1, 12, 0);
            $sql_chart = "SELECT MONTH(ngay_tao) as thang, SUM(tong_tien) as doanh_thu 
                          FROM hoa_don 
                          WHERE trang_thai = 'Đã thanh toán' AND YEAR(ngay_tao) = YEAR(CURDATE())
                          GROUP BY MONTH(ngay_tao)";
            $res_chart = mysqli_query($conn, $sql_chart);
            while($row_c = mysqli_fetch_assoc($res_chart)) {
                $revenue_months[(int)$row_c['thang']] = (float)$row_c['doanh_thu'];
            }
            $chart_values = json_encode(array_values($revenue_months));
        ?>
        
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 text-center">
                <div class="text-primary mb-2"><i class="bi bi-cash-stack fs-3"></i></div>
                <small class="text-muted fw-bold d-block">DOANH THU</small>
                <h4 class="fw-bold mb-0"><?= number_format($revenue ?? 0) ?>đ</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 text-center">
                <div class="text-danger mb-2"><i class="bi bi-exclamation-octagon fs-3"></i></div>
                <small class="text-muted fw-bold d-block">HÓA ĐƠN TREO</small>
                <h4 class="fw-bold mb-0"><?= $pending_bill ?> đơn</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 text-center">
                <div class="text-warning mb-2"><i class="bi bi-tools fs-3"></i></div>
                <small class="text-muted fw-bold d-block">THIẾT BỊ HỎNG</small>
                <h4 class="fw-bold mb-0"><?= $so_luong_hong ?> mục</h4>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-custom border-0 shadow-sm p-3 text-center">
                <div class="text-success mb-2"><i class="bi bi-person-plus fs-3"></i></div>
                <small class="text-muted fw-bold d-block">ĐƠN ĐĂNG KÝ</small>
                <h4 class="fw-bold mb-0"><?= $pending_req ?> đơn</h4>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card card-custom shadow-sm border-0 p-4 mb-4">
                <h5 class="fw-bold mb-4">Thống kê doanh thu năm <?= date('Y') ?></h5>
                <canvas id="revenueChart" style="max-height: 280px;"></canvas>
            </div>

            <div class="card card-custom shadow-sm border-0 p-4">
                <h5 class="fw-bold mb-4">Danh sách sinh viên mới</h5>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead class="table-light">
                            <tr><th>Mã SV</th><th>Họ tên</th><th>Phòng</th></tr>
                        </thead>
                        <tbody>
                            <?php 
                            $new_sv = mysqli_query($conn, "SELECT * FROM sinh_vien ORDER BY ma_sv DESC LIMIT 5");
                            while($s = mysqli_fetch_assoc($new_sv)): ?>
                            <tr>
                                <td><?= $s['ma_sv'] ?></td>
                                <td><?= $s['ho_ten'] ?></td>
                                <td><span class="badge bg-light text-dark"><?= $s['ma_phong'] ?></span></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-custom shadow-sm border-0 p-4 bg-dark text-white">
                <h5 class="fw-bold mb-3">Thao tác nhanh</h5>
                <div class="d-grid gap-2">
                    <a href="invoice/tao_hoa_don.php" class="btn btn-primary text-start"><i class="bi bi-plus-lg me-2"></i>Tạo hóa đơn tháng</a>
                    <a href="room/phong.php" class="btn btn-outline-light text-start"><i class="bi bi-door-open me-2"></i>Kiểm tra phòng trống</a>
                    <a href="report/bao_cao_thong_ke.php" class="btn btn-outline-light text-start"><i class="bi bi-file-earmark-pdf me-2"></i>Xuất báo cáo PDF</a>
                </div>
            </div>
        </div>

        <?php elseif($role == 'nhan_vien'): ?>
            <?php elseif($role == 'sinh_vien'): ?>
            <?php endif; ?>
    </div>
</div>

<style>
.card-custom { border-radius: 15px; transition: 0.3s; }
.card-custom:hover { transform: translateY(-5px); }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Vẽ biểu đồ nếu là Admin
if (document.getElementById('revenueChart')) {
    const ctx = document.getElementById('revenueChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['T1', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'T8', 'T9', 'T10', 'T11', 'T12'],
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: <?= $chart_values ?? '[]' ?>,
                backgroundColor: '#0d6efd',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { 
                    beginAtZero: true,
                    ticks: { callback: (v) => v.toLocaleString() + 'đ' }
                }
            }
        }
    });
}
</script>

<?php include 'footer.php'; ?>