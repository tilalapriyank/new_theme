<?php
/**
 * Template Name: Blog Page
 */

get_header();

// Get featured post
$featured_args = array(
    'post_type' => 'blog_post',
    'posts_per_page' => 1,
    'meta_query' => array(
        array(
            'key' => 'featured_post',
            'value' => '1',
            'compare' => '='
        )
    )
);
$featured_query = new WP_Query($featured_args);

// Get latest posts
$latest_args = array(
    'post_type' => 'blog_post',
    'posts_per_page' => 6,
    'post__not_in' => $featured_query->have_posts() ? array($featured_query->posts[0]->ID) : array()
);
$latest_query = new WP_Query($latest_args);

// Get all categories
$categories = get_terms(array(
    'taxonomy' => 'blog_category',
    'hide_empty' => true,
    'orderby' => 'count',
    'order' => 'DESC',
    'number' => 6
));

// Ensure we have categories
if (is_wp_error($categories)) {
    $categories = array();
}
?>

<main id="main-content">
    <!-- Hero Section -->
    <div class="relative bg-[#1A1A1A] text-white">
        <div class="absolute inset-0 opacity-20">
            <?php 
            $hero_image = get_field('blog_hero_image');
            if ($hero_image) : ?>
            <img 
                    src="<?php echo esc_url($hero_image['url']); ?>" 
                    alt="<?php echo esc_attr($hero_image['alt']); ?>" 
                class="w-full h-full object-cover"
            >
            <?php else: ?>
            <img src="https://via.placeholder.com/1200x400?text=Blog+Hero+Image" alt="Default Blog Hero" class="w-full h-full object-cover">
            <?php endif; ?>
        </div>
        <div class="relative container py-20 md:py-28">
            <div class="max-w-3xl">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
                    Hype Pups <span class="text-[#FF3A5E]">Blog</span>
                </h1>
                <p class="text-lg md:text-xl text-white/80 max-w-2xl">
                    <?php 
                    $blog_desc = get_field('blog_description', get_the_ID());
                    echo $blog_desc ? $blog_desc : 'Insights, stories, and guides from the world of premium dog streetwear. Stay updated with the latest trends, behind-the-scenes content, and community stories.';
                    ?>
                </p>
            </div>
        </div>
    </div>

    <div class="container py-12 md:py-16">
        <!-- Search and Filter Section -->
        <div class="mb-12">
            <div class="flex flex-col md:flex-row gap-6 items-start md:items-center justify-between">
                <div class="relative w-full md:w-96">
                    <svg xmlns="http://www.w3.org/2000/svg" class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input 
                        type="text" 
                        placeholder="Search articles..." 
                        class="pl-10 w-full h-10 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#FF3A5E]"
                        id="blog-search"
                    >
                </div>
                <div class="w-full md:w-auto">
                    <div class="bg-gray-100 p-1 rounded-lg w-full md:w-auto flex flex-wrap gap-2">
                        <button class="rounded-md px-4 py-2 text-sm font-medium transition-all bg-white shadow-sm" data-category="all">All</button>
                        <?php 
                        if (!empty($categories)) :
                            foreach ($categories as $category) : ?>
                                <button class="rounded-md px-4 py-2 text-sm font-medium transition-all bg-transparent" data-category="<?php echo esc_attr($category->slug); ?>">
                                    <?php echo esc_html($category->name); ?>
                                </button>
                            <?php endforeach;
                        endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Post -->
        <?php if ($featured_query->have_posts()) : 
            while ($featured_query->have_posts()) : $featured_query->the_post(); 
                $featured_categories = get_the_terms(get_the_ID(), 'blog_category');
                $featured_category = $featured_categories ? $featured_categories[0]->name : '';
        ?>
        <div class="mb-16">
            <div class="grid md:grid-cols-2 gap-8 items-center bg-gray-50 rounded-2xl overflow-hidden">
                <div class="aspect-square md:aspect-auto md:h-full relative flex items-center justify-center">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('large', array('class' => 'w-full h-full object-cover')); ?>
                        <?php endif; ?>
                </div>
                <div class="p-6 md:p-10 md:pr-12">
                    <span class="inline-block mb-4 px-3 py-1 bg-[#FF3A5E] text-white text-xs font-semibold rounded-full">Featured</span>
                        <h2 class="text-2xl md:text-3xl lg:text-4xl font-bold mb-4"><?php the_title(); ?></h2>
                        <p class="text-gray-600 mb-6 text-lg"><?php echo get_the_excerpt(); ?></p>
                    <div class="flex items-center gap-4 mb-6">
                        <div class="flex items-center gap-2">
                                <?php 
                                $author_id = get_the_author_meta('ID');
                                $author_avatar = get_avatar_url($author_id, array('size' => 40));
                                ?>
                            <div class="w-10 h-10 rounded-full overflow-hidden relative">
                                    <img src="<?php echo esc_url($author_avatar); ?>" alt="<?php the_author(); ?>" class="w-full h-full object-cover">
                            </div>
                                <span class="text-gray-700 font-medium"><?php the_author(); ?></span>
                        </div>
                        <div class="flex items-center gap-1 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                                <span><?php echo get_the_date('F j, Y'); ?></span>
                            </div>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="inline-flex items-center px-4 py-2 bg-[#FF3A5E] hover:bg-[#FF3A5E]/90 text-white rounded-md font-medium">
                        Read Article 
                        <svg xmlns="http://www.w3.org/2000/svg" class="ml-2 h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                            <polyline points="12 5 19 12 12 19"></polyline>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
        <?php 
            endwhile;
            wp_reset_postdata();
        endif; ?>

        <!-- Latest Articles -->
        <h2 class="text-2xl md:text-3xl font-bold mb-8">Latest Articles</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-16" id="latest-posts">
            <?php if ($latest_query->have_posts()) : 
                while ($latest_query->have_posts()) : $latest_query->the_post(); 
                    $categories = get_the_terms(get_the_ID(), 'blog_category');
                    $category = $categories ? $categories[0]->name : '';
            ?>
            <div class="overflow-hidden border-none shadow-md hover:shadow-lg transition-shadow duration-300 h-full flex flex-col rounded-lg">
                <div class="aspect-[4/3] relative flex items-center justify-center">
                        <?php if (has_post_thumbnail()) : ?>
                            <?php the_post_thumbnail('medium', array('class' => 'w-full h-full object-cover')); ?>
                        <?php endif; ?>
                        <?php if ($category) : ?>
                            <span class="absolute top-3 left-3 bg-[#FF3A5E] text-white text-xs font-semibold px-2 py-1 rounded-full"><?php echo esc_html($category); ?></span>
                        <?php endif; ?>
                </div>
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex items-center gap-4 mb-3 text-sm text-gray-500">
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                                <span><?php the_author(); ?></span>
                        </div>
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="16" y1="2" x2="16" y2="6"></line>
                                <line x1="8" y1="2" x2="8" y2="6"></line>
                                <line x1="3" y1="10" x2="21" y2="10"></line>
                            </svg>
                                <span><?php echo get_the_date('F j, Y'); ?></span>
                        </div>
                    </div>
                    <h3 class="text-xl font-bold mb-2 line-clamp-2 hover:text-[#FF3A5E] transition-colors">
                            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                        <p class="text-gray-600 mb-4 line-clamp-3 flex-grow"><?php echo get_the_excerpt(); ?></p>
                        <a href="<?php the_permalink(); ?>" class="inline-flex items-center text-[#FF3A5E] font-medium hover:underline mt-auto self-start">
                            Read More 
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </a>
                    </div>
                </div>
            <?php 
                endwhile;
                wp_reset_postdata();
            endif; ?>
        </div>

        <!-- Popular Categories -->
        <h2 class="text-2xl md:text-3xl font-bold mb-8">Popular Categories</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-16">
            <?php 
            if (!empty($categories)) :
                foreach ($categories as $category) : ?>
                    <a href="<?php echo esc_url(get_term_link($category)); ?>" class="bg-[#232323] text-white rounded-lg flex items-center justify-center h-24 text-lg font-semibold hover:bg-[#FF3A5E] transition-colors">
                        <?php echo esc_html($category->name); ?>
                    </a>
                <?php endforeach;
            endif; ?>
        </div>

        <!-- Newsletter Section -->
        <div class="bg-[#1A1A1A] text-white rounded-2xl overflow-hidden mb-16">
            <div class="grid md:grid-cols-2 items-center">
                <div class="p-8 md:p-10 lg:p-12">
                    <h2 class="text-2xl md:text-3xl font-bold mb-4">Join Our Newsletter</h2>
                    <p class="text-white/80 mb-6">
                        Get the latest articles, style guides, and exclusive offers delivered directly to your inbox. No spam, just the content you want.
                    </p>
                    <form class="flex flex-col sm:flex-row gap-3" id="newsletter-form">
                        <input 
                            type="email" 
                            placeholder="Your email address" 
                            class="bg-white/10 border-white/20 text-white placeholder:text-white/60 px-4 py-2 rounded-md w-full"
                            required
                        >
                        <button type="submit" class="bg-[#FF3A5E] hover:bg-[#FF3A5E]/90 text-white whitespace-nowrap flex items-center justify-center px-4 py-2 rounded-md font-medium">
                            Subscribe 
                            <svg xmlns="http://www.w3.org/2000/svg" class="ml-1 h-4 w-4" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="5" y1="12" x2="19" y2="12"></line>
                                <polyline points="12 5 19 12 12 19"></polyline>
                            </svg>
                        </button>
                    </form>
                </div>
                <div class="hidden md:block relative h-full min-h-[300px]">
                    <?php 
                    $newsletter_image = get_field('newsletter_image');
                    if ($newsletter_image) : ?>
                        <img src="<?php echo esc_url($newsletter_image['url']); ?>" alt="<?php echo esc_attr($newsletter_image['alt']); ?>" class="w-full h-full object-cover">
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php get_footer(); ?> 