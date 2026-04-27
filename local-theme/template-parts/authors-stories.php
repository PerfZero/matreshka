<?php
/**
 * Authors stories section.
 *
 * @var array<string, mixed> $args
 */

$story_authors = function_exists('local_theme_get_story_authors')
    ? local_theme_get_story_authors(20)
    : array();

$current_author_id = isset($args['current_author_id']) ? (int) $args['current_author_id'] : 0;
?>
<section class="authors-stories" aria-labelledby="authors-stories-title">
  <div class="authors-stories__head">
    <h2 id="authors-stories-title" class="authors-stories__title">Истории авторов</h2>
    <a class="authors-stories__all-link" href="<?php echo esc_url(home_url('/authors/')); ?>">Смотреть всех авторов</a>
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
      <?php if (!empty($story_authors)) : ?>
        <?php foreach ($story_authors as $story_author) : ?>
          <?php
          $story_author_id = (int) $story_author->ID;
          $author_name = function_exists('local_theme_get_author_public_name')
              ? local_theme_get_author_public_name($story_author_id)
              : (string) $story_author->display_name;

          $author_avatar_url = function_exists('local_theme_get_author_avatar_url')
              ? local_theme_get_author_avatar_url($story_author_id, 160)
              : '';

          $author_link = get_author_posts_url($story_author_id, $story_author->user_nicename);
          $first_letter = function_exists('mb_substr')
              ? mb_strtoupper(mb_substr($author_name, 0, 1))
              : strtoupper(substr($author_name, 0, 1));
          ?>
          <article class="author-story" role="listitem">
            <a class="author-story__link<?php echo $current_author_id === $story_author_id ? ' author-story__link--active' : ''; ?>" href="<?php echo esc_url($author_link); ?>">
              <span class="author-story__avatar-ring">
                <?php if ($author_avatar_url) : ?>
                  <img class="author-story__avatar" src="<?php echo esc_url($author_avatar_url); ?>" alt="<?php echo esc_attr($author_name); ?>">
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
