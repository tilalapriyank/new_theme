<?php
if ( ! defined( 'ABSPATH' ) ) exit;

// Widget settings
$title = isset($instance['title']) ? $instance['title'] : __('Shop Our Collections', 'woocommerce');
$product_count = isset($instance['count']) ? (int)$instance['count'] : 4;

// Query products
$args = array(
    'post_type' => 'product',
    'posts_per_page' => $product_count,
    'post_status' => 'publish',
);
$products = new WP_Query($args);
?>
<style>
.product-collections-widget {
    margin-bottom: 3rem;
}

.widget-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.widget-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1a1a1a;
    margin: 0;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
}

.view-all-link {
    color: #666;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: color 0.2s ease;
}

.view-all-link:hover {
    color: #333;
}

.view-all-link::after {
    content: '→';
    font-size: 1.2rem;
}

.collection-tabs {
    display: flex;
    gap: 2rem;
    margin-bottom: 2.5rem;
    border-bottom: 1px solid #e5e5e5;
}

.tab-item {
    color: #999;
    text-decoration: none;
    font-size: 1rem;
    font-weight: 500;
    padding-bottom: 1rem;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
    position: relative;
}

.tab-item.active {
    color: #ff3b5c;
    border-bottom-color: #ff3b5c;
}

.tab-item:hover {
    color: #666;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 1.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

@media (min-width: 768px) {
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (min-width: 1024px) {
    .products-grid {
        grid-template-columns: repeat(4, 1fr);
    }
}

.product-card {
    background: #ffffff;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid #f0f0f0;
    position: relative;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
}

.product-image-container {
    position: relative;
    aspect-ratio: 1;
    background: #f8f9fa;
    overflow: hidden;
}

.product-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: #ff3b5c;
    color: white;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    z-index: 2;
}

.product-badge.bestseller {
    background: #ff3b5c;
}

.product-badge.new {
    background: #ff3b5c;
}

.product-badge.limited {
    background: #ff3b5c;
}

.product-badge.sale {
    background: #ff3b5c;
}

.product-badge.featured {
    background: #ff3b5c;
}

.wishlist-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    background: rgba(255, 255, 255, 0.9);
    border: none;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    z-index: 2;
}

.wishlist-btn:hover {
    background: white;
    transform: scale(1.1);
}

.wishlist-btn svg {
    width: 18px;
    height: 18px;
    stroke: #666;
    transition: stroke 0.2s ease;
}

.wishlist-btn:hover svg {
    stroke: #ff3b5c;
}

.product-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
    padding: 2rem 1rem 1rem;
    opacity: 0;
    transform: translateY(100%);
    transition: all 0.3s ease;
    z-index: 3;
}

.product-card:hover .product-overlay {
    opacity: 1;
    transform: translateY(0);
}

.overlay-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
    align-items: center;
}

.quick-view-btn {
    background: white;
    color: #333;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}

.quick-view-btn:hover {
    background: #f0f0f0;
    transform: translateY(-1px);
}

.add-to-cart-btn {
    background: #ff3b5c;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    white-space: nowrap;
}

.add-to-cart-btn:hover {
    background: #e6334d;
    transform: translateY(-1px);
}

.add-to-cart-btn svg {
    width: 16px;
    height: 16px;
}

.product-content {
    padding: 1.25rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.product-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 0.75rem 0;
    line-height: 1.3;
}

.product-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s ease;
}

.product-title a:hover {
    color: #ff3b5c;
}

.product-rating {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

.rating-stars {
    display: flex;
    gap: 2px;
}

.rating-stars svg {
    width: 14px;
    height: 14px;
}

.rating-count {
    font-size: 0.85rem;
    color: #888;
    font-weight: 500;
}

.product-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #1a1a1a;
    margin-top: auto;
}

.price-sale {
    color: #ff3b5c;
}

.price-regular {
    color: #999;
    text-decoration: line-through;
    font-weight: 500;
    margin-right: 0.5rem;
    font-size: 1rem;
}

