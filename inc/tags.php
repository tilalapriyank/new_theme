<?php
/**
 * Tag functionality for the theme
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register tag color meta field
 */
function hype_pups_register_tag_meta() {
    register_term_meta('post_tag', 'tag_color', array(
        'type' => 'string',
        'description' => 'Tag color for styling',
        'single' => true,
        'show_in_rest' => true,
        'default' => '#FF3A5E'
    ));
}
add_action('init', 'hype_pups_register_tag_meta');

/**
 * Add color picker to tag edit screen
 */
function hype_pups_add_tag_color_field($term) {
    $color = get_term_meta($term->term_id, 'tag_color', true) ?: '#FF3A5E';
    ?>
    <tr class="form-field">
        <th scope="row">
            <label for="tag_color"><?php _e('Tag Color', 'hype-pups'); ?></label>
        </th>
        <td>
            <input type="color" name="tag_color" id="tag_color" value="<?php echo esc_attr($color); ?>" />
            <p class="description"><?php _e('Choose a color for this tag', 'hype-pups'); ?></p>
        </td>
    </tr>
    <?php
}
add_action('post_tag_edit_form_fields', 'hype_pups_add_tag_color_field');

/**
 * Save tag color
 */
function hype_pups_save_tag_color($term_id) {
    if (isset($_POST['tag_color'])) {
        update_term_meta($term_id, 'tag_color', sanitize_hex_color($_POST['tag_color']));
    }
}
add_action('edited_post_tag', 'hype_pups_save_tag_color');
add_action('created_post_tag', 'hype_pups_save_tag_color');

/**
 * Add color picker to new tag form
 */
function hype_pups_add_tag_color_field_new() {
    ?>
    <div class="form-field">
        <label for="tag_color"><?php _e('Tag Color', 'hype-pups'); ?></label>
        <input type="color" name="tag_color" id="tag_color" value="#FF3A5E" />
        <p><?php _e('Choose a color for this tag', 'hype-pups'); ?></p>
    </div>
    <?php
}
add_action('post_tag_add_form_fields', 'hype_pups_add_tag_color_field_new');

/**
 * Enqueue color picker scripts
 */
function hype_pups_enqueue_tag_color_picker($taxonomy) {
    if ($taxonomy !== 'post_tag') {
        return;
    }
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    wp_add_inline_script('wp-color-picker', '
        jQuery(document).ready(function($) {
            $("#tag_color").wpColorPicker();
        });
    ');
}
add_action('admin_enqueue_scripts', function() {
    $screen = get_current_screen();
    if ($screen && $screen->base === 'edit-tags' && $screen->taxonomy === 'post_tag') {
        hype_pups_enqueue_tag_color_picker('post_tag');
    }
}); 