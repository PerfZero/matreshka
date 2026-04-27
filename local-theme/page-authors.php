<?php
/**
 * Template Name: Все авторы
 */
get_header();

$authors = function_exists('local_theme_get_story_authors')
    ? local_theme_get_story_authors(100)
    : array();
?>
<main class="page-content">

  <?php get_template_part('template-parts/authors-stories'); ?>

  <section class="all-authors">
    <?php foreach ($authors as $author) :
        $author_id    = (int) $author->ID;
        $name         = function_exists('local_theme_get_author_public_name')
            ? local_theme_get_author_public_name($author_id)
            : (string) $author->display_name;
        $age          = (string) get_user_meta($author_id, 'local_theme_author_age', true);
        $city         = (string) get_user_meta($author_id, 'local_theme_author_city', true);
        $status       = (string) get_user_meta($author_id, 'local_theme_author_status', true);
        $hobbies      = (string) get_user_meta($author_id, 'local_theme_author_hobbies', true);
        $contact_link = (string) get_user_meta($author_id, 'local_theme_author_contact_link', true);
        if ('' === $contact_link) {
            $contact_link = 'mailto:' . antispambot((string) $author->user_email);
        }
        $avatar_url   = function_exists('local_theme_get_author_avatar_url')
            ? local_theme_get_author_avatar_url($author_id, 200)
            : get_avatar_url($author_id, array('size' => 200));

        $author_posts = get_posts(array(
            'author'         => $author_id,
            'post_status'    => 'publish',
            'posts_per_page' => 5,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));

        $heading_parts = array('Автор статей:', $name);
        if ('' !== $age)  { $heading_parts[] = $age . ' лет'; }
        if ('' !== $city) { $heading_parts[] = 'г. ' . $city; }
        $heading = implode(' ', $heading_parts);
    ?>
      <div class="author-row">

        <div class="author-row__card">
          <div class="author-row__avatar-wrap">
            <?php if ($avatar_url) : ?>
              <img
                class="author-row__avatar"
                src="<?php echo esc_url($avatar_url); ?>"
                alt="<?php echo esc_attr($name); ?>"
                width="120"
                height="120"
              >
            <?php else : ?>
              <span class="author-row__avatar-fallback">
                <?php echo esc_html(mb_strtoupper(mb_substr($name, 0, 1))); ?>
              </span>
            <?php endif; ?>
          </div>

          <?php if ('' !== $status) : ?>
            <p class="author-row__status">Статус: <?php echo esc_html($status); ?></p>
          <?php endif; ?>

          <?php if ('' !== $hobbies) : ?>
            <p class="author-row__hobbies-label">Увлечения:</p>
            <p class="author-row__hobbies"><?php echo esc_html($hobbies); ?></p>
          <?php endif; ?>

          <div class="author-row__card-deco" aria-hidden="true"></div>
        </div>

        <div class="author-row__main">
          <h2 class="author-row__heading"><?php echo esc_html($heading); ?></h2>

          <div class="author-row__posts-card">
            <?php if (!empty($author_posts)) : ?>
              <ul class="author-row__posts">
                <?php foreach ($author_posts as $apost) : ?>
                  <li class="author-row__post-item">
                    <a href="<?php echo esc_url(get_permalink($apost->ID)); ?>" class="author-row__post-link">
                      "<?php echo esc_html(get_the_title($apost->ID)); ?>"
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else : ?>
              <p class="author-row__posts-empty">Публикаций пока нет.</p>
            <?php endif; ?>

            <a href="<?php echo esc_url($contact_link); ?>" class="author-row__contact-btn">
              <span class="author-row__contact-plus" aria-hidden="true">+</span>
              Написать автору
            </a>
          </div>

          <div class="author-row__deco" aria-hidden="true"></div>
        </div>

      </div>
    <?php endforeach; ?>

    <?php if (empty($authors)) : ?>
      <p class="all-authors__empty">Авторы пока не добавлены.</p>
    <?php endif; ?>
  </section>

</main>
<?php get_footer(); ?>
