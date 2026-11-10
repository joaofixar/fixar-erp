"""Copia a estrutura da produção para o banco local e semeia o acesso.

A produção só é lida. A sessão é somente leitura, o dump não pede trava
e a conta precisa ser de leitura. O que entra de registro é a empresa que
concentra orçamento, pedido e nota fiscal de saída, um usuário daqui e o
vínculo de permissão. Cliente, produto, orçamento, pedido, nota e título
não são copiados.
"""

import base64
import json
import os
import re
import subprocess
import sys

RAIZ = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
USUARIO_LOCAL = "fixar"
SENHA_LOCAL = "fixar-local"
# A produção é MySQL 5.6. O cliente 8/11 pede uma coluna de metadado que esse servidor não tem.
IMAGEM_LEITURA = "mysql:5.7"
ESCRITA = {
    "INSERT",
    "UPDATE",
    "DELETE",
    "CREATE",
    "DROP",
    "ALTER",
    "INDEX",
    "TRIGGER",
    "EVENT",
    "FILE",
    "SUPER",
    "RELOAD",
    "SHUTDOWN",
}

credencial = {}


def ambiente():
    env = os.environ.copy()
    if sys.platform == "win32" or env.get("MSYSTEM"):
        env.setdefault("DOCKER_HOST", "npipe:////./pipe/kiri")
    return env


def sem_senha(texto):
    if not texto:
        return ""
    senha = credencial.get("PRODUCAO_SENHA", "")
    if senha:
        texto = texto.replace(senha, "***")
    return texto


def executar(comando, entrada=None, cwd=None):
    return subprocess.run(
        comando,
        input=entrada,
        capture_output=True,
        cwd=cwd,
        env=ambiente(),
    )


def falhar(mensagem, resultado=None):
    if resultado is not None:
        detalhe = sem_senha((resultado.stderr or resultado.stdout).decode("utf-8", "replace")).strip()
        if detalhe:
            mensagem = f"{mensagem}\n{detalhe}"
    sys.exit(mensagem)


def local(sql):
    if isinstance(sql, str):
        sql = sql.encode("utf-8")
    prefixo = b"SET SESSION sql_mode=''; SET NAMES utf8mb4; SET FOREIGN_KEY_CHECKS=0;\n"
    resultado = executar(
        ["docker", "compose", "exec", "-T", "db", "mariadb", "-ufixar", "-pfixar", "-N", "-B", "fixar"],
        entrada=prefixo + sql,
        cwd=RAIZ,
    )
    if resultado.returncode != 0:
        falhar("Falha ao gravar no banco local.", resultado)
    return resultado.stdout.decode("utf-8", "replace")


def linhas(sql):
    texto = local(sql).strip()
    if not texto:
        return []
    return [linha.split("\t") for linha in texto.splitlines() if linha != ""]


def celula(sql):
    resultado = linhas(sql)
    if not resultado or not resultado[0]:
        return ""
    return resultado[0][0]


def ler_env():
    caminho = os.path.join(RAIZ, ".env")
    if not os.path.isfile(caminho):
        sys.exit("Falta o .env com a credencial de leitura da produção. O modelo está em .env.example.")
    valores = {}
    with open(caminho, encoding="utf-8") as arquivo:
        for linha in arquivo:
            linha = linha.strip()
            if not linha or linha.startswith("#") or "=" not in linha:
                continue
            chave, valor = linha.split("=", 1)
            valores[chave.strip()] = valor.strip().strip('"').strip("'")
    faltando = [
        chave
        for chave in ("PRODUCAO_HOST", "PRODUCAO_BANCO", "PRODUCAO_USUARIO", "PRODUCAO_SENHA")
        if not valores.get(chave)
    ]
    if faltando:
        sys.exit("O .env não tem: " + ", ".join(faltando))
    if not re.fullmatch(r"[A-Za-z0-9_.-]+", valores["PRODUCAO_BANCO"]):
        sys.exit("PRODUCAO_BANCO inválido")
    valores.setdefault("PRODUCAO_PORT", "3306")
    if not re.fullmatch(r"\d+", valores["PRODUCAO_PORT"]):
        sys.exit("PRODUCAO_PORT inválida")
    return valores


