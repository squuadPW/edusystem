<?php if ($student_access) { ?>
	<div class="content-dashboard">
		<?php if($admin_virtual_access) { ?>
			<?php if(count($access) == 0) { ?>
				<h4 style="font-size:18px;text-align:center; margin-bottom: 10px"><?= __('You will soon be assigned your corresponding courses and will have access to','edusystem'); ?></h4>
			<?php } else { ?>
				<h4 style="font-size:18px;text-align:center; margin-bottom: 10px"><?= __('Access','edusystem'); ?></h4>
			<?php } ?>
			<div style="display:flex">
				<div style="width: 100%; text-align: center">
					<a style="width: 60% !important; <?php echo count($access) == 0 ? 'background-color: #002fbd75 !important; pointer-events: none;' : '' ?>" target="_blank" href="<?= home_url('?action=access_moodle_url&student_id='.$student->id); ?>" class="button button-primary"><?= __('Virtual Classroom','edusystem'); ?></a>
				</div>
			</div>
		<?php } else { ?>
			<h4 style="font-size: 18px; text-align: center; margin: 10px 0px; background-color: #e71f3b6e; padding: 20px; border-radius: 10px;"><?= __('For now the virtual classroom is under maintenance, we invite you to be attentive to your email to receive information about the next academic term.','edusystem'); ?></h4>
		<?php } ?>
	</div>
<?php } else if ($error_access) { ?>
	<h4 style="font-size: 18px; text-align: center; margin: 10px 0px; background-color: #e71f3b6e; padding: 20px; border-radius: 10px;"><?= $error_access; ?></h4>
<?php } ?>

<?php if (get_option('show_table_subjects_coursing') && isset($subjects_coursing) && count($subjects_coursing) > 0): ?>
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
					<?php foreach ($subjects_coursing as $key => $val): 
						$subject = get_subject_details($val->subject_id);
					?>
						<tr>
							<td colspan="4" style="border: 1px solid gray;"><?php echo $subject->code_subject; ?></td>
							<td colspan="8" style="border: 1px solid gray;"><?php echo $subject->name; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
<?php endif; ?>