// Указываем, что это модуль
export {};

declare global {
  interface Window {
    dokan_seller_rating_nonce?: string;
    ajaxurl?: string;
    omnis_ajax_object?: {
      ajaxurl?: string;
      nonce_dokan?: string;  
    };
  }
}

/**
 * Класс для работы с Dokan Review
 */
class DokanReview {
  private static instance: DokanReview;

  private constructor() {
    console.log('[DokanReview] constructor()');
    this.initEventListeners();
    this.initEditFormSubmitListener();
  }

  public static getInstance(): DokanReview {
    if (!DokanReview.instance) {
      DokanReview.instance = new DokanReview();
    }
    return DokanReview.instance;
  }

  /**
   * Вешаем обработчики на кнопки создания (Review Product) и редактирования (Edit Your Review).
   */
  private initEventListeners(): void {
    console.log('[DokanReview] initEventListeners()');
    // 1) "Review Product" — создаём новый отзыв
    const reviewButtons = document.querySelectorAll<HTMLButtonElement>('.dokan-store-review-button');
    reviewButtons.forEach((btn) => {
      btn.addEventListener('click', (event) => {
        event.preventDefault();
        this.submitDokanStoreReview(btn);
      });
    });

    // 2) "Edit Your Review" — редактируем существующий отзыв
    const editButtons = document.querySelectorAll<HTMLButtonElement>('.edit-review-btn');
    editButtons.forEach((btn) => {
      btn.addEventListener('click', (event) => {
        event.preventDefault();
        this.openEditReviewForm(btn);
      });
    });
  }

