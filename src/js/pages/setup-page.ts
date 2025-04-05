import axios from 'axios';
import $ from 'jquery';
import 'parsleyjs';

interface AjaxObject {
    ajaxurl: string;
    home_url: string;
    nonce: string;
}

declare const omnis_ajax_object: AjaxObject | undefined;

class SetupPage {
    private static instance: SetupPage;
    private setupPage: HTMLElement;
    private parsleyInstance: any = null;
    private hasImage: boolean = false;
    private currentStreamEditor: MediaStream | null = null;
    private imageData: any = null;
    

    private constructor() {
        this.setupPage = (document.querySelector('.main-container.setup') as HTMLElement) || null;

        if (this.setupPage) {
            this.closeSetup();
            this.setupNav();
            this.initParsley();
            this.textareaControl();
            this.formSectionControlBottomBtn();
            this.inputControl();
            this.modalControl();
            this.editLogo();
            this.closeModal();
            this.deletedLogo();
            this.initCamera();
            this.initCloseStreamBtn();
        }
    }

    public static getInstance(): SetupPage {
        if (!this.instance) {
            this.instance = new this();
        }
        return this.instance;
    }

    private setupNav(): void {
        let sectionList = this.setupPage.querySelectorAll('.modal-section') as NodeListOf<HTMLElement> | null;
        let navBtnList = this.setupPage.querySelectorAll('.go-to button') as NodeListOf<HTMLElement> | null;

        if (sectionList && navBtnList) {
            navBtnList.forEach((btnElement) => {
                btnElement.addEventListener('click', (event) => {
                    let currentBtb = event.currentTarget as HTMLButtonElement | null;
                    if (currentBtb && sectionList) {
                        let goTo = currentBtb.dataset.goTo;

                        sectionList.forEach((sectionElement) => {
                            if (sectionElement.dataset.setupSection == goTo) {
                                sectionElement.style.display = 'flex';
                            } else {
                                sectionElement.style.display = 'none';
                            }
                        });
                    }
                });
            });
        }

        this.changeFormSectionMinus();
    }

    private closeSetup() {
        let closeList = this.setupPage.querySelectorAll('.close_setup') as NodeListOf<HTMLElement> | null;
        if (closeList) {
            closeList.forEach((closeElement) => {
                closeElement.addEventListener('click', () => {
                    if (omnis_ajax_object?.home_url) {
                        window.location.replace(omnis_ajax_object?.home_url);
                    }
                });
            });
        }
    }

    private initParsley() {
        let formDetails = $('.setup-form.modal-section form');
        if (formDetails.length > 0) {
            this.parsleyInstance = formDetails.parsley();
        }
    }

    private textareaControl() {
        let textarea = this.setupPage.querySelector('.store-biography textarea') as HTMLTextAreaElement | null;
        let storeBiography = this.setupPage.querySelector('.store-biography') as HTMLElement | null;
        let detailsCount = this.setupPage.querySelector('.details-count-current') as HTMLElement | null;

        if (textarea && storeBiography) {
            textarea.addEventListener('input', (event) => {
                let currentCount = (event.currentTarget as HTMLTextAreaElement).value.length;
                if (currentCount && detailsCount && storeBiography) {
                    storeBiography.classList.add('with-text');
                    detailsCount.textContent = currentCount.toString();
                } else {
                    if (storeBiography) storeBiography.classList.remove('with-text');
                }
            });
        }
    }

    private inputControl() {
        let input = this.setupPage.querySelector('.name_store input') as HTMLInputElement | null;
        let logoSectionName = this.setupPage.querySelector('.logo-section .store-name') as HTMLElement | null;
        let logoSectionName2 = this.setupPage.querySelector('.top-content .store-name') as HTMLElement | null;
        let logoSectionConfigName = this.setupPage.querySelector(
            '.logo-section-config .store-name'
        ) as HTMLElement | null;

        if (input && logoSectionName && logoSectionConfigName) {
            input.addEventListener('input', (event) => {
                let value = (event.currentTarget as HTMLTextAreaElement).value;
                if (logoSectionName) logoSectionName.textContent = value.toString();
                if (logoSectionName2) logoSectionName2.textContent = value.toString();
                if (logoSectionConfigName) logoSectionConfigName.textContent = value.toString();
            });
        }
    }

    private formSectionControlBottomBtn() {
        let btn = this.setupPage.querySelector('.form-section-js') as HTMLElement | null;

        if (btn) {
            btn.addEventListener('click', (event) => {
                this.changeFormSectionPlus();
            });
        }
    }

