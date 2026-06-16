<?php
include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Orders</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">📦 Orders History</div>

<div class="container">

<?php
$result = mysqli_query($conn, "SELECT * FROM orders ORDER BY id DESC");

while ($row = mysqli_fetch_assoc($result)) {
    echo "
    <div class='card'>
        <h3>Order #{$row['id']}</h3>
        <p>Name: {$row['customer_name']}</p>
        <p>Phone: {$row['phone']}</p>
        <p>Address: {$row['address']}</p>
        <p>Total: {$row['total']}</p>
        <p>Date: {$row['order_date']}</p>
    </div>
    <hr>";
}
?>

</div>

</body>
</html>