#!/usr/bin/env python3
"""Integration tests for stage 1. Requires MySQL and CREATE TEMPORARY TABLES.

Usage: python3 schema.py mysql --database=test
An arbitrary command prefix (e.g. docker exec -i ... mysql) is accepted.
Only temporary tables are created; never run the entire ERP migration here.
"""
import pathlib
import re
import subprocess
import sys


migration = (pathlib.Path(__file__).resolve().parents[3]
             / 'scripts/atualizacaoBase.php').read_text()
ddl = []
for table in ('MCP020', 'MCP021'):
    matches = re.findall(r'CREATE TABLE IF NOT EXISTS ' + table
                         + r' \(.*?ENGINE=InnoDB;', migration, re.S)
    assert len(matches) == 1, 'Expected exactly one migration for ' + table
    ddl.append(matches[0].replace('CREATE TABLE IF NOT EXISTS',
                                 'CREATE TEMPORARY TABLE IF NOT EXISTS', 1))

command = sys.argv[1:]
if not command:
    sys.exit('Pass a mysql client command with a selected test database.')

setup = "SET time_zone='+00:00'; SET SESSION sql_mode='STRICT_ALL_TABLES';\n"
setup += '\n'.join(ddl) + '\n'


def batch(identifier=1, company=1, connection=1, key='request-1', public=None):
    public = public or format(identifier, '032x')
    return """INSERT INTO MCP020
      (MCP020_Id,MCP020_Public_Id,MCP020_C004_Id,MCP020_MCP001_Id,
       MCP020_C007_Id,MCP020_Chave_Idempotencia,MCP020_Hash_Entrada,
       MCP020_Origem,MCP020_Data_Expiracao)
      VALUES (%d,'%s',%d,%d,1,'%s',UNHEX(SHA2('payload',256)),
              'spreadsheet','2030-01-01');
    """ % (identifier, public, company, connection, key)


def item(identifier=1, line=1, product='123', batch_id=1):
    return """INSERT INTO MCP021
      (MCP021_Id,MCP021_MCP020_Id,MCP021_C004_Id,MCP021_Linha,
       MCP021_Codigo_Produto,MCP021_D009_Id)
      VALUES (%d,%d,1,%d,'00123',%s);
    """ % (identifier, batch_id, line, product)


