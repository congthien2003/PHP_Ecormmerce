<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f4f4f4;
        margin: 0;
        padding: 0;
    }

    h1 {
        text-align: center;
        margin-top: 20px;
        color: #333;
    }

    .cart-container {
        width: 80%;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }

    .cart-container table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

    .cart-container th, .cart-container td {
        padding: 10px;
        text-align: center;
        border: 1px solid #ddd;
    }

    .cart-container th {
        background-color: #f8f8f8;
    }

    .cart-container td {
        background-color: #fafafa;
    }

    .cart-container input[type="number"] {
        width: 60px;
        padding: 5px;
        margin: 5px;
        text-align: center;
    }

    .cart-container input[type="submit"] {
        padding: 6px 12px;
        background-color: #28a745;
        border: none;
        color: white;
        cursor: pointer;
        border-radius: 4px;
    }

    .cart-container input[type="submit"]:hover {
        background-color: #218838;
    }

    .cart-container a {
        display: inline-block;
        margin-top: 20px;
        padding: 12px 20px;
        background-color: #007bff;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        text-align: center;
    }

    .cart-container a:hover {
        background-color: #0056b3;
    }

    .cart-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
    }

    .cart-footer .total {
        font-size: 1.2em;
        font-weight: bold;
    }

    .cart-footer form {
        display: inline;
    }

    .cart-footer form input[type="submit"] {
        background-color: #dc3545;
        color: white;
    }

    .cart-footer form input[type="submit"]:hover {
        background-color: #c82333;
    }
</style>

<div class="cart-container">
    <h1>Giỏ hàng của bạn</h1>

    <?php if (empty($cartItems)): ?>
        <p>Giỏ hàng của bạn đang trống.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Số lượng</th>
                    <th>Giá</th>
                    <th>Tổng tiền</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cartItems as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['productName']); ?></td>
                        <td>
                            <form action="/php/S4_PHP/Cart/updateCart" method="POST">
                                <input type="hidden" name="idProduct" value="<?php echo $item['IdProduct']; ?>">
                                <input type="number" name="quantity" value="<?php echo $item['Quantity']; ?>" min="1" required>
                                <input type="submit" value="Cập nhật">
                            </form>
                        </td>
                        <td><?php echo number_format($item['productPrice'], 0, ',', '.'); ?> VNĐ</td>
                        <td><?php echo number_format($item['productPrice'] * $item['Quantity'], 0, ',', '.'); ?> VNĐ</td>
                        <td>
                            <form action="/php/S4_PHP/Cart/removeFromCart" method="POST">
                                <input type="hidden" name="idProduct" value="<?php echo $item['IdProduct']; ?>">
                                <input type="submit" value="Xóa">
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-footer">
            <div class="total">
                Tổng tiền: <?php echo number_format($total, 0, ',', '.'); ?> VNĐ
            </div>
            <form action="/php/S4_PHP/Cart/clearCart" method="POST">
                <input type="submit" value="Xóa toàn bộ giỏ hàng" onclick="return confirm('Bạn có chắc chắn muốn xóa tất cả sản phẩm trong giỏ hàng?');">
            </form>
        </div>

        <a href="/php/S4_PHP/Checkout">Tiến hành thanh toán</a>
    <?php endif; ?>
</div>
