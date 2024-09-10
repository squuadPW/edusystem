<!-- Modal container -->
<div id="modal-contraseña" class="modal" style="overflow: auto; padding: 0 !important">
    <div class="modal-content modal-enrollment">
        <!-- <div class="modal-body" style="display: none">
            <div style="padding: 0 !important; text-align: center">
                <span style="font-size: 12px;">3105 NW 107 Avenue, Doral, FL 33172 | (786) 361 – 9307 |
                    www.American-elite.us</span>
                <h4 style="padding: 4px; text-align: center; font-weight: bold; font-size: 20px;">ENROLLMENT AGREEMENT
                </h4>
            </div>
            <div style="padding: 0; margin: 10px 0px !important; border: 1px solid gray;" id="student-information">
                <h4
                    style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
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
                <h4
                    style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
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
                <h4
                    style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
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
                <h4
                    style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
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
        </div> -->

        <div class="modal-body" id="content-pdf">
            <div id="part1" style="font-size: 12px; color: #000">
                <div style="padding: 0 !important; text-align: center; border: 1px solid gray;">
                    <img style="margin: auto;width: 50%;margin-bottom: 20px;margin-top: 20px;"
                        src="data:image/png;base64,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" />
                    <span style="font-size: 12px;">3105 NW 107 Avenue, Doral, FL 33172 | (786) 361 – 9307 |
                        www.American-elite.us</span>
                    <h4 style="padding: 4px; text-align: center; font-weight: bold; font-size: 20px;">ENROLLMENT
                        AGREEMENT</h4>
                </div>
                <div>
                    <input type="hidden" name="student_user_id" value="<?php echo $student_id ?>">
                    <input type="hidden" name="parent_user_id" value="<?php echo $partner_id ?>">
                    <h4
                        style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
                        STUDENT PERSONAL INFORMATION</h4>
                    <div style="display: flex">
                        <div style="flex: 50%; border: 1px solid gray; padding: 8px;"><strong>Complete name:</strong>
                            <br> <?php echo $user['student_full_name'] ?>
                        </div>
                        <div style="flex: 50%; border: 1px solid gray; padding: 8px;"><strong>Date:</strong>
                            <br> <?php echo $user['student_created_at'] ?>
                        </div>
                    </div>
                    <div style="display: flex">
                        <div style="flex: 50%; border: 1px solid gray; padding: 8px;"><strong>Date of Birth:</strong>
                            <br> <?php echo $user['student_birth_date'] ?>
                        </div>
                        <div style="flex: 50%; border: 1px solid gray; padding: 8px;"><strong>Gender:</strong>
                            <br> <?php echo $user['student_gender'] ?>
                        </div>
                    </div>
                    <div style="display: flex">
                        <div style="flex: auto; border: 1px solid gray; padding: 8px;"><strong>Address:</strong>
                            <br> <?php echo $user['student_address'] ?>
                        </div>
                    </div>
                    <div style="display: flex">
                        <div style="flex: auto; border: 1px solid gray; padding: 8px;"><strong>Country:</strong>
                            <br> <?php echo $user['student_country'] ?>
                        </div>
                    </div>
                    <div style="display: flex">
                        <div style="flex: auto; border: 1px solid gray; padding: 8px;"><strong>Phone:</strong>
                            <br> <?php echo $user['student_phone'] ?>
                        </div>
                        <div style="flex: auto; border: 1px solid gray; padding: 8px;"><strong>Cell:</strong>
                            <br> <?php echo $user['student_phone'] ?>
                        </div>
                        <div style="flex: auto; border: 1px solid gray; padding: 8px;"><strong>Parent cell:</strong>
                            <br> <?php echo $user['parent_cell'] ?>
                        </div>
                    </div>
                    <div style="display: flex">
                        <div style="flex: auto; border: 1px solid gray; padding: 8px;"><strong>Identification # or
                                Passport # of parent:</strong>
                            <br> <?php echo $user['parent_identification'] ?>

                            <br><br><strong>Identification or Passport # if child is above 16:</strong>
                            <br> <?php echo $user['student_identification'] ?>
                        </div>
                    </div>
                    <div style="display: flex">
                        <div style="flex: auto; border: 1px solid gray; padding: 8px;"><strong>Parent/Legal Guardian
                                Full Name:</strong>
                            <br> <?php echo $user['parent_full_name'] ?>
                            <br><br><strong>Email (Required to access parent portal):</strong>
                            <br> <?php echo $user['parent_email'] ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id="part2" style="font-size: 12px; color: #000">
                <div>
                    <h4
                        style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
                        SCHOOL INFORMATION</h4>
                    <div style="display: flex">
                        <div style="flex: auto; border: 1px solid gray; padding: 8px;">
                            <strong>Name of Last or Concurrent School:</strong>
                            <?php echo isset($institute) ? $institute->name : $institute_name ?> <br><br>
                            <strong>School Address:</strong> <?php echo isset($institute) ? $institute->address : '' ?>
                            <br><br>
                            <strong>Phone:</strong> <?php echo isset($institute) ? $institute->phone : '' ?> <br><br>
                            <strong>Last date Attended:</strong> <br><br>
                            <strong>Last completed grade:</strong> <br>
                            <span onclick="updateGrade('grade5')" style="cursor: pointer"><span id="grade5">( )</span>
                                5th Grade</span>
                            <span onclick="updateGrade('grade6')" style="cursor: pointer"><span id="grade6">( )</span>
                                6th Grade</span>
                            <span onclick="updateGrade('grade7')" style="cursor: pointer"><span id="grade7">( )</span>
                                7th Grade</span>
                            <span onclick="updateGrade('grade8')" style="cursor: pointer"><span id="grade8">( )</span>
                                8th Grade</span>
                            <span onclick="updateGrade('grade9')" style="cursor: pointer"><span id="grade9">( )</span>
                                9th Grade</span>
                            <span onclick="updateGrade('grade10')" style="cursor: pointer"><span id="grade10">( )</span>
                                10th Grade</span>
                            <span onclick="updateGrade('grade11')" style="cursor: pointer"><span id="grade11">( )</span>
                                11th Grade</span>
                            <span onclick="updateGrade('grade12')" style="cursor: pointer"><span id="grade12">( )</span>
                                12th Grade</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h4
                        style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
                        METHOD OF TUITION PAYMENT</h4>
                    <div style="display: flex">
                        <div style="flex: auto; border: 1px solid gray; padding: 8px;">
                            <strong>Name of Last or Concurrent School:</strong> <br>
                            [<?php echo $user['student_payment'] == '2' ? '✓' : '  ' ?>] Full Academic year payment at
                            time of signing enrollment agreement. <br>
                            [<?php echo $user['student_payment'] == '1' ? '✓' : '  ' ?>] Balance paid prior to
                            graduation through payment plan, according to an agreed upon
                            schedule. Final transcript and diploma is
                            contingent upon full balance payment.
                        </div>
                    </div>
                </div>
                <div>
                    <h4
                        style="background-color: #091c5c; color: white; padding: 4px; text-align: center; font-weight: 600;">
                        METHOD OF TUITION PAYMENT</h4>
                    <div style="display: flex">
                        <div style="flex: auto; border: 1px solid gray; padding: 8px;">
                            <p>By signing the agreement herein, the student and their parent/guardian enter into
                                agreement with
                                American Elite School (AES), under
                                which the student/parent/guardian will pay tuition and fees and adhere to the school’s
                                policies
                                including refund policies as set forth in
                                AES website and catalog. In signing this application, the student understands that
                                successful
                                completion of all courses and exams and
                                full payment are required for graduation. The student also certifies that the
                                information
                                included
                                on this form is correct.
                            </p> <br>

                            <p style="color: red; font-style: italic">PLEASE NOTE: You must enroll in a course within
                                the first
                                three months of
                                acceptance to the program. Failure to do so will result in
                                being dropped from the program and you will have to reapply for admission.</p><br>

                            <div style="display: flex">
                                <div style="flex: 50%">
                                    <div>
                                        <div style="padding: 8px;"><strong>Applicant Full Name:</strong>
                                            <br> <?php echo $user['student_full_name'] ?>
                                        </div>
                                    </div>
                                    <div style="padding: 8px;">
                                        <canvas id="signature-student" width="100%" height="200"
                                            style="border-bottom: 1px solid gray"></canvas>
                                        <button id="clear-student">Clear</button>
                                    </div>
                                </div>
                                <div style="flex: 50%">
                                    <div>
                                        <div style="padding: 8px;"><strong>Parent/Legal Guardian Full Name:</strong>
                                            <br> <?php echo $user['parent_full_name'] ?>
                                        </div>
                                    </div>
                                    <div style="padding: 8px;">
                                        <canvas id="signature-parent" width="100%" height="200"
                                            style="border-bottom: 1px solid gray"></canvas>
                                        <button id="clear-parent" style="margin-bottom: 10px;">Clear</button>
                                    </div>
                                </div>
                            </div>
                            <div style="display: flex">
                                <div style="flex: auto; padding: 8px;"><strong>Date:</strong>
                                    <br> <?php echo $user['today'] ?>
                                </div>
                            </div>

                        </div>
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