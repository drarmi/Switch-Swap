import Helpers from './helpers';

(() => {
    const scrollToTop = document.querySelector('.js-scroll-to-top') as HTMLElement;

    if (scrollToTop) {
        scrollToTop.addEventListener('click', () => {
            // Call the scrollToTop function, passing any parameters if needed
            Helpers.scrollToTop();
        });
    }
})();
