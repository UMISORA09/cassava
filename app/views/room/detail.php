<?php
/**
 * Room Detail Page
 */

session_start();
require_once '../../../config/database.php';
require_once '../../../config/config.php';
require_once '../../models/Room.php';
require_once '../../models/Review.php';
require_once '../../models/ContactRequest.php';

if (!isset($_GET['id'])) {
    header('Location: ../../../index.php');
    exit;
}

$room_model = new Room($conn);
$review_model = new Review($conn);
$contact_model = new ContactRequest($conn);

$room_id = (int)$_GET['id'];
$room = $room_model->getRoomById($room_id);

if (!$room) {
    header('Location: ../../../index.php');
    exit;
}

// Get reviews
$reviews = $review_model->getReviewsByRoom($room_id);
$rating_info = $review_model->getAverageRating($room_id);

// Handle contact request
$contact_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact'])) {
    if (!isset($_SESSION['user_id'])) {
        $contact_message = '<div class="alert alert-warning">Vui lòng <a href="../../auth/login.php">đăng nhập</a> để gửi yêu cầu liên hệ</div>';
    } else {
        $phone = trim($_POST['phone']);
        $message = trim($_POST['message']);
        
        if (!empty($phone)) {
            $contact_model->createRequest($room_id, $_SESSION['user_id'], $phone, $message);
            $contact_message = '<div class="alert alert-success">Yêu cầu liên hệ đã được gửi! Chủ nhà sẽ liên hệ với bạn trong thời gian sớm nhất.</div>';
        } else {
            $contact_message = '<div class="alert alert-error">Vui lòng nhập số điện thoại</div>';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($room['title']); ?> - <?php echo SITE_NAME; ?></title>
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
                <li><a href="../../../index.php">← Quay lại</a></li>
            </ul>
        </div>
    </nav>

    <!-- Room Detail -->
    <section class="room-detail">
        <div class="container">
            <div class="detail-content">
                <div class="detail-images">
                    <div class="main-image">
                        <img src="<?php echo !empty($room['image_main']) ? $room['image_main'] : '../../../public/images/no-image.jpg'; ?>" 
                             alt="<?php echo htmlspecialchars($room['title']); ?>">
                    </div>
                </div>

                <div class="detail-info">
                    <h1><?php echo htmlspecialchars($room['title']); ?></h1>
                    
                    <div class="room-meta">
                        <p><strong>📍 Địa chỉ:</strong> <?php echo htmlspecialchars($room['address']); ?></p>
                        <p><strong>📐 Diện tích:</strong> <?php echo $room['area']; ?> m²</p>
                        <p><strong>🛏️ Phòng ngủ:</strong> <?php echo $room['bedrooms']; ?></p>
                        <p><strong>🚿 Phòng tắm:</strong> <?php echo $room['bathrooms']; ?></p>
                        <p><strong>📍 Quận/Huyện:</strong> <?php echo htmlspecialchars($room['district']); ?>, <?php echo htmlspecialchars($room['city']); ?></p>
                    </div>

                    <div class="price-section">
                        <h2 class="price">💰 <?php echo number_format($room['price']); ?> ₫/tháng</h2>
                    </div>

                    <div class="landlord-section">
                        <h3>Thông tin chủ nhà</h3>
                        <p><strong>Tên:</strong> <?php echo htmlspecialchars($room['landlord_name']); ?></p>
                        <p><strong>Điện thoại:</strong> <a href="tel:<?php echo $room['landlord_phone']; ?>"><?php echo htmlspecialchars($room['landlord_phone']); ?></a></p>
                        <p><strong>Email:</strong> <a href="mailto:<?php echo $room['landlord_email']; ?>"><?php echo htmlspecialchars($room['landlord_email']); ?></a></p>
                    </div>

                    <div class="description-section">
                        <h3>Mô tả</h3>
                        <p><?php echo nl2br(htmlspecialchars($room['description'])); ?></p>
                    </div>

                    <!-- Contact Form -->
                    <div class="contact-section">
                        <h3>Gửi yêu cầu liên hệ</h3>
                        <?php echo $contact_message; ?>
                        <form method="POST" novalidate>
                            <div class="form-group">
                                <label for="phone">Số điện thoại</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>

                            <div class="form-group">
                                <label for="message">Lời nhắn</label>
                                <textarea id="message" name="message" rows="4" placeholder="Nhập lời nhắn của bạn..."></textarea>
                            </div>

                            <button type="submit" name="contact" value="1" class="btn-primary">Gửi yêu cầu liên hệ</button>
                        </form>
                    </div>
                </div>

                <!-- Reviews Section -->
                <div class="reviews-section">
                    <h3>Đánh giá (<?php echo $rating_info['total'] ?? 0; ?>)</h3>
                    
                    <?php if ($rating_info['avg_rating']): ?>
                        <div class="rating-summary">
                            <span class="avg-rating">⭐ <?php echo number_format($rating_info['avg_rating'], 1); ?>/5</span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($reviews)): ?>
                        <div class="reviews-list">
                            <?php foreach ($reviews as $review): ?>
                                <div class="review-item">
                                    <p><strong><?php echo htmlspecialchars($review['user_name']); ?></strong></p>
                                    <p class="rating">⭐ <?php echo $review['rating']; ?>/5</p>
                                    <p><?php echo htmlspecialchars($review['comment']); ?></p>
                                    <small><?php echo date('d/m/Y', strtotime($review['created_at'])); ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>Chưa có đánh giá nào.</p>
                    <?php endif; ?>
                </div>
            </div>
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
