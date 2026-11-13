(function () {
    'use strict';

    var dialog = document.querySelector('[data-task60-batch-modal], [data-task60-result-modal]');
    if (!dialog || !dialog.showModal) return;

    dialog.addEventListener('cancel', function (event) {
        event.preventDefault();
    });
    dialog.close();
    dialog.showModal();
}());
