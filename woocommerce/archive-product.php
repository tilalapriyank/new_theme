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
add_action('pre_get_posts', function($query) {
    if (!is_admin() && $query->is_main_query() && (is_shop() || is_product_category())) {
        if (isset($_GET['product_cat']) && !empty($_GET['product_cat'])) {
            $categories = $_GET['product_cat'];
            
            // If it's an array, sanitize each value
            if (is_array($categories)) {
                $categories = array_map('sanitize_title', $categories);
            } else {
                // If it's a string, handle comma-separated values
                $categories = array_map('sanitize_title', explode(',', $categories));
            }
            
            // Set up the tax query for categories
            $tax_query = $query->get('tax_query') ?: array();
            $tax_query[] = array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $categories,
                'operator' => 'IN'
            );
            $query->set('tax_query', $tax_query);
        }
        
        // Handle price filtering
        if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
            $meta_query = $query->get('meta_query') ?: array();
            $meta_query[] = array(
                'key'     => '_price',
                'value'   => floatval($_GET['min_price']),
                'compare' => '>=',
                'type'    => 'NUMERIC'
            );
            $query->set('meta_query', $meta_query);
        }
        
        if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
            $meta_query = $query->get('meta_query') ?: array();
            $meta_query[] = array(
                'key'     => '_price',
                'value'   => floatval($_GET['max_price']),
                'compare' => '<=',
                'type'    => 'NUMERIC'
            );
            $query->set('meta_query', $meta_query);
        }
        
        // Handle attribute filtering (size, color)
        $attributes = array('pa_size', 'pa_color');
        foreach ($attributes as $attribute) {
            $filter_key = 'filter_' . $attribute;
            if (isset($_GET[$filter_key]) && !empty($_GET[$filter_key])) {
                $terms = is_array($_GET[$filter_key]) ? $_GET[$filter_key] : array($_GET[$filter_key]);
                $terms = array_map('sanitize_title', $terms);
                
                $tax_query = $query->get('tax_query') ?: array();
                $tax_query[] = array(
                    'taxonomy' => $attribute,
                    'field'    => 'slug',
                    'terms'    => $terms,
                    'operator' => 'IN'
                );
                $query->set('tax_query', $tax_query);
            }
        }
    }
});

get_header();
?>

<script>
// Add Alpine.js data for mobile filters
document.addEventListener('alpine:init', () => {
    Alpine.data('shopFilters', () => ({
        mobileFiltersOpen: false
    }))
});
</script>

