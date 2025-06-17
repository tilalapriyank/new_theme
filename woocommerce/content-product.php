<?php
/**
 * The template for displaying product content within loops
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
$product_badge = '';

// Check if product is on sale
if ($product->is_on_sale()) {
    $product_badge = 'Sale';
} elseif ($product->is_featured()) {
    $product_badge = 'Featured';
}

// Get product categories
$categories = get_the_terms($product_id, 'product_cat');
$category_name = $categories ? $categories[0]->name : '';

// Get product attributes
$attributes = $product->get_attributes();
$sizes = [];
$colors = [];

if (isset($attributes['pa_size'])) {
    $sizes = $attributes['pa_size']->get_options();
}

if (isset($attributes['pa_color'])) {
    $colors = $attributes['pa_color']->get_options();
}

// Prepare product data for cart
$product_data = [
    'id' => $product_id,
    'name' => $product_name,
    'price' => (float) $product_price,
    'image' => $product_image,
    'url' => get_permalink($product_id),
    'quantity' => 1
];

// Check product type for add to cart handling
$is_variable = $product->get_type() === 'variable';
$is_simple = $product->get_type() === 'simple';
?>

<div class="bg-white rounded-2xl shadow-md overflow-hidden group relative flex flex-col justify-between h-full">
    <div class="relative">
        <?php if ($product_badge) : ?>
            <span class="absolute top-4 left-4 z-20 bg-[#FF3A5E] text-white text-xs font-semibold px-3 py-1 rounded-full">
                <?php echo esc_html($product_badge); ?>
            </span>
        <?php endif; ?>
        
        <div class="absolute top-4 right-4 z-20">
            <?php if (function_exists('yith_wcwl_add_to_wishlist')) {
                echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . esc_attr($product_id) . '"]');
            } else { ?>
                <button class="wishlist-btn text-gray-400 hover:text-[#FF3A5E] focus:outline-none" onclick="addToWishlist(<?php echo esc_attr($product_id); ?>)">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            <?php } ?>
        </div>
        
        <a href="<?php echo esc_url($product->get_permalink()); ?>" class="block aspect-square w-full overflow-hidden bg-gray-100">
            <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_name); ?>" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-105" />
        </a>
        
        <!-- FIXED: Hover Overlay with Proper Add to Cart -->
        <div class="absolute inset-0 flex flex-col items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/40 z-10">
            <!-- Quick View Button -->
            <button 
                class="mb-2 px-6 py-2 rounded-full bg-white/90 text-gray-900 font-medium text-base shadow hover:bg-white quick-view-btn"
                onclick="openQuickView(<?php echo esc_attr($product_id); ?>)"
            >
                Quick View
            </button>
            
            <!-- Add to Cart Button - Fixed for Variable Products -->
            <?php if ($is_variable) : ?>
                <!-- Variable Product - Redirect to Product Page -->
                <a 
                    href="<?php echo esc_url($product->get_permalink()); ?>"
                    class="px-8 py-2 rounded-full bg-[#FF3A5E] text-white font-semibold text-base shadow hover:bg-[#E02E50] flex items-center gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                    Select Options
                </a>
            <?php elseif ($is_simple) : ?>
                <!-- Simple Product - AJAX Add to Cart -->
                <button 
                    class="ajax-add-to-cart-single px-8 py-2 rounded-full bg-[#FF3A5E] text-white font-semibold text-base shadow hover:bg-[#E02E50] flex items-center gap-2"
                    data-product_id="<?php echo esc_attr($product_id); ?>"
                    data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                    data-quantity="1"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 9H19m-7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                    </svg>
                    <span class="add-to-cart-text">Add to Cart</span>
                </button>
            <?php else : ?>
                <!-- Other Product Types - Use WooCommerce Default -->
                <div class="woocommerce-loop-add-to-cart-wrapper">
                    <?php woocommerce_template_loop_add_to_cart(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="flex-1 flex flex-col justify-between p-6">
        <div>
            <h3 class="font-semibold text-lg text-gray-900 mb-1">
                <a href="<?php echo esc_url($product->get_permalink()); ?>" class="hover:text-[#FF3A5E] transition-colors"><?php echo esc_html($product_name); ?></a>
            </h3>
            
            <!-- Product Rating -->
            <div class="flex items-center gap-1 mb-2">
                <?php
                $rating = $product_rating;
                for ($i = 1; $i <= 5; $i++) {
                    $fill_class = $i <= floor($rating) ? 'text-[#FFD100]' : 'text-gray-300';
                    echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ' . $fill_class . '" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
                }
                ?>
                <span class="text-xs text-gray-500 ml-1">(<?php echo esc_html($product_review_count); ?>)</span>
            </div>
            
            <!-- Product Price -->
            <div class="flex items-center gap-2 mt-2">
                <?php if ($product->is_on_sale()) : ?>
                    <span class="text-gray-400 text-lg font-semibold line-through"><?php echo wc_price($product->get_regular_price()); ?></span>
                    <span class="text-[#FF3A5E] text-lg font-bold"><?php echo wc_price($product->get_sale_price()); ?></span>
                <?php else : ?>
                    <span class="text-gray-900 text-lg font-bold"><?php echo wc_price($product_price); ?></span>
                <?php endif; ?>
            </div>  
        </div>
    </div>
</div>

<script>
// Add this script only once per page
if (!window.productContentInitialized) {
    window.productContentInitialized = true;
    
    // AJAX Add to Cart for Simple Products
    function handleAjaxAddToCartSingle() {
        const buttons = document.querySelectorAll('.ajax-add-to-cart-single');
        
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const productId = this.getAttribute('data-product_id');
                const quantity = this.getAttribute('data-quantity') || 1;
                const textElement = this.querySelector('.add-to-cart-text');
                const originalText = textElement.textContent;
                
                // Show loading state
                this.classList.add('loading');
                this.disabled = true;
                textElement.textContent = 'Adding...';
                
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
                    if (data.error && data.product_url) {
                        // If there's an error, redirect to product page
                        window.location.href = data.product_url;
                        return;
                    }
                    
                    // Success - update cart fragments if available
                    if (data.fragments) {
                        Object.keys(data.fragments).forEach(key => {
                            const element = document.querySelector(key);
                            if (element) {
                                element.innerHTML = data.fragments[key];
                            }
                        });
                    }
                    
                    // Show success state
                    this.classList.remove('loading');
                    this.classList.add('added');
                    textElement.textContent = 'Added!';
                    
                    // Show toast notification if function exists
                    if (typeof showToast === 'function') {
                        showToast('Product added to cart!', 'success');
                    }
                    
                    // Reset button after 2 seconds
                    setTimeout(() => {
                        this.classList.remove('added');
                        this.disabled = false;
                        textElement.textContent = originalText;
                    }, 2000);
                    
                    // Trigger WooCommerce events if jQuery is available
                    if (typeof jQuery !== 'undefined') {
                        jQuery('body').trigger('added_to_cart', [data.fragments, data.cart_hash, this]);
                    }
                })
                .catch(error => {
                    console.error('Add to cart error:', error);
                    
                    // Reset button state
                    this.classList.remove('loading');
                    this.disabled = false;
                    textElement.textContent = originalText;
                    
                    // Show error message
                    if (typeof showToast === 'function') {
                        showToast('Error adding product to cart', 'error');
                    }
                });
            });
        });
    }
    
    // Quick View Function (placeholder - implement based on your needs)
    function openQuickView(productId) {
        // Implement your quick view modal logic here
        console.log('Opening quick view for product:', productId);
        // For now, redirect to product page
        window.location.href = '<?php echo home_url(); ?>/?p=' + productId;
    }
    
    // Wishlist Function (placeholder - implement based on your needs)
    function addToWishlist(productId) {
        // Implement your wishlist logic here
        console.log('Adding to wishlist:', productId);
        if (typeof showToast === 'function') {
            showToast('Product added to wishlist!', 'success');
        }
    }
    
    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        handleAjaxAddToCartSingle();
    });
    
    // Make functions globally accessible
    window.openQuickView = openQuickView;
    window.addToWishlist = addToWishlist;
}
</script>

<style>
/* Loading and success states */
.ajax-add-to-cart-single.loading {
    opacity: 0.6;
    cursor: not-allowed;
}

.ajax-add-to-cart-single.added {
    background-color: #28a745 !important;
}

/* Ensure hover overlay is properly positioned */
.group:hover .absolute.inset-0 {
    z-index: 10;
}

/* WooCommerce default button styling in overlay */
.woocommerce-loop-add-to-cart-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
}

.woocommerce-loop-add-to-cart-wrapper .button {
    background-color: #FF3A5E !important;
    color: white !important;
    border: none !important;
    padding: 8px 32px !important;
    border-radius: 9999px !important;
    font-weight: 600 !important;
    font-size: 16px !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1) !important;
    transition: all 0.2s !important;
}

.woocommerce-loop-add-to-cart-wrapper .button:hover {
    background-color: #E02E50 !important;
}

/* Responsive adjustments */
@media (max-width: 640px) {
    .absolute.inset-0 {
        opacity: 1;
        background: linear-gradient(to top, rgba(0,0,0,0.7), transparent);
    }
    
    .absolute.inset-0 > button,
    .absolute.inset-0 > a {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        margin: 0;
    }
    
    .absolute.inset-0 > button:first-child,
    .absolute.inset-0 > a:first-child {
        bottom: 70px;
    }
}
</style>