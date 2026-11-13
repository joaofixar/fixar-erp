#!/usr/bin/env python3
"""Run the repository's generated SELECTs on isolated, in-memory fixtures.

No tenant connection or credentials are used. SQLite executes the real joins,
filters and grouping; only MySQL's ordered GROUP_CONCAT is adapted, and
SUBSTRING_INDEX/REGEXP are registered as local functions.
"""

import json
from pathlib import Path
import re
import sqlite3
import subprocess


MCP_ROOT = Path(__file__).resolve().parents[2]
CAPTURE_SQL = r'''
require $argv[1] . '/autoload.php';
class CertificateCaptureStatement extends PDOStatement {
    private $owner;
    public function __construct($owner) { $this->owner = $owner; }
    public function execute($parameters = null) { $this->owner->last['parameters'] = $parameters; return true; }
    public function fetchAll($mode = null, $class = null, $arguments = null) { return []; }
    public function fetch($mode = null, $orientation = PDO::FETCH_ORI_NEXT, $offset = 0) { return false; }
}
class CertificateCapturePdo extends PDO {
    public $last;
    public function __construct() {}
    public function prepare($query, $options = []) {
        $this->last = ['sql' => $query]; return new CertificateCaptureStatement($this);
    }
}
$pdo = new CertificateCapturePdo();
$repository = new HardnessMcp\Tools\Inecel\Certificate\PdoInecelCertificateTraceRepository($pdo);
$repository->trace(5, 112239); $queries['trace_id'] = $pdo->last;
$repository->traceByInvoiceNumber(5, '10591'); $queries['trace_number'] = $pdo->last;
$repository->findReadableDocument(5, 1119); $queries['read'] = $pdo->last;
$repository->findReadableDocument(9, 1119); $queries['read_other_company'] = $pdo->last;
echo json_encode($queries);
'''
captured = subprocess.run(
    ['php', '-r', CAPTURE_SQL, str(MCP_ROOT)],
    check=True, capture_output=True, text=True,
)
QUERIES = json.loads(captured.stdout)


class LotList:
    def __init__(self):
        self.values = set()

    def step(self, value):
        if value is not None:
            self.values.add(value)

    def finalize(self):
        return ', '.join(sorted(self.values)) or None


def substring_index(value, separator, count):
    if value is None:
        return None
    parts = value.split(separator)
    return separator.join(parts[:count] if count > 0 else parts[count:]) if count else ''


