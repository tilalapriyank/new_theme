<?php
/**
 * View Order
 */

defined('ABSPATH') || exit;

// Include WordPress functions
require_once(ABSPATH . 'wp-includes/pluggable.php');
require_once(ABSPATH . 'wp-includes/formatting.php');
require_once(ABSPATH . 'wp-includes/link-template.php');

// Include WooCommerce functions
require_once(WC()->plugin_path() . '/includes/wc-template-functions.php');
require_once(WC()->plugin_path() . '/includes/wc-account-functions.php');

$order = wc_get_order($order_id);

if (!$order || !current_user_can('view_order', $order_id)) {
    wp_die('Invalid order.');
}
?>

<div class="view-order-wrapper">
    <div class="order-header">
        <div class="header-content">
            <h1>Order #<?php echo esc_html($order->get_order_number()); ?></h1>
            <p class="order-date"><?php echo esc_html($order->get_date_created()->date_i18n('F j, Y')); ?></p>
        </div>
        <div class="order-status <?php echo esc_attr($order->get_status()); ?>">
            <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
        </div>
    </div>

    <div class="order-content">
        <div class="order-details">
            <div class="details-section">
                <h2>Order Details</h2>
                <div class="details-grid">
                    <div class="detail-item">
                        <label>Order Number</label>
                        <p>#<?php echo esc_html($order->get_order_number()); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Date</label>
                        <p><?php echo esc_html($order->get_date_created()->date_i18n('F j, Y')); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Total</label>
                        <p><?php echo wp_kses_post($order->get_formatted_order_total()); ?></p>
                    </div>
                    <div class="detail-item">
                        <label>Payment Method</label>
                        <p><?php echo wp_kses_post($order->get_payment_method_title()); ?></p>
                    </div>
                </div>
            </div>

            <div class="details-section">
                <h2>Billing Address</h2>
                <address>
                    <?php echo wp_kses_post($order->get_formatted_billing_address()); ?>
                    <?php if ($order->get_billing_phone()) : ?>
                        <p>Phone: <?php echo esc_html($order->get_billing_phone()); ?></p>
                    <?php endif; ?>
                    <?php if ($order->get_billing_email()) : ?>
                        <p>Email: <?php echo esc_html($order->get_billing_email()); ?></p>
                    <?php endif; ?>
                </address>
            </div>

            <div class="details-section">
                <h2>Shipping Address</h2>
                <address>
                    <?php echo wp_kses_post($order->get_formatted_shipping_address()); ?>
                </address>
            </div>
        </div>

        <div class="order-items">
            <h2>Order Items</h2>
            <div class="items-list">
                <?php
                foreach ($order->get_items() as $item) :
                    $product = $item->get_product();
                    if (!$product) continue;
                ?>
                    <div class="order-item">
                        <div class="item-image">
                            <?php echo $product->get_image('thumbnail'); ?>
                        </div>
                        <div class="item-details">
                            <h3><?php echo esc_html($item->get_name()); ?></h3>
                            <p class="item-meta">
                                <?php echo esc_html($item->get_quantity()); ?> × <?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?>
                            </p>
                            <?php if ($item->get_meta_data()) : ?>
                                <div class="item-meta-data">
                                    <?php foreach ($item->get_meta_data() as $meta) : ?>
                                        <p class="meta-item">
                                            <strong><?php echo esc_html($meta->key); ?>:</strong>
                                            <?php echo esc_html($meta->value); ?>
                                        </p>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="item-total">
                            <?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="order-totals">
                <div class="totals-row">
                    <span>Subtotal</span>
                    <span><?php echo wp_kses_post($order->get_subtotal_to_display()); ?></span>
                </div>
                <?php if ($order->get_shipping_total() > 0) : ?>
                    <div class="totals-row">
                        <span>Shipping</span>
                        <span><?php echo wp_kses_post($order->get_shipping_to_display()); ?></span>
                    </div>
                <?php endif; ?>
                <?php if ($order->get_total_tax() > 0) : ?>
                    <div class="totals-row">
                        <span>Tax</span>
                        <span><?php echo wp_kses_post($order->get_total_tax()); ?></span>
                    </div>
                <?php endif; ?>
                <div class="totals-row total">
                    <span>Total</span>
                    <span><?php echo wp_kses_post($order->get_formatted_order_total()); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="order-actions">
        <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>" class="button back-button">← Back to Orders</a>
        <?php if ($order->needs_payment()) : ?>
            <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="button pay-button">Pay Now</a>
        <?php endif; ?>
    </div>
</div>

<style>
.view-order-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.header-content h1 {
    font-size: 2rem;
    margin: 0 0 0.5rem;
}

.order-date {
    color: #666;
    margin: 0;
}

.order-status {
    padding: 0.5rem 1rem;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
}

.order-status.completed {
    background: #e3f9e5;
    color: #1b4332;
}

.order-status.processing {
    background: #e3f2fd;
    color: #1a237e;
}

.order-status.on-hold {
    background: #fff3e0;
    color: #e65100;
}

.order-content {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
}

.details-section {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.details-section h2 {
    font-size: 1.25rem;
    margin: 0 0 1rem;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.detail-item label {
    display: block;
    color: #666;
    margin-bottom: 0.25rem;
}

.detail-item p {
    margin: 0;
    font-weight: 500;
}

address {
    font-style: normal;
    margin: 0;
    line-height: 1.6;
}

.order-items {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.order-items h2 {
    font-size: 1.25rem;
    margin: 0 0 1.5rem;
}

.items-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.order-item {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 1rem;
    align-items: center;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #eee;
}

.order-item:last-child {
    border-bottom: none;
    padding-bottom: 0;
}

.item-image img {
    width: 80px;
    height: 80px;
    object-fit: cover;
    border-radius: 4px;
}

.item-details h3 {
    margin: 0 0 0.5rem;
    font-size: 1rem;
}

.item-meta {
    color: #666;
    margin: 0 0 0.5rem;
}

.item-meta-data {
    font-size: 0.875rem;
    color: #666;
}

.meta-item {
    margin: 0.25rem 0;
}

.item-total {
    font-weight: 500;
}

.order-totals {
    border-top: 1px solid #eee;
    padding-top: 1.5rem;
}

.totals-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 0.75rem;
}

.totals-row.total {
    font-weight: 600;
    font-size: 1.125rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.order-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
}

.button {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.back-button {
    background: #f5f5f5;
    color: #333;
}

.pay-button {
    background: #0066cc;
    color: #fff;
}

.button:hover {
    opacity: 0.9;
}

@media (max-width: 768px) {
    .order-content {
        grid-template-columns: 1fr;
    }
    
    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .order-item {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .item-image {
        margin: 0 auto;
    }
    
    .order-actions {
        flex-direction: column;
    }
    
    .button {
        width: 100%;
        text-align: center;
    }
}
</style> 