<?php

/**
 * The template for displaying product content in the single-product.php template
 * With YITH WooCommerce Wishlist integration and CreateIT Size Guide Plugin - Pixel Perfect Design
 */

defined('ABSPATH') || exit;

get_header(); ?>

<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 pt-32">
        <?php while (have_posts()) : ?>
            <?php the_post(); ?>
            <?php
            global $product;
            // Get available variations for variable products
            $available_variations = array();
            $attributes = array();
            if ($product->is_type('variable')) {
                $available_variations = $product->get_available_variations();
                $attributes = $product->get_variation_attributes();
            }

            // Get Size Guide for this product
            $size_guide_id = get_post_meta(get_the_ID(), '_ct_selectsizeguide', true);
            $has_size_guide = !empty($size_guide_id) && get_post_status($size_guide_id) === 'publish';
            ?>

            <div id="product-<?php the_ID(); ?>" <?php wc_product_class('single-product-wrapper', $product); ?>>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16">
                    <!-- Product Images Column -->
                    <div class="product-images-section">
                        <div class="relative">
                            <!-- Main Product Image -->
                            <div class="main-product-image-container mb-4">
                                <div class="aspect-square w-full bg-gray-50 rounded-2xl overflow-hidden relative group">
                                    <?php
                                    $image_id = $product->get_image_id();
                                    if ($image_id) {
                                        $image_url = wp_get_attachment_image_url($image_id, 'woocommerce_single');
                                        echo '<img id="main-product-image" src="' . esc_url($image_url) . '" alt="' . esc_attr($product->get_name()) . '" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">';
                                    }
                                    ?>

                                    <!-- Sale Badge -->
                                    <?php if ($product->is_on_sale()) : ?>
                                        <div class="absolute top-4 left-4 bg-red-500 text-white text-sm font-semibold px-3 py-1 rounded-full z-10">
                                            Sale
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Thumbnail Images -->
                            <div class="thumbnail-container">
                                <div class="flex gap-3 overflow-x-auto pb-2">
                                    <?php
                                    $attachment_ids = $product->get_gallery_image_ids();

                                    // Add main image as first thumbnail
                                    if ($image_id) {
                                        $main_image_url = wp_get_attachment_image_url($image_id, 'woocommerce_thumbnail');
                                        echo '<div class="thumbnail-item flex-shrink-0">';
                                        echo '<img src="' . esc_url($main_image_url) . '" alt="Product Image" class="product-gallery-image w-20 h-20 sm:w-20 sm:h-20 object-cover rounded-lg border-2 border-red-500 cursor-pointer thumbnail-image active" data-full="' . esc_url($image_url) . '">';
                                        echo '</div>';
                                    }

                                    // Add gallery images as thumbnails
                                    foreach ($attachment_ids as $attachment_id) {
                                        $thumbnail_url = wp_get_attachment_image_url($attachment_id, 'woocommerce_thumbnail');
                                        $full_url = wp_get_attachment_image_url($attachment_id, 'woocommerce_single');

                                        echo '<div class="thumbnail-item flex-shrink-0">';
                                        echo '<img src="' . esc_url($thumbnail_url) . '" alt="Product Image" class="w-20 h-20 sm:w-20 sm:h-20 object-cover rounded-lg border-2 border-gray-200 hover:border-gray-400 cursor-pointer thumbnail-image transition-colors product-gallery-image" data-full="' . esc_url($full_url) . '">';
                                        echo '</div>';
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Summary Column -->
                    <div class="product-summary-section">
                        <div class="sticky top-8">
                            <!-- Product Title -->
                            <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4 leading-tight">
                                <?php the_title(); ?>
                            </h1>

                            <!-- Product Rating -->
                            <?php if (wc_review_ratings_enabled()) : ?>
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="flex items-center">
                                        <?php
                                        $rating = $product->get_average_rating();
                                        for ($i = 1; $i <= 5; $i++) {
                                            $fill_class = $i <= floor($rating) ? 'text-yellow-400' : 'text-gray-300';
                                            echo '<svg class="w-5 h-5 ' . $fill_class . '" fill="currentColor" viewBox="0 0 20 20">';
                                            echo '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>';
                                            echo '</svg>';
                                        }
                                        ?>
                                    </div>
                                    <span class="text-sm text-gray-600">
                                        <?php echo esc_html($rating); ?> (<?php echo esc_html($product->get_review_count()); ?> reviews)
                                    </span>
                                </div>
                            <?php endif; ?>

                            <!-- Product Price -->
                            <div class="mb-6">
                                <?php if ($product->is_on_sale()) : ?>
                                    <div class="flex items-center gap-3">
                                        <span class="text-3xl font-bold text-red-500">
                                            <?php echo wc_price($product->get_sale_price()); ?>
                                        </span>
                                        <span class="text-xl text-gray-400 line-through">
                                            <?php echo wc_price($product->get_regular_price()); ?>
                                        </span>
                                    </div>
                                <?php else : ?>
                                    <span class="text-3xl font-bold text-gray-900">
                                        <?php echo $product->get_price_html(); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Product Short Description -->
                            <div class="text-gray-600 leading-relaxed mb-6">
                                <?php echo apply_filters('woocommerce_short_description', $post->post_excerpt); ?>
                            </div>

                            <!-- Key Features -->
                            <?php
                            $features = get_post_meta(get_the_ID(), '_product_key_features', true);
                            if ($features) {
                            ?>
                                <div class="mb-8">
                                    <h3 class="font-semibold text-lg mb-4 text-gray-900">Key Features</h3>
                                    <ul class="space-y-3">
                                        <?php
                                        foreach ($features as $feature) {
                                            echo '<li class="flex items-center text-gray-700">';
                                            echo '<svg class="w-5 h-5 text-[#ed1c24] mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">';
                                            echo '<path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>';
                                            echo '</svg>';
                                            echo esc_html($feature);
                                            echo '</li>';
                                        }
                                        ?>
                                    </ul>
                                </div>
                            <?php } ?>

                            <!-- Cart Messages Area -->
                            <div id="cart-messages" class="cart-messages hidden mb-4">
                                <div class="alert"></div>
                            </div>

                            <!-- Product Options Form -->
                            <?php if ($product->is_type('variable')) : ?>
                                <form class="variations_form cart custom-cart-form" action="#" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint($product->get_id()); ?>" data-product_variations="<?php echo htmlspecialchars(wp_json_encode($available_variations)) ?>">
                                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                                    <?php do_action('woocommerce_before_variations_form'); ?>

                                    <?php if (empty($available_variations) && false !== $available_variations) : ?>
                                        <p class="stock out-of-stock text-red-500 font-medium"><?php esc_html_e('This product is currently out of stock and unavailable.', 'woocommerce'); ?></p>
                                    <?php else : ?>

                                        <!-- Color Selection -->
                                        <?php if (isset($attributes['Color'])) : ?>
                                            <div class="mb-6">
                                                <div class="flex items-center justify-between mb-3">
                                                    <label class="text-sm font-semibold text-gray-900">Color</label>
                                                </div>
                                                <div class="flex gap-3">
                                                    <?php foreach ($attributes['Color'] as $color) : ?>
                                                        <button type="button"
                                                            class="color-swatch w-10 h-10 rounded-full border-2 border-gray-300 focus:outline-none focus:ring-2 focus:ring-red-500 hover:ring-2 hover:ring-red-400 transition-all relative overflow-hidden"
                                                            data-value="<?php echo esc_attr($color); ?>"
                                                            style="background-color: <?php echo esc_attr(strtolower($color)); ?>;"
                                                            title="<?php echo esc_attr($color); ?>">
                                                            <span class="sr-only"><?php echo esc_html($color); ?></span>
                                                        </button>
                                                    <?php endforeach; ?>
                                                </div>
                                                <input type="hidden" name="attribute_Color" class="selected-color" value="" />
                                            </div>
                                        <?php endif; ?>

                                        <!-- Size Selection -->
                                        <?php if (isset($attributes['Size'])) : ?>
                                            <div class="mb-6">
                                                <div class="flex items-center justify-between mb-3">
                                                    <label class="text-sm font-semibold text-gray-900">Size</label>
                                                    <?php if ($has_size_guide) : ?>
                                                        <button type="button" id="size-guide-trigger" class="text-sm text-red-500 hover:text-red-600 underline font-medium" data-size-guide-id="<?php echo esc_attr($size_guide_id); ?>">
                                                            Size Guide
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="flex gap-3">
                                                    <?php foreach ($attributes['Size'] as $size) : ?>
                                                        <button type="button"
                                                            class="size-btn px-3 py-3 min-w-[48px] border-2 border-gray-300 text-sm font-semibold text-gray-700 rounded-full hover:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all"
                                                            data-value="<?php echo esc_attr($size); ?>">
                                                            <?php echo esc_html($size); ?>
                                                        </button>
                                                    <?php endforeach; ?>
                                                </div>
                                                <input type="hidden" name="attribute_Size" class="selected-size" value="" />
                                            </div>
                                        <?php endif; ?>

                                        <!-- Quantity and Add to Cart -->
                                        <div class="space-y-4">
                                            <!-- Quantity Selector -->
                                            <div class="mb-6">
                                                <label class="text-sm font-semibold text-gray-900 mb-3 block" for="quantity-input">Quantity</label>
                                                <div class="flex items-center gap-8">
                                                    <button type="button" aria-label="Decrease quantity"
                                                        class="quantity-btn quantity-minus flex items-center justify-center w-16 h-16 rounded-full border border-gray-200 bg-white text-2xl font-light text-gray-700 hover:border-red-400 hover:text-red-500 transition">
                                                        &minus;
                                                    </button>
                                                    <input
                                                        id="quantity-input"
                                                        type="number"
                                                        name="quantity"
                                                        value="<?php echo esc_attr(isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity()); ?>"
                                                        min="<?php echo esc_attr($product->get_min_purchase_quantity()); ?>"
                                                        max="<?php echo esc_attr(0 < $product->get_max_purchase_quantity() ? $product->get_max_purchase_quantity() : ''); ?>"
                                                        step="1"
                                                        class="quantity-input w-8 text-center border-0 outline-none text-xl font-semibold bg-transparent"
                                                        inputmode="numeric"
                                                        pattern="[0-9]*"
                                                        style="pointer-events: none;"
                                                        readonly>
                                                    <button type="button" aria-label="Increase quantity"
                                                        class="quantity-btn quantity-plus flex items-center justify-center w-16 h-16 rounded-full border border-gray-200 bg-white text-2xl font-light text-gray-700 hover:border-red-400 hover:text-red-500 transition">
                                                        &#43;
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex gap-4 w-full items-center">
                                                <!-- Add to Cart Button -->
                                                <button type="submit"
                                                    name="add-to-cart"
                                                    value="<?php echo esc_attr($product->get_id()); ?>"
                                                    class="flex-1 text-base single_add_to_cart_button bg-red-500 hover:bg-red-600 text-white font-semibold py-4 px-8 rounded-xl transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-none border-0">
                                                    <span class="button-text">Add to Cart</span>
                                                    <span class="loading-spinner hidden">
                                                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                    </span>
                                                    <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 9H19m-7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                                    </svg>
                                                </button>

                                                <!-- Add to Wishlist Button -->
                                                <?php if (function_exists('YITH_WCWL')) : ?>
                                                    <div class="flex-1 yith-wishlist-wrapper custom-wishlist-wrapper">
                                                        <?php
                                                        $label = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg> Add to Wishlist';
                                                        echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . $product->get_id() . '" label="' . $label . '" browse_wishlist_text="Browse Wishlist" already_in_wishlist_text="In Wishlist" product_added_text="Product added to wishlist!" icon="none" link_classes="wishlist-btn whitespace-nowrap w-full border-2 border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-500 font-semibold py-4 px-8 rounded-xl transition-colors flex items-center justify-center gap-2 bg-white shadow-none text-base" ]');
                                                        ?>
                                                    </div>
                                                <?php else : ?>
                                                    <button type="button" class="flex-1 add-to-wishlist-fallback whitespace-nowrap w-full border-2 border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-500 font-semibold py-4 px-8 rounded-xl transition-colors flex items-center justify-center gap-2 bg-white shadow-none text-base" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                        </svg>
                                                        Add to Wishlist
                                                    </button>
                                                <?php endif; ?>

                                                <!-- Share Button -->
                                                <button type="button" 
                                                    id="share-product-btn"
                                                    class="flex items-center justify-center border-2 border-gray-300 text-gray-700 hover:border-gray-400 font-semibold py-4 px-4 rounded-full transition-colors bg-white shadow-none w-[50px] h-[50px] min-w-[50px] min-h-[50px] max-w-[50px] max-h-[50px]"
                                                    data-product-title="<?php echo esc_attr($product->get_name()); ?>"
                                                    data-product-url="<?php echo esc_url(get_permalink()); ?>"
                                                    data-product-price="<?php echo esc_attr($product->get_price_html()); ?>"
                                                    title="Share this product">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <input type="hidden" name="product_id" value="<?php echo esc_attr($product->get_id()); ?>" />
                                        <input type="hidden" name="variation_id" class="variation_id" value="0" />

                                    <?php endif; ?>
                                    <?php do_action('woocommerce_after_variations_form'); ?>
                                </form>
                            <?php else : ?>
                                <!-- Simple Product Form -->
                                <form class="cart custom-cart-form" action="#" method="post" enctype='multipart/form-data' data-product_id="<?php echo esc_attr($product->get_id()); ?>">
                                    <?php wp_nonce_field('woocommerce-cart', 'woocommerce-cart-nonce'); ?>
                                    <?php do_action('woocommerce_before_add_to_cart_button'); ?>

                                    <!-- Size Guide for Simple Products (if has Size attribute) -->
                                    <?php
                                    $product_attributes = $product->get_attributes();
                                    $has_size_attribute = false;
                                    foreach ($product_attributes as $attribute) {
                                        if (strtolower($attribute->get_name()) === 'size' || strtolower($attribute->get_name()) === 'pa_size') {
                                            $has_size_attribute = true;
                                            break;
                                        }
                                    }
                                    if ($has_size_guide && $has_size_attribute) : ?>
                                        <div class="mb-6">
                                            <div class="flex items-center justify-between mb-3">
                                                <label class="text-sm font-semibold text-gray-900">Size Information</label>
                                                <button type="button" id="size-guide-trigger" class="text-sm text-red-500 hover:text-red-600 underline font-medium" data-size-guide-id="<?php echo esc_attr($size_guide_id); ?>">
                                                    Size Guide
                                                </button>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Quantity Selector -->
                                    <div class="mb-6">
                                        <label class="text-sm font-semibold text-gray-900 mb-3 block" for="quantity-input">Quantity</label>
                                        <div class="flex items-center gap-8">
                                            <button type="button" aria-label="Decrease quantity"
                                                class="quantity-btn quantity-minus flex items-center justify-center w-12 h-12 rounded-full border border-gray-200 bg-white text-2xl font-light text-gray-700 hover:border-red-400 hover:text-red-500 transition">
                                                &minus;
                                            </button>
                                            <input
                                                id="quantity-input"
                                                type="number"
                                                name="quantity"
                                                value="<?php echo esc_attr(isset($_POST['quantity']) ? wc_stock_amount(wp_unslash($_POST['quantity'])) : $product->get_min_purchase_quantity()); ?>"
                                                min="<?php echo esc_attr($product->get_min_purchase_quantity()); ?>"
                                                max="<?php echo esc_attr(0 < $product->get_max_purchase_quantity() ? $product->get_max_purchase_quantity() : ''); ?>"
                                                step="1"
                                                class="quantity-input w-8 text-center border-0 outline-none text-xl font-semibold bg-transparent"
                                                inputmode="numeric"
                                                pattern="[0-9]*"
                                                style="pointer-events: none;"
                                                readonly>
                                            <button type="button" aria-label="Increase quantity"
                                                class="quantity-btn quantity-plus flex items-center justify-center w-12 h-12 rounded-full border border-gray-200 bg-white text-2xl font-light text-gray-700 hover:border-red-400 hover:text-red-500 transition">
                                                &#43;
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex gap-4 w-full items-center">
                                        <!-- Add to Cart Button -->
                                        <button type="submit"
                                            name="add-to-cart"
                                            value="<?php echo esc_attr($product->get_id()); ?>"
                                            class="flex-1 text-base single_add_to_cart_button bg-red-500 hover:bg-red-600 text-white font-semibold py-4 px-8 rounded-xl transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed shadow-none border-0">
                                            <span class="button-text">Add to Cart</span>
                                            <span class="loading-spinner hidden">
                                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                            <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 9H19m-7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                            </svg>
                                        </button>

                                        <!-- Add to Wishlist Button -->
                                        <?php if (function_exists('YITH_WCWL')) : ?>
                                            <div class="flex-1 yith-wishlist-wrapper">
                                                <?php
                                                $label = '<svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg> Add to Wishlist';
                                                echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . $product->get_id() . '" label="' . $label . '" browse_wishlist_text="Browse Wishlist" already_in_wishlist_text="In Wishlist" product_added_text="Product added to wishlist!" icon="none" link_classes="wishlist-btn whitespace-nowrap w-full border-2 border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-500 font-semibold py-4 px-8 rounded-xl transition-colors flex items-center justify-center gap-2 bg-white shadow-none text-base" ]');
                                                ?>
                                            </div>
                                        <?php else : ?>
                                            <button type="button" class="flex-1 add-to-wishlist-fallback whitespace-nowrap w-full border-2 border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-500 font-semibold py-4 px-8 rounded-xl transition-colors flex items-center justify-center gap-2 bg-white shadow-none text-base" data-product-id="<?php echo esc_attr($product->get_id()); ?>">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                                Add to Wishlist
                                            </button>
                                        <?php endif; ?>

                                        <!-- Share Button -->
                                        <button type="button" 
                                            id="share-product-btn"
                                            class="flex items-center justify-center border-2 border-gray-300 text-gray-700 hover:border-gray-400 font-semibold py-4 px-4 rounded-full transition-colors bg-white shadow-none w-[50px] h-[50px] min-w-[50px] min-h-[50px] max-w-[50px] max-h-[50px]"
                                            data-product-title="<?php echo esc_attr($product->get_name()); ?>"
                                            data-product-url="<?php echo esc_url(get_permalink()); ?>"
                                            data-product-price="<?php echo esc_attr($product->get_price_html()); ?>"
                                            title="Share this product">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                            </svg>
                                        </button>
                                    </div>

                                    <?php do_action('woocommerce_after_add_to_cart_button'); ?>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Product Tabs Section -->
                <div class="mt-16">
                    <div class="border-b border-gray-200">
                        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                            <button class="product-tab-btn active border-transparent text-red-600 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm hover:text-red-700 hover:border-red-300" data-tab="description">
                                Description
                            </button>
                            <button class="product-tab-btn border-transparent text-gray-500 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm hover:text-gray-700 hover:border-gray-300" data-tab="sizing">
                                Sizing
                            </button>
                            <button class="product-tab-btn border-transparent text-gray-500 whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm hover:text-gray-700 hover:border-gray-300" data-tab="reviews">
                                Reviews
                            </button>
                        </nav>
                    </div>

                    <div class="mt-8">
                        <!-- Description Tab -->
                        <div id="description-tab" class="product-tab-content active">
                            <div class="prose prose-gray max-w-none">
                                <?php
                                $description = $product->get_description();
                                if ($description) {
                                    // First apply WordPress content filters
                                    $content = apply_filters('the_content', $description);
                                    // Then ensure proper HTML rendering
                                    echo wp_kses_post($content);
                                }
                                ?>
                            </div>
                        </div>

                        <!-- Sizing Tab -->
                        <!-- Sizing Tab -->
                        <div id="sizing-tab" class="product-tab-content pb-6">

                            <?php
                            global $post, $product;
                            $size_guide_id = get_post_meta(get_the_ID(), '_ct_selectsizeguide', true);

                            if (!empty($size_guide_id) && get_post_status($size_guide_id) === 'publish') {
                                $original_post = $post;
                                $post = get_post($size_guide_id);
                                $size_guide_data = maybe_unserialize(get_post_meta($size_guide_id, '_ct_sizeguide', true));
                                $post = $original_post;

                                if (!empty($size_guide_data) && is_array($size_guide_data)) {
                            ?>

                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-bold text-gray-900">Size Guide</h3>
                                        <div class="flex bg-gray-100 rounded-full p-1">
                                            <button id="tab-inches-btn" class="px-4 py-1 text-sm font-medium text-red-500 bg-white rounded-full focus:outline-none">Inches</button>
                                            <button id="tab-cm-btn" class="px-4 py-1 text-sm font-medium text-gray-600 hover:text-red-500 rounded-full focus:outline-none">Centimeters</button>
                                        </div>
                                    </div>

                            <?php
                                    foreach ($size_guide_data as $index => $guide) {
                                        $title = strtolower(trim($guide['title']));
                                        $table_data = $guide['table'] ?? [];

                                        // Determine the ID to show/hide tables
                                        $table_id = ($title === 'cm' || $title === 'centimeters') ? 'cm-table' : 'inches-table';
                                        $hidden = ($table_id === 'cm-table') ? 'hidden' : '';

                                        echo '<div id="' . esc_attr($table_id) . '" class="' . $hidden . '">';
                                        echo '<div class="overflow-auto">';
                                        echo '<table class="min-w-full border border-gray-200 text-sm text-left">';
                                        foreach ($table_data as $i => $row) {
                                            echo '<tr class="' . ($i === 0 ? 'bg-gray-100 font-semibold text-gray-800' : '') . '">';
                                            foreach ($row as $cell) {
                                                echo '<td class="border px-4 py-2 text-gray-700">' . esc_html($cell) . '</td>';
                                            }
                                            echo '</tr>';
                                        }
                                        echo '</table>';
                                        echo '</div>';
                                        echo '</div>';
                                    }
                                } else {
                                    echo '<p class="text-center text-gray-500 py-6">Size guide data is not available.</p>';
                                }
                            } else {
                                echo '<p class="text-center text-gray-500 py-6">No size guide assigned to this product.</p>';
                            }
                            ?>

                        </div>

                        <script>
                            // Toggle tables based on button click
                            document.addEventListener("DOMContentLoaded", function() {
                                const btnInches = document.getElementById("tab-inches-btn");
                                const btnCm = document.getElementById("tab-cm-btn");
                                const tableInches = document.getElementById("inches-table");
                                const tableCm = document.getElementById("cm-table");

                                btnInches.addEventListener("click", function() {
                                    tableInches.classList.remove("hidden");
                                    tableCm.classList.add("hidden");
                                    btnInches.classList.add("bg-white", "text-red-500");
                                    btnCm.classList.remove("bg-white", "text-red-500");
                                    btnCm.classList.add("text-gray-600");
                                });

                                btnCm.addEventListener("click", function() {
                                    tableInches.classList.add("hidden");
                                    tableCm.classList.remove("hidden");
                                    btnCm.classList.add("bg-white", "text-red-500");
                                    btnInches.classList.remove("bg-white", "text-red-500");
                                    btnInches.classList.add("text-gray-600");
                                });
                            });
                        </script>


                        <?php
                        /**
                         * Custom Reviews Tab Template
                         * Replace your existing reviews tab content with this code
                         */

                        // Get product ID (assuming this is for WooCommerce products)
                        global $product;
                        $product_id = get_the_ID();

                        // Get all approved comments/reviews for this product
                        $reviews = get_comments(array(
                            'post_id' => $product_id,
                            'status' => 'approve',
                            'type' => 'review'
                        ));

                        // Calculate average rating
                        $total_rating = 0;
                        $review_count = count($reviews);

                        if ($review_count > 0) {
                            foreach ($reviews as $review) {
                                $rating = get_comment_meta($review->comment_ID, 'rating', true);
                                $total_rating += intval($rating);
                            }
                            $average_rating = round($total_rating / $review_count, 1);
                        } else {
                            $average_rating = 0;
                        }
                        ?>

                        <div id="reviews-tab" class="product-tab-content">
                            <div class="reviews-container">
                                <!-- Header Section -->
                                <div class="reviews-header flex justify-between items-center mb-8 border-b border-gray-200 pb-6">
                                    <div class="reviews-summary">
                                        <h2 class="text-2xl font-bold text-gray-900 mb-2">Customer Reviews</h2>
                                        <div class="rating-summary flex items-center gap-3">
                                            <div class="stars flex">
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <svg class="w-5 h-5 <?= $i <= $average_rating ? 'text-yellow-400' : 'text-gray-300' ?>"
                                                        fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                    </svg>
                                                <?php endfor; ?>
                                            </div>
                                            <span class="text-sm text-gray-600">Based on <?= $review_count ?> reviews</span>
                                        </div>
                                    </div>

                                    <!-- Write Review Button -->
                                    <button id="write-review-btn" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-full font-medium transition-colors">
                                        Write a Review
                                    </button>
                                </div>

                                <!-- Review Form (Initially Hidden) -->
                                <div id="review-form-container" class="review-form-container hidden mb-8 p-6 bg-gray-50 rounded-lg">
                                    <h3 class="text-lg font-semibold mb-4">Write Your Review</h3>

                                    <?php if (is_user_logged_in()): ?>
                                        <form id="review-form" method="post" action="<?= esc_url(admin_url('admin-post.php')) ?>">
                                            <?php wp_nonce_field('submit_review', 'review_nonce'); ?>
                                            <input type="hidden" name="action" value="submit_product_review">
                                            <input type="hidden" name="product_id" value="<?= $product_id ?>">

                                            <div class="mb-4">
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                                <div class="rating-input flex gap-1">
                                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                                        <button type="button" class="star-btn text-2xl text-gray-300 hover:text-yellow-400 transition-colors" data-rating="<?= $i ?>">
                                                            ★
                                                        </button>
                                                    <?php endfor; ?>
                                                    <input type="hidden" name="rating" id="rating-input" value="5" required>
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label for="review-title" class="block text-sm font-medium text-gray-700 mb-2">Review Title</label>
                                                <input type="text" id="review-title" name="review_title"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                                    required>
                                            </div>

                                            <div class="mb-4">
                                                <label for="review-content" class="block text-sm font-medium text-gray-700 mb-2">Your Review</label>
                                                <textarea id="review-content" name="review_content" rows="4"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-pink-500 focus:border-transparent"
                                                    required></textarea>
                                            </div>

                                            <div class="flex gap-3">
                                                <button type="submit" class="bg-pink-500 hover:bg-pink-600 text-white px-6 py-2 rounded-md font-medium transition-colors">
                                                    Submit Review
                                                </button>
                                                <button type="button" id="cancel-review" class="bg-gray-300 hover:bg-gray-400 text-gray-700 px-6 py-2 rounded-md font-medium transition-colors">
                                                    Cancel
                                                </button>
                                            </div>
                                        </form>
                                    <?php else: ?>
                                        <p class="text-gray-600">
                                            Please <a href="<?= wp_login_url(get_permalink()) ?>" class="text-pink-500 hover:text-pink-600">log in</a> to write a review.
                                        </p>
                                    <?php endif; ?>
                                </div>

                                <!-- Reviews List -->
                                <div class="reviews-list space-y-6">
                                    <?php if ($review_count > 0): ?>
                                        <?php foreach ($reviews as $review):
                                            $rating = get_comment_meta($review->comment_ID, 'rating', true);
                                            $review_images = get_comment_meta($review->comment_ID, 'ivole_review_image2', false);
                                            $reviewer_name = $review->comment_author;
                                            $review_date = get_comment_date('F j, Y', $review->comment_ID);
                                            $review_content = $review->comment_content;
                                        ?>
                                            <div class="review-item border-b border-gray-200 pb-6 last:border-b-0">
                                                <div class="review-header mb-3">
                                                    <div class="review-meta flex items-center gap-4 mb-2">
                                                        <div class="stars flex">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <svg class="w-4 h-4 <?= $i <= $rating ? 'text-yellow-400' : 'text-gray-300' ?>"
                                                                    fill="currentColor" viewBox="0 0 20 20">
                                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                                                </svg>
                                                            <?php endfor; ?>
                                                        </div>
                                                        <span class="text-sm font-medium text-gray-900"><?= esc_html($reviewer_name) ?></span>
                                                        <span class="text-sm text-gray-500"><?= $review_date ?></span>
                                                    </div>
                                                    <div class="review-content mt-3">
                                                        <p class="text-gray-700"><?= wp_kses_post($review_content) ?></p>
                                                    </div>
                                                    <?php if (!empty($review_images)): ?>
                                                        <div class="review-images flex gap-2 mt-2">
                                                            <?php foreach ($review_images as $image_id): ?>
                                                                <div>
                                                                    <a href="<?php echo wp_get_attachment_image_url($image_id, 'full'); ?>" target="_blank">
                                                                        <?php echo wp_get_attachment_image($image_id, 'thumbnail', false, array('class' => 'review-image-thumb')); ?>
                                                                    </a>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="no-reviews text-center py-12">
                                            <div class="text-gray-400 mb-4">
                                                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.955 8.955 0 01-4.126-.98L3 21l1.98-5.874A8.955 8.955 0 013 12a8 8 0 018-8c4.418 0 8 3.582 8 8z" />
                                                </svg>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-2">No reviews yet</h3>
                                            <p class="text-gray-500">Be the first to review this product!</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <style>
                            /* Custom CSS for additional styling */
                            .reviews-container {
                                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                            }

                            .star-btn:hover,
                            .star-btn.active {
                                color: #fbbf24 !important;
                            }

                            .review-form-container {
                                animation: slideDown 0.3s ease-out;
                            }

                            .review-image-thumb {
                                width: 75%;
                                height: 25%;
                                object-fit: cover;
                                border-radius: 8px;
                                box-shadow: 0 1px 4px rgba(0, 0, 0, 0.04);
                                border: 1px solid #eee;
                                background: #fff;
                                display: block;
                            }

                            @keyframes slideDown {
                                from {
                                    opacity: 0;
                                    transform: translateY(-10px);
                                }

                                to {
                                    opacity: 1;
                                    transform: translateY(0);
                                }
                            }

                            .review-item:hover {
                                background-color: #fafafa;
                                border-radius: 8px;
                                padding: 1.5rem;
                                margin: -1.5rem;
                                transition: all 0.2s ease;
                            }
                        </style>

                        <script>
                            jQuery(document).ready(function($) {
                                // Show/hide review form
                                $('#write-review-btn').click(function() {
                                    $('#review-form-container').toggleClass('hidden');
                                    if (!$('#review-form-container').hasClass('hidden')) {
                                        $('html, body').animate({
                                            scrollTop: $('#review-form-container').offset().top - 100
                                        }, 500);
                                    }
                                });

                                $('#cancel-review').click(function() {
                                    $('#review-form-container').addClass('hidden');
                                });

                                // Star rating functionality
                                $('.star-btn').click(function() {
                                    var rating = $(this).data('rating');
                                    $('#rating-input').val(rating);

                                    $('.star-btn').removeClass('active').each(function(index) {
                                        if (index < rating) {
                                            $(this).addClass('active');
                                        }
                                    });
                                });

                                // Initialize with 5 stars
                                $('.star-btn').each(function(index) {
                                    if (index < 5) {
                                        $(this).addClass('active');
                                    }
                                });
                            });
                        </script>

                    </div>
                </div>

               <!-- You May Also Like Section -->
