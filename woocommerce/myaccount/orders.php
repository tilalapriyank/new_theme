<?php
/**
 * Orders
 */

defined('ABSPATH') || exit;

// Include WordPress functions
require_once(ABSPATH . 'wp-includes/pluggable.php');
require_once(ABSPATH . 'wp-includes/formatting.php');
require_once(ABSPATH . 'wp-includes/link-template.php');

// Include WooCommerce functions
require_once(WC()->plugin_path() . '/includes/wc-template-functions.php');
require_once(WC()->plugin_path() . '/includes/wc-account-functions.php');

$customer_orders = wc_get_orders(array(
    'customer_id' => get_current_user_id(),
    'page' => get_query_var('paged') ? get_query_var('paged') : 1,
    'paginate' => true,
));
?>

<div class="orders-wrapper">
    <div class="orders-header">
        <h1>My Orders</h1>
    </div>

    <?php if ($customer_orders->orders) : ?>
        <div class="orders-list">
            <?php foreach ($customer_orders->orders as $order) : ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="order-info">
                            <span class="order-number">Order #<?php echo esc_html($order->get_order_number()); ?></span>
                            <span class="order-date"><?php echo esc_html($order->get_date_created()->date_i18n('F j, Y')); ?></span>
                        </div>
                        <div class="order-status <?php echo esc_attr($order->get_status()); ?>">
                            <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                        </div>
                    </div>

                    <div class="order-items">
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
                                    <h4><?php echo esc_html($item->get_name()); ?></h4>
                                    <p class="item-meta">
                                        <?php echo esc_html($item->get_quantity()); ?> × <?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="order-footer">
                        <div class="order-total">
                            <span>Total:</span>
                            <strong><?php echo wp_kses_post($order->get_formatted_order_total()); ?></strong>
                        </div>
                        <div class="order-actions">
                            <a href="<?php echo esc_url($order->get_view_order_url()); ?>" class="button view-order">View Details</a>
                            <?php if ($order->needs_payment()) : ?>
                                <a href="<?php echo esc_url($order->get_checkout_payment_url()); ?>" class="button pay-order">Pay Now</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($customer_orders->max_num_pages > 1) : ?>
            <div class="pagination">
                <?php
                echo paginate_links(array(
                    'base' => wc_get_endpoint_url('orders') . '%_%',
                    'format' => '?paged=%#%',
                    'current' => get_query_var('paged') ? get_query_var('paged') : 1,
                    'total' => $customer_orders->max_num_pages,
                    'prev_text' => '&larr;',
                    'next_text' => '&rarr;',
                ));
                ?>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <div class="no-orders">
            <p>No orders found.</p>
            <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="button">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>

<style>
.orders-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.orders-header {
    margin-bottom: 2rem;
}

.orders-header h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.order-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #eee;
}

.order-info {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.order-number {
    font-weight: 600;
}

.order-date {
    color: #666;
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

.order-items {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    margin-bottom: 1rem;
}

.order-item {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.item-image img {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 4px;
}

.item-details h4 {
    margin: 0 0 0.25rem;
    font-size: 1rem;
}

.item-meta {
    color: #666;
    font-size: 0.875rem;
    margin: 0;
}

.order-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #eee;
}

.order-total {
    font-size: 1.125rem;
}

.order-total strong {
    margin-left: 0.5rem;
}

.order-actions {
    display: flex;
    gap: 1rem;
}

.button {
    display: inline-block;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
}

.view-order {
    background: #f5f5f5;
    color: #333;
}

.pay-order {
    background: #0066cc;
    color: #fff;
}

.button:hover {
    opacity: 0.9;
}

.pagination {
    margin-top: 2rem;
    text-align: center;
}

.pagination .page-numbers {
    display: inline-block;
    padding: 0.5rem 1rem;
    margin: 0 0.25rem;
    border-radius: 4px;
    text-decoration: none;
    color: #333;
    background: #f5f5f5;
}

.pagination .current {
    background: #0066cc;
    color: #fff;
}

.no-orders {
    text-align: center;
    padding: 3rem;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.no-orders p {
    margin-bottom: 1rem;
    color: #666;
}

@media (max-width: 768px) {
    .order-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .order-footer {
        flex-direction: column;
        gap: 1rem;
        align-items: flex-start;
    }
    
    .order-actions {
        width: 100%;
    }
    
    .button {
        width: 100%;
        text-align: center;
    }
}
</style> 