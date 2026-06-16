<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: cart.php");
    exit();
}

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

/* INPUTS */
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$address = trim($_POST['address'] ?? '');

if ($name == '' || $phone == '' || $address == '') {
    die("Please fill all fields");
}

/* CUSTOMER */
$stmt = mysqli_prepare($conn, "SELECT id FROM customers WHERE phone = ?");
mysqli_stmt_bind_param($stmt, "s", $phone);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$customer = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if ($customer) {
    $customer_id = $customer['id'];
} else {
    $stmt = mysqli_prepare($conn, "INSERT INTO customers (name, phone, address) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $name, $phone, $address);
    mysqli_stmt_execute($stmt);
    $customer_id = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);
}

/* TOTAL */
$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    $id = (int)$id;

    $res = mysqli_query($conn, "SELECT price FROM products WHERE id=$id");
    $row = mysqli_fetch_assoc($res);

    if ($row) {
        $total += $row['price'] * $qty;
    }
}

/* ORDER */
$stmt = mysqli_prepare($conn, "
INSERT INTO orders (customer_id, total, order_date)
VALUES (?, ?, NOW())
");
mysqli_stmt_bind_param($stmt, "id", $customer_id, $total);
mysqli_stmt_execute($stmt);
$order_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

/* CLEAR CART */
$_SESSION['cart'] = [];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Confirmed</title>
    <link rel="stylesheet" href="style.css">

    <style>
        body {
            font-family: Arial;
            background: linear-gradient(135deg, #f5f2ee, #e7dcd2);
            margin: 0;
        }

        .header {
            background: #3b1f1a;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 20px;
        }

        .container {
            width: 85%;
            max-width: 600px;
            margin: auto;
            padding: 30px 0;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from {opacity: 0; transform: translateY(20px);}
            to {opacity: 1; transform: translateY(0);}
        }

        .check {
            font-size: 60px;
            margin-bottom: 10px;
        }

        h2 {
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .info {
            text-align: left;
            margin-top: 20px;
            line-height: 1.6;
        }

        .total {
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
            color: #3b1f1a;
        }

        .btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 18px;
            background: #7b4b2a;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        .btn:hover {
            background: #5a351e;
        }
    </style>
</head>

<body>

<div class="header">☕ CoffeeShop</div>

<div class="container">

<div class="card">

    <div class="check">✅</div>

    <h2>Order Confirmed!</h2>
    <p>Thank you <b><?php echo htmlspecialchars($name); ?></b></p>

    <div class="info">
        <p><b>Phone:</b> <?php echo htmlspecialchars($phone); ?></p>
        <p><b>Address:</b> <?php echo htmlspecialchars($address); ?></p>
        <p><b>Order ID:</b> #<?php echo $order_id; ?></p>
    </div>

    <div class="total">
        Total: $<?php echo number_format($total, 2); ?>
    </div>

    <a class="btn" href="products.php">Continue Shopping</a>

</div>

</div>

</body>
</html>