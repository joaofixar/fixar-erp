"""SQLite em memória com registros fictícios, sem acesso ao banco do cliente."""
import sqlite3,json,re
from pathlib import Path
root=Path(__file__).resolve().parents[2]
d=json.loads((root/'dashboard-personalizado/indicadores-pcp-realizado.json').read_text())
i=next(x for x in d['indicadores'] if x['id']=='inecel_pcp_planejamento')
params=dict(empresa=1,abrangenciaEmpresa='atual',dataInicio='2026-10-01',dataFim='2026-10-31',maquina='',status='',op=0,setor='',acabamento='',material='',produto='',cliente='',planoCorte='')
c=sqlite3.connect(':memory:');c.row_factory=sqlite3.Row
c.create_function('CONCAT',-1,lambda *args:''.join(map(str,args)))
tables={
'T257':'T257_Id INTEGER, T257_T256_Id INTEGER, T257_Data_Producao TEXT',
'T257A':'T257A_T257_Id INTEGER, T257A_Peso REAL, T257A_T005_Id INTEGER DEFAULT 30',
'SMT134':'SMT134_Id INTEGER, SMT134_T255_Id INTEGER',
'T256':'T256_Id INTEGER, T256_T255_Id INTEGER, T256_D009_Id INTEGER, T256_T006_Id INTEGER',
'T255':'T255_Id INTEGER, T255_C004_Id INTEGER, T255_Flag_Status TEXT',
'T255A':'T255A_Id INTEGER, T255A_T255_Id INTEGER, T255A_Flag_Maquina TEXT, T255A_Flag_Cancelamento TEXT, T255A_Data_Inicio_Producao TEXT',
'D009':'D009_Id INTEGER, D009_D001_Id INTEGER',
'D001':'D001_Id INTEGER, D001_D060_Id INTEGER, D001_D015_Id INTEGER, D001_Codigo_Produto TEXT, D001_Descricao_Produto TEXT',
'D001A':'D001A_D001_Id INTEGER, D001A_SMT029_Id INTEGER',
'T006':'T006_Id INTEGER, T006_T005_Id INTEGER',
'T005':'T005_Id INTEGER, T005_C004_Id INTEGER, T005_D024_Id INTEGER',
'D024':'D024_Id INTEGER, D024_Nome_Empresa TEXT'}
for t,cols in tables.items():c.execute(f'CREATE TABLE {t} ({cols})')
for op,empresa,status,maquina in [(1,1,'99','0'),(2,1,'00','1'),(3,2,'99','0'),(4,1,'98','2')]:
 c.execute('INSERT INTO T255 VALUES (?,?,?)',(op,empresa,status))
 c.execute('INSERT INTO T255A VALUES (?,?,?,?,?)',(op,op,maquina,'0','2026-09-01'))
 c.execute('INSERT INTO T256 VALUES (?,?,?,?)',(op,op,10,20))
c.execute("INSERT INTO T255A VALUES (0,1,'2','0','2026-09-01')")
c.execute("INSERT INTO D009 VALUES (10,100)")
c.execute("INSERT INTO D001 VALUES (100,7,8,'ABC','Produto')")
c.executemany('INSERT INTO D001A VALUES (100,9)',[(),()])
c.execute('INSERT INTO T006 VALUES (20,30)');c.execute('INSERT INTO T005 VALUES (30,1,40)');c.execute("INSERT INTO D024 VALUES (40,'Cliente')")
records=[(1,1,'2026-10-10',100),(2,1,'2026-10-11',200),(3,2,'2026-10-10',50),(4,3,'2026-10-10',999),(5,4,'2026-10-10',999),(6,1,'2026-09-30',999),(7,2,'2026-10-12',None)]
for id,item,date,weight in records:
 c.execute('INSERT INTO T257 VALUES (?,?,?)',(id,item,date))
 if weight is not None:c.execute('INSERT INTO T257A (T257A_T257_Id, T257A_Peso) VALUES (?,?)',(id,weight))
c.execute('INSERT INTO T257A (T257A_T257_Id, T257A_Peso) VALUES (1,25)')
c.executemany('INSERT INTO SMT134 VALUES (?,1)',[(1,),(2,)])
c.execute("INSERT INTO D024 VALUES (41,'Cliente realizado')")
c.execute('INSERT INTO T005 VALUES (31,1,41)')
c.execute('UPDATE T257A SET T257A_T005_Id=31 WHERE T257A_T257_Id=2')
queries=['\n'.join(s['sqlBase']) for s in i['secoes'][-2:]]
for q in queries:assert set(re.findall(r':(\w+)',q))<=params.keys()
def run(which=0,**changes):return [dict(r) for r in c.execute(queries[which],dict(params,**changes))]
assert sum(r['valor'] for r in run(planoCorte='S'))==325
assert sum(r['valor'] for r in run(planoCorte='N'))==50
assert sum(r['valor'] for r in run(cliente='Cliente realizado'))==200
# Um pedido de outra empresa não pode passar no filtro de cliente.
c.execute('UPDATE T005 SET T005_C004_Id=2 WHERE T005_Id=31')
assert run(cliente='Cliente realizado')==[]
c.execute('UPDATE T005 SET T005_C004_Id=1 WHERE T005_Id=31')
a=run();assert [r['valor'] for r in a]==[175,200,0],a
assert a[0]['blank']==125 and a[0]['slitter']==50 and a[0]['ops']==2 and a[0]['apontamentos']==2
assert a[2]['semPeso']==1
assert sum(r['valor'] for r in run(1))==375
assert sum(r['valor'] for r in run(maquina='0'))==325
assert sum(r['valor'] for r in run(status='Finalizada'))==325
assert sum(r['valor'] for r in run(status='Cancelada'))==999
assert sum(r['valor'] for r in run(op=2))==50
assert sum(r['valor'] for r in run(setor=7,material=9,cliente='Cliente',produto='ABC'))==375
assert run(setor=999)==[]
assert sum(r['valor'] for r in run(empresa=2))==999
assert sum(r['valor'] for r in run(dataInicio='2026-10-11',dataFim='2026-10-11'))==200