<div class="mt-20">
    <div class="flex items-center justify-between mb-12">
        <div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">You May Also Like</h2>
            <p class="text-gray-600 text-lg">Discover more amazing products</p>
        </div>
        <a href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>" class="inline-flex items-center gap-2 text-red-500 hover:text-red-600 font-semibold text-base transition-colors group">
            View All Products 
            <svg class="w-5 h-5 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
            </svg>
        </a>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <?php
        // Get related products
        $related_products = wc_get_related_products($product->get_id(), 4);
        if ($related_products) {
            foreach ($related_products as $related_id) {
                $related_product = wc_get_product($related_id);
                if ($related_product) {
                    $product_id = $related_product->get_id();
                    $product_link = get_permalink($product_id);
                    $product_img = get_the_post_thumbnail_url($product_id, 'woocommerce_thumbnail');
                    $product_title = $related_product->get_name();
                    $product_price = $related_product->get_price_html();
                    $review_count = $related_product->get_review_count();
                    $average = $related_product->get_average_rating();
                    $badge_label = '';
                    $badge_class = '';
                    
                    if ($related_product->is_on_sale()) {
                        $badge_label = 'SALE';
                        $badge_class = 'bg-[#ed1c24]';
                    } elseif ((time() - strtotime($related_product->get_date_created())) < (30 * 24 * 60 * 60)) {
                        $badge_label = 'NEW';
                        $badge_class = 'bg-blue-500';
                    } elseif ($related_product->get_attribute('pa_limited') || $related_product->get_attribute('limited')) {
                        $badge_label = 'LIMITED';
                        $badge_class = 'bg-purple-500';
                    }
        ?>
                    <a href="<?php echo esc_url($product_link); ?>" class="block group">
                        <div class="rounded-lg border text-card-foreground bg-white border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300 group">
                            <div class="relative">
                                <div class="aspect-square relative overflow-hidden">
                                    <?php if ($product_img) : ?>
                                        <img 
                                            src="<?php echo esc_url($product_img); ?>" 
                                            alt="<?php echo esc_attr($product_title); ?>" 
                                            loading="lazy" 
                                            decoding="async" 
                                            class="object-cover group-hover:scale-105 transition-transform duration-500"
                                            style="position: absolute; height: 100%; width: 100%; inset: 0px; color: transparent;"
                                        >
                                    <?php else : ?>
                                        <div class="w-full h-full bg-gray-200 flex items-center justify-center" style="position: absolute; height: 100%; width: 100%; inset: 0px;">
                                            <span class="text-gray-400">No Image</span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Badge -->
                                <?php if ($badge_label) : ?>
                                    <div class="inline-flex items-center rounded-full border font-semibold transition-colors focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 border-transparent hover:bg-primary/80 absolute top-3 left-3 <?php echo esc_attr($badge_class); ?> text-white text-xs px-2 py-1 z-10">
                                        <?php echo esc_html($badge_label); ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Wishlist Icon -->
                                <div class="absolute top-3 right-3 z-10">
                                    <?php
                                    if (function_exists('YITH_WCWL')) {
                                        echo do_shortcode('[yith_wcwl_add_to_wishlist product_id="' . esc_attr($product_id) . '" label="" browse_wishlist_text="" already_in_wishlist_text="" product_added_text="" show_count="no"]');
                                    } else {
                                        ?>
                                        <a href="<?php echo esc_url(add_query_arg('add_to_wishlist', $product_id)); ?>"
                                           class="w-10 h-10 rounded-full flex items-center justify-center transition-all duration-300 bg-white/90 text-gray-600 hover:bg-white hover:text-[#ed1c24] shadow-md"
                                           aria-label="Add to wishlist"
                                           data-product-id="<?php echo esc_attr($product_id); ?>">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-heart w-5 h-5 transition-all">
                                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"></path>
                                            </svg>
                                        </a>
                                        <?php
                                    }
                                    ?>
                                </div>
                                
                                <!-- Overlay Actions -->
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <div class="absolute bottom-4 left-4 right-4 flex flex-col gap-3">
                                        <button 
                                            class="gap-2 whitespace-nowrap rounded-md text-sm ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 border px-4 py-2 w-full bg-white/95 border-white text-gray-900 hover:bg-white hover:text-black font-medium h-11 backdrop-blur-sm flex items-center justify-center quick-view-btn"
                                            data-product-id="<?php echo esc_attr($product_id); ?>"
                                            data-product-url="<?php echo esc_url($product_link); ?>"
                                            onclick="event.preventDefault();"
                                            aria-label="Quick view product"
                                        >
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-eye h-4 w-4 mr-2">
                                                    <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                                Quick View
                                            </span>
                                        </button>
                                        
                                        <?php if ($related_product->get_type() === 'variable') : ?>
                                            <button
                                                type="button"
                                                class="gap-2 whitespace-nowrap rounded-md text-sm ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 px-4 py-2 w-full bg-[#ed1c24] hover:bg-[#ed1c24]/90 text-white font-medium h-11 shadow-lg flex items-center justify-center"
                                                onclick="event.preventDefault(); window.location.href='<?php echo esc_url($product_link); ?>';"
                                                aria-label="Select options"
                                            >
                                                <span class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-4 w-4 mr-2">
                                                        <circle cx="8" cy="21" r="1"></circle>
                                                        <circle cx="19" cy="21" r="1"></circle>
                                                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                                                    </svg>
                                                    Select Options
                                                </span>
                                            </button>
                                        <?php else : ?>
                                            <button
                                                type="button"
                                                class="ajax-add-to-cart gap-2 whitespace-nowrap rounded-md text-sm ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 [&_svg]:pointer-events-none [&_svg]:size-4 [&_svg]:shrink-0 px-4 py-2 w-full bg-[#ed1c24] hover:bg-[#ed1c24]/90 text-white font-medium h-11 shadow-lg flex items-center justify-center"
                                                data-product_id="<?php echo esc_attr($product_id); ?>"
                                                data-product_sku="<?php echo esc_attr($related_product->get_sku()); ?>"
                                                data-quantity="1"
                                                onclick="event.preventDefault();"
                                                aria-label="Add to cart"
                                            >
                                                <span class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-shopping-cart h-4 w-4 mr-2">
                                                        <circle cx="8" cy="21" r="1"></circle>
                                                        <circle cx="19" cy="21" r="1"></circle>
                                                        <path d="M2.05 2.05h2l2.66 12.42a2 2 0 0 0 2 1.58h9.78a2 2 0 0 0 1.95-1.57l1.65-7.43H5.12"></path>
                                                    </svg>
                                                    <span class="add-to-cart-text">Add to Cart</span>
                                                </span>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-4">
                                <div class="space-y-2">
                                    <h3 class="font-semibold text-gray-900 group-hover:text-[#ed1c24] transition-colors line-clamp-2 leading-tight">
                                        <?php echo esc_html($product_title); ?>
                                    </h3>
                                    
                                    <div class="flex items-center gap-1">
                                        <?php
                                        for ($i = 1; $i <= 5; $i++) {
                                            if ($i <= round($average)) {
                                                echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 fill-yellow-400 text-yellow-400"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
                                            } else {
                                                echo '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-star h-3.5 w-3.5 text-gray-300"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>';
                                            }
                                        }
                                        ?>
                                        <span class="text-xs text-gray-500 ml-1">(<?php echo esc_html($review_count); ?>)</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-2 pt-1">
                                        <?php if ($related_product->is_on_sale()) : ?>
                                            <span class="font-bold text-[#ed1c24] text-lg">
                                                <?php echo wc_price($related_product->get_sale_price()); ?>
                                            </span>
                                            <span class="text-sm text-gray-400 line-through">
                                                <?php echo wc_price($related_product->get_regular_price()); ?>
                                            </span>
                                        <?php else : ?>
                                            <span class="font-semibold text-gray-900 text-lg">
                                                <?php echo wc_price($related_product->get_price()); ?>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
        <?php
                }
            }
        } else {
            // No related products found
            echo '<div class="col-span-full text-center py-12">';
            echo '<div class="text-gray-400 mb-4">';
            echo '<svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">';
            echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>';
            echo '</svg>';
            echo '</div>';
            echo '<h3 class="text-lg font-medium text-gray-900 mb-2">No related products found</h3>';
            echo '<p class="text-gray-500">Check back later for more amazing products!</p>';
            echo '</div>';
        }
        ?>
    </div>
