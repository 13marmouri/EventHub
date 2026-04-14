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
    <title>EventHub-Accueil</title>
    <link rel="stylesheet" href="../CSS/Acceuil.css">
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Mulish:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../images/mp.png">
</head>
<style>
     :root{ --yellow: #ffd166;}
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
            <div class="logo-container">
                <img src="../images/mp.png" alt="EventHub Logo" class="logo">
                <h1>Event<span>Hub</span></h1>
            </div> 
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Accueil</a></li>
                    <li><a href="../api/Evenements.php"><i class="fas fa-calendar-alt"></i> Événements</a></li>
                    <?php if ($isOrganisateur): ?>
                    <li><a href="../api/organiser.php"><i class="fas fa-plus-circle"></i> Organiser</a></li>
                    <?php endif; ?>
                    <li><a href="../api/contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    <?php if ($isLoggedIn): ?>
                    <li><a href="../api/Profil.php"><i class="fas fa-user-circle"></i> Mon Profil</a></li>
                    <?php endif; ?>
                </ul>
            </nav>  
           <?php if ($isLoggedIn): ?>
           <div class="user-info">
            <span class="user-name">
              <i class="fas fa-user-circle"></i>
            <?php echo htmlspecialchars($userName); ?>
            </span>
            <a href="../api/logout.php" class="btn-logout">Déconnexion</a>
            </div>
            <?php else: ?>
             <div class="user-actions">
             <a href="../HTML/login.html" class="btn-login">Connexion</a>
             <a href="../HTML/register.html" class="btn-register">Inscription</a>
             </div>
            <?php endif; ?>
        </div>
    </header>
    <div class="page">
    <section class="hero">
        <div class="hero-content">
            <h2>Bienvenue sur <span>EventHub</span></h2>
            <div class="phrase-sub">
    <i class="fas fa-users" style="color: rgba(18, 25, 106, 0.7); font-size: 16px;"></i>
    <span>Entre étudiants : on organise, on participe, on vit les events.</span>
</div>
<br>
<br>
            <div class="hero-buttons">
                <a href="#evenements" class="btn-primary" id="btn-event">Découvrir les événements</a>
            </div>
        </div>
        <div class="slider-container">
            <div class="slider">
                <div class="slide active">
                    <img src="../images/hg.jpg" alt="Événement étudiant 1">
                    <div class="slide-overlay">
                        <h3>Conférences inspirantes</h3>
                        <p>Participez à des conférences avec des experts</p>
                    </div>
                </div>
                <div class="slide">
                    <img src="../images/jk.jpg" alt="Événement étudiant 2">
                    <div class="slide-overlay">
                        <h3>Ateliers pratiques</h3>
                        <p>Développez vos compétences avec nos ateliers</p>
                    </div>
                </div>
                <div class="slide">
                    <img src="../images/gf.jpg" alt="Événement étudiant 3">
                    <div class="slide-overlay">
                        <h3>Activités sociales</h3>
                        <p>Rencontrez d'autres étudiants lors d'événements conviviaux</p>
                    </div>
                </div>
            </div>  
            <div class="slider-controls">
                <button class="slider-btn prev-btn"><i class="fas fa-chevron-left"></i></button>
                <div class="slider-dots">
                    <span class="dot active"></span>
                    <span class="dot"></span>
                    <span class="dot"></span>
                </div>
                <button class="slider-btn next-btn"><i class="fas fa-chevron-right"></i></button>
            </div>
        </div>
    </section>
    <main>
        <section class="features">
            <div class="container">
                <h2 class="section-title">Pourquoi choisir EventHub ?</h2>
                <p class="section-subtitle">EventHub centralise toutes les activités étudiantes en un seul endroit et permet une inscription rapide et facile.</p>        
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <h3>Découvrir</h3>
                        <p>Trouvez facilement des événements qui vous intéressent grâce à notre système de filtres et de catégories.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <h3>Participer</h3>
                        <p>Inscrivez-vous en quelques clics aux événements et recevez des rappels pour ne rien manquer.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-bullhorn"></i>
                        </div>
                        <h3>Organiser</h3>
                        <p>Créez et gérez vos propres événements facilement pour partager vos passions avec la communauté.</p>
                    </div>
                </div>
            </div>
        </section>
        <!-- Section événements à venir -->
        <section class="upcoming-events" id="evenements">
            <div class="container">
                <h2 class="section-title">Événements à venir</h2>
                <p class="section-subtitle">Ne manquez pas les prochains événements étudiants</p>
                
                <div class="events-grid">
                    <div class="event-card">
                        <div class="event-image">
                            <img src="../images/PH1.jpg" alt="Atelier développement web">
                        </div>
                        <div class="event-info">
                            <h3>Atelier Développement Web</h3>
                            <p><i class="fas fa-map-marker-alt"></i> Amphi A, Bâtiment Informatique</p>
                            <p><i class="fas fa-clock"></i> 14h00 - 17h00</p>
                            <a href="#" class="btn-event">Voir détails</a>
                        </div>
                    </div>
                    
                    <div class="event-card">
                        <div class="event-image">
                            <img src="../images/PH2.jpg" alt="Conférence IA">
                        </div>
                        <div class="event-info">
                            <h3>Conférence sur l'Intelligence Artificielle</h3>
                            <p><i class="fas fa-map-marker-alt"></i> Grand Amphi, Bâtiment Principal</p>
                            <p><i class="fas fa-clock"></i> 10h00 - 12h30</p>
                            <a href="#" class="btn-event">Voir détails</a>
                        </div>
                    </div> 
                    <div class="event-card">
                        <div class="event-image">
                            <img src="../images/ph3.jpg" alt="Tournoi sportif">
                        </div>
                        <div class="event-info">
                            <h3>Tournoi Sportif Inter-filières</h3>
                            <p><i class="fas fa-map-marker-alt"></i> Complexe Sportif Universitaire</p>
                            <p><i class="fas fa-clock"></i> 09h00 - 18h00</p>
                            <a href="#" class="btn-event">Voir détails</a>
                        </div>
                    </div>
                </div>          
                <div class="events-button">
                    <a href="evenements.html" class="btn-primary">Voir tous les événements</a>
                </div>
            </div>
        </section> 
        <section class="testimonials">
            <div class="container">
                <h2 class="section-title">Ce que disent nos utilisateurs</h2>        
                <div class="testimonials-grid">
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"EventHub a transformé ma vie étudiante ! Je n'ai plus à chercher des événements sur différents sites, tout est centralisé."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="../images/nn.jpg" alt="Marie">
                            <div>
                                <h4>Marie D.</h4>
                                <span>Étudiante en Informatique</span>
                            </div>
                        </div>
                    </div>        
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"En tant que président de club, organiser des événements est devenu beaucoup plus simple avec EventHub."</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="../images/df.png" alt="Thomas">
                            <div>
                                <h4>Thomas L.</h4>
                                <span>Président du Club Robotique</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="testimonial-card">
                        <div class="testimonial-content">
                            <p>"J'ai rencontré des personnes incroyables lors d'événements trouvés sur EventHub. Vraiment essentiel pour la vie étudiante !"</p>
                        </div>
                        <div class="testimonial-author">
                            <img src="../images/kj.png" alt="Sophie">
                            <div>
                                <h4>Sophie M.</h4>
                                <span>Étudiante en Design</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <div class="footer-logo">
                    <img src="../images/mp.png" alt="EventHub Logo">
                    <h3>Event<span>Hub</span></h3>
                </div>
                <p>EventHub est une plateforme dédiée aux étudiants pour découvrir, organiser et participer aux événements universitaires.</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-linkedin"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Liens rapides</h4>
                <ul>
                    <li><a href="index.html">Accueil</a></li>
                    <li><a href="evenements.html">Événements</a></li>
                    <li><a href="organiser.html">Organiser un événement</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Catégories</h4>
                <ul>
                    <li><a href="#">Conférences</a></li>
                    <li><a href="#">Ateliers</a></li>
                    <li><a href="#">Activités sociales</a></li>
                    <li><a href="#">Formations</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contact</h4>
                <ul class="contact-info">
                    <li><i class="fas fa-map-marker-alt"></i>Institut superieure de mathématique et d'informatique,Monastir</li>
                    <li><i class="fas fa-envelope"></i> contact@eventhub.tn</li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; 2026 EventHub - Institut. Tous droits réservés.</p>
        </div>
    </footer>
    </div>
    <script src="../JS/Acceuil.js"></script>
</body>
</html>