# Lê Criar_Campo, Criar_Campos e Criar_Indice. O resto do PHP fica de fora.
function trim(s) {
    sub(/^[[:space:]]+/, "", s)
    sub(/[[:space:]]+$/, "", s)
    return s
}

function nome_ok(s) {
    return s ~ /^[A-Za-z0-9_]+$/
}

function tabela_de(s,    p) {
    p = index(s, "_")
    if (p < 2) return ""
    return substr(s, 1, p - 1)
}

function tipo_ok(s,    i, n, c, aspa) {
    if (s == "") return 0
    if (index(s, "`") > 0) return 0
    n = length(s)
    i = 1
    aspa = 0
    while (i <= n) {
        c = substr(s, i, 1)
        if (c == "\\") {
            i += 2
            continue
        }
        if (aspa) {
            if (c == "'") aspa = 0
        } else if (c == "'") {
            aspa = 1
        } else if (c == ";") {
            return 0
        }
        i++
    }
    return aspa == 0
}

# QPOS fica na posição seguinte. Vazio com QPOS 0 é falha.
function ler_aspas(s, i,    q, n, c, out) {
    n = length(s)
    if (i < 1 || i > n) {
        QPOS = 0
        return ""
    }
    q = substr(s, i, 1)
    if (q != "'" && q != "\"") {
        QPOS = 0
        return ""
    }
    i++
    out = ""
    while (i <= n) {
        c = substr(s, i, 1)
        if (c == "\\") {
            i++
            if (i <= n) out = out substr(s, i, 1)
            i++
            continue
        }
        if (c == q) {
            QPOS = i + 1
            return out
        }
        out = out c
        i++
    }
    QPOS = 0
    return ""
}

function pular_espaco(s, i,    n, c) {
    n = length(s)
    while (i <= n) {
        c = substr(s, i, 1)
        if (c != " " && c != "\t") break
        i++
    }
    return i
}

function emitir_coluna(nome, tipo,    tabela) {
    nome = trim(nome)
    if (!nome_ok(nome) || !tipo_ok(tipo)) return
    tabela = tabela_de(nome)
    if (!nome_ok(tabela)) return
    print "coluna\t" tabela "\t" nome "\t" tipo
}

function emitir_indice(bruto, modo,    nome, campos, tabela, parte, n, i, ok) {
    nome = trim(bruto)
    modo = trim(modo)
    if (modo != "" && modo != "UNIQUE") return
    n = split(nome, parte, ",,")
    ok = 1
    for (i = 1; i <= n; i++) {
        parte[i] = trim(parte[i])
        if (!nome_ok(parte[i])) ok = 0
    }
    if (!ok || n < 1) return
    campos = parte[1]
    for (i = 2; i <= n; i++) campos = campos "," parte[i]
    tabela = tabela_de(parte[1])
    if (!nome_ok(tabela)) return
    nome = "IDX_" parte[1]
    for (i = 2; i <= n; i++) nome = nome "_" parte[i]
    print "indice\t" tabela "\t" nome "\t" campos "\t" modo
}

function primeira_aspas(s,    i, n, c) {
    n = length(s)
    for (i = 1; i <= n; i++) {
        c = substr(s, i, 1)
        if (c == "'" || c == "\"") return i
    }
    return 0
}

{
    sub(/\r$/, "")
    if (em) {
        if ($0 ~ /\$/) {
            if (index($0, "));") > 0) em = 0
            next
        }
        i = primeira_aspas($0)
        if (i > 0 && index($0, "=>") > 0) {
            chave = ler_aspas($0, i)
            if (QPOS > 0) {
                j = primeira_aspas(substr($0, QPOS))
                if (j > 0) {
                    tipo = ler_aspas($0, QPOS + j - 1)
                    if (QPOS > 0) emitir_coluna(chave, tipo)
                }
            }
        }
        if (index($0, "));") > 0) em = 0
        next
    }
    if (index($0, "Criar_Campos(") > 0) {
        i = index($0, "(")
        i = pular_espaco($0, i + 1)
        nome = ler_aspas($0, i)
        if (QPOS > 0 && nome_ok(trim(nome))) em = 1
        next
    }
    if (index($0, "Criar_Campo(") > 0) {
        i = index($0, "(")
        i = pular_espaco($0, i + 1)
        nome = ler_aspas($0, i)
        if (QPOS == 0) next
        i = pular_espaco($0, QPOS)
        if (substr($0, i, 1) != ",") next
        i = pular_espaco($0, i + 1)
        tipo = ler_aspas($0, i)
        if (QPOS == 0) next
        emitir_coluna(nome, tipo)
        next
    }
    if (index($0, "Criar_Indice(") > 0) {
        i = index($0, "(")
        i = pular_espaco($0, i + 1)
        nome = ler_aspas($0, i)
        if (QPOS == 0) next
        modo = ""
        i = pular_espaco($0, QPOS)
        if (substr($0, i, 1) == ",") {
            i = pular_espaco($0, i + 1)
            if (substr($0, i, 1) == "'" || substr($0, i, 1) == "\"") {
                modo = ler_aspas($0, i)
                if (QPOS == 0) next
            }
        }
        emitir_indice(nome, modo)
    }
}
