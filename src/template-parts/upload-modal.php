<?php
echo "<div class='main-form-wrapper'>";
echo "<form class='main-upload-form' data-step='1'>";
get_template_part("src/template-parts/upload/info", null);
get_template_part("src/template-parts/upload/media", null);
get_template_part("src/template-parts/upload/image-editor", null);
get_template_part("src/template-parts/upload/nav-btn", null);
get_template_part("src/template-parts/upload/drop-down", null);
get_template_part("src/template-parts/upload/prices", null);
get_template_part("src/template-parts/upload/approval", null);
get_template_part("src/template-parts/upload/product-created", null);
echo "</form>";
echo "</div>";
