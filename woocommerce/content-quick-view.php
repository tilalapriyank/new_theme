<?php
$product = isset($args['product']) ? $args['product'] : null;
if (!$product) return;
?>
<div class="quick-view-modal-content">
    <div class="quick-view-image">
        <?php echo $product->get_image('large'); ?>
    </div>
    <div class="quick-view-details">
        <h2><?php echo esc_html($product->get_name()); ?></h2>
        <div class="quick-view-price"><?php echo $product->get_price_html(); ?></div>
        <div class="quick-view-desc"><?php echo $product->get_short_description(); ?></div>
        <form class="quick-view-cart" method="post">
            <?php
            if ($product->is_type('variable')) {
                woocommerce_variable_add_to_cart();
            } else {
                woocommerce_simple_add_to_cart();
            }
            ?>
        </form>
    </div>
</div> 