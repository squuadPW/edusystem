<div class="content-dashboard">
    <div>
        <?php if (!empty($students)): ?>
            <?php foreach ($students as $student): ?>
                <?php 
                $documents = get_documents($student->id);
                $pending_documents = array_filter($documents, 'should_display_document');
                
                if (!empty($pending_documents)): 
                ?>
                    <div class="document-container">
                        <div class="document-header">
                            <h4>
                                <?= __('Pending Documents for', 'edusystem') . ' ' .  $student->name . ' ' . $student->last_name; ?>
                            </h4>
                        </div>
                        
                        <?php foreach ($documents as $document): ?>
                            <?php if ($document->is_visible && should_display_document($document)): ?>
                                <div class="document-row">
                                    <span class="document-name">
                                        <?php 
                                        $status = get_status_document($document->status);
                                        $status_class = get_document_status_class($status);
                                        $is_required = $document->is_required ? 'font-weight: bold;' : '';
                                        ?>
                                        <a class="document-link <?= $status_class ?>" 
                                           style="<?= $is_required ?>"
                                           href="<?= get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents'; ?>">
                                            <?php $name = get_name_document($document->document_id); ?>
                                            • <?= $name; ?>
                                            <?php if ($document->max_date_upload): ?>
                                                <span class="deadline">- <?= __('DEADLINE', 'edusystem') ?>: <?= date('m/d/Y', strtotime($document->max_date_upload)) ?></span>
                                            <?php endif; ?>
                                        </a>
                                    </span>
                                    
                                    <span class="document-status <?= $status_class ?>" style="<?= $is_required ?>">
                                        <?= $status === __('No sent', 'edusystem') ? __('Pending', 'edusystem') : __($status, 'edusystem') ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <div class="upload-link">
                            <a href="<?= get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents'; ?>">
                                <? __('Upload documents here', 'edusystem'); ?>
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>