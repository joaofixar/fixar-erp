"""SQL real do servico em SQLite :memory:, sem configuracao ou MySQL do ERP."""
import json
from pathlib import Path
import sqlite3
import subprocess

HERE = Path(__file__).resolve().parent
CHECKS = 0


def php(*args, payload=None):
    result = subprocess.run(["php", str(HERE / "composicao-retorno-extrato.php"), *args],
                            input=payload, text=True, capture_output=True, check=True)
    return json.loads(result.stdout)


def check(condition, label):
    global CHECKS
    assert condition, label
    CHECKS += 1


sql_header, sql_credits, sql_events = php("--sql")
db = sqlite3.connect(":memory:")
db.row_factory = sqlite3.Row
db.executescript("""
CREATE TABLE T215(T215_Id INTEGER PRIMARY KEY,T215_C004_Id INTEGER,T215_D007_Id INTEGER);
CREATE TABLE D007(D007_Id INTEGER PRIMARY KEY,D007_Nome_Banco TEXT);
CREATE TABLE T216(T216_Id INTEGER PRIMARY KEY,T216_T215_Id INTEGER,T216_Data TEXT,T216_Valor NUMERIC,T216_Flag_Excluido TEXT);
CREATE TABLE T037(T037_Id INTEGER PRIMARY KEY,T037_C004_Id INTEGER,T037_D007_Id INTEGER,T037_Codigo_Banco TEXT,T037_Nome_Arquivo TEXT);
CREATE TABLE T038(T038_Id INTEGER PRIMARY KEY,T038_T037_Id INTEGER,T038_T002_Id INTEGER,T038_Data_Recebimento TEXT,
 T038_Data_Credito TEXT,T038_Valor_Recebido NUMERIC,T038_Codigo_Natureza_Recebimento TEXT);
CREATE TABLE T002(T002_Id INTEGER PRIMARY KEY,T002_C004_Id INTEGER,T002_D024_Id INTEGER,T002_Numero_Duplicata TEXT);
CREATE TABLE C004(C004_Id INTEGER PRIMARY KEY,C004_Nome_Abreviado TEXT);
CREATE TABLE D024(D024_Id INTEGER PRIMARY KEY,D024_Nome_Empresa TEXT);
INSERT INTO T215 VALUES(10,7,20),(11,8,20);
INSERT INTO D007 VALUES(20,'CONTA TESTE');
INSERT INTO T216 VALUES(101,10,'2026-01-15',100.00,'N'),(102,10,'2026-01-15',-100.00,'N'),
 (103,10,'2026-01-15',100.00,'S'),(104,11,'2026-01-15',100.00,'N');
INSERT INTO T037 VALUES(500,7,20,'341','TESTE.RET');
INSERT INTO T038 VALUES(1,500,1001,'2026-01-14','2026-01-15',40.00,'06'),
 (2,500,1002,'2026-01-14','2026-01-15',60.00,'06');
INSERT INTO T002 VALUES(1001,7,1,'TESTE-1'),(1002,9,2,'TESTE-2');
INSERT INTO C004 VALUES(7,'EMPRESA TESTE'),(9,'FILIAL TESTE');
INSERT INTO D024 VALUES(1,'CLIENTE 1'),(2,'CLIENTE 2');
""")


def eventos():
    result = [dict(row) for row in db.execute(sql_events)]
    for row in result:
        # DECIMAL do MySQL chega como texto; SQLite usa afinidade numerica.
        row["recebido_no_retorno"] = str(row["recebido_no_retorno"])
    return result


header = [dict(r) for r in db.execute(sql_header)]
credits = [dict(r) for r in db.execute(sql_credits)]
check(len(header) == 1 and header[0]["empresa_id"] == 7, "Autoriza somente o extrato da empresa atual")
check([r["movimento_id"] for r in credits] == [101], "Le creditos ativos do extrato, sem debitos/excluidos/outro extrato")
events = eventos()
check(len(events) == 2 and events[1]["empresa_titulo"] == "FILIAL TESTE", "Preserva os membros do retorno autorizado, inclusive filial")
composition = php("--compor-json", payload=json.dumps({"movimentos": [{"movimento_id": 101, "valor_movimento": "100.00", "data_movimento": "2026-01-15"}], "eventos": events}))
check(composition["101"]["total"] == 10000 and len(composition["101"]["membros"]) == 2, "SQL alimenta composicao PHP exata")
check("T019" not in sql_events and "T002_Valor" not in sql_events, "Sem busca de baixas ou dependencia do valor nominal")

for field, value in [("T037_C004_Id", 8), ("T037_D007_Id", 21)]:
    db.execute(f"UPDATE T037 SET {field}=?", (value,))
    check(not eventos(), "Outra empresa/conta do retorno e recusada: " + field)
    db.execute("UPDATE T037 SET T037_C004_Id=7,T037_D007_Id=20")
db.execute("UPDATE T038 SET T038_Data_Credito='2026-01-14'")
check(not eventos(), "Pagamento no dia anterior nao substitui a data do credito")
db.execute("UPDATE T038 SET T038_Data_Credito='2026-01-15'")

for banco, ocorrencias in [("001", [5, 6, 15]), ("104", [21, 22, 23, 25]), ("237", [6, 15, 17]),
                           ("033", [6, 7, 8, 17]), ("341", [6, 8])]:
    db.execute("UPDATE T037 SET T037_Codigo_Banco=?", (banco,))
    for occurrence in ocorrencias:
        db.execute("UPDATE T038 SET T038_Codigo_Natureza_Recebimento=?", (str(occurrence).zfill(2),))
        check(len(eventos()) == 2, f"Ocorrencia financeira {banco}/{occurrence}")
    for occurrence in [2, 3, 9, 99]:
        db.execute("UPDATE T038 SET T038_Codigo_Natureza_Recebimento=?", (str(occurrence),))
        check(not eventos(), f"Ocorrencia administrativa {banco}/{occurrence} excluida")
db.execute("UPDATE T037 SET T037_Codigo_Banco='033'")
db.execute("UPDATE T038 SET T038_Codigo_Natureza_Recebimento='15'")
check(not eventos(), "Santander enviado para cartorio nao e liquidacao")
db.execute("UPDATE T037 SET T037_Codigo_Banco='999'")
db.execute("UPDATE T038 SET T038_Codigo_Natureza_Recebimento='6'")
check(not eventos(), "Banco sem regra nao gera correspondencia")
db.execute("UPDATE T037 SET T037_Codigo_Banco='341'")
for value in [0, -40]:
    db.execute("UPDATE T038 SET T038_Valor_Recebido=?", (value,))
    check(not eventos(), "Valor nao positivo nao entra na composicao")
db.execute("UPDATE T038 SET T038_Valor_Recebido=40 WHERE T038_Id=1")
db.execute("UPDATE T038 SET T038_Valor_Recebido=60,T038_T002_Id=0 WHERE T038_Id=2")
events = eventos()
check(len(events) == 2 and events[1]["titulo_id"] is None, "LEFT JOIN mantem titulo nao identificado para impedir grupo parcial")
composition = php("--compor-json", payload=json.dumps({"movimentos": [{"movimento_id": 101, "valor_movimento": "100.00", "data_movimento": "2026-01-15"}], "eventos": events}))
check(not composition, "Composicao com titulo desconhecido e rejeitada")
check("LIMIT 501" in sql_credits and "LIMIT 5001" in sql_events, "Consultas em lote limitadas")
print(f"OK: {CHECKS} verificações de SQL e composição em SQLite, sem MySQL real.")