    private changeFormSectionPlus() {
        let btnBottom = this.setupPage.querySelector('.form-section-js') as HTMLTextAreaElement | null;
        let sections = this.setupPage.querySelectorAll(
            '.setup-form.modal-section form section'
        ) as NodeListOf<HTMLElement> | null;
        let returnTo = this.setupPage.querySelector('.return_to.go-to') as HTMLTextAreaElement | null;
        let sectionList = this.setupPage.querySelectorAll('.modal-section') as NodeListOf<HTMLElement> | null;

        if (btnBottom && sections && returnTo) {
            if (btnBottom && btnBottom.dataset.goTo == '2' && !this.parsleyInstance.isValid()) {
                return;
            }

            if (btnBottom && btnBottom.dataset.goTo == '3' && !this.hasImage) {
                return;
            }

            if (Number(btnBottom.dataset.goTo) == 2 && this.hasImage && Number(btnBottom.dataset.goTo) != 4) {
                if (sections) {
                    sections.forEach((section) => {
                        if ('3' == section.dataset.section) {
                            section.style.display = 'flex';
                        } else {
                            section.style.display = 'none';
                        }
                    });
                }
            } else if (Number(btnBottom.dataset.goTo) != 4) {
                if (sections) {
                    sections.forEach((section) => {
                        if (btnBottom && Number(btnBottom.dataset.goTo).toString() == section.dataset.section) {
                            section.style.display = 'flex';
                        } else {
                            section.style.display = 'none';
                        }
                    });
                }
            } else if (
                Number(btnBottom.dataset.goTo) == 4 &&
                this.hasImage &&
                this.parsleyInstance.isValid() &&
                sectionList
            ) {
                let form = this.setupPage.querySelector('.setup-form.modal-section form') as HTMLFormElement | null;
                if (form) {
                    let formData = new FormData(form);
                    formData.append("photo", this.imageData);
                    console.log(...formData);
                }

                sectionList.forEach((sectionElement) => {
                    if (sectionElement.dataset.setupSection == 'setup-final') {
                        sectionElement.style.display = 'block';
                    } else {
                        sectionElement.style.display = 'none';
                    }
                });
            }

            if (btnBottom && sections && sections.length >= Number(btnBottom.dataset.goTo)) {
                if (Number(btnBottom.dataset.goTo) == 2 && this.hasImage) {
                    btnBottom.dataset.goTo = (Number(btnBottom.dataset.goTo) + 2).toString();
                } else {
                    btnBottom.dataset.goTo = (Number(btnBottom.dataset.goTo) + 1).toString();
                }
            }
            if (returnTo && sections && sections.length > Number(returnTo.dataset.step)) {
                if (Number(btnBottom.dataset.goTo) == 2 && this.hasImage) {
                    returnTo.dataset.step = (Number(returnTo.dataset.step) + 2).toString();
                } else {
                    returnTo.dataset.step = (Number(returnTo.dataset.step) + 1).toString();
                }
            }
        }
    }

    private changeFormSectionMinus() {
        let sectionList = this.setupPage.querySelectorAll('.modal-section') as NodeListOf<HTMLElement> | null;
        let returnBtnList = this.setupPage.querySelectorAll('.return_to.go-to') as NodeListOf<HTMLElement> | null;
        let sectionsForm = this.setupPage.querySelectorAll(
            '.setup-form.modal-section form section'
        ) as NodeListOf<HTMLElement> | null;

        if (sectionList && returnBtnList) {
            returnBtnList.forEach((btnElement) => {
                btnElement.addEventListener('click', (event) => {
                    let currentBtb = event.currentTarget as HTMLButtonElement | null;
                    if (currentBtb) {
                        let goTo = currentBtb.dataset.goTo;
                        let step = currentBtb.dataset.step;
                        if (step == '1' && sectionList) {
                            sectionList.forEach((sectionElement) => {
                                if (sectionElement.dataset.setupSection == goTo) {
                                    sectionElement.style.display = 'flex';
                                } else {
                                    sectionElement.style.display = 'none';
                                }
                            });
                        } else {
                            if (sectionsForm) {
                                let btn = this.setupPage.querySelector(
                                    '.form-section-js'
                                ) as HTMLTextAreaElement | null;

                                if (Number(currentBtb.dataset.step) == 3 && this.hasImage) {
                                    sectionsForm.forEach((section) => {
                                        if ('1' == section.dataset.section && currentBtb && btn) {
                                            btn.dataset.goTo = (Number(step) - 1).toString();
                                            currentBtb.dataset.step = (Number(step) - 2).toString();
                                            section.style.display = 'flex';
                                        } else {
                                            section.style.display = 'none';
                                        }
                                    });
                                } else {
                                    sectionsForm.forEach((section) => {
                                        if (
                                            currentBtb &&
                                            btn &&
                                            (Number(step) - 1).toString() == section.dataset.section
                                        ) {
                                            btn.dataset.goTo = Number(step).toString();
                                            currentBtb.dataset.step = (Number(step) - 1).toString();
                                            section.style.display = 'flex';
                                        } else {
                                            section.style.display = 'none';
                                        }
                                    });
                                }
                            }
                        }
                    }
                });
            });
        }
    }

