<?php
session_start();
require_once '../config/db.php';

header('Content-Type: application/json');

// DEBUG : Vérifier la session
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'error' => 'Non connecté', 
        'debug' => [
            'session_exists' => isset($_SESSION),
            'session_data' => $_SESSION
        ]
    ]);
    exit;
}

// DEBUG : Vérifier les données reçues
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Méthode non autorisée']);
    exit;
}

// DEBUG : Afficher les données POST reçues
$debug_info = [
    'post_data' => $_POST,
    'session_user_id' => $_SESSION['user_id']
];

$titre = trim($_POST['titre'] ?? '');
$date = $_POST['date'] ?? '';
$heure = $_POST['heure'] ?? '';
$lieu = trim($_POST['lieu'] ?? '');
$places_total = intval($_POST['places_total'] ?? 0);
$club_id = intval($_POST['club_id'] ?? 0);
$description = trim($_POST['description'] ?? '');
$createur_id = $_SESSION['user_id'];

// Validation avec debug
if (empty($titre) || empty($date) || empty($heure) || empty($lieu) || $places_total <= 0 || $club_id <= 0) {
    echo json_encode([
        'success' => false, 
        'error' => 'Tous les champs obligatoires doivent être remplis',
        'debug' => [
            'titre' => $titre,
            'date' => $date,
            'heure' => $heure,
            'lieu' => $lieu,
            'places_total' => $places_total,
            'club_id' => $club_id
        ]
    ]);
    exit;
}

try {
    // DEBUG : Tester la connexion PDO
    if (!$pdo) {
        throw new Exception("Connexion PDO échouée");
    }
    
    $sql = "INSERT INTO evenements (titre, description, date, heure, lieu, places_total, places_restantes, club_id, createur_id) 
            VALUES (:titre, :description, :date, :heure, :lieu, :places_total, :places_total, :club_id, :createur_id)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':titre' => $titre,
        ':description' => $description,
        ':date' => $date,
        ':heure' => $heure,
        ':lieu' => $lieu,
        ':places_total' => $places_total,
        ':club_id' => $club_id,
        ':createur_id' => $createur_id
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Événement créé avec succès', 
        'event_id' => $pdo->lastInsertId(),
        'debug' => $debug_info
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur PDO : ' . $e->getMessage(),
        'sql_state' => $e->getCode(),
        'debug' => $debug_info
    ]);
} catch(Exception $e) {
    echo json_encode([
        'success' => false, 
        'error' => 'Erreur générale : ' . $e->getMessage(),
        'debug' => $debug_info
    ]);
}
?>