def producao(argumentos, entrada=None):
    binario, *resto = argumentos
    comando = [
        "docker",
        "run",
        "--rm",
        "--entrypoint",
        binario,
        "--add-host=host.docker.internal:host-gateway",
        "-e",
        "MYSQL_PWD=" + credencial["PRODUCAO_SENHA"],
        IMAGEM_LEITURA,
        *resto,
    ]
    return executar(comando, entrada=entrada)


def conexao():
    return [
        "--host",
        credencial["PRODUCAO_HOST"],
        "--port",
        credencial["PRODUCAO_PORT"],
        "--user",
        credencial["PRODUCAO_USUARIO"],
        "--default-character-set=utf8mb4",
    ]


def consultar_producao(sql):
    tentativas = (
        "SET SESSION tx_read_only=1; " + sql,
        "SET SESSION transaction_read_only=1; " + sql,
        sql,
    )
    resultado = None
    for leitura in tentativas:
        resultado = producao(
            [
                "mysql",
                *conexao(),
                "--batch",
                "--skip-column-names",
                "--database",
                credencial["PRODUCAO_BANCO"],
                "--execute",
                leitura,
            ]
        )
        if resultado.returncode == 0:
            return resultado.stdout.decode("utf-8", "replace")
        detalhe = sem_senha((resultado.stderr or b"").decode("utf-8", "replace")).lower()
        if "transaction_read_only" not in detalhe and "read_only" not in detalhe:
            break
    falhar("A leitura da produção falhou.", resultado)


def privilegio_escreve(texto):
    for linha in texto.splitlines():
        if "GRANT " not in linha.upper():
            continue
        miolo = linha.upper().split(" ON ", 1)[0]
        miolo = miolo.split("GRANT ", 1)[1]
        if "ALL" in miolo.split(","):
            return True
        if "ALL PRIVILEGES" in miolo:
            return True
        if "WITH GRANT OPTION" in linha.upper():
            return True
        for parte in miolo.split(","):
            nome = parte.strip().strip("`")
            if nome in ESCRITA or nome.startswith(("CREATE", "ALTER", "DROP")):
                return True
    return False


def conferir_somente_leitura():
    resultado = producao(
        [
            "mysql",
            *conexao(),
            "--batch",
            "--skip-column-names",
            "--execute",
            "SHOW GRANTS",
        ]
    )
    if resultado.returncode != 0:
        falhar("Não foi possível ler os privilégios da produção.", resultado)
    grants = resultado.stdout.decode("utf-8", "replace")
    if privilegio_escreve(grants):
        sys.exit("A credencial da produção não é somente leitura.")


def ajuda_dump():
    if not hasattr(ajuda_dump, "texto"):
        resultado = producao(["mysqldump", "--help"])
        ajuda_dump.texto = (resultado.stdout + resultado.stderr).decode("utf-8", "replace")
    return ajuda_dump.texto


def dump(extra, sessao_somente_leitura=True):
    ajuda = ajuda_dump()
    argumentos = ["mysqldump", *conexao()]
    for flag in (
        "--single-transaction",
        "--skip-lock-tables",
        "--skip-add-locks",
        "--no-tablespaces",
        "--column-statistics=0",
        "--set-gtid-purged=OFF",
        "--skip-comments",
    ):
        if flag.split("=", 1)[0] in ajuda or flag in ajuda:
            argumentos.append(flag)
    if "--single-transaction" not in argumentos or "--skip-lock-tables" not in argumentos:
        sys.exit("O cliente de dump não oferece leitura sem trava.")
    if sessao_somente_leitura and "--init-command" in ajuda:
        argumentos.append("--init-command=SET SESSION tx_read_only=1")
    argumentos.extend(extra)
    resultado = producao(argumentos)
    if resultado.returncode != 0 and sessao_somente_leitura:
        detalhe = sem_senha((resultado.stderr or b"").decode("utf-8", "replace")).lower()
        if "read_only" in detalhe or "init-command" in detalhe:
            return dump(extra, sessao_somente_leitura=False)
    if resultado.returncode != 0 and "--routines" in extra:
        sem_rotina = [item for item in extra if item not in {"--routines", "--triggers"}]
        return dump(["--skip-triggers", *sem_rotina], sessao_somente_leitura=sessao_somente_leitura)
    if resultado.returncode != 0:
        falhar("Não foi possível ler a estrutura da produção.", resultado)
    conteudo = resultado.stdout
    if not conteudo.strip():
        sys.exit("A leitura da estrutura veio vazia.")
    conteudo = re.sub(br"DEFINER=`[^`]*`@`[^`]*`\s*", b"", conteudo)
    conteudo = re.sub(br"^SET @@(GLOBAL|SESSION)\.GTID_PURGED=.*?;\r?\n", b"", conteudo, flags=re.M)
    return conteudo


