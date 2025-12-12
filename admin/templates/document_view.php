<div id="document_view" class="wrap">
    
    <h2 style="margin-bottom:15px;"><?= __('Document','edusystem'); ?></h2>

    <?php
        include(plugin_dir_path(__FILE__) . 'cookie-message.php');
        
        $document_id   = $document->id ?? 0;
        $document_name = $document->name ?? ''; ;
        $is_required   = $document->is_required == 1 ? 'checked' : '';
        $type_file     = $document->type_file ?? '';
        $id_requisito  = $document->id_requisito ?? '';
        $academic_department = !empty($document->academic_department) 
                                    ? json_decode($document->academic_department, true) 
                                    : [];

        $form_data = [];
        if ( isset($_COOKIE['form_data']) ) {
            $form_data = json_decode(stripslashes($_COOKIE['form_data']), true);

            $document_id   = $form_data['document_id']  ?? $document_id;
            $document_name = $form_data['name']         ?? $document_name;
            $is_required   = $form_data['is_required']  ?? $is_required;
            $type_file     = $form_data['type_file']    ?? $type_file;
            $id_requisito  = $form_data['id_requisito'] ?? $id_requisito;

            $academic_department = $form_data['academic_scope'] ?? $academic_department;  
        }
    ?>

    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=admission-documents'); ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder" >
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <form method="post" id="form_document" action="<?= admin_url('admin.php?page=admission-documents&action=update_document'); ?>">
                            
                            <input type="hidden" name="document_id" value="<?= $document_id ?>">

                            <h3 class="title" >
                                <b><?= __('Document Information', 'edusystem'); ?></b>
                            </h3>

                            <div>
                                <label for="input_id">
                                    <b><?= __('Name','edusystem'); ?></b>

                                    <input type="text" name="name" value="<?= $document_name ?>">

                                </label>
                                
                            </div>
                            
                            <div>
                                <label for="is_required">
                                    <input name="is_required" type="checkbox" <?= $is_required ?> >
                                    
                                    <b><?= __('Required to access the virtual classroom in all areas','edusystem'); ?></b>
                                </label>
                            </div>

                            <div>
                                <label for="type_file">
                                    <b><?= __('Accepted File Types','edusystem'); ?></b>
                                    <input 
                                        type="text" 
                                        name="type_file" 
                                        value="<?= esc_attr($type_file); ?>" 
                                        placeholder = <?= __('Ej: .pdf, .docx, .jpg, .png','edusystem') ?>
                                        accept=".pdf,.doc,.docx,.xl,.xls,.jpg,.jpge,.png,.web"
                                    >
                                </label>
                                <p class="description">
                                    <?= __('Allowed formats: .pdf, .doc, .docx, .xl, .xls, .jpg, .jpge, .png, .web','edusystem'); ?>
                                </p>
                            </div>

                            <div>
                                <label for="id_requisito">
                                    <b><?= __('Code admin','edusystem'); ?></b>
                                    <input type="text" 
                                        name="id_requisito" 
                                        value="<?= esc_attr($id_requisito); ?>"
                                    >
                                </label>
                            </div>

                            <div>
                                <h3 class="title" >
                                    <b><?= __('Scope of application','edusystem') ?></b>
                                </h3>

                                <br/>

                                <label for="type_file">
                                    <b><?= __('Scope','edusystem'); ?></b>

                                    <?php
                                        global $wpdb;
                                        $programs = $wpdb->get_results(
                                            "SELECT identificator, name FROM {$wpdb->prefix}student_program"
                                        );
                                    ?>

                                    <select id="select_scope" multiple >
                                        <?php if ( $programs ): ?>
                                            <?php foreach ( $programs as $program ): ?>
                                                
                                                <optgroup >

                                                    <?php
                                                        $program_info = scope_info($program->identificator, $academic_department);
                                                    ?>

                                                    <option 
                                                        value="<?= esc_attr($program->identificator) ?>"
                                                        class="program"
                                                        data-type="program"
                                                        <?= $program_info ? 'selected' : '' ?>
                                                        data-required="<?= $program_info && !empty($program_info['required']) ? 'true' : 'false'; ?>"
                                                    >
                                                        <?= esc_html($program->name) ?>
                                                    </option>

                                                    <?php
                                                        // Carreras del programa
                                                        $careers = $wpdb->get_results($wpdb->prepare(
                                                            "SELECT identificator, name 
                                                            FROM {$wpdb->prefix}careers_by_program
                                                            WHERE program_identificator = %s",
                                                            $program->identificator
                                                        ));
                                                    ?>

                                                    <?php if ($careers): ?>
                                                        <?php foreach ($careers as $career): ?>
                                                            
                                                            <?php
                                                                $career_info = scope_info($career->identificator, $academic_department);
                                                            ?>

                                                            <option 
                                                                value="<?= esc_attr($career->identificator) ?>"
                                                                class="career"
                                                                data-type="career"
                                                                <?= $career_info ? 'selected' : '' ?>
                                                                data-required="<?= $career_info && !empty($career_info['required']) ? 'true' : 'false'; ?>"
                                                            >
                                                                <?= esc_html($career->name) ?>
                                                            </option>

                                                            <?php
                                                                // Menciones de la carrera
                                                                $mentions = $wpdb->get_results($wpdb->prepare(
                                                                    "SELECT identificator, name 
                                                                    FROM {$wpdb->prefix}mentions_by_career
                                                                    WHERE career_identificator = %s",
                                                                    $career->identificator
                                                                ));
                                                            ?>

                                                            <?php if ($mentions): ?>
                                                                <?php foreach ($mentions as $mention): ?>

                                                                    <?php
                                                                        $mention_info = scope_info($mention->identificator, $academic_department);
                                                                    ?>

                                                                    <option 
                                                                        value="<?= esc_attr($mention->identificator) ?>"
                                                                        class="mention"
                                                                        data-type="mention"
                                                                        <?= $mention_info ? 'selected' : '' ?>
                                                                        data-required="<?= $mention_info && !empty($mention_info['required']) ? 'true' : 'false'; ?>"
                                                                    >
                                                                        <?= esc_html($mention->name) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>

                                                </optgroup>

                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </label>
                                
                                <div id="header_selected_list" >
                                    <span><?= __('Scope','edusystem') ?></span>
                                    <span><?= __('Required','edusystem') ?></span>
                                </div>

                                <div id="selected_list">

                                </div>
                                
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

<?php

    function scope_info($id, $academic_department) {
        foreach ($academic_department as $items) {
            if (isset($items[$id])) {
                return $items[$id]; // devuelve todo el array (identificator, required, etc.)
            }
        }
        return null;
    }

?>




