(function () {
  const form = document.querySelector('[data-track]');
  if (!form) return;
  const input = form.querySelector('input');
  const items = Array.from(document.querySelectorAll('.tl-item'));
  const idOut = document.querySelector('[data-track-id]');
  const eta = document.querySelector('[data-eta]');
  const note = document.querySelector('[data-track-note]');
  let timers = [];

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const v = input.value.trim();
    if (!v) {
      note.textContent = 'Please enter a tracking number first.';
      input.focus();
      return;
    }
    note.textContent = 'Demo status shown. Live tracking will connect to real orders.';
    idOut.textContent = v.toUpperCase();
    timers.forEach(clearTimeout);
    timers = [];
    items.forEach((i) => i.classList.remove('is-done', 'is-active'));
    items[0].classList.add('is-active');
    [0, 1, 2].forEach(function (k) {
      timers.push(setTimeout(function () {
        items[k].classList.remove('is-active');
        items[k].classList.add('is-done');
        items[k + 1].classList.add('is-active');
        eta.textContent = [ '12 min', '6 min', '2 min' ][k];
      }, (k + 1) * 700));
    });
  });
})();
