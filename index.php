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
            // --- DASHBOARD ADMIN ---
            $total_sv = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM sinh_vien"))['total'];
            $empty_beds = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(so_giuong_trong) as total FROM phong"))['total'];
            $pending_req = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM dang_ky_noi_tru WHERE trang_thai = 'Chờ duyệt'"))['total'];
        ?>
            <div class="col-md-4">
                <div class="card card-custom h-100 shadow-sm border-0 p-4">
                    <div class="icon-box icon-blue mb-3"><i class="bi bi-people"></i></div>
                    <h6 class="text-muted fw-bold small">TỔNG SINH VIÊN</h6>
                    <h2 class="fw-bold"><?= $total_sv ?></h2>
                    <a href="sinh_vien.php" class="text-decoration-none small fw-bold mt-2 d-block">Quản lý danh sách <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom h-100 shadow-sm border-0 p-4">
                    <div class="icon-box icon-green mb-3"><i class="bi bi-door-open"></i></div>
                    <h6 class="text-muted fw-bold small">CHỖ TRỐNG HIỆN TẠI</h6>
                    <h2 class="fw-bold text-success"><?= $empty_beds ?? 0 ?></h2>
                    <a href="phong.php" class="text-decoration-none text-success small fw-bold mt-2 d-block">Sơ đồ phòng <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card card-custom h-100 shadow-sm border-0 p-4">
                    <div class="icon-box icon-orange mb-3"><i class="bi bi-clipboard-check"></i></div>
                    <h6 class="text-muted fw-bold small">ĐƠN ĐĂNG KÝ MỚI</h6>
                    <h2 class="fw-bold text-danger"><?= $pending_req ?></h2>
                    <a href="approve_requests.php" class="text-decoration-none text-danger small fw-bold mt-2 d-block">Phê duyệt ngay <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>

        <?php elseif($role == 'nhan_vien'): 
            // --- DASHBOARD NHÂN VIÊN ---
            $latest_tasks = mysqli_query($conn, "SELECT yc.*, p.ten_phong FROM yeu_cau_sua_chua yc JOIN phong p ON yc.ma_phong = p.ma_phong WHERE yc.trang_thai = 'Chờ xử lý' ORDER BY yc.ngay_gui DESC LIMIT 3");
        ?>
            <div class="col-lg-8">
                <div class="card card-custom shadow-sm border-0 p-4">
                    <h5 class="fw-bold mb-4"><i class="bi bi-tools me-2 text-danger"></i>Nhiệm vụ cần xử lý</h5>
                    <div class="list-group list-group-flush">
                        <?php while($task = mysqli_fetch_assoc($latest_tasks)): ?>
                            <div class="list-group-item px-0 py-3 d-flex justify-content-between">
                                <div>
                                    <h6 class="fw-bold mb-1">Phòng: <?= $task['ten_phong'] ?></h6>
                                    <p class="text-muted small mb-0"><?= $task['noi_dung_hong'] ?></p>
                                </div>
                                <a href="xu_ly_sua_chua.php?id=<?= $task['ma_yc'] ?>" class="btn btn-sm btn-primary rounded-pill align-self-center">Tiếp nhận</a>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-custom shadow-sm border-0 bg-dark text-white p-4 mb-4">
                    <h5 class="fw-bold">Điện & Nước</h5>
                    <p class="small opacity-75">Ghi chỉ số tiêu thụ hàng tháng.</p>
                    <a href="ghi_dien_nuoc.php" class="btn btn-warning w-100 fw-bold rounded-pill">BẮT ĐẦU GHI SỐ</a>
                </div>
                <div class="card card-custom shadow-sm border-0 p-4">
                    <h6 class="text-muted fw-bold mb-3 small text-uppercase">Công cụ nhanh</h6>
                    <div class="d-grid gap-2">
                        <a href="danh_sach_phong.php" class="btn btn-light text-start"><i class="bi bi-search me-2"></i>Tra cứu phòng</a>
                        <a href="lich_truc.php" class="btn btn-light text-start"><i class="bi bi-calendar-check me-2"></i>Lịch trực</a>
                    </div>
                </div>
            </div>

        <?php elseif($role == 'sinh_vien'): 
            // --- DASHBOARD SINH VIÊN ---
            $sql = "SELECT sv.*, p.ten_phong FROM sinh_vien sv LEFT JOIN phong p ON sv.ma_phong = p.ma_phong WHERE sv.ten_dang_nhap = '$user_display'";
            $data = mysqli_fetch_assoc(mysqli_query($conn, $sql));
        ?>
            <div class="col-lg-8">
                <div class="card card-custom shadow-sm border-0 p-4">
                    <h5 class="fw-bold mb-4">Hồ sơ cá nhân</h5>
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center border-end">
                            <i class="bi bi-person-circle text-primary display-4"></i>
                            <h6 class="fw-bold mt-2"><?= $data['ho_ten'] ?></h6>
                        </div>
                        <div class="col-md-8 ps-md-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="small text-muted d-block">Phòng ở</label>
                                    <span class="fw-bold"><?= $data['ten_phong'] ?? 'Chưa xếp' ?></span>
                                </div>
                                <div class="col-6">
                                    <label class="small text-muted d-block">Trạng thái</label>
                                    <span class="badge bg-success">Nội trú</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card card-custom shadow-sm border-0 p-4 h-100">
                    <h5 class="fw-bold mb-3">Lối tắt</h5>
                    <div class="d-grid gap-2">
                        <a href="hoa_don_chi_tiet.php" class="btn btn-outline-primary text-start px-3 py-2"><i class="bi bi-cash-coin me-2"></i>Thanh toán hóa đơn</a>
                        <a href="sua_chua.php" class="btn btn-outline-danger text-start px-3 py-2"><i class="bi bi-wrench me-2"></i>Báo sửa chữa</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.card-custom { border-radius: 15px; transition: 0.3s; }
.card-custom:hover { transform: translateY(-5px); }
.icon-box { width: 45px; height: 45px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
.icon-blue { background: #e7f1ff; color: #0d6efd; }
.icon-green { background: #e8f5e9; color: #2e7d32; }
.icon-orange { background: #fff3e0; color: #ef6c00; }
</style>

<?php include 'footer.php'; ?>