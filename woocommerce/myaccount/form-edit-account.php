<?php
/**
 * Edit account form
 */

defined('ABSPATH') || exit;

// Include WordPress functions
require_once(ABSPATH . 'wp-includes/pluggable.php');
require_once(ABSPATH . 'wp-includes/formatting.php');
require_once(ABSPATH . 'wp-includes/link-template.php');

// Include WooCommerce functions
require_once(WC()->plugin_path() . '/includes/wc-template-functions.php');
require_once(WC()->plugin_path() . '/includes/wc-account-functions.php');

$user = wp_get_current_user();
?>

<div class="edit-account-wrapper">
    <div class="form-header">
        <h1>Account Details</h1>
        <p>Update your personal information and password</p>
    </div>

    <form class="woocommerce-EditAccountForm edit-account" action="" method="post" <?php do_action('woocommerce_edit_account_form_tag'); ?>>
        <?php do_action('woocommerce_edit_account_form_start'); ?>

        <div class="form-grid">
            <div class="form-section">
                <h2>Personal Information</h2>
                
                <div class="form-row">
                    <label for="account_first_name">First name <span class="required">*</span></label>
                    <input type="text" class="input-text" name="account_first_name" id="account_first_name" value="<?php echo esc_attr($user->first_name); ?>" required />
                </div>

                <div class="form-row">
                    <label for="account_last_name">Last name <span class="required">*</span></label>
                    <input type="text" class="input-text" name="account_last_name" id="account_last_name" value="<?php echo esc_attr($user->last_name); ?>" required />
                </div>

                <div class="form-row">
                    <label for="account_display_name">Display name <span class="required">*</span></label>
                    <input type="text" class="input-text" name="account_display_name" id="account_display_name" value="<?php echo esc_attr($user->display_name); ?>" required />
                    <span class="description">This will be how your name will be displayed in the account section and in reviews</span>
                </div>

                <div class="form-row">
                    <label for="account_email">Email address <span class="required">*</span></label>
                    <input type="email" class="input-text" name="account_email" id="account_email" value="<?php echo esc_attr($user->user_email); ?>" required />
                </div>

                <div class="form-row">
                    <label for="account_phone">Phone Number</label>
                    <input type="text" class="input-text" name="account_phone" id="account_phone" value="<?php echo esc_attr(get_user_meta($user->ID, 'billing_phone', true)); ?>" />
                </div>
            </div>

            <div class="form-section">
                <h2>Password Change</h2>
                
                <div class="form-row">
                    <label for="password_current">Current password (leave blank to leave unchanged)</label>
                    <input type="password" class="input-text" name="password_current" id="password_current" />
                </div>

                <div class="form-row">
                    <label for="password_1">New password (leave blank to leave unchanged)</label>
                    <input type="password" class="input-text" name="password_1" id="password_1" />
                </div>

                <div class="form-row">
                    <label for="password_2">Confirm new password</label>
                    <input type="password" class="input-text" name="password_2" id="password_2" />
                </div>
            </div>
        </div>

        <?php do_action('woocommerce_edit_account_form'); ?>

        <div class="form-actions">
            <?php wp_nonce_field('save_account_details', 'save-account-details-nonce'); ?>
            <button type="submit" class="button save-button" name="save_account_details" value="<?php esc_attr_e('Save changes', 'woocommerce'); ?>">Save Changes</button>
            <input type="hidden" name="action" value="save_account_details" />
        </div>

        <?php do_action('woocommerce_edit_account_form_end'); ?>
    </form>
</div>

<style>
.edit-account-wrapper {
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

.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    margin-bottom: 2rem;
}

.form-section {
    background: #fff;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.form-section h2 {
    font-size: 1.25rem;
    margin: 0 0 1.5rem;
    padding-bottom: 0.75rem;
    border-bottom: 1px solid #eee;
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

.form-row .description {
    display: block;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #666;
}

.input-text {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.3s ease;
}

.input-text:focus {
    outline: none;
    border-color: #0066cc;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 2rem;
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
    .form-grid {
        grid-template-columns: 1fr;
    }
    
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
    $('.edit-account').on('submit', function(e) {
        var password1 = $('#password_1').val();
        var password2 = $('#password_2').val();
        
        if (password1 || password2) {
            if (password1 !== password2) {
                e.preventDefault();
                alert('New passwords do not match.');
                return false;
            }
            
            if (password1.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long.');
                return false;
            }
        }
    });
});
</script> 