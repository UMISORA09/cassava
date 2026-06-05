<?php
/**
 * Cassava - Global Configuration
 */

// Site info
define('SITE_NAME', 'Cassava');
define('SITE_URL', 'http://localhost/cassava');
define('SITE_DESC', 'Nền tảng quản lý và tìm kiếm phòng trọ');

// App settings
define('ITEMS_PER_PAGE', 12);
define('MAX_IMAGE_SIZE', 5242880); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 1800);

// Admin email
define('ADMIN_EMAIL', 'admin@cassava.local');

?>
