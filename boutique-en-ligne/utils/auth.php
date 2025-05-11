<?php
// utils/auth.php
class Auth {
    // Vérifier si l'utilisateur est authentifié
    public static function verifierAuthentification($admin_requis = false) {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if(!isset($_SESSION['utilisateur'])) {
            return [
                'authentifie' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        // Vérifier si l'admin est requis
        if($admin_requis && !$_SESSION['utilisateur']['est_admin']) {
            return [
                'authentifie' => false,
                'message' => 'Accès réservé aux administrateurs.'
            ];
        }
        
        return [
            'authentifie' => true,
            'utilisateur' => $_SESSION['utilisateur']
        ];
    }
    
    // Générer un jeton d'authentification
    public static function genererJeton() {
        return bin2hex(random_bytes(32));
    }
    
    // Définir un cookie d'authentification
    public static function definirCookie($nom, $valeur, $expiration = 2592000) { // 30 jours par défaut
        setcookie(
            $nom,
            $valeur,
            [
                'expires' => time() + $expiration,
                'path' => '/',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on', // Only use secure for HTTPS
                'httponly' => true,
                'samesite' => 'Lax' // Changed from Strict to Lax for better compatibility
            ]
        );
    }
}
?>