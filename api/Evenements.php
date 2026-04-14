<?php
session_start();
require_once '../config/db.php';

$isLoggedIn = isset($_SESSION['user_id']);
$isOrganisateur = isset($_SESSION['role']) && $_SESSION['role'] === 'admin_club';
$userName = $_SESSION['prenom'] ?? '';
$sql = "SELECT e.*, 
               c.nom as club_nom, 
               c.acronyme as club_acronyme,
               u.prenom as createur_prenom, 
               u.nom as createur_nom,
               (SELECT COUNT(*) FROM inscriptions WHERE evenement_id = e.id) as nb_inscrits
        FROM evenements e
        JOIN clubs c ON e.club_id = c.id
        JOIN utilisateurs u ON e.createur_id = u.id
        WHERE e.date >= CURDATE()
        ORDER BY e.date ASC, e.heure ASC";
$stmt = $pdo->query($sql);
$evenements = $stmt->fetchAll(PDO::FETCH_ASSOC);
$clubs_sql = "SELECT DISTINCT acronyme FROM clubs ORDER BY acronyme";
$clubs_stmt = $pdo->query($clubs_sql);
$clubs = $clubs_stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Événements</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Mulish:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../images/mp.png">
    <style>
        :root {
            --yellow: #ffd166;
            --primary: #ffd166;
            --secondary: #06d6a0;
            --dark: #073b4c;
            --light: #f8f9fa;
            --gray: #6c757d;
            --success: #06d6a0;
            --danger: #ef476f;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Mulish', sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }

        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
        header {
            background: linear-gradient(135deg, #548dc7 0%, #2c5aa0 100%);
            padding: 15px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
        }
        .logo-container {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .logo {
            width: 40px;
            height: 40px;
            border-radius: 8px;
        }

        .logo-container h1 {
            color: white;
            font-size: 24px;
            font-weight: 700;
        }

        .logo-container h1 span {
            color: var(--yellow);
        }

        .main-nav ul {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .main-nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .main-nav a:hover,
        .main-nav a.active {
            color: var(--yellow);
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
        }

        .user-name i {
            color: var(--yellow);
            background: rgba(255, 255, 255, 0.2);
            padding: 6px;
            border-radius: 50%;
        }

        .btn-logout {
            background: rgba(255,255,255,0.2);
            padding: 8px 16px;
            border-radius: 50px;
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s;
        }

        .btn-logout:hover {
            background: rgba(255,255,255,0.3);
        }

        .user-actions {
            display: flex;
            gap: 10px;
        }

        .btn-login, .btn-register {
            padding: 8px 16px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-login {
            color: white;
            border: 1px solid var(--yellow);
        }

        .btn-login:hover {
            background: rgba(255, 209, 102, 0.1);
        }

        .btn-register {
            background: var(--yellow);
            color: var(--dark);
        }

        .btn-register:hover {
            background: #ffc233;
        }
        main {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        .page-header {
            background: white;
            padding: 30px 40px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .page-header h2 {
            font-size: 32px;
            color:#2c5aa0;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
        }


        .page-header p {
            color: var(--gray);
            font-size: 16px;
            margin-left: 58px;
        }

        .search-filters {
            background: white;
            padding: 25px 30px;
            border-radius: 20px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }
        .search-box {
            position: relative;
            margin-bottom: 20px;
        }
        .search-box i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray);
            font-size: 18px;
        }
        .search-box input {
            width: 100%;
            padding: 15px 20px 15px 50px;
            border: 2px solid #e9ecef;
            border-radius: 14px;
            font-size: 16px;
            font-family: 'Mulish', sans-serif;
            transition: all 0.3s;
            background: #fafbfc;
        }
        .search-box input:focus {
            outline: none;
            border-color: #40609ccf;
            background: white;
            box-shadow: 0 0 0 4px rgba(255, 209, 102, 0.1);
        }

        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 10px 22px;
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 30px;
            cursor: pointer;
            font-weight: 600;
            font-family: 'Outfit', sans-serif;
            transition: all 0.3s;
            color: var(--gray);
            font-size: 14px;
        }

        .filter-btn:hover {
            border-color:#548dc7;
            background: white;
        }

        .filter-btn.active {
            background: #548dc7;
            color:white;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
        }

        .event-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            cursor: pointer;
            border: 1px solid rgba(0,0,0,0.05);
        }

        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        .event-image {
            height: 150px;
            background:url("../images/hg.jpg");
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .event-image i {
            font-size: 48px;
            opacity: 0.25;
        }

        .event-club-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: rgba(255,255,255,0.25);
            backdrop-filter: blur(10px);
            padding: 6px 16px;
            border-radius: 30px;
            color: white;
            font-weight: 700;
            font-size: 13px;
            letter-spacing: 0.5px;
            border: 1px solid rgba(255,255,255,0.2);
        }

        .event-content {
            padding: 22px;
        }

        .event-title {
            font-size: 20px;
            font-weight: 700;
                background: linear-gradient(130deg, rgba(60, 69, 74, 0.74), #548dc7);
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
            bottom: 18px;
            line-height: 1.4;
            font-family: 'Outfit', sans-serif;
        }

        .event-info {
            margin-bottom: 18px;
            margin-top:6px;
        }

        .event-info-item {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 12px;
            color: var(--gray);
            font-size: 14px;
        }

        .event-info-item i {
            width: 20px;
            color:#548dc7;
            font-size: 16px;
        }

        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 18px;
            border-top: 1px solid #f0f0f0;
        }

        .event-participants {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--gray);
            font-size: 13px;
            font-weight: 600;
        }

        .event-participants i {
            color:#40609ccf;
        }

        .btn-details {
            padding: 10px 22px;
            background: #f3e7cf; 
            border: none;
            border-radius: 30px;
            color: var(--dark);
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Outfit', sans-serif;
            font-size: 14px;
        }

        .btn-details:hover {
            background: #f3e7cf;
            transform: scale(1.02);
        }

        .no-events {
            text-align: center;
            padding: 80px 20px;
            color: var(--gray);
            grid-column: 1 / -1;
            background: white;
            border-radius: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
        }

        .no-events i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.3;
            color: var(--yellow);
        }

        .no-events h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--dark);
        }

        /* Modal - Style assorti */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            backdrop-filter: blur(5px);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 24px;
            max-width: 600px;
            width: 90%;
            max-height: 85vh;
            overflow-y: auto;
            box-shadow: 0 25px 50px rgba(0,0,0,0.15);
        }

        .modal-header {
            padding: 25px 25px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            background: white;
            z-index: 1;
        }

        .modal-header h3 {
            font-size: 24px;
            color: var(--dark);
            font-family: 'Outfit', sans-serif;
        }

        .modal-close {
            background: #f8f9fa;
            border: none;
            width: 36px;
            height: 36px;
            border-radius: 12px;
            font-size: 22px;
            cursor: pointer;
            color: var(--gray);
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-close:hover {
            background: var(--danger);
            color: white;
        }

        .modal-body {
            padding: 25px;
        }

        .modal-section {
            margin-bottom: 28px;
        }

        .modal-section h4 {
            color: var(--dark);
            margin-bottom: 18px;
            font-size: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .modal-section h4 i {
            color: var(--yellow);
            background: rgba(255, 209, 102, 0.15);
            padding: 8px;
            border-radius: 10px;
        }

        .btn-inscription {
            width: 100%;
            padding: 16px;
            background: var(--success);
            border: none;
            border-radius: 16px;
            color: white;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            font-family: 'Outfit', sans-serif;
        }

        .btn-inscription:hover {
            background: #05b880;
            transform: scale(1.01);
        }

        .btn-inscription:disabled {
            background: var(--gray);
            cursor: not-allowed;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-nav ul {
                gap: 15px;
            }
            
            .main-nav a span {
                display: none;
            }
            
            .main-nav a i {
                font-size: 20px;
            }
            
            .events-grid {
                grid-template-columns: 1fr;
            }
            
            .page-header {
                padding: 20px 25px;
            }
            
            .page-header h2 {
                font-size: 24px;
            }
            
            .page-header p {
                margin-left: 50px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-container">
            <div class="logo-container" onclick="window.location.href='index.php'">
                <img src="../images/mp.png" alt="EventHub Logo" class="logo">
                <h1>Event<span>Hub</span></h1>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i><span> Accueil</span></a></li>
                    <li><a href="Evenements.php" class="active"><i class="fas fa-calendar-alt"></i><span> Événements</span></a></li>
                    <?php if ($isOrganisateur): ?>
                    <li><a href="organiser.php"><i class="fas fa-plus-circle"></i><span> Organiser</span></a></li>
                    <?php endif; ?>
                    <li><a href="contact.php"><i class="fas fa-envelope"></i><span> Contact</span></a></li>
                    <?php if ($isLoggedIn): ?>
                    <li><a href="Profil.php"><i class="fas fa-user-circle"></i><span> Mon Profil</span></a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php if ($isLoggedIn): ?>
                <div class="user-info">
                    <span class="user-name"><i class="fas fa-user"></i> <?php echo htmlspecialchars($userName); ?></span>
                    <a href="logout.php" class="btn-logout">Déconnexion</a>
                </div>
            <?php else: ?>
                <div class="user-actions">
                    <a href="../HTML/login.html" class="btn-login"><i class="fas fa-user"></i> Connexion</a>
                    <a href="../HTML/register.html" class="btn-register">S'inscrire</a>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <div class="page-header">
            <h2><i class="fas fa-calendar-alt"></i> Événements à venir</h2>
            <p>Découvre et participe aux événements étudiants</p>
        </div>

        <div class="search-filters">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Rechercher un événement...">
            </div>
            <div class="filter-buttons" id="filterButtons">
                <button class="filter-btn active" data-club="">Tous</button>
                <?php foreach ($clubs as $club): ?>
                <button class="filter-btn" data-club="<?= htmlspecialchars($club) ?>"><?= htmlspecialchars($club) ?></button>
                <?php endforeach; ?>
            </div>
        </div>

        <div id="eventsGrid" class="events-grid">
            <?php if (empty($evenements)): ?>
                <div class="no-events">
                    <i class="fas fa-calendar-times"></i>
                    <h3>Aucun événement à venir</h3>
                </div>
            <?php else: ?>
                <?php foreach ($evenements as $event): ?>
                    <?php 
                        $date = new DateTime($event['date']);
                        $heure = substr($event['heure'], 0, 5);
                        $places_restantes = $event['places_total'] - $event['nb_inscrits'];
                        $isFull = $places_restantes <= 0;
                    ?>
                    <div class="event-card" data-event-id="<?= $event['id'] ?>" data-club="<?= htmlspecialchars($event['club_acronyme']) ?>">
                        <div class="event-image">
                            <i class="fas fa-calendar-star"></i>
                            <span class="event-club-badge"><?= htmlspecialchars($event['club_acronyme']) ?></span>
                        </div>
                        <div class="event-content">
                            <h3 class="event-title"><?= htmlspecialchars($event['titre']) ?></h3>
                            <div class="event-info">
                                <div class="event-info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?= htmlspecialchars($event['lieu']) ?></span>
                                </div>
                                <div class="event-info-item">
                                    <i class="fas fa-clock"></i>
                                    <span><?= $date->format('d/m/Y') ?> • <?= $heure ?></span>
                                </div>
                                <div class="event-info-item">
                                    <i class="fas fa-user-circle"></i>
                                    <span><?= htmlspecialchars($event['createur_prenom'] . ' ' . $event['createur_nom']) ?></span>
                                </div>
                            </div>
                            <div class="event-footer">
                                <div class="event-participants">
                                    <i class="fas fa-users"></i>
                                    <span><?= $event['nb_inscrits'] ?> / <?= $event['places_total'] ?></span>
                                </div>
                                <button class="btn-details" onclick="showEventDetails(<?= $event['id'] ?>)">
                                    Voir détails <i class="fas fa-arrow-right" style="margin-left: 5px; font-size: 12px;"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="modalTitle"></h3>
                <button class="modal-close" onclick="closeModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" id="modalBody"></div>
        </div>
    </div>

    <script>
        const eventsData = <?php echo json_encode($evenements); ?>;
        
        function showEventDetails(eventId) {
            const event = eventsData.find(e => e.id == eventId);
            if (!event) return;
            
            const modal = document.getElementById('eventModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalBody = document.getElementById('modalBody');
            
            const date = new Date(event.date);
            const placesRestantes = event.places_total - event.nb_inscrits;
            const isFull = placesRestantes <= 0;
            
            modalTitle.textContent = event.titre;
            
            modalBody.innerHTML = `
                <div class="modal-section">
                    <h4><i class="fas fa-info-circle"></i> Description</h4>
                    <p style="color: var(--gray); line-height: 1.6;">${event.description || 'Aucune description fournie.'}</p>
                </div>
                
                <div class="modal-section">
                    <h4><i class="fas fa-calendar"></i> Date et heure</h4>
                    <div class="event-info-item">
                        <i class="fas fa-calendar-day"></i>
                        <span>${date.toLocaleDateString('fr-FR', {weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'})}</span>
                    </div>
                    <div class="event-info-item">
                        <i class="fas fa-clock"></i>
                        <span>${event.heure.substring(0, 5)}</span>
                    </div>
                </div>
                
                <div class="modal-section">
                    <h4><i class="fas fa-location-dot"></i> Lieu</h4>
                    <div class="event-info-item">
                        <i class="fas fa-map-pin"></i>
                        <span>${event.lieu}</span>
                    </div>
                </div>
                
                <div class="modal-section">
                    <h4><i class="fas fa-building"></i> Organisateur</h4>
                    <div class="event-info-item">
                        <i class="fas fa-users-cog"></i>
                        <span>${event.club_nom} (${event.club_acronyme})</span>
                    </div>
                    <div class="event-info-item">
                        <i class="fas fa-user-tie"></i>
                        <span>${event.createur_prenom} ${event.createur_nom}</span>
                    </div>
                </div>
                
                <div class="modal-section">
                    <h4><i class="fas fa-ticket"></i> Places disponibles</h4>
                    <div class="event-info-item">
                        <i class="fas fa-user-group"></i>
                        <span><strong>${event.nb_inscrits}</strong> participants / <strong>${event.places_total}</strong> places</span>
                    </div>
                    <div style="margin-top: 15px;">
                        <div style="background: #e9ecef; height: 8px; border-radius: 10px; overflow: hidden;">
                            <div style="width: ${(event.nb_inscrits / event.places_total) * 100}%; height: 100%; background: ${isFull ? '#ef476f' : '#06d6a0'}; border-radius: 10px; transition: width 0.3s;"></div>
                        </div>
                    </div>
                </div>
                
                <?php if ($isLoggedIn): ?>
                <button class="btn-inscription" onclick="inscrireEvent(${event.id})" ${isFull ? 'disabled' : ''}>
                    ${isFull ? '<i class="fas fa-ban"></i> Complet' : '<i class="fas fa-check-circle"></i> S\'inscrire à cet événement'}
                </button>
                <?php else: ?>
                <button class="btn-inscription" onclick="window.location.href='../HTML/login.html'">
                    <i class="fas fa-sign-in-alt"></i> Connectez-vous pour vous inscrire
                </button>
                <?php endif; ?>
            `;
            
            modal.classList.add('active');
        }
        
        function closeModal() {
            document.getElementById('eventModal').classList.remove('active');
        }
        
        function inscrireEvent(eventId) {
            fetch('registre_event.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ event_id: eventId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Inscription réussie !');
                    location.reload();
                } else {
                    alert(data.error);
                }
            })
            .catch(error => alert('Erreur lors de l\'inscription'));
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const filterBtns = document.querySelectorAll('.filter-btn');
            const eventCards = document.querySelectorAll('.event-card');
            
            let currentClub = '';
            let searchTerm = '';
            
            function filterEvents() {
                eventCards.forEach(card => {
                    const club = card.dataset.club;
                    const title = card.querySelector('.event-title').textContent.toLowerCase();
                    const location = card.querySelector('.event-info-item span').textContent.toLowerCase();
                    
                    const matchesClub = !currentClub || club === currentClub;
                    const matchesSearch = !searchTerm || title.includes(searchTerm) || location.includes(searchTerm);
                    
                    card.style.display = matchesClub && matchesSearch ? 'block' : 'none';
                });
            }
            
            filterBtns.forEach(btn => {
                btn.addEventListener('click', function() {
                    filterBtns.forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentClub = this.dataset.club;
                    filterEvents();
                });
            });
            
            searchInput.addEventListener('input', function() {
                searchTerm = this.value.toLowerCase();
                filterEvents();
            });
            
            document.getElementById('eventModal').addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
        });
    </script>
</body>
</html>