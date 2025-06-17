<?php
/**
 * The template for displaying product content in the single-product.php template
 * With YITH WooCommerce Wishlist integration - Pixel Perfect Design
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' ); ?>

<div class="bg-white min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <?php while ( have_posts() ) : ?>
            <?php the_post(); ?>
            <?php 
            global $product;
            // Get available variations for variable products
            $available_variations = array();
            $attributes = array();
            if ( $product->is_type( 'variable' ) ) {
                $available_variations = $product->get_available_variations();
                $attributes = $product->get_variation_attributes();
            }
            ?>

            <div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'single-product-wrapper', $product ); ?>>
                
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-16">
                    <!-- Product Images Column -->
                    <div class="product-images-section">
                        <div class="relative">
                            <!-- Main Product Image -->
                            <div class="main-product-image-container mb-4">
                                <div class="aspect-square w-full bg-gray-50 rounded-2xl overflow-hidden relative group">
                                    <?php
                                    $image_id = $product->get_image_id();
                                    if ( $image_id ) {
                                        $image_url = wp_get_attachment_image_url( $image_id, 'woocommerce_single' );
                                        echo '<img id="main-product-image" src="' . esc_url( $image_url ) . '" alt="' . esc_attr( $product->get_name() ) . '" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">';
                                    }
                                    ?>
                                    
                                    <!-- Sale Badge -->
                                    <?php if ( $product->is_on_sale() ) : ?>
                                        <div class="absolute top-4 left-4 bg-red-500 text-white text-sm font-semibold px-3 py-1 rounded-full z-10">
                                            Sale
                                        </div>
                                    <?php endif; ?>
                                    
                                    <!-- Zoom Icon -->
                                    <div class="absolute top-4 right-4 bg-white/80 hover:bg-white rounded-full p-2 cursor-pointer opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Thumbnail Images -->
                            <div class="thumbnail-container">
                                <div class="flex gap-3 overflow-x-auto pb-2">
                                    <?php
                                    $attachment_ids = $product->get_gallery_image_ids();
                                    
                                    // Add main image as first thumbnail
                                    if ( $image_id ) {
                                        $main_image_url = wp_get_attachment_image_url( $image_id, 'woocommerce_thumbnail' );
                                        echo '<div class="thumbnail-item flex-shrink-0">';
                                        echo '<img src="' . esc_url( $main_image_url ) . '" alt="Product Image" class="w-20 h-20 object-cover rounded-lg border-2 border-red-500 cursor-pointer thumbnail-image active" data-full="' . esc_url( $image_url ) . '">';
                                        echo '</div>';
                                    }
                                    
                                    // Add gallery images as thumbnails
                                    foreach ( $attachment_ids as $attachment_id ) {
                                        $thumbnail_url = wp_get_attachment_image_url( $attachment_id, 'woocommerce_thumbnail' );
                                        $full_url = wp_get_attachment_image_url( $attachment_id, 'woocommerce_single' );
                                        
                                        echo '<div class="thumbnail-item flex-shrink-0">';
                                        echo '<img src="' . esc_url( $thumbnail_url ) . '" alt="Product Image" class="w-20 h-20 object-cover rounded-lg border-2 border-gray-200 hover:border-gray-400 cursor-pointer thumbnail-image transition-colors" data-full="' . esc_url( $full_url ) . '">';
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
                            <?php if ( wc_review_ratings_enabled() ) : ?>
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="flex items-center">
                                        <?php 
                                        $rating = $product->get_average_rating();
                                        for ( $i = 1; $i <= 5; $i++ ) {
                                            $fill_class = $i <= floor( $rating ) ? 'text-yellow-400' : 'text-gray-300';
                                            echo '<svg class="w-5 h-5 ' . $fill_class . '" fill="currentColor" viewBox="0 0 20 20">';
                                            echo '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>';
                                            echo '</svg>';
                                        }
                                        ?>
                                    </div>
                                    <span class="text-sm text-gray-600">
                                        <?php echo esc_html( $rating ); ?> (<?php echo esc_html( $product->get_review_count() ); ?> reviews)
                                    </span>
                                </div>
                            <?php endif; ?>

                            <!-- Product Price -->
                            <div class="mb-6">
                                <?php if ( $product->is_on_sale() ) : ?>
                                    <div class="flex items-center gap-3">
                                        <span class="text-3xl font-bold text-red-500">
                                            <?php echo wc_price( $product->get_sale_price() ); ?>
                                        </span>
                                        <span class="text-xl text-gray-400 line-through">
                                            <?php echo wc_price( $product->get_regular_price() ); ?>
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
                                <?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
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
                                            echo '<svg class="w-5 h-5 text-green-500 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">';
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
                            <?php if ( $product->is_type( 'variable' ) ) : ?>
                                <form class="variations_form cart custom-cart-form" action="#" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo htmlspecialchars( wp_json_encode( $available_variations ) ) ?>">
                                    <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                                    <?php do_action( 'woocommerce_before_variations_form' ); ?>
                                    
                                    <?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
                                        <p class="stock out-of-stock text-red-500 font-medium"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'woocommerce' ); ?></p>
                                    <?php else : ?>
                                        
                                        <!-- Color Selection -->
                                        <?php if ( isset($attributes['Color']) ) : ?>
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
                                        <?php if ( isset($attributes['Size']) ) : ?>
                                            <div class="mb-6">
                                                <div class="flex items-center justify-between mb-3">
                                                    <label class="text-sm font-semibold text-gray-900">Size</label>
                                                    <a href="#" class="text-sm text-red-500 hover:text-red-600 underline font-medium">Size Guide</a>
                                                </div>
                                                <div class="flex gap-3">
                                                    <?php foreach ($attributes['Size'] as $size) : ?>
                                                        <button type="button" 
                                                            class="size-btn px-4 py-2 min-w-[48px] border-2 border-gray-300 text-sm font-semibold text-gray-700 rounded-lg hover:border-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 transition-all" 
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
                                            <div>
                                                <label class="text-sm font-semibold text-gray-900 mb-3 block">Quantity</label>
                                                <div class="flex items-center border-2 border-gray-300 rounded-lg w-32 bg-white">
                                                    <button type="button" class="quantity-btn quantity-minus flex-shrink-0 w-10 h-10 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                        </svg>
                                                    </button>
                                                    <input type="number" 
                                                           name="quantity" 
                                                           value="<?php echo esc_attr( isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity() ); ?>" 
                                                           min="<?php echo esc_attr( $product->get_min_purchase_quantity() ); ?>" 
                                                           max="<?php echo esc_attr( 0 < $product->get_max_purchase_quantity() ? $product->get_max_purchase_quantity() : '' ); ?>" 
                                                           step="1" 
                                                           class="quantity-input flex-1 text-center border-0 outline-none text-sm font-semibold h-10 bg-transparent">
                                                    <button type="button" class="quantity-btn quantity-plus flex-shrink-0 w-10 h-10 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="space-y-3">
                                                <!-- Add to Cart Button -->
                                                <button type="submit" 
                                                        name="add-to-cart" 
                                                        value="<?php echo esc_attr( $product->get_id() ); ?>" 
                                                        class="single_add_to_cart_button w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-4 px-8 rounded-lg transition-colors flex items-center justify-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed">
                                                    <span class="button-text">Add to Cart</span>
                                                    <span class="loading-spinner hidden">
                                                        <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                        </svg>
                                                    </span>
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 9H19m-7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                                    </svg>
                                                </button>

                                                <!-- Add to Wishlist Button -->
                                                <?php if ( function_exists( 'YITH_WCWL' ) ) : ?>
                                                    <div class="yith-wishlist-wrapper">
                                                        <?php echo do_shortcode( '[yith_wcwl_add_to_wishlist product_id="' . $product->get_id() . '" label="Add to Wishlist" browse_wishlist_text="Browse Wishlist" already_in_wishlist_text="In Wishlist" product_added_text="Product added to wishlist!" icon="fa-heart-o" link_classes="wishlist-btn w-full border-2 border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-500 font-semibold py-4 px-8 rounded-lg transition-colors flex items-center justify-center gap-2"]' ); ?>
                                                    </div>
                                                <?php else : ?>
                                                    <button type="button" class="add-to-wishlist-fallback w-full border-2 border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-500 font-semibold py-4 px-8 rounded-lg transition-colors flex items-center justify-center gap-2" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                        </svg>
                                                        Add to Wishlist
                                                    </button>
                                                <?php endif; ?>

                                                <!-- Share Button -->
                                                <button type="button" class="w-full border-2 border-gray-300 text-gray-700 hover:border-gray-400 font-semibold py-4 px-8 rounded-lg transition-colors flex items-center justify-center gap-2">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                                    </svg>
                                                    Share
                                                </button>
                                            </div>
                                        </div>

                                        <input type="hidden" name="product_id" value="<?php echo esc_attr( $product->get_id() ); ?>" />
                                        <input type="hidden" name="variation_id" class="variation_id" value="0" />

                                    <?php endif; ?>
                                    <?php do_action( 'woocommerce_after_variations_form' ); ?>
                                </form>
                            <?php else : ?>
                                <!-- Simple Product Form -->
                                <form class="cart custom-cart-form" action="#" method="post" enctype='multipart/form-data' data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">
                                    <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
                                    <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
                                    
                                    <!-- Quantity Selector -->
                                    <div class="mb-6">
                                        <label class="text-sm font-semibold text-gray-900 mb-3 block">Quantity</label>
                                        <div class="flex items-center border-2 border-gray-300 rounded-lg w-32 bg-white">
                                            <button type="button" class="quantity-btn quantity-minus flex-shrink-0 w-10 h-10 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <input type="number" 
                                                   name="quantity" 
                                                   value="<?php echo esc_attr( isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity() ); ?>" 
                                                   min="<?php echo esc_attr( $product->get_min_purchase_quantity() ); ?>" 
                                                   max="<?php echo esc_attr( 0 < $product->get_max_purchase_quantity() ? $product->get_max_purchase_quantity() : '' ); ?>" 
                                                   step="1" 
                                                   class="quantity-input flex-1 text-center border-0 outline-none text-sm font-semibold h-10 bg-transparent">
                                            <button type="button" class="quantity-btn quantity-plus flex-shrink-0 w-10 h-10 flex items-center justify-center text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="space-y-3">
                                        <!-- Add to Cart Button -->
                                        <button type="submit" 
                                                name="add-to-cart" 
                                                value="<?php echo esc_attr( $product->get_id() ); ?>" 
                                                class="single_add_to_cart_button w-full bg-red-500 hover:bg-red-600 text-white font-semibold py-4 px-8 rounded-lg transition-colors flex items-center justify-center gap-2">
                                            <span class="button-text">Add to Cart</span>
                                            <span class="loading-spinner hidden">
                                                <svg class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </span>
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-1.5 9H19m-7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                                            </svg>
                                        </button>

                                        <!-- Add to Wishlist Button -->
                                        <?php if ( function_exists( 'YITH_WCWL' ) ) : ?>
                                            <div class="yith-wishlist-wrapper">
                                                <?php echo do_shortcode( '[yith_wcwl_add_to_wishlist product_id="' . $product->get_id() . '" label="Add to Wishlist" browse_wishlist_text="Browse Wishlist" already_in_wishlist_text="In Wishlist" product_added_text="Product added to wishlist!" icon="fa-heart-o" link_classes="wishlist-btn w-full border-2 border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-500 font-semibold py-4 px-8 rounded-lg transition-colors flex items-center justify-center gap-2"]' ); ?>
                                            </div>
                                        <?php else : ?>
                                            <button type="button" class="add-to-wishlist-fallback w-full border-2 border-gray-300 text-gray-700 hover:border-red-500 hover:text-red-500 font-semibold py-4 px-8 rounded-lg transition-colors flex items-center justify-center gap-2" data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                                </svg>
                                                Add to Wishlist
                                            </button>
                                        <?php endif; ?>

                                        <!-- Share Button -->
                                        <button type="button" class="w-full border-2 border-gray-300 text-gray-700 hover:border-gray-400 font-semibold py-4 px-8 rounded-lg transition-colors flex items-center justify-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                                            </svg>
                                            Share
                                        </button>
                                    </div>

                                    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
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
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-4">Premium Design & Materials</h3>
                                    <div class="prose prose-gray max-w-none">
                                        <?php
                                        $description = $product->get_description();
                                        if ( $description ) {
                                            echo apply_filters( 'the_content', $description );
                                        } else {
                                            echo '<p>Our Hype Puffer Jacket combines streetwear-inspired design with premium materials. The water-resistant ripstop nylon outer shell repels moisture while remaining breathable, and the premium synthetic insulation traps heat while maintaining a lightweight feel. Taking cues from human streetwear trends, this puffer features bold color blocking, an oversized fit, and our signature Hype Pups logo.</p>';
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-2xl p-1 aspect-square">
                                    <!-- Placeholder for second image -->
                                    <div class="w-full h-full bg-gray-200 rounded-xl flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sizing Tab -->
                        <div id="sizing-tab" class="product-tab-content hidden">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900 mb-4">Comfort & Functionality</h3>
                                    <div class="prose prose-gray max-w-none">
                                        <p>We've designed this jacket with both style and function in mind. The full-length YKK zipper makes it easy to put on and take off, even for dogs who don't typically enjoy wearing clothes. Elastic leg openings provide comfort without restricting movement, and the adjustable straps ensure a perfect fit for dogs of all shapes. Safety features include 3M Scotchlite™ reflective piping integrated along the seams for 360° visibility.</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-2xl p-1 aspect-square">
                                    <!-- Placeholder for sizing chart -->
                                    <div class="w-full h-full bg-gray-200 rounded-xl flex items-center justify-center">
                                        <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews Tab -->
                        <div id="reviews-tab" class="product-tab-content hidden">
                            <?php
                            if ( comments_open() || get_comments_number() ) {
                                comments_template();
                            }
                            ?>
                        </div>
                    </div>
                </div>

                <!-- You May Also Like Section -->
                <div class="mt-20">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="text-2xl font-bold text-gray-900">You May Also Like</h2>
                        <a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="text-red-500 hover:text-red-600 font-medium text-sm">
                            View All Products →
                        </a>
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        <?php
                        // Get related products
                        $related_products = wc_get_related_products( $product->get_id(), 4 );
                        
                        if ( $related_products ) {
                            foreach ( $related_products as $related_id ) {
                                $related_product = wc_get_product( $related_id );
                                if ( $related_product ) {
                                    ?>
                                    <div class="group">
                                        <div class="aspect-square w-full overflow-hidden rounded-xl bg-gray-100 relative">
                                            <?php
                                            $related_image = wp_get_attachment_image_url( $related_product->get_image_id(), 'woocommerce_thumbnail' );
                                            if ( $related_image ) {
                                                echo '<img src="' . esc_url( $related_image ) . '" alt="' . esc_attr( $related_product->get_name() ) . '" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">';
                                            }
                                            ?>
                                            
                                            <!-- Sale Badge -->
                                            <?php if ( $related_product->is_on_sale() ) : ?>
                                                <div class="absolute top-3 left-3 bg-green-500 text-white text-xs font-semibold px-2 py-1 rounded-full">
                                                    Sale
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Quick Add Button -->
                                            <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity bg-black/40">
                                                <a href="<?php echo esc_url( $related_product->get_permalink() ); ?>" class="bg-white text-gray-900 px-4 py-2 rounded-lg font-medium text-sm hover:bg-gray-100 transition-colors">
                                                    Quick View
                                                </a>
                                            </div>
                                        </div>
                                        
                                        <div class="mt-4">
                                            <h3 class="text-sm font-medium text-gray-900 line-clamp-2">
                                                <a href="<?php echo esc_url( $related_product->get_permalink() ); ?>" class="hover:text-red-500 transition-colors">
                                                    <?php echo esc_html( $related_product->get_name() ); ?>
                                                </a>
                                            </h3>
                                            
                                            <!-- Rating -->
                                            <div class="flex items-center mt-2">
                                                <?php
                                                $related_rating = $related_product->get_average_rating();
                                                for ( $i = 1; $i <= 5; $i++ ) {
                                                    $fill_class = $i <= floor( $related_rating ) ? 'text-yellow-400' : 'text-gray-300';
                                                    echo '<svg class="w-4 h-4 ' . $fill_class . '" fill="currentColor" viewBox="0 0 20 20">';
                                                    echo '<path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>';
                                                    echo '</svg>';
                                                }
                                                ?>
                                                <span class="text-xs text-gray-500 ml-1">(<?php echo esc_html( $related_product->get_review_count() ); ?>)</span>
                                            </div>
                                            
                                            <!-- Price -->
                                            <div class="mt-2">
                                                <?php if ( $related_product->is_on_sale() ) : ?>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm font-bold text-red-500">
                                                            <?php echo wc_price( $related_product->get_sale_price() ); ?>
                                                        </span>
                                                        <span class="text-sm text-gray-400 line-through">
                                                            <?php echo wc_price( $related_product->get_regular_price() ); ?>
                                                        </span>
                                                    </div>
                                                <?php else : ?>
                                                    <span class="text-sm font-bold text-gray-900">
                                                        <?php echo $related_product->get_price_html(); ?>
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<style>
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
}

.product-tab-content {
    display: none;
}

.product-tab-content.active {
    display: block;
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
    
    .space-y-3 > * + * {
        margin-top: 0.75rem;
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
.add-to-wishlist{
    background-color: #fff !important;
    color: #ff3a5e !important;
    border: 2px solid #ff3a5e !important;
    padding:10px !important;
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

.woocommerce-Price-amount.amount{
    color: #ff3a5e !important;
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
    background-color: #ff3a5e !important;
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
    background-color: #77a464;
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
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
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

.woocommerce #content div.product div.images, .woocommerce div.product div.images, .woocommerce-page #content div.product div.images, .woocommerce-page div.product div.images{
    width: 100%;
    object-fit: cover;
}

.woocommerce div.product div.summary{
    width: 100%;
    padding: 0 30px;
}

/* Main product image */
.woocommerce div.product div.images img,
.woocommerce-page div.product div.images img {
    width: 100%;           /* Makes image responsive */
    max-width: 500px;      /* Set your desired max width */
    height: 500px;         /* Fixed height */
    object-fit: cover;     /* Ensures image covers the area without distortion */
    border-radius: 8px;    /* Optional: rounded corners */
    margin: 0 auto 16px auto !important;
    display: block;
}