<main id="main-content" class="py-12 md:py-16" x-data="shopFilters">
    <div class="container mx-auto px-4">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">Shop All Products</h1>
            <div class="flex items-center text-sm text-gray-500">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="hover:text-[#FF3A5E]">
                    Home
                </a>
                <span class="mx-2">/</span>
                <span>Shop</span>
            </div>
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
                            if (!in_array($key, array('product_cat', 'min_price', 'max_price', 'filter_pa_size', 'filter_pa_color'))) {
                                if (is_array($value)) {
                                    foreach ($value as $v) {
                                        echo '<input type="hidden" name="' . esc_attr($key) . '[]" value="' . esc_attr($v) . '">';
                                    }
                                } else {
                                    echo '<input type="hidden" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '">';
                                }
                            }
                        }
                        ?>
                        
                        <div class="mb-6">
                            <h3 class="font-bold mb-4 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                </svg>
                                Filter Products
                            </h3>
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-medium mb-3">Categories</h4>
                                <?php
                                $product_categories = get_terms(array(
                                    'taxonomy' => 'product_cat',
                                    'hide_empty' => true,
                                    'parent' => 0
                                ));
                                $selected_cats = array();
                                if (isset($_GET['product_cat'])) {
                                    if (is_array($_GET['product_cat'])) {
                                        $selected_cats = array_map('sanitize_text_field', $_GET['product_cat']);
                                    } else {
                                        $selected_cats = explode(',', sanitize_text_field($_GET['product_cat']));
                                    }
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
                                                name="product_cat[]"
                                                value="<?php echo esc_attr($category->slug); ?>"
                                                class="rounded border-gray-300 text-[#FF3A5E] focus:ring-[#FF3A5E]"
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
                        </div>

                        <div class="mb-6">
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-medium mb-3">Price Range</h4>
                                <div class="px-2">
                                    <div class="mb-6 flex items-center gap-2">
                                        <?php
                                        $min = isset($_GET['min_price']) ? intval($_GET['min_price']) : 0;
                                        $max = isset($_GET['max_price']) ? intval($_GET['max_price']) : 200;
                                        ?>
                                        <input 
                                            type="range" 
                                            min="0" 
                                            max="200" 
                                            step="1" 
                                            name="min_price"
                                            value="<?php echo esc_attr($min); ?>"
                                            class="w-full"
                                            oninput="updatePriceDisplay(this); debouncedPriceSubmit()"
                                        >
                                        <input type="hidden" name="max_price" value="200">
                                        <output class="ml-2 text-sm text-gray-700">$<?php echo esc_html($min); ?></output>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm">$0</span>
                                        <span class="text-sm">$200</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-medium mb-3">Size</h4>
                                <div class="flex flex-wrap gap-2">
                                    <?php
                                    $sizes = get_terms(array('taxonomy' => 'pa_size', 'hide_empty' => true));
                                    $selected_sizes = isset($_GET['filter_pa_size']) ? (array) $_GET['filter_pa_size'] : array();
                                    if (!empty($sizes) && !is_wp_error($sizes)) :
                                        foreach ($sizes as $size) :
                                    ?>
                                            <label
                                                class="flex items-center justify-center w-12 h-10 border rounded-md text-sm cursor-pointer border-gray-300 hover:border-[#FF3A5E] hover:text-[#FF3A5E] px-2 <?php if (in_array($size->slug, $selected_sizes)) echo 'bg-[#FF3A5E] text-white border-[#FF3A5E]'; ?>"
                                            >
                                                <input 
                                                    type="checkbox" 
                                                    name="filter_pa_size[]"
                                                    value="<?php echo esc_attr($size->slug); ?>" 
                                                    class="sr-only"
                                                    <?php checked(in_array($size->slug, $selected_sizes)); ?>
                                                    onchange="submitFilters()"
                                                >
                                                <span><?php echo esc_html($size->name); ?></span>
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
                                <div class="flex flex-wrap gap-2">
                                    <?php
                                    $colors = get_terms(array('taxonomy' => 'pa_color', 'hide_empty' => true));
                                    $selected_colors = isset($_GET['filter_pa_color']) ? (array) $_GET['filter_pa_color'] : array();
                                    if (!empty($colors) && !is_wp_error($colors)) :
                                        foreach ($colors as $color) :
                                            $color_class = '';
                                            switch ($color->slug) {
                                                case 'black': $color_class = 'bg-black'; break;
                                                case 'white': $color_class = 'bg-white border border-gray-300'; break;
                                                case 'red': $color_class = 'bg-red-500'; break;
                                                case 'blue': $color_class = 'bg-blue-500'; break;
                                                case 'green': $color_class = 'bg-green-500'; break;
                                                case 'yellow': $color_class = 'bg-yellow-400'; break;
                                                case 'gray': $color_class = 'bg-gray-500'; break;
                                                case 'camo': $color_class = 'bg-olive-600'; break;
                                                default: $color_class = 'bg-gray-200'; break;
                                            }
                                    ?>
                                            <label
                                                class="flex flex-col items-center gap-1 cursor-pointer"
                                                title="<?php echo esc_attr($color->name); ?>"
                                            >
                                                <div
                                                    class="w-8 h-8 rounded-full flex items-center justify-center <?php echo esc_attr($color_class); ?> hover:ring-2 hover:ring-[#FF3A5E] hover:ring-offset-2 <?php if (in_array($color->slug, $selected_colors)) echo 'ring-2 ring-[#FF3A5E]'; ?>"
                                                >
                                                    <input 
                                                        type="checkbox" 
                                                        name="filter_pa_color[]"
                                                        value="<?php echo esc_attr($color->slug); ?>" 
                                                        class="sr-only"
                                                        <?php checked(in_array($color->slug, $selected_colors)); ?>
                                                        onchange="submitFilters()"
                                                    >
                                                </div>
                                                <span class="text-xs font-medium <?php if (in_array($color->slug, $selected_colors)) echo 'text-[#FF3A5E]'; ?>"><?php echo esc_html($color->name); ?></span>
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
                            class="w-full bg-[#FF3A5E] hover:bg-[#FF3A5E]/90 text-white font-medium px-4 py-2 rounded-md mb-3"
                        >
                            Apply Filters
                        </button>
                        <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="w-full border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-md text-center block">
                            Reset Filters
                        </a>
                    </form>
                </div>
            </div>

            <!-- Products - Right Side -->
            <div class="lg:w-3/4">
                <!-- Sort and View Options -->
                <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                    <p class="text-sm text-gray-500">
                        <?php
                        global $wp_query;
                        $found_posts = $wp_query->found_posts ?? 0;
                        echo esc_html($found_posts) . ' products';
                        ?>
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
                    if (have_posts()) :
                        while (have_posts()) : the_post();
                            global $product;
                            $product_id = $product->get_id();
                            $product_link = get_permalink($product_id);
                            $product_img = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail');
                            $product_title = $product->get_name();
                            $product_price = $product->get_price_html();
                            $review_count = $product->get_review_count();
                            $average = $product->get_average_rating();
                            $badge = '';
                            
                            // Check if product is on sale
                            if ($product->is_on_sale()) {
                                $badge = 'Sale';
                            }
                            // Check if product is new (less than 30 days old)
                            elseif ((time() - strtotime($product->get_date_created())) < (30 * 24 * 60 * 60)) {
                                $badge = 'New';
                            }
                            // Check if product is bestseller
                            elseif ($product->get_total_sales() > 10) {
                                $badge = 'Bestseller';
                            }
                    ?>
                            <div class="rounded-lg overflow-hidden border-none shadow-md group bg-white">
                                <a href="<?php echo esc_url($product_link); ?>" class="block">
                                    <div class="relative">
                                        <div class="aspect-square relative overflow-hidden">
                                            <?php if ($product_img) : ?>
                                                <img 
                                                    src="<?php echo esc_url($product_img); ?>" 
                                                    alt="<?php echo esc_attr($product_title); ?>" 
                                                    class="object-cover w-full h-full transition-transform group-hover:scale-105 duration-500"
                                                >
                                            <?php else : ?>
                                                <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                    <span class="text-gray-400">No Image</span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <?php if ($badge) : ?>
                                            <div class="absolute top-2 left-2 bg-[#FF3A5E] text-white text-xs font-semibold px-2 py-1 rounded-full">
                                                <?php echo esc_html($badge); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100">
                                            <div class="flex flex-col gap-2 w-full max-w-[200px] px-4">
                                                <a
                                                    href="<?php echo esc_url($product_link); ?>"
                                                    class="bg-white text-black hover:bg-white/90 w-full py-2 px-4 rounded-full text-sm font-medium flex items-center justify-center"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                    Quick View
                                                </a>
                                                
                                                <!-- FIXED ADD TO CART SECTION -->
                                                <?php if ($product->get_type() === 'variable') : ?>
                                                    <!-- For variable products, redirect to product page -->
                                                    <a
                                                        href="<?php echo esc_url($product_link); ?>"
                                                        class="bg-[#FF3A5E] hover:bg-[#FF3A5E]/90 text-white w-full py-2 px-4 rounded-full text-sm font-medium flex items-center justify-center"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 9H19m-7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                                        </svg>
                                                        Select Options
                                                    </a>
                                                <?php else : ?>
                                                    <!-- For simple products, use AJAX add to cart -->
                                                    <button
                                                        type="button"
                                                        class="ajax-add-to-cart bg-[#FF3A5E] hover:bg-[#FF3A5E]/90 text-white w-full py-2 px-4 rounded-full text-sm font-medium flex items-center justify-center"
                                                        data-product_id="<?php echo esc_attr($product_id); ?>"
                                                        data-product_sku="<?php echo esc_attr($product->get_sku()); ?>"
                                                        data-quantity="1"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 9H19m-7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z" />
                                                        </svg>
                                                        <span class="add-to-cart-text">Add to Cart</span>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-4">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="font-medium text-lg font-montserrat hover:text-[#FF3A5E] transition-colors">
                                                <?php echo esc_html($product_title); ?>
                                            </h3>
                                            <button
                                                class="text-gray-700 hover:text-[#FF3A5E] p-1 rounded-full"
                                                onclick="event.preventDefault(); addToWishlist(<?php echo esc_attr($product_id); ?>)"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                                </svg>
                                            </button>
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
                                        <p class="font-bold text-lg"><?php echo $product_price; ?></p>
                                    </div>
                                </a>
                            </div>
                    <?php
                        endwhile;
                    else :
                    ?>
                        <div class="col-span-full text-center py-12">
                            <p class="text-gray-500">No products found matching your criteria.</p>
                            <a href="<?php echo esc_url(get_permalink(wc_get_page_id('shop'))); ?>" class="text-[#FF3A5E] hover:underline mt-2 inline-block">Clear all filters</a>
                        </div>
                    <?php
                    endif;
                    ?>
                </div>

                <!-- Pagination -->
                <?php if (have_posts()) : ?>
                <div class="flex justify-center mt-12">
                    <?php
                    echo paginate_links(array(
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

// Submit filters function with debouncing
function submitFilters() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        document.getElementById('shop-filters-form').submit();
    }, 300);
}

// Price display update
function updatePriceDisplay(slider) {
    const output = slider.parentNode.querySelector('output');
    if (output) {
        output.textContent = ' + slider.value;
    }
}

// Debounced price submission
function debouncedPriceSubmit() {
    clearTimeout(priceTimeout);
    priceTimeout = setTimeout(() => {
        document.getElementById('shop-filters-form').submit();
    }, 1000);
}

// Wishlist function
function addToWishlist(productId) {
    // Implement your wishlist logic here
    showToast('Product added to wishlist!', 'success');
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
    
    console.log('Shop page initialized with fixed add to cart functionality');
});

// jQuery compatibility for WooCommerce (if available)
if (typeof jQuery !== 'undefined') {
    jQuery(document).ready(function($) {
        // Handle WooCommerce events
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
    border-color: #FF3A5E;
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
    background: #FF3A5E;
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
    background: #FF3A5E;
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

.page-numbers:hover,
.page-numbers.current {
    background-color: #FF3A5E;
    border-color: #FF3A5E;
    color: white;
}

.page-numbers.prev,
.page-numbers.next {
    padding: 8px;
}
</style>

<?php
get_footer();
?>