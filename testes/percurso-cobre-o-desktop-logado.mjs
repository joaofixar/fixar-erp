// Percurso do desktop logado, no navegador, contra a base já no ar.
// Não troca o checkout, não sobe a base e não roda o trazer.
import assert from "node:assert/strict";
import { execFileSync } from "node:child_process";
import { existsSync } from "node:fs";
import { createRequire } from "node:module";
import { homedir, tmpdir } from "node:os";
import { join } from "node:path";
import { fileURLToPath } from "node:url";

const base = process.env.BASE_URL || "http://127.0.0.1:8080";
const usuario = process.env.USUARIO_LOCAL || "fixar";
const senha = process.env.SENHA_LOCAL || "fixar-local";
const vazioDoSino = "Você não possui nenhuma mensagem ou alerta.";

function carregarPlaywright() {
  const prefixos = [
    process.env.PLAYWRIGHT_PREFIX,
    join(homedir(), "AppData", "Local", "Temp", "fx-test-deps"),
    "/tmp/fx-test-deps",
  ].filter(Boolean);
  for (const prefixo of prefixos) {
    const pacote = join(prefixo, "node_modules", "playwright-core", "package.json");
    if (!existsSync(pacote)) continue;
    return createRequire(pacote)("playwright-core");
  }
  throw new Error(
    "playwright-core ausente. Instale com: npm install --prefix /tmp/fx-test-deps playwright-core",
  );
}

function chrome() {
  const candidatos = [
    process.env.CHROME_PATH,
    "C:\\Program Files\\Google\\Chrome\\Application\\chrome.exe",
    "/usr/bin/google-chrome",
    "/usr/bin/chromium",
  ].filter(Boolean);
  for (const caminho of candidatos) {
    if (existsSync(caminho)) return caminho;
  }
  throw new Error("Chrome ausente. Informe CHROME_PATH.");
}

function checkout() {
  return execFileSync("git", ["rev-parse", "--abbrev-ref", "HEAD"], {
    encoding: "utf8",
  }).trim();
}

const raiz = join(fileURLToPath(new URL(".", import.meta.url)), "..");

// O critério de hoje é o id 63 ou o flag de suporte da linha do usuário
// já logado. A leitura não copia linha de produção.
function flagSuporte(id) {
  if (!/^\d+$/.test(id)) throw new Error("o desktop não informou o id do usuário logado");
  const sql =
    "SELECT IFNULL(C007_Usuario_Suporte,'') FROM C007 WHERE C007_Id=" + id + " LIMIT 1";
  let saida;
  try {
    saida = execFileSync(
      "docker",
      [
        "compose",
        "exec",
        "-T",
        "db",
        "mariadb",
        "--protocol=socket",
        "-uroot",
        "-hlocalhost",
        "-pfixar",
        "fixar",
        "-N",
        "-B",
        "-e",
        sql,
      ],
      {
        encoding: "utf8",
        cwd: raiz,
        env: { ...process.env, MSYS_NO_PATHCONV: "1" },
      },
    );
  } catch (erro) {
    throw new Error(
      "não deu para ler o flag de suporte do usuário já logado (" + erro.message + ")",
    );
  }
  return saida.replace(/\s+/g, "");
}

function rgb(hex) {
  const n = Number.parseInt(hex.slice(1), 16);
  return [n >> 16, (n >> 8) & 255, n & 255];
}

