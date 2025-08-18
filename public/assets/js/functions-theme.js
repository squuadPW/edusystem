// Scroll al top cuando el DOM está listo
document.addEventListener('DOMContentLoaded', () => {
    window.scrollTo(0, 0);
    
    // Deshabilitar scroll restoration del navegador
    if (history.scrollRestoration) {
      history.scrollRestoration = 'manual';
    }
  });
  
  // Eliminar bloqueo después de carga completa
  window.addEventListener('load', () => {
    const scrollLockElement = document.getElementById('scrollLock');
    if (scrollLockElement) {
      scrollLockElement.remove();
    }
  
    document.documentElement.style.overflow = '';
    document.body.style.overflow = '';
  });