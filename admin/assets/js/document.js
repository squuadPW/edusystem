document.addEventListener('DOMContentLoaded',function(){

    buttons_change_status = document.querySelectorAll('.change-status');

    if(buttons_change_status){

        buttons_change_status.forEach((button) => {

            button.addEventListener('click',(e) => {

                document_id = button.getAttribute('data-document-id');
                status_id = button.getAttribute('data-status');
                student_id = button.getAttribute('data-student-id');

                let htmlLoading = "";
                htmlLoading += "<td class='column-primary id column-id' colspan='3' style='text-align:center;float:none;'><span class='spinner is-active' style='float:none;'></span></td>";
                    
                document.getElementById('tr_document_' + document_id).innerHTML = htmlLoading;
                document.getElementById('notice-status').style.display = "none";

                const XHR= new XMLHttpRequest();
                XHR.open('POST',update_status_documents.url,true);
                XHR.setRequestHeader('Content-type','application/x-www-form-urlencoded');
                XHR.responseType ='text';
                XHR.send('action='+ update_status_documents.action + "&student_id=" + student_id + '&document_id=' + document_id + '&status=' + status_id);
                XHR.onload = function(){

                    if(this.readyState=='4' && XHR.status === 200) {
            
                        let result = JSON.parse(XHR.responseText);
                        if(result.status == "success"){
                            document.getElementById('notice-status').style.display = "block";
                            document.getElementById('tr_document_' + document_id).innerHTML = result.html;
                            
                            setTimeout(() => {
                                document.getElementById('notice-status').style.display = "none"; 
                            },2000);
                        }
                    }
                }
            });
        });
    }
});