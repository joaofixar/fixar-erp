(function () {
  'use strict';
  var model = window.DashboardCarteiraBancosModel;
  function esc(value) { return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]; }); }
  function money(cents) { return (cents / 100).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' }); }
  function date(value) { return value ? value.slice(8, 10) + '/' + value.slice(5, 7) + '/' + value.slice(0, 4) : ''; }
  function shortDate(value) { return date(value).slice(0, 5); }
  function color(bank) { return /^#[a-f0-9]{6}$/i.test(bank.cor) ? bank.cor : '#2563eb'; }
  function legend(banks) { return '<div class="wallet-legend">' + banks.map(function (bank) { return '<span><i style="background:' + color(bank) + '"></i>' + esc(bank.nome) + '</span>'; }).join('') + '</div>'; }
  function chart(banks, dates, lines) {
    var width = 1120, height = 365, left = 160, right = 24, top = 22, bottom = 43;
    var plotWidth = width - left - right, plotHeight = height - top - bottom;
    var values = [0];
    banks.forEach(function (bank) { bank.pontos.slice(0, dates.length).forEach(function (point) { values.push(point.saldoCentavos); }); });
    var low = Math.min.apply(null, values), high = Math.max.apply(null, values);
    if (high === low) { high += 10000; low -= 10000; }
    var pad = (high - low) * 0.08;
    if (low < 0) low -= pad;
    if (high > 0) high += pad;
    function y(value) { return top + (high - value) / (high - low) * plotHeight; }
    function x(i) { return left + (i + 0.5) * plotWidth / dates.length; }
    var svg = '<svg viewBox="0 0 ' + width + ' ' + height + '" role="img" aria-label="' + (lines ? 'Evolução do saldo livre dos bancos selecionados' : 'Saldo livre de cada banco nos próximos sete dias') + '"><title>' + (lines ? 'Saldo livre projetado por banco' : 'Análise global de sete dias') + '</title>';
    for (var tick = 0; tick <= 4; tick++) {
      var value = low + (high - low) * tick / 4, ty = y(value);
      svg += '<path d="M' + left + ' ' + ty + 'H' + (width - right) + '" class="wallet-grid-line"/><text x="' + (left - 10) + '" y="' + (ty + 4) + '" text-anchor="end">' + esc(money(Math.round(value))) + '</text>';
    }
    svg += '<path d="M' + left + ' ' + y(0) + 'H' + (width - right) + '" class="wallet-zero-line"/>';
    var step = Math.max(1, Math.ceil(dates.length / 10));
    dates.forEach(function (day, i) {
      if (i % step === 0 || i === dates.length - 1) svg += '<text x="' + x(i) + '" y="' + (height - 17) + '" text-anchor="middle">' + shortDate(day) + '</text>';
    });
    banks.forEach(function (bank, bi) {
      var points = bank.pontos.slice(0, dates.length);
      if (lines) svg += '<polyline fill="none" stroke="' + color(bank) + '" stroke-width="2.5" points="' + points.map(function (point, i) { return x(i) + ',' + y(point.saldoCentavos); }).join(' ') + '"/>';
      points.forEach(function (point, i) {
        var title = esc(bank.nome + ' · ' + date(point.data) + ' · Saldo livre: ' + money(point.saldoCentavos) + ' · Vencimentos: ' + money(point.vencimentosCentavos));
        if (lines) {
          svg += '<circle cx="' + x(i) + '" cy="' + y(point.saldoCentavos) + '" r="' + (point.data === bank.primeiraData ? 5 : 2.5) + '" fill="' + color(bank) + '"><title>' + title + '</title></circle>';
        } else {
          var barWidth = plotWidth / dates.length * 0.78 / banks.length;
          var bx = x(i) - plotWidth / dates.length * 0.39 + bi * barWidth;
          svg += '<rect x="' + bx + '" y="' + Math.min(y(0), y(point.saldoCentavos)) + '" width="' + Math.max(1, barWidth - 2) + '" height="' + Math.max(1, Math.abs(y(0) - y(point.saldoCentavos))) + '" fill="' + color(bank) + '"><title>' + title + '</title></rect>';
        }
      });
    });
    return '<div class="wallet-chart-scroll">' + svg + '</svg></div>' + legend(banks);
  }
  function mount(container, data, options) {
    options = options || {};
    var active = true, saving = false, revision = data.revisao, guarantees = {}, saved = {}, selected = options.selected || [];
    data.bancos.forEach(function (bank) { guarantees[bank.id] = bank.garantia; saved[bank.id] = bank.garantia; });
    var result = model.calculate(data, guarantees);
    function dirty() { return Object.keys(guarantees).some(function (id) { return guarantees[id] !== saved[id]; }); }
    function invalid() { return Array.prototype.some.call(container.querySelectorAll('[data-wallet-guarantee]'), function (input) { return !input.validity.valid || input.value === ''; }); }
    function status(text, error) { var node = container.querySelector('.wallet-status'); node.textContent = text; node.classList.toggle('wallet-error', !!error); }
    function updateSave() { container.querySelector('[data-wallet-save]').disabled = saving || !dirty() || invalid(); }
    function alertText(bank) { return bank.primeiraData ? (bank.insuficienteNaAbertura ? 'Desde ' : 'A partir de ') + date(bank.primeiraData) : 'Coberto até ' + date(data.dataFim); }
    function renderComputed() {
      result = model.calculate(data, guarantees);
      result.bancos.forEach(function (bank) {
        var row = container.querySelector('[data-wallet-bank="' + bank.id + '"]');
        row.querySelector('[data-wallet-margin]').textContent = money(bank.saldoInicialCentavos);
        row.querySelector('[data-wallet-margin]').classList.toggle('wallet-negative', bank.saldoInicialCentavos < 0);
        var alert = row.querySelector('[data-wallet-alert]');
        alert.textContent = alertText(bank);
        alert.className = 'wallet-alert ' + (bank.primeiraData ? 'wallet-alert-danger' : 'wallet-alert-ok');
        row.querySelector('[data-wallet-replace]').textContent = money(bank.recomporCentavos);
      });
      var metrics = [['Carteira nos bancos', result.totalBancosCentavos], ['Garantia exigida', result.garantiaCentavos], ['Saldo livre na abertura', result.saldoInicialCentavos], ['Saldo livre em 7 dias', result.saldo7Centavos]];
      container.querySelector('.wallet-metrics').innerHTML = metrics.map(function (metric) { return '<article><span>' + metric[0] + '</span><strong class="' + (metric[1] < 0 ? 'wallet-negative' : '') + '">' + money(metric[1]) + '</strong></article>'; }).join('');
      container.querySelector('.wallet-global-chart').innerHTML = chart(result.bancos, result.datas.slice(0, 7), false);
      renderSelected(); updateSave();
    }
    function renderSelected() {
      var banks = result.bancos.filter(function (bank) { return selected.indexOf(bank.id) >= 0; });
      container.querySelector('.wallet-selected-chart').innerHTML = banks.length ? chart(banks, result.datas, true)
        + '<ul class="wallet-alert-list">' + banks.map(function (bank) { return '<li><strong>' + esc(bank.nome) + ':</strong> ' + esc(alertText(bank)) + '</li>'; }).join('')
        : '<p class="wallet-empty">Marque os bancos na listagem acima para comparar a evolução em um único gráfico.</p>';
    }
    container.innerHTML = '<div class="bank-wallet"><section class="wallet-panel"><header class="wallet-header"><div><h2>Controle de carteira bancos</h2><p>De ' + date(data.dataInicio) + ' a ' + date(data.dataFim) + ' · ' + esc(data.empresasTitulo || 'Empresa atual') + ' · Projeção por vencimento</p></div><button type="button" data-wallet-save disabled>Salvar garantias</button></header>'
      + '<p class="wallet-status" role="status" aria-live="polite">As garantias salvas são compartilhadas pelos usuários desta empresa.</p>'
      + '<div class="wallet-table-scroll"><table class="wallet-banks"><thead><tr><th>Comparar</th><th>Banco</th><th>Carteira atual</th><th>Valor da garantia (R$)</th><th>Saldo livre inicial</th><th>Orientar emissão de boletos</th><th>Repor até o fim do período</th></tr></thead><tbody>'
      + result.bancos.map(function (bank) { return '<tr data-wallet-bank="' + bank.id + '"><td><input type="checkbox" data-wallet-select="' + bank.id + '" aria-label="Comparar ' + esc(bank.nome) + '"' + (selected.indexOf(bank.id) >= 0 ? ' checked' : '') + '></td><th scope="row"><i class="wallet-bank-dot" style="background:' + color(bank) + '"></i>' + esc(bank.nome) + '</th><td>' + money(bank.carteiraCentavos) + '</td><td><input type="number" min="0" max="1000000000000" step="0.01" required inputmode="decimal" data-wallet-guarantee="' + bank.id + '" aria-label="Garantia de ' + esc(bank.nome) + '" value="' + bank.garantia.toFixed(2) + '"></td><td data-wallet-margin></td><td><span data-wallet-alert></span></td><td data-wallet-replace></td></tr>'; }).join('')
      + '</tbody></table></div><p class="wallet-note">Saldo livre = carteira atual − garantia exigida − vencimentos acumulados. A data de alerta é o primeiro dia com saldo livre menor ou igual a zero. Usa os títulos atualmente em aberto a partir da data inicial, sem prever novas emissões.</p>'
      + (data.composicao.some(function (item) { return !item.banco; }) ? '<p class="wallet-note">O total dos bancos inclui somente os portadores vinculados a eles. Os demais aparecem na composição da carteira.</p>' : '')
      + '<div class="wallet-metrics"></div></section>'
      + '<section class="wallet-panel wallet-chart-panel"><h2>Análise global — próximos 7 dias</h2><p>Saldo livre de todos os bancos ao final de cada dia.</p><div class="wallet-global-chart"></div></section>'
      + '<section class="wallet-panel wallet-chart-panel"><header class="wallet-header"><div><h2>Evolução dos bancos selecionados</h2><p>Compare as datas em que será necessário reforçar cada carteira. A linha zero indica o limite da garantia.</p></div><div class="wallet-selection-actions"><button type="button" data-wallet-all>Marcar todos</button><button type="button" data-wallet-none>Limpar seleção</button></div></header><div class="wallet-selected-chart"></div></section>'
      + '<details class="wallet-panel wallet-composition"><summary>Composição da carteira por portador</summary><p>Total da carteira: <strong>' + money(data.totalCentavos) + '</strong> · Boletos: ' + (data.totalCentavos ? (100 * data.boletosCentavos / data.totalCentavos).toFixed(1).replace('.', ',') : '0,0') + '%</p><div class="wallet-table-scroll"><table><thead><tr><th>Portador</th><th>Títulos</th><th>Valor em aberto</th></tr></thead><tbody>'
      + data.composicao.map(function (item) { return '<tr><th scope="row">' + esc(item.portador) + '</th><td>' + item.quantidade + '</td><td>' + money(item.valorCentavos) + '</td></tr>'; }).join('') + '</tbody></table></div></details></div>';
    function selectionChanged() {
      selected = Array.prototype.filter.call(container.querySelectorAll('[data-wallet-select]'), function (input) { return input.checked; }).map(function (input) { return input.getAttribute('data-wallet-select'); });
      if (options.onSelection) options.onSelection(selected.slice());
      renderSelected();
    }
    container.querySelectorAll('[data-wallet-select]').forEach(function (input) { input.addEventListener('change', selectionChanged); });
    ['all', 'none'].forEach(function (action) { container.querySelector('[data-wallet-' + action + ']').addEventListener('click', function () {
      container.querySelectorAll('[data-wallet-select]').forEach(function (input) { input.checked = action === 'all'; }); selectionChanged();
    }); });
    container.querySelectorAll('[data-wallet-guarantee]').forEach(function (input) { input.addEventListener('input', function () {
      if (invalid() || !model.validGuarantee(input.valueAsNumber)) { status('Informe uma garantia válida, com até duas casas decimais.', true); updateSave(); return; }
      container.querySelectorAll('[data-wallet-guarantee]').forEach(function (field) { guarantees[field.getAttribute('data-wallet-guarantee')] = field.valueAsNumber; });
      renderComputed(); status(dirty() ? 'Projeção recalculada. Salve para compartilhar os novos valores.' : 'As garantias salvas são compartilhadas pelos usuários desta empresa.');
    }); });
    container.querySelector('[data-wallet-save]').addEventListener('click', function () {
      if (saving || !dirty() || invalid()) return;
      saving = true; updateSave(); status('Salvando garantias…');
      container.querySelectorAll('[data-wallet-guarantee]').forEach(function (input) { input.disabled = true; });
      var payload = { garantias: Object.assign({}, guarantees), revisao: revision };
      Promise.resolve().then(function () { return options.save(payload); }).then(function (response) {
        if (!active) return;
        revision = response.revisao; saved = Object.assign({}, payload.garantias);
        status('Garantias salvas para os usuários desta empresa.');
      }).catch(function (error) { if (active) status(error.message || 'Não foi possível salvar as garantias.', true); })
        .finally(function () { if (active) { saving = false; container.querySelectorAll('[data-wallet-guarantee]').forEach(function (input) { input.disabled = false; }); updateSave(); } });
    });
    renderComputed();
    if (!data.composicao.length) status('Nenhum título em aberto com vencimento a partir da data inicial.');
    return {
      destroy: function () { active = false; },
      exportCsv: function () {
        if (invalid()) { status('Corrija as garantias antes de exportar.', true); return; }
        var blob = new Blob([model.csv(data, result)], { type: 'text/csv;charset=utf-8;' });
        var url = URL.createObjectURL(blob), link = document.createElement('a');
        link.href = url; link.download = 'controle-carteira-bancos-' + data.dataInicio + '.csv'; link.click(); setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
      },
      print: function () {
        if (invalid()) { status('Corrija as garantias antes de exportar.', true); return; }
        var popup = window.open('', '_blank');
        if (!popup) { status('Permita a abertura da janela de impressão para gerar o PDF.', true); return; }
        var clone = container.querySelector('.bank-wallet').cloneNode(true);
        clone.querySelectorAll('button').forEach(function (button) { button.remove(); });
        clone.querySelectorAll('[data-wallet-guarantee]').forEach(function (input) { input.replaceWith(document.createTextNode(money(Math.round(guarantees[input.getAttribute('data-wallet-guarantee')] * 100)))); });
        clone.querySelectorAll('[data-wallet-select]').forEach(function (input) { input.replaceWith(document.createTextNode(input.checked ? 'Sim' : '')); });
        clone.querySelectorAll('details').forEach(function (item) { item.open = true; });
        popup.document.write('<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><title>Controle de carteira bancos</title><link rel="stylesheet" href="' + esc(new URL('controle-carteira-bancos.css?v=1', window.location.href).href) + '"></head><body><p>' + (dirty() ? 'Simulação com garantias ainda não salvas.' : 'Garantias salvas.') + '</p>' + clone.outerHTML + '</body></html>');
        popup.document.close(); popup.onload = function () { popup.focus(); popup.print(); };
      }
    };
  }
  window.DashboardCarteiraBancos = { mount: mount };
}());
