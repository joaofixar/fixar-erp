"""Consultas reais do componente, executadas apenas sobre fixtures SQLite locais."""
import contextlib
import io
import json
import pathlib
import re
import runpy
import subprocess

root = pathlib.Path(__file__).resolve().parents[1]
with contextlib.redirect_stdout(io.StringIO()):
    fixture = runpy.run_path(str(root / 'tests/desempenho-comercial-sql.py'))
db = fixture['db']
insert = fixture['insert']
db.executescript('''
ALTER TABLE C007 ADD COLUMN C007_C007_Id INTEGER;
ALTER TABLE C007 ADD COLUMN C007_C007_Id_2 INTEGER;
UPDATE C007 SET C007_C007_Id=70,C007_C007_Id_2=80 WHERE C007_Id=7;
UPDATE C007 SET C007_C007_Id=80,C007_C007_Id_2=70 WHERE C007_Id=8;
INSERT INTO C007 VALUES (70,'Supervisor A','Supervisor A',NULL,NULL),(80,'Supervisor B','Supervisor B',NULL,NULL);
ALTER TABLE D024 ADD COLUMN D024_Nome_Fantasia TEXT;
ALTER TABLE D024 ADD COLUMN D024_D046_Id INTEGER;
CREATE TABLE D046 (D046_Id INTEGER PRIMARY KEY,D046_Nome TEXT);
INSERT INTO D046 VALUES(1,'Grupo real');
UPDATE D024 SET D024_D046_Id=1 WHERE D024_Id=1;
ALTER TABLE T005 ADD COLUMN T005_Nome_Status TEXT;
ALTER TABLE T007 ADD COLUMN T007_Numero_Nota_Fiscal INTEGER;
CREATE TABLE D201 (D201_Id INTEGER PRIMARY KEY,D201_Chave_Sistema TEXT);
INSERT INTO D201 VALUES(1,'MCP_IA'),(2,'MANUAL');
CREATE TABLE T003A (T003A_T003_Id INTEGER,T003A_D024_Id_Cliente_Proposta INTEGER,T003A_D201_Id INTEGER);
CREATE TABLE T005A (T005A_T005_Id INTEGER,T005A_D024_Id_Cliente_Proposta INTEGER);
CREATE TABLE T007A (T007A_T007_Id INTEGER,T007A_D024_Id_Cliente_Proposta INTEGER);
INSERT INTO T003A VALUES(1,2,1),(1,1,1),(2,NULL,2);
''')
insert('T003', 50, C007_Id_Vendedor_Interno=70, Valor_Total=50)
insert('T003', 51, C004_Id=3, Valor_Total=99999)
queries=json.loads(subprocess.check_output(['php',str(root/'tests/painel-cotacoes.php'),'--sql']))
params=dict(servicosInicio='2025-07-20',empresa=1,dataInicio='2026-04-01',dataFim='2026-04-15',restrito=0,equipeRestrita=0,secundario=0)
def run(kind, multi=False, **filters):
    sql=queries[kind].replace('DATE_ADD(:dataFim, INTERVAL 1 DAY)','NEXT_DAY(:dataFim)')
    if multi: sql=re.sub(r'=\s*:empresa\b',' IN (1,5)',sql)
    return [dict(r) for r in db.execute(sql,dict(params,**filters))]
