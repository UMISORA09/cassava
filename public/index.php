<?php
/**
 * Cassava - Index/Home Page
 */

session_start();
require_once 'config/database.php';
require_once 'config/config.php';
require_once 'app/models/Room.php';

$room = new Room($conn);

// Get current page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get search keyword
$keyword = isset($_GET['search']) ? trim($_GET['search']) : '';

// Get rooms
if (!empty($keyword)) {
    $rooms = $room->searchRooms($keyword, $page);
    $total = $room->getTotalCount($keyword);
} else {
    $rooms = $room->getAllAvailableRooms($page);
    $total = $room->getTotalCount();
}

$total_pages = ceil($total / ITEMS_PER_PAGE);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - <?php echo SITE_DESC; ?></title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/responsive.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar">
        <div class="container">
            <div class="navbar-brand">
                <a href="index.php" class="logo">
                    <span class="logo-icon">🏠</span> <?php echo SITE_NAME; ?>
                </a>
            </div>
            <ul class="navbar-menu">
                <li><a href="index.php">Trang chủ</a></li>
                <li><a href="index.php">Tìm phòng</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] === 'landlord'): ?>
                        <li><a href="app/views/landlord/my-rooms.php">Quản lý phòng</a></li>
                        <li><a href="app/views/landlord/post-room.php">Đăng tin</a></li>
                    <?php endif; ?>
                    <li><a href="app/views/user/profile.php">Hồ sơ</a></li>
                    <li><a href="app/controllers/logout.php">Đăng xuất</a></li>
                <?php else: ?>
                    <li><a href="app/views/auth/login.php">Đăng nhập</a></li>
                    <li><a href="app/views/auth/register.php">Đăng ký</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-content">
            <h1>Tìm phòng trọ yêu thích</h1>
            <p>Khám phá hàng ngàn phòng trọ chất lượng tại các vị trí chiến lược</p>
            
            <form class="search-form" method="GET">
                <input type="text" name="search" placeholder="Tìm phòng trọ..." 
                       value="<?php echo htmlspecialchars($keyword); ?>" required>
                <button type="submit">Tìm kiếm</button>
            </form>
        </div>
    </section>

    <!-- Rooms Listing -->
    <section class="rooms-section">
        <div class="container">
            <h2>Phòng trọ nổi bật</h2>
            
            <?php if (empty($rooms)): ?>
                <div class="empty-state">
                    <p>Không tìm thấy phòng trọ phù hợp</p>
                </div>
            <?php else: ?>
                <div class="rooms-grid">
                    <?php foreach ($rooms as $r): ?>
                        <div class="room-card">
                            <div class="room-image">
                                <img src="<?php echo !empty($r['image_main']) ? $r['image_main'] : 'public/images/no-image.jpg'; ?>" 
                                     alt="<?php echo htmlspecialchars($r['title']); ?>">
                                <span class="room-status"><?php echo ucfirst($r['status']); ?></span>
                            </div>
                            <div class="room-info">
                                <h3><?php echo htmlspecialchars($r['title']); ?></h3>
                                <p class="address">📍 <?php echo htmlspecialchars($r['address']); ?></p>
                                <p class="price">💰 <?php echo number_format($r['price']); ?> ₫/tháng</p>
                                <p class="details">
                                    <span>📐 <?php echo $r['area']; ?> m²</span>
                                    <span>🛏️ <?php echo $r['bedrooms']; ?> phòng ngủ</span>
                                </p>
                                <p class="landlord">Chủ nhà: <strong><?php echo htmlspecialchars($r['landlord_name']); ?></strong></p>
                                <a href="app/views/room/detail.php?id=<?php echo $r['id']; ?>" class="btn-view">Xem chi tiết</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=1<?php echo $keyword ? '&search=' . urlencode($keyword) : ''; ?>" class="btn-page">« Đầu tiên</a>
                            <a href="?page=<?php echo $page - 1; ?><?php echo $keyword ? '&search=' . urlencode($keyword) : ''; ?>" class="btn-page">‹ Trước</a>
                        <?php endif; ?>

                        <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $keyword ? '&search=' . urlencode($keyword) : ''; ?>" 
                               class="btn-page <?php echo $i === $page ? 'active' : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>

                        <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?><?php echo $keyword ? '&search=' . urlencode($keyword) : ''; ?>" class="btn-page">Sau ›</a>
                            <a href="?page=<?php echo $total_pages; ?><?php echo $keyword ? '&search=' . urlencode($keyword) : ''; ?>" class="btn-page">Cuối cùng »</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2026 <?php echo SITE_NAME; ?>. Tất cả quyền được bảo lưu.</p>
            <p>
                <a href="#">Về chúng tôi</a> | 
                <a href="#">Liên hệ</a> | 
                <a href="#">Điều khoản</a>
            </p>
        </div>
    </footer>

    <script src="public/js/main.js"></script>
</body>
</html>
