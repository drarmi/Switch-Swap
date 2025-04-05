import axios from 'axios';
import $ from '../../../node_modules/jquery';
import 'parsleyjs';

interface AjaxObject {
    ajaxurl: string;
}

declare const omnis_ajax_object: AjaxObject | undefined;

class ProfilePage {
    private static instance: ProfilePage;
    private profilePage: HTMLElement;
    private parsleyInstance: any = null;

    private constructor() {
        this.profilePage = (document.querySelector('.main-container.profile') as HTMLElement) || null;
        
        if (this.profilePage) {   
            this.closeModalPopUp();
            this.mainNav();
            this.toMainModal();
            this.controlInputValue();
            this.formSubmit();
            this.initParsley();
            this.textareaCount();
            this.editLogo();
            this.submitDetails();
            this.notificationsSwitches();
        }
    }

    public static getInstance(): ProfilePage {
        if (!this.instance) {
            this.instance = new this();
        }
        return this.instance;
    }

    private notificationsSwitches() {
        const notificationsSwitches = document.querySelectorAll('.switch-box') as NodeListOf<HTMLElement>;

        if (notificationsSwitches.length > 0) {
            notificationsSwitches.forEach((btn) => {
                btn.addEventListener('click', (event) => {
                    const currentSwitch = event.currentTarget as HTMLElement;
                    const li = currentSwitch.closest('.notification-li') as HTMLElement;

                    if (li && li.dataset.notificationStatus !== undefined) {
                        let status = li.dataset.notificationStatus === '1' ? '0' : '1';
                        li.dataset.notificationStatus = status;
                        let currentInput = li.querySelector("input[type='text']") as HTMLInputElement;
                        if (currentInput) {
                            currentInput.value = status;
                            let form = document.querySelector('.personal_notifications-form form') as HTMLFormElement;
                            let formData = new FormData(form);
                            formData.append("action", "notifications_switch")

                            if (omnis_ajax_object?.ajaxurl) {
                                        axios
                                            .post(omnis_ajax_object.ajaxurl, formData)
                                            .then((response) => {
                                                if (response.data.success) {
                                                    
                                                } else {
                                                    const message_error = response.data.message || response.data.data || 'Unknown error occurred';

                                                }
                                            })
                                            .catch((error) => {
                                                console.error('Error resetting password:', error);
                                            })
                                            .finally(() => {
                                                
                                            });
                                    } else {
                                        console.error('ajaxUrl is undefined or invalid.');
                                        alert('An error occurred: Unable to send the request.');
                                    }
                            console.log(...formData);
                        }
                    }
                });
            });
        }
    }

    private initParsley() {
        let formDetails = $('.personal_details-form form');
        if (formDetails.length > 0) {
            this.parsleyInstance = formDetails.parsley();
        }
    }

    private textareaCount() {
        let textarea = document.querySelector('.textarea-details') as HTMLTextAreaElement || null;
        let detailsCount = document.querySelector('.details-count-current') as HTMLElement || null;

        if (textarea && detailsCount) {
            detailsCount.textContent = textarea.value.length.toString();

            textarea.addEventListener('input', (event) => {
                let currentCount = (event.currentTarget as HTMLTextAreaElement).value.length;
                detailsCount.textContent = currentCount.toString();
            });
        }
    }

    private editLogo() {
        let btn = document.querySelector('.edit-logo') as HTMLElement || null;
        let fileInput = document.querySelector(".logo-wrap input[type='file']") as HTMLInputElement || null;
        let img = document.querySelector('.logo-wrap img') as HTMLImageElement || null;

        if (btn && fileInput && img) {
            btn.addEventListener('click', () => {
                fileInput.click();
            });

            fileInput.addEventListener('change', (event) => {
                let target = event.target as HTMLInputElement;
                if (target.files && target.files[0]) {
                    let file = target.files[0];
                    img.src = URL.createObjectURL(file);
                }
            });
        }
    }

