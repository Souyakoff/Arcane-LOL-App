<?php
// Démarrer la session et récupérer les informations de l'utilisateur
session_start();

// Vérifier si l'utilisateur est connecté
$user_id = $_SESSION['user_id'] ?? null;
$user = null;

if ($user_id) {
    // Récupérer les informations de l'utilisateur
    include('db_connect.php');
    $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Définir l'image de profil
    $profile_picture = (!empty($user['profile_picture']) && file_exists($user['profile_picture'])) 
        ? $user['profile_picture'] 
        : 'images/default_profile_picture.jpg'; // Image par défaut
}
?>
