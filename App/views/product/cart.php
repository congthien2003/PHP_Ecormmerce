<?php
// Kiểm tra xem cookie 'cart' có tồn tại không
$cart = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) : [];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Giỏ hàng</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            max-width: 800px;
        }
        h1 {
            color: #333;
        }
        .cart-item {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-item h4, .cart-item p {
            margin: 0;
        }
        .empty-cart {
            text-align: center;
            font-size: 18px;
            color: #666;
        }
        .total {
            font-size: 20px;
            font-weight: bold;
            text-align: right;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1>Giỏ hàng của bạn</h1>

    <?php if (!empty($cart)): ?>
        <div class="cart-list">
            <?php 
            $totalPrice = 0;
            foreach ($cart as $item): 
                $totalPrice += $item['price'] * $item['quantity'];
            ?>
                <div class="cart-item">
                    <div>
                        <h4><?= htmlspecialchars($item['name']) ?> (<?= htmlspecialchars($item['quantity']) ?>)</h4>
                        <p>Giá: <?= number_format($item['price'], 0, ',', '.') ?> VND</p>
                    </div>
                    <p>Tổng: <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?> VND</p>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="total">Tổng cộng: <?= number_format($totalPrice, 0, ',', '.') ?> VND</div>
    <?php else: ?>
        <p class="empty-cart">Giỏ hàng của bạn đang trống!</p>
    <?php endif; ?>
</body>
</html>