.no-products {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem;
    color: #666;
    font-size: 1.1rem;
}
</style>

<section class="product-collections-widget">
    <div class="widget-header">
        <?php if ($title) : ?>
            <h2 class="widget-title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>
        <a href="#" class="view-all-link"><?php esc_html_e('View All Collections', 'woocommerce'); ?></a>
    </div>
    
    <div class="collection-tabs">
        <a href="#" class="tab-item active"><?php esc_html_e('Best Sellers', 'woocommerce'); ?></a>
        <a href="#" class="tab-item"><?php esc_html_e('New Arrivals', 'woocommerce'); ?></a>
        <a href="#" class="tab-item"><?php esc_html_e('Limited Edition', 'woocommerce'); ?></a>
    </div>
    
    <ul class="products-grid">
        <?php if ($products->have_posts()) : 
            $counter = 0;
            while ($products->have_posts()) : $products->the_post();
                global $product;
                $product_id = $product->get_id();
                $product_name = $product->get_name();
                $product_price = $product->get_price();
                $product_image = wp_get_attachment_image_url($product->get_image_id(), 'woocommerce_thumbnail');
                $product_rating = $product->get_average_rating();
                $product_review_count = $product->get_review_count();
                
                // Determine badge based on position for demo
                $badge_types = ['Bestseller', 'New', 'Limited', 'Sale'];
                $product_badge = $badge_types[$counter % 4];
                $counter++;
        ?>
        <li>
            <div class="product-card">
                <div class="product-image-container">
                    <span class="product-badge <?php echo strtolower($product_badge); ?>">
                        <?php echo esc_html($product_badge); ?>
                    </span>
                    
                    <button class="wishlist-btn" type="button" aria-label="<?php esc_attr_e('Add to wishlist', 'woocommerce'); ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                    
                    <img src="<?php echo esc_url($product_image); ?>" alt="<?php echo esc_attr($product_name); ?>" class="product-image" />
                    
                    <div class="product-overlay">
                        <div class="overlay-buttons">
                            <button class="quick-view-btn" type="button">
                                <?php esc_html_e('Quick View', 'woocommerce'); ?>
                            </button>
                            <button class="add-to-cart-btn" type="button">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13l-2.4-4.8M7 13l-2.05 4.1a1 1 0 00.9 1.4h9.3a1 1 0 00.9-1.4L13 13M7 13h10m-5-7V4a1 1 0 011-1h0a1 1 0 011 1v2"/>
                                </svg>
                                <?php esc_html_e('Add to Cart', 'woocommerce'); ?>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="product-content">
                    <h3 class="product-title">
                        <a href="<?php echo esc_url($product->get_permalink()); ?>">
                            <?php echo esc_html($product_name); ?>
                        </a>
                    </h3>
                    
                    <div class="product-rating">
                        <div class="rating-stars">
                            <?php
                            $rating = $product_rating ?: 4; // Default to 4 if no rating
                            for ($i = 1; $i <= 5; $i++) {
                                $fill = $i <= floor($rating) ? '#FFD700' : '#e5e7eb';
                                echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="' . $fill . '">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>';
                            }
                            ?>
                        </div>
                        <span class="rating-count">(<?php echo esc_html($product_review_count ?: rand(50, 150)); ?>)</span>
                    </div>
                    
                    <div class="product-price">
                        <?php if ($product->is_on_sale()) : ?>
                            <span class="price-regular"><?php echo wc_price($product->get_regular_price()); ?></span>
                            <span class="price-sale"><?php echo wc_price($product->get_sale_price()); ?></span>
                        <?php else : ?>
                            <span><?php echo wc_price($product_price); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </li>
        <?php endwhile; wp_reset_postdata(); else : ?>
            <li class="no-products">
                <?php esc_html_e('No products found.', 'woocommerce'); ?>
            </li>
        <?php endif; ?>
    </ul>
</section>