<div id="document_view" class="wrap">
    
    <h2 style="margin-bottom:15px;"><?= __('Document','edusystem'); ?></h2>

    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=admission-documents'); ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <form method="post" id="form_document" action="<?= admin_url('admin.php?page=admission-documents&action=update_document'); ?>">
                            
                            <input type="hidden" name="document_id" value="<?= $document->id; ?>">

                            <div>
                                <label for="input_id">
                                    <b><?= __('Name','edusystem'); ?></b>

                                    <input type="text" name="name" value="<?= $document->name; ?>" style="width:100%">

                                </label>
                                
                            </div>
                            
                            <div>
                                <label for="checkbox_id">

                                    <span><?= __('Required for access to the virtual classroom','edusystem'); ?></span>

                                    <input name="is_required" type="checkbox" <?= ($document->is_required == 1) ? 'checked' : ''; ?> >

                                </label>
                            </div>

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