</div> 


            </div>
        <?php endwhile; ?>
    </div>
</div>

<!-- Size Guide Popup Modal -->
<div id="size-guide-modal" class="fixed inset-0 z-50 hidden overflow-y-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="relative bg-white rounded-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden shadow-2xl">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 bg-gray-50">
            <h3 class="text-xl font-bold text-gray-900">Size Guide</h3>
            <button type="button" id="close-size-guide" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="p-6 overflow-y-auto max-h-[calc(90vh-120px)]">
            <div id="size-guide-content" class="prose prose-gray max-w-none">
                <?php
                global $post, $product;
                $size_guide_id = get_post_meta(get_the_ID(), '_ct_selectsizeguide', true);

                if (!empty($size_guide_id) && get_post_status($size_guide_id) === 'publish') {
                    $original_post = $post;
                    $post = get_post($size_guide_id);
                    $size_guide_data = maybe_unserialize(get_post_meta($size_guide_id, '_ct_sizeguide', true));
                    $post = $original_post;

                    if (!empty($size_guide_data) && is_array($size_guide_data)) {
                ?>
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Size Guide</h3>
                            <div class="flex bg-gray-100 rounded-full p-1">
                                <button id="modal-inches-btn" class="px-4 py-1 text-sm font-medium text-red-500 bg-white rounded-full focus:outline-none">Inches</button>
                                <button id="modal-cm-btn" class="px-4 py-1 text-sm font-medium text-gray-600 hover:text-red-500 rounded-full focus:outline-none">Centimeters</button>
                            </div>
                        </div>

                <?php
                        foreach ($size_guide_data as $index => $guide) {
                            $title = strtolower(trim($guide['title']));
                            $table_data = $guide['table'] ?? [];

                            // Determine the ID to show/hide tables
                            $table_id = ($title === 'cm' || $title === 'centimeters') ? 'modal-cm-table' : 'modal-inches-table';
                            $hidden = ($table_id === 'modal-cm-table') ? 'hidden' : '';

                            echo '<div id="' . esc_attr($table_id) . '" class="' . $hidden . '">';
                            echo '<div class="overflow-auto">';
                            echo '<table class="min-w-full border border-gray-200 text-sm text-left">';
                            foreach ($table_data as $i => $row) {
                                echo '<tr class="' . ($i === 0 ? 'bg-gray-100 font-semibold text-gray-800' : '') . '">';
                                foreach ($row as $cell) {
                                    echo '<td class="border px-4 py-2 text-gray-700">' . esc_html($cell) . '</td>';
                                }
                                echo '</tr>';
                            }
                            echo '</table>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
    .product-gallery-image {
        width: 8rem !important;
    }

    /* Custom CSS for pixel-perfect design */
    .color-swatch.selected {
        @apply ring-2 ring-red-500 ring-offset-2;
    }

    .size-btn.selected {
        @apply border-red-500 bg-red-50 text-red-600;
    }

    .thumbnail-image.active {
        @apply border-red-500;
    }

    .product-tab-btn.active {
        @apply border-red-500 text-red-600;
        border-bottom: 2px solid #ed1c24 !important;
    }

    .product-tab-btn {
        font-size: 1.2rem !important;
    }

    .product-tab-content {
        display: none;
    }

    .product-tab-content.active {
        display: block;
    }

    /* Size Guide Modal Styling */
    #size-guide-modal {
        backdrop-filter: blur(4px);
    }

    #size-guide-modal .prose {
        color: inherit;
    }

    #size-guide-modal .prose h1,
    #size-guide-modal .prose h2,
    #size-guide-modal .prose h3,
    #size-guide-modal .prose h4 {
        color: #1f2937;
        font-weight: 600;
    }

    #size-guide-modal .prose table {
        width: 100%;
        border-collapse: collapse;
        margin: 1rem 0;
        font-size: 0.9rem;
    }

    #size-guide-modal .prose table th,
    #size-guide-modal .prose table td {
        border: 1px solid #e5e7eb;
        padding: 0.75rem;
        text-align: center;
    }

    #size-guide-modal .prose table th {
        background-color: #f9fafb;
        font-weight: 600;
        color: #374151;
    }

    #size-guide-modal .prose table tr:nth-child(even) {
        background-color: #f9fafb;
    }

    #size-guide-modal .prose table tr:hover {
        background-color: #fef2f2;
    }

    /* YITH Wishlist Styling Override */
    .yith-wishlist-wrapper .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a,
    .yith-wishlist-wrapper .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
    .yith-wishlist-wrapper .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a {
        @apply w-full border-2 border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-500 font-semibold py-4 px-8 rounded-lg transition-colors flex items-center justify-center gap-2 !important;
        background-color: white !important;
        text-decoration: none !important;
    }

    /* Cart Messages */
    .cart-messages .alert {
        @apply p-4 rounded-lg mb-4;
    }

    .cart-messages .alert.success {
        @apply bg-green-50 border border-green-200 text-green-800;
    }

    .cart-messages .alert.error {
        @apply bg-red-50 border border-red-200 text-red-800;
    }

    /* Loading States */
    .single_add_to_cart_button.loading {
        @apply opacity-60 cursor-not-allowed;
    }

    .single_add_to_cart_button.loading .button-text {
        display: none;
    }

    .single_add_to_cart_button.loading .loading-spinner {
        display: inline-flex !important;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .product-summary-section .sticky {
            position: static;
        }
    }

    @media (max-width: 640px) {
        .thumbnail-container .flex {
            justify-content: center;
        }

        .thumbnail-item img {
            @apply w-16 h-16;
        }

        /* Fix for product gallery images on mobile */
        .product-gallery-image {
            width: 4rem !important;
            height: 4rem !important;
        }

        .space-y-3>*+* {
            margin-top: 0.75rem;
        }

        #size-guide-modal .relative {
            margin: 1rem;
            max-height: calc(100vh - 2rem);
        }
    }

    /* Additional mobile fixes for thumbnails */
    @media (max-width: 480px) {
        .product-gallery-image {
            width: 3.5rem !important;
            height: 3.5rem !important;
        }
        
        .thumbnail-item img {
            @apply w-14 h-14;
        }
        
        .thumbnail-container .flex {
            gap: 0.5rem !important;
        }
    }

    /* Line clamp utility */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Original styles preserved for backward compatibility */
    .add-to-wishlist {
        background-color: #fff !important;
        color: #ed1c24 !important;
        border: 2px solid #ed1c24 !important;
        padding: 10px !important;
        height: 40px !important;
        margin-top: 20px !important;
    }

    .single-product-wrapper {
        margin: 20px 0;
    }

    .product-images {
        margin-bottom: 30px;
    }

    .product_title {
        font-size: 2em;
        margin-bottom: 15px;
        color: #333;
    }

    .woocommerce-Price-amount.amount {
        color: #ed1c24 !important;
    }

    .price {
        font-size: 1.5em;
        font-weight: bold;
        color: #77a464;
    }

    .variations {
        width: 100%;
        margin-bottom: 20px;
    }

    .variations td {
        padding: 8px 0;
        vertical-align: middle;
    }

    .variations .label {
        font-weight: bold;
        width: 30%;
    }

    .variations .value {
        width: 70%;
    }

    .variations select {
        width: 100%;
        padding: 8px;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .woocommerce-variation-add-to-cart {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 20px;
    }

    .woocommerce-variation-add-to-cart button {
        background-color: #ed1c24 !important;
        color: #fff !important;
        border: none !important;
        border-radius: 4px !important;
        cursor: pointer !important;
        font-size: 16px !important;
        transition: background-color 0.3s !important;
    }

    .quantity input {
        width: 80px;
        padding: 8px;
        text-align: center;
        border: 1px solid #ddd;
        border-radius: 4px;
    }

    .single_add_to_cart_button {
        background-color: #ED1C24;
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .single_add_to_cart_button:hover {
        background-color: #5a7c4a;
    }

    .single_add_to_cart_button.disabled {
        background-color: #ccc;
        cursor: not-allowed;
    }

    .product_meta {
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .product_meta span {
        display: block;
        margin-bottom: 8px;
    }

    /* Cart Messages */
    .cart-messages {
        margin-bottom: 1rem;
    }

    .cart-messages .alert {
        padding: 0.75rem 1rem;
        border-radius: 0.375rem;
        margin-bottom: 0.5rem;
    }

    .cart-messages .alert.success {
        background-color: #d1fae5;
        border: 1px solid #34d399;
        color: #065f46;
    }

    .cart-messages .alert.error {
        background-color: #fee2e2;
        border: 1px solid #f87171;
        color: #991b1b;
    }

    /* Loading Spinner */
    .loading-spinner svg {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 768px) {
        .woocommerce-variation-add-to-cart {
            flex-direction: column;
            align-items: stretch;
        }

        .single_add_to_cart_button {
            width: 100%;
            margin-top: 10px;
        }

        .yith-wishlist-wrapper {
            width: 100% !important;
            margin-top: 12px !important;
        }
    }

    /* Custom styles for color swatches and size buttons */
    .product-summary .w-7.h-7 {
        display: inline-block;
        box-shadow: 0 0 0 2px #fff, 0 0 0 3px #e5e7eb;
        transition: box-shadow 0.2s;
    }

    .product-summary .w-7.h-7.ring-2 {
        box-shadow: 0 0 0 2px #fff, 0 0 0 3px #ec4899;
    }

    .product-summary .w-9.h-9 {
        transition: border-color 0.2s, color 0.2s;
    }

    .product-summary .w-9.h-9.border-2 {
        border-width: 2px;
    }

    .woocommerce #content div.product div.images,
    .woocommerce div.product div.images,
    .woocommerce-page #content div.product div.images,
    .woocommerce-page div.product div.images {
        width: 100%;
        object-fit: cover;
    }

    .woocommerce div.product div.summary {
        width: 100%;
        padding: 0 30px;
    }

    /* Main product image */
    .woocommerce div.product div.images img,
    .woocommerce-page div.product div.images img {
        width: 100%;
        /* Makes image responsive */
        max-width: 500px;
        /* Set your desired max width */
        height: 500px;
        /* Fixed height */
        object-fit: cover;
        /* Ensures image covers the area without distortion */
        border-radius: 8px;
        /* Optional: rounded corners */
        margin: 0 auto 16px auto !important;
        display: block;
    }

    /* Gallery thumbnails */
    .woocommerce div.product div.images .thumbnails img,
    .woocommerce-page div.product div.images .thumbnails img {
        width: 100px;
        /* Fixed width for thumbnails */
        min-height: 100px !important;
        /* Fixed height for thumbnails */
        object-fit: cover;
        /* Ensures thumbnails are not distorted */
        border-radius: 6px;
        /* Optional: rounded corners */
        margin-right: 10px;
        border: 1px solid #eee;
        transition: border 0.2s;
        cursor: pointer;
    }

    .woocommerce-product-gallery__trigger {
        right: 6.5rem !important;
    }

    .woocommerce div.product div.images .thumbnails img:hover,
    .woocommerce-page div.product div.images .thumbnails img:hover {
        border: 1px solid #ed1c24;
    }

    img.flex-active {
        border: 2px solid #ed1c24 !important;
    }

    .flex-control-nav.flex-control-thumbs li img {
        width: 150px !important;
        height: 150px !important;
    }

    /* Custom WooCommerce Product Tabs */
    .woocommerce-tabs .wc-tabs {
        display: flex;
        border-bottom: 2px solid #f3f4f6 !important;
        margin-bottom: 0;
        padding-left: 0;
        gap: 2rem;
        background: none;
        box-shadow: none;
    }

    .woocommerce-tabs .wc-tabs li {
        margin: 0;
        padding: 0;
        border: none !important;
        background: none !important;
        list-style: none;
    }

    .woocommerce-tabs .wc-tabs li a {
        display: inline-block;
        padding: 0 0 8px 0;
        font-size: 1.25rem;
        color: #64748b;
        font-weight: 500;
        border: none;
        background: none;
        text-decoration: none;
        transition: color 0.2s;
        position: relative;
    }

    .woocommerce-tabs .wc-tabs li.active a,
    .woocommerce-tabs .wc-tabs li a:focus,
    .woocommerce-tabs .wc-tabs li a:hover {
        color: #ed1c24 !important;
        font-weight: 600 !important;
    }

    .woocommerce-tabs .wc-tabs li.active a::after {
        content: "";
        display: block;
        height: 5px;
        width: 100%;
        background: #ed1c24;
        border-radius: 2px;
        position: absolute;
        left: 0;
        bottom: -2px;
    }

    .woocommerce-tabs .wc-tab {
        padding: 2rem 0 0 0;
        border: none;
        background: none;
    }

    .woocommerce-Tabs-panel h2 {
        display: none;
    }

    .quantity-input-wrapper input.qty {
        width: 48px;
        text-align: center;
        border: none;
        background: transparent;
        font-size: 1.1rem;
        font-weight: 500;
        outline: none;
    }

    .quantity-minus,
    .quantity-plus {
        min-width: 44px;
        min-height: 44px;
        font-size: 1.5rem;
        line-height: 1;
        background: #fff;
    }

    @media (max-width: 640px) {
        .flex-row.gap-4.w-full {
            flex-direction: column !important;
            gap: 0.75rem !important;
        }

        .flex-1 {
            width: 100% !important;
        }
    }

    /* Pixel-perfect quantity selector styles */
    .quantity-btn {
        width: 30px !important;
        height: 30px !important;
        border-radius: 50% !important;
        border: 1px solid #e5e7eb !important;
        background: #fff !important;
        font-size: 2rem !important;
        font-weight: 300 !important;
        display: flex !important;
        align-items: center;
        justify-content: center;
        transition: border 0.2s, color 0.2s;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .quantity-btn:hover,
    .quantity-btn:focus {
        border-color: #f87171 !important;
        color: #ef4444 !important;
        outline: none !important;
    }

    .quantity-input {
        width: 2rem !important;
        min-width: 2rem !important;
        font-size: 1.25rem !important;
        font-weight: 600 !important;
        text-align: center !important;
        border: none !important;
        background: transparent !important;
        pointer-events: none !important;
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type=number] {
        -moz-appearance: textfield;
    }

    .yith-wcwl-add-to-wishlist {
        margin-top: 0px !important;
    }

    /* Pixel-perfect Add to Wishlist button styling */
    .wishlist-btn,
    .add-to-wishlist-fallback {
        display: flex !important;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        min-width: 0;
        height: 60px;
        padding: 0 2rem;
        border: 2px solid #d1d5db;
        /* gray-300 */
        background: #fff;
        color: #222;
        font-size: 1.125rem;
        font-weight: 600;
        border-radius: 1rem;
        transition: border-color 0.2s, color 0.2s, background 0.2s;
        box-shadow: none;
        cursor: pointer;
        text-align: center;
    }

    .wishlist-btn svg,
    .add-to-wishlist-fallback svg {
        width: 1.5rem;
        height: 1.5rem;
        margin-left: 0.5rem;
        color: #222;
    }

    .wishlist-btn:hover,
    .add-to-wishlist-fallback:hover {
        border-color: #ef4444;
        /* red-500 */
        color: #ef4444;
        background: #fff;
    }

    .wishlist-btn:active,
    .add-to-wishlist-fallback:active {
        border-color: #b91c1c;
        /* red-700 */
        color: #b91c1c;
    }

    .wishlist-btn.disabled,
    .add-to-wishlist-fallback.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .yith-wcwl-add-button a {
        /* Your custom styles here */
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid #d1d5db;
        background: #fff;
        color: #222;
        font-size: 1rem !important;
        font-weight: 600;
        border-radius: 10px;
        height: 50px;
        padding: 0 2rem;
        transition: border-color 0.2s, color 0.2s, background 0.2s;
        cursor: pointer;
    }

    .yith-wcwl-add-button a:hover {
        border-color: #ef4444;
        color: #ef4444;
    }

    .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a.loading,
    .yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a.loading {
        opacity: 0.5;
        cursor: wait !important;
    }

    /* --- FIX: Override to show text in custom wishlist wrapper --- */
    .custom-wishlist-wrapper .yith-wcwl-add-to-wishlist,
    .custom-wishlist-wrapper .yith-wcwl-add-to-wishlist * {
        font-size: inherit !important;
        color: inherit !important;
        text-indent: 0 !important;
        line-height: inherit !important;
    }
    .custom-wishlist-wrapper .yith-wcwl-add-to-wishlist a:before {
        display: none !important;
    }

    /* Share Button Styling */
    #share-product-btn {
        transition: all 0.2s ease-in-out;
        position: relative;
        overflow: hidden;
    }

    #share-product-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    #share-product-btn:active {
        transform: scale(0.95);
    }

    #share-product-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none;
    }

    #share-product-btn .animate-spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }
        to {
            transform: rotate(360deg);
        }
    }

    /* Fallback Share Modal Styling */
    #fallback-share-modal {
        backdrop-filter: blur(4px);
    }

    #fallback-share-modal .bg-white {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }

    #fallback-share-modal button {
        transition: all 0.2s ease;
    }

    #fallback-share-modal button:hover {
        background-color: #f3f4f6;
        transform: translateY(-1px);
    }
