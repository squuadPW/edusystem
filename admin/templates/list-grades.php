<div class="tabs-content">
     <div class="wrap">
        <div style="text-align:start;">
                <h1 class="wp-heading-line"><?= __('Required Documents','aes'); ?></h1>
        </div>
        <div style="text-align:end;">
            <button class="button button-primary" disabled><?= __('Add Document','aes'); ?></button>
        </div>
        <div>
            <table id="table-products" class="wp-list-table widefat fixed posts" style="margin-top:20px;">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-primary column-title"><?= __('Document','aes') ?></th>
                        <th scope="col" class="manage-column column-title-translate"><?= __('Required for access to the virtual classroom','aes') ?></th>
                        <th scope="col" class="manage-column column-title-translate"><?= __('Actions','aes') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(isset($grades) && !empty($grades)): ?>
                        <?php foreach($grades as $grade): ?>
                            <tr>
                                <td style="text-align:center;background-color: #f6f7f7;" colspan="3"><b><?=  $grade->name; ?></b></td>
                            </tr>
                            <?php foreach($documents as $document): ?>
                                <?php if($document->grade_id == $grade->id): ?>
                                    <tr>
                                        <td class="column-primary">
                                            <?=  get_name_document($document->name); ?>
                                            <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                                        </td>
                                        <td data-colname="<?= __('Required for access to the virtual classroom','aes'); ?>">
                                            <?php if($document->is_required): ?>
                                                <span class="color-success"><?= __('Yes','aes'); ?></span>
                                            <?php else: ?>
                                                <span class="color-danger"><?= __('No','aes'); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td data-colname="<?= __('Actions','aes'); ?>">
                                            <a href="<?= admin_url('admin.php?page=admission-documents&action=edit&document_id='.$document->id); ?>" class="button button-outline-primary"><?= __('Edit','aes'); ?></a>
                                            <button class="button button-danger" disabled><?= __('Delete','aes'); ?></button>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php  endforeach;?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>   
        </div>
    </div>
<div>