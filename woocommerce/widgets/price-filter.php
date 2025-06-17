<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
?>
<section class="mb-6">
	<?php if ( $title ) : ?>
		<h3 class="font-semibold mb-2 text-base text-gray-900"><?php echo esc_html( $title ); ?></h3>
	<?php endif; ?>
	<div class="price_slider_wrapper">
		<?php woocommerce_price_filter(); ?>
	</div>
	<div class="flex justify-between text-sm mt-2">
		<span class="text-gray-700">$0</span>
		<span class="text-gray-700">$200</span>
	</div>
</section> 