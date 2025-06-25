    /**
 * YITH WooCommerce Wishlist Initialization
 * This script ensures YITH wishlist is properly detected and initialized
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Check for YITH plugin by looking for elements on the page
        const yithDetected = $('.yith-wcwl-add-to-wishlist').length > 0 ||
                            (typeof hypePupsYith !== 'undefined' && hypePupsYith.yith_active);
        
        if (yithDetected) {
            // Initialize YITH wishlist events
            $(document).on('yith_wcwl_product_added_to_wishlist', function(event, button, wishlistData) {
                if (typeof showToast === 'function') {
                    showToast('Product added to wishlist!', 'success');
                }
            });
            
            $(document).on('yith_wcwl_product_removed_from_wishlist', function(event, button, wishlistData) {
                if (typeof showToast === 'function') {
                    showToast('Product removed from wishlist!', 'success');
                }
            });
            
            // Handle wishlist button clicks for better UX
            $(document).on('click', '.yith-wcwl-add-to-wishlist a', function(e) {
                const $button = $(this).closest('.yith-wcwl-add-to-wishlist');
                $button.addClass('loading');
                
                // Remove loading class after a short delay
                setTimeout(function() {
                    $button.removeClass('loading');
                }, 1000);
            });
            
        } else {
            // Show fallback message if user clicks wishlist button
            $(document).on('click', '.wishlist-fallback', function(e) {
                e.preventDefault();
                if (typeof showToast === 'function') {
                    showToast('YITH WooCommerce Wishlist plugin is required for wishlist functionality.', 'error');
                }
            });
        }
    });
    
})(jQuery); 