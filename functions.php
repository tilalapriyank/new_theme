<?php
/**
 * Theme functions and definitions
 */

// Ensure WooCommerce is loaded
if (!class_exists('WooCommerce')) {
    return;
}

// Add WooCommerce support
function hype_pups_add_woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'hype_pups_add_woocommerce_support');

// Include ACF fields
if (file_exists(get_template_directory() . '/inc/acf-fields.php')) {
    require_once get_template_directory() . '/inc/acf-fields.php';
}

// Include tag functionality
if (file_exists(get_template_directory() . '/inc/tags.php')) {
    require_once get_template_directory() . '/inc/tags.php';
}

// Include navigation walkers
if (file_exists(get_template_directory() . '/inc/nav-walkers.php')) {
    require_once get_template_directory() . '/inc/nav-walkers.php';
}

// Add WooCommerce support
function hype_pups_woocommerce_template_path() {
    return 'woocommerce/';
}
add_filter('woocommerce_template_path', 'hype_pups_woocommerce_template_path');

// Include WordPress core functions
require_once(ABSPATH . 'wp-includes/pluggable.php');
require_once(ABSPATH . 'wp-includes/formatting.php');
require_once(ABSPATH . 'wp-includes/link-template.php');

// Include WooCommerce functions
require_once(WC()->plugin_path() . '/includes/wc-template-functions.php');
require_once(WC()->plugin_path() . '/includes/wc-account-functions.php');

add_filter('use_block_editor_for_post_type', '__return_false', 10, 2);

function hype_pups_theme_setup() {
    // Add theme support features
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Register navigation menus
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'hype-pups'),
        'mobile'  => __('Mobile Menu', 'hype-pups'),
        'footer_shop' => __('Footer Shop Menu', 'hype-pups'),
        'footer_company' => __('Footer Company Menu', 'hype-pups'),
        'footer_orders' => __('Footer Orders Menu', 'hype-pups'),
        'footer_bottom' => __('Footer Bottom Menu', 'hype-pups'),
    ));

    // Add customizer settings
    add_action('customize_register', 'hype_pups_customize_register');
}
add_action('after_setup_theme', 'hype_pups_theme_setup');

// Register additional menu locations
function hype_pups_register_menus() {
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'hype-pups'),
        'mobile'  => __('Mobile Menu', 'hype-pups'),
        'footer_shop' => __('Footer Shop Menu', 'hype-pups'),
        'footer_company' => __('Footer Company Menu', 'hype-pups'),
        'footer_orders' => __('Footer Orders Menu', 'hype-pups'),
        'footer_bottom' => __('Footer Bottom Menu', 'hype-pups'),
        'top_bar' => __('Top Bar Menu', 'hype-pups'),
        'account_menu' => __('Account Menu', 'hype-pups'),
    ));
}
add_action('init', 'hype_pups_register_menus');

// Enqueue scripts and styles
function hype_pups_scripts() {
    // Enqueue custom CSS
    wp_enqueue_style('hype-pups-style', get_stylesheet_uri());
    
    // Enqueue custom JS
    wp_enqueue_script('hype-pups-script', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0', true);
    
    // Enqueue shop JS
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_script('hype-pups-shop', get_template_directory_uri() . '/assets/js/shop.js', array('jquery'), '1.0', true);
        wp_localize_script('hype-pups-shop', 'wc_add_to_cart_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'cart_url' => wc_get_cart_url(),
        ));
    }

    // Enqueue product details JS
    if (is_product() || is_shop() || is_product_category()) {
        wp_enqueue_script('hype-pups-product-details', get_template_directory_uri() . '/assets/js/product-details.js', array('jquery'), '1.0', true);
        wp_localize_script('hype-pups-product-details', 'wc_add_to_cart_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'cart_url' => wc_get_cart_url(),
        ));
    }
}
add_action('wp_enqueue_scripts', 'hype_pups_scripts');

function enqueue_swiper_assets() {
    // Swiper CSS
    wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css');
    // Swiper JS
    wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), null, true);
}
add_action('wp_enqueue_scripts', 'enqueue_swiper_assets');

// FIXED: Variable Product AJAX Handlers - ADD THESE TO YOUR FUNCTIONS.PHP

// AJAX handler for finding variation ID based on attributes
add_action('wp_ajax_find_variation_id', 'find_variation_id_ajax');
add_action('wp_ajax_nopriv_find_variation_id', 'find_variation_id_ajax');

function find_variation_id_ajax() {
    if (!isset($_POST['product_id']) || !isset($_POST['attributes'])) {
        wp_send_json_error('Missing required parameters');
    }
    
    $product_id = intval($_POST['product_id']);
    $attributes_input = $_POST['attributes'];
    
    // Handle both JSON string and array input
    if (is_string($attributes_input)) {
        $attributes = json_decode(stripslashes($attributes_input), true);
    } else {
        $attributes = $attributes_input;
    }
    
    if (!$attributes) {
        wp_send_json_error('Invalid attributes format');
    }
    
    $product = wc_get_product($product_id);
    
    if (!$product || !$product->is_type('variable')) {
        wp_send_json_error('Invalid variable product');
    }
    
    // Get available variations
    $available_variations = $product->get_available_variations();
    
    // Find matching variation
    foreach ($available_variations as $variation) {
        $variation_attributes = $variation['attributes'];
        $match = true;
        
        foreach ($attributes as $attribute_name => $attribute_value) {
            // Ensure proper attribute name format
            if (!str_starts_with($attribute_name, 'attribute_')) {
                $variation_attribute_name = 'attribute_' . $attribute_name;
            } else {
                $variation_attribute_name = $attribute_name;
            }
            
            // Check if this variation has the required attribute value
            if (!isset($variation_attributes[$variation_attribute_name])) {
                $match = false;
                break;
            }
            
            $variation_value = $variation_attributes[$variation_attribute_name];
            
            // Handle empty variation attributes (means "any")
            if ($variation_value !== '' && $variation_value !== $attribute_value) {
                $match = false;
                break;
            }
        }
        
        if ($match) {
            wp_send_json_success([
                'variation_id' => $variation['variation_id'],
                'is_purchasable' => $variation['is_purchasable'],
                'is_in_stock' => $variation['is_in_stock'],
                'price_html' => $variation['price_html'],
                'matched_attributes' => $variation_attributes
            ]);
        }
    }
    
    wp_send_json_error('No matching variation found for the selected attributes');
}

// Enhanced AJAX add to cart handler for variable products
add_action('wp_ajax_add_variable_to_cart', 'add_variable_to_cart_ajax');
add_action('wp_ajax_nopriv_add_variable_to_cart', 'add_variable_to_cart_ajax');

function add_variable_to_cart_ajax() {
    if (!isset($_POST['product_id']) || !isset($_POST['variation_id'])) {
        wp_send_json_error('Missing required parameters');
    }
    
    $product_id = intval($_POST['product_id']);
    $variation_id = intval($_POST['variation_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $attributes = isset($_POST['attributes']) ? $_POST['attributes'] : array();
    
    // Validate the variation
    $variation = wc_get_product($variation_id);
    if (!$variation || !$variation->is_purchasable()) {
        wp_send_json_error('Product variation is not available');
    }
    
    // Format attributes for cart
    $variation_data = array();
    foreach ($attributes as $key => $value) {
        $variation_data['attribute_' . $key] = $value;
    }
    
    // Add to cart
    $cart_item_key = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $variation_data);
    
    if ($cart_item_key) {
        // Get updated cart fragments
        WC_AJAX::get_refreshed_fragments();
    } else {
        wp_send_json_error('Failed to add product to cart');
    }
}

// AJAX handler to get product variations
add_action('wp_ajax_get_product_variations', 'get_product_variations_ajax');
add_action('wp_ajax_nopriv_get_product_variations', 'get_product_variations_ajax');

function get_product_variations_ajax() {
    if (!isset($_POST['product_id'])) {
        wp_send_json_error('Missing product ID');
    }
    
    $product_id = intval($_POST['product_id']);
    $product = wc_get_product($product_id);
    
    if (!$product || !$product->is_type('variable')) {
        wp_send_json_error('Invalid variable product');
    }
    
    $variations = $product->get_available_variations();
    
    // Filter out unnecessary data to reduce payload size
    $filtered_variations = array();
    foreach ($variations as $variation) {
        $filtered_variations[] = array(
            'variation_id' => $variation['variation_id'],
            'attributes' => $variation['attributes'],
            'is_purchasable' => $variation['is_purchasable'],
            'is_in_stock' => $variation['is_in_stock'],
            'price_html' => $variation['price_html']
        );
    }
    
    wp_send_json_success($filtered_variations);
}

// Debug function to check variations
function debug_product_variations($product_id) {
    $product = wc_get_product($product_id);
    
    if (!$product || !$product->is_type('variable')) {
        return 'Not a variable product';
    }
    
    $variations = $product->get_available_variations();
    
    echo '<pre>';
    echo "Product ID: $product_id\n";
    echo "Total Variations: " . count($variations) . "\n\n";
    
    foreach ($variations as $variation) {
        echo "Variation ID: " . $variation['variation_id'] . "\n";
        echo "Attributes: " . print_r($variation['attributes'], true) . "\n";
        echo "In Stock: " . ($variation['is_in_stock'] ? 'Yes' : 'No') . "\n";
        echo "Purchasable: " . ($variation['is_purchasable'] ? 'Yes' : 'No') . "\n";
        echo "---\n";
    }
    echo '</pre>';
}

// Shortcode to debug variations (use [debug_variations id="105"])
add_shortcode('debug_variations', function($atts) {
    $atts = shortcode_atts(['id' => 0], $atts);
    if ($atts['id']) {
        ob_start();
        debug_product_variations($atts['id']);
        return ob_get_clean();
    }
    return 'Please provide product ID';
});

// Fix for single product page variations
add_action('wp_footer', 'add_variable_product_scripts');

function add_variable_product_scripts() {
    if (is_product()) {
        global $product;
        if ($product && $product->is_type('variable')) {
            ?>
            <script>
            jQuery(document).ready(function($) {
                // Override the default variation form behavior
                $('form.variations_form').on('woocommerce_variation_has_changed', function() {
                    var $form = $(this);
                    var product_id = $form.find('input[name="product_id"]').val();
                    var $variations = $form.find('select[name^="attribute_"]');
                    var attributes = {};
                    var allSelected = true;
                    
                    $variations.each(function() {
                        var attribute_name = $(this).attr('name');
                        var attribute_value = $(this).val();
                        
                        if (attribute_value === '') {
                            allSelected = false;
                        } else {
                            attributes[attribute_name] = attribute_value;
                        }
                    });
                    
                    if (allSelected) {
                        // Find the variation ID
                        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            action: 'find_variation_id',
                            product_id: product_id,
                            attributes: attributes
                        }, function(response) {
                            if (response.success) {
                                $form.find('input[name="variation_id"]').val(response.data.variation_id);
                                
                                // Update add to cart button
                                var $button = $form.find('.single_add_to_cart_button');
                                if (response.data.is_purchasable && response.data.is_in_stock) {
                                    $button.removeClass('disabled wc-variation-is-unavailable')
                                           .addClass('wc-variation-selection-needed');
                                } else {
                                    $button.addClass('disabled wc-variation-is-unavailable');
                                }
                            }
                        });
                    }
                });
                
                // Enhanced add to cart for variable products
                $('form.variations_form').on('submit', function(e) {
                    var $form = $(this);
                    var $button = $form.find('.single_add_to_cart_button');
                    
                    // Check if it's an AJAX add to cart
                    if ($button.hasClass('ajax_add_to_cart')) {
                        e.preventDefault();
                        
                        var product_id = $form.find('input[name="product_id"]').val();
                        var variation_id = $form.find('input[name="variation_id"]').val();
                        var quantity = $form.find('input[name="quantity"]').val();
                        var attributes = {};
                        
                        $form.find('select[name^="attribute_"]').each(function() {
                            var name = $(this).attr('name').replace('attribute_', '');
                            attributes[name] = $(this).val();
                        });
                        
                        if (!variation_id || variation_id === '0') {
                            alert('Please select all product options');
                            return false;
                        }
                        
                        // Show loading
                        $button.addClass('loading').text('Adding...');
                        
                        $.post('<?php echo admin_url('admin-ajax.php'); ?>', {
                            action: 'add_variable_to_cart',
                            product_id: product_id,
                            variation_id: variation_id,
                            quantity: quantity,
                            attributes: attributes
                        }, function(response) {
                            if (response.success) {
                                // Update cart fragments
                                $(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $button]);
                                
                                // Show success
                                $button.removeClass('loading').addClass('added').text('Added!');
                                
                                setTimeout(function() {
                                    $button.removeClass('added').text('Add to cart');
                                }, 2000);
                            } else {
                                $button.removeClass('loading');
                                alert('Error: ' + (response.data || 'Failed to add to cart'));
                            }
                        }).fail(function() {
                            $button.removeClass('loading');
                            alert('Error adding product to cart');
                        });
                    }
                });
            });
            </script>
            <?php
        }
    }
}

// Register Blog Post Type
function register_blog_post_type() {
    $labels = array(
        'name'               => 'Blog Posts',
        'singular_name'      => 'Blog Post',
        'menu_name'          => 'Blog Posts',
        'add_new'           => 'Add New',
        'add_new_item'      => 'Add New Blog Post',
        'edit_item'         => 'Edit Blog Post',
        'new_item'          => 'New Blog Post',
        'view_item'         => 'View Blog Post',
        'search_items'      => 'Search Blog Posts',
        'not_found'         => 'No blog posts found',
        'not_found_in_trash'=> 'No blog posts found in Trash'
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'has_archive'         => true,
        'publicly_queryable'  => true,
        'query_var'           => true,
        'rewrite'            => array('slug' => 'blog'),
        'capability_type'     => 'post',
        'hierarchical'        => false,
        'supports'            => array('title', 'editor', 'thumbnail', 'excerpt', 'comments'),
        'menu_position'       => 5,
        'menu_icon'          => 'dashicons-admin-post',
        'show_in_rest'       => true
    );

    register_post_type('blog_post', $args);

    // Register Blog Categories Taxonomy
    register_taxonomy('blog_category', 'blog_post', array(
        'label' => 'Blog Categories',
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite' => array('slug' => 'blog-category'),
    ));
}
add_action('init', 'register_blog_post_type');

// Add ACF fields for blog posts
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_blog_fields',
        'title' => 'Blog Post Details',
        'fields' => array(
            array(
                'key' => 'field_author_name',
                'label' => 'Author Name',
                'name' => 'author_name',
                'type' => 'text',
                'required' => 1,
            ),
            array(
                'key' => 'field_author_avatar',
                'label' => 'Author Avatar',
                'name' => 'author_avatar',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'thumbnail',
                'required' => 1,
            ),
            array(
                'key' => 'field_featured_post',
                'label' => 'Featured Post',
                'name' => 'featured_post',
                'type' => 'true_false',
                'ui' => 1,
                'default_value' => 0,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => 'blog_post',
                ),
            ),
        ),
    ));
}

