// Com a faixa vazia, a área mostra o certificado, os avisos e as notas.
// Uma aba visível tira os três. A costura observada é o DOM do desktop.
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

function pagina(corpo, abrir) {
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
  window.abrirJanela = abrir || function () {};
  window.g = { empresaAtual: "1" };
  window.eval(fonte);
  return window;
}

function base(miolo) {
  return (
    '<div id="topo"></div>' +
    '<div id="desktop">' +
    miolo +
    "</div>" +
    '<div id="footer"><div>' +
    '<font id="campoEmpresa"><span>VAL</span></font>' +
    " | Diretor | " +
    '<font title="banco">banco</font>' +
    "</div></div>"
  );
}

function areaCheia() {
  return (
    '<div id="versao">versão 9.9</div>' +
    '<div class="boletoNotificacao">Boleto de parceiro disponível</div>' +
    '<img class="logo-fundo-inicio" alt="logoHardnessBranca" src="/logo.png">' +
    '<div class="ultimasAttTitulo">Últimas atualizações:</div>' +
    '<div class="notificacao-video">notificacao_00041.php</div>' +
    '<div class="senha-card">Sua senha expirou</div>' +
    '<div style="border: 1px groove #FF0000">' +
    '<img src="/hardness3/static/img/certificadoDigital.jpg" alt="">' +
    "<div>Seu certificado digital vence em 3 dias!" +
    "<script>dialogConfirm('vencimento')</script></div></div>" +
    '<div data-fx-aviso>Aviso do perfil</div>' +
    '<div data-fx-aviso>Segundo aviso</div>' +
    '<article data-fx-nota><time datetime="2026-03-01">01/03/2026</time>' +
    "<h2>Antiga</h2><p>Primeira mudança.</p></article>" +
    '<article data-fx-nota><time datetime="2026-11-02">02/11/2026</time>' +
    "<h2>Nova</h2><p>Segunda mudança.</p>" +
    '<iframe src="notificacao_00001.php"></iframe></article>'
  );
}

function nomes(trabalho) {
  return [...trabalho.children].map(function (el) {
    if (el.hasAttribute("data-fx-certificado")) return "certificado";
    if (el.hasAttribute("data-fx-aviso")) return "aviso:" + el.textContent.trim();
    const data = el.querySelector("time").getAttribute("datetime");
    return "nota:" + data;
  });
}

function abrirModulo(window, raiz) {
  window.abrirJanela(
    {},
    raiz,
    "",
    "id",
    "Clientes",
    "",
    "/cad/cad001/content/",
    "",
    "full",
    false,
    "topleft",
    false,
  );
}

test("a faixa vazia mostra certificado, avisos e notas da mais nova à mais antiga", () => {
  const window = pagina(base(areaCheia()));
  const trabalho = window.document.getElementById("fx-trabalho");
  assert.ok(trabalho);
  assert.equal(trabalho.hidden, false);
  assert.deepEqual(nomes(trabalho), [
    "certificado",
    "aviso:Aviso do perfil",
    "aviso:Segundo aviso",
    "nota:2026-11-02",
    "nota:2026-03-01",
  ]);
  assert.match(trabalho.textContent, /dialogConfirm/);
  assert.equal(trabalho.querySelector("iframe"), null);
  assert.equal(trabalho.textContent.includes("versão 9.9"), false);
  assert.equal(trabalho.textContent.includes("Boleto de parceiro"), false);
  assert.equal(trabalho.textContent.includes("Últimas atualizações"), false);
  assert.equal(trabalho.textContent.includes("notificacao_00041"), false);
  assert.equal(trabalho.textContent.includes("Sua senha expirou"), false);
  assert.equal(trabalho.querySelector(".logo-fundo-inicio"), null);
  const versao = window.document.getElementById("versao");
  assert.equal(versao.hidden, true);
  assert.equal(
    window.document.querySelector("#fx-shell").textContent.includes("versão 9.9"),
    false,
  );
});

test("sem nota publicada a área não reserva bloco vazio", () => {
  const window = pagina(
    base('<div data-fx-aviso>Só o aviso</div>'),
  );
  const trabalho = window.document.getElementById("fx-trabalho");
  assert.equal(trabalho.querySelector("[data-fx-nota]"), null);
  assert.equal(trabalho.textContent.includes("Não há"), false);
  assert.equal(trabalho.textContent.trim(), "Só o aviso");
});

test("uma aba visível tira os três e a faixa vazia traz de volta", () => {
  const window = pagina(base(areaCheia()));
  const trabalho = window.document.getElementById("fx-trabalho");
  abrirModulo(window, "clientes");
  assert.equal(trabalho.hidden, true);
  window.document.querySelector("[data-fx-fechar]").click();
  assert.equal(trabalho.hidden, false);
  assert.deepEqual(nomes(trabalho), [
    "certificado",
    "aviso:Aviso do perfil",
    "aviso:Segundo aviso",
    "nota:2026-11-02",
    "nota:2026-03-01",
  ]);
});

test("a senha continua janela e o cockpit inicial não ocupa a faixa", () => {
  let abriu = 0;
  const window = pagina(
    base(areaCheia()) +
      '<div class="ui-dialog"><div class="senha-dialogo">Sua senha expirou</div></div>',
    function () {
      abriu += 1;
    },
  );
  const senha = window.document.querySelector(".senha-dialogo");
  assert.equal(senha.closest("#fx-trabalho"), null);
  assert.equal(senha.hidden, false);
  window.abrirJanela(
    {},
    "Cockpit",
    "cockpit",
    "menu",
    "",
    "",
    "/etc/etc011/content/etc011ContentCockpit/",
    "",
    "full",
    false,
    "topleft",
    false,
  );
  assert.equal(abriu, 0);
  assert.equal(window.document.querySelector("[data-fx-aba]"), null);
  assert.equal(window.document.getElementById("fx-trabalho").hidden, false);
  window.abrirJanela(
    {},
    "B.I.",
    "",
    "menu",
    "B.I.",
    ".: B.I. :.",
    "/etc/etc011/content/etc011ContentCockpit/",
    "",
    "full",
    false,
    "topleft",
    false,
  );
  assert.equal(abriu, 1);
  assert.equal(
    window.document.querySelector("[data-fx-aba]").textContent.includes("B.I."),
    true,
  );
});

test("trocar o miolo do desktop monta a área de novo", () => {
  const window = pagina(base('<div data-fx-aviso>Aviso velho</div>'));
  window.document.getElementById("desktop").innerHTML =
    '<div data-fx-aviso>Aviso novo</div>' +
    '<article data-fx-nota><time datetime="2026-11-02">02/11/2026</time>' +
    "<h2>Nova</h2><p>Texto novo.</p></article>";
  abrirModulo(window, "estoque");
  const trabalho = window.document.getElementById("fx-trabalho");
  assert.equal(trabalho.hidden, true);
  assert.equal(trabalho.textContent.includes("Aviso novo"), true);
  assert.equal(trabalho.textContent.includes("Aviso velho"), false);
  window.document.querySelector("[data-fx-fechar]").click();
  assert.equal(trabalho.hidden, false);
});
