<?php
/**
 * User Profile Page
 */

session_start();
require_once '../../../config/database.php';
require_once '../../../config/config.php';
require_once '../../models/User.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../auth/login.php');
    exit;
}

$user = new User($conn);
$userData = $user->getUserById($_SESSION['user_id']);
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    
    if (empty($name)) {
        $message = '<div class="alert alert-error">Tên không được để trống</div>';
    } else {
        if ($user->updateProfile($_SESSION['user_id'], $name, $phone)) {
            $_SESSION['name'] = $name;
            $userData = $user->getUserById($_SESSION['user_id']);
            $message = '<div class="alert alert-success">Hồ sơ đã được cập nhật thành công</div>';
        } else {
            $message = '<div class="alert alert-error">Lỗi cập nhật hồ sơ. Vui lòng thử lại.</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ sơ - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="../../../public/css/style.css">
    <link rel="stylesheet" href="../../../public/css/responsive.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="../../../index.php" class="logo">
                    <span class="logo-icon">🏠</span> <?php echo SITE_NAME; ?>
                </a>
            </div>
            <ul class="navbar-menu">
                <li><a href="../../../index.php">Trang chủ</a></li>
                <li><a href="../../../index.php">Tìm phòng</a></li>
                <?php if ($_SESSION['role'] === 'landlord'): ?>
                    <li><a href="../landlord/my-rooms.php">Quản lý phòng</a></li>
                    <li><a href="../landlord/post-room.php">Đăng tin</a></li>
                <?php endif; ?>
                <li><a href="../user/profile.php">Hồ sơ</a></li>
                <li><a href="../../controllers/logout.php">Đăng xuất</a></li>
            </ul>
        </div>
    </nav>

    <!-- Profile Section -->
    <section class="auth-container">
        <div class="auth-form" style="max-width: 500px;">
            <h1>Hồ sơ cá nhân</h1>
            
            <?php echo $message; ?>

            <form method="POST" novalidate>
                <div class="form-group">
                    <label for="name">Tên</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($userData['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['email']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="phone">Số điện thoại</label>
                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($userData['phone'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="role">Loại tài khoản</label>
                    <input type="text" id="role" value="<?php echo $userData['role'] === 'landlord' ? 'Chủ nhà trọ' : 'Người tìm phòng'; ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="created">Ngày tạo tài khoản</label>
                    <input type="text" id="created" value="<?php echo date('d/m/Y', strtotime($userData['created_at'])); ?>" disabled>
                </div>

                <button type="submit" class="btn-primary">Cập nhật hồ sơ</button>
            </form>

            <p class="auth-link">
                <a href="../../../index.php">← Quay lại trang chủ</a>
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 <?php echo SITE_NAME; ?>. Tất cả quyền được bảo lưu.</p>
        </div>
    </footer>

    <script src="../../../public/js/main.js"></script>
</body>
</html>