// Add ACF fields for blog page settings
if (function_exists('acf_add_local_field_group')) {
    acf_add_local_field_group(array(
        'key' => 'group_blog_page_settings',
        'title' => 'Blog Page Settings',
        'fields' => array(
            array(
                'key' => 'field_blog_hero_image',
                'label' => 'Hero Background Image',
                'name' => 'blog_hero_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'required' => 1,
            ),
            array(
                'key' => 'field_blog_description',
                'label' => 'Blog Description',
                'name' => 'blog_description',
                'type' => 'textarea',
                'required' => 1,
                'default_value' => 'Insights, stories, and guides from the world of premium dog streetwear. Stay updated with the latest trends, behind-the-scenes content, and community stories.',
            ),
            array(
                'key' => 'field_newsletter_title',
                'label' => 'Newsletter Title',
                'name' => 'newsletter_title',
                'type' => 'text',
                'required' => 1,
                'default_value' => 'Join Our Newsletter',
            ),
            array(
                'key' => 'field_newsletter_description',
                'label' => 'Newsletter Description',
                'name' => 'newsletter_description',
                'type' => 'textarea',
                'required' => 1,
                'default_value' => 'Get the latest articles, style guides, and exclusive offers delivered directly to your inbox. No spam, just the content you want.',
            ),
            array(
                'key' => 'field_newsletter_image',
                'label' => 'Newsletter Image',
                'name' => 'newsletter_image',
                'type' => 'image',
                'return_format' => 'array',
                'preview_size' => 'medium',
                'required' => 1,
            ),
        ),
        'location' => array(
            array(
                array(
                    'param' => 'page_template',
                    'operator' => '==',
                    'value' => 'page-blog.php',
                ),
            ),
        ),
    ));
}

/**
 * Calculate reading time for blog posts
 * @return int Reading time in minutes
 */
function reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Assuming 200 words per minute reading speed
    return max(1, $reading_time); // Return at least 1 minute
}

/**
 * Track post views
 */
function track_post_views() {
    if (is_single()) {
        $post_id = get_the_ID();
        $count = get_post_meta($post_id, 'post_views_count', true);
        if ($count == '') {
            delete_post_meta($post_id, 'post_views_count');
            add_post_meta($post_id, 'post_views_count', 1);
        } else {
            update_post_meta($post_id, 'post_views_count', $count + 1);
        }
    }
}
add_action('wp_head', 'track_post_views');

/**
 * Custom comment callback function
 */
function hype_pups_comment_callback($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    $comment_id = get_comment_ID();
    $comment_author = get_comment_author();
    $comment_date = get_comment_date('F j, Y');
    $comment_time = get_comment_time();
    $comment_content = get_comment_text();
    $comment_avatar = get_avatar($comment, 50);
    $comment_reply_link = get_comment_reply_link(array(
        'reply_text' => 'Reply',
        'depth' => $depth,
        'max_depth' => $args['max_depth'],
        'before' => '<span class="text-[#FF3A5E] hover:underline text-sm font-medium">',
        'after' => '</span>'
    ));
    ?>
    <div id="comment-<?php echo $comment_id; ?>" class="comment">
        <div class="flex gap-4">
            <div class="flex-shrink-0">
                <?php echo $comment_avatar; ?>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    <div>
                        <h4 class="font-medium"><?php echo $comment_author; ?></h4>
                        <p class="text-sm text-gray-500">
                            <?php echo $comment_date; ?> at <?php echo $comment_time; ?>
                        </p>
                    </div>
                    <?php if ($comment_reply_link) : ?>
                        <div>
                            <?php echo $comment_reply_link; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="prose prose-sm max-w-none">
                    <?php echo $comment_content; ?>
                </div>
            </div>
        </div>
    </div>
    <?php
}

/**
 * Comment end callback function
 */
function hype_pups_comment_end_callback($comment, $args, $depth) {
    echo '</div>'; // Close the comment div
}

/**
 * Add comment form fields
 */
function hype_pups_comment_form_fields($fields) {
    $commenter = wp_get_current_commenter();
    $req = get_option('require_name_email');
    $aria_req = ($req ? " aria-required='true'" : '');
    
    $fields['author'] = '<div class="flex flex-col sm:flex-row gap-4 mb-4">
        <input 
            type="text" 
            name="author" 
            placeholder="Name' . ($req ? ' *' : '') . '" 
            value="' . esc_attr($commenter['comment_author']) . '" 
            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#FF3A5E]"
            ' . $aria_req . '
        >';
    
    $fields['email'] = '<input 
        type="email" 
        name="email" 
        placeholder="Email' . ($req ? ' *' : '') . '" 
        value="' . esc_attr($commenter['comment_author_email']) . '" 
        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#FF3A5E]"
        ' . $aria_req . '
    >
    </div>';
    
    $fields['url'] = '<input 
        type="url" 
        name="url" 
        placeholder="Website" 
        value="' . esc_attr($commenter['comment_author_url']) . '" 
        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-[#FF3A5E] mb-4"
    >';
    
    return $fields;
}
add_filter('comment_form_default_fields', 'hype_pups_comment_form_fields');

/**
 * Add comment form comment field
 */
function hype_pups_comment_form_comment_field($comment_field) {
    $comment_field = '<div class="mb-4">
        <textarea 
            name="comment" 
            placeholder="Join the discussion..." 
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-[#FF3A5E]"
            rows="4"
            required
        ></textarea>
    </div>';
    return $comment_field;
}
add_filter('comment_form_field_comment', 'hype_pups_comment_form_comment_field');

/**
 * Add comment form submit button
 */
function hype_pups_comment_form_submit_button($submit_button) {
    return '<button type="submit" class="bg-[#FF3A5E] hover:bg-[#E02E50] text-white font-medium py-2 px-6 rounded-lg transition-colors">Post Comment</button>';
}
add_filter('comment_form_submit_button', 'hype_pups_comment_form_submit_button');

/**
 * Add comment form cookie consent
 */
function hype_pups_comment_form_cookie_consent($fields) {
    $fields['cookies'] = '<div class="flex items-center mb-4">
        <input type="checkbox" id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" value="yes" class="mr-2">
        <label for="wp-comment-cookies-consent" class="text-sm text-gray-600">Save my name and email for the next time I comment</label>
    </div>';
    return $fields;
}
add_filter('comment_form_default_fields', 'hype_pups_comment_form_cookie_consent');

/**
 * Handle comment submission via AJAX
 */
function hype_pups_handle_comment_submission() {
    check_ajax_referer('comment_nonce', 'nonce');
    
    $comment_data = array(
        'comment_post_ID' => intval($_POST['post_id']),
        'comment_author' => sanitize_text_field($_POST['author']),
        'comment_author_email' => sanitize_email($_POST['email']),
        'comment_author_url' => esc_url_raw($_POST['url']),
        'comment_content' => wp_kses_post($_POST['comment']),
        'comment_type' => 'comment',
        'comment_parent' => 0,
        'user_id' => get_current_user_id(),
        'comment_approved' => 1
    );
    
    $comment_id = wp_insert_comment($comment_data);
    
    if ($comment_id) {
        wp_send_json_success(array(
            'message' => 'Comment posted successfully!',
            'comment_id' => $comment_id
        ));
    } else {
        wp_send_json_error(array(
            'message' => 'Failed to post comment. Please try again.'
        ));
    }
}
add_action('wp_ajax_submit_comment', 'hype_pups_handle_comment_submission');
add_action('wp_ajax_nopriv_submit_comment', 'hype_pups_handle_comment_submission');

function hype_pups_enqueue_product_tabs_js() {
    if (is_product()) {
        wp_enqueue_script('hype-pups-product-tabs', get_template_directory_uri() . '/assets/js/product-tabs.js', array(), '1.0', true);
    }
}
add_action('wp_enqueue_scripts', 'hype_pups_enqueue_product_tabs_js');

// Add support for ACF
function hype_pups_acf_init() {
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page(array(
            'page_title' => 'Theme Settings',
            'menu_title' => 'Theme Settings',
            'menu_slug' => 'theme-settings',
            'capability' => 'edit_posts',
            'redirect' => false
        ));
    }
}
add_action('acf/init', 'hype_pups_acf_init');

// Add ACF fields support
function hype_pups_acf_fields() {
    if (function_exists('acf_add_local_field_group')) {
        // Fields are defined in inc/acf-fields.php
    }
}
add_action('acf/init', 'hype_pups_acf_fields');

// // Save shipping address to user meta when Step 1 is completed
// add_action('wp_ajax_hype_pups_save_`checkout`_step', function() {
//     $step = isset($_POST['step']) ? sanitize_text_field($_POST['step']) : '';
//     $fields = isset($_POST['fields']) ? $_POST['fields'] : array();
//     if (!is_array($fields)) {
//         parse_str($fields, $fields);
//     }
//     if ($step === 'shipping' && is_user_logged_in()) {
//         $user_id = get_current_user_id();
//         $shipping_fields = [
//             'shipping_first_name', 'shipping_last_name', 'shipping_address_1', 'shipping_address_2',
//             'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_country',
//             'billing_email', 'billing_phone'
//         ];
//         foreach ($shipping_fields as $field) {
//             if (isset($fields[$field])) {
//                 update_user_meta($user_id, $field, sanitize_text_field($fields[$field]));
//             }
//         }
//     }
// }, 1);

// Prefill checkout fields from user meta
add_filter('woocommerce_checkout_get_value', function($value, $input) {
    if (is_user_logged_in() && empty($value)) {
        $user_id = get_current_user_id();
        $meta = get_user_meta($user_id, $input, true);
        if (!empty($meta)) {
            return $meta;
        }
    }
    return $value;
}, 10, 2);

// Add custom meta box for product key features
function hype_pups_add_product_key_features_meta_box() {
    add_meta_box(
        'product_key_features',
        'Key Features',
        'hype_pups_product_key_features_callback',
        'product',
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'hype_pups_add_product_key_features_meta_box');

// Callback function to display the meta box
function hype_pups_product_key_features_callback($post) {
    $features = get_post_meta($post->ID, '_product_key_features', true);
    if (!is_array($features)) {
        $features = array();
    }
    ?>
    <div class="key-features-container">
        <div id="key-features-list">
            <?php foreach ($features as $index => $feature) : ?>
                <div class="key-feature-item">
                    <input type="text" name="product_key_features[]" value="<?php echo esc_attr($feature); ?>" class="widefat">
                    <button type="button" class="button remove-feature" style="color: #dc2626;">Remove</button>
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" class="button add-feature" style="margin-top: 10px;">Add Feature</button>
    </div>
    <style>
        .key-feature-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        .key-feature-item input {
            flex: 1;
        }
        .remove-feature {
            color: #dc2626;
        }
    </style>
    <script>
    jQuery(document).ready(function($) {
        // Add new feature
        $('.add-feature').on('click', function() {
            var newFeature = `
                <div class="key-feature-item">
                    <input type="text" name="product_key_features[]" value="" class="widefat">
                    <button type="button" class="button remove-feature" style="color: #dc2626;">Remove</button>
                </div>
            `;
            $('#key-features-list').append(newFeature);
        });

        // Remove feature
        $(document).on('click', '.remove-feature', function() {
            $(this).closest('.key-feature-item').remove();
        });
    });
    </script>
    <?php
}

// Save the meta box data
function hype_pups_save_product_key_features($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;
    
    if (isset($_POST['product_key_features'])) {
        $features = array_map('sanitize_text_field', $_POST['product_key_features']);
        $features = array_filter($features); // Remove empty values
        update_post_meta($post_id, '_product_key_features', $features);
    }
}
add_action('save_post_product', 'hype_pups_save_product_key_features');

add_action('wp_ajax_save_account_address', function() {
    if (!is_user_logged_in()) {
        wp_send_json_error('Not logged in');
    }
    $user_id = get_current_user_id();
    $type = $_POST['address_type'] ?? 'shipping';
    $fields = [
        'first_name', 'last_name', 'address_1', 'address_2', 'city', 'state', 'postcode', 'country'
    ];
    $meta_prefix = $type . '_';
    foreach ($fields as $field) {
        update_user_meta($user_id, $meta_prefix . $field, sanitize_text_field($_POST[$field] ?? ''));
    }
    wp_send_json_success('Address saved');
});

add_action('woocommerce_save_account_details', function($user_id) {
    if (isset($_POST['account_phone'])) {
        update_user_meta($user_id, 'billing_phone', sanitize_text_field($_POST['account_phone']));
    }
});

// Add AJAX handler for getting cart contents
add_action('wp_ajax_get_cart_contents', 'hype_pups_get_cart_contents');
add_action('wp_ajax_nopriv_get_cart_contents', 'hype_pups_get_cart_contents');

function hype_pups_get_cart_contents() {
    $cart_items = array();
    
    foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
        $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
        
        if ($_product && $_product->exists() && $cart_item['quantity'] > 0) {
            $cart_items[] = array(
                'product_id' => $_product->get_id(),
                'name' => $_product->get_name(),
                'price' => $_product->get_price(),
                'quantity' => $cart_item['quantity'],
                'image' => wp_get_attachment_image_url($_product->get_image_id(), 'thumbnail'),
                'url' => get_permalink($_product->get_id()),
                'size' => isset($cart_item['variation']['attribute_pa_size']) ? $cart_item['variation']['attribute_pa_size'] : '',
                'color' => isset($cart_item['variation']['attribute_pa_color']) ? $cart_item['variation']['attribute_pa_color'] : ''
            );
        }
    }
    
    wp_send_json_success($cart_items);
}

// Enqueue WooCommerce scripts and styles
function hype_pups_woocommerce_scripts() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        // Only enqueue custom AJAX for shop/archive pages
        wp_enqueue_script('hype-pups-ajax-add-to-cart', get_template_directory_uri() . '/assets/js/ajax-add-to-cart.js', array('jquery'), '1.0', true);
        wp_localize_script('hype-pups-ajax-add-to-cart', 'hype_pups_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('hype-pups-ajax-nonce')
        ));
    }
    // DO NOT enqueue custom AJAX on single product page!
}
add_action('wp_enqueue_scripts', 'hype_pups_woocommerce_scripts');

function hype_pups_enqueue_checkout_assets() {
    if (is_checkout()) {
        wp_enqueue_style('tailwindcss', 'https://cdn.tailwindcss.com');
        wp_enqueue_style('montserrat-font', 'https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap', false);
    }
}
add_action('wp_enqueue_scripts', 'hype_pups_enqueue_checkout_assets');

// Register Custom Product Widget
class HypePups_Product_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'hypepups_product_widget',
            __('HypePups Product Widget', 'hype-pups'),
            array('description' => __('Displays a grid of WooCommerce products with theme styling.', 'hype-pups'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        include get_template_directory() . '/woocommerce/widgets/product-widget.php';
        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : __('Products', 'woocommerce');
        $count = isset($instance['count']) ? (int)$instance['count'] : 6;
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of products to show:'); ?></label>
            <input class="tiny-text" id="<?php echo $this->get_field_id('count'); ?>" name="<?php echo $this->get_field_name('count'); ?>" type="number" step="1" min="1" value="<?php echo esc_attr($count); ?>" size="3" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['count'] = (!empty($new_instance['count'])) ? (int)$new_instance['count'] : 6;
        return $instance;
    }
}

function hypepups_register_product_widget() {
    register_widget('HypePups_Product_Widget');
}
add_action('widgets_init', 'hypepups_register_product_widget');

// Add theme styling to WooCommerce loop add-to-cart button
add_filter('woocommerce_loop_add_to_cart_link', function($button, $product) {
    if ($product && $product->is_type('simple')) {
        $button = sprintf(
            '<a href="%s" data-quantity="1" data-product_id="%s" data-product_sku="%s" class="button product_type_simple add_to_cart_button ajax_add_to_cart bg-[#FF3A5E] text-white hover:bg-[#FF3A5E]/90 w-full py-2 px-4 rounded-full text-sm font-medium flex items-center justify-center gap-2" rel="nofollow">'
            . '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" /></svg>'
            . '%s'
            . '</a>',
            esc_url($product->add_to_cart_url()),
            esc_attr($product->get_id()),
            esc_attr($product->get_sku()),
            esc_html($product->add_to_cart_text())
        );
    }
    return $button;
}, 10, 2);