    private submitDetails() {
        const formDetails = document.querySelector('.personal_details-form form') as HTMLFormElement | null;
    
        if (formDetails) {
            formDetails.addEventListener('submit', (event) => {
                event.preventDefault();
    
                if (this.parsleyInstance?.isValid()) {
                    const formData = new FormData(formDetails);
                    formData.append('action', 'update_vendor_details');
    
                    if (omnis_ajax_object?.ajaxurl) {
                        axios.post(omnis_ajax_object.ajaxurl, formData)
                            .then((response) => {
                                if (response.data.success) {
                                    alert('הפרטים נשמרו בהצלחה');
                                    location.reload(); // або інше оновлення
                                } else {
                                    alert('שגיאה: ' + (response.data.message || 'Unknown error'));
                                }
                            })
                            .catch((error) => {
                                console.error('AJAX error:', error);
                                alert('אירעה שגיאה בשליחת הטופס');
                            });
                    } else {
                        console.error('ajaxurl is missing');
                    }
                }
            });
        }
    }

    private closeModalPopUp(): void {
        let btns = document.querySelectorAll('.close-authenticated-popup-js') as NodeListOf<HTMLElement> || null;
        if(btns){
            btns.forEach((btn) => {
                btn.addEventListener('click', (event) => {
                    let wrapper = (event.currentTarget as HTMLElement).closest('.modal-authenticated') as HTMLElement;
                    if (wrapper) {
                        wrapper.style.display = 'none';
                    }
                });
            });
        }
    }

    private toMainModal(): void {
        let btns = document.querySelectorAll('.to-main-js') as NodeListOf<HTMLElement> || null;
        let modals = document.querySelectorAll('.main-container.profile .modal-section') as NodeListOf<HTMLElement> || null;
        if(btns && modals){
            btns.forEach((btn) => {
                btn.addEventListener('click', (event) => {
                    modals.forEach((modal) => {
                        modal.style.display = modal.dataset.profile === 'main-nav-profile' ? 'block' : 'none';
                    });
                });
            });
        }
    }

    private mainNav(): void {
        let navList = document.querySelectorAll('.profile-nav-list nav ul li') as NodeListOf<HTMLElement> || null;
        if(navList){
            navList.forEach((liElement) => {
                liElement.addEventListener('click', (event) => {
                    let liEvent = event.currentTarget as HTMLElement;
                    let profile = liEvent.dataset.profile;
                    let modals = (liEvent.closest('.main-container.profile') as HTMLElement).querySelectorAll(
                        '.modal-section'
                    ) as NodeListOf<HTMLElement>;
    
                    let showModal = Array.from(modals).some((modal) => modal.dataset.profile === profile);
    
                    if (showModal) {
                        modals.forEach((modal) => {
                            modal.style.display = modal.dataset.profile === profile ? 'block' : 'none';
                        });
                    }
                });
            });
        }
    }

    private controlInputValue() {
        let wrappers = document.querySelectorAll('.input-soc-wrapper') as NodeListOf<HTMLElement> || null;;
        if(wrappers){
            wrappers.forEach((wrapper) => {
                let input = wrapper.querySelector('input') as HTMLInputElement;
    
                if (input && input.value) {
                    wrapper.classList.add('has_value');
                }
    
                input?.addEventListener('change', () => {
                    if (input.value) {
                        wrapper.classList.add('has_value');
                    } else {
                        wrapper.classList.remove('has_value');
                    }
                });
            });
        }
    }

    private formSubmit() {
        let form = document.querySelector('.profile-main-form form') as HTMLFormElement || null;
        let popupSend = document.querySelector('.authenticated-popup-wrap-send') as HTMLElement || null;
        if (form && popupSend) {
            const usernamePattern = /^[a-zA-Z0-9._-]{3,}$/;

            form.addEventListener('submit', (event) => {
                event.preventDefault();

                let formData = new FormData(form);

                let tikTokUsername = formData.get('tik-tok') as string;
                let instagramUsername = formData.get('instagram') as string;

                if (!tikTokUsername && !instagramUsername) {
                    alert('Please enter at least one TikTok or Instagram username.');
                    return;
                }

                if (tikTokUsername && !usernamePattern.test(tikTokUsername)) {
                    alert('Invalid TikTok username');
                    return;
                }

                if (instagramUsername && !usernamePattern.test(instagramUsername)) {
                    alert('Invalid Instagram username');
                    return;
                }

                console.log(...formData);

                popupSend.style.display = 'flex';
            });
        }
    }
}

// Initialize the ProfilePage functionality
ProfilePage.getInstance();

export {};
