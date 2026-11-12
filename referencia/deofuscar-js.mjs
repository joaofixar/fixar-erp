/**
 * Troca os números do sistema.js pelo texto que eles escondem.
 * Uso: node referencia/deofuscar-js.mjs
 *
 * A saída é referência de leitura. O sistema legado não a carrega.
 * Os nomes _0x… permanecem: o ofuscador não guardou o nome original.
 */
import fs from "fs";
import path from "path";
import { fileURLToPath } from "url";

const raiz = path.resolve(path.dirname(fileURLToPath(import.meta.url)), "..");
const origem = path.join(raiz, "sistema-legado", "static", "js", "sistema.js");
const destino = path.join(raiz, "referencia", "static", "js", "sistema.js");
const fonte = fs.readFileSync(origem, "utf8");

function casar(texto, inicio, abre, fecha) {
  let profundidade = 0;
  let aspas = null;
  for (let i = inicio; i < texto.length; i++) {
    const c = texto[i];
    if (aspas) {
      if (c === "\\") {
        i++;
        continue;
      }
      if (c === aspas) aspas = null;
      continue;
    }
    if (c === '"' || c === "'" || c === "`") {
      aspas = c;
      continue;
    }
    if (c === abre) profundidade++;
    else if (c === fecha) {
      profundidade--;
      if (profundidade === 0) return i;
    }
  }
  throw new Error("bloco desbalanceado em " + inicio);
}

function blocoFuncao(texto, nome) {
  const marca = "function " + nome;
  const inicio = texto.indexOf(marca);
  if (inicio < 0) throw new Error("não achei " + nome);
  const abre = texto.indexOf("{", inicio);
  const fecha = casar(texto, abre, "{", "}");
  return texto.slice(inicio, fecha + 1);
}

const fimDoGiro = fonte.indexOf("}(a0_0x3cb0,0x5e96c)");
if (fimDoGiro < 0) throw new Error("não achei o giro do vetor");
const giro =
  fonte.slice(fonte.indexOf("(function"), fimDoGiro + "}(a0_0x3cb0,0x5e96c)".length) + ")";
const preparo = blocoFuncao(fonte, "a0_0x3cb0") + "\n" + blocoFuncao(fonte, "a0_0x43ee") + "\n" + giro;
const vm = new Function(preparo + ";\nreturn a0_0x43ee;");
const decodificar = vm();

const textos = new Map();
for (let indice = 0; indice < 0x8000; indice++) {
  const valor = decodificar(indice);
  if (typeof valor === "string") textos.set(indice, valor);
}

const apelidos = new Set(["a0_0x43ee", "a0_0x1016e2"]);
const copia = /\bvar\s+(_0x[0-9a-fA-F]+)\s*=\s*(a0_0x43ee|a0_0x1016e2|_0x[0-9a-fA-F]+)\s*(?=[,;])/g;
const arestas = [...fonte.matchAll(copia)];
let cresceu = true;
while (cresceu) {
  cresceu = false;
  for (const achado of arestas) {
    if (apelidos.has(achado[2]) && !apelidos.has(achado[1])) {
      apelidos.add(achado[1]);
      cresceu = true;
    }
  }
}

const grupo = fonte.indexOf(";(function");
const fechaGrupo = casar(fonte, grupo + 1, "(", ")");
const corpo =
  fonte.slice(fimDoGiro + "}(a0_0x3cb0,0x5e96c),".length, fechaGrupo) +
  fonte.slice(fechaGrupo + 1);

function semFuncao(texto, nome) {
  const marca = "function " + nome;
  const inicio = texto.indexOf(marca);
  if (inicio < 0) return texto;
  const abre = texto.indexOf("{", inicio);
  const fecha = casar(texto, abre, "{", "}");
  return texto.slice(0, inicio) + texto.slice(fecha + 1);
}

let saida = semFuncao(semFuncao(corpo, "a0_0x43ee"), "a0_0x3cb0");
let trocas = 0;
let faltando = 0;
saida = saida.replace(/\b([A-Za-z0-9_]+)\((0x[0-9a-fA-F]+)\)/g, (todo, nome, numero) => {
  if (!apelidos.has(nome)) return todo;
  const valor = textos.get(Number(numero));
  if (valor === undefined) {
    faltando++;
    return todo;
  }
  trocas++;
  return JSON.stringify(valor);
});

