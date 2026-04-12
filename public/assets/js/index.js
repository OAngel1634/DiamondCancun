document.addEventListener('DOMContentLoaded', () => {
    const userIcon = document.getElementById('userIcon');
    const userDropdown = document.getElementById('userDropdown');

    userIcon.addEventListener('click', (e) => {
        e.stopPropagation(); // Evita que el clic se propague al documento
        const isExpanded = userIcon.getAttribute('aria-expanded') === 'true';
        
        // Alternar visibilidad
        userIcon.setAttribute('aria-expanded', !isExpanded);
        userDropdown.classList.toggle('show');
        userDropdown.setAttribute('aria-hidden', isExpanded);
    });

    // Cerrar el menú si haces clic en cualquier otro lugar de la pantalla
    document.addEventListener('click', () => {
        if (userDropdown.classList.contains('show')) {
            userIcon.setAttribute('aria-expanded', 'false');
            userDropdown.classList.remove('show');
            userDropdown.setAttribute('aria-hidden', 'true');
        }
    });
});