import $ from '../../../node_modules/jquery';
import 'parsleyjs';
import axios from 'axios';

interface AjaxObject {
    ajaxurl: string;
}

declare const omnis_ajax_object: AjaxObject | undefined;

class CustomerRegistration {
    private static instance: CustomerRegistration;
    private currentStep: number = 0;

    private steps: NodeListOf<HTMLElement>;
    private progressItems: NodeListOf<HTMLElement>;
    private btnNext: NodeListOf<HTMLElement>;
    private btnNextStep1: HTMLButtonElement;
    private btnSkip: NodeListOf<HTMLElement>;
    private btnBack: HTMLElement;
    private usernameInput: HTMLInputElement;
    private emailInput: HTMLInputElement;
    private agreeCheckbox: HTMLInputElement;
    private agreeToS: HTMLInputElement | null;
    private firstNextButton: HTMLButtonElement;
    private fileInput: HTMLInputElement;
    private previewImage: HTMLImageElement;
    private addMediaPopup: HTMLImageElement;
    private cameraButton: HTMLButtonElement;
    private videoElement: HTMLVideoElement;
    private captureButton: HTMLButtonElement;
    private canvasElement: HTMLCanvasElement;
    private birthdayInput: HTMLInputElement;
    private dateHelper: HTMLElement;
    private registrationInfo: HTMLElement;
    private phoneInput: HTMLInputElement;
    private parsleyRegForm: any;
    private checkAge: any;
    private dashboardURL: any;

    private constructor() {
        this.steps = document.querySelectorAll(".registration-step");
        this.progressItems = document.querySelectorAll(".registration-progress-bar__step");
        this.btnNext = document.querySelectorAll(".registration-btn-dark--next");
        this.btnNextStep1 = document.querySelector('.auth-btn.auth-btn-dark.registration-btn-dark--next') as HTMLButtonElement;
        this.btnSkip = document.querySelectorAll(".auth-btn-skip");
        this.btnBack = document.querySelector(".registration-progress-bar__back")!;
        this.usernameInput = document.getElementById("reg_username") as HTMLInputElement;
        this.emailInput = document.getElementById("email") as HTMLInputElement;
        this.agreeCheckbox = document.getElementById("agree") as HTMLInputElement;
        this.agreeToS = document.getElementById("agreeToS") as HTMLInputElement | null;
        this.firstNextButton = this.steps[0].querySelector(".registration-btn-dark--next") as HTMLButtonElement;
        this.fileInput = document.getElementById("profile_photo") as HTMLInputElement;
        this.previewImage = document.getElementById("previewImage") as HTMLImageElement;
        this.addMediaPopup = document.querySelector(".add-image-popup") as HTMLImageElement;
        this.cameraButton = document.getElementById("openCamera") as HTMLButtonElement;
        this.videoElement = document.getElementById("camera") as HTMLVideoElement;
        this.captureButton = document.getElementById("capturePhoto") as HTMLButtonElement;
        this.canvasElement = document.getElementById("canvas") as HTMLCanvasElement;
        this.birthdayInput = document.getElementById("birthday") as HTMLInputElement;
        this.dateHelper = document.querySelector(".auth-custom-date-helper")!;
        this.registrationInfo = document.querySelector(".registration-field__info")!;
        this.phoneInput = document.getElementById("phone") as HTMLInputElement; // Отримуємо поле телефону


        this.initRegistrationSteps();
        this.initFileInputHandler();
        this.initCameraHandler();
        this.initBirthdayValidation(); // Add birthday validation
        this.syncUsernameToImageName(); // Додаємо синхронізацію імені
        this.validationStepOneParsley();


        if(this.agreeToS){
            this.controlTos();
        }
    }

    public static getInstance(): CustomerRegistration {
        if (!this.instance) {
            this.instance = new this();
        }
        return this.instance;
    }

    private initRegistrationSteps(): void {
        this.updateStep();
        // this.toggleFirstNextButton();
        this.addEventListeners();
    }

