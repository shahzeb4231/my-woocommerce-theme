(function () {
  const form = document.querySelector('[data-booking]');
  if (!form) return;

  const fares = { economy: 2000, taxi: 3200, suv: 4500, luxury: 7000 };
  const names = { economy: 'Economy', taxi: 'Taxi', suv: 'SUV', luxury: 'Luxury' };
  const out = form.querySelector('[data-fare]');
  const tabs = form.querySelectorAll('.vtab');
  let type = 'taxi';

  const money = (n) => String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' CFA';
  const render = () => { out.textContent = money(fares[type]); };

  tabs.forEach(function (t) {
    t.addEventListener('click', function () {
      type = t.dataset.type;
      tabs.forEach((x) => {
        x.classList.toggle('is-active', x === t);
        x.setAttribute('aria-pressed', x === t ? 'true' : 'false');
      });
      render();
    });
  });

  const date = form.querySelector('input[type="date"]');
  if (date) date.min = new Date().toISOString().split('T')[0];

  form.addEventListener('submit', function (e) {
    e.preventDefault();
    const d = new FormData(form);
    const msg = [
      'Hello XASSIDA SN, I would like to book a ride.',
      'Pickup: ' + d.get('pickup'),
      'Destination: ' + d.get('dest'),
      'Date: ' + d.get('date') + ' at ' + d.get('time'),
      'Passengers: ' + d.get('pax'),
      'Vehicle: ' + names[type],
      'Estimated fare: ' + money(fares[type])
    ].join('\n');
    window.open('https://wa.me/' + form.dataset.wa + '?text=' + encodeURIComponent(msg), '_blank', 'noopener');
  });

  render();
})();
