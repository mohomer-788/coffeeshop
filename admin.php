<?php
include 'db.php';

/* ─────────────────────────────
   ADD PRODUCT
──────────────────────────── */
if (isset($_POST['add_product'])) {

    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category'];

    mysqli_query($conn,
        "INSERT INTO products (name, price, category)
         VALUES ('$name', '$price', '$category')"
    );

    header("Location: admin.php");
    exit();
}

/* ─────────────────────────────
   DELETE PRODUCT
──────────────────────────── */
if (isset($_GET['delete'])) {

    $id = (int)$_GET['delete'];

    mysqli_query($conn, "DELETE FROM products WHERE id=$id");

    header("Location: admin.php");
    exit();
}

/* ─────────────────────────────
   UPDATE PRICE
──────────────────────────── */
if (isset($_POST['update_price'])) {

    $id = (int)$_POST['id'];
    $price = $_POST['price'];

    mysqli_query($conn,
        "UPDATE products SET price='$price' WHERE id=$id"
    );

    header("Location: admin.php");
    exit();
}

/* ─────────────────────────────
   DISCOUNT BY NAME
──────────────────────────── */
if (isset($_POST['discount'])) {

    $name = $_POST['name'];
    $percent = (float)$_POST['percent'];

    mysqli_query($conn,
        "UPDATE products
         SET price = price - (price * $percent / 100)
         WHERE name = '$name'"
    );

    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>CoffeeShop Admin</title>

<style>
body{
    margin:0;
    font-family:'Segoe UI';
    background:#f5f2ee;
    color:#2c1a0e;
}

.header{
    background:linear-gradient(135deg,#2b1611,#6b3a2a);
    color:white;
    padding:20px;
    text-align:center;
    font-size:22px;
    font-weight:800;
}

.container{
    width:92%;
    max-width:1100px;
    margin:25px auto;
}

.box{
    background:white;
    padding:18px;
    border-radius:14px;
    box-shadow:0 5px 15px rgba(0,0,0,0.08);
    margin-bottom:20px;
}

input{
    padding:10px;
    margin:6px 0;
    width:100%;
    border:1px solid #ddd;
    border-radius:8px;
}

button{
    padding:10px 14px;
    background:#6b3a2a;
    color:white;
    border:none;
    border-radius:8px;
    cursor:pointer;
    font-weight:600;
}

button:hover{
    background:#4b2e2a;
}

table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#6b3a2a;
    color:white;
    padding:10px;
}

td{
    padding:10px;
    border-bottom:1px solid #eee;
}

.action a{
    color:red;
    text-decoration:none;
    font-weight:700;
}
</style>

</head>

<body>

<div class="header">☕ CoffeeShop Admin Panel (Pro)</div>

<div class="container">

<!-- ADD PRODUCT -->
<div class="box">
<h3>Add Product</h3>

<form method="POST">
    <input type="text" name="name" placeholder="Product Name" required>
    <input type="text" name="price" placeholder="Price" required>
    <input type="text" name="category" placeholder="Category" required>
    <button name="add_product">Add</button>
</form>
</div>

<!-- DISCOUNT -->
<div class="box">
<h3>Discount by Name</h3>

<form method="POST">
    <input type="text" name="name" placeholder="Product Name (exact)" required>
    <input type="number" name="percent" placeholder="Discount %" required>
    <button name="discount">Apply Discount</button>
</form>
</div>

<!-- PRODUCTS -->
<div class="box">
<h3>Products</h3>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Price</th>
    <th>Category</th>
    <th>Action</th>
</tr>

<?php
$result = mysqli_query($conn,"SELECT * FROM products");

while($row = mysqli_fetch_assoc($result)){
    echo "
    <tr>
        <td>{$row['id']}</td>
        <td>{$row['name']}</td>
        <td>{$row['price']}</td>
        <td>{$row['category']}</td>
        <td class='action'>
            <a href='admin.php?delete={$row['id']}'>Delete</a>
        </td>
    </tr>";
}
?>
</table>
</div>

<!-- ORDERS -->
<div class="box">
<h3>Orders Management</h3>

<table>
<tr>
    <th>Order ID</th>
    <th>Customer Name</th>
    <th>Phone</th>
    <th>Address</th>
    <th>Total</th>
    <th>Order Date</th>
</tr>

<?php

$order_result = mysqli_query($conn,"
SELECT
orders.id,
customers.name,
customers.phone,
customers.address,
orders.total,
orders.order_date
FROM orders
LEFT JOIN customers
ON orders.customer_id = customers.id
ORDER BY orders.id DESC
");

while($order = mysqli_fetch_assoc($order_result)){
    echo "
    <tr>
        <td>{$order['id']}</td>
        <td>{$order['name']}</td>
        <td>{$order['phone']}</td>
        <td>{$order['address']}</td>
        <td>{$order['total']}</td>
        <td>{$order['order_date']}</td>
    </tr>";
}
?>
</table>
</div>

<!-- UPDATE PRICE -->
<div class="box">
<h3>Update Price</h3>

<form method="POST">
    <input type="number" name="id" placeholder="Product ID" required>
    <input type="text" name="price" placeholder="New Price" required>
    <button name="update_price">Update</button>
</form>
</div>

</div>

</body>
</html>