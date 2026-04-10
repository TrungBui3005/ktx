<?php
// Nhớ include file kết nối database của bạn, tùy đường dẫn bạn đặt
include '../connect.php'; 

if (isset($_POST['btn_cap_nhat'])) {
    $ma_yc = $_POST['ma_yc'];
    $trang_thai = $_POST['trang_thai'];
    $ma_nv = $_POST['ma_nv'];
    $phan_hoi = mysqli_real_escape_string($conn, $_POST['phan_hoi_nv']);

    $sql = "UPDATE yeu_cau_sua_chua 
            SET trang_thai = '$trang_thai', 
                ma_nv = '$ma_nv', 
                phan_hoi_nv = '$phan_hoi' 
            WHERE ma_yc = $ma_yc";
    
    if (mysqli_query($conn, $sql)) {
        header("Location: quan_ly_sua_chua.php?status=success");
    } else {
        header("Location: quan_ly_sua_chua.php?status=error");
    }
    exit();
}
?>