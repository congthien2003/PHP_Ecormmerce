<!DOCTYPE html>
<html>
<head>
    <title>Đăng Ký</title>
</head>
<body>
    <h1>Đăng Ký</h1>
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>
    <form method="POST" action="/php/s4_php/user/register">
        <label for="username">Tên đăng nhập:</label>
        <input type="text" id="username" name="username" required>
        <br>
        <label for="password">Mật khẩu:</label>
        <input type="password" id="password" name="password" required>
        <br>
        <button type="submit">Đăng Ký</button>
    </form>
</body>
</html>