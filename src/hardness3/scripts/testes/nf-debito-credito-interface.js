// php scripts/testes/nf-debito-credito-compatibilidade.php --regras-json | node scripts/testes/nf-debito-credito-interface.js
// Executa funções reais da tela em um contexto DOM simulado, sem navegador/banco.
'use strict';
const fs = require('fs');
const path = require('path');
const vm = require('vm');
const root = path.resolve(__dirname, '../..');
const rules = JSON.parse(fs.readFileSync(0, 'utf8'));
const content = fs.readFileSync(path.join(root, 'prog/ven/ven003/content.php'), 'utf8');
const start = content.indexOf("case 'ven003ContentNFDebitoCreditoAutorizada':");
const route = content.slice(start, content.indexOf("case 'ven003ContentNotaTransporte':", start));
let script = route.slice(route.indexOf('<script type="text/javascript">') + '<script type="text/javascript">'.length, route.indexOf('</script>'));
script = script.replace(/\{\$idModal\}/g, 'modalTeste').replace(/\{\$casasQuantidade\}/g, '3')
    .replace(/\{\$regrasFiscaisNFDC\}/g, JSON.stringify(rules)).replace(/\{\$credito02Disabled\}/g, ' disabled="disabled"')
    .replace(/\{\$T007_IdOrigem\}/g, '100');
new vm.Script(script); // Valida o JS completo da rota, incluindo interpolação da matriz PHP.
let count = 0;
function check(ok, msg) { count++; if (!ok) throw new Error(msg); }
const state = {operation: 'debito', type: '03', values: {}, shown: []};
const modal = {find(selector) {
    return {
        val() { return selector === '#tipoOperacaoNFDCOrigem' ? state.operation : state.type; },
        html(value) { state.values[selector] = value; return this; },
        text(value) { state.values[selector] = value; return this; },
        toggle() { return this; },
        hide() { state.shown = []; return this; },
        css(name, value) { if (name === 'display' && value === 'flex') state.shown.push(selector); return this; }
    };
}};
const context = {modal, regrasFiscaisNFDCOrigem: rules, quantidadeRevisadaNFDCOrigem: false,
    atualizarAutoajudaNFDCOrigem() {}, casasQuantidadeNFDCOrigem: 3, $: {trim: s => String(s).trim()}};
vm.createContext(context);
const messageStart = script.indexOf('function atualizarMensagemNFDCOrigem()');
const messageEnd = script.indexOf("modal.undelegate('#alternarAutoajudaNFDCOrigem'", messageStart);
vm.runInContext(script.slice(messageStart, messageEnd), context);
const numberStart = script.indexOf('function numeroNFDCOrigem(');
const numberEnd = script.indexOf('function atualizarTipoNFDCOrigem()', numberStart);
vm.runInContext(script.slice(numberStart, numberEnd), context);
for (const key of Object.keys(rules)) {
    state.operation = key[0] === '6' ? 'debito' : 'credito'; state.type = key.slice(2);
    context.atualizarMensagemNFDCOrigem();
    const rule = rules[key];
    const expected = rule.grupo === 'zfm' ? [] :
        rule.grupo === 'transferencia' ? ['.grupoTransferenciaNFDC'] :
        rule.grupo === 'ajuste' ? ['.grupoAjusteNFDC'] : ['.grupoEstornoNFDC'];
    check(JSON.stringify(expected) === JSON.stringify(state.shown), 'Campos incorretos para ' + key);
    check(!state.values['#mensagemCompatibilidadeNFDCOrigem'], 'Interface voltou a orientar/bloquear classificação');
}
state.operation = 'credito'; state.type = '06'; context.atualizarMensagemNFDCOrigem();
check(state.values['#mensagemReferenciaNFDCOrigem'].includes('com nItem'), 'Crédito 06 perdeu referência por item');
state.operation = 'debito'; state.type = '03'; context.atualizarMensagemNFDCOrigem();
check(state.values['#mensagemReferenciaNFDCOrigem'].includes('sem nItem'), 'Débito 03 passou a exigir nItem');
const tiposDebito = script.match(/var tiposDebito = '(.*?)';/)[1];
check(!tiposDebito.includes('value="06"'), 'Débito 06 voltou para o botão da NF autorizada');
check(context.formatarQuantidadeNFDCOrigem(context.numeroNFDCOrigem('8,000') * 35 / 100) === '2,800', 'Percentual/quantidade incorretos');

// Executa o envio real com valores por item, preservando a precisão textual do preço.
const inicioEnvio = script.indexOf("modal.undelegate('#ven003GerarNFDCOrigem'");
const codigoEnvio = script.slice(inicioEnvio, script.lastIndexOf('});') + 3);
let enviar, requisicao, carregando = false;
const botao = {disabled: false, prop(nome, valor) { if (arguments.length === 1) return this[nome]; this[nome] = valor; return this; }};
function campo(id, valor) { return {attr: () => id, val: () => valor}; }
const campos = {
    '.qtdNFDCOrigem': [campo('101', '3,500'), campo('102', '2,000')],
    '.precoNFDCOrigem': [campo('101', '1.234,5678901234'), campo('102', '')],
    '.linhaReformaNFDCOrigem': []
};
const valoresFormulario = {'#tipoOperacaoNFDCOrigem': 'credito', '#tipoFiscalNFDCOrigem': '04', '#cfopNFDCOrigem': '3'};
const modalEnvio = {
    undelegate() { return this; },
    delegate(selector, evento, callback) { enviar = callback; return this; },
    find(selector) { return campos[selector] ? {each(fn) { campos[selector].forEach(c => fn.call(c)); }} : {val() { return valoresFormulario[selector]; }}; },
    showLoading() { carregando = true; }
};
const jqueryEnvio = valor => valor;
jqueryEnvio.ajax = opcoes => { requisicao = opcoes; };
vm.runInNewContext(codigoEnvio, {modal: modalEnvio, $: jqueryEnvio, JSON});
enviar.call(botao);
check(carregando && botao.disabled, 'Envio não bloqueou duplo clique');
const precosEnviados = JSON.parse(requisicao.data.precos);
check(precosEnviados['101'] === '1.234,5678901234', 'Preço perdeu casas decimais no navegador');
check(precosEnviados['102'] === '', 'Preço não preenchido perdeu semântica de preservar origem');
check(JSON.parse(requisicao.data.quantidades)['101'] === '3,500', 'Preço alterou quantidade enviada');
check(requisicao.url === '/ven/ven003/form_func-ajax/gerarNFDebitoCreditoAutorizada/', 'Preço enviado para rota diferente');
const ajaxFonte = fs.readFileSync(path.join(root, 'prog/ven/ven003/form_func-ajax.php'), 'utf8');
check(ajaxFonte.includes("json_decode($_REQUEST['precos'], true)") && ajaxFonte.includes("'precos' => $precos"), 'Endpoint não encaminha preços ao gerador');
const inicioPercentual = script.indexOf("modal.undelegate('#preencherPercentualNFDCOrigem'");
check(!script.slice(inicioPercentual, inicioEnvio).includes('precoNFDCOrigem'), 'Preencher percentual passou a alterar preço');
console.log('OK: ' + count + ' verificações da interface e sintaxe do JS completo.');
