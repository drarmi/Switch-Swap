<?php
/*
Template Name: New Store Setup 1
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Защита от прямого доступа
}

get_header();

// Обработка отправки формы (пример, доработка обязательна)
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    // Получаем и санитизируем данные
    $store_name = sanitize_text_field( $_POST['store_name'] );
    $store_bio  = sanitize_textarea_field( $_POST['store_bio'] );
    
    // Обработка загрузки изображения профиля
    $profile_image_url = '';
    if ( ! empty( $_FILES['store_profile_image']['name'] ) ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        $uploaded = wp_handle_upload( $_FILES['store_profile_image'], array( 'test_form' => false ) );
        if ( isset( $uploaded['url'] ) ) {
            $profile_image_url = $uploaded['url'];
        }
    }
    
    // Пример сохранения данных в мета текущего пользователя (адаптируйте под Dokan)
    $current_user = wp_get_current_user();
    update_user_meta( $current_user->ID, 'store_name', $store_name );
    update_user_meta( $current_user->ID, 'store_bio', $store_bio );
    if ( $profile_image_url ) {
        update_user_meta( $current_user->ID, 'store_profile_image', $profile_image_url );
    }
    
    // Если требуется, обработайте и загрузку нового товара (поле new_item)
    // …

    echo '<div class="success-message">החנות נוצרה בהצלחה!</div>';
}
?>

<div id="new-store-setup">
  <form id="new-store-setup-form" method="post" enctype="multipart/form-data">
    
    <!-- Экран 1: Приветствие -->
    <div class="step step-1 active">
       <h1>הגדרת חנות חדשה</h1>
       <ul>
         <li>
            <strong>התחילי להרוויח בקלות</strong>
            <p>הפכי פריטים שלא בשימוש להכנסה זמינה על ידי העלאתם לחנות האישית שלך.</p>
         </li>
         <li>
            <strong>ניהול פריטים וחנות</strong>
            <p>קבעי מחירים, זמינות ותנאים אישיים לכל פריט בצורה פשוטה ונוחה.</p>
         </li>
         <li>
            <strong>ביטוח וניקוי יבש</strong>
            <p>כל עסקה מגובה בביטוח וניקוי יבש, כך שהפריטים שלך תמיד מוגנים.</p>
         </li>
         <li>
            <strong>שיתוף וקהילה רחבה</strong>
            <p>שתפי את הפריטים שלך לאלפי משתמשות המחפשות בדיוק את מה שאת מציעה.</p>
         </li>
       </ul>
       <button type="button" class="next-step">התחלה</button>
    </div>
    
    <!-- Экран 2: Ввод данных о магазине -->
    <div class="step step-2">
      <h1>פרטי החנות</h1>
      <p>הזן את פרטי החנות החדשה שלך.</p>
      <div class="form-group">
         <label for="store_name">שם החנות</label>
         <input type="text" id="store_name" name="store_name" placeholder="הזן/י את שם החנות" required>
      </div>
      <div class="form-group">
         <label for="store_bio">ביוגרפיה</label>
         <textarea id="store_bio" name="store_bio" placeholder="הוספת תיאור קצר" required></textarea>
      </div>
      <button type="button" class="next-step">המשך</button>
    </div>
    
    <!-- Экран 3: Загрузка изображения профиля -->
    <div class="step step-3">
      <h1>תמונת פרופיל</h1>
      <p>לחץ/י להעלאת תמונה</p>
      <div class="form-group">
         <input type="file" id="store_profile_image" name="store_profile_image" accept="image/*" required>
      </div>
      <button type="button" class="next-step">המשך</button>
      <div class="skip-upload" style="display:none;">
         <button type="button" class="skip-button">דילוג</button>
      </div>
      <!-- Попап с вариантами загрузки -->
      <div class="popup" id="image-upload-popup" style="display:none;">
         <div class="popup-content">
             <button type="button" class="upload-option" data-option="camera">צילום תמונה והעלאה</button>
             <button type="button" class="upload-option" data-option="gallery">העלאה מתוך גלריית התמונות</button>
             <button type="button" class="popup-cancel">ביטול</button>
         </div>
      </div>
      <!-- Попап с опцией удаления изображения после загрузки -->
      <div class="popup" id="delete-image-popup" style="display:none;">
         <div class="popup-content">
             <label>
                <input type="checkbox" name="delete_image" value="1"> מחיקת התמונה
             </label>
             <button type="button" class="popup-close">סגור</button>
         </div>
      </div>
    </div>
    
    <!-- Экран 4: Завершение -->
    <div class="step step-4">
      <h1>ברכות על החנות החדשה!</h1>
      <p>כעת ניתן להוסיף ולהעלות פריטים חדשים</p>
      <div class="form-group">
         <label for="new_item">העלאת פריט חדש</label>
         <input type="file" id="new_item" name="new_item" accept="image/*">
      </div>
      <button type="submit" class="finish-button">סיום</button>
    </div>
    
  </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const steps = document.querySelectorAll(".step");
    let currentStep = 0;
    
    // При клике на кнопку "next-step" переходим к следующему экрану
    document.querySelectorAll(".next-step").forEach(button => {
        button.addEventListener("click", function() {
            // Для шага 3: если нажали "המשך" – сначала показать попап с вариантами загрузки
            if ( steps[currentStep].classList.contains("step-3") ) {
                const popup = document.getElementById("image-upload-popup");
                if ( popup.style.display === "none" || popup.style.display === "" ) {
                    popup.style.display = "flex";
                    return;
                }
            }
            goToStep( currentStep + 1 );
        });
    });
    
    // Обработка кнопок "ביטול" в попапе
    document.querySelectorAll(".popup-cancel").forEach(button => {
       button.addEventListener("click", function() {
          this.closest(".popup").style.display = "none";
       });
    });
    
    // Обработка вариантов загрузки изображения
    document.querySelectorAll(".upload-option").forEach(button => {
        button.addEventListener("click", function(){
            document.getElementById("image-upload-popup").style.display = "none";
            // Инициировать клик по input[type=file]
            document.getElementById("store_profile_image").click();
            // Показать кнопку "דילוג" (skip)
            document.querySelector(".skip-upload").style.display = "block";
        });
    });
    
    // При выборе файла – показываем кнопку "דילוג"
    document.getElementById("store_profile_image").addEventListener("change", function(){
         document.querySelector(".skip-upload").style.display = "block";
    });
    
    // Логика кнопки "דילוג" для шага 3
    const skipButton = document.querySelector(".skip-button");
    if ( skipButton ) {
        skipButton.addEventListener("click", function(){
            // После загрузки файла можно показать дополнительный попап с опцией удаления изображения
            const deletePopup = document.getElementById("delete-image-popup");
            deletePopup.style.display = "flex";
            deletePopup.querySelector(".popup-close").addEventListener("click", function(){
                deletePopup.style.display = "none";
                goToStep( currentStep + 1 );
            });
        });
    }
    
    function goToStep( n ) {
        if ( n >= steps.length ) return;
        steps[currentStep].classList.remove("active");
        currentStep = n;
        steps[currentStep].classList.add("active");
    }
});
</script>

<style>
/* Основные стили для формы (порядок, отступы и т.д.) */
#new-store-setup {
   max-width: 600px;
   margin: 0 auto;
   padding: 20px;
   direction: rtl; /* Для поддержки правостороннего отображения (עברית) */
}
.step {
   display: none;
}
.step.active {
   display: block;
}
.form-group {
   margin-bottom: 15px;
}
input[type="text"],
textarea {
   width: 100%;
   padding: 8px;
   margin-top: 5px;
   box-sizing: border-box;
}
button {
   padding: 10px 20px;
   background-color: #C7A77F;
   color: #fff;
   border: none;
   cursor: pointer;
   margin-top: 10px;
}
button:hover {
   background-color: #8F6B45;
}
/* Стили для попапов */
.popup {
   position: fixed;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   background: rgba(0,0,0,0.5);
   display: flex;
   align-items: center;
   justify-content: center;
}
.popup-content {
   background: #fff;
   padding: 20px;
   border-radius: 5px;
   text-align: center;
}
</style>

<?php get_footer(); ?>
