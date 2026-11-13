// Exercita o envio real da aba Técnico em DOM simulado, sem aplicação/banco.
'use strict';
const fs = require('fs');
const vm = require('vm');
const assert = require('assert');
const source = fs.readFileSync(__dirname + '/../../prog/ven/ven003/form.php', 'utf8');
const start = source.indexOf("$('#{$formId} #ven003FormSalvarTipoDanfe')");
const code = source.slice(start, source.indexOf('</script>', start))
    .replace(/\{\$formId\}/g, 'teste').replace(/\{\$idNotaDanfe\}/g, '123');
const elements = {};
let click, request, callbacks, requests = 0;
const $ = selector => {
    if (typeof selector !== 'string') return selector;
    if (!elements[selector]) elements[selector] = {
        value: '', disabled: false, message: '',
        val() { return this.value; },
        button(action) { if (action) this.disabled = action === 'disable'; return this; },
        text(value) { this.message = value; return this; },
        // API disponível no jQuery 1.6.2 carregado pelo ERP. Sem .on().
        unbind(event) { assert.strictEqual(event, 'click.salvarTipoDanfe'); click = null; return this; },
        bind(event, handler) { assert.strictEqual(event, 'click.salvarTipoDanfe'); click = handler; return this; }
    };
    return elements[selector];
};
$.ajax = options => {
    requests++;
    request = options;
    callbacks = {};
    const chain = {
        done(fn) { callbacks.done = fn; return chain; },
        fail(fn) { callbacks.fail = fn; return chain; },
        always(fn) { callbacks.always = fn; return chain; }
    };
    return chain;
};
vm.runInNewContext(code, {$});
assert.strictEqual(requests, 0);
assert.strictEqual(typeof click, 'function');
// Recarregar o formulário reaplica somente seu evento, sem duplicá-lo.
vm.runInNewContext(code, {$});
for (const value of ['V', 'H']) {
    $('#teste #T007A_Tipo_DANFE').value = value;
    click.call($('#teste #ven003FormSalvarTipoDanfe'));
    assert.strictEqual(request.type, 'POST');
    assert.strictEqual(request.dataType, 'json');
    assert.strictEqual(request.data.T007_Id, 123);
    assert.strictEqual(request.data.tipoDanfe, value);
    assert.deepStrictEqual(Object.keys(request.data).sort(), ['T007_Id', 'tipoDanfe']);
    assert.strictEqual($('#teste #ven003FormSalvarTipoDanfe').disabled, true);
    assert.strictEqual($('#teste #ven003FormStatusTipoDanfe').message, 'Salvando...');
    callbacks.done({code: true, data: 'Tipo de DANFE salvo.'});
    callbacks.always();
    assert.strictEqual($('#teste #ven003FormStatusTipoDanfe').message, 'Tipo de DANFE salvo.');
    assert.strictEqual($('#teste #ven003FormSalvarTipoDanfe').disabled, false);
}
assert.strictEqual(requests, 2, 'Cada clique precisa enviar exatamente uma gravação');
click.call($('#teste #ven003FormSalvarTipoDanfe'));
callbacks.done({code: false, data: 'NF-e de outra empresa.'});
assert.strictEqual($('#teste #ven003FormStatusTipoDanfe').message, 'NF-e de outra empresa.');
callbacks.fail(); callbacks.always();
assert.strictEqual($('#teste #ven003FormSalvarTipoDanfe').disabled, false);
assert.ok(/Não foi possível salvar/.test($('#teste #ven003FormStatusTipoDanfe').message));
console.log('OK: interface envia apenas a preferência, mostra erros e libera o botão.');
