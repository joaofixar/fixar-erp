(() => {
  'use strict';
  const el = id => document.getElementById(id);
  let config = null;
  let paineis = [];
  let indice = 0;
  let pagina = 0;
  let pausado = false;
  let encerrado = false;
  let consultando = false;
  let preparando = true;
  let painelConsultado = null;
  let erroConfiguracao = '';
  let timerAtualizacao;
  let proximaTroca = 0;
  let logoContexto = '';
  const cache = new Map();

  function atualizarCarregamento() {
    const atual = paineis[indice];
    const dados = atual && cache.get(atual.id);
    const aguardaDados = atual && atual.tipo !== 'mensagem' && (painelConsultado === atual.id || (!dados || (!dados.linhas && !dados.erro)));
    const mostrar = !encerrado && Boolean(preparando || aguardaDados);
    const carregamento = el('carregamentoPainel');
    // Ao terminar a consulta, a TV recebe um intervalo completo para leitura.
    const terminou = !mostrar && !carregamento.hidden && config;
    carregamento.hidden = !mostrar;
    el('conteudo').setAttribute('aria-busy', String(mostrar));
    if (terminou) reiniciarAlternancia();
  }

  async function requisitar(acao, parametros = {}, opcoes = {}) {
    const url = new URL(window.location.pathname, window.location.origin);
    url.search = new URLSearchParams({ acao, ...(config ? { contexto: config.contexto } : {}), ...parametros }).toString();
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 12000);
    try {
      const resposta = await fetch(url, { credentials: 'same-origin', cache: 'no-store', ...opcoes, signal: controller.signal });
      const dados = await resposta.json();
      if (!resposta.ok) {
        const erro = new Error(dados.erro || 'Não foi possível carregar o painel.');
        erro.status = resposta.status;
        throw erro;
      }
      return dados;
    } finally {
      clearTimeout(timeout);
    }
  }

  function mensagem(texto) {
    const caixa = document.createElement('div');
    caixa.className = 'mensagem';
    const p = document.createElement('p');
    p.textContent = texto;
    caixa.appendChild(p);
    el('conteudo').replaceChildren(caixa);
  }

  function avisar(texto) {
    el('aviso').textContent = texto;
    el('aviso').hidden = !texto;
  }

  function mostrarAtualizacao(dados, erro, texto = 'Aguardando dados') {
    const no = el('atualizacao');
    const data = dados && dados.atualizadoEm ? new Date(dados.atualizadoEm) : null;
    const temData = data && Number.isFinite(data.getTime());
    no.dataset.estado = erro ? 'erro' : temData ? 'atualizado' : '';
    no.textContent = temData ? `${erro ? 'Últimos dados' : 'Atualizado'} às ${data.toLocaleTimeString('pt-BR')}` : texto;
    no.title = temData ? `Última atualização: ${data.toLocaleString('pt-BR')}${dados.limitado ? ' · Exibindo os primeiros 200 registros' : ''}` : texto;
    no.setAttribute('aria-label', no.title);
  }

  function falhaFatal(erro) {
    encerrado = true;
    atualizarCarregamento();
    clearTimeout(timerAtualizacao);
    cache.clear();
    paineis = [];
    el('marcaEmpresa').hidden = true;
    el('logoEmpresa').removeAttribute('src');
    el('tituloPainel').textContent = 'Painel indisponível';
    el('setorAtual').textContent = 'ACESSO AO PAINEL';
    el('situacao').textContent = 'Acesso encerrado';
    el('situacao').dataset.estado = 'erro';
    el('paginacao').textContent = '';
    mostrarAtualizacao(null, true, 'Acesso encerrado');
    el('alternancia').textContent = 'Reabra pelo sistema';
    ['setor', 'pausar', 'anterior', 'proximo'].forEach(id => { el(id).disabled = true; });
    avisar('');
    mensagem(erro.message);
  }

  function porPagina() {
    const atual = paineis[indice];
    if (atual && atual.tipo === 'comercial') return 8;
    if (atual && atual.tipo === 'grafico') {
      return Math.max(3, Math.min(atual.grafico.categoriasPorPagina || 8, Math.floor((el('conteudo').clientWidth - 180) / 100)));
    }
    const rem = parseFloat(getComputedStyle(document.documentElement).fontSize);
    const disponivel = window.innerHeight - el('conteudo').getBoundingClientRect().top - document.querySelector('footer').offsetHeight - 50;
    return Math.max(1, Math.min(config.linhasPorPagina, Math.floor((disponivel - rem * 3) / (rem * 4))));
  }

  function totalPaginas() {
    const atual = paineis[indice];
    if (atual && atual.tipo === 'comercial') return 1;
    if (atual && atual.tipo === 'grafico' && atual.grafico.tipo === 'rosca') return 1;
    const dados = atual && cache.get(atual.id);
    return dados && dados.linhas ? Math.max(1, Math.ceil(dados.linhas.length / porPagina())) : 1;
  }

  function formatar(valor, formato) {
    if (valor === null || valor === undefined || valor === '') return '—';
    if (formato === 'numero' || formato === 'moeda') {
      const numero = Number(valor);
      if (!Number.isFinite(numero)) return String(valor);
      return numero.toLocaleString('pt-BR', formato === 'moeda' ? { style: 'currency', currency: 'BRL' } : { maximumFractionDigits: 2 });
    }
    if (formato === 'data') {
      const partes = String(valor).match(/^(\d{4})-(\d{2})-(\d{2})/);
      if (partes) return `${partes[3]}/${partes[2]}/${partes[1]}`;
    }
    return String(valor);
  }

  function tabela(painel, linhas) {
    const container = document.createElement('div');
    container.className = 'tabela-container';
    const table = document.createElement('table');
    const head = document.createElement('thead');
    const tr = document.createElement('tr');
    painel.colunas.forEach(coluna => {
      const th = document.createElement('th');
      th.scope = 'col';
      th.textContent = coluna.titulo;
      tr.appendChild(th);
    });
    head.appendChild(tr);
    table.appendChild(head);
    const body = document.createElement('tbody');
    linhas.slice(pagina * porPagina(), (pagina + 1) * porPagina()).forEach(linha => {
      const row = document.createElement('tr');
      painel.colunas.forEach(coluna => {
        const td = document.createElement('td');
        const texto = formatar(linha[coluna.campo], coluna.formato);
        const celula = document.createElement('span');
        celula.className = 'celula';
        celula.textContent = texto;
        td.title = texto;
        if (coluna.formato === 'status') {
          celula.className += ' status';
          const status = texto.toLowerCase();
          if (/atras|bloque|cancel|parad/.test(status)) celula.dataset.cor = 'vermelho';
          else if (/pend|aguard|andamento|separa/.test(status)) celula.dataset.cor = 'amarelo';
          else if (/conclu|pronto|liber|entreg|finaliz/.test(status)) celula.dataset.cor = 'verde';
        }
        if (coluna.formato === 'numero' || coluna.formato === 'moeda') td.className = 'numero';
        td.appendChild(celula);
        row.appendChild(td);
      });
      body.appendChild(row);
    });
    table.appendChild(body);
    container.appendChild(table);
    el('conteudo').replaceChildren(container);
  }

  function desenhar() {
    atualizarCarregamento();
    if (encerrado || !paineis.length) return;
    const atual = paineis[indice];
    const dados = cache.get(atual.id);
    document.body.classList.toggle('quadro-comercial-ativo', atual.tipo === 'comercial');
    document.body.classList.toggle('painel-unico', paineis.length === 1 && totalPaginas() === 1);
    pagina = Math.min(pagina, totalPaginas() - 1);
    el('tituloPainel').textContent = atual.titulo;
    el('setorAtual').textContent = atual.setor;
    const erro = erroConfiguracao || (dados && dados.erro) || '';
    avisar(erro);
    el('situacao').dataset.estado = erro ? 'erro' : '';
    el('situacao').textContent = !el('carregamentoPainel').hidden ? 'Carregando…' : erro ? 'Atualização pendente' : (atual.tipo === 'mensagem' ? 'Comunicado' : dados && dados.linhas ? 'Atualizado' : 'Carregando…');
    el('paginacao').textContent = `Painel ${indice + 1} de ${paineis.length} · Página ${pagina + 1} de ${totalPaginas()}`;
    el('anterior').disabled = el('proximo').disabled = paineis.length === 1 && totalPaginas() === 1;
    mostrarAtualizacao(null, erro, atual.tipo === 'mensagem' ? 'Comunicado do setor' : 'Aguardando dados');
    if (atual.tipo === 'mensagem') {
      mensagem(atual.mensagem);
    } else if (dados && dados.linhas) {
      if (dados.linhas.length) {
        if (atual.tipo === 'comercial') {
          try {
            window.PainelComercial.desenhar(el('conteudo'), atual, dados.linhas);
          } catch (erroComercial) {
            mensagem(erroComercial.message || 'Não foi possível montar o quadro comercial.');
            el('situacao').textContent = 'Revise os dados';
            el('situacao').dataset.estado = 'erro';
          }
        } else if (atual.tipo === 'grafico') {
          const linhas = atual.grafico.tipo === 'rosca' ? dados.linhas : dados.linhas.slice(pagina * porPagina(), (pagina + 1) * porPagina());
          try {
            window.PainelGraficos.desenhar(el('conteudo'), atual, linhas, dados.linhas);
          } catch (erroGrafico) {
            mensagem(erroGrafico.message || 'Não foi possível desenhar o gráfico.');
            el('situacao').textContent = 'Revise os dados';
            el('situacao').dataset.estado = 'erro';
          }
        } else tabela(atual, dados.linhas);
      }
      else mensagem('Nenhum registro para os filtros configurados.');
      mostrarAtualizacao(dados, erro || el('situacao').dataset.estado === 'erro');
      if (dados.limitado && !erro && el('situacao').dataset.estado !== 'erro') el('situacao').textContent = 'Atualizado · Primeiros 200 registros';
    } else {
      mensagem(erro ? 'Aguardando uma nova tentativa de atualização.' : 'Carregando informações do setor…');
    }
  }

  async function carregarAtual() {
    if (encerrado || consultando || !paineis.length) return;
    const atual = paineis[indice];
    if (!['tabela', 'grafico', 'comercial'].includes(atual.tipo)) return;
    const anterior = cache.get(atual.id) || {};
    if (anterior.tentativa && Date.now() - anterior.tentativa < config.atualizacaoSegundos * 1000) return;
    const tentativa = Date.now();
    consultando = true;
    painelConsultado = atual.id;
    atualizarCarregamento();
    try {
      const dados = await requisitar('dados', { id: atual.id });
      if (!encerrado) cache.set(atual.id, { ...dados, tentativa });
    } catch (erro) {
      if (erro.status === 401 || erro.status === 403) return falhaFatal(erro);
      if (erro.status === 404) {
        // Uma permissão pode ter sido removida enquanto a TV estava aberta.
        cache.set(atual.id, { tentativa, erro: 'Painel indisponível. Aguardando atualização das permissões.' });
        if (paineis[indice] && paineis[indice].id === atual.id) mensagem('Painel indisponível. Atualizando permissões…');
      } else if (!encerrado) {
        cache.set(atual.id, { ...anterior, tentativa, erro: 'Falha na atualização. Os dados exibidos podem estar desatualizados; tentaremos novamente automaticamente.' });
      }
    } finally {
      consultando = false;
      painelConsultado = null;
      desenhar();
      if (!encerrado && paineis[indice] && paineis[indice].id !== atual.id) carregarAtual();
    }
  }

  function reiniciarAlternancia() {
    proximaTroca = Date.now() + config.alternanciaSegundos * 1000;
    desenharAlternancia();
  }

  function desenharAlternancia() {
    const progresso = el('progressoAlternancia');
    progresso.hidden = true;
    if (!config || encerrado) return;
    if (!el('carregamentoPainel').hidden) {
      el('alternancia').textContent = 'Aguardando dados';
      return;
    }
    const alternar = paineis.length > 1 || totalPaginas() > 1;
    const restante = Math.max(0, proximaTroca - Date.now());
    el('alternancia').textContent = pausado ? 'Alternância pausada · Dados continuam atualizando' : alternar ? `Próxima troca em ${Math.ceil(restante / 1000)}s` : 'Atualização automática ativa';
    if (!pausado && alternar) {
      progresso.hidden = false;
      progresso.firstElementChild.style.width = `${Math.min(100, restante / (config.alternanciaSegundos * 1000) * 100)}%`;
    }
  }

  function avancar(direcao) {
    if (!paineis.length) return;
    if (direcao > 0 && pagina + 1 < totalPaginas()) pagina++;
    else if (direcao < 0 && pagina > 0) pagina--;
    else {
      indice = (indice + direcao + paineis.length) % paineis.length;
      pagina = 0;
    }
    reiniciarAlternancia();
    desenhar();
    carregarAtual();
  }

  function filtrar(idAnterior) {
    paineis = config.paineis.filter(p => !el('setor').value || p.setor === el('setor').value);
    indice = Math.max(0, paineis.findIndex(p => p.id === idAnterior));
    pagina = 0;
    reiniciarAlternancia();
    desenhar();
  }

  async function atualizar() {
    if (encerrado) return;
    if (!config) {
      preparando = true;
      atualizarCarregamento();
    }
    try {
      const nova = await requisitar('configuracao');
      if (encerrado) return;
      const atualId = paineis[indice] && paineis[indice].id;
      const setorAnterior = el('setor').value;
      const paginaAnterior = pagina;
      const antiga = config;
      config = nova;
      preparando = false;
      erroConfiguracao = '';
      cache.forEach((valor, id) => {
        const novoPainel = config.paineis.find(p => p.id === id);
        const antigoPainel = antiga && antiga.paineis.find(p => p.id === id);
        if (!novoPainel || JSON.stringify(novoPainel) !== JSON.stringify(antigoPainel)) cache.delete(id);
      });
      document.title = `${config.titulo} — PAINEL`;
      el('tituloGeral').textContent = config.titulo;
      if (logoContexto !== config.contexto) {
        logoContexto = config.contexto;
        const logoUrl = new URL(window.location.pathname, window.location.origin);
        logoUrl.search = new URLSearchParams({ acao: 'logo-empresa', contexto: config.contexto }).toString();
        el('logoEmpresa').src = logoUrl.toString();
      }
      el('setor').replaceChildren(new Option('Todos os setores', ''));
      [...new Set(config.paineis.map(p => p.setor))].forEach(setor => el('setor').add(new Option(setor, setor)));
      el('setor').value = [...el('setor').options].some(o => o.value === setorAnterior) ? setorAnterior : '';
      el('setor').disabled = el('pausar').disabled = false;
      const trocaAnterior = proximaTroca;
      filtrar(atualId);
      if (antiga && paineis[indice] && paineis[indice].id === atualId) {
        pagina = Math.min(paginaAnterior, totalPaginas() - 1);
        proximaTroca = trocaAnterior;
        desenhar();
      }
      await carregarAtual();
    } catch (erro) {
      preparando = false;
      atualizarCarregamento();
      if (erro.status === 401 || erro.status === 403) return falhaFatal(erro);
      erroConfiguracao = 'Não foi possível atualizar a configuração. Uma nova tentativa será feita automaticamente.';
      avisar(erroConfiguracao);
      if (config) desenhar();
      else {
        el('situacao').textContent = 'Sem conexão';
        el('situacao').dataset.estado = 'erro';
        mostrarAtualizacao(null, true, 'Sem conexão');
        mensagem('Aguardando conexão para carregar os painéis.');
      }
    } finally {
      if (!encerrado) timerAtualizacao = setTimeout(atualizar, (config ? config.atualizacaoSegundos : 30) * 1000);
    }
  }

  el('logoEmpresa').addEventListener('load', () => { el('marcaEmpresa').hidden = encerrado; desenhar(); });
  el('logoEmpresa').addEventListener('error', () => { el('marcaEmpresa').hidden = true; logoContexto = ''; desenhar(); });
  el('setor').addEventListener('change', () => { filtrar(); carregarAtual(); });
  el('anterior').addEventListener('click', () => avancar(-1));
  el('proximo').addEventListener('click', () => avancar(1));
  el('pausar').addEventListener('click', () => {
    pausado = !pausado;
    el('pausar').textContent = pausado ? 'Retomar alternância' : 'Pausar alternância';
    el('pausar').setAttribute('aria-pressed', String(pausado));
    reiniciarAlternancia();
  });
  el('telaCheia').addEventListener('click', async () => {
    try {
      if (document.fullscreenElement) await document.exitFullscreen();
      else if (document.documentElement.requestFullscreen) await document.documentElement.requestFullscreen();
      else avisar('Use a opção de tela cheia do navegador para ampliar o painel.');
    } catch (erro) { avisar('Use a opção de tela cheia do navegador para ampliar o painel.'); }
  });
  document.addEventListener('fullscreenchange', () => { el('telaCheia').textContent = document.fullscreenElement ? 'Sair da tela cheia' : 'Tela cheia'; });
  window.addEventListener('resize', () => desenhar());
  function tick() {
    const agora = new Date();
    el('hora').textContent = agora.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
    el('data').textContent = agora.toLocaleDateString('pt-BR', { weekday: 'short', day: '2-digit', month: 'long' });
    desenharAlternancia();
    if (!config || encerrado) return;
    if (!el('carregamentoPainel').hidden) return;
    const alternar = paineis.length > 1 || totalPaginas() > 1;
    if (!pausado && alternar && Date.now() >= proximaTroca) avancar(1);
  }
  tick();
  setInterval(tick, 1000);

  async function iniciar() {
    const fragmento = new URLSearchParams(window.location.hash.slice(1));
    const token = fragmento.get('session');
    if (token) {
      history.replaceState(null, '', window.location.pathname + window.location.search);
      try {
        await requisitar('autorizar', {}, { method: 'POST', body: new URLSearchParams({ token }) });
      } catch (erro) {
        falhaFatal(new Error('Não foi possível autorizar o acesso. Reabra o PAINEL pela tela inicial do sistema.'));
        return;
      }
    }
    await atualizar();
  }
  iniciar();
})();
