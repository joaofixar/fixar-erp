// php scripts/testes/nf-debito-credito-vinculo-cancelado.php --javascript | node scripts/testes/nf-debito-credito-listagem-interface.js
// Executa o JS gerado pela grade com a API de eventos do jQuery legado simulada.
'use strict';
const fs = require('fs');
const vm = require('vm');
const path = require('path');
const script = fs.readFileSync(0, 'utf8');
let verificacoes = 0;
function conferir(ok, mensagem) {
    verificacoes++;
    if (!ok) throw new Error(mensagem);
}
const handlers = new Map();
const janelas = [];
let cliqueLinha;
let inicializacoesConcluidas = 0;
const container = {
    // .on/.off não existem no jQuery 1.6.2 carregado pelo framework.
    undelegate(selector, evento) { handlers.delete(selector + ':' + evento); return this; },
    delegate(selector, evento, callback) { handlers.set(selector + ':' + evento, callback); return this; }
};
function $(elemento) {
    if (elemento === '#testeGrid') return container;
    return {
        closest(selector) { return {length: selector === '.nfDCOrigem' && elemento.origem ? 1 : 0}; },
        attr(nome) { return nome === 'data-nf-origem' ? elemento.idOrigem : undefined; }
    };
}
const contexto = vm.createContext({$, jQuery: $,
    acaoClickLinha(div, callback) { conferir(div === 'testeGrid', 'Clique fora da grade'); cliqueLinha = callback; },
    abrirJanela(...args) { janelas.push(args); },
    menuId: () => 'janelaNF', unique: () => 'janelaOrigem', encodeURIComponent,
    continuarInicializacao() { inicializacoesConcluidas++; }
});
const bootstrap = fs.readFileSync(path.resolve(__dirname, '../../sistema/index-framework.php'), 'utf8');
conferir(bootstrap.includes('static/js/jquery-1.6.2.min.js'), 'Rever o teste: versão de jQuery do framework mudou');
// Uma exceção no JS da aba não pode interromper os passos seguintes do carregamento.
vm.runInContext(script + '\ncontinuarInicializacao();', contexto);
conferir(inicializacoesConcluidas === 1, 'Inicialização interrompida antes de concluir o carregamento');
conferir(handlers.size === 1, 'Clique na origem não registrado');
cliqueLinha({target: {origem: false}}, {todosCampos: {T007_Id: 583}});
conferir(janelas.length === 1 && janelas[0][7] === '&acaoId=583', 'Linha não abre a nota gerada');
conferir(janelas[0][6] === '/ven/ven003/content/ven003contentIncluirNotaFiscal/', 'Formulário fora do framework');
cliqueLinha({target: {origem: true}}, {todosCampos: {T007_Id: 583}});
conferir(janelas.length === 1, 'Clique na origem também abriu a nota gerada');
let preveniu = false;
let interrompeu = false;
const evento = {target: {origem: true}, preventDefault() { preveniu = true; }, stopPropagation() { interrompeu = true; }};
handlers.get('.nfDCOrigem:click.nfDCOrigem').call({idOrigem: '567'}, evento);
conferir(preveniu && interrompeu, 'Clique na origem propagou ou navegou para #');
conferir(janelas.length === 2 && janelas[1][7] === '&acaoId=567', 'Não abriu o ID da origem');
conferir(janelas[1][2] === 'testeGrid', 'Janela não mantém a grade como pai para atualização');
// Atualizar a grade substitui o evento anterior e mantém o clique delegado
// funcionando também para links inseridos na atualização de uma linha.
vm.runInContext(script + '\ncontinuarInicializacao();', contexto);
conferir(handlers.size === 1 && inicializacoesConcluidas === 2, 'Refresh duplicou eventos ou interrompeu inicialização');
handlers.get('.nfDCOrigem:click.nfDCOrigem').call({idOrigem: '558'}, evento);
conferir(janelas.length === 3 && janelas[2][7] === '&acaoId=558', 'Origem de linha atualizada não abriu');
console.log('OK: ' + verificacoes + ' verificações do clique e compatibilidade com eventos legados; sem navegador/banco.');
