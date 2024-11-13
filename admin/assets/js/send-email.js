const segmentButtons = document.querySelectorAll(".segment-button");

segmentButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
        // Remove active class from all buttons
        segmentButtons.forEach((btn) => btn.classList.remove("active"));

        // Add active class to the clicked button
        event.target.classList.add("active");

        // Get the currently selected option
        const selectedOption = event.target.getAttribute("data-option");
        if (selectedOption == "group") {
            const formEmail = document.getElementById("by_email");
            formEmail.style.display = "none";

            const formGroup = document.getElementById("by_group");
            formGroup.style.display = "block";

            document.querySelector('input[name="type"]').value = '1';

            document.querySelector('select[name="academic_period"]').required = true;
            document.querySelector('select[name="academic_period_cut"]').required = true;
            document.querySelector('input[name="email_student"]').required = false;
        } else {
            const formOthers = document.getElementById("by_group");
            formOthers.style.display = "none";

            const formMe = document.getElementById("by_email");
            formMe.style.display = "block";

            document.querySelector('input[name="type"]').value = '2';

            document.querySelector('select[name="academic_period"]').required = false;
            document.querySelector('select[name="academic_period_cut"]').required = false;
            document.querySelector('input[name="email_student"]').required = true;
        }
    });
});
