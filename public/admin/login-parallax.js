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
        window.addEventListener('deviceorientation', (e) => { onDeviceOrientation(e); scheduleRender(); }, { passive: true });
    }

    // gentle reset on resize
    window.addEventListener('resize', () => { tx = ty = 0; scale = 1; scheduleRender(); });
})();
