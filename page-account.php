<?php
/**
 * Template Name: Account Page
 * 
 * This is the template that displays the WooCommerce account pages.
 */

get_header();
$user = wp_get_current_user();

// Set the active tab for preview. Change this value to 'orders', 'wishlist', 'addresses', or 'payment' to preview different active states.
$active_tab = isset($_GET['active_tab']) ? $_GET['active_tab'] : 'orders';
?>

<main id="main-content" class="min-h-screen mx-auto px-0">
  <?php if (!is_user_logged_in()) : ?>
    <!-- Login Form -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8">
        <!-- Logo/Brand -->
        <div class="text-center">
          <h2 class="mt-6 text-4xl font-extrabold text-gray-900">
            Welcome Back
          </h2>
          <p class="mt-2 text-sm text-gray-600">
            Sign in to your account to continue
          </p>
        </div>

        <!-- Login Form -->
        <div class="mt-8 bg-white py-8 px-4 shadow-xl rounded-2xl sm:px-10 border border-gray-100">
          <form name="loginform" id="loginform" action="<?php echo esc_url(site_url('wp-login.php', 'login_post')); ?>" method="post" class="space-y-6">
            <div>
              <label for="user_login" class="block text-sm font-medium text-gray-700">
                Username or Email
              </label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input type="text" name="log" id="user_login" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm" placeholder="Enter your username or email" required>
              </div>
            </div>

            <div>
              <label for="user_pass" class="block text-sm font-medium text-gray-700">
                Password
              </label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input type="password" name="pwd" id="user_pass" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm" placeholder="Enter your password" required>
              </div>
            </div>

            <div class="flex items-center justify-between">
              <div class="flex items-center">
                <input type="checkbox" name="rememberme" id="rememberme" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                <label for="rememberme" class="ml-2 block text-sm text-gray-900">
                  Remember me
                </label>
              </div>

              <div class="text-sm">
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>" class="font-medium text-primary hover:text-primary-dark transition-colors duration-200">
                  Forgot password?
                </a>
              </div>
            </div>

            <div>
              <button type="submit" name="wp-submit" id="wp-submit" 
                class="w-full flex justify-center py-3 px-4 border border-2 border-primary rounded-lg shadow-sm text-base font-semibold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200 transform hover:scale-[1.02]"
                style="display:block; background-color:#FF3A5E; border-color:#FF3A5E; color:#fff;">
                Sign in
              </button>
              <input type="hidden" name="redirect_to" value="<?php echo esc_url(get_permalink()); ?>">
            </div>
          </form>

          <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
              Don't have an account? 
              <a href="<?php echo esc_url(wp_registration_url()); ?>" class="font-medium text-primary hover:text-primary-dark transition-colors duration-200">
                Create an account
              </a>
            </p>
          </div>
        </div>
      </div>
    </div>

    <style>
      /* Custom styles for the login form */
      #loginform {
        @apply space-y-6;
      }
      
      #wp-submit {
        @apply w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-semibold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200 transform hover:scale-[1.02];
      }
      
      #user_login,
      #user_pass {
        @apply block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm;
      }
      
      #rememberme {
        @apply h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded;
      }

      /* Animation for form elements */
      .bg-white {
        animation: fadeIn 0.5s ease-out;
      }

      @keyframes fadeIn {
        from {
          opacity: 0;
          transform: translateY(10px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }
    </style>
  <?php else : ?>
    <!-- Account Dashboard -->
    <div x-data="{ activeTab: 'orders' }">
      <h1 class="text-3xl font-bold mb-8">My Account</h1>
      <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Account Sidebar -->
        <div class="lg:col-span-1 space-y-6">
          <!-- User Info -->
          <div class="bg-white rounded-lg border border-gray-200 p-6">
            <div class="flex items-center gap-4 mb-4">
              <div class="h-12 w-12 rounded-full bg-primary flex items-center justify-center text-white text-lg font-bold">
                <?php echo strtoupper(substr($user->display_name, 0, 1)); ?>
              </div>
              <div>
                <p class="font-medium"><?php echo esc_html($user->display_name); ?></p>
                <p class="text-sm text-gray-500"><?php echo esc_html($user->user_email); ?></p>
              </div>
            </div>
            <a href="<?php echo esc_url(wc_get_account_endpoint_url('edit-account')); ?>" class="inline-flex items-center justify-center rounded-md text-sm font-medium border bg-background hover:bg-accent h-10 px-4 py-2 w-full">
              <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
              Edit Profile
            </a>
          </div>
          <!-- Navigation Links -->
          <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <div class="py-2">
              <button class="w-full flex items-center justify-between px-6 py-3 hover:bg-gray-50 text-left" :class="activeTab === 'orders' ? 'text-[#FF3A5E] font-medium' : ''" @click="activeTab = 'orders'">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                  Orders
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </button>
              <button class="w-full flex items-center justify-between px-6 py-3 hover:bg-gray-50 text-left" :class="activeTab === 'wishlist' ? 'text-[#FF3A5E] font-medium' : ''" @click="activeTab = 'wishlist'">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                  Wishlist
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </button>
              <button class="w-full flex items-center justify-between px-6 py-3 hover:bg-gray-50 text-left" :class="activeTab === 'addresses' ? 'text-[#FF3A5E] font-medium' : ''" @click="activeTab = 'addresses'">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                  Addresses
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </button>
              <button class="w-full flex items-center justify-between px-6 py-3 hover:bg-gray-50 text-left" :class="activeTab === 'payment' ? 'text-[#FF3A5E] font-medium' : ''" @click="activeTab = 'payment'">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                  Payment Methods
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </button>
            </div>
            <div class="border-t border-gray-200">
              <a href="<?php echo esc_url(wc_logout_url()); ?>" class="w-full flex items-center px-6 py-3 text-red-600 hover:bg-red-50 text-left">
                <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Logout
              </a>
            </div>
          </div>
        </div>
        <!-- Main Content Tabs -->
        <div class="lg:col-span-3">
          <!-- Orders Tab -->
          <div x-show="activeTab === 'orders'">
            <?php wc_get_template('myaccount/orders.php'); ?>
          </div>
          <!-- Wishlist Tab -->
          <div x-show="activeTab === 'wishlist'" x-cloak>
            <?php get_template_part('woocommerce/myaccount/wishlist'); ?>
          </div>
          <!-- Addresses Tab -->
          <div x-show="activeTab === 'addresses'" x-cloak>
            <div class="rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden">
              <div class="flex items-center justify-between px-8 py-6 border-b border-gray-200">
                <h2 class="text-2xl font-bold text-gray-900 mb-0">My Addresses</h2>
                <a href="#" class="text-base font-medium text-gray-900 hover:text-[#FF3A5E]" data-add-address data-type="shipping">Add New Address</a>
              </div>
              <div class="p-8">
                <?php
                $customer_id = get_current_user_id();
                $customer = new WC_Customer($customer_id);
                $addresses = array(
                  'shipping' => $customer->get_shipping(),
                  'billing' => $customer->get_billing(),
                );
                $default_type = 'shipping';
                foreach ($addresses as $type => $address) {
                  if (empty($address['address_1'])) continue;
                  $is_default = ($type === $default_type);
                  ?>
                  <div class="relative bg-white border border-gray-200 rounded-lg p-6 mb-6" style="min-width:300px; max-width:400px;">
                    <div class="flex items-center justify-between mb-2">
                      <?php if ($is_default): ?>
                        <span class="inline-block bg-[#FF3A5E] text-white text-xs font-semibold px-4 py-1 rounded-full">Default</span>
                      <?php endif; ?>
                    </div>
                    <div class="text-gray-700 text-base leading-relaxed">
                      <?php echo esc_html($address['address_1']); ?><br>
                      <?php if (!empty($address['address_2'])) echo esc_html($address['address_2']) . '<br>'; ?>
                      <?php echo esc_html($address['city']); ?>, <?php echo esc_html($address['state']); ?> <?php echo esc_html($address['postcode']); ?><br>
                      <?php echo esc_html($address['country']); ?>
                    </div>
                    <div class="mt-4">
                      <a href="#" class="text-base font-medium text-[#1a1a1a] hover:text-[#FF3A5E]" data-edit-address data-type="<?php echo esc_attr($type); ?>" data-address='<?php echo json_encode(array_merge($address, ["edit" => true])); ?>'>Edit</a>
                    </div>
                  </div>
                <?php } ?>
              </div>
            </div>
          </div>
          <!-- Payment Methods Tab -->
          <div x-show="activeTab === 'payment'" x-cloak>
            <?php get_template_part('woocommerce/myaccount/payment-methods'); ?>
          </div>
        </div>
      </div>
    </div>
  <?php endif; ?>
</main>

<!-- Address Modal -->
<div id="addressModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-40 hidden">
  <div class="bg-white rounded-lg shadow-lg w-full max-w-md p-8 relative">
    <button id="closeAddressModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-700 text-2xl">&times;</button>
    <h2 id="addressModalTitle" class="text-xl font-bold mb-4">Add Address</h2>
    <form id="addressForm">
      <input type="hidden" name="address_type" id="address_type" value="shipping">
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Address 1</label>
        <input type="text" name="address_1" id="address_1" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Address 2</label>
        <input type="text" name="address_2" id="address_2" class="w-full border rounded px-3 py-2">
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">City</label>
        <input type="text" name="city" id="city" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">State</label>
        <input type="text" name="state" id="state" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Postcode</label>
        <input type="text" name="postcode" id="postcode" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 mb-1">Country</label>
        <input type="text" name="country" id="country" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="flex justify-end">
        <button type="submit" class="bg-[#FF3A5E] text-white px-6 py-2 rounded font-semibold">Save Address</button>
      </div>
      <div id="addressFormMessage" class="mt-4 text-sm"></div>
    </form>
  </div>
</div>

<script>
// Modal open/close logic
function openAddressModal(type, data = {}) {
  document.getElementById('addressModal').classList.remove('hidden');
  document.getElementById('addressModalTitle').textContent = data.edit ? 'Edit Address' : 'Add Address';
  document.getElementById('address_type').value = type || 'shipping';
  document.getElementById('address_1').value = data.address_1 || '';
  document.getElementById('address_2').value = data.address_2 || '';
  document.getElementById('city').value = data.city || '';
  document.getElementById('state').value = data.state || '';
  document.getElementById('postcode').value = data.postcode || '';
  document.getElementById('country').value = data.country || '';
  document.getElementById('addressFormMessage').textContent = '';
}
document.querySelectorAll('[data-add-address]').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    openAddressModal(this.dataset.type);
  });
});
document.querySelectorAll('[data-edit-address]').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.preventDefault();
    openAddressModal(this.dataset.type, JSON.parse(this.dataset.address));
  });
});
document.getElementById('closeAddressModal').onclick = function() {
  document.getElementById('addressModal').classList.add('hidden');
};
document.getElementById('addressModal').onclick = function(e) {
  if (e.target === this) this.classList.add('hidden');
};

