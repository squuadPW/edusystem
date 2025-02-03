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
    $projection_obj = json_decode($projection->projection);
    $student_enrolled = 0;
    $count_expected_subject = 0;
    $count_expected_subject_elective = 0;
    $skip_cut = $student->skip_cut;

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
                    if (count($active_inscriptions) >= 25) {
                        $count_expected_subject++;
                        continue;
                    }

                    $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$expected_subject->subject_id}");
                    $subjectIds = array_column($projection_obj, 'subject_id');
                    $indexToEdit = array_search($subject->id, $subjectIds);
                    if ($indexToEdit !== false) {
                        $projection_obj[$indexToEdit]->cut = $cut;
                        $projection_obj[$indexToEdit]->this_cut = true;
                        $projection_obj[$indexToEdit]->code_period = $code;
                        $projection_obj[$indexToEdit]->calification = '';
                        $projection_obj[$indexToEdit]->is_completed = true;
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
                if (count($active_inscriptions) >= 25) {
                    $count_expected_subject++;
                    continue;
                }

                $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$expected_subject->subject_id}");
                $subjectIds = array_column($projection_obj, 'subject_id');
                $indexToEdit = array_search($subject->id, $subjectIds);
                if ($indexToEdit !== false) {
                    $projection_obj[$indexToEdit]->cut = $cut;
                    $projection_obj[$indexToEdit]->this_cut = true;
                    $projection_obj[$indexToEdit]->code_period = $code;
                    $projection_obj[$indexToEdit]->calification = '';
                    $projection_obj[$indexToEdit]->is_completed = true;
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
    $table_school_subject_matrix_regular = $wpdb->prefix . 'school_subject_matrix_regular';
    $matrix_regular = $wpdb->get_results("SELECT * FROM {$table_school_subject_matrix_regular}");
    $table_school_subjects = $wpdb->prefix . 'school_subjects';
    $projection = [];

    foreach ($matrix_regular as $key => $regular) {
        $subject = $wpdb->get_row("SELECT * FROM {$table_school_subjects} WHERE id = {$regular->subject_id}");
        array_push($projection, ['code_subject' => $subject->code_subject, 'subject_id' => $subject->id, 'subject' => $subject->name, 'hc' => $subject->hc, 'cut' => "", 'code_period' => "", 'calification' => "", 'is_completed' => false, 'this_cut' => false]);
    }

    $wpdb->insert($table_student_academic_projection, [
        'student_id' => $student_id,
        'projection' => json_encode($projection)
    ]);
}