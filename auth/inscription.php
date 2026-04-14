<?php
// inscription.php
session_start();
require_once '../config/db.php';

// Récupérer les données du formulaire
$prenom = $_POST['prenom'] ?? '';
$nom = $_POST['nom'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';
$filiere = $_POST['filiere'] ?? '';
$niveau = $_POST['niveau'] ?? '';

// Vérifications
if (empty($prenom) || empty($nom) || empty($email) || empty($password)) {
    header('Location: register.html?error=1');
    exit;
}

if ($password !== $confirm) {
    header('Location: register.html?error=2');
    exit;
}

if (strlen($password) < 6) {
    header('Location: register.html?error=3');
    exit;
}

// Vérifier si email existe déjà
$stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    header('Location:../HTML/register.html?error=4');
    exit;
}

// Hacher le mot de passe
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insérer dans la base
$stmt = $pdo->prepare("
    INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, filiere, niveau) 
    VALUES (?, ?, ?, ?, ?, ?)
");
$stmt->execute([$prenom, $nom, $email, $hash, $filiere, $niveau]);

// Créer la session
$_SESSION['user_id'] = $pdo->lastInsertId();
$_SESSION['prenom'] = $prenom;
$_SESSION['nom'] = $nom;
$_SESSION['email'] = $email;

// Rediriger vers l'accueil
header('Location: ../HTML/index.html');
exit;
?>