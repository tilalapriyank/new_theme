<?php
/**
 * Wishlist page template
 */

if (function_exists('YITH_WCWL')) {
    // Get the correct wishlist for the current user or guest
    $wishlist = YITH_WCWL_Wishlist_Factory::get_current_wishlist();
    $wishlist_items = $wishlist ? $wishlist->get_items() : array();

    if (!empty($wishlist_items)) : ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php foreach ($wishlist_items as $item) :
                $product = wc_get_product($item->get_product_id());
                if (!$product) continue;
                $is_variable = $product->is_type('variable');
                $remove_url = YITH_WCWL()->get_remove_url($wishlist->get_id(), $product->get_id());
                ?>
                <div class="flex bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden w-full max-w-[500px] mx-auto">
                    <!-- Image -->
                    <div class="flex items-center justify-center bg-gray-100 rounded-l-2xl">
                        <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>">
                            <?php echo $product->get_image('woocommerce_thumbnail', ['class' => 'object-cover w-32 h-32']); ?>
                        </a>
                    </div>
                    <!-- Info -->
                    <div class="flex-1 flex flex-col justify-center px-6 py-2">
                        <h6 class="text-[18px] font-semibold mb-2 leading-tight"><?php echo esc_html($product->get_name()); ?></h6>
                        <div class="text-[18px] font-extrabold text-gray-900 mb-6"><?php echo $product->get_price_html(); ?></div>
                        <div class="flex items-center gap-3">
                            <?php if ($is_variable): ?>
                                <a href="<?php echo esc_url(get_permalink($product->get_id())); ?>" class="flex-1 text-[14px] text-center bg-[#ff3a5e] hover:bg-[#e62e4d] text-white font-semibold rounded-lg py-3 transition-all duration-200">Select Options</a>
                            <?php else: ?>
                                <a href="<?php echo esc_url('?add-to-cart=' . $product->get_id()); ?>" class="flex-1 text-[14px] text-center bg-[#ff3a5e] hover:bg-[#e62e4d] text-white font-semibold rounded-lg py-3 transition-all duration-200">Add to Cart</a>
                            <?php endif; ?>
                            <!-- Heart Button (AJAX remove) -->
                            <button
                                class="wishlist-remove-btn bg-white border border-gray-200 rounded-lg w-12 h-12 flex items-center justify-center text-[#ff3a5e] text-2xl hover:bg-[#ff3a5e] hover:text-white transition-all duration-200"
                                data-remove-url="<?php echo esc_url($remove_url); ?>"
                                title="Remove from wishlist"
                                aria-label="Remove from wishlist"
                            >
                                <!-- Filled Heart SVG -->
                                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" class="w-6 h-6 pointer-events-none">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41 0.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.wishlist-remove-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var button = this;
                    var url = button.getAttribute('data-remove-url');
                    button.disabled = true;
                    button.classList.add('opacity-50');
                    fetch(url, { credentials: 'same-origin' })
                        .then(response => response.ok ? location.reload() : button.disabled = false);
                });
            });
        });
        </script>
    <?php else : ?>
        <p class="text-center text-gray-500 py-12">Your wishlist is empty.</p>
    <?php endif;
} else {
    echo '<div class="p-6 text-center text-gray-500">YITH WooCommerce Wishlist plugin is not active. Please activate it to view your wishlist.</div>';
} 