def fixture():
    db = sqlite3.connect(':memory:')
    db.row_factory = sqlite3.Row
    db.create_function('SUBSTRING_INDEX', 3, substring_index)
    db.create_function('REGEXP', 2, lambda pattern, value: int(bool(re.search(pattern, value or ''))))
    db.create_aggregate('LOT_LIST', 1, LotList)
    schema = {
        'T007': 'T007_Id INTEGER PRIMARY KEY, T007_C004_Id INT, T007_Numero_Nota_Fiscal TEXT, T007_Chave_Acesso_Nfe TEXT',
        'T008': 'T008_Id INTEGER PRIMARY KEY, T008_T007_Id INT, T008_D009_Id INT, T008_C004_Id INT, T008_T014_Id INT',
        'T001': 'T001_Id INTEGER PRIMARY KEY, T001_D009_Id INT, T001_C004_Id INT, T001_T007_Id INT, T001_T008_Id INT, T001_T013_Id INT, T001_T014_Id INT',
        'T001A': 'T001A_Id INTEGER PRIMARY KEY, T001A_T001_Id INT, T001A_Lote TEXT, T001A_T255_Id INT',
        'T255': 'T255_Id INTEGER PRIMARY KEY, T255_C004_Id INT',
        'T264': 'T264_Id INTEGER PRIMARY KEY, T264_T255_Id INT, T264_D009_Id INT',
        'T013': 'T013_Id INTEGER PRIMARY KEY, T013_C004_Id INT, T013_Numero_Nota_Fiscal TEXT, T013_Data_Entrada TEXT, T013_Chave_Acesso_Nfe TEXT',
        'T014': 'T014_Id INTEGER PRIMARY KEY, T014_T013_Id INT, T014_D009_Id INT, T014_C004_Id INT, T014_Codigo_Produto TEXT, T014_Descricao_Produto TEXT, T014_T008_Id INT DEFAULT 0',
        'SMT205': 'SMT205_Id INTEGER PRIMARY KEY, SMT205_T013_Id INT, SMT205_Nome_Arquivo TEXT, SMT205_Descricao TEXT, SMT205_Data_Hora_Upload TEXT',
    }
    for table, columns in schema.items():
        db.execute(f'CREATE TABLE {table} ({columns})')

    def insert(table, *values):
        db.execute(f"INSERT INTO {table} VALUES ({','.join('?' for _ in values)})", values)

    insert('T007', 112239, 5, '10591', '3' * 44)
    insert('T008', 1062227, 112239, 1275436, 5, 0)
    for index in range(12):
        insert('T001', 2448412 + index, 1275436, 5, 112239, 1062227, None, None)
        insert('T001A', 1702203 + index, 2448412 + index, f'10001 - {index + 1}', None)
    insert('T255', 10001, 2)
    insert('T264', 16279, 10001, 1187683)

    # Each tuple describes one physical coil and its complete transfer chain.
    for received, number, key, received_item, received_history, consumed_lot, lot, issued, issued_item, original, original_number, original_item, original_history, original_lot, document in [
        (77308, '10269', '1' * 44, 201112, 2416623, 1670129, '44064-58A3636A00', 111221, 1053770, 76711, '1219', 199800, 2377262, 1630911, 1119),
        (77307, '10268', '2' * 44, 201111, 2416625, 1670131, '44064-58A3674A00', 111220, 1053769, 76712, '1220', 199803, 2377088, 1630737, 1116),
    ]:
        insert('T013', received, 2, number, '2026-10-19', key)
        insert('T014', received_item, received, 1187683, 2, '145410', 'Bobina transferida', 0)
        insert('T001', received_history, 1187683, 2, None, None, received, received_item)
        insert('T001A', consumed_lot, received_history, lot, 10001)
        insert('T007', issued, 5, number, key)
        insert('T008', issued_item, issued, 1187686, 5, original_item)
        # Two issuing histories must not multiply the certificate result.
        for offset in (0, 1):
            history_id = 3000000 + issued_item * 2 + offset
            insert('T001', history_id, 1187686, 5, issued, issued_item, None, None)
            insert('T001A', history_id, history_id, lot, None)
        insert('T013', original, 5, original_number, '2026-10-06', '4' * 44)
        insert('T014', original_item, original, 1187686, 5, '920011', 'Bobina original', 0)
        insert('T001', original_history, 1187686, 5, None, None, original, original_item)
        insert('T001A', original_lot, original_history, lot, None)
        insert('SMT205', document, original, 'DISC25-0076 MTC.pdf', 'Certificado', '2026-10-06')

    # Another coil on the SAME original item must not become a third match.
    insert('T001', 2377263, 1187686, 5, None, None, 76711, 199800)
    insert('T001A', 1630912, 2377263, '44064-58A3677A00', None)
    # Matching lot and filename in an unrelated company grant no access.
    insert('T013', 90, 7, 'OTHER', '2026-10-06', '9' * 44)
    insert('T014', 91, 90, 909, 7, 'OTHER', 'Unrelated material', 0)
    insert('T001', 92, 909, 7, None, None, 90, 91)
    insert('T001A', 93, 92, '44064-58A3636A00', None)
    insert('SMT205', 94, 90, 'DISC25-0076 MTC.pdf', 'Unrelated', '2026-10-06')
    return db


def query(db, name, **parameters):
    command = QUERIES[name]
    # Preserve DISTINCT and ordering through a local equivalent aggregate.
    sql = command['sql'].replace(
        "GROUP_CONCAT(DISTINCT ohd.T001A_Lote ORDER BY ohd.T001A_Lote SEPARATOR ', ')",
        'LOT_LIST(ohd.T001A_Lote)',
    )
    values = {key.lstrip(':'): value for key, value in command['parameters'].items()}
    values.update(parameters)
    return [dict(row) for row in db.execute(sql, values)]


def first_material(rows):
    return [row for row in rows if row['material_inbound_invoice_id'] == 77308]


def check(condition, message):
    if not condition:
        raise AssertionError(message)
    print('PASS', message)


db = fixture()
rows = query(db, 'trace_id')
check(len(rows) == 2, '12 outgoing lots and duplicate transfer histories resolve to two material origins')
check({(r['inbound_invoice_number'], r['document_id']) for r in rows} == {('1219', 1119), ('1220', 1116)}, 'each consumed coil resolves to its own original invoice and document')
check(all(r['inbound_company_id'] == 5 and r['production_company_id'] == 2 for r in rows), 'original and OP companies remain distinct')
check({r['inbound_lot_id'] for r in rows} == {1630911, 1630737}, 'unconsumed coil on the same original item is excluded')
check(all(len(r['outgoing_lots'].split(', ')) == 12 for r in rows), 'all outgoing lots remain grouped in each result')
check(query(db, 'trace_number') == rows, 'invoice number and internal ID have identical results')
check(query(db, 'trace_id', company_id=9) == [] and query(db, 'trace_number', company_id=9) == [], 'invoice lookup stays scoped to the calling company')
check(query(db, 'read', document_id=1119)[0]['document_id'] == 1119 and query(db, 'read', document_id=1116)[0]['document_id'] == 1116, 'both original certificates are authorized by the same trace')
check(query(db, 'read_other_company') == [] and query(db, 'read', document_id=94) == [], 'document ID, lot and matching filename do not authorize an unrelated document')

