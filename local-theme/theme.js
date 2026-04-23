document.addEventListener('DOMContentLoaded', () => {
  const rubricsMenu = document.querySelector('.js-rubrics-menu');
  const rubricsToggle = document.querySelector('.js-rubrics-toggle');
  const rubricsSubmenu = document.getElementById('rubrics-submenu');
  if (rubricsMenu && rubricsToggle && rubricsSubmenu) {
    const openMenu = () => {
      rubricsMenu.classList.add('ms-menu__item--active');
      rubricsToggle.classList.add('is-active');
      rubricsToggle.setAttribute('aria-expanded', 'true');
      rubricsSubmenu.setAttribute('aria-hidden', 'false');
    };

    const closeMenu = () => {
      rubricsMenu.classList.remove('ms-menu__item--active');
      rubricsToggle.classList.remove('is-active');
      rubricsToggle.setAttribute('aria-expanded', 'false');
      rubricsSubmenu.setAttribute('aria-hidden', 'true');
    };

    rubricsToggle.addEventListener('click', (event) => {
      event.stopPropagation();
      if (rubricsMenu.classList.contains('ms-menu__item--active')) {
        closeMenu();
      } else {
        openMenu();
      }
    });

    document.addEventListener('click', (event) => {
      if (!rubricsMenu.contains(event.target)) {
        closeMenu();
      }
    });

    rubricsSubmenu.addEventListener('click', (event) => {
      if (event.target.closest('a')) {
        closeMenu();
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape') {
        closeMenu();
      }
    });
  }

  const citySelect = document.querySelector('.js-city-select');
  if (!citySelect) return;

  const toggle      = citySelect.querySelector('.js-city-select-toggle');
  const panel       = citySelect.querySelector('.js-city-select-panel');
  const input       = citySelect.querySelector('.js-city-select-input');
  const label       = citySelect.querySelector('.js-city-select-label');
  const resetBtn    = citySelect.querySelector('.js-city-select-reset');
  const options     = Array.from(citySelect.querySelectorAll('.js-city-select-option'));
  const storageKey  = 'matre_selected_city';

  if (!toggle || !panel || !input || !label || !options.length) return;

  const closePanel = () => {
    panel.setAttribute('aria-hidden', 'true');
    toggle.setAttribute('aria-expanded', 'false');
  };

  const openPanel = () => {
    panel.setAttribute('aria-hidden', 'false');
    toggle.setAttribute('aria-expanded', 'true');
  };

  const persistSelectedCity = (slug, name) => {
    if (!slug || !name) {
      localStorage.removeItem(storageKey);
      return;
    }
    localStorage.setItem(storageKey, JSON.stringify({ slug, name }));
  };

  const setSelectedCity = (option) => {
    const slug = option.dataset.slug || '';
    const name = option.dataset.name || '';

    label.textContent = name || 'Ваш город';
    options.forEach((node) => node.classList.toggle('is-selected', node === option));
    persistSelectedCity(slug, name);
    if (resetBtn) resetBtn.hidden = !slug;

    const currentUrl = new URL(window.location.href);
    if (slug) {
      currentUrl.searchParams.set('city', slug);
    } else {
      currentUrl.searchParams.delete('city');
    }
    window.history.replaceState({}, '', currentUrl);
  };

  const resetCity = () => {
    label.textContent = 'Ваш город';
    options.forEach((node) => node.classList.remove('is-selected'));
    localStorage.removeItem(storageKey);
    if (resetBtn) resetBtn.hidden = true;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.delete('city');
    window.history.replaceState({}, '', currentUrl);
    closePanel();
  };

  if (resetBtn) {
    resetBtn.addEventListener('click', (event) => {
      event.stopPropagation();
      resetCity();
    });
  }

  const restoreSelectedCity = () => {
    const selectedFromUrl = new URLSearchParams(window.location.search).get('city');
    if (selectedFromUrl) {
      const matchedByUrl = options.find((option) => option.dataset.slug === selectedFromUrl);
      if (matchedByUrl) {
        setSelectedCity(matchedByUrl);
        return;
      }
    }

    const raw = localStorage.getItem(storageKey);
    if (!raw) return;

    try {
      const saved = JSON.parse(raw);
      const matchedSaved = options.find((option) => option.dataset.slug === saved.slug);
      if (matchedSaved) {
        setSelectedCity(matchedSaved);
      }
    } catch (error) {
      localStorage.removeItem(storageKey);
    }
  };

  toggle.addEventListener('click', (event) => {
    event.stopPropagation();
    if (panel.getAttribute('aria-hidden') === 'false') {
      closePanel();
    } else {
      openPanel();
    }
  });

  document.addEventListener('click', (event) => {
    if (!citySelect.contains(event.target)) {
      closePanel();
    }
  });

  options.forEach((option) => {
    option.addEventListener('click', () => {
      setSelectedCity(option);
      closePanel();
    });
  });

  input.addEventListener('input', () => {
    const query = input.value.trim().toLowerCase();
    options.forEach((option) => {
      const name = (option.dataset.name || option.textContent || '').toLowerCase();
      const matched = !query || name.includes(query);
      option.parentElement.style.display = matched ? '' : 'none';
    });
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closePanel();
    }
  });

  restoreSelectedCity();
});

