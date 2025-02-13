<?php

function automatically_enrollment($student_id)
{
    global $wpdb;
    $table_students = $wpdb->prefix . 'students';
    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$student_id}");

    $expected_projection = load_expected_projection($student->initial_cut, $student->grade_id);
    load_automatically_enrollment($expected_projection, $student);
}

function load_expected_projection($initial_cut, $grade)
{
    $electives = 0;
    $max_expected = 0;
    $expected_matrix = [];
    switch ($grade) {
        // LOWER
        case 1:
            $max_expected = 1;
            switch ($initial_cut) {
                case 'A':
                    $electives = 9;
                    $expected_matrix = [
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                    ];
                    break;
                case 'B':
                    $electives = 8;
                    $expected_matrix = [
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ]
                    ];
                    break;
                case 'C':
                    $electives = 7;
                    $expected_matrix = [
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ]
                    ];
                    break;
                case 'D':
                    $electives = 6;
                    $expected_matrix = [
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ]
                    ];
                    break;
                case 'E':
                    $electives = 5;
                    $expected_matrix = [
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ]
                    ];
                    break;
            }
            break;
        // MIDDKE
        case 2:
            $max_expected = 1;
            $electives = 4;
            switch ($initial_cut) {
                case 'A':
                case 'B':
                case 'C':
                case 'D':
                case 'E':
                    $expected_matrix = [
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'regular',
                            'type' => 1 // regular
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ],
                        [
                            'subject' => 'elective',
                            'type' => 2 // elective
                        ]
                    ];
                    break;
            }
            break;
        // UPPER AND GRADUATED
        case 3:
        case 4:
            $max_expected = 2;
            $electives = 2;
            switch ($initial_cut) {
                case 'A':
                case 'B':
                case 'C':
                case 'D':
                case 'E':
                    $expected_matrix = [
                        [
                            [
                                'subject' => 'regular',
                                'type' => 1 // regular
                            ],
                            [
                                'subject' => 'regular',
                                'type' => 1 // regular
                            ]
                        ],
                        [
                            [
                                'subject' => 'regular',
                                'type' => 1 // regular
                            ],
                            [
                                'subject' => 'elective',
                                'type' => 2 // elective
                            ]
                        ],
                        [
                            [
                                'subject' => 'regular',
                                'type' => 1 // regular
                            ],
                            [
                                'subject' => 'elective',
                                'type' => 2 // elective
                            ]
                        ],
                        [
                            [
                                'subject' => 'regular',
                                'type' => 1 // regular
                            ]
                        ],
                        [
                            [
                                'subject' => 'regular',
                                'type' => 1 // regular
                            ]
                        ],
                    ];
                    break;
            }
            break;
    }

    return [
        'expected_matrix' => $expected_matrix,
        'max_expected' => $max_expected,
        'electives' => $electives
    ];
}

