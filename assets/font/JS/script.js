window.addEventListener('scroll', () => {
    const scrollPosition = window.scrollY;
    const layers = document.querySelectorAll('.background-layer');

    layers.forEach((layer, index) => {
        const speed = (index + 1) * 0.3; // Vitesse différente par couche
        const scale = 1 + scrollPosition * 0.0005 * (index + 1); // Éparpillement
        const offset = scrollPosition * speed; // Décalage

        // Appliquer les transformations
        layer.style.transform = `scale(${scale}) translateY(${offset}px)`;
        layer.style.clipPath = `circle(${100 - scrollPosition * 0.05}% at 50% 50%)`;
    });
});
