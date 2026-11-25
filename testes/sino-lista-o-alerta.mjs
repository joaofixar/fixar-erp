// O sino do shell abre o pop Alertas em cima da lista que o Hardness já monta.
// A costura observada é o DOM do desktop: o botão, o pop e a lista #messageAlert.
import assert from "node:assert/strict";
import { existsSync, readFileSync } from "node:fs";
import { createRequire } from "node:module";
import { homedir } from "node:os";
import { join } from "node:path";
import { test } from "node:test";

function carregarJsdom() {
  const prefixos = [
    process.env.JSDOM_PREFIX,
    join(homedir(), "AppData", "Local", "Temp", "fx-test-deps"),
    "/tmp/fx-test-deps",
  ].filter(Boolean);
  for (const prefixo of prefixos) {
    const pacote = join(prefixo, "node_modules", "jsdom", "package.json");
    if (!existsSync(pacote)) continue;
    return createRequire(pacote)("jsdom").JSDOM;
  }
  throw new Error(
    "jsdom ausente. Instale com: npm install --prefix /tmp/fx-test-deps jsdom",
  );
}

const JSDOM = carregarJsdom();

const fonte = readFileSync(new URL("../src/fx/shell.js", import.meta.url), "utf8");

const vazio =
  "Você não possui nenhuma mensagem ou alerta.";

function pagina(corpo) {
  const dom = new JSDOM(
    "<!DOCTYPE html><html><head>" +
      '<meta name="fx-empresa-nome" content="Empresa de validação">' +
      "</head><body>" +
      corpo +
      "</body></html>",
    { url: "http://localhost/erp", runScripts: "outside-only" },
  );
  const window = dom.window;
  window.jQuery = function () {
    return { each() {}, unbind() {} };
  };
  window.abrirJanela = function () {};
  window.g = { empresaAtual: "1" };
  window.eval(fonte);
  return window;
}

function base(extra) {
  return (
    '<div id="topo"></div>' +
    '<div id="desktop"><p id="aviso-da-area">Aviso publicado na área</p></div>' +
    '<div id="footer"><div>' +
    '<font id="campoEmpresa"><span>VAL</span></font>' +
    " | Diretor | " +
    '<font title="banco">banco</font>' +
    "</div></div>" +
    '<select id="selecionaEmpresa">' +
    '<option value="1" selected data-nome="Empresa de validação">Empresa de validação</option>' +
    '<option value="2" data-nome="Outra Empresa Ltda">Outra Empresa Ltda</option>' +
    "</select>" +
    '<ul id="barra"><li><a>Comercial</a><ul class="menu">' +
    '<li><a onclick="return false;">Clientes</a></li></ul></li></ul>' +
    extra
  );
}

function alerta(opcoes) {
  const suporte = opcoes.suporte
    ? '<button type="button" class="enviarSuporte">Suporte</button>'
    : "";
  const itens = opcoes.itens || "";
  const linhaVazia = opcoes.semVazio
    ? ""
    : '<li class="vazio"><div>' + vazio + "</div></li>";
  return (
    '<div id="messageAlert">' +
    '<div class="click"><div class="icone"></div>' +
    '<div class="alerts">' +
    (opcoes.contagem || "") +
    "</div></div>" +
    '<div class="messages" style="display:none">' +
    '<div class="header"><div>' +
    suporte +
    "<span>Notificações</span>" +
    '<img class="exibirOcultarNotificacoes" title="Exibir notificações desabilitadas">' +
    '<img class="refreshNotifications" title="Atualizar notificações">' +
    '<img class="enviarMensagem" title="Enviar mensagem">' +
    "</div></div>" +
    '<div class="body"><ul>' +
    '<li class="boxenviarmensagem" style="display:none"><div>' +
    '<select><option value="0"></option><option value="7">ana</option></select>' +
    '<textarea></textarea>' +
    '<input class="campoData" type="text">' +
    '<input class="campoTempo" type="text">' +
    '<input type="checkbox" name="msgTravar">' +
    '<span style="display:none">* Preencha todos os campos.</span>' +
    "</div></li>" +
    linhaVazia +
    itens +
    "</ul></div></div></div>"
  );
}

function ligarClique(window, item, empresa, acao) {
  item.addEventListener("click", function (evento) {
    if (empresa && empresa !== String(window.g.empresaAtual)) {
      evento.preventDefault();
      evento.stopPropagation();
      return;
    }
    acao();
    const contagem = window.document.querySelector("#messageAlert .alerts");
    const numero = parseInt(contagem.textContent, 10) || 0;
    contagem.textContent = String(Math.max(0, numero - 1));
    item.remove();
    window.document.querySelector("#messageAlert .messages").style.display =
      "none";
  });
}

function pendurar(window) {
  const botao = window.document.querySelector("[data-fx-sino]");
  botao.getBoundingClientRect = function () {
    return {
      top: 12,
      right: 800,
      bottom: 48,
      left: 764,
      width: 36,
      height: 36,
    };
  };
  return botao;
}

function clicar(window, alvo) {
  alvo.dispatchEvent(
    new window.MouseEvent("click", { bubbles: true, cancelable: true }),
  );
}

