document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const userIcon = document.getElementById('userIcon');
    const userDropdown = document.getElementById('userDropdown');
    const guestLinks = document.getElementById('guestLinks');
    const userLinks = document.getElementById('userLinks');
    const loginLink = document.getElementById('loginLink');
    const registerLink = document.getElementById('registerLink');
    const logoutLink = document.getElementById('logoutLink');
    const saberMasBtn = document.getElementById('saberMasBtn');
    const notification = document.getElementById('notification');
    const notificationText = document.getElementById('notificationText');

    // Estado de autenticación desde PHP
    let isAuthenticated = window.authData.isAuthenticated;
    let userName = window.authData.userName || 'Usuario';
    
    // Actualizar UI según estado de autenticación
    function updateAuthUI() {
        if (isAuthenticated) {
            if (guestLinks) guestLinks.style.display = 'none';
            if (userLinks) userLinks.style.display = 'block';
            userIcon.innerHTML = '<i class="fas fa-user-check" aria-hidden="true"></i>';
            userIcon.classList.add('authenticated');
        } else {
            if (guestLinks) guestLinks.style.display = 'block';
            if (userLinks) userLinks.style.display = 'none';
            userIcon.innerHTML = '<i class="fas fa-user" aria-hidden="true"></i>';
            userIcon.classList.remove('authenticated');
        }
    }

    // Inicializar UI
    updateAuthUI();

    // Manejar clic en ícono de usuario
    function toggleUserDropdown() {
        if (userDropdown) {
            const isActive = userDropdown.classList.toggle('active');
            userIcon.setAttribute('aria-expanded', isActive);
            userDropdown.setAttribute('aria-hidden', !isActive);
        }
    }

    if (userIcon) {
        userIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            toggleUserDropdown();
        });
        
        userIcon.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleUserDropdown();
            }
        });
    }

    // Cerrar dropdown al hacer clic fuera
    document.addEventListener('click', (e) => {
        if (userDropdown && userDropdown.classList.contains('active') && 
            !userDropdown.contains(e.target) && 
            !userIcon.contains(e.target)) {
            toggleUserDropdown();
        }
    });

    // MANEJO DE INICIO DE SESIÓN
    if (loginLink) {
        loginLink.addEventListener('click', (e) => {
            e.preventDefault();
            sessionStorage.setItem('returnUrl', window.location.href);
            window.location.href = 'inicio-sesion.php';
        });
    }

    // MANEJO DE REGISTRO
    if (registerLink) {
        registerLink.addEventListener('click', (e) => {
            e.preventDefault();
            sessionStorage.setItem('returnUrl', window.location.href);
            window.location.href = 'registro.php';
        });
    }

    // Manejar cierre de sesión
    if (logoutLink) {
        logoutLink.addEventListener('click', (e) => {
            e.preventDefault();
            window.location.href = 'logout.php';
        });
    }

    // Botón Saber Más
    if (saberMasBtn) {
        saberMasBtn.addEventListener('click', () => {
            window.location.href = '../html/Parqueacuatico.html';
        });
    }

    // Manejar tecla Escape para cerrar modales
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            if (userDropdown && userDropdown.classList.contains('active')) {
                toggleUserDropdown();
            }
        }
    });

    // Función para mostrar notificaciones
    function showNotification(message, type) {
        if (!notification || !notificationText) return;
        
        notificationText.textContent = message;
        notification.className = `notification ${type} active`;
        
        setTimeout(() => {
            notification.classList.remove('active');
        }, 3000);
    }

    // Mensaje de bienvenida
    if (isAuthenticated) {
        setTimeout(() => {
            showNotification(`¡Bienvenido de vuelta, ${userName}!`, 'info');
        }, 1000);
    }
    
    // Verificar si venimos de un login exitoso
    if (sessionStorage.getItem('loginSuccess')) {
        showNotification('¡Inicio de sesión exitoso!', 'success');
        sessionStorage.removeItem('loginSuccess');
    }
});