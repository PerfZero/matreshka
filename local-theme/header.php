<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo('charset'); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<?php
$rubric_categories = function_exists('local_theme_get_sorted_rubric_categories')
    ? local_theme_get_sorted_rubric_categories()
    : array();
?>
<header class="site-header">
  <div class="site-header__inner">
    <div class="brand">
      <?php if (has_custom_logo()) : ?>
        <?php echo get_custom_logo(); ?>
      <?php else : ?>
        <a class="brand__fallback" href="<?php echo esc_url(home_url('/')); ?>">
          <span class="brand__text">МатрёЖка</span>
        </a>
      <?php endif; ?>
    </div>

    <nav class="main-nav" aria-label="Главное меню">
      <a href="#" class="main-nav__link">Главная</a>
      <a href="#" class="main-nav__link">Новости</a>
      <a href="#" class="main-nav__link">Топ-5</a>
      <div class="ms-menu__item ms-menu__item-3 js-rubrics-menu">
        <button
          class="main-nav__link main-nav__link--dropdown js-rubrics-toggle"
          type="button"
          aria-expanded="false"
          aria-haspopup="true"
          aria-controls="rubrics-submenu"
        >
          Рубрики
          <span class="main-nav__item-arrow" aria-hidden="true"></span>
        </button>
        <div id="rubrics-submenu" class="ms-menu__item-substrate" role="menu" aria-hidden="true">
          <div class="ms-column__item ms-column__item-0">
            <div class="ms-column__item--submenu">
              <?php foreach ($rubric_categories as $index => $category) : ?>
                <?php
                $category_link = get_category_link($category->term_id);
                $category_icon = function_exists('local_theme_get_category_icon_url')
                    ? local_theme_get_category_icon_url((int) $category->term_id, 'thumbnail')
                    : '';
                ?>
                <a href="<?php echo esc_url($category_link); ?>" class="ms-submenu__item-link" role="menuitem">
                  <div class="ms-submenu__item ms-submenu__item-<?php echo (int) $index; ?>">
                    <div class="ms-submenu__item-content">
                      <div class="ms-submenu__item-image">
                        <?php if ($category_icon) : ?>
                          <img src="<?php echo esc_url($category_icon); ?>" alt="">
                        <?php else : ?>
                          <span class="ms-submenu__item-image-fallback">•</span>
                        <?php endif; ?>
                      </div>
                      <div class="ms-submenu__item-title">
                        <span class="ms-active-string"><?php echo esc_html($category->name); ?></span>
                      </div>
                    </div>
                  </div>
                </a>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
      <a href="#" class="main-nav__link">Авторы</a>
    </nav>

    <a class="login-btn" href="#">
      <span class="login-btn__img" aria-hidden="true"></span>
      <span class="login-btn__text">
        <span class="login-btn__name">ВОЙТИ</span>
      </span>
    </a>
  </div>
</header>
