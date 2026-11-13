"""Valida a carteira em SQLite local, sem conexão com qualquer base de cliente."""
import json
import pathlib
import sqlite3
import sys

path = pathlib.Path(sys.argv[1]) if len(sys.argv) > 1 else pathlib.Path(__file__).parent/'fixtures/desempenho-comercial-carteira.json'
config = json.loads(path.read_text())
indicator = next(i for i in config['indicadores'] if i.get('desempenhoComercial',{}).get('carteiraRepresentantes'))
sql = '\n'.join(indicator['desempenhoComercial']['carteiraRepresentantes'])
db = sqlite3.connect(':memory:')
db.row_factory = sqlite3.Row
db.create_function('CONCAT', -1, lambda *xs: ''.join(str(x) for x in xs))
db.executescript('''
CREATE TABLE C007 (C007_Id INTEGER PRIMARY KEY,C007_Primeiro_Nome TEXT,C007_Nome TEXT);
CREATE TABLE D021 (D021_Id INTEGER PRIMARY KEY,D021_Nome_Empresa TEXT);
CREATE TABLE D024 (D024_Id INTEGER PRIMARY KEY,D024_Nome_Empresa TEXT,D024_C004_Id INTEGER,D024_Flag_Cliente TEXT,D024_C007_Vendedor_Interno INTEGER,D024_D021_Id INTEGER);
INSERT INTO C007 VALUES (7,'Rafaela','Rafaela'),(8,'Outro vendedor','Outro vendedor');
INSERT INTO D021 VALUES (31,'INECEL'),(32,'Representante B'),(33,'INECEL');
INSERT INTO D024 VALUES
(1,'Cliente A',1,'S',7,31),
(2,'Cliente sem movimento',1,'S',7,31),
(3,'Cliente outro vendedor',1,'S',8,31),
(4,'Cliente outra empresa',2,'S',7,31),
(5,'Compartilhado',0,'S',7,32),
(6,'Compartilhado nulo',NULL,'S',7,32),
(7,'Sem representante',1,'S',7,NULL),
(8,'Sem vendedor',1,'S',NULL,31),
(9,'Fornecedor',1,'N',7,31),
(10,'Cliente A',1,'S',7,33);
''')
def run(**args):
    params = dict(empresa=1,vendedor='7',cliente='')
    params.update(args)
    return [dict(r) for r in db.execute(sql,params)]
def ids(rows): return {r['clienteId'] for r in rows}
rows = run()
assert ids(rows) == {'cad:1','cad:2','cad:5','cad:6','cad:10'}, rows
assert {r['representanteId'] for r in rows} == {31,32,33}, 'Homônimos não se fundem; vínculo vem do cadastro'
assert ids(run(cliente='sem movimento')) == {'cad:2'}, 'Carteira independe de emissão de documentos'
assert not run(cliente="' OR 1=1 --"), 'Busca permanece parametrizada'
assert ids([dict(r) for r in db.execute(sql,dict(empresa=1,vendedor='8',cliente=''))]) == {'cad:3'}
assert ids([dict(r) for r in db.execute(sql,dict(empresa=2,vendedor='7',cliente=''))]) == {'cad:4','cad:5','cad:6'}
assert ids([dict(r) for r in db.execute(sql,dict(empresa=1,vendedor='',cliente=''))]) == ids(rows)|{'cad:3'}, 'Gestor consolida carteiras sem outra empresa'
print('OK: cadastro atual, vendedor principal, representante, empresa, compartilhados, clientes sem movimento e filtros preparados.')
if indicator['desempenhoComercial'].get('carteiraRegioes'):
    db.executescript('''
    ALTER TABLE D024 ADD COLUMN D024_D018_Id INTEGER;
    ALTER TABLE D024 ADD COLUMN D024_D020_Id INTEGER;
    CREATE TABLE D018 (D018_Id INTEGER PRIMARY KEY,D018_UF TEXT);
    CREATE TABLE D020 (D020_Id INTEGER PRIMARY KEY,D020_Nome_Cidade TEXT);
    INSERT INTO D018 VALUES (1,'PR'),(2,'SP');
    INSERT INTO D020 VALUES (10,'Curitiba'),(11,'Londrina'),(12,'São Paulo');
    UPDATE D024 SET D024_D018_Id=1,D024_D020_Id=10 WHERE D024_Id=1;
    UPDATE D024 SET D024_D018_Id=1,D024_D020_Id=11 WHERE D024_Id=2;
    UPDATE D024 SET D024_D018_Id=2,D024_D020_Id=12 WHERE D024_Id IN (3,7);
    ''')
    sql = '\n'.join(indicator['desempenhoComercial']['carteiraRegioes'])
    regional = run()
    assert ids(regional) == ids(rows)|{'cad:7'}, 'Mapa inclui clientes sem representante'
    assert next(r for r in regional if r['clienteId']=='cad:1')['uf']=='PR'
    assert next(r for r in regional if r['clienteId']=='cad:2')['cidade']=='Londrina'
    assert next(r for r in regional if r['clienteId']=='cad:5')['uf']==''
    assert not run(cliente="' OR 1=1 --")
    print('OK: SQL regional usa UF/cidade do cadastro, mantém ACL/empresa e inclui endereço incompleto e carteira sem representante.')
