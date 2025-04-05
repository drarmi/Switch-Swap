jQuery(document).ready(function ($) {
  // Функция для получения vendor_id из URL
  function getVendorIdFromUrl() {
    // Пример URL: http://swap-new.loc/wp-admin/admin.php?page=dokan#/vendors/49
    const hash = window.location.hash; // например, "#/vendors/49"
    const parts = hash.split('/');
    return parts.length >= 3 ? parts[2] : null;
  }

  // Функция для отправки AJAX-запроса и вставки ссылки TikTok
  function insertTikTokLink() {
    const vendorId = getVendorIdFromUrl() || 0;
    if (!vendorId) {
      console.log("Vendor ID not found.");
      return;
    }
    $.ajax({
      url: myAjax.ajax_url, // URL из wp_localize_script
      type: 'POST',
      dataType: 'json',
      data: {
        action: 'my_get_tiktok_link',
        vendor_id: vendorId
      },
      success: function (response) {
        if (response.success && response.data.link) {
          const tiktokLink = response.data.link;
          const tiktokHtml = `
              <a href="${tiktokLink}" target="_blank" class="active">
                <i class="fab fa-tiktok" style="color: #B6224A;"></i>
              </a>
            `;
          $('.dokan-vendor-single .social-profiles .profiles').append(tiktokHtml);
        }
      },
      error: function () {
        console.log('TikTok AJAX error');
      }
    });
  }

  // Проверяем каждые 500 мс, появился ли блок соц. профилей (Vue может загружаться с задержкой)
  var checkInterval = setInterval(function () {
    if ($('.dokan-vendor-single .social-profiles').length) {
      clearInterval(checkInterval);
      insertTikTokLink();
    }
  }, 500);
});