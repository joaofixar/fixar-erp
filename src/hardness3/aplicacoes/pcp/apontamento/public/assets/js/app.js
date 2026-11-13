'use strict';

function pcpApontamentoFormatoDuracao(total) {
    var horas = Math.floor(total / 3600);
    var minutos = Math.floor((total % 3600) / 60);
    var segundos = total % 60;
    return 'Tempo: ' + String(horas).padStart(2, '0') + ':' + String(minutos).padStart(2, '0') + ':' + String(segundos).padStart(2, '0');
}

document.addEventListener('DOMContentLoaded', function () {
    Array.prototype.forEach.call(document.querySelectorAll('.settings-card'), function (card) {
        if (/retrabalho/i.test(card.textContent)) card.hidden = true;
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-inline-op]'), function (alert) {
        var dialogTarget = document.querySelector('[data-pcp-op-dialog-feedback]');
        var dialog = document.querySelector('[data-pcp-op-dialog]');
        var target = dialogTarget || document.querySelector('[data-pcp-op-feedback]');
        if (target) target.appendChild(alert);
        if (dialog) dialog.showModal();
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-open-op-dialog]'), function (button) {
        button.addEventListener('click', function () {
            var dialog = document.querySelector('[data-pcp-op-dialog]');
            if (!dialog) return;
            dialog.showModal();
            var field = dialog.querySelector('input[name="op_id"]');
            if (field) field.focus();
        });
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-close-op-dialog]'), function (button) {
        button.addEventListener('click', function () { button.closest('dialog').close(); });
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-success-dialog]'), function (dialog) {
        dialog.showModal();
        var close = dialog.querySelector('[data-pcp-close-success]');
        if (close) close.addEventListener('click', function () { dialog.close(); });
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-finish-dialog]'), function (dialog) {
        dialog.showModal();
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-timer]'), function (timer) {
        var total = Number(timer.dataset.seconds || 0);
        if (timer.dataset.running !== '1') return;
        var card = timer.closest('.apontamento-card') || timer.closest('.overview-group') || timer.closest('.active-card');
        var startAt = null;
        if (card) {
            var startText = (card.querySelector('.apontamento-card__meta') || card.querySelector('dd') || card).textContent || '';
            var match = startText.match(/(\d{2})\/(\d{2})\/(\d{4})\s+(\d{2}):(\d{2})/);
            if (match) startAt = new Date(Number(match[3]), Number(match[2]) - 1, Number(match[1]), Number(match[4]), Number(match[5])).getTime();
        }
        window.setInterval(function () {
            if (startAt !== null && Date.now() < startAt) return;
            total += 1;
            timer.textContent = pcpApontamentoFormatoDuracao(total);
        }, 1000);
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-open-pause]'), function (button) {
        button.addEventListener('click', function () {
            var dialog = button.parentNode.querySelector('[data-pcp-pause-dialog]');
            dialog.showModal();
            dialog.querySelector('select').focus();
        });
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-close-pause]'), function (button) {
        button.addEventListener('click', function () { button.closest('dialog').close(); });
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-open-inactivation]'), function (button) {
        button.addEventListener('click', function () {
            var dialog = document.getElementById(button.dataset.pcpOpenInactivation);
            if (!dialog) return;
            dialog.showModal();
            var field = dialog.querySelector('textarea[name="justificativa"]');
            if (field) field.focus();
        });
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-close-inactivation]'), function (button) {
        button.addEventListener('click', function () { button.closest('dialog').close(); });
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-lookup-participant]'), function (button) {
        button.addEventListener('click', function () {
            var form = button.closest('form');
            var input = form.querySelector('input[name="matricula"]');
            var result = form.querySelector('[data-pcp-participant-result]');
            var submit = form.querySelector('[data-pcp-add-participant-submit]');
            submit.disabled = true; result.textContent = '';
            if (!input.value.trim()) { result.textContent = 'Informe a matrícula.'; return; }
            var url = new URL(window.location.href);
            url.searchParams.set('ajax', 'buscar_colaborador_apontamento');
            url.searchParams.set('matricula', input.value.trim());
            url.searchParams.set('pcp001_id', form.querySelector('input[name="pcp001_id"]').value);
            fetch(url.toString(), { credentials: 'same-origin' }).then(function (response) { return response.json(); }).then(function (data) {
                if (!data.item) { result.textContent = 'Colaborador ativo não encontrado para esta matrícula.'; return; }
                result.textContent = data.item.nome + ' · Matrícula: ' + data.item.matricula;
                result.classList.add('is-found'); submit.disabled = false;
            }).catch(function () { result.textContent = 'Não foi possível localizar o colaborador.'; });
        });
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-open-process]'), function (button) {
        button.addEventListener('click', function () {
            var dialog = document.getElementById(button.dataset.pcpOpenProcess);
            dialog.showModal();
            var field = dialog.querySelector('input[name="descricao"], input, select, button');
            if (field) field.focus();
        });
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-close-process]'), function (button) {
        button.addEventListener('click', function () { button.closest('dialog').close(); });
    });
    Array.prototype.forEach.call(document.querySelectorAll('.employee-dialog form[method="post"]'), function (form) {
        form.addEventListener('submit', function () { sessionStorage.setItem('pcp-open-dialog', form.closest('dialog').id); });
    });
    var reopenDialogId = sessionStorage.getItem('pcp-open-dialog');
    if (reopenDialogId) {
        sessionStorage.removeItem('pcp-open-dialog');
        var reopenDialog = document.getElementById(reopenDialogId);
        if (reopenDialog) reopenDialog.showModal();
    }
    Array.prototype.forEach.call(document.querySelectorAll('.linked-process'), function (row) {
        var button = row.querySelector('button[name="acao"]');
        if (button && button.textContent.trim() === 'Ativar') row.classList.add('linked-process--inactive');
        if (button && button.textContent.trim() === 'Inativar') button.classList.add('danger');
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-color]'), function (input) {
        var output = input.parentNode.querySelector('output');
        function showColor() { output.textContent = input.value.toUpperCase(); }
        input.addEventListener('input', showColor);
        showColor();
    });
    Array.prototype.forEach.call(document.querySelectorAll('details.include-box'), function (box, index) {
        var summary = box.querySelector('summary');
        var trigger = document.createElement('button');
        trigger.type = 'button';
        trigger.className = 'add-modal-trigger';
        trigger.textContent = summary.textContent.replace(/^＋\s*/, '+ ');
        var dialog = document.createElement('dialog');
        dialog.className = 'include-dialog';
        dialog.id = 'include-dialog-' + index;
        var heading = document.createElement('div');
        heading.className = 'include-dialog__head';
        heading.innerHTML = '<p class="eyebrow">Cadastro</p><h3>' + summary.textContent.replace(/^＋\s*/, '') + '</h3>';
        dialog.appendChild(heading);
        Array.prototype.slice.call(box.childNodes).forEach(function (node) { if (node !== summary) dialog.appendChild(node); });
        var close = document.createElement('button');
        close.type = 'button'; close.className = 'secondary include-dialog__close'; close.textContent = 'Cancelar';
        close.addEventListener('click', function () { dialog.close(); });
        var processForm = dialog.querySelector('.process-form');
        if (processForm) {
            var actions = document.createElement('div');
            actions.className = 'process-form__actions';
            var submit = processForm.querySelector('button[name="acao"]');
            actions.appendChild(close);
            if (submit) actions.appendChild(submit);
            processForm.appendChild(actions);
        } else dialog.appendChild(close);
        box.parentNode.replaceChild(trigger, box);
        document.body.appendChild(dialog);
        trigger.addEventListener('click', function () { dialog.showModal(); });
    });
    Array.prototype.forEach.call(document.querySelectorAll('input[name="incluir"]'), function (input) {
        var form = input.closest('form');
        var results = document.createElement('div');
        results.className = 'employee-suggestions';
        input.parentNode.appendChild(results);
        var timer = null;
        var csrf = '';
        function clear() { results.innerHTML = ''; }
        form.addEventListener('submit', function (event) {
            event.preventDefault();
            input.focus();
        });
        function selectEmployee(item) {
            clear();
            var previous = form.parentNode.querySelector('.employee-selected-form');
            if (previous) previous.remove();
            var selected = document.createElement('form');
            selected.method = 'post';
            selected.className = 'employee-selected-form';
            selected.innerHTML = '<input type="hidden" name="csrf"><input type="hidden" name="d109_id"><strong></strong><span class="tag tag--ok">D109 ativo</span><label>Matrícula PCP<input name="matricula" maxlength="250" required autofocus></label><button name="acao" value="create_employee">Incluir colaborador</button><button type="button" class="secondary">Trocar</button>';
            selected.querySelector('input[name="csrf"]').value = csrf;
            selected.querySelector('input[name="d109_id"]').value = item.id;
            selected.querySelector('strong').textContent = item.nome + ' · #' + item.id;
            selected.querySelector('button.secondary').addEventListener('click', function () { selected.remove(); input.value = ''; input.focus(); });
            form.insertAdjacentElement('afterend', selected);
            selected.querySelector('input[name="matricula"]').focus();
        }
        input.addEventListener('input', function () {
            window.clearTimeout(timer);
            var term = input.value.trim();
            if (term.length < 2) { clear(); return; }
            timer = window.setTimeout(function () {
                var url = new URL(window.location.href);
                url.searchParams.set('ajax', 'buscar_d109');
                url.searchParams.set('termo', term);
                fetch(url.toString(), { credentials: 'same-origin' }).then(function (response) { return response.json(); }).then(function (data) {
                    csrf = data.csrf || csrf;
                    clear();
                    (data.items || []).forEach(function (item) {
                        var button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'employee-suggestion' + (item.status === 'S' ? '' : ' is-inactive');
                        button.textContent = item.nome + ' · #' + item.id + (item.status === 'S' ? '' : ' · funcionário inativo');
                        button.disabled = item.status !== 'S';
                        button.addEventListener('click', function () { selectEmployee(item); });
                        results.appendChild(button);
                    });
                    if (!(data.items || []).length) results.textContent = 'Nenhum funcionário disponível.';
                }).catch(clear);
            }, 220);
        });
    });
    Array.prototype.forEach.call(document.querySelectorAll('.event-participant'), function (section) {
        var name = section.querySelector('h4');
        if (!name) return;
        Array.prototype.forEach.call(section.querySelectorAll('tbody tr'), function (row) {
            var typeCell = row.cells[1];
            var code = typeCell ? typeCell.textContent.trim() : '';
            if (!/^C[IPRF]$/.test(code)) return;
            var names = {CI:'Início', CP:'Pausa', CR:'Retomada', CF:'Finalização'};
            row.classList.add('event-row--cancelled');
            typeCell.textContent = 'Cancelado · ' + names[code];
            Array.prototype.forEach.call(row.querySelectorAll('button,dialog'), function (item) { item.remove(); });
            if (row.cells[3]) row.cells[3].textContent = '—';
        });
        var firstEvent = section.querySelector('tbody tr td:first-child');
        var eventStart = firstEvent ? firstEvent.textContent.trim() : '';
        var participantRow = Array.prototype.find.call(document.querySelectorAll('.management-detail > h3 + .management-table tbody tr'), function (row) {
            return row.cells.length && row.cells[0].textContent.trim() === name.textContent.trim() && row.cells[1].textContent.trim() === eventStart;
        });
        if (!participantRow) return;
        if (participantRow.cells[3] && participantRow.cells[3].textContent.trim() === 'Inativado') {
            participantRow.classList.add('participant-row--inactive');
            section.classList.add('event-participant--inactive');
            var status = document.createElement('span'); status.className = 'participant-removed-tag'; status.textContent = 'Removido deste apontamento';
            name.insertAdjacentElement('afterend', status);
            Array.prototype.forEach.call(section.querySelectorAll('button,dialog'), function (item) { item.remove(); });
            return;
        }
        var button = participantRow.querySelector('[data-pcp-open-inactivation^="edit-participant-"]');
        if (!button) return;
        var participantId = button.dataset.pcpOpenInactivation.replace('edit-participant-', '');
        button.dataset.pcpOpenInactivation = 'inactivate-participant-' + participantId;
        var confirmation = document.getElementById('inactivate-participant-' + participantId);
        if (confirmation) section.appendChild(confirmation);
        button.textContent = 'Remover deste apontamento';
        button.classList.remove('secondary');
        button.classList.add('danger', 'event-participant__inactivate');
        var menu = document.createElement('details');
        menu.className = 'event-participant__actions';
        var summary = document.createElement('summary');
        summary.textContent = 'Ações';
        menu.appendChild(summary);
        var add = document.createElement('button');
        add.type = 'button'; add.className = 'event-participant__add'; add.textContent = 'Adicionar evento';
        add.addEventListener('click', function () {
            menu.removeAttribute('open');
            var last = section.querySelector('tbody tr:not(.event-row--cancelled):last-child td:nth-child(2)');
            var lastType = last ? last.textContent.trim() : '';
            var types = lastType === 'Pausa' ? '<option value="R">Retomada</option><option value="F">Finalização</option>' : '<option value="P">Pausa</option><option value="F">Finalização</option>';
            var dialog = document.createElement('dialog'); dialog.className = 'inactivation-dialog';
            dialog.innerHTML = '<form method="post"><div class="inactivation-dialog__head"><p class="eyebrow">Novo evento</p><h3>' + name.textContent.trim() + '</h3></div><input type="hidden" name="csrf"><input type="hidden" name="pcp001_id"><input type="hidden" name="pcp002_id" value="' + participantId + '"><input type="hidden" name="acao_edicao" value="incluir_evento"><label>Evento<select name="tipo_evento">' + types + '</select></label><label>Data e hora<input type="datetime-local" name="data_hora" required></label><label class="event-pause-reason">Motivo da pausa<select name="smt012_id"><option value="">Carregando...</option></select></label><label>Justificativa<textarea name="justificativa" maxlength="500" required></textarea></label><div class="inactivation-dialog__actions"><button type="button" class="secondary">Cancelar</button><button>Adicionar evento</button></div></form>';
            var source = confirmation || section.querySelector('input[name="pcp001_id"]');
            dialog.querySelector('input[name="csrf"]').value = document.querySelector('input[name="csrf"]').value;
            dialog.querySelector('input[name="pcp001_id"]').value = source.querySelector('input[name="pcp001_id"]').value;
            var now = new Date();
            var localNow = new Date(now.getTime() - now.getTimezoneOffset() * 60000);
            dialog.querySelector('input[name="data_hora"]').value = localNow.toISOString().slice(0,16);
            dialog.querySelector('button.secondary').addEventListener('click', function () { dialog.close(); });
            dialog.querySelector('select[name="tipo_evento"]').addEventListener('change', function (event) { dialog.querySelector('.event-pause-reason').style.display = event.target.value === 'P' ? '' : 'none'; });
            section.appendChild(dialog); dialog.showModal();
            var url = new URL(window.location.href); url.searchParams.set('ajax', 'motivos_pausa_apontamento');
            fetch(url.toString(), {credentials:'same-origin'}).then(function (r) { return r.json(); }).then(function (data) { var select = dialog.querySelector('select[name="smt012_id"]'); select.innerHTML = '<option value="">Selecione</option>'; (data.items || []).forEach(function (item) { var option = document.createElement('option'); option.value = item.id; option.textContent = item.descricao; select.appendChild(option); }); });
        });
        menu.appendChild(add);
        menu.appendChild(button);
        button.addEventListener('click', function () { menu.removeAttribute('open'); });
        name.insertAdjacentElement('afterend', menu);
        Array.prototype.forEach.call(section.querySelectorAll('[data-pcp-open-inactivation^="edit-event-"]'), function (editButton) {
            var editDialog = document.getElementById(editButton.dataset.pcpOpenInactivation);
            if (!editDialog || editDialog.querySelector('h3').textContent.trim() === 'Início') return;
            var cancel = document.createElement('button'); cancel.type = 'button'; cancel.className = 'danger participant-edit-button'; cancel.textContent = editDialog.querySelector('h3').textContent.trim() === 'Pausa' || editDialog.querySelector('h3').textContent.trim() === 'Retomada' ? 'Remover pausa' : 'Remover finalização';
            cancel.addEventListener('click', function () {
                var dialog = document.createElement('dialog'); dialog.className = 'inactivation-dialog';
                dialog.innerHTML = '<form method="post"><div class="inactivation-dialog__head"><p class="eyebrow">Confirmação necessária</p><h3>' + cancel.textContent + '?</h3><p>O evento será inativado e o histórico será preservado.</p></div><input type="hidden" name="csrf"><input type="hidden" name="pcp001_id"><input type="hidden" name="pcp002_id"><input type="hidden" name="pcp003_id"><input type="hidden" name="acao_edicao" value="cancelar_evento"><label>Justificativa<textarea name="justificativa" maxlength="500" required></textarea></label><div class="inactivation-dialog__actions"><button type="button" class="secondary">Cancelar</button><button class="danger">Confirmar remoção</button></div></form>';
                ['csrf','pcp001_id','pcp002_id','pcp003_id'].forEach(function (field) { dialog.querySelector('[name="' + field + '"]').value = editDialog.querySelector('[name="' + field + '"]').value; });
                dialog.querySelector('button.secondary').addEventListener('click', function () { dialog.close(); }); section.appendChild(dialog); dialog.showModal();
            });
            editDialog.querySelector('.inactivation-dialog__actions').insertAdjacentElement('afterbegin', cancel);
        });
    });
    Array.prototype.forEach.call(document.querySelectorAll('.management-detail > h3 + .management-table'), function (table) {
        var removedRows = table.querySelectorAll('.participant-row--inactive');
        var removedBlocks = document.querySelectorAll('.event-participant--inactive');
        if (!removedRows.length) return;
        Array.prototype.forEach.call(removedRows, function (row) { row.classList.add('is-hidden'); });
        Array.prototype.forEach.call(removedBlocks, function (block) { block.classList.add('is-hidden'); });
        var control = document.createElement('button');
        control.type = 'button'; control.className = 'participant-removed-toggle';
        control.textContent = 'Exibir ' + removedRows.length + (removedRows.length === 1 ? ' participante removido' : ' participantes removidos');
        control.addEventListener('click', function () {
            var visible = control.dataset.visible === '1';
            Array.prototype.forEach.call(removedRows, function (row) { row.classList.toggle('is-hidden', visible); });
            Array.prototype.forEach.call(removedBlocks, function (block) { block.classList.toggle('is-hidden', visible); });
            control.dataset.visible = visible ? '0' : '1';
            control.textContent = (visible ? 'Exibir ' : 'Ocultar ') + removedRows.length + (removedRows.length === 1 ? ' participante removido' : ' participantes removidos');
        });
        var heading = table.previousElementSibling;
        if (heading) { heading.classList.add('participants-heading'); heading.appendChild(control); }
        else table.insertAdjacentElement('beforebegin', control);
    });
    Array.prototype.forEach.call(document.querySelectorAll('.apontamento-card__people'), function (people) {
        Array.prototype.forEach.call(people.querySelectorAll('.participant-chip'), function (chip) {
            if (/·\s*Inativado$/.test(chip.textContent.trim())) chip.remove();
        });
        var count = people.querySelectorAll('.participant-chip').length;
        var title = people.querySelector('strong');
        if (title) title.textContent = 'Participantes · ' + count;
    });
    Array.prototype.forEach.call(document.querySelectorAll('.participant-row--inactive td:nth-child(4)'), function (status) {
        status.textContent = 'Removido deste apontamento';
    });
    var auditoriasPorEvento = {};
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-audit-event-id]'), function (item) {
        var eventId = item.dataset.pcpAuditEventId;
        if (!auditoriasPorEvento[eventId]) auditoriasPorEvento[eventId] = [];
        auditoriasPorEvento[eventId].push(item);
    });
    function abrirObservacoesEvento(registros, row) {
        var dialog = document.createElement('dialog'); dialog.className = 'inactivation-dialog edit-history__dialog';
        var head = document.createElement('div'); head.className = 'inactivation-dialog__head';
        var eyebrow = document.createElement('p'); eyebrow.className = 'eyebrow'; eyebrow.textContent = 'Histórico de alterações do evento';
        var title = document.createElement('h3'); title.textContent = row.children[1].textContent.trim();
        head.appendChild(eyebrow); head.appendChild(title); dialog.appendChild(head);
        var body = document.createElement('div'); body.className = 'edit-history__dialog-body';
        registros.forEach(function (registro) {
            var block = document.createElement('div'); block.className = 'event-observation';
            var label = document.createElement('strong'); label.textContent = registro.querySelector('strong').textContent;
            var meta = document.createElement('small'); meta.textContent = registro.querySelector('span').textContent;
            var text = document.createElement('p'); var sourceDialog = document.getElementById(registro.querySelector('[data-pcp-open-inactivation]').dataset.pcpOpenInactivation); text.textContent = sourceDialog ? sourceDialog.querySelector('.edit-history__dialog-body > p').textContent : '';
            block.appendChild(label); block.appendChild(meta); block.appendChild(text); body.appendChild(block);
        });
        var footer = document.createElement('div'); footer.className = 'inactivation-dialog__actions';
        var spacer = document.createElement('span'); var close = document.createElement('button'); close.type = 'button'; close.className = 'secondary'; close.textContent = 'Fechar'; close.addEventListener('click', function () { dialog.close(); }); footer.appendChild(spacer); footer.appendChild(close); body.appendChild(footer); dialog.appendChild(body); document.body.appendChild(dialog); dialog.showModal();
        dialog.addEventListener('close', function () { dialog.remove(); });
    }
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-open-inactivation^="edit-event-"]'), function (editButton) {
        var eventId = editButton.dataset.pcpOpenInactivation.replace('edit-event-', '');
        var registros = auditoriasPorEvento[eventId] || [];
        if (!registros.length) return;
        var row = editButton.closest('tr');
        var actions = row && row.lastElementChild;
        if (!actions) return;
        var note = document.createElement('button');
        note.type = 'button'; note.className = 'event-observation-button';
        note.textContent = 'Hist. alt.';
        note.title = 'Ver histórico de alterações deste evento';
        note.addEventListener('click', function () { abrirObservacoesEvento(registros, row); });
        actions.appendChild(note);
        row.dataset.pcpAuditLinked = '1';
    });
    var eventLabels = {I:'Início',P:'Pausa',R:'Retomada',F:'Finalização'};
    var auditoriasDeEventosSemEdicao = {};
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-audit-event-id]'), function (registro) {
        var id = registro.dataset.pcpAuditEventId;
        if (!auditoriasDeEventosSemEdicao[id]) auditoriasDeEventosSemEdicao[id] = [];
        auditoriasDeEventosSemEdicao[id].push(registro);
    });
    Object.keys(auditoriasDeEventosSemEdicao).forEach(function (id) {
        var registros = auditoriasDeEventosSemEdicao[id];
        var registro = registros[0];
        var date = registro.dataset.pcpAuditEventDate;
        var type = eventLabels[registro.dataset.pcpAuditEventType] || '';
        if (!date || !type) return;
        var matchingRow = null;
        Array.prototype.forEach.call(document.querySelectorAll('.event-participant tbody tr'), function (row) {
            if (matchingRow || row.dataset.pcpAuditLinked === '1') return;
            var cells = row.children;
            if (cells.length > 1 && cells[0].textContent.trim() === date && cells[1].textContent.indexOf(type) !== -1) matchingRow = row;
        });
        if (!matchingRow) return;
        var actions = matchingRow.lastElementChild;
        if (!actions) return;
        var note = document.createElement('button'); note.type = 'button'; note.className = 'event-observation-button'; note.textContent = 'Hist. alt.'; note.title = 'Ver histórico de alterações deste evento';
        note.addEventListener('click', function () { abrirObservacoesEvento(registros, matchingRow); });
        actions.appendChild(note);
    });
    Array.prototype.forEach.call(document.querySelectorAll('[data-pcp-group-history]'), function (history) {
        Array.prototype.forEach.call(history.querySelectorAll('[data-pcp-audit-participant-id]'), function (item) { item.hidden = true; });
        if (!history.querySelector('.edit-history__item:not([hidden])')) history.hidden = true;
    });
});
