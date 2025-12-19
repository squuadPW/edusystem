<div class="wrap">
    
    <h2 style="margin-bottom:15px;"><?= __('Updating student documents', 'edusystem'); ?></h2>

    <?php
        include(plugin_dir_path(__FILE__) . 'cookie-message.php');
    ?>

    <div>
        <?php 
            $students_documents_table->display();
        ?>
    </div>
</div>



