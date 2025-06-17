<?php
/* Template Name: Shop Page */

get_header();
?>

<main id="main-content" class="py-12 md:py-16">
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
                    <div class="mb-6">
                        <h3 class="font-bold mb-4 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                            </svg>
                            Filter Products
                        </h3>
                        <form id="product-filters" method="get">
                            <div class="border-t border-gray-200 pt-4">
                                <h4 class="font-medium mb-3">Categories</h4>
                                <div class="space-y-2">
                                    <?php
                                    $product_categories = get_terms(array(
                                        'taxonomy' => 'product_cat',
                                        'hide_empty' => true,
                                        'parent' => 0
                                    ));

                                    foreach ($product_categories as $category) :
                                        $count = $category->count;
                                    ?>
                                        <div class="flex items-center">
                                            <input 
                                                type="checkbox" 
                                                name="product_cat[]" 
                                                id="category-<?php echo esc_attr($category->slug); ?>" 
                                                value="<?php echo esc_attr($category->slug); ?>"
                                                <?php if (isset($_GET['product_cat']) && in_array($category->slug, (array)$_GET['product_cat'])) echo 'checked'; ?>
                                                class="rounded border-gray-300 text-[#FF3A5E] focus:ring-[#FF3A5E]"
                                                onchange="document.getElementById('product-filters').submit();"
                                            >
                                            <label for="category-<?php echo esc_attr($category->slug); ?>" class="ml-2 text-sm flex-grow">
                                                <?php echo esc_html($category->name); ?>
                                            </label>
                                            <span class="text-xs text-gray-500">(<?php echo esc_html($count); ?>)</span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="mb-6">
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="font-medium mb-3">Price Range</h4>
                            <div class="px-2">
                                <div class="mb-6">
                                    <input 
                                        type="range" 
                                        min="0" 
                                        max="200" 
                                        step="1" 
                                        class="w-full"
                                    >
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
                                $sizes = array('XS', 'S', 'M', 'L', 'XL');
                                foreach ($sizes as $size) :
                                ?>
                                    <label
                                        class="flex items-center justify-center w-10 h-10 border rounded-md text-sm cursor-pointer border-gray-300 hover:border-[#FF3A5E] hover:text-[#FF3A5E]"
                                    >
                                        <input 
                                            type="checkbox" 
                                            value="<?php echo esc_attr($size); ?>" 
                                            class="sr-only"
                                        >
                                        <span><?php echo esc_html($size); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <div class="border-t border-gray-200 pt-4">
                            <h4 class="font-medium mb-3">Color</h4>
                            <div class="flex flex-wrap gap-2">
                                <?php
                                $colors = array(
                                    array('name' => 'Black', 'class' => 'bg-black'),
                                    array('name' => 'White', 'class' => 'bg-white border border-gray-300'),
                                    array('name' => 'Red', 'class' => 'bg-red-500'),
                                    array('name' => 'Blue', 'class' => 'bg-blue-500'),
                                    array('name' => 'Green', 'class' => 'bg-green-500'),
                                    array('name' => 'Yellow', 'class' => 'bg-yellow-400'),
                                    array('name' => 'Gray', 'class' => 'bg-gray-500'),
                                    array('name' => 'Camo', 'class' => 'bg-olive-600')
                                );

                                foreach ($colors as $color) :
                                ?>
                                    <label
                                        class="flex flex-col items-center gap-1 cursor-pointer"
                                        title="<?php echo esc_attr($color['name']); ?>"
                                    >
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center <?php echo esc_attr($color['class']); ?> hover:ring-2 hover:ring-[#FF3A5E] hover:ring-offset-2"
                                        >
                                            <input 
                                                type="checkbox" 
                                                value="<?php echo esc_attr($color['name']); ?>" 
                                                class="sr-only"
                                            >
                                        </div>
                                        <span class="text-xs"><?php echo esc_html($color['name']); ?></span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <button 
                        class="w-full border border-gray-300 bg-white hover:bg-gray-50 text-gray-700 font-medium px-4 py-2 rounded-md mb-3"
                    >
                        Reset Filters
                    </button>
                    
                    <button 
                        class="w-full bg-[#FF3A5E] hover:bg-[#FF3A5E]/90 text-white font-medium px-4 py-2 rounded-md"
                    >
                        Apply Filters
                    </button>
                </div>
            </div>

            <!-- Products - Right Side -->
            <div class="lg:w-3/4">
                <!-- Sort and View Options -->
                <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
                    <p class="text-sm text-gray-500">
                        <?php
                        global $wp_query;
                        echo esc_html($wp_query->found_posts) . ' products';
                        ?>
                    </p>
                    <div class="flex items-center gap-4">
                        <select 
                            class="border rounded-md px-3 py-2 bg-white text-sm"
                            onchange="window.location.href=this.value"
                        >
                            <?php
                            $orderby = isset($_GET['orderby']) ? wc_clean($_GET['orderby']) : 'menu_order';
                            $catalog_orderby_options = apply_filters('woocommerce_catalog_orderby', array(
                                'menu_order' => __('Featured', 'woocommerce'),
                                'date'       => __('Newest', 'woocommerce'),
                                'price'      => __('Price: Low to High', 'woocommerce'),
                                'price-desc' => __('Price: High to Low', 'woocommerce'),
                                'popularity' => __('Best Selling', 'woocommerce'),
                            ));

                            foreach ($catalog_orderby_options as $id => $name) :
                            ?>
                                <option value="<?php echo esc_url(add_query_arg('orderby', $id)); ?>" <?php selected($orderby, $id); ?>>
                                    <?php echo esc_html($name); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Products Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                    <?php
                    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
                    $tax_query = array('relation' => 'AND');
                    if (!empty($_GET['product_cat'])) {
                        $tax_query[] = array(
                            'taxonomy' => 'product_cat',
                            'field'    => 'slug',
                            'terms'    => (array) $_GET['product_cat'],
                        );
                    }
                    $args = array(
                        'post_type' => 'product',
                        'posts_per_page' => 9,
                        'paged' => $paged,
                        'post_status' => 'publish',
                        'tax_query' => $tax_query,
                    );
                    $product_query = new WP_Query($args);

                    if ($product_query->have_posts()) :
                        while ($product_query->have_posts()) : $product_query->the_post();
                            $product_id = get_the_ID();
                            $product = wc_get_product($product_id);
                            if (!$product) continue;
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
                                                <button
                                                    class="bg-[#FF3A5E] text-white hover:bg-[#FF3A5E]/90 w-full py-2 px-4 rounded-full text-sm font-medium flex items-center justify-center"
                                                    onclick="event.preventDefault(); addToCart(<?php echo esc_attr($product_id); ?>)"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                                    </svg>
                                                    Add to Cart
                                                </button>
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
                                            <span class="text-xs text-gray-500 ml-1"><?php echo esc_html($review_count); ?></span>
                                        </div>
                                        <p class="font-bold text-lg"><?php echo $product_price; ?></p>
                                    </div>
                                </a>
                            </div>
                    <?php
                        endwhile;
                        wp_reset_postdata();
                    else :
                        echo '<p>No products found.</p>';
                    endif;
                    ?>
                </div>

                <!-- Pagination -->
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
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
?> 