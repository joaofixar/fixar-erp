"""Exercita o SELECT configurado em SQLite com fixtures, sem banco de cliente."""
import json
import pathlib
import sqlite3
import subprocess

fixture = pathlib.Path(__file__).resolve().parent / 'fixtures/previsao-caixa.php'
indicator = json.loads(subprocess.check_output(['php', '-r', 'echo json_encode(require $argv[1]);', str(fixture)]))
sql = '\n'.join(indicator['previsaoCaixa']['sqlPedidos'])
db = sqlite3.connect(':memory:')
db.row_factory = sqlite3.Row
db.create_function('GREATEST', -1, max)
db.executescript('''
CREATE TABLE T005(T005_Id, T005_C004_Id, T005_Data_Emissao, T005_Data_Entrega, T005_Valor_Total REAL, T005_D024_Id, T005_Flag_Status, T005_Flag_Devolucao, T005_D006_Id);
CREATE TABLE D006(D006_Id, D006_Flag_Gera_Contas);
CREATE TABLE D024(D024_Id, D024_Nome_Empresa);
CREATE TABLE T090(T090_Id, T090_T005_Id, T090_Numero_Parcela, T090_Prazos_Dias, T090_Valor_Parcela, T090_D027_Id);
CREATE TABLE D027(D027_Id, D027_Flag_Boleto);
CREATE TABLE T002(T002_T005_Id, T002_C004_Id, T002_Flag_Cancelada, T002_T007_Id);
CREATE TABLE T006(T006_Id, T006_T005_Id, T006_Quantidade REAL, T006_Valor_Total_Preco REAL, T006_Flag_Cancelado, T006_Flag_Status);
CREATE TABLE T007(T007_Id, T007_C004_Id, T007_Flag_Cancelada, T007_Flag_Inutilizado, T007_Flag_Denegado, T007_T005_Id, T007_Valor_Total REAL);
CREATE TABLE T008(T008_T006_Id, T008_T007_Id, T008_Quantidade);
INSERT INTO D024 VALUES(1, 'Cliente fictício');
INSERT INTO D027 VALUES(1, 'S');
INSERT INTO D006 VALUES(1, 'R'),(2, 'N');
''')
for order, company, emitted, status in [(1, 1, '2026-10-15', 1), (2, 1, '2026-10-16', 1), (3, 1, '2026-10-15', 1), (4, 1, '2026-10-14', 1), (5, 1, '2026-10-15', 8), (6, 5, '2026-10-15', 1), (7, 9, '2026-10-15', 1), (8, 1, '2026-10-15', 1), (9, 1, '2026-10-15', 1), (10, 1, '2026-10-15', 1)]:
    db.execute('INSERT INTO T005 VALUES(?,?,?,?,?,?,?,?,?)', (order, company, emitted, '2026-10-20', 100, 1, status, 'N', 2 if order == 10 else 1))
    db.execute('INSERT INTO T006 VALUES(?,?,?,?,?,?)', (order, order, 10, 100, 'N', ''))
    for part, days in [(1, 0), (2, 30)]:
        db.execute('INSERT INTO T090 VALUES(?,?,?,?,?,?)', (order*10+part, order, part, days, 50, 1))
db.executescript('''
INSERT INTO T007 VALUES(1,1,'N','N','N',2,140),(2,1,'S','N','N',2,30),(3,5,'N','N','N',2,30),(4,1,'N','N','N',9,40);
INSERT INTO T008 VALUES(2,1,4),(2,2,3),(2,3,3),(3,1,10);
INSERT INTO T002 VALUES(8,1,'N',0);
''')
params = dict(empresa=1, abrangenciaEmpresa='atual', emissaoPedidosInicio='2026-10-15', dataFim='2026-11-30')
rows = [dict(r) for r in db.execute(sql, params)]
assert {r['pedido'] for r in rows} == {1, 2, 3, 8, 9}
assert len(rows) == 10  # Uma linha por parcela, sem multiplicar por notas/itens.
assert {r['fatorPendente'] for r in rows if r['pedido'] == 1} == {1}
assert {r['fatorPendente'] for r in rows if r['pedido'] == 2} == {0.6}
assert {r['fatorPendente'] for r in rows if r['pedido'] == 3} == {0}
assert {r['fatorPendente'] for r in rows if r['pedido'] == 9} == {0.6}
assert all(r['financeiroAntecipado'] == 1 for r in rows if r['pedido'] == 8)
params['abrangenciaEmpresa'] = 'todas'
assert {r['pedido'] for r in db.execute(sql, params)} == {1, 2, 3, 6, 8, 9}
print('OK: SQL configurado filtra emissão, empresa/cancelamento e calcula faturamento parcial sem duplicar parcelas; notas canceladas e de outra empresa não abatem o pedido.')
