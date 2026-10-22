(function () {
  const cfg = window.XSN || {};
  const wrap = document.querySelector('.auth-wrap');

  /* ---------- "Show password" tick box (never clashes with the browser's own eye icon) ---------- */
  document.querySelectorAll('.auth-form input[type="password"]').forEach(function (input) {
    const label = document.createElement('label');
    label.className = 'pw-show';
    const cb = document.createElement('input');
    cb.type = 'checkbox';
    cb.addEventListener('change', function () { input.type = cb.checked ? 'text' : 'password'; });
    label.appendChild(cb);
    label.appendChild(document.createTextNode(' Show password'));
    input.insertAdjacentElement('afterend', label);
  });

  if (!wrap) return;

  /* ---------- Tabs ---------- */
  const tabs = wrap.querySelectorAll('[data-auth-tab]');
  const panels = wrap.querySelectorAll('[data-auth-panel]');
  const modal = document.getElementById('reg-modal');
  let chosen = null;

  const showTab = function (name) {
    tabs.forEach((t) => t.classList.toggle('is-active', t.dataset.authTab === name));
    panels.forEach((p) => p.classList.toggle('is-active', p.dataset.authPanel === name));
  };

  /* ---------- Account type pop-up ---------- */
  const openModal = function () {
    if (!modal) return;
    modal.hidden = false;
    document.body.style.overflow = 'hidden';
  };
  const closeModal = function () {
    if (!modal) return;
    modal.hidden = true;
    document.body.style.overflow = '';
  };

  const labels = { customer: 'Customer account', vendor: 'Vendor account' };
  const subs = {
    customer: 'Create your customer account in two quick steps.',
    vendor: 'Apply to open your boutique in three steps. We verify every seller.'
  };

  const choose = function (type) {
    chosen = type;
    wrap.querySelectorAll('[data-reg-form]').forEach((f) => { f.hidden = f.dataset.regForm !== type; });
    const chip = wrap.querySelector('[data-change-type]');
    chip.hidden = false;
    chip.querySelector('[data-type-label]').textContent = labels[type];
    wrap.querySelector('[data-reg-sub]').textContent = subs[type];
    hideAlert();
    const inline = wrap.querySelector('[data-inline-choose]');
    if (inline) inline.hidden = true;
    showTab('register');
    closeModal();
  };

  tabs.forEach(function (t) {
    t.addEventListener('click', function () {
      if (t.dataset.authTab === 'register' && !chosen) { showTab('register'); openModal(); return; }
      showTab(t.dataset.authTab);
    });
  });
  document.querySelectorAll('[data-modal-close]').forEach((b) => b.addEventListener('click', closeModal));
  document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && modal && !modal.hidden) closeModal(); });
  document.querySelectorAll('[data-choose]').forEach((b) => b.addEventListener('click', () => choose(b.dataset.choose)));
  wrap.querySelector('[data-change-type]').addEventListener('click', openModal);

  if (wrap.dataset.start === 'register' || window.location.hash === '#register') { showTab('register'); openModal(); }

  /* ---------- Alerts ---------- */
  const alertBox = wrap.querySelector('[data-alert]');
  const hideAlert = function () { alertBox.hidden = true; alertBox.textContent = ''; };
  const showAlert = function (msg) {
    alertBox.textContent = msg;
    alertBox.hidden = false;
    alertBox.scrollIntoView({ behavior: 'smooth', block: 'center' });
  };

  /* ---------- Multi-step forms ---------- */
  wrap.querySelectorAll('[data-reg-form]').forEach(function (form) {
    const steps = Array.from(form.querySelectorAll('.reg-step'));
    const dots = Array.from(form.querySelectorAll('[data-progress] li'));
    const back = form.querySelector('[data-back]');
    const next = form.querySelector('[data-next]');
    const submit = form.querySelector('[data-submit]');
    let cur = 0;

    const go = function (n) {
      cur = Math.max(0, Math.min(steps.length - 1, n));
      steps.forEach((s, i) => { s.hidden = i !== cur; s.classList.toggle('is-current', i === cur); });
      dots.forEach((d, i) => { d.classList.toggle('is-current', i === cur); d.classList.toggle('is-done', i < cur); });
      back.hidden = cur === 0;
      next.hidden = cur === steps.length - 1;
      submit.hidden = cur !== steps.length - 1;
      hideAlert();
    };

    const valid = function () {
      const fields = steps[cur].querySelectorAll('input, select, textarea');
      for (const f of fields) {
        if (!f.checkValidity()) { f.reportValidity(); return false; }
      }
      return true;
    };

    next.addEventListener('click', () => { if (valid()) go(cur + 1); });
    back.addEventListener('click', () => go(cur - 1));

    // Photos: shrink big phone photos before upload, then show a preview
    const MAX_SIDE = 1600;
    const compress = function (file) {
      return new Promise(function (resolve) {
        const ok = /^image\/(jpeg|png|webp)$/.test(file.type) && window.createImageBitmap && window.DataTransfer;
        if (!ok) { resolve(file); return; }
        createImageBitmap(file).then(function (bmp) {
          const scale = Math.min(1, MAX_SIDE / Math.max(bmp.width, bmp.height));
          if (scale === 1 && file.size < 1.5 * 1024 * 1024) { resolve(file); return; }
          const cv = document.createElement('canvas');
          cv.width = Math.round(bmp.width * scale);
          cv.height = Math.round(bmp.height * scale);
          const ctx = cv.getContext('2d');
          ctx.fillStyle = '#fff';
          ctx.fillRect(0, 0, cv.width, cv.height);
          ctx.drawImage(bmp, 0, 0, cv.width, cv.height);
          cv.toBlob(function (blob) {
            if (!blob || blob.size >= file.size) { resolve(file); return; }
            resolve(new File([blob], file.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' }));
          }, 'image/jpeg', 0.85);
        }).catch(function () { resolve(file); });
      });
    };

    form.querySelectorAll('.file-drop input[type="file"]').forEach(function (inp) {
      inp.addEventListener('change', function () {
        const box = inp.closest('.file-drop');
        const prev = box.querySelector('.file-preview');
        const text = box.querySelector('.file-text b');
        const file = inp.files && inp.files[0];
        if (!file) {
          prev.style.backgroundImage = '';
          box.classList.remove('has-file');
          text.textContent = 'Choose a photo';
          return;
        }
        text.textContent = 'Preparing photo...';
        compress(file).then(function (f) {
          if (f !== file) {
            const dt = new DataTransfer();
            dt.items.add(f);
            inp.files = dt.files;
          }
          if (f.size > 5 * 1024 * 1024) {
            inp.value = '';
            prev.style.backgroundImage = '';
            box.classList.remove('has-file');
            text.textContent = 'Choose a photo';
            showAlert('This photo is too large. Please choose a smaller one (under 5 MB).');
            return;
          }
          prev.style.backgroundImage = 'url(' + URL.createObjectURL(f) + ')';
          box.classList.add('has-file');
          text.textContent = f.name;
        });
      });
    });

    // One token per attempt: if the request is repeated (double click, timeout, refresh) the server will not create a second account
    const tokKey = 'xsn_tok_' + form.dataset.regForm;
    const token = function () {
      let t = null;
      try { t = sessionStorage.getItem(tokKey); } catch (e) { t = null; }
      if (!t) {
        t = (window.crypto && window.crypto.randomUUID)
          ? window.crypto.randomUUID()
          : 't' + Date.now().toString(16) + Math.random().toString(16).slice(2) + Math.random().toString(16).slice(2);
        try { sessionStorage.setItem(tokKey, t); } catch (e) { /* private mode */ }
      }
      return t;
    };

    let busy = false;
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      if (busy || !valid()) return;
      busy = true;

      const label = submit.textContent;
      submit.disabled = true;
      back.disabled = true;
      submit.textContent = 'Please wait...';
      hideAlert();

      const data = new FormData(form);
      data.set('xsn_token', token());

      const ctl = 'AbortController' in window ? new AbortController() : null;
      const timer = ctl ? setTimeout(function () { ctl.abort(); }, 60000) : null;
      const release = function () {
        busy = false;
        submit.disabled = false;
        back.disabled = false;
        submit.textContent = label;
        if (timer) clearTimeout(timer);
      };

      fetch(cfg.ajax + '?action=xsn_register', { method: 'POST', body: data, credentials: 'same-origin', signal: ctl ? ctl.signal : undefined })
        .then(function (r) {
          return r.text().then(function (txt) {
            try { return JSON.parse(txt); } catch (e) {
              console.error('XASSIDA register: unexpected server answer (HTTP ' + r.status + ')', txt.slice(0, 800));
              return { success: false, data: { message: 'The server returned an unexpected answer (HTTP ' + r.status + '). Please try again. If it keeps happening, contact support.' } };
            }
          });
        })
        .then(function (res) {
          if (res && res.success) {
            try { sessionStorage.removeItem(tokKey); } catch (e) { /* ignore */ }
            window.location.href = (res.data && res.data.redirect) || cfg.login || '/';
            return;
          }
          const d = (res && res.data) || {};
          showAlert(d.message || 'Something went wrong. Please try again.');
          if (d.debug) { const s = document.createElement('small'); s.textContent = 'Details: ' + d.debug; alertBox.appendChild(s); }
          if (d.field) {
            const el = form.querySelector('[name="' + d.field + '"]');
            const step = el ? el.closest('.reg-step') : null;
            if (step) { go(steps.indexOf(step)); el.focus(); }
          }
          release();
        })
        .catch(function (err) {
          const slow = err && err.name === 'AbortError';
          showAlert((slow ? 'This is taking longer than usual. ' : 'Network problem. ') + 'Please press the button again. We will not create a duplicate account.');
          release();
        });
    });

    go(0);
  });
})();
