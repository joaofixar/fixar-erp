/* Shell do desktop logado. A faixa fica fora do #desktop e não usa a classe
   .tabs: o C.R.M. chama $('.tabs') na página inteira. */
(function () {
  var abas = [];
  var visivel = null;
  var abrirOriginal = null;

  function escapar(texto) {
    return String(texto).replace(/[&<>"']/g, function (caractere) {
      return {
        "&": "&amp;",
        "<": "&lt;",
        ">": "&gt;",
        '"': "&quot;",
        "'": "&#39;",
      }[caractere];
    });
  }

  function nomeCurto() {
    if (window.g && g.nomeEmpresaAbreviado) {
      return String(g.nomeEmpresaAbreviado).trim();
    }
    var campo = document.querySelector("#campoEmpresa span");
    return campo ? campo.textContent.trim() : "";
  }

  function nomeCompleto() {
    var meta = document.querySelector('meta[name="fx-empresa-nome"]');
    var nome = meta ? meta.content.trim() : "";
    return nome || nomeCurto();
  }

  function letraDoAcesso() {
    var campo = document.querySelector("#campoUsuario span");
    if (campo && campo.textContent.trim()) {
      return campo.textContent.trim().charAt(0);
    }
    var bloco = document.querySelector("#footer > div");
    if (!bloco) return "";
    var partes = bloco.textContent.split("|");
    for (var i = 0; i < partes.length; i++) {
      var texto = partes[i].trim();
      if (!texto) continue;
      if (i === 0) continue;
      return texto.charAt(0);
    }
    return "";
  }

  function gruposDoMenu() {
    var barra = document.querySelector("#barra");
    if (!barra) return [];
    var grupos = [];
    var itens = barra.children;
    for (var i = 0; i < itens.length; i++) {
      var grupo = itens[i];
      if (grupo.id === "menuInicio" || grupo.id === "menuSair") continue;
      var titulo = grupo.querySelector(":scope > a");
      if (!titulo) continue;
      var lista = grupo.querySelector(":scope > ul.menu");
      if (!lista) continue;
      var entradas = [];
      var linhas = lista.children;
      for (var j = 0; j < linhas.length; j++) {
        var ancora = linhas[j].querySelector("a");
        if (!ancora) continue;
        var acao = ancora.getAttribute("onclick");
        if (!acao) continue;
        entradas.push({
          nome: ancora.textContent.replace(/\s+/g, " ").trim(),
          acao: acao,
        });
      }
      if (!entradas.length) continue;
      grupos.push({
        nome: titulo.textContent.replace(/\s+/g, " ").trim(),
        entradas: entradas,
      });
    }
    return grupos;
  }

  function htmlMenu() {
    return gruposDoMenu()
      .map(function (grupo) {
        var itens = grupo.entradas
          .map(function (entrada) {
            return (
              '<button type="button" class="tw:mx-1.5 tw:block tw:w-[calc(100%-0.75rem)] tw:rounded-md tw:px-2 tw:py-1.5 tw:text-left tw:text-side-ink tw:hover:bg-white/10" onclick="' +
              escapar(entrada.acao) +
              '">' +
              escapar(entrada.nome) +
              "</button>"
            );
          })
          .join("");
        return (
          '<div><p class="tw:m-0 tw:px-3.5 tw:pb-1 tw:pt-3 tw:text-[11px] tw:font-semibold tw:uppercase tw:tracking-wide tw:text-side-muted">' +
          escapar(grupo.nome) +
          "</p>" +
          itens +
          "</div>"
        );
      })
      .join("");
  }

  function medirArea() {
    var area = document.getElementById("fx-area");
    if (!area) return;
    var rect = area.getBoundingClientRect();
    var desktop = document.getElementById("desktop");
    window.oneWindow = false;
    window.desktopLeft = Math.round(rect.left);
    window.desktopRight = 0;
    window.desktopDown = 0;
    window.desktopUp = Math.round(rect.top);
    window.desktopUpFull = window.desktopUp;
    window.desktopWidth = Math.round(rect.right);
    window.desktopHeight = Math.round(rect.height);
    if (!desktop) return;
    desktop.style.position = "fixed";
    desktop.style.top = rect.top + "px";
    desktop.style.left = rect.left + "px";
    desktop.style.width = rect.width + "px";
    desktop.style.height = rect.height + "px";
    desktop.style.margin = "0";
  }

  function caixas(raiz) {
    var lista = [];
    window.jQuery(".ui-dialog-content").each(function () {
      var conteudo = window.jQuery(this);
      if (conteudo.data("janelaIdRoot") !== raiz) return;
      lista.push(conteudo.parent());
    });
    return lista;
  }

  function aplicarMiolo() {
    window.jQuery(".ui-dialog-content").each(function () {
      var conteudo = window.jQuery(this);
      var raiz = conteudo.data("janelaIdRoot");
      if (indiceDaAba(raiz) < 0) return;
      var aberto = raiz === visivel;
      var caixa = conteudo.parent();
      caixa.toggle(aberto);
      if (aberto) {
        caixa.removeClass("ui-widget-content-gray").css("opacity", "1");
      }
      window.jQuery("#w-overlay-" + conteudo.attr("id")).toggle(aberto);
    });
  }

  function desenharFaixa() {
    var faixa = document.getElementById("fx-faixa");
    if (!faixa) return;
    faixa.innerHTML = abas
      .map(function (aba) {
        var ativa = aba.raiz === visivel;
        var classe = ativa
          ? "tw:max-w-56 tw:shrink-0 tw:truncate tw:rounded-t-md tw:bg-paper tw:px-3 tw:py-1.5 tw:text-ink"
          : "tw:max-w-56 tw:shrink-0 tw:truncate tw:rounded-t-md tw:px-3 tw:py-1.5 tw:text-muted";
        return (
          '<button type="button" class="' +
          classe +
          '" data-fx-aba="' +
          escapar(aba.raiz) +
          '" aria-selected="' +
          (ativa ? "true" : "false") +
          '">' +
          escapar(aba.titulo) +
          "</button>"
        );
      })
      .join("");
  }

  function indiceDaAba(raiz) {
    for (var i = 0; i < abas.length; i++) {
      if (abas[i].raiz === raiz) return i;
    }
    return -1;
  }

  function esperarMiolo(raiz, pronto) {
    var tentativas = 0;
    (function olhar() {
      if (caixas(raiz).length || tentativas > 40) {
        pronto();
        return;
      }
      tentativas += 1;
      window.setTimeout(olhar, 50);
    })();
  }

  function lembrarAba(raiz, titulo) {
    if (indiceDaAba(raiz) < 0) {
      abas.push({ raiz: raiz, titulo: titulo || raiz });
    }
    visivel = raiz;
    desenharFaixa();
    esperarMiolo(raiz, aplicarMiolo);
  }

  function aoAbrirJanela() {
    var evento = arguments[0];
    var raiz = arguments[1];
    var titulo = arguments[4];
    var posicao = arguments[10];
    if (evento && evento.ctrlKey) {
      return abrirOriginal.apply(this, arguments);
    }
    medirArea();
    var resultado = abrirOriginal.apply(this, arguments);
    if (posicao === "topleft" && raiz) {
      lembrarAba(String(raiz), titulo ? String(titulo) : String(raiz));
    }
    return resultado;
  }

  function montar() {
    if (!document.getElementById("topo") || !document.getElementById("desktop")) {
      return;
    }
    if (document.getElementById("fx-shell")) return;
    if (window.g && String(g.oneWindow) === "1") return;

    var curto = nomeCurto();
    var completo = nomeCompleto();
    var shell = document.createElement("div");
    shell.id = "fx-shell";
    shell.className =
      "tw:fixed tw:inset-0 tw:grid tw:h-dvh tw:grid-cols-[232px_minmax(0,1fr)] tw:bg-canvas tw:font-sans tw:text-[13px] tw:leading-snug tw:text-ink";
    shell.innerHTML =
      '<aside class="tw:flex tw:min-h-0 tw:flex-col tw:bg-side tw:text-side-ink">' +
      '<p class="tw:m-0 tw:px-3.5 tw:pt-4 tw:text-[15px] tw:font-semibold tw:tracking-wide">FX ERP</p>' +
      '<p class="tw:m-0 tw:px-3.5 tw:pb-3 tw:pt-1 tw:text-xs tw:leading-snug tw:text-side-muted" title="' +
      escapar(completo) +
      '">' +
      escapar(curto) +
      "</p>" +
      '<nav class="tw:min-h-0 tw:flex-1 tw:overflow-auto tw:pb-3">' +
      htmlMenu() +
      "</nav></aside>" +
      '<div class="tw:grid tw:min-h-0 tw:min-w-0 tw:grid-rows-[48px_36px_minmax(0,1fr)]">' +
      '<header class="tw:flex tw:min-w-0 tw:items-center tw:gap-2 tw:border-b tw:border-solid tw:border-line tw:bg-paper tw:px-3">' +
      '<div class="tw:flex-1"></div>' +
      '<div class="tw:flex tw:items-center tw:gap-0.5">' +
      '<button type="button" class="tw:grid tw:size-9 tw:place-items-center tw:rounded-lg tw:text-ink tw:hover:bg-black/5" aria-label="Pesquisar o menu">' +
      '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>' +
      "</button>" +
      '<button type="button" class="tw:grid tw:size-9 tw:place-items-center tw:rounded-lg tw:text-ink tw:hover:bg-black/5" aria-label="Alertas">' +
      '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9a6 6 0 1 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9"/><path d="M10 20a2 2 0 0 0 4 0"/></svg>' +
      "</button>" +
      '<button type="button" class="tw:grid tw:size-7 tw:place-items-center tw:rounded-full tw:bg-accent tw:text-xs tw:font-semibold tw:text-white" aria-label="Usuário">' +
      escapar(letraDoAcesso()) +
      "</button></div></header>" +
      '<div id="fx-faixa" class="tw:flex tw:min-w-0 tw:items-end tw:gap-0.5 tw:overflow-x-auto tw:bg-canvas tw:px-2"></div>' +
      '<div id="fx-area" class="tw:min-h-0 tw:min-w-0"></div></div>';
    document.body.appendChild(shell);

    document.getElementById("fx-faixa").addEventListener("click", function (evento) {
      var botao = evento.target.closest("[data-fx-aba]");
      if (!botao) return;
      visivel = botao.getAttribute("data-fx-aba");
      desenharFaixa();
      aplicarMiolo();
    });

    // O Hardness fecha a janela que deixou de ser a visível. A aba precisa ficar.
    window.closeLastBtn = false;
    window.fixDesktopSize = medirArea;
    medirArea();
    window.addEventListener("resize", medirArea);

    abrirOriginal = window.abrirJanela;
    window.abrirJanela = aoAbrirJanela;
  }

  function iniciar() {
    if (typeof window.abrirJanela !== "function" || !window.jQuery) {
      window.setTimeout(iniciar, 30);
      return;
    }
    montar();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", iniciar);
  } else {
    iniciar();
  }
})();
