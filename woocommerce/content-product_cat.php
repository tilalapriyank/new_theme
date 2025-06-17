<?php
/**
 * The template for displaying product category thumbnails within loops.
 */

defined('ABSPATH') || exit;

$thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
$image = wp_get_attachment_url($thumbnail_id);
$image = $image ? $image : wc_placeholder_img_src();
$category_link = get_term_link($category);
?>

<style>
/* Override any existing styles */
.custom-category-grid {
    display: grid !important;
    grid-template-columns: repeat(4, 1fr) !important;
    gap: 1.5rem !important;
    padding: 2rem 0 !important;
    margin: 0 !important;
    max-width: 1200px !important;
    width: 100% !important;
}

.category-card {
    position: relative !important;
    border-radius: 12px !important;
    overflow: hidden !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
    transition: all 0.3s ease !important;
    height: 300px !important;
    background: transparent !important;
    border: none !important;
    margin: 0 !important;
    padding: 0 !important;
    min-width: 0 !important;
    width: 100% !important;
}

.category-card:hover {
    transform: translateY(-8px) !important;
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18) !important;
}

.category-card a {
    text-decoration: none !important;
    color: inherit !important;
    display: block !important;
    height: 100% !important;
    position: relative !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.category-image {
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background-size: cover !important;
    background-position: center !important;
    background-repeat: no-repeat !important;
    z-index: 1 !important;
}

/* Gradient overlay */
.category-image::after {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    background: linear-gradient(135deg, rgba(0,0,0,0.1) 0%, rgba(0,0,0,0.7) 100%) !important;
    z-index: 2 !important;
}

.category-content {
    position: absolute !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    padding: 2rem 1.5rem !important;
    background: transparent !important;
    z-index: 3 !important;
    color: white !important;
    margin: 0 !important;
    border: none !important;
}

.category-content h3 {
    margin: 0 0 0.5rem 0 !important;
    font-size: 1.75rem !important;
    font-weight: 600 !important;
    color: white !important;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3) !important;
    line-height: 1.2 !important;
}

.shop-now {
    color: rgba(255, 255, 255, 0.9) !important;
    font-size: 1rem !important;
    font-weight: 500 !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 0.5rem !important;
    transition: all 0.3s ease !important;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2) !important;
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    margin: 0 !important;
}

.shop-now::after {
    content: '→' !important;
    transition: transform 0.3s ease !important;
}

.category-card:hover .shop-now {
    color: white !important;
    transform: translateX(4px) !important;
}

.category-card:hover .shop-now::after {
    transform: translateX(4px) !important;
}

/* Responsive adjustments */
@media (max-width: 1024px) {
    .custom-category-grid {
        grid-template-columns: repeat(3, 1fr) !important;
    }
}

@media (max-width: 768px) {
    .custom-category-grid {
        grid-template-columns: repeat(2, 1fr) !important;
        gap: 1rem !important;
        padding: 1rem 0 !important;
    }
    
    .category-card {
        height: 250px !important;
    }
    
    .category-content {
        padding: 1.5rem 1rem !important;
    }
    
    .category-content h3 {
        font-size: 1.5rem !important;
    }
}

@media (max-width: 480px) {
    .custom-category-grid {
        grid-template-columns: 1fr !important;
    }
}

/* Additional overrides for common theme conflicts */
.woocommerce .category-card,
.woocommerce .category-card a,
.woocommerce .category-content,
.woocommerce .category-content h3 {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
}

.woocommerce .category-card {
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;
}

.woocommerce .category-card:hover {
    box-shadow: 0 12px 32px rgba(0, 0, 0, 0.18) !important;
}
</style>

<div class="category-card">
    <a href="<?php echo esc_url($category_link); ?>">
        <div class="category-image" style="background-image:url('<?php echo esc_url($image); ?>');"></div>
        <div class="category-content">
            <h3><?php echo esc_html($category->name); ?></h3>
            <span class="shop-now">Shop Now</span>
        </div>
    </a>
</div>

<script>
	document.querySelectorAll('div.woocommerce.column-3').forEach(el => {
  el.classList.remove('column-3');
  el.classList.add('column-1');
});

</script>