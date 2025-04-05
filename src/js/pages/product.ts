import axios from 'axios';
import $ from '../../../node_modules/jquery';
import 'parsleyjs';
import Swiper from 'swiper';
import { Navigation, Pagination } from 'swiper/modules';
import Pikaday from 'pikaday';

interface AjaxObject {
    ajaxurl: string;
    nonce: string;
    checkout_url: string;
    cart_url: string;
    date_set: object;
    discount_period: object;
}

declare const omnis_ajax_object: AjaxObject | undefined;

class ProductCustom {
    private static instance: ProductCustom;

    private pageProductCustom: HTMLElement | null;
    private swiperContainer: HTMLElement | null;
    private swiperContainerPerViewGap: NodeListOf<Element> | null;
    private shareButton: HTMLElement | null;
    private dynamicList: NodeListOf<Element> | null;
    private selectList: NodeListOf<HTMLElement> | null;
    private cardRadio: NodeListOf<Element> | null;
    private submitBTNoffer: HTMLElement | null;

    private constructor() {
        this.pageProductCustom = document.querySelector('.product-custom-wrapper') ?? null;
        this.swiperContainer = this.pageProductCustom?.querySelector('.swiper-container') ?? null;
        this.swiperContainerPerViewGap = this.pageProductCustom?.querySelectorAll('.swiper.mySwipePerViewGap') ?? null;
        this.shareButton = this.pageProductCustom?.querySelector('.share') ?? null;
        this.dynamicList = this.pageProductCustom?.querySelectorAll('.dynamic-list-js') ?? null;
        this.selectList = this.pageProductCustom?.querySelectorAll('.grid-section .section-select') ?? null;
        this.cardRadio = this.pageProductCustom?.querySelectorAll('input[name="card-radio"]') ?? null;
        this.submitBTNoffer = this.pageProductCustom?.querySelector(".offer-form button[type='submit']") ?? null;

        if (this.pageProductCustom) {
            const offerForm = this.pageProductCustom.querySelector('.offer-form');
            if (offerForm) {
                $(offerForm).parsley();
                if (this.submitBTNoffer) {
                    this.submitBTNoffer.addEventListener('click', this.handleSubmitOffer.bind(this));
                }
            }

            if (this.swiperContainer) {
                this.initSwiper();
            }

            if (this.swiperContainerPerViewGap) {
                this.initSwiperPerViewGap();
            }

            this.initEvent();

            this.initPikaday();
        }
    }

    public static getInstance(): ProductCustom {
        if (!this.instance) {
            this.instance = new this();
        }
        return this.instance;
    }

    private initEvent() {
        if (this.shareButton) {
            this.shareButton.addEventListener('click', this.toggleShare);
        }

        if (this.dynamicList) {
            this.dynamicList.forEach((list) => {
                list.querySelectorAll('ul li .li-title').forEach((element) => {
                    element.addEventListener('click', (event) => {
                        const target = event.currentTarget as HTMLElement | null;
                        if (target) {
                            const toggleElement = target
                                .closest('li')
                                ?.querySelector('.li-content') as HTMLElement | null;
                            if (toggleElement) toggleElement.classList.toggle('active');
                            if (toggleElement) target.classList.toggle('active');
                        }
                    });
                });
            });
        }

        if (this.selectList && this.selectList.length > 0) {
            this.selectList.forEach((select) => {
                select.addEventListener('click', this.dateInit.bind(this));
            });
        }

        if (this.cardRadio) {
            this.cardRadio.forEach((element) => {
                element.addEventListener('change', (event) => {
                    const target = event.currentTarget as HTMLInputElement | null;
                    const wrapBorder = target?.closest('.wrap-border');
                    if (this.pageProductCustom) {
                        const wrapBorders = this.pageProductCustom.querySelectorAll(
                            '.wrap-border'
                        ) as NodeListOf<Element> | null;
                        wrapBorders?.forEach((element) => {
                            element.removeAttribute('target');
                        });
                    }

                    if (wrapBorder && target) {
                        wrapBorder.setAttribute('target', target.value);
                    }
                });
            });
        }

        this.closeBitModal();
        this.makeReservation();
    }

