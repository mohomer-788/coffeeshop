<?php
include 'db.php';

$id = $_GET['id'];

$result = mysqli_query($conn, "SELECT * FROM orders WHERE id=$id");
$order = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="header">🧾 Invoice</div>

<div class="container">

<div class="card">

<p>Order ID: <?php echo $order['id']; ?></p>
<p>Name: <?php echo $order['customer_name']; ?></p>
<p>Phone: <?php echo $order['phone']; ?></p>
<p>Address: <?php echo $order['address']; ?></p>
<p>Total: <?php echo $order['total']; ?></p>

<button onclick="window.print()" class="btn">Print</button>

</div>

</div>

</body>
</html>