"""Grava a conexão local e os diretórios de cache.

O Hardness que sobe é o que está em src/. A preparação não recria o produto.
"""

import os
import sys

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
COPIA = os.path.join(RAIZ, "src", "hardness3")


def escrever_configuracao():
    conf = os.path.join(COPIA, "bibliotecas", "confUsuario.php")
    with open(conf, "w", encoding="utf-8", newline="\n") as arquivo:
        arquivo.write(
            """<?php
namespace hardness;
// O Kiri publica o serviço em web.fixar-erp.kiri.localhost. O login
// devolve o navegador para urlRaiz, então o host é o da requisição.
$origem = $_SERVER['HTTP_HOST'] ?? 'web.fixar-erp.kiri.localhost';
$confUsuario = array(
    'dbDatabase' => 'fixar',
    'dbHost' => 'db',
    'dbUser' => 'fixar',
    'dbPass' => 'fixar',
    'pathRaiz' => '/var/www/html/hardness3/',
    'urlRaiz' => 'http://' . $origem . '/hardness3/',
);
"""
        )
    ide = os.path.join(COPIA, "ide")
    os.makedirs(ide, exist_ok=True)
    with open(os.path.join(ide, "sysConfig.php"), "w", encoding="utf-8", newline="\n") as arquivo:
        arquivo.write(
            """<?php
$sys = array(
    'nomeEmpresa' => 'Fixar',
    'dominio' => 'localhost',
    'dbDatabase' => 'fixar',
    'dbHost' => 'db',
    'dbUser' => 'fixar',
    'dbPass' => 'fixar',
);
"""
        )


def garantir_cache():
    for relativa in ("smarty/templates_c", "smarty/cache"):
        os.makedirs(os.path.join(COPIA, relativa), exist_ok=True)


def main():
    if not os.path.isdir(os.path.join(COPIA, "sistema")):
        sys.exit("O Hardness versionado não está em src/.")
    escrever_configuracao()
    garantir_cache()
    print("Conexão local e cache prontos.")


if __name__ == "__main__":
    main()
