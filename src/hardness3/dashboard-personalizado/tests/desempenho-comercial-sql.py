"""Executa somente SQLs sobre fixtures SQLite em memória; nenhuma base de cliente."""
import json
import pathlib
import re
import sqlite3
from datetime import date, timedelta

root = pathlib.Path(__file__).resolve().parents[1]
config = json.loads((root / 'tests/fixtures/desempenho-comercial.json').read_text())
indicator = config['indicadores'][0]
db = sqlite3.connect(':memory:')
db.row_factory = sqlite3.Row
db.create_function('CONCAT', -1, lambda *args: ''.join(str(a) for a in args))
db.create_function('GREATEST', -1, max)
db.create_function('NEXT_DAY', 1, lambda value: (date.fromisoformat(value) + timedelta(days=1)).isoformat())
db.executescript('''
CREATE TABLE D024 (D024_Id INTEGER PRIMARY KEY, D024_Nome_Empresa TEXT);
CREATE TABLE C007 (C007_Id INTEGER PRIMARY KEY, C007_Primeiro_Nome TEXT, C007_Nome TEXT);
CREATE TABLE D021 (D021_Id INTEGER PRIMARY KEY, D021_Nome_Empresa TEXT);
CREATE TABLE D006 (D006_Id INTEGER PRIMARY KEY, D006_Flag_Devolucao TEXT, D006_Flag_Entrada_Saida TEXT, D006_Flag_Venda_Compra_Outros TEXT);
INSERT INTO D024 VALUES (1, 'Cliente repetido'), (2, 'Cliente repetido');
INSERT INTO C007 VALUES (7, 'Vendedor A', 'Vendedor A'), (8, 'Vendedor B', 'Vendedor B');
INSERT INTO D021 VALUES (31, 'Representante A'), (32, 'Representante B');
INSERT INTO D006 VALUES (1, 'N', 'S', 'V'), (2, 'S', 'S', 'V'), (3, 'N', 'S', 'O');
''')
for table, extra in [
    ('T003', 'T003_Nome_Cliente TEXT, T003_Flag_Perdido TEXT, T003_Flag_Status_Orcamento TEXT'),
    ('T005', 'T005_T003_Id INTEGER, T005_Flag_Status INTEGER, T005_Flag_Devolucao TEXT'),
    ('T007', 'T007_Cliente_Razao TEXT, T007_D006_Id INTEGER, T007_Valor_Total_IPI REAL, T007_Valor_Frete REAL, T007_Flag_Entrada_Saida TEXT, T007_Flag_Emitida_Recebida TEXT, T007_Numero_Protocolo_Nfe TEXT, T007_Flag_Cancelada TEXT, T007_Flag_Inutilizado TEXT, T007_Flag_Denegado TEXT')
]:
    db.execute(f'CREATE TABLE {table} ({table}_Id INTEGER PRIMARY KEY, {table}_C004_Id INTEGER, {table}_Data_Emissao TEXT, {table}_D024_Id INTEGER, {table}_C007_Id_Vendedor_Interno INTEGER, {table}_D021_Id INTEGER, {table}_Valor_Total REAL, {extra})')

def insert(table, id, **changes):
    fields = dict(Id=id, C004_Id=1, Data_Emissao='2026-04-15', D024_Id=1, C007_Id_Vendedor_Interno=7, D021_Id=31, Valor_Total=100)
    if table == 'T003': fields.update(Nome_Cliente='Avulso', Flag_Perdido='N', Flag_Status_Orcamento='P')
    elif table == 'T005': fields.update(T003_Id=1, Flag_Status=1, Flag_Devolucao='N')
    else: fields.update(Cliente_Razao='Cliente NF', D006_Id=1, Valor_Total_IPI=0, Valor_Frete=0, Flag_Entrada_Saida='S', Flag_Emitida_Recebida='E', Numero_Protocolo_Nfe='123', Flag_Cancelada='N', Flag_Inutilizado='N', Flag_Denegado='N')
    fields.update(changes)
    columns = ', '.join(table + '_' + k for k in fields)
    db.execute(f'INSERT INTO {table} ({columns}) VALUES ({", ".join("?" for _ in fields)})', tuple(fields.values()))