// Force enqueue WooCommerce add-to-cart script on shop/archive pages
add_action('wp_enqueue_scripts', function() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        wp_enqueue_script('wc-add-to-cart');
        wp_enqueue_script('wc-cart-fragments');
    }
});


// Add to your theme's functions.php - REMOVE after debugging
add_action('wp_ajax_woocommerce_add_to_cart_variable_product', 'debug_ajax_add_to_cart');
add_action('wp_ajax_nopriv_woocommerce_add_to_cart_variable_product', 'debug_ajax_add_to_cart');

function debug_ajax_add_to_cart() {
    error_log('AJAX Add to Cart Debug:');
    error_log('POST data: ' . print_r($_POST, true));
    
    if (isset($_POST['product_id'])) {
        $product = wc_get_product($_POST['product_id']);
        error_log('Product type: ' . $product->get_type());
        error_log('Available variations: ' . print_r($product->get_available_variations(), true));
    }
}

// Ensure WooCommerce variation scripts are loaded on single product pages
function load_wc_variation_scripts() {
    if (is_product()) {
        wp_enqueue_script('wc-add-to-cart-variation');
    }
}
add_action('wp_enqueue_scripts', 'load_wc_variation_scripts');


// Add this to your functions.php file:

// Hook for logged in users
add_action('wp_ajax_ppc_simulate_cart', 'handle_ppc_simulate_cart');
// Hook for non-logged in users
add_action('wp_ajax_nopriv_ppc_simulate_cart', 'handle_ppc_simulate_cart');

// Also register the wc-ajax endpoint
add_action('wc_ajax_ppc-simulate-cart', 'handle_ppc_simulate_cart');

function handle_ppc_simulate_cart() {
    // Verify nonce for security
    if (!wp_verify_nonce($_POST['woocommerce-cart-nonce'], 'woocommerce-cart')) {
        wp_die('Security check failed');
    }
    
    try {
        $product_id = absint($_POST['add-to-cart']);
        $quantity = isset($_POST['quantity']) ? wc_stock_amount($_POST['quantity']) : 1;
        $variation_id = isset($_POST['variation_id']) ? absint($_POST['variation_id']) : 0;
        $variations = array();
        
        // Handle variation attributes
        if ($variation_id) {
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'attribute_') === 0) {
                    $variations[sanitize_title($key)] = wc_clean($value);
                }
            }
        }
        
        // Add product to cart
        $cart_item_key = WC()->cart->add_to_cart(
            $product_id,
            $quantity,
            $variation_id,
            $variations
        );
        
        if ($cart_item_key) {
            // Success response
            wp_send_json_success(array(
                'message' => 'Product added to cart successfully!',
                'cart_count' => WC()->cart->get_cart_contents_count(),
                'cart_total' => WC()->cart->get_cart_total(),
                'cart_hash' => WC()->cart->get_cart_hash(),
                'fragments' => apply_filters('woocommerce_add_to_cart_fragments', array())
            ));
        } else {
            // Failed to add to cart
            wp_send_json_error(array(
                'message' => 'Failed to add product to cart. Please try again.'
            ));
        }
        
    } catch (Exception $e) {
        wp_send_json_error(array(
            'message' => 'An error occurred: ' . $e->getMessage()
        ));
    }
}

// Optional: Add cart fragments update for dynamic cart updates
add_filter('woocommerce_add_to_cart_fragments', 'update_cart_fragments');

function update_cart_fragments($fragments) {
    // Update cart count
    $fragments['.cart-contents-count'] = '<span class="cart-contents-count">' . WC()->cart->get_cart_contents_count() . '</span>';
    
    // Update cart total
    $fragments['.cart-total'] = '<span class="cart-total">' . WC()->cart->get_cart_total() . '</span>';
    
    return $fragments;
}


// REPLACE the existing ppc_simulate_cart handler in your functions.php with this corrected version:

// Remove the duplicate/conflicting handlers first
remove_action('wp_ajax_ppc_simulate_cart', 'handle_ppc_simulate_cart');
remove_action('wp_ajax_nopriv_ppc_simulate_cart', 'handle_ppc_simulate_cart');
remove_action('wc_ajax_ppc-simulate-cart', 'handle_ppc_simulate_cart');

// Register the corrected AJAX endpoint handlers
add_action('init', 'register_ppc_simulate_cart_handlers');

function register_ppc_simulate_cart_handlers() {
    // Standard WordPress AJAX endpoints
    add_action('wp_ajax_ppc_simulate_cart', 'handle_ppc_simulate_cart_corrected');
    add_action('wp_ajax_nopriv_ppc_simulate_cart', 'handle_ppc_simulate_cart_corrected');
    
    // WooCommerce AJAX endpoint
    add_action('wc_ajax_ppc-simulate-cart', 'handle_ppc_simulate_cart_corrected');
}

function handle_ppc_simulate_cart_corrected() {
    // Set proper headers to prevent redirects
    if (!headers_sent()) {
        header('Content-Type: application/json; charset=utf-8');
        status_header(200);
    }
    
    // Prevent caching
    nocache_headers();
    
    // Check if WooCommerce is available
    if (!class_exists('WooCommerce') || !function_exists('WC')) {
        wp_send_json_error(array(
            'message' => 'WooCommerce is not available.'
        ));
        wp_die();
    }
    
    // Initialize WooCommerce cart if needed
    if (WC()->cart === null) {
        WC()->frontend_includes();
        if (WC()->session === null) {
            WC()->session = new WC_Session_Handler();
            WC()->session->init();
        }
        WC()->cart = new WC_Cart();
        if (WC()->customer === null) {
            WC()->customer = new WC_Customer(get_current_user_id(), true);
        }
    }
    
    // Verify nonce for security
    if (!isset($_POST['woocommerce-cart-nonce']) || !wp_verify_nonce($_POST['woocommerce-cart-nonce'], 'woocommerce-cart')) {
        wp_send_json_error(array(
            'message' => 'Security verification failed. Please refresh the page and try again.'
        ));
        wp_die();
    }
    
    try {
        // Get product data with better validation
        $product_id = 0;
        $quantity = 1;
        $variation_id = 0;
        $variations = array();
        
        // Get product ID from multiple possible sources
        if (isset($_POST['add-to-cart']) && !empty($_POST['add-to-cart'])) {
            $product_id = absint($_POST['add-to-cart']);
        } elseif (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
            $product_id = absint($_POST['product_id']);
        }
        
        // Validate product ID
        if (!$product_id) {
            wp_send_json_error(array(
                'message' => 'Product ID is required.'
            ));
            wp_die();
        }
        
        // Get and validate product
        $product = wc_get_product($product_id);
        if (!$product || !$product->exists()) {
            wp_send_json_error(array(
                'message' => 'Product not found.'
            ));
            wp_die();
        }
        
        // Get quantity
        if (isset($_POST['quantity']) && is_numeric($_POST['quantity'])) {
            $quantity = wc_stock_amount($_POST['quantity']);
        }
        
        // Handle variable products
        if ($product->is_type('variable')) {
            // Get variation ID
            if (isset($_POST['variation_id']) && !empty($_POST['variation_id'])) {
                $variation_id = absint($_POST['variation_id']);
            }
            
            // Get variation attributes
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'attribute_') === 0) {
                    $variations[sanitize_title($key)] = wc_clean($value);
                }
            }
            
            // Validate that we have a variation ID for variable products
            if (!$variation_id && !empty($variations)) {
                // Try to find matching variation
                $data_store = WC_Data_Store::load('product');
                $variation_id = $data_store->find_matching_product_variation($product, $variations);
            }
            
            if (!$variation_id) {
                wp_send_json_error(array(
                    'message' => 'Please select all product options.'
                ));
                wp_die();
            }
            
            // Validate the variation exists and is available
            $variation_product = wc_get_product($variation_id);
            if (!$variation_product || !$variation_product->is_purchasable()) {
                wp_send_json_error(array(
                    'message' => 'Selected product variation is not available.'
                ));
                wp_die();
            }
        }
        
        // Check if product is purchasable
        if (!$product->is_purchasable()) {
            wp_send_json_error(array(
                'message' => 'This product cannot be purchased.'
            ));
            wp_die();
        }
        
        // Check stock
        if (!$product->is_in_stock()) {
            wp_send_json_error(array(
                'message' => 'Sorry, this product is out of stock.'
            ));
            wp_die();
        }
        
        // Clear any existing cart notices
        wc_clear_notices();
        
        // Add product to cart
        $cart_item_key = WC()->cart->add_to_cart(
            $product_id,
            $quantity,
            $variation_id,
            $variations
        );
        
        if ($cart_item_key) {
            // Get updated cart data
            $cart_count = WC()->cart->get_cart_contents_count();
            $cart_total = WC()->cart->get_cart_total();
            $cart_hash = WC()->cart->get_cart_hash();
            
            // Get cart fragments for updating displays
            $fragments = apply_filters('woocommerce_add_to_cart_fragments', array(
                '.cart-contents-count' => '<span class="cart-contents-count">' . esc_html($cart_count) . '</span>',
                '.cart-total' => '<span class="cart-total">' . $cart_total . '</span>',
            ));
            
            // Success response
            wp_send_json_success(array(
                'message' => sprintf('%s has been added to your cart.', $product->get_name()),
                'cart_count' => $cart_count,
                'cart_total' => $cart_total,
                'cart_hash' => $cart_hash,
                'fragments' => $fragments,
                'cart_item_key' => $cart_item_key,
                'redirect_url' => false // Prevent any redirects
            ));
        } else {
            // Check for WooCommerce notices/errors
            $notices = wc_get_notices('error');
            $error_message = 'Failed to add product to cart.';
            
            if (!empty($notices)) {
                $error_message = strip_tags($notices[0]['notice']);
                wc_clear_notices();
            }
            
            wp_send_json_error(array(
                'message' => $error_message
            ));
        }
        
    } catch (Exception $e) {
        wp_send_json_error(array(
            'message' => 'An error occurred: ' . $e->getMessage()
        ));
    }
    
    // Ensure we exit properly
    wp_die();
}

// Enhanced cart fragments update
add_filter('woocommerce_add_to_cart_fragments', 'update_cart_fragments_enhanced', 10, 1);

function update_cart_fragments_enhanced($fragments) {
    if (!WC()->cart) {
        return $fragments;
    }
    
    $cart_count = WC()->cart->get_cart_contents_count();
    $cart_total = WC()->cart->get_cart_total();
    
    // Update multiple possible cart count selectors
    $fragments['.cart-contents-count'] = '<span class="cart-contents-count">' . esc_html($cart_count) . '</span>';
    $fragments['.cart-count'] = '<span class="cart-count">' . esc_html($cart_count) . '</span>';
    $fragments['span.cart-count'] = '<span class="cart-count">' . esc_html($cart_count) . '</span>';
    
    // Update cart total
    $fragments['.cart-total'] = '<span class="cart-total">' . $cart_total . '</span>';
    
    // Update mini cart if it exists
    if (function_exists('woocommerce_mini_cart')) {
        ob_start();
        woocommerce_mini_cart();
        $mini_cart = ob_get_clean();
        $fragments['.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>';
    }
    
    return $fragments;
}

// Debug function to check if endpoint is working (remove in production)
add_action('wp_footer', 'debug_ppc_endpoint');

function debug_ppc_endpoint() {
    if (is_product() && current_user_can('manage_options')) {
        ?>
        <script type="text/javascript">
        console.log('PPC Debug Info:', {
            'WC AJAX URL': '<?php echo WC_AJAX::get_endpoint('ppc-simulate-cart'); ?>',
            'Admin AJAX URL': '<?php echo admin_url('admin-ajax.php'); ?>',
            'Current URL': window.location.href,
            'WC Available': typeof wc_add_to_cart_params !== 'undefined'
        });
        </script>
        <?php
    }
}

debug_ppc_endpoint();

add_action('wp_enqueue_scripts', function() {
    if (is_product()) {
        wp_localize_script('jquery', 'wc_cart_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
            'nonce' => wp_create_nonce('woocommerce-cart'),
            'cart_nonce' => wp_create_nonce('woocommerce-cart')
        ));
    }
});


// $nonce_actions_to_try = [
//     'woocommerce-cart',
//     'woocommerce-add-to-cart',
//     'add-to-cart',
//     'woocommerce_add_to_cart_nonce',
//     'woocommerce-process_checkout'
// ];

// foreach ($nonce_actions_to_try as $action) {
//     $is_valid = wp_verify_nonce($_POST['woocommerce-cart-nonce'], $action);
//     echo "Nonce action '$action': " . ($is_valid ? 'valid' : 'invalid');
// }


// echo 'Current server time: ' . current_time('mysql');
// echo 'WordPress timezone: ' . get_option('timezone_string');

// Multi-step checkout AJAX handlers
add_action('wp_ajax_hype_pups_save_checkout_step', 'hype_pups_save_checkout_step');
add_action('wp_ajax_nopriv_hype_pups_save_checkout_step', 'hype_pups_save_checkout_step');
// function hype_pups_save_checkout_step() {
//     $step = isset($_POST['step']) ? sanitize_text_field($_POST['step']) : '';
//     $fields = isset($_POST['fields']) ? $_POST['fields'] : array();
//     if (!is_array($fields)) {
//         parse_str($fields, $fields);
//     }
//     // Validate and save step data in WooCommerce session
//     if ($step === 'shipping' && is_user_logged_in()) {
//         $user_id = get_current_user_id();
//         $shipping_fields = [
//             'shipping_first_name', 'shipping_last_name', 'shipping_address_1', 'shipping_address_2',
//             'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_country',
//             'billing_email', 'billing_phone'
//         ];
//         foreach ($shipping_fields as $field) {
//             if (isset($fields[$field])) {
//                 update_user_meta($user_id, $field, sanitize_text_field($fields[$field]));
//             }
//         }
//     }
//     // ... existing code ...
// }

// function hype_pups_enqueue_multistep_checkout_js() {
//     if (is_checkout()) {
//         wp_enqueue_script('hype-pups-multistep-checkout', get_template_directory_uri() . '/assets/js/multistep-checkout.js', array('jquery'), '1.0', true);
//         wp_localize_script('hype-pups-multistep-checkout', 'hypePupsCheckout', array(
//             'ajax_url' => admin_url('admin-ajax.php'),
//             'nonce' => wp_create_nonce('hype-pups-checkout-nonce')
//         ));
//     }
// }
add_action('wp_enqueue_scripts', 'hype_pups_enqueue_multistep_checkout_js');

// Prefill checkout fields from user meta
add_filter('woocommerce_checkout_get_value', function($value, $input) {
    if (is_user_logged_in() && empty($value)) {
        $user_id = get_current_user_id();
        $meta = get_user_meta($user_id, $input, true);
        if (!empty($meta)) {
            return $meta;
        }
    }
    return $value;
}, 10, 2);

// Remove default YITH positioning (optional)
function remove_yith_wishlist_default_position() {
    if (function_exists('YITH_WCWL')) {
        remove_action('woocommerce_single_product_summary', array(YITH_WCWL_Frontend(), 'print_button'), 31);
        remove_action('woocommerce_after_shop_loop_item', array(YITH_WCWL_Frontend(), 'print_button'), 15);
    }
}
add_action('init', 'remove_yith_wishlist_default_position');

add_action('wp_ajax_hype_pups_save_checkout_step', 'hype_pups_save_checkout_step');
add_action('wp_ajax_nopriv_hype_pups_save_checkout_step', 'hype_pups_save_checkout_step');

