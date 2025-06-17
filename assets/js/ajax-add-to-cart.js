// [REMOVED] Custom AJAX add-to-cart logic. File intentionally left blank to disable custom AJAX add-to-cart JS.

jQuery(function($) {
    // Handle quantity buttons
    $('#decrease-quantity').on('click', function(e) {
        e.preventDefault();
        var $quantity = $('#quantity');
        var currentQty = parseInt($quantity.text());
        if (currentQty > 1) {
            $quantity.text(currentQty - 1);
            $('#quantity-input').val(currentQty - 1);
        }
    });

    $('#increase-quantity').on('click', function(e) {
        e.preventDefault();
        var $quantity = $('#quantity');
        var currentQty = parseInt($quantity.text());
        $quantity.text(currentQty + 1);
        $('#quantity-input').val(currentQty + 1);
    });

    // Handle add to cart form submission
    $('form.cart').on('submit', function(e) {
        e.preventDefault();
      
        var $form = $(this);
        var $button = $form.find('button[type="submit"]');
        var product_id = $form.find('input[name="add-to-cart"]').val();
        var variation_id = $form.find('input[name="variation_id"]').val() || 0;
        var $quantity = $('#quantity-input').val() || 1;
        
        // For variable products, get the selected variation
        if ($form.find('input[name="variation_id"]').length) {
            variation_id = $form.find('input[name="variation_id"]').val();
            if (!variation_id) {
                alert('Please select product options before adding to cart.');
                return;
            }
        }
        
        // Disable button and show loading state
        $button.prop('disabled', true).addClass('loading');
        
        // Make AJAX request
        $.ajax({
            type: 'POST',
            url: hype_pups_ajax.ajax_url,
            data: {
                action: 'hype_pups_ajax_add_to_cart',
                nonce: hype_pups_ajax.nonce,
                product_id: product_id,
                variation_id: variation_id,
                quantity: $quantity
            },
            success: function(response) {
                if (response.error) {
                    alert(response.message);
                } else {
                    // Update cart fragments
                    if (response.fragments) {
                        $.each(response.fragments, function(key, value) {
                            $(key).replaceWith(value);
                        });
                    }
                    
                    // Update cart hash and trigger refresh
                    if (response.cart_hash) {
                        $(document.body).trigger('wc_fragment_refresh');
                        $(document.body).trigger('added_to_cart', [product_id, $quantity, variation_id]);
                    }
                    
                    // Show success message
                    const message = document.createElement('div');
                    message.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
                    message.textContent = 'Product added to cart!';
                    document.body.appendChild(message);
                    
                    setTimeout(() => {
                        message.remove();
                    }, 3000);
                }
            },
            error: function() {
                alert('Error adding product to cart. Please try again.');
            },
            complete: function() {
                $button.prop('disabled', false).removeClass('loading');
            }
        });
    });

    // Handle variation selection
    if ($('form.variations_form').length) {
        $('form.variations_form').on('show_variation', function(event, variation) {
            // Update add to cart button with variation ID
            $('input[name="variation_id"]').val(variation.variation_id);
        });
    }

    // Listen for add to cart button clicks on shop/archive page
    $(document).on('click', '.add_to_cart_button.ajax_add_to_cart', function(e) {
        e.preventDefault();
        var $button = $(this);
        if ($button.hasClass('loading')) return;

        var product_id = $button.data('product_id');
        var quantity = $button.data('quantity') || 1;
        var product_sku = $button.data('product_sku') || '';

        $button.addClass('loading').prop('disabled', true).text('Adding...');

        // AJAX call to WooCommerce built-in endpoint
        $.ajax({
            url: wc_add_to_cart_params.ajax_url,
            type: 'POST',
            data: {
                action: 'woocommerce_ajax_add_to_cart',
                product_id: product_id,
                quantity: quantity,
                product_sku: product_sku
            },
            success: function(response) {
                if (response && response.fragments) {
                    // Update cart fragments
                    $.each(response.fragments, function(key, value) {
                        $(key).replaceWith(value);
                    });
                    // Trigger WooCommerce event
                    $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                    showToast('Product added to cart!');
                } else if (response && response.error && response.product_url) {
                    // Redirect to product page if needed (e.g., variable product)
                    window.location = response.product_url;
                } else {
                    showToast('Error adding to cart', 'error');
                }
            },
            error: function() {
                showToast('Error adding to cart', 'error');
            },
            complete: function() {
                $button.removeClass('loading').prop('disabled', false).text('Add to Cart');
            }
        });
    });

    // Toast notification function (same as single product)
    function showToast(message, type = 'success') {
        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            toast.className = 'fixed top-4 right-4 px-6 py-3 rounded-md opacity-0 transition-opacity duration-300 z-50';
            document.body.appendChild(toast);
        }
        toast.textContent = message;
        toast.className = `fixed top-4 right-4 text-white px-6 py-3 rounded-md transition-opacity duration-300 z-50 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
        toast.style.opacity = '1';
        setTimeout(() => {
            toast.style.opacity = '0';
        }, 3000);
    }
}); 