function load_automatically_enrollment($expected_projection, $student)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $table_school_subject_matrix_regular = $wpdb->prefix . 'school_subject_matrix_regular';
    $table_students = $wpdb->prefix . 'students';
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $matrix_regular = $wpdb->get_results("SELECT * FROM {$table_school_subject_matrix_regular}");
    $projection = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE student_id = {$student->id}");
    $load = load_current_cut_enrollment();
    $matrix_elective = load_available_electives($student);
    $last_inscriptions_electives_count = load_inscriptions_electives($student);
    $real_electives_inscriptions_count = load_inscriptions_electives_valid($student);
    $code = $load['code'];
    $cut = $load['cut'];
    $student_enrolled = 0;
    $count_expected_subject = 0;
    $count_expected_subject_elective = 0;
    $skip_cut = $student->skip_cut;
    $force_skip = false;

    if (!$projection) {
        return;
    }

    $projection_obj = json_decode($projection->projection);
    foreach ($expected_projection['expected_matrix'] as $key => $expected) {
        if ($student_enrolled == $expected_projection['max_expected']) {
            break;
        }

        if (isset($expected[0]['type'])) {
            $expected_projection['max_expected'] = count($expected);
            if ($expected_projection['max_expected'] == 1 && ($count_expected_subject <= 4 || $count_expected_subject_elective < 2)) {
                $expected_projection['max_expected'] = 2;
            }

            foreach ($expected as $key => $exc) {
                if ($exc['type'] == 1) {
                    $expected_subject = $matrix_regular[$count_expected_subject];
                    $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$expected_subject->subject_id}");
                    $inscriptions = $wpdb->get_results(
                        $wpdb->prepare(
                            "SELECT * FROM {$table_student_period_inscriptions} 
                            WHERE student_id = %d 
                            AND subject_id = %d 
                            AND (status_id = 3 OR status_id = 1)",
                            $student->id,
                            $expected_subject->subject_id
                        )
                    );
                    if (count($inscriptions) > 0) {
                        $count_expected_subject++;
                        continue;
                    }

                    $active_inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE subject_id = {$expected_subject->subject_id} AND status_id = 1");
                    if (count($active_inscriptions) >= (int) $subject->max_students) {
                        $count_expected_subject++;
                        $force_skip = true;
                        continue;
                    }

                    if (!$subject->is_open) {
                        $count_expected_subject++;
                        $force_skip = true;
                        continue;
                    }

                    $force_skip = false;
                    $subjectIds = array_column($projection_obj, 'subject_id');
                    $indexToEdit = array_search($subject->id, $subjectIds);
                    if ($indexToEdit !== false) {
                        $projection_obj[$indexToEdit]->cut = $cut;
                        $projection_obj[$indexToEdit]->this_cut = true;
                        $projection_obj[$indexToEdit]->code_period = $code;
                        $projection_obj[$indexToEdit]->calification = '';
                        $projection_obj[$indexToEdit]->is_completed = true;
                        $projection_obj[$indexToEdit]->welcome_email = false;
                    }

                    $wpdb->update($table_student_academic_projection, [
                        'projection' => json_encode($projection_obj)
                    ], ['id' => $projection->id]);

                    $wpdb->insert($table_student_period_inscriptions, [
                        'status_id' => 1,
                        'student_id' => $student->id,
                        'subject_id' => $subject->id,
                        'code_subject' => $subject->code_subject,
                        'code_period' => $code,
                        'cut_period' => $cut,
                        'type' => $subject->is_elective ? 'elective' : 'regular'
                    ]);

                    if ($count_expected_subject >= 4 && $real_electives_inscriptions_count < 2) {
                        $wpdb->update($table_students, [
                            'elective' => 1
                        ], ['id' => $student->id]);
                    }

                    $count_expected_subject++;
                    $student_enrolled++;
                } else {
                    if ($force_skip) {
                        $count_expected_subject_elective++;
                        $last_inscriptions_electives_count++;
                        continue;
                    }

                    if (count($matrix_elective) == 0) {
                        continue;
                    }

                    if ($last_inscriptions_electives_count > $count_expected_subject_elective) {
                        $count_expected_subject_elective++;
                        continue;
                    }

                    if ($skip_cut) {
                        $wpdb->update($table_students, [
                            'elective' => 0,
                            'skip_cut' => 0
                        ], ['id' => $student->id]);

                        $wpdb->insert($table_student_period_inscriptions, [
                            'status_id' => 2,
                            'student_id' => $student->id,
                            'code_period' => $code,
                            'cut_period' => $cut,
                            'type' => 'elective'
                        ]);
                        $count_expected_subject_elective++;
                        $last_inscriptions_electives_count++;
                        $skip_cut = false;
                        continue;
                    }

                    $wpdb->update($table_students, [
                        'elective' => 1
                    ], ['id' => $student->id]);
                    $count_expected_subject_elective++;
                    $student_enrolled++;
                }
            }
        } else {
            if ($expected['type'] == 1) {
                $expected_subject = $matrix_regular[$count_expected_subject];
                $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$expected_subject->subject_id}");
                $inscriptions = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM {$table_student_period_inscriptions} 
                        WHERE student_id = %d 
                        AND subject_id = %d 
                        AND (status_id = 3 OR status_id = 1)",
                        $student->id,
                        $expected_subject->subject_id
                    )
                );
                if (count($inscriptions) > 0) {
                    $count_expected_subject++;
                    continue;
                }

                $active_inscriptions = $wpdb->get_results("SELECT * FROM {$table_student_period_inscriptions} WHERE subject_id = {$expected_subject->subject_id} AND status_id = 1");
                if (count($active_inscriptions) >= (int) $subject->max_students) {
                    $count_expected_subject++;
                    $force_skip = true;
                    continue;
                }

                if (!$subject->is_open) {
                    $count_expected_subject++;
                    $force_skip = true;
                    continue;
                }

                $force_skip = false;
                $subjectIds = array_column($projection_obj, 'subject_id');
                $indexToEdit = array_search($subject->id, $subjectIds);
                if ($indexToEdit !== false) {
                    $projection_obj[$indexToEdit]->cut = $cut;
                    $projection_obj[$indexToEdit]->this_cut = true;
                    $projection_obj[$indexToEdit]->code_period = $code;
                    $projection_obj[$indexToEdit]->calification = '';
                    $projection_obj[$indexToEdit]->is_completed = true;
                    $projection_obj[$indexToEdit]->welcome_email = false;
                }

                $wpdb->update($table_student_academic_projection, [
                    'projection' => json_encode($projection_obj)
                ], ['id' => $projection->id]);

                $wpdb->insert($table_student_period_inscriptions, [
                    'status_id' => 1,
                    'student_id' => $student->id,
                    'subject_id' => $subject->id,
                    'code_subject' => $subject->code_subject,
                    'code_period' => $code,
                    'cut_period' => $cut,
                    'type' => $subject->is_elective ? 'elective' : 'regular'
                ]);

                if ($count_expected_subject >= 4 && $real_electives_inscriptions_count < 2) {
                    $wpdb->update($table_students, [
                        'elective' => 1
                    ], ['id' => $student->id]);
                }

                $count_expected_subject++;
                $student_enrolled++;
            } else {
                if ($force_skip) {
                    $count_expected_subject_elective++;
                    $last_inscriptions_electives_count++;
                    continue;
                }

                if (count($matrix_elective) == 0) {
                    continue;
                }

                if ($last_inscriptions_electives_count > $count_expected_subject_elective) {
                    $count_expected_subject_elective++;
                    continue;
                }

                if ($skip_cut) {
                    $wpdb->update($table_students, [
                        'elective' => 0,
                        'skip_cut' => 0
                    ], ['id' => $student->id]);

                    $wpdb->insert($table_student_period_inscriptions, [
                        'status_id' => 2,
                        'student_id' => $student->id,
                        'code_period' => $code,
                        'cut_period' => $cut,
                        'type' => 'elective'
                    ]);
                    $count_expected_subject_elective++;
                    $last_inscriptions_electives_count++;
                    $skip_cut = false;
                    continue;
                }

                $wpdb->update($table_students, [
                    'elective' => 1
                ], ['id' => $student->id]);
                $count_expected_subject_elective++;
                $student_enrolled++;
            }
        }
    }
}

