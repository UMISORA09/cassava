<?php
/**
 * Register Controller
 */

session_start();
require_once '../../../config/database.php';
require_once '../../../config/config.php';
require_once '../../models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $password_confirm = trim($_POST['password_confirm']);
    $phone = trim($_POST['phone']);
    $role = trim($_POST['role']);
    $errors = [];

    // Validation
    if (empty($name)) $errors[] = 'Tên không được để trống';
    if (empty($email)) $errors[] = 'Email không được để trống';
    if (empty($password)) $errors[] = 'Mật khẩu không được để trống';
    if ($password !== $password_confirm) $errors[] = 'Mật khẩu xác nhận không khớp';
    if (strlen($password) < 6) $errors[] = 'Mật khẩu phải có ít nhất 6 ký tự';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Email không hợp lệ';

    if (empty($errors)) {
        $user = new User($conn);
        
        if ($user->emailExists($email)) {
            $errors[] = 'Email này đã được đăng ký';
        } else {
            if ($user->register($name, $email, $password, $phone, $role)) {
                $_SESSION['success'] = 'Đăng ký thành công! Vui lòng đăng nhập.';
                header('Location: login.php');
                exit;
            } else {
                $errors[] = 'Lỗi đăng ký. Vui lòng thử lại.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../../../public/css/style.css">
    <link rel="stylesheet" href="../../../public/css/responsive.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h1>Đăng ký <?php echo SITE_NAME; ?></h1>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p>❌ <?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="form-group">
                    <label for="name">Tên</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="role">Loại tài khoản</label>
                    <select id="role" name="role" required>
                        <option value="user">Người tìm phòng</option>
                        <option value="landlord">Chủ nhà trọ</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirm">Xác nhận mật khẩu</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>

                <button type="submit" class="btn-primary">Đăng ký</button>
            </form>

            <p class="auth-link">
                Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a>
            </p>
        </div>
    </div>
</body>
</html>
