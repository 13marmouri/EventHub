<?php
session_start();
require_once '../config/db.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../HTML/login.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$isOrganisateur = isset($_SESSION['role']) && $_SESSION['role'] === 'admin_club';
$userName = $_SESSION['prenom'] ?? '';
$familyName = $_SESSION['nom'] ?? '';
$userEmail = $_SESSION['email'] ?? '';

// Récupérer les informations complètes de l'utilisateur
$sql_user = "SELECT * FROM utilisateurs WHERE id = :user_id";
$stmt_user = $pdo->prepare($sql_user);
$stmt_user->execute([':user_id' => $user_id]);
$user = $stmt_user->fetch(PDO::FETCH_ASSOC);

// Récupérer les événements auxquels l'utilisateur est inscrit
$sql_events = "SELECT e.*, 
                      c.acronyme as club_acronyme,
                      c.nom as club_nom,
                      i.date_inscription,
                      i.statut
               FROM inscriptions i
               JOIN evenements e ON i.evenement_id = e.id
               JOIN clubs c ON e.club_id = c.id
               WHERE i.utilisateur_id = :user_id
               ORDER BY e.date ASC, e.heure ASC";

$stmt_events = $pdo->prepare($sql_events);
$stmt_events->execute([':user_id' => $user_id]);
$mes_evenements = $stmt_events->fetchAll(PDO::FETCH_ASSOC);

// Séparer événements à venir et passés
$today = date('Y-m-d');
$events_a_venir = array_filter($mes_evenements, function($e) use ($today) {
    return $e['date'] >= $today;
});
$events_passes = array_filter($mes_evenements, function($e) use ($today) {
    return $e['date'] < $today;
});

// Statistiques
$nb_events_total = count($mes_evenements);
$nb_events_avenir = count($events_a_venir);
$nb_events_passes = count($events_passes);

// Calcul XP
$xp_total = $nb_events_passes * 100;
$niveau = floor($xp_total / 500) + 1;
$xp_niveau_actuel = $xp_total % 500;
$xp_prochain_niveau = 500;
$pourcentage_xp = ($xp_niveau_actuel / $xp_prochain_niveau) * 100;

// Clubs visités
$clubs_visites = array_unique(array_column($mes_evenements, 'club_acronyme'));
$nb_clubs = count($clubs_visites);

// Formater les événements pour JavaScript
$events_for_calendar = [];
foreach ($mes_evenements as $event) {
    $events_for_calendar[] = [
        'id' => $event['id'],
        'titre' => $event['titre'],
        'date' => $event['date'],
        'heure' => substr($event['heure'], 0, 5),
        'lieu' => $event['lieu'],
        'club' => $event['club_acronyme']
    ];
}

