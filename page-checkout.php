<?php
/**
 * Custom Checkout Form Template for Hype Pups
 * Place in: wp-content/themes/hype-pups/woocommerce/checkout/form-checkout.php
 */

defined( 'ABSPATH' ) || exit;
get_header();
$checkout = WC()->checkout();

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

  <!-- Progress Bar -->
  <div class="checkout-progress flex items-center justify-between mb-8">
    <div class="step flex flex-col items-center w-1/3" data-step="1">
      <div class="progress-circle rounded-full w-12 h-12 flex items-center justify-center text-white font-bold bg-[#FF3A5E]" data-step="1">
        <span class="progress-content text-xl">1</span>
      </div>
      <span class="mt-2 text-base font-medium text-[#1A202C]">Shipping</span>
    </div>
    <div class="progress-line flex-1 h-1 bg-gray-200 mx-2" data-bar="2"></div>
    <div class="step flex flex-col items-center w-1/3" data-step="2">
      <div class="progress-circle rounded-full w-12 h-12 flex items-center justify-center text-white font-bold bg-gray-200" data-step="2">
        <span class="progress-content text-xl">2</span>
      </div>
      <span class="mt-2 text-base font-medium text-[#1A202C]">Payment</span>
    </div>
    <div class="progress-line flex-1 h-1 bg-gray-200 mx-2" data-bar="3"></div>
    <div class="step flex flex-col items-center w-1/3" data-step="3">
      <div class="progress-circle rounded-full w-12 h-12 flex items-center justify-center text-white font-bold bg-gray-200" data-step="3">
        <span class="progress-content text-xl">3</span>
      </div>
      <span class="mt-2 text-base font-medium text-[#1A202C]">Review</span>
    </div>
  </div>

  <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
    <div class="lg:col-span-2">
      <form name="checkout" method="post" class="checkout woocommerce-checkout bg-white p-6 rounded-lg border border-gray-200 shadow-sm"
        action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
        
        <?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
        
        <!-- Step 1: Contact & Shipping Information -->
        <div class="checkout-step" data-step="1">
          <div class="space-y-6">
            <!-- Contact Information -->
            <div class="bg-white p-6 rounded-lg border border-gray-200">
              <h2 class="text-lg font-semibold mb-4">Contact Information</h2>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label for="billing_first_name" class="block text-sm font-medium mb-1">First Name <span class="text-[#FF3A5E]">*</span></label>
                  <input type="text" name="billing_first_name" id="billing_first_name" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" value="<?php echo esc_attr( $checkout->get_value( 'billing_first_name' ) ); ?>" required>
                </div>
                <div>
                  <label for="billing_last_name" class="block text-sm font-medium mb-1">Last Name <span class="text-[#FF3A5E]">*</span></label>
                  <input type="text" name="billing_last_name" id="billing_last_name" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" value="<?php echo esc_attr( $checkout->get_value( 'billing_last_name' ) ); ?>" required>
                </div>
                <div>
                  <label for="billing_email" class="block text-sm font-medium mb-1">Email <span class="text-[#FF3A5E]">*</span></label>
                  <input type="email" name="billing_email" id="billing_email" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" value="<?php echo esc_attr( $checkout->get_value( 'billing_email' ) ); ?>" required>
                </div>
                <div>
                  <label for="billing_phone" class="block text-sm font-medium mb-1">Phone <span class="text-[#FF3A5E]">*</span></label>
                  <input type="tel" name="billing_phone" id="billing_phone" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" value="<?php echo esc_attr( $checkout->get_value( 'billing_phone' ) ); ?>" required placeholder="(123) 456-7890">
                </div>
              </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white p-6 rounded-lg border border-gray-200">
              <h2 class="text-lg font-semibold mb-4">Shipping Address</h2>
              
              <!-- Same as billing checkbox -->
              <div class="mb-4">
                <label class="flex items-center">
                  <input type="checkbox" id="ship_to_different_address" name="ship_to_different_address" value="1" class="mr-2 accent-[#FF3A5E]">
                  <span class="text-sm">Ship to a different address?</span>
                </label>
              </div>

              <div id="shipping-fields">
                <div class="mb-4">
                  <label for="billing_address_1" class="block text-sm font-medium mb-1">Street Address <span class="text-[#FF3A5E]">*</span></label>
                  <input type="text" name="billing_address_1" id="billing_address_1" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" value="<?php echo esc_attr( $checkout->get_value( 'billing_address_1' ) ); ?>" required>
                </div>
                <div class="mb-4">
                  <label for="billing_address_2" class="block text-sm font-medium mb-1">Apartment, suite, etc. (optional)</label>
                  <input type="text" name="billing_address_2" id="billing_address_2" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" value="<?php echo esc_attr( $checkout->get_value( 'billing_address_2' ) ); ?>">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                  <div>
                    <label for="billing_city" class="block text-sm font-medium mb-1">City <span class="text-[#FF3A5E]">*</span></label>
                    <input type="text" name="billing_city" id="billing_city" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" value="<?php echo esc_attr( $checkout->get_value( 'billing_city' ) ); ?>" required>
                  </div>
                  <div>
                    <label for="billing_state" class="block text-sm font-medium mb-1">State <span class="text-[#FF3A5E]">*</span></label>
                    <?php
                    $states = WC()->countries->get_states( WC()->countries->get_base_country() );
                    $selected_state = $checkout->get_value( 'billing_state' );
                    ?>
                    <select name="billing_state" id="billing_state" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" required>
                      <option value="">Select state</option>
                      <?php foreach ( $states as $state_code => $state_name ) : ?>
                        <option value="<?php echo esc_attr( $state_code ); ?>" <?php selected( $selected_state, $state_code ); ?>><?php echo esc_html( $state_name ); ?></option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div>
                    <label for="billing_postcode" class="block text-sm font-medium mb-1">Zip Code <span class="text-[#FF3A5E]">*</span></label>
                    <input type="text" name="billing_postcode" id="billing_postcode" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" value="<?php echo esc_attr( $checkout->get_value( 'billing_postcode' ) ); ?>" required>
                  </div>
                </div>
                <div class="mb-4">
                  <label for="billing_country" class="block text-sm font-medium mb-1">Country <span class="text-[#FF3A5E]">*</span></label>
                  <?php
                  $countries = WC()->countries->get_allowed_countries();
                  $selected_country = $checkout->get_value( 'billing_country' ) ?: WC()->countries->get_base_country();
                  ?>
                  <select name="billing_country" id="billing_country" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" required>
                    <?php foreach ( $countries as $country_code => $country_name ) : ?>
                      <option value="<?php echo esc_attr( $country_code ); ?>" <?php selected( $selected_country, $country_code ); ?>><?php echo esc_html( $country_name ); ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
            </div>

            <button type="button" class="continue-to-step bg-[#FF3A5E] text-white w-full py-3 rounded font-semibold mt-6 hover:bg-[#E02E50] transition-colors" data-step="1">Continue to Payment</button>
          </div>
        </div>

        <!-- Step 2: Payment -->
        <div class="checkout-step" data-step="2" style="display:none;">
          <div class="space-y-6">
            <h1 class="text-3xl font-bold mb-6">Payment Details</h1>
            <div class="bg-white p-6 rounded-lg border border-gray-200">
              <h2 class="text-lg font-semibold mb-4">Payment Method</h2>
              
              <?php if ( WC()->cart->needs_payment() ) : ?>
                <?php
                // Force PayPal as default if nothing is chosen
                if (empty(WC()->session->get('chosen_payment_method'))) {
                    WC()->session->set('chosen_payment_method', 'paypal'); // or 'ppec_paypal' if using PayPal Express
                }
                ?>
                <div class="wc_payment_methods payment_methods methods flex flex-col gap-4">
                  <?php
                  $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
                  if ( ! empty( $available_gateways ) ) :
                    foreach ( $available_gateways as $gateway ) :
                  ?>
                    <label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>"
                           class="payment-method-option flex items-center gap-4 p-4 border rounded-lg cursor-pointer transition-all
                                  <?php if ( $gateway->chosen ) echo 'border-[#FF3A5E] bg-[#FFF0F4] shadow'; else echo 'border-gray-200 bg-white'; ?>">
                      <input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>"
                             type="radio"
                             class="hidden peer"
                             name="payment_method"
                             value="<?php echo esc_attr( $gateway->id ); ?>"
                             <?php checked( $gateway->chosen, true ); ?>
                             data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
                      <span class="custom-radio w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center mr-3 peer-checked:border-[#FF3A5E] peer-checked:bg-[#FF3A5E] transition">
                        <span class="dot w-2 h-2 rounded-full bg-white peer-checked:bg-white"></span>
                      </span>
                      <span class="flex flex-col">
                        <span class="font-semibold text-lg <?php if ( $gateway->chosen ) echo 'text-[#FF3A5E]'; else echo 'text-gray-900'; ?>">
                          <?php echo $gateway->get_title(); ?>
                        </span>
                        <span><?php echo $gateway->get_icon(); ?></span>
                      </span>
                    </label>
                    <?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
                      <div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
                        <div class="mt-4 p-4 bg-gray-50 rounded">
                          <?php $gateway->payment_fields(); ?>
                        </div>
                      </div>
                    <?php endif; ?>
                  <?php
                    endforeach;
                  endif;
                  ?>
                </div>
              <?php endif; ?>
            </div>

            <div class="flex justify-between mt-6">
              <button type="button" class="back-to-step bg-gray-200 text-gray-700 px-6 py-3 rounded font-semibold hover:bg-gray-300 transition-colors" data-step="1">Back to Shipping</button>
              <button type="button" class="continue-to-step bg-[#FF3A5E] text-white px-6 py-3 rounded font-semibold hover:bg-[#E02E50] transition-colors" data-step="2">Review Order</button>
            </div>
          </div>
        </div>

        <!-- Step 3: Review -->
        <div class="checkout-step" data-step="3" style="display:none;">
          <div class="space-y-6">
            <h1 class="text-3xl font-bold mb-2">Review Your Order</h1>
            <p class="text-gray-500 mb-6">Please review your order details before placing your order.</p>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
              <!-- Left: Review Details -->
              <div class="lg:col-span-3 space-y-4">
                <!-- Shipping Information -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 flex justify-between items-start">
                  <div>
                    <div class="flex items-center gap-2 mb-2 text-[#FF3A5E] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                      Shipping Information
                    </div>
                    <div class="text-sm text-gray-700">
                      <?php
                        $customer = WC()->customer;
                        $shipping_name = trim($customer->get_shipping_first_name() . ' ' . $customer->get_shipping_last_name());
                        $shipping_address = $customer->get_shipping_address_1();
                        $shipping_address2 = $customer->get_shipping_address_2();
                        $shipping_city = $customer->get_shipping_city();
                        $shipping_state = $customer->get_shipping_state();
                        $shipping_postcode = $customer->get_shipping_postcode();
                        $shipping_country = $customer->get_shipping_country();
                        $shipping_email = $customer->get_billing_email();
                        $shipping_phone = $customer->get_billing_phone();
                        echo esc_html($shipping_name) . '<br>';
                        echo esc_html($shipping_address);
                        if ($shipping_address2) echo ', ' . esc_html($shipping_address2);
                        echo '<br>' . esc_html($shipping_city) . ', ' . esc_html($shipping_state) . ' ' . esc_html($shipping_postcode) . '<br>';
                        if ($shipping_country) echo esc_html(WC()->countries->countries[$shipping_country]) . '<br>';
                        if ($shipping_email) echo esc_html($shipping_email) . '<br>';
                        if ($shipping_phone) echo esc_html($shipping_phone);
                      ?>
                    </div>
                  </div>
                  <button type="button" class="text-[#FF3A5E] font-medium hover:underline edit-step" data-step="1">Edit</button>
                </div>
                <!-- Payment Method -->
                <div class="bg-white rounded-lg border border-gray-200 p-6 flex justify-between items-start">
                  <div>
                    <div class="flex items-center gap-2 mb-2 text-[#FF3A5E] font-semibold">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="2" y="7" width="20" height="10" rx="2" /><path d="M2 9h20" /></svg>
                      Payment Method
                    </div>
                    <div class="text-sm text-gray-700">
                      <?php
                        $payment_gateways = WC()->payment_gateways()->get_available_payment_gateways();
                        $chosen_payment = WC()->session->get('chosen_payment_method');
                        if ($chosen_payment && isset($payment_gateways[$chosen_payment])) {
                          $gateway = $payment_gateways[$chosen_payment];
                          echo esc_html($gateway->get_title());
                          if (method_exists($gateway, 'get_icon')) echo ' ' . $gateway->get_icon();
                          if (method_exists($gateway, 'get_description')) echo '<br>' . $gateway->get_description();
                        }
                      ?>
                    </div>
                  </div>
                  <button type="button" class="text-[#FF3A5E] font-medium hover:underline edit-step" data-step="2">Edit</button>
                </div>
                <!-- Totals -->
                <div class="bg-white rounded-lg border border-gray-200 p-6">
                  <div class="flex justify-between text-gray-700 mb-2">
                    <span>Subtotal</span>
                    <span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
                  </div>
                  <?php if ( WC()->cart->get_coupons() ) : ?>
                  <div class="flex justify-between text-green-600 mb-2">
                    <span><?php esc_html_e( 'Discount', 'woocommerce' ); ?></span>
                    <span>-<?php echo wc_price( WC()->cart->get_discount_total() ); ?></span>
                  </div>
                  <?php endif; ?>
                  <div class="flex justify-between text-gray-700 mb-2">
                    <span>Shipping</span>
                    <span><?php echo WC()->cart->get_cart_shipping_total(); ?></span>
                  </div>
                  <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
                  <div class="flex justify-between text-gray-700 mb-2">
                    <span>Tax</span>
                    <span><?php echo WC()->cart->get_taxes_total(); ?></span>
                  </div>
                  <?php endif; ?>
                  <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
                    <span>Total</span>
                    <span class="text-[#FF3A5E]"><?php echo WC()->cart->get_total(); ?></span>
                  </div>
                </div>
                <!-- Terms and Place Order -->
                <div class="flex items-center mt-4">
                  <input type="checkbox" id="terms" name="terms" class="accent-[#FF3A5E] mr-2" required>
                  <label for="terms" class="text-sm">I agree to the <a href="<?php echo esc_url( wc_get_page_permalink( 'terms' ) ); ?>" class="text-[#FF3A5E] underline" target="_blank">Terms and Conditions</a> and <a href="<?php echo esc_url( wc_get_page_permalink( 'privacy-policy' ) ); ?>" class="text-[#FF3A5E] underline" target="_blank">Privacy Policy</a></label>
                </div>
                <div class="flex justify-between mt-6">
                  <button type="button" class="back-to-step bg-gray-200 text-gray-700 px-6 py-3 rounded font-semibold hover:bg-gray-300 transition-colors" data-step="2">Back to Payment</button>
                  <button type="submit" name="woocommerce_checkout_place_order" class="bg-[#FF3A5E] text-white px-6 py-3 rounded font-semibold hover:bg-[#E02E50] transition-colors" id="place_order" value="<?php esc_attr_e( 'Place order', 'woocommerce' ); ?>">
                    <?php
                    $selected_gateway = WC()->session->get('chosen_payment_method');
                    if ($selected_gateway && (strpos($selected_gateway, 'paypal') !== false || strpos($selected_gateway, 'ppec') !== false)) {
                        esc_html_e( 'Pay with PayPal', 'woocommerce' );
                    } else {
                        esc_html_e( 'Place Order', 'woocommerce' );
                    }
                    ?>
                  </button>
                </div>
              </div>
              <!-- Right: Order Summary (already present) -->
            </div>
          </div>
        </div>
      </form>
    </div>

    <!-- Order Summary Sidebar -->
    <div class="lg:col-span-1">
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
                  <div class="absolute -top-2 -right-2 bg-[#FF3A5E] text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-medium">
                    <?php echo esc_html( $cart_item['quantity'] ); ?>
                  </div>
                </div>
                <div class="flex-1">
                  <p class="font-medium text-sm line-clamp-2"><?php echo esc_html( $_product->get_name() ); ?></p>
                  <div class="flex text-xs text-gray-500 mt-1">
                    <?php if ( isset( $cart_item['variation'] ) && is_array( $cart_item['variation'] ) ) : ?>
                      <?php foreach ( $cart_item['variation'] as $key => $value ) : ?>
                        <span class="mr-2"><?php echo esc_html( str_replace( 'attribute_', '', $key ) . ': ' . $value ); ?></span>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </div>
                  <p class="mt-1 font-medium text-[#FF3A5E]"><?php echo wc_price( $_product->get_price() * $cart_item['quantity'] ); ?></p>
                </div>
              </div>
            <?php endif; endforeach; ?>
          </div>
          
          <div class="space-y-2 border-t border-gray-200 pt-4">
            <div class="flex justify-between">
              <span class="text-gray-600"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></span>
              <span><?php echo WC()->cart->get_cart_subtotal(); ?></span>
            </div>
            
            <?php if ( WC()->cart->get_coupons() ) : ?>
            <div class="flex justify-between text-green-600">
              <span><?php esc_html_e( 'Discount', 'woocommerce' ); ?></span>
              <span>-<?php echo wc_price( WC()->cart->get_discount_total() ); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>
            <div class="flex justify-between">
              <span class="text-gray-600"><?php esc_html_e( 'Shipping', 'woocommerce' ); ?></span>
              <span><?php echo WC()->cart->get_cart_shipping_total(); ?></span>
            </div>
            <?php endif; ?>
            
            <?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
            <div class="flex justify-between">
              <span class="text-gray-600"><?php esc_html_e( 'Tax', 'woocommerce' ); ?></span>
              <span><?php echo WC()->cart->get_taxes_total(); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="flex justify-between font-bold text-lg pt-2 border-t border-gray-200">
              <span><?php esc_html_e( 'Total', 'woocommerce' ); ?></span>
              <span class="text-[#FF3A5E]"><?php echo WC()->cart->get_total(); ?></span>
            </div>
          </div>

          <!-- Coupon Code Section -->
          <?php if ( wc_coupons_enabled() ) : ?>
          <div class="mt-6 pt-4 border-t border-gray-200">
            <h3 class="text-sm font-medium mb-2"><?php esc_html_e( 'Coupon Code', 'woocommerce' ); ?></h3>
            <div class="flex gap-2">
              <input type="text" name="coupon_code" id="coupon_code" placeholder="<?php esc_attr_e( 'Enter coupon code', 'woocommerce' ); ?>" class="flex-1 px-3 py-2 border border-gray-300 rounded text-sm focus:border-[#FF3A5E] focus:ring-[#FF3A5E]">
              <button type="button" class="apply-coupon bg-gray-100 text-gray-700 px-4 py-2 rounded text-sm hover:bg-gray-200 transition-colors">
                <?php esc_html_e( 'Apply', 'woocommerce' ); ?>
              </button>
            </div>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Apply coupon functionality
    $('.apply-coupon').on('click', function(e) {
        e.preventDefault();
        var coupon_code = $('#coupon_code').val();
        
        if (!coupon_code) {
            alert('Please enter a coupon code.');
            return;
        }
        
        $(this).text('Applying...').prop('disabled', true);
        
        $.ajax({
            url: wc_checkout_params.wc_ajax_url.replace('%%endpoint%%', 'apply_coupon'),
            type: 'POST',
            data: {
                coupon_code: coupon_code,
                security: wc_checkout_params.apply_coupon_nonce
            },
            success: function(response) {
                if (response.success) {
                    $('body').trigger('update_checkout');
                    $('#coupon_code').val('');
                } else {
                    alert(response.data ? response.data : 'Failed to apply coupon.');
                }
            },
            error: function() {
                alert('Error applying coupon. Please try again.');
            },
            complete: function() {
                $('.apply-coupon').text('Apply').prop('disabled', false);
            }
        });
    });
    
    // Allow enter key to apply coupon
    $('#coupon_code').on('keypress', function(e) {
        if (e.which === 13) {
            $('.apply-coupon').click();
        }
    });

    // Function to update place order button visibility
    function updatePlaceOrderButton() {
        var currentStep = $('.checkout-step:visible').data('step');
        var termsChecked = $('#terms').is(':checked');
        
        if (currentStep === 3 && termsChecked) {
            $('#place_order').removeClass('hidden');
        } else {
            $('#place_order').addClass('hidden');
        }
    }

    // Handle payment method changes
    $('input[name="payment_method"]').on('change', function() {
        var selectedMethod = $(this).val();
        var buttonText = 'Place Order';
        
        if (selectedMethod && (selectedMethod.includes('paypal') || selectedMethod.includes('ppec'))) {
            buttonText = 'Pay with PayPal';
        }
        
        $('#place_order').text(buttonText);
        updatePlaceOrderButton();
    });

    // Handle form submission
    $('form.checkout').on('submit', function(e) {
        if (!$('#terms').is(':checked')) {
            e.preventDefault();
            alert('Please agree to the Terms and Conditions before placing your order.');
            return false;
        }
        
        // Show loading state
        $('#place_order').prop('disabled', true).text('Processing...');
        
        // Let the form submit normally
        return true;
    });

    // Show place order button when terms are checked
    $('#terms').on('change', function() {
        updatePlaceOrderButton();
    });

    // Handle edit step buttons
    $('.edit-step').on('click', function() {
        var step = $(this).data('step');
        $('.checkout-step').hide();
        $('.checkout-step[data-step="' + step + '"]').show();
        
        // Update progress bar
        $('.progress-circle').removeClass('bg-[#FF3A5E]').addClass('bg-gray-200');
        $('.progress-circle[data-step="' + step + '"]').removeClass('bg-gray-200').addClass('bg-[#FF3A5E]');
        
        // Update progress lines
        $('.progress-line').removeClass('bg-[#FF3A5E]').addClass('bg-gray-200');
        for (var i = 1; i < step; i++) {
            $('.progress-line[data-bar="' + i + '"]').removeClass('bg-gray-200').addClass('bg-[#FF3A5E]');
        }

        updatePlaceOrderButton();
    });

    // Handle continue to next step buttons
    $('.continue-to-step').on('click', function() {
        var currentStep = $(this).closest('.checkout-step').data('step');
        var nextStep = currentStep + 1;
        
        // Hide current step and show next step
        $('.checkout-step').hide();
        $('.checkout-step[data-step="' + nextStep + '"]').show();
        
        // Update progress bar
        $('.progress-circle').removeClass('bg-[#FF3A5E]').addClass('bg-gray-200');
        $('.progress-circle[data-step="' + nextStep + '"]').removeClass('bg-gray-200').addClass('bg-[#FF3A5E]');
        
        // Update progress lines
        $('.progress-line').removeClass('bg-[#FF3A5E]').addClass('bg-gray-200');
        for (var i = 1; i < nextStep; i++) {
            $('.progress-line[data-bar="' + i + '"]').removeClass('bg-gray-200').addClass('bg-[#FF3A5E]');
        }

        updatePlaceOrderButton();
    });

    // Handle back to previous step buttons
    $('.back-to-step').on('click', function() {
        var step = $(this).data('step');
        $('.checkout-step').hide();
        $('.checkout-step[data-step="' + step + '"]').show();
        
        // Update progress bar
        $('.progress-circle').removeClass('bg-[#FF3A5E]').addClass('bg-gray-200');
        $('.progress-circle[data-step="' + step + '"]').removeClass('bg-gray-200').addClass('bg-[#FF3A5E]');
        
        // Update progress lines
        $('.progress-line').removeClass('bg-[#FF3A5E]').addClass('bg-gray-200');
        for (var i = 1; i < step; i++) {
            $('.progress-line[data-bar="' + i + '"]').removeClass('bg-gray-200').addClass('bg-[#FF3A5E]');
        }

        updatePlaceOrderButton();
    });

    // Initial button state
    updatePlaceOrderButton();
});
</script>

<style>
.payment-method-option {
  border-width: 2px;
  transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
}
.payment-method-option:hover {
  border-color: #FF3A5E;
  background: #FFF0F4;
}
.payment-method-option input[type="radio"]:focus + .custom-radio {
  box-shadow: 0 0 0 2px #FF3A5E33;
}
.custom-radio {
  position: relative;
  background: #fff;
}
.custom-radio .dot {
  display: none;
}
input[type="radio"].peer:checked + .custom-radio .dot {
  display: block;
  background: #FF3A5E;
}
input[type="radio"].peer:checked + .custom-radio {
  border-color: #FF3A5E;
  background: #FF3A5E;
}
#place_order.ppcp-hidden {
  display: flex !important;
}
</style>

<?php get_footer(); ?>