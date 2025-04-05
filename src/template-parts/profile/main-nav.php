<?php
$nav_list = [
    "personal_details" => esc_html__("פרטים אישיים", "swap"),
    "my_orders" => esc_html__("ההזמנות שלי", "swap"),
    "personal_notifications" => esc_html__("ניהול התראות", "swap"),
    "authenticated" => esc_html__("סנכרון רשתות חברתיות", "swap"),
    "address_management" => esc_html__("ניהול כתובות", "swap"),
];

$current_tab = $_GET['tab'] ?? null;
$valid_tabs = array_keys($nav_list);
?>

<?php if (!$current_tab) : ?>
    <!-- ❱❱❱ Навігація + заголовок тільки на головній -->
    <div class="profile-top-header">
        <h2><?php esc_html_e("חשבון משתמש", "swap"); ?></h2>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </div>

    <div class="profile-nav-list">
        <nav>
            <ul>
                <?php foreach ($nav_list as $key => $value) : ?>
                    <li>
                        <a href="<?php echo esc_url(add_query_arg('tab', $key)); ?>">
                            <span>
                                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 7L9 12L14 17" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <span><?php echo $value; ?></span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </div>

    <div class="log-out-wrap">
        <a href="<?php echo wp_logout_url(home_url()); ?>" class="logout-btn">
            <span>
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10.1469 13.5996L12.6175 13.5996C12.992 13.5996 13.351 13.4521 13.6158 13.1896C13.8806 12.927 14.0293 12.5709 14.0293 12.1996L14.0293 3.79961C14.0293 3.42831 13.8806 3.07221 13.6158 2.80966C13.351 2.54711 12.992 2.39961 12.6175 2.39961L10.1469 2.39961M9.97148 7.99961L1.97148 7.99961M1.97148 7.99961L5.02825 11.1996M1.97148 7.99961L5.02825 4.79961" stroke="#8F6B45" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </span>
            <span><?php esc_html_e("התנתקות", "swap"); ?></span>
        </a>
    </div>
<?php endif; ?>

<!-- ❱❱❱ Контент табів -->
<div class="profile-content">
    <?php
    if ($current_tab && in_array($current_tab, $valid_tabs)) {
        get_template_part('src/template-parts/profile/' . $current_tab);
    } elseif (!$current_tab) {
        // Можна показати загальний опис або dashboard
        get_template_part('template-parts/profile/dashboard');
    } else {
        echo '<p>' . esc_html__('הדף לא נמצא.', 'swap') . '</p>';
    }
    ?>
</div>
