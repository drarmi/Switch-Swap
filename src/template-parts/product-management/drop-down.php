<?php
    if (! defined('ABSPATH')) {
        exit; // Exit if accessed directly
    }

    $product_id = (int)sanitize_text_field($_GET["management-productID"]);
    $product = wc_get_product($product_id);
    $additional_comments = get_post_meta($product_id, '_additional_comments', true) ?? "";
    $title = get_the_title($product_id) ?? "";
    $content = $product->get_description();

    $product_cat = wp_get_object_terms($product_id, 'product_cat');
    $product_cat = !empty($product_cat) && !is_wp_error($product_cat) ? reset($product_cat) : null;
    $product_cat_name = $product_cat ? $product_cat->name : "";
    $product_cat_slug = $product_cat ? $product_cat->slug : "";
    
    $product_brand = wp_get_object_terms($product_id, 'product_brand');
    $product_brand = !empty($product_brand) && !is_wp_error($product_brand) ? reset($product_brand) : null;
    $product_brand_name = $product_brand ? $product_brand->name : "";

    $size = wp_get_object_terms($product_id, 'size');
    $size = !empty($size) && !is_wp_error($size) ? reset($size) : null;
    $size_name = $size ? $size->name : "";

    $color = wp_get_object_terms($product_id, 'color');
    $color = !empty($color) && !is_wp_error($color) ? reset($color) : null;
    $color_name = $color ? $color->name : "";
    $color_hex = get_field("color", "term_". $color->term_id);

?>

<div class="section-title">
    <h4><?php esc_html_e("תמונות", "swap") ?></h4>
</div>

