<?php
/**
 * Login Form
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_customer_login_form'); ?>

<div class="login-form-wrapper">
    <div class="login-form-container">
        <h2><?php esc_html_e('Login', 'woocommerce'); ?></h2>

        <form class="woocommerce-form woocommerce-form-login login" method="post">
            <?php do_action('woocommerce_login_form_start'); ?>

            <div class="form-group">
                <label for="username"><?php esc_html_e('Username or email address', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
                <input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" value="<?php echo (!empty($_POST['username'])) ? esc_attr(wp_unslash($_POST['username'])) : ''; ?>" />
            </div>

            <div class="form-group">
                <label for="password"><?php esc_html_e('Password', 'woocommerce'); ?>&nbsp;<span class="required">*</span></label>
                <input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" />
            </div>

            <?php do_action('woocommerce_login_form'); ?>

            <div class="form-group remember-me">
                <label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
                    <input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" />
                    <span><?php esc_html_e('Remember me', 'woocommerce'); ?></span>
                </label>
            </div>

            <div class="form-group">
                <?php wp_nonce_field('woocommerce-login', 'woocommerce-login-nonce'); ?>
                <button type="submit" class="woocommerce-button button woocommerce-form-login__submit" name="login" value="<?php esc_attr_e('Log in', 'woocommerce'); ?>"><?php esc_html_e('Log in', 'woocommerce'); ?></button>
            </div>

            <div class="lost-password">
                <a href="<?php echo esc_url(wp_lostpassword_url()); ?>"><?php esc_html_e('Lost your password?', 'woocommerce'); ?></a>
            </div>

            <?php do_action('woocommerce_login_form_end'); ?>
        </form>
    </div>
</div>

<style>
.login-form-wrapper {
    max-width: 400px;
    margin: 0 auto;
    padding: 2rem;
}

.login-form-container {
    background: #fff;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.login-form-container h2 {
    margin-bottom: 1.5rem;
    font-size: 1.5rem;
    text-align: center;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #333;
}

.form-group input[type="text"],
.form-group input[type="password"] {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
}

.remember-me {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.woocommerce-button {
    width: 100%;
    padding: 0.75rem;
    background: #FF3A5E;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    cursor: pointer;
    transition: background-color 0.3s;
}

.woocommerce-button:hover {
    background: #E02E50;
}

.lost-password {
    text-align: center;
    margin-top: 1rem;
}

.lost-password a {
    color: #0066cc;
    text-decoration: none;
}

.lost-password a:hover {
    text-decoration: underline;
}
</style>

<?php do_action('woocommerce_after_customer_login_form'); ?> 