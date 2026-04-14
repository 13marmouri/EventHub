<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$isOrganisateur = isset($_SESSION['role']) && $_SESSION['role'] === 'admin_club';
$userName = $_SESSION['prenom'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Mulish:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <title>EventHub - Contacts des clubs</title>
    <link rel="shortcut icon" href="../images/mp.png">
    <link rel="stylesheet" href="../CSS/contact.css">
</head>
<style>
          :root{
         --yellow: #ffd166;
    }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            background: rgba(255, 255, 255, 0.15);
            padding: 5px 15px 5px 10px;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        .user-info:hover {
            background: rgba(255, 255, 255, 0.25);
        }
        .user-name {
            display: flex;
            align-items: center;
            gap: 8px;
            color: white;
            font-weight: 600;
            font-size: 14px;
            font-family: 'Outfit', sans-serif;
            padding: 6px 0;
        }
        .user-name i {
            font-size: 16px;
            color: var(--yellow);
            background: rgba(255, 255, 255, 0.2);
            padding: 6px;
            border-radius: 50%;
        }
        .user-name .fa-user-circle {
            font-size: 28px;
            color: var(--yellow);
            background: transparent;
            padding: 0;
        }
        .btn-logout {
            background: rgba(255,255,255,0.2);
            border: none;
            padding: 8px 16px;
            border-radius: 50px;
            color: white;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
            font-family: 'Outfit', sans-serif;
            text-decoration:none;
        }

        .btn-logout:hover { background: rgba(255,255,255,0.3); }   
</style>
<body>
    <header>
        <div class="header-container">
            <div class="logo-container" onclick="window.location.href='index.html'">
                <img src="../images/mp.png" alt="EventHub Logo" class="logo">
                <h1>Event<span>Hub</span></h1>
            </div>          
            <nav class="main-nav">
                <ul>
                    <li><a href="../api/index.php"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="../api/Evenements.php"><i class="fas fa-calendar-alt"></i> Événements</a></li>
                    <?php if ($isOrganisateur): ?>
                    <li><a href="../api/organiser.php"><i class="fas fa-plus-circle"></i> Organiser</a></li>
                    <?php endif; ?>
                    <li><a href="../api/contact.php" class="active"><i class="fas fa-envelope"></i> Contact</a></li>
                    <?php if ($isLoggedIn): ?>
                    <li><a href="../api/Profil.php"><i class="fas fa-user-circle"></i> Mon Profil</a></li>
                    <?php endif;?>
                </ul>
            </nav>
                <?php if ($isLoggedIn): ?>
                    <div class="user-actions">
                    <span class="user-name"><i class="fas fa-user"></i> <?php echo htmlspecialchars($userName); ?></span>
                    <a href="../api/logout.php" class="btn-logout">Déconnexion</a>
                    </div>
                <?php else: ?>
                    <div class="user-info">
                    <a href="../HTML/login.html" class="btn-login"><i class="fas fa-user"></i> Connexion</a>
                    <a href="../HTML/register.html" class="btn-register">S'inscrire</a>
                    </div>
                <?php endif; ?>
    </div>
    </header>
    <main>
        <div class="page">
            <div class="contact-header">
                <h2>Contacts des clubs</h2>
                <p>Rencontre les responsables des clubs</p>
            </div>
            <div class="stats-row">
                <div class="stat-badge">
                    <i class="fas fa-users"></i>
                    <span>4</span>
                    <small>clubs actifs</small>
                </div>
                <div class="stat-badge">
                    <i class="fas fa-user-graduate"></i>
                    <span>150+</span>
                    <small>membres totaux</small>
                </div>
                <div class="stat-badge">
                    <i class="fas fa-trophy"></i>
                    <span>5</span>
                    <small>compétitions gagnées</small>
                </div>
            </div>
            <div class="admins-grid">
                <div class="admin-card">
                    <div class="admin-avatar">
                        <i class="fas fa-brain"></i>
                    </div>
                    <div class="admin-info">
                        <div class="club-name">ATIA</div>
                        <span class="club-acronym"><i class="fas fa-microchip"></i> Intelligence Artificielle</span>
                        <p><i class="fas fa-user-circle"></i> <strong>Dr.naycen aloulou</strong> (Responsable)</p>
                        <p><i class="fas fa-user"></i> Organisateur : <strong>Sarah Mansouri</strong></p>
                        <p><i class="fas fa-envelope"></i>aloulou.naycen@gmail.com</p>
                        <p><i class="fas fa-phone"></i> +216 71 234 567</p>
                        <div class="club-description-text">
                            <i class="fas fa-info-circle"></i> IA, Machine Learning, Deep Learning, Data Science
                        </div>
                    </div>
                </div>
                <div class="admin-card">
                    <div class="admin-avatar">
                        <i class="fas fa-globe-americas"></i>
                    </div>
                    <div class="admin-info">
                        <div class="club-name">
                            IEEE
                            <span class="international-badge">
                                <i class="fas fa-globe"></i> International
                            </span>
                        </div>
                        <span class="club-acronym"><i class="fas fa-bolt"></i> Génie Électrique & Électronique</span>
                        <p><i class="fas fa-user-circle"></i> <strong>Pr. Mohamed Salah</strong> (Conseiller)</p>
                        <p><i class="fas fa-user"></i> Organisateur :<strong>Amira Ben Ali</strong></p>
                        <p><i class="fas fa-envelope"></i>sb-isimm@ieee.org</p>
                        <p><i class="fas fa-phone"></i> +216 56 109 414</p>
                        <div class="club-description-text">
                            <i class="fas fa-info-circle"></i> IEEE est la plus grande organisation professionnelle mondiale qui œuvre pour l'avancement de la technologie au service de l'humanité.
                        </div>
                    </div>
                </div>
                <div class="admin-card">
                    <div class="admin-avatar">
                        <i class="fas fa-robot"></i>
                    </div>
                    <div class="admin-info">
                        <div class="club-name">CPU</div>
                        <span class="club-acronym"><i class="fas fa-cogs"></i> Robotique</span>
                        <p><i class="fas fa-user-circle"></i> <strong>Dr. Leila Ben Hassen</strong> (Responsable)</p>
                        <p><i class="fas fa-user"></i> Organisateur : <strong>Youssef Ben Hassen</strong></p>
                        <p><i class="fas fa-envelope"></i> cpu-isimm.tn</p>
                        <p><i class="fas fa-phone"></i> +216 71 456 789</p>
                        <div class="club-description-text">
                            <i class="fas fa-info-circle"></i> Robotique, Arduino, Compétitions nationales, Prototypage, Conception mécanique
                        </div>
                    </div>
                </div>
                <div class="admin-card">
                    <div class="admin-avatar">
                        <i class="fab fa-microsoft"></i>
                    </div>
                    <div class="admin-info">
                        <div class="club-name">
                            MTC
                            <span class="international-badge">
                                <i class="fab fa-microsoft"></i> Microsoft Tech Club
                            </span>
                        </div>
                        <span class="club-acronym"><i class="fas fa-code"></i> Programmation Compétitive</span>
                        <p><i class="fas fa-user-circle"></i> <strong>Dr. Nour El Houda</strong> (Conseiller)</p>
                        <p><i class="fas fa-user"></i> Organisateur : <strong>Mohamed Ali Ben Salah</strong></p>
                        <p><i class="fas fa-envelope"></i>president.mtcisimm@gmail.com</p>
                        <p><i class="fas fa-phone"></i> +216 71 567 890</p>
                        <div class="club-description-text">
                            <i class="fas fa-info-circle"></i> MTC est le club de programmation compétitive. ACM ICPC, Algorithmes avancés, Préparation aux concours, Hackathons Microsoft.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</body>
</html>