<?php
/**
 * Review Model
 */

class Review {
    private $conn;
    private $table = 'reviews';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create review
    public function createReview($roomId, $userId, $rating, $comment = '') {
        $query = "INSERT INTO " . $this->table . " (room_id, user_id, rating, comment) 
                  VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iiis", $roomId, $userId, $rating, $comment);
        
        return $stmt->execute();
    }

    // Get reviews by room
    public function getReviewsByRoom($roomId) {
        $query = "SELECT r.*, u.name as user_name, u.avatar FROM " . $this->table . " r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.room_id = ? 
                  ORDER BY r.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get average rating
    public function getAverageRating($roomId) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM " . $this->table . " 
                  WHERE room_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Check if user already reviewed
    public function hasReviewed($roomId, $userId) {
        $query = "SELECT id FROM " . $this->table . " WHERE room_id = ? AND user_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $roomId, $userId);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}

?>
