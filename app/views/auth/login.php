<?php
/**
 * Login Controller
 */

session_start();
require_once '../../../config/database.php';
require_once '../../../config/config.php';
require_once '../../models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $errors = [];

    // Validation
    if (empty($email)) $errors[] = 'Email không được để trống';
    if (empty($password)) $errors[] = 'Mật khẩu không được để trống';

    if (empty($errors)) {
        $user = new User($conn);
        $userData = $user->getUserByEmail($email);

        if ($userData && $user->verifyPassword($password, $userData['password'])) {
            $_SESSION['user_id'] = $userData['id'];
            $_SESSION['email'] = $userData['email'];
            $_SESSION['name'] = $userData['name'];
            $_SESSION['role'] = $userData['role'];
            
            header('Location: ../../../index.php');
            exit;
        } else {
            $errors[] = 'Email hoặc mật khẩu không chính xác';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../../../public/css/style.css">
    <link rel="stylesheet" href="../../../public/css/responsive.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-form">
            <h1>Đăng nhập <?php echo SITE_NAME; ?></h1>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p>❌ <?php echo $error; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" novalidate>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <button type="submit" class="btn-primary">Đăng nhập</button>
            </form>

            <p class="auth-link">
                Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a>
            </p>
        </div>
    </div>
</body>
</html>
