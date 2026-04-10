<?php 
include '../header.php'; 
include '../connect.php'; 

if (isset($_POST['btn_them'])) {
    $ma_sv = mysqli_real_escape_string($conn, $_POST['ma_sv']);
    $ho_ten = mysqli_real_escape_string($conn, $_POST['ho_ten']);
    $ngay_sinh = $_POST['ngay_sinh']; 
    $lop = mysqli_real_escape_string($conn, $_POST['lop']);
    $gioi_tinh = $_POST['gioi_tinh'];
    $sdt = $_POST['so_dien_thoai'];
    $que_quan = mysqli_real_escape_string($conn, $_POST['que_quan']);
    $doi_tuong = mysqli_real_escape_string($conn, $_POST['doi_tuong_uu_tien']);
    
    $password_default = password_hash('123456', PASSWORD_DEFAULT);

    mysqli_begin_transaction($conn);

    try {
        // 1. Chèn vào bảng tai_khoan
        $sql_tk = "INSERT INTO tai_khoan (ten_dang_nhap, mat_khau, vai_tro) 
                   VALUES ('$ma_sv', '$password_default', 'sinh_vien')";
        mysqli_query($conn, $sql_tk);

        // 2. Chèn vào bảng sinh_vien (Lưu ý: ten_dang_nhap trùng với ma_sv để dễ quản lý)
        $sql_sv = "INSERT INTO sinh_vien (ma_sv, ho_ten, ngay_sinh, gioi_tinh, so_dien_thoai, que_quan, lop, doi_tuong_uu_tien, ten_dang_nhap) 
         VALUES ('$ma_sv', '$ho_ten', '$ngay_sinh', '$gioi_tinh', '$sdt', '$que_quan', '$lop', '$doi_tuong', '$ma_sv')";
        
        if(mysqli_query($conn, $sql_sv)) {
            mysqli_commit($conn);
            echo "<script>alert('Thêm sinh viên thành công!'); window.location.href='sinh_vien.php';</script>";
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "<script>alert('Lỗi: " . $e->getMessage() . "');</script>";
    }
}
?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 text-center">
                    <h5 class="fw-bold mb-0 text-primary">THÊM SINH VIÊN MỚI</h5>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Mã Sinh Viên</label>
                                <input type="text" name="ma_sv" class="form-control" placeholder="Ví dụ: B22DCCN001" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Họ và Tên</label>
                                <input type="text" name="ho_ten" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ngày sinh</label>
                                <input type="date" name="ngay_sinh" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Giới tính</label>
                                <select name="gioi_tinh" class="form-select">
                                    <option value="Nam">Nam</option>
                                    <option value="Nữ">Nữ</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Lớp</label>
                                <input type="text" name="lop" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Số điện thoại</label>
                                <input type="text" name="so_dien_thoai" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Quê quán</label>
                                <input type="text" name="que_quan" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Đối tượng ưu tiên</label>
                                <select name="doi_tuong_uu_tien" class="form-select">
                                    <option value="">Không</option>
                                    <option value="Con thương binh/Liệt sĩ">Con thương binh/Liệt sĩ</option>
                                    <option value="Vùng sâu vùng xa">Vùng sâu vùng xa</option>
                                    <option value="Hộ nghèo/Cận nghèo">Hộ nghèo/Cận nghèo</option>
                                </select>
                            </div>
                        </div>
                        <hr class="my-4">
                        <div class="d-flex gap-2">
                            <button type="submit" name="btn_them" class="btn btn-primary px-4 rounded-pill w-25">Lưu thông tin</button>
                            <a href="sinh_vien.php" class="btn btn-light px-4 rounded-pill">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>