function perto(css, hex) {
  const achado = String(css).match(/rgba?\((\d+),\s*(\d+),\s*(\d+)/);
  if (!achado) return false;
  return rgb(hex).every((canal, i) => Math.abs(Number(achado[i + 1]) - canal) <= 2);
}

const { chromium } = carregarPlaywright();

const ramo = checkout();
assert.notEqual(ramo, "master", "o master não é o checkout sob teste");
assert.equal(ramo, "visual-novo", "o checkout sob teste é a branch visual-novo");

let resposta;
try {
  resposta = await fetch(base + "/login", { redirect: "manual" });
} catch (erro) {
  throw new Error(
    "a base não está no ar em " + base + "; o percurso não sobe a base (" + erro.message + ")",
  );
}
assert.ok(resposta.status < 500, "a base já no ar recusou o login");

const browser = await chromium.launch({
  executablePath: chrome(),
  headless: true,
  args: ["--disable-gpu", "--disable-dev-shm-usage"],
});
const contexto = await browser.newContext({ viewport: { width: 1440, height: 900 } });
const pagina = await contexto.newPage();
pagina.setDefaultTimeout(30000);

function dizer(texto) {
  console.log(texto);
}

async function titulos() {
  return pagina.locator("#fx-faixa [role=tab] span").allInnerTexts();
}

async function textoDoModulo() {
  const dialogo = pagina.locator(".ui-dialog.noBorder:visible").first();
  await dialogo.waitFor();
  return (await dialogo.innerText()).replace(/\s+/g, " ").trim().slice(0, 160);
}

async function item(nome) {
  const botao = pagina.locator("#fx-menu button", { hasText: nome }).filter({
    hasText: new RegExp("^\\s*" + nome.replace(/[.*+?^${}()|[\]\\]/g, "\\$&") + "\\s*$"),
  });
  await botao.first().waitFor();
  return botao.first();
}

async function grupos() {
  return pagina.locator("#fx-menu > div").evaluateAll((lista) =>
    lista.map((grupo) => ({
      nome: (grupo.querySelector("p")?.textContent || "").replace(/\s+/g, " ").trim(),
      itens: [...grupo.querySelectorAll("button")].map((botao) =>
        botao.textContent.replace(/\s+/g, " ").trim(),
      ),
    })),
  );
}

async function corDe(seletor, propriedade) {
  return pagina.locator(seletor).first().evaluate((el, prop) => getComputedStyle(el)[prop], propriedade);
}

async function fecharDialogo(trecho) {
  const dialogo = pagina
    .locator(".ui-dialog")
    .filter({ has: pagina.locator(".ui-dialog-titlebar", { hasText: trecho }) })
    .last();
  const fechar = dialogo.locator("> .ui-dialog-titlebar .ui-dialog-titlebar-close");
  await fechar.click();
  try {
    await dialogo.waitFor({ state: "hidden", timeout: 5000 });
  } catch {
    await fechar.click();
    await dialogo.waitFor({ state: "hidden" });
  }
}

try {
  dizer("Entrando com o usuário local");
  await pagina.goto(base + "/login", { waitUntil: "domcontentloaded" });
  const empresa = (await pagina.locator("#login-company").innerText()).replace(/\s+/g, " ").trim();
  assert.ok(empresa, "a tela de login não mostrou a empresa de validação");
  await pagina.locator("#login-user").fill(usuario);
  await pagina.locator("#login-password").fill(senha);
  await pagina.locator("#form-login button[type=submit]").click();
  await pagina.locator("#fx-shell").waitFor({ timeout: 45000 });
  await pagina.getByText("FX ERP", { exact: true }).waitFor();

  dizer("A marca, a empresa e o menu");
  const marca = pagina.locator("#fx-shell aside p").first();
  assert.equal((await marca.innerText()).trim(), "FX ERP");
  const opcoes = await pagina.locator("#selecionaEmpresa option").count();
  const rotulo = pagina.locator("#fx-empresa p");
  const botaoEmpresa = pagina.locator("#fx-empresa button");
  const visivelEmpresa = opcoes < 2 ? rotulo : botaoEmpresa;
  if (opcoes < 2) {
    assert.equal(await botaoEmpresa.count(), 0, "com uma empresa o controle é rótulo");
    assert.equal(await rotulo.count(), 1);
  } else {
    assert.equal(await botaoEmpresa.count(), 1);
  }
  const nomeSobAMarca = (await visivelEmpresa.innerText()).replace(/\s+/g, " ").trim();
  const nomeCompleto = await visivelEmpresa.getAttribute("title");
  assert.equal(nomeSobAMarca, empresa, "a empresa sob a marca não é a do login");
  assert.ok(
    nomeCompleto && nomeCompleto.includes(nomeSobAMarca),
    "o título não mostra o nome completo",
  );

  const menu = await grupos();
  const nomes = menu.map((grupo) => grupo.nome);
  assert.ok(nomes.length > 1, "a sidebar não listou o menu");
  const apps = menu.find((grupo) => grupo.nome === "Apps");
  assert.ok(apps, "quem está logado não vê o grupo Apps");
  assert.ok(apps.itens.includes("Reforma Tributária"), "o grupo Apps não tem Reforma Tributária");
  const gerencial = nomes.indexOf("Gerencial");
  const outros = nomes.indexOf("Outros");
  const indiceApps = nomes.indexOf("Apps");
  if (gerencial >= 0) assert.ok(indiceApps > gerencial, "Apps fica depois de Gerencial");
  if (outros >= 0) assert.ok(indiceApps < outros, "Apps fica antes de Outros");
  const idLogado = await pagina.evaluate(() =>
    window.g && window.g.usuarioAtual != null ? String(window.g.usuarioAtual) : "",
  );
  const root = idLogado === "63" || flagSuporte(idLogado) === "S";
  const loja = menu.find((grupo) => grupo.itens.includes("Loja de apps"));
  if (root) {
    assert.ok(loja, "o root não vê a Loja de apps");
    assert.equal(loja.nome, "Configuração", "a Loja de apps fica em Configuração");
  } else {
    assert.equal(loja, undefined, "quem não é root vê a Loja de apps");
  }

  const fundoSidebar = await corDe("#fx-shell aside", "backgroundColor");
  const fundoArea = await corDe("#desktop", "backgroundColor");
  assert.ok(perto(fundoSidebar, "#1b2430"), "a sidebar não está na cor escura do tema: " + fundoSidebar);
  assert.ok(perto(fundoArea, "#e7eaee"), "o fundo da área não está chapado: " + fundoArea);

  dizer("A paleta pesquisa o menu");
  const abasAntes = await titulos();
  await pagina.keyboard.press("Control+k");
  const paleta = pagina.getByRole("dialog", { name: "Pesquisar o menu" });
  await paleta.waitFor();
  const filtro = pagina.getByLabel("Filtrar o menu");
  assert.equal(await filtro.inputValue(), "");
  assert.equal(await filtro.evaluate((el) => el === document.activeElement), true);
  assert.equal(await pagina.locator("#fx-paleta-lista [aria-selected=true]").count(), 1);
  await filtro.fill("reforma");
  await pagina.locator("#fx-paleta-lista [data-fx-paleta-item]", { hasText: "Reforma Tributária" }).waitFor();
  const filtrados = await pagina.locator("#fx-paleta-lista [data-fx-paleta-item]").allInnerTexts();
  assert.ok(filtrados.every((nome) => /reforma/i.test(nome) || nome === "Reforma Tributária"));
  await pagina.keyboard.press("Escape");
  await paleta.waitFor({ state: "hidden" });
  assert.deepEqual(await titulos(), abasAntes, "desistir da paleta abriu uma aba");

  await pagina.keyboard.press("Control+k");
  await filtro.fill("zzzsemitemxyz");
  await pagina.getByText("Não há item", { exact: true }).waitFor();
  assert.equal(await pagina.locator("#fx-paleta-lista [aria-selected=true]").count(), 0);
  await pagina.keyboard.press("Enter");
  assert.equal(await paleta.count(), 1, "Enter sem resultado fechou a paleta");
  assert.deepEqual(await titulos(), abasAntes, "Enter sem resultado abriu uma aba");
  await pagina.keyboard.press("Escape");

  await pagina.keyboard.press("Control+k");
  await filtro.fill("Relat");
  await pagina.locator("#fx-paleta-lista [data-fx-paleta-item]", { hasText: "Relatórios" }).click();
  await paleta.waitFor({ state: "hidden" });
  await pagina.locator("#fx-faixa [role=tab]", { hasText: "Relatórios" }).waitFor();
  await pagina.locator(".ui-dialog.noBorder:visible").waitFor();
  const relatorios = await textoDoModulo();

  dizer("A faixa abre, troca, reordena, fecha e recarrega vazia");
  const segundos = await pagina.locator("#fx-menu button").evaluateAll((botoes) =>
    botoes
      .map((botao) => {
        const acao = botao.getAttribute("onclick") || "";
        const nome = botao.textContent.replace(/\s+/g, " ").trim();
        const titulo = acao.match(
          /abrirJanela\(\s*event\s*,\s*'[^']*'\s*,\s*'[^']*'\s*,\s*[^,]+,\s*'([^']*)'/,
        );
        return { nome, titulo: titulo ? titulo[1] : nome, acao };
      })
      .filter(
        (botao) =>
          botao.acao.includes("'topleft'") &&
          botao.titulo !== "Relatórios" &&
          botao.nome !== "Loja de apps",
      ),
  );
  assert.ok(segundos.length, "o menu não tem um segundo módulo para a faixa");
  const segundo = segundos[0];
  await (await item(segundo.nome)).click();
  await pagina.locator("#fx-faixa [role=tab]", { hasText: segundo.titulo }).waitFor();
  let faixa = await titulos();
  assert.deepEqual(faixa, ["Relatórios", segundo.titulo]);
  assert.equal(
    await pagina.locator("#fx-faixa [role=tab]", { hasText: segundo.titulo }).getAttribute("aria-selected"),
    "true",
  );

  await pagina.locator("#fx-faixa [role=tab]", { hasText: "Relatórios" }).locator("span").click();
  await pagina.waitForFunction((amostra) => {
    const visiveis = [...document.querySelectorAll(".ui-dialog.noBorder")].filter(
      (el) => getComputedStyle(el).display !== "none" && el.getBoundingClientRect().height > 40,
    );
    if (visiveis.length !== 1) return false;
    const texto = visiveis[0].innerText.replace(/\s+/g, " ").trim().slice(0, 160);
    return texto === amostra;
  }, relatorios, { timeout: 45000 });
  assert.equal(
    await pagina.locator("#fx-faixa [role=tab]", { hasText: "Relatórios" }).getAttribute("aria-selected"),
    "true",
  );
  await pagina.locator("#fx-faixa [role=tab]", { hasText: segundo.titulo }).locator("span").click();
  await pagina.waitForFunction((amostra) => {
    const visiveis = [...document.querySelectorAll(".ui-dialog.noBorder")].filter(
      (el) => getComputedStyle(el).display !== "none" && el.getBoundingClientRect().height > 40,
    );
    if (visiveis.length !== 1) return false;
    const texto = visiveis[0].innerText.replace(/\s+/g, " ").trim().slice(0, 160);
    return texto.length > 15 && texto !== amostra;
  }, relatorios, { timeout: 45000 });

  await pagina.locator("#fx-faixa [role=tab]", { hasText: "Relatórios" }).locator("span").dragTo(
    pagina.locator("#fx-faixa [role=tab]", { hasText: segundo.titulo }).locator("span"),
  );
  faixa = await titulos();
  assert.deepEqual(
    faixa,
    [segundo.titulo, "Relatórios"],
    "arrastar a aba não mudou a ordem: " + faixa.join(", "),
  );

  await pagina.locator("#fx-faixa [aria-label='Fechar Relatórios']").click();
  await pagina.locator("#fx-faixa [role=tab]", { hasText: "Relatórios" }).waitFor({ state: "detached" });
  assert.deepEqual(await titulos(), [segundo.titulo]);

  dizer("A janela de inclusão sobre a aba");
  await (await item("Cliente/Fornecedor")).click();
  await pagina.locator(".ui-dialog:visible .ui-dialog-titlebar", { hasText: "Clientes e Fornecedores" }).waitFor();
  assert.deepEqual(await titulos(), [segundo.titulo], "Cliente/Fornecedor virou aba");
  const incluir = pagina.locator("#cad001Incluir, button[botaoid='cad001Incluir']");
  await incluir.first().waitFor();
  assert.equal(await incluir.first().isEnabled(), true, "o incluir de Cliente/Fornecedor está desligado");
  await incluir.first().click();
  await pagina.locator(".ui-dialog:visible .ui-dialog-titlebar", { hasText: "Clientes/Fornecedores" }).waitFor();
  assert.deepEqual(await titulos(), [segundo.titulo], "a inclusão virou aba");
  await fecharDialogo("Clientes/Fornecedores");
  await fecharDialogo("Clientes e Fornecedores");
  assert.deepEqual(await titulos(), [segundo.titulo]);

  await pagina.reload({ waitUntil: "domcontentloaded" });
  await pagina.locator("#fx-shell").waitFor({ timeout: 45000 });
  assert.deepEqual(await titulos(), [], "recarregar não esvaziou a faixa");

  dizer("A área vazia e Cliente/Fornecedor com a faixa vazia");
  assert.equal(await pagina.locator("[data-fx-certificado], img[src*='certificadoDigital']").count(), 0);
  assert.equal(await pagina.locator("[data-fx-nota]").count(), 0);
  assert.equal(await pagina.locator("[data-fx-aviso]").count(), 0);
  assert.equal(
    await pagina.getByRole("heading", { name: "Notas" }).count(),
    0,
    "a área reservou um bloco vazio de Nota",
  );

  await (await item("Cliente/Fornecedor")).click();
  const lista = pagina.locator(".ui-dialog:visible").filter({
    has: pagina.locator(".ui-dialog-titlebar", { hasText: "Clientes e Fornecedores" }),
  });
  await lista.waitFor();
  assert.deepEqual(await titulos(), [], "com a faixa vazia Cliente/Fornecedor virou aba");
  await lista.getByText("Por favor, utilize o filtro.").waitFor();
  const textoGrade = await lista.innerText();
  assert.equal(/Fatal error|Parse error|Uncaught/.test(textoGrade), false, "a grade abriu com erro");
  assert.equal(
    await lista.locator(".grid:visible tbody tr.color1, .grid:visible tbody tr.color2").count(),
    0,
    "a grade de Cliente/Fornecedor não está vazia",
  );
  await fecharDialogo("Clientes e Fornecedores");
  assert.equal(await pagina.locator(".ui-dialog:visible").count(), 0, "fechar a janela não valeu");
  assert.deepEqual(await titulos(), []);

  dizer("A linha sob o mouse e o passo");
  await pagina.locator("[data-fx-avatar]").click();
  const menuUsuario = pagina.getByRole("menu", { name: "Usuário" });
  await menuUsuario.waitFor();
  const login = (await menuUsuario.locator("p").nth(0).innerText()).trim();
  const perfil = (await menuUsuario.locator("p").nth(1).innerText()).trim();
  assert.equal(login.toLowerCase(), usuario.toLowerCase());
  assert.ok(perfil, "o menu do avatar não mostrou o perfil");
  const permiteTroca = (await pagina.locator("#selecionaUsuario option").count()) > 0;
  const trocas = await pagina.locator("[data-fx-usuario-item]").count();
  if (permiteTroca) assert.ok(trocas > 0, "o perfil permite a troca e a lista não apareceu");
  else assert.equal(trocas, 0, "sem permissão a lista de usuários apareceu");
  const marcado = (await menuUsuario.locator("[aria-checked=true]").innerText()).trim();
  const outro = marcado === "Pequeno" ? "Médio" : "Pequeno";
  const pixels = outro === "Médio" ? "13px" : "12px";
  const passoNovo = outro === "Médio" ? "13" : "12";
  await menuUsuario.getByRole("menuitemradio", { name: outro }).click();
  await pagina.waitForFunction(
    (passo) =>
      document.body?.getAttribute("data-fx-passo") === passo &&
      document.getElementById("fx-shell"),
    passoNovo,
    { timeout: 45000 },
  );

  await (await item("Empresas")).click();
  const empresas = pagina.locator(".ui-dialog:visible").filter({
    has: pagina.locator(".ui-dialog-titlebar", { hasText: "Empresas" }),
  });
  await empresas.waitFor();
  const linha = empresas.locator(".grid:visible tbody tr").first();
  await linha.waitFor();
  await linha.hover();
  const pintura = await linha.evaluate((el) => {
    const celulas = [...el.querySelectorAll("td")].map((td) => getComputedStyle(td).backgroundColor);
    return {
      linha: getComputedStyle(el).backgroundColor,
      celulas,
    };
  });
  const transparente = (cor) => !cor || cor === "transparent" || /rgba\([^)]*,\s*0\s*\)/.test(cor);
  const celulaAmarela = pintura.celulas.some((cor) => perto(cor, "#FFE98C"));
  const linhaAmostra = perto(pintura.linha, "#FFE98C") && pintura.celulas.some(transparente);
  assert.ok(
    celulaAmarela || linhaAmostra,
    "a linha sob o mouse não está no amarelo: " + JSON.stringify(pintura),
  );
  const tipo = await empresas.locator(".grid:visible").first().evaluate((el) => getComputedStyle(el).fontSize);
  assert.equal(tipo, pixels, "o passo " + outro + " não mudou o tipo da grade");
  await fecharDialogo("Empresas");

  const passoOriginal = marcado === "Médio" ? "13" : marcado === "Grande" ? "14" : "12";
  await pagina.locator("[data-fx-avatar]").click();
  await pagina.getByRole("menuitemradio", { name: marcado }).click();
  await pagina.waitForFunction(
    (passo) =>
      document.body?.getAttribute("data-fx-passo") === passo &&
      document.getElementById("fx-shell"),
    passoOriginal,
    { timeout: 45000 },
  );

  dizer("O sino e o grupo Apps");
  await pagina.locator("[data-fx-sino]").click();
  const alertas = pagina.getByRole("dialog", { name: "Alertas" });
  await alertas.waitFor();
  await alertas.getByText(vazioDoSino).waitFor();
  await pagina.keyboard.press("Escape");
  await alertas.waitFor({ state: "hidden" });

  const popup = contexto.waitForEvent("page");
  await (await item("Reforma Tributária")).click();
  const fora = await popup;
  await fora.waitForLoadState("domcontentloaded");
  assert.match(fora.url(), /reformaTributaria/);
  assert.deepEqual(await titulos(), [], "o grupo Apps virou aba do desktop");
  await fora.close();

  dizer("Sair");
  await pagina.locator("[data-fx-avatar]").click();
  await pagina.locator("[data-fx-sair]").click();
  await pagina.locator("#login-password").waitFor({ timeout: 20000 });
  assert.equal(await pagina.locator("#fx-shell").count(), 0);

  dizer("O percurso cobriu o desktop logado.");
} catch (erro) {
  const caminho = join(tmpdir(), "percurso-desktop.png");
  await pagina.screenshot({ path: caminho, fullPage: true }).catch(() => {});
  const info = await pagina
    .evaluate(() =>
      [...document.querySelectorAll(".ui-dialog, .ui-dialog-titlebar")].slice(0, 8).map((el) => ({
        tag: el.tagName,
        classe: el.className,
        texto: (el.textContent || "").replace(/\s+/g, " ").trim().slice(0, 80),
        display: getComputedStyle(el).display,
        vis: getComputedStyle(el).visibility,
        w: Math.round(el.getBoundingClientRect().width),
        h: Math.round(el.getBoundingClientRect().height),
      })),
    )
    .catch(() => []);
  console.error(JSON.stringify(info, null, 2));
  console.error("captura: " + caminho);
  throw erro;
} finally {
  await browser.close();
}
