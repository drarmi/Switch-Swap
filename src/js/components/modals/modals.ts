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


class ModalsCustomEvent {
    private static instance: ModalsCustomEvent;
    private modalMustLog: HTMLElement | null;

    private constructor() {
        this.modalMustLog = document.querySelector('.modal-you-must-log-in-wrap') ?? null;
        this.initEvent();
    }

    public static getInstance(): ModalsCustomEvent {
        if (!this.instance) {
            this.instance = new this();
        }

        return this.instance;
    }

    private initEvent() {
        if (this.modalMustLog) {
           this.controlModalMustLog();
        }
    }

    private controlModalMustLog(): void {
        let closeModalBTM = document.querySelector('.close-must-log-in-modal-js') as HTMLElement;
        if (closeModalBTM) {
            closeModalBTM.addEventListener('click', () => {
                if (this.modalMustLog) {
                    this.modalMustLog.style.display = 'none';
                    document.body.style.overflow = 'auto';
                }
            });
        }
    

        const modalStatus = localStorage.getItem('ModalMustLogShow');
        const parsedData = modalStatus ? JSON.parse(modalStatus) : null;
    
        if (parsedData && parsedData.expires < Date.now()) {
            localStorage.removeItem('ModalMustLogShow');
        }
    
        const userLogin = omnis_ajax_object?.userLogin || '';
        if (!userLogin && !parsedData && this.modalMustLog) {
            this.modalMustLog.style.display = 'block';
            document.body.style.overflow = 'hidden';
    
            const expirationTime = Date.now() + 24 * 60 * 60 * 1000;
    
            localStorage.setItem('ModalMustLogShow', JSON.stringify({
                value: true,
                expires: expirationTime
            }));
        }
    }
}


document.addEventListener('DOMContentLoaded', () => {
    ModalsCustomEvent.getInstance();
});

export {};