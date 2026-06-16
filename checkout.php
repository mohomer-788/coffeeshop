<?php
session_start();
include 'db.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php");
    exit();
}

// ── Calculate order summary for display ──
$cart_items = [];
$total = 0;

foreach ($_SESSION['cart'] as $id => $qty) {
    $id = (int)$id;
    $stmt = mysqli_prepare($conn, "SELECT id, name, price FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    if ($row) {
        $subtotal = $row['price'] * $qty;
        $total += $subtotal;
        $cart_items[] = [
            'name'     => htmlspecialchars($row['name']),
            'price'    => (float)$row['price'],
            'qty'      => (int)$qty,
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
    <title>CoffeeShop — Checkout</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: #f5f2ee;
            color: #2c1a0e;
        }

        /* ── Header ── */
        .header {
            background: linear-gradient(135deg, #3b1f1a, #6b3a2a);
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.25);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .header-logo {
            font-size: 22px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .back-btn {
            display: flex;
            align-items: center;
            gap: 6px;
            background: #fff2e6;
            color: #6b3a2a;
            padding: 9px 18px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .back-btn:hover {
            background: #ffe0c2;
            transform: translateY(-1px);
        }

        /* ── Layout ── */
        .container {
            width: 90%;
            max-width: 900px;
            margin: 40px auto 60px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 28px;
        }

        @media (max-width: 650px) {
            .container {
                grid-template-columns: 1fr;
            }
        }

        .page-title {
            font-size: 22px;
            font-weight: 700;
            color: #3b1f1a;
            margin-bottom: 20px;
        }

        /* ── Form Panel ── */
        .form-panel {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 28px 26px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #7a5c4e;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin-bottom: 7px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e8ddd5;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            color: #2c1a0e;
            background: #fdfaf8;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
            outline: none;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #a0622e;
            box-shadow: 0 0 0 3px rgba(160,98,46,0.12);
            background: white;
        }

        .form-group input.error,
        .form-group textarea.error {
            border-color: #c0392b;
        }

        .form-group textarea {
            height: 110px;
            resize: vertical;
        }

        .error-msg {
            color: #c0392b;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #7b4b2a, #a0622e);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: 0.5px;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(107,58,42,0.3);
            margin-top: 6px;
        }

        .submit-btn:hover {
            background: linear-gradient(135deg, #5a351e, #7b4b2a);
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(107,58,42,0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        /* ── Summary Panel ── */
        .summary-panel {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            padding: 28px 26px;
            align-self: start;
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f0e8e2;
            font-size: 14px;
            color: #5a3e35;
        }

        .summary-item:last-of-type {
            border-bottom: none;
        }

        .summary-item .item-qty {
            font-size: 12px;
            color: #9b6a4e;
            background: #f5ece6;
            padding: 2px 7px;
            border-radius: 20px;
            margin-left: 6px;
        }

        .summary-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 0 0;
            font-size: 19px;
            font-weight: 700;
            color: #3b1f1a;
            margin-top: 8px;
            border-top: 2px solid #e8ddd5;
        }

        .summary-note {
            margin-top: 16px;
            font-size: 12px;
            color: #9b6a4e;
            text-align: center;
            line-height: 1.6;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="header-logo">☕ CoffeeShop</div>
    <a class="back-btn" href="cart.php">← Back to Cart</a>
</div>

<!-- Layout -->
<div class="container">

    <!-- Left: Form -->
    <div class="form-panel">
        <div class="page-title">🧾 Your Details</div>

        <form method="POST" action="confirm.php" id="checkoutForm" novalidate>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    placeholder="e.g. Ahmed Al-Rashid"
                    maxlength="100"
                    required
                >
                <div class="error-msg" id="nameError">Please enter your full name.</div>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input
                    type="tel"
                    id="phone"
                    name="phone"
                    placeholder="e.g. 0712345678"
                    maxlength="20"
                    required
                >
                <div class="error-msg" id="phoneError">Please enter a valid phone number.</div>
            </div>

            <div class="form-group">
                <label for="address">Delivery Address</label>
                <textarea
                    id="address"
                    name="address"
                    placeholder="Street, building, area..."
                    maxlength="300"
                    required
                ></textarea>
                <div class="error-msg" id="addressError">Please enter your delivery address.</div>
            </div>

            <button type="submit" class="submit-btn">✅ Place Order</button>

        </form>
    </div>

    <!-- Right: Order Summary -->
    <div class="summary-panel">
        <div class="page-title">📋 Order Summary</div>

        <?php foreach ($cart_items as $item): ?>
        <div class="summary-item">
            <span>
                <?php echo $item['name']; ?>
                <span class="item-qty">×<?php echo $item['qty']; ?></span>
            </span>
            <span>$<?php echo number_format($item['subtotal'], 2); ?></span>
        </div>
        <?php endforeach; ?>

        <div class="summary-total">
            <span>Total</span>
            <span>$<?php echo number_format($total, 2); ?></span>
        </div>

        <div class="summary-note">
            🚚 Your order will be delivered to your address.<br>
            Payment is cash on delivery.
        </div>
    </div>

</div>

<!-- Client-side validation -->
<script>
document.getElementById('checkoutForm').addEventListener('submit', function(e) {
    let valid = true;

    const name    = document.getElementById('name');
    const phone   = document.getElementById('phone');
    const address = document.getElementById('address');

    // Reset
    [name, phone, address].forEach(el => {
        el.classList.remove('error');
        el.nextElementSibling.style.display = 'none';
    });

    if (name.value.trim().length < 2) {
        name.classList.add('error');
        document.getElementById('nameError').style.display = 'block';
        valid = false;
    }

    if (!/^[0-9+\s\-]{7,20}$/.test(phone.value.trim())) {
        phone.classList.add('error');
        document.getElementById('phoneError').style.display = 'block';
        valid = false;
    }

    if (address.value.trim().length < 5) {
        address.classList.add('error');
        document.getElementById('addressError').style.display = 'block';
        valid = false;
    }

    if (!valid) e.preventDefault();
});
</script>

</body>
</html>