<?php
// utils/auth.php
class Auth {
    // Vérifier si l'utilisateur est authentifié
    public static function verifierAuthentification($admin_requis = false) {
        // Debug session information
        $sessionId = session_id();
        $sessionStatus = session_status();
        $sessionStatusText = ($sessionStatus === PHP_SESSION_DISABLED) ? 'DISABLED' : 
                            (($sessionStatus === PHP_SESSION_NONE) ? 'NONE' : 
                            (($sessionStatus === PHP_SESSION_ACTIVE) ? 'ACTIVE' : 'UNKNOWN'));
        
        error_log("DEBUG [Auth::verifierAuthentification] - Session ID: $sessionId, Status: $sessionStatusText");
        
        if (session_status() === PHP_SESSION_NONE) {
            error_log("DEBUG [Auth::verifierAuthentification] - Starting session");
            session_start();
            // Check if session was successfully started
            $newSessionId = session_id();
            error_log("DEBUG [Auth::verifierAuthentification] - New session ID after start: $newSessionId");
        }
        
        error_log("DEBUG [Auth::verifierAuthentification] - SESSION contents: " . print_r($_SESSION, true));
        
        if(!isset($_SESSION['utilisateur'])) {
            error_log("DEBUG [Auth::verifierAuthentification] - No user in session");
            return [
                'authentifie' => false,
                'message' => 'Utilisateur non connecté.'
            ];
        }
        
        // Vérifier si l'admin est requis
        if($admin_requis && !$_SESSION['utilisateur']['est_admin']) {
            error_log("DEBUG [Auth::verifierAuthentification] - Admin required but user is not admin");
            return [
                'authentifie' => false,
                'message' => 'Accès réservé aux administrateurs.'
            ];
        }
        
        error_log("DEBUG [Auth::verifierAuthentification] - Authentication successful for user: " . $_SESSION['utilisateur']['email']);
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