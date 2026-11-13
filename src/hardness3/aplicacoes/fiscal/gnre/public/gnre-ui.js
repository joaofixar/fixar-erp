/* O contexto GNRE é local: alerta antes de descartar uma edição. */
(function () {
  'use strict';
  function guideForm() {
    var form = document.querySelector('[data-gnre-guide-form]');
    if (!form) return;
    var revenue = form.querySelector('[name="revenue_code"]');
    var detail = form.querySelector('[name="revenue_detail"]');
    var uf = form.querySelector('[name="favored_state"]');
    var config = (window.gnreGuideConfigs || {})[uf ? uf.value : ''];
    function render() {
      var receipt = config && config.rules && config.rules.receipts ? config.rules.receipts[revenue.value] : null;
      var label = detail && detail.closest('label');
      if (!label) return;
      var flag = receipt && receipt.fields ? (receipt.fields.detail || 'N') : 'N';
      var status = label.querySelector('.field-status');
      detail.required = flag === 'S';
      if (flag === 'N') { detail.value = ''; label.style.display = 'none'; }
      else { label.style.display = ''; if (status) status.textContent = flag === 'S' ? '*' : 'opcional'; }
    }
    if (revenue) revenue.addEventListener('change', render);
    render();
  }
  guideForm();
  if (!window.gnreTask09Rules) {
    var itemAction = document.querySelector('input[name="action"][value="save_item"]');
    var itemForm = itemAction && itemAction.form;
    if (itemForm) {
      itemForm.querySelectorAll('input, select, textarea, button').forEach(function (control) {
        if (control.type !== 'hidden') control.disabled = true;
      });
    }
  }
  var dirty = false;
  document.querySelectorAll('form').forEach(function (form) {
    /* Queue selection is an operational choice, not unsaved guide data.
       Its completion reload must never trigger the draft-loss browser alert. */
    if (form.matches('[data-gnre-company-form], [data-task63-form]')) return;
    form.addEventListener('input', function () { dirty = true; });
    form.addEventListener('submit', function () { dirty = false; });
  });
  var company = document.querySelector('[data-gnre-company-form]');
  if (company) company.addEventListener('submit', function (event) {
    if (dirty && !window.confirm('Há uma edição de rascunho não salva. Trocar a empresa GNRE limpará configurações e filtros desta tela. Continuar?')) event.preventDefault();
  });
  var navIcons = {
    'guides': '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l3 3v15H6z"/><path d="M15 3v4h4M9 11h6M9 15h6M9 19h4"/></svg>',
    'new-guide': '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>',
    'batch-transmit': '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16l-6.4 7.2v5.1l-3.2 1.7v-6.8L4 5Z"/></svg>',
    'source-notes': '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M6 3h9l3 3v15H6z"/><path d="M15 3v4h4M9 11h6M9 15h6M9 19h4"/></svg>',
    'config': '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .34 1.88l.06.06-2.12 2.12-.06-.06a1.7 1.7 0 0 0-1.88-.34 1.7 1.7 0 0 0-1.03 1.55V20.3h-3v-.09A1.7 1.7 0 0 0 10.68 18.66a1.7 1.7 0 0 0-1.88.34l-.06.06-2.12-2.12.06-.06A1.7 1.7 0 0 0 7.02 15a1.7 1.7 0 0 0-1.55-1.03H5.4v-3h.07A1.7 1.7 0 0 0 7.02 9.94a1.7 1.7 0 0 0-.34-1.88l-.06-.06 2.12-2.12.06.06a1.7 1.7 0 0 0 1.88.34A1.7 1.7 0 0 0 11.71 4.73V4.65h3v.08a1.7 1.7 0 0 0 1.03 1.55 1.7 1.7 0 0 0 1.88-.34l.06-.06L19.8 8l-.06.06a1.7 1.7 0 0 0-.34 1.88 1.7 1.7 0 0 0 1.55 1.03H21v3h-.05A1.7 1.7 0 0 0 19.4 15Z"/></svg>'
  };
  [['a[href*="view=list"]','guides'], ['a[href*="view=new"]','new-guide']].forEach(function (entry) {
    var icon = document.querySelector('.gnre-nav ' + entry[0] + ' .gnre-nav-icon');
    if (icon) icon.setAttribute('data-gnre-nav-icon', entry[1]);
  });
  document.querySelectorAll('[data-gnre-nav-icon]').forEach(function (icon) {
    var svg = navIcons[icon.getAttribute('data-gnre-nav-icon') || ''];
    if (svg) icon.innerHTML = svg;
  });
  var overviewLink = document.querySelector('.gnre-nav a:first-child');
  if (overviewLink) overviewLink.remove();
  var shellHeading = document.querySelector('.gnre-topbar h1');
  if (shellHeading && typeof window.gnreShellTitle === 'string') shellHeading.textContent = window.gnreShellTitle;
  var environmentFeedback = document.querySelector('[data-gnre-environment-feedback]');
  if (environmentFeedback && typeof environmentFeedback.showModal === 'function') environmentFeedback.showModal();
  var guideHeader = document.querySelector('.guide-operational-header');
  var guideActions = document.querySelector('.guide-header-actions');
  var retentionAction = document.querySelector('.guide-retention-action');
  if (guideActions && retentionAction) guideActions.insertBefore(retentionAction, guideActions.firstChild);
  if (guideHeader) {
    var syncGuideHeaderHeight = function () {
      document.documentElement.style.setProperty('--guide-operational-header-height', guideHeader.offsetHeight + 'px');
    };
    syncGuideHeaderHeight();
    window.addEventListener('resize', syncGuideHeaderHeight);
    if (window.ResizeObserver) new window.ResizeObserver(syncGuideHeaderHeight).observe(guideHeader);
  }
  document.querySelectorAll('.gnre-cards .gnre-card').forEach(function (card) {
    var parts = card.textContent.trim().split(':');
    if (parts.length !== 2) return;
    var label = document.createElement('span');
    var value = document.createElement('strong');
    label.textContent = parts[0].trim();
    value.textContent = parts[1].trim();
    card.textContent = '';
    card.appendChild(label);
    card.appendChild(value);
  });
  var shell = document.querySelector('[data-gnre-shell]');
  var menuToggle = document.querySelector('[data-gnre-menu-toggle]');
  var menuClose = document.querySelector('[data-gnre-menu-close]');
  var menuBackdrop = document.querySelector('[data-gnre-menu-backdrop]');
  var sidebar = document.querySelector('[data-gnre-sidebar]');
  var previousFocus = null;
  function setMenu(open) {
    if (!shell || !menuToggle || !menuBackdrop) return;
    shell.classList.toggle('gnre-menu-open', open);
    menuToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    menuBackdrop.hidden = !open;
    if (open) {
      previousFocus = document.activeElement;
      if (menuClose) menuClose.focus();
    } else if (previousFocus && typeof previousFocus.focus === 'function') {
      previousFocus.focus();
      previousFocus = null;
    }
  }
  if (menuToggle) menuToggle.addEventListener('click', function () { setMenu(menuToggle.getAttribute('aria-expanded') !== 'true'); });
  if (menuClose) menuClose.addEventListener('click', function () { setMenu(false); });
  if (menuBackdrop) menuBackdrop.addEventListener('click', function () { setMenu(false); });
  if (sidebar) sidebar.addEventListener('click', function (event) { if (event.target.closest('a')) setMenu(false); });
  document.addEventListener('keydown', function (event) { if (event.key === 'Escape' && menuToggle && menuToggle.getAttribute('aria-expanded') === 'true') setMenu(false); });
  window.addEventListener('beforeunload', function (event) {
    if (!dirty) return;
    event.preventDefault(); event.returnValue = '';
  });
  var copy = document.querySelector('[data-copy-generated-xml]');
  if (copy) copy.addEventListener('click', function () {
    var xml = document.querySelector('[data-generated-xml]');
    if (!xml) return;
    var text = xml.textContent || '';
    if (navigator.clipboard && navigator.clipboard.writeText) navigator.clipboard.writeText(text);
    else {
      var area = document.createElement('textarea');
      area.value = text; area.setAttribute('readonly',''); area.style.position = 'fixed'; area.style.opacity = '0';
      document.body.appendChild(area); area.select(); document.execCommand('copy'); area.remove();
    }
    copy.textContent = 'XML copiado';
  });
  var xmlView = document.querySelector('[data-view-generated-xml]');
  var xmlDialog = document.querySelector('[data-generated-xml-dialog]');
  if (xmlView && xmlDialog && typeof xmlDialog.showModal === 'function') xmlView.addEventListener('click', function () { xmlDialog.showModal(); });
  /* Task 34 is isolated so the legacy shell keeps loading unchanged. */
  var guidedTransmission = document.createElement('script');
  guidedTransmission.src = 'task34-transmission-modal.js?v=52.3';
  guidedTransmission.defer = true;
  document.head.appendChild(guidedTransmission);
}());
(function(){'use strict';
  function formatMoney(value){var number=Number(String(value).replace(/^R\$\s*/,'').replace(',','.'));return isFinite(number)?'R$ '+number.toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}):value;}
  function formatDate(value){var match=/^(\d{4})-(\d{2})-(\d{2})$/.exec(String(value).trim());return match?match[3]+'/'+match[2]+'/'+match[1]:value;}
  document.querySelectorAll('dl dt').forEach(function(label){var value=label.nextElementSibling;if(!value||value.tagName!=='DD')return;var name=label.textContent.trim().toLowerCase();if(name==='total'||name==='valor')value.textContent=formatMoney(value.textContent.trim());if(name==='vencimento'||name==='data de vencimento')value.textContent=formatDate(value.textContent.trim());});
}());
