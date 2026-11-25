/* Shell do desktop logado. A faixa fica fora do #desktop e não usa a classe
   .tabs: o C.R.M. chama $('.tabs') na página inteira.
   A lista de abas vive só nesta página: recarregar ou entrar de novo abre vazia.
   Fechar a aba chama dialog("close") sem tecla Esc. A pergunta de algumas
   janelas continua no Esc delas (beforeClose em sistema.js, keyCode 27).
   A janela (inclusão, Cliente/Fornecedor) usa a raiz da aba visível, empilha
   por cima e some junto quando a aba fecha ou quando outra aba fica visível.
   Com a faixa vazia, a primeira janela auto ou forcarAuto no centro ocupa a
   área e segue fechável. O atalho F2 manda forcarAuto; o abrirJanela do
   sistema.js troca isso por auto, e o novoDialog, sem diálogo aberto,
   trocaria o caso por full sem barra de título. Abrir um módulo fecha essa
   janela solta e o que estiver empilhado nela; ela não volta.
   A paleta pesquisa o menu do perfil e nasce no body, fora do #desktop.
   Ctrl+K alterna. A lupa abre a mesma paleta. Esc, Ctrl+K de novo ou um
   clique fora fecha sem abrir. Os atalhos Alt+letra do menu (sistema.js,
   abrirMenuPorNome e Alt+M) saem. Setas, Enter e Esc deixam de andar nesse
   menu e seguem no miolo.
   A empresa sob a marca é rótulo quando há uma só. Com mais de uma, o
   controle abre a lista pelo nome completo. Trocar chama a troca do
   Hardness e, no retorno, esvazia a faixa e as janelas, repinta o menu e
   a área. O avatar mostra o login e, abaixo, o perfil. A lista de usuários
   só entra se o rodapé já oferece a troca, e faz o mesmo que a empresa.
   Sair vai para /logout. O nome da pessoa fica de fora. Os dois menus
   nascem no body, como a paleta: o #desktop é uma camada por cima da
   área, e um menu dentro do shell ficaria atrás da janela aberta.
   O sino abre o pop Alertas pendurado no ícone. A lista, a contagem do
   que não está oculto, ocultar, atualizar, enviar e o suporte continuam
   os do Hardness (alertAdicionar e acoesNotificacoes em sistema.js, o
   painel #messageAlert de index.tpl). O Aviso fica na área de trabalho.
   Com a faixa vazia, a área mostra o cartão do certificado quando ele
   existe, os avisos e as notas da mais nova à mais antiga. Uma aba
   visível tira os três. Versão, cobrança, últimas atualizações, logo de
   fundo e notificação de vídeo ficam de fora. A senha continua janela.
   O cockpit que o sistema abre sozinho no login não vira aba.
   Empresa, avatar e paleta fecham esse pop. Fechar descarta o rascunho. */
