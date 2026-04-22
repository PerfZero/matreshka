document.addEventListener('DOMContentLoaded', () => {
  const rubricsMenu = document.querySelector('.js-rubrics-menu');
  const rubricsToggle = document.querySelector('.js-rubrics-toggle');
  const rubricsSubmenu = document.getElementById('rubrics-submenu');
  if (!rubricsMenu || !rubricsToggle || !rubricsSubmenu) return;

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
});
