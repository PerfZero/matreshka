<footer class="site-footer">
  <div class="site-footer__inner">

    <!-- Top: brand + logos -->
    <div class="site-footer__top">
      <p class="site-footer__brand">Медиапроект: "Масс Медиа"</p>
      <div class="site-footer__logos">
        <a href="https://масс-медиа.рус/курс-журналистика" target="_blank" rel="noopener" class="site-footer__logo-link">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/images/logo-course.svg'); ?>" alt="Курс юного журналиста" height="40" loading="lazy">
        </a>
        <a href="https://масс-медиа.рус" target="_blank" rel="noopener" class="site-footer__logo-link">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/images/logo-mass-media.png'); ?>" alt="Масс Медиа" height="40" loading="lazy">
        </a>
        <a href="https://movie-projector.ru/" target="_blank" rel="noopener" class="site-footer__logo-link">
          <img src="<?php echo esc_url(get_template_directory_uri() . '/images/logo-movie-projector.svg'); ?>" alt="Кинопроект Время перемен" height="40" loading="lazy">
        </a>
        <a href="https://matrezhka.ru" target="_blank" rel="noopener" class="site-footer__logo-link site-footer__logo-link--matrezhka">
          <span class="site-footer__matrezhka-text">Матрё<img src="<?php echo esc_url(get_template_directory_uri() . '/images/logo-matrezhka.png'); ?>" alt="" height="30" loading="lazy">ка</span>
        </a>
      </div>
    </div>

    <!-- Main columns -->
    <div class="site-footer__main">

      <!-- О проекте -->
      <nav class="site-footer__col" aria-label="О проекте">
        <p class="site-footer__col-title">О ПРОЕКТЕ:</p>
        <ul class="site-footer__nav">
          <li><a href="https://matrezhka.ru/news" target="_blank" rel="noopener">Главные новости</a></li>
          <li><a href="https://matrezhka.ru/about" target="_blank" rel="noopener">О медиапроекте</a></li>
          <li><a href="https://matrezhka.ru/our-team" target="_blank" rel="noopener">Наша команда</a></li>
          <li><a href="#">Вакансии</a></li>
          <li><a href="https://масс-медиа.рус/россия/курс-журналистика" target="_blank" rel="noopener">Стать автором</a></li>
          <li><a href="https://matrezhka.ru/publication-rules" target="_blank" rel="noopener">Правила</a></li>
          <li><a href="https://matrezhka.ru/contest" target="_blank" rel="noopener">Конкурсы</a></li>
          <li><a href="https://matrezhka.ru/competition-regulations" target="_blank" rel="noopener">Положение</a></li>
        </ul>
      </nav>

      <!-- Рубрики -->
      <nav class="site-footer__col" aria-label="Рубрики">
        <p class="site-footer__col-title">РУБРИКИ:</p>
        <ul class="site-footer__nav">
          <?php
          $footer_cats = get_categories(array('hide_empty' => false, 'orderby' => 'name', 'order' => 'ASC'));
          $default_slugs = array('novosti-dvora','intervyu','shkola','kino-i-igry','sport','tvorchestvo','puteshestviya','hobbi');
          $shown = 0;
          if (!empty($footer_cats)) :
              foreach ($footer_cats as $fcat) :
                  if ('uncategorized' === $fcat->slug) continue;
                  if ($shown >= 8) break;
                  $shown++;
          ?>
            <li><a href="<?php echo esc_url(get_category_link($fcat->term_id)); ?>"><?php echo esc_html($fcat->name); ?></a></li>
          <?php endforeach; endif; ?>
        </ul>
      </nav>

      <!-- CTA + Social -->
      <div class="site-footer__col site-footer__col--center">
        <a href="https://масс-медиа.рус/россия/курс-журналистика" target="_blank" rel="noopener" class="site-footer__cta">
          ЗАЯВИТЬ<br>О СЕБЕ
        </a>
        <p class="site-footer__social-label">Подпишись на наши каналы:</p>
        <div class="site-footer__social">
          <a href="https://t.me/media_matrezhka" target="_blank" rel="noopener" class="site-footer__social-btn" aria-label="Telegram">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/images/icon-telegram.svg'); ?>" alt="Telegram" width="22" height="22">
          </a>
          <a href="https://max.ru/join/U9d79cMac9NGmQG-1_99R49fYOn9aM19B5lhgXhi-ZI" target="_blank" rel="noopener" class="site-footer__social-btn" aria-label="MAX">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/images/icon-max.svg'); ?>" alt="MAX" width="46" height="46">
          </a>
          <a href="https://vk.com/mass_media_group" target="_blank" rel="noopener" class="site-footer__social-btn" aria-label="ВКонтакте">
            <img src="<?php echo esc_url(get_template_directory_uri() . '/images/icon-vk.svg'); ?>" alt="ВКонтакте" width="22" height="22">
          </a>
        </div>
      </div>

      <!-- Редакция -->
      <div class="site-footer__col">
        <p class="site-footer__col-title">РЕДАКЦИЯ:</p>
        <address class="site-footer__address">
          109147, г. Москва, вн.тер.г.<br>
          Муниципальный Округ Таганский,<br>
          ул. Воронцовская, д. 35Б, к. 2,<br>
          помещ. 7/2/4
        </address>
        <a href="mailto:news@matrezhka.ru" class="site-footer__email">news@matrezhka.ru</a>
        <ul class="site-footer__nav site-footer__nav--legal">
          <li><a href="https://matrezhka.ru/advertisers" target="_blank" rel="noopener">Рекламодателям</a></li>
          <li><a href="https://matrezhka.ru/parents-consent" target="_blank" rel="noopener">Оферта</a></li>
        </ul>
      </div>

    </div><!-- /.site-footer__main -->

    <!-- Bottom bar -->
    <div class="site-footer__bottom">
      <a href="https://matrezhka.ru/concent" target="_blank" rel="noopener">Положение об обработке персональных данных</a>
      <span>Продюсерский центр "Масс Медиа" <?php echo date('Y'); ?></span>
      <a href="https://matrezhka.ru/privacy-policy" target="_blank" rel="noopener">Политика конфиденциальности</a>
    </div>

  </div><!-- /.site-footer__inner -->
</footer>

<?php wp_footer(); ?>
</body>
</html>
