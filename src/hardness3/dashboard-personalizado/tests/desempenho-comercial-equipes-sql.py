"""Equipes em SQLite local: mesmas definições do JSON, sem conexão com clientes."""
import contextlib
import io
import json
import pathlib
import runpy
import sys

root=pathlib.Path(__file__).resolve().parents[1]
if len(sys.argv)<2: sys.argv.append(str(root/'tests/fixtures/desempenho-comercial-equipes.json'))
path=pathlib.Path(sys.argv[1])
config=json.loads(path.read_text())
v=next(i for i in config['indicadores'] if i['id']=='desempenho_vendedores')
# A fixture de dois indicadores também confere a atribuição ao cliente proposta.
with contextlib.redirect_stdout(io.StringIO()):
    original=sys.argv[:]
    sys.argv=[sys.argv[0],str(root/'tests/fixtures/cliente-proposta.json')]
    base=runpy.run_path(str(root/'tests/cliente-proposta-sql.py'))
    sys.argv=original
db,insert=base['db'],base['insert']
db.executescript('''
INSERT INTO C004 VALUES(5,'17.750.961/0005-40');
INSERT INTO C007 VALUES(83,'Gian','Gian',NULL,NULL),(79,'Gian 2','Gian 2',NULL,NULL),(89,'Humberto','Humberto',NULL,NULL),(77,'William','William',83,NULL),(100,'ALESSANDRO','ALESSANDRO',NULL,NULL),(101,'Aço','Aço',100,NULL);
UPDATE C007 SET C007_C007_Id=89,C007_C007_Id_2=77 WHERE C007_Id=7;
UPDATE C007 SET C007_C007_Id=77,C007_C007_Id_2=89 WHERE C007_Id=8;
''')
params=dict(empresa=1,dataInicio='2026-04-01',dataFim='2026-04-15',vendedor='',cliente='',equipe='')
team_filter=next(f for f in v['filtros'] if f['id']=='equipe')
options='\n'.join(team_filter['opcoesSql'])
assert {r['valor'] for r in db.execute(options,dict(empresa=1))}=={'gian','humberto_rafaela','william'}
assert {r['valor'] for r in db.execute(options,dict(empresa=5))}=={'alessandro'}
for kind,t in [('orcamentos','T003'),('pedidos','T005'),('notas','T007')]:
    sql='\n'.join(v['sqlBase'] if kind=='notas' else v['desempenhoComercial'][kind]).replace('DATE_ADD(:dataFim, INTERVAL 1 DAY)','NEXT_DAY(:dataFim)')
    for identifier,seller,company in [(73001,7,1),(73002,8,1),(73003,77,1),(73004,83,1),(73005,101,5),(73006,100,5)]:insert(t,identifier,C007_Id_Vendedor_Interno=seller,C004_Id=company,Valor_Total=identifier-73000)
    def ids(team,company=1,seller=''):
        return {r['id'] for r in db.execute(sql,dict(params,equipe=team,empresa=company,vendedor=seller)) if 73001<=r['id']<=73006}
    assert ids('humberto_rafaela')=={73001}, 'Supervisor 2 não altera equipe'
    assert ids('william')=={73002,73003}, 'Supervisor próprio está em sua equipe'
    assert ids('gian')=={73004}, 'William não se duplica na equipe do seu supervisor'
    assert ids('alessandro',5)=={73005,73006}, 'Empresa por CNPJ e supervisor por nome'
    assert ids('alessandro',1)==set()
    assert ids('william',1,'7')==set(), 'Equipe e vendedor são filtros cumulativos'
    assert ids('')=={73001,73002,73003,73004}
db.executescript('''
ALTER TABLE D024 ADD COLUMN D024_C007_Vendedor_Interno INTEGER;
ALTER TABLE D024 ADD COLUMN D024_C004_Id INTEGER;
ALTER TABLE D024 ADD COLUMN D024_D021_Id INTEGER;
ALTER TABLE D024 ADD COLUMN D024_D018_Id INTEGER;
ALTER TABLE D024 ADD COLUMN D024_D020_Id INTEGER;
ALTER TABLE D024 ADD COLUMN D024_Flag_Cliente TEXT;
CREATE TABLE C019 (C019_C007_Id INTEGER,C019_C004_Id INTEGER);
INSERT INTO C019 VALUES(7,1),(8,1);
CREATE TABLE D018 (D018_Id INTEGER,D018_UF TEXT);
INSERT INTO D018 VALUES(1,'PR');
CREATE TABLE D084 (D084_Id INTEGER,D084_Regiao TEXT);
INSERT INTO D084 VALUES(1,'PR1');
CREATE TABLE D020 (D020_Id INTEGER,D020_Nome_Cidade TEXT,D020_Codigo_IBGE TEXT,D020_D018_Id INTEGER,D020_D084_Id INTEGER);
INSERT INTO D020 VALUES(10,'Curitiba','4106902',1,1);
INSERT OR IGNORE INTO D021 VALUES(31,'INECEL');
UPDATE D024 SET D024_C007_Vendedor_Interno=7,D024_C004_Id=1,D024_D021_Id=31,D024_D018_Id=1,D024_D020_Id=10,D024_Flag_Cliente='S' WHERE D024_Id=91002;
UPDATE D024 SET D024_C007_Vendedor_Interno=8,D024_C004_Id=1,D024_D021_Id=31,D024_D018_Id=1,D024_D020_Id=10,D024_Flag_Cliente='S' WHERE D024_Id=91001;
CREATE TABLE SMT120 (SMT120_C007_Id INTEGER,SMT120_Data_Inicial TEXT,SMT120_Data_Final TEXT,SMT120_Valor_Meta REAL,SMT120_Valor_Atingido REAL,SMT120_Percentual_Atingido REAL);
INSERT INTO SMT120 VALUES(7,'2026-04-01','2026-04-30',1000,500,50),(8,'2026-04-01','2026-04-30',2000,500,25);
''')
commercial=v['desempenhoComercial']
for team,customer,seller_id in [('humberto_rafaela','cad:91002',7),('william','cad:91001',8)]:
    selected=dict(params,equipe=team,metaInicio='2026-04-01',metaFim='2026-04-30')
    for key in ['carteiraRepresentantes','carteiraRegioes']:
        assert {r['clienteId'] for r in db.execute('\n'.join(commercial[key]),selected)}=={customer}
    assert {r['vendedorId'] for r in db.execute('\n'.join(commercial['metasVendedores']),selected)}=={seller_id}
    assert {r['uf'] for r in db.execute('\n'.join(commercial['territoriosRegioes']),selected)}=={'PR'}
assert not db.execute('\n'.join(commercial['territoriosRegioes']),dict(params,equipe='gian')).fetchall(), 'Territórios sem carteira na equipe não entram'
print('OK: quatro equipes, Supervisor 1, supervisores, CNPJ, vendedor/equipe, documentos, metas, carteiras e territórios.')
