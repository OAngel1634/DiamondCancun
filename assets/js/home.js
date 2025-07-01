// home.js
document.addEventListener('DOMContentLoaded', () => {
  // Actualizar fecha y hora en widget de clima
  function updateDateTime() {
    const now = new Date();
    const options = { 
      weekday: 'long', 
      year: 'numeric', 
      month: 'long', 
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    };
    document.getElementById('currentDateTime').textContent = 
      now.toLocaleDateString('es-ES', options);
  }
  
  updateDateTime();
  setInterval(updateDateTime, 60000); // Actualizar cada minuto

  // Login overlay
  const loginLink = document.getElementById('loginLink');
  const closeLogin = document.getElementById('closeLogin');
  const loginOverlay = document.getElementById('loginOverlay');
  
  if(loginLink) {
    loginLink.addEventListener('click', (e) => {
      e.preventDefault();
      loginOverlay.style.display = 'flex';
    });
  }
  
  if(closeLogin) {
    closeLogin.addEventListener('click', () => {
      loginOverlay.style.display = 'none';
    });
  }
  
  // Cerrar overlay al hacer clic fuera
  loginOverlay.addEventListener('click', (e) => {
    if(e.target === loginOverlay) {
      loginOverlay.style.display = 'none';
    }
  });

  // Manejo de formulario de login
  const loginForm = document.getElementById('loginForm');
  if(loginForm) {
    loginForm.addEventListener('submit', (e) => {
      e.preventDefault();
      
      // Simulación de login (en producción usaría una petición AJAX real)
      const email = document.getElementById('loginEmail').value;
      const password = document.getElementById('loginPassword').value;
      
      if(email && password) {
        // Aquí iría la lógica real de autenticación
        alert(`Inicio de sesión exitoso para: ${email}`);
        loginOverlay.style.display = 'none';
      }
    });
  }
});