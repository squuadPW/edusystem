<div class="es-wrap">
    <style>
        .es-wrap {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial;
            max-width: 1100px;
            margin: 18px auto;
            padding: 20px
        }

        .es-card {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 6px 18px rgba(22, 27, 34, 0.08);
            padding: 22px
        }

        .es-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px
        }

        .es-title {
            font-size: 20px;
            font-weight: 600;
            color: #111
        }

        .es-sub {
            color: #6b7280;
            font-size: 13px
        }

        .es-actions {
            display: flex;
            gap: 10px;
            align-items: center
        }

        .es-btn {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 10px 14px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600
        }

        .es-btn.secondary {
            background: #eef2ff;
            color: #1f2937
        }

        .es-count {
            background: #eef2ff;
            color: #1f2937;
            padding: 6px 10px;
            border-radius: 999px;
            font-weight: 700
        }

        .es-search {
            padding: 8px 10px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            color: #111
        }

        .es-list {
            margin-top: 18px;
            display: grid;
            gap: 10px
        }

        .es-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #f3f4f6
        }

        .es-item .left {
            display: flex;
            gap: 12px;
            align-items: center
        }

        .es-avatar {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            background: linear-gradient(135deg, #60a5fa, #7c3aed);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 700
        }

        .es-meta {
            font-size: 14px
        }

        .es-subject {
            color: #6b7280;
            font-size: 13px
        }

        .es-empty {
            padding: 20px;
            text-align: center;
            color: #6b7280
        }

        @media (max-width:600px) {
            .es-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px
            }

            .es-actions {
                width: 100%;
                justify-content: space-between
            }
        }

        #enroll-all-button[disabled],
        #enroll-all-button.is-loading {
            pointer-events: none;
            /* Asegura que no se pueda hacer clic */
            opacity: 0.6;
            /* Vuelve el botón semitransparente */
            cursor: default;
            /* Cambia el cursor a normal */
        }
    </style>

    <?php include(plugin_dir_path(__FILE__) . 'cookie-message.php'); ?>

    <div class="es-card">
        <div class="es-header">
            <div>
                <div class="es-title"><?= __('EduSystem Autoenrollment', 'edusystem'); ?></div>
                <div class="es-sub"><?= sprintf(esc_html__('Period: %s — Term: %s', 'edusystem'), esc_html($code), esc_html($cut)); ?></div>
            </div>

            <div class="es-actions">
                <div class="es-count"><?= intval(count($expected_rows)) . ' ' . __('found', 'edusystem'); ?></div>
                <input id="es-search" class="es-search" placeholder="<?= esc_html__('Search student or subject...', 'edusystem'); ?>" />
                <button id="enroll-all-button" class="es-btn"><?= esc_html__('Enroll All', 'edusystem'); ?></button>
            </div>
        </div>

        <div id="es-list" class="es-list">
            <?php if (empty($expected_rows)) : ?>
                <div class="es-empty"><?= esc_html__('No students found for auto-enrollment.', 'edusystem'); ?></div>
            <?php else : ?>
                <?php foreach ($expected_rows as $expected) :
                    // $expected ahora es un objeto que contiene student_name, initials, subject_list y status
                    // student_names_lastnames_helper y get_subject_details ya no son necesarios aquí
                ?>
                    <div class="es-item" data-search="<?= esc_attr($expected->student_name . ' ' . $expected->subject_list); ?>">
                        <div class="left">
                            <div class="es-avatar"><?= esc_html($expected->initials); ?></div>
                            <div class="es-meta">
                                <div class="es-name"><?= esc_html($expected->student_name); ?></div>
                                <div class="es-subject"><?= esc_html($expected->subject_list); ?></div>
                            </div>
                        </div>
                        <div class="right">
                            <span class="es-sub"><?= esc_html($expected->status); ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>