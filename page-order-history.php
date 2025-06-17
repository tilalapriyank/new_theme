<?php
/**
 * Template Name: Order History
 */

get_header(); ?>

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold mb-8">Order History</h1>
        
        <?php
        if (is_user_logged_in()) {
            $customer_orders = wc_get_orders(array(
                'customer_id' => get_current_user_id(),
                'status' => array('completed', 'processing'),
                'limit' => -1,
            ));

            if (!empty($customer_orders)) {
                echo '<div class="bg-white rounded-lg shadow overflow-hidden">';
                echo '<div class="overflow-x-auto">';
                echo '<table class="min-w-full divide-y divide-gray-200">';
                echo '<thead class="bg-gray-50">';
                echo '<tr>';
                echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>';
                echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>';
                echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>';
                echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>';
                echo '<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody class="bg-white divide-y divide-gray-200">';

                foreach ($customer_orders as $order) {
                    $order_id = $order->get_id();
                    $order_date = $order->get_date_created();
                    $order_status = $order->get_status();
                    $order_total = $order->get_formatted_order_total();
                    
                    echo '<tr class="hover:bg-gray-50">';
                    echo '<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#' . $order_id . '</td>';
                    echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . $order_date->date_i18n('F j, Y') . '</td>';
                    echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . wc_get_order_status_name($order_status) . '</td>';
                    echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">' . $order_total . '</td>';
                    echo '<td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">';
                    echo '<a href="' . $order->get_view_order_url() . '" class="text-indigo-600 hover:text-indigo-900">View Details</a>';
                    echo '</td>';
                    echo '</tr>';
                }

                echo '</tbody>';
                echo '</table>';
                echo '</div>';
                echo '</div>';
            } else {
                echo '<div class="bg-white rounded-lg shadow p-6 text-center">';
                echo '<p class="text-gray-500">No orders found.</p>';
                echo '</div>';
            }
        } else {
            echo '<div class="bg-white rounded-lg shadow p-6 text-center">';
            echo '<p class="text-gray-500">Please <a href="' . wc_get_page_permalink('myaccount') . '" class="text-indigo-600 hover:text-indigo-900">login</a> to view your order history.</p>';
            echo '</div>';
        }
        ?>
    </div>
</div>

<?php get_footer(); ?> 