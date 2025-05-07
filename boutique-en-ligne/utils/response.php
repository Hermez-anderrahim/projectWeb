<?php
// utils/response.php
class Response {
    // Renvoyer une réponse de succès
    public static function success($message, $data = []) {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data
        ];
    }
    
    // Renvoyer une réponse d'erreur
    public static function error($message, $code = 400) {
        http_response_code($code);
        
        return [
            'success' => false,
            'message' => $message,
            'code' => $code
        ];
    }
}
?>