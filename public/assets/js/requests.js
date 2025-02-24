let add_new_request = document.getElementById("add_new_request");
if (add_new_request) {
  add_new_request.addEventListener("click", function () {
    document.getElementById("modal-request").style.display = "block";
  });
}

let modal_close = document.querySelectorAll(".modal-close");
if (modal_close) {
  modal_close.forEach((close) => {
    close.addEventListener("click", function () {
      document.getElementById("modal-request").style.display = "none";
      document.getElementById("modal-detail-request").style.display = "none";
    });
  });
}

let send_request = document.getElementById("send-request");
if (send_request) {
  send_request.addEventListener("click", function () {
    document.getElementById("send-request").disabled = true;
    document.getElementById("send-request").innerText = "Loading...";
    let type_id = document.querySelector('select[name="type_id"]').value;
    let reason = document.querySelector('textarea[name="reason"]').value;
    let student_id = null;
    let partner_id = null;
    let by = "Parent";
    if (document.querySelector('select[name="student_id"]')) {
      student_id = document.querySelector('select[name="student_id"]').value;
    } else {
      by = "Student";
      student_id = document.querySelector('input[name="student_id"]').value;
      partner_id = document.querySelector('input[name="partner_id"]').value;
    }

    const XHR = new XMLHttpRequest();
    XHR.open("POST", `${ajax_object.ajax_url}?action=send_request`, true);
    XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XHR.responseType = "text";
    let params = `action=send_request`;
    if (type_id) {
      params += `&type_id=${type_id}`;
    }
    if (reason) {
      params += `&reason=${reason}`;
    }
    if (student_id) {
      params += `&student_id=${student_id}`;
    }
    if (partner_id) {
      params += `&partner_id=${partner_id}`;
    }
    if (by) {
      params += `&by=${by}`;
    }

    XHR.send(params);
    XHR.onload = function () {
      if (XHR.status === 200) {
        location.reload();
      } else {
        document.getElementById("send-request").disabled = false;
        document.getElementById("send-request").innerText = "Save";
      }
    };
  });
}

function view_details_request(type, description, response, status) {
  document.getElementById("request_type").innerText = type == "" ? "N/A" : type;
  document.getElementById("request_description").innerText =
    description == "" ? "N/A" : description;
  document.getElementById("request_response").innerText =
    response == "" ? "N/A" : response;
  document.getElementById("request_status").innerText =
    status == "" ? "N/A" : status;
  document.getElementById("modal-detail-request").style.display = "block";
}
