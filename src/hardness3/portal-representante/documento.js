(() => {
  'use strict';
  const $ = id => document.getElementById(id);
  const { documento, id, csrf, gestor, error } = document.body.dataset;
  const label = documento === 'danfe' ? 'DANFE' : documento === 'disponibilidade' ? 'Impressão de disponibilidade' : 'PDF do orçamento';
  let active, generation = 0, pdfUrl, refreshSession = false;
  function showState(title, message, failed = false) {
    $('documentState').hidden = false;
    $('documentStateTitle').textContent = title;
    $('documentStatus').textContent = message;
    $('documentStatus').setAttribute('role', failed ? 'alert' : 'status');
    document.title = title + ' | Hardness';
  }
  async function prepare() {
    if (active) active.abort();
    const current = ++generation, controller = new AbortController();
    active = controller;
    refreshSession = false;
    let timedOut = false;
    const timeout = setTimeout(() => { timedOut = true; controller.abort(); }, 80000);
    $('documentPdf').hidden = true; $('documentPdf').removeAttribute('data');
    if (pdfUrl) { URL.revokeObjectURL(pdfUrl); pdfUrl = null; }
    ['documentRetry','documentOpen','documentDownload'].forEach(key => { $(key).hidden = true; });
    $('documentCancel').hidden = false;
    showState('Preparando ' + label, 'Aguarde o carregamento do PDF.');
    try {
      const response = await fetch(`api.php?${new URLSearchParams({acao:'documento',documento,id})}`, {
        method:'POST', credentials:'same-origin', signal:controller.signal,
        headers:{'X-CSRF-Token':csrf,...(gestor ? {'X-Portal-Gestor':gestor} : {})}
      });
      if (!response.ok) {
        refreshSession = response.status === 401 || response.status === 403;
        const result = await response.json().catch(() => ({}));
        throw new Error(result.error || 'Não foi possível carregar o documento. Tente novamente.');
      }
      if (!(response.headers.get('Content-Type') || '').toLowerCase().startsWith('application/pdf')) throw new Error('A impressão não retornou um PDF válido. Tente novamente.');
      const pdf = await response.blob();
      if (pdf.size < 8 || pdf.size > 33554432 || !(await pdf.slice(0,5).text()).startsWith('%PDF-')) throw new Error('O PDF está vazio ou inválido. Tente novamente.');
      if (current !== generation) return;
      if (controller.signal.aborted) throw new Error('Preparação interrompida.');
      pdfUrl = URL.createObjectURL(pdf);
      $('documentPdf').data = pdfUrl; $('documentPdf').hidden = false;
      $('documentOpen').href = pdfUrl; $('documentOpen').hidden = false;
      $('documentDownload').href = pdfUrl; $('documentDownload').download = `${documento}-${id}.pdf`; $('documentDownload').hidden = false;
      $('documentState').hidden = true; document.title = label + ' | Hardness';
    } catch (failure) {
      if (current !== generation) return;
      const message = timedOut ? 'O carregamento demorou mais que o esperado. Clique em “Tentar novamente”.' : controller.signal.aborted ? 'Preparação cancelada. Você pode tentar novamente.' : failure instanceof TypeError ? 'A conexão foi interrompida. Clique em “Tentar novamente”.' : failure.message;
      showState('Não foi possível abrir ' + label, message, true);
      $('documentRetry').hidden = false;
      $('documentRetry').textContent = refreshSession ? 'Recarregar' : 'Tentar novamente';
    } finally {
      clearTimeout(timeout);
      if (current === generation) { active = null; $('documentCancel').hidden = true; }
    }
  }
  $('documentRetry').addEventListener('click', () => { if (refreshSession) location.reload(); else prepare(); });
  $('documentCancel').addEventListener('click', () => { if (active) active.abort(); });
  window.addEventListener('pagehide', () => { if (active) active.abort(); });
  if (error) { showState('Documento indisponível', error, true); $('documentCancel').hidden = true; }
  else prepare();
})();
