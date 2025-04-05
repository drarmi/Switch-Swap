import axios from 'axios';

interface AjaxObject {
    ajaxurl: string;
    nonce: string;
    checkout_url: string;
    cart_url: string;
    date_set: object;
    discount_period: object;
    home_url: string;
    userLogin: string;
}

declare const omnis_ajax_object: AjaxObject | undefined;

class Registration {
    private static instance: Registration;

    private constructor() {
        this.initEventListeners();
    }

    public static getInstance(): Registration {
        if (!this.instance) {
            this.instance = new this();
        }
        return this.instance;
    }

    private initEventListeners(): void {
        let link_guest = document.querySelector(".link-to-registration__guest") as HTMLElement | null;
        
        if(link_guest){
            link_guest.addEventListener("click", this.handleGuest.bind(this));
        }
        
        document.querySelectorAll(".btn-next").forEach((button) => {
            button.addEventListener("click", this.handleNextStep.bind(this));
        });
        document.querySelectorAll(".btn-prev").forEach((button) => {
            button.addEventListener("click", this.handlePreviousStep.bind(this));
        });
        document.querySelectorAll(".btn-skip").forEach((button) => {
            button.addEventListener("click", this.handleSkipStep.bind(this));
        });
        document.getElementById("registration-form")?.addEventListener("submit", this.submitForm.bind(this));

        const roleSelect = document.getElementById("role") as HTMLSelectElement;
        if (roleSelect) {
            roleSelect.addEventListener("change", this.toggleVendorFields.bind(this));
            // Initialize vendor fields based on default role
            this.toggleVendorFields({ target: roleSelect } as unknown as Event);

        }

        const shopNameInput = document.getElementById("shop-name") as HTMLInputElement;
        const shopUrlInput = document.getElementById("shop-url") as HTMLInputElement;
        const shopNameError = document.createElement("div");
        shopNameError.className = "error-message";
        shopNameInput?.parentNode?.appendChild(shopNameError);

        // Update URL and show error when entering shop name
        if (shopNameInput && shopUrlInput) {
            shopNameInput.addEventListener("input", () => {
                const formattedUrl = this.formatShopUrl(shopNameInput.value);

                if (formattedUrl !== shopNameInput.value) {
                    shopNameError.textContent = "Use only Latin letters and numbers.";
                    shopNameError.style.display = "block";
                } else {
                    shopNameError.style.display = "none";
                }

                shopUrlInput.value = formattedUrl;
                document.getElementById("url-alert")!.textContent = formattedUrl;
                this.checkShopUrlAvailability({ target: shopUrlInput } as unknown as Event);
            });
        }

        if (shopUrlInput) {
            shopUrlInput.addEventListener("input", this.checkShopUrlAvailability.bind(this));
        }
    }

    private handleGuest(event: Event): void {
        event.preventDefault();
    
        if (!omnis_ajax_object?.ajaxurl) {
            console.error('AJAX URL is undefined.');
            return;
        }
    
        const formData = new FormData();
        formData.append('action', 'set_guest');
    
        if (omnis_ajax_object?.nonce) {
            formData.append('security', omnis_ajax_object.nonce);
        }
    
        axios
            .post(omnis_ajax_object.ajaxurl, formData)
            .then((response) => {
                if (response.data.success) {
                    console.log('Guest mode activated:', response.data.data.message);
                    window.location.href = omnis_ajax_object.home_url;
                } else {
                    console.error('AJAX error:', response.data);
                    alert('Failed to activate guest mode.');
                }
            })
            .catch((error) => {
                console.error('Error:', error);
                alert('An error occurred while processing your request.');
            });
    }

    private toggleVendorFields(event: Event): void {
        const vendorFields = document.getElementById("vendor-fields");
        if (!vendorFields) return;

        const roleSelect = event.target as HTMLSelectElement;
        if (roleSelect.value === "seller") {
            vendorFields.style.display = "block";
            // Add required attribute to seller fields
            vendorFields.querySelectorAll('input').forEach((field) => {
                field.removeAttribute('disabled');
                field.setAttribute('required', 'required');
            });
        } else {
            vendorFields.style.display = "none";
            // Remove required attribute and clear seller fields
            vendorFields.querySelectorAll('input').forEach((field) => {
                field.setAttribute('disabled', 'disabled');
                field.removeAttribute('required');
                (field as HTMLInputElement).value = '';
                field.classList.remove('error');
            });
        }
    }

    private formatShopUrl(shopName: string): string {
        return shopName.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
    }

    private async checkShopUrlAvailability(event: Event): Promise<void> {
        const shopUrl = (event.target as HTMLInputElement).value;
        const urlAlert = document.getElementById("url-alert-msg") as HTMLElement;

        if (shopUrl) {
            const formData = new FormData();
            formData.append("action", "check_shop_url");
            formData.append("shopurl", shopUrl);
            formData.append("security", (window as any).omnis_ajax_object.nonce);

            try {
                const response = await fetch((window as any).omnis_ajax_object.ajaxurl, {
                    method: "POST",
                    body: formData,
                });
                const data = await response.json();

                if (data.success) {
                    urlAlert.textContent = "Available";
                    urlAlert.classList.add("text-success");
                } else {
                    urlAlert.textContent = "Not available";
                    urlAlert.classList.remove("text-success");
                }
            } catch (error) {
                console.error("Error checking shop URL:", error);
            }
        }
    }