/* Gallery thumbnails */
.woocommerce div.product div.images .thumbnails img,
.woocommerce-page div.product div.images .thumbnails img {
    width: 100px;          /* Fixed width for thumbnails */
    min-height: 100px !important;         /* Fixed height for thumbnails */
    object-fit: cover;     /* Ensures thumbnails are not distorted */
    border-radius: 6px;    /* Optional: rounded corners */
    margin-right: 10px;
    border: 1px solid #eee;
    transition: border 0.2s;
    cursor: pointer;
}

.woocommerce-product-gallery__trigger{
    right: 6.5rem !important;
}

.woocommerce div.product div.images .thumbnails img:hover,
.woocommerce-page div.product div.images .thumbnails img:hover {
    border: 1px solid #ff3a5e;
}

img.flex-active{
    border: 2px solid #ff3a5e !important;
}

.flex-control-nav.flex-control-thumbs li img{
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
    color: #ff3a5e !important;
    font-weight: 600 !important;
}

.woocommerce-tabs .wc-tabs li.active a::after {
    content: "";
    display: block;
    height: 5px;
    width: 100%;
    background: #ff3a5e;
    border-radius: 2px;
    position: absolute;
    left: 0;
    bottom: -2px;
}

.woocommerce div.product form.cart .button{
    max-width: 30%;
}

.woocommerce-tabs .wc-tab {
    padding: 2rem 0 0 0;
    border: none;
    background: none;
}

.woocommerce-Tabs-panel h2{
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

.quantity-minus, .quantity-plus {
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

    // Quantity Control
    $('.quantity-plus').on('click', function() {
        var $input = $(this).siblings('.quantity-input');
        var currentVal = parseInt($input.val()) || 1;
        var max = parseInt($input.attr('max'));
        
        if (!max || currentVal < max) {
            $input.val(currentVal + 1).trigger('change');
        }
    });

    $('.quantity-minus').on('click', function() {
        var $input = $(this).siblings('.quantity-input');
        var currentVal = parseInt($input.val()) || 1;
        var min = parseInt($input.attr('min')) || 1;
        
        if (currentVal > min) {
            $input.val(currentVal - 1).trigger('change');
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
            var successMsg = (response.data && response.data.message) 
                ? response.data.message 
                : 'Product added to cart successfully!';
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

<?php if ( $product->is_type( 'variable' ) ) : ?>
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

<?php get_footer( 'shop' ); ?>