<?php
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
?>

<div class="wrap">
    <h1><?php _e('Configuration Notes', 'edusystem'); ?></h1>
    
    <form method="post" action="">
        <?php wp_nonce_field('save_grade_configs_nonce'); ?>
        
        <table class="wp-list-table widefat fixed striped" id="grade-config-table">
            <thead>
                <tr>
                    <th><?php _e('Order', 'edusystem'); ?></th>
                    <th><?php _e('Min Score', 'edusystem'); ?></th>
                    <th><?php _e('Literal Grade', 'edusystem'); ?></th>
                    <th><?php _e('Calculated Grade', 'edusystem'); ?></th>
                    <th><?php _e('Actions', 'edusystem'); ?></th>
                </tr>
            </thead>
            <tbody id="grade-config-rows">
                <?php if (!empty($grade_configs)): ?>
                    <?php foreach ($grade_configs as $config): ?>
                        <tr class="grade-config-row">
                            <td><span class="dashicons dashicons-menu sortable-handle"></span></td>
                            <td>
                                <input type="number" 
                                       name="grade_configs[<?php echo $config['id']; ?>][min_score]" 
                                       value="<?php echo esc_attr($config['min_score']); ?>" 
                                       step="0.01" 
                                       min="0" 
                                       max="100" 
                                       required />
                            </td>
                            <td>
                                <input type="text" 
                                       name="grade_configs[<?php echo $config['id']; ?>][literal_grade]" 
                                       value="<?php echo esc_attr($config['literal_grade']); ?>" 
                                       maxlength="5" 
                                       required />
                            </td>
                            <td>
                                <input type="number" 
                                       name="grade_configs[<?php echo $config['id']; ?>][calc_grade]" 
                                       value="<?php echo esc_attr($config['calc_grade']); ?>" 
                                       step="0.01" 
                                       min="0" 
                                       max="4" 
                                       required />
                            </td>
                            <td>
                                <button type="button" class="button remove-row"><?php _e('Remove', 'edusystem'); ?></button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        
        <p>
            <button type="button" class="button" id="add-new-row"><?php _e('Add New Row', 'edusystem'); ?></button>
            <input type="submit" name="save_grade_configs" class="button button-primary" value="<?php _e('Save Changes', 'edusystem'); ?>" />
        </p>
    </form>
</div>
