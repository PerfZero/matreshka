<?php
/**
 * Single post card — used inside a WP loop (initial render + AJAX responses).
 */
$author_id      = (int) get_the_author_meta('ID');
$first_name     = (string) get_the_author_meta('first_name');
$last_name      = (string) get_the_author_meta('last_name');
$author_name    = trim($first_name . ' ' . $last_name);
if ('' === $author_name) {
    $author_name = (string) get_the_author_meta('display_name');
}
$author_age     = get_user_meta($author_id, 'local_theme_author_age', true);
$avatar_id      = (int) get_user_meta($author_id, 'local_theme_user_avatar_id', true);
$avatar_url     = $avatar_id
    ? wp_get_attachment_image_url($avatar_id, 'thumbnail')
    : get_avatar_url($author_id, array('size' => 80));
$thumb_url      = get_the_post_thumbnail_url(null, 'large');
$comment_count  = (int) get_comments_number();
$post_date_str  = function_exists('local_theme_format_post_time')
    ? local_theme_format_post_time(get_the_ID())
    : get_the_date();
?>
<article class="post-card">
  <a href="<?php echo esc_url(get_the_permalink()); ?>" class="post-card__link">

    <?php if ($thumb_url) : ?>
      <img
        class="post-card__img"
        src="<?php echo esc_url($thumb_url); ?>"
        alt="<?php echo esc_attr(get_the_title()); ?>"
        loading="lazy"
      >
    <?php else : ?>
      <div class="post-card__img-placeholder"></div>
    <?php endif; ?>

    <div class="post-card__gradient" aria-hidden="true"></div>

    <span class="post-card__badge">Статья</span>

    <div class="post-card__body">
      <div class="post-card__author">
        <img
          class="post-card__avatar"
          src="<?php echo esc_url((string) $avatar_url); ?>"
          alt="<?php echo esc_attr($author_name); ?>"
          width="44"
          height="44"
        >
        <div class="post-card__author-info">
          <span class="post-card__author-name"><?php echo esc_html($author_name); ?></span>
          <?php if ($author_age) : ?>
            <span class="post-card__author-age"><?php echo absint($author_age); ?> лет</span>
          <?php endif; ?>
        </div>
      </div>
      <time class="post-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
        <?php echo esc_html($post_date_str); ?>
      </time>
      <h3 class="post-card__title"><?php echo esc_html(get_the_title()); ?></h3>
      <div class="post-card__footer">
        <span class="post-card__read-btn">Читать</span>
        <span class="post-card__stat">
          <?php echo $comment_count; ?>
          <span aria-hidden="true"><?php echo $comment_count > 50 ? '🔥' : '💬'; ?></span>
        </span>
      </div>
    </div>

  </a>
</article>
