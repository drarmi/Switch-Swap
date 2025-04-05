import axios from 'axios';
import $ from '../../../node_modules/jquery';
import 'parsleyjs';

interface AjaxObject {
    ajaxurl: string;
    userLogin: string;
    nonce: string;
}

declare const omnis_ajax_object: AjaxObject | undefined;

class LostPassword {
    private static instance: LostPassword;

    private pageLostPass: HTMLElement | null;
    private pageRestorePass: HTMLElement | null;
    private submitBTNlost: HTMLElement | null;
    private submitBTNrestore: HTMLElement | null;
    private showPassBTN: NodeListOf<SVGSVGElement> | null;
    private hidePassBTN: NodeListOf<SVGSVGElement> | null;
    private inputRestorePass: NodeListOf<HTMLInputElement> | null;
    private restoreFormParsley: any;

    private constructor() {
        this.pageLostPass = document.querySelector('.page-template-lost-password');
        this.pageRestorePass = document.querySelector('.page-template-restore-password');

        if (this.pageRestorePass) {
            const restorePasswordForm = this.pageRestorePass.querySelector('#custom-restore-password-form');
            if (restorePasswordForm) {
                this.restoreFormParsley = $(restorePasswordForm).parsley();
            }
        }

        this.submitBTNlost = this.pageLostPass
            ? this.pageLostPass.querySelector("button[type='submit'][name='lost-password']")
            : null;
        this.submitBTNrestore = this.pageRestorePass
            ? this.pageRestorePass.querySelector("button[type='submit'][name='reset-password']")
            : null;
        this.inputRestorePass = this.pageRestorePass
            ? this.pageRestorePass.querySelectorAll("input[type='password'][name='pwd']")
            : null;
        this.hidePassBTN = this.pageRestorePass ? this.pageRestorePass.querySelectorAll('svg.hide') : null;
        this.showPassBTN = this.pageRestorePass ? this.pageRestorePass.querySelectorAll('svg.show') : null;

        if (this.submitBTNlost) {
            this.submitBTNlost.addEventListener('click', this.handleSubmit.bind(this));
        }

        if (this.submitBTNrestore) {
            this.submitBTNrestore.addEventListener('click', this.handleSubmitRestore.bind(this));
        }

        if (this.inputRestorePass) {
            this.inputRestorePass.forEach((input) => {
                input.addEventListener('keyup', this.controlInput);
            });
        }

        if (this.showPassBTN) {
            this.showPassBTN.forEach((btn) => {
                btn.addEventListener('click', (event: Event) => this.controlShowPassBTN(event));
            });
        }
        if (this.hidePassBTN) {
            this.hidePassBTN.forEach((btn) => {
                btn.addEventListener('click', (event: Event) => this.controlShowPassBTN(event));
            });
        }
    }

    public static getInstance(): LostPassword {
        if (!this.instance) {
            this.instance = new this();
        }
        return this.instance;
    }

