<?php
// backend/controllers/UserController.php
class UserController {
    private $db;
    private $user;
    
    public function __construct($db) {
        $this->db = $db;
        
        // Include model if not already included
        if(!class_exists('User')) {
            require_once __DIR__ . '/../models/User.php';
        }
        
        $this->user = new User($db);
    }
    
    // Register new user
    public function register($userData) {
        // Validate required fields
        $required = ['username', 'email', 'password', 'confirm_password'];
        foreach($required as $field) {
            if(empty($userData[$field])) {
                return ['error' => 'All required fields must be filled.'];
            }
        }
        
        // Validate email format
        if(!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            return ['error' => 'Invalid email format.'];
        }
        
        // Validate password match
        if($userData['password'] !== $userData['confirm_password']) {
            return ['error' => 'Passwords do not match.'];
        }
        
        // Set user properties
        $this->user->username = $userData['username'];
        $this->user->email = $userData['email'];
        $this->user->password = $userData['password'];
        $this->user->first_name = $userData['first_name'] ?? '';
        $this->user->last_name = $userData['last_name'] ?? '';
        $this->user->address = $userData['address'] ?? '';
        $this->user->phone = $userData['phone'] ?? '';
        
        // Register user
        $result = $this->user->register();
        
        if(isset($result['error'])) {
            return $result;
        }
        
        return [
            'success' => true,
            'user_id' => $result['user_id'],
            'message' => 'Registration successful.'
        ];
    }
    
    // Login user
    public function login($email, $password) {
        // Set user properties
        $this->user->email = $email;
        $this->user->password = $password;
        
        // Login user
        if($this->user->login()) {
            return [
                'success' => true,
                'user_id' => $this->user->user_id,
                'username' => $this->user->username,
                'is_admin' => $this->user->is_admin,
                'message' => 'Login successful.'
            ];
        } else {
            return ['error' => 'Invalid email or password.'];
        }
    }
    
    // Get user details
    public function getUserDetails($user_id) {
        return $this->user->getUserDetails($user_id);
    }
    
    // Update user profile
    public function updateProfile($user_id, $userData) {
        // Set user properties
        $this->user->user_id = $user_id;
        $this->user->first_name = $userData['first_name'] ?? '';
        $this->user->last_name = $userData['last_name'] ?? '';
        $this->user->address = $userData['address'] ?? '';
        $this->user->phone = $userData['phone'] ?? '';
        
        // Update user
        if($this->user->updateUser()) {
            return [
                'success' => true,
                'message' => 'Profile updated successfully.'
            ];
        } else {
            return ['error' => 'Error updating profile.'];
        }
    }
    
    // Change password
    public function changePassword($user_id, $current_password, $new_password) {
        // First, verify current password
        $stmt = $this->db->prepare("SELECT password FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if(!$user) {
            return ['error' => 'User not found.'];
        }
        
        if(!password_verify($current_password, $user['password'])) {
            return ['error' => 'Current password is incorrect.'];
        }
        
        // Update password (would need to create a changePassword method in the User model)
        // For now, let's simulate it with a direct query
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        
        $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE user_id = :user_id");
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':user_id', $user_id);
        
        if($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'Password changed successfully.'
            ];
        } else {
            return ['error' => 'Error changing password.'];
        }
    }
    
    // Get all users (admin)
    public function getAllUsers($limit = 10, $offset = 0) {
        $query = "SELECT user_id, username, email, first_name, last_name, is_admin, created_at
                 FROM users
                 ORDER BY user_id DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return [
            'users' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $this->getTotalUsers()
        ];
    }
    
    // Update user (admin)
    public function updateUser($user_id, $userData) {
        // Check if user exists
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        if($stmt->rowCount() === 0) {
            return ['error' => 'User not found.'];
        }
        
        // Build update query based on provided data
        $query = "UPDATE users SET ";
        $params = [];
        
        if(isset($userData['first_name'])) {
            $query .= "first_name = :first_name, ";
            $params[':first_name'] = $userData['first_name'];
        }
        
        if(isset($userData['last_name'])) {
            $query .= "last_name = :last_name, ";
            $params[':last_name'] = $userData['last_name'];
        }
        
        if(isset($userData['address'])) {
            $query .= "address = :address, ";
            $params[':address'] = $userData['address'];
        }
        
        if(isset($userData['phone'])) {
            $query .= "phone = :phone, ";
            $params[':phone'] = $userData['phone'];
        }
        
        if(isset($userData['is_admin'])) {
            $query .= "is_admin = :is_admin, ";
            $params[':is_admin'] = $userData['is_admin'] ? 1 : 0;
        }
        
        // Remove trailing comma and add WHERE clause
        $query = rtrim($query, ', ') . " WHERE user_id = :user_id";
        $params[':user_id'] = $user_id;
        
        // Execute update
        $stmt = $this->db->prepare($query);
        
        foreach($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        if($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'User updated successfully.'
            ];
        } else {
            return ['error' => 'Error updating user.'];
        }
    }
    
    // Delete user (admin)
    public function deleteUser($user_id) {
        // Check if user exists
        $stmt = $this->db->prepare("SELECT user_id FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        if($stmt->rowCount() === 0) {
            return ['error' => 'User not found.'];
        }
        
        // Check if user has any orders
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM orders WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        if($stmt->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
            return ['error' => 'Cannot delete user as they have placed orders.'];
        }
        
        // Delete user
        $stmt = $this->db->prepare("DELETE FROM users WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        
        if($stmt->execute()) {
            return [
                'success' => true,
                'message' => 'User deleted successfully.'
            ];
        } else {
            return ['error' => 'Error deleting user.'];
        }
    }
    
    // Helper method to get total users count
    private function getTotalUsers() {
        $query = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }
}
?>