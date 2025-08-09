<!-- Your modal content here -->

<?php global $current_user ?>
<div class="modal-content modal-enrollment" id="modal-content">
    <div id="modal-contraseña" class="modal" >
        <div class="modal-content modal-enrollment" style="overflow: auto; padding: 0 !important">
            <!-- <span id="close-modal-enrollment" style="float: right; cursor: pointer"><span
                    class='dashicons dashicons-no-alt'></span></span> -->
            <div class="modal-header">
                <div class="title" style="color: #4faf4f">
                    Payment successfully
                </div>
            </div>
            <div class="modal-body" id="content-pdf">
                <form method="POST" action="<?php the_permalink(); ?>?action=save_student_custom" class="form-aes">
                    <input class="formdata" autocomplete="off" type="hidden" id="modal_open" name="modal_open"
                        value="1">

                    <!-- DATOS DEL ESTUDIANTE -->
                    <div class="grid grid-cols-12 gap-4">
                        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6">
                            <div>
                                <p>Welcome to <strong>American Elite School!</strong></p>
                                <p>We're thrilled to have you on board! To ensure the security of your account, we
                                    require you to set a password before accessing your account.</p>
                            </div>
                            <div>
                                <label for="password"><?= __('Password of access', 'edusystem'); ?><span
                                        class="required">*</span></label>
                                <input class="formdata" type="password" name="password" autocomplete="off" required>
                            </div>
                        </div>

                        <!-- DATOS DEL GRADO -->
                        <div class="col-start-1 sm:col-start-4 col-span-12 sm:col-span-6 mt-3"
                            style="text-align:center;">
                            <button class="submit" id="buttonsave"><?= __('Save', 'edusystem'); ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>