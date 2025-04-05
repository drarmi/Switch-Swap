import axios from 'axios';

interface AjaxObject {
    ajaxurl: string;
    nonce: string;
    checkout_url: string;
    cart_url: string;
    date_set: object;
    discount_period: object;
    home_url: string;
}

declare const omnis_ajax_object: AjaxObject | undefined;


class BtnCustomEvent {
    private static instance: BtnCustomEvent;

    private likes: NodeListOf<HTMLElement> | null;
    private previousPageBtn: NodeListOf<HTMLElement> | null;

    private constructor() {
        this.likes = document.querySelectorAll('.like-favorites-js') ?? null;
        this.previousPageBtn = document.querySelectorAll('.previousPage-js') ?? null;
        this.initEvent();
    }

    public static getInstance(): BtnCustomEvent {
        if (!this.instance) {
            this.instance = new this();
        }

        return this.instance;
    }

    private initEvent() {
        if (this.likes) {
            this.likes.forEach(like => {
                like.addEventListener('click', this.toggleLike);
            })
        }

        if(this.previousPageBtn){
            this.previousPageBtn.forEach(btn => {
                btn.addEventListener('click', this.previousPage);
            })
        }
    }

    private toggleLike(event: Event): void {
        event.preventDefault();

        let likeElement = event?.currentTarget as HTMLElement | null;
        if (!likeElement) {
            console.error('Like button element is not found.');
            return;
        }
        // Retrieving the product ID
        let ID = likeElement.getAttribute('data-product-id');
        if (!ID) {
            console.error('Product ID is missing.');
            return;
        }
        const formData = new FormData();
        formData.append('action', 'init_like');
        formData.append('ID', ID);
        formData.append('security', omnis_ajax_object?.nonce || '');

        if (likeElement) likeElement.style.pointerEvents = 'none';

        // Ensure ajaxurl is available
        if (omnis_ajax_object?.ajaxurl) {
            axios
                .post(omnis_ajax_object.ajaxurl, formData)
                .then((response) => {
                    if (response.data.success) {
                        if(likeElement && likeElement.classList.contains("favorites-page-js") && !response.data.data.active){
                            let currentProduct = likeElement.closest(".favorite-products li.favorite-product ") as HTMLElement;
                            currentProduct.remove();
                        }else{
                            response.data.data.active
                            ? likeElement?.classList.add('active')
                            : likeElement?.classList.remove('active');
                        }
                    } else {
                        let error = response.data.message ? response.data.message : response.data.data;
                        console.error('Error:', error);
                    }
                })
                .catch((error) => {
                    console.error('Error:', error);
                })
                .finally(() => {
                    if (likeElement) likeElement.style.pointerEvents = 'auto';
                });
        } else {
            console.error('ajaxUrl is undefined or invalid.');
            alert('An error occurred: Unable to send the request.');
        }
    }


    private previousPage(): void {
        const homeUrl = omnis_ajax_object?.home_url || '';
        
        if(homeUrl){
            const referrer = document.referrer;
            if (referrer.includes(homeUrl)) {
                window.history.back();
            } else {
                window.location.href = homeUrl;
            }
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    BtnCustomEvent.getInstance();
});

export {};