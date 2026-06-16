<?php  

$conn = mysqli_connect(
    "mysql-31d9a205-coffeeshop.b.aivencloud.com",
    "avnadmin",
    "AVNS_ZHlShQkpcNtIERPZYVt",
    "defaultdb",
    13324
);

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>