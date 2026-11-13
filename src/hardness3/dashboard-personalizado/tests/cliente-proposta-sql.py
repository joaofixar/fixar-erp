"""Confere atribuição de orçamento, pedido e NF em SQLite, sem base de cliente."""
import contextlib
import io
import json
import pathlib
import runpy
import subprocess
import sys

root = pathlib.Path(__file__).resolve().parents[1]
path = pathlib.Path(sys.argv[1]) if len(sys.argv)>1 else root/'tests/fixtures/cliente-proposta.json'
config = json.loads(path.read_text())
seller = next(i for i in config['indicadores'] if i['id']=='desempenho_vendedores')
panel = next(i for i in config['indicadores'] if i['id']=='inecel_desempenho_comercial')
assert panel['painelCotacoes']['atribuirClienteProposta'] is True
with contextlib.redirect_stdout(io.StringIO()):
    fixture = runpy.run_path(str(root/'tests/painel-cotacoes-sql.py'))
db,insert = fixture['db'],fixture['insert']
db.executescript('CREATE TABLE C004 (C004_Id INTEGER, C004_Cnpj TEXT); INSERT INTO C004 VALUES (1,NULL);')
db.execute("INSERT INTO D024 VALUES (91001,'FSI',NULL,NULL)")
db.execute("INSERT INTO D024 VALUES (91002,'EPM',NULL,1)")
panel_sql = json.loads(subprocess.check_output(['php',str(root/'tests/cliente-proposta.php'),str(path)]))
params = dict(equipe='',empresa=1,dataInicio='2026-04-01',dataFim='2026-04-15',vendedor='7',cliente='',restrito=0,equipeRestrita=0,secundario=0)
for kind,t in [('orcamentos','T003'),('pedidos','T005'),('notas','T007')]:
    sources = seller['sqlBase'] if kind=='notas' else seller['desempenhoComercial'][kind]
    sql = '\n'.join(sources).replace('DATE_ADD(:dataFim, INTERVAL 1 DAY)','NEXT_DAY(:dataFim)')
    actual_panel = panel_sql[kind].replace('DATE_ADD(:dataFim, INTERVAL 1 DAY)','NEXT_DAY(:dataFim)')
    aux = t+'A'; proposal = aux+'_D024_Id_Cliente_Proposta'; parent = aux+'_'+t+'_Id'
    for identifier,value in [(71001,100),(71002,200),(71003,300),(71004,400),(71005,500),(71006,600)]:
        insert(t,identifier,D024_Id=91001,Valor_Total=value)
    # Preenchido, zero, nulo, extensão ausente, registro repetido e proposta inexistente.
    for identifier,customer in [(71001,91002),(71002,0),(71003,None),(71005,91002),(71005,91002),(71006,999999)]:
        db.execute(f'INSERT INTO {aux} ({parent},{proposal}) VALUES (?,?)',(identifier,customer))
    rows = {r['id']:dict(r) for r in db.execute(sql,params) if 71001<=r['id']<=71006}
    documents = {r['id']:dict(r) for r in db.execute(actual_panel,params) if 71001<=r['id']<=71006}
    assert len(rows)==len(documents)==6
    assert sum(r['valor'] for r in rows.values())==sum(r['valor'] for r in documents.values())==2100
    for identifier in [71001,71005]:
        assert rows[identifier]['clienteId']=='cad:91002' and rows[identifier]['cliente']=='EPM'
        assert documents[identifier]['clienteId']==91002 and documents[identifier]['cliente']=='EPM'
        assert documents[identifier]['grupo']=='Grupo real', 'Grupo segue cadastro da proposta'
    for identifier in [71002,71003,71004]:
        assert rows[identifier]['clienteId']=='cad:91001' and rows[identifier]['cliente']=='FSI'
        assert documents[identifier]['clienteId']==91001 and documents[identifier]['cliente']=='FSI'
    assert rows[71006]['clienteId']=='cad:999999' and 'FSI' not in rows[71006]['cliente']
    assert documents[71006]['clienteId']==999999 and 'FSI' not in documents[71006]['cliente']
    epm = [r for r in db.execute(sql,dict(params,cliente='EPM')) if 71001<=r['id']<=71006]
    fsi = [r for r in db.execute(sql,dict(params,cliente='FSI')) if 71001<=r['id']<=71006]
    assert {r['id'] for r in epm}=={71001,71005} and sum(r['valor'] for r in epm)==600
    assert {r['id'] for r in fsi}=={71002,71003,71004} and sum(r['valor'] for r in fsi)==900
    assert not [r for r in db.execute(sql,dict(params,vendedor='8')) if 71001<=r['id']<=71006], 'Não alterar vendedor para atribuir cliente'
    assert not [r for r in db.execute(actual_panel,dict(params,restrito=8)) if 71001<=r['id']<=71006], 'Painel mantém autorização do documento'
    # Correspondência carteira/representante/região usa agora o ID EPM, sem outro movimento para FSI.
    assert rows[71001]['responsavelId']==7 and rows[71001]['clienteId']=='cad:91002'
print('OK: EPM/FSI nas três etapas e dois indicadores; nomes/grupo, filtro, zero/nulo/ausência, duplicidade, totais e autorização.')
