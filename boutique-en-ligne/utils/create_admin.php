<?php
// utils/create_admin.php
// Script to create a default admin account if none exists

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Utilisateur.php';

/**
 * Create a default admin account if no admin account exists
 * 
 * Default credentials:
 * Email: admin@footcap.com
 * Password: Admin123!
 * 
 * @return array Result of the operation
 */
function createDefaultAdmin() {
    try {
        // First check if any admin account exists
        $db = Database::getInstance()->getConnection();
        $query = "SELECT COUNT(*) as admin_count FROM utilisateurs WHERE est_admin = 1";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['admin_count'] > 0) {
            return [
                'success' => true,
                'message' => 'Un compte administrateur existe déjà.',
                'created' => false
            ];
        }

        // Create new admin account
        $utilisateur = new Utilisateur();
        $admin_id = $utilisateur->creer(
            'Admin', // nom
            'FootCap', // prenom
            'admin@footcap.com', // email
            'Admin123!', // mot_de_passe
            'Admin Office', // adresse
            '0123456789', // telephone
            true // est_admin
        );

        if ($admin_id) {
            return [
                'success' => true,
                'message' => 'Compte administrateur par défaut créé avec succès.',
                'created' => true,
                'credentials' => [
                    'email' => 'admin@footcap.com',
                    'password' => 'Admin123!'
                ]
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Erreur lors de la création du compte administrateur par défaut.',
                'created' => false
            ];
        }
    } catch (PDOException $e) {
        return [
            'success' => false,
            'message' => 'Erreur de base de données: ' . $e->getMessage(),
            'created' => false
        ];
    }
}

// If script is run directly from CLI or browser, execute the function
if (php_sapi_name() === 'cli' || isset($_GET['run'])) {
    $result = createDefaultAdmin();
    echo json_encode($result, JSON_PRETTY_PRINT);
}
?>