<?php
// Se asegura de que $access esté definido como un array para count()
$access = isset($access) && is_array($access) ? $access : [];

if ($student_access) :
?>
	<div class="content-dashboard">
		<?php if ($admin_virtual_access) :
			// Se usa una variable para la condición de acceso al botón, mejor legibilidad.
			$has_assigned_courses = count($access) > 0;
			$button_disabled_style = $has_assigned_courses ? '' : 'background-color: #002fbd75 !important; pointer-events: none;';
			$target_attr = wp_is_mobile() ? '' : 'target="_blank"';
		?>
			<h4 style="font-size:18px;text-align:center; margin-bottom: 10px">
				<?= $has_assigned_courses
					? __('Access', 'edusystem')
					: __('You will soon be assigned your corresponding courses and will have access to', 'edusystem');
				?>
			</h4>
			<div style="display:flex">
				<div style="width: 100%; text-align: center">
					<a style="width: 60% !important; <?php echo $button_disabled_style; ?>"
						<?php echo $target_attr; ?>
						href="<?= home_url('?action=access_moodle_url&student_id=' . $student->id); ?>"
						class="button button-primary"><?= __('Virtual Classroom', 'edusystem'); ?></a>
				</div>
			</div>
		<?php else : ?>
			<h4 style="font-size: 18px; text-align: center; margin: 10px 0px; background-color: #e71f3b6e; padding: 20px; border-radius: 10px;">
				<?= __('For now the virtual classroom is under maintenance, we invite you to be attentive to your email to receive information about the next academic term.', 'edusystem'); ?>
			</h4>
		<?php endif; ?>
	</div>
<?php elseif ($error_access) : ?>
	<h4 style="font-size: 18px; text-align: center; margin: 10px 0px; background-color: #e71f3b6e; padding: 20px; border-radius: 10px;"><?= $error_access; ?></h4>
<?php endif; ?>

<?php
// Se verifica que la opción esté activa y que $subjects_coursing esté definido y no vacío.
if (get_option('show_table_subjects_coursing') && !empty($subjects_coursing) && is_array($subjects_coursing)):
?>
	<div class="text-center info-box content-dashboard">
		<div>
			<p>These are the subjects assigned during this term</p>
			<table style="margin: 20px 0px; border-collapse: collapse; width: 100%; font-size: 12px;">
				<thead>
					<tr>
						<th colspan="4" style="border: 1px solid gray;">
							<strong>COURSE CODE</strong>
						</th>
						<th colspan="8" style="border: 1px solid gray;">
							<strong>SUBJECT</strong>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					// Iteración de las materias. Se asume que get_subject_details() es eficiente y tiene caché.
					foreach ($subjects_coursing as $val):
						// Se asegura que $val sea un objeto y tenga la propiedad subject_id
						$subject_id = isset($val->subject_id) ? $val->subject_id : 0;
						if ($subject_id) {
							$subject = get_subject_details($subject_id);
							// Se asegura que $subject sea un objeto válido con las propiedades requeridas
							if ($subject && isset($subject->code_subject) && isset($subject->name)) :
					?>
								<tr>
									<td colspan="4" style="border: 1px solid gray;"><?php echo $subject->code_subject; ?></td>
									<td colspan="8" style="border: 1px solid gray;"><?php echo $subject->name; ?></td>
								</tr>
					<?php
							endif;
						}
					endforeach;
					?>
				</tbody>
			</table>
		</div>
	</div>
<?php endif; ?>