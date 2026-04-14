<?php
session_start();
$userName = $_SESSION['prenom'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Organiser</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Mulish:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/organiser.css">
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
                    <li><a href="../api/organiser.php" class="active"><i class="fas fa-plus-circle"></i> Organiser</a></li>
                    <li><a href="../api/contact.php"><i class="fas fa-envelope"></i> Contact</a></li>
                    <li><a href="../api/Profil.php"><i class="fas fa-user-circle"></i> Mon Profil</a></li>
                </ul>
            </nav>
            <div class="user-info">
                <span class="user-name"><i class="fas fa-user"></i><?php echo htmlspecialchars($userName); ?></span>
                <a href="../api/logout.php" class="btn-logout">Déconnexion</a>
            </div>
        </div>
    </header>
    <main>
        <div class="page">
            <div class="organizer-header">
                <h2><i class="fas fa-calendar-plus"></i> Créer un événement</h2>
                <p>Remplis les informations ci-dessous</p>
            </div>
            <div class="form-card">
                <form id="createEventForm" action="../api/create_event.php" method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-tag"></i> Titre de l'événement *</label>
                        <input type="text" id="title" name="titre" placeholder="Ex: Hackathon 2026" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-calendar"></i> Date *</label>
                            <input type="date" id="date" name="date" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-clock"></i> Heure *</label>
                            <input type="time" id="time" name="heure" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-map-marker-alt"></i> Lieu *</label>
                        <input type="text" id="location" name="lieu" placeholder="Salle, amphithéâtre..." required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-ticket-alt"></i> Nombre de places *</label>
                            <input type="number" id="places" name="places_total" placeholder="50" required>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-building"></i> Club *</label>
                            <select name="club_id" id="club" required>
                                <option value="">Sélectionne un club</option>
                                <option value="1">ATIA - Intelligence Artificielle</option>
                                <option value="2">IEEE - Génie Électrique</option>
                                <option value="3">CPU - Club Robotique</option>
                                <option value="4">MTC - Microsoft Tech Club</option>
                            </select>
                        </div>
                    </div>       
                    <div class="form-group">
                        <label><i class="fas fa-align-left"></i> Description</label>
                        <textarea id="description" rows="3" name="description" placeholder="Décris ton événement..."></textarea>
                    </div>                  
                    <button type="submit" class="btn-create" id="submitBtn">
                        <i class="fas fa-check-circle"></i> Créer l'événement
                    </button>
                     <div id="successMessage" class="message" style="display: none;">
                      <i class="fas fa-check-circle"></i>Événement créé avec succès !
                    </div>
                    <div id="errorMessage" class="message" style="display: none;" >Veuillez remplir tous les champs obligatoires !</div>
                </form>
            </div>
        </div>
    </main>
    <script>
    console.log('=== Test chargement script ===');
</script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('✅ DOM chargé - Script intégré');
            
            const form = document.getElementById('createEventForm');
            const btn = document.getElementById('submitBtn');
            const successMessage = document.getElementById('successMessage');
            const errorMessage = document.getElementById('errorMessage');
            
            console.log('Formulaire trouvé:', !!form);
            console.log('Bouton trouvé:', !!btn);
            
            if (!form || !btn) {
                console.error('❌ Éléments du formulaire non trouvés !');
                return;
            }
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                console.log('🚀 Soumission du formulaire');
                
                // Récupération des valeurs
                const titre = document.getElementById('title').value.trim();
                const date = document.getElementById('date').value;
                const heure = document.getElementById('time').value;
                const lieu = document.getElementById('location').value.trim();
                const places = document.getElementById('places').value;
                const club = document.getElementById('club').value;
                const description = document.getElementById('description').value.trim();
                
                console.log('Données:', { titre, date, heure, lieu, places, club, description });
                
                // Validation
                if (!titre || !date || !heure || !lieu || !places || !club) {
                    console.log('❌ Validation échouée');
                    errorMessage.textContent = 'Tous les champs obligatoires doivent être remplis';
                    errorMessage.style.display = 'flex';
                    setTimeout(() => {
                        errorMessage.style.display = 'none';
                    }, 3000);
                    return;
                }
                
                // Désactiver le bouton
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
                
                // Créer FormData avec les BONS noms de champs
                const formData = new FormData();
                formData.append('titre', titre);
                formData.append('date', date);
                formData.append('heure', heure);
                formData.append('lieu', lieu);
                formData.append('places_total', places);
                formData.append('club_id', club);
                formData.append('description', description);
                
                console.log('FormData créé');
                
                try {
                    const response = await fetch('create_event.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    console.log('Réponse reçue, status:', response.status);
                    const result = await response.json();
                    console.log('Résultat:', result);
                    
                    if (result.success) {
                        console.log('✅ SUCCÈS !');
                        btn.classList.add('success');
                        btn.innerHTML = '<i class="fas fa-check-circle"></i> Événement créé !';
                        successMessage.style.display = 'flex';
                        
                        setTimeout(() => {
                            form.reset();
                            btn.classList.remove('success');
                            btn.innerHTML = '<i class="fas fa-check-circle"></i> Créer l\'événement';
                            btn.disabled = false;
                            successMessage.style.display = 'none';
                            
                            const today = new Date().toISOString().split('T')[0];
                            document.getElementById('date').value = today;
                        }, 2000);
                    } else {
                        throw new Error(result.error || 'Erreur inconnue');
                    }
                } catch (error) {
                    console.error('💥 Erreur:', error);
                    errorMessage.textContent = error.message || 'Erreur lors de la création';
                    errorMessage.style.display = 'flex';
                    
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle"></i> Créer l\'événement';
                    
                    setTimeout(() => {
                        errorMessage.style.display = 'none';
                    }, 5000);
                }
            });
            
            // Initialisation de la date
            const today = new Date().toISOString().split('T')[0];
            const dateInput = document.getElementById('date');
            if (dateInput) {
                dateInput.value = today;
                console.log('✅ Date initialisée:', today);
            }
        });
    </script>
</body>
</html>
</script>
</body>
</html>