document.addEventListener('DOMContentLoaded', () => {
  const pillsList   = document.querySelector('.js-rubrics-pills-list');
  const grid        = document.querySelector('.js-posts-masonry-grid');
  const sentinel    = document.querySelector('.js-posts-sentinel');
  const sectionTitle = document.querySelector('.posts-masonry__title');
  const sectionDesc  = document.querySelector('.posts-masonry__desc');

  if (!grid || typeof localThemeAjax === 'undefined') return;

  const defaultTitle = sectionTitle ? sectionTitle.textContent : '';
  const defaultDesc  = sectionDesc  ? sectionDesc.textContent  : '';

  let msnry          = null;
  let currentPage    = 1;
  let currentCatId   = '0';
  let isLoading      = false;
  let hasMore        = parseInt(grid.dataset.totalPages || '1', 10) > 1;

  function initMasonry() {
    if (msnry) msnry.destroy();
    msnry = new Masonry(grid, {
      itemSelector: '.post-card',
      columnWidth: '.post-card',
      percentPosition: true,
      gutter: 20,
    });
  }

  imagesLoaded(grid, initMasonry);

  // ── Infinite scroll ───────────────────────────────
  function setSentinel(visible) {
    if (sentinel) sentinel.hidden = !visible;
  }

  setSentinel(hasMore);

  function fetchPage(page, catId, append) {
    if (isLoading) return;
    isLoading = true;

    if (!append) {
      grid.classList.add('is-loading');
      grid.setAttribute('aria-busy', 'true');
    } else {
      setSentinel(true);
    }

    const body = new FormData();
    body.append('action', 'local_theme_filter_posts');
    body.append('nonce', localThemeAjax.nonce);
    body.append('category_id', catId);
    body.append('page', page);

    fetch(localThemeAjax.ajaxUrl, { method: 'POST', body })
      .then((r) => r.json())
      .then((data) => {
        if (!data.success) return;

        hasMore = !!data.data.has_more;

        if (append) {
          const prevCount = grid.querySelectorAll('.post-card').length;
          grid.insertAdjacentHTML('beforeend', data.data.html);
          const newCards = Array.from(grid.querySelectorAll('.post-card')).slice(prevCount);
          imagesLoaded(newCards, () => {
            msnry.appended(newCards);
            setSentinel(hasMore);
            isLoading = false;
          });
        } else {
          grid.innerHTML = data.data.html;
          imagesLoaded(grid, () => {
            initMasonry();
            grid.classList.remove('is-loading');
            grid.setAttribute('aria-busy', 'false');
            setSentinel(hasMore);
            isLoading = false;
          });
        }
      })
      .catch(() => {
        grid.classList.remove('is-loading');
        grid.setAttribute('aria-busy', 'false');
        setSentinel(false);
        isLoading = false;
      });
  }

  if (sentinel) {
    const observer = new IntersectionObserver((entries) => {
      if (entries[0].isIntersecting && hasMore && !isLoading) {
        currentPage++;
        fetchPage(currentPage, currentCatId, true);
      }
    }, { rootMargin: '300px' });
    observer.observe(sentinel);
  }

  // ── Rubric filter ─────────────────────────────────
  if (!pillsList) return;

  pillsList.addEventListener('click', (event) => {
    const pill = event.target.closest('.js-rubric-pill');
    if (!pill) return;

    pillsList.querySelectorAll('.js-rubric-pill').forEach((p) => {
      p.classList.remove('city-rubrics__pill--active');
    });
    pill.classList.add('city-rubrics__pill--active');

    currentCatId = pill.dataset.categoryId || '0';
    currentPage  = 1;
    hasMore      = true;

    if (sectionTitle) {
      sectionTitle.textContent = currentCatId === '0'
        ? defaultTitle
        : (pill.dataset.sectionTitle || defaultTitle);
    }
    if (sectionDesc) {
      const newDesc = currentCatId === '0'
        ? defaultDesc
        : (pill.dataset.sectionDesc || '');
      sectionDesc.textContent = newDesc;
      sectionDesc.hidden = newDesc === '';
    }

    fetchPage(1, currentCatId, false);
  });
});