    private toggleShare(event: any) {
        event.currentTarget.classList.toggle('active');
        const socialContainer = document.querySelector('.social') as HTMLElement | null;
        const swiperNext = document.querySelector('.swiper-button-next') as HTMLElement | null;
        const swiperPrev = document.querySelector('.swiper-button-prev') as HTMLElement | null;

        if (socialContainer && swiperNext && swiperPrev) {
            socialContainer.classList.toggle('active');
            swiperNext.classList.toggle('active');
            swiperPrev.classList.toggle('active');
        }
    }

    private dateInit(event: any) {
        const self = this;

        const target = event.currentTarget as HTMLElement | null;
        let productID = target?.dataset['productId'] ?? null;
        let day = target?.dataset['day'] ?? 0;
        let price = target?.dataset['price'] ?? null;
        let isRange = false;
        const calender = target?.closest('.bay-date-wrap')?.querySelector('.calender-wrap') as HTMLElement | null;
        const calendarPicker = calender?.querySelector('.calendar-picker-container') as HTMLElement | null;
        const calendarRange = calender?.querySelector('.calendar-picker-container-range') as HTMLElement | null;

        if (self.selectList) {
            self.selectList.forEach((select) => {
                if (target !== select && !select.classList.contains('range-selected'))
                    select.classList.remove('active');
                if (select.classList.contains('range-selected')) select.style.display = 'none';
                if (select.classList.contains('range-none')) select.style.display = 'flex';
            });
        }

        if (target) target.classList.toggle('active');

        target && target.classList.contains('active') && target.hasAttribute('days_range')
            ? (isRange = true)
            : (isRange = false);

        if (self.selectList && calender) {
            let isActive = false;

            self.selectList.forEach((select) => {
                if (select.classList.contains('active')) {
                    isActive = true;
                }
            });

            if (isActive) {
                if (isRange && calendarRange) {
                    calendarRange.style.display = 'block';
                    if (calendarPicker) calendarPicker.style.display = 'none';
                } else if (calendarPicker) {
                    calendarPicker.style.display = 'block';
                    if (calendarRange) calendarRange.style.display = 'none';
                }
                calender.style.display = 'flex';
                const total = calender.querySelector('.total .number') as HTMLElement | null;
                const buttonCheckout = calender.querySelector('.bay.button-checkout') as HTMLElement | null;
                const buttonCart = calender.querySelector('.cart.button-add-to-cart') as HTMLElement | null;
                const calenderContainer = calender.querySelector('.calendar-picker-container') as HTMLElement | null;

                if (total && price && productID) {
                    total.textContent = price ?? null;
                    calendarPicker?.setAttribute('price', price);
                    calendarRange?.setAttribute('price', price);
                }

                if (productID) {
                    calendarRange?.setAttribute('productID', productID);
                }

                // Update the href attribute with the dynamic product and variation IDs
                if (buttonCheckout && productID && day) {
                    buttonCheckout.setAttribute(
                        'href',
                        `${omnis_ajax_object?.checkout_url}?add-to-cart=${productID}&quantity=${day}`
                    );
                }

                if (calenderContainer && productID && day) {
                    const dayValue = typeof day === 'number' ? day : parseInt(day, 10);
                    if (!isNaN(dayValue)) {
                        calenderContainer.setAttribute('period', dayValue.toString());
                    } else {
                        console.error('Invalid day value:', day);
                    }
                }

                if (buttonCart && productID && day) {
                    buttonCart.setAttribute(
                        'href',
                        `${omnis_ajax_object?.cart_url}?add-to-cart=${productID}&quantity=${day}`
                    );
                }
            } else {
                calender.style.display = 'none';
            }
        }
    }

