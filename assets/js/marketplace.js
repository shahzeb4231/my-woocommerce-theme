(function () {
  const cfg = window.XSN || {};
  const wishSet = new Set((cfg.wish || []).map(Number));

  const paint = function () {
    document.querySelectorAll('.wish[data-id]').forEach(function (b) {
      b.classList.toggle('is-on', wishSet.has(Number(b.dataset.id)));
    });
  };
  paint();

  const toast = function (msg, href) {
    const old = document.querySelector('.xsn-toast');
    if (old) old.remove();
    const t = document.createElement('div');
    t.className = 'xsn-toast';
    t.innerHTML = '<span></span>' + (href ? ' <a href="' + href + '">Sign in</a>' : '');
    t.firstChild.textContent = msg;
    document.body.appendChild(t);
    setTimeout(function () { t.classList.add('is-in'); }, 20);
    setTimeout(function () { t.classList.remove('is-in'); setTimeout(() => t.remove(), 300); }, 3800);
  };

  /* ---------- Category tabs (homepage marketplace) ---------- */
  const grid = document.querySelector('[data-products]');
  if (grid) {
    const tabs = document.querySelectorAll('.filter-tabs .tab');
    const cards = grid.querySelectorAll('.product-card');
    tabs.forEach(function (t) {
      t.addEventListener('click', function () {
        const c = t.dataset.filter;
        tabs.forEach((x) => x.classList.toggle('is-active', x === t));
        cards.forEach(function (card) {
          card.hidden = !(c === 'all' || card.dataset.cat === c);
        });
      });
    });
  }

  document.addEventListener('click', function (e) {
    /* ---------- Wishlist heart ---------- */
    const wish = e.target.closest('.wish');
    if (wish) {
      const id = Number(wish.dataset.id);
      if (!id) { wish.classList.toggle('is-on'); return; }
      if (!cfg.logged) { toast('Please sign in to save favourites.', cfg.login); return; }
      wish.disabled = true;
      fetch(cfg.ajax, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        credentials: 'same-origin',
        body: new URLSearchParams({ action: 'xsn_wish', nonce: cfg.nonce, id: id })
      })
        .then((r) => r.json())
        .then(function (res) {
          if (res && res.success) {
            if (res.data.on) wishSet.add(id); else wishSet.delete(id);
            paint();
            toast(res.data.on ? 'Saved to your wishlist.' : 'Removed from your wishlist.');
          }
        })
        .catch(function () { toast('Could not update your wishlist.'); })
        .finally(function () { wish.disabled = false; });
      return;
    }

    /* ---------- Demo "Add" buttons (no WooCommerce product) ---------- */
    const demo = e.target.closest('[data-demo-add]');
    if (demo) {
      const txt = demo.querySelector('.add-txt');
      demo.classList.add('is-added');
      if (txt) txt.textContent = 'Added';
      setTimeout(function () {
        demo.classList.remove('is-added');
        if (txt) txt.textContent = 'Add';
      }, 1400);
    }
  });
})();
