document.addEventListener('DOMContentLoaded',function(){
    let button_create =  document.getElementById('create-enrollment');
    if (button_create) {
        button_create.disabled = true;
    }
    let button_search =  document.getElementById('search-student');
    if (button_search) {
        button_search.addEventListener('click', (e) => {
            document.getElementById('search-student').disabled = true;
            document.getElementById('search-student').innerText = 'Loading...';
            let id_document = document.querySelector('input[name=id_document]').value;
            if (!id_document) {
                alert('Please enter the ID of the student to be consulted.');
                return;
            }
            const XHR = new XMLHttpRequest();
            XHR.open("POST", search_student_id_document.url, true);
            XHR.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            XHR.responseType = "text";
            XHR.send(
              "action=" + search_student_id_document.action + "&id_document=" + id_document
            );
            XHR.onload = function () {
              if (this.readyState == "4" && XHR.status === 200) {
                let result = JSON.parse(XHR.responseText);
                let student = result.student;

                if (!student) {
                    document.getElementById('search-student').disabled = false;
                    document.getElementById('search-student').innerText = 'Search student by id document';
                    document.getElementById('type_document').innerText = '';
                    document.getElementById('id_document').innerText = '';
                    document.getElementById('full_name').innerText = '';
                    document.getElementById('email').innerText = '';
                    document.getElementById('phone').innerText = '';
                    document.getElementById('country').innerText = '';
                    document.getElementById('institute').innerText = '';
                    document.querySelector('input[name=student_id]').value = '';
                    alert('We did not find any students for the requested id.');
                    button_create.disabled = true;
                    return;
                }

                document.getElementById('type_document').innerText = student?.type_document;
                document.getElementById('id_document').innerText = student?.id_document;
                document.getElementById('full_name').innerText = `${student.name} ${student.middle_name} ${student.last_name} ${student.middle_last_name}`;
                document.getElementById('email').innerText = student?.email;
                document.getElementById('phone').innerText = student?.phone;
                document.getElementById('country').innerText = student?.country;
                document.getElementById('institute').innerText = student?.name_institute;
                document.querySelector('input[name=student_id]').value = student?.id;

                document.getElementById('search-student').disabled = false;
                document.getElementById('search-student').innerText = 'Search student by id document';
                button_create.disabled = false;
              }
            };
        })
    }

});