  /**
   * Обработчик нажатия кнопки "Review Product" (новый отзыв).
   */
  private submitDokanStoreReview(btn: HTMLButtonElement): void {
    console.log('[DokanReview] submitDokanStoreReview() called');
    const itemId = btn.getAttribute('data-item_id') || '';
    const storeId = btn.getAttribute('data_store_id') || '';

    // 1) Рейтинг
    const ratingSelect = document.getElementsByName('rating_' + itemId)[0] as HTMLSelectElement | undefined;
    const ratingValue = ratingSelect ? ratingSelect.value : '0';

    // 2) Текст отзыва
    const textField = document.getElementsByName('review_text_' + itemId)[0] as HTMLTextAreaElement | undefined;
    const reviewText = textField ? textField.value : '';

    // 3) product_id и product_name
    const productInput = document.getElementsByName('product_id_' + itemId)[0] as HTMLInputElement | undefined;
    const productId = productInput ? productInput.value : '';

    const productNameField = document.getElementsByName('product_name_' + itemId)[0] as HTMLInputElement | undefined;
    const productNameValue = productNameField ? productNameField.value : '';

    // получаем order_id
    const orderIdField = document.getElementsByName('order_id_' + itemId)[0] as HTMLInputElement | undefined;
    const orderIdValue = orderIdField ? orderIdField.value : '';

    // 4) checklist (чекбоксы)
    const checkboxPrefixes = [
      'review_big_',
      'review_small_',
      'review_defective_',
      'review_not_as_pic_',
      'review_late_',
    ];
    const checklist: string[] = [];
    checkboxPrefixes.forEach(prefix => {
      const inputEl = document.getElementsByName(prefix + itemId)[0] as HTMLInputElement | undefined;
      if (inputEl && inputEl.checked) {
        checklist.push(inputEl.value);
      }
    });

    // 5) correctness (radio)
    const radios = document.getElementsByName('item_status_' + itemId) as NodeListOf<HTMLInputElement>;
    let selectedRadioValue = '';
    radios.forEach(r => {
      if (r.checked) {
        selectedRadioValue = r.value;
      }
    });

    // Формируем formData
    const formData = new FormData();
    formData.append('action', 'dokan_store_rating_ajax_handler');
    formData.append('data', 'submit_review');

    // --- [Важный момент] ---
    // Плагин читает РЕЙТИНГ (rating) строго из $_POST['rating'],
    // а всё остальное из parse_str($_POST['form_data'], $postdata).
    // Поэтому rating надо отправить отдельным append:
    formData.append('rating', ratingValue);

    // Остальные поля кладём в form_data
    let fakeFormStr = '';
    fakeFormStr += 'dokan-review-title=' + encodeURIComponent('Review for Item ' + itemId);
    fakeFormStr += '&dokan-review-details=' + encodeURIComponent(reviewText);
    fakeFormStr += '&store_id=' + encodeURIComponent(storeId);
    fakeFormStr += '&order_id=' + encodeURIComponent(orderIdValue);

    // Добавляем nonce, если есть
    if (window.omnis_ajax_object?.nonce_dokan) {
      fakeFormStr += '&dokan-seller-rating-form-nonce=' + encodeURIComponent(window.omnis_ajax_object.nonce_dokan);
    }

    fakeFormStr += '&product_id=' + encodeURIComponent(productId);
    fakeFormStr += '&product_name=' + encodeURIComponent(productNameValue);

    checklist.forEach(val => {
      fakeFormStr += '&checklist[]=' + encodeURIComponent(val);
    });
    if (selectedRadioValue) {
      fakeFormStr += '&correctness=' + encodeURIComponent(selectedRadioValue);
    }

    // Кладём всё в form_data
    formData.set('form_data', fakeFormStr);

    // Обработка файлов: максимум 5
    const fileInputs = document.getElementsByName('review_image_' + itemId + '[]') as NodeListOf<HTMLInputElement>;
    if (fileInputs.length > 0) {
      const fileInput = fileInputs[0];
      if (fileInput.files && fileInput.files.length > 0) {
        const fileCount = Math.min(fileInput.files.length, 5);
        for (let i = 0; i < fileCount; i++) {
          formData.append('review_image_file[]', fileInput.files[i]);
        }
      }
    }

    // AJAX URL
    let ajaxUrl = window.ajaxurl || '/wp-admin/admin-ajax.php';
    if (window.omnis_ajax_object?.ajaxurl) {
      ajaxUrl = window.omnis_ajax_object.ajaxurl;
    }
    console.log('submitDokanStoreReview => posting to:', ajaxUrl);

    fetch(ajaxUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
      .then(resp => resp.json())
      .then(data => {
        console.log('[DokanReview] submitDokanStoreReview => response:', data);
        if (data.success) {
          alert(data.msg);
        } else {
          alert(data.msg || 'Unknown error');
        }
      })
      .catch(err => {
        console.error('[DokanReview] submitDokanStoreReview => error:', err);
      });
  }

  /**
   * Открытие формы редактирования отзыва (через child_edit_review_form).
   */
  private openEditReviewForm(btn: HTMLButtonElement): void {
    const postId = btn.getAttribute('data-post_id') || '';
    const storeId = btn.getAttribute('data-store_id') || '';

    const formData = new FormData();
    formData.append('action', 'child_edit_review_form');
    formData.append('post_id', postId);
    formData.append('store_id', storeId);

    // Добавляем nonce
    if (window.omnis_ajax_object?.nonce_dokan) {
      formData.append('dokan-seller-rating-form-nonce', window.omnis_ajax_object.nonce_dokan);
    }

    let ajaxUrl = window.ajaxurl || '/wp-admin/admin-ajax.php';
    if (window.omnis_ajax_object?.ajaxurl) {
      ajaxUrl = window.omnis_ajax_object.ajaxurl;
    }
    fetch(ajaxUrl, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    })
      .then(resp => resp.json())
      .then(data => {
        console.log('[DokanReview] openEditReviewForm => response:', data);
        if (data.success) {
          this.showModal(data.data);
        } else {
          alert(data.msg || 'Error loading edit form');
        }
      })
      .catch(err => {
        console.error('[DokanReview] openEditReviewForm => error:', err);
      });
  }

