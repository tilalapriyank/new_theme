<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! empty( $list_args['dropdown'] ) ) {
	woocommerce_widget_product_categories_dropdown( $list_args, $args );
	return;
}
$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
?>
<style>
.category-card {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 2px 12px rgba(0,0,0,0.10);
  background: #f3f3f3;
  min-height: 320px;
  display: flex;
  align-items: flex-end;
  transition: box-shadow 0.3s;
}
.category-card:hover .category-image {
  transform: scale(1.07);
}
.category-card:hover .category-gradient {
  background: linear-gradient(180deg, rgba(0,0,0,0.10) 60%, rgba(0,0,0,0.70) 100%);
}
.category-image {
  position: absolute;
  top: 0; left: 0; width: 100%; height: 100%;
  object-fit: cover;
  transition: transform 0.4s cubic-bezier(.4,0,.2,1);
  z-index: 1;
}
.category-gradient {
  position: absolute;
  left: 0; bottom: 0; width: 100%; height: 60%;
  background: linear-gradient(180deg, rgba(0,0,0,0.00) 0%, rgba(0,0,0,0.70) 100%);
  z-index: 2;
  transition: background 0.4s;
}
.category-content {
  position: relative;
  z-index: 3;
  padding: 0 0 32px 24px;
  color: #fff;
}
.category-title {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  letter-spacing: -0.5px;
}
.category-link {
  display: inline-flex;
  align-items: center;
  font-size: 1rem;
  color: #e0e0e0;
  text-decoration: none;
  font-weight: 400;
  transition: color 0.2s;
}
.category-link:hover {
  color: #FF3A5E;
}
.category-link svg {
  margin-left: 4px;
  width: 18px;
  height: 18px;
}
</style>
<section class="mb-6">
	<?php if ( $title ) : ?>
		<h3 class="font-semibold mb-2 text-base text-gray-900"><?php echo esc_html( $title ); ?></h3>
	<?php endif; ?>
	<ul class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
		<?php foreach ( $categories as $category ) : ?>
			<?php
			$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
			$image_url = $thumbnail_id ? wp_get_attachment_url( $thumbnail_id ) : wc_placeholder_img_src();
			$category_link = get_term_link( $category );
			?>
			<li>
				<a href="<?php echo esc_url( $category_link ); ?>" class="category-card group block">
					<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $category->name ); ?>" class="category-image" loading="lazy" />
					<div class="category-gradient"></div>
					<div class="category-content">
						<div class="category-title"><?php echo esc_html( $category->name ); ?></div>
						<span class="category-link">
							Shop Now
							<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
						</span>
					</div>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</section> 