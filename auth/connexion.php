<?php
session_start();
require_once '../config/db.php';

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header('Location:../HTML/login.html?error=1');
    exit;
}
// Chercher l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    header('Location:../HTML/login.html?error=2');
    exit;
}

// Vérifier le mot de passe
if (!password_verify($password, $user['mot_de_passe'])) {
    header('Location:../HTML/login.html?error=3');
    exit;
}
// Créer la session
$_SESSION['user_id'] = $user['id'];
$_SESSION['prenom'] = $user['prenom'];
$_SESSION['nom'] = $user['nom'];
$_SESSION['email'] = $user['email'];
$_SESSION['role'] = $user['role'];

// Rediriger vers l'accueil
header('Location:../api/index.php');
exit;
?>