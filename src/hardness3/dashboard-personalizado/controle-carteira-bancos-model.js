(function (root, factory) {
  'use strict';
  var model = factory();
  if (typeof module === 'object' && module.exports) module.exports = model;
  else root.DashboardCarteiraBancosModel = model;
}(typeof window === 'object' ? window : this, function () {
  'use strict';
  function addDays(date, days) {
    var value = new Date(date + 'T00:00:00Z');
    value.setUTCDate(value.getUTCDate() + days);
    return value.toISOString().slice(0, 10);
  }
  function validGuarantee(value) {
    return typeof value === 'number' && Number.isFinite(value) && value >= 0 && value <= 1e12
      && Math.round(value * 100) / 100 === value;
  }
  function calculate(data, guarantees) {
    var dates = Array.from({ length: data.diasProjecao }, function (_, i) { return addDays(data.dataInicio, i); });
    var banks = data.bancos.map(function (bank) {
      var guarantee = guarantees && Object.prototype.hasOwnProperty.call(guarantees, bank.id) ? guarantees[bank.id] : bank.garantia;
      if (!validGuarantee(guarantee)) throw new Error('Informe uma garantia válida para ' + bank.nome + '.');
      var guaranteeCents = Math.round(guarantee * 100);
      var opening = bank.carteiraCentavos - guaranteeCents;
      var balance = opening;
      var first = opening <= 0 ? data.dataInicio : '';
      var points = dates.map(function (date) {
        var due = Number(bank.vencimentos[date] || 0);
        balance -= due;
        if (!first && balance <= 0) first = date;
        return { data: date, vencimentosCentavos: due, saldoCentavos: balance };
      });
      return Object.assign({}, bank, {
        garantia: guarantee, garantiaCentavos: guaranteeCents, saldoInicialCentavos: opening,
        primeiraData: first, insuficienteNaAbertura: opening <= 0, pontos: points,
        saldo7Centavos: points[Math.min(6, points.length - 1)].saldoCentavos,
        saldoFinalCentavos: balance, recomporCentavos: Math.max(0, -balance)
      });
    });
    function sum(field) { return banks.reduce(function (value, bank) { return value + bank[field]; }, 0); }
    return { datas: dates, bancos: banks, totalBancosCentavos: sum('carteiraCentavos'),
      garantiaCentavos: sum('garantiaCentavos'), saldoInicialCentavos: sum('saldoInicialCentavos'), saldo7Centavos: sum('saldo7Centavos'),
      bancosEmAlerta: banks.filter(function (bank) { return bank.primeiraData; }).length };
  }
  function csv(data, result) {
    function cell(value) {
      value = String(value == null ? '' : value);
      if (/^[=+@\-\t\r]/.test(value) && !/^-?\d+(?:,\d{2})?$/.test(value)) value = "'" + value;
      return '"' + value.replace(/"/g, '""') + '"';
    }
    function money(cents) { return (cents / 100).toFixed(2).replace('.', ','); }
    var rows = [['Banco', 'Data', 'Carteira inicial', 'Garantia exigida', 'Vencimentos do dia', 'Saldo livre projetado', 'Emitir a partir de']];
    result.bancos.forEach(function (bank) {
      rows.push([bank.nome, 'Abertura', money(bank.carteiraCentavos), money(bank.garantiaCentavos), '0,00', money(bank.saldoInicialCentavos), bank.primeiraData]);
      bank.pontos.forEach(function (point) {
        rows.push([bank.nome, point.data, money(bank.carteiraCentavos), money(bank.garantiaCentavos), money(point.vencimentosCentavos), money(point.saldoCentavos), bank.primeiraData]);
      });
    });
    return '\uFEFF' + rows.map(function (row) { return row.map(cell).join(';'); }).join('\r\n');
  }
  return { calculate: calculate, addDays: addDays, validGuarantee: validGuarantee, csv: csv };
}));
