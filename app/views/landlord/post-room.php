<?php
/**
 * Landlord - Post Room Page
 */

session_start();
require_once '../../../config/database.php';
require_once '../../../config/config.php';
require_once '../../models/Room.php';

// Check if user is landlord
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'landlord') {
    header('Location: ../../auth/login.php');
    exit;
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $room = new Room($conn);
    
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $price = (float)$_POST['price'];
    $area = (float)$_POST['area'];
    $address = trim($_POST['address']);
    $ward = trim($_POST['ward']);
    $district = trim($_POST['district']);
    $city = trim($_POST['city']);
    $bedrooms = (int)$_POST['bedrooms'];
    $bathrooms = (int)$_POST['bathrooms'];
    
    $errors = [];
    
    if (empty($title)) $errors[] = 'Tiêu đề không được để trống';
    if (empty($description)) $errors[] = 'Mô tả không được để trống';
    if ($price <= 0) $errors[] = 'Giá phòng phải lớn hơn 0';
    if ($area <= 0) $errors[] = 'Diện tích phòng phải lớn hơn 0';
    if (empty($address)) $errors[] = 'Địa chỉ không được để trống';
    if (empty($city)) $errors[] = 'Thành phố không được để trống';
    
    if (empty($errors)) {
        $room_id = $room->createRoom($_SESSION['user_id'], $title, $description, $price, $area, $address, $ward, $district, $city, $bedrooms, $bathrooms);
        
        if ($room_id) {
            $message = '<div class="alert alert-success">Phòng đã được đăng thành công! Chờ phê duyệt từ admin.</div>';
        } else {
            $message = '<div class="alert alert-error">Lỗi khi đăng phòng. Vui lòng thử lại.</div>';
        }
    } else {
        $message = '<div class="alert alert-error">';
        foreach ($errors as $error) {
            $message .= '<p>❌ ' . $error . '</p>';
        }
        $message .= '</div>';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>��ăng tin phòng - <?php echo SITE_NAME; ?></title>
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
                <li><a href="my-rooms.php">Quản lý phòng</a></li>
                <li><a href="post-room.php">Đăng tin</a></li>
                <li><a href="../user/profile.php">Hồ sơ</a></li>
                <li><a href="../../controllers/logout.php">Đăng xuất</a></li>
            </ul>
        </div>
    </nav>

    <!-- Post Room Section -->
    <section class="auth-container">
        <div class="auth-form" style="max-width: 600px; width: 100%;">
            <h1>Đăng tin phòng trọ</h1>
            
            <?php echo $message; ?>

            <form method="POST" novalidate>
                <div class="form-group">
                    <label for="title">Tiêu đề *</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả chi tiết *</label>
                    <textarea id="description" name="description" rows="6" required><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="price">Giá phòng (₫/tháng) *</label>
                    <input type="number" id="price" name="price" value="<?php echo htmlspecialchars($_POST['price'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="area">Diện tích (m²) *</label>
                    <input type="number" id="area" name="area" step="0.1" value="<?php echo htmlspecialchars($_POST['area'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">Địa chỉ *</label>
                    <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($_POST['address'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="ward">Phường/Xã</label>
                    <input type="text" id="ward" name="ward" value="<?php echo htmlspecialchars($_POST['ward'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="district">Quận/Huyện</label>
                    <input type="text" id="district" name="district" value="<?php echo htmlspecialchars($_POST['district'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="city">Thành phố *</label>
                    <input type="text" id="city" name="city" value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="bedrooms">Số phòng ngủ</label>
                    <input type="number" id="bedrooms" name="bedrooms" value="<?php echo htmlspecialchars($_POST['bedrooms'] ?? 1); ?>" min="1">
                </div>

                <div class="form-group">
                    <label for="bathrooms">Số phòng tắm</label>
                    <input type="number" id="bathrooms" name="bathrooms" value="<?php echo htmlspecialchars($_POST['bathrooms'] ?? 1); ?>" min="1">
                </div>

                <button type="submit" class="btn-primary">Đăng tin</button>
            </form>

            <p class="auth-link">
                <a href="my-rooms.php">← Quay lại quản lý phòng</a>
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
