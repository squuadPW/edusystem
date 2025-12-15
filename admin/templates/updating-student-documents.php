<div class="wrap">
    
    <h2 style="margin-bottom:15px;"><?= __('Updating student documents', 'edusystem'); ?></h2>

    <?php
        include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div>
        <form method="get">
            <input type="hidden" name="page" value="<?php echo isset($_GET['page']) ? esc_attr($_GET['page']) : ''; ?>" />
            <?php 
                // Mostrar filtros
                $students_documents_table->extra_tablenav('top');
                
                // Mostrar búsqueda si la quieres
                // $students_documents_table->search_box('Search', 'search');
                
                // Mostrar la tabla
                $students_documents_table->display();
            ?>
        </form>
    </div>
</div>



