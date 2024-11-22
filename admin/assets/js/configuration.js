const segmentButtons = document.querySelectorAll(".segment-button");

segmentButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
        // Remove active class from all buttons
        segmentButtons.forEach((btn) => btn.classList.remove("active"));

        // Add active class to the clicked button
        event.target.classList.add("active");

        // Get the currently selected option
        const selectedOption = event.target.getAttribute("data-option");
        if (selectedOption == "admission") {
            const formAdministration = document.getElementById("by_administration");
            formAdministration.style.display = "none";

            const formNotifications = document.getElementById("by_notifications");
            formNotifications.style.display = "none";

            const formMoodle = document.getElementById("by_moodle");
            formMoodle.style.display = "none";

            const formOffers = document.getElementById("by_offers");
            formOffers.style.display = "none";

            const formAdmission = document.getElementById("by_admission");
            formAdmission.style.display = "block";

            // form notifications
            document.querySelector('input[name="email_coordination"]').required = false;
            document.querySelector('input[name="email_academic_management"]').required = false;
            document.querySelector('input[name="email_manager"]').required = false;

            // form administration
            document.querySelector('input[name="payment_due"]').required = false;

            // form moodle
            document.querySelector('input[name="moodle_url"]').required = false;
            document.querySelector('input[name="moodle_token"]').required = false;

            // form admission
            document.querySelector('input[name="documents_ok"]').required = true;
            document.querySelector('input[name="documents_warning"]').required = true;
            document.querySelector('input[name="documents_red"]').required = true;
        } else if(selectedOption == "administration") {
            const formAdmission = document.getElementById("by_admission");
            formAdmission.style.display = "none";

            const formNotifications = document.getElementById("by_notifications");
            formNotifications.style.display = "none";

            const formMoodle = document.getElementById("by_moodle");
            formMoodle.style.display = "none";

            const formOffers = document.getElementById("by_offers");
            formOffers.style.display = "none";

            const formAdministration = document.getElementById("by_administration");
            formAdministration.style.display = "block";

            // form notifications
            document.querySelector('input[name="email_coordination"]').required = false;
            document.querySelector('input[name="email_academic_management"]').required = false;
            document.querySelector('input[name="email_manager"]').required = false;

            // form administration
            document.querySelector('input[name="payment_due"]').required = true;

            // form moodle
            document.querySelector('input[name="moodle_url"]').required = false;
            document.querySelector('input[name="moodle_token"]').required = false;

            // form admission
            document.querySelector('input[name="documents_ok"]').required = false;
            document.querySelector('input[name="documents_warning"]').required = false;
            document.querySelector('input[name="documents_red"]').required = false;
        } else if(selectedOption == "moodle") {
            const formAdmission = document.getElementById("by_admission");
            formAdmission.style.display = "none";

            const formNotifications = document.getElementById("by_notifications");
            formNotifications.style.display = "none";

            const formAdministration = document.getElementById("by_administration");
            formAdministration.style.display = "none";

            const formOffers = document.getElementById("by_offers");
            formOffers.style.display = "none";

            const formMoodle = document.getElementById("by_moodle");
            formMoodle.style.display = "block";

            // form notifications
            document.querySelector('input[name="email_coordination"]').required = false;
            document.querySelector('input[name="email_academic_management"]').required = false;
            document.querySelector('input[name="email_manager"]').required = false;

            // form administration
            document.querySelector('input[name="payment_due"]').required = false;

            // form moodle
            document.querySelector('input[name="moodle_url"]').required = true;
            document.querySelector('input[name="moodle_token"]').required = true;

            // form admission
            document.querySelector('input[name="documents_ok"]').required = false;
            document.querySelector('input[name="documents_warning"]').required = false;
            document.querySelector('input[name="documents_red"]').required = false;
        }  else if(selectedOption == "offers") {
            const formAdmission = document.getElementById("by_admission");
            formAdmission.style.display = "none";

            const formNotifications = document.getElementById("by_notifications");
            formNotifications.style.display = "none";

            const formAdministration = document.getElementById("by_administration");
            formAdministration.style.display = "none";

            const formMoodle = document.getElementById("by_moodle");
            formMoodle.style.display = "none";

            const formOffers = document.getElementById("by_offers");
            formOffers.style.display = "block";

            // form notifications
            document.querySelector('input[name="email_coordination"]').required = false;
            document.querySelector('input[name="email_academic_management"]').required = false;
            document.querySelector('input[name="email_manager"]').required = false;

            // form administration
            document.querySelector('input[name="payment_due"]').required = false;

            // form moodle
            document.querySelector('input[name="moodle_url"]').required = false;
            document.querySelector('input[name="moodle_token"]').required = false;

            // form admission
            document.querySelector('input[name="documents_ok"]').required = false;
            document.querySelector('input[name="documents_warning"]').required = false;
            document.querySelector('input[name="documents_red"]').required = false;
        } else {
            const formAdmission = document.getElementById("by_admission");
            formAdmission.style.display = "none";

            const formAdministration = document.getElementById("by_administration");
            formAdministration.style.display = "none";

            const formMoodle = document.getElementById("by_moodle");
            formMoodle.style.display = "none";

            const formOffers = document.getElementById("by_offers");
            formOffers.style.display = "none";

            const formNotifications = document.getElementById("by_notifications");
            formNotifications.style.display = "block";

            // form notifications
            document.querySelector('input[name="email_coordination"]').required = true;
            document.querySelector('input[name="email_academic_management"]').required = true;
            document.querySelector('input[name="email_manager"]').required = true;

            // form administration
            document.querySelector('input[name="payment_due"]').required = false;

            // form moodle
            document.querySelector('input[name="moodle_url"]').required = false;
            document.querySelector('input[name="moodle_token"]').required = false;

            // form admission
            document.querySelector('input[name="documents_ok"]').required = false;
            document.querySelector('input[name="documents_warning"]').required = false;
            document.querySelector('input[name="documents_red"]').required = false;
            
        }
    });
});
