document.addEventListener('DOMContentLoaded', function () {
    var search = document.querySelector('.field--search input');
    if (search && !search.value) search.focus();

    var cameraTriggers = document.querySelectorAll('[data-barcode-camera-open="true"]');
    cameraTriggers.forEach(function (cameraTrigger) {
        var targetInput = document.getElementById(cameraTrigger.getAttribute('data-target-input'));
        if (targetInput) {
            // O foco inicial é destinado ao leitor físico; o teclado virtual só abre quando o operador toca no campo.
            targetInput.placeholder = 'Digite, bipe ou leia pela câmera';
            targetInput.addEventListener('pointerdown', function () {
                targetInput.inputMode = 'text';
            });
            targetInput.addEventListener('blur', function () {
                targetInput.inputMode = 'none';
            });
        }

        cameraTrigger.addEventListener('click', function () {
            targetInput = document.getElementById(cameraTrigger.getAttribute('data-target-input'));
            if (!targetInput) return;

            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia || typeof window.Html5Qrcode === 'undefined') {
                window.alert('Leitura por câmera indisponível neste navegador ou dispositivo. Use a leitura manual ou o leitor físico.');
                return;
            }

            var modalId = 'barcode-reader-modal';
            var readerId = 'barcode-reader-box';
            var existingModal = document.getElementById(modalId);
            if (existingModal) existingModal.remove();

            var modal = document.createElement('div');
            modal.className = 'barcode-modal';
            modal.id = modalId;
            modal.innerHTML = [
                '<div class="barcode-modal__overlay" data-barcode-close="true"></div>',
                '<div class="barcode-modal__panel" role="dialog" aria-modal="true" aria-label="Leitura por câmera">',
                '  <h2 class="barcode-modal__title">Ler ' + (cameraTrigger.getAttribute('data-barcode-label') || 'produto') + ' pela câmera</h2>',
                '  <p class="barcode-modal__text">Aponte a câmera para o código de barras da etiqueta.</p>',
                '  <div id="' + readerId + '" class="barcode-modal__reader"></div>',
                '  <p class="barcode-modal__message" data-barcode-message hidden></p>',
                '  <div class="barcode-modal__actions"><button type="button" class="button button--secondary" data-barcode-close="true">Cancelar</button></div>',
                '</div>'
            ].join('');
            document.body.appendChild(modal);

            var messageNode = modal.querySelector('[data-barcode-message]');
            var scanner = new window.Html5Qrcode(readerId);
            var scannerStarted = false;
            var closed = false;

            function closeScanner() {
                if (closed) return;
                closed = true;
                var removeModal = function () {
                    modal.remove();
                    targetInput.focus();
                };
                if (scannerStarted) {
                    scanner.stop().catch(function () { return null; }).finally(removeModal);
                    return;
                }
                removeModal();
            }

            modal.querySelectorAll('[data-barcode-close="true"]').forEach(function (node) {
                node.addEventListener('click', closeScanner);
            });

            scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 250, height: 250 }, aspectRatio: 1.0 },
                function (decodedText) {
                    if (!decodedText || closed) return;
                    targetInput.value = decodedText.trim();
                    closeScanner();
                    var form = targetInput.closest('form');
                    if (form) window.setTimeout(function () { form.requestSubmit(); }, 0);
                }
            ).then(function () {
                scannerStarted = true;
            }).catch(function (error) {
                if (closed) return;
                if (messageNode) {
                    messageNode.hidden = false;
                    messageNode.textContent = 'Não foi possível abrir a câmera. Verifique a permissão do navegador e tente novamente.';
                }
                // Mantém o modal aberto para o operador entender o erro e cancelá-lo.
                if (window.console && console.warn) console.warn('Erro ao iniciar leitura por câmera:', error);
            });
        });
    });

    document.querySelectorAll('[data-location-search="true"]').forEach(function (input) {
        var hiddenInput = input.parentNode.querySelector('[data-location-search-value="true"]');
        var results = input.parentNode.querySelector('[data-location-search-results="true"]');
        var requestTimer = null;
        var requestId = 0;

        function closeResults() {
            results.hidden = true;
            results.textContent = '';
            input.setAttribute('aria-expanded', 'false');
        }

        function render(items) {
            results.textContent = '';
            if (!items.length) {
                var empty = document.createElement('div');
                empty.className = 'location-search__empty';
                empty.textContent = 'Nenhuma locação disponível encontrada.';
                results.appendChild(empty);
            }
            items.forEach(function (item) {
                var option = document.createElement('button');
                option.type = 'button';
                option.textContent = item.D004_Local;
                option.addEventListener('click', function () {
                    input.value = item.D004_Local;
                    hiddenInput.value = item.D004_Id;
                    closeResults();
                });
                results.appendChild(option);
            });
            results.hidden = false;
            input.setAttribute('aria-expanded', 'true');
        }

        input.addEventListener('input', function () {
            hiddenInput.value = '';
            window.clearTimeout(requestTimer);
            var query = input.value.trim();
            if (query.length < 2) {
                closeResults();
                return;
            }
            requestTimer = window.setTimeout(function () {
                var thisRequest = ++requestId;
                var url = new URL(input.getAttribute('data-url'), window.location.origin);
                url.searchParams.set('d009_id', input.getAttribute('data-d009-id'));
                url.searchParams.set('action', input.getAttribute('data-action'));
                url.searchParams.set('ignore_t066_id', input.getAttribute('data-ignore-t066-id'));
                url.searchParams.set('q', query);
                window.fetch(url.toString(), { credentials: 'same-origin' })
                    .then(function (response) { return response.ok ? response.json() : Promise.reject(); })
                    .then(function (data) { if (thisRequest === requestId && data.ok) render(data.items || []); })
                    .catch(closeResults);
            }, 180);
        });

        input.addEventListener('blur', function () { window.setTimeout(closeResults, 150); });
    });

    document.querySelectorAll('[data-open-dialog]').forEach(function (button) {
        button.addEventListener('click', function () {
            var dialog = document.getElementById(button.getAttribute('data-open-dialog'));
            if (dialog && typeof dialog.showModal === 'function') dialog.showModal();
        });
    });
    document.querySelectorAll('[data-close-dialog="true"]').forEach(function (button) {
        button.addEventListener('click', function () {
            var dialog = button.closest('dialog');
            if (dialog) dialog.close();
        });
    });
});
