<?php
// api/image.php - API pour servir les images stockées en base de données
require_once __DIR__ . '/../utils/auth.php';
require_once __DIR__ . '/../utils/response.php';
require_once __DIR__ . '/../models/Produit.php';

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérifier si l'ID de l'image est fourni
if (!isset($_GET['id'])) {
    header('HTTP/1.1 400 Bad Request');
    echo 'ID d\'image requis';
    exit;
}

$id_image = intval($_GET['id']);

// Récupérer l'image depuis la base de données
$produit = new Produit();
$image = $produit->getImageById($id_image);

if (!$image || !isset($image['image_data']) || !isset($image['mime_type'])) {
    header('HTTP/1.1 404 Not Found');
    echo 'Image non trouvée';
    exit;
}

// Définir les en-têtes appropriés pour l'image
header('Content-Type: ' . $image['mime_type']);
header('Content-Length: ' . strlen($image['image_data']));
header('Cache-Control: public, max-age=86400'); // Cache de 24 heures

// Envoyer les données de l'image
echo $image['image_data'];
exit;