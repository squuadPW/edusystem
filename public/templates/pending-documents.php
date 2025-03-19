<div class="content-dashboard">
    <div style="margin-top:48px;">
        <?php if (!empty($students)): ?>
            <?php foreach ($students as $student): ?>
                <?php $documents = get_documents($student->id); ?>
                <?php if (count(array_filter($documents, function ($document) {
                    return $document->status == 0 || $document->status == 1 || $document->status == 3 || $document->status == 4; })) > 0) { ?>
                    <div
                        style="background-color: #f7f7f7; padding: 20px; border: 1px solid #ddd; border-radius: 10px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); margin-bottom: 10px">
                        <div style="border-bottom: 1px solid #a9a9a9; margin: 0px 0px 20px 0px;">
                            <h4 style="font-size:20px;text-align:center;padding-bottom: 10px">
                                <?= __('Pending Documents for ' . $student->name . ' ' . $student->last_name, 'edusystem'); ?>
                            </h4>
                        </div>
                        <?php foreach ($documents as $document): ?>
                            <?php if ($document->is_visible) { ?>
                                <?php if ($document->status == 0 || $document->status == 1 || $document->status == 3 || $document->status == 4): ?>
                                    <div style="display:flex;width:100%;flex-direction:row; margin-bottom: 10px">
                                        <span style="width:70%">
                                            <?php
                                            $status = get_status_document($document->status);
                                            if ($status === 'No sent') { ?>
                                                <a style="text-decoration: underline !important;"
                                                    href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents'; ?>">
                                                    <?php $name = get_name_document($document->document_id); ?>
                                                    <?= $name; ?>
                                                </a>
                                            </span>
                                        <?php } elseif ($status === 'Sent') { ?>
                                            <a style="text-decoration: underline !important; color: blue;"
                                                href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents'; ?>">
                                                <?php $name = get_name_document($document->document_id); ?>
                                                <?= $name; ?>
                                            </a>
                                            </span>
                                        <?php } elseif ($status === 'Processing') { ?>
                                            <a style="text-decoration: underline !important; color: yellow;"
                                                href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents'; ?>">
                                                <?php $name = get_name_document($document->document_id); ?>
                                                <?= $name; ?>
                                            </a>
                                            </span>
                                        <?php } elseif ($status === 'Declined' || $status === 'Expired') { ?>
                                            <a style="text-decoration: underline !important; color: red;"
                                                href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents'; ?>">
                                                <?php $name = get_name_document($document->document_id); ?>
                                                <?= $name; ?>
                                            </a>
                                            </span>
                                        <?php } elseif ($status === 'Approved') { ?>
                                            <a style="text-decoration: underline !important; color: green;"
                                                href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents'; ?>">
                                                <?php $name = get_name_document($document->document_id); ?>
                                                <?= $name; ?>
                                            </a>
                                            </span>
                                        <?php } else { ?>
                                            <a style="text-decoration: underline !important;"
                                                href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents'; ?>">
                                                <?php $name = get_name_document($document->document_id); ?>
                                                <?= $name; ?>
                                            </a>
                                            </span>
                                        <?php } ?>
                                        <span style="width:30%;text-align:end;">
                                            <?php
                                            $status = get_status_document($document->status);
                                            if ($status === 'No sent') { ?>
                                                <span>Pending</span>
                                            <?php } elseif ($status === 'Sent') { ?>
                                                <span style="color: blue"><?= $status ?></span>
                                            <?php } elseif ($status === 'Processing') { ?>
                                                <span style="color: yellow"><?= $status ?></span>
                                            <?php } elseif ($status === 'Declined' || $status === 'Expired') { ?>
                                                <span style="color: red"><?= $status ?></span>
                                            <?php } elseif ($status === 'Approved') { ?>
                                                <span style="color: green"><?= $status ?></span>
                                            <?php } else { ?>
                                                <span><?= $status ?></span>
                                            <?php } ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            <?php } ?>
                        <?php endforeach; ?>
                        <div style="text-align: center;">
                            <a style="text-decoration: underline !important; color: #002fbd; font-size: 14px;"
                                href="<?php echo get_permalink(get_option('woocommerce_myaccount_page_id')) . '/student-documents'; ?>">
                                Upload documents here
                            </a>
                        </div>
                    </div>
                <?php } ?>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>
</div>