def carregar_estrutura():
    print("Lendo a estrutura da produção, sem gravar e sem travar quem opera.", flush=True)
    extra = ["--no-data", "--routines", "--triggers", credencial["PRODUCAO_BANCO"]]
    local(dump(extra))
    print("Estrutura carregada no banco local.", flush=True)


def escolher_empresa():
    texto = consultar_producao(
        "SELECT C004_Id,"
        " (SELECT COUNT(*) FROM T003 WHERE T003_C004_Id=C004.C004_Id),"
        " (SELECT COUNT(*) FROM T005 WHERE T005_C004_Id=C004.C004_Id),"
        " (SELECT COUNT(*) FROM T007 WHERE T007_C004_Id=C004.C004_Id AND T007_Flag_Entrada_Saida='S'),"
        " REPLACE(REPLACE(C004_Nome_Abreviado, CHAR(9), ' '), CHAR(10), ' ')"
        " FROM C004 WHERE C004_Flag_Ativo='S'"
    )
    melhor = None
    for linha in texto.splitlines():
        if not linha.strip():
            continue
        partes = linha.split("\t")
        if len(partes) < 5:
            continue
        identificador, orcamentos, pedidos, notas, nome = partes[:5]
        contagens = (int(orcamentos), int(pedidos), int(notas))
        chave = (min(contagens), sum(contagens))
        if melhor is None or chave > melhor[0]:
            melhor = (chave, identificador, nome, contagens)
    if melhor is None or melhor[0][0] == 0:
        sys.exit("Nenhuma empresa ativa concentra orçamento, pedido e nota fiscal de saída.")
    return melhor[1], melhor[2]


def copiar_tabela(tabela, condicao):
    if celula(
        "SELECT COUNT(*) FROM information_schema.tables "
        f"WHERE table_schema='fixar' AND LOWER(table_name)=LOWER('{tabela}')"
    ) == "0":
        return
    conteudo = dump(
        [
            "--no-create-info",
            "--complete-insert",
            "--hex-blob",
            f"--where={condicao}",
            credencial["PRODUCAO_BANCO"],
            tabela,
        ]
    )
    local(conteudo)


def copiar_empresa():
    print("Escolhendo a empresa que concentra orçamento, pedido e nota de saída.", flush=True)
    identificador, nome = escolher_empresa()
    if not re.fullmatch(r"\d+", identificador):
        sys.exit("A empresa escolhida não tem identificador numérico.")
    print(f"Copiando a empresa {nome} ({identificador}), sem linhas de negócio.", flush=True)
    copiar_tabela("C004", f"C004_Id={identificador}")
    copiar_tabela("C020", f"C020_C004_Id={identificador}")
    copiar_tabela("C031", f"C031_C004_Id={identificador}")
    completar_empresa()
    return identificador


def literal(valor):
    if valor is None:
        return "NULL"
    texto = str(valor).replace("\\", "\\\\").replace("'", "''")
    return "'" + texto + "'"


def colunas(tabela):
    texto = local(
        "SELECT COLUMN_NAME, DATA_TYPE, COLUMN_TYPE, IS_NULLABLE, "
        "IF(COLUMN_DEFAULT IS NULL, '<<nulo>>', COLUMN_DEFAULT), EXTRA "
        "FROM information_schema.COLUMNS "
        f"WHERE TABLE_SCHEMA='fixar' AND LOWER(TABLE_NAME)=LOWER('{tabela}') "
        "ORDER BY ORDINAL_POSITION"
    )
    definicao = []
    for linha in texto.splitlines():
        if not linha:
            continue
        campos = linha.split("\t")
        while len(campos) < 6:
            campos.append("")
        definicao.append(campos[:6])
    return definicao


