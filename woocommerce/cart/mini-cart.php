<?php
/**
 * Mini Cart Template
 * 
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_mini_cart'); ?>

<div class="mini-cart-overlay" id="mini-cart-overlay" style="display: flex;">
    <div class="mini-cart-container">
        <!-- Cart Header -->
        <div class="mini-cart-header">
            <div class="cart-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.5 5.1 16.5H17M17 13V16.5M9 19.5C9.8 19.5 10.5 20.2 10.5 21S9.8 22.5 9 22.5 7.5 21.8 7.5 21 8.2 19.5 9 19.5ZM20 19.5C20.8 19.5 21.5 20.2 21.5 21S20.8 22.5 20 22.5 18.5 21.8 18.5 21 19.2 19.5 20 19.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <span>Your Cart (<span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span> items)</span>
            </div>
        </div>

        <!-- Cart Items -->
        <div class="mini-cart-items">
            <?php if (!WC()->cart->is_empty()) : ?>
                <?php foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) :
                    $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
                    $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);
                    
                    if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) :
                        $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                        $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                        $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                        $product_permalink = apply_filters('woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink($cart_item) : '', $cart_item, $cart_item_key);
                ?>
                    <div class="mini-cart-item" data-cart-item-key="<?php echo esc_attr($cart_item_key); ?>">
                        <div class="item-image">
                            <?php if (empty($product_permalink)) : ?>
                                <?php echo $thumbnail; ?>
                            <?php else : ?>
                                <a href="<?php echo esc_url($product_permalink); ?>">
                                    <?php echo $thumbnail; ?>
                                </a>
                            <?php endif; ?>
                        </div>
                        
                        <div class="item-info">
                            <div class="item-name">
                                <?php if (empty($product_permalink)) : ?>
                                    <?php echo wp_kses_post($product_name); ?>
                                <?php else : ?>
                                    <a href="<?php echo esc_url($product_permalink); ?>">
                                        <?php echo wp_kses_post($product_name); ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                            
                            <div class="item-controls">
                                <div class="quantity-controls">
                                    <button class="quantity-btn minus" onclick="updateQuantity('<?php echo esc_attr($cart_item_key); ?>', <?php echo $cart_item['quantity'] - 1; ?>)">-</button>
                                    <span class="quantity"><?php echo $cart_item['quantity']; ?></span>
                                    <button class="quantity-btn" onclick="updateQuantity('<?php echo esc_attr($cart_item_key); ?>', <?php echo $cart_item['quantity'] + 1; ?>)">+</button>
                                    <button class="remove-item" onclick="removeCartItem('<?php echo esc_attr($cart_item_key); ?>')">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </button>
                                </div>
                                
                                <div class="item-price">
                                    <?php echo wc_price($_product->get_price()); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="empty-cart">
                    <p>Your cart is empty</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Cart Summary -->
        <?php if (!WC()->cart->is_empty()) : ?>
            <div class="mini-cart-summary">
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span class="subtotal-amount"><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                </div>
                
                <div class="summary-row">
                    <span>Shipping</span>
                    <span class="shipping-amount">
                        <?php if (WC()->cart->needs_shipping() && WC()->cart->show_shipping()) : ?>
                            <?php 
                            $shipping_total = WC()->cart->get_shipping_total();
                            echo $shipping_total > 0 ? wc_price($shipping_total) : 'Free';
                            ?>
                        <?php else : ?>
                            Free
                        <?php endif; ?>
                    </span>
                </div>
                
                <div class="summary-row">
                    <span>Tax (8%)</span>
                    <span class="tax-amount"><?php echo WC()->cart->get_taxes_total(); ?></span>
                </div>
                
                <div class="summary-row total-row">
                    <span>Total</span>
                    <span class="total-amount"><?php echo WC()->cart->get_cart_total(); ?></span>
                </div>
                
                <div class="cart-notice">
                    <small>Shipping and taxes calculated at checkout.</small>
                </div>
            </div>

            <!-- Cart Actions -->
            <div class="mini-cart-actions">
                <button class="view-cart-btn" onclick="window.location.href='<?php echo wc_get_cart_url(); ?>'">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.5 5.1 16.5H17M17 13V16.5M9 19.5C9.8 19.5 10.5 20.2 10.5 21S9.8 22.5 9 22.5 7.5 21.8 7.5 21 8.2 19.5 9 19.5ZM20 19.5C20.8 19.5 21.5 20.2 21.5 21S20.8 22.5 20 22.5 18.5 21.8 18.5 21 19.2 19.5 20 19.5Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    View Cart
                </button>
                
                <button class="checkout-btn" onclick="window.location.href='<?php echo wc_get_checkout_url(); ?>'">
                    Checkout
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Mini Cart Styles */
.mini-cart-overlay {
    position: fixed;
    top: 0;
    right: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    justify-content: flex-end;
    align-items: flex-start;
}

