<?php
if ( is_user_logged_in() ) {
    $user_id = get_current_user_id();
}

$list_arr_field  = get_field('notifications-user', 'user_' . $user_id);

$list_arr = [
    "my_account" => [
        "title" => esc_attr__("החשבון שלי", "swap"),
        "sub_title" => esc_attr__("רישום, אימות חשבון, עדכון פרטים אישיים, שינוי סיסמה.", "swap"),
        "value" => 1,
    ],
    "my_shop" => [
        "title" => esc_attr__("החנות שלי", "swap"),
        "sub_title" => esc_attr__("ניהול מוצרים (העלאה, אישור, הסרה), מועדפים ומבצעים.", "swap"),
        "value" => 0,
    ],
    "shopping_and_purchases" => [
        "title" => esc_attr__("קניות ורכישות", "swap"),
        "sub_title" => esc_attr__("מצב העגלה, השלמת רכישה, תשלומים והחזרים כספיים.", "swap"),
        "value" => 1,
    ],
    "rentals_and_sales" => [
        "title" => esc_attr__("השכרות ומכירות", "swap"),
        "sub_title" => esc_attr__("השכרות, מכירות, תזכורות להחזרות, אישור עסקאות והזמנות.", "swap"),
        "value" => 0,
    ],
    "search_and_match" => [
        "title" => esc_attr__("חיפוש והתאמה", "swap"),
        "sub_title" => esc_attr__("הצעות למוצרים חדשים שיכולים להתאים לך.", "swap"),
        "value" => 0,
    ],
    "ratings_and_reviews" => [
        "title" => esc_attr__("דירוגים וביקורות", "swap"),
        "sub_title" => esc_attr__("ביקורות ודירוגים חדשים, בקשות ועריכות.", "swap"),
        "value" => 1,
    ],
    "auctions" => [
        "title" => esc_attr__("מכירות פומביות", "swap"),
        "sub_title" => esc_attr__("הצעות, סיום מכירה, אישורים וקבלת/דחיית הצעות.", "swap"),
        "value" => 1,
    ],
    "insights_and_data" => [
        "title" => esc_attr__("תובנות ונתונים", "swap"),
        "sub_title" => esc_attr__('אנליטיקה, דו"חות חודשיים, חשיפה וביצועים.', "swap"),
        "value" => 1,
    ],
    "support_and_service" => [
        "title" => esc_attr__("תמיכה ושירות", "swap"),
        "sub_title" => esc_attr__("מענה לפניות, עדכוני צ'אט, הפניית פנייה לנציג ואישור טיפול.", "swap"),
        "value" => 1,
    ],
    "email_updates" => [
        "title" => esc_attr__("עדכוני מייל", "swap"),
        "sub_title" => esc_attr__('אישור רכישה, שינוי פרטים, דו"חות חודשיים וכדומה.', "swap"),
        "value" => 0,
    ],
];

foreach ($list_arr as $key => &$value) {
    if (isset($list_arr_field[$key])) {
        $value["value"] = $list_arr_field[$key] == 1 ? 1 : 0;
    }
}
unset($value);

?>

<section class="notification_management modal-section">

<a href="<?php echo home_url('profile-page'); ?>" class="profile-top-header to-main-js">
    <h2><?php esc_attr_e("ניהול התראות", "swap") ?></h2>
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
</a>


<div class="personal_notifications-form">
    <form action="">
        <ul data-field-group="notifications-user">
            <?php foreach($list_arr as $key => $value) : ?>
                <li class="notification-li" data-notification-type="<?php echo esc_attr($key) ?>" data-notification-status="<?php echo $value["value"]; ?>">
                    <div>
                        <div class="switch-box"></div>
                    </div>
                    <div class="notification-tex">
                        <h5><?php echo $value["title"]; ?></h5>
                        <p><?php echo $value["sub_title"]; ?></p>
                    </div>
                    <input type="text" value="<?php echo $value["value"]; ?>" name="<?php echo esc_attr($key) ?>">
                </li>
            <?php endforeach; ?>
        </ul>
        <?php wp_nonce_field('notifications-form_action', 'notifications-form_nonce'); ?>
    </form>
</div>

</section>