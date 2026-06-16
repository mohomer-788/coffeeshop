<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* ── Add to cart ── */
if (isset($_GET['add'])) {
    $id = (int)$_GET['add'];

    if ($id > 0) {
        $check = mysqli_prepare($conn, "SELECT id FROM products WHERE id = ?");
        mysqli_stmt_bind_param($check, "i", $id);
        mysqli_stmt_execute($check);
        mysqli_stmt_store_result($check);

        if (mysqli_stmt_num_rows($check) > 0) {
            $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + 1;
        }

        mysqli_stmt_close($check);
    }

    header("Location: products.php");
    exit();
}

/* ── Get products ── */
$result = mysqli_query($conn, "SELECT * FROM products ORDER BY category, name");

/* ── Smart image function ── */
function getProductImage($name)
{
    $name = strtolower($name);

    if (str_contains($name, 'espresso')) {
        return "https://images.unsplash.com/photo-1509042239860-f550ce710b93";
    }
    if (str_contains($name, 'cappuccino')) {
        return "https://images.unsplash.com/photo-1511920170033-f8396924c348";
    }
    if (str_contains($name, 'latte')) {
        return "https://images.unsplash.com/photo-1551024601-bec78aea704b";
    }
    if (str_contains($name, 'iced')) {
        return "https://images.unsplash.com/photo-1504754524776-8f4f37790ca0";
    }
    if (str_contains($name, 'croissant')) {
        return "https://images.unsplash.com/photo-1509440159596-0249088772ff";
    }
    if (str_contains($name, 'muffin')) {
        return "https://images.unsplash.com/photo-1517686469429-8bdb88b9f907";
    }

    return "https://images.unsplash.com/photo-1509042239860-f550ce710b93";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CoffeeShop — Menu</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background: radial-gradient(circle at top,#f6f2ee,#e7d7c9);
    color:#2c1a0e;
}

/* HEADER */
.header{
    position:sticky;
    top:0;
    z-index:100;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:16px 24px;
    background:linear-gradient(135deg,#2b1611,#6b3a2a);
    color:white;
    box-shadow:0 10px 30px rgba(0,0,0,0.25);
}

.logo{
    font-size:20px;
    font-weight:800;
}

.cart{
    background:rgba(255,255,255,0.12);
    padding:8px 16px;
    border-radius:999px;
    color:white;
    text-decoration:none;
    display:flex;
    gap:8px;
    align-items:center;
}

.badge{
    background:#ff4d4d;
    width:20px;height:20px;
    display:flex;
    justify-content:center;
    align-items:center;
    border-radius:50%;
    font-size:12px;
}

/* TITLE */
.title{
    text-align:center;
    padding:40px 10px 10px;
}
.title h1{margin:0;font-size:32px}
.title p{color:#7a5c4e}

/* CONTAINER */
.container{
    width:90%;
    max-width:900px;
    margin:auto;
    padding:20px 0 70px;
}

/* CATEGORY */
.cat{
    margin:30px 0 12px;
    font-size:11px;
    letter-spacing:3px;
    font-weight:800;
    color:#a06a45;
    border-left:4px solid #c08a5b;
    padding-left:10px;
}

/* CARD */
.card{
    display:flex;
    align-items:center;
    gap:16px;
    background:rgba(255,255,255,0.9);
    padding:14px;
    border-radius:18px;
    margin-bottom:14px;
    box-shadow:0 6px 18px rgba(0,0,0,0.06);
    transition:.25s;
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 18px 40px rgba(0,0,0,0.12);
}

.card img{
    width:90px;
    height:90px;
    border-radius:16px;
    object-fit:cover;
}

.info{flex:1}

.name{
    font-size:17px;
    font-weight:700;
}

.price{
    font-size:15px;
    font-weight:800;
    color:#5a341e;
}

.btn{
    background:linear-gradient(135deg,#7b4b2a,#a0622e);
    color:white;
    padding:10px 18px;
    border-radius:999px;
    text-decoration:none;
    font-weight:700;
    transition:.2s;
}

.btn:hover{
    transform:scale(1.07);
}
</style>

</head>
<body>

<div class="header">
    <div class="logo">☕ CoffeeShop</div>
    <a class="cart" href="cart.php">
        🛒 Cart
        <span class="badge"><?php echo array_sum($_SESSION['cart']); ?></span>
    </a>
</div>

<div class="title">
    <h1>Our Menu</h1>
    <p>Premium coffee crafted for perfection</p>
</div>

<div class="container">

<?php
$current = '';

while ($row = mysqli_fetch_assoc($result)) {

    $img = getProductImage($row['name']);

    if ($row['category'] !== $current) {
        $current = $row['category'];
        echo "<div class='cat'>{$current}</div>";
    }

    echo "
    <div class='card'>
        <img src='{$img}'>
        <div class='info'>
            <div class='name'>{$row['name']}</div>
            <div class='price'>$ {$row['price']}</div>
        </div>
        <a class='btn' href='products.php?add={$row['id']}'>Add</a>
    </div>";
}
?>

</div>

</body>
</html>