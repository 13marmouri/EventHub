<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');
// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Vous devez être connecté']);
    exit;
}
// Récupérer les données JSON (PAS $_POST)
$input = json_decode(file_get_contents('php://input'), true);
$evenement_id = intval($input['event_id'] ?? 0);
$utilisateur_id = $_SESSION['user_id'];
if ($evenement_id <= 0) {
    echo json_encode(['success' => false, 'error' => 'ID d\'événement invalide']);
    exit;
}
try {
    // Vérifier si déjà inscrit
    $check = $pdo->prepare("SELECT id FROM inscriptions WHERE utilisateur_id = :user_id AND evenement_id = :event_id");
    $check->execute([':user_id' => $utilisateur_id, ':event_id' => $evenement_id]);
    if ($check->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Vous êtes déjà inscrit à cet événement']);
        exit;
    } 
    // Vérifier les places disponibles - CORRECTION: places_total au lieu de places_max
    $event = $pdo->prepare("SELECT places_total, 
                            (SELECT COUNT(*) FROM inscriptions WHERE evenement_id = :event_id) as inscrits 
                            FROM evenements WHERE id = :event_id");
    $event->execute([':event_id' => $evenement_id]);
    $eventData = $event->fetch(PDO::FETCH_ASSOC); 
    if (!$eventData) {
        echo json_encode(['success' => false, 'error' => 'Événement non trouvé']);
        exit;
    }
    if ($eventData['inscrits'] >= $eventData['places_total']) {
        echo json_encode(['success' => false, 'error' => 'Plus de places disponibles']);
        exit;
    } 
    // Inscrire l'utilisateur
    $insert = $pdo->prepare("INSERT INTO inscriptions (utilisateur_id, evenement_id, statut) VALUES (:user_id, :event_id, 'confirmee')");
    $insert->execute([':user_id' => $utilisateur_id, ':event_id' => $evenement_id]);
    // Mettre à jour places_restantes dans la table evenements
    $update = $pdo->prepare("UPDATE evenements SET places_restantes = places_restantes - 1 WHERE id = :event_id");
    $update->execute([':event_id' => $evenement_id]);
    echo json_encode(['success' => true, 'message' => 'Inscription réussie !']);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'Erreur lors de l\'inscription : ' . $e->getMessage()]);
}
?>