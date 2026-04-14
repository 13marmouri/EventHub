const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
const prevBtn = document.querySelector('.prev-btn');
const nextBtn = document.querySelector('.next-btn');
let currentSlide = 0;
let slideInterval;

function showSlide(n) {
    // Masquer toutes les slides
    slides.forEach(slide => slide.classList.remove('active'));
    dots.forEach(dot => dot.classList.remove('active')); 
    // Ajuster l'index si nécessaire
    if (n >= slides.length) currentSlide = 0;
    else if (n < 0) currentSlide = slides.length - 1;
    else currentSlide = n;
    // Afficher la slide active
    slides[currentSlide].classList.add('active');
    dots[currentSlide].classList.add('active');
}
function nextSlide() {
    showSlide(currentSlide + 1);
}
function prevSlide() {
    showSlide(currentSlide - 1);
}
function startSlider() {
    slideInterval = setInterval(nextSlide, 5000);
}
function stopSlider() {
    clearInterval(slideInterval);
}
// Initialiser le slider
if (slides.length > 0) {
    showSlide(0);
    startSlider();
    
    // Événements pour les boutons
    if (nextBtn) nextBtn.addEventListener('click', () => {
        nextSlide();
        stopSlider();
        startSlider();
    });
    
    if (prevBtn) prevBtn.addEventListener('click', () => {
        prevSlide();
        stopSlider();
        startSlider();
    });    
    // Événements pour les points
    dots.forEach((dot, index) => {
        dot.addEventListener('click', () => {
            showSlide(index);
            stopSlider();
            startSlider();
        });
    });  
}
window.addEventListener('scroll', () => {
    const header = document.querySelector('header');
    if (window.scrollY > 100) {
        header.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.15)';
        header.style.padding = '10px 0';
    } else {
        header.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
        header.style.padding = '15px 0';
    }
});
const toutesLesCartes = document.querySelectorAll(
    '.event-card, .feature-card, .testimonial-card'
);
const observateur = new IntersectionObserver(
    function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // La carte entre dans l'écran
                entry.target.classList.add('visible');
            }
        });
    },
    { threshold: 0.1 }
);
toutesLesCartes.forEach(carte => {
    observateur.observe(carte);
});
// Sélectionner le bouton et la section des événements
const btnDecouvrir = document.querySelector('#btn-event'); // Ajustez le sélecteur
const sectionEvenements = document.querySelector('#evenements'); // Ajustez le sélecteur

btnDecouvrir.addEventListener('click', function(e) {
    e.preventDefault();
    
    // Scroll fluide vers la section
    sectionEvenements.scrollIntoView({
        behavior: 'smooth',
        block: 'start'
    });
    
    // Optionnel : Déclencher l'animation des cartes immédiatement
    setTimeout(() => {
        const cartesEvenements = document.querySelectorAll('.event-card');
        cartesEvenements.forEach(carte => {
            carte.classList.add('visible');
        });
    }, 500); // Attendre la fin du scroll
});

  function logout() {
    if(confirm("Voulez-vous vraiment vous déconnecter ?")) {
        window.location.href = "logout.php";
    }
}
