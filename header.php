<?php
include 'connect.php'; // Đảm bảo tên file kết nối đúng
session_start();

// Kiểm tra login
if (!isset($_SESSION['ten_dang_nhap'])) {
    $role = 'guest';
} else {
    $role = $_SESSION['vai_tro'];
    $user_display = $_SESSION['ten_dang_nhap'];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý KTX - <?= ucfirst($role) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        :root { --primary-color: #2c3e50; }
        body { background-color: #f4f7f6; }
        .navbar { background-color: var(--primary-color) !important; }
        .main-content { min-height: 80vh; padding: 30px 0; }
        .card { border: none; border-radius: 12px; transition: 0.3s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php"><i class="bi bi-building"></i> KTX MANAGER</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="index.php">Trang chủ</a></li>

                <?php if($role == 'admin'): ?>
                    <li class="nav-item"><a class="nav-link" href="phong.php">Quản lý Phòng</a></li>
                    <li class="nav-item"><a class="nav-link" href="sinh_vien.php">Sinh viên</a></li>
                    <li class="nav-item"><a class="nav-link" href="hoa_don.php">Hóa đơn</a></li>
                <?php elseif($role == 'sinh_vien'): ?>
                    <li class="nav-item"><a class="nav-link" href="dang_ky_noi_tru.php">Đăng ký ở</a></li>
                    <li class="nav-item"><a class="nav-link" href="phong_cua_toi.php">Phòng của tôi</a></li>
                    <li class="nav-item"><a class="nav-link" href="sua_chua.php">Sửa chữa</a></li>
                <?php endif; ?>

                <?php if($role != 'guest'): ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle btn btn-outline-light btn-sm ms-2" href="#" data-bs-toggle="dropdown">
                            <?= $user_display ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Hồ sơ</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Đăng xuất</a></li>
                        </ul>
                    </li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="login.php">Đăng nhập</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<div class="container main-content">