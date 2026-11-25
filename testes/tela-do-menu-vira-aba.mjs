// O cadastro do menu vira aba que ocupa a área; a caixa pequena e a inclusão
// continuam janela. A grade sem linha e o aviso do filtro viram estado vazio,
// e o filtro abre só com os campos principais.
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

const cliente =
  "abrirJanelaPermissao = '1'; abrirJanela(event, divIdRootAberto(), '', menuId('cad001'), 'Clientes e Fornecedores', '.: Clientes e Fornecedores :.', '/cad/cad001/content/cad001ContentPrincipal/', '', 'auto', false, 'center'); return false;";
const sintegra =
  "abrirJanela(event, divIdRootAberto(), '', menuId('fis006'), 'Exportar Sintegra', '.: Exportar Sintegra :.', '/fis/fis006/content/', '', [400, 300], false, false, 'center'); return false;";

function pagina(miolo) {
  const dom = new JSDOM(
    "<!DOCTYPE html><html><head>" +
      '<meta name="fx-empresa-nome" content="Empresa de validação">' +
      "</head><body>" +
      '<div id="topo"><div id="topoMenu"><ul id="barra">' +
      '<li id="menuInicio"><a href="/erp/">Início</a></li>' +
      '<li><a>Cadastros</a><ul class="menu">' +
      '<li><a onclick="' + cliente + '">Cliente/Fornecedor</a></li>' +
      "</ul></li>" +
      '<li><a>Fiscal</a><ul class="menu">' +
      '<li><a onclick="' + sintegra + '">Exportar Sintegra</a></li>' +
      "</ul></li>" +
      "</ul></div></div>" +
      '<div id="desktop">' + (miolo || "") + "</div>" +
      '<div id="footer"><div><font id="campoEmpresa"><span>VAL</span></font></div></div>' +
      "</body></html>",
    { url: "http://localhost/erp", runScripts: "outside-only" },
  );
  const window = dom.window;
  window.jQuery = function () {
    return { each() {}, unbind() {} };
  };
  window.abertas = [];
  window.guardadoNaChamada = [];
  window.abrirJanela = function () {
    window.abertas.push(Array.from(arguments));
    window.guardadoNaChamada.push(window.g.tamanhosJanelas[arguments[6]]);
  };
  window.g = { empresaAtual: "1", oneWindow: "0", tamanhosJanelas: {} };
  window.menuUniques = {};
  window.menuId = function (codigo) {
    if (!window.menuUniques[codigo]) window.menuUniques[codigo] = "u-" + codigo;
    return window.menuUniques[codigo];
  };
  window.divIdRootAberto = function () {
    return "aberta";
  };
  window.eval(fonte);
  return window;
}

function item(window, nome) {
  return [...window.document.querySelectorAll("#fx-menu [data-fx-item]")].find(
    (botao) => botao.textContent === nome,
  );
}

function acionar(window, botao) {
  const codigo = botao.getAttribute("onclick");
  window.eval("(function (event) { " + codigo + " }).call(null, {})");
}

function abas(window) {
  return [...window.document.querySelectorAll("#fx-faixa [role=tab] span")].map(
    (aba) => aba.textContent,
  );
}

function esperar() {
  return new Promise((pronto) => setTimeout(pronto, 30));
}

test("o cadastro do menu abre como aba que ocupa a área", () => {
  const window = pagina();
  acionar(window, item(window, "Cliente/Fornecedor"));
  assert.equal(window.abertas.length, 1);
  const chamada = window.abertas[0];
  assert.equal(chamada[1], "cad001");
  assert.equal(chamada[8], "full");
  assert.equal(chamada[10], "topleft");
  assert.equal(chamada[11], false);
  assert.deepEqual(abas(window), ["Clientes e Fornecedores"]);
  const caminho = window.document.getElementById("fx-caminho").textContent;
  assert.equal(caminho, "CadastrosCliente/Fornecedor");
  assert.equal(
    item(window, "Cliente/Fornecedor").getAttribute("aria-current"),
    "page",
  );
});

test("abrir o cadastro de novo volta para a mesma aba", () => {
  const window = pagina();
  acionar(window, item(window, "Cliente/Fornecedor"));
  acionar(window, item(window, "Cliente/Fornecedor"));
  assert.deepEqual(abas(window), ["Clientes e Fornecedores"]);
});

test("o tamanho guardado não encolhe a aba e volta depois", () => {
  const window = pagina();
  const url = "/cad/cad001/content/cad001ContentPrincipal/";
  window.g.tamanhosJanelas[url] = [700, 400];
  acionar(window, item(window, "Cliente/Fornecedor"));
  assert.deepEqual(window.guardadoNaChamada, [undefined]);
  assert.deepEqual(window.g.tamanhosJanelas[url], [700, 400]);
});

test("a caixa pequena do menu continua janela sobre a aba visível", () => {
  const window = pagina();
  acionar(window, item(window, "Exportar Sintegra"));
  assert.equal(window.abertas.length, 1);
  assert.equal(window.abertas[0][1], "aberta");
  assert.deepEqual([...window.abertas[0][8]], [400, 300]);
  assert.deepEqual(abas(window), []);
});

test("a inclusão que a tela abre continua janela na mesma aba", () => {
  const window = pagina();
  acionar(window, item(window, "Cliente/Fornecedor"));
  window.abrirJanela(
    {},
    "cad001",
    "conteudo",
    "outra",
    "",
    "Clientes/Fornecedores",
    "/cad/cad001/content/cad001contentCadastro/",
    "",
    [960, 583],
  );
  assert.equal(window.abertas.length, 2);
  assert.deepEqual([...window.abertas[1][8]], [960, 583]);
  assert.equal(window.abertas[1][10], undefined);
  assert.deepEqual(abas(window), ["Clientes e Fornecedores"]);
});

