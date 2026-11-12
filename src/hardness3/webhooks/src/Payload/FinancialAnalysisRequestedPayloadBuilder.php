<?php

declare(strict_types=1);

namespace HardnessWebhooks\Payload;

final class FinancialAnalysisRequestedPayloadBuilder implements PayloadBuilderInterface
{
    public function eventType(): string
    {
        return 'financial_analysis.requested';
    }

    /** @return array<string,mixed> */
    public function build(\mysqli $connection, int $companyId, int $aggregateId): array
    {
        if ($companyId <= 0 || $aggregateId <= 0) {
            throw new \InvalidArgumentException('Webhook payload resolution requires positive company and aggregate IDs.');
        }

        $statement = $connection->prepare(
            'SELECT t.T005_Id, t.T005_Valor_Total, t.T005_D024_Id, '
            . 'c.D024_Nome_Empresa, c.D024_Flag_Pessoa_Fisica_Juridica, c.D024_Cpf, c.D024_Cnpj '
            . 'FROM T005 t INNER JOIN D024 c ON c.D024_Id = t.T005_D024_Id '
            . 'WHERE t.T005_Id = ? AND t.T005_C004_Id = ? LIMIT 1'
        );
        if ($statement === false) {
            throw new \RuntimeException('Could not prepare financial_analysis.requested payload lookup: ' . $connection->error);
        }

        try {
            $statement->bind_param('ii', $aggregateId, $companyId);
            if (!$statement->execute()) {
                throw new \RuntimeException('Could not execute financial_analysis.requested payload lookup: ' . $statement->error);
            }
            $statement->bind_result($orderId, $orderTotal, $customerId, $customerName, $personType, $cpf, $cnpj);
            if (!$statement->fetch()) {
                throw new \RuntimeException('Could not resolve financial_analysis.requested order/customer for the supplied company.');
            }
        } finally {
            $statement->close();
        }

        $document = preg_replace('/\\D+/', '', (string) ($personType === 'F' ? $cpf : $cnpj));

        return array(
            'order' => array(
                'id' => (int) $orderId,
                'total' => (float) $orderTotal,
            ),
            'customer' => array(
                'id' => (int) $customerId,
                'name' => (string) $customerName,
                'document' => (string) $document,
            ),
        );
    }
}
