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

  const toggle = citySelect.querySelector('.js-city-select-toggle');
  const panel = citySelect.querySelector('.js-city-select-panel');
  const input = citySelect.querySelector('.js-city-select-input');
  const label = citySelect.querySelector('.js-city-select-label');
  const options = Array.from(citySelect.querySelectorAll('.js-city-select-option'));
  const storageKey = 'matre_selected_city';

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

    const currentUrl = new URL(window.location.href);
    if (slug) {
      currentUrl.searchParams.set('city', slug);
    } else {
      currentUrl.searchParams.delete('city');
    }
    window.history.replaceState({}, '', currentUrl);
  };

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