function load_available_electives($student)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_school_subject_matrix_elective = $wpdb->prefix . 'school_subject_matrix_elective';
    $conditions = array();
    $params = array();

    $electives_ids = $wpdb->get_col("SELECT subject_id FROM {$table_student_period_inscriptions} WHERE student_id = {$student->id} AND (status_id = 3 OR status_id = 1) AND subject_id IS NOT NULL");
    if (!empty($electives_ids)) {
        $conditions[] = "subject_id NOT IN (" . implode(',', array_fill(0, count($electives_ids), '%d')) . ")";
        $params = array_merge($params, $electives_ids);
    }

    $query = "SELECT * FROM {$table_school_subject_matrix_elective}";

    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }

    $electives = $wpdb->get_results($wpdb->prepare($query, $params));
    return $electives;
}

function load_inscriptions_electives($student)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';

    $conditions = array();
    $params = array();

    $conditions[] = "student_id = %d";
    $params[] = $student->id;

    $conditions[] = "type = %s";
    $params[] = 'elective';

    $query = "SELECT * FROM {$table_student_period_inscriptions}";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    $inscriptions = $wpdb->get_results($wpdb->prepare($query, $params));
    return count($inscriptions);
}

function load_inscriptions_electives_valid($student)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_school_subject_matrix_elective = $wpdb->prefix . 'school_subject_matrix_elective';

    $matrix_elective = $wpdb->get_results("SELECT * FROM {$table_school_subject_matrix_elective}");
    $electives_ids = [];
    foreach ($matrix_elective as $key => $elective) {
        array_push($electives_ids, $elective->subject_id);
    }

    if (empty($electives_ids)) {
        return 0;
    }

    $conditions = array();
    $params = array();

    $conditions[] = "subject_id IN (" . implode(',', array_fill(0, count($electives_ids), '%d')) . ")";
    $params = array_merge($params, $electives_ids);

    $conditions[] = "student_id = %d";
    $params[] = $student->id;

    $conditions[] = "(status_id = 1 OR status_id = 3)";

    $query = "SELECT * FROM {$table_student_period_inscriptions}";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    $inscriptions = $wpdb->get_results($wpdb->prepare($query, $params));
    return count($inscriptions);
}