document.getElementById('addressForm').onsubmit = function(e) {
  e.preventDefault();
  var form = this;
  var formData = new FormData(form);
  var messageDiv = document.getElementById('addressFormMessage');
  messageDiv.textContent = 'Saving...';

  fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
    method: 'POST',
    credentials: 'same-origin',
    body: new URLSearchParams([
      ...Array.from(formData.entries()),
      ['action', 'save_account_address']
    ])
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      messageDiv.textContent = 'Address saved!';
      setTimeout(() => window.location.reload(), 800); // Reload to show updated address
    } else {
      messageDiv.textContent = data.data || 'Error saving address.';
    }
  })
  .catch(() => {
    messageDiv.textContent = 'Error saving address.';
  });
};
</script>

<style>
.account-page-wrapper {
    min-height: 100vh;
    background-color: #f8f9fa;
    padding: 40px 0;
}



.account-content {
    display: grid;
    grid-template-columns: 280px 1fr;
    gap: 30px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    overflow: hidden;
}

/* WooCommerce Account Navigation */
.woocommerce-MyAccount-navigation {
    background: #fff;
    padding: 30px;
    border-right: 1px solid #eee;
}

.woocommerce-MyAccount-navigation ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.woocommerce-MyAccount-navigation li {
    margin-bottom: 8px;
}

