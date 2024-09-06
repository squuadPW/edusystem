<!-- Modal container -->
<div id="modal-contraseña" class="modal" style="overflow: auto; padding: 0 !important">
    <div class="modal-content modal-enrollment">
        <div class="modal-body">
            <div style="padding: 0 !important; text-align: center">
                <span style="font-size: 12px;">3105 NW 107 Avenue, Doral, FL 33172 | (786) 361 – 9307 |
                    www.American-elite.us</span>
                <h4 class="title-enrollment">ENROLLMENT AGREEMENT</h4>
            </div>
            <div class="container-enrollment">
                <h4 class="subtitle-enrollment">STUDENT PERSONAL INFORMATION</h4>
                <div class="d-flex p-0">
                    <div>
                        <label for="complete_name">Complete name</label>
                        <input type="text" name="complete_name">
                    </div>
                    <div>
                        <label for="date">Date</label>
                        <input type="text" name="date">
                    </div>
                </div>
                <div class="d-flex p-0">
                    <div>
                        <label for="birth_date">Date of Birth</label>
                        <input type="text" name="birth_date">
                    </div>
                    <div>
                        <label for="gender">Gender</label>
                        <input type="text" name="gender">
                    </div>
                </div>
                <div>
                    <label for="address">Address</label>
                    <input type="text" name="address">
                </div>
                <div>
                    <label for="country">Country</label>
                    <input type="text" name="country">
                </div>
                <div class="d-flex p-0">
                    <div>
                        <label for="phone">Phone</label>
                        <input type="text" name="phone">
                    </div>
                    <div>
                        <label for="cell">Cell</label>
                        <input type="text" name="cell">
                    </div>
                    <div>
                        <label for="parent_cell">Parents Cell</label>
                        <input type="text" name="parent_cell">
                    </div>
                </div>
                <div>
                    <label for="parent_identification">Identification of parent</label>
                    <input type="text" name="parent_identification">
                </div>
                <div>
                    <label for="child_identification">Identification of child</label>
                    <input type="text" name="child_identification">
                </div>
                <div>
                    <label for="parent_name">Parent/Legal Guardian Full Name</label>
                    <input type="text" name="parent_name">
                </div>
                <div>
                    <label for="parent_email">Email (Required to access parent portal):</label>
                    <input type="text" name="parent_email">
                </div>
            </div>
            <div class="container-enrollment">
                <h4 class="subtitle-enrollment">SCHOOL INFORMATION</h4>
                <div>
                    <label for="name_last_school">Name of Last or Concurrent School</label>
                    <input type="text" name="name_last_school">
                </div>
                <div>
                    <label for="school_address">School Address</label>
                    <input type="text" name="school_address">
                </div>
                <div>
                    <label for="school_phone">Phone</label>
                    <input type="text" name="school_phone">
                </div>
                <div>
                    <label for="last_date_attended">Last date Attended</label>
                    <input type="text" name="last_date_attended">
                </div>
                <div>
                    <label for="address">Last completed grade</label>

                    <div>
                        <input type="radio" id="grade_5" name="last_grade" value="5">
                        <label for="grade_5">5th Grade</label>
                        <input type="radio" id="grade_6" name="last_grade" value="6">
                        <label for="grade_6">6th Grade</label>
                        <input type="radio" id="grade_7" name="last_grade" value="7">
                        <label for="grade_7">7th Grade</label>
                        <input type="radio" id="grade_8" name="last_grade" value="8">
                        <label for="grade_8">8th Grade</label>
                        <input type="radio" id="grade_9" name="last_grade" value="9">
                        <label for="grade_9">9th Grade</label>
                        <input type="radio" id="grade_10" name="last_grade" value="10">
                        <label for="grade_10">10th Grade</label>
                        <input type="radio" id="grade_11" name="last_grade" value="11">
                        <label for="grade_11">11th Grade</label>
                        <input type="radio" id="grade_12" name="last_grade" value="12">
                        <label for="grade_12">12th Grade</label>
                    </div>
                </div>
            </div>
            <div class="container-enrollment">
                <h4 class="subtitle-enrollment">METHOD OF TUITION PAYMENT</h4>
                <div>
                    <label for="address">Method of tuition payment</label>

                    <div>
                        <p>
                            <input type="radio" id="complete" name="tuition_payment" value="1">
                            <label for="complete">Full Academic year payment at time of signing enrollment
                                agreement</label>
                        </p>
                        <p>
                            <input type="radio" id="cuote" name="tuition_payment" value="2">
                            <label for="cuote">Balance paid prior to graduation through payment plan, according to an
                                agreed upon schedule. Final transcript and diploma is
                                contingent upon full balance payment.</label>
                        </p>
                    </div>
                </div>
            </div>
            <div class="container-enrollment">
                <h4 class="subtitle-enrollment">STUDENT AGREEMENT</h4>
                <div>
                    <p>By signing the agreement herein, the student and their parent/guardian enter into agreement with
                        American Elite School (AES), under
                        which the student/parent/guardian will pay tuition and fees and adhere to the school’s policies
                        including refund policies as set forth in
                        AES website and catalog. In signing this application, the student understands that successful
                        completion of all courses and exams and
                        full payment are required for graduation. The student also certifies that the information
                        included
                        on this form is correct.
                    </p>

                    <p style="color: red; font-style: italic">PLEASE NOTE: You must enroll in a course within the first
                        three months of
                        acceptance to the program. Failure to do so will result in
                        being dropped from the program and you will have to reapply for admission.</p>

                    <div class="d-flex p-0">
                        <div>
                            <div class="p-0 mb-10">
                                <label for="complete_name">Applicant name</label>
                                <input type="text" name="complete_name">
                            </div>
                            <p>Applicant signature</p>
                            <canvas id="signature-student" width="100%" height="200"></canvas>
                            <button id="clear-student">Clear</button>
                        </div>
                        <div>
                            <div class="p-0 mb-10">
                                <label for="parent_name">Parent/Legal Guardian Name</label>
                                <input type="text" name="parent_name">
                            </div>
                            <p>Parent / Guardian signature</p>
                            <canvas id="signature-parent" width="100%" height="200"></canvas>
                            <button id="clear-parent" class="mb-10">Clear</button>
                        </div>
                    </div>
                    <div>
                        <label for="date">Date</label>
                        <input type="text" name="date">
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="text-align: center; display: block">
            <button type="button" class="submit">Generar</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>
