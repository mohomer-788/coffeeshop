<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* ───────── CART ACTIONS ───────── */
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];
    if ($id > 0) {
        $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
    }
    header("Location: cart.php");
    exit();
}

if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id]--;
        if ($_SESSION['cart'][$id] <= 0) {
            unset($_SESSION['cart'][$id]);
        }
    }
    header("Location: cart.php");
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit();
}

/* ───────── FETCH ITEMS ───────── */
$cart_items = [];
$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    $id = (int)$id;
    $qty = (int)$qty;

    $stmt = mysqli_prepare($conn, "SELECT id, name, price FROM products WHERE id=?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if ($row) {
        $subtotal = $row['price'] * $qty;
        $total += $subtotal;

        $cart_items[] = [
            'id' => $row['id'],
            'name' => htmlspecialchars($row['name']),
            'price' => $row['price'],
            'qty' => $qty,
            'subtotal' => $subtotal
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CoffeeShop — Cart</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI';
    background:linear-gradient(135deg,#f5f2ee,#e7dcd2);
    color:#2c1a0e;
}

/* HEADER */
.header{
    position:sticky;
    top:0;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:16px 22px;
    background:linear-gradient(135deg,#2d1612,#6b3a2a);
    color:#fff;
}

.logo{font-weight:700}

.back{
    text-decoration:none;
    color:#6b3a2a;
    background:#fff3e6;
    padding:8px 14px;
    border-radius:30px;
}

/* TITLE */
.title{
    text-align:center;
    padding:25px;
    font-size:24px;
    font-weight:700;
}

/* CARD */
.card{
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:#fff;
    margin:10px auto;
    padding:14px;
    width:90%;
    max-width:750px;
    border-radius:14px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
}

/* INFO */
.info h3{margin:0;font-size:16px}
.price{color:#6b3a2a;font-weight:700;margin-top:4px}

/* QTY */
.qty{
    display:flex;
    align-items:center;
    gap:10px;
}

.btn{
    width:32px;
    height:32px;
    border-radius:50%;
    background:#f5ece6;
    display:flex;
    align-items:center;
    justify-content:center;
    text-decoration:none;
    color:#6b3a2a;
    font-weight:700;
}

/* DELETE */
.del{
    color:#c0392b;
    text-decoration:none;
    font-size:13px;
}

/* SUMMARY */
.summary{
    width:90%;
    max-width:750px;
    margin:20px auto;
    background:#fff;
    padding:20px;
    border-radius:14px;
}

.total{
    font-size:18px;
    font-weight:700;
    display:flex;
    justify-content:space-between;
    margin-top:10px;
}

.checkout{
    display:block;
    text-align:center;
    margin-top:15px;
    padding:14px;
    border-radius:30px;
    background:linear-gradient(135deg,#7b4b2a,#a0622e);
    color:#fff;
    text-decoration:none;
    font-weight:700;
}
</style>
</head>

<body>

<div class="header">
    <div class="logo">☕ CoffeeShop</div>
    <a class="back" href="products.php">Menu</a>
</div>

<div class="title">Your Cart 🛒</div>

<?php if (empty($cart_items)): ?>

<p style="text-align:center;">Cart is empty</p>

<?php else: ?>

<?php foreach ($cart_items as $item): ?>
<div class="card">

    <div class="info">
        <h3><?php echo $item['name']; ?></h3>
        <div class="price">$<?php echo number_format($item['price'],2); ?></div>
    </div>

    <div class="qty">
        <a class="btn" href="cart.php?remove=<?php echo $item['id']; ?>">−</a>
        <?php echo $item['qty']; ?>
        <a class="btn" href="cart.php?add=<?php echo $item['id']; ?>">+</a>
    </div>

    <a class="del" href="cart.php?delete=<?php echo $item['id']; ?>">Remove</a>

</div>
<?php endforeach; ?>

<div class="summary">

    <div class="total">
        <span>Total</span>
        <span>$<?php echo number_format($total,2); ?></span>
    </div>

    <a class="checkout" href="checkout.php">Proceed to Checkout</a>

</div>

<?php endif; ?>

</body>
</html>