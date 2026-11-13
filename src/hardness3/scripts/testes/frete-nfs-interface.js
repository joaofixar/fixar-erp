// php scripts/testes/frete-nfs-renderizacao.php | node scripts/testes/frete-nfs-interface.js
// Funções e renderer reais; DOM e rede simulados, sem acesso a banco.
'use strict';
const fs = require('fs');
const vm = require('vm');
const path = require('path');
const root = path.resolve(__dirname, '../..');
const file = fs.readFileSync(path.join(root, 'prog/est/est004/form_func-js.php'), 'utf8');
const source = file.slice(file.indexOf('<script type="text/javascript">') + '<script type="text/javascript">'.length, file.indexOf('</script>'));
let count = 0;
function check(value, message) { count++; if (!value) { throw new Error(message); } }
let fields, request, visible, loading, refreshes, messages, requests, focused, opened, lastEvent, progress, lifecycle, ajaxThrows;
const handlers = {};
const button = {disabled: false};
function field(selector) {
    return {
        val(value) { if (arguments.length) { fields[selector] = value; return this; } return fields[selector]; },
        toggle(value) { visible = value; return this; },
        first() { return this; },
        focus() { focused = selector; return this; },
        bind(event, handler) { handlers[selector + ':' + event] = handler; return this; },
        trigger(event) {
            const handler = handlers[selector + ':' + event.type];
            lastEvent = {event, result: handler ? handler.call(this, event) : undefined};
            return this;
        },
        each() { return this; }
    };
}
const area = {find: field};
const wrapper = {
    closest() { return area; },
    prop(name, value) { if (arguments.length === 1) { return button[name]; } button[name] = value; return this; },
    val(value) { if (!arguments.length) { return button.value; } button.value = value; return this; },
    attr(name, value) { button.attributes[name] = value; return this; },
    removeAttr(name) { delete button.attributes[name]; return this; }
};
function jquery(value) {
    if (value === '#abaFrete') { return {find: field, showLoading() { loading = true; }, hideLoading() { loading = false; }}; }
    if (typeof value === 'string' && value.indexOf('#dc-') === 0) {
        const dialog = progress[value.slice(4)];
        return {
            dialog(command, name, setting) {
                if (command === 'destroy') { dialog.visible = false; lifecycle.push('closeProgress'); }
                if (command === 'option') { dialog[name] = setting; }
                return this;
            }, remove() { return this; }
        };
    }
    if (value === button) { return wrapper; }
    if (typeof value === 'string' && value.indexOf('#abaFrete ') === 0) { return field(value.slice('#abaFrete '.length)); }
    return {closest() { return area; }, val() { return value.value; }};
}
jquery.ajax = options => {
    lifecycle.push('ajax');
    if (ajaxThrows) { throw new Error('Falha simulada ao iniciar a requisição'); }
    request = options; requests++;
};
jquery.Event = (type, properties) => Object.assign({type}, properties);
const context = vm.createContext({
    $: jquery, jQuery: jquery, Number, Math, CSS: {escape: value => value},
    dialogConfirm(title, text) { messages.push({title, text}); lifecycle.push('message'); },
    dialogAlert(title, text) {
        const id = String(Object.keys(progress).length + 1);
        progress[id] = {title, text, visible: true};
        lifecycle.push('progress');
        return id;
    },
    divRefresh(id, force) { refreshes.push({id, force}); },
    abrirJanela(...args) { opened.push(args); }, unique() { return 'pesquisaNotas'; }
});
vm.runInContext(source, context);
context.window = context;
context.clickedButton = button;
// Um handler inline também procura nomes/IDs nos controles do formulário.
context.scopeForm = {est004RatearFreteNFS: button, est004PesquisarFreteNFS: button};
function clicarBotaoRenderizado(id) {
    const inputs = /<input\b[^>]*>/g;
    let input;
    while ((input = inputs.exec(rendered.html)) !== null) {
        if (input[0].includes('id="' + id + '"')) {
            const handler = input[0].match(/onclick="([^"]*)"/);
            check(!!handler, 'Botão sem ação de clique: ' + id);
            return vm.runInContext('(function(){ with(scopeForm) { return (function(){' + handler[1] + '}).call(clickedButton); } })()', context);
        }
    }
    throw new Error('Botão não renderizado: ' + id);
}
function reset() {
    fields = {'#Frete_NFS_Origem_Id': '3402', '#Frete_NFS_Nota_Id': '3389', '#Frete_NFS_Nota_Id_Pesquisa': '112793', '#Frete_NFS_Valor_Salvo': '20.00', '#T013_Valor_Total': '20,00', '#T013_Flag_Rateio_Frete': '1'};
    request = null; visible = null; loading = false; refreshes = []; messages = []; requests = 0; button.disabled = false;
    focused = null; opened = []; lastEvent = null;
    button.value = 'Ratear'; button.attributes = {}; progress = {}; lifecycle = []; ajaxThrows = false;
}
function progressVisible() { return Object.keys(progress).some(id => progress[id].visible); }
// Compila e executa o JS que o renderer PHP realmente inclui na aba.
// Uma quebra de linha dentro de uma string JS interrompe também o reloadUI/tema.
const rendered = JSON.parse(fs.readFileSync(0, 'utf8'));
const scripts = /<script[^>]*>([\s\S]*?)<\/script>/g;
let generated;
while ((generated = scripts.exec(rendered.html)) !== null) {
    new vm.Script(generated[1]);
    vm.runInContext(generated[1], context);
}
check(rendered.html.includes('campo_input_pesquisa_grid itemForm'), 'Campo perdeu as classes usadas pelo tema e pelo Enter');
for (const key of Object.keys(rendered.posicoesOriginais)) {
    check(JSON.stringify(rendered.posicoesAtuais[key]) === JSON.stringify(rendered.posicoesOriginais[key]), 'Alterou posição, largura ou visibilidade do campo original: ' + key);
}
reset();
field('#Frete_NFS_Nota_Id_Pesquisa').trigger(jquery.Event('keypress', {keyCode: 13, which: 13}));
check(opened.length === 1 && lastEvent.result === false, 'Enter não abriu a pesquisa ou deixou enviar o formulário');
check(opened[0][6] === '/sistema/funcoes/pesquisaGrid/content/', 'Não abriu o seletor padrão do sistema');
const params = new URLSearchParams(opened[0][7]);
check(params.get('pesquisa') === '112793' && params.get('campo2') === 'T013_Id,T013_Numero_Nota_Fiscal', 'Busca não recebeu o número digitado e o ID para seleção');
check(params.get('campo4').includes("T013_C004_Id = '1'") && params.get('campo4').includes("T013_Id <> '3402'"), 'Pesquisa perdeu o filtro da empresa ou permite autorreferência');
check(params.get('campo7') === 'est004SelecionarNotaFreteNFS', 'Pesquisa não retorna a seleção para o frete');
reset();
context.est004PesquisarFreteNFS(button);
check(opened.length === 1 && focused === '#Frete_NFS_Nota_Id_Pesquisa', 'Botão Pesquisar não executou a mesma busca do Enter');
context.est004SelecionarNotaFreteNFS('abaFrete', '3389', '112793');
check(fields['#Frete_NFS_Nota_Id'] === '3389' && fields['#Frete_NFS_Nota_Id_Pesquisa'] === '112793', 'Seleção não preencheu ID e número');
check(focused === '#est004RatearFreteNFS', 'Seleção não direcionou para o botão Ratear');
context.est004LimparSelecaoFreteNFS(button);
check(fields['#Frete_NFS_Nota_Id'] === '', 'Editar o número manteve a nota anterior selecionada');
reset();
clicarBotaoRenderizado('est004PesquisarFreteNFS');
check(opened.length === 1, 'Clique real do botão Pesquisar foi bloqueado pelo próprio ID');
reset();
clicarBotaoRenderizado('est004RatearFreteNFS');
check(requests === 1, 'Clique real do botão Ratear foi bloqueado pelo próprio ID');
check(progressVisible() && button.value === 'Rateando...' && button.attributes['aria-busy'] === 'true', 'Clique não mostrou o início do processamento');
check(lifecycle[0] === 'progress' && lifecycle[1] === 'ajax', 'Requisição começou antes da indicação de carregamento');
check(progress['1'].closeOnEscape === false && progress['1'].text.includes('atualizando o custo dos itens'), 'Carregamento sem informação ou permite fechar o processo ativo');
request.success({code: true, data: 'R$ 20,00 rateados na NF 112793'});
check(!progressVisible() && !loading && messages[0].title === 'Rateio concluído', 'Conclusão deixou o carregamento aberto');
check(lifecycle.indexOf('closeProgress') < lifecycle.indexOf('message'), 'Conclusão ficou atrás da janela de carregamento');
check(button.value === 'Ratear' && !button.disabled && !button.attributes['aria-busy'], 'Conclusão não restaurou o botão');
reset();
context.est004AlternarFreteNFS({value: '4'});
check(visible === true, 'Frete NFS não mostrou a busca e o botão');
context.est004AlternarFreteNFS({value: '0'});
check(visible === false, 'Outra opção manteve os campos visíveis');
context.est004RatearFreteNFS(button, 'abaFrete');
check(button.disabled && loading && requests === 1, 'Rateio não protegeu o envio');
check(request.type === 'POST' && request.dataType === 'json', 'Envio deve ser POST com resposta JSON');
check(request.timeout > 0, 'Requisição pode deixar o carregamento sem prazo');
check(request.url === '/est/est004/form_func-ajax/ratearFreteNFS/?ajax=true', 'Endpoint incorreto');
check(request.data.origemId === '3402' && request.data.destinoId === '3389' && request.data.valorEsperado === '20.00', 'Enviou número da nota em lugar do ID ou perdeu valor salvo');
context.est004RatearFreteNFS(button, 'abaFrete');
check(requests === 1, 'Duplo clique enviou outro rateio');
request.success({code: true, data: 'Rateado'});
request.complete();
check(refreshes.length === 1 && refreshes[0].id === 'abaFrete' && refreshes[0].force, 'Não atualizou o vínculo salvo');
check(!button.disabled && !loading, 'Conclusão não liberou a interface');
check(lifecycle.filter(event => event === 'closeProgress').length === 1, 'Carregamento foi destruído duas vezes');
reset();
context.est004RatearFreteNFS(button, 'abaFrete');
request.success({code: false, data: 'Nota finalizada'});
request.complete();
check(messages[0].text === 'Nota finalizada' && refreshes.length === 0, 'Erro da regra não foi exibido');
check(!progressVisible() && !button.disabled, 'Erro da regra deixou o carregamento aberto');
reset();
context.est004RatearFreteNFS(button, 'abaFrete');
request.error(); request.complete();
check(!button.disabled && !loading && messages.length === 1, 'Falha de rede deixou a tela bloqueada');
check(!progressVisible() && messages[0].title !== 'Rateio concluído', 'Falha de rede apareceu como sucesso ou manteve carregamento');
reset();
context.est004RatearFreteNFS(button, 'abaFrete');
request.error({}, 'timeout'); request.complete();
check(!progressVisible() && messages[0].text.includes('pode ainda estar em processamento'), 'Timeout não informa que é preciso conferir o resultado');
reset();
context.est004RatearFreteNFS(button, 'abaFrete');
request.success(null); request.complete();
check(!progressVisible() && !button.disabled && messages[0].text.includes('não confirmou'), 'Resposta inválida deixou a tela sem conclusão');
reset(); ajaxThrows = true;
context.est004RatearFreteNFS(button, 'abaFrete');
check(!progressVisible() && !loading && !button.disabled && messages[0].text.includes('iniciar'), 'Falha ao iniciar deixou o carregamento aberto');
for (const pair of [['#Frete_NFS_Origem_Id', '0'], ['#Frete_NFS_Nota_Id', ''], ['#Frete_NFS_Nota_Id', '0'], ['#T013_Valor_Total', '25,00']]) {
    reset(); fields[pair[0]] = pair[1];
    context.est004RatearFreteNFS(button, 'abaFrete');
    check(requests === 0 && messages.length === 1 && !button.disabled, 'Enviou com NFS não salva, busca vazia ou valor não salvo');
}
reset(); fields['#T013_Valor_Total'] = undefined;
context.est004RatearFreteNFS(button, 'abaFrete');
check(requests === 1, 'Aba principal ainda não carregada impediu usar o valor salvo');
const form = fs.readFileSync(path.join(root, 'prog/est/est004/form.php'), 'utf8');
check(form.includes("array('title' => 'Frete NFS', 'value' => '4')"), 'Quinta opção ausente');
check(form.includes("'type' => 'pesquisaGrid', 'reject' => true"), 'Busca não usa o seletor com Enter');
console.log('OK: ' + count + ' verificações da interface; sem navegador ou acesso a banco.');
