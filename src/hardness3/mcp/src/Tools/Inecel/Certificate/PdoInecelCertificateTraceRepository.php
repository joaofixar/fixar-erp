<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Inecel\Certificate;

use PDO;

/** Read-only, company-scoped implementation of Inecel's production trace. */
final class PdoInecelCertificateTraceRepository implements InecelCertificateTraceRepositoryInterface, InecelCertificateDocumentRepositoryInterface
{
    private PDO $pdo;

    public function __construct(PDO $pdo) { $this->pdo = $pdo; }

    /** @return array<int,array<string,mixed>> */
    public function trace(int $companyId, int $outgoingInvoiceId): array
    {
        return $this->traceWhere($companyId, 'o.T007_Id = :invoice_id', [':invoice_id' => $outgoingInvoiceId]);
    }

    /** @return array<int,array<string,mixed>> */
    public function traceByInvoiceNumber(int $companyId, string $outgoingInvoiceNumber): array
    {
        return $this->traceWhere($companyId, 'o.T007_Numero_Nota_Fiscal = :invoice_number', [':invoice_number' => $outgoingInvoiceNumber]);
    }

    /** @return array<string,mixed>|null */
    public function findOutgoingInvoiceById(int $companyId, int $outgoingInvoiceId): ?array
    {
        return $this->findOutgoingInvoice($companyId, 'T007_Id = :invoice_id', [':invoice_id' => $outgoingInvoiceId]);
    }

    /** @return array<string,mixed>|null */
    public function findOutgoingInvoiceByNumber(int $companyId, string $outgoingInvoiceNumber): ?array
    {
        return $this->findOutgoingInvoice($companyId, 'T007_Numero_Nota_Fiscal = :invoice_number', [':invoice_number' => $outgoingInvoiceNumber]);
    }

    public function findReadableDocument(int $companyId, int $documentId): ?array
    {
        // Reading and tracing must authorize exactly the same graph, including
        // the complete transfer path. A document ID or a matching lot alone
        // must never grant access to another company's attachments.
        // Anchor each authorization branch at the requested document's invoice.
        // Requiring the transfer makes that branch an inner join, so its origin
        // can be filtered before expanding the caller's outgoing invoices.
        $sql = 'SELECT read_document.SMT205_Id AS document_id, read_document.SMT205_Nome_Arquivo AS file_name'
            . ' FROM SMT205 read_document INNER JOIN T013 document_owner ON document_owner.T013_Id = read_document.SMT205_T013_Id'
            . ' WHERE read_document.SMT205_Id = :document_id AND ('
            . ' EXISTS (SELECT 1 ' . $this->traceFromSql()
            . ' WHERE i.T013_Id = document_owner.T013_Id AND d.SMT205_Id = read_document.SMT205_Id AND o.T007_C004_Id = :company_id)'
            . ' OR EXISTS (SELECT 1 ' . $this->traceFromSql(true)
            . ' WHERE origin_invoice.T013_Id = document_owner.T013_Id AND d.SMT205_Id = read_document.SMT205_Id AND o.T007_C004_Id = :transfer_company_id)'
            . ') LIMIT 1';
        $statement = $this->pdo->prepare($sql);
        $statement->execute([':document_id' => $documentId, ':company_id' => $companyId, ':transfer_company_id' => $companyId]);
        $document = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($document) ? $document : null;
    }

    /** @param array<string,int|string> $parameters @return array<string,mixed>|null */
    private function findOutgoingInvoice(int $companyId, string $invoiceCondition, array $parameters): ?array
    {
        $statement = $this->pdo->prepare('SELECT T007_Id AS outgoing_invoice_id, T007_Numero_Nota_Fiscal AS outgoing_invoice_number FROM T007 WHERE ' . $invoiceCondition . ' AND T007_C004_Id = :company_id LIMIT 1');
        $statement->execute($parameters + [':company_id' => $companyId]);
        $invoice = $statement->fetch(PDO::FETCH_ASSOC);
        return is_array($invoice) ? $invoice : null;
    }