function pop(window) {
  return window.document.querySelector("#messageAlert .messages");
}

function tecla(window, key) {
  window.document.dispatchEvent(
    new window.KeyboardEvent("keydown", { key, bubbles: true, cancelable: true }),
  );
}

test("o sino abre o pop Alertas pendurado no ícone, e o vazio é o do Hardness", () => {
  const window = pagina(base(alerta({})));
  const botao = pendurar(window);
  clicar(window, botao);
  const painel = pop(window);
  assert.equal(botao.getAttribute("aria-expanded"), "true");
  assert.equal(
    window.document.getElementById("messageAlert").classList.contains("fx-aberto"),
    true,
  );
  assert.equal(painel.querySelector(".header span").textContent, "Alertas");
  assert.equal(painel.style.display, "block");
  assert.equal(painel.style.position, "fixed");
  assert.equal(painel.style.top, "52px");
  assert.ok(Number.parseFloat(painel.style.left) <= 800);
  assert.ok(painel.textContent.includes(vazio));
  assert.equal(
    window.document.querySelector("#aviso-da-area").textContent,
    "Aviso publicado na área",
  );
  assert.equal(painel.textContent.includes("Aviso publicado na área"), false);
});

test("o sino de novo, Esc ou um clique fora fecha o pop", () => {
  const window = pagina(base(alerta({})));
  const botao = pendurar(window);
  clicar(window, botao);
  clicar(window, botao);
  assert.equal(botao.getAttribute("aria-expanded"), "false");
  assert.equal(pop(window).style.display, "none");

  clicar(window, botao);
  tecla(window, "Escape");
  assert.equal(botao.getAttribute("aria-expanded"), "false");

  clicar(window, botao);
  clicar(window, window.document.getElementById("desktop"));
  assert.equal(botao.getAttribute("aria-expanded"), "false");
  assert.equal(window.document.getElementById("messageAlert").classList.contains("fx-aberto"), false);
});

test("empresa, avatar e paleta fecham o sino, e o sino fecha os outros", () => {
  const window = pagina(base(alerta({})));
  const botao = pendurar(window);
  const doc = window.document;

  clicar(window, botao);
  clicar(window, doc.querySelector("[data-fx-empresa]"));
  assert.equal(botao.getAttribute("aria-expanded"), "false");
  assert.equal(doc.querySelector("[data-fx-pop]").getAttribute("aria-label"), "Empresas");
  assert.equal(doc.querySelectorAll("[data-fx-pop]").length, 1);

  clicar(window, botao);
  assert.equal(doc.querySelector("[data-fx-pop]"), null);
  assert.equal(botao.getAttribute("aria-expanded"), "true");

  clicar(window, doc.querySelector("[data-fx-avatar]"));
  assert.equal(botao.getAttribute("aria-expanded"), "false");
  assert.equal(doc.querySelector("[data-fx-pop]").getAttribute("aria-label"), "Usuário");

  clicar(window, botao);
  clicar(window, doc.querySelector("[data-fx-pesquisar]"));
  assert.equal(botao.getAttribute("aria-expanded"), "false");
  assert.ok(doc.getElementById("fx-paleta"));

  clicar(window, botao);
  assert.equal(doc.getElementById("fx-paleta"), null);
  assert.equal(botao.getAttribute("aria-expanded"), "true");
});

test("fechar descarta o rascunho e abrir de novo começa vazio", () => {
  const window = pagina(base(alerta({})));
  const botao = pendurar(window);
  clicar(window, botao);
  const caixa = pop(window).querySelector(".boxenviarmensagem");
  caixa.style.display = "block";
  caixa.querySelector("textarea").value = "manda isso";
  caixa.querySelector("select").selectedIndex = 1;
  caixa.querySelector(".campoData").value = "25/11/2026";
  caixa.querySelector(".campoTempo").value = "10:00";
  caixa.querySelector('input[name="msgTravar"]').checked = true;
  clicar(window, window.document.getElementById("desktop"));
  assert.equal(caixa.querySelector("textarea").value, "");
  assert.equal(caixa.querySelector("select").selectedIndex, 0);
  assert.equal(caixa.querySelector(".campoData").value, "");
  assert.equal(caixa.querySelector(".campoTempo").value, "");
  assert.equal(caixa.querySelector('input[name="msgTravar"]').checked, false);
  assert.equal(caixa.style.display, "none");
  clicar(window, botao);
  assert.equal(caixa.querySelector("textarea").value, "");
  assert.equal(caixa.style.display, "none");
  assert.equal(pop(window).querySelector(".header span").textContent, "Alertas");
});