</style>

<script>
    jQuery(document).ready(function($) {
        // Thumbnail Image Click Handler
        $('.thumbnail-image').on('click', function() {
            var fullImageUrl = $(this).data('full');
            $('#main-product-image').attr('src', fullImageUrl);

            $('.thumbnail-image').removeClass('active border-red-500').addClass('border-gray-200');
            $(this).removeClass('border-gray-200').addClass('active border-red-500');
        });

        // Color Swatch Selection
        $('.color-swatch').on('click', function() {
            var value = $(this).data('value');
            var $form = $(this).closest('form');

            $form.find('.color-swatch').removeClass('selected ring-2 ring-red-500 ring-offset-2');
            $(this).addClass('selected ring-2 ring-red-500 ring-offset-2');
            $form.find('input.selected-color').val(value).trigger('change');

            // Update WooCommerce variation select
            var $variationSelect = $form.find('select[name="attribute_Color"]');
            if ($variationSelect.length) {
                $variationSelect.val(value).trigger('change');
            }
        });

        // Size Button Selection
        $('.size-btn').on('click', function() {
            var value = $(this).data('value');
            var $form = $(this).closest('form');

            $form.find('.size-btn').removeClass('selected border-red-500 bg-red-50 text-red-600').addClass('border-gray-300 text-gray-700');
            $(this).removeClass('border-gray-300 text-gray-700').addClass('selected border-red-500 bg-red-50 text-red-600');
            $form.find('input.selected-size').val(value).trigger('change');

            // Update WooCommerce variation select
            var $variationSelect = $form.find('select[name="attribute_Size"]');
            if ($variationSelect.length) {
                $variationSelect.val(value).trigger('change');
            }
        });



        // Product Tabs
        $('.product-tab-btn').on('click', function() {
            var tabName = $(this).data('tab');

            // Update tab buttons
            $('.product-tab-btn').removeClass('active border-red-500 text-red-600').addClass('border-transparent text-gray-500');
            $(this).removeClass('border-transparent text-gray-500').addClass('active border-red-500 text-red-600');

            // Update tab content
            $('.product-tab-content').removeClass('active').addClass('hidden');
            $('#' + tabName + '-tab').removeClass('hidden').addClass('active');
        });

        // Enhanced variation selection checking
        function checkVariationSelection() {
            $('.variations_form').each(function() {
                var $form = $(this);
                var $button = $form.find('.single_add_to_cart_button');
                var allSelected = true;

                // Check if all required attributes are selected
                $form.find('input[type="hidden"][name^="attribute_"]').each(function() {
                    if ($(this).val() === '') {
                        allSelected = false;
                        return false;
                    }
                });

                // Update button state
                if (allSelected) {
                    $button.removeClass('opacity-50 cursor-not-allowed').prop('disabled', false);
                } else {
                    $button.addClass('opacity-50 cursor-not-allowed').prop('disabled', true);
                }
            });
        }

        // Bind variation change events
        $(document).on('change', 'input[type="hidden"][name^="attribute_"]', checkVariationSelection);

        // Initial check
        checkVariationSelection();

        // Enhanced AJAX Add to Cart Handler
        function handleAddToCart(form) {
            var $form = $(form);
            var $button = $form.find('.single_add_to_cart_button');
            var $buttonText = $button.find('.button-text');
            var $spinner = $button.find('.loading-spinner');
            var $messages = $('#cart-messages');

            // Validate form before proceeding
            if ($button.hasClass('cursor-not-allowed')) {
                showMessage('Please select all product options before adding to cart.', 'error');
                return false;
            }

            // Show loading state
            $button.prop('disabled', true).addClass('loading');
            $buttonText.text('Adding...');
            $spinner.removeClass('hidden');

            // Prepare form data
            var formData = $form.serialize();
            formData += '&action=ppc_simulate_cart';

            console.log('Submitting form data:', formData);

            // Make AJAX request
            var fallbackUrl = '<?php echo admin_url('admin-ajax.php'); ?>';
            makeAjaxRequest(fallbackUrl, formData, function(success) {
                if (!success) {
                    location.reload();
                }
                resetButtonState($button, $buttonText, $spinner);
            });
        }

        // Reusable AJAX request function
        function makeAjaxRequest(url, data, callback) {
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                dataType: 'json',
                timeout: 10000,
                success: function(response) {
                    console.log('AJAX Success Response:', response);
                    handleAjaxResponse(response);
                    callback(true);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', {
                        url: url,
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });

                    var errorMsg = 'Network error occurred. Please check your connection and try again.';
                    if (xhr.status === 302) {
                        errorMsg = 'Redirect error detected. Please refresh the page and try again.';
                    }
                    showMessage(errorMsg, 'error');
                    callback(false);
                }
            });
        }

        // Handle AJAX response
        function handleAjaxResponse(response) {
            if (response && response.success) {
                var successMsg = (response.data && response.data.message) ?
                    response.data.message :
                    'Product added to cart successfully!';
                showMessage(successMsg, 'success');

                // Update cart elements with fragments
                if (response.data && response.data.fragments) {
                    $.each(response.data.fragments, function(key, value) {
                        $(key).replaceWith(value);
                    });
                }

                // Update cart count specifically
                if (response.data && response.data.cart_count !== undefined) {
                    $('.cart-count, .cart-contents-count, .cart-count-display').text(response.data.cart_count);
                }

                // Update cart total
                if (response.data && response.data.cart_total) {
                    $('.cart-total, .cart-total-display').html(response.data.cart_total);
                }

                // Trigger WooCommerce events
                $(document.body).trigger('added_to_cart', [
                    response.data.fragments || {},
                    response.data.cart_hash || '',
                    $('.single_add_to_cart_button')
                ]);

                $(document.body).trigger('wc_cart_updated');

            } else {
                var errorMsg = 'Failed to add product to cart.';
                if (response && response.data && response.data.message) {
                    errorMsg = response.data.message;
                }
                showMessage(errorMsg, 'error');
            }
        }

        // Reset button to normal state
        function resetButtonState($button, $buttonText, $spinner) {
            $button.prop('disabled', false).removeClass('loading');
            $buttonText.text('Add to Cart');
            $spinner.addClass('hidden');
            checkVariationSelection();
        }

        // Show message function with auto-hide
        function showMessage(message, type) {
            var $messages = $('#cart-messages');
            var $alert = $messages.find('.alert');

            $alert.removeClass('success error').addClass(type);
            $alert.text(message);
            $messages.removeClass('hidden');

            // Auto-hide after 5 seconds
            setTimeout(function() {
                $messages.addClass('hidden');
            }, 5000);
        }

        // Handle form submission
        $(document).on('submit', '.custom-cart-form', function(e) {
            e.preventDefault();
            e.stopPropagation();

            var $form = $(this);
            console.log('Form submitted:', $form.attr('class'));

            handleAddToCart($form);
            return false;
        });

        // Handle direct button clicks
        $(document).on('click', '.single_add_to_cart_button', function(e) {
            var $button = $(this);
            var $form = $button.closest('form');

            if ($form.length && $form.hasClass('custom-cart-form')) {
                e.preventDefault();
                e.stopPropagation();
                console.log('Button clicked, triggering form submit');
                $form.trigger('submit');
                return false;
            }
        });

        // Handle wishlist button clicks (fallback)
        $(document).on('click', '.add-to-wishlist-fallback', function(e) {
            e.preventDefault();
            var $button = $(this);
            var productId = $button.data('product-id');

            console.log('Fallback wishlist clicked for product:', productId);
            showMessage('YITH WooCommerce Wishlist plugin is required for wishlist functionality.', 'error');
        });

        // Handle YITH wishlist AJAX responses (when plugin is active)
        $(document).on('click', '.yith-wcwl-add-button a', function() {
            var $button = $(this);
            var originalText = $button.text();
            $button.text('Adding...');

            // Listen for YITH wishlist events
            $(document).one('yith_wcwl_product_added_to_wishlist', function() {
                showMessage('Product added to wishlist successfully!', 'success');
            });

            $(document).one('yith_wcwl_product_removed_from_wishlist', function() {
                showMessage('Product removed from wishlist.', 'success');
            });

            // Reset button text after delay
            setTimeout(function() {
                if ($button.text() === 'Adding...') {
                    $button.text(originalText);
                }
            }, 2000);
        });

        console.log('Product page initialization complete');
    });
