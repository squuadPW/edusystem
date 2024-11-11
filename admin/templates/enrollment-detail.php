<div class="wrap">
    <?php if (isset($enrollment) && !empty($enrollment)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Enrollment Details', 'aes'); ?></h2>
    <?php else: ?>
        <h2 style="margin-bottom:15px;"><?= __('Create Enrollment', 'aes'); ?></h2>
    <?php endif; ?>

    <?php if (isset($_COOKIE['message']) && !empty($_COOKIE['message'])) { ?>
        <div class="notice notice-success is-dismissible">
            <p><?= $_COOKIE['message']; ?></p>
        </div>
        <?php setcookie('message', '', time(), '/'); ?>
    <?php } ?>
    <?php if (isset($_COOKIE['message-error']) && !empty($_COOKIE['message-error'])) { ?>
        <div class="notice notice-error is-dismissible">
            <p><?= $_COOKIE['message-error']; ?></p>
        </div>
        <?php setcookie('message-error', '', time(), '/'); ?>
    <?php } ?>
    <div style="display:flex;width:100%;">
        <a class="button button-outline-primary"
            href="<?= admin_url('admin.php?page=add_admin_form_enrollments_content'); ?>"><?= __('Back', 'aes'); ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_enrollments_content&action=save_enrollment_details'); ?>">
                            <div>

                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Student Information', 'aes'); ?></b>
                                </h3>
                                <div style="margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($enrollment) && !empty($enrollment)): ?>
                                            <div>
                                                <label
                                                    for="id_document"><b><?= __('ID Document Student', 'aes'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="id_document"
                                                    value="<?php echo $student->id_document ?>" required readonly>
                                            </div>
                                            <input type="hidden" name="student_id"
                                                value="<?php echo $student->id ?>" required>
                                        <?php else: ?>
                                            <div>
                                                <label
                                                    for="id_document"><b><?= __('ID Document Student', 'aes'); ?></b><span
                                                        class="text-danger">*</span></label><br>
                                                <input type="text" name="id_document" required>

                                                <div>
                                                    <button type="button"
                                                        class="button button-primary" style="margin: 10px" id="search-student"><?= __('Search student by id document', 'aes'); ?></button>
                                                </div>
                                            </div>
                                            <input type="hidden" name="student_id" required>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <table class="wp-list-table widefat fixed striped posts"
                                            style="margin-top:20px;">
                                            <thead>
                                                <tr>
                                                    <th scope="col" class=" manage-column column">
                                                        <?= __('Type document', 'restaurant-system-app'); ?>
                                                    </th>
                                                    <th scope="col" class=" manage-column column-primary">
                                                        <?= __('ID document', 'restaurant-system-app'); ?>
                                                    </th>
                                                    <th scope="col" class=" manage-column">
                                                        <?= __('Student', 'restaurant-system-app'); ?>
                                                    </th>
                                                    <th scope="col" class=" manage-column">
                                                        <?= __('Student email', 'restaurant-system-app'); ?>
                                                    </th>
                                                    <th scope="col" class=" manage-column">
                                                        <?= __('Student phone', 'restaurant-system-app'); ?>
                                                    </th>
                                                    <th scope="col" class=" manage-column">
                                                        <?= __('Student country', 'restaurant-system-app'); ?>
                                                    </th>
                                                    <th scope="col" class=" manage-column column">
                                                        <?= __('Institute', 'restaurant-system-app'); ?>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td id="type_document"><?php echo $student ? get_type_document_student($student->type_document) : ''; ?>
                                                    </td>
                                                    <td id="id_document"><?php echo $student->id_document ?></td>
                                                    <td id="full_name">
                                                        <?php echo $student->name . ' ' . $student->middle_name . ' ' . $student->last_name . ' ' . $student->middle_last_name ?>
                                                    </td>
                                                    <td id="email"><?php echo $student->email ?></td>
                                                    <td id="phone"><?php echo $student->phone ?></td>
                                                    <td id="country"><?php echo $student->country ?></td>
                                                    <td id="institute"><?php echo $student->name_institute ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Enrollment Information', 'aes'); ?></b>
                                </h3>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($enrollment) && !empty($enrollment)): ?>
                                            <label for="status_id"><b><?= __('Status', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <select name="status_id" required>
                                                <option value="">Select status</option>
                                                <option value="0" <?= ($enrollment->status_id == 0) ? 'selected' : ''; ?>>To
                                                    begin</option>
                                                <option value="1" <?= ($enrollment->status_id == 1) ? 'selected' : ''; ?>>
                                                    Active</option>
                                                <option value="2" <?= ($enrollment->status_id == 2) ? 'selected' : ''; ?>>
                                                    Unsubscribed</option>
                                                <option value="3" <?= ($enrollment->status_id == 3) ? 'selected' : ''; ?>>
                                                    Completed</option>
                                            </select>
                                            <input type="hidden" name="enrollment_id" id="enrollment_id"
                                                value="<?= $enrollment->id; ?>">
                                        <?php else: ?>
                                            <label for="status_id"><b><?= __('Status', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <select name="status_id" required>
                                                <option value="">Select status</option>
                                                <option value="0">To begin</option>
                                                <option value="1">Active</option>
                                                <option value="2">Unsubscribed</option>
                                                <option value="3">Completed</option>
                                            </select>
                                            <input type="hidden" name="enrollment_id" id="enrollment_id" value="">
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($enrollment) && !empty($enrollment)): ?>
                                            <label for="code_subject"><b><?= __('Subject', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <select name="code_subject">
                                                <option value="">Select a subject</option>
                                                <?php foreach ($subjects as $key => $subject) { ?>
                                                    <option value="<?php echo $subject->code_subject ?>"
                                                        <?= ($enrollment->code_subject == $subject->code_subject) ? 'selected' : ''; ?>>
                                                        <?php echo $subject->name . ' (' . $subject->code_subject . ')'; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        <?php else: ?>
                                            <label for="code_subject"><b><?= __('Subject', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <select name="code_subject">
                                                <option value="">Select a subject</option>
                                                <?php foreach ($subjects as $key => $subject) { ?>
                                                    <option value="<?php echo $subject->code_subject ?>">
                                                        <?php echo $subject->name . ' (' . $subject->code_subject . ')'; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Period enrolled', 'aes'); ?></b>
                                </h3>
                                <div style="display: flex; justify-content: space-evenly; margin: 18px;">
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($enrollment) && !empty($enrollment)): ?>
                                            <label for="code_period"><b><?= __('Period', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <select name="code_period" required>
                                                <option value="">Select a period</option>
                                                <?php foreach ($periods as $key => $period) { ?>
                                                    <option value="<?php echo $period->code ?>"
                                                        <?= ($enrollment->code_period == $period->code) ? 'selected' : ''; ?>>
                                                        <?php echo $period->name . ' (' . $period->code . ')'; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        <?php else: ?>
                                            <label for="code_period"><b><?= __('Period', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <select name="code_period" required>
                                                <option value="">Select a period</option>
                                                <?php foreach ($periods as $key => $period) { ?>
                                                    <option value="<?php echo $period->code ?>">
                                                        <?php echo $period->name . ' (' . $period->code . ')'; ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                    <div style="font-weight:400; text-align: center">
                                        <?php if (isset($enrollment) && !empty($enrollment)): ?>
                                            <label for="cut_period"><b><?= __('Cut-off', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <select name="cut_period" required>
                                                <option value="">Select cut-off period</option>
                                                <option value="A" <?= ($enrollment->cut_period == 'A') ? 'selected' : ''; ?>>A
                                                </option>
                                                <option value="B" <?= ($enrollment->cut_period == 'B') ? 'selected' : ''; ?>>B
                                                </option>
                                                <option value="C" <?= ($enrollment->cut_period == 'C') ? 'selected' : ''; ?>>C
                                                </option>
                                                <option value="D" <?= ($enrollment->cut_period == 'D') ? 'selected' : ''; ?>>D
                                                </option>
                                                <option value="E" <?= ($enrollment->cut_period == 'E') ? 'selected' : ''; ?>>E
                                                </option>
                                            </select>

                                        <?php else: ?>
                                            <label for="cut_period"><b><?= __('Cut-off', 'aes'); ?></b><span
                                                    class="text-danger">*</span></label><br>
                                            <select name="cut_period" required>
                                                <option value="">Select cut-off period</option>
                                                <option value="A">A</option>
                                                <option value="B">B</option>
                                                <option value="C">C</option>
                                                <option value="D">D</option>
                                                <option value="E">E</option>
                                            </select>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($enrollment) && !empty($enrollment)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-success"><?= __('Saves changes', 'aes'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-success" id="create-enrollment"><?= __('Create enrollment', 'aes'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>