const reservada = new Set(
  "break case catch class const continue debugger default delete do else enum export extends false finally for function if implements import in instanceof interface let new null package private protected public return static super switch this throw true try typeof var void while with yield await".split(
    " "
  )
);
function desescaparCadeias(texto) {
  let saida = "";
  let i = 0;
  while (i < texto.length) {
    const c = texto[i];
    if (c !== '"' && c !== "'") {
      saida += c;
      i++;
      continue;
    }
    let valor = "";
    i++;
    while (i < texto.length && texto[i] !== c) {
      if (texto[i] === "\\") {
        const proximo = texto[i + 1];
        if (proximo === "x" && /^[0-9a-fA-F]{2}/.test(texto.slice(i + 2, i + 4))) {
          valor += String.fromCharCode(parseInt(texto.slice(i + 2, i + 4), 16));
          i += 4;
          continue;
        }
        if (proximo === "u" && /^[0-9a-fA-F]{4}/.test(texto.slice(i + 2, i + 6))) {
          valor += String.fromCharCode(parseInt(texto.slice(i + 2, i + 6), 16));
          i += 6;
          continue;
        }
        const simples = { n: "\n", r: "\r", t: "\t", "\\": "\\", "'": "'", '"': '"' };
        valor += proximo in simples ? simples[proximo] : proximo;
        i += 2;
        continue;
      }
      valor += texto[i];
      i++;
    }
    i++;
    saida += JSON.stringify(valor);
  }
  return saida;
}

function fimDaCadeia(texto, inicio) {
  const aspas = texto[inicio];
  for (let i = inicio + 1; i < texto.length; i++) {
    if (texto[i] === "\\") {
      i++;
      continue;
    }
    if (texto[i] === aspas) return i + 1;
  }
  return texto.length;
}

function legivel(texto) {
  let saida = "";
  let i = 0;
  while (i < texto.length) {
    const c = texto[i];
    if (c === '"' || c === "'") {
      const fim = fimDaCadeia(texto, i);
      saida += texto.slice(i, fim);
      i = fim;
      continue;
    }
    if (texto.startsWith("!![]", i)) {
      saida += "true";
      i += 4;
      continue;
    }
    if (texto.startsWith("![]", i)) {
      saida += "false";
      i += 3;
      continue;
    }
    const acesso = texto.slice(i).match(/^\[(["'])([A-Za-z_$][A-Za-z0-9_$]*)\1\]/);
    if (acesso) {
      const nome = acesso[2];
      const palavra = saida.match(/([A-Za-z_$][A-Za-z0-9_$]*)$/);
      const palavraReservada = palavra && reservada.has(palavra[1]);
      if (!reservada.has(nome) && !palavraReservada) {
        const depois = texto[i + acesso[0].length];
        saida += "." + nome + (depois && /[A-Za-z0-9_$]/.test(depois) ? " " : "");
        i += acesso[0].length;
        continue;
      }
    }
    saida += c;
    i++;
  }
  return saida;
}

saida = desescaparCadeias(legivel(saida));

const nomes = [...apelidos].sort((a, b) => b.length - a.length).join("|");
const apelido = "(?:" + nomes + ")";
saida = saida.replace(new RegExp(String.raw`\bvar\s+${apelido}\s*=\s*${apelido}\s*;`, "g"), "");
saida = saida.replace(new RegExp(String.raw`\bvar\s+${apelido}\s*=\s*${apelido}\s*,`, "g"), "var ");
saida = saida.replace(new RegExp(String.raw`,\s*${apelido}\s*=\s*${apelido}\s*(?=[,;])`, "g"), "");

const cabecalho = `/**
 * sistema.js
 *
 * Versão legível de sistema-legado/static/js/sistema.js.
 * O original esconde cada texto num vetor e chama o texto por um número.
 * Aqui o número foi trocado pelo texto. Os nomes _0x… continuam,
 * porque o ofuscador não guardou o nome original.
 * Este arquivo é referência de leitura e não é carregado pelo sistema.
 */

`;

fs.mkdirSync(path.dirname(destino), { recursive: true });
fs.writeFileSync(destino, cabecalho + saida);
console.log(
  "trocas=" + trocas + " faltando=" + faltando + " apelidos=" + apelidos.size + " bytes=" + (cabecalho.length + saida.length)
);
