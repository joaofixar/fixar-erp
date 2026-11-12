<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\Product\PartnerReference;

use HardnessMcp\Tools\ToolDefinition;

final class PartnerReferenceProductDefinition
{
    public function toToolDefinition(): ToolDefinition
    {
        return new ToolDefinition('hardness.product.get_by_partner_reference', 'Locates products by the reference code used by a specific customer or supplier. Use this when the supplied code belongs to the partner, not to the ERP product record. It does not fall back to internal product codes, descriptions, barcodes, or other identifiers.', [
            'type' => 'object',
            'properties' => [
                'partner_code' => ['type' => 'integer', 'minimum' => 1],
                'role' => ['type' => 'string', 'enum' => ['customer', 'supplier']],
                'reference_code' => ['type' => 'string', 'minLength' => 1, 'maxLength' => 30],
            ],
            'required' => ['partner_code', 'role', 'reference_code'],
            'additionalProperties' => false,
        ], ['product:partner-reference:lookup']);
    }
}
