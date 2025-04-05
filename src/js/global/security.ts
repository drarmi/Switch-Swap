class Security {
    private static instance: Security;

    private constructor() {
        // this.disableContextMenu();
        // this.disableDeveloperTools();
        console.log('');
    }

    /**
     * Get a singleton instance of the Security class
     */
    public static getInstance(): Security {
        if (!this.instance) {
            this.instance = new this();
        }
        return this.instance;
    }

    /**
     * Prevent right-click context menu
     */
    public disableContextMenu(): void {
        window.document.addEventListener('contextmenu', (event) => {
            event.preventDefault();
        });
    }

    /**
     * Prevent access to developer tools
     */
    public disableDeveloperTools(): void {
        window.document.addEventListener('keydown', (event) => {
            if (event.key === 'F12' || (event.ctrlKey && event.shiftKey && event.key === 'I')) {
                event.preventDefault();
            }
        });
    }
}

// Initialize the Security instance
Security.getInstance();
export {};
