<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; padding: 25px; border-radius: 10px; box-shadow: 0 6px 12px rgba(0,0,0,0.08);">
    <h3 style="color: #333; font-size: 24px; margin-bottom: 25px; text-align: center;">
        <?= esc_html__('Payments status', 'edusystem') ?>
    </h3>

    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
        <div style="background-color: #fff; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 3px 6px rgba(0,0,0,0.05);">
            <p style="color: #777; font-size: 15px; margin-bottom: 10px;">
                <?= esc_html__('Pending payments', 'edusystem') ?>
            </p>
            <p style="font-size: 36px; font-weight: 600; color: #ff6347; margin: 0;" id="payments-to-do">
                <?= $widget_data['pending_payments_count'] ?>
            </p>
        </div>

        <div style="background-color: #fff; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 3px 6px rgba(0,0,0,0.05);">
            <p style="color: #777; font-size: 15px; margin-bottom: 10px;">
                <?= esc_html__('Payments for review', 'edusystem') ?>
            </p>
            <p style="font-size: 36px; font-weight: 600; color: #2ecc71; margin: 0;" id="payments-to-review">
                <?= $widget_data['payments_to_review_count'] ?>
            </p>
        </div>
    </div>

    <?php
        print_r($widget_data, true);
    ?>
    <div style="text-align: center; margin-top: 30px;">
        <a href="<?= $widget_data['pending_payments_link'] ?>" style="display: inline-block; text-decoration: none; background-color: #007bff; color: #fff; padding: 12px 25px; border-radius: 6px; font-size: 16px; font-weight: 500; transition: background-color 0.3s ease;">
            <?= esc_html__('View Details', 'edusystem') ?>
        </a>
    </div>
</div>