// function hype_pups_save_checkout_step() {
//     // Verify nonce
//     if (!wp_verify_nonce($_POST['nonce'], 'hype-pups-checkout-nonce')) {
//         wp_send_json_error(array('message' => 'Security check failed.'));
//     }

//     $step = isset($_POST['step']) ? sanitize_text_field($_POST['step']) : '';
//     $fields = isset($_POST['fields']) ? $_POST['fields'] : array();
    
//     if (!is_array($fields)) {
//         parse_str($fields, $fields);
//     }

//     // Initialize WooCommerce session if needed
//     if (!WC()->session->has_session()) {
//         WC()->session->set_customer_session_cookie(true);
//     }

//     $errors = array();

//     if ($step === 'shipping') {
//         // Validate required fields
//         $required_fields = array(
//             'billing_first_name' => 'First name',
//             'billing_last_name' => 'Last name',
//             'billing_email' => 'Email',
//             'billing_phone' => 'Phone',
//             'billing_address_1' => 'Address',
//             'billing_city' => 'City',
//             'billing_state' => 'State',
//             'billing_postcode' => 'Zip code',
//             'billing_country' => 'Country'
//         );

//         foreach ($required_fields as $field => $label) {
//             if (empty($fields[$field])) {
//                 $errors[] = $label . ' is required.';
//             }
//         }

//         // Validate email format
//         if (!empty($fields['billing_email']) && !is_email($fields['billing_email'])) {
//             $errors[] = 'Please enter a valid email address.';
//         }

//         if (empty($errors)) {
//             // Save to session
//             foreach ($fields as $key => $value) {
//                 WC()->session->set($key, sanitize_text_field($value));
//             }

//             // Calculate shipping if address changed
//             WC()->cart->calculate_shipping();
//             WC()->cart->calculate_totals();

//             wp_send_json_success(array('message' => 'Shipping information saved.'));
//         } else {
//             wp_send_json_error(array('message' => implode(' ', $errors)));
//         }

//     } elseif ($step === 'payment') {
//         // Validate payment method
//         if (empty($fields['payment_method'])) {
//             $errors[] = 'Please select a payment method.';
//         } else {
//             $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
//             if (!isset($available_gateways[$fields['payment_method']])) {
//                 $errors[] = 'Invalid payment method selected.';
//             }
//         }

//         if (empty($errors)) {
//             // Save payment method to session
//             WC()->session->set('chosen_payment_method', sanitize_text_field($fields['payment_method']));
            
//             // Save other payment fields if needed
//             foreach ($fields as $key => $value) {
//                 if (strpos($key, 'payment_') === 0) {
//                     WC()->session->set($key, sanitize_text_field($value));
//                 }
//             }

//             wp_send_json_success(array('message' => 'Payment method saved.'));
//         } else {
//             wp_send_json_error(array('message' => implode(' ', $errors)));
//         }
//     }

//     wp_send_json_error(array('message' => 'Invalid step.'));
// }

// Enhanced multistep checkout script enqueue
// function hype_pups_enqueue_multistep_checkout_js() {
//     if (is_checkout()) {
//         wp_enqueue_script('jquery');
//         wp_enqueue_script('wc-checkout');
//         wp_enqueue_script('wc-country-select');
//         wp_enqueue_script('wc-address-i18n');
        
//         wp_enqueue_script('hype-pups-multistep-checkout', get_template_directory_uri() . '/assets/js/multistep-checkout.js', array('jquery', 'wc-checkout'), '1.0', true);
        
//         wp_localize_script('hype-pups-multistep-checkout', 'hypePupsCheckout', array(
//             'ajax_url' => admin_url('admin-ajax.php'),
//             'nonce' => wp_create_nonce('hype-pups-checkout-nonce'),
//             'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
//         ));
//     }
// }
// add_action('wp_enqueue_scripts', 'hype_pups_enqueue_multistep_checkout_js');

// Pre-populate checkout fields from session data
add_filter('woocommerce_checkout_get_value', 'hype_pups_checkout_get_value', 10, 2);
// function hype_pups_checkout_get_value($value, $input) {
//     // Check session first
//     if (WC()->session && WC()->session->get($input)) {
//         return WC()->session->get($input);
//     }
    
//     // Then check user meta if logged in
//     if (is_user_logged_in() && empty($value)) {
//         $user_id = get_current_user_id();
//         $meta = get_user_meta($user_id, $input, true);
//         if (!empty($meta)) {
//             return $meta;
//         }
//     }
    
//     return $value;
// }

// Handle shipping address toggle
add_action('wp_footer', 'hype_pups_shipping_address_toggle');

// AJAX handler for updating checkout
add_action('wp_ajax_hype_pups_update_checkout', 'hype_pups_update_checkout');
add_action('wp_ajax_nopriv_hype_pups_update_checkout', 'hype_pups_update_checkout');

function hype_pups_update_checkout() {
    if (!wp_verify_nonce($_POST['nonce'], 'hype-pups-checkout-nonce')) {
        wp_send_json_error(array('message' => 'Security check failed.'));
    }

    $fields = isset($_POST['fields']) ? $_POST['fields'] : array();
    
    if (!is_array($fields)) {
        parse_str($fields, $fields);
    }

    // Update customer data
    $customer = WC()->customer;
    
    foreach ($fields as $key => $value) {
        $method = 'set_' . $key;
        if (method_exists($customer, $method)) {
            $customer->$method(sanitize_text_field($value));
        }
    }

    // Calculate totals
    WC()->cart->calculate_shipping();
    WC()->cart->calculate_totals();

    // Return updated fragments
    ob_start();
    woocommerce_order_review();
    $order_review = ob_get_clean();

    wp_send_json_success(array(
        'order_review' => $order_review,
        'fragments' => apply_filters('woocommerce_update_order_review_fragments', array())
    ));
}

// Handle order completion

// Add checkout body class
function hype_pups_checkout_body_class($classes) {
    if (is_checkout()) {
        $classes[] = 'hype-pups-checkout';
    }
    return $classes;
}
add_filter('body_class', 'hype_pups_checkout_body_class');

// Fix WooCommerce payment method display

// Add these additional functions to your functions.php

// Ensure WooCommerce session is initialized


// Handle PayPal integration


// Fix checkout field validation


// Add AJAX endpoint for checkout update
add_action('wc_ajax_update_checkout_step', 'hype_pups_wc_ajax_update_checkout');
function hype_pups_wc_ajax_update_checkout() {
    if (!wp_verify_nonce($_POST['nonce'], 'hype-pups-checkout-nonce')) {
        wp_send_json_error(array('message' => 'Security check failed.'));
    }
    
    $step = sanitize_text_field($_POST['step']);
    $fields = $_POST['fields'];
    
    if (!is_array($fields)) {
        parse_str($fields, $fields);
    }
    
    // Update checkout data
    foreach ($fields as $key => $value) {
        WC()->session->set($key, sanitize_text_field($value));
    }
    
    // Recalculate totals
    WC()->cart->calculate_shipping();
    WC()->cart->calculate_totals();
    
    // Get updated fragments
    ob_start();
    woocommerce_order_review();
    $order_review = ob_get_clean();
    
    wp_send_json_success(array(
        'order_review' => $order_review,
        'step' => $step
    ));
}

// Ensure checkout scripts are loaded


// Handle shipping method selection

// Fix checkout redirect issues
add_filter('woocommerce_checkout_redirect_empty_cart', '__return_false');
add_filter('woocommerce_checkout_update_order_review_expired', '__return_false');


// Enhanced payment gateway handling


// Add debug logging for checkout issues
add_action('woocommerce_checkout_process', 'hype_pups_debug_checkout_process');
function hype_pups_debug_checkout_process() {
    if (!defined('WP_DEBUG') || !WP_DEBUG) return;
    
    error_log('Checkout Process Debug:');
    error_log('POST Data: ' . print_r($_POST, true));
    error_log('Session Data: ' . print_r(WC()->session->get_session_data(), true));
    error_log('Cart Contents: ' . print_r(WC()->cart->get_cart_contents(), true));
}

function hype_pups_enqueue_multistep_checkout_js() {
    if (is_checkout()) {
        wp_enqueue_script('jquery');
        wp_enqueue_script('wc-checkout');
        wp_enqueue_script('wc-country-select');
        wp_enqueue_script('wc-address-i18n');
        wp_enqueue_script('wc-cart-fragments');
        
        wp_enqueue_script('hype-pups-multistep-checkout', get_template_directory_uri() . '/assets/js/multistep-checkout.js', array('jquery', 'wc-checkout'), '1.0', true);
        
        // Localize script with all necessary data
        wp_localize_script('hype-pups-multistep-checkout', 'hypePupsCheckout', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('hype-pups-checkout-nonce'),
            'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
            'update_order_review_nonce' => wp_create_nonce('update-order-review'),
            'checkout_url' => wc_get_checkout_url(),
            'cart_url' => wc_get_cart_url(),
            'is_user_logged_in' => is_user_logged_in() ? 1 : 0
        ));
    }
}
add_action('wp_enqueue_scripts', 'hype_pups_enqueue_multistep_checkout_js');

// Save checkout step data
add_action('wp_ajax_hype_pups_save_checkout_step', 'hype_pups_save_checkout_step');
add_action('wp_ajax_nopriv_hype_pups_save_checkout_step', 'hype_pups_save_checkout_step');

function hype_pups_save_checkout_step() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'hype-pups-checkout-nonce')) {
        wp_send_json_error(array('message' => 'Security check failed.'));
    }

    $step = isset($_POST['step']) ? intval($_POST['step']) : 0;
    $fields = isset($_POST['fields']) ? $_POST['fields'] : array();
    
    if (!is_array($fields)) {
        parse_str($fields, $fields);
    }

    // Initialize WooCommerce session if needed
    if (!WC()->session->has_session()) {
        WC()->session->set_customer_session_cookie(true);
    }

    $errors = array();

    if ($step === 1) {
        // Validate required fields for step 1
        $required_fields = array(
            'billing_first_name' => 'First name',
            'billing_last_name' => 'Last name',
            'billing_email' => 'Email',
            'billing_phone' => 'Phone',
            'billing_address_1' => 'Address',
            'billing_city' => 'City',
            'billing_state' => 'State',
            'billing_postcode' => 'Zip code',
            'billing_country' => 'Country'
        );

        foreach ($required_fields as $field => $label) {
            if (empty($fields[$field])) {
                $errors[] = $label . ' is required.';
            }
        }

        // Validate email format
        if (!empty($fields['billing_email']) && !is_email($fields['billing_email'])) {
            $errors[] = 'Please enter a valid email address.';
        }

        if (empty($errors)) {
            // Save to session
            foreach ($fields as $key => $value) {
                WC()->session->set($key, sanitize_text_field($value));
            }

            // Update customer data
            $customer = WC()->customer;
            if ($customer) {
                $customer->set_billing_first_name(sanitize_text_field($fields['billing_first_name']));
                $customer->set_billing_last_name(sanitize_text_field($fields['billing_last_name']));
                $customer->set_billing_email(sanitize_email($fields['billing_email']));
                $customer->set_billing_phone(sanitize_text_field($fields['billing_phone']));
                $customer->set_billing_address_1(sanitize_text_field($fields['billing_address_1']));
                $customer->set_billing_address_2(sanitize_text_field($fields['billing_address_2']));
                $customer->set_billing_city(sanitize_text_field($fields['billing_city']));
                $customer->set_billing_state(sanitize_text_field($fields['billing_state']));
                $customer->set_billing_postcode(sanitize_text_field($fields['billing_postcode']));
                $customer->set_billing_country(sanitize_text_field($fields['billing_country']));
                
                // Set shipping address same as billing if no separate shipping
                if (empty($fields['ship_to_different_address'])) {
                    $customer->set_shipping_first_name(sanitize_text_field($fields['billing_first_name']));
                    $customer->set_shipping_last_name(sanitize_text_field($fields['billing_last_name']));
                    $customer->set_shipping_address_1(sanitize_text_field($fields['billing_address_1']));
                    $customer->set_shipping_address_2(sanitize_text_field($fields['billing_address_2']));
                    $customer->set_shipping_city(sanitize_text_field($fields['billing_city']));
                    $customer->set_shipping_state(sanitize_text_field($fields['billing_state']));
                    $customer->set_shipping_postcode(sanitize_text_field($fields['billing_postcode']));
                    $customer->set_shipping_country(sanitize_text_field($fields['billing_country']));
                }
                
                $customer->save();
            }

            // Calculate shipping and totals
            WC()->cart->calculate_shipping();
            WC()->cart->calculate_totals();

            // Save to user meta if logged in
            if (is_user_logged_in()) {
                $user_id = get_current_user_id();
                foreach ($fields as $key => $value) {
                    if (strpos($key, 'billing_') === 0 || strpos($key, 'shipping_') === 0) {
                        update_user_meta($user_id, $key, sanitize_text_field($value));
                    }
                }
            }

            wp_send_json_success(array('message' => 'Shipping information saved successfully.'));
        } else {
            wp_send_json_error(array('message' => implode(' ', $errors)));
        }

    } elseif ($step === 2) {
        // Validate payment method
        if (empty($fields['payment_method'])) {
            $errors[] = 'Please select a payment method.';
        } else {
            $available_gateways = WC()->payment_gateways()->get_available_payment_gateways();
            if (!isset($available_gateways[$fields['payment_method']])) {
                $errors[] = 'Invalid payment method selected.';
            }
        }

        if (empty($errors)) {
            // Save payment method to session
            WC()->session->set('chosen_payment_method', sanitize_text_field($fields['payment_method']));
            
            // Save other payment fields if needed
            foreach ($fields as $key => $value) {
                if (strpos($key, 'payment_') === 0) {
                    WC()->session->set($key, sanitize_text_field($value));
                }
            }

            wp_send_json_success(array('message' => 'Payment method saved successfully.'));
        } else {
            wp_send_json_error(array('message' => implode(' ', $errors)));
        }
    }

    wp_send_json_error(array('message' => 'Invalid step.'));
}

// Update customer data before checkout
add_action('wp_ajax_hype_pups_update_customer_data', 'hype_pups_update_customer_data');
add_action('wp_ajax_nopriv_hype_pups_update_customer_data', 'hype_pups_update_customer_data');

