<?php
/**
 * Homepage block: popular rubrics + city selector.
 */

$popular_categories = function_exists('local_theme_get_sorted_rubric_categories')
    ? local_theme_get_sorted_rubric_categories()
    : array();

$city_terms = function_exists('local_theme_get_city_terms')
    ? local_theme_get_city_terms()
    : array();

$selected_city_slug = isset($_GET['city']) ? sanitize_title(wp_unslash($_GET['city'])) : '';
$selected_city_label = 'Ваш город';

if ('' !== $selected_city_slug && !empty($city_terms)) {
    foreach ($city_terms as $city_term) {
        if ($selected_city_slug === (string) $city_term->slug) {
            $selected_city_label = (string) $city_term->name;
            break;
        }
    }
}
?>
<section class="city-rubrics" aria-labelledby="city-rubrics-title">

  <div class="city-rubrics__top">
    <aside class="city-select js-city-select" aria-label="Выбор города">
      <button
        type="button"
        class="city-select__toggle js-city-select-toggle"
        aria-expanded="false"
        aria-controls="city-select-panel"
      >
        <span class="city-select__label js-city-select-label"><?php echo esc_html($selected_city_label); ?></span>
        <span class="city-select__pin" aria-hidden="true"></span>
      </button>

      <div id="city-select-panel" class="city-select__panel js-city-select-panel" aria-hidden="true">
        <label class="city-select__search" for="city-select-search">
          <span class="city-select__search-icon" aria-hidden="true"></span>
          <input
            id="city-select-search"
            class="city-select__search-input js-city-select-input"
            type="search"
            placeholder="Выбор города..."
            autocomplete="off"
          >
        </label>

        <div class="city-select__scroll">
          <?php if (!empty($city_terms)) : ?>
            <ul class="city-select__list js-city-select-list">
              <?php foreach ($city_terms as $city_term) : ?>
                <li>
                  <button
                    type="button"
                    class="city-select__option js-city-select-option"
                    data-slug="<?php echo esc_attr((string) $city_term->slug); ?>"
                    data-name="<?php echo esc_attr((string) $city_term->name); ?>"
                  >
                    <?php echo esc_html((string) $city_term->name); ?>
                  </button>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else : ?>
            <p class="city-select__empty">Города пока не загружены.</p>
          <?php endif; ?>
        </div>
      </div>
    </aside>
  </div>

  <div class="city-rubrics__heading">
    <h2 id="city-rubrics-title" class="city-rubrics__title">Популярные рубрики:</h2>
    <button type="button" class="city-rubrics__scroll-prev js-rubrics-prev" aria-label="Прокрутить влево">&#9664;</button>
  </div>

  <div class="city-rubrics__card">
    <div class="city-rubrics__list" role="list" aria-label="Популярные рубрики">
      <?php if (!empty($popular_categories)) : ?>
        <?php foreach ($popular_categories as $index => $category) : ?>
          <a
            class="city-rubrics__pill<?php echo 0 === $index ? ' city-rubrics__pill--active' : ''; ?>"
            href="<?php echo esc_url(get_category_link($category->term_id)); ?>"
            role="listitem"
          >
            <?php echo esc_html($category->name); ?>
          </a>
        <?php endforeach; ?>
      <?php else : ?>
        <p class="city-rubrics__empty">Рубрики пока не добавлены.</p>
      <?php endif; ?>
    </div>
  </div>

</section>
