<div class="sub-modal-drop-down-inner">
    <div class="modal-drop-down-content">
        <div class="modal-content-top">
            <div class="close-upload-modal">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div>
                <?php esc_html_e("בחירת מותג", "swap"); ?>
            </div>
        </div>

        <!-- search -->
        <div class="search-container">
            <input class="search" type="text" placeholder="<?php esc_html_e('חפש/י כאן...', 'swap'); ?>" />
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.5 17.5L14.5834 14.5833M16.6667 9.58333C16.6667 13.4954 13.4954 16.6667 9.58333 16.6667C5.67132 16.6667 2.5 13.4954 2.5 9.58333C2.5 5.67132 5.67132 2.5 9.58333 2.5C13.4954 2.5 16.6667 5.67132 16.6667 9.58333Z" stroke="#858585" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </div>

        <div class="brand-wrap">
            <!-- list -->
            <div id="brand-list">
                <ul class="list child-categories">
                    <?php
                    $parent_categories = get_terms([
                        'taxonomy'   => "product_brand",
                        'hide_empty' => false,
                        'parent'     => 0
                    ]);

                    if (!empty($parent_categories)) {
                        usort($parent_categories, function ($a, $b) {
                            return strcmp($a->name, $b->name);
                        });

                        $grouped_categories = [];
                        foreach ($parent_categories as $parent) {
                            $first_letter = strtoupper(mb_substr($parent->name, 0, 1));
                            if (!isset($grouped_categories[$first_letter])) {
                                $grouped_categories[$first_letter] = [];
                            }
                            $grouped_categories[$first_letter][] = $parent;
                        }

                        foreach ($grouped_categories as $letter => $categories) {
                            echo '<li class="letter-group"><strong>' . esc_html($letter) . '</strong></li>';
                            foreach ($categories as $category) {
                                echo '<li data-type="brands" data-name="' . $category->name . '" data-child-id="' . $category->term_id . '"><span class="name">' . esc_html($category->name) . '</span></li>';
                            }
                        }
                    }
                    ?>
                </ul>
            </div>
            <!-- alphabet-sidebar -->
            <div class="alphabet-sidebar">
                <?php
                foreach (range('A', 'Z') as $letter) {
                    echo '<div class="alphabet-item" data-letter="' . $letter . '">' . $letter . '</div>';
                }
                ?>
            </div>
        </div>
    </div>




    <div class="upload-nav-btn-wrap-sub">
        <button class="upload-nav-btn-sub">
            <?php echo esc_html_e("סיום", "swap") ?>
        </button>
    </div>
</div>

<input id="selected-form-brands-name" require style="display: none;" type="text" name="brands-term-name" value=""/>