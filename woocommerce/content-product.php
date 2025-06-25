<?php
/**
 * The template for displaying product content within loops
 * Clean modern card design matching the reference HTML structure
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined('ABSPATH') || exit;

global $product;

// Ensure visibility
if (empty($product) || !$product->is_visible()) {
    return;
}

// Get product data
$product_id = $product->get_id();
$product_name = $product->get_name();
$product_price = $product->get_price();
$product_image = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
$product_rating = $product->get_average_rating();
$product_review_count = $product->get_review_count();
$product_permalink = $product->get_permalink();

// Badge logic based on product properties
$badge_info = null;

if ($product->is_on_sale()) {
    $badge_info = ['label' => 'SALE', 'color' => 'bg-[#ED1C24]'];
} elseif ($product->is_featured()) {
    $badge_info = ['label' => 'BESTSELLER', 'color' => 'bg-green-500'];
} else {
    // Check for custom badges or product tags
    $product_tags = get_the_terms($product_id, 'product_tag');
    if ($product_tags) {
        foreach ($product_tags as $tag) {
            if (in_array($tag->slug, ['new', 'new-arrival', 'latest'])) {
                $badge_info = ['label' => 'NEW', 'color' => 'bg-blue-500'];
                break;
            } elseif (in_array($tag->slug, ['limited', 'limited-edition', 'exclusive'])) {
                $badge_info = ['label' => 'LIMITED', 'color' => 'bg-purple-500'];
                break;
            }
        }
    }
}

// Check product type for add to cart handling
$is_variable = $product->get_type() === 'variable';
$is_simple = $product->get_type() === 'simple';
$is_out_of_stock = !$product->is_in_stock();
?>

<style>
/* Clean Product Grid Styles */
.clean-product-grid {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 1rem !important;
    margin-bottom: 2rem !important;
    padding: 0 !important;
    list-style: none !important;
}

@media (min-width: 640px) {
    .clean-product-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1.5rem !important;
    }
}

@media (min-width: 1024px) {
    .clean-product-grid {
        grid-template-columns: repeat(4, 1fr) !important;
    }
}

/* Product Card - Updated to match reference */
.product-card-clean {
    border-radius: 0.5rem !important;
    border: 1px solid #e5e7eb !important;
    background: #ffffff !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    overflow: hidden !important;
    transition: all 0.3s ease !important;
    display: block !important;
    text-decoration: none !important;
    color: inherit !important;
    height: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    cursor: pointer !important;
}

.product-card-clean:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
    text-decoration: none !important;
}

/* Product Image Container */
.product-image-wrapper {
    position: relative !important;
}

.product-image-container {
    aspect-ratio: 1 !important;
    position: relative !important;
    overflow: hidden !important;
}

.product-image {
    position: absolute !important;
    height: 100% !important;
    width: 100% !important;
    inset: 0 !important;
    object-fit: cover !important;
    transition: transform 0.5s ease !important;
}

.product-card-clean:hover .product-image {
    transform: scale(1.05) !important;
}

/* Badge Styles - Updated to match reference */
.product-badge {
    position: absolute !important;
    top: 0.75rem !important;
    left: 0.75rem !important;
    display: inline-flex !important;
    align-items: center !important;
    border-radius: 9999px !important;
    font-weight: 600 !important;
    color: white !important;
    font-size: 0.75rem !important;
    padding: 0.25rem 0.5rem !important;
    z-index: 20 !important;
    margin: 0 !important;
    border: 1px solid transparent !important;
    text-transform: uppercase !important;
    letter-spacing: 0.025em !important;
    transition: all 0.2s ease !important;
}

.product-badge:hover {
    background-color: rgba(16, 185, 129, 0.8) !important;
}