insert('T003', 1, Valor_Total=1000)
insert('T003', 2, C007_Id_Vendedor_Interno=8, D021_Id=32, Valor_Total=200)
insert('T003', 3, Flag_Status_Orcamento='C')
insert('T003', 4, Flag_Perdido='S')
insert('T003', 5, C004_Id=2)
insert('T003', 6, Data_Emissao='2026-03-31')
insert('T003', 7, D024_Id=None, Valor_Total=50)
insert('T003', 8, D024_Id=None, Valor_Total=60)
insert('T005', 11, Valor_Total=400)
insert('T005', 12, Valor_Total=300)
insert('T005', 13, T003_Id=2, Flag_Status=8)
insert('T005', 14, T003_Id=2, Flag_Devolucao='S')
insert('T005', 15, C004_Id=2, T003_Id=2)
insert('T005', 16, Data_Emissao='2026-03-31', T003_Id=2, Flag_Status=8)
insert('T007', 21, Valor_Total=500, Valor_Total_IPI=50, Valor_Frete=20)
insert('T007', 22, Valor_Total=200, Data_Emissao='2026-04-15 23:59:59')
for id, flags in enumerate([dict(Flag_Cancelada='S'), dict(Numero_Protocolo_Nfe=''), dict(Flag_Emitida_Recebida='R'), dict(D006_Id=2), dict(C004_Id=2), dict(Data_Emissao='2026-04-16'), dict(Flag_Inutilizado='S'), dict(Flag_Denegado='S'), dict(Flag_Entrada_Saida='E'), dict(D006_Id=3)], 23):
    insert('T007', id, **flags)
insert('T007', 40, C007_Id_Vendedor_Interno=8, D021_Id=32)
insert('T007', 41, Valor_Total=10, Valor_Frete=20)

params = dict(empresa=1, dataInicio='2026-04-01', dataFim='2026-04-15', vendedor='', representante='', cliente='')
for tab in indicator['abas']:
    queries = dict(orcamentos=tab['desempenhoComercial']['orcamentos'], pedidos=tab['desempenhoComercial']['pedidos'], notas=tab['sqlBase'])
    def run(kind, **filters):
        sql = '\n'.join(queries[kind]).replace('DATE_ADD(:dataFim, INTERVAL 1 DAY)', 'NEXT_DAY(:dataFim)')
        return [dict(row) for row in db.execute(sql, dict(params, **filters))]
    budget, orders, invoices = (run(kind) for kind in ['orcamentos','pedidos','notas'])
    assert sum(row['valor'] for row in budget) == 1310
    assert sum(row['convertido'] for row in budget) == 1, 'Conversão via EXISTS, sem multiplicar por dois pedidos'
    assert {row['clienteId'] for row in budget} == {'cad:1','T003:7','T003:8'}, 'Avulsos são separados'
    assert sum(row['valor'] for row in orders) == 700
    assert sum(row['valor'] for row in invoices) == 730
    assert len(invoices) == 4, 'Notas inválidas excluídas; último dia e nota zerada incluídos'
    filters = {'vendedor': '8'} if tab['id'] == 'vendedores' else {'representante': '32'}
    assert sum(row['valor'] for row in run('notas', **filters)) == 100
    assert not run('pedidos', **filters)
    assert run('orcamentos', **filters)[0]['convertido'] == 0, 'Pedido cancelado/devolução/outra empresa não converte'
    assert not run('notas', cliente="' OR 1=1 --"), 'Filtro textual preparado'
    assert not run('orcamentos', cliente='Cliente inexistente')
print('OK: seis SQLs com fixtures, vínculos, período, empresa, cancelamentos, impostos e conversão sem duplicidade.')
