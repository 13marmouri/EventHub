 // ===== ANIMATION COMPTEURS =====
    function animateCounters() {
        document.querySelectorAll('[data-target]').forEach(el => {
            const target = parseInt(el.dataset.target);
            let count = 0;
            const step = Math.ceil(target / 30);
            const interval = setInterval(() => {
                count = Math.min(count + step, target);
                el.textContent = count;
                if (count >= target) clearInterval(interval);
            }, 40);
        });
    }

    function animateXP() {
        let xp = 0;
        const target = 480;
        const interval = setInterval(() => {
            xp = Math.min(xp + 16, target);
            document.getElementById('xpNumber').textContent = xp;
            if (xp >= target) clearInterval(interval);
        }, 20);
        setTimeout(() => {
            document.getElementById('xpFill').style.width = '80%';
        }, 300);
    }

    // Animation barres de progression
    function animateBars() {
        document.querySelectorAll('.progress-bar-inner[data-width]').forEach(bar => {
            setTimeout(() => {
                bar.style.width = bar.dataset.width + '%';
            }, 400);
        });
    }

    // Lancer les animations au chargement
    window.addEventListener('load', () => {
        animateCounters();
        animateXP();
        animateBars();
        renderCalendar();
    });

    // ===== TOAST =====
    function showToast(msg) {
        const existing = document.querySelector('.toast');
        if (existing) existing.remove();
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.innerHTML = `<i class="fas fa-info-circle"></i> ${msg}`;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(50px)';
            toast.style.transition = 'all 0.4s';
            setTimeout(() => toast.remove(), 400);
        }, 3000);
    }

    // ===== ONGLETS =====
    function switchTab(tabId, btn) {
        document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.getElementById('tab-' + tabId).classList.add('active');
        btn.classList.add('active');
    }

    // ===== CALENDRIER =====
    const eventDays = [10, 25, 28]; // Jours avec événements en mars 2026
    const months = ['Janvier','Février','Mars','Avril','Mai','Juin','Juillet','Août','Septembre','Octobre','Novembre','Décembre'];
    const dayNames = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
    let calYear = 2026, calMonth = 2; // Mars = index 2

    function renderCalendar() {
        document.getElementById('calMonth').textContent = months[calMonth] + ' ' + calYear;
        const grid = document.getElementById('calGrid');
        grid.innerHTML = '';

        // En-têtes jours
        dayNames.forEach(d => {
            const el = document.createElement('div');
            el.className = 'cal-day-name';
            el.textContent = d;
            grid.appendChild(el);
        });

        const firstDay = new Date(calYear, calMonth, 1).getDay();
        const offset = firstDay === 0 ? 6 : firstDay - 1;
        const totalDays = new Date(calYear, calMonth + 1, 0).getDate();
        const today = new Date();

        for (let i = 0; i < offset; i++) {
            const el = document.createElement('div');
            el.className = 'cal-day empty';
            grid.appendChild(el);
        }

        for (let d = 1; d <= totalDays; d++) {
            const el = document.createElement('div');
            const isToday = d === today.getDate() && calMonth === today.getMonth() && calYear === today.getFullYear();
            const hasEvent = eventDays.includes(d) && calMonth === 2 && calYear === 2026;

            if (hasEvent) {
                el.className = 'cal-day has-event';
                el.innerHTML = `${d}<div class="event-dot"></div>`;
                el.onclick = () => showToast('Tu as un événement le ${d} ${months[calMonth]}!');
            } else if (isToday) {
                el.className = 'cal-day today';
                el.textContent = d;
            } else {
                el.className = 'cal-day normal';
                el.textContent = d;
            }

            grid.appendChild(el);
        }
    }

    function changeMonth(dir) {
        calMonth += dir;
        if (calMonth > 11) { calMonth = 0; calYear++; }
        if (calMonth < 0) { calMonth = 11; calYear--; }
        renderCalendar();
    }