.mini-cart-container {
    background: white;
    width: 420px;
    height: 100vh;
    overflow-y: auto;
    box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
}

/* Header */
.mini-cart-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
    background: white;
    position: sticky;
    top: 0;
    z-index: 10;
}

.cart-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 16px;
    color: #111827;
}

.cart-title svg {
    color: #6b7280;
}

.close-cart {
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    color: #6b7280;
    transition: color 0.2s;
}

.close-cart:hover {
    color: #111827;
}

/* Cart Items */
.mini-cart-items {
    flex: 1;
    padding: 0 24px;
    max-height: calc(100vh - 300px);
    overflow-y: auto;
}

.mini-cart-item {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px 0;
    border-bottom: 1px solid #f3f4f6;
    position: relative;
}

.item-image {
    width: 60px;
    height: 60px;
    flex-shrink: 0;
    border-radius: 8px;
    overflow: hidden;
    background: #f9fafb;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.item-name {
    font-weight: 500;
    font-size: 14px;
    color: #111827;
    line-height: 1.4;
}

.item-name a {
    color: inherit;
    text-decoration: none;
}

.item-name a:hover {
    color: #ef4444;
}

.item-controls {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.quantity-controls {
    display: flex;
    align-items: center;
    gap: 8px;
}

.quantity-btn {
    width: 28px;
    height: 28px;
    border: 1px solid #e5e7eb;
    background: white;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
    transition: all 0.2s;
}

.quantity-btn:hover {
    border-color: #d1d5db;
    background: #f9fafb;
}

.quantity-btn.plus {
    background: #ef4444;
    border-color: #ef4444;
    color: white;
}

.quantity-btn.plus:hover {
    background: #dc2626;
    border-color: #dc2626;
}

.quantity {
    font-weight: 500;
    font-size: 14px;
    min-width: 20px;
    text-align: center;
    color: #111827;
}

.item-price {
    font-weight: 600;
    font-size: 14px;
    color: #111827;
}

.remove-item {
    width: 28px;
    height: 28px;
    background: none;
    border: none;
    cursor: pointer;
    color: #9ca3af;
    transition: color 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-left: 8px;
}

.remove-item:hover {
    color: #ef4444;
}

/* Empty Cart */
.empty-cart {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

/* Cart Summary */
.mini-cart-summary {
    padding: 24px;
    border-top: 1px solid #e5e7eb;
    background: #fafafa;
}

.summary-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
    font-size: 14px;
}

.summary-row:last-child {
    margin-bottom: 0;
}

.summary-row span:first-child {
    color: #6b7280;
}

.summary-row span:last-child {
    font-weight: 500;
    color: #111827;
}

.total-row {
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
    margin-top: 12px;
    font-size: 16px;
    font-weight: 600;
}

.total-row span {
    color: #111827 !important;
}

.cart-notice {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
}

.cart-notice small {
    color: #9ca3af;
    font-size: 12px;
}

/* Cart Actions */
.mini-cart-actions {
    padding: 24px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    gap: 12px;
}

.view-cart-btn {
    flex: 1;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    background: white;
    color: #374151;
    font-weight: 500;
    border-radius: 8px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s;
}

.view-cart-btn:hover {
    border-color: #d1d5db;
    background: #f9fafb;
}

.checkout-btn {
    flex: 1;
    padding: 12px 16px;
    background: #ef4444;
    color: white;
    font-weight: 500;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.2s;
}

.checkout-btn:hover {
    background: #dc2626;
}

/* Responsive */
@media (max-width: 480px) {
    .mini-cart-container {
        width: 100vw;
    }
}
</style>

<script>
// Mini Cart JavaScript Functions
function openMiniCart() {
    document.getElementById('mini-cart-overlay').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeMiniCart() {
    document.getElementById('mini-cart-overlay').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close cart when clicking overlay
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('mini-cart-overlay')) {
        closeMiniCart();
    }
});

// Update quantity function
function updateQuantity(cartItemKey, newQuantity) {
    if (newQuantity < 1) {
        removeCartItem(cartItemKey);
        return;
    }
    
    const data = new FormData();
    data.append('action', 'update_cart_item_quantity');
    data.append('cart_item_key', cartItemKey);
    data.append('quantity', newQuantity);
    data.append('nonce', '<?php echo wp_create_nonce("update_cart_item_quantity"); ?>');
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh to update cart
        }
    })
    .catch(error => {
        console.error('Error updating quantity:', error);
    });
}

// Remove cart item function
function removeCartItem(cartItemKey) {
    const data = new FormData();
    data.append('action', 'remove_cart_item');
    data.append('cart_item_key', cartItemKey);
    data.append('nonce', '<?php echo wp_create_nonce("remove_cart_item"); ?>');
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: data
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh to update cart
        }
    })
    .catch(error => {
        console.error('Error removing item:', error);
    });
}
</script>

<?php do_action('woocommerce_after_mini_cart'); ?>