    private modalControl() {
        let btnLogo = this.setupPage.querySelector(
            '.setup-form.modal-section .logo-section .logo-border'
        ) as HTMLElement | null;
        if (btnLogo) {
            btnLogo.addEventListener('click', (event) => {
                this.showModal(event);
            });
        }

        let btnSvg = this.setupPage.querySelector(
            '.setup-form.modal-section .logo-section-config .logo-border svg'
        ) as HTMLElement | null;
        if (btnSvg) {
            btnSvg.addEventListener('click', (event) => {
                this.showModal(event);
            });
        }
    }

    private showModal(event: Event) {
        let target = event.currentTarget as HTMLElement;
        let section = target.closest('section') as HTMLElement | null;
        if (!section) return;

        let modal = section.querySelector('.logo-modal-wrapper') as HTMLElement | null;
        if (modal) {
            modal.style.display = 'flex';
        }
    }
    private closeModalEvent(event: Event | null) {
        if (event) {
            let target = event.currentTarget as HTMLElement;

            let section = target.closest('section') as HTMLElement | null;
            if (!section) return;

            let modal = section.querySelector('.logo-modal-wrapper') as HTMLElement | null;
            if (modal) {
                modal.style.display = 'none';
            }
        } else {
            let modals = this.setupPage.querySelectorAll('.logo-modal-wrapper') as NodeListOf<HTMLElement> | null;
            if (modals) {
                modals.forEach((modal) => {
                    modal.style.display = 'none';
                });
            }
        }
    }

    private editLogo() {
        let btns = this.setupPage.querySelectorAll(
            '.setup-form.modal-section form .control-btn.device'
        ) as NodeListOf<HTMLElement> | null;

        let fileInput = this.setupPage.querySelector(
            '.setup-form.modal-section form input[type="file"]'
        ) as HTMLInputElement | null;

        let img = this.setupPage.querySelector(
            '.setup-form.modal-section .logo-section-config .logo-border img'
        ) as HTMLImageElement | null;
        let img2 = this.setupPage.querySelector('.logo-final img') as HTMLImageElement | null;

        if (btns && fileInput && img) {
            btns.forEach((btn) => {
                btn.addEventListener('click', (event) => {
                    if (fileInput) fileInput.click();
                });
            });

            fileInput.addEventListener('change', (event) => {
                let target = event.target as HTMLInputElement;
                if (img2 && img && target.files && target.files[0]) {
                    let file = target.files[0];
                    img.src = URL.createObjectURL(file);
                    img2.src = URL.createObjectURL(file);
                    this.closeModalEvent(null);
                    this.hasImage = true;
                    this.changeFormSectionPlus();
                }
            });
        }
    }

