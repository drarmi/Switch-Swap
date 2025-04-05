<?php 

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

$color = $args["color"];
$color_id = $color->term_id;

$terms = get_terms([
    'taxonomy'   => 'color',
    'hide_empty' => false,
]);
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
            בחירת צבע            
            </div>
        </div>
        <div class="colors-wrap">
        <?php if (!empty($terms) && !is_wp_error($terms)): ?>
                <ul class="list child-categories">
                    <?php foreach ($terms as $term_parent): ?>
                        <?php if ($term_parent->parent == 0): ?>
                            <li data-slag="<?php echo esc_attr($term_parent->slug) ?>" class="colors-group"><?php echo esc_html($term_parent->name); ?></li>
                            <?php foreach ($terms as $term_child): ?>
                                <?php if ($term_child->parent == $term_parent->term_id):  ?>
                                    <?php if(!empty(get_field("color", "term_". $term_child->term_id))) : ?>
                                        <li data-type="colors" class="<?php echo $color_id == $term_child->term_id ? "active" : ""; ?>" data-hex="<?php echo esc_attr(get_field("color", "term_". $term_child->term_id)) ?>" data-slag="<?php echo esc_attr($term_child->slug) ?>" data-name="<?php echo esc_attr($term_child->name) ?>" data-id="<?php echo esc_attr($term_child->term_id) ?>"><span class="icon" style="background-color: <?php echo get_field("color", "term_". $term_child->term_id); ?>"></span><?php echo esc_html($term_child->name); ?></li>
                                    <?php endif; ?>
                                    <?php if($term_child->slug === "multi") : ?>
                                        <li data-type="colors" class="multi <?php echo $color_id == $term_child->term_id ? "active" : ""; ?>" data-slag="<?php echo esc_attr($term_child->slug) ?>" data-name="<?php echo esc_attr($term_child->name) ?>" data-id="<?php echo esc_attr($term_child->term_id) ?>">
                                            <svg style="border-radius: 20px" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <g clip-path="url(#paint0_angular_1516_24684_clip_path)" data-figma-skip-parse="true"><g transform="matrix(0 0.01 -0.01 0 10 10)"><foreignObject x="-1100" y="-1100" width="2200" height="2200"><div xmlns="http://www.w3.org/1999/xhtml" style="background:conic-gradient(from 90deg,rgba(143, 107, 69, 1) 0deg,rgba(230, 103, 2, 1) 90deg,rgba(245, 229, 10, 1) 180deg,rgba(150, 210, 0, 1) 270deg,rgba(143, 107, 69, 1) 360deg);height:100%;width:100%;opacity:1"></div></foreignObject></g></g><circle cx="10" cy="10" r="10" data-figma-gradient-fill="{&#34;type&#34;:&#34;GRADIENT_ANGULAR&#34;,&#34;stops&#34;:[{&#34;color&#34;:{&#34;r&#34;:0.90196079015731812,&#34;g&#34;:0.40392157435417175,&#34;b&#34;:0.0078431377187371254,&#34;a&#34;:1.0},&#34;position&#34;:0.250},{&#34;color&#34;:{&#34;r&#34;:0.96078431606292725,&#34;g&#34;:0.89803922176361084,&#34;b&#34;:0.039215687662363052,&#34;a&#34;:1.0},&#34;position&#34;:0.50},{&#34;color&#34;:{&#34;r&#34;:0.58823531866073608,&#34;g&#34;:0.82352942228317261,&#34;b&#34;:0.0,&#34;a&#34;:1.0},&#34;position&#34;:0.750},{&#34;color&#34;:{&#34;r&#34;:0.56078433990478516,&#34;g&#34;:0.41960784792900085,&#34;b&#34;:0.27058824896812439,&#34;a&#34;:1.0},&#34;position&#34;:1.0}],&#34;stopsVar&#34;:[{&#34;color&#34;:{&#34;r&#34;:0.90196079015731812,&#34;g&#34;:0.40392157435417175,&#34;b&#34;:0.0078431377187371254,&#34;a&#34;:1.0},&#34;position&#34;:0.250},{&#34;color&#34;:{&#34;r&#34;:0.96078431606292725,&#34;g&#34;:0.89803922176361084,&#34;b&#34;:0.039215687662363052,&#34;a&#34;:1.0},&#34;position&#34;:0.50},{&#34;color&#34;:{&#34;r&#34;:0.58823531866073608,&#34;g&#34;:0.82352942228317261,&#34;b&#34;:0.0,&#34;a&#34;:1.0},&#34;position&#34;:0.750},{&#34;color&#34;:{&#34;r&#34;:0.56078433990478516,&#34;g&#34;:0.41960784792900085,&#34;b&#34;:0.27058824896812439,&#34;a&#34;:1.0},&#34;position&#34;:1.0}],&#34;transform&#34;:{&#34;m00&#34;:1.2246467996456087e-15,&#34;m01&#34;:-20.0,&#34;m02&#34;:20.0,&#34;m10&#34;:20.0,&#34;m11&#34;:1.2246467996456087e-15,&#34;m12&#34;:-1.2246467996456087e-15},&#34;opacity&#34;:1.0,&#34;blendMode&#34;:&#34;NORMAL&#34;,&#34;visible&#34;:true}"/>
                                                <circle cx="10" cy="10" r="9.5" stroke="black" stroke-opacity="0.1"/>
                                                <defs>
                                                <clipPath id="paint0_angular_1516_24684_clip_path"><circle cx="10" cy="10" r="10"/></clipPath></defs>
                                            </svg>מולטי-צבע
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <div class="upload-nav-btn-wrap-sub">
        <button class="upload-nav-btn-sub">
            <?php echo esc_html_e("סיום", "swap") ?>
        </button>
    </div>
</div>


<input id="selected-form-color" require hidden type="text" name="color-term-id" value="<?php echo esc_html($color_id); ?>"/>