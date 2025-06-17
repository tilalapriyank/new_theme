/**
 * Hype Pups Multistep Checkout JavaScript
 * Place in: /wp-content/themes/hype-pups/assets/js/multistep-checkout.js
 */

jQuery(document).ready(function($) {
    var currentStep = 1;
    var totalSteps = 3;
    var checkoutData = {};

    console.log('Hype Pups Multistep Checkout initialized');

    // Initialize
    function init() {
        console.log('Initializing multistep checkout...');
        showStep(currentStep);
        setupEventListeners();
        loadSavedData();
        
        // Initialize WooCommerce checkout
        if (typeof wc_checkout_params !== 'undefined') {
            console.log('WooCommerce checkout params available');
            $('body').trigger('init_checkout');
        }

        // Update order review initially
        updateOrderReview();
        
        // Initialize payment methods
        setTimeout(function() {
            initializePaymentMethods();
        }, 1000);
    }

    function setupEventListeners() {
        // Continue button click
        $(document).on('click', '.continue-to-step', function(e) {
            e.preventDefault();
            var step = parseInt($(this).data('step'));
            
            console.log('Continue button clicked for step:', step);
            
            if (validateCurrentStep(step)) {
                saveCurrentStepData(step);
                moveToStep(step + 1);
            }
        });

        // Back button click
        $(document).on('click', '.back-to-step', function(e) {
            e.preventDefault();
            var step = parseInt($(this).data('step'));
            console.log('Back button clicked to step:', step);
            moveToStep(step);
        });

        // Payment method change
        $(document).on('change', 'input[name="payment_method"]', function() {
            var selectedMethod = $(this).val();
            console.log('Payment method changed to:', selectedMethod);
            
            // Hide all payment boxes first
            $('.payment_box').hide();
            
            // Show the selected payment box
            $('.payment_method_' + selectedMethod + ' .payment_box').show();
            
            // Special handling for PayPal
            if (selectedMethod.includes('paypal') || selectedMethod.includes('ppec')) {
                console.log('PayPal payment method selected');
                $('#place_order').text('Pay with PayPal');
            } else {
                $('#place_order').text('Place Order');
            }
            
            // Trigger WooCommerce event
            $('body').trigger('payment_method_selected');
        });

        // Ship to different address toggle
        $(document).on('change', '#ship_to_different_address', function() {
            if ($(this).is(':checked')) {
                console.log('Ship to different address checked');
                createShippingFields();
            } else {
                console.log('Ship to different address unchecked');
                removeShippingFields();
            }
            updateOrderReview();
        });

        // Real-time validation
        $(document).on('blur', 'input[required], select[required]', function() {
            validateField($(this));
        });

        // Clear errors on input
        $(document).on('input change', 'input, select', function() {
            clearFieldError($(this));
        });

        // Form submission - UPDATED
        $(document).on('submit', 'form.checkout', function(e) {
            console.log('Form submission attempted on step:', currentStep);
            
            if (currentStep !== 3) {
                console.log('Preventing submission - not on final step');
                e.preventDefault();
                return false;
            }
            
            // Final validation
            if (!validateStep3()) {
                console.log('Final validation failed');
                e.preventDefault();
                return false;
            }
            
            console.log('Processing order with payment...');
            
            // Show loading state
            $('#place_order').prop('disabled', true).addClass('processing');
            
            var selectedPayment = $('input[name="payment_method"]:checked').val();
            if (selectedPayment && (selectedPayment.includes('paypal') || selectedPayment.includes('ppec'))) {
                $('#place_order').text('Redirecting to PayPal...');
            } else {
                $('#place_order').text('Processing Order...');
            }
            
            // Save all form data before submission
            saveAllFormData();
            
            // Let WooCommerce handle the actual submission and payment
            return true;
        });

        // Handle WooCommerce events
        $('body').on('update_checkout', function() {
            console.log('WooCommerce update_checkout triggered');
            updateOrderReview();
        });

        $('body').on('updated_checkout', function(e, data) {
            console.log('WooCommerce updated_checkout triggered');
            if (currentStep === 2) {
                initializePaymentMethods();
            }
        });

        // Country/State change for address updates
        $(document).on('change', '#billing_country, #shipping_country', function() {
            console.log('Country changed:', $(this).val());
            updateStatesForCountry($(this));
            clearTimeout(window.addressUpdateTimer);
            window.addressUpdateTimer = setTimeout(function() {
                updateOrderReview();
            }, 1000);
        });

        // Address field changes for shipping calculation
        $(document).on('change blur', '#billing_postcode, #shipping_postcode, #billing_city, #shipping_city', function() {
            clearTimeout(window.addressUpdateTimer);
            window.addressUpdateTimer = setTimeout(function() {
                updateOrderReview();
            }, 1500);
        });

        // Auto-save form data periodically
        setInterval(function() {
            saveFormDataToStorage();
        }, 10000); // Save every 10 seconds

        // Save on form changes
        $(document).on('change', 'form.checkout input, form.checkout select', function() {
            saveFormDataToStorage();
        });
    }

    function showStep(step) {
        console.log('Showing step:', step);
        
        $('.checkout-step').hide();
        $('.checkout-step[data-step="' + step + '"]').show();
        updateProgressBar(step);
        currentStep = step;
        
        // Update page title based on step
        var titles = {
            1: 'Contact & Shipping Information',
            2: 'Payment Method',
            3: 'Review Your Order'
        };
        
        if (titles[step]) {
            $('#page-title').text(titles[step]);
        }
        
        // Scroll to top
        $('html, body').animate({
            scrollTop: $('#main-content').offset().top - 50
        }, 300);

        // Step-specific actions
        if (step === 2) {
            console.log('Initializing payment methods for step 2');
            setTimeout(function() {
                initializePaymentMethods();
                updateOrderReview();
            }, 500);
        } else if (step === 3) {
            console.log('Updating review summary for step 3');
            // updateReviewSummary();
            updateOrderReview();
        }

        // Save current step to session
        saveStepToSession(step);
    }

    function moveToStep(step) {
        if (step >= 1 && step <= totalSteps) {
            showStep(step);
        }
    }

    function validateCurrentStep(step) {
        console.log('Validating step:', step);
        
        switch(step) {
            case 1:
                return validateStep1();
            case 2:
                return validateStep2();
            case 3:
                return validateStep3();
            default:
                return true;
        }
    }

    function validateStep1() {
        console.log('Validating Step 1');
        
        var isValid = true;
        var requiredFields = [
            'billing_first_name',
            'billing_last_name',
            'billing_email',
            'billing_phone',
            'billing_address_1',
            'billing_city',
            'billing_state',
            'billing_postcode',
            'billing_country'
        ];

        // Add shipping fields if different address is selected
        if ($('#ship_to_different_address').is(':checked')) {
            console.log('Different shipping address selected, adding shipping fields to validation');
            requiredFields = requiredFields.concat([
                'shipping_address_1',
                'shipping_city',
                'shipping_state',
                'shipping_postcode',
                'shipping_country'
            ]);
        }

        requiredFields.forEach(function(fieldName) {
            var $field = $('#' + fieldName);
            if ($field.length && !validateField($field)) {
                console.log('Validation failed for field:', fieldName);
                isValid = false;
            }
        });

        if (!isValid) {
            showMessage('Please fill in all required fields correctly.', 'error');
            return false;
        }

        console.log('Step 1 validation passed');
        return true;
    }

    function validateStep2() {
        console.log('Validating Step 2');
        
        var selectedPayment = $('input[name="payment_method"]:checked');
        
        if (selectedPayment.length === 0) {
            console.log('No payment method selected');
            showMessage('Please select a payment method.', 'error');
            return false;
        }

        // Validate payment method specific fields
        var paymentMethod = selectedPayment.val();
        var $paymentBox = $('.payment_method_' + paymentMethod + ' .payment_box');
        var isValid = true;

        $paymentBox.find('input[required], select[required]').each(function() {
            if (!validateField($(this))) {
                isValid = false;
            }
        });

        if (!isValid) {
            showMessage('Please complete all payment information.', 'error');
            return false;
        }

        console.log('Step 2 validation passed for payment method:', paymentMethod);
        return true;
    }

    function validateStep3() {
        console.log('Validating Step 3');
        
        // Check terms and conditions
        if ($('#terms').length && !$('#terms').is(':checked')) {
            console.log('Terms and conditions not accepted');
            showMessage('You must accept the terms and conditions.', 'error');
            return false;
        }

        console.log('Step 3 validation passed');
        return true;
    }

    function validateField($field) {
        var value = $field.val() ? $field.val().trim() : '';
        var fieldName = $field.attr('name') || $field.attr('id');
        var isValid = true;
        var errorMessage = '';

        // Clear previous error
        clearFieldError($field);

        // Required field validation
        if ($field.prop('required') && !value) {
            errorMessage = 'This field is required.';
            isValid = false;
        }
        // Email validation
        else if (fieldName === 'billing_email' && value && !isValidEmail(value)) {
            errorMessage = 'Please enter a valid email address.';
            isValid = false;
        }
        // Phone validation
        else if (fieldName === 'billing_phone' && value && !isValidPhone(value)) {
            errorMessage = 'Please enter a valid phone number.';
            isValid = false;
        }

        if (!isValid) {
            showFieldError($field, errorMessage);
        }

        return isValid;
    }

    function showFieldError($field, message) {
        $field.addClass('border-red-500 border-red-400');
        var fieldId = $field.attr('id');
        var $error = $('#error_' + fieldId);
        
        if ($error.length === 0) {
            $field.after('<span class="error-message text-xs text-red-500 mt-1 block" id="error_' + fieldId + '">' + message + '</span>');
        } else {
            $error.text(message).removeClass('hidden').show();
        }
    }

    function clearFieldError($field) {
        $field.removeClass('border-red-500 border-red-400');
        var fieldId = $field.attr('id');
        $('#error_' + fieldId).remove();
    }

    function isValidEmail(email) {
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function isValidPhone(phone) {
        var phoneRegex = /^[\d\s\-\(\)\+\.]{10,}$/;
        return phoneRegex.test(phone);
    }

    function updateProgressBar(step) {
        $('.checkout-progress .step').each(function() {
            var stepNum = parseInt($(this).data('step'));
            var $circle = $(this).find('.progress-circle');
            var $content = $circle.find('.progress-content');
            
            $content.empty();
            
            if (stepNum < step) {
                // Completed step
                $circle.removeClass('bg-gray-200').addClass('bg-[#FF3A5E]');
                $content.html('<svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path></svg>');
            } else if (stepNum === step) {
                // Current step
                $circle.removeClass('bg-gray-200').addClass('bg-[#FF3A5E]');
                $content.html('<span class="text-xl">' + stepNum + '</span>');
            } else {
                // Future step
                $circle.removeClass('bg-[#FF3A5E]').addClass('bg-gray-200');
                $content.html('<span class="text-xl text-gray-500">' + stepNum + '</span>');
            }
        });

        // Update progress lines
        $('.checkout-progress .progress-line').each(function() {
            var lineStep = parseInt($(this).data('bar'));
            if (lineStep <= step) {
                $(this).removeClass('bg-gray-200').addClass('bg-[#FF3A5E]');
            } else {
                $(this).removeClass('bg-[#FF3A5E]').addClass('bg-gray-200');
            }
        });
    }

    function updateOrderReview() {
        console.log('Updating order review...');
        
        // Show loading if order review exists
        if ($('#order_review').length) {
            $('#order_review').addClass('checkout-loading');
        }

        // Get current form data
        var formData = getFormData();
        
        // Update WooCommerce with current data
        $.ajax({
            url: hypePupsCheckout.wc_ajax_url.replace('%%endpoint%%', 'update_order_review'),
            type: 'POST',
            data: {
                ...formData,
                action: 'woocommerce_update_order_review',
                security: hypePupsCheckout.update_order_review_nonce
            },
            success: function(response) {
                console.log('Order review updated successfully');
                
                $('#order_review').removeClass('checkout-loading');
                
                if (response.fragments) {
                    // Update fragments
                    $.each(response.fragments, function(key, value) {
                        $(key).html(value);
                    });
                }
                
                if (response.order_review) {
                    $('#order_review').html(response.order_review);
                }

                // Trigger updated event
                $('body').trigger('updated_checkout', [response]);
            },
            error: function(xhr, status, error) {
                console.log('Order review update failed:', error);
                $('#order_review').removeClass('checkout-loading');
                
                // Fallback: trigger WooCommerce update
                $('body').trigger('update_checkout');
            }
        });
    }

    function updateReviewSummary() {
        console.log('Updating review summary');
        
        var formData = getFormData();
        
        // Create or update review summary
        var reviewHtml = '<div class="review-customer-info bg-gray-50 p-4 rounded mb-4">';
        reviewHtml += '<h3 class="font-semibold mb-2">Customer Information</h3>';
        reviewHtml += '<p><strong>Name:</strong> ' + (formData.billing_first_name || '') + ' ' + (formData.billing_last_name || '') + '</p>';
        reviewHtml += '<p><strong>Email:</strong> ' + (formData.billing_email || '') + '</p>';
        reviewHtml += '<p><strong>Phone:</strong> ' + (formData.billing_phone || '') + '</p>';
        
        var paymentMethodText = $('input[name="payment_method"]:checked').closest('.wc_payment_method').find('label').text().trim();
        if (paymentMethodText) {
            reviewHtml += '<p><strong>Payment Method:</strong> ' + paymentMethodText + '</p>';
        }
        
        reviewHtml += '</div>';
        
        // Insert or update the review info
        var $existingReview = $('.review-customer-info');
        if ($existingReview.length) {
            $existingReview.replaceWith(reviewHtml);
        } else {
            $('.checkout-step[data-step="3"] .space-y-6').prepend(reviewHtml);
        }
    }

    function initializePaymentMethods() {
        console.log('Initializing payment methods');
        
        // Ensure a payment method is selected
        if ($('input[name="payment_method"]:checked').length === 0) {
            console.log('No payment method selected, selecting first available');
            $('input[name="payment_method"]:first').prop('checked', true);
        }
        
        // Trigger change event to show payment fields
        $('input[name="payment_method"]:checked').trigger('change');
        
        // Initialize PayPal if selected
        var selectedPayment = $('input[name="payment_method"]:checked').val();
        if (selectedPayment && (selectedPayment.includes('paypal') || selectedPayment.includes('ppec'))) {
            console.log('Initializing PayPal for method:', selectedPayment);
            initializePayPal();
        }
    }

    function initializePayPal() {
        console.log('PayPal initialization');
        
        // PayPal specific initialization
        if (typeof paypal !== 'undefined') {
            console.log('PayPal SDK available');
        } else {
            console.log('PayPal SDK not yet loaded');
        }
    }

    function showMessage(message, type) {
        console.log('Showing message:', message, 'Type:', type);
        
        // Remove existing messages
        $('.checkout-message').remove();
        
        var alertClass = type === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-green-100 border-green-400 text-green-700';
        var messageHtml = '<div class="checkout-message ' + alertClass + ' border px-4 py-3 rounded mb-4">' + 
                         '<span class="block sm:inline">' + message + '</span>' +
                         '</div>';
        
        $('.checkout-step[data-step="' + currentStep + '"]:visible').prepend(messageHtml);
        
        // Scroll to message
        $('html, body').animate({
            scrollTop: $('.checkout-message').offset().top - 100
        }, 300);
        
        // Auto-remove after 5 seconds
        setTimeout(function() {
            $('.checkout-message').fadeOut(function() {
                $(this).remove();
            });
        }, 5000);
    }

    function getFormData() {
        var formData = {};
        $('form.checkout').find('input, select, textarea').each(function() {
            var $field = $(this);
            var name = $field.attr('name');
            var value = $field.val();
            
            if (name) {
                if ($field.is(':checkbox') || $field.is(':radio')) {
                    if ($field.is(':checked')) {
                        formData[name] = value;
                    }
                } else {
                    formData[name] = value || '';
                }
            }
        });
        return formData;
    }

    function saveCurrentStepData(step) {
        console.log('Saving current step data for step:', step);
        
        var formData = getFormData();
        checkoutData['step_' + step] = formData;
        
        // Save to sessionStorage
        saveFormDataToStorage();

        // Save to server using AJAX
        $.ajax({
            url: hypePupsCheckout.ajax_url,
            type: 'POST',
            data: {
                action: 'hype_pups_save_checkout_step',
                step: step,
                fields: formData,
                nonce: hypePupsCheckout.nonce
            },
            success: function(response) {
                console.log('Step ' + step + ' data saved successfully to server');
            },
            error: function(xhr, status, error) {
                console.log('Error saving step data to server:', error);
            }
        });
    }

    function saveAllFormData() {
        console.log('Saving all form data before submission');
        
        var formData = getFormData();
        
        // Update WooCommerce customer data
        $.ajax({
            url: hypePupsCheckout.ajax_url,
            type: 'POST',
            data: {
                action: 'hype_pups_update_customer_data',
                fields: formData,
                nonce: hypePupsCheckout.nonce
            },
            async: false, // Make synchronous to ensure data is saved before form submission
            success: function(response) {
                console.log('Customer data updated successfully');
            },
            error: function(xhr, status, error) {
                console.log('Error updating customer data:', error);
            }
        });
    }

    function loadSavedData() {
        console.log('Loading saved data');
        
        loadFormDataFromStorage();
        
        // Restore step if we have saved data
        try {
            var savedStep = sessionStorage.getItem('hype_pups_checkout_step');
            if (savedStep && parseInt(savedStep) > 1) {
                // Only restore step if we have some form data
                if ($('#billing_first_name').val() || $('#billing_email').val()) {
                    console.log('Restoring saved step:', savedStep);
                    currentStep = parseInt(savedStep);
                    showStep(currentStep);
                }
            }
        } catch (e) {
            console.log('Error loading saved step:', e);
        }
    }

    function saveFormDataToStorage() {
        try {
            var formData = getFormData();
            sessionStorage.setItem('hype_pups_checkout_form_data', JSON.stringify(formData));
            sessionStorage.setItem('hype_pups_checkout_step', currentStep);
        } catch (e) {
            console.log('Error saving form data to storage:', e);
        }
    }

    function loadFormDataFromStorage() {
        try {
            var savedData = sessionStorage.getItem('hype_pups_checkout_form_data');
            if (savedData) {
                var formData = JSON.parse(savedData);
                console.log('Loading form data from storage');
                
                $.each(formData, function(name, value) {
                    var $field = $('[name="' + name + '"]');
                    if ($field.length && value) {
                        if ($field.is(':checkbox') || $field.is(':radio')) {
                            $field.filter('[value="' + value + '"]').prop('checked', true);
                        } else {
                            $field.val(value);
                        }
                    }
                });
            }
        } catch (e) {
            console.log('Error loading form data from storage:', e);
        }
    }

    function saveStepToSession(step) {
        try {
            sessionStorage.setItem('hype_pups_checkout_step', step);
        } catch (e) {
            console.log('Error saving step to session:', e);
        }
    }

    function createShippingFields() {
        var shippingHTML = `
            <div id="shipping-address-fields" class="mt-6">
                <h3 class="text-lg font-semibold mb-4">Shipping Address</h3>
                <div class="mb-4">
                    <label for="shipping_address_1" class="block text-sm font-medium mb-1">Street Address <span class="text-[#FF3A5E]">*</span></label>
                    <input type="text" name="shipping_address_1" id="shipping_address_1" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="shipping_address_2" class="block text-sm font-medium mb-1">Apartment, suite, etc. (optional)</label>
                    <input type="text" name="shipping_address_2" id="shipping_address_2" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm">
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="shipping_city" class="block text-sm font-medium mb-1">City <span class="text-[#FF3A5E]">*</span></label>
                        <input type="text" name="shipping_city" id="shipping_city" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" required>
                    </div>
                    <div>
                        <label for="shipping_state" class="block text-sm font-medium mb-1">State <span class="text-[#FF3A5E]">*</span></label>
                        <select name="shipping_state" id="shipping_state" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" required>
                            <option value="">Select state</option>
                            ${getStateOptions()}
                        </select>
                    </div>
                    <div>
                        <label for="shipping_postcode" class="block text-sm font-medium mb-1">Zip Code <span class="text-[#FF3A5E]">*</span></label>
                        <input type="text" name="shipping_postcode" id="shipping_postcode" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" required>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="shipping_country" class="block text-sm font-medium mb-1">Country <span class="text-[#FF3A5E]">*</span></label>
                    <select name="shipping_country" id="shipping_country" class="w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm" required>
                        ${getCountryOptions()}
                    </select>
                </div>
            </div>
        `;
        
        $('#shipping-fields').append(shippingHTML);
    }

    function removeShippingFields() {
        $('#shipping-address-fields').remove();
    }

    function getStateOptions() {
        var options = '';
        $('#billing_state option').each(function() {
            options += '<option value="' + $(this).val() + '">' + $(this).text() + '</option>';
        });
        return options;
    }

    function getCountryOptions() {
        var options = '';
        $('#billing_country option').each(function() {
            options += '<option value="' + $(this).val() + '">' + $(this).text() + '</option>';
        });
        return options;
    }

    function updateStatesForCountry($countryField) {
        var country = $countryField.val();
        var isShipping = $countryField.attr('id').includes('shipping');
        
        console.log('Updating states for country:', country, 'IsShipping:', isShipping);
        
        // Trigger WooCommerce country/state update
        $('body').trigger('update_checkout');
    }

    function clearAddressFields() {
        console.log('Clearing address fields');
        $('#billing_address_1, #billing_address_2, #billing_city, #billing_state, #billing_postcode').val('');
    }

    // Handle WooCommerce checkout errors
    $('body').on('checkout_error', function(e, error_message) {
        console.log('Checkout error:', error_message);
        showMessage(error_message, 'error');
        $('#place_order').prop('disabled', false).removeClass('processing');
        
        // Update button text for PayPal if needed
        var selectedPayment = $('input[name="payment_method"]:checked').val();
        if (selectedPayment && (selectedPayment.includes('paypal') || selectedPayment.includes('ppec'))) {
            $('#place_order').text('Pay with PayPal');
        } else {
            $('#place_order').text('Place Order');
        }
    });

    // Handle successful checkout initiation
    $('body').on('checkout_place_order', function() {
        console.log('Checkout place order event triggered');
        if (currentStep === 3) {
            $('#place_order').prop('disabled', true).addClass('processing');
            
            var selectedPayment = $('input[name="payment_method"]:checked').val();
            if (selectedPayment && (selectedPayment.includes('paypal') || selectedPayment.includes('ppec'))) {
                $('#place_order').text('Redirecting to PayPal...');
            } else {
                $('#place_order').text('Processing Order...');
            }
        }
    });

    // PayPal specific event handlers
    $(document).on('checkout_place_order_paypal', function() {
        console.log('PayPal order processing initiated');
        return true;
    });

    $(document).on('checkout_place_order_ppec_paypal', function() {
        console.log('PayPal Express Checkout order processing initiated');
        return true;
    });

    // Enhanced form validation with visual feedback
    $('input[required], select[required]').on('blur', function() {
        var $field = $(this);
        var value = $field.val().trim();
        
        if (!value) {
            $field.addClass('border-red-500');
        } else {
            $field.removeClass('border-red-500').addClass('border-green-500');
            setTimeout(function() {
                $field.removeClass('border-green-500');
            }, 2000);
        }
    });

    // Email validation with visual feedback
    $('input[type="email"]').on('blur', function() {
        var $field = $(this);
        var email = $field.val().trim();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email && !emailRegex.test(email)) {
            $field.addClass('border-red-500');
            if (!$('#error_' + $field.attr('id')).length) {
                $field.after('<span class="error-message text-xs text-red-500 mt-1 block" id="error_' + $field.attr('id') + '">Please enter a valid email address</span>');
            }
        } else if (email) {
            $field.removeClass('border-red-500').addClass('border-green-500');
            $('#error_' + $field.attr('id')).remove();
            setTimeout(function() {
                $field.removeClass('border-green-500');
            }, 2000);
        }
    });

    // Phone validation with visual feedback
    $('input[type="tel"]').on('blur', function() {
        var $field = $(this);
        var phone = $field.val().trim();
        var phoneRegex = /^[\d\s\-\(\)\+\.]{10,}$/;
        
        if (phone && !phoneRegex.test(phone)) {
            $field.addClass('border-red-500');
            if (!$('#error_' + $field.attr('id')).length) {
                $field.after('<span class="error-message text-xs text-red-500 mt-1 block" id="error_' + $field.attr('id') + '">Please enter a valid phone number</span>');
            }
        } else if (phone) {
            $field.removeClass('border-red-500').addClass('border-green-500');
            $('#error_' + $field.attr('id')).remove();
            setTimeout(function() {
                $field.removeClass('border-green-500');
            }, 2000);
        }
    });

    // Clear errors on input
    $('input, select').on('input change', function() {
        $(this).removeClass('border-red-500');
        $('#error_' + $(this).attr('id')).remove();
    });

    // Clear saved data when checkout is completed
    if (window.location.href.indexOf('order-received') !== -1) {
        console.log('Order completed, clearing saved data');
        try {
            sessionStorage.removeItem('hype_pups_checkout_form_data');
            sessionStorage.removeItem('hype_pups_checkout_step');
        } catch (e) {
            console.log('Error clearing session data:', e);
        }
    }

    // Debug information
    if (typeof console !== 'undefined' && console.log) {
        console.log('Checkout debug info:', {
            'Current step': currentStep,
            'Payment methods available': $('input[name="payment_method"]').length,
            'Selected payment method': $('input[name="payment_method"]:checked').val(),
            'WooCommerce loaded': typeof wc_checkout_params !== 'undefined',
            'AJAX URL': typeof hypePupsCheckout !== 'undefined' ? hypePupsCheckout.ajax_url : 'Not available',
            'HypePupsCheckout available': typeof hypePupsCheckout !== 'undefined'
        });
    }

    // Initialize everything
    init();
});