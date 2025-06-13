document.addEventListener("DOMContentLoaded", function () {
  const segmentButtons = document.querySelectorAll(".segment-button-history");
  segmentButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
      segmentButtons.forEach((btn) => btn.classList.remove("active"));
      event.target.classList.add("active");
      const selectedOption = event.target.getAttribute("data-option");
      switch (selectedOption) {
        case 'current':
            document.getElementById('history').style.display = 'none';
            document.getElementById('current').style.display = 'block';
            break;
        case 'history':
            document.getElementById('current').style.display = 'none';
            document.getElementById('history').style.display = 'block';        
            break;
      }
    });
  });
});
