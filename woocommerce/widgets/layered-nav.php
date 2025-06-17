<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
$attribute = isset( $instance['attribute'] ) ? $instance['attribute'] : '';
$is_color = ( $attribute === 'pa_color' );
$is_size  = ( $attribute === 'pa_size' );
?>
<section class="mb-6">
	<?php if ( $title ) : ?>
		<h3 class="font-semibold mb-2 text-base text-gray-900"><?php echo esc_html( $title ); ?></h3>
	<?php endif; ?>

	<?php if ( $is_size ) : ?>
		<div class="flex gap-2">
			<?php foreach ( $terms as $term ) : ?>
				<button type="submit" name="filter_<?php echo esc_attr( $attribute ); ?>[]" value="<?php echo esc_attr( $term->slug ); ?>" class="size-btn px-3 py-1 border border-gray-300 rounded text-sm font-medium<?php if ( in_array( $term->slug, $selected_terms ) ) echo ' bg-[#FF3A5E] text-white border-[#FF3A5E]'; ?>">
					<?php echo esc_html( $term->name ); ?>
				</button>
			<?php endforeach; ?>
		</div>
	<?php elseif ( $is_color ) : ?>
		<div class="flex gap-2 flex-wrap">
			<?php
			$color_map = [
				'black' => '#000',
				'white' => '#fff',
				'red' => '#F44336',
				'blue' => '#2196F3',
				'green' => '#4CAF50',
				'yellow' => '#FFEB3B',
				'gray' => '#757575',
				'camo' => 'repeating-linear-gradient(45deg,#888 0 10px,#fff 10px 20px)',
			];
			foreach ( $terms as $term ) :
				$color = isset( $color_map[ $term->slug ] ) ? $color_map[ $term->slug ] : '#ccc';
			?>
				<label class="relative cursor-pointer">
					<input type="checkbox" name="filter_<?php echo esc_attr( $attribute ); ?>[]" value="<?php echo esc_attr( $term->slug ); ?>" class="hidden" <?php checked( in_array( $term->slug, $selected_terms ) ); ?> onchange="this.form.submit()">
					<span class="color-swatch w-8 h-8 rounded-full border-2 border-gray-300 inline-block align-middle<?php if ( in_array( $term->slug, $selected_terms ) ) echo ' ring-2 ring-[#FF3A5E]'; ?>" style="background:<?php echo esc_attr( $color ); ?>;"></span>
				</label>
			<?php endforeach; ?>
		</div>
		<div class="flex gap-4 mt-2 text-xs">
			<?php foreach ( $terms as $term ) : ?>
				<span><?php echo esc_html( ucfirst( $term->name ) ); ?></span>
			<?php endforeach; ?>
		</div>
	<?php else : ?>
		<ul class="space-y-2">
			<?php foreach ( $terms as $term ) : ?>
				<li>
					<label class="flex items-center gap-2 cursor-pointer">
						<input type="checkbox" name="filter_<?php echo esc_attr( $attribute ); ?>[]" value="<?php echo esc_attr( $term->slug ); ?>" class="custom-checkbox accent-[#FF3A5E] w-5 h-5 rounded border-2 border-gray-300 focus:ring-2 focus:ring-[#FF3A5E]" <?php checked( in_array( $term->slug, $selected_terms ) ); ?> onchange="this.form.submit()">
						<span class="text-gray-800 text-sm"><?php echo esc_html( $term->name ); ?></span>
					</label>
				</li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
</section> 