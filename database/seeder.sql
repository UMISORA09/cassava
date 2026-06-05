-- Seeder data for Cassava

INSERT INTO users (name, email, password, phone, role) VALUES
('Admin User', 'admin@cassava.local', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', '0987654321', 'admin'),
('Nguyễn Văn A', 'landlord1@cassava.local', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', '0912345678', 'landlord'),
('Trần Thị B', 'user1@cassava.local', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', '0909876543', 'user'),
('Phạm Văn C', 'landlord2@cassava.local', '$2y$10$abcdefghijklmnopqrstuvwxyz123456789', '0911111111', 'landlord');

INSERT INTO rooms (user_id, title, description, price, area, address, ward, district, city, bedrooms, bathrooms, status) VALUES
(2, 'Phòng trọ gần trường ĐH Bách Khoa', 'Phòng rộng 20m2, sáng sạch, gần trường ĐH Bách Khoa Hà Nội', 3500000, 20, 'Số 10 Đại Cồ Việt', 'Bách Khoa', 'Hai Bà Trưng', 'Hà Nội', 1, 1, 'available'),
(2, 'Phòng trọ cho nữ sinh viên', 'Phòng riêng, an toàn, gần trạm bus, có wifi miễn phí', 3000000, 18, 'Số 5 Ngõ Tứ Hiệp', 'Tây Hồ', 'Tây Hồ', 'Hà Nội', 1, 1, 'available'),
(4, 'Phòng trọ view đẹp gần Hồ Tây', 'Phòng 25m2, view Hồ Tây, tiện nghi đầy đủ, máy lạnh, nóng lạnh', 4500000, 25, 'Số 20 Quảng Khánh', 'Quảng An', 'Tây Hồ', 'Hà Nội', 1, 1, 'available'),
(4, 'Phòng trọ sinh viên giá rẻ', 'Giá rẻ, gần trường CĐ, có chỗ để xe, an ninh tốt', 2500000, 15, 'Số 15 Hàng Bún', 'Hàng Bún', 'Hoàn Kiếm', 'Hà Nội', 1, 1, 'available'),
(2, 'Chung cư mini full nội thất', 'Chung cư mini 30m2, full nội thất, hệ thống an ninh 24/7', 5500000, 30, 'Số 100 Trần Duy Hưng', 'Trung Hòa', 'Cầu Giấy', 'Hà Nội', 1, 1, 'available');

INSERT INTO room_amenities (room_id, amenity) VALUES
(1, 'WiFi'), (1, 'Máy lạnh'), (1, 'Giường'), (1, 'Tủ quần áo'),
(2, 'WiFi'), (2, 'Máy lạnh'), (2, 'Ban công'),
(3, 'WiFi'), (3, 'Máy lạnh'), (3, 'Giường'), (3, 'Tủ lạnh'), (3, 'View Hồ Tây'),
(4, 'WiFi'), (4, 'Quạt'), (4, 'Giường'),
(5, 'WiFi'), (5, 'Máy lạnh'), (5, 'Giường'), (5, 'Tủ lạnh'), (5, 'Khóa thẻ');

INSERT INTO reviews (room_id, user_id, rating, comment) VALUES
(1, 3, 5, 'Phòng sạch sẽ, chủ nhà thân thiện, rất hài lòng'),
(2, 3, 4, 'Tốt, gần trường, có wifi nhanh'),
(3, 3, 5, 'Giá có hơi cao nhưng chất lượng tương xứng'),
(4, 3, 3, 'Bình thường, cần cải thiện vệ sinh');
