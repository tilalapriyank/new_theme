<?php
/**
 * Custom Checkout Form Template for Hype Pups
 * Place in: wp-content/themes/hype-pups/woocommerce/checkout/form-checkout.php
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
    echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
    return;
}
?>

<div class="container mx-auto px-4 py-8" id="main-content">
  <div class="mb-4">
    <a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="inline-flex items-center text-sm text-gray-600 hover:text-[#FF3A5E]">
      <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
      <?php esc_html_e( 'Back to Cart', 'woocommerce' ); ?>
    </a>
  </div>

  <h1 class="text-3xl font-bold mb-6" id="page-title"><?php esc_html_e( 'Checkout', 'woocommerce' ); ?></h1>

  <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
      <form name="checkout" method="post" class="checkout woocommerce-checkout bg-white p-6 rounded-lg border border-gray-200 shadow-sm"
        action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
        <div class="space-y-6">
          <!-- Contact & Shipping fields -->
          <?php do_action( 'woocommerce_checkout_billing' ); ?>
          <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            <?php do_action( 'woocommerce_checkout_shipping' ); ?>
          <?php endif; ?>

          <!-- Shipping methods -->
          <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            <h2 class="text-xl font-semibold mb-4"><?php esc_html_e( 'Shipping Method', 'woocommerce' ); ?></h2>
            <?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
            <div class="space-y-3">
              <!-- Shipping methods are rendered by WooCommerce hooks -->
            </div>
            <?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
          <?php endif; ?>

          <!-- Payment -->
          <div class="pt-4">
            <?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
            <h2 class="text-xl font-semibold mb-4"><?php esc_html_e( 'Payment', 'woocommerce' ); ?></h2>
            <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
            <div id="order_review" class="woocommerce-checkout-review-order">
              <?php woocommerce_order_review(); ?>
            </div>
            <?php do_action( 'woocommerce_checkout_after_order_review' ); ?>
          </div>
        </div>
      </form>
    </div>

    <div class="lg:col-span-1">
      <!-- Order Summary -->
      <div class="bg-white rounded-lg border border-gray-200 shadow-sm sticky top-24">
        <div class="p-6">
          <h2 class="text-xl font-semibold mb-4"><?php esc_html_e( 'Order Summary', 'woocommerce' ); ?></h2>
          <div class="space-y-4 mb-6 max-h-80 overflow-y-auto pr-2">
            <?php foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) :
              $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
              $product_id = $cart_item['product_id'];
              if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) :
            ?>
              <div class="flex gap-3">
                <div class="w-16 h-16 rounded-md border border-gray-200 overflow-hidden relative flex-shrink-0">
                  <?php echo $_product->get_image( 'woocommerce_thumbnail', [ 'class' => 'w-full h-full object-cover' ] ); ?>
                </div>
                <div class="flex-1">
                  <p class="font-medium text-sm line-clamp-1"><?php echo esc_html( $_product->get_name() ); ?></p>
                  <div class="flex text-xs text-gray-500 mt-1">
                    <p><?php printf( esc_html__( 'Qty: %d', 'woocommerce' ), $cart_item['quantity'] ); ?></p>
                    <?php if ( isset( $cart_item['variation']['attribute_pa_size'] ) ) : ?>
                      <p class="ml-3"><?php echo esc_html( $cart_item['variation']['attribute_pa_size'] ); ?></p>
                    <?php endif; ?>
                  </div>
                  <p class="mt-1 font-medium"><?php echo wc_price( $_product->get_price() * $cart_item['quantity'] ); ?></p>
                </div>
              </div>
            <?php endif; endforeach; ?>
          </div>
          <div class="space-y-2 border-t border-gray-200 pt-4">
            <div class="flex justify-between">
              <span class="text-gray-600"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
              <span><?php wc_cart_totals_subtotal_html(); ?></span>
            </div>
            <?php if ( WC()->cart->get_coupons() ) : ?>
            <div class="flex justify-between text-green-600">
              <span><?php esc_html_e( 'Discount', 'woocommerce' ); ?></span>
              <span><?php wc_cart_totals_coupon_html( array_keys( WC()->cart->get_coupons() )[0] ); ?></span>
            </div>
            <?php endif; ?>
            <div class="flex justify-between">
              <span class="text-gray-600"><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
              <span><?php wc_cart_totals_shipping_html(); ?></span>
            </div>
            <div class="flex justify-between">
              <span class="text-gray-600"><?php esc_html_e( 'Tax', 'woocommerce' ); ?></span>
              <span><?php wc_cart_totals_taxes_total_html(); ?></span>
            </div>
            <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
              <span><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
              <span><?php wc_cart_totals_order_total_html(); ?></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?> 