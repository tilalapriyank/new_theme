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

<main id="main-content" class="container min-h-screen mx-auto pt-32 ">
  <?php if (!is_user_logged_in()) : ?>
    <!-- Login/Register Forms -->
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
      <div class="max-w-md w-full space-y-8">
        <!-- Logo/Brand -->
        <div class="text-center">
          <h2 class="mt-6 text-4xl font-extrabold text-gray-900" id="form-title">
            Welcome Back
          </h2>
          <p class="mt-2 text-sm text-gray-600" id="form-subtitle">
            Sign in to your account to continue
          </p>
        </div>

        <!-- Form Toggle -->
        <div class="flex justify-center">
          <div class="bg-gray-100 rounded-lg p-1 flex">
            <button id="login-toggle" class="toggle-btn px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200 active">
              Sign In
            </button>
            <button id="register-toggle" class="toggle-btn px-4 py-2 rounded-md text-sm font-medium transition-colors duration-200">
              Create Account
            </button>
          </div>
        </div>

        <!-- Login Form -->
        <div id="login-form" class="mt-8 bg-white py-8 px-4 shadow-xl rounded-2xl sm:px-10 border border-gray-100">
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
                class="w-full flex justify-center py-3 px-4 border border-2 border-primary rounded-lg shadow-sm text-base font-semibold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
                style="display:block; background-color:#FF3A5E; border-color:#FF3A5E; color:#fff;">
                Sign in
              </button>
              <input type="hidden" name="redirect_to" value="<?php echo esc_url(get_permalink()); ?>">
            </div>
          </form>
        </div>

        <!-- Register Form -->
        <div id="register-form" class="mt-8 bg-white py-8 px-4 shadow-xl rounded-2xl sm:px-10 border border-gray-100 hidden">
          <form name="registerform" id="registerform" method="post" class="space-y-6">
            <?php wp_nonce_field('custom_user_registration', '_wpnonce'); ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label for="reg_first_name" class="block text-sm font-medium text-gray-700">
                  First Name
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <input type="text" name="first_name" id="reg_first_name" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm" placeholder="First name" required>
                </div>
              </div>

              <div>
                <label for="reg_last_name" class="block text-sm font-medium text-gray-700">
                  Last Name
                </label>
                <div class="mt-1 relative rounded-md shadow-sm">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                      <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                    </svg>
                  </div>
                  <input type="text" name="last_name" id="reg_last_name" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm" placeholder="Last name" required>
                </div>
              </div>
            </div>

            <div>
              <label for="reg_email" class="block text-sm font-medium text-gray-700">
                Email Address
              </label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                    <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                  </svg>
                </div>
                <input type="email" name="user_email" id="reg_email" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm" placeholder="Enter your email" required>
              </div>
            </div>

            <div>
              <label for="reg_username" class="block text-sm font-medium text-gray-700">
                Username
              </label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input type="text" name="user_login" id="reg_username" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm" placeholder="Choose a username" required>
              </div>
            </div>

            <div>
              <label for="reg_password" class="block text-sm font-medium text-gray-700">
                Password
              </label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input type="password" name="user_pass" id="reg_password" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm" placeholder="Create a password" required>
              </div>
            </div>

            <div>
              <label for="reg_confirm_password" class="block text-sm font-medium text-gray-700">
                Confirm Password
              </label>
              <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                  </svg>
                </div>
                <input type="password" name="user_pass_confirm" id="reg_confirm_password" class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm" placeholder="Confirm your password" required>
              </div>
            </div>

            <div class="flex items-center">
              <input type="checkbox" name="terms_agreement" id="terms_agreement" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded" required>
              <label for="terms_agreement" class="ml-2 block text-sm text-gray-900">
                I agree to the <a href="#" class="text-primary hover:text-primary-dark">Terms of Service</a> and <a href="#" class="text-primary hover:text-primary-dark">Privacy Policy</a>
              </label>
            </div>

            <div>
              <button type="submit" name="wp-submit" id="register-submit" 
                class="w-full flex justify-center py-3 px-4 border border-2 border-primary rounded-lg shadow-sm text-base font-semibold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200"
                style="display:block; background-color:#FF3A5E; border-color:#FF3A5E; color:#fff;">
                Create Account
              </button>
            </div>
          </form>
        </div>

        <!-- Form Messages -->
        <div id="form-messages" class="mt-4 text-center"></div>
      </div>
    </div>

    <style>
      /* Custom styles for the login/register forms */
      #loginform, #registerform {
        @apply space-y-6;
      }
      
      #wp-submit, #register-submit {
        @apply w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-semibold text-white bg-primary hover:bg-primary-dark focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors duration-200;
      }
      
      #user_login, #user_pass, #reg_first_name, #reg_last_name, #reg_email, #reg_username, #reg_password, #reg_confirm_password {
        @apply block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary sm:text-sm;
      }
      
      #rememberme, #terms_agreement {
        @apply h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded;
      }

      /* Toggle button styles */
      .toggle-active {
        @apply bg-white text-gray-900 shadow-sm;
      }

      .toggle-inactive {
        @apply text-gray-600 hover:text-gray-900;
      }

      /* Active state for toggle buttons */
      .toggle-btn.active {
        background-color: white;
        color: #111827;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
      }

      .toggle-btn:not(.active) {
        background-color: transparent;
        color: #6B7280;
      }

      .toggle-btn:not(.active):hover {
        color: #111827;
      }
    </style>

    <script>
      // Form toggle functionality
      document.addEventListener('DOMContentLoaded', function() {
        const loginToggle = document.getElementById('login-toggle');
        const registerToggle = document.getElementById('register-toggle');
        const loginForm = document.getElementById('login-form');
        const registerForm = document.getElementById('register-form');
        const formTitle = document.getElementById('form-title');
        const formSubtitle = document.getElementById('form-subtitle');

        // Check URL parameter for initial state
        const urlParams = new URLSearchParams(window.location.search);
        const initialForm = urlParams.get('form') || 'login';

        function showLoginForm() {
          loginForm.classList.remove('hidden');
          registerForm.classList.add('hidden');
          loginToggle.classList.add('active');
          registerToggle.classList.remove('active');
          formTitle.textContent = 'Welcome Back';
          formSubtitle.textContent = 'Sign in to your account to continue';
        }

        function showRegisterForm() {
          loginForm.classList.add('hidden');
          registerForm.classList.remove('hidden');
          registerToggle.classList.add('active');
          loginToggle.classList.remove('active');
          formTitle.textContent = 'Create Account';
          formSubtitle.textContent = 'Join us and start shopping today';
        }

        // Set initial state based on URL parameter
        if (initialForm === 'register') {
          showRegisterForm();
        } else {
          showLoginForm();
        }

        loginToggle.addEventListener('click', showLoginForm);
        registerToggle.addEventListener('click', showRegisterForm);

        // Password confirmation validation
        const passwordField = document.getElementById('reg_password');
        const confirmPasswordField = document.getElementById('reg_confirm_password');

        function validatePasswordMatch() {
          if (passwordField.value !== confirmPasswordField.value) {
            confirmPasswordField.setCustomValidity('Passwords do not match');
          } else {
            confirmPasswordField.setCustomValidity('');
          }
        }

        passwordField.addEventListener('change', validatePasswordMatch);
        confirmPasswordField.addEventListener('keyup', validatePasswordMatch);

        // Registration form submission
        const registerFormElement = document.getElementById('registerform');
        const formMessages = document.getElementById('form-messages');

        registerFormElement.addEventListener('submit', function(e) {
          e.preventDefault();
          
          const formData = new FormData(registerFormElement);
          formData.append('action', 'custom_user_registration');
          
          formMessages.innerHTML = '<p class="text-blue-600">Creating your account...</p>';

          fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
            method: 'POST',
            body: formData
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              formMessages.innerHTML = '<p class="text-green-600">Account created successfully! Redirecting...</p>';
              setTimeout(() => {
                window.location.reload();
              }, 2000);
            } else {
              formMessages.innerHTML = '<p class="text-red-600">' + (data.data || 'Registration failed. Please try again.') + '</p>';
            }
          })
          .catch(error => {
            formMessages.innerHTML = '<p class="text-red-600">An error occurred. Please try again.</p>';
          });
        });
      });
    </script>
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
              <button class="w-full flex items-center justify-between px-6 py-3 hover:bg-gray-50 text-left transition-colors duration-200" :class="activeTab === 'orders' ? 'text-[#FF3A5E] font-medium' : ''" @click="activeTab = 'orders'">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>
                  Orders
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </button>
              <button class="w-full flex items-center justify-between px-6 py-3 hover:bg-gray-50 text-left transition-colors duration-200" :class="activeTab === 'wishlist' ? 'text-[#FF3A5E] font-medium' : ''" @click="activeTab = 'wishlist'">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path></svg>
                  Wishlist
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </button>
              <button class="w-full flex items-center justify-between px-6 py-3 hover:bg-gray-50 text-left transition-colors duration-200" :class="activeTab === 'addresses' ? 'text-[#FF3A5E] font-medium' : ''" @click="activeTab = 'addresses'">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                  Addresses
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </button>
              <button class="w-full flex items-center justify-between px-6 py-3 hover:bg-gray-50 text-left transition-colors duration-200" :class="activeTab === 'payment' ? 'text-[#FF3A5E] font-medium' : ''" @click="activeTab = 'payment'">
                <div class="flex items-center">
                  <svg xmlns="http://www.w3.org/2000/svg" class="mr-3 h-5 w-5" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                  Payment Methods
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
              </button>
            </div>
            <div class="border-t border-gray-200">
              <a href="<?php echo esc_url(wc_logout_url()); ?>" class="w-full flex items-center px-6 py-3 text-red-600 hover:bg-red-50 text-left transition-colors duration-200">
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
                <a href="#" class="text-base font-medium text-gray-900 hover:text-[#FF3A5E] transition-colors duration-200" data-add-address data-type="shipping">Add New Address</a>
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
                      <a href="#" class="text-base font-medium text-[#1a1a1a] hover:text-[#FF3A5E] transition-colors duration-200" data-edit-address data-type="<?php echo esc_attr($type); ?>" data-address='<?php echo json_encode(array_merge($address, ["edit" => true])); ?>'>Edit</a>
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
        <button type="submit" class="bg-[#FF3A5E] text-white px-6 py-2 rounded font-semibold hover:bg-[#e62a4d] transition-colors duration-200">Save Address</button>
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

<?php
get_footer(); 