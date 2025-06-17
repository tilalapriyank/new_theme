<?php
/**
 * Template Name: About Page
 */

get_header();
?>

<div class="py-12 md:py-16">
    <div class="container mx-auto px-4">
        <!-- Hero Section -->
        <div class="mb-16 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6"><?php echo esc_html(get_field('hero_title')); ?></h1>
            <p class="text-lg text-gray-600 max-w-3xl mx-auto">
                <?php echo esc_html(get_field('hero_description')); ?>
            </p>
        </div>

        <!-- Our Story Section -->
        <div class="grid md:grid-cols-2 gap-12 items-center mb-20">
            <div class="relative aspect-square rounded-xl overflow-hidden">
                <?php 
                $story_image = get_field('story_image');
                if($story_image): ?>
                    <img src="<?php echo esc_url($story_image['url']); ?>" 
                         alt="<?php echo esc_attr($story_image['alt']); ?>" 
                         class="w-full h-full object-cover">
                <?php endif; ?>
            </div>
            <div class="space-y-6">
                <h2 class="text-3xl font-bold"><?php echo esc_html(get_field('story_title')); ?></h2>
                <div class="text-lg">
                    <?php echo wp_kses_post(get_field('story_content')); ?>
                </div>
            </div>
        </div>

        <!-- Our Values Section -->
        <div class="mb-20">
            <h2 class="text-3xl font-bold mb-10 text-center"><?php echo esc_html(get_field('values_title')); ?></h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                <!-- Value 1 -->
                <div class="bg-white p-10 rounded-xl shadow-lg border border-gray-100 flex flex-col items-start transition hover:shadow-xl">
                    <div class="w-16 h-16 bg-[#FF3A5E]/10 rounded-full flex items-center justify-center mb-6">
                        <?php echo wp_kses(get_field('value_1_icon'), array(
                            'svg' => array(
                                'xmlns' => array(),
                                'class' => array(),
                                'fill' => array(),
                                'viewBox' => array(),
                                'width' => array(),
                                'height' => array(),
                                'color' => array(),
                            ),
                            'path' => array(
                                'stroke-linecap' => array(),
                                'stroke-linejoin' => array(),
                                'stroke-width' => array(),
                                'd' => array(),
                                'fill' => array(),
                                'color' => array(),
                            ),
                        )); ?>
                    </div>
                    <h3 class="text-xl font-bold mb-3"><?php echo esc_html(get_field('value_1_title')); ?></h3>
                    <p class="text-[16px] text-gray-600">
                        <?php echo esc_html(get_field('value_1_description')); ?>
                    </p>
                </div>
                <!-- Value 2 -->
                <div class="bg-white p-10 rounded-xl shadow-lg border border-gray-100 flex flex-col items-start transition hover:shadow-xl">
                    <div class="w-16 h-16 bg-[#FF3A5E]/10 rounded-full flex items-center justify-center mb-6">
                        <?php echo wp_kses(get_field('value_2_icon'), array(
                            'svg' => array(
                                'xmlns' => array(),
                                'class' => array(),
                                'fill' => array(),
                                'viewBox' => array(),
                                'width' => array(),
                                'height' => array(),
                                'color' => array(),
                            ),
                            'path' => array(
                                'stroke-linecap' => array(),
                                'stroke-linejoin' => array(),
                                'stroke-width' => array(),
                                'd' => array(),
                                'fill' => array(),
                                'color' => array(),
                            ),
                        )); ?>
                    </div>
                    <h3 class="text-xl font-bold mb-3"><?php echo esc_html(get_field('value_2_title')); ?></h3>
                    <p class="text-[16px] text-gray-600">
                        <?php echo esc_html(get_field('value_2_description')); ?>
                    </p>
                </div>
                <!-- Value 3 -->
                <div class="bg-white p-10 rounded-xl shadow-lg border border-gray-100 flex flex-col items-start transition hover:shadow-xl">
                    <div class="w-16 h-16 bg-[#FF3A5E]/10 rounded-full flex items-center justify-center mb-6">
                        <?php echo wp_kses(get_field('value_3_icon'), array(
                            'svg' => array(
                                'xmlns' => array(),
                                'class' => array(),
                                'fill' => array(),
                                'viewBox' => array(),
                                'width' => array(),
                                'height' => array(),
                                'color' => array(),
                            ),
                            'path' => array(
                                'stroke-linecap' => array(),
                                'stroke-linejoin' => array(),
                                'stroke-width' => array(),
                                'd' => array(),
                                'fill' => array(),
                                'color' => array(),
                            ),
                        )); ?>
                    </div>
                    <h3 class="text-xl font-bold mb-3"><?php echo esc_html(get_field('value_3_title')); ?></h3>
                    <p class="text-[16px] text-gray-600">
                        <?php echo esc_html(get_field('value_3_description')); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Team Section -->
        <div class="mb-20">
            <h2 class="text-3xl font-bold mb-10 text-center"><?php echo esc_html(get_field('team_title')); ?></h2>
            <div class="grid md:grid-cols-4 gap-6">
                <!-- Team Member 1 -->
                <div class="text-center">
                    <div class="aspect-square relative rounded-xl overflow-hidden mb-4">
                        <?php 
                        $member_image = get_field('team_member_1_image');
                        if($member_image): ?>
                            <img src="<?php echo esc_url($member_image['url']); ?>" 
                                 alt="<?php echo esc_attr($member_image['alt']); ?>" 
                                 class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <h3 class="font-bold text-lg"><?php echo esc_html(get_field('team_member_1_name')); ?></h3>
                    <p class="text-sm text-gray-600"><?php echo esc_html(get_field('team_member_1_position')); ?></p>
                </div>

                <!-- Team Member 2 -->
                <div class="text-center">
                    <div class="aspect-square relative rounded-xl overflow-hidden mb-4">
                        <?php 
                        $member_image = get_field('team_member_2_image');
                        if($member_image): ?>
                            <img src="<?php echo esc_url($member_image['url']); ?>" 
                                 alt="<?php echo esc_attr($member_image['alt']); ?>" 
                                 class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <h3 class="font-bold text-lg"><?php echo esc_html(get_field('team_member_2_name')); ?></h3>
                    <p class="text-sm text-gray-600"><?php echo esc_html(get_field('team_member_2_position')); ?></p>
                </div>

                <!-- Team Member 3 -->
                <div class="text-center">
                    <div class="aspect-square relative rounded-xl overflow-hidden mb-4">
                        <?php 
                        $member_image = get_field('team_member_3_image');
                        if($member_image): ?>
                            <img src="<?php echo esc_url($member_image['url']); ?>" 
                                 alt="<?php echo esc_attr($member_image['alt']); ?>" 
                                 class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <h3 class="font-bold text-lg"><?php echo esc_html(get_field('team_member_3_name')); ?></h3>
                    <p class="text-sm text-gray-600"><?php echo esc_html(get_field('team_member_3_position')); ?></p>
                </div>

                <!-- Team Member 4 -->
                <div class="text-center">
                    <div class="aspect-square relative rounded-xl overflow-hidden mb-4">
                        <?php 
                        $member_image = get_field('team_member_4_image');
                        if($member_image): ?>
                            <img src="<?php echo esc_url($member_image['url']); ?>" 
                                 alt="<?php echo esc_attr($member_image['alt']); ?>" 
                                 class="w-full h-full object-cover">
                        <?php endif; ?>
                    </div>
                    <h3 class="font-bold text-lg"><?php echo esc_html(get_field('team_member_4_name')); ?></h3>
                    <p class="text-sm text-gray-600"><?php echo esc_html(get_field('team_member_4_position')); ?></p>
                </div>
            </div>
        </div>
        <!-- CTA Section -->
        <div class="mb-20">
            <div class="bg-gray-900 text-white rounded-xl p-10 text-center max-w-8xl mx-auto">
                <h2 class="text-2xl md:text-3xl font-bold mb-4">
                    <?php echo esc_html(get_field('cta_title')); ?>
                </h2>
                <p class="mb-6 text-lg">
                    <?php echo esc_html(get_field('cta_description')); ?>
                </p>
                <?php $cta_button_1 = get_field('cta_button_1'); $cta_button_2 = get_field('cta_button_2'); ?>
                <div class="flex flex-col md:flex-row gap-4 justify-center">
                    <?php if ($cta_button_1 && !empty($cta_button_1['text']) && !empty($cta_button_1['url'])): ?>
                        <a href="<?php echo esc_url($cta_button_1['url']); ?>" class="bg-white text-gray-900 font-semibold px-6 py-3 rounded-lg hover:bg-gray-100 transition"><?php echo esc_html($cta_button_1['text']); ?></a>
                    <?php endif; ?>
                    <?php if ($cta_button_2 && !empty($cta_button_2['text']) && !empty($cta_button_2['url'])): ?>
                        <a href="<?php echo esc_url($cta_button_2['url']); ?>" class="bg-transparent border border-white text-white font-semibold px-6 py-3 rounded-lg hover:bg-white hover:text-gray-900 transition"><?php echo esc_html($cta_button_2['text']); ?></a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php get_footer(); ?> 