(function () {
  var abas = [];
  var visivel = null;
  var abrirOriginal = null;
  var arrastando = null;
  var paletaAberta = false;
  var filtroPaleta = "";
  var destaquePaleta = -1;
  var popAberto = null;

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
    var campo = document.querySelector("#campoEmpresa span");
    if (campo && campo.textContent.trim()) return campo.textContent.trim();
    if (window.g && g.nomeEmpresaAbreviado) {
      return String(g.nomeEmpresaAbreviado).trim();
    }
    return "";
  }

  function nomeCompleto() {
    var meta = document.querySelector('meta[name="fx-empresa-nome"]');
    var nome = meta ? meta.content.trim() : "";
    return nome || nomeCurto();
  }

  function decodificar(texto) {
    var el = document.createElement("textarea");
    el.innerHTML = texto;
    return el.value;
  }

  function fatiasAntesDoBanco() {
    var fonte = document.querySelector("#footer > div > font[title]");
    if (!fonte) return [];
    var no = fonte.previousSibling;
    while (no && no.nodeType !== 3) no = no.previousSibling;
    if (!no) return [];
    return no.textContent
      .replace(/\u00a0/g, " ")
      .split("|")
      .map(function (parte) {
        return parte.replace(/\s+/g, " ").trim();
      })
      .filter(Boolean);
  }

  function loginDoAcesso() {
    var campo = document.querySelector("#campoUsuario span");
    if (campo && campo.textContent.trim()) {
      return campo.textContent.replace(/\s+/g, " ").trim();
    }
    var fatias = fatiasAntesDoBanco();
    if (fatias.length >= 2) return fatias[fatias.length - 2];
    return fatias[0] || "";
  }

  function perfilDoAcesso() {
    var fatias = fatiasAntesDoBanco();
    if (!fatias.length) return "";
    if (document.getElementById("campoUsuario")) return fatias[0];
    return fatias[fatias.length - 1];
  }

  function loginDaOpcao(texto) {
    var limpo = String(texto)
      .replace(/\u00a0/g, " ")
      .replace(/\s+/g, " ")
      .trim();
    return limpo.split(" ")[0] || "";
  }

  function empresas() {
    var select = document.getElementById("selecionaEmpresa");
    if (!select || select.options.length < 2) return [];
    var lista = [];
    for (var i = 0; i < select.options.length; i++) {
      var opcao = select.options[i];
      var nome = opcao.getAttribute("data-nome") || opcao.textContent;
      nome = String(nome).replace(/\s+/g, " ").trim();
      if (!nome) continue;
      lista.push({ id: opcao.value, nome: nome });
    }
    return lista;
  }

  function usuariosDoAcesso() {
    var select = document.getElementById("selecionaUsuario");
    if (!select) return [];
    var lista = [];
    for (var i = 0; i < select.options.length; i++) {
      var opcao = select.options[i];
      var login = loginDaOpcao(opcao.textContent);
      if (!login) continue;
      lista.push({ id: opcao.value, login: login });
    }
    return lista;
  }

  function pintarEmpresa() {
    var caixa = document.getElementById("fx-empresa");
    if (!caixa) return;
    var curto = nomeCurto();
    var completo = nomeCompleto();
    var lista = empresas();
    var controle;
    if (lista.length < 2) {
      controle =
        '<p class="tw:m-0 tw:px-3.5 tw:pb-3 tw:pt-1 tw:text-xs tw:leading-snug tw:text-side-muted" title="' +
        escapar(completo) +
        '">' +
        escapar(curto) +
        "</p>";
    } else {
      controle =
        '<button type="button" data-fx-empresa class="tw:m-0 tw:block tw:w-full tw:rounded-md tw:px-3.5 tw:pb-3 tw:pt-1 tw:text-left tw:text-xs tw:leading-snug tw:text-side-muted tw:hover:bg-white/10" title="' +
        escapar(completo) +
        '" aria-expanded="' +
        (popAberto === "empresa" ? "true" : "false") +
        '" aria-label="Empresa">' +
        escapar(curto) +
        "</button>";
    }
    caixa.innerHTML = controle;
  }

  function htmlPopEmpresa() {
    var select = document.getElementById("selecionaEmpresa");
    var atual = select ? String(select.value) : "";
    return (
      '<div data-fx-pop role="listbox" aria-label="Empresas" class="tw:fixed tw:z-[200002] tw:w-64 tw:rounded-lg tw:border tw:border-solid tw:border-line tw:bg-paper tw:p-1.5 tw:text-[13px] tw:text-ink tw:shadow-[0_10px_30px_#141c2829]">' +
      empresas()
        .map(function (empresa) {
          var marcada = String(empresa.id) === atual;
          return (
            '<button type="button" role="option" data-fx-empresa-item="' +
            escapar(empresa.id) +
            '" aria-selected="' +
            (marcada ? "true" : "false") +
            '" class="tw:block tw:w-full tw:rounded-md tw:px-2 tw:py-1.5 tw:text-left tw:text-ink' +
            (marcada ? " tw:font-semibold" : "") +
            ' tw:hover:bg-black/5">' +
            escapar(empresa.nome) +
            "</button>"
          );
        })
        .join("") +
      "</div>"
    );
  }

  function pintarAvatar() {
    var caixa = document.getElementById("fx-avatar");
    if (!caixa) return;
    var login = loginDoAcesso();
    var letra = login ? login.charAt(0) : "";
    var aberto = popAberto === "avatar";
    caixa.innerHTML =
      '<button type="button" data-fx-avatar class="tw:grid tw:size-7 tw:place-items-center tw:rounded-full tw:bg-accent tw:text-xs tw:font-semibold tw:text-white" aria-label="Usuário" aria-expanded="' +
      (aberto ? "true" : "false") +
      '">' +
      escapar(letra) +
      "</button>";
  }

  function htmlPopAvatar() {
    var login = loginDoAcesso();
    var perfil = perfilDoAcesso();
    var usuarios = usuariosDoAcesso();
    var select = document.getElementById("selecionaUsuario");
    var atual = select ? String(select.value) : "";
    var lista = usuarios
      .map(function (usuario) {
        var marcada = String(usuario.id) === atual;
        return (
          '<button type="button" role="menuitem" data-fx-usuario-item="' +
          escapar(usuario.id) +
          '" class="tw:block tw:w-full tw:rounded-md tw:px-2 tw:py-1.5 tw:text-left tw:text-ink' +
          (marcada ? " tw:font-semibold" : "") +
          ' tw:hover:bg-black/5">' +
          escapar(usuario.login) +
          "</button>"
        );
      })
      .join("");
    return (
      '<div data-fx-pop role="menu" aria-label="Usuário" class="tw:fixed tw:z-[200002] tw:w-64 tw:rounded-lg tw:border tw:border-solid tw:border-line tw:bg-paper tw:p-1.5 tw:text-[13px] tw:leading-snug tw:text-ink tw:shadow-[0_10px_30px_#141c2829]">' +
      '<p class="tw:m-0 tw:px-2 tw:pt-1.5 tw:font-semibold tw:text-ink">' +
      escapar(login) +
      "</p>" +
      '<p class="tw:m-0 tw:px-2 tw:pb-1.5 tw:text-xs tw:text-muted">' +
      escapar(perfil) +
      "</p>" +
      lista +
      '<button type="button" data-fx-sair role="menuitem" class="tw:mt-1 tw:block tw:w-full tw:rounded-md tw:border-t tw:border-solid tw:border-line tw:px-2 tw:py-1.5 tw:text-left tw:text-ink tw:hover:bg-black/5">Sair</button>' +
      "</div>"
    );
  }

  function soltarPop() {
    var pop = document.querySelector("[data-fx-pop]");
    if (pop) pop.remove();
  }

  function colocarPop(html, ancora, lado) {
    soltarPop();
    if (!html || !ancora) return;
    var suporte = document.createElement("div");
    suporte.innerHTML = html;
    var pop = suporte.firstElementChild;
    if (!pop) return;
    document.body.appendChild(pop);
    var rect = ancora.getBoundingClientRect();
    var margem = lado === "direita" ? 4 : 0;
    pop.style.top = rect.bottom + margem + "px";
    pop.style.left = "0px";
    var esquerda =
      lado === "direita" ? rect.right - pop.offsetWidth : rect.left + 8;
    if (esquerda < 8) esquerda = 8;
    pop.style.left = esquerda + "px";
  }

  function sincronizarPop() {
    if (popAberto === "empresa") {
      colocarPop(
        htmlPopEmpresa(),
        document.getElementById("fx-empresa"),
        "esquerda",
      );
      return;
    }
    if (popAberto === "avatar") {
      colocarPop(
        htmlPopAvatar(),
        document.getElementById("fx-avatar"),
        "direita",
      );
      return;
    }
    soltarPop();
  }

  function redesenharAcesso() {
    pintarEmpresa();
    pintarAvatar();
    pintarSino();
    sincronizarPop();
  }

  function painelAlertas() {
    return document.querySelector("#messageAlert .messages");
  }

  function tituloAlertas() {
    var painel = painelAlertas();
    if (!painel) return;
    var titulo = painel.querySelector(".header span");
    if (titulo) titulo.textContent = "Alertas";
    painel.setAttribute("role", "dialog");
    painel.setAttribute("aria-label", "Alertas");
  }

  function descartarRascunho() {
    var caixa = document.querySelector("#messageAlert .boxenviarmensagem");
    if (!caixa) return;
    var area = caixa.querySelector("textarea");
    if (area) area.value = "";
    var select = caixa.querySelector("select");
    if (select) select.selectedIndex = 0;
    var data = caixa.querySelector(".campoData");
    if (data) data.value = "";
    var tempo = caixa.querySelector(".campoTempo");
    if (tempo) tempo.value = "";
    var travar = caixa.querySelector('input[name="msgTravar"]');
    if (travar) travar.checked = false;
    var erros = caixa.querySelectorAll("span");
    for (var i = 0; i < erros.length; i++) erros[i].style.display = "none";
    caixa.style.display = "none";
  }

  function restaurarItensOcultos() {
    var itens = document.querySelectorAll("#messageAlert li.msgalert");
    for (var i = 0; i < itens.length; i++) {
      var estilo = itens[i].getAttribute("style") || "";
      if (!/lightgreen/i.test(estilo)) itens[i].setAttribute("style", "");
    }
    var olho = document.querySelector(
      "#messageAlert .exibirOcultarNotificacoes",
    );
    if (!olho) return;
    olho.classList.remove("exibindo");
    olho.setAttribute("src", "/hardness3/static/img/check.png");
    // Rótulo de index.tpl com o olho desligado. O item do pop continua Alerta.
    olho.setAttribute("title", "Exibir notificações desabilitadas");
  }

  function posicionarSino() {
    var botao = document.querySelector("[data-fx-sino]");
    var painel = painelAlertas();
    if (!botao || !painel) return;
    var rect = botao.getBoundingClientRect();
    var largura = painel.offsetWidth || 450;
    var esquerda = rect.right - largura;
    if (esquerda < 8) esquerda = 8;
    painel.style.position = "fixed";
    painel.style.top = rect.bottom + 4 + "px";
    painel.style.right = "auto";
    painel.style.left = esquerda + "px";
    painel.style.zIndex = "200002";
    var alerta = document.getElementById("messageAlert");
    if (alerta) alerta.style.zIndex = "200002";
    var corpo = painel.querySelector(".body");
    var area = document.getElementById("fx-area");
    if (!corpo || !area) return;
    var altura = area.getBoundingClientRect().height;
    var limite = altura > 80 ? altura - 32 : 0;
    corpo.style.height = "auto";
    if (limite && corpo.scrollHeight > limite) corpo.style.height = limite + "px";
  }

  function pararAnimacaoDoPainel() {
    if (!window.jQuery) return;
    var api = window.jQuery("#messageAlert .messages");
    if (api && typeof api.stop === "function") api.stop(true, false);
    var painel = painelAlertas();
    if (!painel) return;
    painel.style.height = "";
    painel.style.overflow = "";
  }

  function vigiarTitulo() {
    var painel = painelAlertas();
    if (!painel || painel.dataset.fxTitulo) return;
    var titulo = painel.querySelector(".header span");
    if (!titulo || typeof MutationObserver !== "function") return;
    painel.dataset.fxTitulo = "1";
    var observador = new MutationObserver(function () {
      if (popAberto !== "sino") return;
      if (titulo.textContent !== "Alertas") titulo.textContent = "Alertas";
    });
    observador.observe(titulo, {
      childList: true,
      characterData: true,
      subtree: true,
    });
  }

  function abrirPainelAlertas() {
    var alerta = document.getElementById("messageAlert");
    var painel = painelAlertas();
    if (!alerta || !painel) return;
    pararAnimacaoDoPainel();
    window.messageAlertHover = true;
    if (window.jQuery) window.jQuery("body").unbind("click.message");
    var enviada = alerta.querySelector(".mensagemenviada");
    if (enviada) enviada.style.display = "none";
    var corpo = painel.querySelector(".body");
    if (corpo) corpo.style.display = "";
    var agendados = alerta.querySelectorAll(".itemAgendado");
    for (var i = 0; i < agendados.length; i++) agendados[i].remove();
    descartarRascunho();
    tituloAlertas();
    vigiarTitulo();
    painel.style.display = "block";
    alerta.classList.add("fx-aberto");
    posicionarSino();
  }

  function fecharPainelAlertas() {
    pararAnimacaoDoPainel();
    window.messageAlertHover = false;
    if (window.jQuery) window.jQuery("body").unbind("click.message");
    descartarRascunho();
    restaurarItensOcultos();
    var painel = painelAlertas();
    if (painel) painel.style.display = "none";
    var alerta = document.getElementById("messageAlert");
    if (!alerta) return;
    alerta.classList.remove("fx-aberto");
    alerta.style.zIndex = "";
  }

  function pintarSino() {
    var botao = document.querySelector("[data-fx-sino]");
    if (!botao) return;
    botao.setAttribute("aria-expanded", popAberto === "sino" ? "true" : "false");
    var origem = document.querySelector("#messageAlert .alerts");
    var numero = origem ? parseInt(origem.textContent, 10) : 0;
    if (isNaN(numero) || numero < 0) numero = 0;
    var selo = botao.querySelector("[data-fx-sino-contagem]");
    if (!selo) return;
    selo.textContent = numero ? String(numero) : "";
    selo.hidden = numero === 0;
  }

  function observarContagem() {
    var origem = document.querySelector("#messageAlert .alerts");
    if (!origem || origem.dataset.fxObservado) return;
    origem.dataset.fxObservado = "1";
    if (typeof MutationObserver !== "function") return;
    var observador = new MutationObserver(pintarSino);
    observador.observe(origem, {
      childList: true,
      characterData: true,
      subtree: true,
    });
  }

  function aoClicarNoPainel(evento) {
    if (popAberto !== "sino") return;
    tituloAlertas();
    var item = evento.target.closest("li.msgalert");
    if (!item || item.isConnected) return;
    popAberto = null;
    fecharPainelAlertas();
    redesenharAcesso();
  }

  function alternarSino() {
    if (popAberto === "sino") {
      fecharPops();
      return;
    }
    if (!document.getElementById("messageAlert")) return;
    if (paletaAberta) fecharPaleta();
    if (popAberto) {
      popAberto = null;
      soltarPop();
    }
    popAberto = "sino";
    abrirPainelAlertas();
    redesenharAcesso();
  }

  function fecharPops() {
    if (!popAberto) return;
    var sino = popAberto === "sino";
    popAberto = null;
    if (sino) fecharPainelAlertas();
    redesenharAcesso();
  }

  function alternarPop(nome) {
    if (paletaAberta) fecharPaleta();
    if (popAberto === "sino") fecharPainelAlertas();
    popAberto = popAberto === nome ? null : nome;
    redesenharAcesso();
  }

  function atualizarNomes(html) {
    if (!html) return;
    var metaNova = html.match(/name="fx-empresa-nome"\s+content="([^"]*)"/);
    if (metaNova) {
      var meta = document.querySelector('meta[name="fx-empresa-nome"]');
      if (meta) meta.setAttribute("content", decodificar(metaNova[1]));
    }
    var curto = html.match(/nomeEmpresaAbreviado'\s*:\s*'([^']*)'/);
    if (curto && window.g) g.nomeEmpresaAbreviado = decodificar(curto[1]);
  }

  function depoisDaTroca(html) {
    atualizarNomes(html);
    abas = [];
    visivel = null;
    fecharJanelasSoltas();
    desenharFaixa();
    var menu = document.getElementById("fx-menu");
    if (menu) menu.innerHTML = htmlMenu();
    popAberto = null;
    redesenharAcesso();
    medirArea();
  }

  function trocar(selectId, nomeFn, id) {
    var select = document.getElementById(selectId);
    var fn = window[nomeFn];
    if (!select || typeof fn !== "function" || !window.jQuery) {
      fecharPops();
      return;
    }
    if (String(select.value) === String(id)) {
      fecharPops();
      return;
    }
    select.value = String(id);
    var ajax = window.jQuery.ajax;
    window.jQuery.ajax = function (opts) {
      if (opts && typeof opts.success === "function") {
        var sucesso = opts.success;
        opts.success = function (html) {
          var resultado = sucesso.apply(this, arguments);
          depoisDaTroca(typeof html === "string" ? html : "");
          return resultado;
        };
      }
      window.jQuery.ajax = ajax;
      return ajax.apply(this, arguments);
    };
    try {
      fn(select);
    } finally {
      window.jQuery.ajax = ajax;
    }
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

  function dobrar(texto) {
    return String(texto)
      .normalize("NFD")
      .replace(/[\u0300-\u036f]/g, "")
      .replace(/\./g, "")
      .toLowerCase();
  }

  function acaoDoMenu(acao) {
    return String(acao).replace(/\s*return false;\s*$/i, "");
  }

  function itensDaPaleta() {
    var chave = dobrar(filtroPaleta.trim());
    var lista = [];
    gruposDoMenu().forEach(function (grupo) {
      grupo.entradas.forEach(function (entrada) {
        if (
          !chave ||
          dobrar(entrada.nome).indexOf(chave) >= 0 ||
          dobrar(grupo.nome).indexOf(chave) >= 0
        ) {
          lista.push({
            grupo: grupo.nome,
            nome: entrada.nome,
            acao: entrada.acao,
          });
        }
      });
    });
    return lista;
  }

  function htmlListaPaleta(itens) {
    if (!itens.length) {
      return '<p class="tw:m-0 tw:px-3.5 tw:py-4 tw:text-muted">Não há item</p>';
    }
    var grupo = "";
    return itens
      .map(function (item, indice) {
        var titulo = "";
        if (item.grupo !== grupo) {
          grupo = item.grupo;
          titulo =
            '<p class="tw:m-0 tw:px-3.5 tw:pb-0.5 tw:pt-2.5 tw:text-[11px] tw:font-semibold tw:uppercase tw:tracking-wide tw:text-muted">' +
            escapar(item.grupo) +
            "</p>";
        }
        var ativo = indice === destaquePaleta;
        return (
          titulo +
          '<button type="button" role="option" data-fx-paleta-item="' +
          indice +
          '" aria-selected="' +
          (ativo ? "true" : "false") +
          '" class="tw:mx-1.5 tw:block tw:w-[calc(100%-0.75rem)] tw:rounded-md tw:px-2 tw:py-1.5 tw:text-left tw:text-ink' +
          (ativo ? " tw:bg-accent-soft" : "") +
          '" onclick="' +
          escapar(acaoDoMenu(item.acao)) +
          '">' +
          escapar(item.nome) +
          "</button>"
        );
      })
      .join("");
  }

  function posicionarPaleta() {
    var caixa = document.querySelector("[data-fx-paleta-caixa]");
    var area = document.getElementById("fx-area");
    if (!caixa || !area) return;
    var rect = area.getBoundingClientRect();
    caixa.style.left = rect.left + rect.width / 2 + "px";
    caixa.style.top = rect.top + rect.height / 2 + "px";
    caixa.style.transform = "translate(-50%, -50%)";
  }

  function mostrarDestaque() {
    var lista = document.getElementById("fx-paleta-lista");
    if (!lista) return;
    var ativo = lista.querySelector('[aria-selected="true"]');
    if (ativo && ativo.scrollIntoView) {
      ativo.scrollIntoView({ block: "nearest" });
    }
  }

  function pintarPaleta() {
    var lista = document.getElementById("fx-paleta-lista");
    if (!lista) return;
    var itens = itensDaPaleta();
    if (!itens.length) destaquePaleta = -1;
    else if (destaquePaleta < 0 || destaquePaleta >= itens.length) {
      destaquePaleta = 0;
    }
    lista.innerHTML = htmlListaPaleta(itens);
    mostrarDestaque();
  }

  function abrirPaleta() {
    if (popAberto) fecharPops();
    paletaAberta = true;
    filtroPaleta = "";
    destaquePaleta = 0;
    if (!document.getElementById("fx-paleta")) {
      var camada = document.createElement("div");
      camada.id = "fx-paleta";
      camada.innerHTML =
        '<div data-fx-paleta-fundo class="tw:fixed tw:inset-0 tw:z-200000 tw:bg-side/40"></div>' +
        '<div data-fx-paleta-caixa role="dialog" aria-modal="true" aria-label="Pesquisar o menu" class="tw:fixed tw:z-200001 tw:flex tw:w-[min(520px,calc(100%-32px))] tw:max-h-[min(540px,68vh)] tw:flex-col tw:overflow-hidden tw:rounded-lg tw:bg-paper tw:text-[13px] tw:leading-snug tw:text-ink tw:shadow-[0_24px_60px_#141c2866]">' +
        '<input id="fx-paleta-filtro" type="text" autocomplete="off" spellcheck="false" placeholder="Filtrar o menu" aria-label="Filtrar o menu" class="tw:w-full tw:border-b tw:border-solid tw:border-line tw:bg-paper tw:px-3.5 tw:py-3 tw:text-ink tw:outline-none">' +
        '<div id="fx-paleta-lista" role="listbox" class="tw:min-h-0 tw:flex-1 tw:overflow-auto tw:py-1"></div></div>';
      document.body.appendChild(camada);
      camada.querySelector("[data-fx-paleta-fundo]").addEventListener(
        "click",
        function () {
          fecharPaleta();
        },
      );
      document.getElementById("fx-paleta-lista").addEventListener(
        "click",
        function (evento) {
          if (!evento.target.closest("[data-fx-paleta-item]")) return;
          fecharPaleta();
        },
      );
      document
        .getElementById("fx-paleta-filtro")
        .addEventListener("input", function (evento) {
          filtroPaleta = evento.target.value;
          destaquePaleta = 0;
          pintarPaleta();
        });
    }
    var campo = document.getElementById("fx-paleta-filtro");
    campo.value = "";
    pintarPaleta();
    posicionarPaleta();
    campo.focus();
  }

  function fecharPaleta() {
    paletaAberta = false;
    filtroPaleta = "";
    destaquePaleta = -1;
    var camada = document.getElementById("fx-paleta");
    if (camada) camada.remove();
  }

  function aoPesquisar() {
    if (paletaAberta) {
      var campo = document.getElementById("fx-paleta-filtro");
      if (campo) campo.focus();
      return;
    }
    abrirPaleta();
  }

  function moverDestaque(passo) {
    var itens = itensDaPaleta();
    if (!itens.length || destaquePaleta < 0) return;
    var proximo = destaquePaleta + passo;
    if (proximo < 0) proximo = 0;
    if (proximo >= itens.length) proximo = itens.length - 1;
    if (proximo === destaquePaleta) return;
    destaquePaleta = proximo;
    pintarPaleta();
  }

  function escolherDestaque() {
    var lista = document.getElementById("fx-paleta-lista");
    if (!lista) return;
    var botao = lista.querySelector('[aria-selected="true"]');
    if (botao) botao.click();
  }

  function aoTecla(evento) {
    var ctrlK =
      evento.ctrlKey &&
      !evento.metaKey &&
      !evento.altKey &&
      !evento.shiftKey &&
      evento.key &&
      evento.key.toLowerCase() === "k";
    if (ctrlK) {
      if (!document.getElementById("fx-shell")) return;
      evento.preventDefault();
      evento.stopPropagation();
      if (paletaAberta) fecharPaleta();
      else abrirPaleta();
      return;
    }
    if (popAberto && evento.key === "Escape") {
      evento.preventDefault();
      evento.stopPropagation();
      fecharPops();
      return;
    }
    if (!paletaAberta) return;
    if (evento.key === "Escape") {
      evento.preventDefault();
      evento.stopPropagation();
      fecharPaleta();
      return;
    }
    if (evento.key === "ArrowDown" || evento.key === "ArrowUp") {
      evento.preventDefault();
      evento.stopPropagation();
      moverDestaque(evento.key === "ArrowDown" ? 1 : -1);
      return;
    }
    if (evento.key === "Enter") {
      evento.preventDefault();
      evento.stopPropagation();
      escolherDestaque();
    }
  }

  function tirarAtalhosDoMenu() {
    if (!window.shortcut || typeof window.shortcut.remove !== "function") {
      return;
    }
    [
      "Alt+M",
      "Alt+C",
      "Alt+V",
      "Alt+E",
      "Alt+F",
      "Alt+L",
      "Alt+X",
      "Alt+B",
      "Alt+G",
      "Alt+T",
      "Alt+O",
      "Alt+S",
    ].forEach(function (atalho) {
      window.shortcut.remove(atalho);
    });
  }

  function tirarTeclasDoMenu() {
    if (typeof window.eventosTeclado !== "function") return;
    if (window.eventosTeclado.fxSemMenu) return;
    var original = window.eventosTeclado;
    function semMenu(tecla) {
      if (
        tecla === "Up" ||
        tecla === "Down" ||
        tecla === "Left" ||
        tecla === "Right" ||
        tecla === "Enter" ||
        tecla === "Esc"
      ) {
        var aberto = document.querySelector("#topoMenu #barra li a.hover");
        if (aberto && window.jQuery) {
          window.jQuery("#topoMenu #barra li a.hover").trigger("mouseout");
        }
      }
      return original.apply(this, arguments);
    }
    semMenu.fxSemMenu = true;
    window.eventosTeclado = semMenu;
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
    if (paletaAberta) posicionarPaleta();
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
          ? "tw:flex tw:max-w-56 tw:shrink-0 tw:items-center tw:rounded-t-md tw:bg-paper tw:text-ink"
          : "tw:flex tw:max-w-56 tw:shrink-0 tw:items-center tw:rounded-t-md tw:text-muted";
        return (
          '<div role="tab" draggable="true" class="' +
          classe +
          '" data-fx-aba="' +
          escapar(aba.raiz) +
          '" aria-selected="' +
          (ativa ? "true" : "false") +
          '">' +
          '<span class="tw:min-w-0 tw:flex-1 tw:truncate tw:px-3 tw:py-1.5 tw:text-left">' +
          escapar(aba.titulo) +
          "</span>" +
          '<button type="button" class="tw:grid tw:size-7 tw:shrink-0 tw:place-items-center tw:rounded-md tw:text-muted tw:hover:bg-black/5" data-fx-fechar="' +
          escapar(aba.raiz) +
          '" aria-label="Fechar ' +
          escapar(aba.titulo) +
          '">×</button></div>'
        );
      })
      .join("");
    mostrarVisivel();
    if (!document.getElementById("fx-trabalho")) montarTrabalho();
    atualizarTrabalho();
  }

  function dataDaNota(nota) {
    var tempo = nota.querySelector("time");
    return tempo ? tempo.getAttribute("datetime") || "" : "";
  }

  function blocoCertificado(desktop) {
    var imgs = desktop.getElementsByTagName("img");
    for (var i = 0; i < imgs.length; i++) {
      var src = imgs[i].getAttribute("src") || "";
      if (src.indexOf("certificadoDigital") < 0) continue;
      var card = imgs[i].parentElement;
      if (!card || card === desktop) return null;
      return card;
    }
    return null;
  }

  function ehCockpitInicial(raiz, titulo, url) {
    return (
      String(raiz) === "Cockpit" &&
      !titulo &&
      String(url).indexOf("etc011ContentCockpit") >= 0
    );
  }

  function montarTrabalho() {
    var desktop = document.getElementById("desktop");
    if (!desktop || document.getElementById("fx-trabalho")) return;
    var certificado = blocoCertificado(desktop);
    var avisos = [];
    var listaAvisos = desktop.querySelectorAll("[data-fx-aviso]");
    for (var i = 0; i < listaAvisos.length; i++) avisos.push(listaAvisos[i]);
    var notas = [];
    var listaNotas = desktop.querySelectorAll("[data-fx-nota]");
    for (var j = 0; j < listaNotas.length; j++) notas.push(listaNotas[j]);
    notas.sort(function (a, b) {
      var da = dataDaNota(a);
      var db = dataDaNota(b);
      if (da === db) return 0;
      return da < db ? 1 : -1;
    });
    var trabalho = document.createElement("div");
    trabalho.id = "fx-trabalho";
    if (certificado) {
      certificado.setAttribute("data-fx-certificado", "");
      trabalho.appendChild(certificado);
    }
    avisos.forEach(function (aviso) {
      trabalho.appendChild(aviso);
    });
    notas.forEach(function (nota) {
      var midias = nota.querySelectorAll("video, iframe");
      for (var k = midias.length - 1; k >= 0; k--) midias[k].remove();
      trabalho.appendChild(nota);
    });
    desktop.appendChild(trabalho);
    for (var n = 0; n < desktop.children.length; n++) {
      var filho = desktop.children[n];
      if (filho === trabalho) continue;
      if (filho.classList && filho.classList.contains("ui-dialog")) continue;
      filho.hidden = true;
    }
  }

  function atualizarTrabalho() {
    var trabalho = document.getElementById("fx-trabalho");
    if (!trabalho) return;
    trabalho.hidden = visivel != null;
  }

  function mostrarVisivel() {
    var faixa = document.getElementById("fx-faixa");
    if (!faixa || visivel == null) return;
    var filhos = faixa.children;
    var aba = null;
    for (var i = 0; i < filhos.length; i++) {
      if (filhos[i].getAttribute("data-fx-aba") === visivel) aba = filhos[i];
    }
    if (!aba) return;
    var faixaRect = faixa.getBoundingClientRect();
    var abaRect = aba.getBoundingClientRect();
    if (abaRect.left < faixaRect.left) {
      faixa.scrollLeft -= faixaRect.left - abaRect.left;
    } else if (abaRect.right > faixaRect.right) {
      faixa.scrollLeft += abaRect.right - faixaRect.right;
    }
  }

  function conteudosDaAba(raiz) {
    var lista = [];
    window.jQuery(".ui-dialog-content").each(function () {
      var conteudo = window.jQuery(this);
      if (String(conteudo.data("janelaIdRoot")) !== String(raiz)) return;
      lista.push(conteudo);
    });
    lista.reverse();
    return lista;
  }

  function fecharJanelas(raiz) {
    conteudosDaAba(raiz).forEach(function (conteudo) {
    conteudo.parent().hide();
    window.jQuery("#w-overlay-" + conteudo.attr("id")).remove();
    if (!conteudo.data("dialog")) return;
    try {
      conteudo.dialog("close");
    } catch (e) {}
    });
  }

  function fecharJanelasSoltas() {
    var raizes = [];
    window.jQuery(".ui-dialog-content").each(function () {
      var raiz = window.jQuery(this).data("janelaIdRoot");
      if (raiz == null) return;
      raiz = String(raiz);
      if (indiceDaAba(raiz) >= 0) return;
      if (raizes.indexOf(raiz) < 0) raizes.push(raiz);
    });
    raizes.forEach(fecharJanelas);
  }

  function semDialogoVisivel() {
    var algum = false;
    window.jQuery(".ui-dialog").each(function () {
      if (window.jQuery(this).is(":visible")) algum = true;
    });
    return !algum;
  }

  function deveOcuparAArea(tamanho, posicao) {
    var auto =
      tamanho === "auto" ||
      tamanho === "forcarAuto" ||
      typeof tamanho === "undefined";
    var centro = posicao === "center" || typeof posicao === "undefined";
    return visivel == null && auto && centro && semDialogoVisivel();
  }

  function fecharAba(raiz) {
    var indice = indiceDaAba(raiz);
    if (indice < 0) return;
    var eraVisivel = visivel === raiz;
    abas.splice(indice, 1);
    if (eraVisivel) {
      var proxima = abas[indice] || abas[indice - 1] || null;
      visivel = proxima ? proxima.raiz : null;
    }
    fecharJanelas(raiz);
    desenharFaixa();
    aplicarMiolo();
    window.setTimeout(aplicarMiolo, 400);
  }

  function soltarAba(destino) {
    if (!arrastando || destino === arrastando) return;
    var de = indiceDaAba(arrastando);
    var para = indiceDaAba(destino);
    if (de < 0 || para < 0) return;
    var item = abas.splice(de, 1)[0];
    abas.splice(para, 0, item);
    arrastando = null;
    desenharFaixa();
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
    var tamanho = arguments[8];
    var posicao = arguments[10];
    if (evento && evento.ctrlKey) {
      return abrirOriginal.apply(this, arguments);
    }
    if (ehCockpitInicial(raiz, titulo, arguments[6])) return;
    medirArea();
    if (deveOcuparAArea(tamanho, posicao)) {
      arguments[8] = "full";
      if (arguments.length < 9) arguments.length = 9;
    }
    if (posicao === "topleft" && raiz) {
      fecharJanelasSoltas();
      var resultado = abrirOriginal.apply(this, arguments);
      lembrarAba(String(raiz), titulo ? String(titulo) : String(raiz));
      return resultado;
    }
    return abrirOriginal.apply(this, arguments);
  }

  function montar() {
    if (!document.getElementById("topo") || !document.getElementById("desktop")) {
      return;
    }
    if (document.getElementById("fx-shell")) return;
    if (window.g && String(g.oneWindow) === "1") return;

    var shell = document.createElement("div");
    shell.id = "fx-shell";
    shell.className =
      "tw:fixed tw:inset-0 tw:grid tw:h-dvh tw:grid-cols-[232px_minmax(0,1fr)] tw:bg-canvas tw:font-sans tw:text-[13px] tw:leading-snug tw:text-ink";
    shell.innerHTML =
      '<aside class="tw:flex tw:min-h-0 tw:flex-col tw:bg-side tw:text-side-ink">' +
      '<p class="tw:m-0 tw:px-3.5 tw:pt-4 tw:text-[15px] tw:font-semibold tw:tracking-wide">FX ERP</p>' +
      '<div id="fx-empresa"></div>' +
      '<nav id="fx-menu" class="tw:min-h-0 tw:flex-1 tw:overflow-auto tw:pb-3">' +
      htmlMenu() +
      "</nav></aside>" +
      '<div class="tw:grid tw:min-h-0 tw:min-w-0 tw:grid-rows-[48px_36px_minmax(0,1fr)]">' +
      '<header class="tw:flex tw:min-w-0 tw:items-center tw:gap-2 tw:border-b tw:border-solid tw:border-line tw:bg-paper tw:px-3">' +
      '<div class="tw:flex-1"></div>' +
      '<div class="tw:flex tw:items-center tw:gap-0.5">' +
      '<button type="button" data-fx-pesquisar class="tw:grid tw:size-9 tw:place-items-center tw:rounded-lg tw:text-ink tw:hover:bg-black/5" aria-label="Pesquisar o menu">' +
      '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>' +
      "</button>" +
      '<button type="button" data-fx-sino class="tw:relative tw:grid tw:size-9 tw:place-items-center tw:rounded-lg tw:text-ink tw:hover:bg-black/5" aria-label="Alertas" aria-haspopup="dialog" aria-expanded="false">' +
      '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M6 9a6 6 0 1 1 12 0c0 7 3 7 3 9H3c0-2 3-2 3-9"/><path d="M10 20a2 2 0 0 0 4 0"/></svg>' +
      '<span data-fx-sino-contagem hidden aria-hidden="true" class="tw:pointer-events-none tw:absolute tw:top-0.5 tw:right-0.5 tw:min-w-3.5 tw:rounded-full tw:bg-accent tw:px-0.5 tw:text-center tw:text-[10px] tw:font-semibold tw:leading-3.5 tw:text-white"></span>' +
      "</button>" +
      '<div id="fx-avatar" class="tw:relative"></div></div></header>' +
      '<div id="fx-faixa" class="tw:flex tw:min-w-0 tw:items-end tw:gap-0.5 tw:overflow-x-auto tw:bg-canvas tw:px-2"></div>' +
      '<div id="fx-area" class="tw:min-h-0 tw:min-w-0"></div></div>';
    document.body.appendChild(shell);
    observarContagem();
    var alerta = document.getElementById("messageAlert");
    if (alerta) alerta.addEventListener("click", aoClicarNoPainel);
    redesenharAcesso();
    shell.addEventListener("click", function (evento) {
      if (evento.target.closest("[data-fx-sino]")) {
        evento.preventDefault();
        alternarSino();
        return;
      }
      if (evento.target.closest("[data-fx-empresa]")) {
        evento.preventDefault();
        alternarPop("empresa");
        return;
      }
      if (evento.target.closest("[data-fx-avatar]")) {
        evento.preventDefault();
        alternarPop("avatar");
      }
    });
    document.addEventListener("click", function (evento) {
      var empresa = evento.target.closest("[data-fx-empresa-item]");
      if (empresa) {
        evento.preventDefault();
        trocar(
          "selecionaEmpresa",
          "changeEmpresaAtual",
          empresa.getAttribute("data-fx-empresa-item"),
        );
        return;
      }
      var usuario = evento.target.closest("[data-fx-usuario-item]");
      if (usuario) {
        evento.preventDefault();
        trocar(
          "selecionaUsuario",
          "changeUsuarioAtual",
          usuario.getAttribute("data-fx-usuario-item"),
        );
        return;
      }
      if (evento.target.closest("[data-fx-sair]")) {
        evento.preventDefault();
        window.location = "/logout";
        return;
      }
      if (!popAberto) return;
      if (
        evento.target.closest(
          "[data-fx-pop], [data-fx-empresa], [data-fx-avatar], [data-fx-sino], #messageAlert, .ui-datepicker",
        )
      ) {
        return;
      }
      fecharPops();
    });
    shell.querySelector("[data-fx-pesquisar]").addEventListener(
      "click",
      aoPesquisar,
    );
    document.addEventListener("keydown", aoTecla, true);
    tirarAtalhosDoMenu();
    tirarTeclasDoMenu();
    window.setTimeout(tirarAtalhosDoMenu, 0);

    var faixa = document.getElementById("fx-faixa");
    faixa.addEventListener("click", function (evento) {
      var fechar = evento.target.closest("[data-fx-fechar]");
      if (fechar) {
        evento.preventDefault();
        fecharAba(fechar.getAttribute("data-fx-fechar"));
        return;
      }
      var aba = evento.target.closest("[data-fx-aba]");
      if (!aba) return;
      visivel = aba.getAttribute("data-fx-aba");
      desenharFaixa();
      aplicarMiolo();
    });
    faixa.addEventListener("dragstart", function (evento) {
      if (evento.target.closest("[data-fx-fechar]")) {
        evento.preventDefault();
        return;
      }
      var aba = evento.target.closest("[data-fx-aba]");
      if (!aba) return;
      arrastando = aba.getAttribute("data-fx-aba");
      evento.dataTransfer.effectAllowed = "move";
      evento.dataTransfer.setData("text/plain", arrastando);
    });
    faixa.addEventListener("dragover", function (evento) {
      if (!arrastando) return;
      if (!evento.target.closest("[data-fx-aba]")) return;
      evento.preventDefault();
      evento.dataTransfer.dropEffect = "move";
    });
    faixa.addEventListener("drop", function (evento) {
      var aba = evento.target.closest("[data-fx-aba]");
      if (!aba || !arrastando) return;
      evento.preventDefault();
      soltarAba(aba.getAttribute("data-fx-aba"));
    });
    faixa.addEventListener("dragend", function () {
      arrastando = null;
    });

    // O Hardness fecha a janela que deixou de ser a visível. A aba precisa ficar.
    window.closeLastBtn = false;
    if (typeof window.divIdRootAberto === "function") {
      var raizAbertaOriginal = window.divIdRootAberto;
      window.divIdRootAberto = function () {
        if (visivel != null) return visivel;
        return raizAbertaOriginal.apply(this, arguments);
      };
    }
    window.fixDesktopSize = medirArea;
    medirArea();
    window.addEventListener("resize", function () {
      medirArea();
      fecharPops();
    });

    montarTrabalho();
    atualizarTrabalho();
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
