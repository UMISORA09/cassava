<?php
/**
 * User Model
 */

class User {
    private $conn;
    private $table = 'users';

    public function __construct($db) {
        $this->conn = $db;
    }

    // Get user by email
    public function getUserByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Get user by ID
    public function getUserById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ? AND is_active = 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    // Register new user
    public function register($name, $email, $password, $phone = '', $role = 'user') {
        $query = "INSERT INTO " . $this->table . " (name, email, password, phone, role) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("sssss", $name, $email, $hashedPassword, $phone, $role);
        
        return $stmt->execute();
    }

    // Update user profile
    public function updateProfile($id, $name, $phone, $avatar = null) {
        if ($avatar) {
            $query = "UPDATE " . $this->table . " SET name = ?, phone = ?, avatar = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sssi", $name, $phone, $avatar, $id);
        } else {
            $query = "UPDATE " . $this->table . " SET name = ?, phone = ? WHERE id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssi", $name, $phone, $id);
        }
        
        return $stmt->execute();
    }

    // Verify password
    public function verifyPassword($password, $hashedPassword) {
        return password_verify($password, $hashedPassword);
    }

    // Get all landlords (for admin)
    public function getAllLandlords() {
        $query = "SELECT * FROM " . $this->table . " WHERE role = 'landlord' ORDER BY created_at DESC";
        $result = $this->conn->query($query);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    // Check if email exists
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->num_rows > 0;
    }
}

?>
