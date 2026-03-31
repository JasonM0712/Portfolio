document.addEventListener('DOMContentLoaded', () => {

    // ── Navbar scroll ──────────────────────────────────────────
    const navbar = document.querySelector(".navigation");
    if (navbar) {
        window.addEventListener("scroll", () => {
            navbar.classList.toggle("scrolled", window.scrollY > 50);
        });
    }

    // ── Modals ─────────────────────────────────────────────────
    document.querySelectorAll("button[data-modal]").forEach(button => {
        button.addEventListener("click", function () {
            const modalId = this.getAttribute("data-modal");
            const modal = document.getElementById(modalId);
            if (modal) modal.style.display = "flex";
        });
    });

    document.querySelectorAll(".close").forEach(closeBtn => {
        closeBtn.addEventListener("click", function () {
            this.closest(".modal").style.display = "none";
        });
    });

    window.addEventListener("click", (e) => {
        document.querySelectorAll(".modal").forEach(modal => {
            if (e.target === modal) modal.style.display = "none";
        });
    });

    // ── Burger menu ────────────────────────────────────────────
    const burger = document.querySelector(".burger");
    const menuMobile = document.querySelector(".navButtonMobile");
    if (burger && menuMobile) {
        burger.addEventListener("click", () => {
            menuMobile.classList.toggle("OpenBurger");
            menuMobile.classList.toggle("closeBurger");
        });
    }

    // ── Formulaire de contact ──────────────────────────────────
    const form = document.getElementById('contactForm');
    if (form) {
        const statusMsg = document.getElementById('status-msg');
        const btn = form.querySelector('button[type="submit"]');

        // Récupération du token CSRF au chargement
        fetch('contact.php')
            .then(res => res.json())
            .then(data => {
                document.getElementById('csrf_token').value = data.csrf_token;
            })
            .catch(() => {
                btn.disabled = true;
                statusMsg.textContent = 'Impossible de charger le formulaire. Rechargez la page.';
                statusMsg.style.color = 'red';
                statusMsg.style.display = 'block';
            });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            e.stopPropagation();

            btn.disabled = true;
            btn.textContent = 'Envoi…';
            statusMsg.style.display = 'none';

            try {
                const res = await fetch('contact.php', {
                    method: 'POST',
                    body: new FormData(form)
                });

                const data = await res.json();

                if (data.success) {
                    statusMsg.textContent = data.success;
                    statusMsg.style.color = 'green';
                    form.reset();

                    // Nouveau token CSRF pour le prochain envoi
                    fetch('contact.php')
                        .then(r => r.json())
                        .then(d => {
                            document.getElementById('csrf_token').value = d.csrf_token;
                        });

                } else {
                    statusMsg.textContent = data.error || 'Une erreur est survenue.';
                    statusMsg.style.color = 'red';
                }

            } catch (err) {
                statusMsg.textContent = 'Erreur réseau. Vérifiez votre connexion.';
                statusMsg.style.color = 'red';
            }

            statusMsg.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Envoyer';
        });
    }

});