quotes=run('orcamentos')
valid=[r for r in quotes if not r['excluido']]
assert sum(r['valor'] for r in valid)==1460
assert len([r for r in quotes if r['id']==1])==1,'Extensões duplicadas não multiplicam documento'
assert sum(r['convertido'] for r in valid)==2,'Dois pedidos convertem somente um orçamento'
assert sum(r['ia'] for r in valid)==1,'Origem IA somente por vínculo real'
assert next(r for r in valid if r['id']==1)['proposta']=='Cliente repetido'
assert {r['vendedorId'] for r in run('orcamentos',restrito=70,equipeRestrita=1)}=={7,70},'Equipe contém supervisor e apenas Supervisor 1'
assert {r['vendedorId'] for r in run('orcamentos',restrito=80,equipeRestrita=1)}=={8},'Supervisor 2 não concede acesso'
assert {r['vendedorId'] for r in run('orcamentos',restrito=7)}=={7},'Escopo individual'
assert {r['empresaId'] for r in run('orcamentos',multi=True)}=={1},'Não incluir empresa fora da configuração'
assert next(r for r in run('orcamentos',multi=True) if r['id']==2)['convertido']==1,'Pedido de devolução não cancelado segue o resumo MCP'
assert sum(r['valor'] for r in run('pedidos') if not r['excluido'])==800
assert sum(r['valor'] for r in run('notas'))==1310,'MCP usa valor total e não filtra natureza da operação'
assert sum(r['valor'] for r in run('pedidos_historico'))==900
assert sum(r['valor'] for r in run('notas_historico'))==1310
assert sum(r['valor'] for r in run('notas_historico',restrito=8))==100
assert not run('orcamentos',restrito=999),'Filtro sem vendedor retorna vazio'
print('OK: SQLs de documentos e histórico, equipes, empresas, IA, cliente da proposta e conversão.')

# Compara a população e os valores com os WHERE reais das três ferramentas MCP.
# Não inicializa PDO nem acessa qualquer base externa.
mcp=json.loads(subprocess.check_output(['php',str(root/'tests/painel-cotacoes.php'),'--sql-mcp']))
db.executescript("INSERT INTO D024 VALUES (9001,'Transferência matriz',NULL,NULL),(9002,'Transferência filial',NULL,NULL);")
for table in ['T003','T005','T007']:
    insert(table,130,C004_Id=1,D024_Id=9001,Valor_Total=10000)
    insert(table,131,C004_Id=5,D024_Id=9002,Valor_Total=20000)
    insert(table,132,C004_Id=5,D024_Id=9001,Valor_Total=30)
    insert(table,133,C004_Id=1,D024_Id=9002,Valor_Total=40)
insert('T003',134,Valor_Total=-80)
insert('T007',221,Flag_Cancelada=None)
insert('T007',222,Numero_Protocolo_Nfe='   ')
transfers={1:{9001},5:{9002}}
def reference(kind,company,history=False):
    table,alias={'orcamentos':('T003','t003'),'pedidos':('T005','t'),'notas':('T007','t007')}[kind]
    where,bindings=mcp[kind];bindings=dict(bindings,company_id=company)
    status=table+'_Flag_Status_Orcamento' if kind=='orcamentos' else table+'_Flag_Status' if kind=='pedidos' else None
    sql=f'SELECT {alias}.{table}_Id AS id,{alias}.{table}_Valor_Total AS valor,{alias}.{table}_D024_Id AS cliente'+(f', {alias}.{status} AS status' if status else '')+f' FROM {table} {alias} '+where
    result=[]
    for row in db.execute(sql,bindings):
        if not history and ((kind=='orcamentos' and row['status']=='C') or (kind=='pedidos' and row['status']==8)):continue
        if kind!='orcamentos' and row['cliente'] in transfers[company]:continue
        result.append(dict(row))
    return result
for kind in ['orcamentos','pedidos','notas']:
    expected=[r for c in [1,5] for r in reference(kind,c)]
    actual=[r for r in run(kind,multi=True) if not r['excluido']]
    assert {r['id']:r['valor'] for r in actual}=={r['id']:r['valor'] for r in expected},kind+' divergiu do MCP'
    if kind!='orcamentos':
        expected_history=sum(r['valor'] for c in [1,5] for r in reference(kind,c,True))
        assert sum(r['valor'] for r in run(kind+'_historico',multi=True))==expected_history,kind+' histórico divergiu do MCP'
assert next(r for r in run('orcamentos') if r['id']==130)['transferencia']==1
assert next(r for r in run('orcamentos') if r['id']==133)['transferencia']==0
assert any(r['id']==132 for r in run('pedidos',multi=True)),'Exclusão da Matriz não se aplica à filial'
assert next(r for r in run('orcamentos') if r['id']==134)['valor']==-80,'Preservar valor negativo'

