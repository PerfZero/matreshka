<?php
/**
 * Basic fallback template for local edits.
 */
get_header();

$author_users = get_users(array(
    'role__in' => array('matre_author', 'author'),
    'orderby' => 'display_name',
    'order' => 'ASC',
    'number' => 20,
));
?>
<main class="page-content">
  <section class="authors-stories" aria-labelledby="authors-stories-title">
    <div class="authors-stories__head">
      <h2 id="authors-stories-title" class="authors-stories__title">Истории авторов</h2>
      <a class="authors-stories__all-link" href="#">Смотреть всех авторов</a>
    </div>

    <div class="authors-stories__card">
      <div class="authors-stories__cta">
        <a href="#" class="authors-cta-btn">
          <span class="authors-cta-btn__circle" aria-hidden="true">+</span>
          <span class="authors-cta-btn__label">Стать автором</span>
        </a>

        <a href="#" class="authors-training-link">
          <span class="authors-training-link__icon" aria-hidden="true">✱</span>
          <span>Пройти обучение</span>
        </a>
      </div>

      <div class="authors-stories__list" role="list" aria-label="Авторы">
        <?php if (!empty($author_users)) : ?>
          <?php foreach ($author_users as $author_user) : ?>
            <?php
            $first_name = trim((string) get_user_meta($author_user->ID, 'first_name', true));
            $last_name = trim((string) get_user_meta($author_user->ID, 'last_name', true));
            $nickname = trim((string) get_user_meta($author_user->ID, 'nickname', true));
            $display_name = trim((string) $author_user->display_name);
            $full_name = trim($first_name . ' ' . $last_name);

            if ('' !== $full_name) {
                $author_name = $full_name;
            } elseif ('' !== $nickname && !is_email($nickname)) {
                $author_name = $nickname;
            } elseif ('' !== $display_name && !is_email($display_name)) {
                $author_name = $display_name;
            } else {
                $author_name = (string) $author_user->user_login;
            }

            $author_link = get_author_posts_url($author_user->ID, $author_user->user_nicename);
            $avatar_data = get_avatar_data($author_user->ID, array(
                'size' => 160,
                'default' => '404',
            ));
            $avatar_url = (!empty($avatar_data['found_avatar']) && !empty($avatar_data['url'])) ? $avatar_data['url'] : '';
            $first_letter = function_exists('mb_substr')
                ? mb_strtoupper(mb_substr($author_name, 0, 1))
                : strtoupper(substr($author_name, 0, 1));
            ?>
            <article class="author-story" role="listitem">
              <a class="author-story__link" href="<?php echo esc_url($author_link); ?>">
                <span class="author-story__avatar-ring">
                  <?php if ($avatar_url) : ?>
                    <img class="author-story__avatar" src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($author_name); ?>">
                  <?php else : ?>
                    <span class="author-story__avatar-fallback" aria-hidden="true"><?php echo esc_html($first_letter); ?></span>
                  <?php endif; ?>
                </span>
                <span class="author-story__name"><?php echo esc_html($author_name); ?></span>
              </a>
            </article>
          <?php endforeach; ?>
        <?php else : ?>
          <div class="authors-stories__empty">Пока нет авторов. Добавьте пользователей с ролью «Автор».</div>
        <?php endif; ?>
      </div>
    </div>
  </section>
</main>
<?php get_footer(); ?>
