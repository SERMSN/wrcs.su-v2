(() => {
    'use strict';

    const root = document.documentElement;
    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const store = {
        get(key) {
            try {
                return window.localStorage.getItem(key);
            } catch (error) {
                return null;
            }
        },
        set(key, value) {
            try {
                window.localStorage.setItem(key, value);
            } catch (error) {
                /* storage might be disabled — theme still works for this page */
            }
        },
    };

    /* ---------------------------------------------------------------------
     * Theme
     * ------------------------------------------------------------------- */

    const themeToggle = document.querySelector('[data-theme-toggle]');
    const themeColors = { dark: '#0a0d12', light: '#f5f6f2' };

    function applyTheme(theme) {
        root.dataset.theme = theme;
        store.set('wrcs-theme', theme);

        if (themeToggle) {
            themeToggle.setAttribute('aria-pressed', theme === 'light' ? 'true' : 'false');
        }

        document.querySelectorAll('meta[name="theme-color"]').forEach((meta) => {
            meta.setAttribute('content', themeColors[theme] || themeColors.dark);
        });
    }

    if (themeToggle) {
        themeToggle.setAttribute('aria-pressed', root.dataset.theme === 'light' ? 'true' : 'false');
        themeToggle.addEventListener('click', () => {
            applyTheme(root.dataset.theme === 'light' ? 'dark' : 'light');
        });
    }

    /* ---------------------------------------------------------------------
     * Header state, mobile menu, back-to-top
     * ------------------------------------------------------------------- */

    const header = document.querySelector('[data-header]');
    const toTop = document.querySelector('[data-to-top]');
    const menuToggle = document.querySelector('[data-menu-toggle]');
    const nav = document.querySelector('[data-nav]');

    function onScroll() {
        const y = window.scrollY;

        if (header) {
            header.classList.toggle('is-scrolled', y > 8);
        }

        if (toTop) {
            toTop.classList.toggle('is-visible', y > 640);
        }
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    function closeMenu() {
        document.body.classList.remove('menu-open');

        if (menuToggle) {
            menuToggle.setAttribute('aria-expanded', 'false');
        }
    }

    if (menuToggle) {
        menuToggle.addEventListener('click', () => {
            const open = document.body.classList.toggle('menu-open');
            menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
        });
    }

    if (nav) {
        nav.querySelectorAll('a').forEach((link) => link.addEventListener('click', closeMenu));
    }

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeMenu();
        }
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth > 980) {
            closeMenu();
        }
    });

    if (toTop) {
        toTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
        });
    }

    /* ---------------------------------------------------------------------
     * Reveal on scroll
     * ------------------------------------------------------------------- */

    const revealItems = Array.from(document.querySelectorAll('[data-reveal]'));

    if (reduceMotion || !('IntersectionObserver' in window)) {
        revealItems.forEach((item) => item.classList.add('is-in'));
    } else {
        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }

                    entry.target.classList.add('is-in');
                    observer.unobserve(entry.target);
                });
            },
            { rootMargin: '0px 0px -6% 0px', threshold: 0.06 },
        );

        revealItems.forEach((item) => observer.observe(item));
    }

    /* ---------------------------------------------------------------------
     * Portfolio filters
     * ------------------------------------------------------------------- */

    const grid = document.querySelector('[data-grid]');
    const workCount = document.querySelector('[data-work-count]');
    const workEmpty = document.querySelector('[data-work-empty]');
    const chips = Array.from(document.querySelectorAll('[data-filter]'));
    const sortSelect = document.querySelector('[data-sort]');
    const minRating = document.querySelector('[data-min-rating]');
    const resetButton = document.querySelector('[data-work-reset]');
    const cards = grid ? Array.from(grid.querySelectorAll('[data-card]')) : [];

    if (grid && cards.length > 0) {
        let activeFilter = 'all';
        let sortMode = 'year';

        const countTemplate = workCount ? workCount.dataset.template || ':count' : ':count';

        function applyFilters() {
            let visible = 0;

            cards.forEach((card) => {
                const stacks = (card.dataset.stack || '').split(' ').filter(Boolean);
                const rating = Number.parseFloat(card.dataset.rating || '0');
                const matchesStack = activeFilter === 'all' || stacks.includes(activeFilter);
                const matchesRating = !minRating || !minRating.checked || rating >= 4.5;
                const show = matchesStack && matchesRating;

                card.classList.toggle('is-hidden', !show);
                card.setAttribute('aria-hidden', show ? 'false' : 'true');

                if (show) {
                    visible += 1;
                }
            });

            const sorted = cards.slice().sort((a, b) => {
                if (sortMode === 'rating') {
                    return Number.parseFloat(b.dataset.rating || '0') - Number.parseFloat(a.dataset.rating || '0');
                }

                return Number.parseInt(b.dataset.year || '0', 10) - Number.parseInt(a.dataset.year || '0', 10);
            });

            sorted.forEach((card) => grid.appendChild(card));

            if (workCount) {
                workCount.textContent = countTemplate.replace(':count', String(visible));
            }

            if (workEmpty) {
                workEmpty.hidden = visible !== 0;
            }
        }

        chips.forEach((chip) => {
            chip.addEventListener('click', () => {
                activeFilter = chip.dataset.filter || 'all';

                chips.forEach((item) => {
                    const active = item === chip;
                    item.classList.toggle('is-active', active);
                    item.setAttribute('aria-pressed', active ? 'true' : 'false');
                });

                applyFilters();
            });
        });

        if (sortSelect) {
            sortSelect.addEventListener('change', () => {
                sortMode = sortSelect.value === 'rating' ? 'rating' : 'year';
                applyFilters();
            });
        }

        if (minRating) {
            minRating.addEventListener('change', applyFilters);
        }

        if (resetButton) {
            resetButton.addEventListener('click', () => {
                activeFilter = 'all';

                chips.forEach((item) => {
                    const active = item.dataset.filter === 'all';
                    item.classList.toggle('is-active', active);
                    item.setAttribute('aria-pressed', active ? 'true' : 'false');
                });

                if (minRating) {
                    minRating.checked = false;
                }

                if (sortSelect) {
                    sortSelect.value = 'year';
                    sortMode = 'year';
                }

                applyFilters();
            });
        }

        applyFilters();
    }

    /* ---------------------------------------------------------------------
     * Star ratings
     * ------------------------------------------------------------------- */

    function formatAverage(stats) {
        const lang = root.lang || 'ru';
        const separator = lang.toLowerCase().startsWith('ru') ? ',' : '.';

        return Number(stats.average).toFixed(1).replace('.', separator);
    }

    document.querySelectorAll('[data-rate]').forEach((form) => {
        const stars = Array.from(form.querySelectorAll('.rate__star'));
        const status = form.querySelector('[data-rate-status]');
        const panel = form.closest('[data-rate-panel]') || document;

        function setPreview(value) {
            stars.forEach((star, index) => {
                star.classList.toggle('is-preview', index < value);
            });
        }

        stars.forEach((star, index) => {
            star.addEventListener('mouseenter', () => setPreview(index + 1));
            star.addEventListener('focus', () => setPreview(index + 1));
        });

        form.querySelector('[data-rate-stars]')?.addEventListener('mouseleave', () => setPreview(0));

        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            if (form.classList.contains('is-loading')) {
                return;
            }

            const submitter = event.submitter;
            const value = Number(submitter?.value || 0);

            if (value < 1 || value > 5) {
                return;
            }

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const body = new FormData(form);
            body.set('value', String(value));

            form.classList.add('is-loading');

            if (status) {
                status.classList.remove('is-ok', 'is-error');
                status.textContent = form.dataset.savingLabel || '…';
            }

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': token,
                    },
                    body,
                });

                if (!response.ok) {
                    throw new Error('HTTP ' + response.status);
                }

                const payload = await response.json();
                const stats = payload.stats || {};

                stars.forEach((star, index) => {
                    star.classList.toggle('is-on', index < value);
                });

                const averageNode = panel.querySelector('[data-rate-avg]');
                const countNode = panel.querySelector('[data-rate-count]');
                const fillNode = panel.querySelector('.rate-panel .stars__fill, .stars--lg .stars__fill');

                if (averageNode) {
                    averageNode.textContent = formatAverage(stats);
                }

                if (countNode && payload.count_label) {
                    countNode.textContent = payload.count_label;
                }

                if (fillNode) {
                    fillNode.style.width = Math.max(0, Math.min(100, (Number(stats.average) / 5) * 100)) + '%';
                }

                panel.querySelectorAll('[data-hist-fill]').forEach((fill) => {
                    const star = Number.parseInt(fill.dataset.histFill, 10);
                    const percent = stats.percent ? stats.percent[5 - star] : 0;
                    fill.style.width = percent + '%';
                });

                panel.querySelectorAll('[data-hist-count]').forEach((node) => {
                    const star = Number.parseInt(node.dataset.histCount, 10);
                    node.textContent = stats.histogram ? stats.histogram[5 - star] : '0';
                });

                if (status) {
                    status.textContent = payload.message || '';
                    status.classList.add('is-ok');
                }
            } catch (error) {
                if (status) {
                    status.textContent = form.dataset.errorLabel || 'Error';
                    status.classList.add('is-error');
                }
            } finally {
                form.classList.remove('is-loading');
                setPreview(0);

                window.setTimeout(() => {
                    if (status?.classList.contains('is-ok')) {
                        status.classList.remove('is-ok');
                    }
                }, 5000);
            }
        });
    });

    /* ---------------------------------------------------------------------
     * Copy to clipboard
     * ------------------------------------------------------------------- */

    document.querySelectorAll('[data-copy]').forEach((button) => {
        button.addEventListener('click', async () => {
            const value = button.dataset.copy || '';
            const label = button.querySelector('[data-copy-label]');
            const original = label ? label.textContent : '';

            try {
                if (navigator.clipboard?.writeText) {
                    await navigator.clipboard.writeText(value);
                } else {
                    const helper = document.createElement('textarea');
                    helper.value = value;
                    helper.setAttribute('readonly', '');
                    helper.style.position = 'fixed';
                    helper.style.opacity = '0';
                    document.body.appendChild(helper);
                    helper.select();
                    document.execCommand('copy');
                    helper.remove();
                }

                if (label) {
                    label.textContent = button.dataset.copiedLabel || original;
                    window.setTimeout(() => {
                        label.textContent = original;
                    }, 1800);
                }
            } catch (error) {
                /* clipboard is a nicety — the mailto link still works */
            }
        });
    });
})();
