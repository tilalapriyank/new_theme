<?php
/**
 * Edit address form
 */

defined('ABSPATH') || exit;

// Include WordPress functions
require_once(ABSPATH . 'wp-includes/pluggable.php');
require_once(ABSPATH . 'wp-includes/formatting.php');
require_once(ABSPATH . 'wp-includes/link-template.php');

// Include WooCommerce functions
require_once(WC()->plugin_path() . '/includes/wc-template-functions.php');
require_once(WC()->plugin_path() . '/includes/wc-account-functions.php');

if (!isset($load_address)) {
    $load_address = 'shipping'; // Default to shipping if not set
}
if (!isset($address)) {
    $address = array(); // Default to empty array if not set
}

$page_title = ($load_address === 'billing') ? __('Billing address', 'woocommerce') : __('Shipping address', 'woocommerce');
$address_type = $load_address;
?>

<div class="edit-address-wrapper">
    <div class="form-header">
        <h1><?php echo esc_html($page_title); ?></h1>
        <p>Update your <?php echo esc_html(strtolower($page_title)); ?> information</p>
    </div>

    <form method="post" class="edit-address">
        <?php do_action("woocommerce_before_edit_address_form_{$load_address}"); ?>

        <div class="address-form">
            <?php
            foreach ($address as $key => $field) {
                woocommerce_form_field($key, $field, wc_get_post_data_by_key($key, $field['value']));
            }
            ?>
        </div>

        <?php do_action("woocommerce_after_edit_address_form_{$load_address}"); ?>

        <div class="form-actions">
            <button type="submit" class="button save-button" name="save_address" value="<?php esc_attr_e('Save address', 'woocommerce'); ?>">Save Address</button>
            <?php wp_nonce_field('woocommerce-edit_address', 'woocommerce-edit-address-nonce'); ?>
            <input type="hidden" name="action" value="edit_address" />
        </div>
    </form>
</div>

<style>
.edit-address-wrapper {
    max-width: 1200px;
    margin: 0 auto;
    padding: 2rem;
}

.form-header {
    margin-bottom: 2rem;
}

.form-header h1 {
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.form-header p {
    color: #666;
    margin: 0;
}

.address-form {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    margin-bottom: 2rem;
}

.form-row {
    margin-bottom: 1.5rem;
}

.form-row:last-child {
    margin-bottom: 0;
}

.form-row label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-row .required {
    color: #dc2626;
}

.form-row .woocommerce-input-wrapper {
    display: block;
}

.form-row input.input-text,
.form-row select,
.form-row textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.form-row input.input-text:focus,
.form-row select:focus,
.form-row textarea:focus {
    outline: none;
    border-color: #0066cc;
}

.form-row .woocommerce-input-wrapper .select2-container {
    width: 100% !important;
}

.form-row .select2-container--default .select2-selection--single {
    height: 45px;
    border: 1px solid #ddd;
    border-radius: 6px;
}

.form-row .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 45px;
    padding-left: 0.75rem;
}

.form-row .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 43px;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
}

.save-button {
    background: #0066cc;
    color: #fff;
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.3s ease;
}

.save-button:hover {
    opacity: 0.9;
}

@media (max-width: 768px) {
    .form-actions {
        flex-direction: column;
    }
    
    .save-button {
        width: 100%;
    }
}
</style>

<script>
jQuery(document).ready(function($) {
    // Initialize Select2 if available
    if ($.fn.select2) {
        $('.woocommerce-input-wrapper select').select2({
            width: '100%',
            minimumResultsForSearch: 6
        });
    }
    
    // Form validation
    $('.edit-address').on('submit', function(e) {
        var requiredFields = $(this).find('input[required], select[required], textarea[required]');
        var isValid = true;
        
        requiredFields.each(function() {
            if (!$(this).val()) {
                isValid = false;
                $(this).addClass('error');
            } else {
                $(this).removeClass('error');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
            return false;
        }
    });
    
    // Remove error class on input
    $('.woocommerce-input-wrapper input, .woocommerce-input-wrapper select, .woocommerce-input-wrapper textarea').on('input change', function() {
        $(this).removeClass('error');
    });
});
</script> 