    private handleNextStep(event: Event): void {
        event.preventDefault();
        const currentStep = (event.target as HTMLButtonElement).dataset.step;
    
        if (currentStep && this.validateStep(parseInt(currentStep))) {
            const nextStep = parseInt(currentStep) + 1;
            if (nextStep === 5) {
                this.submitForm(event);
            } else {
                this.moveToStep(nextStep);
            }
        }
    }

    private handlePreviousStep(event: Event): void {
        event.preventDefault();
        const currentStep = (event.target as HTMLButtonElement).dataset.step;
        if (currentStep) {
            this.moveToStep(parseInt(currentStep) - 1);
        }
    }

    private handleSkipStep(event: Event): void {
        event.preventDefault();
        const currentStep = (event.target as HTMLButtonElement).dataset.step;
        if (currentStep) {
            const nextStep = parseInt(currentStep) + 1;
            this.moveToStep(nextStep);
        }
    }

    private moveToStep(step: number): void {
        document.querySelectorAll(".step").forEach((stepElem) => stepElem.classList.remove("active"));
        document.getElementById(`step-${step}`)?.classList.add("active");
        this.updateProgressBar(step);
    }

    private updateProgressBar(step: number): void {
        const progressBar = document.getElementById("progress") as HTMLDivElement;
        progressBar.style.width = `${(step - 1) * 25}%`;
    }

    private validateStep(step: number): boolean {
        let isValid = true;
        const stepForm = document.getElementById(`step-${step}`);
        if (!stepForm) {
            console.warn(`Step form #step-${step} not found`);
            return false;
        }
    
        const requiredFields = stepForm.querySelectorAll("input[required], select[required]");
    
        requiredFields.forEach((field) => {
            // Определяем переменные
            let inputElement: HTMLInputElement | HTMLSelectElement;
            inputElement = field as HTMLInputElement | HTMLSelectElement;
    
            // Пропускаем валидацию полей vendor-fields, если роль не "seller"
            if (inputElement.closest('#vendor-fields')) {
                const roleSelect = document.getElementById("role") as HTMLSelectElement;
                if (roleSelect && roleSelect.value !== "seller") {
                    return;
                }
            }
    
            if (inputElement instanceof HTMLInputElement) {
                if (inputElement.type === "checkbox") {
                    if (!inputElement.checked) {
                        isValid = false;
                        inputElement.classList.add("error");
                        inputElement.addEventListener("change", () => inputElement.classList.remove("error"));
                    }
                } else if (inputElement.type === "radio") {
                    // Проверяем, если какая-либо радиокнопка с данным именем выбрана
                    const radioGroup = stepForm.querySelectorAll(`input[name="${inputElement.name}"]`) as NodeListOf<HTMLInputElement>;
                    const isChecked = Array.from(radioGroup).some((radio) => radio.checked);
                    if (!isChecked) {
                        isValid = false;
                        inputElement.classList.add("error");
                        radioGroup.forEach((radio) => {
                            radio.addEventListener("change", () => inputElement.classList.remove("error"));
                        });
                    }
                } else {
                    if (!inputElement.value) {
                        isValid = false;
                        inputElement.classList.add("error");
                        inputElement.addEventListener("input", () => inputElement.classList.remove("error"));
                    }
                }
            } else if (inputElement instanceof HTMLSelectElement) {
                if (!inputElement.value) {
                    isValid = false;
                    inputElement.classList.add("error");
                    inputElement.addEventListener("change", () => inputElement.classList.remove("error"));
                }
            }
        });
    
        if (!isValid) {
            alert("Please fill in all required fields before proceeding.");
        }
    
        return isValid;
    }
    

    private submitForm(event: Event): void {
        event.preventDefault();

        const formElement = document.getElementById("registration-form") as HTMLFormElement;
        const formData = new FormData(formElement);
        formData.append("action", "register_user_ajax");
        formData.append("security", (window as any).omnis_ajax_object.nonce);

        fetch((window as any).omnis_ajax_object.ajaxurl, {
            method: "POST",
            body: formData,
        })
        .then((response) => response.json())
        .then((data) => {
            if (data.success) {
                this.moveToStep(5);
                document.getElementById("profile-name")!.textContent = formData.get("name") as string;
                document.getElementById("profile-email")!.textContent = formData.get("email") as string;

                // Display profile picture if uploaded
                const avatarImg = document.getElementById("profile-avatar") as HTMLImageElement;
                const profilePhoto = (formElement.querySelector("#profile_photo") as HTMLInputElement).files?.[0];
                if (profilePhoto) {
                    const reader = new FileReader();
                    reader.onload = () => {
                        avatarImg.src = reader.result as string;
                    };
                    reader.readAsDataURL(profilePhoto);
                } else {
                    // Keep default avatar
                    avatarImg.src = "default_avatar.png";
                }

                if (data.data.redirect_url) { 
                    window.location.href = data.data.redirect_url;
                }
            } else {
                alert(data?.data?.message || "An unexpected error occurred");
            }
        });
    }
}

// Initialize registration functionality
document.addEventListener("DOMContentLoaded", () => Registration.getInstance());

export {};