function load_inscriptions_regular_valid($student)
{
    global $wpdb;
    $table_student_period_inscriptions = $wpdb->prefix . 'student_period_inscriptions';
    $table_school_subject_matrix_regular = $wpdb->prefix . 'school_subject_matrix_regular';

    $matrix_regular = $wpdb->get_results("SELECT * FROM {$table_school_subject_matrix_regular}");
    $regulars_ids = [];
    foreach ($matrix_regular as $key => $regular) {
        array_push($regulars_ids, $regular->subject_id);
    }

    if (empty($regulars_ids)) {
        return 0;
    }

    $conditions = array();
    $params = array();

    $conditions[] = "subject_id IN (" . implode(',', array_fill(0, count($regulars_ids), '%d')) . ")";
    $params = array_merge($params, $regulars_ids);

    $conditions[] = "student_id = %d";
    $params[] = $student->id;

    $conditions[] = "(status_id = 1 OR status_id = 3)";

    $query = "SELECT * FROM {$table_student_period_inscriptions}";
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    $inscriptions = $wpdb->get_results($wpdb->prepare($query, $params));
    return count($inscriptions);
}

function generate_projection_student($student_id)
{
    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';

    $existing_projection = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*) FROM {$table_student_academic_projection} WHERE student_id = %d",
            $student_id
        )
    );

    if ($existing_projection > 0) {
        return;
    }

    $table_school_subject_matrix_regular = $wpdb->prefix . 'school_subject_matrix_regular';
    $matrix_regular = $wpdb->get_results("SELECT * FROM {$table_school_subject_matrix_regular}");
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $projection = [];

    foreach ($matrix_regular as $key => $regular) {
        $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$regular->subject_id}");
        array_push($projection, ['code_subject' => $subject->code_subject, 'subject_id' => $subject->id, 'subject' => $subject->name, 'hc' => $subject->hc, 'cut' => "", 'code_period' => "", 'calification' => "", 'is_completed' => false, 'this_cut' => false, 'welcome_email' => false]);
    }

    $wpdb->insert($table_student_academic_projection, [
        'student_id' => $student_id,
        'projection' => json_encode($projection)
    ]);
}