</script>

<!-- Web Share API Implementation -->
<script>
    jQuery(document).ready(function($) {
        // Web Share API functionality
        function initShareFunctionality() {
            // Check if Web Share API is supported
            if (navigator.share) {
                console.log('Web Share API is supported');
                
                // Add click handler for share buttons
                $(document).on('click', '#share-product-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var $button = $(this);
                    var productTitle = $button.data('product-title');
                    var productUrl = $button.data('product-url');
                    var productPrice = $button.data('product-price');
                    
                    // Create share data
                    var shareData = {
                        title: productTitle,
                        text: 'Check out this amazing product: ' + productTitle + ' - ' + productPrice,
                        url: productUrl
                    };
                    
                    // Show loading state
                    var originalHTML = $button.html();
                    $button.html('<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>');
                    $button.prop('disabled', true);
                    
                    // Attempt to share
                    navigator.share(shareData)
                        .then(function() {
                            console.log('Product shared successfully');
                            showMessage('Product shared successfully!', 'success');
                        })
                        .catch(function(error) {
                            console.log('Error sharing:', error);
                            
                            // Handle specific error cases
                            if (error.name === 'AbortError') {
                                console.log('User cancelled sharing');
                            } else {
                                showMessage('Unable to share. Please try again.', 'error');
                            }
                        })
                        .finally(function() {
                            // Reset button state
                            $button.html(originalHTML);
                            $button.prop('disabled', false);
                        });
                });
                
            } else {
                console.log('Web Share API is not supported');
                
                // Fallback for browsers that don't support Web Share API
                $(document).on('click', '#share-product-btn', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    var $button = $(this);
                    var productTitle = $button.data('product-title');
                    var productUrl = $button.data('product-url');
                    
                    // Create fallback share options
                    var shareText = 'Check out this product: ' + productTitle + ' - ' + productUrl;
                    
                    // Try to copy to clipboard first
                    if (navigator.clipboard && navigator.clipboard.writeText) {
                        navigator.clipboard.writeText(shareText)
                            .then(function() {
                                showMessage('Product link copied to clipboard!', 'success');
                            })
                            .catch(function() {
                                // If clipboard fails, show the text for manual copying
                                showFallbackShareDialog(shareText, productUrl);
                            });
                    } else {
                        // Show fallback dialog for older browsers
                        showFallbackShareDialog(shareText, productUrl);
                    }
                });
            }
        }
        
        // Fallback share dialog for unsupported browsers
        function showFallbackShareDialog(shareText, productUrl) {
            // Create a simple modal or alert with sharing options
            var shareOptions = [
                {
                    name: 'Copy Link',
                    action: function() {
                        copyToClipboard(productUrl);
                        showMessage('Product link copied to clipboard!', 'success');
                        closeFallbackShareModal();
                    }
                },
                {
                    name: 'Copy Text',
                    action: function() {
                        copyToClipboard(shareText);
                        showMessage('Product information copied to clipboard!', 'success');
                        closeFallbackShareModal();
                    }
                },
                {
                    name: 'Open in New Tab',
                    action: function() {
                        window.open(productUrl, '_blank');
                        closeFallbackShareModal();
                    }
                }
            ];
            
            // Create and show fallback modal
            var modalHTML = '<div id="fallback-share-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">' +
                '<div class="bg-white rounded-lg p-6 max-w-sm w-full mx-4">' +
                '<h3 class="text-lg font-semibold mb-4">Share Product</h3>' +
                '<div class="space-y-3">';
            
            // Add share option buttons
            modalHTML += '<button class="w-full text-left px-4 py-2 rounded hover:bg-gray-100 transition-colors" onclick="copyProductLink()">Copy Link</button>';
            modalHTML += '<button class="w-full text-left px-4 py-2 rounded hover:bg-gray-100 transition-colors" onclick="copyProductText()">Copy Text</button>';
            modalHTML += '<button class="w-full text-left px-4 py-2 rounded hover:bg-gray-100 transition-colors" onclick="openProductInNewTab()">Open in New Tab</button>';
            
            modalHTML += '</div>' +
                '<button class="mt-4 w-full px-4 py-2 bg-gray-200 rounded hover:bg-gray-300 transition-colors" onclick="closeFallbackShareModal()">Cancel</button>' +
                '</div>' +
                '</div>';
            
            $('body').append(modalHTML);
            
            // Store data for global functions
            window.fallbackShareData = {
                text: shareText,
                url: productUrl
            };
        }
        
        // Global functions for fallback modal
        window.copyProductLink = function() {
            if (window.fallbackShareData) {
                copyToClipboard(window.fallbackShareData.url);
                showMessage('Product link copied to clipboard!', 'success');
                closeFallbackShareModal();
            }
        };
        
        window.copyProductText = function() {
            if (window.fallbackShareData) {
                copyToClipboard(window.fallbackShareData.text);
                showMessage('Product information copied to clipboard!', 'success');
                closeFallbackShareModal();
            }
        };
        
        window.openProductInNewTab = function() {
            if (window.fallbackShareData) {
                window.open(window.fallbackShareData.url, '_blank');
                closeFallbackShareModal();
            }
        };
        
        // Close fallback share modal
        window.closeFallbackShareModal = function() {
            $('#fallback-share-modal').remove();
            window.fallbackShareData = null;
        };
        
        // Copy to clipboard function for older browsers
        function copyToClipboard(text) {
            var textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            
            try {
                document.execCommand('copy');
            } catch (err) {
                console.error('Failed to copy text: ', err);
            }
            
            document.body.removeChild(textArea);
        }
        
        // Initialize share functionality
        initShareFunctionality();
    });
