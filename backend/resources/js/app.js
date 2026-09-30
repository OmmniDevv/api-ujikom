import './bootstrap';
import { animate, stagger } from 'animejs';

// Pastikan DOM sudah siap
document.addEventListener('DOMContentLoaded', () => {
    // Hormati preferensi aksesibilitas pengguna (prefers-reduced-motion)
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) {
        return;
    }

    // =========================================================================
    // 1. AMBIENT ORBS FLOATING BREATHING ANIMATION
    // =========================================================================
    if (document.querySelector('.ambient-orb-1')) {
        animate('.ambient-orb-1', {
            translateX: [0, 30, -20, 0],
            translateY: [0, -40, 20, 0],
            scale: [1, 1.1, 0.95, 1],
            duration: 16000,
            repeat: -1,
            ease: 'inOutSine',
        });
    }

    if (document.querySelector('.ambient-orb-2')) {
        animate('.ambient-orb-2', {
            translateX: [0, -35, 25, 0],
            translateY: [0, 30, -30, 0],
            scale: [1, 0.9, 1.08, 1],
            duration: 20000,
            repeat: -1,
            ease: 'inOutSine',
        });
    }

    // =========================================================================
    // 2. ENTRANCE STAGGER ANIMATIONS (PAGE LOAD)
    // =========================================================================

    // A. Stat Cards Entry
    const statCards = document.querySelectorAll('.stat-card');
    if (statCards.length > 0) {
        animate(statCards, {
            translateY: [28, 0],
            opacity: [0, 1],
            duration: 750,
            delay: stagger(70, { start: 100 }),
            ease: 'outExpo',
        });
    }

    // B. Glass Cards Entry
    const glassCards = document.querySelectorAll('.glass-card:not(.stat-card)');
    if (glassCards.length > 0) {
        animate(glassCards, {
            translateY: [20, 0],
            opacity: [0, 1],
            duration: 700,
            delay: stagger(80, { start: 150 }),
            ease: 'outExpo',
        });
    }

    // C. Table Rows Stagger Reveal
    const tableRows = document.querySelectorAll('.table-glass tbody tr');
    if (tableRows.length > 0 && tableRows.length <= 30) {
        animate(tableRows, {
            opacity: [0, 1],
            translateX: [-12, 0],
            duration: 500,
            delay: stagger(35, { start: 200 }),
            ease: 'outQuad',
        });
    }

    // D. Flash Message Notification Pop-in
    const alertMsg = document.querySelectorAll('.alert-success, .alert-error');
    if (alertMsg.length > 0) {
        animate(alertMsg, {
            translateY: [-24, 0],
            scale: [0.96, 1],
            opacity: [0, 1],
            duration: 600,
            ease: 'outBack(1.4)',
        });
    }

    // =========================================================================
    // 3. DYNAMIC NUMBER COUNTER ANIMATION
    // =========================================================================
    const counterElements = document.querySelectorAll('[data-counter], .stat-card p.text-3xl, .stat-card p.text-2xl');
    counterElements.forEach((el) => {
        const rawText = el.textContent.trim();
        // Cek jika teks berupa angka murni
        const numericVal = parseInt(rawText.replace(/[^0-9]/g, ''), 10);
        if (!isNaN(numericVal) && numericVal > 0 && numericVal < 1000000) {
            const hasCurrencyPrefix = rawText.includes('Rp');
            const target = { val: 0 };
            
            animate(target, {
                val: numericVal,
                duration: 1100,
                ease: 'outExpo',
                onUpdate: () => {
                    const current = Math.floor(target.val);
                    if (hasCurrencyPrefix) {
                        el.textContent = 'Rp ' + current.toLocaleString('id-ID');
                    } else {
                        el.textContent = current.toString();
                    }
                },
                onComplete: () => {
                    el.textContent = rawText; // Kembalikan ke teks asli yang rapi
                }
            });
        }
    });

    // =========================================================================
    // 4. ANIMATED DELETE MODAL WITH ANIMEJS
    // =========================================================================
    const modal = document.getElementById('deleteModal');
    const modalCard = document.getElementById('deleteModalCard');

    window.confirmDelete = function (url, message) {
        const msgEl = document.getElementById('deleteModalMessage');
        const formEl = document.getElementById('deleteForm');

        if (msgEl) msgEl.textContent = message || 'Yakin ingin menghapus data ini?';
        if (formEl) formEl.action = url;

        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');

            if (modalCard) {
                animate(modalCard, {
                    scale: [0.82, 1],
                    opacity: [0, 1],
                    translateY: [15, 0],
                    duration: 400,
                    ease: 'outBack(1.7)',
                });
            }
        }
    };

    window.closeDeleteModal = function () {
        if (modal && modalCard) {
            animate(modalCard, {
                scale: [1, 0.9],
                opacity: [1, 0],
                translateY: [0, 10],
                duration: 220,
                ease: 'inQuad',
                onComplete: () => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                },
            });
        } else if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    };

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            window.closeDeleteModal();
        }
    });
});
