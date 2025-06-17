<?php
$customer_id = get_current_user_id();
$payment_tokens = WC_Payment_Tokens::get_customer_tokens($customer_id);
?>
<div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
  <h2 class="text-xl font-semibold">Payment Methods</h2>
  <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'add-payment-method' ) ); ?>" class="inline-flex items-center justify-center rounded-md text-sm font-medium bg-primary hover:bg-primary-dark h-9 px-4 py-2 text-white">
    Add Payment Method
  </a>
</div>
<div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
<?php if ($payment_tokens) : ?>
  <?php foreach ($payment_tokens as $token) : ?>
    <div class="border border-gray-200 rounded-lg p-4 relative">
      <?php if ($token->is_default()) : ?>
        <span class="absolute top-2 right-2 bg-primary text-white text-xs px-2 py-1 rounded-full">Default</span>
      <?php endif; ?>
      <div class="flex items-center mb-2">
        <div class="mr-3">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
        </div>
        <div>
          <p class="font-medium"><?php echo esc_html($token->get_display_name()); ?></p>
          <p class="text-sm text-gray-500">Expires <?php echo esc_html($token->get_expiry_month() . '/' . $token->get_expiry_year()); ?></p>
        </div>
      </div>
      <div class="mt-4 flex gap-2">
        <a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-payment-method' ) . '?id=' . $token->get_id() ); ?>" class="inline-flex items-center justify-center rounded-md text-sm font-medium border bg-background hover:bg-accent h-9 px-4 py-2">
          Edit
        </a>
      </div>
    </div>
  <?php endforeach; ?>
<?php else : ?>
  <div class="col-span-2 text-center text-gray-500">No payment methods saved.</div>
<?php endif; ?>
</div> 