function hype_pups_update_customer_data() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'hype-pups-checkout-nonce')) {
        wp_send_json_error(array('message' => 'Security check failed.'));
    }

    $fields = isset($_POST['fields']) ? $_POST['fields'] : array();
    
    if (!is_array($fields)) {
        parse_str($fields, $fields);
    }

    // Update customer data
    $customer = WC()->customer;
    
    if ($customer) {
        // Set billing data
        if (isset($fields['billing_first_name'])) $customer->set_billing_first_name(sanitize_text_field($fields['billing_first_name']));
        if (isset($fields['billing_last_name'])) $customer->set_billing_last_name(sanitize_text_field($fields['billing_last_name']));
        if (isset($fields['billing_email'])) $customer->set_billing_email(sanitize_email($fields['billing_email']));
        if (isset($fields['billing_phone'])) $customer->set_billing_phone(sanitize_text_field($fields['billing_phone']));
        if (isset($fields['billing_address_1'])) $customer->set_billing_address_1(sanitize_text_field($fields['billing_address_1']));
        if (isset($fields['billing_address_2'])) $customer->set_billing_address_2(sanitize_text_field($fields['billing_address_2']));
        if (isset($fields['billing_city'])) $customer->set_billing_city(sanitize_text_field($fields['billing_city']));
        if (isset($fields['billing_state'])) $customer->set_billing_state(sanitize_text_field($fields['billing_state']));
        if (isset($fields['billing_postcode'])) $customer->set_billing_postcode(sanitize_text_field($fields['billing_postcode']));
        if (isset($fields['billing_country'])) $customer->set_billing_country(sanitize_text_field($fields['billing_country']));
        
        // Set shipping data
        if (isset($fields['shipping_first_name'])) $customer->set_shipping_first_name(sanitize_text_field($fields['shipping_first_name']));
        if (isset($fields['shipping_last_name'])) $customer->set_shipping_last_name(sanitize_text_field($fields['shipping_last_name']));
        if (isset($fields['shipping_address_1'])) $customer->set_shipping_address_1(sanitize_text_field($fields['shipping_address_1']));
        if (isset($fields['shipping_address_2'])) $customer->set_shipping_address_2(sanitize_text_field($fields['shipping_address_2']));
        if (isset($fields['shipping_city'])) $customer->set_shipping_city(sanitize_text_field($fields['shipping_city']));
        if (isset($fields['shipping_state'])) $customer->set_shipping_state(sanitize_text_field($fields['shipping_state']));
        if (isset($fields['shipping_postcode'])) $customer->set_shipping_postcode(sanitize_text_field($fields['shipping_postcode']));
        if (isset($fields['shipping_country'])) $customer->set_shipping_country(sanitize_text_field($fields['shipping_country']));
        
        $customer->save();
    }

    // Set chosen payment method
    if (isset($fields['payment_method'])) {
        WC()->session->set('chosen_payment_method', sanitize_text_field($fields['payment_method']));
    }

    wp_send_json_success(array('message' => 'Customer data updated successfully.'));
}

// Pre-populate checkout fields from session data
add_filter('woocommerce_checkout_get_value', 'hype_pups_checkout_get_value', 10, 2);
function hype_pups_checkout_get_value($value, $input) {
    // Check session first
    if (WC()->session && WC()->session->get($input)) {
        return WC()->session->get($input);
    }
    
    // Then check user meta if logged in
    if (is_user_logged_in() && empty($value)) {
        $user_id = get_current_user_id();
        $meta = get_user_meta($user_id, $input, true);
        if (!empty($meta)) {
            return $meta;
        }
    }
    
    return $value;
}

// Handle shipping address toggle
add_action('wp_footer', 'hype_pups_shipping_address_toggle');
function hype_pups_shipping_address_toggle() {
    if (is_checkout()) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Handle shipping address toggle
            $('#ship_to_different_address').change(function() {
                if ($(this).is(':checked')) {
                    // Show shipping fields dynamically
                    if ($('#shipping-address-fields').length === 0) {
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
                                            <?php
                                            $states = WC()->countries->get_states( WC()->countries->get_base_country() );
                                            foreach ( $states as $state_code => $state_name ) {
                                                echo '<option value="' . esc_attr( $state_code ) . '">' . esc_html( $state_name ) . '</option>';
                                            }
                                            ?>
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
                                        <?php
                                        $countries = WC()->countries->get_allowed_countries();
                                        foreach ( $countries as $country_code => $country_name ) {
                                            echo '<option value="' . esc_attr( $country_code ) . '">' . esc_html( $country_name ) . '</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        `;
                        $('#shipping-fields').append(shippingHTML);
                    }
                } else {
                    // Remove shipping fields
                    $('#shipping-address-fields').remove();
                }
                
                // Trigger checkout update
                $('body').trigger('update_checkout');
            });
        });
        </script>
        <?php
    }
}

// Custom checkout validation
add_action('woocommerce_checkout_process', 'hype_pups_checkout_validation');
function hype_pups_checkout_validation() {
    // Additional validation if needed
    if (empty($_POST['billing_phone'])) {
        wc_add_notice('Phone number is required.', 'error');
    }
    
    if (empty($_POST['billing_email']) || !is_email($_POST['billing_email'])) {
        wc_add_notice('A valid email address is required.', 'error');
    }
}

// Save checkout fields to order
add_action('woocommerce_checkout_update_order_meta', 'hype_pups_save_checkout_fields');
function hype_pups_save_checkout_fields($order_id) {
    if (!empty($_POST['billing_phone'])) {
        update_post_meta($order_id, '_billing_phone', sanitize_text_field($_POST['billing_phone']));
    }
}

// Clear cart after successful payment
add_action('woocommerce_payment_complete', 'hype_pups_clear_cart_after_payment');
add_action('woocommerce_order_status_completed', 'hype_pups_clear_cart_after_payment');
add_action('woocommerce_order_status_processing', 'hype_pups_clear_cart_after_payment');

function hype_pups_clear_cart_after_payment($order_id) {
    // Clear the cart
    if (WC()->cart) {
        WC()->cart->empty_cart();
    }
    
    // Clear session data
    if (WC()->session) {
        $session_keys = array(
            'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone',
            'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state',
            'billing_postcode', 'billing_country', 'shipping_address_1', 'shipping_address_2',
            'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_country',
            'chosen_payment_method'
        );
        
        foreach ($session_keys as $key) {
            WC()->session->__unset($key);
        }
    }
    
    // Clear stored checkout data
    try {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        unset($_SESSION['hype_pups_checkout_form_data']);
        unset($_SESSION['hype_pups_checkout_step']);
    } catch (Exception $e) {
        // Session handling error, continue
    }
}

// Handle order completion and redirect
add_action('woocommerce_thankyou', 'hype_pups_order_complete');
function hype_pups_order_complete($order_id) {
    // Clear session data
    if (WC()->session) {
        $session_keys = array(
            'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone',
            'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state',
            'billing_postcode', 'billing_country', 'shipping_address_1', 'shipping_address_2',
            'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_country',
            'chosen_payment_method'
        );
        
        foreach ($session_keys as $key) {
            WC()->session->__unset($key);
        }
    }
    
    // Clear browser storage
    ?>
    <script type="text/javascript">
    try {
        sessionStorage.removeItem('hype_pups_checkout_form_data');
        sessionStorage.removeItem('hype_pups_checkout_step');
    } catch (e) {
        console.log('Error clearing session storage:', e);
    }
    </script>
    <?php
}

// Ensure WooCommerce session is initialized
add_action('wp_loaded', 'hype_pups_init_wc_session');
function hype_pups_init_wc_session() {
    if (is_admin()) return;
    
    if (is_checkout() && !WC()->session->has_session()) {
        WC()->session->set_customer_session_cookie(true);
    }
}

// Fix checkout field validation
add_filter('woocommerce_checkout_fields', 'hype_pups_checkout_fields');
function hype_pups_checkout_fields($fields) {
    // Ensure required fields are properly marked
    $fields['billing']['billing_first_name']['required'] = true;
    $fields['billing']['billing_last_name']['required'] = true;
    $fields['billing']['billing_email']['required'] = true;
    $fields['billing']['billing_phone']['required'] = true;
    $fields['billing']['billing_address_1']['required'] = true;
    $fields['billing']['billing_city']['required'] = true;
    $fields['billing']['billing_state']['required'] = true;
    $fields['billing']['billing_postcode']['required'] = true;
    $fields['billing']['billing_country']['required'] = true;
    
    // Add custom classes
    foreach ($fields['billing'] as $key => $field) {
        $fields['billing'][$key]['class'][] = 'form-row-wide';
        $fields['billing'][$key]['input_class'][] = 'w-full rounded border border-gray-300 focus:border-[#FF3A5E] focus:ring-[#FF3A5E] py-2 px-3 text-sm';
    }
    
    return $fields;
}

// Ensure checkout scripts are loaded
add_action('wp_enqueue_scripts', 'hype_pups_ensure_checkout_scripts');
function hype_pups_ensure_checkout_scripts() {
    if (is_checkout()) {
        // Ensure core WooCommerce scripts are loaded
        wp_enqueue_script('woocommerce');
        wp_enqueue_script('wc-checkout');
        wp_enqueue_script('wc-address-i18n');
        wp_enqueue_script('wc-country-select');
        wp_enqueue_script('wc-cart-fragments');
        
        // Add checkout parameters
        wp_localize_script('wc-checkout', 'wc_checkout_params', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'wc_ajax_url' => WC_AJAX::get_endpoint('%%endpoint%%'),
            'update_order_review_nonce' => wp_create_nonce('update-order-review'),
            'apply_coupon_nonce' => wp_create_nonce('apply-coupon'),
            'remove_coupon_nonce' => wp_create_nonce('remove-coupon'),
            'option_guest_checkout' => get_option('woocommerce_enable_guest_checkout'),
            'checkout_url' => WC_AJAX::get_endpoint('checkout'),
            'is_checkout' => 1,
            'debug_mode' => defined('WP_DEBUG') && WP_DEBUG,
            'i18n_checkout_error' => esc_attr__('Error processing checkout. Please try again.', 'woocommerce'),
        ));
    }
}

// Custom checkout success handling
add_action('woocommerce_checkout_order_processed', 'hype_pups_checkout_success', 10, 3);
function hype_pups_checkout_success($order_id, $posted_data, $order) {
    // Clear multistep checkout session data
    $session_keys = array(
        'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone',
        'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state',
        'billing_postcode', 'billing_country', 'chosen_payment_method'
    );
    
    foreach ($session_keys as $key) {
        WC()->session->__unset($key);
    }
    
    // Add custom order meta if needed
    if (!empty($posted_data['billing_phone'])) {
        $order->update_meta_data('_billing_phone', sanitize_text_field($posted_data['billing_phone']));
        $order->save();
    }
}

// Add checkout page body class
add_filter('body_class', 'hype_pups_checkout_body_classes');
function hype_pups_checkout_body_classes($classes) {
    if (is_checkout()) {
        $classes[] = 'hype-pups-multistep-checkout';
        $classes[] = 'woocommerce-checkout-modern';
    }
    return $classes;
}

// Prevent checkout caching
add_action('wp', 'hype_pups_prevent_checkout_caching');
function hype_pups_prevent_checkout_caching() {
    if (is_checkout()) {
        if (!defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }
        nocache_headers();
    }
}

// Enhanced payment gateway handling
add_filter('woocommerce_available_payment_gateways', 'hype_pups_filter_payment_gateways');
function hype_pups_filter_payment_gateways($gateways) {
    if (is_admin()) return $gateways;
    
    // Ensure gateways are properly initialized
    foreach ($gateways as $gateway_id => $gateway) {
        if (!$gateway->is_available()) {
            unset($gateways[$gateway_id]);
        }
    }
    
    return $gateways;
}

// Fix for checkout field validation
add_action('woocommerce_after_checkout_validation', 'hype_pups_additional_checkout_validation', 10, 2);
function hype_pups_additional_checkout_validation($data, $errors) {
    // Additional validation
    if (empty($data['billing_phone'])) {
        $errors->add('billing_phone', __('Phone number is required.', 'woocommerce'));
    }
    
    if (!empty($data['billing_email']) && !is_email($data['billing_email'])) {
        $errors->add('billing_email', __('Please enter a valid email address.', 'woocommerce'));
    }
}

// Ensure proper order button text
add_filter('woocommerce_order_button_html', 'hype_pups_order_button_html');
function hype_pups_order_button_html($button_html) {
    $selected_gateway = WC()->session->get('chosen_payment_method');
    $button_text = 'Place Order';
    
    if ($selected_gateway && (strpos($selected_gateway, 'paypal') !== false || strpos($selected_gateway, 'ppec') !== false)) {
        $button_text = 'Pay with PayPal';
    }
    
    $button_html = '<button type="submit" class="button alt bg-[#FF3A5E] text-white px-6 py-3 rounded font-semibold hover:bg-[#E02E50] transition-colors" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr($button_text) . '" data-value="' . esc_attr($button_text) . '">' . esc_html($button_text) . '</button>';
    
    return $button_html;
}

// Handle PayPal integration
add_action('woocommerce_review_order_after_payment', 'hype_pups_paypal_integration');
function hype_pups_paypal_integration() {
    if (is_admin()) return;
    
    $chosen_payment_method = WC()->session->get('chosen_payment_method');
    if ($chosen_payment_method === 'ppec_paypal' || $chosen_payment_method === 'paypal') {
        ?>
        <script>
        jQuery(document).ready(function($) {
            // PayPal integration handling
            $(document.body).on('updated_checkout', function() {
                // Reinitialize PayPal buttons if needed
                if (typeof paypal !== 'undefined') {
                    // PayPal SDK handling
                }
            });
        });
        </script>
        <?php
    }
}

// Fix WooCommerce payment method display
add_action('wp_head', 'hype_pups_payment_method_styles');
function hype_pups_payment_method_styles() {
    if (is_checkout()) {
        ?>
        <style>
        .wc_payment_methods {
            list-style: none !important;
            padding: 0 !important;
        }
        .wc_payment_methods .wc_payment_method {
            margin-bottom: 1rem;
            padding: 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
        }
        .wc_payment_methods .wc_payment_method input[type="radio"] {
            margin-right: 0.75rem;
        }
        .wc_payment_methods .payment_box {
            margin-top: 1rem;
            padding: 1rem;
            background: #f9fafb;
            border-radius: 0.375rem;
        }
        .woocommerce-checkout-review-order-table {
            width: 100%;
            border-collapse: collapse;
        }
        .woocommerce-checkout-review-order-table th,
        .woocommerce-checkout-review-order-table td {
            padding: 0.75rem;
            border-bottom: 1px solid #e5e7eb;
        }
        .order-total {
            font-weight: bold;
            font-size: 1.125rem;
        }
        .checkout-loading {
            opacity: 0.6;
            pointer-events: none;
        }
        .checkout-loading::after {
            content: "Updating...";
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            border-radius: 4px;
        }
        
        /* Payment method radio button styling */
        .wc_payment_methods .wc_payment_method label {
            cursor: pointer;
            display: flex;
            align-items: center;
            font-weight: 500;
        }
        
        .wc_payment_methods .wc_payment_method:hover {
            background-color: #f9fafb;
            border-color: #FF3A5E;
        }
        
        .wc_payment_methods .wc_payment_method input[type="radio"]:checked + span {
            color: #FF3A5E;
        }
        
        /* Processing state styles */
        #place_order.processing {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        #place_order.processing::after {
            content: "";
            display: inline-block;
            width: 16px;
            height: 16px;
            margin-left: 8px;
            border: 2px solid transparent;
            border-top: 2px solid currentColor;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        </style>
        <?php
    }
}

// Clear cart and session data after PayPal returns
add_action('woocommerce_api_wc_gateway_paypal', 'hype_pups_paypal_return_handler');
function hype_pups_paypal_return_handler() {
    // This will be called when PayPal returns
    if (isset($_GET['paypal_return']) && $_GET['paypal_return'] === 'success') {
        // Clear cart
        if (WC()->cart) {
            WC()->cart->empty_cart();
        }
        
        // Clear session data
        hype_pups_clear_checkout_session();
    }
}

// Helper function to clear checkout session
function hype_pups_clear_checkout_session() {
    if (WC()->session) {
        $session_keys = array(
            'billing_first_name', 'billing_last_name', 'billing_email', 'billing_phone',
            'billing_address_1', 'billing_address_2', 'billing_city', 'billing_state',
            'billing_postcode', 'billing_country', 'shipping_address_1', 'shipping_address_2',
            'shipping_city', 'shipping_state', 'shipping_postcode', 'shipping_country',
            'chosen_payment_method'
        );
        
        foreach ($session_keys as $key) {
            WC()->session->__unset($key);
        }
    }
}

