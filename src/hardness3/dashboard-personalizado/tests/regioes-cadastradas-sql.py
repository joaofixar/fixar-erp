"""Fixtures SQLite de regiões do ERP; nenhuma consulta a bases de clientes."""
import json
import pathlib
import sqlite3
import sys

path = pathlib.Path(sys.argv[1]) if len(sys.argv)>1 else pathlib.Path(__file__).parent/'fixtures/regioes-cadastradas.json'
config = json.loads(path.read_text())
c = next(i for i in config['indicadores'] if i['id']=='desempenho_vendedores')['desempenhoComercial']
db = sqlite3.connect(':memory:'); db.row_factory = sqlite3.Row
db.create_function('CONCAT',-1,lambda *args: ''.join(str(a) for a in args))
db.executescript('''
CREATE TABLE C007 (C007_Id INTEGER, C007_Primeiro_Nome TEXT, C007_Nome TEXT);
CREATE TABLE D018 (D018_Id INTEGER, D018_UF TEXT);
CREATE TABLE D084 (D084_Id INTEGER, D084_Regiao TEXT);
CREATE TABLE D020 (D020_Id INTEGER, D020_Nome_Cidade TEXT, D020_Codigo_IBGE TEXT, D020_D018_Id INTEGER, D020_D084_Id INTEGER);
CREATE TABLE D024 (D024_Id INTEGER, D024_Nome_Empresa TEXT, D024_C007_Vendedor_Interno INTEGER, D024_C004_Id INTEGER, D024_D018_Id INTEGER, D024_D020_Id INTEGER, D024_Flag_Cliente TEXT);
INSERT INTO C007 VALUES (7,'Rafaela','Rafaela'),(8,'Outro','Outro');
INSERT INTO D018 VALUES (1,'PR'),(2,'SP');
INSERT INTO D084 VALUES (101,'PR1 GRANDE CURITIBA'),(102,'PR2 OESTE'),(103,'SP CAPITAL');
INSERT INTO D020 VALUES (10,'Curitiba','4106902',1,101),(11,'Cascavel','4104808',1,102),(12,'Sem região','4100103',1,NULL),(13,'São Paulo','3550308',2,103);
INSERT INTO D024 VALUES (1,'Cliente A',7,1,1,10,'S'),(2,'Cliente B',7,1,1,11,'S'),(3,'Cliente C',7,0,1,12,'S'),(4,'Cliente outro vendedor',8,1,2,13,'S'),(5,'Cliente outra empresa',7,2,2,13,'S');
''')
params = dict(empresa=1,vendedor='7',cliente='')
rows = [dict(r) for r in db.execute('\n'.join(c['carteiraRegioes']),params)]
assert {r['clienteId'] for r in rows} == {'cad:1','cad:2','cad:3'}
assert [r['regiaoId'] for r in rows] == [101,102,0]
territories = [dict(r) for r in db.execute('\n'.join(c['territoriosRegioes']),params)]
assert {r['uf'] for r in territories} == {'PR'}, 'Não buscar UFs fora da carteira autorizada'
assert len(territories)==3, 'Cadastro municipal sem multiplicação por clientes'
params.update(vendedor='8')
assert {r['uf'] for r in db.execute('\n'.join(c['territoriosRegioes']),params)}=={'SP'}
params.update(vendedor='7',cliente='Cliente inexistente')
assert not db.execute('\n'.join(c['territoriosRegioes']),params).fetchall()
assert not db.execute('\n'.join(c['carteiraRegioes']),params).fetchall()
maps = pathlib.Path(__file__).resolve().parents[1]/'mapas-municipios'
assert len(list(maps.glob('*.json')))==27
assert len(json.loads((maps/'PR.json').read_text())['locations'])==399
for path in maps.glob('*.json'):
    value=json.loads(path.read_text())
    assert value['fonte'].startswith('https://servicodados.ibge.gov.br/api/v3/malhas/')
    assert all(len(p['id'])==7 and p['id'].isdigit() and p['path'].startswith('M') for p in value['locations'])
print('OK: regiões D020/D084, vendedor, empresa, cliente, ausência de região e malhas locais das 27 UFs.')