</script>

<?php if ($product->is_type('variable')) : ?>
    <script>
        jQuery(document).ready(function($) {
            // Additional variable product specific scripts
            $('.color-swatch').on('click', function() {
                var value = $(this).data('value');
                var $form = $(this).closest('form');
                $form.find('.color-swatch').removeClass('ring-2 ring-red-500 border-red-500');
                $(this).addClass('ring-2 ring-red-500 border-red-500');
                $form.find('input.selected-color').val(value).trigger('change');

                // Update WooCommerce variation select
                var $variationSelect = $form.find('select[name="attribute_Color"]');
                if ($variationSelect.length) {
                    $variationSelect.val(value).trigger('change');
                }
            });

            // Size button selection
            $('.size-btn').on('click', function() {
                var value = $(this).data('value');
                var $form = $(this).closest('form');
                $form.find('.size-btn').removeClass('ring-2 ring-red-500 border-red-500 bg-red-50 text-red-600');
                $(this).addClass('ring-2 ring-red-500 border-red-500 bg-red-50 text-red-600');
                $form.find('input.selected-size').val(value).trigger('change');

                // Update WooCommerce variation select
                var $variationSelect = $form.find('select[name="attribute_Size"]');
                if ($variationSelect.length) {
                    $variationSelect.val(value).trigger('change');
                }
            });

            // Enhanced variation selection checking
            function checkVariationSelection() {
                $('.variations_form').each(function() {
                    var $form = $(this);
                    var $button = $form.find('.single_add_to_cart_button');
                    var allSelected = true;

                    // Check if all required attributes are selected
                    $form.find('input[type="hidden"][name^="attribute_"]').each(function() {
                        if ($(this).val() === '') {
                            allSelected = false;
                            return false;
                        }
                    });

                    // Update button state
                    if (allSelected) {
                        $button.removeClass('disabled wc-variation-selection-needed');
                        $button.prop('disabled', false);
                    } else {
                        $button.addClass('disabled wc-variation-selection-needed');
                        $button.prop('disabled', true);
                    }
                });
            }

            // Bind variation change events
            $(document).on('change', 'input[type="hidden"][name^="attribute_"]', checkVariationSelection);

            // Initial check
            checkVariationSelection();
        });
    </script>
