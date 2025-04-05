import axios, { AxiosError } from 'axios';
import $ from 'jquery';
import * as Parsley from 'parsleyjs';
import 'jquery-ui/ui/widgets/sortable';

interface AjaxObject {
    readonly ajaxurl: string;
    readonly nonce: string;
    readonly checkout_url: string;
    readonly cart_url: string;
    readonly date_set: Record<string, unknown>;
    readonly discount_period: Record<string, unknown>;
}

interface ProductResponse {
    success: boolean;
    data: {
        html: string;
        message?: string;
    };
}

declare const omnis_ajax_object: AjaxObject | undefined;

class ProductManagement {
    private static instance: ProductManagement;
    private pageNumber: number = 1;
    private isLoading: boolean = false;
    private showAll: boolean = false;
    private productType: string = "all";
    private readonly managementList: HTMLElement | null;
    private readonly managementProduct: HTMLElement | null;

    private constructor() {
        this.managementList = document.querySelector('.page-template-product-management-list');
        this.managementProduct = document.querySelector('.page-template-product-management-product');
        
        this.initializeComponents();
    }

    private initializeComponents(): void {
        if (this.managementList) {
            this.controlTypeList();
            this.controlListScroll();
        }

        if (this.managementProduct) {
            this.closeSubModal();
            this.controlListScroll();
            this.photoSort();
            this.submitForm();
            this.deletedProductModal();
        }
    }

    public static getInstance(): ProductManagement {
        if (!ProductManagement.instance) {
            ProductManagement.instance = new ProductManagement();
        }
        return ProductManagement.instance;
    }

    private deletedProduct(productId: string): void {
        console.log(productId);
    }

    private deletedProductModal(): void {
        const deletedProductModalBtnHeader = this.managementProduct?.querySelector<HTMLElement>(".deleted-product-modal-header-js");
        const deletedProductModalHeader = this.managementProduct?.querySelector<HTMLElement>(".deleted-product-modal-header");
        const deletedProductModalWrapper = this.managementProduct?.querySelector<HTMLElement>(".deleted-product-modal-wrapper");
        const closeSubModal = this.managementProduct?.querySelector<HTMLElement>(".close-sub-modal-deleted-product-js");
        const confirm = this.managementProduct?.querySelector<HTMLElement>(".confirm-deleted-product-js");
        const deletedProductModalBtns = this.managementProduct?.querySelector<HTMLElement>(".deleted-product-modal");
        const deletedProductModalConfirm = this.managementProduct?.querySelector<HTMLElement>(".deleted-product-modal-confirm");
        const closeDeletedSubModal = this.managementProduct?.querySelectorAll<HTMLElement>(".close-deleted-sub-modal-js");
        const submitDeletedProduct = this.managementProduct?.querySelector<HTMLElement>(".submit-deleted-product-js");


        if (!deletedProductModalBtnHeader || !deletedProductModalHeader || !deletedProductModalWrapper || !confirm || !deletedProductModalBtns || !closeSubModal || !deletedProductModalConfirm || !closeDeletedSubModal || !submitDeletedProduct) return;

        submitDeletedProduct.addEventListener("click", (event) => {
            event.preventDefault();

            const currentBtn = event.currentTarget as HTMLElement;
            const productId = currentBtn.dataset.id;

            if (!productId) return;

            this.deletedProduct(productId);

            deletedProductModalConfirm.style.display = "none";
            deletedProductModalWrapper.style.display = "none";
            deletedProductModalBtns.style.display = "flex";
        });

        closeDeletedSubModal.forEach(btn => {
            btn.addEventListener("click", (event) => {
                event.preventDefault();
                deletedProductModalConfirm.style.display = "none";
                deletedProductModalWrapper.style.display = "none";
                deletedProductModalBtns.style.display = "flex";
            });
        });

        deletedProductModalBtnHeader.addEventListener("click", (event) => {
            event.preventDefault();

            deletedProductModalHeader.style.display =  deletedProductModalHeader.style.display === "flex" ? "none" : "flex";

            if(event.target === deletedProductModalHeader){
                deletedProductModalWrapper.style.display = "flex";
            }
        });

        closeSubModal.addEventListener("click", (event) => {
            event.preventDefault();
            deletedProductModalWrapper.style.display = "none";
        });

        
        confirm.addEventListener("click", (event) => {
            event.preventDefault();
            deletedProductModalBtns.style.display = "none";
            deletedProductModalConfirm.style.display = "flex";
        });

        submitDeletedProduct.addEventListener("click", (event) => {
            event.preventDefault();
            deletedProductModalConfirm.style.display = "none";
            deletedProductModalWrapper.style.display = "none";
            deletedProductModalBtns.style.display = "flex";
        });
    }