function send_welcome_subjects($student_id)
{
    global $wpdb;
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $table_students = $wpdb->prefix . 'students';
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $student = $wpdb->get_row("SELECT * FROM {$table_students} WHERE id = {$student_id}");
    $projection = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE student_id={$student_id}");
    if (!$projection) {
        return;
    }
    $projection_obj = json_decode($projection->projection);

    $filteredArray = array_filter($projection_obj, function ($item) {
        return $item->this_cut == true && $item->welcome_email == false;
    });
    $filteredArray = array_values($filteredArray);

    if (count($filteredArray) > 0) {
        $filteredArray = array_filter($projection_obj, function ($item) {
            return $item->this_cut == true;
        });
        $filteredArray = array_values($filteredArray);
    }

    $text = '';
    if (count($filteredArray) == 0 && !$student->elective) {
        if ($student->initial_cut != 'D') {
            $text = template_not_enrolled($student);
        }
    } else {
        $text = template_welcome_subjects($filteredArray, $student);
    }

    if (empty($text)) {
        return;
    }

    $email_student = WC()->mailer()->get_emails()['WC_Email_Sender_Student_Email'];
    $email_student->trigger($student, 'Welcome', $text);

    $user_parent = get_user_by('id', $student->partner_id);
    $email_student = WC()->mailer()->get_emails()['WC_Email_Sender_User_Email'];
    $email_student->trigger($user_parent, 'Welcome', $text);

    if (count($filteredArray) > 0) {
        foreach ($filteredArray as $key => $val) {
            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$val->subject_id}");
            $subjectIds = array_column($projection_obj, 'code_subject');
            $indexToEdit = array_search($subject->code_subject, $subjectIds);
            if ($indexToEdit !== false) {
                $projection_obj[$indexToEdit]->welcome_email = true;
            }
        }
    
        $wpdb->update($table_student_academic_projection, [
            'projection' => json_encode($projection_obj) // Ajusta el valor de 'projection' según sea necesario
        ], ['id' => $projection->id]);
    }
}