<?php endif; ?>

<script>
    jQuery(document).ready(function($) {
        // Size Guide Modal Functionality
        $('#size-guide-trigger').on('click', function() {
            $('#size-guide-modal').removeClass('hidden');
            $('body').addClass('overflow-hidden');
        });

        // Close button click handler
        $('#close-size-guide').on('click', function() {
            $('#size-guide-modal').addClass('hidden');
            $('body').removeClass('overflow-hidden');
        });

        // Close when clicking outside the modal
        $('#size-guide-modal').on('click', function(e) {
            if (e.target === this) {
                $('#size-guide-modal').addClass('hidden');
                $('body').removeClass('overflow-hidden');
            }
        });

        // Unit switching in modal
        $('#modal-inches-btn').on('click', function() {
            $(this).addClass('text-red-500 bg-white').removeClass('text-gray-600');
            $('#modal-cm-btn').removeClass('text-red-500 bg-white').addClass('text-gray-600');
            $('#modal-inches-table').removeClass('hidden');
            $('#modal-cm-table').addClass('hidden');
        });

        $('#modal-cm-btn').on('click', function() {
            $(this).addClass('text-red-500 bg-white').removeClass('text-gray-600');
            $('#modal-inches-btn').removeClass('text-red-500 bg-white').addClass('text-gray-600');
            $('#modal-cm-table').removeClass('hidden');
            $('#modal-inches-table').addClass('hidden');
        });

        // Unit switching in tab
        $('#tab-inches-btn').on('click', function() {
            $(this).addClass('text-red-500 bg-white').removeClass('text-gray-600');
            $('#tab-cm-btn').removeClass('text-red-500 bg-white').addClass('text-gray-600');
            $('#inches-table').removeClass('hidden');
            $('#cm-table').addClass('hidden');
        });

        $('#tab-cm-btn').on('click', function() {
            $(this).addClass('text-red-500 bg-white').removeClass('text-gray-600');
            $('#tab-inches-btn').removeClass('text-red-500 bg-white').addClass('text-gray-600');
            $('#cm-table').removeClass('hidden');
            $('#inches-table').addClass('hidden');
        });

        // Close modal on escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && !$('#size-guide-modal').hasClass('hidden')) {
                $('#size-guide-modal').addClass('hidden');
                $('body').removeClass('overflow-hidden');
            }
        });
    });
