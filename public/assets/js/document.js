tippy(".help-tooltip", {
  allowHTML: true,
});

const progressButton = document.getElementById("progressButton");
const progressBar = document.getElementById("progressBar");
const buttonText = document.getElementById("buttonText");
const formDocument = document.getElementById("send-documents-student");

progressButton.addEventListener("click", function () {
  if (this.classList.contains("active")) return; // Evitar múltiples clics

  progressButton.style.backgroundColor = "#ffffff !important";
  this.classList.add("active");
  buttonText.textContent = "Loading...";

  const formData = new FormData(formDocument);
  const xhr = new XMLHttpRequest();

  xhr.upload.addEventListener("progress", function (e) {
    if (e.lengthComputable) {
      const percent = (e.loaded / e.total) * 100;
      progressBar.style.width = percent + "%";
    }
  });

  xhr.addEventListener("load", function () {
    // Resetear después de completar
    setTimeout(() => {
      window.scrollTo(0, 0);
      window.location.reload();
    }, 500);
  });

  xhr.open("POST", save_documents.url, true);
  xhr.send(formData);
});

// Variables globales
let cropper;
const $result = document.getElementById('result');

document.getElementById('student_photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    if (file) {
        if (!file.type.match(/^image\//)) {
            alert("Tipo de archivo inválido! Por favor selecciona una imagen.");
            return;
        }

        const reader = new FileReader();
        const imagePreview = document.getElementById('imagePreview');

        reader.onload = function(evt) {
            // Limpiar instancia anterior de Cropper
            if (cropper) {
                cropper.destroy();
            }

            imagePreview.src = evt.target.result;
            // Inicializar Cropper
            cropper = new Cropper(imagePreview, {
                aspectRatio: 16 / 9,
                viewMode: 1,
                autoCropArea: 1,
                responsive: true,
                restore: false
            });
        };
        
        reader.readAsDataURL(file);
    } else {
        alert('No se seleccionó ningún archivo.');
    }
});

// Manejar botón de recorte
document.getElementById('btnCrop').addEventListener('click', function() {
  if (cropper) {
      const croppedCanvas = cropper.getCroppedCanvas();
      
      // Convertir el canvas a Blob
      croppedCanvas.toBlob((blob) => {
          // Crear un File a partir del Blob
          const file = new File([blob], 'imagen_recortada.png', {
              type: 'image/png'
          });

          // Crear un DataTransfer para asignar el archivo al input
          const dataTransfer = new DataTransfer();
          dataTransfer.items.add(file);
          
          // Asignar los archivos al input
          document.getElementById('student_photo').files = dataTransfer.files;

          // Mostrar previsualización
          const croppedImage = document.createElement('img');
          croppedImage.src = URL.createObjectURL(blob);
          $result.appendChild(croppedImage);
          
          // Limpiar cropper si es necesario
          cropper.destroy();
      }, 'image/png');
  }
});

// Manejar botón de restauración
document.getElementById('btnRestore').addEventListener('click', function() {
    if (cropper) {
        cropper.reset();
        $result.innerHTML = '';
    }
});