// Mois en français
$mois_fr = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
$jours_fr = ['LUN', 'MAR', 'MER', 'JEU', 'VEN', 'SAM', 'DIM'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - EventHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Mulish:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../images/mp.png">
    <link rel="stylesheet" href="../CSS/Profil.css">
</head>
<body>
<header>
    <div class="header-container">
        <a href="../api/index.php" class="logo-container">
            <img src="../images/mp.png" alt="EventHub Logo" class="logo">
            <h1>Event<span>Hub</span></h1>
        </a>
        <nav class="main-nav">
            <ul>
                <li><a href="../api/index.php"><i class="fas fa-home"></i> Accueil</a></li>
                <li><a href="../api/Evenements.php"><i class="fas fa-calendar-alt"></i> Événements</a></li>
                <?php if ($isOrganisateur): ?>
                <li><a href="../api/organiser.php"><i class="fas fa-plus-circle"></i> Organiser</a></li>
                <?php endif; ?>
                <li><a href="../api/contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                <li><a href="../api/Profil.php" class="active"><i class="fas fa-user-circle"></i> Mon Profil</a></li>
            </ul>
        </nav>
        <div class="user-info">
            <span class="user-name"><i class="fas fa-user"></i><?php echo htmlspecialchars($userName); ?></span>
            <a href="../api/logout.php" class="btn-logout">Déconnexion</a>
        </div>
    </div>
</header>

<main>
    <!-- Profile Hero -->
    <div class="profile-hero">
        <div class="avatar-wrapper">
            <div class="avatar-big"><?php echo strtoupper(substr($userName, 0, 1)); ?></div>
            <div class="avatar-level">Niv. <?php echo $niveau; ?></div>
        </div>
        <div class="profile-info">
            <h2><?php echo htmlspecialchars($familyName . ' ' . $userName); ?></h2>
            <div class="filiere"><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($user['filiere'] ?? 'Informatique'); ?> — <?php echo htmlspecialchars($user['niveau'] ?? 'Licence 3'); ?></div>
            <div class="profile-tags">
                <span class="tag"><i class="fas fa-university"></i> <?php echo htmlspecialchars($user['institut'] ?? 'ISIMM'); ?></span>
                <span class="tag"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($userEmail); ?></span>
                <span class="tag"><i class="fas fa-calendar-check"></i> <?php echo $nb_events_total; ?> événement(s)</span>
            </div>
        </div>
        <div class="profile-xp">
            <div class="xp-circle">
                <div class="xp-number" id="xpNumber"><?php echo $xp_total; ?></div>
                <div class="xp-label">XP</div>
            </div>
            <div class="xp-bar-container">
                <div class="xp-bar-label">→ Niveau <?php echo $niveau + 1; ?> : <?php echo $xp_niveau_actuel; ?>/<?php echo $xp_prochain_niveau; ?> XP</div>
                <div class="xp-bar">
                    <div class="xp-bar-fill" id="xpFill" style="width: <?php echo $pourcentage_xp; ?>%;"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="profile-grid">
        <!-- Informations personnelles -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-user-circle"></i> Informations personnelles</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Prénom</div>
                    <div class="info-value"><i class="fas fa-user"></i> <?php echo htmlspecialchars($userName); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Nom</div>
                    <div class="info-value"><i class="fas fa-user"></i> <?php echo htmlspecialchars($familyName); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email</div>
                    <div class="info-value"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($userEmail); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Filière</div>
                    <div class="info-value"><i class="fas fa-laptop-code"></i> <?php echo htmlspecialchars($user['filiere'] ?? 'Informatique'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Niveau</div>
                    <div class="info-value"><i class="fas fa-layer-group"></i> <?php echo htmlspecialchars($user['niveau'] ?? 'Licence 3'); ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Institut</div>
                    <div class="info-value"><i class="fas fa-university"></i> <?php echo htmlspecialchars($user['institut'] ?? 'ISIMM'); ?></div>
                </div>
            </div>
            <button class="btn-edit" onclick="showToast('Modification bientôt disponible !')">
                <i class="fas fa-pen"></i> Modifier mon profil
            </button>
        </div>

        <!-- Badges -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-medal"></i> Mes badges</div>
            <div class="badges-grid">
                <?php
                $badges = [
                    ['name' => 'Premier Pas', 'icon' => 'fa-flag', 'desc' => '1er événement rejoint', 'unlocked' => $nb_events_total >= 1],
                    ['name' => 'Régulier', 'icon' => 'fa-fire', 'desc' => '5 événements suivis', 'unlocked' => $nb_events_passes >= 5],
                    ['name' => 'Explorateur', 'icon' => 'fa-compass', 'desc' => '3 clubs différents', 'unlocked' => $nb_clubs >= 3],
                    ['name' => 'Actif', 'icon' => 'fa-star', 'desc' => '3 événements à venir', 'unlocked' => $nb_events_avenir >= 3],
                    ['name' => 'Tech Fan', 'icon' => 'fa-microchip', 'desc' => 'Événement ATIA/IEEE', 'unlocked' => in_array('ATIA', $clubs_visites) || in_array('IEEE', $clubs_visites)],
                    ['name' => 'Organisateur', 'icon' => 'fa-calendar-plus', 'desc' => 'Crée ton 1er event', 'unlocked' => $isOrganisateur],
                    ['name' => 'Champion', 'icon' => 'fa-crown', 'desc' => '10 événements suivis', 'unlocked' => $nb_events_passes >= 10],
                    ['name' => 'Légende', 'icon' => 'fa-trophy', 'desc' => 'Niveau 10 atteint', 'unlocked' => $niveau >= 10],
                    ['name' => 'Ambassadeur', 'icon' => 'fa-share-alt', 'desc' => '3 partages', 'unlocked' => false],
                ];
                
                foreach ($badges as $badge):
                    $unlocked = $badge['unlocked'];
                ?>
                <div class="badge-item <?php echo $unlocked ? 'unlocked' : 'locked'; ?>" onclick="showToast('Badge : <?php echo $badge['name']; ?> — <?php echo $badge['desc']; ?>')">
                    <?php if ($unlocked): ?>
                    <div class="badge-unlock-tag"><i class="fas fa-check"></i></div>
                    <?php endif; ?>
                    <div class="badge-icon"><i class="fas <?php echo $badge['icon']; ?>"></i></div>
                    <div class="badge-name"><?php echo $badge['name']; ?></div>
                    <div class="badge-desc"><?php echo $badge['desc']; ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Calendrier + Mes événements -->
    <div class="profile-grid">
        <!-- Calendrier -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-calendar-alt"></i> Calendrier de mes événements</div>
            <div class="calendar-wrapper">
                <div class="calendar-nav">
                    <button class="cal-btn" onclick="changeMonth(-1)"><i class="fas fa-chevron-left"></i></button>
                    <div class="cal-month" id="calMonth">Mars 2026</div>
                    <button class="cal-btn" onclick="changeMonth(1)"><i class="fas fa-chevron-right"></i></button>
                </div>
                <div class="calendar-grid" id="calGrid">
                    <!-- Généré par JS -->
                </div>
            </div>
        </div>

        <!-- Liste des événements avec onglets -->
        <div class="section-card">
            <div class="section-title"><i class="fas fa-list-alt"></i> Mes événements</div>
            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('upcoming', this)"><i class="fas fa-clock"></i> À venir (<?php echo $nb_events_avenir; ?>)</button>
                <button class="tab-btn" onclick="switchTab('past', this)"><i class="fas fa-history"></i> Passés (<?php echo $nb_events_passes; ?>)</button>
            </div>

            <!-- Événements à venir -->
            <div class="tab-content active" id="tab-upcoming">
                <div class="events-list">
                    <?php if (empty($events_a_venir)): ?>
                        <div style="text-align: center; padding: 30px; color: var(--text-light);">
                            <i class="fas fa-calendar" style="font-size: 36px; margin-bottom: 10px; opacity: 0.5;"></i>
                            <p>Aucun événement à venir</p>
                            <a href="Evenements.php" style="color: var(--blue-mid); text-decoration: none; font-weight: 600;">Découvrir des événements →</a>
                        </div>
                    <?php else: ?>
                        <?php foreach ($events_a_venir as $event): 
                            $date = new DateTime($event['date']);
                            $jour = $date->format('d');
                            $mois = $mois_fr[$date->format('n') - 1];
                        ?>
                        <div class="event-item" onclick="showToast('📋 <?php echo htmlspecialchars($event['titre']); ?> — <?php echo htmlspecialchars($event['club_acronyme']); ?>')">
                            <div class="event-date-box">
                                <div class="event-day"><?php echo $jour; ?></div>
                                <div class="event-month"><?php echo substr($mois, 0, 3); ?></div>
                            </div>
                            <div class="event-details">
                                <div class="event-name"><?php echo htmlspecialchars($event['titre']); ?></div>
                                <div class="event-meta-small">
                                    <span><i class="fas fa-clock"></i> <?php echo substr($event['heure'], 0, 5); ?></span>
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['lieu']); ?></span>
                                </div>
                            </div>
                            <div class="event-club-badge"><?php echo htmlspecialchars($event['club_acronyme']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Événements passés -->
            <div class="tab-content" id="tab-past">
                <div class="events-list">
                    <?php if (empty($events_passes)): ?>
                        <div style="text-align: center; padding: 30px; color: var(--text-light);">
                            <i class="fas fa-history" style="font-size: 36px; margin-bottom: 10px; opacity: 0.5;"></i>
                            <p>Aucun événement passé</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($events_passes as $event): 
                            $date = new DateTime($event['date']);
                            $jour = $date->format('d');
                            $mois = $mois_fr[$date->format('n') - 1];
                        ?>
                        <div class="event-item" style="opacity:0.75;" onclick="showToast('Événement terminé — <?php echo htmlspecialchars($event['titre']); ?>')">
                            <div class="event-date-box" style="background: linear-gradient(135deg, #888, #aaa);">
                                <div class="event-day"><?php echo $jour; ?></div>
                                <div class="event-month"><?php echo substr($mois, 0, 3); ?></div>
                            </div>
                            <div class="event-details">
                                <div class="event-name"><?php echo htmlspecialchars($event['titre']); ?></div>
                                <div class="event-meta-small">
                                    <span><i class="fas fa-check-circle" style="color: var(--green);"></i> Complété</span>
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($event['lieu']); ?></span>
                                </div>
                            </div>
                            <div class="event-club-badge"><?php echo htmlspecialchars($event['club_acronyme']); ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Challenge du mois -->
    <div class="challenge-card">
        <h3><i class="fas fa-trophy"></i> Challenge du mois</h3>
        <p>Participe à <strong>3 événements</strong> ce mois pour débloquer le badge <strong>"Super Actif"</strong> !</p>
        <div class="challenge-progress">
            <div class="challenge-fill" style="width: <?php echo min(($nb_events_avenir / 3) * 100, 100); ?>%;"></div>
        </div>
        <div class="challenge-label"><?php echo $nb_events_avenir; ?> / 3 événements</div>
        <button style="margin-top: 15px; background: var(--yellow); border: none; padding: 10px 18px; border-radius: 12px; font-weight: 700; color: var(--blue-dark); cursor: pointer; transition: all 0.3s;" onclick="window.location.href='Evenements.php'">
            <i class="fas fa-arrow-right"></i> Voir les événements
        </button>
    </div>
</main>

<script>
    // Données des événements depuis PHP
    const userEvents = <?php echo json_encode($events_for_calendar); ?>;
    const moisFR = <?php echo json_encode($mois_fr); ?>;
    const joursFR = <?php echo json_encode($jours_fr); ?>;
    
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    
    // Fonction pour vérifier si une date a un événement
    function hasEventOnDate(year, month, day) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        return userEvents.some(event => event.date === dateStr);
    }
    
    // Générer le calendrier
    function renderCalendar() {
        const calGrid = document.getElementById('calGrid');
        const calMonth = document.getElementById('calMonth');
        
        // Mettre à jour le titre du mois
        calMonth.textContent = `${moisFR[currentMonth]} ${currentYear}`;
        
        // Vider la grille
        calGrid.innerHTML = '';
        
        // Ajouter les jours de la semaine
        joursFR.forEach(jour => {
            const dayName = document.createElement('div');
            dayName.className = 'cal-day-name';
            dayName.textContent = jour;
            calGrid.appendChild(dayName);
        });
        
        // Premier jour du mois
        const firstDay = new Date(currentYear, currentMonth, 1);
        let startDay = firstDay.getDay(); // 0 = Dimanche
        startDay = startDay === 0 ? 6 : startDay - 1; // Ajuster pour que Lundi = 0
        
        // Nombre de jours dans le mois
        const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
        
        // Ajouter les cases vides
        for (let i = 0; i < startDay; i++) {
            const emptyDay = document.createElement('div');
            emptyDay.className = 'cal-day empty';
            calGrid.appendChild(emptyDay);
        }
        
        // Date d'aujourd'hui
        const today = new Date();
        const isCurrentMonth = today.getMonth() === currentMonth && today.getFullYear() === currentYear;
        
        // Ajouter les jours du mois
        for (let day = 1; day <= daysInMonth; day++) {
            const dayEl = document.createElement('div');
            dayEl.className = 'cal-day';
            
            // Vérifier si c'est aujourd'hui
            if (isCurrentMonth && day === today.getDate()) {
                dayEl.classList.add('today');
            }
            
            // Vérifier s'il y a un événement
            if (hasEventOnDate(currentYear, currentMonth, day)) {
                dayEl.classList.add('has-event');
                dayEl.onclick = () => showEventsForDate(currentYear, currentMonth, day);
            } else {
                dayEl.classList.add('normal');
            }
            
            dayEl.textContent = day;
            calGrid.appendChild(dayEl);
        }
    }
    
    // Afficher les événements d'une date
    function showEventsForDate(year, month, day) {
        const dateStr = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        const events = userEvents.filter(event => event.date === dateStr);
        
        if (events.length === 0) return;
        
        let message = `📅 Événements du ${day} ${moisFR[month].toLowerCase()} :\n\n`;
        events.forEach(event => {
            message += `• ${event.titre} (${event.club})\n  ${event.heure} - ${event.lieu}\n\n`;
        });
        
        showToast(message);
    }
    
    // Changer de mois
    function changeMonth(delta) {
        currentMonth += delta;
        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        } else if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }
        renderCalendar();
    }
    
    // Changer d'onglet
    function switchTab(tabName, btn) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
        
        document.getElementById(`tab-${tabName}`).classList.add('active');
        btn.classList.add('active');
    }
    
    // Afficher un toast
    function showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `<i class="fas fa-info-circle"></i> ${message}`;
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(50px)';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
    
    // Initialiser le calendrier au chargement
    document.addEventListener('DOMContentLoaded', function() {
        renderCalendar();
        
        // Animer les barres de progression
        setTimeout(() => {
            document.querySelectorAll('[data-width]').forEach(el => {
                el.style.width = el.dataset.width + '%';
            });
        }, 100);
    });
</script>
</body>
</html>