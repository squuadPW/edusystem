tippy(".help-tooltip", {
  allowHTML: true,
});

const progressButton = document.getElementById('progressButton');
const progressBar = document.getElementById('progressBar');
const buttonText = document.getElementById('buttonText');
const formDocument = document.getElementById('send-documents-student');

progressButton.addEventListener('click', function() {
    if (this.classList.contains('active')) return; // Evitar múltiples clics
    
    progressButton.style.backgroundColor = '#ffffff !important'
    this.classList.add('active');
    buttonText.textContent = 'Loading...';
    
    const formData = new FormData(formDocument);
    const xhr = new XMLHttpRequest();

    xhr.upload.addEventListener('progress', function(e) {
        if (e.lengthComputable) {
            const percent = (e.loaded / e.total) * 100;
            progressBar.style.width = percent + '%';
        }
    });

    xhr.addEventListener('load', function() {
        // Resetear después de completar
        setTimeout(() => {            
          window.scrollTo(0,0);
          window.location.reload();
        }, 500);
    });

    xhr.open("POST", save_documents.url, true);
    xhr.send(formData);
});