def valor_obrigatorio(tipo, tipo_completo):
    if tipo in {"int", "bigint", "tinyint", "smallint", "mediumint", "decimal", "float", "double", "bit"}:
        return "0"
    if tipo == "date":
        return "1000-01-01"
    if tipo == "datetime" or tipo == "timestamp":
        return "1000-01-01 00:00:00"
    if tipo == "time":
        return "00:00:00"
    if tipo == "enum":
        achado = re.search(r"'([^']*)'", tipo_completo)
        return achado.group(1) if achado else ""
    return ""


def inserir(tabela, explicitos):
    campos = []
    valores = []
    for nome, tipo, tipo_completo, anulavel, padrao, extra in colunas(tabela):
        if "auto_increment" in extra.lower():
            continue
        if nome in explicitos:
            campos.append(nome)
            valores.append(literal(explicitos[nome]))
            continue
        if padrao != "<<nulo>>":
            continue
        if anulavel == "YES":
            continue
        campos.append(nome)
        valores.append(literal(valor_obrigatorio(tipo, tipo_completo)))
    if not campos:
        sys.exit(f"Não há colunas para inserir em {tabela}.")
    saida = local(
        f"INSERT INTO {tabela} ({', '.join(campos)}) VALUES ({', '.join(valores)});\n"
        "SELECT LAST_INSERT_ID();"
    )
    identificador = ""
    for linha in saida.splitlines():
        if linha.strip():
            identificador = linha.split("\t")[0]
    if not identificador:
        sys.exit(f"Não foi possível obter o identificador de {tabela}.")
    return identificador


# Itens do menu.php que o ciclo deste marco abre. Os demais ficam sem permissão.
TELAS_DO_CICLO = (
    1,  # Cliente/Fornecedor
    5,  # Produtos
    14,  # Orçamentos
    15,  # Pedidos
    16,  # Notas Fiscais de saída
    20,  # Expedição
    21,  # Nota fiscal de entrada
    110,  # Estoque
    35,  # Contas a receber
    36,  # Contas a pagar
    37,  # Caixa e bancos
    38,  # Boletos
    41,  # CFOP
    42,  # NCM
    43,  # ICMS
    57,  # Empresas
    84,  # Usuários/Perfis
)


def garantir_rotina(nome, marca, retorno):
    # O dump da produção cai para tabelas quando a conta de leitura não
    # exporta rotina. A tabela é latin1; o retorno precisa da mesma colação,
    # senão a grade não roda no MariaDB.
    # Origem: sistema-legado/scripts/sql/<nome>.sql
    colacao = celula(
        "SELECT COLLATION_NAME FROM information_schema.routines "
        f"WHERE routine_schema='fixar' AND routine_name='{nome}'"
    )
    if colacao == "latin1_swedish_ci":
        return
    caminho = os.path.join(RAIZ, "sistema-legado", "scripts", "sql", f"{nome}.sql")
    if not os.path.isfile(caminho):
        sys.exit(f"não achei a rotina {nome}")
    with open(caminho, "rb") as arquivo:
        corpo = arquivo.read().strip()
    if corpo.endswith(b";"):
        corpo = corpo[:-1]
    if marca not in corpo:
        sys.exit(f"não achei o retorno de {nome}")
    corpo = corpo.replace(marca, retorno, 1)
    sql = (
        b"DROP FUNCTION IF EXISTS `" + nome.encode() + b"`;\n"
        b"DELIMITER $$\n" + corpo + b"$$\nDELIMITER ;\n"
    )
    local(sql)
    print(f"Rotina {nome} disponível na base local.")


def garantir_status_do_pedido():
    # A listagem de orçamento e de pedido chama esta função.
    garantir_rotina(
        "T005_Status_Pedido",
        b"RETURNS varchar(30)",
        b"RETURNS varchar(30) CHARSET latin1 COLLATE latin1_swedish_ci",
    )


