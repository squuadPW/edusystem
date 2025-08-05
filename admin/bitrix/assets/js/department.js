document.addEventListener('DOMContentLoaded',function(){

    delete_department = document.getElementById('delete_department');

    if(delete_department){

        delete_department.addEventListener('click',(e) => {

            id = delete_department.getAttribute('data-id');
            document.getElementById('delete_department_id').value = id;
            document.getElementById('modalDeleteDepartment').style.display = "block";
        });
    }

    document.querySelectorAll('.modal-close').forEach((close) => {
        close.addEventListener('click',(e) => {
            document.getElementById('modalDeleteDepartment').style.display = "none";
        });
    });
});