<div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f9f9f9; padding: 25px; border-radius: 10px; box-shadow: 0 6px 12px rgba(0,0,0,0.08);">
    <h3 style="color: #333; font-size: 24px; margin-bottom: 25px; text-align: center;">Requests status</h3>

    <div style="display: grid; grid-template-columns: repeat(1, 1fr); gap: 20px;">
        <div style="background-color: #fff; padding: 20px; border-radius: 8px; text-align: center; box-shadow: 0 3px 6px rgba(0,0,0,0.05);">
            <p style="color: #777; font-size: 15px; margin-bottom: 10px;">Requests for review</p>
            <p style="font-size: 36px; font-weight: 600; color: #2ecc71; margin: 0;" id="payments-to-review"><?= $widget_data['count'] ?></p>
        </div>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="<?= $widget_data['link'] ?>" style="display: inline-block; text-decoration: none; background-color: #007bff; color: #fff; padding: 12px 25px; border-radius: 6px; font-size: 16px; font-weight: 500; transition: background-color 0.3s ease;">View Details</a>
    </div>
</div>