<?php
require 'App/Middlewares/auth.php';
requireLogin();
requireAdminRole();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Thêm sản phẩm</title>
    <script>
        function validateForm() {
            let name = document.getElementById('name').value.trim();
            let price = document.getElementById('price').value;
            let image = document.getElementById('image').value;
            let errors = [];

            // Validate name length
            if (name.length < 10 || name.length > 100) {
                errors.push('Tên sản phẩm phải có từ 10 đến 100 ký tự.');
            }

            // Validate price
            if (price <= 0 || isNaN(price)) {
                errors.push('Giá phải là một số dương lớn hơn 0.');
            }

            // Validate image file (must not be empty)
            if (image === '') {
                errors.push('Hình ảnh là bắt buộc.');
            }

            // Display errors if any
            if (errors.length > 0) {
                alert(errors.join('\n'));
                return false;
            }

            return true;
        }
    </script>
</head>
<body>
    <h1>Thêm sản phẩm mới</h1>

    <?php if (!empty($errors)): ?>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <form method="POST" action="/php/S4_PHP/Product/add" enctype="multipart/form-data" onsubmit="return validateForm();">
        <label for="name">Tên sản phẩm:</label>
        <input type="text" id="name" name="name" required><br><br>

        <label for="description">Mô tả:</label>
        <textarea id="description" name="description" required></textarea><br><br>

        <label for="price">Giá:</label>
        <input type="number" id="price" name="price" step="0.01" required><br><br>

        <label for="image">Hình ảnh:</label>
        <input type="file" id="image" name="image" accept="image/*" required><br><br>

        <label for="category_id">Danh mục:</label>
        <select id="category_id" name="category_id" required>
            <!-- Populate dynamically -->
            <option value="1">Danh mục 1</option>
            <option value="2">Danh mục 2</option>
        </select><br><br>

        <button type="submit">Thêm sản phẩm</button>
    </form>

    <a href="/php/S4_PHP/Product/list">Quay lại danh sách sản phẩm</a>
</body>
</html>