// Additional cart clearing hooks for different payment scenarios
add_action('woocommerce_payment_complete_order_status_processing', 'hype_pups_clear_cart_after_payment');
add_action('woocommerce_payment_complete_order_status_completed', 'hype_pups_clear_cart_after_payment');
add_action('woocommerce_thankyou_paypal', 'hype_pups_clear_cart_after_payment');
add_action('woocommerce_thankyou_ppec_paypal', 'hype_pups_clear_cart_after_payment');

// Ensure cart is empty on order received page
add_action('template_redirect', 'hype_pups_ensure_cart_empty_on_thank_you');
function hype_pups_ensure_cart_empty_on_thank_you() {
    if (is_wc_endpoint_url('order-received')) {
        if (WC()->cart && WC()->cart->get_cart_contents_count() > 0) {
            WC()->cart->empty_cart();
        }
        hype_pups_clear_checkout_session();
    }
}

// Handle checkout errors and restore button state
add_action('wp_footer', 'hype_pups_checkout_error_handling');
function hype_pups_checkout_error_handling() {
    if (!is_checkout()) return;
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Handle WooCommerce checkout errors
        $(document.body).on('checkout_error', function(e, error_message) {
            $('.checkout-error').remove();
            
            var errorHtml = '<div class="checkout-error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">' + 
                           '<span class="block sm:inline">' + error_message + '</span>' +
                           '</div>';
            
            $('.checkout-step:visible').prepend(errorHtml);
            
            // Restore button state
            $('#place_order').prop('disabled', false).removeClass('processing');
            
            var selectedPayment = $('input[name="payment_method"]:checked').val();
            if (selectedPayment && (selectedPayment.includes('paypal') || selectedPayment.includes('ppec'))) {
                $('#place_order').text('Pay with PayPal');
            } else {
                $('#place_order').text('Place Order');
            }
            
            $('html, body').animate({
                scrollTop: $('.checkout-error').offset().top - 100
            }, 500);
        });
        
        // Handle form submission success
        $(document.body).on('checkout_place_order', function() {
            // Show processing state
            $('#place_order').prop('disabled', true).addClass('processing');
            
            var selectedPayment = $('input[name="payment_method"]:checked').val();
            if (selectedPayment && (selectedPayment.includes('paypal') || selectedPayment.includes('ppec'))) {
                $('#place_order').text('Redirecting to PayPal...');
            } else {
                $('#place_order').text('Processing Order...');
            }
            
            return true;
        });
    });
    </script>
    <?php
}

// Debug function for development (remove in production)
if (defined('WP_DEBUG') && WP_DEBUG) {
    add_action('wp_footer', 'hype_pups_checkout_debug_info');
    function hype_pups_checkout_debug_info() {
        if (is_checkout() && current_user_can('manage_options')) {
            ?>
            <script type="text/javascript">
            console.log('Hype Pups Checkout Debug Info:', {
                'Cart Items': <?php echo json_encode(WC()->cart->get_cart_contents_count()); ?>,
                'Cart Total': '<?php echo WC()->cart->get_total(); ?>',
                'Chosen Payment Method': '<?php echo WC()->session->get('chosen_payment_method'); ?>',
                'Customer Country': '<?php echo WC()->customer->get_billing_country(); ?>',
                'Session ID': '<?php echo WC()->session->get_customer_id(); ?>',
                'Available Gateways': <?php 
                $gateways = array_keys(WC()->payment_gateways()->get_available_payment_gateways());
                echo json_encode($gateways); 
                ?>
            });
            </script>
            <?php
        }
    }
}

// Ensure proper nonce handling for AJAX requests
add_action('wp_ajax_nopriv_woocommerce_checkout', 'hype_pups_handle_guest_checkout');
function hype_pups_handle_guest_checkout() {
    // Ensure guest checkout is handled properly
    if (!is_user_logged_in()) {
        // Initialize customer session for guests
        if (!WC()->session->has_session()) {
            WC()->session->set_customer_session_cookie(true);
        }
    }
}

// Ensure shipping is calculated properly
add_action('woocommerce_checkout_update_order_review', 'hype_pups_ensure_shipping_calculation');
function hype_pups_ensure_shipping_calculation() {
    if (WC()->cart->needs_shipping()) {
        WC()->cart->calculate_shipping();
    }
    WC()->cart->calculate_totals();
}    


add_action('admin_post_submit_product_review', 'handle_product_review_submission');
add_action('admin_post_nopriv_submit_product_review', 'handle_product_review_submission');

function handle_product_review_submission() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['review_nonce'], 'submit_review')) {
        wp_die('Security check failed');
    }
    
    // Check if user is logged in
    if (!is_user_logged_in()) {
        wp_redirect(wp_login_url(get_permalink($_POST['product_id'])));
        exit;
    }
    
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $review_title = sanitize_text_field($_POST['review_title']);
    $review_content = sanitize_textarea_field($_POST['review_content']);
    $user_id = get_current_user_id();
    $user_info = get_userdata($user_id);
    
    // Validate required fields
    if (empty($rating) || empty($review_title) || empty($review_content)) {
        wp_redirect(add_query_arg('review_error', 'missing_fields', get_permalink($product_id)));
        exit;
    }
    
    // Check if user already reviewed this product
    $existing_review = get_comments(array(
        'post_id' => $product_id,
        'user_id' => $user_id,
        'count' => true
    ));
    
    if ($existing_review > 0) {
        wp_redirect(add_query_arg('review_error', 'already_reviewed', get_permalink($product_id)));
        exit;
    }
    
    // Create the review
    $comment_data = array(
        'comment_post_ID' => $product_id,
        'comment_author' => $user_info->display_name,
        'comment_author_email' => $user_info->user_email,
        'comment_author_url' => $user_info->user_url,
        'comment_content' => $review_content,
        'comment_type' => 'review',
        'comment_approved' => 1, // Auto-approve or set to 0 for moderation
        'user_id' => $user_id,
    );
    
    $comment_id = wp_insert_comment($comment_data);
    
    if ($comment_id) {
        // Add rating and title as comment meta
        add_comment_meta($comment_id, 'rating', $rating);
        add_comment_meta($comment_id, 'review_title', $review_title);
        
        // Redirect with success message
        wp_redirect(add_query_arg('review_success', '1', get_permalink($product_id)));
    } else {
        // Redirect with error message
        wp_redirect(add_query_arg('review_error', 'submission_failed', get_permalink($product_id)));
    }
    
    exit;
}

// Display success/error messages
add_action('wp_head', 'display_review_messages');

function display_review_messages() {
    if (isset($_GET['review_success'])) {
        echo '<script>
            jQuery(document).ready(function($) {
                $("body").prepend("<div class=\"review-message success bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4\" role=\"alert\">Your review has been submitted successfully!</div>");
                setTimeout(function() {
                    $(".review-message").fadeOut();
                }, 5000);
            });
        </script>';
    }
    
    if (isset($_GET['review_error'])) {
        $error = $_GET['review_error'];
        $message = '';
        
        switch ($error) {
            case 'missing_fields':
                $message = 'Please fill in all required fields.';
                break;
            case 'already_reviewed':
                $message = 'You have already reviewed this product.';
                break;
            case 'submission_failed':
                $message = 'Failed to submit review. Please try again.';
                break;
            default:
                $message = 'An error occurred. Please try again.';
        }
        
        echo '<script>
            jQuery(document).ready(function($) {
                $("body").prepend("<div class=\"review-message error bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4\" role=\"alert\">" + "' . $message . '" + "</div>");
                setTimeout(function() {
                    $(".review-message").fadeOut();
                }, 5000);
            });
        </script>';
    }
}

// Enqueue jQuery if not already loaded
add_action('wp_enqueue_scripts', 'enqueue_review_scripts');

function enqueue_review_scripts() {
    if (!wp_script_is('jquery', 'enqueued')) {
        wp_enqueue_script('jquery');
    }
}

// Add custom comment type for reviews
add_filter('comment_form_default_fields', 'modify_comment_form_fields');

function modify_comment_form_fields($fields) {
    // This ensures our custom review type works with WordPress comments system
    return $fields;
}

// Optional: Add review schema markup for SEO
add_action('wp_footer', 'add_review_schema_markup');

function add_review_schema_markup() {
    if (is_singular('product')) { // Adjust post type as needed
        global $post;
        
        $reviews = get_comments(array(
            'post_id' => $post->ID,
            'status' => 'approve',
            'type' => 'review'
        ));
        
        if (!empty($reviews)) {
            $total_rating = 0;
            $review_count = count($reviews);
            
            foreach ($reviews as $review) {
                $rating = get_comment_meta($review->comment_ID, 'rating', true);
                $total_rating += intval($rating);
            }
            
            $average_rating = round($total_rating / $review_count, 1);
            
            $schema = array(
                "@context" => "https://schema.org/",
                "@type" => "Product",
                "name" => get_the_title(),
                "aggregateRating" => array(
                    "@type" => "AggregateRating",
                    "ratingValue" => $average_rating,
                    "reviewCount" => $review_count
                )
            );
            
            echo '<script type="application/ld+json">' . json_encode($schema) . '</script>';
        }
    }
}


// Enqueue scripts and styles for quick view
function quickview_enqueue_scripts() {
    if (is_woocommerce() || is_cart() || is_shop() || is_product_category() || is_product_tag() || is_product() || is_page()) {
        wp_enqueue_script('jquery');
        
        // Enqueue WooCommerce scripts for variations
        wp_enqueue_script('wc-add-to-cart-variation');
        wp_enqueue_script('wc-single-product');
        
        wp_localize_script('jquery', 'quickview_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('quickview_nonce'),
            'loading_text' => __('Loading...', 'textdomain'),
            'error_text' => __('Error loading product', 'textdomain'),
            'cart_url' => wc_get_cart_url()
        ));
    }
}
add_action('wp_enqueue_scripts', 'quickview_enqueue_scripts');

/**
 * Get properly formatted product attributes for quick view
 */
function quickview_get_product_attributes($product) {
    $attributes = array();
    
    if ($product->is_type('variable')) {
        // For variable products, get variation attributes
        $variation_attributes = $product->get_variation_attributes();
        
        foreach ($variation_attributes as $attribute_name => $attribute_values) {
            $clean_name = str_replace('attribute_', '', $attribute_name);
            
            if (taxonomy_exists($clean_name)) {
                $attribute_label = wc_attribute_label($clean_name);
                $terms = get_terms(array(
                    'taxonomy' => $clean_name,
                    'slug' => $attribute_values,
                    'hide_empty' => false,
                ));
                
                if (!empty($terms) && !is_wp_error($terms)) {
                    $options = array();
                    foreach ($terms as $term) {
                        $options[] = array(
                            'name' => $term->name,
                            'slug' => $term->slug,
                            'id' => $term->term_id
                        );
                    }
                    
                    $attributes[$clean_name] = array(
                        'label' => $attribute_label,
                        'options' => $options,
                        'is_taxonomy' => true,
                        'is_variation' => true,
                        'attribute_name' => $attribute_name
                    );
                }
        } else {
                // Custom attribute
                $options = array();
                foreach ($attribute_values as $value) {
                    if (!empty($value)) {
                        $options[] = array(
                            'name' => ucfirst($value),
                            'slug' => sanitize_title($value),
                            'id' => sanitize_title($value)
                        );
                    }
                }
                
                if (!empty($options)) {
                    $clean_label = ucwords(str_replace(array('-', '_'), ' ', $clean_name));
                    $attributes[$clean_name] = array(
                        'label' => $clean_label,
                        'options' => $options,
                        'is_taxonomy' => false,
                        'is_variation' => true,
                        'attribute_name' => $attribute_name
                    );
                }
            }
        }
        } else {
        // For simple products, get display attributes
        $product_attributes = $product->get_attributes();
        
        foreach ($product_attributes as $attribute_name => $attribute_data) {
            if (!$attribute_data->get_variation()) {
                $attribute_label = wc_attribute_label($attribute_name);
                
                if ($attribute_data->is_taxonomy()) {
                    $terms = wp_get_post_terms($product->get_id(), $attribute_data->get_taxonomy());
                    
                    if (!empty($terms) && !is_wp_error($terms)) {
                        $options = array();
                        foreach ($terms as $term) {
                            $options[] = array(
                                'name' => $term->name,
                                'slug' => $term->slug,
                                'id' => $term->term_id
                            );
                        }
                        
                        $attributes[$attribute_name] = array(
                            'label' => $attribute_label,
                            'options' => $options,
                            'is_taxonomy' => true,
                            'is_variation' => false
                        );
                    }
                } else {
                    $attribute_values = $attribute_data->get_options();
                    
                    if (!empty($attribute_values)) {
                        $options = array();
                        foreach ($attribute_values as $value) {
                            $value = trim($value);
                            if (!empty($value)) {
                                $options[] = array(
                                    'name' => $value,
                                    'slug' => sanitize_title($value),
                                    'id' => sanitize_title($value)
                                );
                            }
                        }
                        
                        if (!empty($options)) {
                            $attributes[$attribute_name] = array(
                                'label' => $attribute_label,
                                'options' => $options,
                                'is_taxonomy' => false,
                                'is_variation' => false
                            );
                        }
                    }
                }
            }
        }
    }
    
    return $attributes;
}

/**
 * Get product gallery images
 */
function quickview_get_product_gallery($product) {
    $gallery_images = array();
    
    // Get main product image
    $main_image_id = $product->get_image_id();
    if ($main_image_id) {
        $gallery_images[] = array(
            'id' => $main_image_id,
            'thumbnail' => wp_get_attachment_image_url($main_image_id, 'woocommerce_gallery_thumbnail'),
            'full' => wp_get_attachment_image_url($main_image_id, 'woocommerce_single'),
            'alt' => get_post_meta($main_image_id, '_wp_attachment_image_alt', true) ?: $product->get_name()
        );
    }
    
    // Get gallery images
    $gallery_ids = $product->get_gallery_image_ids();
    foreach ($gallery_ids as $gallery_id) {
        $gallery_images[] = array(
            'id' => $gallery_id,
            'thumbnail' => wp_get_attachment_image_url($gallery_id, 'woocommerce_gallery_thumbnail'),
            'full' => wp_get_attachment_image_url($gallery_id, 'woocommerce_single'),
            'alt' => get_post_meta($gallery_id, '_wp_attachment_image_alt', true) ?: $product->get_name()
        );
    }
    
    // If no images, add placeholder
    if (empty($gallery_images)) {
        $placeholder = wc_placeholder_img_src('woocommerce_single');
        $gallery_images[] = array(
            'id' => 0,
            'thumbnail' => $placeholder,
            'full' => $placeholder,
            'alt' => $product->get_name()
        );
    }
    
    return $gallery_images;
}

/**
 * Get variation data for variable products
 */
function quickview_get_variation_data($product) {
    $variations_data = array();
    
    if ($product->is_type('variable')) {
        $available_variations = $product->get_available_variations();
        
        foreach ($available_variations as $variation) {
            $variation_obj = wc_get_product($variation['variation_id']);
            
            if ($variation_obj && $variation_obj->exists()) {
                $variation_image_id = $variation_obj->get_image_id();
                $variation_image = '';
                
                if ($variation_image_id) {
                    $variation_image = wp_get_attachment_image_url($variation_image_id, 'woocommerce_single');
                }
                
                $variations_data[$variation['variation_id']] = array(
                    'price_html' => $variation_obj->get_price_html(),
                    'display_price' => wc_get_price_to_display($variation_obj),
                    'display_regular_price' => wc_get_price_to_display($variation_obj, array('price' => $variation_obj->get_regular_price())),
                    'image' => $variation_image,
                    'attributes' => $variation['attributes'],
                    'is_in_stock' => $variation_obj->is_in_stock(),
                    'stock_quantity' => $variation_obj->get_stock_quantity(),
                    'variation_is_active' => $variation['variation_is_active'],
                    'availability_html' => wc_get_stock_html($variation_obj)
                );
            }
        }
    }
    
    return $variations_data;
}