</script>

<style>
/* --- YITH Wishlist Styles for Single Product Page --- */
.yith-wcwl-add-to-wishlist, .yith-wcwl-add-to-wishlist * { font-size: 0 !important; color: transparent !important; text-indent: -9999px !important; line-height: 0 !important; }
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a { display: flex !important; align-items: center !important; justify-content: center !important; width: 40px !important; height: 40px !important; background: white !important; border-radius: 50% !important; box-shadow: 0 2px 8px rgba(0,0,0,0.10) !important; transition: all 0.2s ease !important; text-decoration: none !important; border: none !important; padding: 0 !important; margin: 0 !important; position: relative; }
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a:before,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:before,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:before { content: ''; display: inline-block; width: 24px; height: 24px; background-size: contain; background-repeat: no-repeat; vertical-align: middle; text-indent: 0; margin: 0; }

/* Outline heart (not in wishlist) */
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a:before {
    background-image: url('data:image/svg+xml;utf8,<svg fill="none" stroke="%236b7280" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>');
    position: absolute;
}

/* Filled red heart on white (in wishlist) */
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a {
    background: white !important;
}
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:before,
.yith-wcwl-add-to-wishlist.exists a:before {
    background-image: url('data:image/svg+xml;utf8,<svg fill="%23ed1c24" stroke="%23ed1c24" stroke-width="1.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>');
}
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a:hover,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistexistsbrowse a:hover {
    background: #f3f4f6 !important;
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

/* Loading state for wishlist button */
.yith-wcwl-add-to-wishlist .yith-wcwl-add-button a.loading,
.yith-wcwl-add-to-wishlist .yith-wcwl-wishlistaddedbrowse a.loading {
    opacity: 0.5;
    cursor: wait !important;
}

.custom-wishlist-wrapper .yith-wcwl-add-button a {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 0.5rem !important;
    width: 100% !important;
    height: 60px !important;
    padding: 0 2rem !important;
    border: 1px solid #d1d5db !important;
    background: #fff !important;
    color: #374151 !important;
    font-size: 1rem !important;
    font-weight: 500 !important;
    border-radius: 0.75rem !important;
    transition: border-color 0.2s, color 0.2s, background 0.2s !important;
    box-shadow: none !important;
    cursor: pointer !important;
    text-align: center !important;
}

.custom-wishlist-wrapper .yith-wcwl-add-to-wishlist .yith-wcwl-add-button a span,
.custom-wishlist-wrapper .yith-wcwl-wishlistaddedbrowse a span,
.custom-wishlist-wrapper .yith-wcwl-wishlistexistsbrowse a span,
.custom-wishlist-wrapper .yith-wcwl-add-button a .yith-wcwl-icon,
.custom-wishlist-wrapper .yith-wcwl-wishlistaddedbrowse a .yith-wcwl-icon,
.custom-wishlist-wrapper .yith-wcwl-wishlistexistsbrowse a .yith-wcwl-icon {
    display: block !important;
}


.custom-wishlist-wrapper .yith-wcwl-add-button a:before {
    display: none !important;
}

</style>

<?php get_footer(); ?>