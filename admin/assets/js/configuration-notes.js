jQuery(document).ready(function($) {
    var rowIndex = $('#grade-config-rows .grade-config-row').length;

    // Make table sortable
    $('#grade-config-rows').sortable({
        handle: '.sortable-handle',
        placeholder: 'sortable-placeholder',
        update: function(event, ui) {
            // Optional: update row indices if needed
        }
    });

    // Add new row
    $('#add-new-row').on('click', function() {
        var newRow = '<tr class="grade-config-row">' +
            '<td><span class="dashicons dashicons-menu sortable-handle"></span></td>' +
            '<td><input type="number" name="grade_configs[new_' + rowIndex + '][min_score]" step="0.01" min="0" max="100" required /></td>' +
            '<td><input type="text" name="grade_configs[new_' + rowIndex + '][literal_grade]" maxlength="5" required /></td>' +
            '<td><input type="number" name="grade_configs[new_' + rowIndex + '][calc_grade]" step="0.01" min="0" max="4" required /></td>' +
            '<td><button type="button" class="button remove-row">Remove</button></td>' +
            '</tr>';
        $('#grade-config-rows').append(newRow);
        rowIndex++;
    });

    // Remove row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
    });
});