  /**
   * При отправке формы #dokan-edit-review-form обновляем отзыв
   */
  private initEditFormSubmitListener(): void {
    document.addEventListener('submit', (evt: Event) => {
      const form = evt.target as HTMLFormElement;
      if (form && form.id === 'dokan-edit-review-form') {
        evt.preventDefault();

        const formData = new FormData(form);
        // Плагин ждёт rating из $_POST['rating']
        // => придётся вручную достать rating из select или input
        //   (или если уже есть в форме как <input name="rating" ...>, возьмём оттуда)
        const ratingInput = form.querySelector('[name="rating"]') as HTMLSelectElement | HTMLInputElement | null;
        let ratingValue = '0';
        if (ratingInput) {
          ratingValue = (ratingInput as HTMLSelectElement).value || '0';
        }
        formData.append('rating', ratingValue);

        formData.append('action', 'dokan_store_rating_ajax_handler');
        formData.append('data', 'submit_review');

        // Также плагин читает всё остальное из parse_str($_POST['form_data'], $postdata)
        // => Соберём строку fakeFormStr = "store_id=...&dokan-review-title=...&..."
        // Но у нас уже есть все эти поля напрямую в formData. НО parse_str() их не видит,
        // т.к. parse_str() вызывается только на $_POST['form_data'].
        //
        // => Нужно вручную склеить строку и .set('form_data', fakeFormStr).
        let fakeFormStr = '';

        // Собираем все поля формы, кроме rating / файлов:
        // (для простоты возьмём те, что точно обрабатываются плагином)
        const titleField = form.querySelector<HTMLInputElement>('[name="dokan-review-title"]');
        if (titleField) {
          fakeFormStr += 'dokan-review-title=' + encodeURIComponent(titleField.value);
        }

        const detailsField = form.querySelector<HTMLTextAreaElement>('[name="dokan-review-details"]');
        if (detailsField) {
          fakeFormStr += '&dokan-review-details=' + encodeURIComponent(detailsField.value);
        }

        const nonceField = form.querySelector<HTMLInputElement>('[name="dokan-seller-rating-form-nonce"]');
        if (nonceField) {
          fakeFormStr += '&dokan-seller-rating-form-nonce=' + encodeURIComponent(nonceField.value);
        }

        // store_id
        const storeIdField = form.querySelector<HTMLInputElement>('[name="store_id"]');
        if (storeIdField) {
          fakeFormStr += '&store_id=' + encodeURIComponent(storeIdField.value);
        }

        // (Важное!) Добавляем post_id:
        const postIdField = form.querySelector<HTMLInputElement>('[name="post_id"]');
        if (postIdField) {
          fakeFormStr += '&post_id=' + encodeURIComponent(postIdField.value); 
        }

        // product_id
        const productIdField = form.querySelector<HTMLInputElement>('[name="product_id"]');
        if (productIdField) {
          fakeFormStr += '&product_id=' + encodeURIComponent(productIdField.value);
        }

        // product_name
        // (Если нужно, можно так же взять <input name="product_name">, если он есть)
        // checklist[]  => multiple checkboxes
        const checklistBoxes = form.querySelectorAll<HTMLInputElement>('input[name="checklist[]"]');
        checklistBoxes.forEach(box => {
          if (box.checked) {
            fakeFormStr += '&checklist[]=' + encodeURIComponent(box.value);
          }
        });

        // correctness => radio
        const correctnessRadios = form.querySelectorAll<HTMLInputElement>('input[name="correctness"]');
        correctnessRadios.forEach(r => {
          if (r.checked) {
            fakeFormStr += '&correctness=' + encodeURIComponent(r.value);
          }
        });

        // delete_images
        const deleteImagesField = form.querySelector<HTMLInputElement>('[name="delete_images"]');
        if (deleteImagesField) {
          fakeFormStr += '&delete_images=' + encodeURIComponent(deleteImagesField.value);
        }

        // Кладём fakeFormStr в formData
        formData.set('form_data', fakeFormStr);

        // Теперь файлы
        // 1) Проверяем уже прикрепленные
        let existingImagesCount = 0;
        const imagesContainer = form.querySelector('.dokan-review-images');
        if (imagesContainer) {
          existingImagesCount = imagesContainer.querySelectorAll('.dokan-review-image-item:not([data-deleted])').length;
          // Учитываем delete_images
          if (deleteImagesField && deleteImagesField.value) {
            const deletedCount = deleteImagesField.value.split(',').filter(val => val.trim() !== '').length;
            existingImagesCount = Math.max(0, existingImagesCount - deletedCount);
          }
        }

        // 2) Новые файлы (не более 5 - existingImagesCount)
        const fileInput = form.querySelector<HTMLInputElement>('input[name="review_image_file[]"]');
        if (fileInput && fileInput.files) {
          const allowed = 5 - existingImagesCount;
          const fileCount = Math.min(fileInput.files.length, allowed);
          for (let i = 0; i < fileCount; i++) {
            formData.append('review_image_file[]', fileInput.files[i]);
          }
        }

        // Выполняем AJAX
        let ajaxUrl = window.ajaxurl || '/wp-admin/admin-ajax.php';
        if (window.omnis_ajax_object?.ajaxurl) {
          ajaxUrl = window.omnis_ajax_object.ajaxurl;
        }

        fetch(ajaxUrl, {
          method: 'POST',
          body: formData,
          credentials: 'same-origin',
        })
          .then(resp => resp.json())
          .then((json) => {
            console.log('[DokanReview] edit-form response:', json);
            if (json.success) {
              alert('Review updated successfully.');
              this.closeModal();
              // Обновляем страницу, чтобы увидеть изменения
              location.reload();
            } else {
              alert(json.msg || 'Error updating review.');
            }
          })
          .catch((err) => console.error('[DokanReview] edit-form error:', err));
      }
    });
  }

