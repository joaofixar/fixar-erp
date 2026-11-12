(function () {
    'use strict';

    document.documentElement.className += ' js';

    var overlay = document.querySelector('[data-processing-overlay]');
    var overlayText = document.querySelector('[data-processing-overlay-text]');

    function resolveSubmitter(ev) {
        if (ev && ev.submitter) {
            return ev.submitter;
        }
        if (document.activeElement && document.activeElement.form === ev.target) {
            return document.activeElement;
        }
        return ev.target.querySelector('button[type="submit"], input[type="submit"]');
    }

    function showProcessing(message) {
        if (!overlay || !overlayText) {
            return;
        }
        overlayText.textContent = message || 'Processando...';
        overlay.hidden = false;
        document.body.classList.add('processing-active');
    }

    function hideProcessing() {
        if (!overlay) {
            return;
        }
        overlay.hidden = true;
        document.body.classList.remove('processing-active');
    }

    function bindProcessingForms(scope) {
        var root = scope || document;
        var forms = root.querySelectorAll('form[data-processing-form="1"]');
        Array.prototype.forEach.call(forms, function (form) {
            if (form.getAttribute('data-processing-bound') === '1') {
                return;
            }
            form.setAttribute('data-processing-bound', '1');
            form.addEventListener('submit', function (ev) {
                var submitter = resolveSubmitter(ev);
                var text = '';
                if (submitter && submitter.getAttribute('data-processing-text')) {
                    text = submitter.getAttribute('data-processing-text');
                }
                if (!text) {
                    text = form.getAttribute('data-processing-text') || '';
                }
                if (!text && submitter) {
                    text = (submitter.textContent || submitter.value || '').trim();
                    if (text !== '') {
                        text += '...';
                    }
                }
                showProcessing(text || 'Processando...');
            });
        });
    }

    bindProcessingForms(document);

    window.ImportadorOrcamentoPdfProcessing = {
        show: showProcessing,
        hide: hideProcessing,
        update: showProcessing,
        bindForms: bindProcessingForms
    };
}());
