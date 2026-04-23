<?php
/**
 * Homepage block: masonry grid of latest posts.
 *
 * @var string $section_title   Section heading.
 * @var string $section_desc    Section tagline.
 * @var int    $posts_per_page  How many posts to show.
 * @var string $category_slug   Optional category filter.
 */

$section_title = isset($args['section_title']) ? (string) $args['section_title'] : 'Новости двора:';
$section_desc  = isset($args['section_desc'])  ? (string) $args['section_desc']  : '';
$pm_limit      = isset($args['pm_limit'])      ? (int) $args['pm_limit']         : (int) get_option('posts_per_page', 8);
$category_slug = isset($args['category_slug']) ? (string) $args['category_slug'] : '';

$query_args = array(
    'posts_per_page'      => $pm_limit,
    'post_status'         => 'publish',
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => true,
);

if ('' !== $category_slug) {
    $query_args['category_name'] = $category_slug;
}

$posts_query = new WP_Query($query_args);
?>
<section class="posts-masonry" id="posts-masonry-section">

  <div class="posts-masonry__header">
    <h2 class="posts-masonry__title"><?php echo esc_html($section_title); ?></h2>
    <span class="posts-masonry__pin" aria-hidden="true">📌</span>
    <p class="posts-masonry__desc"<?php echo '' === $section_desc ? ' hidden' : ''; ?>>
      <?php echo esc_html($section_desc); ?>
    </p>
  </div>

  <div class="posts-masonry__grid js-posts-masonry-grid" aria-live="polite" aria-busy="false"
    data-total-pages="<?php echo (int) $posts_query->max_num_pages; ?>"
  >
    <?php if ($posts_query->have_posts()) : ?>
      <?php while ($posts_query->have_posts()) : $posts_query->the_post(); ?>
        <?php get_template_part('template-parts/post-card'); ?>
      <?php endwhile; wp_reset_postdata(); ?>
    <?php else : ?>
      <p class="posts-masonry__empty">Записей не найдено.</p>
    <?php endif; ?>
  </div>

  <div class="posts-masonry__sentinel js-posts-sentinel" aria-hidden="true"></div>

</section>
