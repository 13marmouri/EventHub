 // Récupération des éléments
        const steps = document.querySelectorAll('.step-content');
        const stepIndicators = document.querySelectorAll('.step');
        const progressFill = document.getElementById('progress-fill');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const submitBtn = document.getElementById('submitBtn');
        let currentStep = 0;

        // Données du formulaire
        let formData = {
            prenom: '',
            nom: '',
            email: '',
            filiere: '',
            niveau: '',
            password: '',
            source: ''
        };

        // Gestion des options "D'où nous connais-tu ?"
        const optionCards = document.querySelectorAll('.option-card');
        optionCards.forEach(card => {
            card.addEventListener('click', function() {
                // Retirer la sélection de toutes les cartes
                optionCards.forEach(c => c.classList.remove('selected'));
                // Ajouter la sélection à la carte cliquée
                this.classList.add('selected');
                // Sauvegarder la valeur
                formData.source = this.dataset.value;
            });
        });

        // Mise à jour de la progression
        function updateProgress() {
            // Mise à jour des indicateurs d'étape
            stepIndicators.forEach((indicator, index) => {
                if (index < currentStep) {
                    indicator.classList.add('completed');
                    indicator.classList.remove('active');
                } else if (index === currentStep) {
                    indicator.classList.add('active');
                    indicator.classList.remove('completed');
                } else {
                    indicator.classList.remove('active', 'completed');
                }
            });

            // Mise à jour de la barre de progression
            const progress = ((currentStep + 1) / steps.length) * 100;
            progressFill.style.width = `${progress}%`;

            // Affichage des étapes
            steps.forEach((step, index) => {
                if (index === currentStep) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });

            // Gestion des boutons
            prevBtn.disabled = currentStep === 0;

            if (currentStep === steps.length - 1) {
                nextBtn.style.display = 'none';
                submitBtn.style.display = 'flex';
                generateSummary();
            } else {
                nextBtn.style.display = 'flex';
                submitBtn.style.display = 'none';
            }
        }

        // Validation de l'étape 1
        function validateStep1() {
            const prenom = document.getElementById('prenom').value;
            const nom = document.getElementById('nom').value;
            const email = document.getElementById('email').value;
            const filiere = document.getElementById('filiere').value;
            const niveau = document.getElementById('niveau').value;
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm-password').value;

            // Sauvegarde des données
            formData.prenom = prenom;
            formData.nom = nom;
            formData.email = email;
            formData.filiere = filiere;
            formData.niveau = niveau;
            formData.password = password;

            // Vérifications
            if (!prenom || !nom || !email || !filiere || !niveau || !password || !confirmPassword) {
                alert('Tous les champs sont requis !');
                return false;
            }

            if (!email.includes('@') || !email.includes('.')) {
                alert('Email invalide !');
                return false;
            }

            if (password.length < 6) {
                alert('Le mot de passe doit faire au moins 6 caractères !');
                return false;
            }

            if (password !== confirmPassword) {
                alert('Les mots de passe ne correspondent pas !');
                return false;
            }

            return true;
        }

        // Validation de l'étape 2
        function validateStep2() {
            if (!formData.source) {
                alert('Dis-nous d\'où tu nous connais !');
                return false;
            }
            return true;
        }


        // Convertir la valeur source en label
        function getSourceLabel(value) {
            const sources = {
                'amis': 'Par des amis',
                'reseau': 'Réseaux sociaux',
                'ecole': 'Par l\'école',
                'evenement': 'Lors d\'un événement',
                'recherche': 'En recherchant',
                'autre': 'Autre'
            };
            return sources[value] || value;
        }

        // Navigation suivante
        nextBtn.addEventListener('click', () => {
            if (currentStep === 0 && !validateStep1()) return;
            if (currentStep === 1 && !validateStep2()) return;
            
            if (currentStep < steps.length - 1) {
                currentStep++;
                updateProgress();
            }
        });

        // Navigation précédente
        prevBtn.addEventListener('click', () => {
            if (currentStep > 0) {
                currentStep--;
                updateProgress();
            }
        });

        // Soumission finale
        submitBtn.addEventListener('click', () => {
            // Animation de chargement
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inscription...';
            
            setTimeout(() => {
                alert(`Bienvenue ${formData.prenom} ! Ton compte EventHub est créé !`);
                window.location.href = 'index.html';
            }, 2000);
        });


        // Initialisation
        updateProgress();