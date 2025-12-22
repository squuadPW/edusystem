<div class="wrap">

    <?php
                global $wpdb;
                $table_academic_periods = $wpdb->prefix . 'academic_periods';
                $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
                
                // Obtener valores 
                $academic_periods = $wpdb->get_results( "SELECT * FROM `{$table_academic_periods}`" );
                
                // Obtener valores actuales de los filtros
                $from_period = isset($_GET['from_period']) ? $_GET['from_period'] : '';
                $from_period_cut = isset($_GET['from_period_cut']) ? $_GET['from_period_cut'] : '';
                $to_period = isset($_GET['to_period']) ? $_GET['to_period'] : '';
                $to_period_cut = isset($_GET['to_period_cut']) ? $_GET['to_period_cut'] : '';

    ?>  
    
    <h2 style="margin-bottom:15px;"><?= __('Updating student documents', 'edusystem'); ?></h2>

    <?php
        include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <form method="get" >

            
                <div id="accions-docummets">

                    <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? esc_attr($_GET['page']) : ''; ?>" />

                    <div class="group" >

                        <!-- <h3><?= __('From:','edusystem') ?></h3> -->

                        <div>
                            <label for="from_period" >

                                <b><?= __('Academic Period:', 'edusystem'); ?></b>

                                <select id="from-period" name="from_period" required >
                                    <option value=""><?= __('select academic period', 'edusystem'); ?></option>
                                    <?php foreach ($academic_periods as $period): ?>
                                        <option value="<?= esc_attr($period->code); ?>" <?php selected($from_period, $period->code); ?>>
                                            <?= esc_html($period->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                    
                            <label for="from_period_cut"  >

                                <b><?= __('Cuts:', 'edusystem'); ?></b>

                                <select id="from-period-cut" name="from_period_cut" data-selected="<?= esc_attr($from_period_cut); ?>" >
                                    <option value=""><?= __('select academic cut', 'edusystem'); ?></option>
                                </select>
                            </label>
                        </div>
                    </div>

                    <!-- <div class="group" >

                        <h3><?= __('To:','edusystem') ?></h3>
                        
                        <div>
                            <label for="to_period" >

                                <b><?= __('Academic Period:', 'edusystem'); ?></b>

                                <select id="to-period" name="to_period" required >
                                    <option value=""><?= __('select academic period', 'edusystem'); ?></option>
                                    <?php foreach ($academic_periods as $period): ?>
                                        <option value="<?= esc_attr($period->code); ?>" <?php selected($to_period, $period->code); ?>>
                                            <?= esc_html($period->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                    
                            <label for="to_period_cut" >

                                <b><?= __('Cuts:', 'edusystem'); ?></b>

                                <select id="to-period-cut" name="to_period_cut" data-selected="<?= esc_attr($to_period_cut); ?>" required>
                                    <option value=""><?= __('select academic cut', 'edusystem'); ?></option>
                                </select>
                            </label>
                        </div>
                        
                    </div> -->
                    
                    <div class="group-accion" >
                        <?php 
                            submit_button(__('Filter', 'edusystem'), 'primary', 'filter_action', false, array('id' => 'post-query-submit')); 
                        ?>

                        <?php if (!empty($_GET['filter_action']) || !empty($_GET['filter_action'])): ?>
                            <a href="<?= remove_query_arg(['filter_action','from_period', 'from_period_cut', 'to_period', 'to_period_cut', 'paged']); ?>" class="button">
                                <?= __('Clear Filters', 'edusystem'); ?>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                </div>
        <?php
        
            $students_documents_table->display();
        ?>
    </form>
</div>



