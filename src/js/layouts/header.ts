/**
 * Represents the header functionality of the website.
 */
class Header {
    private static instance: Header;

    /**
     * Creates an instance of the Header class.
     */
    private constructor() {
        this.initMobileMenu();
        this.initBannerClose();

        
    }

    /**
     * Get a singleton instance of the Header class.
     *
     * @return {Header} The singleton instance of the Header class.
     */
    public static getInstance(): Header {
        if (!this.instance) {
            this.instance = new this();
        }
        return this.instance;
    }

    /**
     * Initializes the mobile menu toggle functionality.
     */
    
    private initMobileMenu(): void {
        document.addEventListener('DOMContentLoaded', () => {
            const navTrigger = document.querySelector<HTMLElement>('.nav-trigger');
            const navClose = document.querySelector<HTMLElement>('.nav__close');
            const headerNav = document.querySelector<HTMLElement>('.nav');
    
            if (navTrigger) {
                navTrigger.addEventListener('click', () => {
                    document.body.classList.toggle('mobile-nav');
                });
            }
    
            if (navClose) {
                navClose.addEventListener('click', () => {
                    document.body.classList.remove('mobile-nav');
                });
            }
    
            document.addEventListener('click', (event) => {
                const target = event.target as HTMLElement;
    
                // Check if the click was outside of .header-nav
                if (
                    document.body.classList.contains('mobile-nav') && // Only check when the menu is open
                    headerNav && 
                    !headerNav.contains(target) && // Click is not inside the .header-nav
                    !target.closest('.nav-trigger') // Click is not on the nav-trigger
                ) {
                    document.body.classList.remove('mobile-nav'); // Close the menu
                }
            });
        });
    }

    private initBannerClose(): void {
        document.addEventListener('DOMContentLoaded', () => {
            const closeButtons = document.querySelectorAll<HTMLElement>('.banner__close-btn');
    
            closeButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const banner = button.closest('.banner');
                    if (banner) {
                        banner.remove(); // Removes the banner element
                    }
                });
            });
        });
    }



}

// Initialize the Header functionality
Header.getInstance();

export {};
