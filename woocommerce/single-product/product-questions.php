<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$questions = get_field('questions_section', 'options');


?>
<div class="about-wrap dynamic-list-js questions">
    <?php if (!empty($questions)): ?>
        <h3><?php esc_html_e("שאלות נפוצות", "swap") ?></h3>
        <ul>
            <?php foreach ($questions as $question): ?>
                <li>
                    <div class="li-title">
                        <span>
                            <?php echo esc_html($question["title"]); ?>
                        </span>
                        <span>
                            <svg width="13" height="7" viewBox="0 0 13 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11.5 6L6.5 1L1.5 6" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                    </div>
                    <div class="li-content"><?php echo esc_html($question["text"]); ?></div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>