    private closeModal() {
        let closeBtns = this.setupPage.querySelectorAll(
            '.setup-form.modal-section .btn-section .close-btn'
        ) as NodeListOf<HTMLElement>;

        closeBtns.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                let target = event.currentTarget as HTMLElement;
                let modal = target.closest('.logo-modal-wrapper') as HTMLElement | null;
                if (modal) {
                    modal.style.display = 'none';
                }
            });
        });
    }

    private deletedLogo() {
        let deletedBtns = this.setupPage.querySelectorAll(
            '.setup-form.modal-section .btn-section .control-btn.remove'
        ) as NodeListOf<HTMLElement>;
        let fileInput = this.setupPage.querySelector(
            '.setup-form.modal-section form input[type="file"]'
        ) as HTMLInputElement | null;
        let img = this.setupPage.querySelector(
            '.setup-form.modal-section .logo-section-config .logo-border img'
        ) as HTMLImageElement | null;
        let sectionsForm = this.setupPage.querySelectorAll(
            '.setup-form.modal-section form section'
        ) as NodeListOf<HTMLElement> | null;
        let btnBottom = this.setupPage.querySelector('.form-section-js') as HTMLTextAreaElement | null;
        let returnBtnList = this.setupPage.querySelectorAll('.return_to.go-to') as NodeListOf<HTMLElement> | null;

        if (deletedBtns) {
            deletedBtns.forEach((btn) => {
                btn.addEventListener('click', (event) => {
                    if (img && fileInput) {
                        img.src = '';
                        fileInput.value = '';
                        this.hasImage = false;

                        if (sectionsForm && btn) {
                            sectionsForm.forEach((section) => {
                                if (returnBtnList && btnBottom && '2' == section.dataset.section) {
                                    btnBottom.dataset.goTo = '3';
                                    returnBtnList.forEach((returnBtn) => {
                                        returnBtn.dataset.step = '2';
                                    });
                                    section.style.display = 'flex';
                                } else {
                                    section.style.display = 'none';
                                }
                            });
                        }
                        this.closeModalEvent(event);
                    }
                });
            });
        }
    }

    private initCamera(){
        let btns = this.setupPage.querySelectorAll(".control-btn.camera") as NodeListOf<HTMLElement> | null;
        btns?.forEach(btn => {
            btn.addEventListener("click", (event : Event) => {
                let currentBtn = event.currentTarget as HTMLElement;
                let parent = currentBtn.closest(".logo-modal-wrapper") as HTMLElement | null;
                if(parent){
                    this.showCameraEditor(parent);
                }
            })
        })
    
    }

    public showCameraEditor(parent: HTMLElement) {
        const self = this;
        const controlElement = document.querySelector('.control-element') as HTMLButtonElement | null;
        const cameraParentWrap = parent.querySelector('.camera-wrap-editor') as HTMLElement | null;
        const captureBtn = parent.querySelector('#captureBtn-editor') as HTMLElement | null;
        const canvas = parent.querySelector('.canvas-editor') as HTMLCanvasElement | null;
        const snapshot = parent.querySelector('#snapshot-editor') as HTMLImageElement | null;
        const saveBtn = parent.querySelector('#saveBtn-editor') as HTMLElement | null;

        if (cameraParentWrap && captureBtn && canvas && snapshot && saveBtn) {
            if (cameraParentWrap.style.display === 'none') {
                if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                    console.log(cameraParentWrap.style.display);
                    
                    navigator.mediaDevices
                        .getUserMedia({ video: true })
                        .then((stream) => {
                            const video = parent.querySelector('#video-editor') as HTMLVideoElement | null;
                            if (video && cameraParentWrap) {
                                self.currentStreamEditor = stream;
                                cameraParentWrap.style.display = 'block';
                                //controlElement.style.display = 'none';
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
                    const video = parent.querySelector('#video-editor') as HTMLVideoElement | null;


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
                self.closeStream(parent);
            }
        }
    }

    public initCloseStreamBtn(){
        let btns = this.setupPage.querySelectorAll(".close-stream") as NodeListOf<HTMLElement> | null;
        const self = this;
        if(btns){
            btns.forEach(btn => {
                btn.addEventListener("click", (event) => {
                    let currentBtn = event.currentTarget as HTMLElement;
                    let parent = currentBtn.closest(".logo-modal-wrapper") as HTMLElement;
                    self.closeStream(parent);
                })
            })
        }
    }

    public closeStream(parent: HTMLElement){
        const self = this;
        const cameraParentWrap = parent.querySelector('.camera-wrap-editor') as HTMLElement | null;
        const snapshot = parent.querySelector('#snapshot-editor') as HTMLImageElement | null;
        const saveBtn = parent.querySelector('#saveBtn-editor') as HTMLElement | null;

        if (self.currentStreamEditor && cameraParentWrap && snapshot && saveBtn) {
            const tracks = self.currentStreamEditor.getTracks();
            tracks.forEach((track) => track.stop());
            self.currentStreamEditor = null;

            cameraParentWrap.style.display = 'none';
            snapshot.style.display = 'none';
            saveBtn.style.display = 'none';
        }
    }

    public savePhotoEditor(event: Event) {
        event.preventDefault();
        let currentBtn = event.currentTarget as HTMLElement;
        let parent = currentBtn.closest(".logo-modal-wrapper") as HTMLElement;
        const self = this;
        const canvas = parent.querySelector('.canvas-editor') as HTMLCanvasElement | null;
        let fileInput = this.setupPage.querySelector(
            '.setup-form.modal-section form input[type="file"]'
        ) as HTMLInputElement | null;

        let img = this.setupPage.querySelector(
            '.setup-form.modal-section .logo-section-config .logo-border img'
        ) as HTMLImageElement | null;
        let img2 = this.setupPage.querySelector('.logo-final img') as HTMLImageElement | null;


        if (canvas && img && img2 && fileInput) {
            self.imageData = canvas.toDataURL('image/png');
            self.closeStream(parent);
            img.src = self.imageData;
            img2.src = self.imageData;
            this.closeModalEvent(null);
            this.hasImage = true;
            this.changeFormSectionPlus();  
            fileInput.value = "";
        }
    }
}

// Initialize the SetupPage functionality
SetupPage.getInstance();

export {};