    private async controlTypeList(): Promise<void> {
        const selects = this.managementList?.querySelectorAll<HTMLElement>(".type-selection-js");
    
        if (!selects?.length) return;
    
        const toggleButtonsState = (enabled: boolean): void => {
            selects.forEach(button => {
                button.style.pointerEvents = enabled ? "auto" : "none";
                button.style.opacity = enabled ? "1" : "0.5";
            });
        };
    
        selects.forEach(btn => {
            btn.addEventListener("click", async (event) => {
                const currentBtn = event.currentTarget as HTMLElement;
    
                if (currentBtn.classList.contains("active")) return;
    
                toggleButtonsState(false);
                selects.forEach(button => button.classList.remove("active"));
                currentBtn.classList.add("active");
    
                const sortType = currentBtn.dataset.sort;
                if (!sortType) return;
    
                this.productType = sortType;
                this.showAll = true;
                
                try {
                    const data = await this.getNewProductType(this.productType, "1");
                    const list = this.managementList?.querySelector<HTMLElement>(".user-list-product");
                    
                    if (!list) throw new Error("Product list container not found");
                    
                    list.innerHTML = data || "<div>אין מוצרים</div>";
                    this.pageNumber = 1;
                    this.showAll = false;
                } catch (error) {
                    console.error("Error while fetching products:", error);
                } finally {
                    toggleButtonsState(true);
                }
            });
        });
    }

    private controlListScroll(): void {
        const scrollHandler = async (): Promise<void> => {
            if (this.showAll || this.isLoading) return;

            const scrolledToBottom = window.innerHeight + window.scrollY >= document.body.offsetHeight - 100;
            
            if (scrolledToBottom) {
                this.isLoading = true;
                try {
                    const data = await this.getNewProductType(this.productType, (this.pageNumber + 1).toString());
                    const list = this.managementList?.querySelector<HTMLElement>(".user-list-product");
                    
                    if (data && list) {
                        list.insertAdjacentHTML("beforeend", data);
                        this.pageNumber++;
                        this.showAll = false;
                    } else {
                        this.showAll = true;
                    }
                } catch (error) {
                    console.error("Error while loading more products:", error);
                } finally {
                    this.isLoading = false;
                }
            }
        };

        window.addEventListener("scroll", scrollHandler, { passive: true });
    }

    private async getNewProductType(type: string, page: string): Promise<string> {
        if (!omnis_ajax_object?.ajaxurl || !omnis_ajax_object?.nonce) {
            throw new Error('Missing required AJAX configuration');
        }

        const formData = new FormData();
        formData.append('action', 'get_product_managementHTML');
        formData.append('type', type);
        formData.append('page', page);
        formData.append('security', omnis_ajax_object.nonce);

        try {
            const response = await axios.post<ProductResponse>(
                omnis_ajax_object.ajaxurl,
                formData
            );

            if (response.data.success) {
                return response.data.data.html;
            }
            
            throw new Error(response.data.data.message || 'Unknown error occurred');
        } catch (error) {
            const errorMessage = error instanceof AxiosError 
                ? error.response?.data?.message || error.message
                : 'An unexpected error occurred';
            console.error('Error:', errorMessage);
            throw error;
        }
    }

    private closeSubModal(): void {
        const closeBtns = this.managementProduct?.querySelectorAll<HTMLElement>(".close-sub-modal-js");

        if (!closeBtns?.length) return;

        closeBtns.forEach(btn => {
            btn.addEventListener("click", (event) => {
                event.preventDefault();
                const subModal = (event.currentTarget as HTMLElement).closest<HTMLElement>(".sub-modal-drop-down");
                if (subModal) {
                    subModal.style.display = "none";
                }
            });
        });
    }

    private photoSort(): void {
        const managementForm = document.querySelector<HTMLFormElement>("#management-product-main-form");
        const paginationList = document.querySelector<HTMLElement>('.pagination-list');

        if (!paginationList || !managementForm) return;

        $(paginationList).sortable({
            update: () => {
                const numbers = paginationList.querySelectorAll<HTMLElement>('.ui-sortable-handle .num');
                numbers.forEach((num, index) => {
                    num.textContent = (index + 1).toString();
                });

                managementForm.querySelectorAll('input.selectedIMG').forEach((input) => {
                    input.remove();
                });

                let count = 1;
                paginationList.querySelectorAll<HTMLElement>('.ui-sortable-handle img').forEach((img) => {
                    const input = document.createElement('input');
                    input.setAttribute('src', img.getAttribute('src') || '');
                    input.setAttribute('name', `selectedIMG-${count.toString()}`);
                    input.setAttribute('count', count.toString() || '');
                    input.value = img.getAttribute('data-id') || '';
                    input.setAttribute('hidden', "");
                    input.classList.add('selectedIMG');
                    ++count;
                    if (managementForm) managementForm.appendChild(input);
                });
            },
        });

        const topImg = document.querySelector<HTMLElement>('.selected-img .img-wrap img');
        const imgList = paginationList.querySelectorAll<HTMLElement>('img');
        if (!topImg || !imgList) return;
        imgList.forEach((img) => {
            img.addEventListener('click', (event) => {
                let img = event.currentTarget as HTMLElement;
                let src = img.getAttribute('src');
                topImg.setAttribute('src', src || '');
                let id = img.getAttribute('data-id');
                topImg.setAttribute('data-id', id || '');
            });
        });
    }

    private submitForm(): void {
        const managementForm = document.querySelector<HTMLFormElement>("#management-product-main-form");
        
        if (!managementForm) return;

        managementForm.addEventListener("submit", (event) => {
            event.preventDefault();
            const currentForm = event.currentTarget as HTMLFormElement;
            const formData = new FormData(currentForm);
            console.log(...formData);
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    ProductManagement.getInstance();
});

export {};
