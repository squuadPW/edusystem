(function($) {
  $(document).ready(function() {
    // Select the radio inputs
    var selectedValue = 'Complete';
    var $radioInputs = $('input[type="radio"][name="option"]');

    $(document).on('updated_checkout', function() {
      reloadTable();
      reloadButton();
    });

    // Add an event listener to the radio inputs
    $radioInputs.on('change', function() {
      // Get the selected radio input value
      selectedValue = $(this).val();

      switch (selectedValue) {
        case 'Complete':
          var complete = document.getElementById('Complete');
          if (complete) {
            complete.style.display = 'table';
          }
          var annual = document.getElementById('Annual');
          if (annual) {
            annual.style.display = 'none';
          }
          var semmiannual = document.getElementById('Semiannual');
          if (semmiannual) {
            semmiannual.style.display = 'none';
          }
          break;
        case 'Annual':
          var annual = document.getElementById('Annual');
          if (annual) {
            annual.style.display = 'table';
          }
          var complete = document.getElementById('Complete');
          if (complete) {
            complete.style.display = 'none';
          }
          var semmiannual = document.getElementById('Semiannual');
          if (semmiannual) {
            semmiannual.style.display = 'none';
          }
          break;
        case 'Semiannual':
          var semmiannual = document.getElementById('Semiannual');
          if (semmiannual) {
            semmiannual.style.display = 'table';
          }
          var complete = document.getElementById('Complete');
          if (complete) {
            complete.style.display = 'none';
          }
          var annual = document.getElementById('Annual');
          if (annual) {
            annual.style.display = 'none';
          }
          break;
      }

      // Get the cart update URL
      var updateCartUrl = ajax_object.ajax_url + '?action=woocommerce_update_cart';

      // Send an AJAX request to update the cart
      $.ajax({
        type: 'POST',
        url: updateCartUrl,
        data: {
          'action': 'woocommerce_update_cart',
          'option': selectedValue
        },
        success: function(response) {
          // Update the cart price
          $('#cart_totals').html(response);
          $(document.body).trigger('update_checkout');
          setTimeout(() => {
            reloadTable();
          }, 250);
        }
      });
    });

    $('input[name="fee"]').on('change', function() {
      // Get the cart update URL
      var updateCartUrl = ajax_object.ajax_url + '?action=fee_update';

      // Send an AJAX request to update the cart
      $.ajax({
        type: 'POST',
        url: updateCartUrl,
        data: {
          'action': 'fee_update',
          'option': $(this).is(':checked')
        },
        success: function(response) {
          // Update the cart price
          $('#cart_totals').html(response);
          $(document.body).trigger('update_checkout');
          setTimeout(() => {
            reloadTable();
          }, 250);
        }
      });
    });

    // Add the applyScholarship function
    function applyScholarship() {
      // Apply the scholarship discount
      $.ajax({
        type: 'POST',
        url: ajax_object.ajax_url + '?action=apply_scholarship',
        data: {
          'action': 'apply_scholarship'
        },
        success: function(response) {
          // Update the cart price
          $('#cart_totals').html(response);
          $(document.body).trigger('update_checkout');
        }
      });
    }

    function reloadTable() {
      // Apply the scholarship discount
      $.ajax({
        type: 'POST',
        url: ajax_object.ajax_url + '?action=reload_payment_table',
        data: {
          'action': 'reload_payment_table',
          'option': selectedValue
        },
        success: function(response) {
          // Update the cart price
          $('#table-payment').html(response);
        }
      });
    }

    function reloadButton() {
      // Apply the scholarship discount
      $.ajax({
        type: 'GET',
        url: ajax_object.ajax_url + '?action=reload_button_schoolship',
        success: function(response) {
          // Update the cart price
          $('#button-schoolship').html(response);
          // Add an event listener to the button scholaships
          $('#apply-scholarship-btn').on('click', function() {
            applyScholarship();
          });
        }
      });
    }
  });
})(jQuery);