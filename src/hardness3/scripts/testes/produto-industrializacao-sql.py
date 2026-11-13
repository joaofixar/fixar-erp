#!/usr/bin/env python3
"""python3 scripts/testes/produto-industrializacao-sql.py

Compara os filtros reais com fixtures SQLite em memória. Não usa o conector
MySQL, credenciais, bases de clientes ou arquivos de personalização.
"""
import json
from pathlib import Path
import re
import sqlite3
import subprocess

ROOT = Path(__file__).resolve().parents[2]
ROOTS = ["prog", "bibliotecas", "configuracoes", "scripts/sql", "formulas",
         "aplicacoes", "integradores", "mobile"]
files = subprocess.check_output(
    ["rg", "-l", "-0", "D049_Flag_Tipo", *ROOTS,
     "--glob", "*.php", "--glob", "*.sql", "--glob", "*.inc",
     "--glob", "!**/vendor/**", "--glob", "!**/node_modules/**",
     "--glob", "!**/db.*"], cwd=ROOT
).decode().strip("\0").split("\0")

# Retira somente comentários PHP. Preserva SQL dentro de strings e heredocs.
tokenizer = r"""
$result = [];
foreach (array_slice($argv, 1) as $file) {
    $source = file_get_contents($file);
    if (substr($file, -4) !== '.sql') {
        $source = '';
        foreach (token_get_all(file_get_contents($file)) as $token) {
            if (is_array($token)) {
                $source .= in_array($token[0], [T_COMMENT, T_DOC_COMMENT], true)
                    ? str_repeat("\n", substr_count($token[1], "\n")) : $token[1];
            } else { $source .= $token; }
        }
    }
    $result[$file] = $source;
}
echo json_encode($result);
"""
sources = json.loads(subprocess.check_output(
    ["php", "-r", tokenizer, *files], cwd=ROOT
))

memory = sqlite3.connect(":memory:")
memory.create_function("IF", 3, lambda condition, yes, no: yes if condition else no)
predicate = re.compile(
    r"(?:\bIFNULL\(\s*)?(?:\b\w+\.)?\bx?D049_Flag_Tipo"
    r"(?:\s*,\s*'[^']*'\s*\))?\s*"
    r"(?:(?:=|!=|<>)\s*'(?:CP|PD|IN|MT|MD)?'|(?:NOT\s+)?IN\s*\([^)]*\))", re.I
)
checks = 0
filters = 0
production_filters = 0
for name, source in sources.items():
    # Comentários SQL não representam filtros ativos.
    source = re.sub(r"/\*.*?\*/|--[^\n]*", "", source, flags=re.S)
    for match in predicate.finditer(source):
        expression = re.sub(r"\b\w+\.(?=D049_Flag_Tipo)", "", match.group(), flags=re.I)
        query = ("SELECT " + expression + " FROM "
                 "(SELECT ? AS D049_Flag_Tipo, ? AS xD049_Flag_Tipo)")
        produced = memory.execute(query, ("PD", "PD")).fetchone()[0]
        industrial = memory.execute(query, ("IN", "IN")).fetchone()[0]
        assert produced == industrial, f"Tipos seguem filtros diferentes em {name}: {expression}"
        filters += 1
        checks += 1
        if "'PD'" in expression:
            production_filters += 1
            for other in ["CP", "MT", "MD", "", None]:
                # Um filtro reservado à produção não deve aceitar outro tipo.
                result = memory.execute(query, (other, other)).fetchone()[0]
                assert not result, f"Filtro de produção aceita outro tipo: {name}/{other}"
                checks += 1
assert production_filters >= 23, "Filtros de produção não encontrados; revisar a coleta"

# Executa a projeção real que bloqueia cotações sem engenharia.
for program in ["009", "019"]:
    source = sources[f"prog/est/est{program}/grid_func-ajax.php"]
    projection = re.search(r"SELECT COUNT\(distinct T006_Id\) as Total_Cotar,\s*"
                           r"(.*?)\bFROM T006", source, flags=re.S | re.I)
    assert projection, f"Validação agregada ausente: EST{program}"
    query = ("SELECT COUNT(distinct T006_Id) AS Total_Cotar, " + projection[1]
             + " FROM (SELECT 1 AS T006_Id, ? AS D049_Flag_Tipo, ? AS D074_Id)")
    for engineering in [None, 10]:
        produced = memory.execute(query, ("PD", engineering)).fetchone()
        industrial = memory.execute(query, ("IN", engineering)).fetchone()
        assert industrial == produced == (1, int(engineering is None))
        checks += 1
    assert memory.execute(query, ("CP", None)).fetchone() == (1, 0)
    checks += 1

memory.close()
print(f"OK: {checks} verificações SQL; {filters} filtros, incluindo "
      f"{production_filters} de produção; somente fixtures em memória.")
