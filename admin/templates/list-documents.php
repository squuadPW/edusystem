<div class="tabs-content">
     <div class="wrap">
        <div style="text-align:start;">
                <h1 class="wp-heading-line"><?= __('Required Documents','edusystem'); ?></h1>
        </div>

        <?php
            include(plugin_dir_path(__FILE__) . 'cookie-message.php');
        ?>

        <div>

            <div>
                
                <form method="GET" >
                    
                    <input type="hidden" name="page" value="<?= $_REQUEST['page'] ?>" />

                    <?php 
                        global $wpdb;
                        $programs = $wpdb->get_results(
                            "SELECT identificator, name FROM {$wpdb->prefix}student_program"
                        );
                    ?>
                    <select name="program_id" >
                        <option value="" <?= selected($program->identificator, '') ?> ><?= __('Select program','edusystem') ?></option>
                        <?php foreach( $programs AS $program ): ?>
                            <option value="<?=$program->identificator?>" <?= selected($program->identificator,$_GET['program_id']) ?> ><?= $program->name ?></option>
                        <?php endforeach ?>
                    </select>
                    
                    <input type="submit" class="button" value="<?=__('Change','edusystem')?>" />
                </form>
            </div>

            <div style="text-align:end;">
                <a href="<?= admin_url('admin.php?page=admission-documents&action=edit')?>" class="button button-primary" ><?= __('Add Document','edusystem'); ?></a>
            </div>
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

                                    <?
                                        $list_programs = [];

                                        global $wpdb;
                                        $programs = $wpdb->get_results(
                                            "SELECT identificator, name FROM {$wpdb->prefix}student_program"
                                        );
                                        
                                        foreach ( $programs AS $program ) {

                                            $program_exists  = !empty( $academic_department['program'][$program->identificator]  );

                                            $list_programs[$program->identificator] = [
                                                'name' => $program->name,
                                                'exists' => $program_exists,
                                                'careers' => []
                                            ];

                                            // Carreras del programa
                                            $careers = $wpdb->get_results($wpdb->prepare(
                                                "SELECT identificator, name 
                                                FROM {$wpdb->prefix}careers_by_program
                                                WHERE program_identificator = %s",
                                                $program->identificator
                                            ));  

                                            foreach( $careers AS $career ) {

                                                $career_exists = !empty( $academic_department['career'][$career->identificator]  );

                                                $list_programs[$program->identificator]['careers'][$career->identificator] = [
                                                    'name' => $career->name,
                                                    'exists' => $career_exists,
                                                    'mentions' => []
                                                ];

                                                $mentions = $wpdb->get_results($wpdb->prepare(
                                                    "SELECT identificator, name 
                                                    FROM {$wpdb->prefix}mentions_by_career
                                                    WHERE career_identificator = %s",
                                                    $career->identificator
                                                )); 

                                                foreach( $mentions AS $mention  ) {

                                                    if( !empty( $academic_department['mention'][$mention->identificator]  ) ){
                                                        
                                                        $list_programs[$program->identificator]['careers'][$career->identificator]['mentions'][$mention->identificator]  = [
                                                            'name' => $mention->name,
                                                        ];
                                                    }
                                                }
                                                        
                                                if ( !$career_exists && empty($list_programs[$program->identificator]['careers'][$career->identificator]['mentions']) ) { 
                                                    unset($list_programs[$program->identificator]['careers'][$career->identificator]); 
                                                }

                                            }

                                            if ( !$program_exists && empty($list_programs[$program->identificator]['careers'])) { 
                                                unset($list_programs[$program->identificator]); 
                                            }
                                        }

                                    ?>
                                        
                                    <ul class="program-list-document">
                                        <?php foreach( $list_programs AS $program ): ?>
                                            <li class="program-item-document">
                                                <?php if( $program['exists'] ): ?>
                                                    <b class="program-name-document"><?= $program['name'] ?></b>
                                                <?php else: ?>
                                                    <span class="program-name-document"><?= $program['name'] ?></span>
                                                <?php endif ?>

                                                <ul class="career-list-document">
                                                    <?php foreach( $program['careers'] AS $careers ): ?>
                                                        <li class="career-item-document">
                                                            <?php if( $careers['exists'] ): ?>
                                                                <b class="career-name-document"><?= $careers['name'] ?></b>
                                                            <?php else: ?>
                                                                <span class="career-name-document"><?= $careers['name'] ?></span>
                                                            <?php endif ?>

                                                            <ul class="mention-list-document">
                                                                <?php foreach( $careers['mentions'] AS $mention ): ?>
                                                                    <li class="mention-item-document">
                                                                        <b class="mention-name-document"><?= $mention['name'] ?></b>
                                                                    </li>
                                                                <?php endforeach ?>
                                                            </ul>
                                                        </li>
                                                    <?php endforeach ?>
                                                </ul>
                                            </li>
                                        <?php endforeach ?>
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