// A nuvem e os selos saem do desktop. A sidebar e a paleta listam o menu,
// e o grupo Apps abre numa aba nova do navegador, sem virar aba do desktop.
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

const reforma =
  "window.open('/hardness3/aplicacoes/fiscal/reformaTributaria/public/', '_blank', 'noopener,noreferrer'); return false;";
const loja =
  "abrirJanela(event, 'Loja Apps', '', menuId('lojaAppsCliente'), 'Loja Apps', '.: Loja Apps :.', '/sistema/personalizacaoLowCode/content/appsListarCliente/', '', 'full', false, 'topleft', false); return false;";

function pagina() {
  const dom = new JSDOM(
    "<!DOCTYPE html><html><head>" +
      '<meta name="fx-empresa-nome" content="Empresa de validação">' +
      "</head><body>" +
      '<div id="topo">' +
      '<div id="topoMenu"><ul id="barra">' +
      '<li id="menuInicio"><a href="/erp/">Início</a></li>' +
      "<li><a>Gerencial</a><ul class=\"menu\">" +
      '<li><a onclick="abrirJanela(event, \'relatorios\', \'\', \'id\', \'Relatórios\', \'\', \'/rel/\', \'\', \'full\', false, \'topleft\', false); return false;">Relatórios</a></li>' +
      "</ul></li>" +
      "<li><a>Apps</a><ul class=\"menu\">" +
      '<li><a onclick="' + reforma + '">Reforma Tributária</a></li>' +
      "</ul></li>" +
      "<li><a>Outros</a><ul class=\"menu\">" +
      '<li><a onclick="return false;">Log Erros</a></li>' +
      "</ul></li>" +
      "<li><a>Configuração</a><ul class=\"menu\">" +
      '<li><a onclick="' + loja + '">Loja de apps</a></li>' +
      "</ul></li>" +
      "</ul></div>" +
      '<div class="lojaApp"><span class="material-icons">cloud_download</span></div>' +
      "</div>" +
      '<div id="desktop"></div>' +
      '<div id="footer"><div>' +
      '<font id="campoEmpresa"><span>VAL</span></font>' +
      " | Diretor | " +
      '<font title="banco">banco</font>' +
      '<span> &nbsp; *** Modo IDE *** &nbsp; </span>' +
      '<span id="modoDesenvolvedorApp"> &nbsp; *** Editando App: Pacote *** &nbsp; </span>' +
      "</div></div>" +
      "</body></html>",
    { url: "http://localhost/erp", runScripts: "outside-only" },
  );
  const window = dom.window;
  window.jQuery = function () {
    return { each() {}, unbind() {} };
  };
  window.abertas = [];
  window.abrirJanela = function () {
    window.abertas.push(Array.from(arguments));
  };
  window.abertasFora = [];
  window.open = function (url) {
    window.abertasFora.push(url);
    return { focus() {} };
  };
  window.g = { empresaAtual: "1", oneWindow: "0" };
  window.menuId = function (id) {
    return id;
  };
  window.eval(fonte);
  return window;
}

function grupos(window) {
  return [...window.document.querySelectorAll("#fx-menu [data-fx-grupo]")].map(function (grupo) {
    return {
      nome: grupo.querySelector("[data-fx-grupo-titulo]").textContent,
      itens: [...grupo.querySelectorAll("[data-fx-item]")].map(function (botao) {
        return botao.textContent;
      }),
    };
  });
}

test("a nuvem sai do topo e os selos não aparecem", () => {
  const window = pagina();
  const doc = window.document;
  assert.equal(doc.querySelector(".lojaApp"), null);
  assert.equal(doc.getElementById("modoDesenvolvedorApp"), null);
  assert.equal(doc.body.textContent.includes("Modo IDE"), false);
  assert.equal(doc.body.textContent.includes("Editando App"), false);
  assert.equal(doc.querySelector("#fx-shell header .lojaApp"), null);
  assert.equal(doc.querySelector("#campoEmpresa span").textContent, "VAL");
});

test("a sidebar e a paleta alcançam o grupo Apps e a Loja de apps", () => {
  const window = pagina();
  const doc = window.document;
  assert.deepEqual(grupos(window), [
    { nome: "Gerencial", itens: ["Relatórios"] },
    { nome: "Apps", itens: ["Reforma Tributária"] },
    { nome: "Outros", itens: ["Log Erros"] },
    { nome: "Configuração", itens: ["Loja de apps"] },
  ]);

  doc.querySelector("[data-fx-pesquisar]").click();
  const filtro = doc.getElementById("fx-paleta-filtro");
  filtro.value = "apps";
  filtro.dispatchEvent(new window.Event("input"));
  const lista = doc.getElementById("fx-paleta-lista").textContent;
  assert.equal(lista.includes("Apps"), true);
  assert.equal(lista.includes("Reforma Tributária"), true);
  assert.equal(lista.includes("Loja de apps"), true);
  assert.equal(lista.includes("Relatórios"), false);
});

function acionar(window, botao) {
  const codigo = botao.getAttribute("onclick");
  window.eval("(function (event) { " + codigo + " }).call(null, {})");
}

test("o item do grupo Apps abre o endereço de hoje e não vira aba", () => {
  const window = pagina();
  const doc = window.document;
  const botao = [...doc.querySelectorAll("#fx-menu [data-fx-item]")].find(function (item) {
    return item.textContent === "Reforma Tributária";
  });
  acionar(window, botao);
  assert.deepEqual(window.abertasFora, [
    "/hardness3/aplicacoes/fiscal/reformaTributaria/public/",
  ]);
  assert.equal(window.abertas.length, 0);
  assert.equal(doc.querySelector("[data-fx-aba]"), null);
});

test("a Loja de apps abre o catálogo", () => {
  const window = pagina();
  const doc = window.document;
  const botao = [...doc.querySelectorAll("#fx-menu [data-fx-item]")].find(function (item) {
    return item.textContent === "Loja de apps";
  });
  acionar(window, botao);
  assert.equal(window.abertas.length, 1);
  assert.equal(
    window.abertas[0][6],
    "/sistema/personalizacaoLowCode/content/appsListarCliente/",
  );
  assert.equal(window.abertasFora.length, 0);
});
