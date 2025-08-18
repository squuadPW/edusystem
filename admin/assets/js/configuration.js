jQuery(document).ready(function ($) {
    $(".js-example-basic").select2();
});

// Configuration object that maps options to their forms and required fields
const formConfig = {
    admission: {
        formId: "by_admission",
        requiredFields: ["documents_ok", "documents_warning", "documents_red"]
    },
    administration: {
        formId: "by_administration",
        requiredFields: ["payment_due"]
    },
    moodle: {
        formId: "by_moodle",
        requiredFields: ["moodle_url", "moodle_token"]
    },
    crm: {
        formId: "by_crm",
        requiredFields: ["crm_url", "crm_token"]
    },
    offers: {
        formId: "by_offers",
        requiredFields: []
    },
    inscriptions: {
        formId: "inscriptions",
        requiredFields: [],
        hideSaveButton: true
    },
    notifications: {
        formId: "by_notifications",
        requiredFields: []
    },
    design: {
        formId: "by_design",
        requiredFields: []
    },
};

// Get all form IDs
const allFormIds = Object.values(formConfig).map(config => config.formId);

// Function to handle form visibility and required fields
function handleFormSelection(selectedOption) {
    // Hide all forms first
    allFormIds.forEach(formId => {
        const form = document.getElementById(formId);
        if (form) form.style.display = "none";
    });

    // Get configuration for selected option
    const config = formConfig[selectedOption] || formConfig.notifications;
    
    // Show selected form
    const selectedForm = document.getElementById(config.formId);
    if (selectedForm) selectedForm.style.display = "block";

    // Handle save button visibility
    const saveButton = document.getElementById("save-configuration");
    if (saveButton) {
        saveButton.style.display = config.hideSaveButton ? "none" : "block";
    }

    // Reset all required fields first
    Object.values(formConfig).forEach(config => {
        config.requiredFields.forEach(fieldName => {
            const field = document.querySelector(`input[name="${fieldName}"]`);
            if (field) field.required = false;
        });
    });

    // Set required fields for selected form
    config.requiredFields.forEach(fieldName => {
        const field = document.querySelector(`input[name="${fieldName}"]`);
        if (field) field.required = true;
    });
}

// Add click event listeners to segment buttons
const segmentButtons = document.querySelectorAll(".segment-button");
segmentButtons.forEach((button) => {
    button.addEventListener("click", (event) => {
        // Remove active class from all buttons
        segmentButtons.forEach((btn) => btn.classList.remove("active"));

        // Add active class to the clicked button
        event.target.classList.add("active");

        // Get the currently selected option and handle form selection
        const selectedOption = event.target.getAttribute("data-option");
        handleFormSelection(selectedOption);
    });
});
