import axios from 'axios';
import $ from 'jquery';

interface AjaxObject {
  ajaxurl: string;
}

declare const omnis_ajax_object: AjaxObject;

class OmnisSearch {
  private static instance: OmnisSearch;
  private searchInput: HTMLInputElement | null;
  private resultsContainer: HTMLElement | null;
  private noResultsElement: HTMLElement | null;
  private searchSummary: HTMLElement | null;
  private historyItems: NodeListOf<HTMLButtonElement> | null;
  private placeholderInterval: number | undefined;
  private originalPlaceholder: string; 
  private sectionTitle: HTMLElement | null;
  private pageWrapper: HTMLElement | null;
  private searchTimeout: number | undefined;

  private currentPage: number = 1;
  private currentSearchTerm: string = '';
  private isLoading: boolean = false;
  private maxPages: number = 1;

  private constructor() {
    this.pageWrapper = document.querySelector('.search-page-wrapper');
    this.searchInput = document.querySelector('#search-input');
    this.resultsContainer = document.querySelector('#search-results');
    this.noResultsElement = document.querySelector('#no-results');
    this.searchSummary = document.querySelector('#search-summary');
    this.historyItems = document.querySelectorAll('.history-item'); 
    this.sectionTitle = document.querySelector('.section-title');  
    this.originalPlaceholder = this.searchInput ? this.searchInput.getAttribute('placeholder') || '' : '';

    this.initEvents();
    this.initScrollListener();
  }

  public static getInstance(): OmnisSearch {
    if (!this.instance) {
      this.instance = new OmnisSearch();
    }
    return this.instance;
  }

  private initEvents(): void {
    if (this.searchInput) {
      this.searchInput.addEventListener('input', this.handleSearchInput.bind(this));
    }
    if (this.historyItems && this.historyItems.length) {
      this.historyItems.forEach((btn) => {
        btn.addEventListener('click', (e) => {
          if (this.sectionTitle) {
            this.sectionTitle.style.display = 'none';
          }
          
          this.historyItems!.forEach(item => item.classList.remove('active'));

          (e.currentTarget as HTMLButtonElement).classList.add('active');

          const value = (e.currentTarget as HTMLButtonElement).textContent || '';

          if (this.searchInput) {
            this.searchInput.value = '';
          }
          if (value.length >= 3) {
            this.currentPage = 1;
            this.currentSearchTerm = value;
            this.performSearch(value, 1);
          }
        });
      });
    }
  }

  private initScrollListener(): void {
    window.addEventListener('scroll', () => {
      if (this.isLoading) return;
      if (window.innerHeight + window.scrollY >= document.body.offsetHeight - 200) {
        if (this.currentSearchTerm && this.currentPage < this.maxPages) {
          this.loadMore();
        } else if (this.currentSearchTerm && this.currentPage >= this.maxPages) {
          if (this.resultsContainer && !this.resultsContainer.querySelector('.end-message')) {
            const endMessage = document.createElement('span');
            endMessage.textContent = 'לא נמצאו עוד חיפושים.';
            endMessage.classList.add('end-message');
            this.resultsContainer.appendChild(endMessage);
          }
        }
      }
    });
  }

  private handleSearchInput(e: Event): void {
    const target = e.currentTarget as HTMLInputElement;
    const value = target.value.trim();
    if (this.searchTimeout) {
      clearTimeout(this.searchTimeout);
    }
    if (value.length >= 3) {
      this.searchTimeout = window.setTimeout(() => {
        this.currentPage = 1;
        this.currentSearchTerm = value;
        this.performSearch(value, 1);
      }, 300);
    } else {
      this.clearResults();
      if (this.searchSummary) {
        this.searchSummary.style.display = 'none';
      }
    }
  }

  private performSearch(searchTerm: string, page: number = 1): void {
    if (!omnis_ajax_object || !omnis_ajax_object.ajaxurl) {
      console.error('Ajax object or ajaxurl is missing');
      return;
    }
    const nonceElement = document.querySelector('#search-nonce') as HTMLInputElement | null;
    const search_nonce = nonceElement ? nonceElement.value : '';

    this.isLoading = true;
    this.showLoader();

    const formData = new FormData();
    formData.append('action', 'product_search');
    formData.append('search_term', searchTerm);
    formData.append('security', search_nonce);
    formData.append('paged', page.toString());
    if (this.sectionTitle) {
      this.sectionTitle.style.display = 'none';
    }
    
    axios.post(omnis_ajax_object.ajaxurl, formData)
      .then((response) => {
        if (response.data.success) {
          const { html, found, term, max_pages } = response.data.data;
          this.maxPages = max_pages;
          if (page === 1) {
            if (this.resultsContainer) {
              this.resultsContainer.innerHTML = html;
            }
          } else {
            if (this.resultsContainer) {
              const endMsg = this.resultsContainer.querySelector('.end-message');
              if (endMsg) {
                endMsg.remove();
              }
              this.resultsContainer.insertAdjacentHTML('beforeend', html);
            }
          }
          if (this.noResultsElement) {
            this.noResultsElement.style.display = found === 0 ? 'flex' : 'none';
          }
          if (this.searchSummary) {
            this.searchSummary.style.display = 'flex';
            const countText = found === 0 ? 'אין תוצאות' : `${found} תוצאות`;
            this.searchSummary.innerHTML = `<span class="result">${term}</span><span class="count">${countText}</span>`;
          }
          this.currentPage = page;
        } else {
          console.warn(response.data.data.message || 'Неизвестная ошибка');
        }
      })
      .catch((error) => {
        console.error('Search request error:', error);
      })
      .finally(() => {
        this.isLoading = false;
        this.hideLoader();
        this.stopPlaceholderAnimation();
        this.resetPlaceholder();
      });
  }

  private loadMore(): void {
    this.performSearch(this.currentSearchTerm, this.currentPage + 1);
  }

  private clearResults(): void {
    if (this.resultsContainer) {
      this.resultsContainer.innerHTML = '';
    }
    if (this.noResultsElement) {
      this.noResultsElement.style.display = 'none';
    }
  }

  private showLoader(): void { 
    this.hideLoader();
    if (this.resultsContainer) {
      const loader = document.createElement('div');
      loader.className = 'loader';
      this.resultsContainer.insertAdjacentElement('afterend', loader);
      if(this.pageWrapper){
        this.pageWrapper.classList.add('active');
      }
    }
  }

  private hideLoader(): void {
    const loader = document.querySelector('.loader');
    if (loader && loader.parentElement) {
      loader.parentElement.removeChild(loader);
      if(this.pageWrapper){
        this.pageWrapper.classList.remove('active');
      }
    }
  }

  private startPlaceholderAnimation(): void {
    if (!this.searchInput) return;
    let dotCount = 0;
    this.placeholderInterval = window.setInterval(() => {
      dotCount = (dotCount % 3) + 1;
      const dots = '.'.repeat(dotCount);
      this.searchInput!.setAttribute('placeholder', `${this.originalPlaceholder} ${dots}`);
    }, 500);
  }

  private stopPlaceholderAnimation(): void {
    if (this.placeholderInterval) {
      clearInterval(this.placeholderInterval);
      this.placeholderInterval = undefined;
    }
  }

  private resetPlaceholder(): void {
    if (this.searchInput) {
      this.searchInput.setAttribute('placeholder', this.originalPlaceholder);
    }
  }
}

document.addEventListener('DOMContentLoaded', () => {
  OmnisSearch.getInstance();
});
