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
        setCookie('message', 'All students have been enrolled successfully.');
        location.reload();
      }
    };

    XHR.send(params.toString());
  });

  function setCookie(name, value) {
    var date = new Date();
    date.setTime(date.getTime() + 1 * 24 * 60 * 60 * 1000);
    expires = "; expires=" + date.toGMTString();
    document.cookie = name + "=" + value + expires + "; path=/";
  }
});
