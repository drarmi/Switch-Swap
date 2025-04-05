
<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$product_cat = $args["product_cat"];
$product_cat_id = $product_cat->term_id;

$parent_categories = get_terms([
    'taxonomy'   => "product_cat",
    'hide_empty' => false,
    'parent'     => 0
]);

if (is_wp_error($parent_categories)) {
    return [];
}

?>
<div class="sub-modal-drop-down-inner">
    <div class="modal-drop-down-content">
        <div class="modal-content-top">
            <div class="close-sub-modal-js">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <?php esc_html_e("בחירת קטגוריה", "swap"); ?>
            </div>
        </div>

        <?php
        foreach ($parent_categories as $parent) {
            if ($parent->slug == "uncategorized") {
                continue;
            }
            echo '<h2 class="parent-categories">' . esc_html($parent->name) . '</h2>';

            $child_categories = get_terms([
                'taxonomy'   => "product_cat",
                'hide_empty' => false,
                'parent'     => $parent->term_id
            ]);

            if (!empty($child_categories) && !is_wp_error($child_categories)) {
                echo '<ul class="child-categories">';
                    echo '<li data-type="category" data-name="' . $parent->name . '" data-slug="' . $parent->slug . '" data-id="' . $parent->term_id . '">' . esc_html($parent->name) . '</li>';
                foreach ($child_categories as $child) {
                    echo '<li data-type="category" class="' . ($child->term_id == $product_cat_id ? "active" : "") . '" data-name="' . esc_attr($child->name) . '" data-slug="' . esc_attr($child->slug) . '" data-id="' . esc_attr($child->term_id) . '">' . esc_html($child->name) . '</li>';
                }
                echo '</ul>';
            }
        }
        ?>
    </div>
    <div class="upload-nav-btn-wrap-sub">
        <button class="upload-nav-btn-sub">
            <?php echo esc_html_e("סיום", "swap") ?>
        </button>
    </div>
</div>

<input id="selected-form-category"  hidden type="text" name="category-term-id" value="<?php echo esc_html($product_cat_id); ?>"/>