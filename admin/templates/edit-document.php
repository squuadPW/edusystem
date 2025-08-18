<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Edit Document','edusystem'); ?></h2>

    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=admission-documents'); ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <form method="post" action="<?= admin_url('admin.php?page=admission-documents&action=update_document'); ?>">
                            <table class="form-table" style="margin-top:0px;">
                                <tbody>
                                    <tr>
                                        <th scope="row" style="font-weight:400;">
                                            <label for="input_id"><b><?= __('Name','edusystem'); ?></b></label><br>
                                        </th>
                                        <td>
                                            <input type="text" name="name" value="<?= $document->name; ?>" style="width:100%">
                                            <input type="hidden" name="document_id" value="<?= $document->id; ?>">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>
                                            <span><?= __('Required for access to the virtual classroom','edusystem'); ?></span>
                                        </th>
                                        <td>
                                            <fieldset>
                                                <label for="checkbox_id">
                                                    <input name="is_required" type="checkbox" <?= ($document->is_required == 1) ? 'checked' : ''; ?> >
                                                </label>
                                            </fieldset>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <div style="display:flex;width:100%;justify-content:end;">
                                <button class="button button-primary" type="submit"><?= __('Save Changes','edusystem'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>