    /** @param array<string,int|string> $parameters @return array<int,array<string,mixed>> */
    private function traceWhere(int $companyId, string $invoiceCondition, array $parameters): array
    {
        // The OP comes from T001A_T255_Id whenever available. The numeric
        // fragment in a lot is only a legacy fallback, verified against T255.
        // The outgoing invoice stays scoped to the connection's company;
        // the linked OP and original receipt may belong to other companies.
        $sql = <<<'SQL'
SELECT
  o.T007_Id AS outgoing_invoice_id,
  o.T007_Numero_Nota_Fiscal AS outgoing_invoice_number,
  oi.T008_Id AS outgoing_item_id,
  oi.T008_D009_Id AS outgoing_product_id,
  MIN(oh.T001_Id) AS outgoing_history_id,
  MIN(ohd.T001A_Lote) AS outgoing_lot,
  GROUP_CONCAT(DISTINCT ohd.T001A_Lote ORDER BY ohd.T001A_Lote SEPARATOR ', ') AS outgoing_lots,
  op.T255_Id AS production_order_id,
  op.T255_C004_Id AS production_company_id,
  MIN(c.T264_Id) AS consumption_id,
  COALESCE(origin_history.T001_Id, ih.T001_Id) AS inbound_history_id,
  COALESCE(origin_lot.T001A_Id, ihd.T001A_Id) AS inbound_lot_id,
  COALESCE(origin_lot.T001A_Lote, ihd.T001A_Lote) AS inbound_lot,
  COALESCE(origin_invoice.T013_Id, i.T013_Id) AS inbound_invoice_id,
  COALESCE(origin_invoice.T013_Numero_Nota_Fiscal, i.T013_Numero_Nota_Fiscal) AS inbound_invoice_number,
  COALESCE(origin_invoice.T013_C004_Id, i.T013_C004_Id) AS inbound_company_id,
  COALESCE(origin_invoice.T013_Data_Entrada, i.T013_Data_Entrada) AS inbound_at,
  COALESCE(origin_item.T014_Id, ii.T014_Id) AS inbound_item_id,
  COALESCE(origin_item.T014_Codigo_Produto, ii.T014_Codigo_Produto) AS inbound_product_code,
  COALESCE(origin_item.T014_Descricao_Produto, ii.T014_Descricao_Produto) AS inbound_product_description,
  i.T013_Id AS material_inbound_invoice_id,
  i.T013_Numero_Nota_Fiscal AS material_inbound_invoice_number,
  i.T013_C004_Id AS material_inbound_company_id,
  transfer_invoice.T007_Id AS transfer_invoice_id,
  transfer_invoice.T007_Numero_Nota_Fiscal AS transfer_invoice_number,
  transfer_invoice.T007_C004_Id AS transfer_company_id,
  transfer_item.T008_Id AS transfer_item_id,
  d.SMT205_Id AS document_id,
  d.SMT205_Nome_Arquivo AS document_file_name,
  d.SMT205_Descricao AS document_description,
  d.SMT205_Data_Hora_Upload AS document_uploaded_at
%s
WHERE %s AND o.T007_C004_Id = :company_id
GROUP BY
  o.T007_Id, o.T007_Numero_Nota_Fiscal, oi.T008_Id, oi.T008_D009_Id,
  op.T255_Id, op.T255_C004_Id, ih.T001_Id, ihd.T001A_Id, ihd.T001A_Lote,
  i.T013_Id, i.T013_Numero_Nota_Fiscal, i.T013_C004_Id, i.T013_Data_Entrada,
  ii.T014_Id, ii.T014_Codigo_Produto, ii.T014_Descricao_Produto,
  origin_history.T001_Id, origin_lot.T001A_Id, origin_lot.T001A_Lote,
  origin_invoice.T013_Id, origin_invoice.T013_Numero_Nota_Fiscal, origin_invoice.T013_C004_Id, origin_invoice.T013_Data_Entrada,
  origin_item.T014_Id, origin_item.T014_Codigo_Produto, origin_item.T014_Descricao_Produto,
  transfer_invoice.T007_Id, transfer_invoice.T007_Numero_Nota_Fiscal, transfer_invoice.T007_C004_Id, transfer_item.T008_Id,
  d.SMT205_Id, d.SMT205_Nome_Arquivo, d.SMT205_Descricao, d.SMT205_Data_Hora_Upload
ORDER BY oi.T008_Id, op.T255_Id, ihd.T001A_Id, d.SMT205_Id
LIMIT 101
SQL;
        $sql = sprintf($sql, $this->traceFromSql(), $invoiceCondition);
        $statement = $this->pdo->prepare($sql);
        $statement->execute($parameters + [':company_id' => $companyId]);
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    private function traceFromSql(bool $requireTransfer = false): string
    {
        // A transfer receipt can have T014_T008_Id = 0. Its NF-e key identifies
        // the issuing invoice, whose T008_T014_Id identifies the original item.
        // Keep the whole path in one LEFT JOIN: partial matches must not change
        // the source invoice or authorize a document. Require the exact lot in
        // the OP receipt, transfer issue and original receipt, with each item
        // and history checked against its own company and product.
        // Existing attachments on the OP receipt keep their current priority.
        $sql = <<<'SQL'
FROM T007 o
INNER JOIN T008 oi ON oi.T008_T007_Id = o.T007_Id AND oi.T008_C004_Id = o.T007_C004_Id
INNER JOIN T001 oh ON oh.T001_T007_Id = o.T007_Id AND oh.T001_T008_Id = oi.T008_Id AND oh.T001_C004_Id = o.T007_C004_Id
INNER JOIN T001A ohd ON ohd.T001A_T001_Id = oh.T001_Id
INNER JOIN T255 op ON op.T255_Id = CASE
    WHEN ohd.T001A_T255_Id IS NOT NULL AND ohd.T001A_T255_Id > 0 THEN ohd.T001A_T255_Id
    WHEN TRIM(SUBSTRING_INDEX(ohd.T001A_Lote, '-', 1)) REGEXP '^[0-9]+$' THEN CAST(TRIM(SUBSTRING_INDEX(ohd.T001A_Lote, '-', 1)) AS UNSIGNED)
    ELSE 0 END
INNER JOIN T264 c ON c.T264_T255_Id = op.T255_Id
INNER JOIN T001 ih ON ih.T001_D009_Id = c.T264_D009_Id AND ih.T001_C004_Id = op.T255_C004_Id
  AND ih.T001_T013_Id IS NOT NULL AND ih.T001_T014_Id IS NOT NULL
INNER JOIN T001A ihd ON ihd.T001A_T001_Id = ih.T001_Id AND ihd.T001A_T255_Id = op.T255_Id
  AND TRIM(ihd.T001A_Lote) <> ''
INNER JOIN T013 i ON i.T013_Id = ih.T001_T013_Id AND i.T013_C004_Id = op.T255_C004_Id
INNER JOIN T014 ii ON ii.T014_Id = ih.T001_T014_Id AND ii.T014_T013_Id = i.T013_Id
  AND ii.T014_C004_Id = op.T255_C004_Id AND ii.T014_D009_Id = ih.T001_D009_Id
%s (
  T007 transfer_invoice
  INNER JOIN T008 transfer_item ON transfer_item.T008_T007_Id = transfer_invoice.T007_Id
    AND transfer_item.T008_C004_Id = transfer_invoice.T007_C004_Id
  INNER JOIN T001 transfer_history ON transfer_history.T001_T007_Id = transfer_invoice.T007_Id
    AND transfer_history.T001_T008_Id = transfer_item.T008_Id
    AND transfer_history.T001_C004_Id = transfer_invoice.T007_C004_Id
    AND transfer_history.T001_D009_Id = transfer_item.T008_D009_Id
  INNER JOIN T001A transfer_lot ON transfer_lot.T001A_T001_Id = transfer_history.T001_Id
  INNER JOIN T014 origin_item ON origin_item.T014_Id = transfer_item.T008_T014_Id
    AND origin_item.T014_C004_Id = transfer_invoice.T007_C004_Id
    AND origin_item.T014_D009_Id = transfer_item.T008_D009_Id
  INNER JOIN T013 origin_invoice ON origin_invoice.T013_Id = origin_item.T014_T013_Id
    AND origin_invoice.T013_C004_Id = origin_item.T014_C004_Id
  INNER JOIN T001 origin_history ON origin_history.T001_T013_Id = origin_invoice.T013_Id
    AND origin_history.T001_T014_Id = origin_item.T014_Id
    AND origin_history.T001_C004_Id = origin_invoice.T013_C004_Id
    AND origin_history.T001_D009_Id = origin_item.T014_D009_Id
  INNER JOIN T001A origin_lot ON origin_lot.T001A_T001_Id = origin_history.T001_Id
    AND TRIM(origin_lot.T001A_Lote) = TRIM(transfer_lot.T001A_Lote)
) ON transfer_invoice.T007_Chave_Acesso_Nfe = i.T013_Chave_Acesso_Nfe
  AND i.T013_Chave_Acesso_Nfe REGEXP '^[0-9]{44}$'
  AND transfer_invoice.T007_C004_Id <> i.T013_C004_Id
  AND TRIM(transfer_lot.T001A_Lote) = TRIM(ihd.T001A_Lote)
  AND NOT EXISTS (SELECT 1 FROM SMT205 immediate_document WHERE immediate_document.SMT205_T013_Id = i.T013_Id)
LEFT JOIN SMT205 d ON d.SMT205_T013_Id = COALESCE(origin_invoice.T013_Id, i.T013_Id)
SQL;
        return sprintf($sql, $requireTransfer ? 'INNER JOIN' : 'LEFT JOIN');
    }
}
