/**
 * Helper class containing static methods for common tasks.
 */
export default class Helpers {
    /**
     * Get the value of a URL parameter by name.
     *
     * @param {string} name - The name of the URL parameter to retrieve.
     * @return {string} The value of the URL parameter or an empty string if not found.
     */
    static getUrlParameter(name: string): string {
        // Escape special characters in the parameter name
        const escapedName = name.replace(/\[/g, '\\[').replace(/\]/g, '\\]');
        // Create a regular expression to match the parameter in the URL
        const regex = new RegExp(`[?&]${escapedName}=([^&#]*)`);
        // Execute the regex on the current location search string
        const results = regex.exec(window.location.search);
        // Return the parameter value if found, or an empty string otherwise
        return results ? decodeURIComponent(results[1].replace(/\+/g, ' ')) : '';
    }

    /**
     * Smoothly scrolls the page to the top.
     * If the browser supports smooth scrolling, it will use the native behavior.
     * Otherwise, it falls back to an interval-based scrolling approach.
     *
     * @param {number} [scrollingTime=10] - The interval time in milliseconds for the fallback scrolling (only used if
     *     smooth behavior is unsupported).
     * @param {number} [offsetFromPageTop=50] - The pixel offset used to move the page up in each step of the fallback
     *     method.
     */
    static scrollToTop = (scrollingTime = 10, offsetFromPageTop = 50) => {
        // Check if the browser supports smooth behavior in the scroll method
        if ('scrollBehavior' in document.documentElement.style) {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
            // If not supported, use a fallback method
            const scrollInterval = setInterval(() => {
                // Get the current scroll position
                const scrollPosition =
                    window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop;

                // If the scroll position is not at the top, move it up by 50px
                if (scrollPosition > 0) {
                    window.scrollBy(0, -offsetFromPageTop);
                } else {
                    // Once at the top, clear the interval
                    clearInterval(scrollInterval);
                }
            }, scrollingTime);
        }
    };
}
