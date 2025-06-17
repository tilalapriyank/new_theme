<?php
/**
 * Template Name: Cart Page
 */

get_header();

// Ensure WooCommerce is active
if (!function_exists('WC')) {
    return;
}

// Get cart items
$cart_items = WC()->cart->get_cart();
?>

<main id="main-content" class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-12">
        <h1 class="text-3xl font-bold mb-2">Shopping Cart</h1>
        <p class="text-gray-500 dark:text-gray-400 mb-8">Review your items and proceed to checkout</p>

        <?php if (empty($cart_items)) : ?>
            <!-- Empty Cart State -->
            <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-800 shadow-sm p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-gray-100 dark:bg-gray-800 rounded-full mb-6">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-gray-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="21" r="1"></circle>
                        <circle cx="20" cy="21" r="1"></circle>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-medium mb-4">Your cart is empty</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto">
                    Looks like you haven't added any products to your cart yet. Browse our collection to find something for your pup!
                </p>
                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" 
                   class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                    Continue Shopping
                </a>
            </div>
        <?php else : ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items Card -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                        <div class="p-6 pb-0 flex items-center justify-between">
                            <h2 class="text-xl font-bold">Cart Items (<?php echo WC()->cart->get_cart_contents_count(); ?>)</h2>
                        </div>
                        <div class="divide-y divide-gray-200">
                            <?php foreach ($cart_items as $cart_item_key => $cart_item) : 
                                $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                                if ($_product && $_product->exists() && $cart_item['quantity'] > 0) :
                                    $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink() : '', $cart_item, $cart_item_key);
                            ?>
                                <div class="flex items-center px-6 py-6">
                                    <!-- Product Image -->
                                    <div class="w-20 h-20 bg-gray-100 flex items-center justify-center rounded-md overflow-hidden mr-6">
                                        <?php
                                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image('woocommerce_thumbnail', ['class' => 'object-cover w-full h-full']), $cart_item, $cart_item_key);
                                        if (!$product_permalink) {
                                            echo $thumbnail;
                                        } else {
                                            printf('<a href="%s">%s</a>', esc_url($product_permalink), $thumbnail);
                                        }
                                        ?>
                                    </div>
                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-base font-bold leading-tight mb-1">
                                            <?php
                                            if (!$product_permalink) {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key));
                                            } else {
                                                echo wp_kses_post(apply_filters('woocommerce_cart_item_name', sprintf('<a href="%s">%s</a>', esc_url($product_permalink), $_product->get_name()), $cart_item, $cart_item_key));
                                            }
                                            ?>
                                        </h3>
                                        <div class="text-gray-500 text-sm mb-1">
                                            <?php echo wc_price($_product->get_price()); ?> each
                                        </div>
                                        <?php
                                        if ($_product->is_type('variation')) {
                                            $variation_attributes = $_product->get_variation_attributes();
                                            foreach ($variation_attributes as $attribute => $value) {
                                                echo '<p class="text-xs text-gray-400">' . wc_attribute_label(str_replace('attribute_', '', $attribute)) . ': ' . $value . '</p>';
                                            }
                                        }
                                        ?>
                                    </div>
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center mx-6">
                                        <form class="cart flex items-center" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                                            <div class="flex items-center border rounded-full px-2 py-1 bg-gray-50">
                                                <button type="button" class="quantity-minus px-2 text-lg text-gray-400 focus:outline-none" onclick="var qty = this.parentNode.querySelector('input[type=number]'); if(qty.value>1)qty.stepDown();">–</button>
                                                <?php
                                                if ($_product->is_sold_individually()) {
                                                    $min_quantity = 1;
                                                    $max_quantity = 1;
                                                } else {
                                                    $min_quantity = 1;
                                                    $max_quantity = $_product->get_max_purchase_quantity();
                                                }
                                                $product_quantity = woocommerce_quantity_input(
                                                    array(
                                                        'input_name'   => "cart[{$cart_item_key}][qty]",
                                                        'input_value'  => $cart_item['quantity'],
                                                        'max_value'    => $max_quantity,
                                                        'min_value'    => $min_quantity,
                                                        'product_name' => $_product->get_name(),
                                                        'classes'      => 'w-10 text-center border-0 bg-transparent focus:ring-0',
                                                    ),
                                                    $_product,
                                                    false
                                                );
                                                echo apply_filters('woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item);
                                                ?>
                                                <button type="button" class="quantity-plus px-2 text-lg text-gray-400 focus:outline-none" onclick="var qty = this.parentNode.querySelector('input[type=number]'); qty.stepUp();">+</button>
                                            </div>
                                            <button type="submit" class="hidden" name="update_cart" value="<?php esc_attr_e('Update', 'woocommerce'); ?>">Update</button>
                                        </form>
                                    </div>
                                    <!-- Price & Remove -->
                                    <div class="flex flex-col items-end min-w-[100px]">
                                        <div class="text-lg font-medium mb-2">
                                            <?php
                                            echo apply_filters('woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal($_product, $cart_item['quantity']), $cart_item, $cart_item_key);
                                            ?>
                                        </div>
                                        <a href="<?php echo esc_url(wc_get_cart_remove_url($cart_item_key)); ?>" class="inline-flex items-center justify-center w-8 h-8 rounded hover:bg-gray-100 text-gray-400 hover:text-red-600" data-product_id="<?php echo esc_attr($_product->get_id()); ?>" data-product_sku="<?php echo esc_attr($_product->get_sku()); ?>" aria-label="Remove item">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 7h12M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3m2 0v12a2 2 0 01-2 2H8a2 2 0 01-2-2V7h12z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 11v6m4-6v6" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            <?php endif; endforeach; ?>
                        </div>
                        <!-- Continue Shopping Button -->
                        <div class="p-6 border-t bg-gray-50">
                            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="inline-flex items-center text-pink-500 hover:text-pink-700 font-medium text-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
                <!-- Order Summary Card -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg border border-gray-200 shadow-sm p-6">
                        <h2 class="text-lg font-bold mb-4">Order Summary</h2>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium"><?php wc_cart_totals_subtotal_html(); ?></span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium"><?php echo WC()->cart->get_cart_shipping_total(); ?></span>
                        </div>
                        <?php if (wc_tax_enabled() && WC()->cart->get_taxes_total() > 0) : ?>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Tax (<?php echo WC()->countries->get_base_country(); ?>)</span>
                            <span class="font-medium"><?php echo wc_price(WC()->cart->get_taxes_total()); ?></span>
                        </div>
                        <?php endif; ?>
                        <div class="flex justify-between mb-6 pt-4 border-t">
                            <span class="text-lg font-bold">Total</span>
                            <span class="text-lg font-bold"><?php wc_cart_totals_order_total_html(); ?></span>
                        </div>
                        <!-- Promo Code Form -->
                        <form class="flex mb-4" action="<?php echo esc_url(wc_get_cart_url()); ?>" method="post">
                            <input type="text" name="coupon_code" class="flex-1 border border-gray-300 rounded-l px-3 py-2 text-sm" placeholder="Promo code" value="" />
                            <button type="submit" class="px-4 py-2 bg-gray-100 border border-l-0 border-gray-300 rounded-r text-gray-700 hover:bg-gray-200" name="apply_coupon" value="Apply">Apply</button>
                        </form>
                        <!-- Checkout Button -->
                        <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" 
                           class="hype-checkout-button w-full inline-flex items-center justify-center px-6 py-3 border border-transparent text-black font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700">
                            Proceed to Checkout
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                        </a>
                        <p class="text-xs text-gray-400 mt-2 text-center">Secure checkout powered by Stripe</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<style>
.hype-checkout-button {
background-color: #ff3a5e !important;
}

</style>



<?php get_footer(); ?> 