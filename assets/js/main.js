// Initialize Alpine.js components
document.addEventListener('alpine:init', () => {
    // Add any Alpine.js component initialization here
});

// Handle mobile menu
document.addEventListener('DOMContentLoaded', () => {
    // Add any vanilla JavaScript functionality here
});

document.addEventListener('DOMContentLoaded', function() {
    if (typeof Swiper !== 'undefined') {
        new Swiper('.hero-swiper', {
            loop: true,
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    }
}); 