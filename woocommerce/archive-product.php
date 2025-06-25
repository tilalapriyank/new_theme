<?php
/**
 * The Template for displaying product archives, including the main shop page
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined('ABSPATH') || exit;

// Remove the problematic template redirect hook and replace with proper query modification
remove_action('template_redirect', 'handle_product_cat_array');

// Properly handle multiple category selection in WooCommerce
if (!function_exists('sanitize_text_field')) {
    require_once ABSPATH . 'wp-includes/formatting.php';
}


get_header();
?>

<script>
// Add Alpine.js data for mobile filters
document.addEventListener('alpine:init', () => {
    Alpine.data('shopFilters', () => ({
        mobileFiltersOpen: false
    }))
});

document.addEventListener('DOMContentLoaded', function() {
  document.body.addEventListener('click', function(e) {
    var heartLink = e.target.closest('.yith-wcwl-wishlistaddedbrowse a, .yith-wcwl-wishlistexistsbrowse a');
    if (heartLink) {
      e.preventDefault();
      e.stopPropagation();
      // Try to get product ID from data attributes or parent
      var productId = heartLink.getAttribute('data-product-id') ||
                      (heartLink.closest('[data-product-id]') ? heartLink.closest('[data-product-id]').getAttribute('data-product-id') : null);
      if (!productId) {
        // Try to extract from href (e.g., ...?add_to_wishlist=123)
        var match = heartLink.href.match(/add_to_wishlist=(\d+)/);
        if (match) productId = match[1];
      }
      if (productId && typeof jQuery !== 'undefined' && typeof window.yith_wcwl_ajax_remove_product === 'function') {
        // Use YITH's AJAX remove function if available
        window.yith_wcwl_ajax_remove_product(productId);
      } else if (productId && typeof jQuery !== 'undefined' && typeof window.yith_wcwl_l10n !== 'undefined') {
        // Fallback: Use YITH's AJAX endpoint
        jQuery.post(
          window.yith_wcwl_l10n.ajax_url,
          {
            action: 'yith_wcwl_remove_product',
            add_to_wishlist: productId,
            product_id: productId,
            wishlist_id: false
          },
          function(response) {
            // Optionally, refresh the wishlist icon
            location.reload();
          }
        );
      } else {
        // As a last resort, reload the page
        location.reload();
      }
    }
  }, true);
});
</script>

<main id="main-content" class="py-12 md:pt-32 md:pb-16" x-data="shopFilters">
    <div class="container mx-auto md:px-32">
        <!-- Page Header -->
        <div class="mb-8">
            <?php if (is_product_category()) : ?>
                <?php
                $current_category = get_queried_object();
                $category_name = $current_category->name;
                $category_description = $current_category->description;
                ?>
                <h1 class="text-3xl font-bold mb-2"><?php echo esc_html($category_name); ?></h1>
                <?php if ($category_description) : ?>
                    <p class="text-gray-600 mb-4"><?php echo esc_html($category_description); ?></p>
                <?php endif; ?>
                <div class="flex items-center text-sm text-gray-500">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-[#ed1c24]">
                        Home
                    </a>
                    <span class="mx-2">/</span>
                    <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="hover:text-[#ed1c24]">
                        Shop
                    </a>
                    <span class="mx-2">/</span>
                    <span><?php echo esc_html($category_name); ?></span>
                </div>
            <?php else : ?>
                <h1 class="text-3xl font-bold mb-2">Shop All Products</h1>
                <div class="flex items-center text-sm text-gray-500">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-[#ed1c24]">
                        Home
                    </a>
                    <span class="mx-2">/</span>
                    <span>Shop</span>
                </div>
            <?php endif; ?>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Mobile Filter Toggle -->
            <div class="lg:hidden w-full mb-4">
                <button
                    @click="mobileFiltersOpen = !mobileFiltersOpen"
                    class="w-full flex items-center justify-between border border-gray-300 rounded-md px-4 py-2 bg-white"
                >
                    <span class="flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        Filters
                    </span>
                    <svg 
                        xmlns="http://www.w3.org/2000/svg" 
                        class="h-4 w-4 transition-transform" 
                        :class="mobileFiltersOpen ? 'rotate-180' : ''" 
                        fill="none" 
                        viewBox="0 0 24 24" 
                        stroke="currentColor"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>

            <!-- Filters - Left Side -->
            <div 
                class="lg:w-1/4" 
                :class="mobileFiltersOpen ? 'block' : 'hidden lg:block'"
            >
                <div class="bg-white rounded-lg border border-gray-200 p-6 sticky top-24">
                    <form method="get" id="shop-filters-form" action="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>">
                        <!-- Preserve existing query parameters -->
                        <?php
                        $current_url_params = $_GET;
                        foreach ($current_url_params as $key => $value) {
                            if (!in_array($key, array('category', 'min_price', 'max_price', 'filter_pa_size', 'filter_pa_color'))) {
                                if (is_array($value)) {
                                    foreach ($value as $v) {
                                        echo '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($v) . '">';
                                    }
                                } else {
                                    echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                                }
                            }
                        }
                        
                        // Add current category as hidden input if on category page
                        if (is_product_category()) {
                            $current_category = get_queried_object();
                            echo '<input type="hidden" name="category" value="' . esc_attr($current_category->slug) . '">';
                        }
                        ?>
                        
                        <div class="mb-6">
                            <h3 class="font-bold mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                Filter Products
                            </h3>
                            <?php if (!is_product_category()) : ?>
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-medium mb-3">Categories</h4>
                                <?php
                                $product_categories = get_terms(array(
                                    'taxonomy' => 'product_cat',
                                    'hide_empty' => true,
                                    'parent' => 0
                                ));
                                $selected_cats = array();
                                if (isset($_GET['category'])) {
                                    if (is_array($_GET['category'])) {
                                        $selected_cats = array_map('sanitize_text_field', $_GET['category']);
                                    } else {
                                        $selected_cats = explode(',', sanitize_text_field($_GET['category']));
                                    }
                                }
                                
                                // If we're on a category page and no categories are selected, pre-select the current category
                                if (is_product_category() && empty($selected_cats)) {
                                    $current_category = get_queried_object();
                                    $selected_cats[] = $current_category->slug;
                                }
                                
                                $cat_count = count($product_categories);
                                ?>
                                <div class="space-y-2 <?php if ($cat_count > 5) echo 'overflow-y-auto'; ?>" style="<?php if ($cat_count > 5) echo 'max-height: 200px;'; ?>">
                                    <?php foreach ($product_categories as $category) :
                                        $count = $category->count;
                                    ?>
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                id="category-<?php echo esc_attr($category->slug); ?>" 
                                                name="category[]"
                                                value="<?php echo esc_attr($category->slug); ?>"
                                                class="rounded border-gray-300 text-[#ed1c24] focus:ring-[#ed1c24]"
                                                <?php checked(in_array($category->slug, $selected_cats)); ?>
                                                onchange="submitFilters()"
                                            >
                                            <label for="category-<?php echo esc_attr($category->slug); ?>" class="ml-2 text-sm flex-grow">
                                                <?php echo esc_html($category->name); ?>
                                            </label>
                                            <span class="text-xs text-gray-500">(<?php echo esc_html($count); ?>)</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-6">
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-medium mb-3">Price Range</h4>
                                <div class="px-2">
                                    <?php
                                    // Get min and max prices from products
                                    global $wpdb;
                                    $price_range = $wpdb->get_row("
                                        SELECT 
                                            MIN(CAST(price_meta.meta_value AS DECIMAL)) as min_price,
                                            MAX(CAST(price_meta.meta_value AS DECIMAL)) as max_price
                                        FROM {$wpdb->posts} as posts
                                        INNER JOIN {$wpdb->postmeta} as price_meta ON posts.ID = price_meta.post_id
                                        WHERE posts.post_type = 'product'
                                        AND posts.post_status = 'publish'
                                        AND price_meta.meta_key = '_price'
                                    ");

                                    $min_price = floor($price_range->min_price);
                                    $max_price = ceil($price_range->max_price);
                                    $current_min = isset($_GET['min_price']) ? intval($_GET['min_price']) : $min_price;
                                    $current_max = isset($_GET['max_price']) ? intval($_GET['max_price']) : $max_price;
                                    ?>
                                    <!-- noUiSlider container -->
                                    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.css">
                                    <div id="price-slider"></div>
                                    <div class="flex items-center justify-between mt-2">
                                        <span id="slider-min" class="text-sm">$<?php echo esc_html($current_min); ?></span>
                                        <span id="slider-max" class="text-sm">$<?php echo esc_html($current_max); ?></span>
                                    </div>
                                    <input type="hidden" name="min_price" id="min_price" value="<?php echo esc_attr($current_min); ?>">
                                    <input type="hidden" name="max_price" id="max_price" value="<?php echo esc_attr($current_max); ?>">
                                    <style>
                                    /* noUiSlider custom design for WooCommerce price filter */
                                    #price-slider .noUi-base {
                                        background: transparent;
                                    }
                                    #price-slider .noUi-target {
                                        background: #f3f4f6;
                                        border-radius: 999px;
                                        border: none;
                                        box-shadow: none;
                                        height: 8px;
                                    }
                                    #price-slider .noUi-connect {
                                        background: #ed1c24;
                                        border-radius: 999px;
                                    }
                                    #price-slider .noUi-horizontal .noUi-handle {
                                        width: 24px;
                                        height: 24px;
                                        border-radius: 50%;
                                        background: #fff;
                                        border: 4px solid #ed1c24;
                                        box-shadow: 0 2px 8px rgba(237, 28, 36, 0.10);
                                        top: -8px;
                                        cursor: pointer;
                                        transition: border 0.2s, box-shadow 0.2s;
                                    }
                                    #price-slider .noUi-handle:after,
                                    #price-slider .noUi-handle:before {
                                        display: none;
                                    }
                                    #price-slider .noUi-handle:hover,
                                    #price-slider .noUi-handle:focus {
                                        border-color: #ed1c24;
                                        box-shadow: 0 0 0 4px rgba(237, 28, 36, 0.15);
                                    }
                                    #price-slider .noUi-tooltip {
                                        display: none;
                                    }
                                    #price-slider .noUi-handle.noUi-handle-upper,
                                    #price-slider .noUi-handle.noUi-handle-lower{
                                        border-radius: 20px;
                                        width: 28px !important;
                                    }
                                    </style>
                                    <script src="https://cdn.jsdelivr.net/npm/nouislider@15.7.1/dist/nouislider.min.js"></script>
                                    <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        var priceSlider = document.getElementById('price-slider');
                                        if (priceSlider) {
                                            var minPrice = <?php echo $min_price; ?>;
                                            var maxPrice = <?php echo $max_price; ?>;
                                            var startMin = <?php echo $current_min; ?>;
                                            var startMax = <?php echo $current_max; ?>;

                                            noUiSlider.create(priceSlider, {
                                                start: [startMin, startMax],
                                                connect: true,
                                                step: 1,
                                                range: {
                                                    'min': minPrice,
                                                    'max': maxPrice
                                                },
                                                format: {
                                                    to: function (value) { return Math.round(value); },
                                                    from: function (value) { return Number(value); }
                                                }
                                            });

                                            var minInput = document.getElementById('min_price');
                                            var maxInput = document.getElementById('max_price');
                                            var minLabel = document.getElementById('slider-min');
                                            var maxLabel = document.getElementById('slider-max');

                                            priceSlider.noUiSlider.on('update', function(values, handle) {
                                                minInput.value = values[0];
                                                maxInput.value = values[1];
                                                minLabel.textContent = '$' + values[0];
                                                maxLabel.textContent = '$' + values[1];
                                            });

                                            priceSlider.noUiSlider.on('change', function() {
                                                debouncedPriceSubmit();
                                            });
                                        }
                                    });
                                    </script>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-medium mb-3">Size</h4>
                                <div class="scrollable-size-grid">
                                    <?php
                                    // Get all sizes using the comprehensive function
                                    global $wpdb;
                                    $all_sizes = [];

                                    // 1. Get global attribute terms (pa_size)
                                    $global_terms = get_terms([
                                        'taxonomy'   => 'pa_size',
                                        'hide_empty' => false,
                                    ]);

                                    foreach ($global_terms as $term) {
                                        $all_sizes[$term->slug] = [
                                            'name' => $term->name,
                                            'type' => 'global',
                                            'count' => $term->count
                                        ];
                                    }

                                    // 2. Get custom attributes from _product_attributes in postmeta
                                    $product_ids = $wpdb->get_col("
                                        SELECT ID FROM {$wpdb->posts}
                                        WHERE post_type = 'product' AND post_status = 'publish'
                                    ");

                                    foreach ($product_ids as $product_id) {
                                        $attributes = get_post_meta($product_id, '_product_attributes', true);

                                        if (!empty($attributes)) {
                                            foreach ($attributes as $attribute_name => $attribute_data) {
                                                // Match if the attribute is "size" (non-taxonomy)
                                                if (strtolower($attribute_name) === 'size' && empty($attribute_data['is_taxonomy'])) {
                                                    $options = explode('|', $attribute_data['value']);
                                                    foreach ($options as $opt) {
                                                        $opt = trim($opt);
                                                        $slug = sanitize_title($opt);
                                                        if (!isset($all_sizes[$slug])) {
                                                            $all_sizes[$slug] = [
                                                                'name' => $opt,
                                                                'type' => 'custom',
                                                                'count' => 0
                                                            ];
                                                        }
                                                        $all_sizes[$slug]['count']++;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $selected_sizes = isset($_GET['filter_pa_size']) ? explode(',', sanitize_text_field($_GET['filter_pa_size'])) : array();
                                    
                                    if (!empty($all_sizes)) :
                                        foreach ($all_sizes as $slug => $size_data) :
                                    ?>
                                            <label
                                                class="size-option <?php if (in_array($slug, $selected_sizes)) echo ' selected'; ?>"
                                                title="<?php echo esc_attr($size_data['name']); ?> (<?php echo esc_attr($size_data['count']); ?> products)"
                                            >
                                                <input 
                                                    type="checkbox" 
                                                    name="filter_pa_size"
                                                    value="<?php echo esc_attr($slug); ?>" 
                                                    class="sr-only"
                                                    <?php checked(in_array($slug, $selected_sizes)); ?>
                                                    onchange="submitFilters()"
                                                >
                                                <span class="truncate" style="max-width: 80px; display: inline-block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo esc_attr($size_data['name']); ?>">
                                                    <?php echo esc_html($size_data['name']); ?>
                                                </span>
                                            </label>
                                    <?php 
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-medium mb-3">Color</h4>
                                <div class="scrollable-color-grid">
                                    <?php
                                    // Get all colors using the comprehensive function
                                    global $wpdb;
                                    $all_colors = [];

                                    // 1. Get global attribute terms (pa_color)
                                    $global_terms = get_terms([
                                        'taxonomy'   => 'pa_color',
                                        'hide_empty' => false,
                                    ]);

                                    foreach ($global_terms as $term) {
                                        $all_colors[$term->slug] = [
                                            'name' => $term->name,
                                            'type' => 'global',
                                            'count' => $term->count
                                        ];
                                    }

                                    // 2. Get custom attributes from _product_attributes in postmeta
                                    $product_ids = $wpdb->get_col("
                                        SELECT ID FROM {$wpdb->posts}
                                        WHERE post_type = 'product' AND post_status = 'publish'
                                    ");

                                    foreach ($product_ids as $product_id) {
                                        $attributes = get_post_meta($product_id, '_product_attributes', true);

                                        if (!empty($attributes)) {
                                            foreach ($attributes as $attribute_name => $attribute_data) {
                                                // Match if the attribute is "color" (non-taxonomy)
                                                if (strtolower($attribute_name) === 'color' && empty($attribute_data['is_taxonomy'])) {
                                                    $options = explode('|', $attribute_data['value']);
                                                    foreach ($options as $opt) {
                                                        $opt = trim($opt);
                                                        $slug = sanitize_title($opt);
                                                        if (!isset($all_colors[$slug])) {
                                                            $all_colors[$slug] = [
                                                                'name' => $opt,
                                                                'type' => 'custom',
                                                                'count' => 0
                                                            ];
                                                        }
                                                        $all_colors[$slug]['count']++;
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    $selected_colors = isset($_GET['filter_pa_color']) ? explode(',', sanitize_text_field($_GET['filter_pa_color'])) : array();
                                    
                                    if (!empty($all_colors)) :
                                        foreach ($all_colors as $slug => $color_data) :
                                            $color_class = '';
                                            $color_class = strtolower($slug);
                                        
                                            
                                    ?>
                                            <label
                                                class="color-option <?php if (in_array($slug, $selected_colors)) echo ' selected'; ?>"
                                                title="<?php echo esc_attr($color_data['name']); ?> (<?php echo esc_attr($color_data['count']); ?> products)"
                                            >
                                                <input 
                                                    type="checkbox" 
                                                    name="filter_pa_color"
                                                    value="<?php echo esc_attr($slug); ?>" 
                                                    class="sr-only"
                                                    <?php checked(in_array($slug, $selected_colors)); ?>
                                                    onchange="submitFilters()"
                                                >
                                                <div class="flex flex-col items-center gap-1">
                                                    <div style="background-color: <?php echo esc_attr($color_class); ?>;" class="w-8 h-8 rounded-full hover:ring-2 hover:ring-[#ed1c24] hover:ring-offset-2 <?php if (in_array($slug, $selected_colors)) echo 'ring-2 ring-[#ed1c24]'; ?>"></div>
                                                    <span class="text-xs font-medium <?php if (in_array($slug, $selected_colors)) echo 'text-[#ed1c24]'; ?>"><?php echo esc_html($color_data['name']); ?></span>
                                                </div>
                                            </label>
                                    <?php 
                                        endforeach;
                                    endif;
                                    ?>
                                </div>
                            </div>
                        </div>

                        <button 
                            type="submit"
                            class="w-full bg-[#ed1c24] hover:bg-[#ed1c24]/90 text-white font-medium px-4 py-2 rounded-md mb-3"
                            onclick="event.preventDefault(); submitFilters();"
                        >
                            Apply Filters
                        </button>
                        <?php if (is_product_category()) : ?>
                            <a href="<?php echo esc_url(get_term_link(get_queried_object())); ?>" class="w-full border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-md text-center block">
                                Reset Filters
                            </a>
                        <?php else : ?>
                            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="w-full border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-md text-center block">
                                Reset Filters
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
<?php
  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    
  // Build query arguments
  $args = array(
      'post_type' => 'product',
      'post_status' => 'publish',
      'posts_per_page' => 12, // Adjust as needed
      'paged' => $paged,
      'meta_query' => array(),
      'tax_query' => array(),
  );
  
  // Handle Product Categories
  if (!empty($_GET['category'])) {
      $categories = is_array($_GET['category']) ? $_GET['category'] : explode(',', $_GET['category']);
      $categories = array_map('sanitize_title', array_filter($categories));
      
      if (!empty($categories)) {
          $args['tax_query'][] = array(
              'taxonomy' => 'product_cat',
              'field'    => 'slug',
              'terms'    => $categories,
              'operator' => 'IN',
          );
      }
  }
  
  // Handle Price Filters
  if (!empty($_GET['min_price']) && is_numeric($_GET['min_price'])) {
      $args['meta_query'][] = array(
          'key'     => '_price',
          'value'   => floatval($_GET['min_price']),
          'compare' => '>=',
          'type'    => 'NUMERIC',
      );
  }
  
  if (!empty($_GET['max_price']) && is_numeric($_GET['max_price'])) {
      $args['meta_query'][] = array(
          'key'     => '_price',
          'value'   => floatval($_GET['max_price']),
          'compare' => '<=',
          'type'    => 'NUMERIC',
      );
  }
  
  // Handle Attribute Filters (Color & Size)
  if (!empty($_GET['filter_pa_color']) || !empty($_GET['filter_pa_size'])) {
      global $wpdb;
      
      $color_filter = !empty($_GET['filter_pa_color']) ? array_map('sanitize_text_field', array_filter(explode(',', $_GET['filter_pa_color']))) : [];
      $size_filter = !empty($_GET['filter_pa_size']) ? array_map('sanitize_text_field', array_filter(explode(',', $_GET['filter_pa_size']))) : [];
      
      if (!empty($color_filter) || !empty($size_filter)) {
          $attribute_queries = [];
          $all_params = [];
          
          // Build individual attribute queries
          if (!empty($color_filter)) {
              $color_placeholders = implode(',', array_fill(0, count($color_filter), '%s'));
              $attribute_queries[] = "
                  SELECT DISTINCT pv.post_parent as product_id
                  FROM {$wpdb->posts} pv
                  INNER JOIN {$wpdb->postmeta} pm ON pv.ID = pm.post_id
                  INNER JOIN {$wpdb->posts} pp ON pv.post_parent = pp.ID
                  WHERE pv.post_type = 'product_variation'
                  AND pv.post_status = 'publish'
                  AND pp.post_type = 'product'
                  AND pp.post_status = 'publish'
                  AND pm.meta_key = 'attribute_color'
                  AND pm.meta_value IN ($color_placeholders)
                  AND pv.post_parent > 0
              ";
              $all_params = array_merge($all_params, $color_filter);
          }
          
          if (!empty($size_filter)) {
              $size_placeholders = implode(',', array_fill(0, count($size_filter), '%s'));
              $attribute_queries[] = "
                  SELECT DISTINCT pv.post_parent as product_id
                  FROM {$wpdb->posts} pv
                  INNER JOIN {$wpdb->postmeta} pm ON pv.ID = pm.post_id
                  INNER JOIN {$wpdb->posts} pp ON pv.post_parent = pp.ID
                  WHERE pv.post_type = 'product_variation'
                  AND pv.post_status = 'publish'
                  AND pp.post_type = 'product'
                  AND pp.post_status = 'publish'
                  AND pm.meta_key = 'attribute_size'
                  AND pm.meta_value IN ($size_placeholders)
                  AND pv.post_parent > 0
              ";
              $all_params = array_merge($all_params, $size_filter);
          }
          
          // If both filters are applied, we need products that match BOTH attributes
          if (!empty($color_filter) && !empty($size_filter)) {
              $sql = "
                  SELECT product_id
                  FROM (
                      (" . $attribute_queries[0] . ")
                      INTERSECT
                      (" . $attribute_queries[1] . ")
                  ) as matched_products
              ";
          } else {
              // If only one filter is applied
              $sql = "
                  SELECT DISTINCT product_id
                  FROM (
                      " . implode(' UNION ', $attribute_queries) . "
                  ) as matched_products
              ";
          }
          
          $matched_ids = $wpdb->get_col($wpdb->prepare($sql, $all_params));
          
          if (!empty($matched_ids)) {
              $args['post__in'] = array_map('intval', $matched_ids);
          } else {
              // No matches found
              $args['post__in'] = array(0);
          }
      }
  }
  
  // Handle Sorting
  if (!empty($_GET['orderby'])) {
      $orderby = sanitize_text_field($_GET['orderby']);
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
              // Handle default WooCommerce sorting
              break;
      }
  }
  
  // Set proper relations for meta and tax queries
  if (count($args['meta_query']) > 1) {
      $args['meta_query']['relation'] = 'AND';
  }
  if (count($args['tax_query']) > 1) {
      $args['tax_query']['relation'] = 'AND';
  }
  
  // Remove empty arrays
  if (empty($args['meta_query'])) {
      unset($args['meta_query']);
  }
  if (empty($args['tax_query'])) {
      unset($args['tax_query']);
  }
  
  // Execute custom query
  $products_query = new WP_Query($args);
?>
            <!-- Products - Right Side -->
            <div class="lg:w-3/4">
                <!-- Sort and View Options -->
                <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                    <p class="text-sm text-gray-500">
                    <?php echo esc_html($products_query->found_posts) . ' products'; ?>
                    </p>
                    <div class="flex items-center gap-4">
                        <form class="woocommerce-ordering" method="get">
                            <?php
                            // Preserve current filters in sort form
                            foreach ($_GET as $key => $value) {
                                if ($key !== 'orderby' && $key !== 'order') {
                                    if (is_array($value)) {
                                        foreach ($value as $v) {
                                            echo '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($v) . '">';
                                        }
                                    } else {
                                        echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                                    }
                                }
                            }
                            woocommerce_catalog_ordering(); 
                            ?>
                        </form>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <?php
                    // Custom query for products with filtering
                    if ($products_query->have_posts()) :
                        while ($products_query->have_posts()) : $products_query->the_post();
                            global $product;
                            // Check if $product is valid before proceeding
                            if (!$product || !is_object($product) || !method_exists($product, 'get_id')) {
                                continue; // Skip this iteration if product is invalid
                            }
                            $product_id = $product->get_id();
                            $product_link = get_permalink($product_id);
                            $product_img = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail');
                            $product_title = $product->get_name();
                            $product_price = $product->get_price_html();
                            $review_count = $product->get_review_count();
                            $average = $product->get_average_rating();
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
                                <!-- Badge -->
                                <?php if ($badge_label) : ?>
                                    <span class="product-badge <?php echo esc_attr($badge_class); ?>">
                                        <?php echo esc_html($badge_label); ?>
                                    </span>
                                <?php endif; ?>
                                <!-- Wishlist Icon -->
                                <div class="absolute top-4 right-4 z-20">
                                    <?php if (function_exists('YITH_WCWL') || function_exists('yith_wcwl_add_to_wishlist')) : ?>
                                        <?php echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . esc_attr($product_id) . '" label="" browse_wishlist_text="" already_in_wishlist_text="" product_added_text="" show_count="no"]'); ?>
                                    <?php else : ?>
                                        <button
                                            class="wishlist-fallback bg-white shadow-lg rounded-full p-2 flex items-center justify-center transition hover:bg-gray-100"
                                            onclick="event.preventDefault(); showWishlistMessage();"
                                            aria-label="Add to wishlist"
                                            style="box-shadow:0 2px 8px rgba(0,0,0,0.10);"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                            </svg>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <!-- Card as Link (everything except overlay and wishlist) -->
                                <a href="<?php echo esc_url($product_link); ?>" class="block group/card focus:outline-none" tabindex="0" style="text-decoration:none;">
                                    <div class="relative">
                                        <div class="aspect-square relative overflow-hidden bg-gray-100 flex items-center justify-center">
                                            <?php if ($product_img) : ?>
                                                <img 
                                                    src="<?php echo esc_url($product_img); ?>" 
                                                    alt="<?php echo esc_attr($product_title); ?>" 
                                                    class="object-cover w-full h-full transition-transform duration-500 group-hover/card:scale-105"
                                                >
                                            <?php else : ?>
                                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-gray-400">No Image</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <!-- Overlay Actions -->
                                        <div class="absolute inset-0 flex flex-col items-center pb-4 justify-end bg-black/60 opacity-0 group-hover/card:opacity-100 transition-opacity z-10">
                                            <a href="#"
                                                    class="flex items-center w-56 mb-4 justify-center bg-white text-gray-800 font-medium rounded-lg px-4 py-2 shadow hover:bg-gray-100 text-base gap-2 quick-view-btn"
                                                    data-product-id="<?php echo esc_attr($product_id); ?>"
                                                    data-product-url="<?php echo esc_url($product_link); ?>">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Quick View
                                                </a>
                                            <?php if ($product->get_type() === 'variable') : ?>
                                                <a
                                                    href="<?php echo esc_url($product_link); ?>"
                                                    class="bg-[#ed1c24] hover:bg-[#ed1c24]/90 text-white w-56 py-2 px-4 rounded-lg text-base font-medium flex items-center justify-center shadow"
                                                    style="box-shadow:0 2px 8px rgba(0,0,0,0.10);"
                                                >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-4 w-4 mr-2" __v0_r="0,5832,5846"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                                                    Select Options
                                                </a>
                                            <?php else : ?>
                                                <button
                                                    type="button"
                                                    class="ajax-add-to-cart bg-[#ed1c24] hover:bg-[#ed1c24]/90 text-white w-56 py-2 px-4 rounded-lg text-base font-medium flex items-center justify-center shadow"
                                                    data-product_id="<?php echo esc_attr($product_id); ?>"
                                                    data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                                                    data-quantity="1"
                                                    style="box-shadow:0 2px 8px rgba(0,0,0,0.10);"
                                                >
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-4 w-4 mr-2" __v0_r="0,5832,5846"><circle cx="8" cy="21" r="1"></circle><circle cx="19" cy="21" r="1"></circle><path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path></svg>
                                                    <span class="add-to-cart-text">Add to Cart</span>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <div class="p-6 pt-4">
                                        <div class="mb-2">
                                            <span class="block text-base font-bold font-montserrat <?php echo $product->is_on_sale() ? 'text-[#ed1c24]' : 'text-gray-800'; ?>" style="line-height:1.2;">
                                                <?php echo esc_html($product_title); ?>
                                            </span>
                                        </div>
                                        <div class="flex items-center gap-1 mb-2">
                                            <?php
                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= round($average)) {
                                                    echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 fill-[#FFD100] text-[#FFD100]" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
                                                } else {
                                                    echo '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-300" viewBox="0 0 20 20" fill="currentColor"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>';
                                                }
                                            }
                                            ?>
                                            <span class="text-xs text-gray-500 ml-1">(<?php echo esc_html($review_count); ?>)</span>
                                        </div>
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
                        endwhile;
                        
                        // Reset post data
                        wp_reset_postdata();
                        
                    else :
                    ?>
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500">No products found matching your criteria.</p>
                            <?php if (is_product_category()) : ?>
                                <a href="<?php echo esc_url(get_term_link(get_queried_object())); ?>" class="text-[#ed1c24] hover:underline mt-2 inline-block">Clear all filters</a>
                            <?php else : ?>
                                <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="text-[#ed1c24] hover:underline mt-2 inline-block">Clear all filters</a>
                            <?php endif; ?>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>

                <!-- Pagination -->
                <?php if ($products_query->have_posts()) : ?>
                <div class="flex justify-center mt-12">
                    <?php
                    // Custom pagination for our custom query
                    $big = 999999999;
                    echo paginate_links(array(
                        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
                        'format' => '?paged=%#%',
                        'current' => max(1, get_query_var('paged')),
                        'total' => $products_query->max_num_pages,
                        'prev_text' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>',
                        'next_text' => '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>',
                        'type' => 'list',
                        'class' => 'flex items-center gap-2'
                    ));
                    ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Toast Notification -->
<div id="toast" class="fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-md opacity-0 transition-opacity duration-300 z-50">
    Product added to cart
</div>

<script>
// Global variables for debouncing
let filterTimeout;
let priceTimeout;

// Auto-submit form when filters change
function submitFilters() {
    const form = document.getElementById('shop-filters-form');
    if (!form) return;
    
    // Prevent default form submission
    event.preventDefault();
    
    // Get all form data
    const formData = new FormData(form);
    
    // Build query string
    const params = new URLSearchParams();
    
    // Handle product categories separately
    const categories = [];
    const sizes = [];
    const colors = [];
    for (let [key, value] of formData.entries()) {
        if (key === 'category[]') {
            categories.push(value);
        } else if (key === 'filter_pa_size') {
            sizes.push(value);
        } else if (key === 'filter_pa_color') {
            colors.push(value);
        } else if (value) {
            params.append(key, value);
        }
    }
    
    // Add categories as a single string parameter
    if (categories.length > 0) {
        params.append('category', categories.join(','));
    }
    
    // Add sizes as a single comma-separated string
    if (sizes.length > 0) {
        params.append('filter_pa_size', sizes.join(','));
    }
    
    // Add colors as a single comma-separated string
    if (colors.length > 0) {
        params.append('filter_pa_color', colors.join(','));
    }
    
    // Get current URL and preserve the base path
    let currentUrl = window.location.href;
    let baseUrl;
    
    // Check if we're on a category page or shop page
    if (currentUrl.includes('/product-category/')) {
        // We're on a category page, keep the category URL structure
        baseUrl = currentUrl.split('?')[0];
    } else if (currentUrl.includes('/shop/') || currentUrl.includes('/shop')) {
        // We're on the shop page
        baseUrl = currentUrl.split('?')[0];
    } else {
        // Fallback to shop page
        baseUrl = window.location.origin + '/shop/';
    }
    
    // Remove paged parameter if it exists (reset to page 1 when filtering)
    params.delete('paged');
    
    // Build the new URL
    const newUrl = params.toString() ? `${baseUrl}?${params.toString()}` : baseUrl;
    
    // Navigate to the new URL
    window.location.href = newUrl;
}

// Update price display in real-time
function updatePriceDisplay(input) {
    const output = document.getElementById('price-output');
    if (output) {
        output.textContent = '$' + input.value;
    }
}

// Debounced price submission to avoid too many requests
function debouncedPriceSubmit() {
    clearTimeout(priceTimeout);
    priceTimeout = setTimeout(function() {
        submitFilters();
    }, 500); // Wait 500ms after user stops moving the slider
}

// Toast notification function
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    if (!toast) return;
    
    // Set message and styling
    toast.textContent = message;
    toast.className = `fixed top-4 right-4 text-white px-6 py-3 rounded-md transition-opacity duration-300 z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    
    // Show toast
    toast.style.opacity = '1';
    
    // Hide toast after 3 seconds
    setTimeout(() => {
        toast.style.opacity = '0';
    }, 3000);
}

// FIXED: Custom AJAX Add to Cart for Simple Products
function handleAjaxAddToCart() {
    const buttons = document.querySelectorAll('.ajax-add-to-cart');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const productId = this.getAttribute('data-product_id');
            const quantity = this.getAttribute('data-quantity') || 1;
            const textElement = this.querySelector('.add-to-cart-text');
            const originalText = textElement.textContent;
            
            // Show loading state
            this.classList.add('loading');
            this.disabled = true;
            textElement.textContent = 'Adding...';
            
            // Prepare form data
            const formData = new FormData();
            formData.append('action', 'woocommerce_add_to_cart');
            formData.append('product_id', productId);
            formData.append('quantity', quantity);
            formData.append('add-to-cart', productId);
            
            // Make AJAX request
            fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.error && data.product_url) {
                    // If there's an error, redirect to product page
                    window.location.href = data.product_url;
                    return;
                }
                
                // Success - update cart fragments if available
                if (data.fragments) {
                    // Update cart fragments
                    Object.keys(data.fragments).forEach(key => {
                        const element = document.querySelector(key);
                        if (element) {
                            element.innerHTML = data.fragments[key];
                        }
                    });
                }
                
                // Show success state
                this.classList.remove('loading');
                this.classList.add('added');
                textElement.textContent = 'Added!';
                
                // Show toast notification
                showToast('Product added to cart!', 'success');
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    this.classList.remove('added');
                    this.disabled = false;
                    textElement.textContent = originalText;
                }, 2000);
                
                // Trigger WooCommerce events if jQuery is available
                if (typeof jQuery !== 'undefined') {
                    jQuery('body').trigger('added_to_cart', [data.fragments, data.cart_hash, this]);
                }
            })
            .catch(error => {
                console.error('Add to cart error:', error);
                
                // Reset button state
                this.classList.remove('loading');
                this.disabled = false;
                textElement.textContent = originalText;
                
                // Show error message
                showToast('Error adding product to cart', 'error');
            });
        });
    });
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize custom add to cart handlers
    handleAjaxAddToCart();
    
    // Check if WooCommerce add to cart params are available
    if (typeof wc_add_to_cart_params === 'undefined') {
        window.wc_add_to_cart_params = {
            ajax_url: '<?php echo admin_url('admin-ajax.php'); ?>',
            wc_ajax_url: '<?php echo WC_AJAX::get_endpoint('%%endpoint%%'); ?>',
            i18n_view_cart: 'View cart',
            cart_url: '<?php echo wc_get_cart_url(); ?>',
            is_cart: false,
            cart_redirect_after_add: '<?php echo get_option('woocommerce_cart_redirect_after_add'); ?>'
        };
    }
    
    // Initialize ordering form auto-submit
    const orderingSelect = document.querySelector('.woocommerce-ordering select');
    if (orderingSelect) {
        orderingSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
    
    console.log('Shop page initialized with add to cart functionality');
});

// jQuery compatibility for WooCommerce (if available)
if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function($) {
        // Handle WooCommerce cart events
        $('body').on('adding_to_cart', function(event, $button, data) {
            if ($button) {
                $button.removeClass('added').addClass('loading');
            }
        });
        
        $('body').on('added_to_cart', function(event, fragments, cart_hash, $button) {
            if ($button) {
                $button.removeClass('loading').addClass('added');
                setTimeout(function() {
                    $button.removeClass('added');
                }, 2000);
            }
        });
        
        $('body').on('wc_cart_button_updated', function(event, $button) {
            if ($button) {
                $button.removeClass('loading');
            }
        });
        
        // Refresh cart fragments on page load
        if (typeof wc_cart_fragments_params !== 'undefined') {
            $(document.body).trigger('wc_fragment_refresh');
        }

        // --- YITH Wishlist Comprehensive Event Handling ---

        // 1. On Product Added: Force icon to "filled" state
        $(document).on('yith_wcwl_product_added', function(event, response, button) {
            showToast('Product added to wishlist!', 'success');
            var wishlist_button_container = $(button).closest('.yith-wcwl-add-to-wishlist');
            if (wishlist_button_container.length) {
                setTimeout(function() {
                    var link = wishlist_button_container.find('a');
                    link.css({
                        'background': '#ed1c24',
                        'background-image': 'url(\'data:image/svg+xml;utf8,<svg fill="%23ffffff" stroke="%23ffffff" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>\')'
                    });
                }, 100);
            }
        });

        // 2. On Product Removed: Force icon to "outline" state
        $(document).on('yith_wcwl_product_removed', function(event, el) {
            showToast('Product removed from wishlist!', 'success');
            var wishlist_button_container = $(el).closest('.yith-wcwl-add-to-wishlist');
            if (wishlist_button_container.length) {
                setTimeout(function() {
                    var link = wishlist_button_container.find('a');
                    link.css({
                        'background': 'white',
                        'background-image': 'url(\'data:image/svg+xml;utf8,<svg fill="none" stroke="%236b7280" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>\')'
                    });
                }, 100);
            }
        });

        // 3. On Click Filled Heart: Manually trigger AJAX remove
        $(document).on('click', '.yith-wcwl-wishlistaddedbrowse a, .yith-wcwl-wishlistexistsbrowse a', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var el = $(this);
            var product_id = el.data('product-id');
            var product_wrapper = el.closest('.yith-wcwl-add-to-wishlist');

            if (!product_id) {
                // Find product ID from a parent element if not on the link itself
                var parent_with_id = el.closest('[data-product-id]');
                 if(parent_with_id.length > 0) {
                    product_id = parent_with_id.data('product-id');
                 }
            }
            
            if (!product_id || typeof yith_wcwl_l10n === 'undefined') {
                location.reload(); // Fallback if we can't get info
                return;
            }

            var data = {
                action: yith_wcwl_l10n.actions.remove_product_from_wishlist_action,
                product_id: product_id,
                nonce: yith_wcwl_l10n.nonce.remove_product_from_wishlist_nonce,
                context: 'frontend'
            };

            $.ajax({
                url: yith_wcwl_l10n.ajax_url,
                data: data,
                method: 'POST',
                beforeSend: function(){ el.addClass('loading'); },
                complete: function(){ el.removeClass('loading'); },
                success: function(response) {
                    // Manually trigger the 'removed' event so our other handler can update the style
                    $(document.body).trigger('yith_wcwl_product_removed', [el]);
                    
                    // Let YITH update fragments if it provides them
                    if( response.fragments ){
                        $(document.body).trigger( 'yith_wcwl_fragments_refreshed', [ response.fragments ] );
                    }
                },
                error: function() { location.reload(); } // Fallback
            });
            
            return false;
        });
    });
}
</script>

<style>
/* Additional CSS for better functionality */
.ajax-add-to-cart.loading {
    opacity: 0.6;
    cursor: not-allowed;
}

.ajax-add-to-cart.added {
    background-color: #28a745 !important;
}

.woocommerce-ordering select {
    padding: 8px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background-color: white;
    font-size: 14px;
}

.woocommerce-ordering select:focus {
    outline: none;
    border-color: #ed1c24;
    box-shadow: 0 0 0 3px rgba(255, 58, 94, 0.1);
}

/* Loading animation */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

/* Filter form styling improvements */
#shop-filters-form input[type="range"] {
    -webkit-appearance: none;
    appearance: none;
    background: transparent;
    cursor: pointer;
}

#shop-filters-form input[type="range"]::-webkit-slider-track {
    background: #e5e7eb;
    height: 4px;
    border-radius: 2px;
}

#shop-filters-form input[type="range"]::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    background: #ed1c24;
    height: 16px;
    width: 16px;
    border-radius: 50%;
    cursor: pointer;
}

#shop-filters-form input[type="range"]::-moz-range-track {
    background: #e5e7eb;
    height: 4px;
    border-radius: 2px;
    border: none;
}

#shop-filters-form input[type="range"]::-moz-range-thumb {
    background: #ed1c24;
    height: 16px;
    width: 16px;
    border-radius: 50%;
    cursor: pointer;
    border: none;
}

/* Toast notification improvements */
#toast {
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    font-weight: 500;
}

/* Pagination styling */
.page-numbers {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    margin: 0 2px;
    text-decoration: none;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    color: #374151;
    transition: all 0.2s;
}

li a.page-numbers:hover,
li span.page-numbers.current {
    background-color: #ed1c24;
    border-color: #ed1c24;
    color: white;
}

.page-numbers.prev,
.page-numbers.next {
    padding: 8px;
}

.scrollable-size-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 8px;
    max-height: 220px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fafbfc;
    padding: 8px;
    position: relative;

}
.size-option {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 0;
    padding: 6px 8px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: #fff;
    font-size: 13px;
    cursor: pointer;
    transition: border 0.2s, color 0.2s, background 0.2s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.size-option.selected {
    background: #ed1c24;
    color: #fff;
    border-color: #ed1c24;
}
.scrollable-size-grid::-webkit-scrollbar {
    width: 6px;
    background: #f1f1f1;
}
.scrollable-size-grid::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 6px;
}

.scrollable-color-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
    gap: 8px;
    max-height: 220px;
    overflow-y: auto;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fafbfc;
    padding: 8px;
    position: relative;

}

.color-option {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 0;
    padding: 6px 8px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    background: #fff;
    font-size: 13px;
    cursor: pointer;
    transition: border 0.2s, color 0.2s, background 0.2s;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.color-option.selected {
    background: #f8f9fa;
    border-color: #ed1c24;
}

.scrollable-color-grid::-webkit-scrollbar {
    width: 6px;
    background: #f1f1f1;
}

.scrollable-color-grid::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 6px;
}

.product-badge {
  display: inline-block;
  position: absolute;
  top: 18px;
  left: 18px;
  z-index: 20;
  padding: 6px 12px;
  font-size: 0.75rem;
  line-height: 1rem;
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

/* Pixel-perfect design for the sorting dropdown */
.woocommerce-ordering {
    position: relative;
}
.woocommerce-ordering select {
    padding: 12px 16px;
    /* border: 2px solid #ed1c24; */
    border-radius: 10px;
    background: #fff;
    font-size: 16px;
    font-weight: 500;
    color: #222;
    /* box-shadow: 0 2px 8px rgba(237, 28, 36, 0.08); */
    transition: border 0.2s, box-shadow 0.2s;
    outline: none;
    appearance: none;
    -webkit-appearance: none;
    -moz-appearance: none;
    cursor: pointer;
    min-width: 180px;
}
.woocommerce-ordering select:focus {
    border-color: #ed1c24;
    box-shadow: 0 0 0 3px rgba(237, 28, 36, 0.15);
}
.woocommerce-ordering option {
    font-size: 16px;
    color: #222;
    background: #fff;
}
.woocommerce-ordering select::-ms-expand {
    display: none;
}
.woocommerce-ordering::after {
    content: '';
    position: absolute;
    right: 18px;
    top: 50%;
    width: 12px;
    height: 12px;
    pointer-events: none;
    background: url('data:image/svg+xml;utf8,<svg fill="none" stroke="black" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M19 9l-7 7-7-7"/></svg>') no-repeat center center;
    transform: translateY(-50%);
}
.woocommerce-ordering select:active,
.woocommerce-ordering select:focus {
    outline: none;
}

/* YITH Wishlist Button: Only Heart Icon, No Text */
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
    font-size: 0 !important;
    color: transparent !important;
}
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a:before,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:before,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:before {
    content: '';
    display: inline-block;
    width: 24px;
    height: 24px;
    background-size: contain;
    background-repeat: no-repeat;
    vertical-align: middle;
    text-indent: 0;
    margin: 0;
}
/* Outline heart (not in wishlist) */
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a:before {
    position:absolute;
    background-image: url('data:image/svg+xml;utf8,<svg fill="none" stroke="%236b7280" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>');
}
/* Filled white heart on red (in wishlist) */
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a {
    background: #ed1c24 !important;
}
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:before,
.yith-wcwl-add-to-wishlist.exists a:before {
    background-image: url('data:image/svg+xml;utf8,<svg fill="%23ed1c24" stroke="%23ed1c24" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>');
}
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:hover,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:hover {
    background: #d31920 !important;
}
/* Hide all text, links, and spans inside the button */
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a span,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a span,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a span,
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a .yith-wcwl-icon,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a .yith-wcwl-icon,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a .yith-wcwl-icon {
    display: none !important;
}
/* Responsive */
@media (max-width: 768px) {
    .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a,
    .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
    .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a {
        width: 36px !important;
        height: 36px !important;
    }
    .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a:before,
    .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:before,
    .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:before {
        width: 20px !important;
        height: 20px !important;
    }
}

/* Loading state for wishlist button */
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a.loading,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a.loading {
    opacity: 0.5;
    cursor: wait !important;
}
</style>

<?php
get_footer();
?>