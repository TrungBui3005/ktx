<?php
$host = "localhost";
$username = "root"; // Mặc định của XAMPP
$password = "";     // Mặc định của XAMPP là trống
$dbname = "quan_ly_ktx";

$conn = mysqli_connect($host, $username, $password, $dbname);

// Kiểm tra kết nối
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

// Thiết lập font tiếng Việt
mysqli_set_charset($conn, "utf8mb4");
?>