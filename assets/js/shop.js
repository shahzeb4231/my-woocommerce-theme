(function () {
  /* ---------- Search overlay ---------- */
  const ov = document.getElementById('search-overlay');
  if (ov) {
    const input = ov.querySelector('input[type="search"]');
    const open = function () {
      ov.hidden = false;
      document.body.style.overflow = 'hidden';
      setTimeout(function () { if (input) input.focus(); }, 60);
    };
    const close = function () {
      ov.hidden = true;
      document.body.style.overflow = '';
    };
    document.querySelectorAll('[data-search-open]').forEach((b) => b.addEventListener('click', open));
    ov.querySelectorAll('[data-search-close]').forEach((b) => b.addEventListener('click', close));
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !ov.hidden) close();
    });
  }

  /* ---------- Quantity stepper (+ / -) ---------- */
  const makeBtn = function (txt, dir, input) {
    const b = document.createElement('button');
    b.type = 'button';
    b.className = 'qty-btn';
    b.textContent = txt;
    b.setAttribute('aria-label', dir > 0 ? 'Increase quantity' : 'Decrease quantity');
    b.addEventListener('click', function () {
      const step = parseFloat(input.step) || 1;
      const min = input.min !== '' ? parseFloat(input.min) : 1;
      const max = input.max !== '' ? parseFloat(input.max) : Infinity;
      let v = (parseFloat(input.value) || min) + dir * step;
      v = Math.max(min, Math.min(max, v));
      input.value = v;
      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });
    return b;
  };

  const enhance = function () {
    document.querySelectorAll('.quantity:not(.xsn-qty)').forEach(function (q) {
      const input = q.querySelector('input.qty');
      if (!input || input.type === 'hidden') return;
      q.classList.add('xsn-qty');
      q.insertBefore(makeBtn('\u2212', -1, input), input);
      q.appendChild(makeBtn('+', 1, input));
    });
  };
  enhance();
  if (window.jQuery) {
    window.jQuery(document.body).on('updated_wc_div updated_cart_totals', enhance);
  }

  /* ---------- Horizontal product scroller ---------- */
  document.querySelectorAll('[data-scroller]').forEach(function (track) {
    const sec = track.closest('section');
    const move = (dir) => track.scrollBy({ left: dir * track.clientWidth * 0.8, behavior: 'smooth' });
    const prev = sec.querySelector('[data-scroll-prev]');
    const next = sec.querySelector('[data-scroll-next]');
    if (prev) prev.addEventListener('click', () => move(-1));
    if (next) next.addEventListener('click', () => move(1));
  });
})();