function template_welcome_subjects($filteredArray, $student) {
    global $wpdb;
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $load = load_current_cut_enrollment();
    $academic_period = $load['code'];
    $cut = $load['cut'];
    $period_cut = $wpdb->get_row("SELECT * FROM {$table_academic_periods_cut} WHERE code = '{$academic_period}' && cut = '{$cut}'");
    $date_start = DateTime::createFromFormat('Y-m-d', $period_cut->start_date);
    $date_end = DateTime::createFromFormat('Y-m-d', $period_cut->end_date);
    $start_date = $date_start->format('l, F j, Y');
    $end_date = $date_end->format('l, F j, Y');
    $text = '';
    $text .= '<div>';
    $text .= 'Estimado(a) estudiante ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', ' . strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ', en nombre del equipo académico de American Elite School, con sede en la ciudad del Doral, Florida-EEUU, nos permitimos anunciarle que, por disposición del Departamento de Educación de Florida y, dando cumplimiento a nuestro compromiso de trabajar en la mejora continua, el inicio de clases periodo ' . $cut . ' del programa Dual Diploma de American Elite School se ha reprogramado, teniendo su inicio el ' . translateDateToSpanish(dateString: $start_date);
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div>';
    $text .= 'Esta actualización garantiza que nuestros estudiantes continúen recibiendo la mejor experiencia educativa con herramientas optimizadas y alineadas con los más altos estándares académicos. Marcando el inicio del Periodo ' . $cut . ' correspondiente al Año Escolar ' . $academic_period;
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div>';
    $text .= '<div><strong>FECHA DE INICIO:</strong> ' . translateDateToSpanish(dateString: $start_date) . ' </div>';
    $text .= '<div><strong>FECHA DE CULMINACIÓN:</strong> ' . translateDateToSpanish($end_date) . ' </div>';
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div> A continuación, detallamos su <strong>Carga Académica</strong> de cursos ofertados para este periodo ' . $cut . ': </div>';

    if (count($filteredArray) > 0) {
        $text .= '<table style="margin: 20px 0px; border-collapse: collapse; width: 100%;">';
        $text .= '<thead>
        <tr>
            <th colspan="4" style="border: 1px solid gray;">
               <strong>CÓDIGO</strong>
            </th>
            <th colspan="8" style="border: 1px solid gray;">
                <strong>MATERIA</strong>
            </th>
        </tr>
    </thead>';
        $text .= '<tbody>';
        foreach ($filteredArray as $key => $val) {
            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$val->subject_id}");
            $text .= '<tr>';
            $text .= '<td colspan="4" style="border: 1px solid gray;">' . $subject->code_subject . '</td>';
            $text .= '<td colspan="8" style="border: 1px solid gray;">' . $subject->name . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $date_start->format('m-d-y') . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $date_end->format('m-d-y') . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $cut . '</td>';
            $text .= '</tr>';
        }
        $text .= '</tbody>';
        $text .= '</table>';
    }

    $text .= '<br>';

    if ($student->elective) {
        $text .= '<div>';
        $text .= '<strong>ELECTIVA CONFORME A SU ELECCIÓN</strong>';
        $text .= '</div>';
        $text .= '<br>';
    }

    $text .= '<div>';
    $text .= 'Si anteriormente recibió un correo electrónico con su carga académica, se debió a una actualización del sistema y le pedimos disculpas. Aquí está su carga académica correcta.';
    $text .= '</div>';
    $text .= '<br>';

    $text .= '<div> Dejamos a su disposición enlaces y contactos de interés: </div>';

    $text .= '<ul>';
    $text .= '<li>Página web: <a href="https://american-elite.us/" target="_blank">https://american-elite.us/</a></li>';
    $text .= '<li>Aula virtual: <a href="https://online.american-elite.us/" target="_blank">https://online.american-elite.us/</a></li>';
    $text .= '<li>Contacto: <a href="https://soporte.american-elite.us" target="_blank">https://soporte.american-elite.us</a></li>';
    $text .= '</ul>';
    $text .= '<div>En nombre de nuestra institución, le agradecemos por su compromiso y le deseamos un periodo académico lleno de logros satisfactorios.</div>';
    $text .= '<div style="margin: 10px 0px; border-bottom: 1px solid gray;"></div>';

    
    $text .= '<div>';
    $text .= 'Dear student ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', ' . strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ', on behalf of the academic team at American Elite School, located in Doral, Florida, USA, we would like to inform you that, in accordance with the Florida Department of Education\'s guidelines and in compliance with our commitment to continuous improvement, the start of classes for Period ' . $cut . ' of the Dual Diploma Program at American Elite School has been rescheduled to begin on ' . $start_date;
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div>';
    $text .= 'This update ensures that our students continue to receive the best educational experience with optimized tools and aligned with the highest academic standards, marking the start of Period ' . $cut . ' for the ' . $academic_period . ' school year.';
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div>';
    $text .= '<div><strong>START DATE:</strong> ' . $start_date . ' </div>';
    $text .= '<div><strong>END DATE:</strong> ' . $end_date . ' </div>';
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div> Listed below is your <strong>Academic Load</strong> of mandatory courses registered for this Period ' . $cut . ': </div>';

    if (count($filteredArray) > 0) {
        $text .= '<table style="margin: 20px 0px; border-collapse: collapse; width: 100%;">';
        $text .= '<thead>
            <tr>
                <th colspan="4" style="border: 1px solid gray;">
                    <strong>COURSE CODE</strong>
                </th>
                <th colspan="8" style="border: 1px solid gray;">
                    <strong>SUBJECT</strong>
                </th>
            </tr>
        </thead>';
        $text .= '<tbody>';
        foreach ($filteredArray as $key => $val) {
            $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$val->subject_id}");
            $text .= '<tr>';
            $text .= '<td colspan="4" style="border: 1px solid gray;">' . $subject->code_subject . '</td>';
            $text .= '<td colspan="8" style="border: 1px solid gray;">' . $subject->name . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $date_start->format('m-d-y') . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $date_end->format('m-d-y') . '</td>';
            // $text .= '<td style="border: 1px solid gray;">' . $cut . '</td>';
            $text .= '</tr>';
        }
        $text .= '</tbody>';
        $text .= '</table>';
    }

    $text .= '<br>';

    if ($student->elective) {
        $text .= '<div>';
        $text .= '<strong>ELECTIVE ACCORDING TO YOUR SELECTION</strong>';
        $text .= '</div>';
        $text .= '<br>';
    }

    $text .= '<div>';
    $text .= 'If you previously received an email with your academic load, it was due to a system update and we apologize. Here is your correct academic load.';
    $text .= '</div>';
    $text .= '<br>';

    $text .= '<div> We leave at your disposal links and contacts of interest: </div>';

    $text .= '<ul>';
    $text .= '<li>Website: <a href="https://american-elite.us/" target="_blank">https://american-elite.us/</a></li>';
    $text .= '<li>Virtual classroom: <a href="https://online.american-elite.us/" target="_blank">https://online.american-elite.us/</a></li>';
    $text .= '<li>Contact us: <a href="https://soporte.american-elite.us" target="_blank">https://soporte.american-elite.us</a></li>';
    $text .= '</ul>';

    $text .= '<div>On behalf of our institution, we thank you for your commitment and wish you a successful academic term.</div>';

    return $text;
}

