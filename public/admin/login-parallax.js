(() => {
    const bg = document.querySelector('.parallax-bg');
    if (!bg) return;

    // state
    let tx = 0, ty = 0, scale = 1;

    function onMouseMove(e) {
        const w = window.innerWidth, h = window.innerHeight;
        const nx = (e.clientX - w / 2) / (w / 2); // -1..1
        const ny = (e.clientY - h / 2) / (h / 2);
        // move less for subtle effect
        tx = nx * 20;
        ty = ny * 12;
        scale = 1 + (Math.abs(nx) + Math.abs(ny)) * 0.02;
    }

    function onDeviceOrientation(e) {
        const gamma = e.gamma || 0; // left to right
        const beta = e.beta || 0;   // front to back
        tx = (gamma / 90) * 22;
        ty = ((beta - 45) / 90) * 18; // center around typical portrait tilt
        scale = 1 + (Math.abs(gamma) + Math.abs(beta - 45)) / 900;
    }

    let ticking = false;
    function render() {
        bg.style.transform = `translate3d(${-tx}px, ${-ty}px, 0) scale(${scale})`;
        ticking = false;
    }

    function scheduleRender() {
        if (!ticking) {
            ticking = true;
            requestAnimationFrame(render);
        }
    }

    // Event listeners
    window.addEventListener('mousemove', (e) => { onMouseMove(e); scheduleRender(); }, { passive: true });

    // Mobile: deviceorientation
    if (window.DeviceOrientationEvent) {
        // On iOS 13+ permissions must be requested from user gesture
        const bindOrientation = () => {
            window.addEventListener('deviceorientation', (e) => { onDeviceOrientation(e); scheduleRender(); }, { passive: true });
        };

        if (typeof DeviceMotionEvent !== 'undefined' && typeof DeviceMotionEvent.requestPermission === 'function') {
            // show a small permission button overlay
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = 'Activar movimiento';
            Object.assign(btn.style, {
                position: 'fixed',
                right: '16px',
                bottom: '16px',
                zIndex: 9999,
                padding: '10px 14px',
                borderRadius: '8px',
                border: 'none',
                background: 'rgba(0,0,0,0.6)',
                color: '#fff',
                fontSize: '14px',
                cursor: 'pointer'
            });
            document.body.appendChild(btn);

            btn.addEventListener('click', async () => {
                try {
                    const resp = await DeviceMotionEvent.requestPermission();
                    // Some platforms also have DeviceOrientationEvent.requestPermission
                    if (typeof DeviceOrientationEvent !== 'undefined' && typeof DeviceOrientationEvent.requestPermission === 'function') {
                        try { await DeviceOrientationEvent.requestPermission(); } catch (e) { /* ignore */ }
                    }
                    if (resp === 'granted') {
                        bindOrientation();
                    }
                } catch (err) {
                    // permission denied or not supported
                }
                // remove button regardless
                btn.remove();
            }, { once: true });
        } else {
            bindOrientation();
        }
    }

    // gentle reset on resize
    window.addEventListener('resize', () => { tx = ty = 0; scale = 1; scheduleRender(); });
})();
