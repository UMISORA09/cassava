<?php
/**
 * ContactRequest Model
 */

class ContactRequest {
    private $conn;
    private $table = 'contact_requests';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create contact request
    public function createRequest($roomId, $userId, $phone, $message = '') {
        $status = 'pending';
        $query = "INSERT INTO " . $this->table . " (room_id, user_id, phone, message, status) 
                  VALUES (?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iisss", $roomId, $userId, $phone, $message, $status);
        
        return $stmt->execute() ? $this->conn->insert_id : false;
    }

    // Get requests by room
    public function getRequestsByRoom($roomId) {
        $query = "SELECT cr.*, u.name, u.email FROM " . $this->table . " cr 
                  JOIN users u ON cr.user_id = u.id 
                  WHERE cr.room_id = ? 
                  ORDER BY cr.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $roomId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get requests by user
    public function getRequestsByUser($userId) {
        $query = "SELECT cr.*, r.title, r.price, r.address FROM " . $this->table . " cr 
                  JOIN rooms r ON cr.room_id = r.id 
                  WHERE cr.user_id = ? 
                  ORDER BY cr.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Update status
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }
}

?>
