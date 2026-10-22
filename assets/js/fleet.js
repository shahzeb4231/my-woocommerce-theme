(function () {
  const root = document.querySelector('[data-fleet]');
  if (!root) return;
  const slides = Array.from(root.querySelectorAll('.fleet-slide'));
  const dots = Array.from(root.querySelectorAll('.fleet-dot'));
  let i = 0, timer;

  const go = function (n) {
    i = (n + slides.length) % slides.length;
    slides.forEach((s, k) => s.classList.toggle('is-active', k === i));
    dots.forEach((d, k) => d.classList.toggle('is-active', k === i));
  };
  const start = () => { timer = setInterval(() => go(i + 1), 6000); };
  const stop = () => clearInterval(timer);

  root.querySelector('[data-prev]').addEventListener('click', () => go(i - 1));
  root.querySelector('[data-next]').addEventListener('click', () => go(i + 1));
  dots.forEach((d, k) => d.addEventListener('click', () => go(k)));
  root.addEventListener('mouseenter', stop);
  root.addEventListener('mouseleave', start);
  start();
})();
