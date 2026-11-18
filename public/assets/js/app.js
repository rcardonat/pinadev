/**
 * PinaDev — mejoras progresivas (Vanilla JS, sin dependencias).
 * Todo el sitio funciona sin este archivo; aquí solo se añaden comodidades.
 */
(() => {
    'use strict';

    const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

    /* ---------------------------------------------------------------------
       Menú lateral móvil (drawer).
       Sin JS se abre con el ancla #menu-movil (:target). Con JS se convierte
       en un diálogo modal: foco dentro del panel, resto de la página inerte,
       cierre con Escape, con el fondo o con el botón, y foco devuelto al
       botón de apertura.
       --------------------------------------------------------------------- */
    const drawer = document.querySelector('[data-drawer]');
    const drawerOpenBtn = document.querySelector('[data-drawer-open]');

    if (drawer && drawerOpenBtn) {
        const panel = drawer.querySelector('[data-drawer-panel]');
        const closeBtn = drawer.querySelector('.drawer-close');
        const desktop = window.matchMedia('(min-width: 768px)');

        panel.setAttribute('role', 'dialog');
        panel.setAttribute('aria-modal', 'true');
        panel.tabIndex = -1;
        drawerOpenBtn.setAttribute('role', 'button');

        // Todo lo que no es el drawer queda inerte mientras está abierto.
        const outside = () => Array.from(document.body.children)
            .filter((el) => el !== drawer && el.tagName !== 'SCRIPT');

        const isOpen = () => drawer.classList.contains('is-open');

        function openDrawer() {
            drawer.classList.add('is-open');
            drawerOpenBtn.setAttribute('aria-expanded', 'true');
            outside().forEach((el) => { el.inert = true; });
            (closeBtn || panel).focus();
        }

        function closeDrawer(returnFocus = true) {
            drawer.classList.remove('is-open');
            drawerOpenBtn.setAttribute('aria-expanded', 'false');
            outside().forEach((el) => { el.inert = false; });
            if (returnFocus && !desktop.matches) drawerOpenBtn.focus();
        }

        drawerOpenBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openDrawer();
        });

        drawer.querySelectorAll('[data-drawer-close]').forEach((el) => {
            el.addEventListener('click', (e) => {
                // Si se abrió por el ancla (:target), dejamos que href="#" lo limpie.
                if (!drawer.matches(':target')) e.preventDefault();
                closeDrawer();
            });
        });

        // El espacio activa el botón de apertura (es un enlace con role="button").
        drawerOpenBtn.addEventListener('keydown', (e) => {
            if (e.key === ' ') {
                e.preventDefault();
                openDrawer();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && isOpen()) closeDrawer();
        });

        // Si se pasa a escritorio con el menú abierto, se cierra.
        desktop.addEventListener('change', () => {
            if (desktop.matches && isOpen()) closeDrawer(false);
        });

        // Al volver con "Atrás" (caché del navegador) el menú no debe seguir abierto.
        window.addEventListener('pageshow', (e) => {
            if (e.persisted && isOpen()) closeDrawer(false);
        });
    }

    /* ---------------------------------------------------------------------
       Tema oscuro / claro.
       El servidor lee la cookie "theme" y pinta data-theme desde el HTML
       inicial (sin destellos). Aquí solo se cambia y se guarda la elección.
       --------------------------------------------------------------------- */
    const root = document.documentElement;
    // Hay dos botones: el de la cabecera (escritorio) y el del menú lateral (móvil).
    const themeBtns = Array.from(document.querySelectorAll('[data-theme-toggle]'));
    const THEME_COLORS = { dark: '#101419', light: '#F5F7FA' };

    function applyTheme(theme) {
        root.setAttribute('data-theme', theme);
        const meta = document.querySelector('meta[name="theme-color"]');
        if (meta) meta.setAttribute('content', THEME_COLORS[theme]);
        const scheme = document.querySelector('meta[name="color-scheme"]');
        if (scheme) scheme.setAttribute('content', theme);

        const label = theme === 'dark' ? 'Activar modo claro' : 'Activar modo oscuro';
        themeBtns.forEach((btn) => {
            const text = btn.querySelector('[data-theme-text]');
            if (text) {
                text.textContent = label;          // botón con texto visible (menú móvil)
            } else {
                btn.setAttribute('aria-label', label);
                btn.title = label;
            }
        });
    }

    function saveTheme(theme) {
        const path = (root.dataset.base || '') + '/';
        const secure = location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = `theme=${theme}; Path=${path}; Max-Age=31536000; SameSite=Lax${secure}`;
    }

    themeBtns.forEach((themeBtn) => {
        themeBtn.hidden = false;

        themeBtn.addEventListener('click', () => {
            const next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            saveTheme(next);

            // Sin soporte de View Transitions o con movimiento reducido: cambio directo.
            if (!document.startViewTransition || reduceMotion.matches) {
                applyTheme(next);
                return;
            }

            // Círculo que se expande desde el botón hasta cubrir la pantalla.
            const rect = themeBtn.getBoundingClientRect();
            const x = rect.left + rect.width / 2;
            const y = rect.top + rect.height / 2;
            const radius = Math.hypot(Math.max(x, innerWidth - x), Math.max(y, innerHeight - y));

            root.classList.add('theme-switching');
            const transition = document.startViewTransition(() => applyTheme(next));
            transition.ready.then(() => {
                root.animate(
                    { clipPath: [`circle(0px at ${x}px ${y}px)`, `circle(${radius}px at ${x}px ${y}px)`] },
                    { duration: 550, easing: 'cubic-bezier(.22, 1, .36, 1)', pseudoElement: '::view-transition-new(root)' }
                );
            }).catch(() => {});
            transition.finished.finally(() => root.classList.remove('theme-switching'));
        });
    });

    /* ---------------------------------------------------------------------
       Fondo de partículas (Inicio).
       Oscuro: estrellas que titilan + luciérnagas que vagan y brillan.
       Claro:  polvo dorado flotando con destellos suaves.
       - Cantidad proporcional al área de la pantalla (menos en móviles).
       - Se pausa con la pestaña oculta.
       - Con "reducir movimiento" se dibuja un solo cuadro estático.
       - Cambia de modo al cambiar el tema.
       --------------------------------------------------------------------- */
    const particlesCanvas = document.querySelector('[data-particles]');

    if (particlesCanvas && particlesCanvas.getContext) {
        const ctx = particlesCanvas.getContext('2d');
        const TAU = Math.PI * 2;
        const rand = (min, max) => min + Math.random() * (max - min);

        let width = 0;
        let height = 0;
        let dpr = 1;
        let mode = '';
        let stars = [];
        let flies = [];
        let dust = [];
        let rafId = 0;
        let last = 0;

        // Brillo pre-renderizado (un degradado radial) para dibujar luciérnagas
        // y destellos de polvo con drawImage, mucho más barato que crear
        // degradados en cada cuadro.
        function makeGlow(rgb) {
            const size = 64;
            const c = document.createElement('canvas');
            c.width = c.height = size;
            const g = c.getContext('2d');
            const grad = g.createRadialGradient(size / 2, size / 2, 0, size / 2, size / 2, size / 2);
            grad.addColorStop(0, `rgba(${rgb}, 1)`);
            grad.addColorStop(0.25, `rgba(${rgb}, .55)`);
            grad.addColorStop(1, `rgba(${rgb}, 0)`);
            g.fillStyle = grad;
            g.fillRect(0, 0, size, size);
            return c;
        }
        const fireflyGlow = makeGlow('214, 245, 120');
        const dustGlow = makeGlow('214, 168, 74');

        function resize() {
            dpr = Math.min(window.devicePixelRatio || 1, 1.5);
            width = window.innerWidth;
            height = window.innerHeight;
            particlesCanvas.width = Math.round(width * dpr);
            particlesCanvas.height = Math.round(height * dpr);
            ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        }

        function build() {
            mode = root.getAttribute('data-theme') === 'light' ? 'day' : 'night';
            const area = width * height;
            stars = [];
            flies = [];
            dust = [];

            if (mode === 'night') {
                const nStars = Math.min(160, Math.round(area / 9000));
                for (let i = 0; i < nStars; i++) {
                    stars.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        r: rand(0.4, 1.4),
                        a: rand(0.25, 0.85),
                        speed: rand(0.6, 2.2),
                        phase: Math.random() * TAU,
                    });
                }
                const nFlies = Math.max(6, Math.min(22, Math.round(area / 55000)));
                for (let i = 0; i < nFlies; i++) {
                    flies.push({
                        x: Math.random() * width,
                        y: rand(height * 0.25, height),
                        dir: Math.random() * TAU,
                        speed: rand(12, 28),          // px por segundo
                        size: rand(18, 34),
                        pulse: rand(0.6, 1.4),
                        phase: Math.random() * TAU,
                    });
                }
            } else {
                const nDust = Math.min(90, Math.round(area / 13000));
                for (let i = 0; i < nDust; i++) {
                    dust.push({
                        x: Math.random() * width,
                        y: Math.random() * height,
                        r: rand(0.8, 2.6),
                        a: rand(0.3, 0.65),
                        vx: rand(-6, 6),
                        vy: rand(-10, -3),            // sube despacio
                        sway: rand(0.4, 1.2),
                        phase: Math.random() * TAU,
                        glint: Math.random() < 0.25,  // algunas destellan
                    });
                }
            }
        }

        function wrap(p, margin) {
            if (p.x < -margin) p.x = width + margin;
            if (p.x > width + margin) p.x = -margin;
            if (p.y < -margin) p.y = height + margin;
            if (p.y > height + margin) p.y = -margin;
        }

        function draw(t, dt) {
            ctx.clearRect(0, 0, width, height);
            const s = t / 1000;

            if (mode === 'night') {
                ctx.fillStyle = '#EDF2F7';
                for (const st of stars) {
                    ctx.globalAlpha = st.a * (0.55 + 0.45 * Math.sin(s * st.speed + st.phase));
                    ctx.beginPath();
                    ctx.arc(st.x, st.y, st.r, 0, TAU);
                    ctx.fill();
                }
                for (const f of flies) {
                    // Paseo aleatorio suave.
                    f.dir += rand(-1.2, 1.2) * dt;
                    f.x += Math.cos(f.dir) * f.speed * dt;
                    f.y += Math.sin(f.dir) * f.speed * dt;
                    wrap(f, 30);
                    const glow = Math.max(0, Math.sin(s * f.pulse + f.phase));
                    ctx.globalAlpha = 0.15 + 0.75 * glow * glow;
                    ctx.drawImage(fireflyGlow, f.x - f.size / 2, f.y - f.size / 2, f.size, f.size);
                }
            } else {
                for (const d of dust) {
                    d.x += (d.vx + Math.sin(s * d.sway + d.phase) * 6) * dt;
                    d.y += d.vy * dt;
                    wrap(d, 10);
                    if (d.glint) {
                        const g = Math.max(0, Math.sin(s * d.sway * 1.5 + d.phase));
                        const size = d.r * 7;
                        ctx.globalAlpha = 0.25 + 0.6 * g;
                        ctx.drawImage(dustGlow, d.x - size / 2, d.y - size / 2, size, size);
                    } else {
                        ctx.globalAlpha = d.a;
                        ctx.fillStyle = '#B98F45';
                        ctx.beginPath();
                        ctx.arc(d.x, d.y, d.r, 0, TAU);
                        ctx.fill();
                    }
                }
            }
            ctx.globalAlpha = 1;
        }

        function frame(t) {
            const dt = Math.min((t - (last || t)) / 1000, 0.05);  // evita saltos tras pausas
            last = t;
            draw(t, dt);
            rafId = requestAnimationFrame(frame);
        }

        function start() {
            stop();
            if (reduceMotion.matches) {
                draw(0, 0);           // un cuadro estático
                return;
            }
            last = 0;
            rafId = requestAnimationFrame(frame);
        }

        function stop() {
            cancelAnimationFrame(rafId);
            rafId = 0;
        }

        resize();
        build();
        start();
        particlesCanvas.classList.add('is-ready');

        let resizeTimer = 0;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => { resize(); build(); start(); }, 150);
        });

        document.addEventListener('visibilitychange', () => {
            if (document.hidden) stop();
            else start();
        });

        reduceMotion.addEventListener('change', start);

        // Cambio de tema: se reconstruyen las partículas del modo nuevo.
        new MutationObserver(() => {
            const next = root.getAttribute('data-theme') === 'light' ? 'day' : 'night';
            if (next !== mode) {
                build();
                start();
            }
        }).observe(root, { attributes: true, attributeFilter: ['data-theme'] });
    }

    /* ---------------------------------------------------------------------
       Volver arriba.
       Aparece tras bajar ~60 % de la pantalla; el anillo muestra el avance
       de lectura. Al pulsarlo sube (suave, salvo "reducir movimiento") y
       mueve el foco al contenido principal para usuarios de teclado.
       --------------------------------------------------------------------- */
    const toTop = document.querySelector('[data-to-top]');

    if (toTop) {
        const progress = toTop.querySelector('[data-to-top-progress]');
        const CIRCUMFERENCE = 138.23;   // 2 · π · r (r = 22, ver app.css)
        let ticking = false;

        toTop.hidden = false;

        function updateToTop() {
            ticking = false;
            const y = window.scrollY;
            const max = document.documentElement.scrollHeight - window.innerHeight;
            toTop.classList.toggle('is-visible', y > window.innerHeight * 0.6);
            if (progress) {
                const ratio = max > 0 ? Math.min(y / max, 1) : 0;
                progress.style.strokeDashoffset = String(CIRCUMFERENCE * (1 - ratio));
            }
        }

        window.addEventListener('scroll', () => {
            if (!ticking) {
                ticking = true;
                requestAnimationFrame(updateToTop);
            }
        }, { passive: true });
        window.addEventListener('resize', updateToTop);

        toTop.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: reduceMotion.matches ? 'auto' : 'smooth' });
            const main = document.getElementById('contenido');
            if (main) main.focus({ preventScroll: true });
        });

        updateToTop();
    }

    /* ---------------------------------------------------------------------
       Slider accesible.
       Sin JS: carrusel deslizable con scroll-snap.
       Con JS: flechas, puntos, autoplay con botón de pausa (WCAG 2.2.2),
       pausa al pasar el ratón o enfocar, y sin autoplay si se prefiere
       reducir el movimiento.
       --------------------------------------------------------------------- */
    const INTERVAL = 6000;

    document.querySelectorAll('[data-slider]').forEach((root) => {
        const track = root.querySelector('[data-slider-track]');
        const controls = root.querySelector('[data-slider-controls]');
        const slides = track ? Array.from(track.children) : [];
        if (!controls || slides.length < 2) return;

        const prevBtn = root.querySelector('[data-slider-prev]');
        const nextBtn = root.querySelector('[data-slider-next]');
        const pauseBtn = root.querySelector('[data-slider-pause]');
        const dots = Array.from(root.querySelectorAll('[data-slider-dot]'));

        let index = 0;
        let playing = !reduceMotion.matches;
        let hovered = false;
        let focused = false;
        let timer = null;
        let scrollTimer = null;

        controls.hidden = false;
        root.classList.add('is-ready');

        const behavior = () => (reduceMotion.matches ? 'auto' : 'smooth');

        function update(i) {
            index = i;
            slides.forEach((slide, n) => {
                const active = n === index;
                slide.classList.toggle('is-active', active);
                // Las diapositivas ocultas no reciben foco ni clics.
                slide.inert = !active;
            });
            dots.forEach((dot, n) => dot.setAttribute('aria-current', n === index ? 'true' : 'false'));
        }

        function goTo(i) {
            const target = (i + slides.length) % slides.length;
            track.scrollTo({ left: target * track.clientWidth, behavior: behavior() });
            update(target);
        }

        function setPlaying(value) {
            playing = value;
            pauseBtn.setAttribute('aria-label', playing ? 'Pausar carrusel' : 'Reproducir carrusel');
            pauseBtn.dataset.state = playing ? 'playing' : 'paused';
            // Con autoplay activo no se anuncian los cambios (serían molestos).
            track.setAttribute('aria-live', playing ? 'off' : 'polite');
            restart();
        }

        function restart() {
            clearInterval(timer);
            if (!playing) return;
            timer = setInterval(() => {
                if (!hovered && !focused && !document.hidden) goTo(index + 1);
            }, INTERVAL);
        }

        prevBtn.addEventListener('click', () => { goTo(index - 1); restart(); });
        nextBtn.addEventListener('click', () => { goTo(index + 1); restart(); });
        pauseBtn.addEventListener('click', () => setPlaying(!playing));
        dots.forEach((dot) => dot.addEventListener('click', () => {
            goTo(Number(dot.dataset.sliderDot));
            restart();
        }));

        // Teclado sobre el carrusel: flechas izquierda/derecha.
        track.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                e.preventDefault();
                goTo(index + (e.key === 'ArrowRight' ? 1 : -1));
                restart();
            }
        });

        // Deslizamiento táctil / rueda: sincroniza el índice al terminar.
        track.addEventListener('scroll', () => {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(() => {
                const i = Math.round(track.scrollLeft / track.clientWidth);
                if (i !== index) update(i);
            }, 120);
        }, { passive: true });

        root.addEventListener('mouseenter', () => { hovered = true; });
        root.addEventListener('mouseleave', () => { hovered = false; });
        root.addEventListener('focusin', () => { focused = true; });
        root.addEventListener('focusout', (e) => {
            if (!root.contains(e.relatedTarget)) focused = false;
        });

        window.addEventListener('resize', () => {
            track.scrollTo({ left: index * track.clientWidth, behavior: 'auto' });
        });

        reduceMotion.addEventListener('change', () => {
            if (reduceMotion.matches) setPlaying(false);
        });

        update(0);
        setPlaying(playing);
    });
})();