test("a lista é o alerta não lido, agrupado pela empresa, com ocultar, atualizar e enviar", () => {
  const itens =
    '<li id="empresa-1" class="notificacaoHeader">VAL</li>' +
    '<li class="msgalert">Pedido parado</li>' +
    '<li id="empresa-2" class="notificacaoHeader">OUT</li>' +
    '<li class="msgalert notifOculta" style="display:none">Oculto</li>';
  const window = pagina(
    base(alerta({ itens, contagem: "1", semVazio: true })),
  );
  const botao = pendurar(window);
  assert.equal(botao.querySelector("[data-fx-sino-contagem]").hidden, false);
  assert.equal(botao.querySelector("[data-fx-sino-contagem]").textContent, "1");
  clicar(window, botao);
  const painel = pop(window);
  assert.equal(painel.querySelector("#empresa-1").textContent, "VAL");
  assert.equal(painel.querySelector("#empresa-2").textContent, "OUT");
  assert.ok(painel.textContent.includes("Pedido parado"));
  assert.equal(painel.textContent.includes(vazio), false);
  assert.equal(
    painel.querySelector(".exibirOcultarNotificacoes").getAttribute("title"),
    "Exibir notificações desabilitadas",
  );
  assert.equal(
    painel.querySelector(".refreshNotifications").getAttribute("title"),
    "Atualizar notificações",
  );
  assert.equal(
    painel.querySelector(".enviarMensagem").getAttribute("title"),
    "Enviar mensagem",
  );
  assert.equal(painel.querySelector(".enviarSuporte"), null);
});

test("o suporte entra no pop só quando o Hardness já desenhou o botão", () => {
  const window = pagina(base(alerta({ suporte: true })));
  clicar(window, pendurar(window));
  assert.equal(pop(window).querySelector(".enviarSuporte").textContent, "Suporte");
});

test("clicar no alerta da empresa atual executa a ação, tira da lista e fecha", () => {
  const itens =
    '<li id="empresa-1" class="notificacaoHeader">VAL</li>' +
    '<li class="msgalert" id="alerta-atual">Pedido parado</li>';
  const window = pagina(base(alerta({ itens, contagem: "1", semVazio: true })));
  let acao = 0;
  ligarClique(
    window,
    window.document.getElementById("alerta-atual"),
    "1",
    () => {
      acao += 1;
    },
  );
  const botao = pendurar(window);
  clicar(window, botao);
  clicar(window, window.document.getElementById("alerta-atual"));
  assert.equal(acao, 1);
  assert.equal(window.document.getElementById("alerta-atual"), null);
  assert.equal(botao.querySelector("[data-fx-sino-contagem]").textContent, "");
  assert.equal(botao.querySelector("[data-fx-sino-contagem]").hidden, true);
  assert.equal(botao.getAttribute("aria-expanded"), "false");
});

test("alerta de outra empresa não abre", () => {
  const itens =
    '<li id="empresa-2" class="notificacaoHeader">OUT</li>' +
    '<li class="msgalert" id="alerta-outra">Nota da outra</li>';
  const window = pagina(base(alerta({ itens, contagem: "1", semVazio: true })));
  let acao = 0;
  ligarClique(
    window,
    window.document.getElementById("alerta-outra"),
    "2",
    () => {
      acao += 1;
    },
  );
  const botao = pendurar(window);
  clicar(window, botao);
  clicar(window, window.document.getElementById("alerta-outra"));
  assert.equal(acao, 0);
  assert.equal(
    window.document.getElementById("alerta-outra").textContent,
    "Nota da outra",
  );
  assert.equal(botao.getAttribute("aria-expanded"), "true");
  assert.equal(pop(window).style.display, "block");
});

test("enviar mensagem não troca o título Alertas", async () => {
  const window = pagina(base(alerta({})));
  clicar(window, pendurar(window));
  const painel = pop(window);
  const titulo = painel.querySelector(".header span");
  painel.querySelector(".enviarMensagem").addEventListener("click", (evento) => {
    titulo.textContent = "Enviar uma Mensagem";
    evento.preventDefault();
    evento.stopPropagation();
    window.setTimeout(() => {
      titulo.textContent = "Notificações";
    }, 30);
  });
  clicar(window, painel.querySelector(".enviarMensagem"));
  await new Promise((resolver) => window.setTimeout(resolver, 0));
  assert.equal(titulo.textContent, "Alertas");
  await new Promise((resolver) => window.setTimeout(resolver, 50));
  assert.equal(titulo.textContent, "Alertas");
});

test("reabrir o sino corta o slideUp que o Hardness deixou andando", async () => {
  const window = pagina(base(alerta({ itens: '<li class="msgalert" id="alerta-atual">Pedido</li>', contagem: "1", semVazio: true })));
  const timers = new Set();
  window.jQuery = function () {
    return {
      each() {},
      unbind() {},
      stop() {
        for (const id of timers) window.clearTimeout(id);
        timers.clear();
        return this;
      },
      slideUp() {
        const id = window.setTimeout(() => {
          pop(window).style.display = "none";
        }, 40);
        timers.add(id);
        return this;
      },
    };
  };
  const item = window.document.getElementById("alerta-atual");
  item.addEventListener("click", () => {
    item.remove();
    window.jQuery("#messageAlert .messages").slideUp();
  });
  const botao = pendurar(window);
  clicar(window, botao);
  clicar(window, item);
  clicar(window, botao);
  await new Promise((resolver) => window.setTimeout(resolver, 60));
  assert.equal(pop(window).style.display, "block");
  assert.equal(botao.getAttribute("aria-expanded"), "true");
});
