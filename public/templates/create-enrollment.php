<!-- Modal container -->
<div id="modal-contraseña" class="modal" style="overflow: auto; padding: 0 !important">
    <div class="modal-content modal-enrollment">
        <div class="modal-body" id="content-pdf">
            <div style="padding: 0 !important; text-align: center">
                <span style="font-size: 12px;">3105 NW 107 Avenue, Doral, FL 33172 | (786) 361 – 9307 |
                    www.American-elite.us</span>
                <h4 style="padding: 4px; text-align: center; font-weight: bold; font-size: 20px;">ENROLLMENT AGREEMENT
                </h4>
            </div>
            <div style="padding: 0; margin: 10px 0px !important; border: 1px solid gray;" id="student-information">
                <h4 style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
                    STUDENT PERSONAL INFORMATION</h4>
                <div style="padding: 0 !important; display: flex;">
                    <div style="flex: 50%;padding: 8px;">
                        <label for="complete_name">Complete name</label>
                        <input type="text" name="complete_name" value="<?php echo $user['student_full_name'] ?>"
                            disabled>

                        <input type="hidden" name="student_user_id" value="<?php echo $student_id ?>">
                        <input type="hidden" name="parent_user_id" value="<?php echo $partner_id ?>">
                    </div>
                    <div style="flex: 50%;padding: 8px;">
                        <label for="date">Date</label>
                        <input type="text" name="date" value="<?php echo $user['student_created_at'] ?>" disabled>
                    </div>
                </div>
                <div style="padding: 0 !important; display: flex">
                    <div style="flex: 50%;padding: 8px;">
                        <label for="birth_date">Date of Birth</label>
                        <input type="text" name="birth_date" value="<?php echo $user['student_birth_date'] ?>" disabled>
                    </div>
                    <div style="flex: 50%;padding: 8px;">
                        <label for="gender">Gender</label>
                        <input type="text" name="gender" value="<?php echo $user['student_gender'] ?>" disabled>
                    </div>
                </div>
                <div style="padding: 8px;">
                    <label for="address">Address</label>
                    <input type="text" name="address" value="<?php echo $user['student_address'] ?>" disabled>
                </div>
                <div style="padding: 8px;">
                    <label for="country">Country</label>
                    <input type="text" name="country" value="<?php echo $user['student_country'] ?>" disabled>
                </div>
                <div style="padding: 0 !important; display: flex;">
                    <div style="flex: 50%; padding: 8px">
                        <label for="phone">Phone</label>
                        <input type="text" name="phone" value="<?php echo $user['student_phone'] ?>" disabled>
                    </div>
                    <div style="flex: 50%; padding: 8px">
                        <label for="cell">Cell</label>
                        <input type="text" name="cell" value="<?php echo $user['student_phone'] ?>" disabled>
                    </div>
                    <div style="flex: 50%; padding: 8px">
                        <label for="parent_cell">Parents Cell</label>
                        <input type="text" name="parent_cell" value="<?php echo $user['parent_cell'] ?>" disabled>
                    </div>
                </div>
                <div style="padding: 8px;">
                    <label for="parent_identification">Identification of parent</label>
                    <input type="text" name="parent_identification" value="<?php echo $user['parent_identification'] ?>"
                        disabled>
                </div>
                <div style="padding: 8px;">
                    <label for="child_identification">Identification of child</label>
                    <input type="text" name="child_identification" value="<?php echo $user['student_identification'] ?>"
                        disabled>
                </div>
                <div style="padding: 8px;">
                    <label for="parent_name">Parent/Legal Guardian Full Name</label>
                    <input type="text" name="parent_name" value="<?php echo $user['parent_full_name'] ?>" disabled>
                </div>
                <div style="padding: 8px;">
                    <label for="parent_email">Email (Required to access parent portal):</label>
                    <input type="text" name="parent_email" value="<?php echo $user['parent_email'] ?>" disabled>
                </div>
                <div style="padding: 8px;">
                    <label for="parent_email">Email (Required to access student portal):</label>
                    <input type="text" name="student_email" value="<?php echo $user['student_email'] ?>" disabled>
                </div>
            </div>
            <div style="padding: 0; margin: 10px 0px !important; border: 1px solid gray;" id="school-information">
                <h4 style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
                    SCHOOL INFORMATION</h4>
                <div style="padding: 8px;">
                    <label for="name_last_school">Name of Last or Concurrent School</label>
                    <input type="text" name="name_last_school"
                        value="<?php echo isset($institute) ? $institute->name : $institute_name ?>" disabled>
                </div>
                <div style="padding: 8px;">
                    <label for="school_address">School Address</label>
                    <input type="text" name="school_address"
                        value="<?php echo isset($institute) ? $institute->address : '' ?>" <?php echo isset($institute) ? 'disabled' : '' ?>>
                </div>
                <div style="padding: 8px;">
                    <label for="school_phone">Phone</label>
                    <input type="text" name="school_phone"
                        value="<?php echo isset($institute) ? $institute->phone : '' ?>" <?php echo isset($institute) ? 'disabled' : '' ?>>
                </div>
                <div style="padding: 8px;">
                    <label for="last_date_attended">Last date Attended</label>
                    <input type="text" name="last_date_attended">
                </div>
                <div style="padding: 8px;">
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
            <div style="padding: 0; margin: 10px 0px !important; border: 1px solid gray;" id="tuition-payment">
                <h4 style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
                    METHOD OF TUITION PAYMENT</h4>
                <div style="padding: 8px;">
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
            <div style="padding: 0; margin: 10px 0px !important; border: 1px solid gray;" id="student-agreement">
                <h4 style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
                    STUDENT AGREEMENT</h4>
                <div style="padding: 8px;">
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

                    <div style="padding: 0 !important; display: flex">
                        <div style="flex: 50%;">
                            <div style="padding: 8px !important; margin-bottom: 10px;">
                                <label for="complete_name">Applicant name</label>
                                <input type="text" name="complete_name" value="<?php echo $user['student_full_name'] ?>"
                                    disabled>
                            </div>
                            <div style="padding: 8px !important;">
                                <p>Applicant signature</p>
                                <?php if (!isset($parent_signature)) { ?>
                                    <canvas id="signature-student" width="100%" height="200"
                                        style="border-bottom: 1px solid gray"></canvas>
                                    <button id="clear-student">Clear</button>
                                <?php } else { ?>
                                    <p>Firma ya realizada</p>
                                <?php } ?>
                            </div>
                        </div>
                        <div style="flex: 50%">
                            <div style="padding: 8px !important; margin-bottom: 10px;">
                                <label for="parent_name">Parent/Legal Guardian Name</label>
                                <input type="text" name="parent_name" value="<?php echo $user['parent_full_name'] ?>"
                                    disabled>
                            </div>
                            <div style="padding: 8px !important;">
                                <p>Parent / Guardian signature</p>
                                <?php if (!isset($parent_signature)) { ?>
                                    <canvas id="signature-parent" width="100%" height="200"
                                        style="border-bottom: 1px solid gray"></canvas>
                                    <button id="clear-parent" style="margin-bottom: 10px;">Clear</button>
                                <?php } else { ?>
                                    <p>Firma ya realizada</p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div style="padding: 8px;">
                        <label for="date">Date</label>
                        <input type="text" name="date" value="<?php echo $user['today'] ?>" disabled>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer" style="text-align: center; display: block">
            <button type="button" class="submit" id="saveSignatures">Save</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"
    integrity="sha512-GsLlZN/3F2ErC5ifS5QtgpiJtWd43JWSuIgh7mbzZ8zBps+dvLusV+eNQATqgA/HdeKFVgA5v3S/cIrLF7QnIg=="
    crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>