.woocommerce-MyAccount-navigation a {
    display: flex;
    align-items: center;
    padding: 12px 16px;
    color: #333;
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.2s;
    font-size: 15px;
}

.woocommerce-MyAccount-navigation a:hover {
    background-color: #f8f9fa;
    color: #FF3A5E;
}

.woocommerce-MyAccount-navigation .is-active a {
    background-color: #FF3A5E;
    color: white;
}

/* WooCommerce Account Content */
.woocommerce-MyAccount-content {
    padding: 40px;
}

/* Orders Table */
.woocommerce-orders-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

.woocommerce-orders-table th,
.woocommerce-orders-table td {
    padding: 16px;
    text-align: left;
    border-bottom: 1px solid #eee;
}

.woocommerce-orders-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #333;
}

/* Order Status */
.woocommerce-orders-table__row--status-processing {
    color: #059669;
}

.woocommerce-orders-table__row--status-completed {
    color: #059669;
}

.woocommerce-orders-table__row--status-on-hold {
    color: #d97706;
}

.woocommerce-orders-table__row--status-cancelled {
    color: #dc2626;
}

/* Form Styles */
.woocommerce-form {
    max-width: 400px;
    margin: 0 auto;
}

.woocommerce-form-row {
    margin-bottom: 20px;
}

.woocommerce-form-row label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #333;
}

