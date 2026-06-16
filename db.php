<?php  

$host = "mysql-31d9a205-coffeeshop.b.aivencloud.com";
$user = "avnadmin";
$pass = "AVNS_ZHlShQkpcNtIERPZYVt";
$db   = "defaultdb";
$port = 13324;

$conn = mysqli_init();

/* مهم جداً لـ Aiven SSL */
mysqli_options($conn, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, false);

if (!mysqli_real_connect($conn, $host, $user, $pass, $db, $port)) {
    die("Database connection failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, 'utf8mb4');
?>