  /**
   * Показ модального окна (overlay + container) + обработка удаления изображений
   */
  private showModal(html: string): void {
    let overlay = document.getElementById('dokan-modal-overlay');
    if (!overlay) {
      overlay = document.createElement('div');
      overlay.id = 'dokan-modal-overlay';
      Object.assign(overlay.style, {
        position: 'fixed',
        top: '0',
        left: '0',
        width: '100%',
        height: '100%',
        backgroundColor: 'rgba(0,0,0,0.5)',
        zIndex: '9999',
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
      });
      document.body.appendChild(overlay);
    }

    let container = document.getElementById('dokan-modal-container');
    if (!container) {
      container = document.createElement('div');
      container.id = 'dokan-modal-container';
      Object.assign(container.style, {
        backgroundColor: '#fff',
        padding: '20px',
        borderRadius: '5px',
        maxWidth: '600px',
        width: '100%',
        maxHeight: '80%',
        overflowY: 'auto',
      });
      overlay.appendChild(container);
    }

    container.innerHTML = html;

    // Навешиваем обработчики на кнопки удаления
    const delBtns = container.querySelectorAll<HTMLButtonElement>('.delete-image-btn');
    delBtns.forEach((btn) => {
      btn.addEventListener('click', (ev) => {
        ev.preventDefault();
        const imgId = btn.getAttribute('data-img-id');
        if (imgId && btn.parentElement) {
          btn.parentElement.style.opacity = '0.5';
          btn.parentElement.setAttribute('data-deleted', 'true');
        }
        const delInput = container!.querySelector<HTMLInputElement>('#delete_images');
        if (delInput) {
          if (delInput.value) {
            delInput.value += ',' + imgId;
          } else {
            delInput.value = imgId || '';
          }
        }
      });
    });

    // Закрытие при клике вне контейнера
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) {
        this.closeModal();
      }
    });
  }

  /**
   * Закрыть модальное окно
   */
  private closeModal(): void {
    const overlay = document.getElementById('dokan-modal-overlay');
    if (overlay) {
      overlay.remove();
    }
  }
}

// Инициализируем класс по DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
  DokanReview.getInstance();
});