/**
 * AJAX handler for quick view
 */
function quickview_get_product_data() {
    // Check nonce for security
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'quickview_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }
    
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    
    if (!$product_id) {
        wp_send_json_error(array('message' => 'Invalid product ID'));
        return;
    }
    
    $product = wc_get_product($product_id);
    
    if (!$product || !$product->exists()) {
        wp_send_json_error(array('message' => 'Product not found'));
        return;
    }
    
    try {
        // Get product categories
        $categories = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'names'));
        $category = !empty($categories) ? implode(', ', array_slice($categories, 0, 2)) : __('Uncategorized', 'textdomain');
        
        // Get product attributes
        $attributes = quickview_get_product_attributes($product);
        
        // Get gallery images
        $gallery = quickview_get_product_gallery($product);
        
        // Get variation data
        $variations = quickview_get_variation_data($product);
        
        // Get reviews data
        $review_count = $product->get_review_count();
        $average_rating = $product->get_average_rating();
        
        // Get stock status
        $stock_status = $product->get_stock_status();
        $stock_text = __('In Stock', 'textdomain');
        
        switch ($stock_status) {
            case 'outofstock':
                $stock_text = __('Out of Stock', 'textdomain');
                break;
            case 'onbackorder':
                $stock_text = __('On Backorder', 'textdomain');
                break;
            case 'instock':
            default:
                $stock_text = __('In Stock', 'textdomain');
                break;
        }
        
        // Get description
        $description = $product->get_short_description();
        if (empty($description)) {
            $description = wp_trim_words($product->get_description(), 30, '...');
        }
        
        // Check if product is bestseller (you can customize this logic)
        $is_bestseller = get_post_meta($product_id, '_is_bestseller', true) || $product->is_featured();
        
        // Prepare response data
        $response_data = array(
            'id' => $product_id,
            'title' => $product->get_name(),
            'price' => $product->get_price_html(),
            'description' => wp_strip_all_tags($description),
            'image' => !empty($gallery) ? $gallery[0]['full'] : '',
            'gallery' => $gallery,
            'category' => $category,
            'rating' => floatval($average_rating),
            'review_count' => intval($review_count),
            'attributes' => $attributes,
            'variations' => $variations,
            'in_stock' => $product->is_in_stock(),
            'stock_status' => $stock_text,
            'type' => $product->get_type(),
            'permalink' => $product->get_permalink(),
            'add_to_cart_text' => $product->add_to_cart_text(),
            'is_purchasable' => $product->is_purchasable(),
            'is_bestseller' => $is_bestseller,
            'min_qty' => 1,
            'max_qty' => $product->get_max_purchase_quantity()
        );
        
        wp_send_json_success($response_data);
        
    } catch (Exception $e) {
        wp_send_json_error(array('message' => 'Error loading product: ' . $e->getMessage()));
    }
}

// Hook AJAX actions
add_action('wp_ajax_get_quick_view_product', 'quickview_get_product_data');
add_action('wp_ajax_nopriv_get_quick_view_product', 'quickview_get_product_data');

/**
 * AJAX handler for adding product to cart from quick view
 */
function quickview_add_to_cart() {
    // Debug logging
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('QuickView Add to Cart - POST data: ' . print_r($_POST, true));
    }
    
    // Check nonce
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'quickview_nonce')) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('QuickView Add to Cart - Nonce verification failed');
        }
        wp_send_json_error(array('message' => 'Security check failed'));
        return;
    }
    
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $variation_id = isset($_POST['variation_id']) ? intval($_POST['variation_id']) : 0;
    $variation_data = isset($_POST['variation']) ? $_POST['variation'] : array();
    
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('QuickView Add to Cart - Processed data: Product ID: ' . $product_id . ', Quantity: ' . $quantity . ', Variation ID: ' . $variation_id . ', Variation Data: ' . print_r($variation_data, true));
    }
    
    if (!$product_id) {
        wp_send_json_error(array('message' => 'Invalid product ID'));
        return;
    }
    
    try {
        // Clean variation data
        $cleaned_variation = array();
        if (!empty($variation_data)) {
            foreach ($variation_data as $key => $value) {
                if (!empty($value)) {
                    $cleaned_variation[sanitize_text_field($key)] = sanitize_text_field($value);
                }
            }
        }
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('QuickView Add to Cart - Cleaned variation data: ' . print_r($cleaned_variation, true));
        }
        
        // Add to cart
        if ($variation_id && !empty($cleaned_variation)) {
            $added = WC()->cart->add_to_cart($product_id, $quantity, $variation_id, $cleaned_variation);
        } else {
            $added = WC()->cart->add_to_cart($product_id, $quantity);
        }
        
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('QuickView Add to Cart - Add to cart result: ' . ($added ? 'success' : 'failed'));
        }
        
        if ($added) {
            // Get updated cart data
            $cart_count = WC()->cart->get_cart_contents_count();
            $cart_total = WC()->cart->get_cart_total();
            
            wp_send_json_success(array(
                'message' => __('Product added to cart successfully!', 'textdomain'),
                'cart_count' => $cart_count,
                'cart_total' => $cart_total,
                'cart_url' => wc_get_cart_url(),
                'cart_hash' => WC()->cart->get_cart_hash()
            ));
        } else {
            wp_send_json_error(array('message' => __('Failed to add product to cart', 'textdomain')));
        }
        
    } catch (Exception $e) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('QuickView Add to Cart - Exception: ' . $e->getMessage());
        }
        wp_send_json_error(array('message' => 'Error: ' . $e->getMessage()));
    }
}

// Hook AJAX actions for add to cart
add_action('wp_ajax_quickview_add_to_cart', 'quickview_add_to_cart');
add_action('wp_ajax_nopriv_quickview_add_to_cart', 'quickview_add_to_cart');

/**
 * Add quick view button to product loops
 */
function quickview_add_button($product_id = null) {
    if (!$product_id) {
        global $product;
        $product_id = $product->get_id();
    }
    
    echo '<a href="#" class="quickview-btn flex items-center bg-white text-gray-800 font-medium rounded-lg px-4 py-2 shadow hover:bg-gray-100 text-base gap-2" data-product-id="' . esc_attr($product_id) . '">';
    echo '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">';
    echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />';
    echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
    echo '</svg>';
    echo __('Quick View', 'textdomain');
    echo '</a>';
}

/**
 * Add the popup HTML to footer
 */
function quickview_add_popup_html() {
    if (is_woocommerce() || is_cart() || is_shop() || is_product_category() || is_product_tag() || is_product()) {
        ?>
        <!-- Quick View Popup HTML will be added here -->
        <?php
    }
}
add_action('wp_footer', 'quickview_add_popup_html');


// Custom User Registration AJAX Handler
add_action('wp_ajax_nopriv_custom_user_registration', 'handle_custom_user_registration');
add_action('wp_ajax_custom_user_registration', 'handle_custom_user_registration');

function handle_custom_user_registration() {
    // Debug: Log the incoming request
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Registration Debug: POST data received: ' . print_r($_POST, true));
    }

    // Verify nonce for security
    if (!wp_verify_nonce($_POST['_wpnonce'], 'custom_user_registration')) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Registration Debug: Nonce verification failed');
        }
        wp_send_json_error('Security check failed');
    }

    // Get form data
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $email = sanitize_email($_POST['user_email']);
    $username = sanitize_user($_POST['user_login']);
    $password = $_POST['user_pass'];
    $confirm_password = $_POST['user_pass_confirm'];
    $terms_agreement = isset($_POST['terms_agreement']) ? true : false;

    // Debug: Log the processed data
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Registration Debug: Processed data - First: ' . $first_name . ', Last: ' . $last_name . ', Email: ' . $email . ', Username: ' . $username . ', Terms: ' . ($terms_agreement ? 'yes' : 'no'));
    }

    // Validation
    $errors = array();

    // Check if terms are agreed
    if (!$terms_agreement) {
        $errors[] = 'You must agree to the Terms of Service and Privacy Policy';
    }

    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = 'Passwords do not match';
    }

    // Check password strength
    if (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long';
    }

    // Check if username exists
    if (username_exists($username)) {
        $errors[] = 'Username already exists';
    }

    // Check if email exists
    if (email_exists($email)) {
        $errors[] = 'Email address already exists';
    }

    // Validate email format
    if (!is_email($email)) {
        $errors[] = 'Invalid email address';
    }

    // If there are errors, return them
    if (!empty($errors)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Registration Debug: Validation errors: ' . implode(', ', $errors));
        }
        wp_send_json_error(implode(', ', $errors));
    }

    // Create user
    $user_data = array(
        'user_login' => $username,
        'user_email' => $email,
        'user_pass' => $password,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'display_name' => $first_name . ' ' . $last_name,
        'role' => 'customer'
    );

    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Registration Debug: Attempting to create user with data: ' . print_r($user_data, true));
    }

    $user_id = wp_insert_user($user_data);

    if (is_wp_error($user_id)) {
        if (defined('WP_DEBUG') && WP_DEBUG) {
            error_log('Registration Debug: User creation failed: ' . $user_id->get_error_message());
        }
        wp_send_json_error($user_id->get_error_message());
    }

    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('Registration Debug: User created successfully with ID: ' . $user_id);
    }

    // Auto-login the user
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    // Send success response
    wp_send_json_success(array(
        'message' => 'Account created successfully!',
        'user_id' => $user_id,
        'redirect_url' => get_permalink()
    ));
}

// Add nonce to registration form
function add_registration_nonce() {
    if (!is_user_logged_in()) {
        wp_nonce_field('custom_user_registration', '_wpnonce');
    }
}
add_action('wp_footer', 'add_registration_nonce');

// Enable user registration if not already enabled
function enable_user_registration() {
    if (!get_option('users_can_register')) {
        update_option('users_can_register', 1);
    }
}
add_action('init', 'enable_user_registration');

// YITH WooCommerce Wishlist Integration
function hype_pups_yith_wishlist_setup() {
    // Remove default YITH positioning
    if (function_exists('YITH_WCWL')) {
        remove_action('woocommerce_single_product_summary', array(YITH_WCWL_Frontend(), 'print_button'), 31);
        remove_action('woocommerce_after_shop_loop_item', array(YITH_WCWL_Frontend(), 'print_button'), 15);
    }
}
add_action('init', 'hype_pups_yith_wishlist_setup');

// Ensure YITH wishlist scripts are loaded on shop pages
function hype_pups_enqueue_yith_wishlist_scripts() {
    // Check if YITH plugin is active
    if (function_exists('YITH_WCWL') || function_exists('yith_wcwl_add_to_wishlist')) {
        if (is_shop() || is_product_category() || is_product()) {
            // Ensure YITH wishlist scripts are loaded
            if (function_exists('YITH_WCWL_Frontend')) {
                YITH_WCWL_Frontend()->enqueue_styles_and_stuffs();
            }
            
            // Add custom script to ensure YITH is properly initialized
            wp_enqueue_script('hype-pups-yith-wishlist', get_template_directory_uri() . '/assets/js/yith-wishlist-init.js', array('jquery'), '1.0', true);
            
            // Pass YITH status to JavaScript
            wp_localize_script('hype-pups-yith-wishlist', 'hypePupsYith', array(
                'yith_active' => function_exists('YITH_WCWL'),
                'yith_frontend_active' => function_exists('YITH_WCWL_Frontend'),
                'ajax_url' => admin_url('admin-ajax.php')
            ));
        }
    }
}
add_action('wp_enqueue_scripts', 'hype_pups_enqueue_yith_wishlist_scripts');
    

///////// FAHIM KAAAAND ////////////

// Enqueue required scripts and styles
function enqueue_filter_assets() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        // Enqueue Alpine.js for mobile filters
        wp_enqueue_script('alpine-js', 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js', [], '3.0', true);
        wp_script_add_data('alpine-js', 'defer', true);
        
        // Localize script for AJAX
        wp_localize_script('alpine-js', 'woo_filter_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('filter_nonce'),
            'cart_url' => wc_get_cart_url(),
        ]);
    }
}
add_action('wp_enqueue_scripts', 'enqueue_filter_assets');

// AJAX handler to get filter options
function handle_get_filter_options() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'filter_nonce')) {
        wp_die('Security check failed');
    }
    
    global $wpdb;
    
    // Get price range from all published products
    $price_range = $wpdb->get_row("
        SELECT 
            MIN(CAST(pm.meta_value AS DECIMAL(10,2))) as min_price,
            MAX(CAST(pm.meta_value AS DECIMAL(10,2))) as max_price
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE p.post_type = 'product'
        AND p.post_status = 'publish'
        AND pm.meta_key = '_price'
        AND pm.meta_value != ''
        AND pm.meta_value > 0
    ");
    
    // Get product categories (only parent categories)
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => 0,
        'orderby' => 'name',
        'order' => 'ASC'
    ]);
    
    $category_data = [];
    if (!is_wp_error($categories)) {
        $category_data = array_map(function($cat) {
            return [
                'slug' => $cat->slug,
                'name' => $cat->name,
                'count' => $cat->count
            ];
        }, $categories);
    }
    
    // Get product attributes (sizes and colors)
    $attributes = [
        'sizes' => get_product_attribute_values('size'),
        'colors' => get_product_attribute_values('color')
    ];
    
    wp_send_json_success([
        'price_range' => [
            'min' => floor($price_range->min_price ?? 0),
            'max' => ceil($price_range->max_price ?? 1000)
        ],
        'categories' => $category_data,
        'attributes' => $attributes
    ]);
}
add_action('wp_ajax_get_filter_options', 'handle_get_filter_options');
add_action('wp_ajax_nopriv_get_filter_options', 'handle_get_filter_options');

