"""Valida as consultas reais com tabelas fictícias em SQLite na memória."""
import json
from pathlib import Path
import sqlite3
import subprocess

queries = json.loads(subprocess.check_output([
    'php', str(Path(__file__).with_name('agenda.php')), '--fixture'
], text=True))


def database():
    db = sqlite3.connect(':memory:')
    db.executescript('''
        CREATE TABLE T002 (T002_Id, T002_D024_Id, T002_C004_Id, T002_Nome_Cliente, T002_Flag_Cancelada);
        CREATE TABLE D085 (D085_Id, D085_T002_Id);
        CREATE TABLE C007 (C007_Id, C007_Ativo, C007_Usuario_Suporte);
        CREATE TABLE C004 (C004_Id, C004_Flag_Ativo);
        CREATE TABLE C019 (C019_C007_Id, C019_C004_Id, C019_C021_Id);
        CREATE TABLE C030 (C030_C021_Id, C030_C029_Id, C030_Acesso_Liberado);
        INSERT INTO T002 VALUES (123, 45, 5, 'Cliente d''Exemplo', 'N');
        INSERT INTO D085 VALUES (89, 123), (90, 123), (999, 124);
        INSERT INTO C007 VALUES (7, 'S', 'N'), (63, 'S', 'N');
        INSERT INTO C004 VALUES (1, 'S'), (5, 'S');
        INSERT INTO C019 VALUES (7, 1, 10), (7, 5, 20);
        INSERT INTO C030 VALUES (10, 35, 'S'), (20, 35, 'S');
    ''')
    return db


def execute(db, index, values=None):
    query = queries[index]
    return db.execute(query['sql'], values or query['valores']).fetchall()


with database() as db:
    assert execute(db, 0) == [(123, 45, 5)]
    assert execute(db, 0, ['atual', 1, 'atual', 123, "Cliente d'Exemplo"]) == []
    assert execute(db, 0, ['atual', 5, 'atual', 123, "Cliente d'Exemplo"]) == [(123, 45, 5)]
    assert execute(db, 0, ['todas', 1, 'todas', 123, 'Outro cliente']) == []
    assert execute(db, 1) == [(123, 45, 5, 90)]

for change in [
    "UPDATE C007 SET C007_Ativo='N' WHERE C007_Id=7",
    "UPDATE C004 SET C004_Flag_Ativo='N' WHERE C004_Id=5",
    "UPDATE T002 SET T002_Flag_Cancelada='S'",
    "UPDATE T002 SET T002_D024_Id=99",
    "UPDATE T002 SET T002_C004_Id=4",
    "DELETE FROM C019 WHERE C019_C004_Id=1",
    "DELETE FROM C019 WHERE C019_C004_Id=5",
    "UPDATE C030 SET C030_Acesso_Liberado='N' WHERE C030_C021_Id=10",
    "UPDATE C030 SET C030_Acesso_Liberado='N' WHERE C030_C021_Id=20",
    "UPDATE C030 SET C030_C029_Id=104",
]:
    with database() as db:
        db.execute(change)
        assert execute(db, 1) == [], change

with database() as db:
    db.execute('DELETE FROM C019')
    assert execute(db, 1, [63, 123, 45, 5, 0, 63, 5, 63, 1]) == [(123, 45, 5, 90)]
    assert execute(db, 1, [7, 123, 45, 5, 1, 7, 5, 7, 1]) == [(123, 45, 5, 90)]
    db.execute("UPDATE C007 SET C007_Usuario_Suporte='S' WHERE C007_Id=7")
    assert execute(db, 1) == [(123, 45, 5, 90)]

print('OK: SQL com empresa atual/todas, cliente, vínculo, última agenda e permissões do ERP.')
