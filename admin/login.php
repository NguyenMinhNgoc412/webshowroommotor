<?php
session_start();
require_once "../config/database.php";

if (isset($_SESSION['admin'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === '' || $password === '') {
        $error = 'Vui lòng nhập đầy đủ thông tin';
    } else {
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($admin = $result->fetch_assoc()) {
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin'] = [
                    'id' => $admin['id'],
                    'username' => $admin['username'],
                    'login_time' => time()
                ];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Sai mật khẩu';
            }
        } else {
            $error = 'Tài khoản không tồn tại';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN Login</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-body">
    <div class="login-box">
        <h2>ADMIN LOGIN</h2>
        <?php if ($error): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Tên đăng nhập">
        <input type="password" name="password" placeholder="Mật khẩu">
        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>