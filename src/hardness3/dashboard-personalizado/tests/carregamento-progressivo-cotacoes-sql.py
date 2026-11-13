"""SQL final dos lotes com propostas agrupadas; somente fixtures SQLite locais."""
import contextlib
import io
import json
import pathlib
import runpy
import subprocess
import sys

root = pathlib.Path(__file__).resolve().parents[1]
with contextlib.redirect_stdout(io.StringIO()):
    fixture = runpy.run_path(str(root/'tests/painel-cotacoes-sql.py'))
db,insert = fixture['db'],fixture['insert']
command=['php',str(root/'tests/carregamento-progressivo-cotacoes.php')]
if len(sys.argv)>1:
    command.append('--config='+sys.argv[1])
queries = json.loads(subprocess.check_output(command))
for table,aux in [('T003','T003A'),('T005','T005A'),('T007','T007A')]:
    db.execute(f'CREATE INDEX fixture_{aux}_parent ON {aux} ({aux}_{table}_Id)')
    for identifier in range(810001,812506):
        insert(table,identifier,C004_Id=1 if identifier%2 else 5,Data_Emissao='2026-04-10',C007_Id_Vendedor_Interno=7 if identifier%5 else 70)
        fields={f'{aux}_{table}_Id':identifier,f'{aux}_D024_Id_Cliente_Proposta':2 if identifier%3 else None}
        if table=='T003': fields['T003A_D201_Id']=1
        db.execute(f'INSERT INTO {aux} ({",".join(fields)}) VALUES ({",".join("?" for _ in fields)})',tuple(fields.values()))
def dialect(sql):
    for field in ('dataFim','loteFim'):
        sql=sql.replace(f'DATE_ADD(:{field}, INTERVAL 1 DAY)',f'NEXT_DAY(:{field})')
    return sql
checked=0
for name,query in queries.items():
    params=query['pagina1']['parametros']
    expected=[dict(row) for row in db.execute(dialect(query['base']),params)]
    first=[dict(row) for row in db.execute(dialect(query['pagina1']['sql']),params)]
    second=[dict(row) for row in db.execute(dialect(query['pagina2']['sql']),query['pagina2']['parametros'])]
    assert first+second==expected,(name,len(expected),len(first),len(second))
    if name=='0_0_orcamentos_periodo':
        assert len(first)==2000 and len(second)>0,'Deve exercer duas páginas reais'
        assert len({(row['empresaId'],row['id']) for row in first+second})==len(expected),'Sem documentos duplicados'
    checked+=1
print(f'OK: {checked} consultas paginadas; duas páginas reconciliam com o SQL sem lotes, incluindo propostas, IA, escopos, histórico e toneladas. Sem banco de cliente.')
