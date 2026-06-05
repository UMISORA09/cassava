<?php
/**
 * Room Model
 */

class Room {
    private $conn;
    private $table = 'rooms';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get all available rooms with pagination
    public function getAllAvailableRooms($page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT r.*, u.name as landlord_name, u.phone as landlord_phone 
                  FROM " . $this->table . " r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.status = 'available' 
                  ORDER BY r.created_at DESC 
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Search rooms
    public function searchRooms($keyword, $page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        $keyword = "%$keyword%";
        
        $query = "SELECT r.*, u.name as landlord_name, u.phone as landlord_phone 
                  FROM " . $this->table . " r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.status = 'available' AND (
                    r.title LIKE ? OR 
                    r.description LIKE ? OR 
                    r.address LIKE ? OR 
                    r.district LIKE ? OR 
                    r.city LIKE ?
                  )
                  ORDER BY r.created_at DESC 
                  LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssii", $keyword, $keyword, $keyword, $keyword, $keyword, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Filter rooms
    public function filterRooms($filters, $page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT r.*, u.name as landlord_name, u.phone as landlord_phone 
                  FROM " . $this->table . " r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.status = 'available'";
        
        $params = [];
        $types = "";

        if (!empty($filters['city'])) {
            $query .= " AND r.city = ?";
            $params[] = $filters['city'];
            $types .= "s";
        }

        if (!empty($filters['district'])) {
            $query .= " AND r.district = ?";
            $params[] = $filters['district'];
            $types .= "s";
        }

        if (!empty($filters['min_price'])) {
            $query .= " AND r.price >= ?";
            $params[] = $filters['min_price'];
            $types .= "d";
        }

        if (!empty($filters['max_price'])) {
            $query .= " AND r.price <= ?";
            $params[] = $filters['max_price'];
            $types .= "d";
        }

        if (!empty($filters['bedrooms'])) {
            $query .= " AND r.bedrooms >= ?";
            $params[] = $filters['bedrooms'];
            $types .= "i";
        }

        $query .= " ORDER BY r.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = $this->conn->prepare($query);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Get room by ID
    public function getRoomById($id) {
        $query = "SELECT r.*, u.name as landlord_name, u.phone as landlord_phone, u.email as landlord_email
                  FROM " . $this->table . " r 
                  JOIN users u ON r.user_id = u.id 
                  WHERE r.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Create room
    public function createRoom($userId, $title, $description, $price, $area, $address, $ward, $district, $city, $bedrooms, $bathrooms) {
        $status = 'pending';
        $query = "INSERT INTO " . $this->table . " 
                  (user_id, title, description, price, area, address, ward, district, city, bedrooms, bathrooms, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("issdsssssii", $userId, $title, $description, $price, $area, $address, $ward, $district, $city, $bedrooms, $bathrooms, $status);
        
        return $stmt->execute() ? $this->conn->insert_id : false;
    }

    // Update room
    public function updateRoom($id, $title, $description, $price, $area, $address, $bedrooms, $bathrooms) {
        $query = "UPDATE " . $this->table . " 
                  SET title = ?, description = ?, price = ?, area = ?, address = ?, bedrooms = ?, bathrooms = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssdsdsii", $title, $description, $price, $area, $address, $bedrooms, $bathrooms, $id);
        
        return $stmt->execute();
    }

    // Get total count
    public function getTotalCount($search = null) {
        if ($search) {
            $keyword = "%$search%";
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " 
                      WHERE status = 'available' AND (
                        title LIKE ? OR description LIKE ? OR address LIKE ?
                      )";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sss", $keyword, $keyword, $keyword);
        } else {
            $query = "SELECT COUNT(*) as total FROM " . $this->table . " WHERE status = 'available'";
            $stmt = $this->conn->prepare($query);
        }
        
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['total'];
    }

    // Get rooms by landlord
    public function getRoomsByLandlord($userId, $page = 1, $limit = 12) {
        $offset = ($page - 1) * $limit;
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $userId, $limit, $offset);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    // Delete room
    public function deleteRoom($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

?>