// Helper function to get product attribute values
function get_product_attribute_values($attribute_name) {
    global $wpdb;
    
    $values = [];
    
    // 1. Get global attribute terms (pa_attribute)
    $taxonomy = 'pa_' . $attribute_name;
    $terms = get_terms([
        'taxonomy' => $taxonomy,
        'hide_empty' => true,
        'orderby' => 'name',
        'order' => 'ASC'
    ]);
    
    if (!is_wp_error($terms)) {
        foreach ($terms as $term) {
            $values[] = [
                'slug' => $term->slug,
                'name' => $term->name,
                'value' => $term->slug
            ];
        }
    }
    
    // 2. Get custom attribute values from product variations and simple products
    $custom_values = $wpdb->get_results($wpdb->prepare("
        SELECT DISTINCT pm.meta_value
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
        WHERE p.post_type IN ('product', 'product_variation')
        AND p.post_status = 'publish'
        AND pm.meta_key = %s
        AND pm.meta_value != ''
        ORDER BY pm.meta_value
    ", 'attribute_' . $attribute_name));
    
    foreach ($custom_values as $value) {
        $slug = sanitize_title($value->meta_value);
        // Check if this slug already exists to avoid duplicates
        $exists = false;
        foreach ($values as $existing) {
            if ($existing['slug'] === $slug) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            $values[] = [
                'slug' => $slug,
                'name' => $value->meta_value,
                'value' => $value->meta_value
            ];
        }
    }
    
    return $values;
}

// AJAX handler for filtering products
function handle_filter_products() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'filter_nonce')) {
        wp_die('Security check failed');
    }
    
    $filters = json_decode(stripslashes($_POST['filters']), true);
    $category_slug = sanitize_text_field($_POST['category_slug'] ?? '');
    
    // Build WP_Query arguments
    $args = [
        'post_type' => 'product',
        'post_status' => 'publish',
        'posts_per_page' => 12,
        'paged' => intval($filters['paged'] ?? 1),
        'meta_query' => ['relation' => 'AND'],
        'tax_query' => ['relation' => 'AND']
    ];
    
    // Category filter
    if (!empty($filters['categories'])) {
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => array_map('sanitize_text_field', $filters['categories']),
            'operator' => 'IN'
        ];
    } elseif (!empty($category_slug)) {
        // Maintain category context if on a category page
        $args['tax_query'][] = [
            'taxonomy' => 'product_cat',
            'field' => 'slug',
            'terms' => $category_slug
        ];
    }
    
    // Price filter
    if (!empty($filters['min_price']) || !empty($filters['max_price'])) {
        $min_price = floatval($filters['min_price'] ?? 0);
        $max_price = floatval($filters['max_price'] ?? 999999);
        
        $args['meta_query'][] = [
            'key' => '_price',
            'value' => [$min_price, $max_price],
            'type' => 'NUMERIC',
            'compare' => 'BETWEEN'
        ];
    }
    
    // Handle attribute filters (sizes and colors)
    $attribute_product_ids = null;
    
    if (!empty($filters['sizes']) || !empty($filters['colors'])) {
        $attribute_product_ids = get_products_by_attributes($filters);
        
        if (empty($attribute_product_ids)) {
            // No products match the attribute criteria
            $args['post__in'] = [0]; // This will return no results
        } else {
            $args['post__in'] = $attribute_product_ids;
        }
    }
    
    // Sorting
    $orderby = sanitize_text_field($filters['orderby'] ?? 'menu_order');
    switch ($orderby) {
        case 'price':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'ASC';
            break;
        case 'price-desc':
            $args['meta_key'] = '_price';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'popularity':
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'rating':
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby'] = 'meta_value_num';
            $args['order'] = 'DESC';
            break;
        case 'date':
            $args['orderby'] = 'date';
            $args['order'] = 'DESC';
            break;
        default:
            $args['orderby'] = 'menu_order';
            $args['order'] = 'ASC';
            break;
    }
    
    // Execute the query
    $query = new WP_Query($args);
    
    // Generate products HTML
    ob_start();
    if ($query->have_posts()) {
        while ($query->have_posts()) {
            $query->the_post();
            render_product_card();
        }
    } else {
        echo '<div class="col-span-full text-center py-12">';
        echo '<div class="text-gray-500 mb-4">';
        echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto mb-4 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
        echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6-4h6m2 5.291A7.962 7.962 0 0112 15c-2.34 0-4.47-.881-6.08-2.33" />';
        echo '</svg>';
        echo '<p class="text-lg font-medium">No products found</p>';
        echo '<p class="text-sm">Try adjusting your filters or search criteria</p>';
        echo '</div>';
        echo '<button onclick="wooFilters.clearAllFilters()" class="bg-[#ed1c24] hover:bg-[#ed1c24]/90 text-white px-6 py-2 rounded-md font-medium transition-colors">';
        echo 'Clear all filters';
        echo '</button>';
        echo '</div>';
    }
    $products_html = ob_get_clean();
    
    // Generate pagination HTML
    $pagination_html = generate_pagination_html($query);
    
    wp_reset_postdata();
    
    wp_send_json_success([
        'products' => $products_html,
        'found_posts' => $query->found_posts,
        'pagination' => $pagination_html
    ]);
}
add_action('wp_ajax_filter_products', 'handle_filter_products');
add_action('wp_ajax_nopriv_filter_products', 'handle_filter_products');

// Helper function to get products matching attribute filters
function get_products_by_attributes($filters) {
    global $wpdb;
    
    $queries = [];
    $all_params = [];
    
    // Build queries for each attribute type
    if (!empty($filters['sizes'])) {
        $size_values = array_map('sanitize_text_field', $filters['sizes']);
        $size_placeholders = implode(',', array_fill(0, count($size_values), '%s'));
        
        $queries[] = "
            SELECT DISTINCT 
                CASE 
                    WHEN p.post_type = 'product_variation' THEN p.post_parent
                    ELSE p.ID
                END as product_id
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
            AND p.post_type IN ('product', 'product_variation')
            AND (
                (pm.meta_key = 'attribute_size' AND pm.meta_value IN ($size_placeholders))
                OR (tt.taxonomy = 'pa_size' AND t.slug IN ($size_placeholders))
            )
        ";
        $all_params = array_merge($all_params, $size_values, $size_values);
    }
    
    if (!empty($filters['colors'])) {
        $color_values = array_map('sanitize_text_field', $filters['colors']);
        $color_placeholders = implode(',', array_fill(0, count($color_values), '%s'));
        
        $queries[] = "
            SELECT DISTINCT 
                CASE 
                    WHEN p.post_type = 'product_variation' THEN p.post_parent
                    ELSE p.ID
                END as product_id
            FROM {$wpdb->posts} p
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
            LEFT JOIN {$wpdb->term_relationships} tr ON p.ID = tr.object_id
            LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
            LEFT JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
            WHERE p.post_status = 'publish'
            AND p.post_type IN ('product', 'product_variation')
            AND (
                (pm.meta_key = 'attribute_color' AND pm.meta_value IN ($color_placeholders))
                OR (tt.taxonomy = 'pa_color' AND t.slug IN ($color_placeholders))
            )
        ";
        $all_params = array_merge($all_params, $color_values, $color_values);
    }
    
    if (empty($queries)) {
        return null;
    }
    
    // If multiple attribute filters, get intersection (products that match ALL filters)
    if (count($queries) > 1) {
        $sql = "
            SELECT product_id 
            FROM (
                (" . implode(') INTERSECT (', $queries) . ")
            ) as matched_products
        ";
    } else {
        // Single attribute filter
        $sql = $queries[0];
    }
    
    // Execute the query
    $product_ids = $wpdb->get_col($wpdb->prepare($sql, $all_params));
    
    // Filter out any invalid IDs and ensure they're integers
    $product_ids = array_filter(array_map('intval', $product_ids));
    
    return $product_ids;
}

// Function to render individual product cards
function render_product_card() {
    global $product;
    
    // Validate product object
    if (!$product || !is_object($product) || !method_exists($product, 'get_id')) {
        return;
    }
    
    $product_id = $product->get_id();
    $product_link = get_permalink($product_id);
    $product_img = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail');
    $product_title = $product->get_name();
    $review_count = $product->get_review_count();
    $average = $product->get_average_rating();
    
    // Determine product badge
    $badge_label = '';
    $badge_class = '';
    if ($product->is_on_sale()) {
        $badge_label = 'SALE';
        $badge_class = 'sale';
    } elseif ((time() - strtotime($product->get_date_created())) < (30 * 24 * 60 * 60)) {
        $badge_label = 'NEW';
        $badge_class = 'new';
    } elseif ($product->get_attribute('pa_limited') || $product->get_attribute('limited')) {
        $badge_label = 'LIMITED';
        $badge_class = 'limited';
    }
    ?>
    
    <div class="rounded-lg overflow-hidden border-none shadow-md group bg-white relative transition-all duration-200 hover:shadow-lg">
        <!-- Product Badge -->
        <?php if ($badge_label) : ?>
            <span class="product-badge <?php echo esc_attr($badge_class); ?>">
                <?php echo esc_html($badge_label); ?>
            </span>
        <?php endif; ?>
        
        <!-- Wishlist Button -->
        <div class="absolute top-4 right-4 z-20">
            <?php if (function_exists('YITH_WCWL')) : ?>
                <?php echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . esc_attr($product_id) . '" label="" browse_wishlist_text="" already_in_wishlist_text="" product_added_text="" show_count="no"]'); ?>
            <?php else : ?>
                <button class="wishlist-fallback bg-white shadow-lg rounded-full p-2 flex items-center justify-center transition hover:bg-gray-100" 
                        onclick="alert('Wishlist functionality requires YITH WooCommerce Wishlist plugin');" 
                        aria-label="Add to wishlist">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                    </svg>
                </button>
            <?php endif; ?>
        </div>
        
        <!-- Product Link -->
        <a href="<?php echo esc_url($product_link); ?>" class="block group/card focus:outline-none" style="text-decoration: none;">
            <div class="relative">
                <!-- Product Image -->
                <div class="aspect-square relative overflow-hidden bg-gray-100 flex items-center justify-center">
                    <?php if ($product_img) : ?>
                        <img src="<?php echo esc_url($product_img); ?>" 
                             alt="<?php echo esc_attr($product_title); ?>" 
                             class="object-cover w-full h-full transition-transform duration-500 group-hover/card:scale-105">
                    <?php else : ?>
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Hover Overlay with Actions -->
                <div class="absolute inset-0 flex flex-col items-center pb-4 justify-end bg-black/60 opacity-0 group-hover/card:opacity-100 transition-opacity z-10">
                    <!-- Quick View Button -->
                    <a href="<?php echo esc_url($product_link); ?>" 
                       class="flex items-center w-56 mb-4 justify-center bg-white text-gray-800 font-medium rounded-lg px-4 py-2 shadow hover:bg-gray-100 text-base gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Quick View
                    </a>
                    
                    <!-- Add to Cart / Select Options Button -->
                    <?php if ($product->get_type() === 'variable') : ?>
                        <a href="<?php echo esc_url($product_link); ?>" 
                           class="bg-[#ed1c24] hover:bg-[#ed1c24]/90 text-white w-56 py-2 px-4 rounded-lg text-base font-medium flex items-center justify-center shadow">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-2">
                                <circle cx="8" cy="21" r="1"></circle>
                                <circle cx="19" cy="21" r="1"></circle>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                            </svg>
                            Select Options
                        </a>
                    <?php else : ?>
                        <button type="button" 
                                class="ajax-add-to-cart bg-[#ed1c24] hover:bg-[#ed1c24]/90 text-white w-56 py-2 px-4 rounded-lg text-base font-medium flex items-center justify-center shadow" 
                                data-product_id="<?php echo esc_attr($product_id); ?>" 
                                data-quantity="1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="h-4 w-4 mr-2">
                                <circle cx="8" cy="21" r="1"></circle>
                                <circle cx="19" cy="21" r="1"></circle>
                                <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                            </svg>
                            <span class="add-to-cart-text">Add to Cart</span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Product Info -->
            <div class="p-6 pt-4">
                <!-- Product Title -->
                <div class="mb-2">
                    <span class="block text-base font-bold font-montserrat <?php echo $product->is_on_sale() ? 'text-[#ed1c24]' : 'text-gray-800'; ?>" style="line-height:1.2;">
                        <?php echo esc_html($product_title); ?>
                    </span>
                </div>
                
                <!-- Product Rating -->
                <div class="flex items-center gap-1 mb-2">
                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                        <?php if ($i <= round($average)) : ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-[#FFD100] text-[#FFD100]" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        <?php else : ?>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                        <?php endif; ?>
                    <?php endfor; ?>
                    <span class="text-xs text-gray-500 ml-1">(<?php echo esc_html($review_count); ?>)</span>
                </div>
                
                <!-- Product Price -->
                <div class="flex items-center gap-2 mt-1">
                    <?php if ($product->is_on_sale()) : ?>
                        <span class="text-lg font-bold text-[#ed1c24]">
                            <?php echo wc_price($product->get_sale_price()); ?>
                        </span>
                        <span class="text-base font-semibold text-gray-400 line-through">
                            <?php echo wc_price($product->get_regular_price()); ?>
                        </span>
                    <?php else : ?>
                        <span class="text-lg font-bold text-gray-800">
                            <?php echo wc_price($product->get_price()); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
        </a>
    </div>
    
    <?php
}

// Generate pagination HTML
function generate_pagination_html($query) {
    if ($query->max_num_pages <= 1) {
        return '';
    }
    
    $current_page = max(1, $query->get('paged'));
    $total_pages = $query->max_num_pages;
    
    $pagination = '<div class="flex items-center gap-2 justify-center">';
    
    // Previous button
    if ($current_page > 1) {
        $pagination .= '<a href="#" class="page-link flex items-center px-3 py-2" data-page="' . ($current_page - 1) . '">';
        $pagination .= '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
        $pagination .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />';
        $pagination .= '</svg>Previous</a>';
    }
    
    // Page numbers
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    // First page + ellipsis
    if ($start > 1) {
        $pagination .= '<a href="#" class="page-link" data-page="1">1</a>';
        if ($start > 2) {
            $pagination .= '<span class="px-3 py-2 text-gray-400">...</span>';
        }
    }
    
    // Page number links
    for ($i = $start; $i <= $end; $i++) {
        $class = $i === $current_page ? 'page-link current' : 'page-link';
        $pagination .= '<a href="#" class="' . $class . '" data-page="' . $i . '">' . $i . '</a>';
    }
    
    // Last page + ellipsis
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $pagination .= '<span class="px-3 py-2 text-gray-400">...</span>';
        }
        $pagination .= '<a href="#" class="page-link" data-page="' . $total_pages . '">' . $total_pages . '</a>';
    }
    
    // Next button
    if ($current_page < $total_pages) {
        $pagination .= '<a href="#" class="page-link flex items-center px-3 py-2" data-page="' . ($current_page + 1) . '">';
        $pagination .= 'Next<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">';
        $pagination .= '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />';
        $pagination .= '</svg></a>';
    }
    
    $pagination .= '</div>';
    
    return $pagination;
}

// Add custom CSS for product badges and other styling
function add_filter_system_styles() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        ?>
        <style>
        /* Product Badge Styles */
        .product-badge {
            position: absolute;
            top: 18px;
            left: 18px;
            z-index: 20;
            padding: 6px 12px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.5px;
            border-radius: 999px;
            color: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            text-transform: uppercase;
            line-height: 1;
        }
        .product-badge.sale { background: #ed1c24; }
        .product-badge.new { background: #2563eb; }
        .product-badge.limited { background: #a259e6; }
        
        /* YITH Wishlist Styling - Heart Icon Only */
        .yith-wcwl-add-to-wishlist,
        .yith-wcwl-add-to-wishlist * {
            font-size: 0 !important;
            color: transparent !important;
            text-indent: -9999px !important;
            line-height: 0 !important;
        }
        .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a,
        .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
        .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            width: 40px !important;
            height: 40px !important;
            background: white !important;
            border-radius: 50% !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10) !important;
            transition: all 0.2s ease !important;
            text-decoration: none !important;
            border: none !important;
            padding: 0 !important;
            margin: 0 !important;
            position: relative;
        }
        .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a:before,
        .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:before,
        .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:before {
            content: '';
            position: absolute;
            width: 24px;
            height: 24px;
            background-size: contain;
            background-repeat: no-repeat;
        }
        .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a:before {
            background-image: url('data:image/svg+xml;utf8,<svg fill="none" stroke="%236b7280" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>');
        }
        .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
        .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a {
            background: #ed1c24 !important;
        }
        .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:before,
        .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:before {
            background-image: url('data:image/svg+xml;utf8,<svg fill="%23ffffff" stroke="%23ffffff" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>');
        }
        .yith-wcwl-add-to-wishlist span,
        .yith-wcwl-add-to-wishlist .yith-wcwl-icon {
            display: none !important;
        }
        </style>
        <?php
    }
}
add_action('wp_head', 'add_filter_system_styles');

// Disable default WooCommerce scripts that might conflict
function disable_conflicting_woo_scripts() {
    if (is_shop() || is_product_category() || is_product_tag()) {
        // Remove default WooCommerce ordering
        remove_action('woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);
        remove_action('woocommerce_before_shop_loop', 'woocommerce_result_count', 20);
    }
}
add_action('init', 'disable_conflicting_woo_scripts');