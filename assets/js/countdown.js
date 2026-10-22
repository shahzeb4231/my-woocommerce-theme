(function () {
  const el = document.querySelector('[data-countdown]');
  if (!el) return;

  const end = parseInt(el.dataset.countdown, 10) * 1000;
  const q = (s) => el.querySelector(s);
  const pad = (n) => String(n).padStart(2, '0');
  let timer;

  const tick = function () {
    const left = end - Date.now();
    if (left <= 0) {
      clearInterval(timer);
      el.textContent = 'This sale has ended';
      el.classList.add('is-ended');
      return;
    }
    const s = Math.floor(left / 1000);
    q('[data-d]').textContent = pad(Math.floor(s / 86400));
    q('[data-h]').textContent = pad(Math.floor((s % 86400) / 3600));
    q('[data-m]').textContent = pad(Math.floor((s % 3600) / 60));
    q('[data-s]').textContent = pad(s % 60);
  };

  tick();
  timer = setInterval(tick, 1000);
})();
