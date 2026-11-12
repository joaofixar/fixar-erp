(() => {
  'use strict';
  const cores = ['#69b5ff', '#55dac0', '#ffc96b', '#ba9dff', '#ff8fa9', '#83d36c', '#72deec', '#b3bed2'];
  const svgNS = 'http://www.w3.org/2000/svg';
  const svg = (tag, atributos = {}, texto) => {
    const elemento = document.createElementNS(svgNS, tag);
    Object.entries(atributos).forEach(([nome, valor]) => { if (valor !== null) elemento.setAttribute(nome, valor); });
    if (texto !== undefined) elemento.textContent = texto;
    return elemento;
  };
  const numero = valor => (typeof valor === 'number' || (typeof valor === 'string' && valor.trim() !== '')) && Number.isFinite(Number(valor)) ? Number(valor) : null;
  const corSerie = (serie, indice) => /^#[a-f\d]{6}$/i.test(serie.cor || '') ? serie.cor : cores[indice % cores.length];
  const formato = (valor, tipo) => {
    if (valor === null) return '—';
    if (tipo === 'duracao') {
      if (valor < 0) return '—';
      const minutos = Math.round(valor);
      return minutos < 60 ? `${minutos}min` : `${Math.floor(minutos / 60)}h ${String(minutos % 60).padStart(2, '0')}min`;
    }
    const opcoes = tipo === 'moeda' ? { style: 'currency', currency: 'BRL', maximumFractionDigits: 2 } : { maximumFractionDigits: 2 };
    return valor.toLocaleString('pt-BR', opcoes) + (tipo === 'percentual' ? '%' : '');
  };
  const categoria = (linha, definicao) => String(linha[definicao.campoCategoria] == null ? 'Sem categoria' : linha[definicao.campoCategoria]);
  const abreviar = (texto, tamanho) => texto.length > tamanho ? texto.slice(0, tamanho - 1) + '…' : texto;

  function legenda(itens) {
    const lista = document.createElement('ul');
    lista.className = 'grafico-legenda';
    itens.forEach(item => {
      const li = document.createElement('li');
      const ponto = document.createElement('i');
      ponto.style.backgroundColor = item.cor;
      ponto.setAttribute('aria-hidden', 'true');
      const nome = document.createElement('span');
      nome.textContent = item.titulo;
      li.append(ponto, nome);
      if (item.valor !== undefined) {
        const valor = document.createElement('strong');
        valor.textContent = item.valor;
        li.appendChild(valor);
      }
      lista.appendChild(li);
    });
    return lista;
  }

  function calcularResumo(definicao, resumo, linhas) {
    const somar = (registros, campos) => {
      if (!registros.length) return 0;
      const valores = registros.flatMap(linha => campos.map(campo => numero(linha[campo])));
      // Ausência não equivale a zero: não apresentar um total parcial como completo.
      if (valores.some(valor => valor === null)) return null;
      const total = valores.reduce((soma, valor) => soma + valor, 0);
      return Number.isFinite(total) ? total : null;
    };
    const registros = resumo.categorias ? linhas.filter(linha => resumo.categorias.includes(categoria(linha, definicao))) : linhas;
    let valor = somar(registros, resumo.campos);
    let detalhe = resumo.descricao || '';
    if (resumo.calculo === 'pico') {
      const valores = registros.map(linha => somar([linha], resumo.campos));
      valor = valores.length && valores.every(v => v !== null) ? Math.max(...valores) : null;
      if (valor !== null) {
        const categorias = [...new Set(registros.filter((linha, i) => valores[i] === valor).map(linha => categoria(linha, definicao)))];
        const rotulo = valores.every(v => v === 0) ? 'Sem movimento' : categorias.slice(0, 2).join(' / ') + (categorias.length > 2 ? ` +${categorias.length - 2}` : '');
        detalhe = [rotulo, detalhe].filter(Boolean).join(' · ');
      }
    }
    if (resumo.calculo === 'media') valor = valor !== null && registros.length ? valor / registros.length : null;
    if (resumo.calculo === 'percentual') {
      const total = somar(linhas, resumo.camposTotal);
      valor = valor !== null && total !== null && total > 0 ? valor / total * 100 : null;
    }
    if (!Number.isFinite(valor)) valor = null;
    return { texto: formato(valor, resumo.calculo === 'percentual' ? 'percentual' : (resumo.formato || definicao.formato)), detalhe };
  }

  function resumos(container, definicao, linhas) {
    if (!definicao.resumos || !definicao.resumos.length) return;
    const lista = document.createElement('div');
    lista.className = 'grafico-resumos';
    lista.style.setProperty('--quantidade', definicao.resumos.length);
    definicao.resumos.forEach(resumo => {
      const resultado = calcularResumo(definicao, resumo, linhas);
      const card = document.createElement('article');
      card.className = 'grafico-resumo';
      card.style.setProperty('--cor-resumo', corSerie(resumo, 0));
      const titulo = document.createElement('span');
      titulo.textContent = resumo.titulo;
      const numeroResumo = document.createElement('strong');
      numeroResumo.textContent = resultado.texto;
      card.append(titulo, numeroResumo);
      if (resultado.detalhe) {
        const descricao = document.createElement('small');
        descricao.textContent = resultado.detalhe;
        card.appendChild(descricao);
      }
      if (resumo.complemento) {
        const complemento = document.createElement('small');
        complemento.className = 'grafico-resumo-complemento';
        complemento.textContent = `${resumo.complemento.titulo}: ${calcularResumo(definicao, resumo.complemento, linhas).texto}`;
        card.appendChild(complemento);
      }
      lista.appendChild(card);
    });
    container.appendChild(lista);
  }

  function dimensoes(caixa, listaLegenda) {
    const estilo = getComputedStyle(caixa);
    const rodape = document.querySelector('body > footer');
    const alturaLegenda = listaLegenda ? listaLegenda.offsetHeight + parseFloat(getComputedStyle(listaLegenda).marginTop) : 0;
    const espaco = parseFloat(estilo.paddingTop) + parseFloat(estilo.paddingBottom) + alturaLegenda + parseFloat(getComputedStyle(document.querySelector('main')).paddingBottom) + 6;
    return {
      largura: Math.max(680, caixa.clientWidth - parseFloat(estilo.paddingLeft) - parseFloat(estilo.paddingRight)),
      altura: Math.max(180, Math.min(680, innerHeight - caixa.getBoundingClientRect().top - rodape.offsetHeight - espaco))
    };
  }

  function rosca(caixa, definicao, linhas, titulo) {
    const serie = definicao.series[0];
    let fatias = linhas.map((linha, indice) => ({ titulo: categoria(linha, definicao), valor: numero(linha[serie.campo]), cor: corSerie({ cor: definicao.coresCategorias && definicao.coresCategorias[categoria(linha, definicao)] }, indice) }));
    if (fatias.some(fatia => fatia.valor === null || fatia.valor < 0)) throw new Error('O gráfico de rosca precisa de valores numéricos iguais ou maiores que zero.');
    const total = fatias.reduce((soma, fatia) => soma + fatia.valor, 0);
    if (!Number.isFinite(total)) throw new Error('Os valores do gráfico excedem o limite de exibição.');
    if (fatias.length > 8) {
      fatias = fatias.slice(0, 7).concat({ titulo: `Outros (${fatias.length - 7})`, valor: fatias.slice(7).reduce((soma, fatia) => soma + fatia.valor, 0), cor: cores[7] });
    }
    caixa.classList.add('grafico-rosca');
    const desenho = svg('svg', { viewBox: '0 0 460 360', class: 'grafico-svg', role: 'img', 'aria-label': titulo });
    desenho.style.height = `${dimensoes(caixa).altura}px`;
    desenho.appendChild(svg('title', {}, titulo));
    desenho.appendChild(svg('circle', { cx: 230, cy: 180, r: 125, class: 'grafico-anel', fill: 'none', 'stroke-width': 42 }));
    const comprimento = 2 * Math.PI * 125;
    let acumulado = 0;
    fatias.forEach(fatia => {
      if (!total || !fatia.valor) return;
      const tamanho = fatia.valor / total * comprimento;
      const arco = svg('circle', { cx: 230, cy: 180, r: 125, fill: 'none', stroke: fatia.cor, 'stroke-width': 42, 'stroke-dasharray': `${tamanho} ${comprimento - tamanho}`, 'stroke-dashoffset': -acumulado, transform: 'rotate(-90 230 180)', class: 'grafico-fatia' });
      arco.appendChild(svg('title', {}, `${fatia.titulo}: ${formato(fatia.valor, definicao.formato)}`));
      desenho.appendChild(arco);
      acumulado += tamanho;
    });
    desenho.appendChild(svg('text', { x: 230, y: 179, 'text-anchor': 'middle', class: 'grafico-total', textLength: formato(total, definicao.formato).length > 13 ? 195 : null, lengthAdjust: 'spacingAndGlyphs' }, formato(total, definicao.formato)));
    desenho.appendChild(svg('text', { x: 230, y: 211, 'text-anchor': 'middle', class: 'grafico-rotulo-total' }, total ? (definicao.rotuloTotal || 'TOTAL') : 'Sem valores no período'));
    caixa.append(desenho, legenda(fatias.map(fatia => ({ ...fatia, valor: `${formato(fatia.valor, definicao.formato)} · ${formato(total ? fatia.valor / total * 100 : 0, 'percentual')}` }))));
  }

  function horizontais(caixa, definicao, linhas, titulo) {
    const totais = linhas.map(linha => definicao.series.reduce((total, serie) => {
      const valor = numero(linha[serie.campo]);
      if (valor === null || valor < 0) throw new Error('As barras empilhadas precisam de valores numéricos iguais ou maiores que zero.');
      return total + valor;
    }, 0));
    const total = totais.reduce((soma, valor) => soma + valor, 0);
    if (!Number.isFinite(total)) throw new Error('Os valores do gráfico excedem o limite de exibição.');
    const listaLegenda = legenda(definicao.series.map((serie, indice) => ({ titulo: serie.titulo, cor: corSerie(serie, indice) })));
    caixa.appendChild(listaLegenda);
    const { largura, altura } = dimensoes(caixa, listaLegenda);
    const detalhes = definicao.detalhesCategoria || [];
    const intervalo = (altura - 40) / linhas.length;
    // Configurações anteriores podem não informar detalhesEmColunas.
    // Dois detalhes sob cada nome precisam caber antes da próxima categoria.
    const faltouAltura = detalhes.length > 1 && intervalo < 28 + detalhes.length * 23;
    const detalhesEmColunas = detalhes.length > 0 && (definicao.detalhesEmColunas === true || faltouAltura);
    const larguraColuna = Math.min(170, largura * .14);
    const esquerda = Math.min(310, largura * .28);
    const direita = 90 + (detalhesEmColunas ? detalhes.length * larguraColuna : 0);
    const maximo = Math.max(1, ...totais);
    const plot = largura - esquerda - direita;
    const desenho = svg('svg', { viewBox: `0 0 ${largura} ${altura}`, class: 'grafico-svg', role: 'img', 'aria-label': titulo });
    desenho.appendChild(svg('title', {}, titulo));
    const colunaX = i => largura - (detalhes.length - i - 1) * larguraColuna - 8;
    if (detalhesEmColunas) detalhes.forEach((detalhe, i) => {
      const cabecalho = svg('text', { x: colunaX(i), y: 18, 'text-anchor': 'end', class: 'grafico-detalhe grafico-cabecalho-coluna' }, abreviar(detalhe.titulo, Math.floor((larguraColuna - 12) / 9)));
      cabecalho.appendChild(svg('title', {}, detalhe.titulo));
      desenho.appendChild(cabecalho);
    });
    desenho.appendChild(svg('text', { x: largura - direita + 20, y: 18, class: 'grafico-eixo' }, 'Total'));
    linhas.forEach((linha, indice) => {
      const centro = 34 + intervalo * (indice + .5);
      const alturaBarra = Math.min(56, intervalo * .52);
      const rotuloY = centro + 6 - (detalhesEmColunas ? 0 : detalhes.length * 12);
      const rotulo = svg('text', { x: 0, y: rotuloY, class: 'grafico-eixo' }, abreviar(categoria(linha, definicao), Math.floor((esquerda - 20) / 11)));
      rotulo.appendChild(svg('title', {}, categoria(linha, definicao)));
      desenho.appendChild(rotulo);
      detalhes.forEach((detalhe, i) => {
        const valor = formato(numero(linha[detalhe.campo]), detalhe.formato);
        if (detalhesEmColunas) {
          const descricao = svg('text', { x: colunaX(i), y: centro + 7, 'text-anchor': 'end', class: 'grafico-valor grafico-detalhe-coluna', fill: '#b8cce5' }, valor);
          descricao.appendChild(svg('title', {}, `${categoria(linha, definicao)} · ${detalhe.titulo}: ${valor}`));
          desenho.appendChild(descricao);
          return;
        }
        const texto = `${detalhe.titulo}: ${valor}`;
        const descricao = svg('text', { x: 0, y: rotuloY + 23 * (i + 1), class: 'grafico-detalhe' }, abreviar(texto, Math.floor((esquerda - 20) / 8)));
        descricao.appendChild(svg('title', {}, texto));
        desenho.appendChild(descricao);
      });
      desenho.appendChild(svg('rect', { x: esquerda, y: centro - alturaBarra / 2, width: plot, height: alturaBarra, rx: 5, class: 'grafico-trilho' }));
      let acumulado = 0;
      definicao.series.forEach((serie, indiceSerie) => {
        const valor = numero(linha[serie.campo]);
        const larguraBarra = valor / maximo * plot;
        const x = esquerda + acumulado / maximo * plot;
        const marca = svg('rect', { x, y: centro - alturaBarra / 2, width: larguraBarra, height: alturaBarra, fill: corSerie(serie, indiceSerie), class: 'grafico-barra' });
        marca.appendChild(svg('title', {}, `${categoria(linha, definicao)} · ${serie.titulo}: ${formato(valor, definicao.formato)}`));
        desenho.appendChild(marca);
        const texto = formato(valor, definicao.formato);
        if (!(detalhesEmColunas && definicao.series.length === 1) && larguraBarra > texto.length * 18 + 12) desenho.appendChild(svg('text', { x: x + larguraBarra / 2, y: centro + 7, 'text-anchor': 'middle', class: 'grafico-valor grafico-valor-interno' }, texto));
        acumulado += valor;
      });
      desenho.appendChild(svg('text', { x: esquerda + plot + 20, y: centro + 7, class: 'grafico-valor', fill: '#f3f6fc' }, formato(totais[indice], definicao.formato)));
    });
    const area = document.createElement('div');
    area.className = 'grafico-area';
    area.appendChild(desenho);
    caixa.prepend(area);
  }

  function cartesiano(caixa, definicao, linhas, titulo) {
    const series = definicao.series;
    const valores = linhas.flatMap(linha => series.map(serie => numero(linha[serie.campo]))).filter(valor => valor !== null);
    if (!valores.length) throw new Error('Não há valores numéricos para desenhar este gráfico.');
    const menor = Math.min(0, ...valores);
    const maior = Math.max(0, ...valores);
    const amplitude = maior - menor;
    if (!Number.isFinite(amplitude)) throw new Error('Os valores do gráfico excedem o limite de exibição.');
    const bruto = (amplitude || 1) / 4;
    const potencia = Math.pow(10, Math.floor(Math.log10(bruto)));
    const passo = [1, 2, 5, 10].find(valor => valor * potencia >= bruto) * potencia;
    const minimo = Math.floor(menor / passo) * passo;
    const maximo = Math.ceil(maior / passo) * passo || (menor === 0 ? 1 : 0);
    if (!Number.isFinite(minimo) || !Number.isFinite(maximo) || maximo <= minimo) throw new Error('Os valores do gráfico excedem o limite de exibição.');
    const listaLegenda = legenda(series.map((serie, indice) => ({ titulo: serie.titulo, cor: corSerie(serie, indice) })));
    caixa.appendChild(listaLegenda);
    const { largura, altura } = dimensoes(caixa, listaLegenda);
    const margem = { esquerda: definicao.formato === 'moeda' ? 175 : 80, direita: 40, topo: altura < 320 ? 38 : 60, base: altura < 320 ? 42 : 60 };
    // Duas linhas podem ter rótulos abaixo dos pontos: reservar espaço antes das categorias.
    if (definicao.tipo === 'linhas' && series.length === 2) margem.base = 76;
    const base = altura - margem.base;
    const plot = largura - margem.esquerda - margem.direita;
    const intervalo = plot / linhas.length;
    const y = valor => base - (valor - minimo) / (maximo - minimo) * (base - margem.topo);
    const desenho = svg('svg', { viewBox: `0 0 ${largura} ${altura}`, class: 'grafico-svg', role: 'img', 'aria-label': titulo });
    desenho.appendChild(svg('title', {}, titulo));
    const gradientes = svg('defs');
    series.forEach((serie, indice) => {
      const gradiente = svg('linearGradient', { id: `painel-grafico-cor-${indice}`, x1: '0', y1: '0', x2: '0', y2: '1' });
      gradiente.append(svg('stop', { offset: '0%', 'stop-color': corSerie(serie, indice) }), svg('stop', { offset: '100%', 'stop-color': corSerie(serie, indice), 'stop-opacity': '.72' }));
      gradientes.appendChild(gradiente);
    });
    desenho.appendChild(gradientes);
    for (let i = 0; i <= 4; i++) {
      const valor = minimo + (maximo - minimo) * i / 4;
      const posicao = y(valor);
      desenho.appendChild(svg('line', { x1: margem.esquerda, y1: posicao, x2: largura - margem.direita, y2: posicao, class: 'grafico-grade' }));
      desenho.appendChild(svg('text', { x: margem.esquerda - 12, y: posicao + 6, 'text-anchor': 'end', class: 'grafico-eixo' }, formato(valor, definicao.formato)));
    }
    desenho.appendChild(svg('line', { x1: margem.esquerda, y1: y(0), x2: largura - margem.direita, y2: y(0), class: 'grafico-zero' }));
    linhas.forEach((linha, indice) => {
      const x = margem.esquerda + intervalo * (indice + .5);
      const rotulo = svg('text', { x, y: altura - 18, 'text-anchor': 'middle', class: 'grafico-eixo' }, abreviar(categoria(linha, definicao), Math.max(6, Math.floor(intervalo / 10))));
      rotulo.appendChild(svg('title', {}, categoria(linha, definicao)));
      desenho.appendChild(rotulo);
    });
    series.forEach((serie, indiceSerie) => {
      const cor = corSerie(serie, indiceSerie);
      let caminho = '';
      let segmento = false;
      linhas.forEach((linha, indice) => {
        const valor = numero(linha[serie.campo]);
        if (valor === null) { segmento = false; return; }
        const centro = margem.esquerda + intervalo * (indice + .5);
        const pontoY = y(valor);
        let marca;
        let rotuloX = centro;
        if (definicao.tipo === 'barras') {
          const larguraGrupo = Math.min(intervalo * .72, series.length * 140);
          const larguraBarra = larguraGrupo / series.length;
          const intervaloBarras = Math.max(5, Math.min(12, larguraBarra * .12));
          const x = centro - larguraGrupo / 2 + indiceSerie * larguraBarra + intervaloBarras / 2;
          marca = svg('rect', { x, y: Math.min(y(0), pontoY), width: Math.max(1, larguraBarra - intervaloBarras), height: Math.max(valor === 0 ? 0 : 1, Math.abs(y(0) - pontoY)), rx: 6, fill: `url(#painel-grafico-cor-${indiceSerie})`, stroke: cor, 'stroke-opacity': '.25', class: 'grafico-barra' });
          rotuloX = x + (larguraBarra - intervaloBarras) / 2;
        } else {
          caminho += `${segmento ? 'L' : 'M'}${centro},${pontoY} `;
          segmento = true;
          marca = svg('circle', { cx: centro, cy: pontoY, r: 5, fill: cor, class: 'grafico-ponto' });
        }
        const descricao = `${categoria(linha, definicao)} · ${serie.titulo}: ${formato(valor, definicao.formato)}`;
        marca.appendChild(svg('title', {}, descricao));
        desenho.appendChild(marca);
        // Valores visíveis na TV; séries densas continuam identificadas por legenda e tooltip.
        if (series.length === 1 || (series.length <= 2 && (linhas.length <= 6 || indice === linhas.length - 1))) {
          let rotuloY = pontoY + (valor < 0 ? 22 : -12);
          if (definicao.tipo === 'linhas' && series.length === 2) {
            const outroValor = numero(linha[series[1 - indiceSerie].campo]);
            if (outroValor !== null && Math.abs(y(outroValor) - pontoY) < 48) {
              const acima = pontoY < y(outroValor) || (pontoY === y(outroValor) && indiceSerie === 0);
              rotuloY = pontoY + (acima ? -14 : 28);
            }
          }
          desenho.appendChild(svg('text', { x: rotuloX, y: rotuloY, 'text-anchor': 'middle', fill: cor, class: 'grafico-valor' }, formato(valor, definicao.formato)));
        }
      });
      if (definicao.tipo === 'linhas' && caminho) desenho.appendChild(svg('path', { d: caminho, fill: 'none', stroke: cor, 'stroke-width': 3, class: 'grafico-linha' }));
    });
    const area = document.createElement('div');
    area.className = 'grafico-area';
    area.appendChild(desenho);
    caixa.prepend(area);
  }

  window.PainelGraficos = {
    desenhar(container, painel, linhas, todasLinhas = linhas) {
      container.replaceChildren();
      resumos(container, painel.grafico, todasLinhas);
      if (painel.grafico.subtitulo) {
        const subtitulo = document.createElement('p');
        subtitulo.className = 'grafico-subtitulo';
        subtitulo.textContent = painel.grafico.subtitulo;
        container.appendChild(subtitulo);
      }
      const caixa = document.createElement('div');
      caixa.className = 'grafico-card';
      caixa.dataset.grafico = painel.grafico.tipo;
      container.appendChild(caixa);
      if (painel.grafico.tipo === 'rosca') rosca(caixa, painel.grafico, linhas, painel.titulo);
      else if (painel.grafico.tipo === 'barras_horizontais') horizontais(caixa, painel.grafico, linhas, painel.titulo);
      else cartesiano(caixa, painel.grafico, linhas, painel.titulo);
    }
  };
})();
