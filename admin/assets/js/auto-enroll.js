document.addEventListener("DOMContentLoaded", () => {
  console.log(ajax_object)
  const enrollAllButton = document.getElementById("enroll-all-button");
  enrollAllButton?.addEventListener("click", () => {
    const XHR = new XMLHttpRequest();
    XHR.open(
      "POST",
      `${ajax_object.ajax_url}?action=auto_enroll_students_bulk`,
      true
    );
    XHR.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    XHR.responseType = "json";

    const params = new URLSearchParams({
      action: "auto_enroll_students_bulk",
    });

    XHR.onload = () => {
      if (XHR.status === 200 && XHR.response && XHR.response) {
        console.log("Enrollment successful:", XHR.response);
        alert("All students have been enrolled successfully.");
        location.reload();
      }
    };

    XHR.send(params.toString());
  });
});
