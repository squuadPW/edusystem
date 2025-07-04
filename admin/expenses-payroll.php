<?php

function get_expense_detail($expense_id)
{

    global $wpdb;
    $table_expenses = $wpdb->prefix . 'expenses';

    $expense = $wpdb->get_row("SELECT * FROM {$table_expenses} WHERE id={$expense_id}");
    return $expense;
}