<script>

    let signaturePadStudent;
    let signaturePadParent;

    function resizeCanvas(canvasId) {
        const canvas = document.getElementById(canvasId);
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        let width, height;

        // Set different canvas sizes based on screen sizes
        if (window.matchMedia("(max-width: 768px)").matches) { // mobile
            width = 150;
            height = 100;
        } else { // laptop
            width = 300;
            height = 100;
        }

        canvas.width = width * ratio;
        canvas.height = height * ratio;
        canvas.style.width = width + 'px';
        canvas.style.height = height + 'px';
        canvas.getContext("2d").scale(ratio, ratio);
    }

    window.addEventListener("resize", function () {
        resizeCanvas('signature-student');
        resizeCanvas('signature-parent');
    });

    window.addEventListener("orientationchange", function () {
        resizeCanvas('signature-student');
        resizeCanvas('signature-parent');
    });

    resizeCanvas('signature-student');
    resizeCanvas('signature-parent');

    // Create the SignaturePad objects after the canvas elements have been resized
    signaturePadStudent = new SignaturePad(document.getElementById('signature-student'));
    signaturePadParent = new SignaturePad(document.getElementById('signature-parent'));

    document.getElementById('clear-student').addEventListener('click', () => {
        signaturePadStudent.clear();
    });

    document.getElementById('clear-parent').addEventListener('click', () => {
        signaturePadParent.clear();
    });

</script>