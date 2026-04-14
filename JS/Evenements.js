// ========== VARIABLES GLOBALES ==========
let currentEvents = [];
let currentSearch = '';
let currentClub = '';

// ========== FONCTIONS UTILITAIRES ==========

// Formater la date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('fr-FR', { 
        day: 'numeric', 
        month: 'long', 
        year: 'numeric' 
    });
}

// Protéger le texte HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Vérifier si l'utilisateur est connecté
function isUserLoggedIn() {
    // Vérifie si l'utilisateur a une session (stockée dans localStorage ou sessionStorage)
    const user = localStorage.getItem('eventhub_user');
    return user !== null;
}

// Afficher une notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: ${type === 'success' ? '#10b981' : (type === 'error' ? '#ef4444' : '#548dc7')};
        color: white;
        padding: 12px 24px;
        border-radius: 50px;
        z-index: 1200;
        animation: slideIn 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    `;
    notification.innerHTML = `<i class="fas ${type === 'success' ? 'fa-check-circle' : (type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle')}"></i> ${message}`;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// ========== CHARGEMENT DES ÉVÉNEMENTS ==========

// Charger les événements depuis get_events.php
async function loadEvents() {
    const url = `api/get_events.php?search=${encodeURIComponent(currentSearch)}&club=${encodeURIComponent(currentClub)}`;
    
    try {
        const response = await fetch(url);
        const events = await response.json();
        currentEvents = events;
        displayEvents(events);
    } catch (error) {
        console.error('Erreur:', error);
        document.getElementById('eventsGrid').innerHTML = `
            <div class="no-events">
                <i class="fas fa-exclamation-circle"></i>
                <p>Erreur de chargement des événements</p>
            </div>
        `;
    }
}

// Afficher les événements dans la grille
function displayEvents(events) {
    const grid = document.getElementById('eventsGrid');
    
    if (!events || events.length === 0) {
        grid.innerHTML = `
            <div class="no-events">
                <i class="fas fa-calendar-times"></i>
                <p>Aucun événement trouvé</p>
            </div>
        `;
        return;
    }

    grid.innerHTML = events.map(event => `
        <div class="event-card" onclick="showEventDetails(${event.id})">
            <div class="event-badge">${escapeHtml(event.acronyme || event.club_nom)}</div>
            <h3>${escapeHtml(event.titre)}</h3>
            <div class="event-meta">
                <p><i class="fas fa-calendar"></i> ${formatDate(event.date)}</p>
                <p><i class="fas fa-clock"></i> ${event.heure.substring(0, 5)}</p>
                <p><i class="fas fa-map-marker-alt"></i> ${escapeHtml(event.lieu)}</p>
            </div>
            <div class="event-places">
                <span class="places-left"><i class="fas fa-ticket-alt"></i> ${event.places_restantes}/${event.places_total} places</span>
                <button class="btn-details">Voir détails</button>
            </div>
        </div>
    `).join('');
}

// ========== GESTION DU MODAL ==========

// Afficher les détails d'un événement
async function showEventDetails(eventId) {
    try {
        // Récupérer les détails de l'événement
        const response = await fetch(`api/get_event_details.php?id=${eventId}`);
        const data = await response.json();
        
        if (data.success) {
            const event = data.event;
            
            // Récupérer les participants
            const participantsResponse = await fetch(`api/get_participants.php?event_id=${eventId}`);
            const participants = await participantsResponse.json();
            
            const isFull = event.places_restantes <= 0;
            const isLoggedIn = isUserLoggedIn();
            
            // Remplir le modal
            document.getElementById('modalTitle').innerHTML = escapeHtml(event.titre);
            document.getElementById('modalBody').innerHTML = `
                <div class="event-detail-info">
                    <p><i class="fas fa-building"></i> <strong>Club :</strong> ${escapeHtml(event.club_nom)}</p>
                    <p><i class="fas fa-calendar"></i> <strong>Date :</strong> ${formatDate(event.date)}</p>
                    <p><i class="fas fa-clock"></i> <strong>Heure :</strong> ${event.heure.substring(0, 5)}</p>
                    <p><i class="fas fa-map-marker-alt"></i> <strong>Lieu :</strong> ${escapeHtml(event.lieu)}</p>
                    <p><i class="fas fa-ticket-alt"></i> <strong>Places restantes :</strong> ${event.places_restantes}/${event.places_total}</p>
                    <p><i class="fas fa-align-left"></i> <strong>Description :</strong></p>
                    <p style="margin-left: 35px; margin-bottom: 25px;">${escapeHtml(event.description) || 'Aucune description'}</p>
                </div>
                
                ${isLoggedIn ? `
                    <button class="btn-register-modal ${isFull ? 'disabled' : ''}" 
                            onclick="registerToEvent(${event.id})" 
                            ${isFull ? 'disabled' : ''}>
                        ${isFull ? '❌ Complet' : '✅ S\'inscrire à cet événement'}
                    </button>
                ` : `
                    <button class="btn-register-modal" onclick="window.location.href='login.html'">
                        🔐 Connectez-vous pour vous inscrire
                    </button>
                `}
                
                <div class="participants-section">
                    <h4><i class="fas fa-users"></i> Participants (${participants.length})</h4>
                    <div class="participants-list">
                        ${participants.length === 0 ? '<p>Aucun participant pour le moment</p>' : 
                            participants.map(p => `
                                <div class="participant-item">
                                    <i class="fas fa-user-circle"></i>
                                    <span>${escapeHtml(p.prenom)} ${escapeHtml(p.nom)}</span>
                                    <small style="margin-left: auto;">Inscrit le ${formatDate(p.date_inscription)}</small>
                                </div>
                            `).join('')
                        }
                    </div>
                </div>
            `;
            
            // Afficher le modal
            document.getElementById('eventModal').classList.add('active');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('Erreur de chargement des détails', 'error');
    }
}

// ========== INSCRIPTION ==========

// S'inscrire à un événement
async function registerToEvent(eventId) {
    const formData = new FormData();
    formData.append('event_id', eventId);
    
    // Désactiver le bouton pendant l'inscription
    const btn = document.querySelector('.btn-register-modal');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inscription...';
    btn.disabled = true;
    
    try {
        const response = await fetch('api/register_event.php', {
            method: 'POST',
            body: formData
        });
        const data = await response.json();
        
        if (data.success) {
            showNotification('✅ Inscription réussie !', 'success');
            closeModal();
            loadEvents(); // Recharger la liste des événements
        } else {
            showNotification('❌ ' + data.error, 'error');
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    } catch (error) {
        console.error('Erreur:', error);
        showNotification('❌ Erreur lors de l\'inscription', 'error');
        btn.innerHTML = originalText;
        btn.disabled = false;
    }
}

// Fermer le modal
function closeModal() {
    document.getElementById('eventModal').classList.remove('active');
}

// ========== INITIALISATION ==========

// Initialiser la page
function init() {
    // Charger les événements
    loadEvents();
    
    // Recherche en temps réel
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            currentSearch = e.target.value;
            loadEvents();
        });
    }
    
    // Filtres par club
    const filterBtns = document.querySelectorAll('.filter-btn');
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Mettre à jour la classe active
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            currentClub = btn.dataset.club;
            loadEvents();
        });
    });
    
    // Fermer le modal en cliquant en dehors
    window.onclick = function(event) {
        const modal = document.getElementById('eventModal');
        if (event.target === modal) {
            closeModal();
        }
    };
}

// Démarrer quand la page est chargée
document.addEventListener('DOMContentLoaded', init);