<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.6/Sortable.min.js"></script>

<div class="wrap">
    <?php if (isset($pensum) && !empty($pensum)): ?>
        <h2 style="margin-bottom:15px;"><?= __('Pensum details', 'edusystem'); ?></h2>
    <?php else: ?>
        <?php if ($institute): ?>
            <h2 style="margin-bottom:15px;"><?= __('Institute\'s pensum', 'edusystem'); ?></h2>
        <?php else: ?>
            <h2 style="margin-bottom:15px;"><?= __('Program pensum', 'edusystem'); ?></h2>
        <?php endif; ?>
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
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder admin-add-offer" style="width: 70%">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">

                        <form method="post"
                            action="<?= admin_url('admin.php?page=add_admin_form_pensum_content&action=save_pensum_details'); ?>">
                            <div>
                                <h3
                                    style="margin-top:20px;margin-bottom:0px;text-align:center; border-bottom: 1px solid #8080805c;">
                                    <b><?= __('Pensum Information', 'edusystem'); ?></b>
                                </h3>

                                <div style="margin: 18px;">
                                    <input type="hidden" name="pensum_id" value="<?= $pensum->id ?>">
                                    <input type="hidden" name="program_institute" value="<?= $institute ?>">
                                    <div style="font-weight:400; text-align: center; margin-bottom: 10px;">
                                            <div>
                                                <input style="width: auto !important;" type="checkbox" name="status" id="status" <?= ($pensum->status == 1) ? 'checked' : ''; ?>>
                                                <label for="status"><b><?= __('Active', 'edusystem'); ?></b></label>
                                            </div>
                                    </div>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="name"><b><?= __('Name', 'edusystem'); ?></b><span
                                                class="text-danger">*</span></label><br>
                                        <input type="text" name="name" value="<?= $pensum->name; ?>" required>
                                    </div>

                                    <?php if (!$institute) { ?>
                                        <div style="font-weight:400;" class="space-offer">
                                            <label for="hc"><b><?= __('Program', 'edusystem'); ?></b></label><br>
                                            <select name="program_id" required>
                                                <option value="" selected>Assigns a program</option>
                                                <option value="aes" <?= ($pensum->program_id == 'aes') ? 'selected' : ''; ?>>
                                                    <?= __('Dual diploma', 'edusystem'); ?></option>
                                            </select>
                                        </div>
                                    <?php } ?>

                                    <?php if ($institute) { ?>
                                        <div style="font-weight:400;" class="space-offer">
                                            <label for="hc"><b><?= __('Institute', 'edusystem'); ?></b></label><br>
                                            <select class="js-example-basic" name="institute_id">
                                                <option value="" selected>Assigns an institute</option>
                                                <?php foreach ($institutes as $institute) { ?>
                                                    <option value="<?= $institute->id; ?>"
                                                        <?= ($pensum->institute_id == $institute->id) ? 'selected' : ''; ?>>
                                                        <?= $institute->name ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <?php } ?>

                                    <div style="font-weight:400;" class="space-offer">
                                        <label for="hc"><b><?= __('Subject', 'edusystem'); ?></b></label><br>
                                        <select class="js-example-basic" name="subject_id">
                                            <option value="" selected>Select a subject</option>
                                            <?php foreach ($subjects as $subject) { ?>
                                                <option value="<?= $subject->id; ?>" data-name="<?= $subject->name; ?>"
                                                    data-code="<?= $subject->code_subject; ?>"
                                                    data-type="<?= ucwords($subject->type); ?>">
                                                    <?= $subject->name ?> (<?= $subject->code_subject ?>)
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </div>

                                    <div
                                        style="margin:20px;display:flex;flex-direction:row;justify-content:center;gap:5px;">
                                        <button type="button" class="button button-secondary"
                                            id="add-subject-pensum"><?= __('Add', 'edusystem'); ?></button>
                                    </div>

                                    <div>
                                        <table class="wp-list-table widefat fixed posts striped">
                                            <thead>
                                                <tr>
                                                    <th colspan="4">Subject</th>
                                                    <th colspan="4">Code</th>
                                                    <th colspan="2">Type</th>
                                                    <th colspan="2" style="text-align: end">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody id="sortable-list">
                                                <?php foreach (json_decode($pensum->matrix) as $key => $subject) { ?>
                                                    <tr data-subject-id="<?= $subject->id ?>" style="cursor: move">
                                                        <th colspan="4"><?= $subject->name ?></th>
                                                        <th colspan="4"><?= $subject->code_subject ?></th>
                                                        <th colspan="2"><?= $subject->type ?></th>
                                                        <th colspan="2" style="text-align: end">
                                                            <button class="button button-danger"><span
                                                                    class="dashicons dashicons-trash"></span></button>
                                                        </th>
                                                    </tr>
                                                    <!-- Input oculto para cada subject guardado -->
                                                    <input type="hidden" name="subjects[]" value="<?= $subject->id ?>">
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($pensum) && !empty($pensum)): ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Saves changes', 'edusystem'); ?></button>
                                </div>
                            <?php else: ?>
                                <div style="margin-top:20px;display:flex;flex-direction:row;justify-content:end;gap:5px;">
                                    <button type="submit"
                                        class="button button-primary"><?= __('Add pensum', 'edusystem'); ?></button>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>