<div class="wrap">
    <h2 style="margin-bottom:15px;"><?= __('Scholarship details','aes'); ?></h2>

    <div style="diplay:flex;width:100%;">
        <a class="button button-outline-primary" href="<?= $_SERVER['HTTP_REFERER']; ?>"><?= __('Back') ?></a>
    </div>
    <div id="notice-scholarship-completed" style="display:none;" class="notice notice-info"><p><?= __('Scholarship Completed','aes'); ?></p></div>
    <div id="dashboard-widgets" class="metabox-holder">
        <div id="postbox-container-1" style="width:100% !important;">
            <div id="normal-sortables">
                <div id="metabox" class="postbox" style="width:100%;min-width:0px;">
                    <div class="inside">
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row" ><label for="input_id"><?= __('Scholarship ID','aes').':'; ?></label></th>
                                    <td>
                                        <?= '#'.$scholarship->id; ?>
                                    </td>

                                    <th scope="row"><label for="input_id"><?= __('Date','aes').':'; ?></label></th>
                                    <td><?= $scholarship->created_at ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="input_id"><?= __('Type Document','aes').':'; ?></label></th>
                                    <td><?= ucwords($student->type_document) ?></td>
                                    <th scope="row"><label for="input_id"><?= __('ID Document','aes').':'; ?></label></th>
                                    <td><?= $student->id_document ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="input_id"><?= __('Student','aes').':'; ?></label></th>
                                    <td><?= $student->name . ' ' . $student->middle_name . ' ' . $student->last_name . ' ' . $student->middle_last_name ?></td>

                                    <th scope="row"><label for="input_id"><?= __('Student contact','aes').':'; ?></label></th>
                                    <td><?= $student->email . ' - ' . $student->phone ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="input_id"><?= __('Birth date','aes').':'; ?></label></th>
                                    <td><?= $student->birth_date ?></td>
                                    <th scope="row"><label for="input_id"><?= __('Gender','aes').':'; ?></label></th>
                                    <td><?= $student->gender ?></td>
                                </tr>
                                
                                <tr>
                                    <th scope="row"><label for="input_id"><?= __('Country','aes').':'; ?></label></th>
                                    <td><?= $student->country ?></td>
                                    <th scope="row"><label for="input_id"><?= __('City','aes').':'; ?></label></th>
                                    <td><?= $student->city ?></td>
                                </tr>
                                <?php
                                $birth_date = $student->birth_date;
                                $today = new DateTime();
                                $age = $today->diff(new DateTime($birth_date))->y;

                                if ($age < 18) {
                                    ?>
                                    <tr>
                                        <th scope="row"><label for="input_id"><?= __('Partner','aes').':'; ?></label></th>
                                        <td><?= $partner->name . ' ' . $partner->middle_name . ' ' . $partner->last_name . ' ' . $partner->middle_last_name ?></td>

                                        <th scope="row"><label for="input_id"><?= __('Partner contact','aes').':'; ?></label></th>
                                        <td><?= $partner->email . ' - ' . $partner->phone ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <th scope="row"><label for="input_id"><?= __('Grade','aes').':'; ?></label></th>
                                    <td><?= $grade ? $grade->name : '' ?></td>

                                    <th scope="row"><label for="input_id"><?= __('Program','aes').':'; ?></label></th>
                                    <td><?= $student->program_id == 'aes' ? 'Dual diploma' : '' ?></td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="input_id"><?= __('Institute','aes').':'; ?></label></th>
                                    <td><?= $institute ? $institute->name : '' ?></td>
                                </tr>
                                
                            </tbody>
                        </table>
                            <?php if($scholarship->status_id != 1){ ?>
                                <div style="margin-top:10px;display:flex;flex-direction:row;width:100%;justify-content:end;">
                                    <?php if(wp_is_mobile()){ ?>
                                        <button data-message="<?= __('Do you want to approve this scholarship?','aes'); ?>" data-title="<?= __('Approve Scholarship','aes'); ?>" data-id="<?= $scholarship->id; ?>" id="approved_scholarship" style="width:100%;" class="button button-primary"><?= __('Approve Scholarship','aes'); ?></button>
                                    <?php }else{ ?>
                                        <button data-message="<?= __('Do you want to approve this scholarship?','aes'); ?>" data-title="<?= __('Approve Scholarship','aes'); ?>" data-id="<?= $scholarship->id; ?>" id="approved_scholarship" class="button button-primary"><?= __('Approve Scholarship','aes'); ?></button>
                                    <?php } ?>
                                </div>
                            <?php } ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    include(plugin_dir_path(__FILE__).'modal-status-scholarship.php');
?>