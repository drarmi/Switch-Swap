<?php 
$terms = get_terms([
    'taxonomy'   => 'size',
    'hide_empty' => false,
]);
?>

<div class="sub-modal-drop-down-inner">
    <div class="modal-drop-down-content">
        <div class="modal-content-top">
            <div class="close-upload-modal">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="type-switcher-wrap">
                <div><?php esc_html_e("בחירת מידה", "swap"); ?></div>
                <div class="type-switcher">
                    <div class="type-switcher-item active" data-type="eu"><?php esc_html_e("EU", "swap"); ?></div>
                    <div class="type-switcher-item" data-type="us"><?php esc_html_e("US", "swap"); ?></div>
                </div>
            </div>
        </div>
        <div class="sizes-wrap eu">
            <?php if (!empty($terms) && !is_wp_error($terms)): ?>  
                <?php foreach ($terms as $term_parent): ?>
                        <?php if ($term_parent->parent == 0): ?>
                            <ul class="list child-categories size-group-js" data-slug="<?php echo esc_attr($term_parent->slug); ?>">
                                <li data-slug="<?php echo esc_attr($term_parent->slug); ?>" class="sizes-group"><?php echo esc_html($term_parent->name); ?></li>
                                <?php foreach ($terms as $term_child): ?>
                                    <?php if ($term_child->parent == $term_parent->term_id): ?>
                                        <li <?php echo get_field("us-size", "term_" . $term_child->term_id) ? "type-us" : "type-eu"; ?> data-type="sizes" data-name=" <?php echo esc_attr($term_child->name); ?>" data-ID="<?php echo esc_attr($term_child->term_id); ?>">
                                            <?php echo esc_html($term_child->name); ?>
                                        </li>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <div class="upload-nav-btn-wrap-sub">
        <button class="upload-nav-btn-sub">
            <?php echo esc_html_e("סיום", "swap") ?>
        </button>
    </div>
</div>

<input id="selected-form-size" require style="display: none;" type="text" name="size-term" value=""/>
<input id="selected-form-size-name" require style="display: none;" type="text" name="size-term-name" value=""/>