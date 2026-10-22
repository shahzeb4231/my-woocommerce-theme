(function () {
  const list = document.querySelector('[data-faq]');
  if (!list) return;
  const items = Array.from(list.querySelectorAll('.faq-item'));

  const close = function (item) {
    item.classList.remove('is-open');
    item.querySelector('.faq-q').setAttribute('aria-expanded', 'false');
    item.querySelector('.faq-a').style.maxHeight = null;
  };
  const open = function (item) {
    item.classList.add('is-open');
    item.querySelector('.faq-q').setAttribute('aria-expanded', 'true');
    const a = item.querySelector('.faq-a');
    a.style.maxHeight = a.scrollHeight + 'px';
  };

  items.forEach(function (item) {
    item.querySelector('.faq-q').addEventListener('click', function () {
      const was = item.classList.contains('is-open');
      items.forEach(close);
      if (!was) open(item);
    });
  });

  open(items[0]);
})();