.bg-green-500 { background-color: #10b981 !important; }
.bg-blue-500 { background-color: #3b82f6 !important; }
.bg-purple-500 { background-color: #8b5cf6 !important; }

/* Wishlist Button - Updated to match reference */
.wishlist-btn {
    position: absolute !important;
    top: 0.75rem !important;
    right: 0.75rem !important;
    width: 2.5rem !important;
    height: 2.5rem !important;
    background: rgba(255, 255, 255, 0.9) !important;
    border: none !important;
    border-radius: 50% !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.3s ease !important;
    cursor: pointer !important;
    z-index: 20 !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    color: #6b7280 !important;
}

.wishlist-btn:hover {
    background: white !important;
    color: #ED1C24 !important;
}

.wishlist-btn svg {
    width: 1.25rem !important;
    height: 1.25rem !important;
    color: currentColor !important;
    fill: none !important;
    transition: all 0.2s ease !important;
}

.wishlist-btn svg.filled {
    fill: currentColor !important;
}

/* Hover Overlay - Updated to match reference */
.product-overlay {
    position: absolute !important;
    inset: 0 !important;
    background: rgba(0, 0, 0, 0.4) !important;
    display: flex !important;
    align-items: flex-end !important;
    justify-content: flex-start !important;
    opacity: 0 !important;
    transition: opacity 0.3s ease !important;
    z-index: 10 !important;
    padding: 1rem !important;
}

.product-card-clean:hover .product-overlay {
    opacity: 1 !important;
}

.overlay-buttons {
    display: flex !important;
    flex-direction: column !important;
    gap: 0.75rem !important;
    align-items: stretch !important;
    justify-content: flex-end !important;
    width: 100% !important;
}

.quick-view-btn, .add-to-cart-btn {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.5rem !important;
    white-space: nowrap !important;
    font-size: 0.875rem !important;
    font-weight: 500 !important;
    transition: all 0.2s ease !important;
    height: 2.75rem !important;
    border-radius: 0.375rem !important;
    padding: 0 1rem !important;
    cursor: pointer !important;
    text-decoration: none !important;
    border: none !important;
    margin: 0 !important;
    width: 100% !important;
    text-align: center !important;
    backdrop-filter: blur(4px) !important;
}

.quick-view-btn {
    background: rgba(255, 255, 255, 0.95) !important;
    border: 1px solid white !important;
    color: #111827 !important;
}

.quick-view-btn:hover {
    background: white !important;
    color: #000000 !important;
    text-decoration: none !important;
}

.add-to-cart-btn {
    background: #ED1C24 !important;
    color: white !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
}

.add-to-cart-btn:hover {
    background: #D91A21 !important;
    text-decoration: none !important;
}

.add-to-cart-btn.loading {
    opacity: 0.7 !important;
    cursor: not-allowed !important;
}

.add-to-cart-btn.added {
    background: #10b981 !important;
}

.add-to-cart-btn:disabled {
    background: #9ca3af !important;
    cursor: not-allowed !important;
}

.quick-view-btn svg, .add-to-cart-btn svg {
    width: 1rem !important;
    height: 1rem !important;
    margin-right: 0.5rem !important;
    pointer-events: none !important;
    flex-shrink: 0 !important;
}

/* Product Content - Updated to match reference */
.product-content {
    padding: 1rem !important;
    background: #ffffff !important;
    margin: 0 !important;
    border: none !important;
}

.product-info {
    display: flex !important;
    flex-direction: column !important;
    gap: 0.5rem !important;
}

.product-title {
    font-weight: 600 !important;
    color: #111827 !important;
    transition: color 0.2s ease !important;
    text-decoration: none !important;
    display: block !important;
    margin: 0 !important;
    font-size: 1rem !important;
    line-height: 1.4 !important;
    overflow: hidden !important;
    text-overflow: ellipsis !important;
    white-space: nowrap !important;
}

.product-title-link {
    font-weight: 600 !important;
    color: #111827 !important;
    transition: color 0.2s ease !important;
    text-decoration: none !important;
    display: block !important;
    margin: 0 !important;
    font-size: 1rem !important;
    line-height: 1.4 !important;
}

.product-title-link:hover,
.product-card-clean:hover .product-title-link {
    color: #ED1C24 !important;
    text-decoration: none !important;
}

/* Product Rating - Updated to match reference */
.product-rating {
    display: flex !important;
    align-items: center !important;
    gap: 0.25rem !important;
}

/* Updated star sizes to match reference */
.h-3\.5 { height: 0.875rem !important; }
.w-3\.5 { width: 0.875rem !important; }

/* Flex utility classes */
.flex { display: flex !important; }
.items-center { align-items: center !important; }
.gap-1 { gap: 0.25rem !important; }
.gap-2 { gap: 0.5rem !important; }

.fill-yellow-400 { 
    fill: #facc15 !important; 
}

.text-yellow-400 { 
    color: #facc15 !important; 
}

.text-gray-300 { 
    color: #d1d5db !important; 
    fill: none !important;
}

.text-xs { 
    font-size: 0.75rem !important; 
    line-height: 1rem !important; 
}

.text-gray-500 { 
    color: #6b7280 !important; 
}

.ml-1 { 
    margin-left: 0.25rem !important; 
}

/* Product Price - Updated to match reference */
.product-price {
    display: flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    padding-top: 0.25rem !important;
}

.price-sale {
    font-weight: 700 !important;
    color: #ED1C24 !important;
    margin: 0 !important;
    font-size: 1.125rem !important;
}

.price-regular {
    font-weight: 700 !important;
    color: #ED1C24 !important;
    margin: 0 !important;
    font-size: 1.125rem !important;
}

.price-original {
    font-size: 0.875rem !important;
    color: #9ca3af !important;
    text-decoration: line-through !important;
    margin: 0 !important;
}

/* Responsive Design */
@media (max-width: 640px) {
    .clean-product-grid {
        gap: 1rem !important;
    }
    
    .product-content {
        padding: 0.75rem !important;
    }
    
    .product-title {
        font-size: 0.875rem !important;
    }
    
    .product-overlay {
        opacity: 1 !important;
        background: linear-gradient(to top, rgba(0,0,0,0.6), transparent 60%) !important;
        justify-content: flex-end !important;
        align-items: flex-end !important;
        padding: 1rem !important;
    }
    
    .overlay-buttons {
        flex-direction: column !important;
        width: 100% !important;
        gap: 0.5rem !important;
    }
    
    .quick-view-btn, .add-to-cart-btn {
        font-size: 0.75rem !important;
        height: 2rem !important;
        padding: 0 0.75rem !important;
        min-width: 100px !important;
        width: 100% !important;
    }
}

/* WooCommerce Compatibility */
.woocommerce .product-card-clean,
.woocommerce .product-content,
.woocommerce .product-title,
.woocommerce .product-title-link {
    background: #ffffff !important;
    border: none !important;
}

.woocommerce ul.products.clean-product-grid {
    display: grid !important;
    grid-template-columns: repeat(2, 1fr) !important;
    gap: 1rem !important;
    list-style: none !important;
    padding: 0 !important;
    margin: 0 0 2rem 0 !important;
}

@media (min-width: 640px) {
    .woocommerce ul.products.clean-product-grid {
        gap: 1.5rem !important;
    }
}

@media (min-width: 1024px) {
    .woocommerce ul.products.clean-product-grid {
        grid-template-columns: repeat(4, 1fr) !important;
    }
}

.woocommerce ul.products.clean-product-grid li {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    float: none !important;
}

/* Remove default WooCommerce styles */
.woocommerce ul.products li.product,
.woocommerce-page ul.products li.product {
    background: transparent !important;
    border: none !important;
    margin: 0 !important;
    padding: 0 !important;
    float: none !important;
    width: 100% !important;
}

.woocommerce ul.products li.product a img,
.woocommerce-page ul.products li.product a img {
    width: 100% !important;
    height: auto !important;
    box-shadow: none !important;
}

.woocommerce ul.products li.product .woocommerce-loop-product__title,
.woocommerce-page ul.products li.product .woocommerce-loop-product__title {
    font-size: 1rem !important;
    font-weight: 600 !important;
    color: #111827 !important;
    padding: 0 !important;
    margin: 0 !important;
}

.woocommerce ul.products li.product .woocommerce-loop-product__title a,
.woocommerce-page ul.products li.product .woocommerce-loop-product__title a {
    color: #111827 !important;
    text-decoration: none !important;
}

.woocommerce ul.products li.product .woocommerce-loop-product__title a:hover,
.woocommerce-page ul.products li.product .woocommerce-loop-product__title a:hover {
    color: #ED1C24 !important;
    text-decoration: none !important;
}

/* Ensure proper hover states for all interactive elements */
.product-card-clean:hover .product-title {
    color: #ED1C24 !important;
}

/* Additional utility classes for wishlist button */
.w-5 { width: 1.25rem !important; }
.h-5 { height: 1.25rem !important; }
.transition-all { transition: all 0.2s ease !important; }

/* Ensure wishlist button hover states work properly */
.wishlist-btn:hover svg {
    color: #ED1C24 !important;
}
</style>

<div class="product-card-clean group">
    <a href="<?php echo esc_url($product_permalink); ?>">
        <div class="product-image-wrapper">
            <div class="product-image-container">
                <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_name); ?>" class="product-image" />
            </div>
            
            <?php if ($badge_info): ?>
            <div class="product-badge <?php echo esc_attr($badge_info['color']); ?>">
                <?php echo esc_html($badge_info['label']); ?>
            </div>
            <?php endif; ?>
            
            <div class="wishlist-btn-container" style="position: absolute; top: 0.75rem; right: 0.75rem; z-index: 20;">
                <?php echo do_shortcode('[yith_wcwl_add_to_wishlist]'); ?>
            </div>
            
            <div class="product-overlay">
                <div class="overlay-buttons">
                     <a href="#"
                                                    class="flex items-center w-full bg-white text-gray-800 font-medium rounded-lg px-4 py-2 shadow hover:bg-gray-100 text-base gap-2 quick-view-btn"
                                                    data-product-id="<?php echo esc_attr($product_id); ?>"
                                                    data-product-url="<?php echo esc_url($product_permalink); ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Quick View
                                                </a>
                    
                    <?php if ($is_out_of_stock): ?>
                        <button class="add-to-cart-btn" disabled aria-label="Out of stock">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <path d="m15 9-6 6"></path>
                                    <path d="m9 9 6 6"></path>
                                </svg>
                                Out of Stock
                            </span>
                        </button>
                    <?php elseif ($is_variable): ?>
                        <a href="<?php echo esc_url($product_permalink); ?>" class="add-to-cart-btn" onclick="event.stopPropagation();" aria-label="Select options">
                            <span class="flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-4 w-4 mr-2" __v0_r="0,5832,5846"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                                Select Options
                            </span>
                        </a>
                    <?php else: ?>
                        <button class="add-to-cart-btn ajax-add-to-cart" 
                                onclick="event.preventDefault(); event.stopPropagation();"
                                data-product_id="<?php echo esc_attr($product_id); ?>"
                                data-quantity="1"
                                aria-label="Add to cart">
                            <span class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="8" cy="21" r="1"></circle>
                                    <circle cx="19" cy="21" r="1"></circle>
                                    <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                                </svg>
                                <span class="add-to-cart-text">Add to Cart</span>
                            </span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <a class="product-link" href="<?php echo esc_url($product_permalink); ?>">
        <div class="product-content">
            <div class="product-info">
                <h3 class="product-title">
                    <?php echo esc_html($product_name); ?>
                </h3>
                
                <?php if ($product_review_count > 0): ?>
                <div class="flex items-center gap-1">
                    <?php
                    for ($i = 1; $i <= 5; $i++) {
                        $star_classes = $i <= floor($product_rating) 
                            ? 'lucide lucide-star h-3.5 w-3.5 fill-yellow-400 text-yellow-400' 
                            : 'lucide lucide-star h-3.5 w-3.5 text-gray-300';
                        echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="' . $star_classes . '"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
                    }
                    ?>
                    <span class="text-xs text-gray-500 ml-1">(<?php echo esc_html($product_review_count); ?>)</span>
                </div>
                <?php endif; ?>
                
                <div class="product-price">
                    <?php if ($product->is_on_sale()): ?>
                        <span class="price-sale"><?php echo wc_price($product->get_sale_price()); ?></span>
                        <span class="price-original"><?php echo wc_price($product->get_regular_price()); ?></span>
                    <?php else: ?>
                        <span class="price-regular"><?php echo wc_price($product_price); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        </a>
    </a>
</div>

<script>
// Initialize clean product card functionality
if (!window.cleanProductCardInitialized) {
    window.cleanProductCardInitialized = true;
    
    // AJAX Add to Cart
    function handleAjaxAddToCart() {
        document.addEventListener('click', function(e) {
            if (e.target.closest('.ajax-add-to-cart')) {
                e.preventDefault();
                e.stopPropagation();
                
                const button = e.target.closest('.ajax-add-to-cart');
                const productId = button.getAttribute('data-product_id');
                const quantity = button.getAttribute('data-quantity') || 1;
                const textElement = button.querySelector('.add-to-cart-text');
                const originalText = textElement ? textElement.textContent : 'Add to Cart';
                
                // Show loading state
                button.classList.add('loading');
                button.disabled = true;
                if (textElement) textElement.textContent = 'Adding...';
                
                // Prepare form data
                const formData = new FormData();
                formData.append('action', 'woocommerce_add_to_cart');
                formData.append('product_id', productId);
                formData.append('quantity', quantity);
                formData.append('add-to-cart', productId);
                
                // Make AJAX request
                fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                    method: 'POST',
                    body: formData,
                    credentials: 'same-origin'
                })
                .then(response => response.json())
                .then(data => {
                    // Success state
                    button.classList.remove('loading');
                    button.classList.add('added');
                    if (textElement) textElement.textContent = 'Added!';
                    
                    // Update cart fragments
                    if (data.fragments) {
                        Object.keys(data.fragments).forEach(key => {
                            const element = document.querySelector(key);
                            if (element) {
                                element.innerHTML = data.fragments[key];
                            }
                        });
                    }
                    
                    // Reset after 2 seconds
                    setTimeout(() => {
                        button.classList.remove('added');
                        button.disabled = false;
                        if (textElement) textElement.textContent = originalText;
                    }, 2000);
                    
                    // Trigger WooCommerce events
                    if (typeof jQuery !== 'undefined') {
                        jQuery('body').trigger('added_to_cart', [data.fragments, data.cart_hash, button]);
                    }
                })
                .catch(error => {
                    console.error('Add to cart error:', error);
                    button.classList.remove('loading');
                    button.disabled = false;
                    if (textElement) textElement.textContent = originalText;
                });
            }
        });
    }
    
    // Quick View Function
    window.openQuickView = function(productId) {
        console.log('Opening quick view for product:', productId);
        // Implement your quick view modal here
        // For now, redirect to product page
        window.location.href = '<?php echo home_url(); ?>/?p=' + productId;
    };
    
    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        handleAjaxAddToCart();
        
        // Apply clean grid to product containers
        const productContainers = document.querySelectorAll('.woocommerce ul.products, .woocommerce-page ul.products');
        productContainers.forEach(container => {
            container.classList.add('clean-product-grid');
        });

        if (typeof jQuery !== 'undefined') {
            jQuery(function($) {
                // AJAX remove from wishlist for product cards
                $('body').on('click', '.product-card-clean .yith-wcwl-wishlistaddedbrowse a, .product-card-clean .yith-wcwl-wishlistexistsbrowse a', function(e) {
                    e.preventDefault();
                    
                    var button = $(this);
                    var wishlist_container = button.closest('.yith-wcwl-add-to-wishlist');
                    var product_id = wishlist_container.data('product-id');

                    if (!product_id || typeof yith_wcwl_l10n === 'undefined') {
                        return;
                    }
                    
                    var ajax_data = {
                        'action': yith_wcwl_l10n.actions.remove_from_wishlist_action,
                        'remove_from_wishlist': product_id,
                        'context': 'frontend',
                        'fragments_options': yith_wcwl_l10n.fragments_options
                    };
                    
                    // Add loading state to the parent container
                    var visual_container = button.closest('.wishlist-btn-container');
                    if(visual_container.length > 0) {
                       visual_container.css('opacity', '0.5');
                    }
                    
                    $.ajax({
                        type: 'POST',
                        url: yith_wcwl_l10n.ajax_url,
                        data: ajax_data,
                        success: function(response) {
                            if (response.fragments) {
                                // Find the specific fragment for this product's wishlist button and replace the container content.
                                var fragment_key = '.yith-wcwl-add-to-wishlist-' + product_id;
                                if (response.fragments[fragment_key]) {
                                     wishlist_container.parent().html(response.fragments[fragment_key]);
                                }

                                // Trigger event for other JS to catch
                                $(document.body).trigger('removed_from_wishlist');

                                if (typeof toastr !== 'undefined') {
                                    toastr.info('Product removed from your wishlist.');
                                }
                            }
                        },
                        error: function() {
                             if (typeof toastr !== 'undefined') {
                                toastr.error('Could not remove product from wishlist.');
                            }
                        }
                    }).always(function() {
                        // The container is replaced, so no need to remove loading state manually.
                    });
                });
            });
        }
    });
}
</script>
<style>
/* --- YITH Wishlist Styles for Single Product Page --- */
.yith-wcwl-add-to-wishlist, .yith-wcwl-add-to-wishlist * { font-size: 0 !important; color: transparent !important; text-indent: -9999px !important; line-height: 0 !important; }
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a { display: flex !important; align-items: center !important; justify-content: center !important; width: 40px !important; height: 40px !important; background: white !important; border-radius: 50% !important; box-shadow: 0 2px 8px rgba(0,0,0,0.10) !important; transition: all 0.2s ease !important; text-decoration: none !important; border: none !important; padding: 0 !important; margin: 0 !important; position: relative; }
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a:before,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:before,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:before { content: ''; display: inline-block; width: 24px; height: 24px; background-size: contain; background-repeat: no-repeat; vertical-align: middle; text-indent: 0; margin: 0; }

/* Outline heart (not in wishlist) */
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a:before {
    background-image: url('data:image/svg+xml;utf8,<svg fill="none" stroke="%236b7280" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>');
    position: absolute;
}

/* Filled red heart on white (in wishlist) */
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a {
    background: white !important;
}
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:before,
.yith-wcwl-add-to-wishlist.exists a:before {
    background-image: url('data:image/svg+xml;utf8,<svg fill="%23ed1c24" stroke="%23ed1c24" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>');
}
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:hover,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:hover {
    background: #f3f4f6 !important;
}

/* Hide all text, links, and spans inside the button */
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a span,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a span,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a span,
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a .yith-wcwl-icon,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a .yith-wcwl-icon,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a .yith-wcwl-icon {
    display: none !important;
}

/* Loading state for wishlist button */
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a.loading,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a.loading {
    opacity: 0.5;
    cursor: wait !important;
}
</style>