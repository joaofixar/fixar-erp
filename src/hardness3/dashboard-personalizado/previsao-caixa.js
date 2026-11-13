(function () {
  'use strict';
  function escape(value) {
    return String(value == null ? '' : value).replace(/[&<>"']/g, function (char) { return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[char]; });
  }
  function mount(container, data, options) {
    var rows = data.lancamentos || [], pending = data.pedidosPendentes || [], simulation = data.simulacao;
    var simulationHint = simulation && simulation.mensagem ? '<details class="cash-forecast-warning"><summary>' + escape(simulation.mensagem) + '</summary><p>Empresa: ' + escape(simulation.empresa) + ' · Abrangência: ' + escape(simulation.abrangenciaEmpresa) + ' · Emissão desde: ' + escape(simulation.emissaoPedidosInicio) + ' · Parcelas consultadas: ' + escape(simulation.parcelasConsultadas) + ' · Geradas: ' + escape(simulation.parcelasGeradas) + ' · No período: ' + escape(simulation.parcelasNoPeriodo) + '</p></details>' : '';
    var money = new Intl.NumberFormat('pt-BR', { style: 'currency', currency: 'BRL' });
    container.innerHTML = '<article class="wide-card cash-forecast"><header><div><p class="card-kicker">SIMULAÇÃO</p><h3>Previsão de contas a pagar</h3>' +
      '<p class="list-footnote">As previsões são compartilhadas com todos os usuários que têm acesso a este indicador.</p>' +
      (options.compact ? '' : '<p class="list-footnote">Inclua despesas adicionais que ainda não possuem título no financeiro.</p>') + '</div></header>' +
      '<form class="filters cash-forecast-form"><input type="hidden" name="id"><label><span>Descrição</span><input name="descricao" maxlength="80" required></label>' +
      '<label><span>Data prevista</span><input name="data" type="date" required></label><label><span>Valor (R$)</span><input name="valor" type="number" min="0.01" max="1000000000000" step="0.01" required></label>' +
      '<div class="filter-actions"><button class="primary" type="submit">Adicionar previsão</button><button class="secondary" type="button" data-cancel hidden>Cancelar edição</button></div></form>' +
      '<p class="cash-forecast-status" role="status" aria-live="polite"></p>' + simulationHint +
      (pending.length ? '<p class="cash-forecast-warning">Pedidos sem previsão por entrega ou parcelamento incompleto, ou financeiro antecipado: ' + pending.map(escape).join(', ') + '. Confira esses pedidos no ERP.</p>' : '') +
      (options.compact ? '' : '<p class="list-footnote">As barras usam a data projetada e os filtros acima. A lista abaixo inclui as previsões salvas fora do período. Ao selecionar todas as empresas, também aparecem as previsões das empresas incluídas; edite cada uma pela empresa de origem.</p>') +
      (rows.length ? '<div class="table-scroll"><table><thead><tr><th>Empresa</th><th>Descrição</th><th>Data prevista</th><th>Valor</th><th>Ações</th></tr></thead><tbody>' + rows.map(function (row, index) {
        return '<tr><td>' + escape(row.empresa) + '</td><td>' + escape(row.descricao) + '</td><td>' + escape(row.data.split('-').reverse().join('/')) + '</td><td>' + escape(money.format(row.valor)) + '</td><td>' +
          (row.editavel ? '<button class="secondary" type="button" data-edit="' + index + '">Editar</button> <button class="secondary" type="button" data-remove="' + index + '">Remover</button>' : 'Empresa de origem') + '</td></tr>';
      }).join('') + '</tbody></table></div>' : '<p class="empty-state">Nenhuma previsão manual cadastrada.</p>') + '</article>';
    var form = container.querySelector('form'), status = container.querySelector('[role="status"]'), cancel = container.querySelector('[data-cancel]'), busy = false;
    function field(name) { return form.elements.namedItem(name); }
    function reset() { form.reset(); field('id').value = ''; form.querySelector('[type="submit"]').textContent = 'Adicionar previsão'; cancel.hidden = true; }
    function save(payload) {
      if (busy) return;
      busy = true;
      container.querySelectorAll('button').forEach(function (button) { button.disabled = true; });
      status.textContent = 'Salvando previsão…';
      options.save(payload).then(function () { status.textContent = 'Previsão salva.'; options.reload(); }).catch(function (error) {
        status.textContent = error.message || 'Não foi possível salvar a previsão.';
        busy = false;
        container.querySelectorAll('button').forEach(function (button) { button.disabled = false; });
      });
    }
    form.addEventListener('submit', function (event) {
      event.preventDefault();
      if (!form.reportValidity()) return;
      save({ id: field('id').value, descricao: field('descricao').value.trim(), data: field('data').value, valor: Number(field('valor').value) });
    });
    cancel.addEventListener('click', reset);
    container.querySelectorAll('[data-edit]').forEach(function (button) { button.addEventListener('click', function () {
      var row = rows[Number(button.getAttribute('data-edit'))];
      ['id', 'descricao', 'data', 'valor'].forEach(function (key) { field(key).value = row[key]; });
      form.querySelector('[type="submit"]').textContent = 'Salvar alteração'; cancel.hidden = false; field('descricao').focus();
    }); });
    container.querySelectorAll('[data-remove]').forEach(function (button) { button.addEventListener('click', function () {
      save({ id: rows[Number(button.getAttribute('data-remove'))].id, remover: true });
    }); });
  }
  window.DashboardPrevisaoCaixa = { mount: mount };
}());
