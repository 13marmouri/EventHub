        // Récupération des éléments
        const emailInput = document.getElementById('email');
        const passwordInput = document.getElementById('password');
        const leftEye = document.getElementById('leftEye');
        const rightEye = document.getElementById('rightEye');
        const characterSmile = document.getElementById('characterSmile');
        const characterMessage = document.getElementById('characterMessage');
        const mainCharacter = document.getElementById('mainCharacter');
        const notificationArea = document.getElementById('notificationArea');
        const loginForm = document.getElementById('loginForm');
        const loginBtn = document.getElementById('loginBtn');
        
        const emailPattern = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
        // Éléments du carrousel
        const phrases = document.querySelectorAll('.phrase-item');
        let currentPhrase = 0;
        let phraseInterval;

        // Fonction pour changer de phrase
        function changePhrase(index) {
            phrases.forEach(phrase => phrase.classList.remove('active'));
            phrases[index].classList.add('active');
            currentPhrase = index;
            
            // Animation du personnage qui regarde la nouvelle phrase
            leftEye.style.transform = 'translateX(5px)';
            rightEye.style.transform = 'translateX(5px)';
            setTimeout(() => {
                leftEye.style.transform = 'translateX(0)';
                rightEye.style.transform = 'translateX(0)';
            }, 500);
        }

        // Fonction pour passer à la phrase suivante
        function nextPhrase() {
            let nextIndex = (currentPhrase + 1) % phrases.length;
            changePhrase(nextIndex);
        }

        // Démarrer le carrousel automatique
        function startPhraseCarousel() {
            phraseInterval = setInterval(nextPhrase, 4000); // Change toutes les 4 secondes
        }

        // Arrêter le carrousel
        function stopPhraseCarousel() {
            clearInterval(phraseInterval);
        }

        // Arrêter le carrousel au survol
        const carousel = document.querySelector('.phrase-carousel');
        carousel.addEventListener('mouseenter', stopPhraseCarousel);
        carousel.addEventListener('mouseleave', startPhraseCarousel);

        // Démarrer le carrousel
        startPhraseCarousel();

        // Fonction pour créer une notification
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = 'notification-bubble';
            
            let icon = 'fa-info-circle';
            if (type === 'error') icon = 'fa-exclamation-circle';
            if (type === 'success') icon = 'fa-check-circle';
            
            notification.innerHTML = `
                <i class="fas ${icon}"></i>
                <span>${message}</span>
            `;
            
            notificationArea.appendChild(notification);
            
            setTimeout(() => {
                notification.classList.add('fade-out');
                setTimeout(() => {
                    notification.remove();
                }, 500);
            }, 3000);
        }

        // Animation de clignement des yeux
        function blinkEyes() {
            leftEye.classList.add('blink');
            rightEye.classList.add('blink');
            
            setTimeout(() => {
                leftEye.classList.remove('blink');
                rightEye.classList.remove('blink');
            }, 200);
        }

        // Clignement toutes les 3 secondes
        setInterval(blinkEyes, 3000);

        // Réaction quand on tape dans l'email
        emailInput.addEventListener('input', function() {
            const value = this.value;
            
            if (value.includes('@') && value.includes('.')) {
                characterSmile.className = 'character-smile happy';
                showNotification('Email valide !','success');
            }else{
                characterSmile.className = 'character-smile';
            }
            leftEye.style.transform = 'translateX(2px)';
            rightEye.style.transform = 'translateX(2px)';
            setTimeout(() => {
                leftEye.style.transform = 'translateX(0)';
                rightEye.style.transform = 'translateX(0)';
            }, 500);
        });
        // Réaction au mot de passe
        passwordInput.addEventListener('input', function() {
            const value = this.value;
            
            if (value.length === 0) {
                characterSmile.className = 'character-smile';
            } else if (value.length < 6) {
                characterSmile.className = 'character-smile worried';
                showNotification('Mot de passe trop court (min 6 caractères)','error');
            } else if (value.length >= 8) {
                characterSmile.className = 'character-smile happy';
                showNotification('Mot de passe sécurisé !','success');
            } else {
                characterSmile.className = 'character-smile';
            }
            leftEye.style.transform = 'translateY(2px)';
            rightEye.style.transform = 'translateY(2px)';
            setTimeout(() => {
                leftEye.style.transform = 'translateY(0)';
                rightEye.style.transform = 'translateY(0)';
            }, 300);
        });
        // Focus sur email
        emailInput.addEventListener('focus', function() {
            leftEye.style.transform = 'translateX(-2px)';
            rightEye.style.transform = 'translateX(-2px)';
        });

      // Focus sur mot de passe
       passwordInput.addEventListener('focus', function() {
            const email = emailInput.value;
    
            // Vérifier si l'email existe et est valide
            if (!email || email.trim() === '') {
               showNotification(' Veuillez d\'abord saisir votre email !', 'error');
                // L'email est vide, on focus sur l'email à la place
                emailInput.focus();
                return;
            }
    
         // Vérifier le format de l'email
            const emailPattern = /^[^\s@]+@([^\s@]+\.)+[^\s@]+$/;
             if (!emailPattern.test(email)) {
                showNotification('Email invalide ! Vérifie le format (exemple@domaine.com)', 'error');
                emailInput.focus();
                return;
                }
    
        // Si l'email est valide, on peut passer au mot de passe
        showNotification('Tape ton mot de passe et fait confiance en moi je ne regarde rien !!', 'info');
        leftEye.style.transform = 'translateY(2px)';
        rightEye.style.transform = 'translateY(2px)';
       });
