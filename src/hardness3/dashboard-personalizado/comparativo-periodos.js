(function (root, factory) {
  if (typeof module === 'object' && module.exports) module.exports = factory();
  else root.HardnessPeriodComparison = factory();
}(typeof window !== 'undefined' ? window : this, function () {
  'use strict';
  var series = [
    { key: 'atual', label: 'Período filtrado', color: '#245dcc', dash: '' },
    { key: 'mesAnterior', label: 'Mês anterior', color: '#b96809', dash: '7 4' },
    { key: 'anoAnterior', label: 'Ano anterior', color: '#8450b8', dash: '3 4' }
  ];
  var number = new Intl.NumberFormat('pt-BR', { maximumFractionDigits: 3 });
  function escape(value) {
    return String(value == null ? '' : value).replace(/[&<>"']/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
    });
  }
  function date(value) {
    var parts = String(value || '').split('-');
    return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : 'Sem correspondência';
  }
  function kg(value) { return value == null ? '—' : number.format(Number(value)) + ' kg'; }
  function render(data, definition) {
    if (!definition || definition.habilitado === false) return '';
    var rows = data && Array.isArray(data.linhas) ? data.linhas : [];
    var title = definition.titulo || 'Comparativo da produção apontada (kg)';
    var header = '<article class="wide-card period-comparison"><header><div><p class="card-kicker">PRODUÇÃO APONTADA</p><h3>' + escape(title) + '</h3></div><span class="chip">kg por dia</span></header>';
    if (!rows.length) return header + '<p class="empty-state">Não há dados para o período filtrado.</p></article>';
    var available = {};
    (data.series || []).forEach(function (s) { available[s.chave] = s; });
    var legend = '<div class="period-comparison-legend">' + series.map(function (s) {
      var meta = available[s.key] || {}, total = meta.dataInicio ? meta.total : null;
      return '<div style="--period-color:' + s.color + '"><span><i aria-hidden="true"></i>' + s.label + '</span><strong>' + escape(kg(total)) + '</strong><small>' + escape(meta.dataInicio ? date(meta.dataInicio) + ' a ' + date(meta.dataFim) : 'Sem data correspondente') + '</small></div>';
    }).join('') + '</div>';
    var width = 900, height = 300, left = 90, right = 22, top = 24, bottom = 58;
    var values = [0];
    rows.forEach(function (r) { series.forEach(function (s) { if (r[s.key] != null) values.push(Number(r[s.key])); }); });
    var min = Math.min.apply(Math, values), max = Math.max.apply(Math, values);
    if (max === min) max = min + 1;
    var plotWidth = width - left - right, plotHeight = height - top - bottom;
    function x(index) { return left + (rows.length > 1 ? index * plotWidth / (rows.length - 1) : plotWidth / 2); }
    function y(value) { return top + plotHeight - (Number(value) - min) / (max - min) * plotHeight; }
    var grid = [0, 0.25, 0.5, 0.75, 1].map(function (level) {
      var value = min + (max - min) * level, pos = y(value);
      return '<line class="chart-grid" x1="' + left + '" y1="' + pos + '" x2="' + (width - right) + '" y2="' + pos + '"/><text class="chart-label" x="' + (left - 12) + '" y="' + (pos + 4) + '" text-anchor="end">' + escape(number.format(value)) + '</text>';
    }).join('');
    var labels = rows.map(function (r, index) {
      if (index !== rows.length - 1 && index % Math.max(1, Math.ceil(rows.length / 8))) return '';
      var anchor = rows.length === 1 ? 'middle' : (index === 0 ? 'start' : (index === rows.length - 1 ? 'end' : 'middle'));
      return '<text class="chart-label" x="' + x(index) + '" y="' + (height - 24) + '" text-anchor="' + anchor + '">' + escape(date(r.periodo)) + '</text>';
    }).join('');
    var lines = series.map(function (s) {
      var connected = false, path = '', marks = '';
      rows.forEach(function (r, index) {
        if (r[s.key] == null) { connected = false; return; }
        var px = x(index).toFixed(2), py = y(r[s.key]).toFixed(2);
        path += (connected ? ' L' : ' M') + px + ',' + py;
        connected = true;
        if (rows.length <= 93) {
          var sourceDate = r['data' + s.key.charAt(0).toUpperCase() + s.key.slice(1)];
          marks += '<circle class="period-point" cx="' + px + '" cy="' + py + '" r="3.5"><title>' + escape(s.label + ' · ' + date(sourceDate) + ': ' + kg(r[s.key])) + '</title></circle>';
        }
      });
      return '<g style="--period-color:' + s.color + '"><path class="period-line" data-period-series="' + s.key + '" d="' + path + '" stroke-dasharray="' + s.dash + '"/>' + marks + '</g>';
    }).join('');
    var description = 'Produção em kg por dia: período filtrado, mês anterior e ano anterior.';
    var plot = '<div class="chart-scroll"><svg class="chart" role="img" aria-label="' + description + '" viewBox="0 0 ' + width + ' ' + height + '"><title>' + description + '</title><text class="chart-label" x="' + left + '" y="14">kg</text>' + grid + labels + lines + '</svg></div>';
    var table = '<details class="period-comparison-values"><summary>Ver valores por dia</summary><div class="table-scroll"><table><thead><tr><th>Dia do período filtrado</th><th>Período filtrado (kg)</th><th>Mês anterior (kg)</th><th>Ano anterior (kg)</th></tr></thead><tbody>' + rows.map(function (r) {
      return '<tr><td>' + escape(date(r.periodo)) + '</td>' + series.map(function (s) {
        var sourceDate = r['data' + s.key.charAt(0).toUpperCase() + s.key.slice(1)];
        return '<td>' + escape(r[s.key] == null ? '—' : number.format(Number(r[s.key]))) + '<small>' + escape(date(sourceDate)) + '</small></td>';
      }).join('') + '</tr>';
    }).join('') + '</tbody></table></div></details>';
    return header + legend + '<p class="comparison-description">' + escape(definition.subtitulo || 'Quantidade apontada nos mesmos dias do período filtrado, do mês anterior e do ano anterior.') + '</p>' + plot + '<p class="period-comparison-note">Dias sem produção aparecem como zero. Datas inexistentes no mês ou ano anterior ficam sem correspondência.</p>' + table + '</article>';
  }
  return { render: render };
}));
