"""Confere o JSON final com os filtros MCP, somente em SQLite em memória.

Uso: python3 desempenho-comercial-paridade.py /caminho/painel-completo.json
Não lê credenciais nem abre conexão com o ERP.
"""
import contextlib
import io
import json
import pathlib
import runpy
import sys

root = pathlib.Path(__file__).resolve().parents[1]
if len(sys.argv) != 2:
    raise SystemExit('Informe o caminho do JSON completo com Vendedores e Painel de Cotações.')
config = json.loads(pathlib.Path(sys.argv[1]).read_text())
commercial = next(i for i in config['indicadores'] if i.get('desempenhoComercial', {}).get('habilitado') or (i.get('abas') and i['abas'][0].get('desempenhoComercial', {}).get('habilitado')))
shared = bool(commercial.get('desempenhoComercial', {}).get('carteiraRepresentantes'))
tabs = [dict(commercial, **tab) for tab in commercial['abas']] if shared else commercial['abas']
panel = next(i for i in config['indicadores'] if i.get('painelCotacoes', {}).get('habilitado'))
with contextlib.redirect_stdout(io.StringIO()):
    fixture = runpy.run_path(str(root / 'tests/painel-cotacoes-sql.py'))
db, insert, mcp = fixture['db'], fixture['insert'], fixture['mcp']
companies = panel['painelCotacoes']['empresasConfiguradas']
assert len(companies) == 2 and companies[0]['id'] == 1 and companies[1].get('cnpj')
# O ID 5 é fictício: a personalização precisa localizar a filial pelo CNPJ.
db.execute('CREATE TABLE C004 (C004_Id INTEGER PRIMARY KEY, C004_Cnpj TEXT)')
db.execute('INSERT INTO C004 VALUES (1, NULL)')
cnpj = ''.join(c for c in companies[1]['cnpj'] if c.isdigit())
formatted = f'{cnpj[:2]}.{cnpj[2:5]}.{cnpj[5:8]}/{cnpj[8:12]}-{cnpj[12:]}'
db.execute('INSERT INTO C004 VALUES (5, ?)', (formatted,))
db.execute("INSERT INTO C004 VALUES (3, '00.000.000/0000-00')")
transfers = {1: set(companies[0]['clientesTransferencia']), 5: set(companies[1]['clientesTransferencia']), 3: set()}
for customer in set.union(*transfers.values()):
    db.execute('INSERT OR IGNORE INTO D024 (D024_Id,D024_Nome_Empresa) VALUES (?, ?)', (customer, 'Transferência fictícia'))
document = 1000
for company in [1, 5, 3]:
    for customer in set.union(*transfers.values()):
        document += 1
        for table in ['T003', 'T005', 'T007']:
            insert(table, document, C004_Id=company, D024_Id=customer, Valor_Total=777)
insert('T003', 5000, Flag_Perdido='S', Flag_Status_Orcamento='P', Valor_Total=250)
insert('T003', 5001, Flag_Perdido='P', Flag_Status_Orcamento='C', Valor_Total=300)
insert('T003', 5002, Valor_Total=-50)
insert('T005', 5000, T003_Id=5000, Valor_Total=250, Data_Emissao='2026-05-01')
insert('T005', 5001, T003_Id=5000, Valor_Total=250, Data_Emissao='2026-05-02')

def reference(kind, company, seller='', representative=''):
    table, alias = {'orcamentos': ('T003', 't003'), 'pedidos': ('T005', 't'), 'notas': ('T007', 't007')}[kind]
    where, bindings = mcp[kind]
    rows = db.execute(f'SELECT {alias}.* FROM {table} {alias} ' + where, dict(bindings, company_id=company))
    result = {}
    for row in rows:
        if kind == 'orcamentos' and row['T003_Flag_Status_Orcamento'] == 'C': continue
        if kind == 'pedidos' and row['T005_Flag_Status'] == 8: continue
        if kind != 'orcamentos' and row[table + '_D024_Id'] in transfers[company]: continue
        if seller != '' and str(row[table + '_C007_Id_Vendedor_Interno']) != seller: continue
        if representative != '' and str(row[table + '_D021_Id']) != representative: continue
        result[row[table + '_Id']] = row[table + '_Valor_Total'] or 0
    return result

comparisons = 0
for tab in tabs:
    assert tab['desempenhoComercial']['valorNotas'] == 'total'
    for company in [1, 5, 3]:
        for who in ['', '7', '8', '999'] if shared or tab['id'] == 'vendedores' else ['', '31', '32', '999']:
            filters = dict(empresa=company, dataInicio='2026-04-01', dataFim='2026-04-15', vendedor='', representante='', cliente='')
            filters['vendedor' if shared or tab['id'] == 'vendedores' else 'representante'] = who
            for kind in ['orcamentos', 'pedidos', 'notas']:
                lines = tab['sqlBase'] if kind == 'notas' else tab['desempenhoComercial'][kind]
                sql = '\n'.join(lines).replace('DATE_ADD(:dataFim, INTERVAL 1 DAY)', 'NEXT_DAY(:dataFim)')
                actual = [dict(r) for r in db.execute(sql, filters)]
                expected = reference(kind, company, filters['vendedor'], filters['representante'])
                assert {r['id']: r['valor'] for r in actual} == expected, (tab['id'], company, who, kind)
                assert len(actual) == len(expected), 'Vínculos não podem multiplicar documentos'
                if kind == 'orcamentos':
                    for row in actual:
                        expected_conversion = bool(db.execute('SELECT 1 FROM T005 WHERE T005_C004_Id=? AND T005_T003_Id=? AND COALESCE(T005_Flag_Status,0)<>8 LIMIT 1', (company, row['id'])).fetchone())
                        assert row['convertido'] == expected_conversion
                    if company == 1 and who == '':
                        assert next(r for r in actual if r['id'] == 5000)['convertido'] == 1
                        assert all(r['id'] != 5001 for r in actual), 'Revisão cancelada deve ficar fora'
                        assert next(r for r in actual if r['id'] == 5002)['valor'] == -50
                assert not list(db.execute(sql, dict(filters, cliente="' OR 1=1 --"))), 'Busca textual não modifica a consulta'
                comparisons += 1
db.execute('UPDATE C004 SET C004_Cnpj=? WHERE C004_Id=5', (cnpj,))
for tab in tabs:
    for kind in ['pedidos', 'notas']:
        lines = tab['sqlBase'] if kind == 'notas' else tab['desempenhoComercial'][kind]
        sql = '\n'.join(lines).replace('DATE_ADD(:dataFim, INTERVAL 1 DAY)', 'NEXT_DAY(:dataFim)')
        params = dict(empresa=5, dataInicio='2026-04-01', dataFim='2026-04-15', vendedor='', representante='', cliente='')
        assert {r['id']: r['valor'] for r in db.execute(sql, params)} == reference(kind, 5), 'CNPJ sem máscara identifica a mesma filial'
        comparisons += 1
print(f'OK: {comparisons} comparações dos SQLs do JSON final com o MCP; empresa, vendedor, representante, transferências, total das notas e conversão.')