# Every broken edge must preserve the immediate receipt as a missing
# certificate, without exposing a partial origin or authorizing its document.
for label, mutation in [
    ('different NF-e key, despite the same invoice number', "UPDATE T007 SET T007_Chave_Acesso_Nfe = 'wrong' WHERE T007_Id = 111221"),
    ('blank NF-e key on both sides', "UPDATE T013 SET T013_Chave_Acesso_Nfe = '' WHERE T013_Id = 77308"),
    ('44 nonnumeric characters in the key', "UPDATE T013 SET T013_Chave_Acesso_Nfe = '" + 'X' * 44 + "' WHERE T013_Id = 77308"),
    ('missing original-item foreign key', 'UPDATE T008 SET T008_T014_Id = 0 WHERE T008_Id = 1053770'),
    ('different lot on the transfer issue', "UPDATE T001A SET T001A_Lote = 'OTHER' WHERE T001A_T001_Id IN (SELECT T001_Id FROM T001 WHERE T001_T008_Id = 1053770)"),
    ('different lot on the original receipt', "UPDATE T001A SET T001A_Lote = 'OTHER' WHERE T001A_Id = 1630911"),
    ('original item points to another product', 'UPDATE T014 SET T014_D009_Id = 999 WHERE T014_Id = 199800'),
    ('original item points to another company', 'UPDATE T014 SET T014_C004_Id = 7 WHERE T014_Id = 199800'),
    ('original invoice belongs to another company', 'UPDATE T013 SET T013_C004_Id = 7 WHERE T013_Id = 76711'),
    ('transfer history belongs to another product', 'UPDATE T001 SET T001_D009_Id = 999 WHERE T001_T008_Id = 1053770'),
    ('transfer history belongs to another company', 'UPDATE T001 SET T001_C004_Id = 7 WHERE T001_T008_Id = 1053770'),
    ('original history belongs to another product', 'UPDATE T001 SET T001_D009_Id = 999 WHERE T001_Id = 2377262'),
    ('original history belongs to another company', 'UPDATE T001 SET T001_C004_Id = 7 WHERE T001_Id = 2377262'),
]:
    db = fixture()
    db.execute(mutation)
    if label == 'blank NF-e key on both sides':
        db.execute("UPDATE T007 SET T007_Chave_Acesso_Nfe = '' WHERE T007_Id = 111221")
    elif label == '44 nonnumeric characters in the key':
        db.execute('UPDATE T007 SET T007_Chave_Acesso_Nfe = ? WHERE T007_Id = 111221', ('X' * 44,))
    fallback = first_material(query(db, 'trace_id'))
    check(len(fallback) == 1 and fallback[0]['inbound_invoice_id'] == 77308
          and fallback[0]['document_id'] is None and fallback[0]['transfer_invoice_id'] is None
          and query(db, 'read', document_id=1119) == [], 'broken path is rejected: ' + label)

db = fixture()
db.execute("INSERT INTO SMT205 VALUES (2222, 77308, 'direct.pdf', 'Direct', '2026-10-19')")
direct = first_material(query(db, 'trace_id'))
check(len(direct) == 1 and direct[0]['inbound_invoice_id'] == 77308 and direct[0]['document_id'] == 2222
      and direct[0]['transfer_invoice_id'] is None, 'existing immediate attachment keeps its original priority')
check(query(db, 'read', document_id=2222) != [] and query(db, 'read', document_id=1119) == [], 'read authorization follows the same document priority')

db = fixture()
db.execute('DELETE FROM SMT205 WHERE SMT205_Id = 1119')
missing = first_material(query(db, 'trace_id'))
check(len(missing) == 1 and missing[0]['inbound_invoice_id'] == 76711 and missing[0]['document_id'] is None,
      'valid original receipt remains visible when its certificate is missing')

db = fixture()
db.execute("UPDATE T001A SET T001A_Lote = 'not-an-op' WHERE T001A_Id BETWEEN 1702203 AND 1702214")
check(query(db, 'trace_id') == [] and query(db, 'read') == [], 'invalid outgoing OP prefix cannot authorize certificates')
db.execute('UPDATE T001A SET T001A_T255_Id = 10001 WHERE T001A_Id BETWEEN 1702203 AND 1702214')
check(len(query(db, 'trace_id')) == 2 and query(db, 'read') != [], 'explicit outgoing OP remains supported without a numeric lot prefix')

db = fixture()
for table, company_column, condition in [
    ('T007', 'T007_C004_Id', 'T007_Id IN (111221, 111220)'),
    ('T008', 'T008_C004_Id', 'T008_Id IN (1053770, 1053769)'),
    ('T001', 'T001_C004_Id', 'T001_T007_Id IN (111221, 111220) OR T001_T013_Id IN (76711, 76712)'),
    ('T013', 'T013_C004_Id', 'T013_Id IN (76711, 76712)'),
    ('T014', 'T014_C004_Id', 'T014_Id IN (199800, 199803)'),
]:
    db.execute(f'UPDATE {table} SET {company_column} = 7 WHERE {condition}')
check(all(row['inbound_company_id'] == 7 for row in query(db, 'trace_id')) and query(db, 'read') != [],
      'a linked origin company is resolved from the data rather than hardcoded to the outgoing company')

print('Inecel certificate SQL regression checks passed (in-memory data only).')
