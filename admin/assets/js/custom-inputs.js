document.addEventListener("DOMContentLoaded", function () {
  const inputModeSelect = document.getElementById("input_mode_select");
  const inputTypeField = document.getElementById("input_type_field");
  const inputTypeSelect = document.getElementById("input_type_select");
  const inputOptionsField = document.getElementById("input_options_field");
  const inputOptionsTextarea = document.getElementById(
    "input_options_textarea"
  );

  function toggleFieldsVisibility() {
    const selectedMode = inputModeSelect.value;
    const selectedType = inputTypeSelect.value; // Get selected input type

    // Logic for Input Type field
    if (selectedMode === "input") {
      inputTypeField.style.display = "block";
      inputTypeSelect.setAttribute("required", "required");
    } else {
      inputTypeField.style.display = "none";
      inputTypeSelect.removeAttribute("required");
      inputTypeSelect.value = ""; // Clear selection when hidden
    }

    // Logic for Input Options field
    // Show if input_mode is 'select' OR if input_mode is 'input' and input_type is 'radio' or 'checkbox'
    if (
      selectedMode === "select" ||
      (selectedMode === "input" &&
        (selectedType === "radio" || selectedType === "checkbox"))
    ) {
      inputOptionsField.style.display = "block";
    } else {
      inputOptionsField.style.display = "none";
      inputOptionsTextarea.value = ""; // Clear content when hidden
    }
  }

  // Initial call to set visibility based on current values on page load
  toggleFieldsVisibility();

  // Listen for changes on the input mode select
  inputModeSelect.addEventListener("change", toggleFieldsVisibility);
  // Listen for changes on the input type select (important for radio/checkbox)
  inputTypeSelect.addEventListener("change", toggleFieldsVisibility);
});
