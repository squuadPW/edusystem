<div class="tabs-content">
     <div class="wrap">
        <div style="text-align:start;">
                <h1 class="wp-heading-line"><?= __('Required Documents','edusystem'); ?></h1>
        </div>

        <?php
            include(plugin_dir_path(__FILE__) . 'cookie-message.php');
        ?>

        <div style="text-align:end;">
            <a href="<?= admin_url('admin.php?page=admission-documents&action=edit')?>" class="button button-primary" ><?= __('Add Document','edusystem'); ?></a>
        </div>
        <div>
            <table id="table-products" class="wp-list-table widefat fixed posts" style="margin-top:20px;">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column column-primary column-title"><?= __('Document','edusystem') ?></th>
                        <th scope="col" class="manage-column column-title-translate"><?= __('Type file','edusystem') ?></th>
                        <th scope="col" class="manage-column column-title-translate"><?= __('Programs','edusystem') ?></th>
                        <th scope="col" class="manage-column column-title-translate"><?= __('Actions','edusystem') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($documents as $document): ?>
                        <tr>
                            <td class="column-primary">
                                <?=  get_name_document($document->name); ?>
                                <button type='button' class='toggle-row'><span class='screen-reader-text'></span></button>
                            </td>

                            <td data-colname="<?= __('Type file','edusystem'); ?>">
                                <span><?= $document->type_file; ?></span>
                            </td>

                            <td data-colname="<?= __('Programs','edusystem'); ?>">

                                <?php $academic_department = json_decode($document->academic_department, true) ?>
                                <?php if( !empty( $academic_department )  ): ?>

                                    <?php
                                        global $wpdb;
                                        $programs = $wpdb->get_results(
                                            "SELECT identificator, name FROM {$wpdb->prefix}student_program"
                                        );

                                        $mentions = $wpdb->get_results($wpdb->prepare(
                                            "SELECT identificator, name 
                                            FROM {$wpdb->prefix}mentions_by_career
                                            WHERE career_identificator = %s",
                                            $career->identificator
                                        ));
                                    ?>
                                    
                                    <ul>
                                        <?php foreach ( $programs AS $program ) : ?>
                                            <li>
                                                <?php if( in_array( $program->identificator, $academic_department['program'] ) ) ?>
                                                    <b><?= $program->name ?></b>
                                                

                                                <ul> 
                                                    <?php foreach ($academic_department['program'] as $program): ?>
                                                        <li><?= esc_html($program['identificator']) ?> </li>
                                                    <?php endforeach ?>
                                                </ul>
                                            </li>
                                        <?php endif ?>
                                        
                                        <?php if (!empty($academic_department['career'])): ?> 
                                            <li>
                                                <strong>Carreras</strong>
                                                
                                                <ul>
                                                    <?php foreach ($academic_department['career'] as $career): ?>
                                                        <li><?= esc_html($career['identificator'])?></li>
                                                    <?php endforeach ?>
                                                </ul>
                                            </li>
                                        <?php endif ?> 

                                        <?php if (!empty($academic_department['mention'])): ?>
                                            <li>
                                                <strong>Menciones</strong>
                                                
                                                <ul>
                                                    <?php foreach ($academic_department['mention'] as $mention): ?>
                                                        <li> <?= esc_html($mention['identificator'])?> </li> 
                                                    <?php endforeach ?>
                                                </ul>
                                            </li>
                                        <?php endif ?>
                                    </ul>
                                <?php else: ?>
                                    <span><?= __('All programs','edusystem') ?></span>
                                <?php endif; ?>

                            </td>

                            <td data-colname="<?= __('Actions','edusystem'); ?>">
                                <a href="<?= admin_url('admin.php?page=admission-documents&action=edit&document_id='.$document->id); ?>" class="button button-outline-primary"><?= __('Edit','edusystem'); ?></a>
                                <button class="button button-danger" onclick="open_delete_modal( <?= $document->id ?>, '<?= $document->name ?>')" ><?= __('Delete','edusystem'); ?></button>
                            </td>
                        </tr>
                    <?php  endforeach;?>
                </tbody>
            </table>   
        </div>
    </div>
<div>