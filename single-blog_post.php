<?php
/**
 * Template Name: Single Blog Post
 * Template Post Type: post
 */

get_header(); ?>

<main id="main-content">
  <!-- Blog Post Header -->
  <div class="bg-[#1A1A1A] text-white">
    <div class="container py-12 md:py-16">
      <div class="flex flex-col space-y-2">
        <a href="<?php echo get_permalink(get_option('page_for_posts')); ?>" class="inline-flex items-center text-white/80 hover:text-white mb-4 transition-colors">
          <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 h-4 w-4">
            <path d="m12 19-7-7 7-7"></path>
            <path d="M19 12H5"></path>
          </svg>
          Back to Blog
        </a>
        <div class="inline-flex items-center">
          <?php if (has_tag('featured')): ?>
            <span class="bg-[#FF3A5E] text-white text-xs font-medium px-2.5 py-0.5 rounded-full">Featured</span>
            <span class="mx-2 text-white/60">•</span>
          <?php endif; ?>
          <span class="text-white/60 text-sm"><?php echo get_the_date('F j, Y'); ?></span>
        </div>
        <h1 class="text-3xl md:text-4xl lg:text-5xl font-bold mt-2 mb-6"><?php the_title(); ?></h1>
        <div class="flex items-center space-x-4">
          <div class="flex items-center">
            <div class="w-10 h-10 rounded-full overflow-hidden mr-3">
              <?php echo get_avatar(get_the_author_meta('ID'), 100, '', '', array('class' => 'w-full h-full object-cover')); ?>
            </div>
            <div>
              <p class="font-medium"><?php the_author(); ?></p>
              <p class="text-sm text-white/60"><?php echo get_the_author_meta('description'); ?></p>
            </div>
          </div>
          <div class="flex items-center text-white/60">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1 h-4 w-4">
              <path d="M21 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
              <circle cx="12" cy="10" r="3"></circle>
            </svg>
            <span class="text-sm"><?php echo reading_time(); ?> min read</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Featured Image -->
  <?php if (has_post_thumbnail()): ?>
    <div class="w-full h-[300px] md:h-[500px] relative">
      <?php the_post_thumbnail('full', array('class' => 'w-full h-full object-cover')); ?>
    </div>
  <?php endif; ?>

  <!-- Blog Content -->
  <div class="container py-12">
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
      <!-- Main Content -->
      <div class="lg:col-span-8">
        <article class="bg-white rounded-xl shadow-sm p-6 md:p-8 lg:p-10">
          <div class="blog-content prose prose-lg max-w-none">
            <?php the_content(); ?>
          </div>

          <!-- Tags -->
          <?php 
          $post_tags = get_the_tags();
          if ($post_tags) : ?>
            <div class="mt-10 pt-6 border-t">
              <div class="flex flex-wrap gap-2">
                <span class="text-sm font-medium text-gray-700 mr-2">Tags:</span>
                <?php
                foreach ($post_tags as $tag) {
                  $tag_link = get_tag_link($tag->term_id);
                  $tag_color = get_term_meta($tag->term_id, 'tag_color', true) ?: '#FF3A5E';
                  ?>
                  <a href="<?php echo esc_url($tag_link); ?>" 
                     class="px-3 py-1.5 bg-gray-100 hover:bg-gray-200 rounded-full text-sm text-gray-700 transition-colors flex items-center gap-1.5"
                     style="--tag-color: <?php echo esc_attr($tag_color); ?>">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-[var(--tag-color)]">
                      <path d="M12 2H2v10l9.29 9.29c.94.94 2.48.94 3.42 0l6.58-6.58c.94-.94.94-2.48 0-3.42L12 2Z"></path>
                      <path d="M7 7h.01"></path>
                    </svg>
                    <?php echo esc_html($tag->name); ?>
                  </a>
                <?php } ?>
              </div>
            </div>
          <?php endif; ?>

          <!-- Share -->
          <div class="mt-6 flex items-center">
            <span class="text-sm font-medium text-gray-700 mr-4">Share this article:</span>
            <div class="flex space-x-2">
              <button class="share-button p-2 rounded-full border border-gray-200 transition-colors" aria-label="Share on Facebook" data-platform="facebook">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                </svg>
              </button>
              <button class="share-button p-2 rounded-full border border-gray-200 transition-colors" aria-label="Share on Twitter" data-platform="twitter">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
                </svg>
              </button>
              <button class="share-button p-2 rounded-full border border-gray-200 transition-colors" aria-label="Share on LinkedIn" data-platform="linkedin">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                  <rect width="4" height="12" x="2" y="9"></rect>
                  <circle cx="4" cy="4" r="2"></circle>
                </svg>
              </button>
              <button class="share-button p-2 rounded-full border border-gray-200 transition-colors" aria-label="Copy Link" data-platform="copy">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                  <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                </svg>
              </button>
            </div>
          </div>

          <!-- Author Bio -->
          <div class="mt-10 pt-6 border-t">
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
              <div class="w-16 h-16 rounded-full overflow-hidden flex-shrink-0">
                <?php echo get_avatar(get_the_author_meta('ID'), 100, '', '', array('class' => 'w-full h-full object-cover')); ?>
              </div>
              <div>
                <h3 class="font-bold text-lg"><?php the_author(); ?></h3>
                <p class="text-gray-600 mb-2"><?php echo get_the_author_meta('description'); ?></p>
                <p class="text-sm text-gray-600"><?php echo get_the_author_meta('user_description'); ?></p>
              </div>
            </div>
          </div>

          <!-- Comments Section -->
          <?php if (comments_open() || get_comments_number()): ?>
            <div class="mt-10 pt-6 border-t">
              <h3 class="text-xl font-bold mb-6">Comments (<?php echo get_comments_number(); ?>)</h3>
              
              <!-- Comment Form -->
              <div class="mb-8">
                <?php 
                comment_form(array(
                  'title_reply' => '',
                  'title_reply_to' => 'Reply to %s',
                  'cancel_reply_link' => 'Cancel Reply',
                  'label_submit' => 'Post Comment',
                  'class_submit' => 'submit',
                  'comment_notes_before' => '',
                  'comment_notes_after' => '',
                  'comment_field' => '',
                  'id_form' => 'commentform',
                  'class_form' => 'comment-form',
                  'id_submit' => 'submit',
                  'name_submit' => 'submit',
                  'submit_button' => '<button type="submit" class="bg-[#FF3A5E] hover:bg-[#E02E50] text-white font-medium py-2 px-6 rounded-lg transition-colors">%4$s</button>',
                  'submit_field' => '<div class="form-submit">%1$s %2$s</div>',
                  'format' => 'html5'
                ));
                ?>
              </div>

              <!-- Comments List -->
              <div class="space-y-6">
                <?php
                wp_list_comments(array(
                  'style' => 'div',
                  'callback' => 'hype_pups_comment_callback',
                  'avatar_size' => 50,
                  'reverse_top_level' => false,
                  'max_depth' => 3,
                  'end-callback' => 'hype_pups_comment_end_callback'
                ));
                ?>
              </div>

              <!-- Comment Navigation -->
              <?php if (get_comment_pages_count() > 1 && get_option('page_comments')) : ?>
                <nav class="comment-navigation mt-8 flex justify-between">
                  <div class="nav-previous">
                    <?php previous_comments_link('← Older Comments'); ?>
                  </div>
                  <div class="nav-next">
                    <?php next_comments_link('Newer Comments →'); ?>
                  </div>
                </nav>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </article>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-4">
        <!-- Author Card -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
          <div class="flex flex-col items-center text-center">
            <div class="w-20 h-20 rounded-full overflow-hidden mb-4">
              <?php echo get_avatar(get_the_author_meta('ID'), 100, '', '', array('class' => 'w-full h-full object-cover')); ?>
            </div>
            <h3 class="font-bold text-lg"><?php the_author(); ?></h3>
            <p class="text-gray-600 mb-4"><?php echo get_the_author_meta('description'); ?></p>
            <a href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>" class="text-[#FF3A5E] hover:underline text-sm font-medium">
              View all posts
            </a>
          </div>
        </div>

        <!-- Popular Posts -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
          <h3 class="font-bold text-lg mb-4">Popular Posts</h3>
          <div class="space-y-4">
            <?php
            $popular_posts = new WP_Query(array(
              'post_type' => 'post',
              'posts_per_page' => 4,
              'meta_key' => 'post_views_count',
              'orderby' => 'meta_value_num',
              'order' => 'DESC',
              'post_status' => 'publish'
            ));

            if ($popular_posts->have_posts()) :
              while ($popular_posts->have_posts()) : $popular_posts->the_post();
            ?>
              <a href="<?php the_permalink(); ?>" class="flex items-start gap-3 group">
                <div class="w-16 h-16 rounded-md overflow-hidden flex-shrink-0">
                  <?php 
                  if (has_post_thumbnail()) {
                    the_post_thumbnail('thumbnail', array('class' => 'w-full h-full object-cover'));
                  } else {
                    echo '<div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                          </div>';
                  }
                  ?>
                </div>
                <div>
                  <h4 class="font-medium text-sm group-hover:text-[#FF3A5E] transition-colors"><?php the_title(); ?></h4>
                  <p class="text-xs text-gray-500 mt-1"><?php echo get_the_date('F j, Y'); ?></p>
                </div>
              </a>
            <?php
              endwhile;
              wp_reset_postdata();
            else:
            ?>
              <p class="text-gray-500 text-sm">No popular posts found.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Categories -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
          <h3 class="font-bold text-lg mb-4">Categories</h3>
          <div class="space-y-2">
            <?php
            $categories = get_terms([
              'taxonomy' => 'blog_category',
              'orderby' => 'count',
              'order' => 'DESC',
              'hide_empty' => true
            ]);
            
            if (!empty($categories)) :
              foreach ($categories as $category) :
                $category_link = get_term_link($category);
            ?>
              <a href="<?php echo esc_url($category_link); ?>" class="flex justify-between items-center py-2 border-b border-gray-100 hover:text-[#FF3A5E] transition-colors">
                <span><?php echo esc_html($category->name); ?></span>
                <span class="text-gray-500 text-sm"><?php echo esc_html($category->count); ?></span>
              </a>
            <?php 
              endforeach;
            else:
            ?>
              <p class="text-gray-500 text-sm">No categories found.</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Newsletter -->
        <div class="bg-[#1A1A1A] text-white rounded-xl shadow-sm p-6">
          <h3 class="font-bold text-lg mb-2">Subscribe to Our Newsletter</h3>
          <p class="text-white/80 text-sm mb-4">Get the latest articles, style guides, and exclusive offers delivered directly to your inbox.</p>
          <form id="newsletter-form" class="newsletter-form">
            <div class="mb-3">
              <input 
                type="email" 
                name="email"
                placeholder="Your email address" 
                class="w-full px-4 py-2 bg-white/10 border border-white/20 rounded-lg text-white placeholder:text-white/60 focus:outline-none focus:border-[#FF3A5E]"
                required
              >
            </div>
            <button type="submit" class="w-full bg-[#FF3A5E] hover:bg-[#E02E50] text-white font-medium py-2 px-4 rounded-lg transition-colors flex items-center justify-center">
              Subscribe
              <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-2 h-4 w-4">
                <path d="M5 12h14"></path>
                <path d="m12 5 7 7-7 7"></path>
              </svg>
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Related Posts -->
    <?php
    $categories = get_the_terms(get_the_ID(), 'blog_category');
    if ($categories) :
      $category_ids = array();
      foreach ($categories as $category) {
        $category_ids[] = $category->term_id;
      }
      
      $related_posts = new WP_Query(array(
        'post_type' => 'blog_post',
        'tax_query' => [
          [
            'taxonomy' => 'blog_category',
            'field' => 'term_id',
            'terms' => $category_ids,
          ]
        ],
        'post__not_in' => array(get_the_ID()),
        'posts_per_page' => 3,
        'orderby' => 'rand',
        'post_status' => 'publish'
      ));

      if ($related_posts->have_posts()) :
    ?>
      <div class="mt-16">
        <h2 class="text-2xl font-bold mb-8">You Might Also Like</h2>
        <div class="grid md:grid-cols-3 gap-8">
          <?php while ($related_posts->have_posts()) : $related_posts->the_post(); ?>
            <div class="bg-white rounded-xl shadow-sm overflow-hidden group">
              <div class="aspect-[4/3] relative">
                <?php 
                if (has_post_thumbnail()) {
                  the_post_thumbnail('large', array('class' => 'w-full h-full object-cover transition-transform group-hover:scale-105 duration-300'));
                } else {
                  echo '<div class="w-full h-full bg-gray-200 flex items-center justify-center">
                          <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                          </svg>
                        </div>';
                }
                ?>
                <div class="absolute top-0 left-0 w-full h-full bg-gradient-to-b from-black/0 to-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
              </div>
              <div class="p-6">
                <div class="flex items-center gap-4 mb-3 text-sm text-gray-500">
                  <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                      <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span><?php the_author(); ?></span>
                  </div>
                  <div class="flex items-center gap-1">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                      <rect width="18" height="18" x="3" y="4" rx="2" ry="2"></rect>
                      <line x1="16" x2="16" y1="2" y2="6"></line>
                      <line x1="8" x2="8" y1="2" y2="6"></line>
                      <line x1="3" x2="21" y1="10" y2="10"></line>
                    </svg>
                    <span><?php echo get_the_date('F j, Y'); ?></span>
                  </div>
                </div>
                <h3 class="text-xl font-bold mb-2 group-hover:text-[#FF3A5E] transition-colors">
                  <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </h3>
                <p class="text-gray-600 mb-4"><?php echo wp_trim_words(get_the_excerpt(), 20); ?></p>
                <a href="<?php the_permalink(); ?>" class="text-[#FF3A5E] hover:underline font-medium flex items-center">
                  Read Article
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-1 h-4 w-4">
                    <path d="M5 12h14"></path>
                    <path d="m12 5 7 7-7 7"></path>
                  </svg>
                </a>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </div>
    <?php
      endif;
      wp_reset_postdata();
    endif;
    ?>
  </div>
</main>

<?php get_footer(); ?>

<script>
  // Share buttons functionality
  document.querySelectorAll('.share-button').forEach(button => {
    button.addEventListener('click', function() {
      const platform = this.getAttribute('data-platform');
      const url = encodeURIComponent(window.location.href);
      const title = encodeURIComponent(document.title);
      
      let shareUrl = '';
      
      switch(platform) {
        case 'facebook':
          shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${url}`;
          break;
        case 'twitter':
          shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${title}`;
          break;
        case 'linkedin':
          shareUrl = `https://www.linkedin.com/shareArticle?mini=true&url=${url}&title=${title}`;
          break;
        case 'copy':
          navigator.clipboard.writeText(window.location.href)
            .then(() => {
              // Show success message
              const button = this;
              const originalHTML = button.innerHTML;
              button.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-green-500">
                  <polyline points="20 6 9 17 4 12"></polyline>
                </svg>
              `;
              button.classList.add('bg-green-50', 'border-green-200');
              setTimeout(() => {
                button.innerHTML = originalHTML;
                button.classList.remove('bg-green-50', 'border-green-200');
              }, 2000);
            })
            .catch(err => {
              console.error('Could not copy text: ', err);
              alert('Failed to copy link. Please try again.');
            });
          return;
      }
      
      if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
      }
    });
  });

  // Newsletter form submission
  const newsletterForm = document.getElementById('newsletter-form');
  if (newsletterForm) {
    newsletterForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const email = this.querySelector('input[name="email"]').value;
      const submitButton = this.querySelector('button[type="submit"]');
      const originalButtonText = submitButton.innerHTML;
      
      // Show loading state
      submitButton.disabled = true;
      submitButton.innerHTML = `
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Subscribing...
      `;
      
      // Send AJAX request to WordPress backend
      const formData = new FormData();
      formData.append('action', 'newsletter_subscription');
      formData.append('email', email);
      formData.append('nonce', '<?php echo wp_create_nonce("newsletter_nonce"); ?>');

      fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Show success message
          submitButton.innerHTML = `
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2">
              <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            Subscribed!
          `;
          submitButton.classList.add('bg-green-600', 'hover:bg-green-700');
          this.reset();
          
          // Reset button after 2 seconds
          setTimeout(() => {
            submitButton.innerHTML = originalButtonText;
            submitButton.classList.remove('bg-green-600', 'hover:bg-green-700');
            submitButton.disabled = false;
          }, 2000);
        } else {
          throw new Error(data.message || 'An error occurred. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
        alert(error.message || 'An error occurred. Please try again.');
      });
    });
  }

  // Comment form submission
  const commentForm = document.getElementById('commentform');
  if (commentForm) {
    commentForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const submitButton = this.querySelector('button[type="submit"]');
      const originalButtonText = submitButton.innerHTML;
      
      // Show loading state
      submitButton.disabled = true;
      submitButton.innerHTML = `
        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Posting...
      `;
      
      const formData = new FormData(this);
      formData.append('action', 'submit_comment');
      formData.append('post_id', '<?php echo get_the_ID(); ?>');
      formData.append('nonce', '<?php echo wp_create_nonce("comment_nonce"); ?>');

      fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          location.reload();
        } else {
          throw new Error(data.message || 'An error occurred. Please try again.');
        }
      })
      .catch(error => {
        console.error('Error:', error);
        submitButton.innerHTML = originalButtonText;
        submitButton.disabled = false;
        alert(error.message || 'An error occurred. Please try again.');
      });
    });
  }
</script> 