def garantir_prazos():
    # A separação da expedição chama esta função.
    garantir_rotina(
        "T005_Prazos",
        b"RETURNS varchar(255)",
        b"RETURNS varchar(255) CHARSET latin1 COLLATE latin1_swedish_ci",
    )


def liberar_telas():
    if celula(
        "SELECT COUNT(*) FROM information_schema.tables "
        "WHERE table_schema='fixar' AND LOWER(table_name)=LOWER('C030')"
    ) == "0":
        sys.exit("A tabela de permissão não existe na base local.")
    perfil = celula(
        "SELECT C021_Id FROM C021 WHERE C021_Nome_Perfil='Local' ORDER BY C021_Id LIMIT 1"
    )
    if not perfil:
        sys.exit("O perfil local não existe.")
    for tela in TELAS_DO_CICLO:
        if celula(
            "SELECT COUNT(*) FROM C030 "
            f"WHERE C030_C021_Id={perfil} AND c030_C029_Id={tela}"
        ) != "0":
            local(
                "UPDATE C030 SET C030_Acesso_Liberado='S' "
                f"WHERE C030_C021_Id={perfil} AND c030_C029_Id={tela}"
            )
            continue
        inserir(
            "C030",
            {
                "C030_C021_Id": perfil,
                "c030_C029_Id": str(tela),
                "C030_Id_Perfil": perfil,
                "C030_Acesso_Liberado": "S",
            },
        )
    print(f"Perfil {perfil} abre as telas do ciclo.")


def semear_acesso(empresa):
    perfil = inserir("C021", {"C021_Nome_Perfil": "Local"})
    senha = base64.b64encode(SENHA_LOCAL.encode("utf-8")).decode("ascii")
    usuario = inserir(
        "C007",
        {
            "C007_Login": USUARIO_LOCAL,
            "C007_Senha": senha,
            "C007_Nome": "Desenvolvedor",
            "C007_Primeiro_Nome": "Fixar",
            "C007_Ativo": "S",
            "C007_Flag_Ausente": "N",
            "C007_Dispositivo_Acesso": "0",
            "C007_D024_Id": "0",
            "C007_Acesso_Hora_Inicio": "00:00:00",
            "C007_Acesso_Hora_Fim": "00:00:00",
        },
    )
    if usuario == "63":
        local("UPDATE C007 SET C007_Id=1000 WHERE C007_Id=63")
        usuario = "1000"
    inserir(
        "C019",
        {
            "C019_C007_Id": usuario,
            "C019_C004_Id": empresa,
            "C019_C021_Id": perfil,
            "C019_Ordem_Empresa": "0",
        },
    )
    print(f"Usuário local {USUARIO_LOCAL} vinculado à empresa {empresa}.")


def alinhar_nomes():
    texto = local(
        "SELECT table_name FROM information_schema.tables "
        "WHERE table_schema='fixar' AND table_type='BASE TABLE' "
        "AND table_name <> UPPER(table_name)"
    )
    partes = []
    for nome in texto.splitlines():
        nome = nome.strip()
        if nome:
            partes.append("`" + nome + "` TO `" + nome.upper() + "`")
    if not partes:
        return
    print(f"Ajustando {len(partes)} tabelas para o nome que o legado consulta.", flush=True)
    local("RENAME TABLE " + ", ".join(partes))


def estrutura_pronta():
    return celula(
        "SELECT COUNT(*) FROM information_schema.tables "
        "WHERE table_schema='fixar' AND LOWER(table_name)=LOWER('C004')"
    ) != "0"


def empresa_copiada():
    if not estrutura_pronta():
        return False
    return celula("SELECT COUNT(*) FROM C004") != "0"


def usuario_pronto():
    if celula(
        "SELECT COUNT(*) FROM information_schema.tables "
        "WHERE table_schema='fixar' AND LOWER(table_name)=LOWER('C007')"
    ) == "0":
        return False
    return celula(f"SELECT COUNT(*) FROM C007 WHERE C007_Login='{USUARIO_LOCAL}'") != "0"


def empresa_local():
    return celula("SELECT C004_Id FROM C004 ORDER BY C004_Id LIMIT 1")


def arquivo_exportado(tabela):
    return os.path.join(RAIZ, "base", f"{tabela}.json")


