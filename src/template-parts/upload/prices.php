<div id="prices-wrap" class="modal modal-step" data-step="5">
    <div class="prices">
        <div class="modal-content-top">
            <div class="close-upload-modal">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M18 6L6 18M6 6L18 18" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>

            <div class="roll-back-js" data-step="4">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M9 18L15 12L9 6" stroke="black" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
        </div>
        <div class="modal-text-top">
            <h3>מחיר ותמחור</h3>
            <p>בחירת סוג עסקה</p>
        </div>
        <div class="prices-body">
            <details class="row">
                <summary class="price-option custom-radio"><input type="checkbox" name="renting-option"><span></span>השכרה</summary>
                <div>
                    <div class="price">
                        <span class="title">הזנת תעריף יומי</span>
                        <div class="input-placeholder">
                            <span class="placeholder">₪</span>
                            <input class="row-main-price-js" type="number" name="renting_price" placeholder="100">
                        </div>
                    </div>
                    <div class="discounts-by-day">
                        <span class="title">הנחות לפי ימים</span>
                        <div class="days row-discount-parent-js">
                            <span class="days-count">4 יום ומעלה</span>
                            <div class="input-placeholder">
                                <span class="placeholder">%</span>
                                <input class="row-discount-input-js" type="number" name="rent-discount-day-4" placeholder="10" >
                            </div>
                            <span class="discount"><span class="discount-price row-discount-result-js">90</span> ₪ ליום</span>
                        </div>
                        <div class="days row-discount-parent-js">
                            <span class="days-count">8 יום ומעלה</span>
                            <div class="input-placeholder">
                                <span class="placeholder">%</span>
                                <input class="row-discount-input-js" type="number" name="rent-discount-day-8" placeholder="15">
                            </div>
                            <span class="discount"><span class="discount-price row-discount-result-js">85</span> ₪ ליום</span>
                        </div>
                    </div>
                </div>
            </details>
            <details class="row">
                <summary class="price-option custom-radio"><input type="checkbox" name="sale-option"><span></span>מכירה</summary>
                <div class="price">
                    <span class="title">מחיר לצרכן</span>
                    <div class="input-placeholder">
                        <span class="placeholder">₪</span>
                        <input class="row-main-price-js" type="number" name="price-bay-only" placeholder="100">
                    </div>
                </div>

                <div class="discounts-by-day">
                    <span class="title"><?php esc_html_e("אפשרות הנחה", "swap") ?></span>
                    <div class="days row-discount-parent-js">
                        <span class="days-count"><?php echo esc_html_e("אחוז הנחה", "swap") ?></span>
                        <div class="input-placeholder">
                            <span class="placeholder">%</span>
                            <input class="row-discount-input-js" type="number" name="sale-discount" placeholder="10">
                        </div>
                        <span class="discount"><span class="discount-price row-discount-result-js">90</span> ₪ ליום</span>
                    </div>
                </div>
            </details>
            <details class="row">
                <summary class="price-option custom-radio"><input type="checkbox" name="bids-option"><span></span>הגשת הצעות</summary>
                <div class="bids-prices">
                    <div class="bid-price">
                        <label>מחיר התחלתי</label>
                        <div class="input-placeholder">
                            <span class='placeholder'>₪</span>
                            <input type="number" name="min-price-rent" placeholder="300">
                        </div>
                    </div>
                    <div class="bid-price">
                        <!-- <label>מחיר מקסימום (אופציונלי)</label>
                        <div class="input-placeholder">
                            <span class='placeholder'>₪</span>
                            <input type="text" name="max-price-rent" placeholder="600">
                        </div> -->
                    </div>
                </div>
                <div class="bids-dates">
                    <div class="bid-date">
                        <label>תחילת המכירה</label>
                        <div class="input-placeholder">
                            <span class='placeholder'>
                                <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.4981 5.71085H11.0872M3.156 1.50007V2.87377M3.156 2.87377L11.1245 2.87362M3.156 2.87377C1.83569 2.87377 0.765494 3.9625 0.765555 5.30565L0.765925 13.412C0.765986 14.755 1.83626 15.8438 3.15648 15.8438H11.125C12.4453 15.8438 13.5156 14.7549 13.5156 13.4117L13.5152 5.30542C13.5151 3.96236 12.4447 2.87362 11.1245 2.87362M11.1245 1.5V2.87362M5.54715 13.0065V8.14271L3.95344 9.35866M9.92983 13.0065V8.14271L8.33613 9.35866" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <input class="date-validation-parsley" type="text" name="start-date-rent" placeholder="DD/MM/YYYY"
                                pattern="(0[1-9]|[12][0-9]|3[01])(\/|-|.)(0[1-9]|1[0-2])(\/|-|.)(19|20)\d{2}"
                                data-parsley-pattern="(0[1-9]|[12][0-9]|3[01])(\/|-|.)(0[1-9]|1[0-2])(\/|-|.)(19|20)\d{2}"
                                data-parsley-trigger="focusout"
                                data-parsley-pattern-message="DD-MM-YYYY או DD/MM/YYYY או DD.MM.YYYY"
                                data-parsley-check-past-date="true"
                                data-parsley-check-past-date-message="תאריך לא יכול להיות עבר">
                        </div>
                    </div>
                    <div class="bid-date">
                        <label>סיום המכירה</label>
                        <div class="input-placeholder">
                            <span class='placeholder'>
                                <svg width="15" height="17" viewBox="0 0 15 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M3.4981 5.71085H11.0872M3.156 1.50007V2.87377M3.156 2.87377L11.1245 2.87362M3.156 2.87377C1.83569 2.87377 0.765494 3.9625 0.765555 5.30565L0.765925 13.412C0.765986 14.755 1.83626 15.8438 3.15648 15.8438H11.125C12.4453 15.8438 13.5156 14.7549 13.5156 13.4117L13.5152 5.30542C13.5151 3.96236 12.4447 2.87362 11.1245 2.87362M11.1245 1.5V2.87362M5.54715 13.0065V8.14271L3.95344 9.35866M9.92983 13.0065V8.14271L8.33613 9.35866" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <input class="date-validation-parsley" type="text" name="end-date-rent" placeholder="DD/MM/YYYY"
                                pattern="(0[1-9]|[12][0-9]|3[01])(\/|-|.)(0[1-9]|1[0-2])(\/|-|.)(19|20)\d{2}"
                                data-parsley-pattern="(0[1-9]|[12][0-9]|3[01])(\/|-|.)(0[1-9]|1[0-2])(\/|-|.)(19|20)\d{2}"
                                data-parsley-trigger="focusout"
                                data-parsley-pattern-message="DD-MM-YYYY או DD/MM/YYYY או DD.MM.YYYY"
                                data-parsley-check-past-date="true"
                                data-parsley-check-past-date-message="תאריך לא יכול להיות עבר">
                        </div>
                    </div>
                </div>
                <div class="aditional-options">
                    <div class="aditional-option bids-buy-now">
                        <label class="custom-radio">
                            <input type="checkbox" name="bids-buy-now">
                            <span></span>
                            <?php esc_html_e("הוספת אפשרות “קני עכשיו”", "swap"); ?>
                        </label>

                        <div class="limited-discount-rent-wrap aditional-options-wrap">
                            <div class="price">
                                <span class="title"><?php esc_html_e("מחיר לצרכן", "swap") ?></span>
                                <div class="input-placeholder">
                                    <span class="placeholder">₪</span>
                                    <input class="row-main-price-js" type="number" name="price-bay-now" placeholder="100">
                                </div>
                            </div>

                            <div class="discounts-by-day">
                                <span class="title"><?php esc_html_e("אפשרות הנחה", "swap") ?></span>
                                <div class="days row-discount-parent-js">
                                    <span class="days-count"><?php echo esc_html_e("אחוז הנחה", "swap") ?></span>
                                    <div class="input-placeholder">
                                        <span class="placeholder">%</span>
                                        <input class="row-discount-input-js" type="number" name="discount-bay-now" placeholder="10">
                                    </div>
                                    <span class="discount"><span class="discount-price row-discount-result-js">90</span> ₪ ליום</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="show-bids-condition">
                    <button><?php esc_html_e("לתקנון ולתנאים", "swap"); ?></button>
                </div>
            </details>

            <div class="item-condition">
                <span class="title">מצב הפריט</span>
                <div class="conditions-list">
                    <label class="condition-btn">
                        <input type="radio" name="condition" value="new">
                        חדש
                    </label>
                    <label class="condition-btn">
                        <input type="radio" name="condition" value="good">
                        במצב טוב
                    </label>
                    <label class="condition-btn">
                        <input type="radio" name="condition" value="used">
                        משומש
                    </label>
                </div>
            </div>
        </div>
        <div class="upload-nav-btn-wrap">
            <button class="upload-nav-btn upload-nav-btn-black" data-step="6">
                המשך
            </button>
        </div>
    </div>
</div>

<?php
get_template_part('src/template-parts/upload/bids.conditions');
?>