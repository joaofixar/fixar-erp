"""Exercita a consulta do executor em SQLite com títulos fictícios, sem MySQL."""
import json
import pathlib
import re
import sqlite3
import subprocess

root = pathlib.Path(__file__).resolve().parent
query = json.loads(subprocess.check_output(['php', str(root / 'controle-carteira-bancos.php'), '--query']))
# Equivalente SQLite da única operação de data específica do MySQL.
sql = re.sub(r'DATE_ADD\(\?, INTERVAL 1 DAY\)', "DATE(?, '+1 day')", query['sql'])
db = sqlite3.connect(':memory:')
db.row_factory = sqlite3.Row
db.executescript('''
CREATE TABLE D027(D027_Id INTEGER, D027_Portador TEXT, D027_Flag_Boleto TEXT);
CREATE TABLE T002(T002_Id INTEGER, T002_D027_Id INTEGER, T002_C004_Id INTEGER, T002_Data_Vencimento TEXT,
 T002_Valor_Saldo REAL, T002_Flag_Cancelada TEXT, T002_Data_Recebimento TEXT, T002_T002_Id_Agrupado INTEGER, T002_Flag_Status TEXT);
INSERT INTO D027 VALUES(1,'BOLETO ITAU CC 18626-7','S'),(2,'DEPOSITO/TRANSFERENCIA/PIX','N');
''')
base = dict(portador=1, empresa=1, data='2026-11-04', valor=100, cancelada='N', recebimento='0000-00-00', agrupado=0, status='1')


def title(id, **changes):
    item = dict(base, **changes)
    db.execute('INSERT INTO T002 VALUES(?,?,?,?,?,?,?,?,?)', [id] + [item[k] for k in ['portador', 'empresa', 'data', 'valor', 'cancelada', 'recebimento', 'agrupado', 'status']])


title(1)
title(2, data='2026-11-05', valor=25.55)  # saldo remanescente de baixa parcial
title(3, data='2045-11-30', valor=300)
title(4, data='2027-01-01', valor=200)
title(5, cancelada='S')
title(6, recebimento='2026-11-03')
title(7, agrupado=99)
title(8, status='6')
title(9, valor=0)
title(10, valor=-100)
title(11, empresa=5)
title(12, data='2026-11-03')
title(13, portador=2, cancelada=None, recebimento=None, agrupado=None, status=None, valor=10)
title(14, empresa=2, valor=200)
title(15, empresa=4, valor=400)
title(16, empresa=9, valor=900)
rows = [dict(row) for row in db.execute(sql, query['params'])]
itau = [row for row in rows if row['portador'].startswith('BOLETO ITAU')]
assert len(itau) == 3
assert sum(row['valor'] for row in itau) == 1325.55
future = next(row for row in itau if row['dataCarteira'] == '2026-12-19')
assert future['valor'] == 500 and future['quantidade'] == 2, 'Toda a carteira além do horizonte permanece na abertura, em um único grupo'
assert sum(row['quantidade'] for row in rows) == 8
assert next(row for row in rows if row['portador'].startswith('DEPOSITO'))['valor'] == 10
for scope, total in [('atual', 635.55), ('2', 200), ('4', 400), ('5', 100), ('9', 0)]:
    params = [scope if value == 'todas' else value for value in query['params']]
    selected = [dict(row) for row in db.execute(sql, params)]
    assert sum(row['valor'] for row in selected) == total, (scope, selected)
print('OK: SQL real consolida o grupo 1/2/4/5, filtra empresa atual ou específica, exclui outra empresa e mantém as regras de títulos e carteira além do horizonte.')
