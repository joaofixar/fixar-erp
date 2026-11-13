(function () {
  'use strict';
  var names = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
  function iso(date) {
    return date.getFullYear() + '-' + String(date.getMonth() + 1).padStart(2, '0') + '-' + String(date.getDate()).padStart(2, '0');
  }
  function mount(form, onChange) {
    var from = form.elements.namedItem('f_dataInicio'), to = form.elements.namedItem('f_dataFim');
    if (!from || !to) return;
    var today = iso(new Date()), currentMonth = today.slice(0, 7), blocked = from.disabled || to.disabled;
    var selected = /^\d{4}-(0[1-9]|1[0-2])-\d{2}$/.test(to.value) ? to.value.slice(0, 7) : currentMonth;
    if (selected > currentMonth) selected = currentMonth;
    var annual = from.value === selected.slice(0, 4) + '-01-01' && from.value.slice(0, 7) !== selected;
    var picker = document.createElement('div');
    picker.className = 'commercial-month-filter';
    picker.innerHTML = '<div class="commercial-month-heading"><span>Mês de emissão</span><label for="commercial-month-year">Ano<input id="commercial-month-year" type="number" min="1900" max="' + today.slice(0, 4) + '" step="1" required' + (blocked ? ' disabled' : '') + '></label></div>' +
      '<div class="commercial-month-buttons" role="group" aria-label="Mês de emissão"></div><p class="commercial-month-period" aria-live="polite"></p>';
    form.insertBefore(picker, form.firstChild);
    var year = picker.querySelector('input'), buttons = picker.querySelector('[role=group]'), period = picker.querySelector('p');
    function draw() {
      year.value = selected.slice(0, 4);
      buttons.innerHTML = names.map(function (name, index) {
        var month = year.value + '-' + String(index + 1).padStart(2, '0');
        return '<button type="button" data-commercial-month="' + month + '" aria-pressed="' + (!annual && month === selected) + '"' + (blocked || month > currentMonth ? ' disabled' : '') + '>' + name + '</button>';
      }).join('');
      period.textContent = from.value.split('-').reverse().join('/') + ' a ' + to.value.split('-').reverse().join('/') + (blocked ? ' · Período definido pelo acesso.' : annual ? ' · Acumulado do ano. Clique em um mês para filtrar.' : ' · Mês atual até hoje; meses anteriores completos. Clique novamente no mês selecionado para ver o acumulado do ano.');
    }
    function apply(month, notify, wholeYear) {
      selected = month;
      annual = wholeYear === true;
      if (!blocked) {
        from.value = annual ? selected.slice(0, 4) + '-01-01' : selected + '-01';
        to.value = annual ? (selected.slice(0, 4) === today.slice(0, 4) ? today : selected.slice(0, 4) + '-12-31') :
          (selected === currentMonth ? today : iso(new Date(Number(selected.slice(0, 4)), Number(selected.slice(5)), 0)));
      }
      draw();
      if (notify && onChange) onChange();
    }
    buttons.addEventListener('click', function (event) {
      var button = event.target.closest('[data-commercial-month]');
      if (!button || button.disabled) return;
      var month = button.getAttribute('data-commercial-month');
      apply(month, true, !annual && month === selected);
      var focused = buttons.querySelector('[data-commercial-month="' + month + '"]');
      if (focused) focused.focus({ preventScroll: true });
    });
    year.addEventListener('change', function () {
      if (blocked || !year.reportValidity()) return;
      var month = year.value + '-' + selected.slice(5);
      apply(month > currentMonth ? currentMonth : month, true, annual);
    });
    apply(selected, false, annual);
  }
  window.DashboardFiltrosMesesComerciais = { mount: mount };
}());
