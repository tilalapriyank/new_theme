<?php
/**
 * My Account Dashboard
 */

defined('ABSPATH') || exit;

$user = wp_get_current_user();
$customer = new WC_Customer($user->ID);
$first_name = $customer->get_first_name();
$last_name = $customer->get_last_name();
$email = $customer->get_email();
$phone = $customer->get_billing_phone();
?>

<div class="account-dashboard-wrapper">
    <div class="account-header">
        <div class="welcome-message">
            <h1>Welcome back, <?php echo esc_html($first_name); ?>!</h1>
            <p>Manage your account and view your orders</p>
        </div>
    </div>

    <div class="account-content">
        <div class="account-sidebar">
            <nav class="account-navigation">
                <?php
                $menu_items = wc_get_account_menu_items();
                foreach ($menu_items as $endpoint => $label) :
                    $active = is_wc_endpoint_url($endpoint) ? 'active' : '';
                ?>
                    <a href="<?php echo esc_url(wc_get_account_endpoint_url($endpoint)); ?>" class="nav-item <?php echo esc_attr($active); ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
        </div>

        <div class="account-main">
            <div class="account-overview">
                <div class="overview-card">
                    <h3>Account Information</h3>
                    <div class="info-grid">
                        <div class="info-item">
                            <label>Name</label>
                            <p><?php echo esc_html($first_name . ' ' . $last_name); ?></p>
                        </div>
                        <div class="info-item">
                            <label>Email</label>
                            <p><?php echo esc_html($email); ?></p>
                        </div>
                        <div class="info-item">
                            <label>Phone</label>
                            <p><?php echo esc_html($phone); ?></p>
                        </div>
                    </div>
                </div>

                <div class="overview-card">
                    <h3>Recent Orders</h3>
                    <?php
                    $customer_orders = wc_get_orders(array(
                        'customer_id' => get_current_user_id(),
                        'limit' => 5,
                        'orderby' => 'date',
                        'order' => 'DESC',
                    ));

                    if (!empty($customer_orders)) : ?>
                        <div class="orders-list">
                            <?php foreach ($customer_orders as $order) : ?>
                                <div class="order-item">
                                    <div class="order-info">
                                        <span class="order-number">#<?php echo esc_html($order->get_order_number()); ?></span>
                                        <span class="order-date"><?php echo esc_html($order->get_date_created()->date_i18n('M d, Y')); ?></span>
                                        <span class="order-status <?php echo esc_attr($order->get_status()); ?>">
                                            <?php echo esc_html(wc_get_order_status_name($order->get_status())); ?>
                                        </span>
                                    </div>
                                    <div class="order-total">
                                        <?php echo wp_kses_post($order->get_formatted_order_total()); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <a href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>" class="view-all-link">View All Orders</a>
                    <?php else : ?>
                        <p class="no-orders">No orders found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.account-dashboard-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.account-header {
    margin-bottom: 2rem;
}

.welcome-message h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.account-content {
    display: grid;
    grid-template-columns: 250px 1fr;
    gap: 2rem;
}

.account-navigation {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.nav-item {
    padding: 1rem;
    text-decoration: none;
    color: #333;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.nav-item:hover,
.nav-item.active {
    background-color: #f5f5f5;
    color: #000;
}

.overview-card {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.overview-card h3 {
    margin-bottom: 1rem;
    font-size: 1.25rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item label {
    display: block;
    color: #666;
    margin-bottom: 0.25rem;
}

.orders-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem;
    background: #f9f9f9;
    border-radius: 8px;
}

.order-info {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.order-status {
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-size: 0.875rem;
}

.order-status.completed {
    background: #e3f9e5;
    color: #1b4332;
}

.order-status.processing {
    background: #e3f2fd;
    color: #1a237e;
}

.view-all-link {
    display: inline-block;
    margin-top: 1rem;
    color: #0066cc;
    text-decoration: none;
}

.view-all-link:hover {
    text-decoration: underline;
}

.no-orders {
    color: #666;
    font-style: italic;
}

@media (max-width: 768px) {
    .account-content {
        grid-template-columns: 1fr;
    }
    
    .account-sidebar {
        margin-bottom: 2rem;
    }
    
    .account-navigation {
        flex-direction: row;
        flex-wrap: wrap;
    }
    
    .nav-item {
        flex: 1;
        text-align: center;
    }
}
</style> 