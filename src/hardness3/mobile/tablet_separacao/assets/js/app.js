document.addEventListener('DOMContentLoaded', function () {
    function closeConfirmModal() {
        var existingModal = document.getElementById('tablet-confirm-modal');
        if (existingModal) {
            existingModal.remove();
        }
    }

    function openConfirmModal(message, onConfirm, onCancel) {
        closeConfirmModal();

        var modal = document.createElement('div');
        modal.className = 'barcode-modal label-print-modal';
        modal.id = 'tablet-confirm-modal';
        modal.innerHTML = [
            '<div class="barcode-modal__overlay" data-confirm-close="true"></div>',
            '<div class="barcode-modal__panel label-print-modal__panel" role="dialog" aria-modal="true" aria-label="Confirmar ação">',
            '  <h3 class="barcode-modal__title">Confirmar ação</h3>',
            '  <p class="barcode-modal__text">' + message + '</p>',
            '  <div class="barcode-modal__actions">',
            '    <button type="button" class="button button--primary" data-confirm-accept="true">Sim</button>',
            '    <button type="button" class="button button--secondary" data-confirm-cancel="true">Não</button>',
            '  </div>',
            '</div>'
        ].join('');

        document.body.appendChild(modal);

        function handleCancel() {
            closeConfirmModal();
            if (typeof onCancel === 'function') {
                onCancel();
            }
        }

        modal.querySelectorAll('[data-confirm-close="true"]').forEach(function (node) {
            node.addEventListener('click', handleCancel);
        });

        var cancelButton = modal.querySelector('[data-confirm-cancel="true"]');
        if (cancelButton) {
            cancelButton.addEventListener('click', handleCancel);
        }

        var confirmButton = modal.querySelector('[data-confirm-accept="true"]');
        if (confirmButton) {
            confirmButton.addEventListener('click', function () {
                closeConfirmModal();
                if (typeof onConfirm === 'function') {
                    onConfirm();
                }
            });
        }
    }

    var autoRefreshSeconds = parseInt(document.body.getAttribute('data-auto-refresh') || '0', 10);
    if (autoRefreshSeconds > 0) {
        window.setTimeout(function () {
            var activeElement = document.activeElement;
            var isEditingField = activeElement && (
                activeElement.tagName === 'INPUT' ||
                activeElement.tagName === 'TEXTAREA' ||
                activeElement.tagName === 'SELECT'
            );
            var hasOpenModal = document.getElementById('barcode-reader-modal') || document.getElementById('label-print-modal');
            if (!isEditingField && !hasOpenModal) {
                window.location.reload();
            }
        }, autoRefreshSeconds * 1000);
    }

    var confirmForms = document.querySelectorAll('form[data-confirm]');
    confirmForms.forEach(function (form) {
        form.addEventListener('submit', function (event) {
            var message = form.getAttribute('data-confirm');
            if (!message) {
                return;
            }

            if (form.getAttribute('data-confirm-modal') === 'true') {
                if (form.getAttribute('data-confirm-approved') === 'true') {
                    form.removeAttribute('data-confirm-approved');
                    return;
                }

                event.preventDefault();
                openConfirmModal(message, function () {
                    form.setAttribute('data-confirm-approved', 'true');
                    if (typeof form.requestSubmit === 'function') {
                        form.requestSubmit();
                        return;
                    }
                    form.submit();
                });
                return;
            }

            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    var autoSubmitSelects = document.querySelectorAll('[data-auto-submit="true"]');
    autoSubmitSelects.forEach(function (select) {
        select.addEventListener('change', function () {
            var form = select.closest('form');
            if (form) {
                form.requestSubmit();
            }
        });
    });

    var scanInput = document.querySelector('.scan-input');
    if (scanInput) {
        var scanForm = scanInput.closest('form');
        var quantityFieldInitial = scanForm ? scanForm.querySelector('input[name="quantity"]') : null;
        if (quantityFieldInitial && quantityFieldInitial.value.trim() === '') {
            quantityFieldInitial.focus();
        } else {
            scanInput.focus();
        }
        var allowTyping = scanInput.getAttribute('data-allow-typing') === 'true';
        var scanBuffer = '';
        var scanTimer = null;

        function flushScanBuffer(submitAfter) {
            if (scanBuffer.trim() !== '') {
                scanInput.value = scanBuffer.trim();
            }
            scanBuffer = '';
            if (scanTimer) {
                window.clearTimeout(scanTimer);
                scanTimer = null;
            }

            if (submitAfter) {
                var form = scanInput.closest('form');
                if (form) {
                    var quantityField = form.querySelector('input[name="quantity"]');
                    if (quantityField && quantityField.value.trim() === '') {
                        quantityField.focus();
                    } else {
                        form.requestSubmit();
                    }
                }
            }
        }

        if (!allowTyping) {
            scanInput.addEventListener('keydown', function (event) {
                event.preventDefault();
            });
        }

        document.addEventListener('keydown', function (event) {
            var activeElement = document.activeElement;
            if (!scanInput.closest('form')) {
                return;
            }

            if (allowTyping) {
                return;
            }

            if (activeElement && activeElement.name === 'quantity') {
                return;
            }

            if (event.key === 'Tab' || event.key === 'Shift') {
                return;
            }

            if (event.key === 'Enter') {
                event.preventDefault();
                flushScanBuffer(true);
                return;
            }

            if (event.key === 'Backspace') {
                event.preventDefault();
                scanBuffer = scanBuffer.slice(0, -1);
                scanInput.value = scanBuffer;
                return;
            }

            if (event.key.length === 1 && !event.ctrlKey && !event.metaKey && !event.altKey) {
                event.preventDefault();
                scanBuffer += event.key;
                scanInput.value = scanBuffer;

                if (scanTimer) {
                    window.clearTimeout(scanTimer);
                }
                scanTimer = window.setTimeout(function () {
                    flushScanBuffer(false);
                }, 120);
            }
        });
    }

    var cameraTrigger = document.querySelector('[data-barcode-camera-open="true"]');
    if (cameraTrigger) {
        cameraTrigger.addEventListener('click', function () {
            if (typeof window.Html5Qrcode === 'undefined') {
                window.alert('Leitura por câmera indisponível no momento.');
                return;
            }

            var inputName = cameraTrigger.getAttribute('data-target-input');
            var targetInput = document.querySelector('[name="' + inputName + '"]');
            if (!targetInput) {
                return;
            }

            var modalId = 'barcode-reader-modal';
            var readerId = 'barcode-reader-box';
            var existingModal = document.getElementById(modalId);
            if (existingModal) {
                existingModal.remove();
            }

            var modal = document.createElement('div');
            modal.className = 'barcode-modal';
            modal.id = modalId;
            modal.innerHTML = [
                '<div class="barcode-modal__overlay" data-barcode-close="true"></div>',
                '<div class="barcode-modal__panel" role="dialog" aria-modal="true" aria-label="Leitura por câmera">',
                '  <h3 class="barcode-modal__title">Ler locação pela câmera</h3>',
                '  <p class="barcode-modal__text">Aponte a câmera para a etiqueta da locação.</p>',
                '  <div id="' + readerId + '" class="barcode-modal__reader"></div>',
                '  <p class="barcode-modal__message" data-barcode-message hidden></p>',
                '  <div class="barcode-modal__actions">',
                '    <button type="button" class="button button--secondary" data-barcode-close="true">Fechar</button>',
                '  </div>',
                '</div>'
            ].join('');

            document.body.appendChild(modal);

            var messageNode = modal.querySelector('[data-barcode-message]');
            var scanner = new window.Html5Qrcode(readerId);

            function stopScanner() {
                scanner.stop().catch(function () {
                    return null;
                }).finally(function () {
                    modal.remove();
                    targetInput.focus();
                });
            }

            modal.querySelectorAll('[data-barcode-close="true"]').forEach(function (node) {
                node.addEventListener('click', function () {
                    stopScanner();
                });
            });

            scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                function (decodedText) {
                    targetInput.value = decodedText;
                    stopScanner();
                }
            ).catch(function (error) {
                if (messageNode) {
                    messageNode.hidden = false;
                    messageNode.textContent = 'Não foi possível iniciar a câmera. ' + error;
                }
            });
        });
    }

    var labelPrintTriggers = document.querySelectorAll('[data-label-print="true"]');
    if (labelPrintTriggers.length > 0) {
        var labelOptionsNode = document.getElementById('label-print-options') || document.getElementById('item-label-options');
        var labelOptionsByScope = {};

        if (labelOptionsNode) {
            try {
                labelOptionsByScope = JSON.parse(labelOptionsNode.textContent || '{}');
            } catch (error) {
                labelOptionsByScope = {};
            }
        }

        if (Array.isArray(labelOptionsByScope)) {
            labelOptionsByScope = {
                item: labelOptionsByScope
            };
        }

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function closeLabelModal() {
            var existingModal = document.getElementById('label-print-modal');
            if (existingModal) {
                existingModal.remove();
            }
        }

        function isTouchDevice() {
            var userAgent = navigator.userAgent || '';
            var platform = navigator.platform || '';
            var hasTouch = (navigator.maxTouchPoints && navigator.maxTouchPoints > 0) ||
                ('ontouchstart' in window);
            var coarsePointer = window.matchMedia && window.matchMedia('(pointer: coarse)').matches;
            var mobileOrTabletUserAgent = /Android|iPad|iPhone|iPod|Mobile|Tablet/i.test(userAgent);
            var iPadDesktopMode = /Macintosh/i.test(platform) && hasTouch;
            var viewportWidth = Math.max(window.innerWidth || 0, window.innerHeight || 0);
            var tabletSizedScreen = viewportWidth > 0 && viewportWidth <= 1280;
            var tabletSizedTouchScreen = hasTouch && tabletSizedScreen;

            return coarsePointer || mobileOrTabletUserAgent || iPadDesktopMode || tabletSizedTouchScreen;
        }

        function shouldUseServerSideLabelGeneration() {
            var viewportWidth = Math.max(window.innerWidth || 0, window.innerHeight || 0);

            return isTouchDevice() || (viewportWidth > 0 && viewportWidth <= 1280);
        }

        function showGeneratedLabelLink(url, messageNode) {
            var modal = messageNode ? messageNode.closest('.label-print-modal') : null;
            var actionsNode = modal ? modal.querySelector('.barcode-modal__actions') : null;
            var linkNode = modal ? modal.querySelector('[data-generated-label-link]') : null;

            if (messageNode) {
                messageNode.hidden = false;
                messageNode.textContent = 'Etiqueta gerada. Toque em Abrir PDF para visualizar/imprimir.';
            }

            if (!actionsNode) {
                window.location.href = url;
                return;
            }

            if (!linkNode) {
                linkNode = document.createElement('a');
                linkNode.className = 'button button--primary';
                linkNode.setAttribute('target', '_blank');
                linkNode.setAttribute('rel', 'noopener');
                linkNode.setAttribute('data-generated-label-link', 'true');
                linkNode.textContent = 'Abrir PDF';
                actionsNode.insertBefore(linkNode, actionsNode.firstChild);
            }

            linkNode.href = url;
            modal.querySelectorAll('[data-label-option]').forEach(function (button) {
                button.disabled = false;
                button.innerHTML = button.getAttribute('data-label-html') || button.innerHTML;
            });
        }

        function requestLabelPdfServerSide(model, idType, id, sql, messageNode) {
            var params = new URLSearchParams({
                model: model,
                id_type: idType,
                id: id,
                sql: sql || ''
            });

            var controller = window.AbortController ? new AbortController() : null;
            var timeoutId = window.setTimeout(function () {
                if (controller) {
                    controller.abort();
                }
            }, 90000);
            var requestOptions = {
                credentials: 'same-origin'
            };

            if (controller) {
                requestOptions.signal = controller.signal;
            }

            return fetch('gerar_etiqueta.php?' + params.toString(), requestOptions)
                .then(function (response) {
                    return response.json();
                })
                .then(function (response) {
                    if (response && response.ok && response.url) {
                        return response.url;
                    }

                    throw new Error((response && response.message) || 'Não foi possível gerar a etiqueta.');
                })
                .catch(function (error) {
                    if (error && error.name === 'AbortError') {
                        throw new Error('Tempo esgotado aguardando o PDF.');
                    }

                    throw error;
                })
                .finally(function () {
                    window.clearTimeout(timeoutId);
                });
        }

        function openServerSideLabelInNewWindow(model, idType, id, sql) {
            var form = document.createElement('form');
            form.method = 'GET';
            form.action = 'gerar_etiqueta.php';
            form.target = '_blank';
            form.style.display = 'none';

            [
                { name: 'model', value: model },
                { name: 'id_type', value: idType },
                { name: 'id', value: id },
                { name: 'sql', value: sql || '' },
                { name: 'redirect', value: '1' }
            ].forEach(function (field) {
                var input = document.createElement('input');
                input.type = 'hidden';
                input.name = field.name;
                input.value = field.value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
            form.remove();
        }

        function openSingleLabelConfirmModal(onConfirm, onCancel) {
            closeLabelModal();

            var modal = document.createElement('div');
            modal.className = 'barcode-modal label-print-modal';
            modal.id = 'label-print-modal';
            modal.innerHTML = [
                '<div class="barcode-modal__overlay" data-label-close="true"></div>',
                '<div class="barcode-modal__panel label-print-modal__panel" role="dialog" aria-modal="true" aria-label="Imprimir etiqueta">',
                '  <h3 class="barcode-modal__title">Imprimir etiqueta</h3>',
                '  <p class="barcode-modal__text">Deseja imprimir a etiqueta?</p>',
                '  <div class="barcode-modal__actions">',
                '    <button type="button" class="button button--primary" data-label-confirm-single="true">Sim</button>',
                '    <button type="button" class="button button--secondary" data-label-cancel-single="true">Não</button>',
                '  </div>',
                '</div>'
            ].join('');

            document.body.appendChild(modal);

            function handleCancel() {
                closeLabelModal();
                if (typeof onCancel === 'function') {
                    onCancel();
                }
            }

            var confirmButton = modal.querySelector('[data-label-confirm-single="true"]');
            var cancelButton = modal.querySelector('[data-label-cancel-single="true"]');

            modal.querySelectorAll('[data-label-close="true"]').forEach(function (node) {
                node.addEventListener('click', handleCancel);
            });

            if (cancelButton) {
                cancelButton.addEventListener('click', handleCancel);
            }

            if (confirmButton) {
                confirmButton.addEventListener('click', function () {
                    closeLabelModal();
                    if (typeof onConfirm === 'function') {
                        onConfirm();
                    }
                });
            }
        }

        function requestLabelPdf(model, idType, id, sql, popupWindow, messageNode, onSuccess) {
            var callbackName = 'tabletSeparacaoLabelCallback' + Date.now();
            var script = document.createElement('script');
            var completed = false;
            var timeoutId = null;
            var link = '/sistema/personalizacao/tela/' + model + '/?ajax=true&' + idType + '=' + encodeURIComponent(id) + '&sql=' + encodeURIComponent(sql || '');
            var params = new URLSearchParams({
                ajax: 'true',
                acaoId: model,
                link: link,
                callback: callbackName
            });

            function finishWithError(message) {
                completed = true;
                if (timeoutId) {
                    window.clearTimeout(timeoutId);
                }
                if (popupWindow && !popupWindow.closed) {
                    popupWindow.close();
                }
                if (messageNode) {
                    messageNode.hidden = false;
                    messageNode.textContent = message || 'Não foi possível gerar a etiqueta.';
                    var modal = messageNode.closest('.label-print-modal');
                    if (modal) {
                        modal.querySelectorAll('[data-label-option]').forEach(function (button) {
                            button.disabled = false;
                            button.innerHTML = button.getAttribute('data-label-html') || button.innerHTML;
                        });
                    }
                }
            }

            window[callbackName] = function (response) {
                if (completed) {
                    return;
                }
                completed = true;
                if (timeoutId) {
                    window.clearTimeout(timeoutId);
                }
                script.remove();
                delete window[callbackName];

                if (response && response.data) {
                    if (typeof onSuccess === 'function') {
                        onSuccess(response.data);
                    }
                    if (popupWindow && !popupWindow.closed) {
                        popupWindow.location.href = response.data;
                        closeLabelModal();
                    } else {
                        showGeneratedLabelLink(response.data, messageNode);
                    }
                    return;
                }

                finishWithError('O gerador respondeu, mas não enviou o link do PDF.');
            };

            script.onerror = function () {
                if (completed) {
                    return;
                }
                script.remove();
                delete window[callbackName];
                finishWithError('Não foi possível carregar o gerador de PDF no celular.');
            };

            script.src = '/sistema/personalizacaoLowCode/grid_func-ajax/telaGerarPdf/?' + params.toString();
            document.body.appendChild(script);
            timeoutId = window.setTimeout(function () {
                if (completed) {
                    return;
                }
                script.remove();
                delete window[callbackName];
                finishWithError('Tempo esgotado aguardando o PDF.');
            }, 60000);
        }

        function openLabelModal(labelPrintTrigger) {
            closeLabelModal();

            var labelScope = labelPrintTrigger.getAttribute('data-label-scope') || 'item';
            var labelId = labelPrintTrigger.getAttribute('data-label-id') || labelPrintTrigger.getAttribute('data-item-id') || '';
            var labelOptions = Array.isArray(labelOptionsByScope[labelScope]) ? labelOptionsByScope[labelScope] : [];
            var sql = labelPrintTrigger.getAttribute('data-label-sql') || '';
            var returnUrl = labelPrintTrigger.getAttribute('data-label-return-url') || '';
            var modal = document.createElement('div');
            var optionButtons = [];
            var optionIndex = 0;
            var availableOptions = [];

            labelOptions.forEach(function (group) {
                if (!group || !Array.isArray(group.opcoes)) {
                    return;
                }

                group.opcoes.forEach(function (option) {
                    if (!option || option.mostrar === false || !option.modelo || !option.id_type) {
                        return;
                    }

                    availableOptions.push(option);

                    var groupName = group.nome || 'Etiqueta';
                    var optionName = option.nome || 'Opção';
                    optionButtons.push(
                        '<button type="button" class="button ' + (optionIndex % 2 === 0 ? 'button--primary' : 'button--secondary') + '" data-label-option="' + optionIndex + '">' +
                        escapeHtml(groupName + ' - ' + optionName) +
                        '</button>'
                    );
                    optionIndex += 1;
                });
            });

            if (optionButtons.length === 0) {
                window.alert('Nenhuma etiqueta configurada para este item.');
                return;
            }

            if (availableOptions.length === 1) {
                var selectedOption = availableOptions[0];
                var useServerSideLabelGeneration = shouldUseServerSideLabelGeneration();
                var navigateAfterPrint = function () {
                    if (returnUrl) {
                        window.location.href = returnUrl;
                    }
                };
                openSingleLabelConfirmModal(function () {
                    if (useServerSideLabelGeneration) {
                        openServerSideLabelInNewWindow(selectedOption.modelo, selectedOption.id_type, labelId, sql);
                        navigateAfterPrint();
                        return;
                    }

                    requestLabelPdf(selectedOption.modelo, selectedOption.id_type, labelId, sql, window.open('', '_blank'), null, navigateAfterPrint);
                }, function () {
                    navigateAfterPrint();
                });
                return;
            }

            modal.className = 'barcode-modal label-print-modal';
            modal.id = 'label-print-modal';
            modal.innerHTML = [
                '<div class="barcode-modal__overlay" data-label-close="true"></div>',
                '<div class="barcode-modal__panel label-print-modal__panel" role="dialog" aria-modal="true" aria-label="Imprimir etiqueta">',
                '  <h3 class="barcode-modal__title">Imprimir etiqueta</h3>',
                '  <p class="barcode-modal__text">Escolha o modelo para gerar a etiqueta em PDF.</p>',
                '  <div class="label-print-modal__actions">',
                optionButtons.join(''),
                '  </div>',
                '  <p class="barcode-modal__message" data-label-message hidden></p>',
                '  <div class="barcode-modal__actions">',
                '    <button type="button" class="button button--secondary" data-label-close="true">Cancelar</button>',
                '  </div>',
                '</div>'
            ].join('');

            document.body.appendChild(modal);

            modal.querySelectorAll('[data-label-close="true"]').forEach(function (node) {
                node.addEventListener('click', closeLabelModal);
            });

            modal.querySelectorAll('[data-label-option]').forEach(function (button) {
                button.setAttribute('data-label-html', button.innerHTML);
                button.addEventListener('click', function () {
                    var selectedOption = findLabelOption(parseInt(button.getAttribute('data-label-option') || '-1', 10), labelOptions);
                    if (!selectedOption) {
                        return;
                    }
                    var messageNode = modal.querySelector('[data-label-message]');
                    var useServerSideLabelGeneration = shouldUseServerSideLabelGeneration();
                    var popupWindow = useServerSideLabelGeneration ? null : window.open('', '_blank');
                    var navigateAfterPrint = function () {
                        if (returnUrl) {
                            window.location.href = returnUrl;
                        }
                    };

                    modal.querySelectorAll('[data-label-option]').forEach(function (actionButton) {
                        actionButton.disabled = true;
                    });
                    if (button.classList.contains('button--icon')) {
                        button.innerHTML = '<span class="button__dots" aria-hidden="true">...</span>';
                    } else {
                        button.textContent = 'Gerando...';
                    }

                    if (useServerSideLabelGeneration) {
                        closeLabelModal();
                        openServerSideLabelInNewWindow(selectedOption.modelo, selectedOption.id_type, labelId, sql);
                        navigateAfterPrint();
                        return;
                    }

                    requestLabelPdf(selectedOption.modelo, selectedOption.id_type, labelId, sql, popupWindow, messageNode, navigateAfterPrint);
                });
            });
        }

        function findLabelOption(selectedIndex, labelOptions) {
            var currentIndex = 0;
            var foundOption = null;

            labelOptions.forEach(function (group) {
                if (foundOption || !group || !Array.isArray(group.opcoes)) {
                    return;
                }

                group.opcoes.forEach(function (option) {
                    if (foundOption || !option || option.mostrar === false || !option.modelo || !option.id_type) {
                        return;
                    }

                    if (currentIndex === selectedIndex) {
                        foundOption = option;
                    }
                    currentIndex += 1;
                });
            });

            return foundOption;
        }

        labelPrintTriggers.forEach(function (labelPrintTrigger) {
            labelPrintTrigger.addEventListener('click', function () {
                openLabelModal(labelPrintTrigger);
            });
        });

        var autoOpenLabelTrigger = document.querySelector('[data-label-print="true"][data-label-auto-open="true"]');
        if (autoOpenLabelTrigger) {
            window.setTimeout(function () {
                openLabelModal(autoOpenLabelTrigger);
            }, 150);
        }
    }
});
