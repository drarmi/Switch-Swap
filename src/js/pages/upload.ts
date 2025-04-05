import axios from 'axios';
import List from 'list.js';
import $ from '../../../node_modules/jquery';
import * as Parsley from 'parsleyjs';
import 'jquery-ui/ui/widgets/sortable';

interface AjaxObject {
    ajaxurl: string;
    nonce: string;
    checkout_url: string;
    cart_url: string;
    date_set: object;
    discount_period: object;
    home_url: string;
    userLogin: boolean;
    userRole: string;
}

declare const omnis_ajax_object: AjaxObject | undefined;

class UplouadProduct {
    private static instance: UplouadProduct;

    private uploadBTN: HTMLElement | null;
    private closeBTN: HTMLElement | null;
    private navBTN: NodeListOf<HTMLButtonElement> | null;
    private galleryBTN: HTMLButtonElement | null;
    private cameraBTN: HTMLButtonElement | null;
    private modals: NodeListOf<Element> | null;
    private step: number | 0;
    private countSelect: number | 1;
    private currentStream: MediaStream | null = null;
    private currentStreamEditor: MediaStream | null = null;
    private cameraParentWrap: HTMLElement | null;
    private mediaGallery: HTMLElement | null;
    private captureBtn: HTMLElement | null;
    private canvas: HTMLCanvasElement | null;
    private snapshot: HTMLImageElement | null;
    private saveBtn: HTMLElement | null;
    private mainForm: HTMLFormElement | null;
    private imgEditor: HTMLElement | null;
    private closeImgEditor: HTMLElement | null;
    private deletedImgEditor: HTMLElement | null;
    private getImgEditor: HTMLElement | null;
    private makePhoto: HTMLElement | null;
    private subModalsRow: NodeListOf<Element> | null;
    private subModals: NodeListOf<Element> | null;
    private childCategories: NodeListOf<Element> | null;
    private inputCategory: NodeListOf<Element> | null;
    private subModalsBTN: NodeListOf<Element> | null;
    private subModalsBTNClose: NodeListOf<Element> | null;
    private parsleyInstance: any;

    private constructor() {
        this.uploadBTN = document.getElementById('upload-product') ?? null;
        this.closeBTN = document.querySelector('.close-upload-modal') ?? null;
        this.navBTN = document.querySelectorAll('.upload-nav-btn') ?? null;
        this.modals = document.querySelectorAll('.modal-step') ?? null;
        this.galleryBTN = document.querySelector('#open-gallery') ?? null;
        this.cameraBTN = document.querySelector('#open-camera') ?? null;
        this.cameraParentWrap = document.querySelector('.camera-wrap') as HTMLElement | null;
        this.mediaGallery = document.querySelector('.user-media-gallery') as HTMLElement | null;
        this.captureBtn = document.getElementById('captureBtn') as HTMLElement | null;
        this.canvas = document.getElementById('canvas') as HTMLCanvasElement | null;
        this.snapshot = document.getElementById('snapshot') as HTMLImageElement | null;
        this.saveBtn = document.getElementById('saveBtn') as HTMLElement | null;
        this.mainForm = document.querySelector('.main-upload-form') as HTMLFormElement | null;
        this.imgEditor = document.querySelector('.img-editor') ?? null;
        this.closeImgEditor = document.querySelector('.control-element-close') ?? null;
        this.deletedImgEditor = document.querySelector('.delete-photo') ?? null;
        this.getImgEditor = document.querySelector('.get-photo') ?? null;
        this.makePhoto = document.querySelector('.make-photo') ?? null;
        this.subModalsRow = document.querySelectorAll('.drop-down-body .row') ?? null;
        this.subModals = document.querySelectorAll('.sub-modal-drop-down') ?? null;
        this.childCategories = document.querySelectorAll('.sub-modal-drop-down .child-categories li') ?? null;
        this.inputCategory = document.querySelectorAll('.selected-filters') ?? null;
        this.subModalsBTN = document.querySelectorAll('.upload-nav-btn-sub') ?? null;
        this.subModalsBTNClose = document.querySelectorAll('.sub-modal-drop-down .close-upload-modal') ?? null;

        this.step = 0;
        this.countSelect = 1;

        if(!omnis_ajax_object?.userLogin || omnis_ajax_object?.userRole == 'Subscriber'){
            return;
        }

        this.showUploadModal = this.showUploadModal.bind(this);
        this.hideUploadModal = this.hideUploadModal.bind(this);

        this.initEvent();
        this.initList();
        this.initAlphabetSidebar();
        this.calculateDiscount();
        this.hideBuyNow();
        this.activeBtnStep3();
        this.initParsley();
        this.resetForm();
        this.showUploadModalSession();
        this.changeCountrySize();
        this.rollBackStep();
    }

    public static getInstance(): UplouadProduct {
        if (!this.instance) {
            this.instance = new this();
        }
        return this.instance;
    }

    private initEvent() {
        if (this.uploadBTN) {
            this.uploadBTN.addEventListener('click', this.showUploadModal.bind(this));
        }
        if (this.closeBTN) {
            this.closeBTN.addEventListener('click', this.hideUploadModal.bind(this));
        }
        if (this.navBTN) {
            this.navBTN.forEach((btn) => {
                if (btn.dataset.listener === 'true') return;
                btn.dataset.listener = 'true';
                btn.addEventListener('click', this.controlUploadModals.bind(this));
            });
        }
        if (this.galleryBTN) {
            this.galleryBTN.addEventListener('click', this.uploadImageFromStore.bind(this));
        }
        if (this.cameraBTN) {
            this.cameraBTN.addEventListener('click', this.showCamera.bind(this));
        }
        if (this.mediaGallery) {
            this.mediaGallery.querySelectorAll('div img').forEach((img) => {
                img.addEventListener('click', this.selectedFromList.bind(this));
            });
        }
        if (this.imgEditor) {
            this.imgEditor.addEventListener('click', this.editSelectedImag.bind(this));
        }
        if (this.closeImgEditor) {
            this.closeImgEditor.addEventListener('click', this.closeEditor.bind(this));
        }
        if (this.deletedImgEditor) {
            this.deletedImgEditor.addEventListener('click', this.deletedImgFromSelected.bind(this));
        }
        if (this.getImgEditor) {
            this.getImgEditor.addEventListener('click', this.changeImgFromSelected.bind(this));
        }
        if (this.makePhoto) {
            this.makePhoto.addEventListener('click', this.changeImgFromSelectedCamera.bind(this));
        }
        if (this.subModalsRow && this.subModals) {
            this.subModalsRow.forEach((element) => {
                element.addEventListener('click', this.subModalDropDownControl.bind(this));
            });
        }

        if (this.childCategories) {
            this.childCategories.forEach((element) => {
                element.addEventListener('click', this.childCategoriesControl.bind(this));
            });
        }
        if (this.subModalsBTN) {
            this.addEventListeners(this.subModalsBTN as NodeListOf<HTMLElement>);
        }
        if (this.subModalsBTNClose) {
            this.addEventListeners(this.subModalsBTNClose as NodeListOf<HTMLElement>);
        }

        if (this.inputCategory) {
            this.inputCategory.forEach((element) => {
                element.addEventListener('change', this.handleCategoryChange.bind(this));
            });
        }
        if (this.mainForm) {
            this.createProductSendFormData();
            this.controlUploadTextArea();
            this.pricesRadioControl();
            this.controlScroll();
            this.controlBidsModal();
        }
    }

    private initParsley() {
        const managementProductPage = document.querySelector(".page-template-product-management-product") as HTMLElement | null;
        if (!this.mainForm || managementProductPage) {
            return;
        }

        
        Parsley.addValidator('checkPastDate', {
            validateString: function (value) {
                const inputDate = new Date(
                    value
                        .split(/\/|\.|\-/)
                        .reverse()
                        .join('/')
                );
                const today = new Date();

                inputDate.setHours(0, 0, 0, 0);
                today.setHours(0, 0, 0, 0);

                return inputDate >= today;
            },
            messages: {
                en: 'The date cannot be in the past',
                he: 'תאריך לא יכול להיות עבר',
            },
        });

        this.parsleyInstance = $(this.mainForm)
            .parsley()
            .on('field:validate', function () {});
    }

