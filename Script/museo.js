 // Script para el formulario de reservas
    document.querySelector('form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Mostrar mensaje de confirmación
      alert('¡Gracias por tu reserva! Te hemos enviado un correo de confirmación con los detalles de tu tour.');
      
      // Resetear formulario
      this.reset();
    });
    
    // Cambiar color de fondo del nav al hacer scroll
    window.addEventListener('scroll', function() {
      const nav = document.querySelector('nav');
      if (window.scrollY > 50) {
        nav.style.background = 'rgba(10, 30, 60, 0.95)';
      } else {
        nav.style.background = 'rgba(0, 0, 0, 0.4)';
      }
    });