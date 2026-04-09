<?php
include 'connect.php'; // Nhớ kiểm tra đúng tên file kết nối của ông
session_start();

// Nếu đã đăng nhập rồi thì cho vào index luôn
if (isset($_SESSION['ten_dang_nhap'])) {
    header("Location: index.php");
    exit();
}

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password']; // Ở đây tôi để pass thô, nếu ông dùng password_hash thì cần verify nhé

    if (empty($username) || empty($password)) {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {
        // Truy vấn kiểm tra tài khoản và vai trò từ bảng tai_khoan
        $sql = "SELECT * FROM tai_khoan WHERE ten_dang_nhap = '$username' AND mat_khau = '$password'";
        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);
            
            // Lưu thông tin vào Session
            $_SESSION['ten_dang_nhap'] = $user['ten_dang_nhap'];
            $_SESSION['vai_tro'] = $user['vai_tro'];

            // Chuyển hướng về trang chủ
            header("Location: index.php");
            exit();
        } else {
            $error = "Tên đăng nhập hoặc mật khẩu không đúng!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Hệ thống KTX</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body { background: #f4f7f6; height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { width: 100%; max-width: 400px; padding: 20px; border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-primary { background-color: #2c3e50; border: none; }
        .btn-primary:hover { background-color: #1a252f; }
    </small></style>
</head>
<body>

<div class="card login-card">
    <div class="card-body">
        <div class="text-center mb-4">
            <i class="bi bi-building-lock fs-1 text-primary"></i>
            <h3 class="fw-bold mt-2">ĐĂNG NHẬP</h3>
            <p class="text-muted small">Hệ thống Quản lý Ký túc xá</p>
        </div>

        <?php if ($error != ""): ?>
            <div class="alert alert-danger py-2 small text-center"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label small fw-bold">Tên đăng nhập</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                    <input type="text" name="username" class="form-control" placeholder="Nhập username..." required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-bold">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-key"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
                </div>
            </div>

            <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-bold">ĐĂNG NHẬP</button>
        </form>
    </div>
    <div class="card-footer bg-white border-0 text-center pb-3">
        <small class="text-muted">Chưa có tài khoản? Liên hệ Ban quản lý.</small>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>