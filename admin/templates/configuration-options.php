<!-- Administrative Panel Header -->
<header>
  <h1>Administrative Panel</h1>
  <p>Welcome to the configuration options page!</p>
</header>

<!-- Configuration Options Section -->
<section>
  <h2>Configuration Options</h2>
  <form method="post" action="<?= admin_url('admin.php?page=add_admin_form_configuration_options_content&action=save_options'); ?>">
    <h4><strong>Settings for admission</strong></h4>
    <div class="form-group" style="padding: 0px 10px 10px 10px;">
      <label for="documents-ok">Days elapsed to display documents in green (less than):</label> <br>
      <span><</span><input type="number" id="documents-ok" name="documents_ok" value="<?php echo get_option('documents_ok') ?>" required>
    </div>
    <div class="form-group" style="padding: 10px">
      <label for="documents-warning">Days elapsed to display documents in warning (less than):</label> <br>
      <span><</span><input type="number" id="documents-warning" name="documents_warning" value="<?php echo get_option('documents_warning') ?>" required>
    </div>
    <div class="form-group" style="padding: 10px">
      <label for="documents-red">Days elapsed to display documents in red (greater than):</label> <br>
      <span>></span><input type="number" id="documents-red" name="documents_red" value="<?php echo get_option('documents_red') ?>" required>
    </div>

    <h4><strong>Settings for administration</strong></h4>
    <div class="form-group" style="padding: 0px 10px 10px 10px;">
      <label for="payment-due">Days elapsed after payment due to block access to the site (greater than):</label> <br>
      <span>></span><input type="number" id="payment-due" name="payment_due" value="<?php echo get_option('payment_due') ?>" required>
    </div>
    <button type="submit" class="button button-primary">Save Options</button>
  </form>
</section>