def check(name, sql, expected=None, error=None):
    result = subprocess.run(command, input=setup + sql, text=True,
                            stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    if error is not None:
        assert result.returncode != 0 and error in result.stderr, name + ': expected ' + error
    else:
        assert result.returncode == 0, name + ': ' + result.stderr
        assert result.stdout.strip() == expected, name + ': ' + result.stdout
    print('PASS ' + name)


check('migration can run twice', '\n'.join(ddl) + batch()
      + 'SELECT COUNT(*) FROM MCP020;', '1')
check('request key cannot be repeated', batch() + batch(2), error='ERROR 1062')
check('public id cannot be repeated', batch() + batch(2, key='other', public=format(1, '032x')),
      error='ERROR 1062')
check('request keys scoped by company and connection', batch() + batch(2, company=2)
      + batch(3, connection=2) + 'SELECT COUNT(*) FROM MCP020;', '3')
check('request keys are case sensitive', batch() + batch(2, key='REQUEST-1')
      + 'SELECT COUNT(*) FROM MCP020;', '2')
check('duplicate input line blocked', batch() + item() + item(2, product='124'),
      error='ERROR 1062')
check('same target cannot appear twice in a batch', batch() + item() + item(2, line=2),
      error='ERROR 1062')
check('unresolved input rows can coexist', batch() + item(product='NULL')
      + item(2, line=2, product='NULL') + 'SELECT COUNT(*) FROM MCP021;', '2')
check('same target allowed in another operation', batch() + batch(2, key='other')
      + item() + item(2, batch_id=2) + 'SELECT COUNT(*) FROM MCP021;', '2')
check('exact decimals, NULL, zero and leading zeros preserved', batch() + item() + """
UPDATE MCP021 SET MCP021_Preco_1_Antes=9999999999.1234567890,
  MCP021_Preco_1_Depois=0.0000000001, MCP021_Preco_2_Antes=NULL,
  MCP021_Preco_2_Depois=0, MCP021_Selecionado_1=1, MCP021_Selecionado_2=1;
SELECT MCP021_Codigo_Produto,MCP021_Preco_1_Antes,MCP021_Preco_1_Depois,
  MCP021_Preco_2_Antes IS NULL,MCP021_Preco_2_Depois FROM MCP021;
""", '00123\t9999999999.1234567890\t0.0000000001\t1\t0.0000000000')
check('overflow rejected instead of silently truncating backup', batch() + item() + """
UPDATE MCP021 SET MCP021_Preco_1_Antes=10000000000;
""", error='ERROR 1264')
check('price 5 selection and exact backup preserved', batch() + item() + """
SELECT MCP021_Selecionado_5,MCP021_Preco_5_Antes IS NULL,
  MCP021_Preco_5_Depois IS NULL FROM MCP021;
UPDATE MCP021 SET MCP021_Selecionado_5=1,
  MCP021_Preco_5_Antes=9999999999.1234567890,MCP021_Preco_5_Depois=0.0000000001;
SELECT MCP021_Selecionado_5,MCP021_Preco_5_Antes,MCP021_Preco_5_Depois FROM MCP021;
UPDATE MCP021 SET MCP021_Preco_5_Antes=NULL,MCP021_Preco_5_Depois=0;
SELECT MCP021_Preco_5_Antes IS NULL,MCP021_Preco_5_Depois FROM MCP021;
""", '0\t1\t1\n1\t9999999999.1234567890\t0.0000000001\n1\t0.0000000000')
check('rollback preserves price and pending history together', batch() + item() + """
CREATE TEMPORARY TABLE D009 (D009_Id BIGINT PRIMARY KEY,
  D009_Preco_1 DECIMAL(20,10)) ENGINE=InnoDB;
CREATE TEMPORARY TABLE D009A (D009A_D009_Id BIGINT PRIMARY KEY,
  D009A_Preco_5 DECIMAL(20,10)) ENGINE=InnoDB;
INSERT INTO D009 VALUES (123,90);
INSERT INTO D009A VALUES (123,95);
START TRANSACTION;
UPDATE D009 SET D009_Preco_1=100 WHERE D009_Id=123;
UPDATE D009A SET D009A_Preco_5=105 WHERE D009A_D009_Id=123;
UPDATE MCP021 SET MCP021_Preco_1_Antes=90,MCP021_Preco_1_Depois=100,
  MCP021_Preco_5_Antes=95,MCP021_Preco_5_Depois=105,
  MCP021_Status='applied',MCP021_Data_Aplicacao=UTC_TIMESTAMP() WHERE MCP021_Id=1;
ROLLBACK;
SELECT D009_Preco_1,D009A_Preco_5,MCP021_Status,MCP021_Data_Aplicacao IS NULL,
  MCP021_Preco_5_Antes IS NULL,MCP021_Preco_5_Depois IS NULL
  FROM D009 JOIN MCP021 ON MCP021_D009_Id=D009_Id
  JOIN D009A ON D009A_D009_Id=D009_Id;
""", '90.0000000000\t95.0000000000\tpending\t1\t1\t1')
check('reversal references preserve original operation', batch() + item()
      + batch(2, key='undo-1') + item(2, batch_id=2) + """
UPDATE MCP020 SET MCP020_Tipo='reversal',MCP020_MCP020_Id_Original=1 WHERE MCP020_Id=2;
UPDATE MCP021 SET MCP021_MCP021_Id_Original=1 WHERE MCP021_Id=2;
SELECT b.MCP020_Tipo,b.MCP020_MCP020_Id_Original,i.MCP021_MCP021_Id_Original
  FROM MCP020 b JOIN MCP021 i ON i.MCP021_MCP020_Id=b.MCP020_Id WHERE b.MCP020_Id=2;
""", 'reversal\t1\t1')
print('All 14 schema integration checks passed (temporary tables only).')
