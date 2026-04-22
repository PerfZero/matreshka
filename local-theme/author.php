<?php
/**
 * Author archive template.
 */

get_header();

$author = get_queried_object();
if (!$author instanceof WP_User) {
    $author = wp_get_current_user();
}

$author_id = $author instanceof WP_User ? (int) $author->ID : 0;
$author_name = $author_id > 0 && function_exists('local_theme_get_author_public_name')
    ? local_theme_get_author_public_name($author_id)
    : '';

$author_avatar = $author_id > 0 && function_exists('local_theme_get_author_avatar_url')
    ? local_theme_get_author_avatar_url($author_id, 520)
    : '';

$author_status = $author_id > 0 ? trim((string) get_user_meta($author_id, 'local_theme_author_status', true)) : '';
$author_age = $author_id > 0 ? absint((string) get_user_meta($author_id, 'local_theme_author_age', true)) : 0;
$author_city = $author_id > 0 ? trim((string) get_user_meta($author_id, 'local_theme_author_city', true)) : '';
$author_hobbies = $author_id > 0 ? trim((string) get_user_meta($author_id, 'local_theme_author_hobbies', true)) : '';
$author_contact_link = $author_id > 0 ? esc_url((string) get_user_meta($author_id, 'local_theme_author_contact_link', true)) : '';

if ('' === $author_contact_link && $author instanceof WP_User && !empty($author->user_email)) {
    $author_contact_link = 'mailto:' . antispambot($author->user_email);
}

$title_bits = array();
if ('' !== $author_name) {
    $title_bits[] = $author_name;
}
if ($author_age > 0) {
    $title_bits[] = $author_age . ' лет';
}
if ('' !== $author_city) {
    $title_bits[] = 'г. ' . $author_city;
}

$author_heading = !empty($title_bits)
    ? 'Автор статей: ' . implode(' ', $title_bits)
    : 'Автор статей';

$author_posts = array();
if ($author_id > 0) {
    $author_posts = get_posts(array(
        'post_type' => 'post',
        'post_status' => 'publish',
        'author' => $author_id,
        'posts_per_page' => 10,
        'orderby' => 'date',
        'order' => 'DESC',
    ));
}

$hobbies_rows = preg_split('/\r\n|\r|\n/', $author_hobbies);
$hobbies_rows = is_array($hobbies_rows) ? array_values(array_filter(array_map('trim', $hobbies_rows), static fn($line) => '' !== $line)) : array();
if (empty($hobbies_rows) && '' !== $author_hobbies) {
    $hobbies_rows = array($author_hobbies);
}

$author_fallback_letter = '' !== $author_name
    ? (function_exists('mb_substr') ? mb_strtoupper(mb_substr($author_name, 0, 1)) : strtoupper(substr($author_name, 0, 1)))
    : 'A';
?>
<main class="page-content author-page">
  <?php get_template_part('template-parts/authors-stories', null, array('current_author_id' => $author_id)); ?>

  <section class="author-profile" aria-label="Профиль автора">
    <div class="author-profile__content">
      <aside class="author-profile__card" aria-label="Информация об авторе">
        <div class="author-profile__avatar-wrap">
          <?php if ($author_avatar) : ?>
            <img class="author-profile__avatar" src="<?php echo esc_url($author_avatar); ?>" alt="<?php echo esc_attr($author_name); ?>">
          <?php else : ?>
            <span class="author-profile__avatar-fallback" aria-hidden="true"><?php echo esc_html($author_fallback_letter); ?></span>
          <?php endif; ?>
        </div>

        <?php if ('' !== $author_status) : ?>
          <p class="author-profile__status">Статус: <?php echo esc_html($author_status); ?></p>
        <?php endif; ?>

        <div class="author-profile__meta-block">
          <h3 class="author-profile__meta-title">Увлечения:</h3>
          <?php if (!empty($hobbies_rows)) : ?>
            <ul class="author-profile__meta-list">
              <?php foreach ($hobbies_rows as $hobby) : ?>
                <li><?php echo esc_html($hobby); ?></li>
              <?php endforeach; ?>
            </ul>
          <?php else : ?>
            <p class="author-profile__meta-empty">Информация пока не заполнена.</p>
          <?php endif; ?>
        </div>
      </aside>

      <div class="author-profile__main">
        <h1 class="author-profile__heading"><?php echo esc_html($author_heading); ?></h1>

        <div class="author-profile__posts-card">
          <?php if (!empty($author_posts)) : ?>
            <ul class="author-profile__posts-list">
              <?php foreach ($author_posts as $post_item) : ?>
                <li class="author-profile__post-item">
                  <a href="<?php echo esc_url(get_permalink($post_item)); ?>">"<?php echo esc_html(get_the_title($post_item)); ?>"</a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else : ?>
            <p class="author-profile__posts-empty">У автора пока нет опубликованных статей.</p>
          <?php endif; ?>

          <div class="author-profile__contact-wrap">
            <a href="<?php echo esc_url('' !== $author_contact_link ? $author_contact_link : '#'); ?>" class="author-profile__contact-btn">
              <span class="author-profile__contact-circle" aria-hidden="true">+</span>
              <span class="author-profile__contact-text">Написать автору</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php get_footer(); ?>
