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
            opacity: 0.6;
            cursor: default;
        }

        .es-tabs {
            display: flex;
            margin-top: 18px;
            border-bottom: 2px solid #e5e7eb;
            overflow-x: auto;
            white-space: nowrap;
            padding-bottom: 2px;
        }

        .es-tab-button {
            padding: 10px 16px;
            cursor: pointer;
            border: none;
            background: transparent;
            font-size: 15px;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 2px solid transparent;
            transition: all 0.2s ease-in-out;
            margin-right: 1px;
        }

        .es-tab-button:hover {
            color: #111;
        }

        .es-tab-button.active {
            color: #2563eb;
            border-bottom: 2px solid #2563eb;
        }

        .es-tab-content {
            padding-top: 20px;
            display: none;
        }

        .es-tab-content.active {
            display: block;
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
                <div class="es-count"><?= intval($total_unique_students) . ' ' . __('students found', 'edusystem'); ?></div>
                <input id="es-search" class="es-search" placeholder="<?= esc_html__('Search student...', 'edusystem'); ?>" />
                <button id="enroll-all-button" class="es-btn"><?= esc_html__('Enroll All', 'edusystem'); ?></button>
            </div>
        </div>

        <?php if ($total_unique_students === 0) : ?>
            <div class="es-empty"><?= esc_html__('No students found for auto-enrollment.', 'edusystem'); ?></div>
        <?php else : ?>
            <div id="es-tabs" class="es-tabs">
                <?php $tab_index = 0; ?>
                <?php foreach ($expected_rows as $subject_data) : ?>
                    <button
                        class="es-tab-button <?= $tab_index === 0 ? 'active' : '' ?>"
                        data-target="tab-<?= $tab_index ?>">
                        <?= esc_html($subject_data['subject_name']); ?> (<?= count($subject_data['students']); ?>)
                    </button>
                    <?php $tab_index++; ?>
                <?php endforeach; ?>
            </div>

            <div id="es-list-container">
                <?php $tab_index = 0; ?>
                <?php foreach ($expected_rows as $subject_data) : ?>
                    <div id="tab-<?= $tab_index ?>" class="es-tab-content <?= $tab_index === 0 ? 'active' : '' ?>">
                        <div class="es-list">
                            <?php foreach ($subject_data['students'] as $student) : ?>
                                <div class="es-item" data-search="<?= esc_attr($student->student_name); ?>" data-student-id="<?= esc_attr($student->student_id); ?>">
                                    <div class="left">
                                        <div class="es-avatar"><?= esc_html($student->initials); ?></div>
                                        <div class="es-meta">
                                            <div class="es-name"><?= esc_html($student->student_name); ?></div>
                                            <div class="es-subject"><?= sprintf(esc_html__('Subject: %s', 'edusystem'), esc_html($subject_data['subject_name'])); ?></div>
                                        </div>
                                    </div>
                                    <div class="right">
                                        <span class="es-sub">
                                            <a target="_blank" class="button button-primary" href="<?= admin_url('admin.php?page=add_admin_form_academic_projection_content&section_tab=student_matrix&student_id=') . $student->student_id ?>">
                                                <?= __('Matrix', 'edusystem') ?>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php $tab_index++; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>