    private initBirthdayValidation(): void {
        this.birthdayInput.addEventListener("click", () => {
            this.birthdayInput.showPicker();
        });

        this.birthdayInput.addEventListener("input", () => {
            if (this.birthdayInput.value) {
                const birthDate = new Date(this.birthdayInput.value);
                const options: Intl.DateTimeFormatOptions = { day: "numeric", month: "long", year: "numeric" };
                this.dateHelper.textContent = birthDate.toLocaleDateString("he-IL", options);

                // Age validation
                const today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                const monthDiff = today.getMonth() - birthDate.getMonth();
                const dayDiff = today.getDate() - birthDate.getDate();

                if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
                    age--;
                }

                if (age < 18) {
                    this.registrationInfo.classList.add("underage-warning");
                } else {
                    this.registrationInfo.classList.remove("underage-warning");
                }
            } else {
                this.dateHelper.textContent = "";
                this.registrationInfo.classList.remove("underage-warning");
            }
        });
    }

    private updateStep(): void {
        this.steps.forEach((step) => (step.style.display = "none"));
        this.steps[this.currentStep].style.display = "flex";

        this.progressItems.forEach((item, index) => {
            if (index <= this.currentStep) {
                item.classList.add("registration-progress-bar__step--active");
            } else {
                item.classList.remove("registration-progress-bar__step--active");
            }
        });
    }

    private controlTos(){
        let closeToS = document.querySelectorAll('.close-ToS-modal-js') as NodeListOf<HTMLElement>;
        let showTosModalWrap = document.querySelector('.showTosModalWrap') as HTMLElement;
        let btnTransparent = document.querySelector('.ToS-conditions-modal-footer button.transparent') as HTMLElement;

        closeToS.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                showTosModalWrap.style.display = 'none';
                
                if(this.agreeToS && this.agreeCheckbox && !this.agreeToS.checked){
                    this.agreeToS.checked = false;
                    this.agreeCheckbox.checked = false;
                    btnTransparent.classList.remove('active');
                }
            });
        });
        
        this.showTosModal();
        this.controlScroll();
        this.confirmTos();
    }

    private showTosModal(): void {
        let showTosBtn = document.querySelector('.showTos') as HTMLElement;
        let showTosModalWrap = document.querySelector('.showTosModalWrap') as HTMLElement;
        showTosBtn.addEventListener('click', (event) => {
            showTosModalWrap.style.display = 'block';
            let scroll = document.querySelector('.ToS-conditions-modal-content-rules') as HTMLElement;
            if (scroll) {
                scroll.scrollTop = 0; 
            }
        });
        
        this.agreeCheckbox.addEventListener('click', (event) => {
            event.preventDefault();

            setTimeout(() => {
                let currentCheck = this.agreeCheckbox;

                if (this.agreeToS) {
                    if (this.agreeToS.checked) {
                        currentCheck.checked = !currentCheck.checked;
                        if (this.parsleyRegForm.isValid() && this.checkAge()) {
                            this.btnNextStep1.disabled = false;
                        }else{
                            this.btnNextStep1.disabled = true;
                        }
                    } else {
                        const showTosModalWrap = document.querySelector('.showTosModalWrap') as HTMLElement | null;
                        if (showTosModalWrap) {
                            showTosModalWrap.style.display = 'block';
                        }
                    }
                }
            }, 0); 
        });
    }

    private controlScroll(): void {
        let scroll = document.querySelector('.ToS-conditions-modal-content-rules') as HTMLElement;

        if (this.agreeToS && this.agreeToS.checked) {
            return;
        }

        if (scroll) {
            scroll.addEventListener('scroll', () => {
                const scrollHeight = scroll.scrollHeight;
                const scrollTop = scroll.scrollTop;
                const clientHeight = scroll.clientHeight;


                if (scrollTop + clientHeight >= scrollHeight) {
                    let btnTransparent = document.querySelector('.ToS-conditions-modal-footer .transparent') as HTMLElement;
                    
                    if (this.agreeToS && this.agreeCheckbox) {
                        if(!this.agreeToS.checked){
                            btnTransparent.classList.add('active');
                        }
                    }
                }
            });
        }
    }

    private confirmTos(): void {
        document.querySelector('.ToS-conditions-modal-footer button.transparent')?.addEventListener('click', (event) => {
            event.preventDefault();
            const button = event.currentTarget as HTMLElement;
            if (button.classList.contains('active')) {
                const showTosModalWrap = document.querySelector('.showTosModalWrap') as HTMLElement | null;
    
                if (this.agreeToS && this.agreeCheckbox && !this.agreeToS.checked) {
                    this.agreeToS.checked = true;
                    this.agreeCheckbox.checked = true;
                    
                    if (this.parsleyRegForm.isValid() && this.checkAge()) {
                        this.btnNextStep1.disabled = false;
                    }else{
                        this.btnNextStep1.disabled = true;
                    }
                }

                if (showTosModalWrap) {
                    showTosModalWrap.style.display = 'none';
                }
            }
        });
    }

    private validationStepOneParsley() {
        const restorePasswordForm = document.querySelector('.auth-form--registration form.woocommerce-form.woocommerce-form-register.register') as HTMLFormElement | null;
        
        if (!restorePasswordForm) return;
        
        this.parsleyRegForm = $(restorePasswordForm).parsley();
    
        this.checkAge = () => {
            if (this.birthdayInput) {
                const dob = new Date(this.birthdayInput.value);
                const currentDate = new Date();
                let age = currentDate.getFullYear() - dob.getFullYear();
                const monthDifference = currentDate.getMonth() - dob.getMonth();
        
                if (monthDifference < 0 || (monthDifference === 0 && currentDate.getDate() < dob.getDate())) {
                    age--;
                }
                return age >= 18;
            }
            return false;
        };

        const pasConfirm = document.getElementById('pas-confirm-registration') as HTMLInputElement;
        const pasRegistration = document.getElementById('pas-registration') as HTMLInputElement;
    
        const validateForm = () => {
            if (this.parsleyRegForm.isValid() && this.checkAge()) {
                this.btnNextStep1.disabled = false;
            } else {
                this.btnNextStep1.disabled = true;
            }
        };
    
        const fieldsToValidate = [
            this.usernameInput,
            this.emailInput,
            this.phoneInput,
            this.birthdayInput,
            pasConfirm,
            pasRegistration
        ];
    
        fieldsToValidate.forEach(field => {
            if (field) {
                field.addEventListener("change", validateForm);
            }
        });
    }
    

    // private toggleFirstNextButton(): void {
    //     // Перевіряємо, чи заповнені всі поля
    //     const isUsernameValid = this.usernameInput.value.trim() !== "";
    //     const isEmailValid = this.emailInput.value.trim() !== "";
    //     const isPhoneValid = this.phoneInput.value.trim() !== "";
    //     const isDateValid = this.birthdayInput.value.trim() !== "";
    //     const isAgreed = this.agreeCheckbox.checked;

    //     // Вік користувача
    //     let isOldEnough = false;
    //     if (this.birthdayInput.value) {
    //         const birthDate = new Date(this.birthdayInput.value);
    //         const today = new Date();
    //         let age = today.getFullYear() - birthDate.getFullYear();
    //         const monthDiff = today.getMonth() - birthDate.getMonth();
    //         const dayDiff = today.getDate() - birthDate.getDate();

    //         if (monthDiff < 0 || (monthDiff === 0 && dayDiff < 0)) {
    //             age--;
    //         }
    //         isOldEnough = age >= 18;
    //     }
    //     // Активуємо кнопку, якщо всі умови виконані
    //     this.firstNextButton.disabled = !(isUsernameValid && isEmailValid && isPhoneValid && isDateValid && isOldEnough && isAgreed);
    // }

    private syncUsernameToImageName(): void {
        const nameDisplay = document.querySelector(".registration-image-name") as HTMLElement;
        
        this.usernameInput.addEventListener("input", () => {
            nameDisplay.textContent = this.usernameInput.value.trim() || " ";
        });
    
        // При переході на другий крок оновити текст
        this.btnNext.forEach((button) => {
            button.addEventListener("click", () => {
                if (this.currentStep === 1) { // Другий крок
                    nameDisplay.textContent = this.usernameInput.value.trim() || " ";
                }
            });
        });
    }

    private submitRegistrationForm() {
        const registrationForm = document.querySelector(
            '.auth-form--registration form.woocommerce-form.woocommerce-form-register.register'
        ) as HTMLFormElement | null;
    
        if (!registrationForm || !this.parsleyRegForm.isValid()) return;
    
        let submitData = new FormData(registrationForm);
    
        submitData.append('action', 'dokan_register_vendor');
        submitData.append('role', 'seller');
    
        const nonceField = registrationForm.querySelector('input[name="user_registration_nonce"]') as HTMLInputElement;
        const logo = registrationForm.querySelector('.top-logo .user-logo') as HTMLImageElement;
        const name = registrationForm.querySelector('.top-logo h3') as HTMLElement;
        const logoSvg = registrationForm.querySelector('.top-logo .user-logo-svg') as HTMLImageElement;
        

        if (nonceField) {
            submitData.append('security', nonceField.value);
        }
    



        if (omnis_ajax_object?.ajaxurl) {
            axios
                .post(omnis_ajax_object.ajaxurl, submitData)
                .then((response) => {
                    console.log(response);
                    
                    if (response.data.success) {
                        this.dashboardURL = response.data.data.redirect_url;
                        name.textContent = response.data.data.name;
                    
                        if (response.data.data.logo_url && response.data.data.logo_url !== "") {
                            logo.style.display = "block";
                            logoSvg.style.display = "none";
                            logo.setAttribute("src", response.data.data.logo_url);
                        } else {
                            logo.style.display = "none";
                            logoSvg.style.display = "block";
                        }
                    
                        this.currentStep++;
                        this.updateStep();
                    } else {
                        alert(response.data.data.message || 'Registration failed.');
                    }
                })
                .catch((error) => {
                    console.error('Error registering vendor:', error);
                    alert('An error occurred while processing your request.');
                });
        } else {
            console.error('ajaxUrl is undefined or invalid.');
        }
    }

    private closeFinalModal(){
        const restorePasswordForm = document.querySelector('.auth-form--registration form.woocommerce-form.woocommerce-form-register.register') as HTMLFormElement | null;
        
        if (!restorePasswordForm) return;

        window.location.href = this.dashboardURL;

    }

    private addEventListeners(): void {
        // this.usernameInput.addEventListener("input", () => this.toggleFirstNextButton());
        // this.emailInput.addEventListener("input", () => this.toggleFirstNextButton());
        // this.agreeCheckbox.addEventListener("change", () => this.toggleFirstNextButton());

        this.btnNext.forEach((button) => {
            button.addEventListener("click", (event) => {
                let currentBtn = event.currentTarget as HTMLButtonElement;

                if(currentBtn.classList.contains("submit-data")){
                    this.submitRegistrationForm();
                }else if(currentBtn.classList.contains("close-custom-registration-final")){
                    this.closeFinalModal();
                }else{
                    if (this.currentStep < this.steps.length - 1) {
                        this.currentStep++;
                        this.updateStep();
                    }
                }
            });
        });

        this.btnSkip.forEach((button) => {
            button.addEventListener("click", () => {
                if (this.currentStep < this.steps.length - 1) {
                    this.currentStep++;
                    this.updateStep();
                }
            });
        });

        this.btnBack.addEventListener("click", () => {
            if (this.currentStep > 0) {
                this.currentStep--;
                this.updateStep();
            }
        });
    }

    private initFileInputHandler(): void {
        this.fileInput.addEventListener("change", () => {
            const file = this.fileInput.files?.[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImage.src = e.target?.result as string;
                    this.previewImage.style.display = "block";
                    this.addMediaPopup.style.display = "none";
                };
                reader.readAsDataURL(file);
            } else {
                this.previewImage.src = "";
                this.previewImage.style.display = "none";
            }
        });
    }

    private initCameraHandler(): void {
        this.cameraButton.addEventListener("click", () => {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then((stream) => {
                    this.videoElement.srcObject = stream;
                    this.videoElement.style.display = "block";
                    this.addMediaPopup.style.display = "none";
                    this.captureButton.style.display = "block";
                })
                .catch((err) => {
                    console.error("Error accessing camera:", err);
                });
        });

        this.captureButton.addEventListener("click", () => {
            const context = this.canvasElement.getContext("2d");
            if (context) {
                this.canvasElement.width = this.videoElement.videoWidth;
                this.canvasElement.height = this.videoElement.videoHeight;
                context.drawImage(this.videoElement, 0, 0, this.canvasElement.width, this.canvasElement.height);

                this.canvasElement.toBlob((blob) => {
                    if (blob) {
                        const file = new File([blob], "photo.jpg", { type: "image/jpeg" });
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        this.fileInput.files = dataTransfer.files;

                        const reader = new FileReader();
                        reader.onload = (e) => {
                            this.previewImage.src = e.target?.result as string;
                            this.previewImage.style.display = "block";
                            this.captureButton.style.display = "none";
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }
        });
    }
}

if (document.querySelector(".registration-step")) {
    CustomerRegistration.getInstance();
}

export {};
