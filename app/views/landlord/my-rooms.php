<?php
/**
 * Landlord - My Rooms Page
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

$room = new Room($conn);
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$rooms = $room->getRoomsByLandlord($_SESSION['user_id'], $page);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý phòng - <?php echo SITE_NAME; ?></title>
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

    <!-- My Rooms Section -->
    <section class="rooms-section">
        <div class="container">
            <h2>Phòng trọ của tôi</h2>
            
            <?php if (empty($rooms)): ?>
                <div class="empty-state">
                    <p>Bạn chưa đăng phòng nào. <a href="post-room.php">Đăng phòng ngay</a></p>
                </div>
            <?php else: ?>
                <div class="admin-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Tiêu đề</th>
                                <th>Giá</th>
                                <th>Địa chỉ</th>
                                <th>Trạng thái</th>
                                <th>Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($rooms as $r): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($r['title']); ?></td>
                                    <td><?php echo number_format($r['price']); ?> ₫</td>
                                    <td><?php echo htmlspecialchars($r['address']); ?></td>
                                    <td>
                                        <span class="status-badge status-<?php echo $r['status']; ?>">
                                            <?php echo ucfirst($r['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="edit-room.php?id=<?php echo $r['id']; ?>" class="btn-small">Sửa</a>
                                        <a href="delete-room.php?id=<?php echo $r['id']; ?>" class="btn-small btn-danger">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
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
