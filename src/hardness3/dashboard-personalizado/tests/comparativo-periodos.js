'use strict';
const assert = require('assert');
const graph = require('../comparativo-periodos.js');
const definition = { titulo: '<Produção>', subtitulo: '<img src=x onerror=alert(1)>' };
const data = {
  series: [
    { chave: 'atual', total: 2050, dataInicio: '2026-03-30', dataFim: '2026-03-31' },
    { chave: 'mesAnterior', total: 0, dataInicio: null, dataFim: null },
    { chave: 'anoAnterior', total: 900, dataInicio: '2025-03-30', dataFim: '2025-03-31' }
  ],
  linhas: [
    { periodo: '2026-03-30', dataAtual: '2026-03-30', dataMesAnterior: null, dataAnoAnterior: '2025-03-30', atual: 2050, mesAnterior: null, anoAnterior: 900 },
    { periodo: '2026-03-31', dataAtual: '2026-03-31', dataMesAnterior: null, dataAnoAnterior: '2025-03-31', atual: 0, mesAnterior: null, anoAnterior: 0 }
  ]
};
const html = graph.render(data, definition);
assert.equal((html.match(/data-period-series=/g) || []).length, 3);
assert(html.includes('2.050 kg') && html.includes('900 kg'));
assert(html.includes('Sem correspondência') && html.includes('Ver valores por dia'));
assert(!html.includes('<img') && html.includes('&lt;Produção&gt;'));
assert(!html.includes('NaN') && !html.includes('Infinity'));
assert.equal(graph.render(data, { habilitado: false }), '');
assert(graph.render(null, {}).includes('Não há dados'));
const single = graph.render({ series: data.series, linhas: [data.linhas[0]] }, {});
assert(!single.includes('NaN') && single.includes('data-period-series="atual"'));
// Datas sem correspondência interrompem a linha; não viram zeros nem duplicam o último dia.
const gap = JSON.parse(JSON.stringify(data));
gap.linhas = [Object.assign({}, data.linhas[0], { mesAnterior: 10 }), data.linhas[1], Object.assign({}, data.linhas[1], { mesAnterior: 20 })];
const path = graph.render(gap, {}).match(/data-period-series="mesAnterior" d="([^"]*)"/)[1];
assert.equal((path.match(/M/g) || []).length, 2);
console.log('OK: três linhas, totais em kg, datas ausentes, detalhes, escape de HTML e série com um único ponto.');