test("o aviso do filtro vira estado vazio", () => {
  const window = pagina(
    '<div class="ui-dialog"><div class="divToScroll"><table class="grid"></table>' +
      '<div class="gridMsgFiltre">Por favor, utilize o filtro.</div></div></div>',
  );
  const vazio = window.document.querySelector(".gridMsgFiltre [data-fx-vazio]");
  assert.ok(vazio);
  assert.equal(vazio.querySelector("[data-fx-vazio-titulo]").textContent, "Utilize o filtro");
  assert.equal(vazio.querySelector("[data-fx-vazio-limpar]"), null);
  assert.equal(window.document.querySelectorAll("[data-fx-vazio]").length, 1);
});

test("o aviso próprio da tela continua o texto dela", () => {
  const window = pagina(
    '<div class="ui-dialog"><div class="gridMsgFiltre">Informe o período.</div></div>',
  );
  const titulo = window.document.querySelector(".gridMsgFiltre [data-fx-vazio-titulo]");
  assert.equal(titulo.textContent, "Informe o período.");
});

test("a grade sem linha mostra o estado vazio e limpa o filtro", async () => {
  const window = pagina(
    '<div class="ui-dialog"><div class="ui-dialog-content">' +
      '<div class="gridFiltro"><button type="submit" class="limparFiltro">x</button></div>' +
      '<div class="divToScroll"><table class="grid"><thead><tr><th>Código</th></tr></thead>' +
      "<tbody></tbody></table></div></div></div>",
  );
  const doc = window.document;
  const vazio = doc.querySelector(".divToScroll > [data-fx-vazio]");
  assert.ok(vazio);
  assert.equal(
    vazio.querySelector("[data-fx-vazio-titulo]").textContent,
    "Nenhum registro encontrado",
  );
  let limpou = 0;
  doc.querySelector(".limparFiltro").addEventListener("click", (evento) => {
    evento.preventDefault();
    limpou += 1;
  });
  vazio.querySelector("[data-fx-vazio-limpar]").click();
  assert.equal(limpou, 1);

  doc.querySelector(".grid tbody").innerHTML = "<tr><td>1</td></tr>";
  await esperar();
  assert.equal(doc.querySelector(".divToScroll > [data-fx-vazio]"), null);
});

function filtro(campos, botao) {
  return (
    '<div class="ui-dialog"><div class="gridFiltro"><div class="gridFiltroAvancado">' +
    campos +
    '<div><span class="gridFiltroBotoes"><button class="' + botao + '">+</button></span></div>' +
    "</div></div></div>"
  );
}

function campo(classe, estilo, entrada) {
  return (
    '<div class="campoFiltro ' + classe + '" style="' + estilo + '"><div>' +
    (entrada || '<input class="formInput">') +
    "</div></div>"
  );
}

test("o filtro obrigatório recolhe o que passa dos campos principais", () => {
  let campos = "";
  for (let i = 0; i < 9; i++) campos += campo("", "");
  campos += campo("", "", '<input class="formInput amarela" value="x">');
  const window = pagina(
    filtro(campos, "ocultarExibirFiltros ui-icon-plusthick botaoOculto"),
  );
  const doc = window.document;
  const todos = [...doc.querySelectorAll(".campoFiltro")];
  assert.deepEqual(
    todos.map((c) => c.classList.contains("filtroOculto")),
    [false, false, false, false, false, false, true, true, true, false],
  );
  assert.equal(todos[6].style.display, "none");
  assert.equal(
    doc.querySelector(".ocultarExibirFiltros").getAttribute("data-fx-ocultos"),
    "3",
  );
});

test("o campo escondido que o Hardness abriu volta a fechar", () => {
  const window = pagina(
    filtro(
      campo("", "") + campo("filtroOculto", "display: inline") + campo("filtroOculto", "display: none"),
      "ocultarExibirFiltros ui-icon-plusthick botaoOculto",
    ),
  );
  const ocultos = [...window.document.querySelectorAll(".campoFiltro.filtroOculto")];
  assert.deepEqual(ocultos.map((c) => c.style.display), ["none", "none"]);
  assert.equal(
    window.document.querySelector(".ocultarExibirFiltros").getAttribute("data-fx-ocultos"),
    "2",
  );
});

test("com mais filtros aberto o shell não fecha os campos", () => {
  const window = pagina(
    filtro(
      campo("", "") + campo("filtroOculto", "display: inline"),
      "ocultarExibirFiltros ui-icon-minusthick",
    ),
  );
  const oculto = window.document.querySelector(".campoFiltro.filtroOculto");
  assert.equal(oculto.style.display, "inline");
});

test("sem filtro obrigatório o shell não esconde campo", () => {
  let campos = "";
  for (let i = 0; i < 8; i++) campos += campo("", "");
  const window = pagina(filtro(campos, "ocultarExibirFiltros ui-icon-plusthick"));
  assert.equal(window.document.querySelectorAll(".filtroOculto").length, 0);
  assert.equal(
    window.document.querySelector(".ocultarExibirFiltros").getAttribute("data-fx-ocultos"),
    "0",
  );
});

test("a grade sem filtro e sem linha não oferece limpar", () => {
  const window = pagina(
    '<div class="ui-dialog"><div class="divToScroll"><table class="grid">' +
      "<thead><tr><th>Código</th></tr></thead><tbody></tbody></table></div></div>",
  );
  const vazio = window.document.querySelector("[data-fx-vazio]");
  assert.ok(vazio);
  assert.equal(vazio.querySelector("[data-fx-vazio-limpar]"), null);
});
