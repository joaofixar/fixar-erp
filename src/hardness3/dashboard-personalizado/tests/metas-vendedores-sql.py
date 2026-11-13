"""Executa o SELECT de metas em SQLite local; não acessa bases de clientes."""
import json
import pathlib
import sqlite3
import sys

fixture = pathlib.Path(__file__).parent / 'fixtures/metas-vendedores.sql'
sql = fixture.read_text()
if len(sys.argv) > 1:
    config = json.loads(pathlib.Path(sys.argv[1]).read_text())
    indicator = next(i for i in config['indicadores'] if i['id'] == 'desempenho_vendedores')
    configured = '\n'.join(indicator['desempenhoComercial']['metasVendedores']) + '\n'
    assert configured == sql, 'Entrega deve usar o SELECT testado'
db = sqlite3.connect(':memory:')
db.executescript('''
CREATE TABLE C019 (C019_C007_Id INTEGER, C019_C004_Id INTEGER);
CREATE TABLE SMT120 (SMT120_Id INTEGER, SMT120_C007_Id INTEGER,
 SMT120_Data_Inicial TEXT, SMT120_Data_Final TEXT, SMT120_Valor_Meta REAL,
 SMT120_Valor_Atingido REAL, SMT120_Percentual_Atingido REAL);
INSERT INTO C019 VALUES (7,1),(7,1),(7,4),(8,1),(9,4);
INSERT INTO SMT120 VALUES
 (1,7,'2026-04-01','2026-04-30',1000,4900.50,490.05),
 (2,8,'2026-04-01','2026-04-30',3000,1200,40),
 (3,9,'2026-04-01','2026-04-30',100000,100000,100),
 (4,7,'2026-03-01','2026-03-31',1000000,1000000,100),
 (5,7,'2026-04-15','2026-04-30',20000,20000,100);
''')
params = dict(metaInicio='2026-04-01', metaFim='2026-04-30', empresa=1, vendedor='')
rows = db.execute(sql, params).fetchall()
assert len(rows) == 2 and sum(r[3] for r in rows) == 4000
params['vendedor'] = '7'
rows = db.execute(sql, params).fetchall()
assert len(rows) == 1 and rows[0][3:] == (1000, 4900.5, 490.05)
params['vendedor'] = '9'
assert not db.execute(sql, params).fetchall(), 'Vendedor de outra empresa não entra'
params.update(vendedor='7', empresa=4)
assert len(db.execute(sql, params).fetchall()) == 1, 'Meta global do vendedor não é multiplicada por vínculos'
params.update(metaInicio='2026-05-01', metaFim='2026-05-31')
assert not db.execute(sql, params).fetchall(), 'Não reutilizar meta anterior'
print('OK: SQL de metas, mês, vendedor, empresa e vínculos duplicados.')
