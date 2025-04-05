jQuery(document).ready(function($) {

    // Проверяем, что мы на странице вендоров
    if ( window.location.href.indexOf('admin.php?page=dokan') === -1 ||
         window.location.hash.indexOf('/vendors') === -1 ) {
      return;
    }
  
    function addAuthenticateColumnHeader() {
      $('.wp-list-table thead tr').append('<th class="column authenticate">Authenticate</th>');
      $('.wp-list-table tfoot tr').append('<th class="column authenticate">Authenticate</th>');
    }
  
    function addAuthenticateCells() {
      $('.wp-list-table tbody tr').each(function() {
        var $row = $(this);
        var vendorId = $row.find('th.check-column input[type="checkbox"]').val();
        console.log(vendorId)
        if (!vendorId) {
          return;
        }
        var $cell = $('<td class="column authenticate">Loading...</td>');
        $row.append($cell);
  
        $.ajax({
          url: myAuthAjax.ajax_url,
          type: 'POST',
          dataType: 'json',
          data: {
            action: 'my_get_vendor_verification_status',
            vendor_id: vendorId
          },
          success: function(response) {
            if (response.success && response.data.status) {
              var status = response.data.status;
              var cellHtml = '<span class="verification-status">' + status + '</span>';
              if (status === 'pending') {
                cellHtml += ' <button class="approve-vendor button" data-vendor="' + vendorId + '">Approve</button>';
              }
              $cell.html(cellHtml);
            } else {
              $cell.html('<span class="verification-status">-</span>');
            }
          },
          error: function() {
            $cell.html('<span class="verification-status">Error</span>');
          }
        });
      });
    }
  
    $(document).on('click', '.approve-vendor', function(e) {
      e.preventDefault();
      var vendorId = $(this).data('vendor');
      var $button = $(this);
      $.ajax({
        url: myAuthAjax.ajax_url,
        type: 'POST',
        dataType: 'json',
        data: {
          action: 'my_update_vendor_verification_status',
          nonce: myAuthAjax.nonce,
          vendor_id: vendorId,
          status: 'approved'
        },
        success: function(response) {
          if (response.success && response.data.status) {
            $button.parent().find('.verification-status').text(response.data.status);
            $button.remove();
          } else {
            alert('Error updating status');
          }
        },
        error: function() {
          alert('AJAX error');
        }
      });
    });
  
    var checkInterval = setInterval(function() {
        if ($('.wp-list-table tbody .store_name').eq(0).prev().find('input[type="checkbox"]').val()) {

        console.log($('.wp-list-table .store_name').length)
        clearInterval(checkInterval);
        addAuthenticateColumnHeader();
        addAuthenticateCells();
      }
    }, 500);
  
  });
  