    private initPikaday() {
        const self = this;

        const calendarPickerContainerWrapper = this.pageProductCustom?.querySelector(
            '.calender-picker-wrap'
        ) as HTMLElement | null;

        if(!calendarPickerContainerWrapper){
            return;
        }

        const calendarPickerContainer = this.pageProductCustom?.querySelector(
            '.calendar-picker-container'
        ) as HTMLElement;

        const calendarPickerContainerRange = this.pageProductCustom?.querySelector(
            '.calendar-picker-container-range'
        ) as HTMLElement;

        let monthPeriod = calendarPickerContainer.dataset.maxMonthPeriod;
        let disabledDates = JSON.parse(calendarPickerContainerRange.getAttribute('data-rent-date') ?? '');

        const minDate = new Date();
        minDate.setHours(0, 0, 0, 0);
        const maxDate = new Date(
            minDate.getFullYear(),
            minDate.getMonth() + Number(monthPeriod ?? 1),
            minDate.getDate()
        );

        const fromInput = document.getElementById('from-datepicker') as HTMLInputElement;
        const rangeInput = document.getElementById('range-datepicker') as HTMLInputElement;
        const toInput = document.getElementById('to-datepicker') as HTMLInputElement;
        if (calendarPickerContainer) {
            new Pikaday({
                field: fromInput,
                bound: false,
                container: calendarPickerContainer,
                format: 'DD/MM/YYYY',
                numberOfMonths: 1,
                minDate: minDate,
                maxDate: maxDate,
                isRTL: true,
                i18n: {
                    previousMonth: 'חודש קודם',
                    nextMonth: 'חודש הבא',
                    months: [
                        'ינואר',
                        'פברואר',
                        'מרץ',
                        'אפריל',
                        'מאי',
                        'יוני',
                        'יולי',
                        'אוגוסט',
                        'ספטמבר',
                        'אוקטובר',
                        'נובמבר',
                        'דצמבר',
                    ],
                    weekdays: ['ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת'],
                    weekdaysShort: ['א', 'ב', 'ג', 'ד', 'ה', 'ו', 'ש'],
                },
                disableDayFn: function (date: Date) {
                    return disabledDates.some((disabledDate) => {
                        let formattedDateFrom =
                            disabledDate[0].substring(0, 4) +
                            '-' +
                            disabledDate[0].substring(4, 6) +
                            '-' +
                            disabledDate[0].substring(6, 8);
                        let formattedDateTo =
                            disabledDate[1].substring(0, 4) +
                            '-' +
                            disabledDate[1].substring(4, 6) +
                            '-' +
                            disabledDate[1].substring(6, 8);

                        let from = new Date(formattedDateFrom);
                        let to = new Date(formattedDateTo);

                        let formattedDate =
                            date.getFullYear() +
                            '-' +
                            (date.getMonth() + 1).toString().padStart(2, '0') +
                            '-' +
                            date.getDate().toString().padStart(2, '0');
                        let currentDate = new Date(formattedDate);

                        return currentDate >= from && currentDate <= to;
                    });
                },
                onSelect: function (selectedDate: Date) {
                    const periodDays = parseInt(calendarPickerContainer.getAttribute('period') || '1', 10);

                    const datesToSelect = [new Date(selectedDate)];
                    for (let i = 0; i < periodDays - 1; i++) {
                        const nextDate = new Date(selectedDate);
                        nextDate.setDate(nextDate.getDate() + i + 1);
                        if (nextDate <= maxDate) {
                            datesToSelect.push(nextDate);
                        }
                    }

                    fromInput.value = new Date(selectedDate).toLocaleDateString('he-IL', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                        year: 'numeric',
                    });

                    const fromDate = datesToSelect[0];
                    const toDate = datesToSelect[datesToSelect.length - 1];
                    calendarPickerContainer.setAttribute('data-from-date', fromDate.toISOString());
                    calendarPickerContainer.setAttribute('data-to-date', toDate.toISOString());
                 
                    const formattedDateFrom = fromDate.getFullYear() +
                      String(fromDate.getMonth() + 1).padStart(2, '0') +
                      String(fromDate.getDate()).padStart(2, '0');
                    const formattedDateTo = toDate.getFullYear() +
                      String(toDate.getMonth() + 1).padStart(2, '0') +
                      String(toDate.getDate()).padStart(2, '0');

                    calendarPickerContainerWrapper.setAttribute('data-from-date', formattedDateFrom.toString());
                    calendarPickerContainerWrapper.setAttribute('data-to-date', formattedDateTo.toString());
                    self.selectDatesInCalendar(datesToSelect, disabledDates);
                },
                onDraw: function () {
                    self.markDatesInCalendar(calendarPickerContainer);
                },
            });
        }
        if (calendarPickerContainerRange) {
            let firstSelectedDate: Date | null = null;
            let secondSelectedDate: Date | null = null;

            const rangePikaday = new Pikaday({
                field: rangeInput,
                bound: false,
                container: calendarPickerContainerRange,
                format: 'DD/MM/YYYY',
                numberOfMonths: 1,
                minDate: minDate,
                maxDate: maxDate,
                isRTL: true,
                i18n: {
                    previousMonth: 'חודש קודם',
                    nextMonth: 'חודש הבא',
                    months: [
                        'ינואר',
                        'פברואר',
                        'מרץ',
                        'אפריל',
                        'מאי',
                        'יוני',
                        'יולי',
                        'אוגוסט',
                        'ספטמבר',
                        'אוקטובר',
                        'נובמבר',
                        'דצמבר',
                    ],
                    weekdays: ['ראשון', 'שני', 'שלישי', 'רביעי', 'חמישי', 'שישי', 'שבת'],
                    weekdaysShort: ['א', 'ב', 'ג', 'ד', 'ה', 'ו', 'ש'],
                },
                disableDayFn: function (date: Date) {
                    if (!firstSelectedDate) {
                        return disabledDates.some((disabledDate) => {
                            let formattedDateFrom =
                                disabledDate[0].substring(0, 4) +
                                '-' +
                                disabledDate[0].substring(4, 6) +
                                '-' +
                                disabledDate[0].substring(6, 8);
                            let formattedDateTo =
                                disabledDate[1].substring(0, 4) +
                                '-' +
                                disabledDate[1].substring(4, 6) +
                                '-' +
                                disabledDate[1].substring(6, 8);

                            let from = new Date(formattedDateFrom);
                            let to = new Date(formattedDateTo);

                            let formattedDate =
                                date.getFullYear() +
                                '-' +
                                (date.getMonth() + 1).toString().padStart(2, '0') +
                                '-' +
                                date.getDate().toString().padStart(2, '0');
                            let currentDate = new Date(formattedDate);

                            return currentDate >= from && currentDate <= to;
                        });
                    } else {
                        const diff = Math.abs(date.getTime() - firstSelectedDate.getTime());
                        const daysDiff = diff / (1000 * 60 * 60 * 24);

                        return (
                            date < firstSelectedDate || 
                            disabledDates.some((disabledDate) => {
                                let formattedDateFrom =
                                    disabledDate[0].substring(0, 4) +
                                    '-' +
                                    disabledDate[0].substring(4, 6) +
                                    '-' +
                                    disabledDate[0].substring(6, 8);
                                let formattedDateTo =
                                    disabledDate[1].substring(0, 4) +
                                    '-' +
                                    disabledDate[1].substring(4, 6) +
                                    '-' +
                                    disabledDate[1].substring(6, 8);

                                let from = new Date(formattedDateFrom);
                                let to = new Date(formattedDateTo);

                                let formattedDate =
                                    date.getFullYear() +
                                    '-' +
                                    (date.getMonth() + 1).toString().padStart(2, '0') +
                                    '-' +
                                    date.getDate().toString().padStart(2, '0');
                                let currentDate = new Date(formattedDate);

                                return currentDate >= from && currentDate <= to;
                            })
                        );
                    }
                },
                onSelect: function (selectedDate: Date) {
                    if (firstSelectedDate && selectedDate.getTime() === firstSelectedDate.getTime()) {
                        firstSelectedDate = null;
                        secondSelectedDate = null;
                        rangeInput.value = '';
                        fromInput.value = '';
                        toInput.value = '';
                        calendarPickerContainerRange.setAttribute('data-from-date', '');
                        calendarPickerContainerRange.setAttribute('data-to-date', '');
                        rangePikaday.setDate(null);
                    } else if (!firstSelectedDate) {
                        firstSelectedDate = selectedDate;
                        fromInput.value = firstSelectedDate.toLocaleDateString('he-IL', {
                            weekday: 'long',
                            day: 'numeric',
                            month: 'long',
                            year: 'numeric',
                        });
                    } else {
                        secondSelectedDate = selectedDate;
                    }

                    if (firstSelectedDate && secondSelectedDate) {
                        let select = document.querySelector('.section-select.range-selected') as HTMLElement;
                        (document.querySelector('.section-select.range-none') as HTMLElement).style.display = 'none';
                        select.style.display = 'flex';
                        const price = parseInt(calendarPickerContainerRange.getAttribute('price') || '0', 10);
                        const productID = parseInt(calendarPickerContainerRange.getAttribute('productID') || '0', 10);
                        const start = Math.min(firstSelectedDate.getTime(), secondSelectedDate.getTime());
                        const end = Math.max(firstSelectedDate.getTime(), secondSelectedDate.getTime());

                        const rangeStart = new Date(start);
                        const rangeEnd = new Date(end);

                        const highlightedDates: Date[] = [];
                        for (let d = new Date(rangeStart); d <= rangeEnd; d.setDate(d.getDate() + 1)) {
                            highlightedDates.push(new Date(d));
                        }

                        let selectedDay = highlightedDates.length;
                        let discount4 = Number(calendarPickerContainerRange.getAttribute('days_discount_4'));
                        let discount8 = Number(calendarPickerContainerRange.getAttribute('days_discount_8'));
                        let discount = 0;
                        let totalPrice = 0;

                        if (selectedDay >= 4) discount = discount4;
                        if (selectedDay >= 8) discount = discount8;

                        if (discount) {
                            totalPrice = selectedDay * (price - discount);
                        } else {
                            totalPrice = selectedDay * price;
                        }

                        totalPrice = Number(totalPrice.toFixed(2));
                        const calender = document.querySelector('.calender-wrap') as HTMLElement | null;
                        const total = calender?.querySelector('.total .number') as HTMLElement | null;
                        const buttonCheckout = calender?.querySelector('.bay.button-checkout') as HTMLElement | null;
                        const buttonCart = calender?.querySelector('.cart.button-add-to-cart') as HTMLElement | null;

                        calendarPickerContainerRange.setAttribute('data-from-date', rangeStart.toISOString());
                        calendarPickerContainerRange.setAttribute('data-to-date', rangeEnd.toISOString());

                        const formattedDateFrom = rangeStart.getFullYear() +
                        String(rangeStart.getMonth() + 1).padStart(2, '0') +
                        String(rangeStart.getDate()).padStart(2, '0');
                        const formattedDateTo = rangeEnd.getFullYear() +
                        String(rangeEnd.getMonth() + 1).padStart(2, '0') +
                        String(rangeEnd.getDate()).padStart(2, '0');
                        calendarPickerContainerWrapper.setAttribute('data-from-date', formattedDateFrom.toString());
                        calendarPickerContainerWrapper.setAttribute('data-to-date', formattedDateTo.toString());

                        selectedDay
                        if (total && totalPrice) {
                            let discount_percent = discount ? ((discount / price) * 100).toFixed(1) : '0';

                            (select.querySelector('.select-count') as HTMLElement).textContent = selectedDay
                                ? selectedDay.toString()
                                : '';

                            (select.querySelector('.select-price') as HTMLElement).textContent = totalPrice
                                ? totalPrice.toString()
                                : '';

                            (select.querySelector('.select-count-discount-percent') as HTMLElement).textContent =
                                discount_percent;

                            (select.querySelector('.select-count-discount-price') as HTMLElement).textContent = discount
                                ? (price - discount).toString()
                                : price.toString();

                            total.textContent = totalPrice ? totalPrice.toString() : '';

                            if (buttonCheckout && productID && selectedDay) {
                                buttonCheckout.setAttribute(
                                    'href',
                                    `${omnis_ajax_object?.checkout_url}?add-to-cart=${productID}&quantity=${selectedDay}`
                                );
                            }

                            if (buttonCart && productID && selectedDay) {
                                buttonCart.setAttribute(
                                    'href',
                                    `${omnis_ajax_object?.cart_url}?add-to-cart=${productID}&quantity=${selectedDay}`
                                );
                            }
                        }
                        self.selectDatesInCalendar(highlightedDates, disabledDates);
                    } else {
                        (document.querySelector('.section-select.range-none') as HTMLElement).style.display = 'flex';
                        (document.querySelector('.section-select.range-selected') as HTMLElement).style.display =
                            'none';
                    }
                },
                onDraw: function () {
                    if (firstSelectedDate && secondSelectedDate) {
                        self.markDatesInCalendar(calendarPickerContainerRange);
                    }
                },
            });
        }
    }

    private selectDatesInCalendar(dates: Date[], disabledDates: Date[]) {
        const calendarDays = document.querySelectorAll('.pika-day');

        calendarDays.forEach((day) => {
            const td = day.closest('td') as HTMLElement | null;
            td?.classList.remove('is-selected');
            td?.removeAttribute('aria-selected');
        });

        const hasBlockedDate = dates.some((selectedDate) =>
            disabledDates.some((disabledDate) => {
                let formattedDateFrom =
                    disabledDate[0].substring(0, 4) +
                    '-' +
                    disabledDate[0].substring(4, 6) +
                    '-' +
                    disabledDate[0].substring(6, 8);
                let formattedDateTo =
                    disabledDate[1].substring(0, 4) +
                    '-' +
                    disabledDate[1].substring(4, 6) +
                    '-' +
                    disabledDate[1].substring(6, 8);

                let from = new Date(formattedDateFrom);
                let to = new Date(formattedDateTo);
                let formattedDate =
                    selectedDate.getFullYear() +
                    '-' +
                    (selectedDate.getMonth() + 1).toString().padStart(2, '0') +
                    '-' +
                    selectedDate.getDate().toString().padStart(2, '0');
                let currentDate = new Date(formattedDate);

                return currentDate >= from && currentDate <= to;
            })
        );

        const latestDate = new Date(Math.max(...dates.map((date) => date.getTime())));

        const formattedDate = latestDate.toLocaleDateString('he-IL', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        });

        const toDatepickerInput = document.getElementById('to-datepicker') as HTMLInputElement | null;

        const dayNum = this.pageProductCustom?.querySelector('.day-num') as HTMLElement | null;

        if (toDatepickerInput && latestDate.getDate()) {
            hasBlockedDate ? (toDatepickerInput.value = '') : (toDatepickerInput.value = formattedDate);
        }

        if (dayNum) {
            hasBlockedDate ? (dayNum.textContent = '') : (dayNum.textContent = dates.length.toString());
        }

        if (hasBlockedDate) {
            console.warn('One or more selected dates are blocked. No dates will be marked.');
            return;
        }

        calendarDays.forEach((day) => {
            const td = day.closest('td') as HTMLElement | null;

            const dayDate = new Date(
                parseInt(day.getAttribute('data-pika-year') || '', 10),
                parseInt(day.getAttribute('data-pika-month') || '', 10),
                parseInt(day.getAttribute('data-pika-day') || '', 10)
            );

            if (dates.some((selectedDate) => selectedDate.getTime() === dayDate.getTime())) {
                td?.classList.add('is-selected');
                td?.setAttribute('aria-selected', 'true');
            }
        });
    }

    private markDatesInCalendar(container: HTMLElement) {
        if (!container) {
            return;
        }

        const from = container?.getAttribute('data-from-date');
        const to = container?.getAttribute('data-to-date');

        const fromDate = from ? new Date(from) : null;
        const toDate = to ? new Date(to) : null;

        const calendarDays = document.querySelectorAll('.pika-day');

        calendarDays.forEach((day) => {
            let td = day.closest('td') as HTMLElement | null;
            td?.classList.remove('is-selected');
            td?.removeAttribute('aria-selected');

            // Get the current day date
            const dayDate = new Date(
                parseInt(day.getAttribute('data-pika-year') || '', 10),
                parseInt(day.getAttribute('data-pika-month') || '', 10),
                parseInt(day.getAttribute('data-pika-day') || '', 10)
            );

            // Check if the current day is within the selected range (from-to dates)
            if (fromDate && toDate) {
                if (dayDate >= fromDate && dayDate <= toDate) {
                    td?.classList.add('is-selected');
                    td?.setAttribute('aria-selected', 'true');
                }
            }
        });
    }

    private initSwiper(): void {
        new Swiper(this.swiperContainer as HTMLElement, {
            modules: [Navigation, Pagination],
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            loop: true,
            slidesPerView: 1,
        });
    }

    private initSwiperPerViewGap(): void {
        this.swiperContainerPerViewGap?.forEach((element) => {
            new Swiper(element as HTMLElement, {
                slidesPerView: 1.95,
                spaceBetween: 32,
                loop: true,
            });
        });
    }

    private handleSubmitOffer(event: Event): void {
        const self = this;
        event.preventDefault();

        const form = (event.target as HTMLElement).closest('form') as HTMLFormElement;
        if (!form) return;

        if (!$(form).parsley().isValid()) {
            console.log('No valid');
            return;
        }

        const formData = new FormData(form);
        formData.append('action', 'custom_offer');
        if (omnis_ajax_object?.nonce) {
            formData.append('security', omnis_ajax_object.nonce);
        }

        const submitBTN = form.querySelector("button[type='submit']") as HTMLButtonElement;
        const responseMessage = form.querySelector('#response-message') as HTMLElement;

        if (submitBTN) submitBTN.disabled = true;

        if (omnis_ajax_object?.ajaxurl) {
            axios
                .post(omnis_ajax_object.ajaxurl, formData)
                .then((response) => {
                    if (response.data.success) {
                        const input = form.querySelector('#offer-form-input') as HTMLInputElement;
                        const bid = document.querySelector('.single-product .min-offer .min-offer-bid') as HTMLElement;
                        bid.textContent = input.value.toString();
                        self.showBitModal(input.value);
                        input.setAttribute("data-parsley-min", (Number(input.value) + 1).toString());
                        
                        setTimeout(() => {
                            responseMessage.innerHTML = '';
                            input.value = '';
                        }, 4000);
                    } else {
                        const messageError = response.data.data.message || 'Unknown error occurred';
                        if (responseMessage) {
                            responseMessage.textContent = messageError;
                            responseMessage.style.display = 'block';
                            setTimeout(() => {
                                responseMessage.textContent = '';
                                responseMessage.style.display = 'none';
                            }, 4000);
                        }
                    }
                })
                .catch((error) => {
                    console.error('Error :', error);
                    alert('An error occurred while processing your request.');
                })
                .finally(() => {
                    if (submitBTN) submitBTN.disabled = false;
                });
        } else {
            console.error('ajaxUrl is undefined or invalid.');
            alert('An error occurred: Unable to send the request.');
        }
    }

    private closeBitModal(){
        const closeBtns = document.querySelectorAll(".single-product  .bid-modal-wrap .close-bid-js") as NodeListOf<HTMLElement> | null;
        if(!closeBtns){
            return;
        }

        closeBtns.forEach(btn => {
            btn.addEventListener("click", event => {
                const btn = event.currentTarget as HTMLElement;
                const parentWrapper = btn.closest(".bid-modal-wrap") as HTMLElement;
                parentWrapper.style.display = "none"
                const body = document.querySelector("body") as HTMLElement
                body.style.overflow = "auto";
            })
        })
    }
    private showBitModal(value: string | number){
        const parentWrapper = document.querySelector(".single-product  .bid-modal-wrap") as HTMLElement | null;
        if(!parentWrapper){
            return;
        }
        const body = document.querySelector("body") as HTMLElement
        body.style.overflow = "hidden";

        parentWrapper.style.display = "flex";
        parentWrapper.style.top = `${window.scrollY - 100}px`;
        const price = parentWrapper.querySelector(".bid-modal-price .price") as HTMLElement
        price.textContent = value.toString();

    }

    private makeReservation(){
        if(!this.pageProductCustom){
            return;
        }
        const reservationBTNS = document.querySelectorAll(".make-reservation-js") as NodeListOf<HTMLElement> | null;
        const calendarPickerContainerWrapper = this.pageProductCustom.querySelector('.calender-picker-wrap') as HTMLElement | null;
        
        if (!reservationBTNS || !calendarPickerContainerWrapper) return;
        
        reservationBTNS.forEach(btn => {
            btn.addEventListener("click", event => {
                event.preventDefault();
                const currentBtn = event.currentTarget as HTMLAnchorElement;
                const toCart = currentBtn.classList.contains("button-add-to-cart");
                const from = calendarPickerContainerWrapper.getAttribute('data-from-date') ?? "";
                const to = calendarPickerContainerWrapper.getAttribute('data-to-date') ?? "";
                const ID = calendarPickerContainerWrapper.getAttribute('data-product-id') ?? "";
                
                const formData = new FormData();
                formData.append('action', 'create_booking');
                formData.append('start_date', from);
                formData.append('end_date', to);
                formData.append('product_id', ID);

                if (omnis_ajax_object?.nonce) {
                    formData.append('security', omnis_ajax_object.nonce);
                }

                currentBtn.style.pointerEvents = "none";

                if (omnis_ajax_object?.ajaxurl) {
                    axios
                        .post(omnis_ajax_object.ajaxurl, formData)
                        .then((response) => {
                            if (response.data.success) {
                                if(toCart && omnis_ajax_object?.cart_url){
                                    window.location.href = omnis_ajax_object?.cart_url
                                }else if(omnis_ajax_object?.checkout_url){
                                    window.location.href = omnis_ajax_object?.checkout_url
                                }
                            } 
                        })
                        .catch((error) => {
                            console.error('Error :', error);
                            alert('An error occurred while processing your request.');
                        })
                        .finally(() => {
                            currentBtn.style.pointerEvents = "painted";
                        });
                } else {
                    console.error('ajaxUrl is undefined or invalid.');
                    alert('An error occurred: Unable to send the request.');
                }

            });
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    ProductCustom.getInstance();
});

export {};
