(function () {
  'use strict';
  function now() { return typeof performance === 'undefined' ? Date.now() : performance.now(); }
  function text(progress) {
    if (progress.fontesConcluidas != null) return 'Consultando o indicador. Aguarde a conclusão para ver os totais.';
    return 'Consultando período ' + progress.intervalo + ' de ' + progress.intervalos + ' • ' + Number(progress.registros || 0).toLocaleString('pt-BR') + ' registros consultados. Aguarde a conclusão para ver os totais.';
  }
  async function request(url, options) {
    options = options || {};
    var target = new URL(url, location.href);
    target.searchParams.set('carregarProgressivo', '1');
    var started = now();
    try {
    while (true) {
      if (options.signal && options.signal.aborted) throw new DOMException('Consulta cancelada', 'AbortError');
      started = now();
      var response = await fetch(target.toString(), { credentials: 'same-origin', cache: 'no-store', signal: options.signal });
      var data = window.DashboardSqlDiagnostics
        ? await window.DashboardSqlDiagnostics.read(response, target.toString(), started)
        : await response.json().catch(function () { return { error: 'Não foi possível concluir a consulta. Tente atualizar novamente.' }; });
      if (!response.ok || data.error) {
        var error = new Error(data.error || 'Não foi possível carregar o indicador.');
        error.dashboardSqlRecorded = !!data.analiseSql;
        throw error;
      }
      if (response.status !== 202) return data;
      if (!data.carregamento || !/^[a-f0-9]{32}$/.test(data.carregamento.token)) throw new Error('Atualize os arquivos do carregamento do indicador.');
      target.searchParams.set('cargaToken', data.carregamento.token);
      if (options.onProgress) options.onProgress(data.carregamento);
      await new Promise(function (resolve) { setTimeout(resolve, 0); });
    }
    } catch (error) {
      if (window.DashboardSqlDiagnostics && !error.dashboardSqlRecorded) window.DashboardSqlDiagnostics.failure(error, target.toString(), started);
      throw error;
    }
  }
  function limitDates(from, to) {
    function apply() {
      if (!to.value) { from.min = ''; return; }
      var date = new Date(to.value + 'T12:00:00'); date.setFullYear(date.getFullYear() - 2);
      from.min = date.getFullYear() + '-' + String(date.getMonth()+1).padStart(2,'0') + '-' + String(date.getDate()).padStart(2,'0');
    }
    to.addEventListener('change', apply); apply();
  }
  window.DashboardProgressivo = { request: request, text: text, limitDates: limitDates };
}());