def exportacao_presente():
    return all(os.path.isfile(arquivo_exportado(tabela)) for tabela in ("C004", "C020", "C031"))


def valor_exportado(valor):
    if isinstance(valor, dict):
        if not valor:
            return None
        if all(str(chave).isdigit() for chave in valor):
            bruto = bytes(int(valor[str(indice)]) & 0xFF for indice in range(len(valor)))
            try:
                return bruto.decode("utf-8")
            except UnicodeDecodeError:
                return bruto.decode("latin1")
        sys.exit("Um valor exportado não é texto, nulo nem sequência de bytes.")
    if isinstance(valor, bool):
        return int(valor)
    return valor


def semear_tabela(tabela):
    if celula(
        "SELECT COUNT(*) FROM information_schema.tables "
        f"WHERE table_schema='fixar' AND LOWER(table_name)=LOWER('{tabela}')"
    ) == "0":
        sys.exit(f"A tabela {tabela} não existe na base local.")
    caminho = arquivo_exportado(tabela)
    registros = json.loads(open(caminho, encoding="utf-8").read())
    if not isinstance(registros, list) or not registros:
        sys.exit(f"{tabela}.json não traz linhas.")
    if celula(f"SELECT COUNT(*) FROM {tabela}") == str(len(registros)):
        print(f"{tabela} já tem {len(registros)} linhas.", flush=True)
        return
    local(f"DELETE FROM {tabela}")
    definicao = colunas(tabela)
    existentes = {nome for nome, *_resto in definicao}
    comandos = []
    for registro in registros:
        if not isinstance(registro, dict):
            sys.exit(f"{tabela}.json tem uma linha que não é um registro.")
        presentes = {
            chave: valor_exportado(valor)
            for chave, valor in registro.items()
            if chave in existentes
        }
        campos = []
        valores = []
        for nome, tipo, tipo_completo, anulavel, padrao, extra in definicao:
            if nome in presentes:
                valor = presentes[nome]
                if valor is None and anulavel != "YES":
                    valor = ""
                campos.append(nome)
                valores.append(literal(valor))
                continue
            if "auto_increment" in extra.lower():
                continue
            if padrao != "<<nulo>>" or anulavel == "YES":
                continue
            campos.append(nome)
            valores.append(literal(valor_obrigatorio(tipo, tipo_completo)))
        if not campos:
            sys.exit(f"Não há colunas para inserir em {tabela}.")
        comandos.append(
            f"INSERT INTO {tabela} ({', '.join(campos)}) VALUES ({', '.join(valores)})"
        )
    local("START TRANSACTION;\n" + ";\n".join(comandos) + ";\nCOMMIT;\n")
    print(f"{tabela}: {len(registros)} linhas.", flush=True)


def completar_empresa():
    for coluna, valor in (
        ("C004_Banco_Configuracao", "fixar"),
        ("C004_Caminho_Sistema", "fixar"),
        ("C004_Flag_Ativo", "S"),
    ):
        if celula(
            "SELECT COUNT(*) FROM information_schema.columns "
            f"WHERE table_schema='fixar' AND LOWER(table_name)=LOWER('C004') AND column_name='{coluna}'"
        ) == "0":
            continue
        local(
            "UPDATE C004 SET "
            + coluna
            + "="
            + literal(valor)
            + " WHERE "
            + coluna
            + " IS NULL OR "
            + coluna
            + "=''"
        )


def carregar_empresa_exportada():
    print("Carregando a empresa a partir dos arquivos exportados.", flush=True)
    for tabela in ("C004", "C020", "C031"):
        semear_tabela(tabela)
    completar_empresa()


def main():
    global credencial
    if not estrutura_pronta():
        credencial = ler_env()
        conferir_somente_leitura()
        carregar_estrutura()
    alinhar_nomes()
    if exportacao_presente():
        carregar_empresa_exportada()
    elif not empresa_copiada():
        credencial = ler_env()
        conferir_somente_leitura()
        copiar_empresa()
    if not usuario_pronto():
        semear_acesso(empresa_local())
    else:
        print("O acesso local já está semeado.")
    garantir_status_do_pedido()
    garantir_prazos()
    liberar_telas()


if __name__ == "__main__":
    main()