    private handleSubmit(event: Event): void {
        event.preventDefault();

        const form = (event.target as HTMLElement).closest('form') as HTMLFormElement;
        if (!form) return;

        const formData = new FormData(form);
        formData.append('action', 'reset_password');

        const lostPasswordForm = document.querySelector('.lost-pasword-form') as HTMLElement | null;
        const lostPasswordSend = document.querySelector('.lost-pasword-send') as HTMLElement | null;
        const submitBTN = form.querySelector("button[type='submit']") as HTMLButtonElement;
        const responseMessage = form.querySelector('#response-message') as HTMLElement;

        // Disable the submit button to prevent multiple submissions
        if (submitBTN) submitBTN.disabled = true;

        if (omnis_ajax_object?.ajaxurl) {
            axios
                .post(omnis_ajax_object.ajaxurl, formData)
                .then((response) => {
                    if (response.data.success) {
                        // Ensure elements exist before trying to modify their display
                        if (lostPasswordForm && lostPasswordSend) {
                            lostPasswordForm.style.display = 'none';
                            lostPasswordSend.style.display = 'block';
                        }
                    } else {
                        let message_error = response.data.message ? response.data.message : response.data.data;
                        responseMessage.textContent = message_error;
                        responseMessage.style.display = 'block';
                        setTimeout(function () {
                            responseMessage.textContent = '';
                            responseMessage.style.display = 'none';
                        }, 4000);
                    }
                })
                .catch((error) => {
                    console.error('Error resetting password:', error);
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

    private resetPass(form: HTMLFormElement){
        const formData = new FormData(form);
        formData.append('action', 'custom_reset_password');
        const wrap = form.closest('.lost-password-wrap') as HTMLElement;
        const lostPasswordForm = wrap.querySelector('.restore-pasword-form') as HTMLElement | null;
        const lostPasswordSend = wrap.querySelector('.restore-pasword-done') as HTMLElement | null;

        const submitBTN = form.querySelector("button[type='submit']") as HTMLButtonElement;
        const responseMessage = form.querySelector('#response-message') as HTMLElement;

        if (submitBTN) submitBTN.disabled = true;

        if (omnis_ajax_object?.ajaxurl) {
            axios
                .post(omnis_ajax_object.ajaxurl, formData)
                .then((response) => {
                    if (response.data.success) {
                        if (lostPasswordForm && lostPasswordSend) {
                            lostPasswordForm.style.display = 'none';
                            lostPasswordSend.style.display = 'flex';
                        }
                    } else {
                        const message_error =
                            response.data.data.message || response.data.data || 'Unknown error occurred';
                        if (responseMessage) {
                            responseMessage.textContent = message_error;
                            responseMessage.style.display = 'block';
                            setTimeout(() => {
                                responseMessage.textContent = '';
                                responseMessage.style.display = 'none';
                            }, 15000);
                        }
                    }
                })
                .catch((error) => {
                    console.error('Error resetting password:', error);
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

    private changePass(form: HTMLFormElement){
        const formData = new FormData(form);

        if(omnis_ajax_object?.nonce){
            formData.append('security', omnis_ajax_object?.nonce);
        }
        
        formData.append('action', 'custom_change_password_no_old');
        
        const wrap = form.closest('.lost-password-wrap') as HTMLElement;
        const lostPasswordForm = wrap.querySelector('.restore-pasword-form') as HTMLElement | null;
        const lostPasswordSend = wrap.querySelector('.restore-pasword-done') as HTMLElement | null;

        const submitBTN = form.querySelector("button[type='submit']") as HTMLButtonElement;
        const responseMessage = form.querySelector('#response-message') as HTMLElement;

        if (submitBTN) submitBTN.disabled = true;

        if (omnis_ajax_object?.ajaxurl) {
            axios
                .post(omnis_ajax_object.ajaxurl, formData)
                .then((response) => {
                    if (response.data.success) {
                        if (lostPasswordForm && lostPasswordSend) {
                            lostPasswordForm.style.display = 'none';
                            lostPasswordSend.style.display = 'flex';
                        }
                    } else {
                        const message_error =
                            response.data.data.message || response.data.data || 'Unknown error occurred';
                        if (responseMessage) {
                            responseMessage.textContent = message_error;
                            responseMessage.style.display = 'block';
                            setTimeout(() => {
                                responseMessage.textContent = '';
                                responseMessage.style.display = 'none';
                            }, 15000);
                        }
                    }
                })
                .catch((error) => {
                    console.error('Error resetting password:', error);
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

    private handleSubmitRestore(event: Event): void {
        event.preventDefault();

        const userLogin = omnis_ajax_object?.userLogin || '';

        const form = (event.target as HTMLElement).closest('form') as HTMLFormElement;
        if (!form) return;
        this.restoreFormParsley.validate();
        if (!this.restoreFormParsley.isValid()) {
            console.log('No valid');
            return;
        }

        if (userLogin) {
            this.changePass(form);
        } else {
            this.resetPass(form);
        }
    }

    private controlShowPassBTN(event: Event): void {
        const target = event.target as SVGSVGElement;
        const parent = target.closest('.auth-field');

        if (!parent) return;

        const show = parent.querySelector('.show') as SVGSVGElement | null;
        const hide = parent.querySelector('.hide') as SVGSVGElement | null;
        const input = parent.querySelector('input[type="password"], input[type="text"]') as HTMLInputElement | null;

        if (!input) return;

        if (show && hide) {
            if (show.style.display === 'none') {
                show.style.display = 'block';
                hide.style.display = 'none';
                input.type = 'password';
            } else {
                show.style.display = 'none';
                hide.style.display = 'block';
                input.type = 'text';
            }
        }
    }

    private controlInput(event: Event): void {
        const target = event.target as HTMLInputElement;
        const parent = target.closest('.auth-field');

        if (parent) {
            const inputInfo = parent.querySelector('.input-info');
            if (inputInfo) {
                const span = inputInfo.querySelector('span') as HTMLElement | null;
                const svgs = inputInfo.querySelectorAll('svg') as NodeListOf<SVGSVGElement>;
                const svgShow = inputInfo.querySelector('svg.show') as SVGSVGElement;
                const svgHide = inputInfo.querySelector('svg.hide') as SVGSVGElement;
                if (target.value) {
                    if (span) span.style.display = 'none';
                    if (svgHide?.style.display === 'none') {
                        svgShow.style.display = 'block';
                    }
                } else {
                    if (span) span.style.display = 'block';

                    svgs.forEach((svg) => {
                        svg.style.display = 'none';
                    });
                }
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    LostPassword.getInstance();
});

export {};
