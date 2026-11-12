(() => {
  'use strict';
  const documentos = [
    { id: 'orcamentos', titulo: 'Orçamentos', cor: '#f6b84c', situacoes: [['com_pedido', 'Gerou pedido', '#4bd4ab'], ['em_aberto', 'Pendente', '#65adff'], ['cancelado', 'Cancelado', '#fb8091']] },
    { id: 'pedidos', titulo: 'Pedidos', cor: '#65adff', situacoes: [['pendente', 'Pendente', '#65adff'], ['em_expedicao', 'Em expedição', '#f6b84c'], ['faturado', 'Faturados', '#4bd4ab']] },
    { id: 'notas_fiscais', titulo: 'Notas fiscais', cor: '#4bd4ab', situacoes: [['emitida', 'NF emitidas', '#4bd4ab'], ['cancelada', 'Canceladas', '#fb8091']] },
  ];
  const pedidosLegados = { ...documentos[1], legado: true, situacoes: [['com_nf', 'Com NF', '#4bd4ab'], ['sem_nf', 'Sem NF', '#f6b84c'], ['cancelado', 'Cancelado', '#fb8091']] };
  const notasComFila = { ...documentos[2], fila: true, situacoes: [['emitida', 'NF emitidas', '#4bd4ab'], ['aguardando_autorizacao', 'Aguardando autorização', '#f6b84c'], ['cancelada', 'Canceladas', '#fb8091']] };
  const elemento = (tag, classe, texto) => {
    const no = document.createElement(tag);
    if (classe) no.className = classe;
    if (texto !== undefined) no.textContent = texto;
    return no;
  };
  const numero = valor => {
    if ((typeof valor !== 'number' && typeof valor !== 'string') || String(valor).trim() === '') return null;
    const n = Number(valor);
    return Number.isFinite(n) && n >= 0 && n <= Number.MAX_SAFE_INTEGER ? n : null;
  };
  const quantidade = n => n.toLocaleString('pt-BR', { maximumFractionDigits: 0 });
  const percentual = (parte, total) => total > 0 ? `${(parte / total * 100).toLocaleString('pt-BR', { minimumFractionDigits: 1, maximumFractionDigits: 1 })}%` : '—';
  const moedaExata = n => n.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
  function moeda(n, classe, compacta = true) {
    const no = elemento('strong', `comercial-moeda ${classe || ''}`);
    if (n === null) { no.textContent = '—'; return no; }
    let divisor = 1;
    let unidade = '';
    if (compacta && n >= 1e9) { divisor = 1e9; unidade = 'bi'; }
    else if (compacta && n >= 1e6) { divisor = 1e6; unidade = 'mi'; }
    else if (compacta && n >= 1e3) { divisor = 1e3; unidade = 'mil'; }
    const casas = divisor === 1 ? 0 : divisor === 1e9 ? 2 : 1;
    no.append(elemento('span', 'comercial-cifrao', 'R$'), elemento('span', 'comercial-montante', (n / divisor).toLocaleString(divisor === 1 ? 'pt-BR' : 'en-US', { minimumFractionDigits: casas, maximumFractionDigits: casas })));
    if (unidade) no.appendChild(elemento('span', 'comercial-unidade', unidade));
    no.title = moedaExata(n);
    no.setAttribute('aria-label', moedaExata(n));
    return no;
  }
  function registros(linhas) {
    if (!Array.isArray(linhas) || ![8, 9].includes(linhas.length)) throw new Error('Dados comerciais incompletos. Revise as situações dos três documentos.');
    const mapa = new Map();
    const pedidoLegado = linhas.some(linha => linha.documento === 'pedidos' && pedidosLegados.situacoes.some(s => s[0] === linha.situacao));
    const comFila = linhas.some(linha => linha.documento === 'notas_fiscais' && linha.situacao === 'aguardando_autorizacao');
    const documentosAtuais = [documentos[0], pedidoLegado ? pedidosLegados : documentos[1], comFila ? notasComFila : documentos[2]];
    linhas.forEach(linha => {
      const doc = documentosAtuais.find(d => d.id === linha.documento);
      const chave = `${linha.documento}:${linha.situacao}`;
      if (!doc || !doc.situacoes.some(s => s[0] === linha.situacao) || mapa.has(chave)) throw new Error('Documento ou situação comercial inválido ou repetido.');
      const valores = {};
      ['quantidade_hoje', 'valor_hoje', 'quantidade_mes', 'valor_mes'].forEach(campo => {
        const n = numero(linha[campo]);
        if (n === null || (campo.startsWith('quantidade') && !Number.isSafeInteger(n))) throw new Error('Há valores comerciais ausentes ou inválidos.');
        valores[campo] = n;
      });
      if (valores.quantidade_hoje > valores.quantidade_mes || valores.valor_hoje > valores.valor_mes) throw new Error('O movimento do dia deve estar incluído no mês.');
      ['hoje', 'mes'].forEach(periodo => {
        if (valores[`quantidade_${periodo}`] === 0 && valores[`valor_${periodo}`] !== 0) throw new Error('Há valor informado em uma situação sem documentos.');
      });
      mapa.set(chave, valores);
    });
    if (documentosAtuais.some(doc => doc.situacoes.some(s => !mapa.has(`${doc.id}:${s[0]}`)))) throw new Error('Falta uma situação comercial nos dados.');
    return { mapa, documentosAtuais };
  }
  const somar = (linhas, campo) => {
    const total = linhas.reduce((soma, linha) => soma + linha[campo], 0);
    if (!Number.isFinite(total) || total > Number.MAX_SAFE_INTEGER) throw new Error('Os totais comerciais excedem o limite de exibição.');
    return total;
  };
  function situacao(doc, linhas) {
    const caixa = elemento('section', 'comercial-situacoes');
    caixa.setAttribute('aria-label', `${doc.titulo}: situação no mês`);
    const consideradas = (doc.id === 'pedidos' && doc.legado) || (doc.id === 'notas_fiscais' && doc.fila) ? linhas.slice(0, 2) : linhas;
    const totalValor = somar(consideradas, 'valor_mes');
    const totalQuantidade = somar(consideradas, 'quantidade_mes');
    const cabecalho = elemento('div', 'comercial-situacao-titulo');
    cabecalho.appendChild(elemento('h3', '', doc.id === 'orcamentos' ? 'Conversão dos orçamentos' : doc.id === 'pedidos' ? 'Situação dos pedidos' : 'Emissão de notas'));
    caixa.appendChild(cabecalho);
    const barra = elemento('div', 'comercial-distribuicao');
    barra.setAttribute('aria-hidden', 'true');
    const visiveis = doc.id === 'notas_fiscais' && !doc.fila ? linhas.slice(0, 1) : consideradas;
    visiveis.forEach((linha, i) => {
      const faixa = elemento('span', '');
      faixa.style.width = `${totalValor ? linha.valor_mes / totalValor * 100 : 0}%`;
      faixa.style.backgroundColor = doc.situacoes[i][2];
      barra.appendChild(faixa);
    });
    caixa.appendChild(barra);
    const tabela = elemento('div', 'comercial-situacao-linhas');
    const titulos = elemento('div', 'comercial-situacao-linha comercial-situacao-cabecalho');
    titulos.append(elemento('span', '', 'Situação'), elemento('span', '', '% valor'), elemento('span', '', '% qtd.'));
    tabela.appendChild(titulos);
    visiveis.forEach((linha, i) => {
      const row = elemento('div', 'comercial-situacao-linha');
      const nome = elemento('span', 'comercial-situacao-nome');
      const ponto = elemento('i', '');
      ponto.style.backgroundColor = doc.situacoes[i][2];
      nome.append(ponto, document.createTextNode(doc.situacoes[i][1]));
      row.appendChild(nome);
      row.append(elemento('strong', '', percentual(linha.valor_mes, totalValor)), elemento('strong', '', percentual(linha.quantidade_mes, totalQuantidade)));
      row.title = `${quantidade(linha.quantidade_mes)} documentos · ${moedaExata(linha.valor_mes)}`;
      tabela.appendChild(row);
    });
    caixa.appendChild(tabela);
    return caixa;
  }
  function periodo(doc, totais, nome) {
    const caixa = elemento('section', `comercial-periodo comercial-periodo-${nome}`);
    caixa.setAttribute('aria-label', `${doc.titulo}: ${nome === 'hoje' ? 'hoje' : 'mês até hoje'}`);
    caixa.appendChild(elemento('h3', 'comercial-periodo-rotulo', nome === 'hoje' ? 'Hoje' : 'Mês até hoje'));
    const valor = elemento('div', 'comercial-valores');
    valor.appendChild(moeda(totais.valor));
    const detalhes = elemento('div', 'comercial-periodo-detalhes');
    const qtd = elemento('div', 'comercial-quantidade');
    qtd.append(elemento('span', 'comercial-legenda', 'Quantidade'), elemento('strong', '', quantidade(totais.quantidade)));
    const ticket = elemento('div', 'comercial-ticket');
    ticket.append(elemento('span', 'comercial-legenda', 'Ticket médio'), moeda(totais.quantidade ? totais.valor / totais.quantidade : null, '', false));
    detalhes.append(qtd, ticket);
    caixa.append(valor, detalhes);
    return caixa;
  }
  function faixa(doc, mapa) {
    const linhas = doc.situacoes.map(s => mapa.get(`${doc.id}:${s[0]}`));
    const validas = linhas.filter((linha, i) => doc.id === 'orcamentos' || (doc.id === 'notas_fiscais' ? doc.situacoes[i][0] === 'emitida' : !doc.situacoes[i][0].startsWith('cancelad')));
    const totais = {};
    ['hoje', 'mes'].forEach(periodo => {
      totais[periodo] = { quantidade: somar(validas, `quantidade_${periodo}`), valor: somar(validas, `valor_${periodo}`) };
    });
    const card = elemento('article', 'comercial-faixa');
    card.dataset.documento = doc.id;
    card.style.setProperty('--cor-comercial', doc.cor);
    const topo = elemento('header', 'comercial-documento');
    topo.appendChild(elemento('h2', '', doc.titulo));
    card.appendChild(topo);
    ['hoje', 'mes'].forEach(nome => card.appendChild(periodo(doc, totais[nome], nome)));
    card.appendChild(situacao(doc, linhas));
    return card;
  }
  window.PainelComercial = {
    desenhar(container, painel, linhas) {
      const { mapa, documentosAtuais } = registros(linhas);
      const quadro = elemento('div', 'comercial-quadro');
      const data = painel.referencia ? new Date(`${painel.referencia}T12:00:00`) : new Date();
      quadro.setAttribute('aria-label', `Quadro comercial: dia ${data.toLocaleDateString('pt-BR')} e acumulado do mês até essa data`);
      const cabecalho = elemento('div', 'comercial-cabecalho');
      cabecalho.setAttribute('aria-hidden', 'true');
      const dia = elemento('div', 'comercial-cabecalho-periodo');
      dia.appendChild(elemento('strong', '', 'Hoje'));
      const mes = elemento('div', 'comercial-cabecalho-periodo');
      mes.appendChild(elemento('strong', '', 'Mês'));
      cabecalho.append(elemento('span'), dia, mes, elemento('span', 'comercial-cabecalho-titulo', 'Situação no mês'));
      quadro.appendChild(cabecalho);
      const faixas = elemento('div', 'comercial-faixas');
      documentosAtuais.forEach(doc => faixas.appendChild(faixa(doc, mapa)));
      quadro.appendChild(faixas);
      container.replaceChildren(quadro);
      // Centraliza os períodos sobre os números da primeira faixa, inclusive ao redimensionar.
      const valores = faixas.firstElementChild.querySelectorAll('.comercial-valores .comercial-montante');
      cabecalho.querySelectorAll('.comercial-cabecalho-periodo strong').forEach((titulo, i) => {
        if (!titulo.getClientRects().length) return;
        const valor = valores[i].getBoundingClientRect();
        const rotulo = titulo.getBoundingClientRect();
        const deslocamento = valor.left + valor.width / 2 - rotulo.left - rotulo.width / 2;
        titulo.style.transform = `translateX(${deslocamento}px)`;
      });
    }
  };
})();