# Os detalhes reais devem reconciliar exatamente com os agregados, inclusive
# apontamentos sem peso, registros T257A múltiplos e OP produzida em vários dias.
c.execute('ALTER TABLE T257 ADD COLUMN T257_C007_Id INTEGER DEFAULT 1')
c.execute('ALTER TABLE T257 ADD COLUMN T257_Quantidade REAL DEFAULT 1')
c.execute('ALTER TABLE T257A ADD COLUMN T257A_Id INTEGER')
c.execute('ALTER TABLE T257A ADD COLUMN T257A_Lote TEXT')
c.execute('UPDATE T257A SET T257A_Id = rowid, T257A_Lote = "Lote"')
c.execute('CREATE TABLE C007 (C007_Id INTEGER, C007_Primeiro_Nome TEXT)')
c.execute('INSERT INTO C007 VALUES (1,"Operador")')
for key,which,group in [('realizado_dia',0,'data'),('realizado_maquina',1,'maquina')]:
 q='\n'.join(i['conferencia'][key]['sqlBase'])
 allrows=[dict(r) for r in c.execute(q,params)]
 assert sum(r['valor'] for r in allrows)==375
 assert len(allrows)==4
 assert sum(r['valor'] for r in c.execute(q,dict(params,planoCorte='S')))==325
 assert sum(r['valor'] for r in c.execute(q,dict(params,planoCorte='N')))==50
 assert sum(r['valor'] for r in c.execute(q,dict(params,cliente='Cliente realizado')))==200
 for aggregate in run(which):
  details=[dict(r) for r in c.execute('SELECT * FROM ('+q+') AS detalhe WHERE `'+group+'` = :grupo',dict(params,grupo=aggregate[group]))]
  assert sum(r['valor'] for r in details)==aggregate['valor']
  assert len(details)==aggregate['apontamentos']
  assert len(set(r['op'] for r in details))==aggregate['ops']
  assert sum(r['semPeso'] for r in details)==aggregate['semPeso']
  if which==0:
   for f in ['blank','slitter','combinado','semMaquina']:assert sum(r[f] for r in details)==aggregate[f]
 assert sum(r['valor'] for r in c.execute(q,dict(params,status='Cancelada')))==999
 assert sum(r['valor'] for r in c.execute(q,dict(params,empresa=2)))==999
 assert sum(r['valor'] for r in c.execute(q,dict(params,produto='ABC',material=9,cliente='Cliente')))==375
# O consolidado inclui as empresas configuradas e exclui as demais.
c.execute("INSERT INTO T255 VALUES (5,3,'99')")
c.execute("INSERT INTO T255A VALUES (5,5,'0','0','2026-09-01')")
c.execute('INSERT INTO T256 VALUES (5,5,10,20)')
c.execute("INSERT INTO T257 VALUES (8,5,'2026-10-10',1,1)")
c.execute('INSERT INTO T257A (T257A_T257_Id,T257A_Peso) VALUES (8,777)')
for which in (0,1):
 assert sum(r['valor'] for r in run(which,abrangenciaEmpresa='2'))==999
 assert sum(r['valor'] for r in run(which,abrangenciaEmpresa='1'))==375
 assert run(which,abrangenciaEmpresa='3')==[]
 assert sum(r['valor'] for r in run(which,abrangenciaEmpresa='todas'))==1374
 assert sum(r['valor'] for r in run(which))==375
 assert sum(r['valor'] for r in run(which,abrangenciaEmpresa='todas',maquina='0'))==1324
for key in ('realizado_dia','realizado_maquina'):
 q='\n'.join(i['conferencia'][key]['sqlBase'])
 rows=[dict(r) for r in c.execute(q,dict(params,abrangenciaEmpresa='todas'))]
 assert sum(r['valor'] for r in rows)==1374
 assert len(rows)==5
 assert sum(r['valor'] for r in c.execute(q,params))==375
 assert sum(r['valor'] for r in c.execute(q,dict(params,abrangenciaEmpresa='2')))==999
 assert list(c.execute(q,dict(params,abrangenciaEmpresa='3')))==[]
print('OK: produção e conferências reconciliam por empresa e grupo, sem duplicar pesos; filtros preservados e empresas fora do grupo excluídas.')