// Soumission du formulaire
loginForm.addEventListener('submit', function(e) {  
    const email = emailInput.value;
    const password = passwordInput.value;     
    let hasError = false;
    
    // === VALIDATION EMAIL ===
    if (!email) {
        emailInput.classList.add('error');
        showNotification('Email manquant', 'error');
        hasError = true;
    } else if (!emailPattern.test(email)) {
        emailInput.classList.add('error');
        showNotification('Email invalide ! (exemple@domaine.com)', 'error');
        hasError = true;
    } else {
        emailInput.classList.remove('error');
    }
    
    // === VALIDATION MOT DE PASSE ===
    if (!password) {
        passwordInput.classList.add('error');
        showNotification('Mot de passe manquant', 'error');
        hasError = true;
    } else if (password.length < 6) {
        passwordInput.classList.add('error');
        showNotification('Mot de passe trop court', 'error');
        hasError = true;
    } else {
        passwordInput.classList.remove('error');
    }
    
    // === GESTION DES ERREURS ===
    if (hasError) {
        e.preventDefault(); // ⛔ STOP ! On bloque l'envoi si erreur
        characterSmile.className = 'character-smile worried';
        leftEye.style.transform = 'translateY(3px) scale(0.9)';
        rightEye.style.transform = 'translateY(3px) scale(0.9)';
        setTimeout(() => {
            leftEye.style.transform = 'translateY(0) scale(1)';
            rightEye.style.transform = 'translateY(0) scale(1)';
        }, 2000);
    } 
    // === TOUT EST BON ===
    else {
        characterSmile.className = 'character-smile happy';   
        mainCharacter.style.animation = 'none';
        mainCharacter.offsetHeight; // Force reflow pour relancer l'animation
        mainCharacter.style.animation = 'characterFloat 3s ease-in-out infinite';
        
        showNotification("Connexion en cours...", "info");
    }
});
        // Interaction avec le personnage principal
        mainCharacter.addEventListener('click', function() {
            characterMessage.textContent = 'Hé! Connecte-toi vite !';
            characterSmile.className = 'character-smile happy';
            
            for (let i = 0; i < 3; i++) {
                setTimeout(() => {
                    leftEye.classList.add('blink');
                    rightEye.classList.add('blink');
                    setTimeout(() => {
                        leftEye.classList.remove('blink');
                        rightEye.classList.remove('blink');
                    }, 100);
                }, i * 200);
            }
            
            showNotification('Ton camarade te dit bonjour !', 'info');
        });