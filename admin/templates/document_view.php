<div id="document_view" class="wrap">
    
    <h2 style="margin-bottom:15px;"><?= __('Document','edusystem'); ?></h2>

    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= admin_url('admin.php?page=admission-documents'); ?>"><?= __('Back') ?></a>
    </div>

    <div id="dashboard-widgets" class="metabox-holder" >
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <form method="post" id="form_document" action="<?= admin_url('admin.php?page=admission-documents&action=update_document'); ?>">
                            
                            <input type="hidden" name="document_id" value="<?= $document->id ?? 0 ?>">

                            <h3 class="title" >
                                <b><?= __('Document Information', 'edusystem'); ?></b>
                            </h3>

                            <div>
                                <label for="input_id">
                                    <b><?= __('Name','edusystem'); ?></b>

                                    <input type="text" name="name" value="<?= $document->name ?? ''; ?>" style="width:100%">

                                </label>
                                
                            </div>
                            
                            <div>
                                <label for="is_required">
                                    <input name="is_required" type="checkbox" <?= ($document->is_required == 1) ? 'checked' : ''; ?> >
                                    
                                    <b><?= __('Required to access the virtual classroom in all areas','edusystem'); ?></b>
                                </label>
                            </div>

                            <div>
                                <label for="type_file">
                                    <b><?= __('Accepted File Types','edusystem'); ?></b>
                                    <input 
                                        type="text" 
                                        name="type_file" 
                                        value="<?= esc_attr($document->type_file ?? ''); ?>" 
                                        accept=".pdf,.docx,.jpg,.png"
                                    >
                                </label>
                                <p class="description">
                                    <?= __('Allowed formats: .pdf, .docx, .jpg, .png','edusystem'); ?>
                                </p>
                            </div>

                            <div>
                                <label for="id_requisito">
                                    <b><?= __('Moodle Requirement','edusystem'); ?></b>
                                    <input type="text" 
                                        name="id_requisito" 
                                        value="<?= esc_attr($document->id_requisito ?? ''); ?>"
                                    >
                                </label>
                            </div>

                            <div>
                                <h3 class="title" >
                                    <b><?= __('Scope of application','edusystem') ?></b>
                                </h3>

                                <br/>

                                <?php

                                    $academic_department = !empty($document->academic_department) 
                                    ? json_decode($document->academic_department, true) 
                                    : [];

                                    global $wpdb;
                                    $programs = $wpdb->get_results(
                                        "SELECT identificator, name FROM {$wpdb->prefix}student_program"
                                    );
                                ?>

                                <select id="select_scope" multiple name="academic_scope[]">
                                    <?php if ($programs): ?>
                                        <?php foreach ($programs as $program): ?>
                                            
                                            <optgroup >
                                                
                                                <!-- Programa seleccionable -->
                                                <option 
                                                    value="<?= esc_attr($program->identificator) ?>"
                                                    class="program"
                                                    <?= selected( isset($academic_department[$program->identificator]) ); ?>

                                                    data-required="<?= isset($academic_department[$program->identificator]) 
                                                        ? ($academic_department[$program->identificator]['required'] ? 'true' : 'false') 
                                                        : 'false'; ?>"
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
                                                            
                                                        <option 
                                                            value="<?= esc_attr($career->identificator) ?>"
                                                            class="career"
                                                            <?= selected( isset($academic_department[$career->identificator]) ); ?>

                                                            data-required="<?= isset($academic_department[$career->identificator]) 
                                                                        ? ($academic_department[$career->identificator]['required'] ? 'true' : 'false') 
                                                                        : 'false'; ?>"
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
                                                                <option 
                                                                    value="<?= esc_attr($mention->identificator) ?>"
                                                                    class="mention"
                                                                    <?= selected( isset($academic_department[$mention->identificator]) ); ?>
                                                                    
                                                                    data-required="<?= isset($academic_department[$mention->identificator]) 
                                                                        ? ($academic_department[$mention->identificator]['required'] ? 'true' : 'false') 
                                                                        : 'false'; ?>"
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





