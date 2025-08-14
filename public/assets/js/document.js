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
let originalFile; // Variable para guardar la imagen original
const $result = document.getElementById("result");

document
  .getElementById("student_photo")
  .addEventListener("change", function (e) {
    const file = e.target.files[0];
    if (file) {
      if (!file.type.match(/^image\//)) {
        // alert("Tipo de archivo inválido! Por favor selecciona una imagen.");
        closeSelectorPhoto();
        return;
      }

      // Guardar la imagen original
      originalFile = file;

      const reader = new FileReader();
      const imagePreview = document.getElementById("imagePreview");

      reader.onload = function (evt) {
        // Limpiar instancia anterior de Cropper
        if (cropper) {
          cropper.destroy();
        }

        imagePreview.src = evt.target.result;
        // Inicializar Cropper
        cropper = new Cropper(imagePreview, {
          aspectRatio: 37 / 45,
          viewMode: 1,
          autoCropArea: 1,
          responsive: true,
          restore: false,
        });
      };

      reader.readAsDataURL(file);
      document.body.classList.add("modal-open");
      document.getElementById("modal-cropperjs").style.display = "block";
    } else {
      alert("No se seleccionó ningún archivo.");
    }
  });

// Manejar botón de recorte
document.getElementById("btnCrop").addEventListener("click", function () {
  if (cropper) {
    const croppedCanvas = cropper.getCroppedCanvas();

    // Convertir el canvas a Blob
    croppedCanvas.toBlob((blob) => {
      // Crear un File a partir del Blob
      const file = new File([blob], "imagen_recortada.jpeg", {
        type: "image/jpeg",
      });

      // Crear un DataTransfer para asignar el archivo al input
      const dataTransfer = new DataTransfer();
      dataTransfer.items.add(file);

      // Asignar los archivos al input
      document.getElementById("student_photo").files = dataTransfer.files;

      const croppedImage = document.createElement("img");
      croppedImage.src = URL.createObjectURL(blob);
      croppedImage.style.cssText = `
        width: 100%;
        height: auto;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -45%);
      `;
      
      $result.innerHTML = "";
      $result.appendChild(croppedImage);

      // Limpiar cropper si es necesario
      cropper.destroy();
      document.getElementById("pre-image").style.display = "none";
      document.getElementById("preview").style.display = "block";

      document.getElementById("pre-image-buttons").style.display = "none";
      document.getElementById("preview-buttons").style.display = "flex";
    }, "image/jpeg");
  }
});

// Manejar botón de restauración
document.getElementById("btnRestore").addEventListener("click", function () {
  if (cropper) {
    cropper.reset();
    $result.innerHTML = "";
  }
});

document.getElementById("btnConfirm").addEventListener("click", function () {
  document.getElementById("modal-cropperjs").style.display = "none";
  document.body.classList.remove("modal-open");

  clearCropper();
});

document.getElementById("btnBack").addEventListener("click", function () {
  const fileInput = document.getElementById("student_photo");

  // Restaurar la imagen original
  if (originalFile) {
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(originalFile);
    fileInput.files = dataTransfer.files;
  }

  // Disparar el evento change
  const event = new Event("change");
  fileInput.dispatchEvent(event);

  document.getElementById("pre-image").style.display = "block";
  document.getElementById("preview").style.display = "none";

  document.getElementById("pre-image-buttons").style.display = "flex";
  document.getElementById("preview-buttons").style.display = "none";
});

document.querySelector(".modal-close").addEventListener("click", function () {
  closeSelectorPhoto()
});

function closeSelectorPhoto() {
  const fileInput = document.getElementById("student_photo");

  // Resetear el input de tipo file
  fileInput.value = ""; // Esto elimina cualquier archivo seleccionado

  // También puedes usar DataTransfer para asegurarte de que no haya archivos
  const dataTransfer = new DataTransfer();
  fileInput.files = dataTransfer.files;

  document.getElementById("student_photo_label_input").textContent = "Select file";

  clearCropper();
}

function clearCropper() {

  document.getElementById("pre-image").style.display = "block";
  document.getElementById("preview").style.display = "none";

  document.getElementById("pre-image-buttons").style.display = "flex";
  document.getElementById("preview-buttons").style.display = "none";

  document.getElementById("modal-cropperjs").style.display = "none";
  document.body.classList.remove("modal-open");

  // Limpiar el resultado del recorte si existe
  $result.innerHTML = "";

  // Limpiar la instancia de Cropper si existe
  if (cropper) {
    cropper.destroy();
    cropper = null;
  }
}