.woocommerce-form-row input[type="text"],
.woocommerce-form-row input[type="email"],
.woocommerce-form-row input[type="password"] {
    width: 100%;
    padding: 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 15px;
}

.woocommerce-form-row input[type="text"]:focus,
.woocommerce-form-row input[type="email"]:focus,
.woocommerce-form-row input[type="password"]:focus {
    border-color: #FF3A5E;
    outline: none;
}

.woocommerce-form-login__submit,
.woocommerce-form-register__submit {
    width: 100%;
    padding: 14px;
    background-color: #FF3A5E;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s;
}

.woocommerce-form-login__submit:hover,
.woocommerce-form-register__submit:hover {
    background-color: #e62a4d;
}

/* Responsive Design */
@media (max-width: 768px) {
    .account-content {
        grid-template-columns: 1fr;
    }

    .woocommerce-MyAccount-navigation {
        border-right: none;
        border-bottom: 1px solid #eee;
        padding: 20px;
    }

    .woocommerce-MyAccount-content {
        padding: 20px;
    }
}
</style>

<?php if (isset($_GET['show_new_design']) && $_GET['show_new_design'] == '1') : ?>
<!-- PIXEL-PERFECT ACCOUNT PAGE DESIGN PREVIEW -->
<div class="min-h-screen flex flex-col items-center justify-start bg-[#fff] py-12">
  <div class="w-full max-w-6xl grid grid-cols-1 md:grid-cols-3 gap-8">
    <!-- Sidebar -->
    <div class="col-span-1 flex flex-col gap-6">
      <!-- User Card -->
      <div class="rounded-xl bg-white border border-gray-200 px-8 py-7 flex flex-col items-start gap-4 shadow-sm">
        <div class="flex items-center gap-4">
          <div class="h-14 w-14 rounded-full bg-[#FF3A5E] flex items-center justify-center text-white text-2xl font-bold">A</div>
          <div>
            <div class="font-semibold text-lg text-gray-900">Alex Johnson</div>
            <div class="text-sm text-gray-500">alex@example.com</div>
          </div>
        </div>
        <a href="#" class="flex items-center gap-2 text-sm text-gray-700 font-medium mt-2 hover:text-[#FF3A5E]">
          <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline-block"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
          Edit Profile
        </a>
      </div>
      <!-- Sidebar Menu -->
      <div class="rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="flex flex-col divide-y divide-gray-200">
          <!-- Orders -->
          <button class="flex items-center justify-between px-8 py-4 w-full focus:outline-none <?php echo $active_tab === 'orders' ? 'font-semibold bg-white' : 'font-normal bg-white'; ?>" style="<?php echo $active_tab === 'orders' ? 'color:#FF3A5E;' : 'color:#111827;'; ?>">
            <span class="flex items-center gap-3">
              <svg width="22" height="22" fill="none" stroke="<?php echo $active_tab === 'orders' ? '#FF3A5E' : '#111827'; ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="3.5" width="17" height="13" rx="2"/><line x1="8" y1="20" x2="16" y2="20"/><line x1="12" y1="16" x2="12" y2="20"/></svg>
              Orders
            </span>
            <svg width="18" height="18" fill="none" stroke="<?php echo $active_tab === 'orders' ? '#FF3A5E' : '#111827'; ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </button>
          <!-- Wishlist -->
          <button class="flex items-center justify-between px-8 py-4 w-full focus:outline-none <?php echo $active_tab === 'wishlist' ? 'font-semibold bg-white' : 'font-normal bg-white'; ?>" style="<?php echo $active_tab === 'wishlist' ? 'color:#FF3A5E;' : 'color:#111827;'; ?>">
            <span class="flex items-center gap-3">
              <svg width="22" height="22" fill="none" stroke="<?php echo $active_tab === 'wishlist' ? '#FF3A5E' : '#111827'; ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
              Wishlist
            </span>
            <svg width="18" height="18" fill="none" stroke="<?php echo $active_tab === 'wishlist' ? '#FF3A5E' : '#111827'; ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </button>
          <!-- Addresses (with gray background if not active) -->
          <button class="flex items-center justify-between px-8 py-4 w-full focus:outline-none <?php echo $active_tab === 'addresses' ? 'font-semibold bg-white' : 'font-normal bg-[#f8fafc]'; ?>" style="<?php echo $active_tab === 'addresses' ? 'color:#FF3A5E;' : 'color:#111827;'; ?>">
            <span class="flex items-center gap-3">
              <svg width="22" height="22" fill="none" stroke="<?php echo $active_tab === 'addresses' ? '#FF3A5E' : '#111827'; ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
              Addresses
            </span>
            <svg width="18" height="18" fill="none" stroke="<?php echo $active_tab === 'addresses' ? '#FF3A5E' : '#111827'; ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </button>
          <!-- Payment Methods -->
          <button class="flex items-center justify-between px-8 py-4 w-full focus:outline-none <?php echo $active_tab === 'payment' ? 'font-semibold bg-white' : 'font-normal bg-white'; ?>" style="<?php echo $active_tab === 'payment' ? 'color:#FF3A5E;' : 'color:#111827;'; ?>">
            <span class="flex items-center gap-3">
              <svg width="22" height="22" fill="none" stroke="<?php echo $active_tab === 'payment' ? '#FF3A5E' : '#111827'; ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2.5" y="4.5" width="17" height="13" rx="2"/><line x1="2.5" y1="10" x2="19.5" y2="10"/></svg>
              Payment Methods
            </span>
            <svg width="18" height="18" fill="none" stroke="<?php echo $active_tab === 'payment' ? '#FF3A5E' : '#111827'; ?>" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
          </button>
        </div>
        <div class="border-t border-gray-200">
          <button class="flex items-center gap-3 px-8 py-4 text-[#FF3A5E] font-semibold w-full focus:outline-none">
            <svg width="22" height="22" fill="none" stroke="#FF3A5E" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="3.5" width="15" height="15" rx="2"/><path d="M8 15l4-4-4-4"/></svg>
            Logout
          </button>
        </div>
      </div>
    </div>
    <!-- Main Content -->
    <div class="col-span-1 md:col-span-2 flex flex-col gap-6">
      <div class="rounded-xl bg-white border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-8 py-6 border-b border-gray-200">
          <h2 class="text-2xl font-bold text-gray-900">Order History</h2>
        </div>
        <div>
          <!-- Order 1 -->
          <div class="flex flex-col md:flex-row items-start md:items-center justify-between px-8 py-6 border-b border-gray-100">
            <div>
              <div class="font-semibold text-lg text-gray-900">ORD12345</div>
              <div class="text-sm text-gray-500 mb-2">May 3, 2025</div>
              <div class="text-base text-gray-700">3 items</div>
              <a href="#" class="text-base text-[#1a1a1a] mt-2 block font-medium hover:text-[#FF3A5E]">View Order</a>
            </div>
            <div class="flex flex-col items-end gap-2 mt-4 md:mt-0">
              <span class="inline-block bg-green-100 text-green-700 text-sm font-medium px-4 py-1 rounded-full mb-2">Delivered</span>
              <span class="text-2xl font-bold text-gray-900">$89.97</span>
              <a href="#" class="text-base text-[#1a1a1a] font-medium hover:text-[#FF3A5E]">Write a Review</a>
            </div>
          </div>
          <!-- Order 2 -->
          <div class="flex flex-col md:flex-row items-start md:items-center justify-between px-8 py-6 border-b border-gray-100">
            <div>
              <div class="font-semibold text-lg text-gray-900">ORD12346</div>
              <div class="text-sm text-gray-500 mb-2">April 15, 2025</div>
              <div class="text-base text-gray-700">1 item</div>
              <a href="#" class="text-base text-[#1a1a1a] mt-2 block font-medium hover:text-[#FF3A5E]">View Order</a>
            </div>
            <div class="flex flex-col items-end gap-2 mt-4 md:mt-0">
              <span class="inline-block bg-blue-100 text-blue-700 text-sm font-medium px-4 py-1 rounded-full mb-2">Shipped</span>
              <span class="text-2xl font-bold text-gray-900">$54.99</span>
            </div>
          </div>
          <!-- Order 3 -->
          <div class="flex flex-col md:flex-row items-start md:items-center justify-between px-8 py-6">
            <div>
              <div class="font-semibold text-lg text-gray-900">ORD12347</div>
              <div class="text-sm text-gray-500 mb-2">March 28, 2025</div>
              <div class="text-base text-gray-700">2 items</div>
              <a href="#" class="text-base text-[#1a1a1a] mt-2 block font-medium hover:text-[#FF3A5E]">View Order</a>
            </div>
            <div class="flex flex-col items-end gap-2 mt-4 md:mt-0">
              <span class="inline-block bg-yellow-100 text-yellow-800 text-sm font-medium px-4 py-1 rounded-full mb-2">Processing</span>
              <span class="text-2xl font-bold text-gray-900">$124.95</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<!-- END PIXEL-PERFECT ACCOUNT PAGE DESIGN PREVIEW -->
<?php endif; ?>

<?php
get_footer(); 