<div id="dropdown-wrap" class="modal modal-step" data-step="4">
    <div class="drop-down">
        <div class="drop-down-body">
            <div class="row" data-type="category">
                <span class="text category-title-span"><?php echo !empty($product_cat_name) ? esc_html($product_cat_name) : esc_html__("קטגוריה", "swap"); ?></span>
                <span>
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L6 6L11 1" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>
            <div class="row" data-type="brands">
                <span class="text"><?php echo !empty($product_brand_name) ? esc_html($product_brand_name) : esc_html__("מותג", "swap"); ?></span>
                <span>
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L6 6L11 1" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>
            <div class="row" data-type="sizes">
                <span class="text size-title-span"><?php echo !empty($size_name) ? esc_html($size_name) : esc_html__("מידה", "swap"); ?></span>
                <span>
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L6 6L11 1" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>
            <div class="row" data-type="colors">
                <svg class="color-hex-multi" style="display: none; margin-left: 10px" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#paint0_angular_1516_24684_clip_path)" data-figma-skip-parse="true"><g transform="matrix(0 0.01 -0.01 0 10 10)"><foreignObject x="-1100" y="-1100" width="2200" height="2200"><div xmlns="http://www.w3.org/1999/xhtml" style="background:conic-gradient(from 90deg,rgba(143, 107, 69, 1) 0deg,rgba(230, 103, 2, 1) 90deg,rgba(245, 229, 10, 1) 180deg,rgba(150, 210, 0, 1) 270deg,rgba(143, 107, 69, 1) 360deg);height:100%;width:100%;opacity:1"></div></foreignObject></g></g><circle cx="10" cy="10" r="10" data-figma-gradient-fill="{&#34;type&#34;:&#34;GRADIENT_ANGULAR&#34;,&#34;stops&#34;:[{&#34;color&#34;:{&#34;r&#34;:0.90196079015731812,&#34;g&#34;:0.40392157435417175,&#34;b&#34;:0.0078431377187371254,&#34;a&#34;:1.0},&#34;position&#34;:0.250},{&#34;color&#34;:{&#34;r&#34;:0.96078431606292725,&#34;g&#34;:0.89803922176361084,&#34;b&#34;:0.039215687662363052,&#34;a&#34;:1.0},&#34;position&#34;:0.50},{&#34;color&#34;:{&#34;r&#34;:0.58823531866073608,&#34;g&#34;:0.82352942228317261,&#34;b&#34;:0.0,&#34;a&#34;:1.0},&#34;position&#34;:0.750},{&#34;color&#34;:{&#34;r&#34;:0.56078433990478516,&#34;g&#34;:0.41960784792900085,&#34;b&#34;:0.27058824896812439,&#34;a&#34;:1.0},&#34;position&#34;:1.0}],&#34;stopsVar&#34;:[{&#34;color&#34;:{&#34;r&#34;:0.90196079015731812,&#34;g&#34;:0.40392157435417175,&#34;b&#34;:0.0078431377187371254,&#34;a&#34;:1.0},&#34;position&#34;:0.250},{&#34;color&#34;:{&#34;r&#34;:0.96078431606292725,&#34;g&#34;:0.89803922176361084,&#34;b&#34;:0.039215687662363052,&#34;a&#34;:1.0},&#34;position&#34;:0.50},{&#34;color&#34;:{&#34;r&#34;:0.58823531866073608,&#34;g&#34;:0.82352942228317261,&#34;b&#34;:0.0,&#34;a&#34;:1.0},&#34;position&#34;:0.750},{&#34;color&#34;:{&#34;r&#34;:0.56078433990478516,&#34;g&#34;:0.41960784792900085,&#34;b&#34;:0.27058824896812439,&#34;a&#34;:1.0},&#34;position&#34;:1.0}],&#34;transform&#34;:{&#34;m00&#34;:1.2246467996456087e-15,&#34;m01&#34;:-20.0,&#34;m02&#34;:20.0,&#34;m10&#34;:20.0,&#34;m11&#34;:1.2246467996456087e-15,&#34;m12&#34;:-1.2246467996456087e-15},&#34;opacity&#34;:1.0,&#34;blendMode&#34;:&#34;NORMAL&#34;,&#34;visible&#34;:true}"/>
                    <circle cx="10" cy="10" r="9.5" stroke="black" stroke-opacity="0.1"/>
                    <defs>
                    <clipPath id="paint0_angular_1516_24684_clip_path"><circle cx="10" cy="10" r="10"/></clipPath></defs>
                </svg> 
                <span class="color-hex" style="<?php echo !empty($color_hex) ? 'background: ' . $color_hex . '; width: 20px; margin-left: 10px; border: 1px solid rgba(0, 0, 0, 0.1);' : '';?>"></span>
                <span class="text color-title-span"><?php echo !empty($color_name) ? esc_html($color_name) : esc_html__("צבע", "swap"); ?></span>
                <span>
                    <svg width="12" height="7" viewBox="0 0 12 7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M1 1L6 6L11 1" stroke="#BFBFBF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
            </div>

            <input class="text-row" name="title" value="<?php echo esc_html($title); ?>" type="text" require placeholder="<?php esc_attr_e("כותרת", "swap") ?>">

            <div class="text-textarea">
                <span class="title"><?php esc_html_e("תיאור", "span"); ?></span>
                <textarea class="textarea-upload-content" data-max-length=128 name="description" require><?php echo esc_html($content); ?></textarea>
                <div class="text-area-count-wrap" style="<?php echo !empty($content) ? 'display: block' : '';?>"><span class="text-area-count"><?php echo esc_html(!empty($content) ? strlen($content) : 0) ?></span>/128</div>
            </div>

            <div class="text-textarea">
                <span class="title"><?php esc_html_e("הערות נוספות", "span"); ?></span>
                <textarea class="textarea-upload-content" data-max-length=128 name="additional_comments" require><?php echo esc_html($additional_comments); ?></textarea>
                <div class="text-area-count-wrap" style="<?php echo !empty($additional_comments) ? 'display: block' : '';?>"><span class="text-area-count"><?php echo esc_html(!empty($additional_comments) ? strlen($additional_comments) : 0) ?></span>/128</div>
            </div>

            <div class="require-field-error"><?php esc_html_e("מלא את כל השדות.") ?></div>
        </div>

        <div class="sub-modal-drop-down" data-type="category">
            <?php get_template_part("src/template-parts/product-management/categories", null, ["product_cat" => $product_cat]); ?>
        </div>
        <div class="sub-modal-drop-down" data-type="brands">
            <?php get_template_part("src/template-parts/product-management/brands", null, ["product_brand" => $product_brand]); ?>
        </div>
        <div class="sub-modal-drop-down" data-type="sizes">
            <?php get_template_part("src/template-parts/product-management/sizes", null, ["size" => $size, "product_cat_slug" => $product_cat_slug]); ?>
        </div>
        <div class="sub-modal-drop-down" data-type="colors">
            <?php get_template_part("src/template-parts/product-management/colors", null, ["color" => $color]); ?>
        </div>
   
    </div>
</div>