function translateDateToSpanish($dateString)
{
    $days = [
        'Monday' => 'Lunes',
        'Tuesday' => 'Martes',
        'Wednesday' => 'Miércoles',
        'Thursday' => 'Jueves',
        'Friday' => 'Viernes',
        'Saturday' => 'Sábado',
        'Sunday' => 'Domingo'
    ];

    $months = [
        'January' => 'Enero',
        'February' => 'Febrero',
        'March' => 'Marzo',
        'April' => 'Abril',
        'May' => 'Mayo',
        'June' => 'Junio',
        'July' => 'Julio',
        'August' => 'Agosto',
        'September' => 'Septiembre',
        'October' => 'Octubre',
        'November' => 'Noviembre',
        'December' => 'Diciembre'
    ];

    // Reemplazar días y meses en el string
    $dateString = str_replace(array_keys($days), array_values($days), $dateString);
    $dateString = str_replace(array_keys($months), array_values($months), $dateString);

    return $dateString;
}

function template_not_enrolled($student) {
    global $wpdb;
    $table_academic_periods_cut = $wpdb->prefix . 'academic_periods_cut';
    $load = load_current_cut_enrollment();
    $academic_period = $load['code'];
    $cut = $load['cut'];
    $period_cut = $wpdb->get_row("SELECT * FROM {$table_academic_periods_cut} WHERE code = '{$academic_period}' && cut = '{$cut}'");
    $date_start = DateTime::createFromFormat('Y-m-d', $period_cut->start_date);
    $date_end = DateTime::createFromFormat('Y-m-d', $period_cut->end_date);
    $start_date = $date_start->format('l, F j, Y');
    $end_date = $date_end->format('l, F j, Y');
    $text = '';
    $text .= '<div>';
    $text .= 'Estimado(a) estudiante ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', ' . strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ', en nombre del equipo académico de American Elite School, con sede en la ciudad del Doral, Florida-EEUU, nos permitimos anunciarle que, durante el Periodo ' . $cut . ' correspondiente al Año Escolar ' . $academic_period . ' no le será asignada carga académica ya que cuenta actualmente con el avance académico que corresponde a su año de ingreso.';
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div>';
    $text .= 'Dado que el periodo académico ' . $cut . ' tiene fecha de inicio el ' . translateDateToSpanish(dateString: $start_date) . ' y culmina el ' . translateDateToSpanish($end_date) . ', los invitamos a estar atentos a sus correos electrónicos donde serán notificados con la carga académica correspondiente al periodo siguiente.';
    $text .= '</div>';

    $text .= '<br>';
    $text .= '<div>';
    $text .= 'Si anteriormente recibió un correo electrónico con su carga académica, se debió a una actualización del sistema y le pedimos disculpas. Aquí está su carga académica correcta.';
    $text .= '</div>';

    $text .= '<br>';
    $text .= '<div> Dejamos a su disposición enlaces y contactos de interés: </div>';

    $text .= '<ul>';
    $text .= '<li>Página web: <a href="https://american-elite.us/" target="_blank">https://american-elite.us/</a></li>';
    $text .= '<li>Aula virtual: <a href="https://online.american-elite.us/" target="_blank">https://online.american-elite.us/</a></li>';
    $text .= '<li>Contacto: <a href="https://soporte.american-elite.us" target="_blank">https://soporte.american-elite.us</a></li>';
    $text .= '</ul>';
    $text .= '<div>En nombre de nuestra institución, le agradecemos por su compromiso y le deseamos un feliz descanso durante este periodo.</div>';
    $text .= '<div style="margin: 10px 0px; border-bottom: 1px solid gray;"></div>';

    $text .= '<div>';
    $text .= 'Dear student ' . strtoupper($student->last_name) . ' ' . strtoupper($student->middle_last_name) . ', ' . strtoupper($student->name) . ' ' . strtoupper($student->middle_name) . ', on behalf of the academic team at American Elite School, located in Doral, Florida, USA, we would like to inform you that, during Period ' . $cut . ' of the ' . $academic_period . ' school year, no academic load will be assigned to you, as you currently have the academic progress corresponding to your year of admission.';
    $text .= '</div>';

    $text .= '<br>';

    $text .= '<div>';
    $text .= 'Since Period ' . $cut . ' starts on ' . $start_date . ' and ends on ' . $end_date . ', we invite you to stay alert to your emails where you will be notified of the academic load for the following period.';
    $text .= '</div>';

    $text .= '<br>';
    $text .= '<div>';
    $text .= 'If you previously received an email with your academic load, it was due to a system update and we apologize. Here is your correct academic load.';
    $text .= '</div>';

    $text .= '<br>';
    $text .= '<div> We leave at your disposal links and contacts of interest: </div>';

    $text .= '<ul>';
    $text .= '<li>Website: <a href="https://american-elite.us/" target="_blank">https://american-elite.us/</a></li>';
    $text .= '<li>Virtual classroom: <a href="https://online.american-elite.us/" target="_blank">https://online.american-elite.us/</a></li>';
    $text .= '<li>Contact us: <a href="https://soporte.american-elite.us" target="_blank">https://soporte.american-elite.us</a></li>';
    $text .= '</ul>';
    $text .= '<div>On behalf of our institution, we thank you for your commitment and wish you a pleasant rest during this period.</div>';

    return $text;
}

function fix_projections($student_id)
{
    global $wpdb;
    $table_student_academic_projection = $wpdb->prefix . 'student_academic_projection';
    $projection = $wpdb->get_row("SELECT * FROM {$table_student_academic_projection} WHERE student_id={$student_id}");
    $projection_obj = json_decode($projection->projection);
    foreach ($projection_obj as $key => $value) {
        $projection_obj[$key]->welcome_email = $projection_obj[$key]->welcome_email ? true : ($projection_obj[$key]->is_completed && !$projection_obj[$key]->this_cut ? true : false);
    }

    $wpdb->update($table_student_academic_projection, [
        'projection' => json_encode($projection_obj) // Ajusta el valor de 'projection' según sea necesario
    ], ['id' => $projection->id]);
}