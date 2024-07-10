<div class="content-dashboard">
    <h4 style="font-size:20px;text-align:start;"><?= __('Pending Documents','aes'); ?></h4>

    <div style="margin-top:10px;">
        <?php if(!empty($students)): ?>
           <?php foreach($students as $student): ?>
                <?php $documents = get_documents($student->id); ?>
                <div style="border: 1px solid #091c5c; background-color: #f2f2f3; padding: 18px; border-radius: 15px;">
                <?php foreach($documents as $document): ?>
                    <?php if($document->status == 0 || $document->status == 1 || $document->status == 3 || $document->status == 4): ?>
                        <div style="display:flex;width:100%;flex-direction:row; margin-bottom: 10px">
                            <span style="width:70%">
                                <?php 
                                    $status = get_status_document($document->status);
                                    if($status === 'No sent') { ?>
                                        <span>
                                            <?php $name = get_name_document($document->document_id); ?>
                                            <?= $student->name.' '.$student->last_name.' - '.$name; ?>
                                        </span>
                                    <?php } elseif($status === 'Sent') { ?>
                                        <span style="color: blue">
                                            <?php $name = get_name_document($document->document_id); ?>
                                            <?= $student->name.' '.$student->last_name.' - '.$name; ?>
                                        </span>
                                    <?php } elseif($status === 'Processing') { ?>
                                        <span style="color: yellow">
                                            <?php $name = get_name_document($document->document_id); ?>
                                            <?= $student->name.' '.$student->last_name.' - '.$name; ?>
                                        </span>
                                    <?php } elseif($status === 'Declined' || $status === 'Expired') { ?>
                                        <span style="color: red">
                                            <?php $name = get_name_document($document->document_id); ?>
                                            <?= $student->name.' '.$student->last_name.' - '.$name; ?>
                                        </span>
                                    <?php } elseif($status === 'Approved') { ?>
                                        <span style="color: green">
                                            <?php $name = get_name_document($document->document_id); ?>
                                            <?= $student->name.' '.$student->last_name.' - '.$name; ?>
                                        </span>
                                    <?php } else { ?>
                                        <span>
                                            <?php $name = get_name_document($document->document_id); ?>
                                            <?= $student->name.' '.$student->last_name.' - '.$name; ?>
                                        </span>
                                    <?php } ?>
                            </span>
                            <span style="width:30%;text-align:end;">
                                <?php 
                                $status = get_status_document($document->status);
                                if($status === 'No sent') { ?>
                                    <span><?= $status ?></span>
                                <?php } elseif($status === 'Sent') { ?>
                                    <span style="color: blue"><?= $status ?></span>
                                <?php } elseif($status === 'Processing') { ?>
                                    <span style="color: yellow"><?= $status ?></span>
                                <?php } elseif($status === 'Declined' || $status === 'Expired') { ?>
                                    <span style="color: red"><?= $status ?></span>
                                <?php } elseif($status === 'Approved') { ?>
                                    <span style="color: green"><?= $status ?></span>
                                <?php } else { ?>
                                    <span><?= $status ?></span>
                                <?php } ?>
                            </span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
       
    </div>
</div>