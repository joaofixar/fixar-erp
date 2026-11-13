"""Executa a consulta real da listagem em SQLite com parcelas fictícias."""
import json
import pathlib
import sqlite3
import subprocess

root = pathlib.Path(__file__).resolve().parent
payload = json.loads(subprocess.check_output(['php', str(root / 'endividamento.php'), '--queries']))
db = sqlite3.connect(':memory:')
db.row_factory = sqlite3.Row
db.create_function('DATE_FORMAT', 2, lambda value, pattern: value[:7] if value else None)
db.execute('CREATE TABLE T015(T015_Id, T015_C004_Id, T015_Data_Vencimento, T015_Valor_Pago, T015_Valor_Saldo, T015_Observacao)')
for row in payload['rows']:
    db.execute('INSERT INTO T015 VALUES(?,?,?,?,?,?)', (row['id'], 1, row['dataVencimento'], row['valorPago'], row['valorAberto'], row['conta']))
db.execute("INSERT INTO T015 VALUES(999,5,'2026-11-15',0,99999,'Capital Giro')")
count, page = payload['queries']
totals = dict(db.execute(count['sql'], count['values']).fetchone())
rows = [dict(row) for row in db.execute(page['sql'], page['values'])]
assert totals['totalLinhas'] == 52
assert totals['total2'] == 520
assert len(rows) == 50
assert all(row['dataVencimento'].startswith('2026-11') and row['conta'] == 'Capital Giro' for row in rows)
assert not any(row['id'] == 999 for row in rows)
params = page['values'][:-1] + ["Capital Giro' OR 1=1"]
assert not list(db.execute(page['sql'], params)), 'Categoria injetada é tratada como valor literal'
db.execute("INSERT INTO T015 VALUES(1000,1,'2026-11-25',0,7.5,'Capital Giro')")
partial_month = ['2026-11-20' if value == '2026-01-01' else value for value in page['values']]
filtered = [dict(row) for row in db.execute(page['sql'], partial_month)]
assert [row['id'] for row in filtered] == [1000], 'Clicar no mês não expande o intervalo inicial dos filtros'
print('OK: consulta real retorna somente parcelas do mês/categoria/empresa, preserva total acima da página e bloqueia categoria injetada.')
