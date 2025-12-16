<?php
add_action('admin_menu', function() {
    add_menu_page(
        'Actualizar Documentos',
        'Actualizar Documentos',
        'manage_options',
        'render_update_documents_page',
        'render_update_documents_page',
        'dashicons-update',
        20
    );
});

function render_update_documents_page() {
    global $wpdb;
    $prefix = $wpdb->prefix;
    
    // Ejecutar acción 1
    if (isset($_POST['update_documents_1'])) {
        $sql1 = "UPDATE {$prefix}student_documents AS sd 
                 INNER JOIN {$prefix}documents AS d ON sd.document_id = d.name 
                 SET sd.type_file = d.type_file, sd.id_requisito = d.id_requisito, 
                     sd.doc_id = d.id";
        $wpdb->query($sql1);
        echo '<div class="updated"><p>Acción 1 ejecutada ✅</p></div>';
    }
    
    // Ejecutar acción 2
    if (isset($_POST['update_documents_2'])) {
        $sql2 = "UPDATE {$prefix}student_documents AS sd 
                 INNER JOIN {$prefix}documents_certificates AS dc ON sd.document_id = dc.document_identificator 
                 SET sd.type_file = dc.type_file, sd.id_requisito = dc.id_requisito, 
                     sd.doc_id = dc.id, sd.automatic = 1";
        $wpdb->query($sql2);
        echo '<div class="updated"><p>Acción 2 ejecutada ✅</p></div>';
    }
    
    // Paginación
    $per_page = 20;
    $page = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $offset = ($page - 1) * $per_page;
    
    // Consulta con LEFT JOIN para mostrar null/vacíos
    $results = $wpdb->get_results("
        SELECT sd.document_id, sd.type_file, sd.id_requisito 
        FROM {$prefix}student_documents AS sd 
        WHERE type_file IS NULL OR type_file = '' OR id_requisito IS NULL OR id_requisito = '' OR
              doc_id IS NULL OR doc_id = '' 
        LIMIT $offset, $per_page
    ");
    
    // Conteo de registros con null o vacíos
    $count_null = $wpdb->get_var("
        SELECT COUNT(*) 
        FROM {$prefix}student_documents 
        WHERE type_file IS NULL OR type_file = '' OR id_requisito IS NULL OR id_requisito = '' OR
              doc_id IS NULL OR doc_id = '' 
    ");
    
    // Calcular total de páginas SOLO si hay resultados
    $total_pages = 0;
    if ($count_null > 0) {
        $total_pages = ceil($count_null / $per_page);
    }
    
    ?>
    <div class="wrap">
        <h1>Actualizar Documentos de Estudiantes</h1>
        
        <form method="post">
            <input type="submit" name="update_documents_1" class="button button-primary" value="Actualizar por tabla de documentos">
            <input type="submit" name="update_documents_2" class="button button-secondary" value="Actualizar por tabla de certificados">
        </form>
        
        <h2>Listado de Documentos</h2>
        <p>Total registros con campos vacíos o NULL: <strong><?php echo $count_null; ?></strong></p>
        
        <?php if (empty($results)): ?>
            <div class="notice notice-info">
                <p>No hay documentos con campos vacíos o NULL.</p>
            </div>
        <?php else: ?>
            <table class="widefat fixed striped">
                <thead>
                    <tr>
                        <th>Document ID</th>
                        <th>Type File</th>
                        <th>ID Requisito</th>
                        <th>ID document</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo esc_html($row->document_id); ?></td>
                        <td><?php echo $row->type_file !== null && $row->type_file !== '' ? esc_html($row->type_file) : '<em>NULL/Vacío</em>'; ?></td>
                        <td><?php echo $row->id_requisito !== null && $row->id_requisito !== '' ? esc_html($row->id_requisito) : '<em>NULL/Vacío</em>'; ?></td>
                        <td><?php echo $row->doc_id !== null && $row->doc_id !== '' ? esc_html($row->doc_id) : '<em>NULL/Vacío</em>'; ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <?php if ($total_pages > 1): ?>
            <div class="tablenav">
                <div class="tablenav-pages">
                    <?php 
                    // Botón anterior
                    if ($page > 1): 
                        $prev_page = $page - 1;
                    ?>
                        <a class="prev-page button" href="?page=render_update_documents_page&paged=<?php echo $prev_page; ?>">
                            <span class="screen-reader-text">Página anterior</span>
                            <span aria-hidden="true">‹</span>
                        </a>
                    <?php endif; ?>
                    
                    <span class="paging-input">
                        <span class="tablenav-paging-text">
                            <?php printf('%d de <span class="total-pages">%d</span>', $page, $total_pages); ?>
                        </span>
                    </span>
                    
                    <?php 
                    // Botón siguiente
                    if ($page < $total_pages): 
                        $next_page = $page + 1;
                    ?>
                        <a class="next-page button" href="?page=render_update_documents_page&paged=<?php echo $next_page; ?>">
                            <span class="screen-reader-text">Página siguiente</span>
                            <span aria-hidden="true">›</span>
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php
}