    private controlScroll(): void {
        let scroll = document.querySelector('.bids-conditions-modal-content-rules') as HTMLElement;

        if (scroll) {
            scroll.addEventListener('scroll', () => {
                const scrollHeight = scroll.scrollHeight;
                const scrollTop = scroll.scrollTop;
                const clientHeight = scroll.clientHeight;
                if (scrollTop + clientHeight >= scrollHeight) {
                    let btnTransparent = document.querySelector(
                        '.bids-conditions-modal button.transparent'
                    ) as HTMLElement;
                    btnTransparent.classList.add('active');
                }
            });
        }
    }

    private controlBidsModal(): void {
        let parentWrap = document.querySelector('.bids-conditions-wrap') as HTMLElement | null;

        if (!parentWrap) {
            return;
        }

        let showBidsBtn = document.querySelector('.show-bids-condition button') as HTMLElement;
        let btnsClose = document.querySelectorAll(
            '.bids-conditions-modal .close-upload-modal-js'
        ) as NodeListOf<HTMLElement>;
        let modal1 = document.querySelector('.bids-conditions-modal.step1') as HTMLElement;
        let modal2 = document.querySelector('.bids-conditions-modal.step2') as HTMLElement;
        let nextBnts = document.querySelectorAll(
            '.bids-conditions-modal .bids-conditions-modal-footer button.next-bids'
        ) as NodeListOf<HTMLElement>;
        let btnTransparent = document.querySelector('.bids-conditions-modal button.transparent') as HTMLElement;

        const closeModalBit = () => {
            if (!parentWrap) {
                return;
            }
            parentWrap.style.display = 'none';
            modal1.style.display = 'block';
            modal2.style.display = 'none';

            let check = document.querySelector('#bids-conditions-rule') as HTMLInputElement;
            if (!check.checked) {
                btnTransparent.classList.remove('active');
            }
        };

        btnsClose.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                closeModalBit();
            });
        });

        showBidsBtn.addEventListener('click', (event) => {
            if (!parentWrap) {
                return;
            }
            event.preventDefault();
            parentWrap.style.display = 'block';
            modal1.style.display = 'block';
            modal2.style.display = 'none';
        });

        nextBnts.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                let currentButton = event.currentTarget as HTMLElement;
                let parent = currentButton.closest('.bids-conditions-modal') as HTMLElement;
                if (!parent) {
                    return;
                }

                if (parent.classList.contains('step1')) {
                    modal1.style.display = 'none';
                    modal2.style.display = 'block';
                    let scroll = document.querySelector('.bids-conditions-modal-content-rules') as HTMLElement;
                    if (scroll) {
                        scroll.scrollTop = 0;
                    }
                } else {
                    let nextBntActive = parent?.querySelector('.transparent') as HTMLElement;
                    if (nextBntActive?.classList.contains('active')) {
                        let check = document.querySelector('#bids-conditions-rule') as HTMLInputElement;
                        if (check) {
                            check.checked = true;
                            closeModalBit();
                        }
                    }
                }
            });
        });
    }

    private initList(): void {
        const options = {
            valueNames: ['name'],
        };

        const brandList = new List('brand-list', options);

        const searchInput = document.querySelector<HTMLInputElement>('.search');
        if (searchInput) {
            searchInput.addEventListener('input', (event) => {
                const value = (event.target as HTMLInputElement).value.trim();
                if (value.length >= 1) {
                    brandList.search(value);
                } else {
                    brandList.search('');
                }
            });
        }
    }

    private pricesRadioControl(): void {
        let priceOptionRadios = document.querySelectorAll(
            ".prices-body .price-option.custom-radio input[type='checkbox']"
        ) as NodeListOf<HTMLInputElement>;

        priceOptionRadios.forEach((radio) => {
            radio.addEventListener('change', (event) => {
                let target = event.currentTarget as HTMLInputElement;
                let parent = target.closest('.row') as HTMLElement;

                if (parent && target.checked) {
                    parent.setAttribute('open', 'true');
                } else {
                    parent.removeAttribute('open');
                }
            });
        });

        let aditionalOptionRadios = document.querySelectorAll(
            ".prices-body .aditional-option input[type='checkbox']"
        ) as NodeListOf<HTMLInputElement>;

        aditionalOptionRadios.forEach((radio) => {
            radio.addEventListener('change', (event) => {
                let target = event.currentTarget as HTMLInputElement;
                let parent = target.closest('.aditional-option.bids-buy-now') as HTMLElement;

                if (parent && target.checked) {
                    parent.setAttribute('open', 'true');
                } else {
                    parent.removeAttribute('open');
                }
            });
        });
    }

    private calculateDiscount() {
        let inputsDiscount = document.querySelectorAll('.row-discount-input-js') as NodeListOf<HTMLInputElement> | null;

        if (!inputsDiscount) {
            return;
        }

        inputsDiscount.forEach((input) => {
            input.addEventListener('keyup', (event) => {
                let currentInput = event.currentTarget as HTMLInputElement;
                let parentModal = currentInput.closest('.row') as HTMLElement;
                let parentRow = currentInput.closest('.row-discount-parent-js') as HTMLElement;
                let price = parentModal.querySelector('.row-main-price-js') as HTMLInputElement;
                let result = parentRow.querySelector('.row-discount-result-js') as HTMLElement;

                if (price.value.length > 0) {
                    let priceWithoutDiscount = Number(price.value);

                    let priceDiscountPercent = currentInput.value.length > 0 ? Number(currentInput.value) : 0;
                    if (priceDiscountPercent < 100) {
                        let discountValue = (priceWithoutDiscount / 100) * priceDiscountPercent;
                        result.textContent = (priceWithoutDiscount - discountValue).toFixed(2).toString();
                    }
                }
            });
        });

        let inputsPrice = document.querySelectorAll('.row-main-price-js') as NodeListOf<HTMLInputElement> | null;

        if (!inputsPrice) {
            return;
        }

        inputsPrice.forEach((input) => {
            input.addEventListener('keyup', (event) => {
                let currentInput = event.currentTarget as HTMLInputElement;
                let parentModal = currentInput.closest('.row') as HTMLElement;
                let rowDiscount = parentModal.querySelectorAll(
                    '.row-discount-parent-js'
                ) as NodeListOf<HTMLElement> | null;

                if (rowDiscount) {
                    rowDiscount.forEach((row) => {
                        let rowInput = row.querySelector('.row-discount-input-js') as HTMLInputElement;
                        let rowDiscount = row.querySelector('.row-discount-result-js') as HTMLInputElement;

                        if (rowInput.value.length > 0 && currentInput.value.length > 0) {
                            let priceWithoutDiscount = Number(currentInput.value);

                            let priceDiscountPercent = rowInput.value.length > 0 ? Number(rowInput.value) : 0;
                            if (priceDiscountPercent < 100) {
                                let discountValue = (priceWithoutDiscount / 100) * priceDiscountPercent;
                                rowDiscount.textContent = (priceWithoutDiscount - discountValue).toFixed(2).toString();
                            }
                        }
                    });
                }
            });
        });
    }

    private hideBuyNow() {
        const alphabetItems = document.querySelector(
            '.price-option.custom-radio input[name="sale-option"]'
        ) as HTMLInputElement | null;
        const buyNowSection = document.querySelector('.aditional-option.bids-buy-now') as HTMLElement | null;

        if (!alphabetItems || !buyNowSection) {
            return;
        }

        alphabetItems.addEventListener('change', (event) => {
            let currentCheck = event.currentTarget as HTMLInputElement;

            if (currentCheck.checked) {
                buyNowSection.style.display = 'none';
            } else {
                buyNowSection.style.display = 'block';
            }
        });
    }

    private initAlphabetSidebar(): void {
        const alphabetItems = document.querySelectorAll<HTMLElement>('.alphabet-item');
        const brandListItems = document.querySelectorAll<HTMLElement>('.child-categories li');

        alphabetItems.forEach((item) => {
            item.addEventListener('click', () => {
                const letter = item.getAttribute('data-letter');
                if (letter) {
                    for (const brandItem of brandListItems) {
                        const brandName = brandItem.textContent?.trim() || '';
                        if (brandName.startsWith(letter)) {
                            brandItem.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            break;
                        }
                    }
                }
            });
        });
    }

    private handleCategoryChange(event: Event): void {
        event.preventDefault();
        const input = event.currentTarget as HTMLInputElement | null;

        const name = input?.getAttribute('name');
        const categoryText = document.querySelector(`.row[data-type='${name}'] .text`);

        if (input && categoryText) {
            categoryText.textContent = input.getAttribute('data-title') || 'קטגוריה';
        }
    }

    private hideSubModal(event: Event): void {
        event.preventDefault();
        const blt = event.currentTarget as HTMLElement | null;
        if (blt) {
            let modal = blt.closest('.sub-modal-drop-down') as HTMLElement;
            modal.style.display = 'none';
        }
    }

    private addEventListeners(elements: NodeListOf<HTMLElement> | null): void {
        if (elements) {
            elements.forEach((element) => {
                element.addEventListener('click', this.hideSubModal);
            });
        }
    }

    public changeCountrySize() {
        let switchers = document.querySelectorAll('.type-switcher .type-switcher-item') as NodeListOf<HTMLElement> || null;
        
        if(!switchers) return;

        switchers.forEach((element) => {
            element.addEventListener('click', (event) => {
                const currentElement = event.currentTarget as HTMLElement;
                const parent = currentElement.closest('.modal-drop-down-content') as HTMLElement;
                const type = currentElement.getAttribute('data-type');
                currentElement.classList.add('active');
                let wrapper = parent.querySelector(".sizes-wrap");
                wrapper?.classList.remove('eu');
                wrapper?.classList.remove('us');
                wrapper?.classList.add(type?.toString() || '');
                
                switchers.forEach((element) => {
                    if(element.dataset.type != type){
                        element.classList.remove('active');
                    }else{
                        element.classList.add('active');
                    }
                });

            });
        });
    }

    public rollBackStep() {
        const self = this;
        if(!self.mainForm){
            return;
        }

        let btns = self.mainForm.querySelectorAll('.roll-back-js') as NodeListOf<HTMLElement>;

        btns.forEach((element) => {
            element.addEventListener('click', (event) => {
                event.preventDefault();
                
                let currentElement = event.currentTarget as HTMLElement;
                let step = currentElement.getAttribute('data-step');

                if(!self.modals || !self.mainForm){
                    return;
                }

                self.modals.forEach((modal) => {
                    const modalElement = modal as HTMLElement;
                    modalElement.style.display = 'none';
                });

                let openModal = self.mainForm.querySelector(`.modal.modal-step[data-step="${step}"]`) as HTMLElement || null;

                if(openModal){
                    openModal.style.display = 'block';
                }

                self.navBTN?.forEach((btn) => {
                    btn.setAttribute('data-step', step || '1');
                });    
                self.mainForm.setAttribute('data-step', step || '1');
            });
        });
    }

    public childCategoriesControl(event: Event) {
        let currentLI = event.currentTarget as HTMLElement;

        if (currentLI && currentLI.dataset.type == 'sizes') {
            let sizeValue = document.querySelector('#selected-form-size') as HTMLInputElement;
            let sizeValueName = document.querySelector('#selected-form-size-name') as HTMLInputElement;
            let parentSizeTitle = document.querySelector(
                ".drop-down-body .row[data-type='sizes'] .size-title-span"
            ) as HTMLElement;
            
            let defText = 'מידה';
            let ID = currentLI.dataset.id;
            let name = currentLI.dataset.name;

            if (!sizeValue || !parentSizeTitle || !sizeValueName) return;

            let selectedName: string = defText;

            if (ID && name) {
                sizeValue.value = ID;
                sizeValueName.value = name;
                selectedName = name || defText;
                setTimeout(() => {
                    parentSizeTitle.textContent = selectedName;

                }, 100);
            }
        } else if (currentLI && currentLI.dataset.type == 'colors') {
            let colorValue = document.querySelector('#selected-form-color') as HTMLInputElement;
            let colorValueName = document.querySelector('#selected-form-color-name') as HTMLInputElement;
            let colorValueHex = document.querySelector('#selected-form-color-hex') as HTMLInputElement;
            let parentColorTitle = document.querySelector(
                ".drop-down-body .row[data-type='colors'] .color-title-span"
            ) as HTMLElement;
            let parentColorHex = document.querySelector(
                ".drop-down-body .row[data-type='colors'] .color-hex"
            ) as HTMLElement;
            let parentMultiSVG = document.querySelector(
                ".drop-down-body .row[data-type='colors'] .color-hex-multi"
            ) as HTMLElement;
            let defText = 'צבע';
            let ID = currentLI.dataset.id;
            let name = currentLI.dataset.name;
            let hex = currentLI.dataset.hex;
            let slag = currentLI.dataset.slag ? currentLI.dataset.slag : '';

            if (!colorValue || !parentColorTitle || !colorValueName || !colorValueHex) return;

            let selectedName: string = defText;

            if (ID && name && hex) {
                colorValue.value = ID;
                colorValueName.value = name;
                selectedName = name || defText;
                colorValueHex.value = hex;
                setTimeout(() => {
                    parentColorTitle.textContent = selectedName;
                    if (slag == 'multi') {
                        parentMultiSVG.style.display = 'block';
                        parentColorHex.style.width = '0';
                        parentColorHex.style.marginLeft = '0';
                        parentColorHex.style.border = '0';
                    } else {
                        parentColorHex.style.backgroundColor = hex || '#fff';
                        parentColorHex.style.width = '20px';
                        parentColorHex.style.marginLeft = '10px';
                        parentColorHex.style.border = '1px solid rgba(0,0,0,.1)';
                        parentMultiSVG.style.display = 'none';
                    }
                }, 100);
            } else {
                setTimeout(() => {
                    if (slag == 'multi') {
                        parentMultiSVG.style.display = 'none';
                    } else {
                        parentColorHex.style.width = '0';
                        parentColorHex.style.marginLeft = '0';
                        parentColorHex.style.border = '0';
                    }
                }, 100);
            }
        } else if (currentLI && currentLI.dataset.type == 'category') {
            let parentCategoryTitle = document.querySelector(
                ".drop-down-body .row[data-type='category'] .category-title-span"
            ) as HTMLElement;
            let categoryValue = document.querySelector('#selected-form-category') as HTMLInputElement;
            let categoryName = document.querySelector('#selected-form-category-name') as HTMLInputElement;
            let sizeValue = document.querySelector('#selected-form-size') as HTMLInputElement;
            let sizeValueName = document.querySelector('#selected-form-size-name') as HTMLInputElement;
            let parentSizeTitle = document.querySelector(
                ".drop-down-body .row[data-type='sizes'] .size-title-span"
            ) as HTMLElement;
            let defText = 'קטגוריה';
            let defTextSize = 'מידה';
            let ID = currentLI.dataset.id;
            let name = currentLI.dataset.name;
            let slug = currentLI.dataset.slug;
            
            let ulList = document.querySelectorAll(
                ".sub-modal-drop-down-inner ul.size-group-js"
            ) as NodeListOf<HTMLElement>;


            if (!categoryValue || !parentCategoryTitle || !categoryName) return;

            let selectedName: string = defText;

            if (ID && name) {
                categoryValue.value = ID;
                categoryName.value = name;
                selectedName = name || defText;
                setTimeout(() => {
                    parentCategoryTitle.textContent = selectedName;

                    sizeValue.value = "";
                    sizeValueName.value = "";
                    parentSizeTitle.textContent = defTextSize;

                    ulList.forEach((ul) => {
                        if(ul.dataset.slug == slug){
                            ul.style.display = 'block';
                        }else{
                            ul.style.display = 'none';
                        }
                        
                    });
                }, 100);
            }
        } else if (currentLI && currentLI.dataset.type == 'brands') {
            let brandsNameValue = document.querySelector('#selected-form-brands-name') as HTMLInputElement;
            let name = currentLI.dataset.name;

            if (brandsNameValue && name) {
                brandsNameValue.value = name;
            }
        }

        let row = event.currentTarget as HTMLElement | null;

        if (this.childCategories && row && this.inputCategory && row.getAttribute('class') !== 'letter-group') {
            let rowType = row?.getAttribute('data-type');

            this.childCategories.forEach((element) => {
                let li = element as HTMLElement;
                li.classList.remove('active');
            });
            row.classList.add('active');

            this.inputCategory.forEach((element) => {
                let input = element as HTMLInputElement;
                if (row && rowType === input.getAttribute('name')) {
                    input.value = row.getAttribute('data-child-id')?.toString() || '';
                    input.setAttribute('data-title', row.textContent?.toString() || '');

                    const changeEvent = new Event('change', { bubbles: true });
                    input.dispatchEvent(changeEvent);
                }
            });
        }
    }

    public subModalDropDownControl(event: Event) {
        event.preventDefault();

        let dropdownSelect = event.currentTarget as HTMLElement | null;
        if (dropdownSelect && this.subModals) {
            let selectType = dropdownSelect.getAttribute('data-type');

            this.subModals.forEach((modal) => {
                let modalPopup = modal as HTMLElement;
                if (modal.getAttribute('data-type') === selectType) {
                    modalPopup.style.display = 'flex';
                } else {
                    modalPopup.style.display = 'none';
                }
            });
        }
    }

    public changeImgFromSelectedCamera(event: Event) {
        event.preventDefault();
        this.showCameraEditor();
    }

    public showCameraEditor() {
        const self = this;
        const controlElement = document.querySelector('.control-element') as HTMLButtonElement | null;
        const cameraParentWrap = document.querySelector('.camera-wrap-editor') as HTMLElement | null;
        const captureBtn = document.getElementById('captureBtn-editor') as HTMLElement | null;
        const canvas = document.getElementById('canvas-editor') as HTMLCanvasElement | null;
        const snapshot = document.getElementById('snapshot-editor') as HTMLImageElement | null;
        const saveBtn = document.getElementById('saveBtn-editor') as HTMLElement | null;

        if (controlElement && cameraParentWrap && captureBtn && canvas && snapshot && saveBtn) {
            if (cameraParentWrap.style.display === 'none') {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices
                        .getUserMedia({ video: true })
                        .then((stream) => {
                            const video = document.getElementById('video-editor') as HTMLVideoElement | null;
                            if (video && self.cameraParentWrap && self.mediaGallery) {
                                self.currentStreamEditor = stream;
                                cameraParentWrap.style.display = 'block';
                                controlElement.style.display = 'none';
                                video.srcObject = stream;
                            }
                        })
                        .catch((err) => {
                            console.error('Error accessing the camera:', err);
                            alert('Error accessing the camera');
                        });
                } else {
                    alert('Error accessing the camera');
                }

                const context = canvas.getContext('2d');
                captureBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    const video = document.getElementById('video-editor') as HTMLVideoElement | null;

                    if (video && context) {
                        if (video.videoWidth && video.videoHeight && canvas && snapshot && saveBtn) {
                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            context.drawImage(video, 0, 0, canvas.width, canvas.height);
                            const imageData = canvas.toDataURL('image/png');
                            snapshot.src = imageData;
                            snapshot.style.display = 'block';
                            saveBtn.style.display = 'inline';
                        }
                    }
                });

                saveBtn.addEventListener('click', self.savePhotoEditor.bind(self));
            } else {
                if (self.currentStreamEditor) {
                    const tracks = self.currentStreamEditor.getTracks();
                    tracks.forEach((track) => track.stop());
                    self.currentStreamEditor = null;
                }
                cameraParentWrap.style.display = 'none';
                snapshot.style.display = 'none';
                saveBtn.style.display = 'none';
                controlElement.style.display = 'block';
            }
        }
    }

    public savePhotoEditor(event: Event) {
        event.preventDefault();
        const self = this;
        const controlElement = document.querySelector('.control-element') as HTMLElement | null;
        const cameraParentWrap = document.querySelector('.camera-wrap-editor') as HTMLElement | null;
        const canvas = document.getElementById('canvas-editor') as HTMLCanvasElement | null;

        if (canvas) {
            const imageData = canvas.toDataURL('image/png');

            const formData = new FormData();
            formData.append('action', 'handle_save_snapshot');
            formData.append('security', omnis_ajax_object?.nonce || '');
            formData.append('image', imageData);
            if (omnis_ajax_object?.ajaxurl) {
                axios
                    .post(omnis_ajax_object.ajaxurl, formData)
                    .then((response) => {
                        let selectedIMG = document.querySelector('.selected-img .img-wrap img') as HTMLElement | null;
                        let paginationImgs = document.querySelectorAll(
                            '.pagination-list img'
                        ) as NodeListOf<HTMLElement> | null;

                        if (selectedIMG && paginationImgs && response.data.success && response.data.data.url) {
                            let imgID = selectedIMG.getAttribute('data-id');
                            selectedIMG.setAttribute('data-id', response.data.data.id);
                            selectedIMG.setAttribute('src', response.data.data.url);
                            paginationImgs.forEach((paginationImg) => {
                                if (paginationImg && paginationImg.getAttribute('data-id') == imgID) {
                                    paginationImg.setAttribute('src', response.data.data.url);
                                    paginationImg.setAttribute('data-id', response.data.data.id);
                                }
                            });

                            const controlWrap = document.querySelector('.control-element-wrap') as HTMLElement | null;
                            if (controlWrap && controlElement && cameraParentWrap) {
                                controlWrap.classList.remove('active');
                                controlElement.style.display = 'block';
                                cameraParentWrap.style.display = 'none';
                            }

                            if (self.currentStreamEditor) {
                                const tracks = self.currentStreamEditor.getTracks();
                                tracks.forEach((track) => track.stop());
                                self.currentStream = null;
                            }
                        } else {
                            console.error('Error saving image:', response.data.message);
                        }
                    })
                    .catch((error) => {
                        console.error('Error saving image:', error);
                    });
            }
        }
    }

    public changeImgFromSelected(event: Event) {
        event.preventDefault();

        const selectImages = document.querySelector('#select-images-one') as HTMLButtonElement | null;

        if (selectImages) {
            selectImages.click();
            this.saveImageDevice();
        }
    }

    public saveImageDevice() {
        const self = this;
        const select = document.querySelector('#select-images-one') as HTMLInputElement | null;

        select?.addEventListener('change', (event) => {
            const target = event.currentTarget as HTMLInputElement | null;

            if (target?.files) {
                const files = target.files;

                const formData = new FormData();
                formData.append('action', 'saveImages');
                formData.append('security', omnis_ajax_object?.nonce || '');
                Array.from(files).forEach((file) => {
                    formData.append('files[]', file);
                });

                if (omnis_ajax_object?.ajaxurl) {
                    axios
                        .post(omnis_ajax_object.ajaxurl, formData)
                        .then((response) => {
                            let selectedIMG = document.querySelector(
                                '.selected-img .img-wrap img'
                            ) as HTMLElement | null;
                            let paginationImgs = document.querySelectorAll(
                                '.pagination-list img'
                            ) as NodeListOf<HTMLElement> | null;

                            if (
                                paginationImgs &&
                                selectedIMG &&
                                response.data.success &&
                                response.data.data.urls &&
                                self.mediaGallery
                            ) {
                                let imgID = selectedIMG.getAttribute('data-id');

                                response.data.data.urls.forEach((img) => {
                                    if (selectedIMG && paginationImgs) {
                                        selectedIMG.setAttribute('data-id', img.id);
                                        selectedIMG.setAttribute('src', img.url);

                                        paginationImgs.forEach((paginationImg) => {
                                            if (paginationImg && paginationImg.getAttribute('data-id') == imgID) {
                                                paginationImg.setAttribute('src', img.url);
                                                paginationImg.setAttribute('data-id', img.id);
                                            }
                                        });
                                    }

                                    const controlWrap = document.querySelector(
                                        '.control-element-wrap'
                                    ) as HTMLElement | null;
                                    if (controlWrap) controlWrap.classList.remove('active');
                                });
                            } else {
                                let error = response.data.message ? response.data.message : response.data.data;
                                console.error('Error:', error);
                            }
                        })
                        .catch((error) => {
                            console.error('Error:', error);
                        });
                } else {
                    console.error('ajaxUrl is undefined or invalid.');
                    alert('An error occurred: Unable to send the request.');
                }
            } else {
                console.warn('No files are selected');
            }
        });
    }

    public deletedImgFromSelected(event: Event) {
        event.preventDefault();

        const controlWrap = document.querySelector('.control-element-wrap') as HTMLElement | null;

        let selectedIMG = document.querySelector('.selected-img .img-wrap img') as HTMLElement | null;
        let paginationListImgs = document.querySelectorAll('.pagination-list img') as NodeListOf<HTMLElement> | null;
        let selectedIMGInput = document.querySelectorAll('.selectedIMG') as NodeListOf<HTMLInputElement> | null;

        if (selectedIMG && paginationListImgs && selectedIMGInput) {
            let imgID = selectedIMG.getAttribute('data-id');
            let selectedNum = 0;

            paginationListImgs.forEach((img) => {
                let imgElement = img as HTMLElement;
                if (imgElement.getAttribute('data-id') === imgID) {
                    let parentElement = imgElement.closest('div') as HTMLElement | null;
                    if (parentElement) {
                        selectedNum = parentElement.querySelector('.num')?.textContent
                            ? Number(parentElement.querySelector('.num')?.textContent)
                            : 0;
                        parentElement.remove();
                    }
                }
            });

            document.querySelectorAll('.pagination-list.ui-sortable .ui-sortable-handle .num').forEach((num) => {
                let number = num as HTMLElement | null;
                if (number && Number(number.textContent) > selectedNum && selectedNum != 0) {
                    number.textContent = (Number(number.textContent) - 1).toString();
                }
            });

            document.querySelectorAll('.pagination-list.ui-sortable .ui-sortable-handle .num').forEach((num) => {
                let number = num as HTMLElement | null;
                if (number && Number(number.textContent) == 1) {
                    let parentElement = number.closest('.ui-sortable-handle') as HTMLElement | null;
                    if (parentElement && selectedIMG) {
                        let img = parentElement.querySelector('img') as HTMLElement | null;
                        if (img) {
                            let src = img.getAttribute('src');
                            selectedIMG.setAttribute('src', src || '');
                            let id = img.getAttribute('data-id');
                            selectedIMG.setAttribute('data-id', id || '');
                        }
                    }
                }
            });

            selectedIMGInput.forEach((input) => {
                if (input.value === imgID) {
                    input.remove();
                }
            });

            if (controlWrap) controlWrap.classList.remove('active');
        }
    }

    public closeEditor(event: Event) {
        const controlWrap = document.querySelector('.control-element-wrap') as HTMLElement | null;
        if (controlWrap) controlWrap.classList.remove('active');
    }

    public editSelectedImag(event: Event) {
        event.preventDefault();
        const self = this;
        const target = event.target as HTMLElement | null;
        const parentMainIMG = target?.closest('.selected-img') as HTMLElement | null;
        const controlWrap = document.querySelector('.control-element-wrap') as HTMLElement | null;
        let mainImagID = parentMainIMG?.querySelector('.img-wrap img')?.getAttribute('data-id');

        if (controlWrap) controlWrap.classList.add('active');
    }

    public hideUploadModal() {
        let body = document.querySelector('body') as HTMLElement | null;
        let modal = body?.querySelector('.main-form-wrapper') as HTMLElement | null;

        if (body && modal && this.modals) {
            modal.classList.remove('active');
            modal.style.display = 'none';
            body.classList.remove('active');
            this.step = 0;

            if (this.navBTN) {
                this.navBTN.forEach((btn) => {
                    btn.setAttribute('data-step', '1');
                });
            }
            if (this.mainForm) this.mainForm.setAttribute('data-step', '1');

            this.modals.forEach((modal) => {
                const modalElement = modal as HTMLElement;
                modalElement.style.display = 'none';
            });
        }
    }

    public showUploadModal(event: Event | null) {
        if (event){
            event.preventDefault();
        }
       
        let body = document.querySelector('body') as HTMLElement | null;
        let modal = body?.querySelector('.main-form-wrapper') as HTMLElement | null;

        if (body && modal && this.modals) {
            modal.classList.add('active');
            modal.style.display = 'flex';
            body.classList.add('active');

            this.modals.forEach((modal) => {
                const modalElement = modal as HTMLElement;
                if (modalElement.dataset['step'] == '1') {
                    modalElement.style.display = 'block';
                }
            });
        }
    }

    public uploadImageFromStore(event: Event) {
        const self = this;
        event.preventDefault();
        const target = event.currentTarget as HTMLButtonElement | null;
        const parent = target?.closest('.modal-step') as HTMLButtonElement | null;
        const selectImages = parent?.querySelector('#select-images') as HTMLButtonElement | null;

        if (selectImages) {
            selectImages.click();
            self.saveImageFromDevice();
        }
    }

    public addFormPreload(element: HTMLElement | null, zIndex: string) {
        if (!element) {
            return;
        }

        let spinnerParent = document.createElement('div');
        spinnerParent.classList.add('spinner-form-parent');
        spinnerParent.style.position = 'absolute';
        spinnerParent.style.width = '100%';
        spinnerParent.style.height = '100%';
        spinnerParent.style.display = 'flex';
        spinnerParent.style.justifyContent = 'center';
        spinnerParent.style.alignItems = 'center';
        spinnerParent.style.top = '0';
        spinnerParent.style.zIndex = zIndex;
        spinnerParent.style.background = '#00000059';

        spinnerParent.innerHTML = `<svg width="24" height="24" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <style>.spinner_P7sC{transform-origin:center;animation:spinner_svv2 .75s infinite linear}@keyframes spinner_svv2{100%{transform:rotate(360deg)}}</style>
            <path d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" class="spinner_P7sC"/>
        </svg>`;

        element.append(spinnerParent);
    }

    public removeFormPreload(element: HTMLElement | null) {
        if (!element) {
            return;
        }

        element.querySelector('.spinner-form-parent')?.remove();
    }

    public saveImageFromDevice() {
        const self = this;

        const select = document.querySelector('#select-images') as HTMLInputElement | null;
        const mediaModal = document.querySelector('#media-modal') as HTMLElement | null;

        if (!select) return;

        if (select.dataset.listener === 'true') return;

        select.dataset.listener = 'true';

        select.addEventListener('change', (event) => {
            const target = event.currentTarget as HTMLInputElement;

            if (!target?.files) {
                console.warn('Not selected');
                return;
            }

            const files = target.files;

            const formData = new FormData();
            formData.append('action', 'saveImages');
            formData.append('security', omnis_ajax_object?.nonce || '');
            Array.from(files).forEach((file) => {
                formData.append('files[]', file);
            });

            this.addFormPreload(mediaModal, '10');

            if (omnis_ajax_object?.ajaxurl) {
                axios
                    .post(omnis_ajax_object.ajaxurl, formData)
                    .then((response) => {
                        if (response.data.success && response.data.data.urls && self.mediaGallery) {
                            response.data.data.urls.forEach((img) => {
                                let imageContainer = document.createElement('img');
                                imageContainer.setAttribute('src', img.url);
                                imageContainer.setAttribute('data-id', img.id);
                                imageContainer.addEventListener('click', self.selectedFromList.bind(self));

                                let divContainer = document.createElement('div');
                                const numDiv = document.createElement('div');
                                numDiv.setAttribute('class', 'num');
                                divContainer.appendChild(numDiv);
                                divContainer.appendChild(imageContainer);

                                if (self.mediaGallery)
                                    self.mediaGallery.insertBefore(divContainer, self.mediaGallery.firstChild);
                            });
                        } else {
                            let error = response.data.message ? response.data.message : response.data.data;
                            console.error('Error:', error);
                        }
                    })
                    .catch((error) => {
                        console.error('Error:', error);
                    })
                    .finally(() => {
                        this.removeFormPreload(mediaModal);
                        select.value = '';
                    });
            } else {
                this.removeFormPreload(mediaModal);
                console.error('ajaxUrl is undefined or invalid.');
                alert('An error occurred: Unable to send the request.');
            }
        });
    }

    public controlUploadModals(event: Event) {
        const self = this;
        event.preventDefault();

        const target = event.currentTarget as HTMLButtonElement | null;
        if (target && self.modals) {
            let stepCurrent = target.dataset['step'] ? Number(target.dataset['step']) : 1;

            if (stepCurrent == 4) {
                let isValid = this.checkRequiredFields();
                if (!isValid) {
                    return;
                }
            }

            if (stepCurrent == 5) {
                this.showBids();
                if (this.controlFormDataFinalStep()) {
                    let nextModalBtn = document.querySelector(
                        '.upload-nav-btn.upload-nav-btn-black[data-step="5"]'
                    ) as HTMLElement;
                    if (nextModalBtn) nextModalBtn.click();
                } else {
                    return;
                }
            }

            if (stepCurrent == 2 && self.countSelect == 1) {
                return;
            }

            stepCurrent += 1;

            if (stepCurrent == 3) {
                self.photoSort();
            }

            self.modals.forEach((modal) => {
                const modalElement = modal as HTMLElement;
                let modalStep = modalElement.dataset['step'] ?? 1;

                if (modalStep == stepCurrent && self.navBTN) {
                    modalElement.style.display = 'block';
                    self.closeBTN = modal.querySelector('.close-upload-modal') ?? null;
                    if (self.closeBTN) {
                        self.closeBTN.addEventListener('click', self.hideUploadModal.bind(self));
                    }
                    self.navBTN.forEach((btn) => {
                        btn.setAttribute('data-step', stepCurrent.toString());
                    });

                    if (self.mainForm) self.mainForm.setAttribute('data-step', stepCurrent.toString());

                    self.step = stepCurrent;

                    if (modalStep == 6) {
                        this.completeFinalModal();
                    }
                } else {
                    modalElement.style.display = 'none';
                }
            });
        }
    }

    public controlFormDataFinalStep(): boolean {
        if (!this.mainForm) {
            return false;
        }

        let finalData = new FormData(this.mainForm);
        let check = document.querySelector('#bids-conditions-rule') as HTMLInputElement | null;

        if (!finalData.get('renting-option') && !finalData.get('sale-option') && !finalData.get('bids-option')) {
            return false;
        }

        if (!this.parsleyInstance.isValid()) {
            return false;
        }

        if (!finalData.get('condition')) {
            return false;
        }

        if (!finalData.get('renting-option') && !finalData.get('sale-option') && !finalData.get('bids-option')) {
            return false;
        }

        if (finalData.get('renting-option') && !(finalData.get('renting_price')?.toString().trim().length ?? 0)) {
            return false;
        }

        if (finalData.get('sale-option') && !(finalData.get('price-bay-only')?.toString().trim().length ?? 0)) {
            return false;
        }

        if (finalData.get('bids-option')) {
            if (
                !(finalData.get('min-price-rent')?.toString().trim().length ?? 0) ||
                !(finalData.get('start-date-rent')?.toString().trim().length ?? 0) ||
                !(finalData.get('end-date-rent')?.toString().trim().length ?? 0) ||
                (check && !check.checked)
            ) {
                return false;
            }
        }

        if (finalData.get('bids-buy-now') && !(finalData.get('price-bay-now')?.toString().trim().length ?? 0)) {
            return false;
        }

        let isValid = this.checkRequiredFields();
        if (!isValid) {
            return false;
        }

        for (let [key, value] of finalData.entries()) {
            console.log(`${key}: ${value}`);
        }

        return true;
    }

    public showBids(): void {
        let selectedOption = document.querySelector(
            'input[type="checkbox"][name="bids-option"]'
        ) as HTMLInputElement | null;

        if (selectedOption && selectedOption.checked) {
            let bids = document.querySelector('.bids-conditions-wrap') as HTMLElement | null;
            let check = document.querySelector('#bids-conditions-rule') as HTMLInputElement;
            if (check && bids && !check.checked) {
                bids.style.display = 'block';
            }
        }
    }

    public checkRequiredFields() {
        const formDropdown = document.querySelector('.main-upload-form #dropdown-wrap') as HTMLFormElement | null;
        const btn = document.querySelector('.upload-nav-btn-wrap.drop-btn') as HTMLFormElement | null;

        if (!formDropdown || !btn) {
            return false;
        }

        let requiredFieldsInput = formDropdown.querySelectorAll('input[require]') as NodeListOf<HTMLInputElement>;
        let requiredFieldsArea = formDropdown.querySelectorAll('textarea[require]') as NodeListOf<HTMLTextAreaElement>;
        const requireError = document.querySelector('.require-field-error') as HTMLFormElement;

        let isValid = true;

        requiredFieldsInput.forEach((field) => {
            if (field.value === '') {
                isValid = false;
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });

        requiredFieldsArea.forEach((field) => {
            if (field.value === '') {
                isValid = false;
                field.classList.add('error');
            } else {
                field.classList.remove('error');
            }
        });

        if (!isValid) {
            requireError.style.display = 'block';
            btn.classList.remove('active');
        } else {
            requireError.style.display = 'none';
            btn.classList.add('active');
        }

        return isValid;
    }

    public activeBtnStep3(): boolean {
        const formDropdown = document.querySelector('.main-upload-form #dropdown-wrap') as HTMLFormElement | null;

        if (!formDropdown) {
            return false;
        }

        let requiredFieldsInput = formDropdown.querySelectorAll('input') as NodeListOf<HTMLInputElement>;
        let requiredFieldsArea = formDropdown.querySelectorAll('textarea') as NodeListOf<HTMLTextAreaElement>;

        const initField = (field: HTMLInputElement | HTMLTextAreaElement) => {
            field.addEventListener('change', () => {
                this.checkRequiredFields();
            });
            field.addEventListener('keyup', () => {
                this.checkRequiredFields();
            });
        };

        requiredFieldsInput.forEach(initField);
        requiredFieldsArea.forEach(initField);

        return true;
    }

    public controlUploadTextArea() {
        let textAreas = document.querySelectorAll('.textarea-upload-content') as NodeListOf<HTMLTextAreaElement>;

        if (textAreas) {
            textAreas.forEach((textArea) => {
                let maxLength = textArea.dataset.maxLength ? Number(textArea.dataset.maxLength) : 0;

                textArea.addEventListener('input', (event) => {
                    let target = event.currentTarget as HTMLTextAreaElement;
                    let parent = target.closest('.text-textarea') as HTMLElement;
                    let countWrap = parent.querySelector('.text-area-count-wrap') as HTMLElement;
                    let countElement = parent.querySelector('.text-area-count') as HTMLElement;

                    if (countElement) {
                        let textLength = target.value.length;

                        if (textLength > maxLength) {
                            target.value = target.value.substring(0, maxLength);
                            textLength = maxLength;
                        }

                        countElement.textContent = textLength.toString();
                        countWrap.style.display = textLength > 0 ? 'block' : 'none';
                    }
                });
            });
        }
    }

    private shoeFinalModal() {
        const self = this;
        let step = "7";

        if(!self.modals || !self.mainForm){
            return;
        }

        self.modals.forEach((modal) => {
            const modalElement = modal as HTMLElement;
            modalElement.style.display = 'none';
        });

        let openModal = self.mainForm.querySelector(`.modal.modal-step[data-step="${step}"]`) as HTMLElement || null;

        if(openModal){
            openModal.style.display = 'block';
        }

        self.navBTN?.forEach((btn) => {
            btn.setAttribute('data-step', step || '1');
        });    
        self.mainForm.setAttribute('data-step', step || '1');
    }

    public createProductSendFormData() {
        const self = this;
        let form = document.querySelector('.main-upload-form') as HTMLFormElement;

        if (form) {
            let btn = form.querySelector('.created-product-js') as HTMLButtonElement;
            if (btn) {
                btn.addEventListener('click', (event) => {
                    event.preventDefault();

                    btn.disabled = true;
                    if (!form) return;
                    const formData = new FormData(form);
                    formData.append('action', 'createNewProduct');
                    formData.append('security', omnis_ajax_object?.nonce || '');

                    if (omnis_ajax_object?.ajaxurl) {
                        axios
                            .post(omnis_ajax_object.ajaxurl, formData)
                            .then((response) => {
                                if (response.data.success) {
                                    if (response.data.data.parent_id) {
                                        self.shoeFinalModal();
                                    } else {
                                        console.error('Could not get product ID.');
                                    }
                                } else {
                                    console.error('Error:', response.data.message);
                                }
                            })
                            .catch((error) => {
                                console.error('Error:', error);
                            })
                            .finally(() => {
                                btn.disabled = false;
                            });
                    }
                });
            }
        }
    }

    public completeFinalModal() {
        if (!this.mainForm) {
            return false;
        }

        let finalData = new FormData(this.mainForm);

        let title = this.mainForm.querySelector('.approval-product-info .title-approval-title') as HTMLElement;
        let description = this.mainForm.querySelector(
            '.approval-product-info .description-approval-title'
        ) as HTMLElement;
        let condition = this.mainForm.querySelector(
            '.approval-product-info .condition-approval-title .condition-title'
        ) as HTMLElement;
        let size = this.mainForm.querySelector('.approval-product-info .size-approval-title') as HTMLElement;
        let color = this.mainForm.querySelector(
            '.approval-product-info .color-approval-title .color-title'
        ) as HTMLElement;
        let colorHex = this.mainForm.querySelector(
            '.approval-product-info .color-approval-title .color-hex'
        ) as HTMLElement;
        let brands = this.mainForm.querySelector('.approval-product-info .brands-approval-title') as HTMLElement;
        let category = this.mainForm.querySelector('.approval-product-info .category-approval-title') as HTMLElement;
        let additional_comments = this.mainForm.querySelector(
            '.approval-product-info .additional_comments-approval-title'
        ) as HTMLElement;

        let wrap_bay = this.mainForm.querySelector('.approval-price-wrap.bay-js') as HTMLElement;
        let bay_input = this.mainForm.querySelector('.bay-approval-value') as HTMLInputElement;
        let discount_bay = this.mainForm.querySelector('.approval-discount.bay .bay-discount') as HTMLElement;

        let wrap_rent = this.mainForm.querySelector('.approval-price-wrap.rent-js') as HTMLElement;
        let rent_input = this.mainForm.querySelector('.rent-approval-value') as HTMLInputElement;
        let rent_discount_4 = this.mainForm.querySelector('.rent-discount-4-js') as HTMLElement;
        let rent_discount_8 = this.mainForm.querySelector('.rent-discount-8-js') as HTMLElement;

        let wrap_bid = this.mainForm.querySelector('.approval-price-wrap.bid-js') as HTMLElement;
        let bids_start = this.mainForm.querySelector('.bids-approval-value-start') as HTMLInputElement;
        let bids_end = this.mainForm.querySelector('.bids-approval-value-end') as HTMLInputElement;

        let product_image = this.mainForm.querySelector('.approval-product-image') as HTMLElement;
        let gallery = this.mainForm.querySelector('.approval-product-gallery') as HTMLElement;
        let final_logo = this.mainForm.querySelector('.added-final-logo') as HTMLElement;

        const quality: Record<string, string> = {
            new: 'חדש',
            good: 'במצב טוב',
            used: 'משומש',
        };

        let conditionData = finalData.get('condition');

        if (conditionData && typeof conditionData === 'string') {
            conditionData = quality[conditionData] || conditionData;
        }

        let key = 1;
        product_image.innerHTML = '';
        gallery.innerHTML = '';
        final_logo.innerHTML = '';
        this.mainForm.querySelectorAll('.selectedIMG').forEach((item) => {
            const element = item as HTMLElement;
            let img = document.createElement('img');
            img.setAttribute('src', element.getAttribute('src') || '');
            if (key == 1) {
                let cloneImage = img.cloneNode(true) as HTMLElement;
                product_image.appendChild(img);
                final_logo.appendChild(cloneImage);
            } else {
                gallery.appendChild(img);
            }
            ++key;
        });

        if (title) title.textContent = finalData.get('title')?.toString() || '';
        if (description) description.textContent = finalData.get('description')?.toString() || '';
        if (condition) condition.textContent = conditionData?.toString() || '';
        if (size) size.textContent = finalData.get('size-term-name')?.toString() || '';
        if (color) color.textContent = finalData.get('color-term-name')?.toString() || '';
        if (colorHex) colorHex.style.backgroundColor = finalData.get('color-term-name-hex')?.toString() || '';
        if (brands) brands.textContent = finalData.get('brands-term-name')?.toString() || '';
        if (category) category.textContent = finalData.get('category-name')?.toString() || '';
        if (additional_comments)
            additional_comments.textContent = finalData.get('additional_comments')?.toString() || '';

        if (finalData.get('sale-option') && bay_input) {
            wrap_bay.style.display = 'block';

            bay_input.value = finalData.get('price-bay-only')?.toString() || '';
            let parentApproval = discount_bay.closest('.approval-discount') as HTMLElement;

            if (finalData.get('sale-discount') && discount_bay) {
                parentApproval.style.display = 'block';
                discount_bay.textContent = finalData.get('sale-discount')?.toString() || '';
            } else {
                parentApproval.style.display = 'none';
            }
        } else if (finalData.get('bids-buy-now') && finalData.get('price-bay-now') && bay_input) {
            wrap_bay.style.display = 'block';

            bay_input.value = finalData.get('price-bay-now')?.toString() || '';
            let parentApproval = discount_bay.closest('.approval-discount') as HTMLElement;

            if (finalData.get('discount-bay-now') && discount_bay) {
                parentApproval.style.display = 'block';
                discount_bay.textContent = finalData.get('discount-bay-now')?.toString() || '';
            } else {
                parentApproval.style.display = 'none';
            }
        } else {
            wrap_bay.style.display = 'none';
        }

        if (finalData.get('renting-option') && rent_input) {
            wrap_rent.style.display = 'block';
            rent_input.value = finalData.get('renting_price')?.toString() || '';

            let parentApproval4 = rent_discount_4.closest('.approval-discount') as HTMLElement;
            let parentApproval8 = rent_discount_8.closest('.approval-discount') as HTMLElement;

            if (finalData.get('rent-discount-day-4') && rent_discount_4) {
                parentApproval4.style.display = 'block';
                rent_discount_4.textContent = finalData.get('rent-discount-day-4')?.toString() || '';
            } else {
                parentApproval4.style.display = 'none';
            }

            if (finalData.get('rent-discount-day-8') && rent_discount_8) {
                parentApproval8.style.display = 'block';
                rent_discount_8.textContent = finalData.get('rent-discount-day-8')?.toString() || '';
            } else {
                parentApproval8.style.display = 'none';
            }
        } else {
            wrap_rent.style.display = 'none';
        }

        if (finalData.get('bids-option')) {
            wrap_bid.style.display = 'block';
            if (bids_start) bids_start.value = finalData.get('min-price-rent')?.toString() || '';
            if (bids_end) bids_end.value = finalData.get('max-price-rent')?.toString() || '';
        } else {
            wrap_bid.style.display = 'none';
        }
    }

    private resetForm() {
        if (!this.mainForm) {
            return;
        }
    
        const buttonReset = this.mainForm.querySelector('.clean-form') as HTMLButtonElement | null;
        
        if (!buttonReset) {
            return;
        }
    
        buttonReset.addEventListener('click', (event) => {
            if (!this.mainForm) {
                return;
            }
            event.preventDefault();
    
            sessionStorage.setItem('triggerUploadClick', 'true');
    
            if (omnis_ajax_object?.home_url) {
                window.location.href = omnis_ajax_object.home_url;
            }
        }, { once: true });
    }

    private showUploadModalSession() {
        if (sessionStorage.getItem('triggerUploadClick') === 'true') {
            sessionStorage.removeItem('triggerUploadClick');
            this.showUploadModal(null);
        }
    }

    public photoSort() {
        const selectedImages: { count: number; src: string; value: string }[] = [];
        const self = this;
        document.querySelectorAll('.selectedIMG').forEach((input) => {
            const image = input as HTMLInputElement | null;
            if (image) {
                const count = image.getAttribute('count') ? Number(image.getAttribute('count')) : 0;
                const src = image.getAttribute('src') || '';
                const value = image.value;
                selectedImages.push({ count, src, value });
            }
        });

        selectedImages.sort((a, b) => a.count - b.count);

        const paginationList = document.querySelector('.pagination-list') as HTMLElement | null;
        const selectedImg = document.querySelector('.selected-img .img-wrap') as HTMLElement | null;
        if (paginationList && selectedImg) {
            paginationList.innerHTML = '';
            selectedImg.innerHTML = '';
        }

        let key = 0;
        selectedImages.forEach((imgData) => {
            const imgElement = document.createElement('img');
            imgElement.setAttribute('src', imgData.src);
            imgElement.setAttribute('data-id', imgData.value);

            if (paginationList) {
                const divElement = document.createElement('div');
                const numElement = document.createElement('div');
                numElement.setAttribute('class', 'num');
                numElement.textContent = imgData.count.toString();
                divElement.appendChild(numElement);
                divElement.appendChild(imgElement);
                paginationList.appendChild(divElement);
            }

            if (key === 0 && selectedImg) {
                const firstImgElement = imgElement.cloneNode(true) as HTMLElement;
                selectedImg.appendChild(firstImgElement);
            }

            key++;
        });

        if (paginationList) {
            $(paginationList).sortable({
                update: function () {
                    let key = 1;
                    document
                        .querySelectorAll('.pagination-list.ui-sortable .ui-sortable-handle .num')
                        .forEach((num) => {
                            let selectedIMG = document.querySelectorAll('.selectedIMG') as NodeListOf<HTMLElement>;

                            let numElement = num as HTMLElement;
                            numElement.textContent = key.toString();
                            let elementID = numElement.closest('div')?.querySelector('img')?.getAttribute('data-id');

                            selectedIMG.forEach((img) => {
                                let imgContainer = img as HTMLInputElement;
                                if (imgContainer.value === elementID) {
                                    img.setAttribute('count', key.toString());
                                }
                            });
                            key++;
                        });
                    document.querySelectorAll('input.hidden.selectedIMG').forEach((input) => {
                        input.remove();
                    });

                    let count = 1;
                    document.querySelectorAll('.pagination-list.ui-sortable .ui-sortable-handle img').forEach((img) => {
                        let input = document.createElement('input');
                        input.setAttribute('src', img.getAttribute('src') || '');
                        input.setAttribute('name', `selectedIMG-${count.toString()}`);
                        input.setAttribute('count', count.toString() || '');
                        input.value = img.getAttribute('data-id') || '';
                        input.classList.add('hidden');
                        input.classList.add('selectedIMG');
                        ++count;
                        if (self.mainForm) self.mainForm.appendChild(input);
                    });
                },
            });
        }

        let topImg = document.querySelector('.selected-img .img-wrap img') as HTMLElement;
        document.querySelectorAll('.pagination-list > div').forEach((element) => {
            let parentElement = element as HTMLElement;
            parentElement?.querySelector('img')?.addEventListener('click', (event) => {
                let img = event.currentTarget as HTMLElement;
                let src = img.getAttribute('src');
                topImg.setAttribute('src', src || '');
                let id = img.getAttribute('data-id');
                topImg.setAttribute('data-id', id || '');
            });
        });
    }

    public savePhoto(event: Event) {
        event.preventDefault();
        const self = this;
        const mediaModal = document.querySelector('#media-modal') as HTMLElement | null;

        if (self.canvas) {
            const imageData = self.canvas.toDataURL('image/png');

            const formData = new FormData();
            formData.append('action', 'handle_save_snapshot');
            formData.append('security', omnis_ajax_object?.nonce || '');
            formData.append('image', imageData);

            this.addFormPreload(mediaModal, '10');
            if (omnis_ajax_object?.ajaxurl) {
                axios
                    .post(omnis_ajax_object.ajaxurl, formData)
                    .then((response) => {
                        if (response.data.success && response.data.data.url && self.mediaGallery) {
                            let imageContainer = document.createElement('img');
                            imageContainer.setAttribute('src', response.data.data.url);
                            imageContainer.setAttribute('data-id', response.data.data.id);
                            imageContainer.addEventListener('click', self.selectedFromList.bind(self));
                            let divContainer = document.createElement('div');
                            const numDiv = document.createElement('div');
                            numDiv.setAttribute('class', 'num');
                            divContainer.appendChild(numDiv);
                            divContainer.appendChild(imageContainer);
                            self.mediaGallery.insertBefore(divContainer, self.mediaGallery.firstChild);
                            if (self.currentStream) {
                                const tracks = self.currentStream.getTracks();
                                tracks.forEach((track) => track.stop());
                                self.currentStream = null;
                            }
                            if (self.cameraParentWrap && self.snapshot && self.saveBtn && self.mediaGallery) {
                                self.cameraParentWrap.style.display = 'none';
                                self.snapshot.style.display = 'none';
                                self.saveBtn.style.display = 'none';
                                self.mediaGallery.style.display = 'grid';
                            }
                        } else {
                            console.error('Error saving image:', response.data.message);
                        }
                    })
                    .catch((error) => {
                        console.error('Error saving image:', error);
                    })
                    .finally(() => {
                        this.removeFormPreload(mediaModal);
                    });
            }
        }
    }

    public showCamera(event: Event) {
        event.preventDefault();
        const self = this;
        self.cameraParentWrap = document.querySelector('.camera-wrap') as HTMLElement | null;
        self.mediaGallery = document.querySelector('.user-media-gallery') as HTMLElement | null;
        self.captureBtn = document.getElementById('captureBtn') as HTMLElement | null;
        self.canvas = document.getElementById('canvas') as HTMLCanvasElement | null;
        self.snapshot = document.getElementById('snapshot') as HTMLImageElement | null;
        self.saveBtn = document.getElementById('saveBtn') as HTMLElement | null;

        if (
            self.cameraParentWrap &&
            self.mediaGallery &&
            self.captureBtn &&
            self.canvas &&
            self.snapshot &&
            self.saveBtn
        ) {
            if (self.cameraParentWrap.style.display === 'none') {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    navigator.mediaDevices
                        .getUserMedia({ video: true })
                        .then((stream) => {
                            const video = document.querySelector('video') as HTMLVideoElement | null;
                            if (video && self.cameraParentWrap && self.mediaGallery) {
                                self.currentStream = stream;
                                self.cameraParentWrap.style.display = 'block';
                                self.mediaGallery.style.display = 'none';
                                video.srcObject = stream;
                            }
                        })
                        .catch((err) => {
                            console.error('Error accessing the camera:', err);
                            alert('Error accessing the camera');
                        });
                } else {
                    alert('Error accessing the camera');
                }

                const context = self.canvas.getContext('2d');
                self.captureBtn.addEventListener('click', (event) => {
                    event.preventDefault();
                    const video = document.querySelector('video') as HTMLVideoElement | null;
                    if (video && context) {
                        if (video.videoWidth && video.videoHeight && self.canvas && self.snapshot && self.saveBtn) {
                            self.canvas.width = video.videoWidth;
                            self.canvas.height = video.videoHeight;
                            context.drawImage(video, 0, 0, self.canvas.width, self.canvas.height);
                            const imageData = self.canvas.toDataURL('image/png');
                            self.snapshot.src = imageData;
                            self.snapshot.style.display = 'block';
                            self.saveBtn.style.display = 'inline';
                        }
                    }
                });

                if (self.saveBtn.dataset.listener === 'true') return;

                self.saveBtn.dataset.listener = 'true';

                self.saveBtn.addEventListener('click', self.savePhoto.bind(self));
            } else {
                if (self.currentStream) {
                    const tracks = self.currentStream.getTracks();
                    tracks.forEach((track) => track.stop());
                    self.currentStream = null;
                }
                self.cameraParentWrap.style.display = 'none';
                self.snapshot.style.display = 'none';
                self.saveBtn.style.display = 'none';
                self.mediaGallery.style.display = 'grid';
            }
        }
    }

    public selectedFromList(event: Event) {
        const self = this;
        event.preventDefault();
        const target = event.target as HTMLElement | null;

        if (target && target.tagName === 'IMG') {
            const divElement = target.closest('div');
            if (divElement) {
                if (divElement.getAttribute('count')) {
                    divElement.classList.remove('selected');
                    const parentElement = target.closest('.main-form-wrapper');
                    let removeCount = divElement.getAttribute('count') ? Number(divElement.getAttribute('count')) : 0;
                    divElement.removeAttribute('count');
                    --self.countSelect;
                    let allDiv = parentElement?.querySelectorAll('div');
                    allDiv?.forEach((div) => {
                        if (div.getAttribute('count')) {
                            let countChange = div.getAttribute('count') ? Number(div.getAttribute('count')) : 0;
                            if (countChange && removeCount && countChange > removeCount) {
                                --countChange;
                                div.setAttribute('count', countChange.toString());
                                let num = div.querySelector('.num') as HTMLElement | null;
                                if (num) num.textContent = countChange.toString();
                            }
                        }
                    });
                } else {
                    if (self.countSelect < 6) {
                        divElement.classList.add('selected');
                        divElement.setAttribute('count', self.countSelect.toString());
                        let num = divElement.querySelector('.num') as HTMLElement | null;
                        if (num) num.textContent = self.countSelect.toString();
                        self.countSelect++;
                    }
                }
            }

            let countCount = document.querySelector('.count-count') as HTMLElement | null;
            let countText = document.querySelector('.text-count') as HTMLElement | null;

            let countNum = countCount ? countCount.querySelector('.num') : null;
            if (self.countSelect > 1 && self.mediaGallery) {
                self.mediaGallery.classList.add('selected');
                if (countText && countCount && countCount.querySelector('.num')) {
                    if (countNum) countNum.textContent = (self.countSelect - 1).toString();
                    countCount.style.display = 'block';
                    countText.style.display = 'none';
                }
            } else {
                self.mediaGallery?.classList.remove('selected');
                if (countCount) countCount.style.display = 'none';
                if (countText) countText.style.display = 'block';
            }

            if (self.mainForm) {
                const parentElement = target.closest('.main-form-wrapper') as HTMLElement | null;
                let allDiv = parentElement?.querySelectorAll('div') as NodeListOf<HTMLDivElement>;

                self.mainForm.querySelectorAll('.selectedIMG').forEach((input) => {
                    input.remove();
                });
                allDiv?.forEach((div) => {
                    if (div.getAttribute('count')) {
                        let count = div.getAttribute('count');
                        let src = div.querySelector('img')?.getAttribute('src');
                        let id = div.querySelector('img')?.getAttribute('data-id');
                        const input = document.createElement('input');
                        input.setAttribute('src', src || '');
                        input.setAttribute('name', `selectedIMG-${count}`);
                        input.setAttribute('count', count || '');
                        input.classList.add('hidden');
                        input.classList.add('selectedIMG');
                        input.value = id || '';
                        if (self.mainForm) self.mainForm.appendChild(input);
                    }
                });
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    UplouadProduct.getInstance();
});

export {};