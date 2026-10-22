(function () {
  const btn = document.querySelector('.nav-toggle');
  const menu = document.getElementById('mobile-menu');
  if (!btn || !menu) return;

  const close = () => {
    menu.classList.remove('is-open');
    btn.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  };

  btn.addEventListener('click', () => {
    const open = menu.classList.toggle('is-open');
    btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    document.body.style.overflow = open ? 'hidden' : '';
  });

  menu.querySelectorAll('a').forEach((a) => a.addEventListener('click', close));
  document.addEventListener('keydown', (e) => e.key === 'Escape' && close());
})();