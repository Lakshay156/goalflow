import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

/* ============================================================
   SCROLL REVEAL
   ============================================================ */
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.06, rootMargin: '0px 0px -30px 0px' });

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));
});

/* ============================================================
   PROGRESS BAR ANIMATE-IN
   ============================================================ */
function animateProgressBars() {
    document.querySelectorAll('.progress-fill').forEach(bar => {
        if (bar.dataset.animated) return;
        bar.dataset.animated = '1';
        const targetWidth = bar.style.width;
        bar.style.width = '0%';
        requestAnimationFrame(() => {
            setTimeout(() => {
                bar.style.transition = 'width 1.2s cubic-bezier(0.16,1,0.3,1)';
                bar.style.width = targetWidth;
            }, 120);
        });
    });
}

document.addEventListener('DOMContentLoaded', animateProgressBars);

/* ============================================================
   COUNTER ANIMATION (smooth easing)
   ============================================================ */
function animateCounters() {
    document.querySelectorAll('.counter:not([data-animated])').forEach(el => {
        el.dataset.animated = '1';
        const target = parseInt(el.dataset.target) || 0;
        if (!target) { el.textContent = '0'; return; }

        let startTime = null;
        const duration = 900;

        function easeOut(t) { return 1 - Math.pow(1 - t, 4); }

        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            const elapsed = timestamp - startTime;
            const progress = Math.min(elapsed / duration, 1);
            el.textContent = Math.floor(easeOut(progress) * target);
            if (progress < 1) requestAnimationFrame(step);
            else el.textContent = target;
        }
        requestAnimationFrame(step);
    });
}

document.addEventListener('DOMContentLoaded', animateCounters);

/* ============================================================
   MAGNETIC BUTTON EFFECT
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.btn-primary').forEach(btn => {
        btn.addEventListener('mousemove', e => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width  / 2;
            const y = e.clientY - rect.top  - rect.height / 2;
            btn.style.transform = `translate(${x * 0.12}px, ${y * 0.12}px) translateY(-1px)`;
        });
        btn.addEventListener('mouseleave', () => {
            btn.style.transform = '';
        });
    });
});

/* ============================================================
   SMOOTH PAGE TRANSITIONS
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    document.body.style.opacity = '1';
    document.body.style.transition = 'opacity 0.2s ease';

    document.querySelectorAll('a[href]:not([target]):not([href^="#"]):not([href^="javascript"]):not([href^="mailto"])').forEach(link => {
        link.addEventListener('click', e => {
            const href = link.getAttribute('href');
            if (!href || href === window.location.href) return;
            // Don't intercept form-adjacent links
            if (link.closest('form')) return;
            document.body.style.opacity = '0.6';
        });
    });
});

/* ============================================================
   HOVER LIFT — enhanced card interactions
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.bento-card, .goal-card, .glass-card').forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width  - 0.5;
            const y = (e.clientY - rect.top)  / rect.height - 0.5;
            card.style.transform = `perspective(800px) rotateY(${x * 3}deg) rotateX(${-y * 3}deg) translateZ(4px)`;
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = '';
        });
    });
});

/* ============================================================
   KEYBOARD SHORTCUTS HINT
   ============================================================ */
document.addEventListener('keydown', e => {
    // G + D = Dashboard, G + G = Goals, G + T = Tasks
    if (!e.target.matches('input, textarea, select')) {
        if (e.key === 'g') {
            document._gPressed = true;
            setTimeout(() => { document._gPressed = false; }, 500);
        } else if (document._gPressed) {
            const shortcuts = { d: '/dashboard', g: '/goals', t: '/tasks', a: '/analytics', c: '/calendar' };
            if (shortcuts[e.key]) {
                window.location.href = shortcuts[e.key];
                document._gPressed = false;
            }
        }
    }
});

/* ============================================================
   AUTO-HIDE TOP BAR ON SCROLL
   ============================================================ */
document.addEventListener('DOMContentLoaded', () => {
    let lastY = 0;
    const header = document.querySelector('header');
    if (!header) return;

    window.addEventListener('scroll', () => {
        const y = window.scrollY;
        if (y > lastY && y > 80) {
            header.style.transform = 'translateY(-100%)';
        } else {
            header.style.transform = 'translateY(0)';
        }
        lastY = y;
    }, { passive: true });
});

/* ============================================================
   LANDING PAGE — SCROLL REVEAL
   ============================================================ */
const landingObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('revealed');
            landingObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.08 });

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.reveal, .feature-card').forEach(el => landingObserver.observe(el));
});