# Itens iguais aos resumos MCP: KG, sinalizadores de item, serviços por código
# e por preço agregado em 270 dias, sem dupla subtração de transferências.
db.executescript("CREATE TABLE D037 (D037_Id INTEGER PRIMARY KEY,D037_Unidade TEXT); INSERT INTO D037 VALUES(1,'KG'),(2,'UN');")
for table,item in [('T003','T004'),('T005','T006'),('T007','T008')]:
    extra=f'{item}_Unidade TEXT,{item}_Flag_Excluir TEXT' if item=='T008' else f'{item}_D037_Id INTEGER,{item}_Flag_Cancelado TEXT,{item}_Flag_Excluir INTEGER'
    db.execute(f'CREATE TABLE {item} ({item}_Id INTEGER PRIMARY KEY,{item}_{table}_Id INTEGER,{item}_Codigo_Produto TEXT,{item}_Descricao_Produto TEXT,{item}_Quantidade REAL,{item}_Valor_Total_Preco REAL,{item}_Valor_Total_Custo REAL,{extra})')
    insert(table,110,C004_Id=5)
    flags={'Flag_Status_Orcamento':'C'} if table=='T003' else {'Flag_Status':8} if table=='T005' else {'Flag_Cancelada':'S'}
    insert(table,111,C004_Id=5,**flags)
    insert(table,112,C004_Id=5,C007_Id_Vendedor_Interno=8)
    insert(table,119,C004_Id=5,Data_Emissao='2026-03-15')
    samples=[(110,'6001',1000,2000,1,False,False),(110,'6002',2000,1000,1,False,False),
             (110,'7001',10000,20000,1,False,False),(110,'7002',5000,2500,1,False,False),
             (110,'6003',999,1000,2,False,False),(110,'6001',700,1400,1,True,False),
             (110,'6001',900,1800,1,False,True),(131,'6001',4000,8000,1,False,False),
             (111,'6001',500,1000,1,False,False),(112,'6001',400,800,1,False,False),
             (119,'6002',9000,18000,1,False,False)]
    for n,(doc,code,qty,net,unit,cancel,exclude) in enumerate(samples):
        if item=='T008' and cancel:continue  # A fonte de itens de notas não tem Flag_Cancelado.
        row=[n,doc,code,'Produto teste',qty,net,0]
        row+=['KG' if unit==1 else 'UN','S' if exclude else 'N'] if item=='T008' else [unit,'S' if cancel else 'N',1 if exclude else 2]
        db.execute(f'INSERT INTO {item} VALUES ({",".join("?" for _ in row)})',row)

def reference_tons(kind):
    parts,where,bindings=mcp['itens_'+kind]
    sql='SELECT i.*,h.customer_id FROM '+parts+' '+where
    history=[dict(r) for r in db.execute(sql,dict(bindings,date_from=params['servicosInicio'])) if r['unit']=='KG']
    totals={}
    for r in history:
        qty,net=totals.get(r['product_code'],(0,0));totals[r['product_code']]=(qty+r['quantity'],net+r['net_total'])
    services={code for code,(qty,net) in totals.items() if qty>0 and net/qty<1}|{'7001'}
    expected={}
    for r in db.execute(sql,bindings):
        if r['unit']!='KG' or r['customer_id'] in transfers[5] or r['product_code'] in services:continue
        expected[r['document_id']]=expected.get(r['document_id'],0)+r['quantity']/1000
    return expected
for kind in ['orcamentos','pedidos','notas']:
    actual={r['id']:r['valor'] for r in run(kind+'_toneladas')}
    assert actual==reference_tons(kind),(kind,actual,reference_tons(kind))
    assert actual[110]==3.0,'Produto barato na venda continua material quando histórico é >= R$ 1/kg'
    assert 131 not in actual,'Transferência não pode gerar toneladas'
    assert {r['id'] for r in run(kind+'_toneladas',restrito=8)}=={112},'Itens respeitam acesso individual'
assert sum(r['valor'] for r in run('pedidos_toneladas_historico'))==3.9
print('OK: paridade com os resumos MCP, transferências por empresa, valor total das notas, negativos e toneladas reais dos itens.')
