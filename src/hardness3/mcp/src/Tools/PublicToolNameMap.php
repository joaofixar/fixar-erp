<?php

declare(strict_types=1);

namespace HardnessMcp\Tools;

/** Public compatibility boundary for MCP clients with restricted tool-name grammars. */
final class PublicToolNameMap
{
    /** @var array<string,string> */
    private const INTERNAL_TO_PUBLIC = [
        'hardness.company.get_current' => 'hardness_company_get_current',
        'hardness.partner.search' => 'hardness_partner_search',
        'hardness.partner.get' => 'hardness_partner_get',
        'hardness.partner.get_by_document' => 'hardness_partner_get_by_document',
        'hardness.product.prices.apply' => 'hardness_product_prices_apply',
        'hardness.product.prices.history' => 'hardness_product_prices_history',
        'hardness.product.prices.export' => 'hardness_product_prices_export',
        'hardness.product.prices.reversal_prepare' => 'hardness_product_prices_reversal_prepare',
        'hardness.product.prices.reversal_apply' => 'hardness_product_prices_reversal_apply',
        'hardness.product.prices.prepare' => 'hardness_product_prices_prepare',
        'hardness.product.search' => 'hardness_product_search',
        'hardness.product.get_by_partner_reference' => 'hardness_product_get_by_partner_reference',
        'hardness.quote.search' => 'hardness_quote_search',
        'hardness.quote.get_by_id' => 'hardness_quote_get_by_id',
        'hardness.quote.summary' => 'hardness_quote_summary',
        'hardness.order.summary' => 'hardness_order_summary',
        'hardness.quote.item_summary' => 'hardness_quote_item_summary',
        'hardness.order.item_summary' => 'hardness_order_item_summary',
        'hardness.sales.item_summary' => 'hardness_sales_item_summary',
        'hardness.product.conversion_summary' => 'hardness_product_conversion_summary',
        'hardness.commercial.opportunities' => 'hardness_commercial_opportunities',
        'hardness.quote.create' => 'hardness_quote_create',
        'hardness.quote_item.add' => 'hardness_quote_item_add',
        'hardness.quote_item.update' => 'hardness_quote_item_update',
        'hardness.quote_item.delete' => 'hardness_quote_item_delete',
        'hardness.quote.generate_pdf_link' => 'hardness_quote_generate_pdf_link',
        'hardness.sales.summary' => 'hardness_sales_summary',
        'hardness.finance.receivables_summary' => 'hardness_finance_receivables_summary',
        'hardness.finance.payables_summary' => 'hardness_finance_payables_summary',
        'hardness.finance.cash_flow_summary' => 'hardness_finance_cash_flow_summary',
        'hardness.system.context_pilot' => 'hardness_system_context_pilot',
        'hardness.ide_smt.quote.generate_order_by_destination' => 'hardness_ide_smt_quote_generate_order_by_destination',
    ];

    public static function publicName(string $internalName): string
    {
        return self::INTERNAL_TO_PUBLIC[$internalName] ?? $internalName;
    }

    /** Accepts public names and the temporary legacy dotted aliases. */
    public static function internalName(string $receivedName): string
    {
        $internalName = array_search($receivedName, self::INTERNAL_TO_PUBLIC, true);
        return is_string($internalName) ? $internalName : $receivedName;
    }
}
