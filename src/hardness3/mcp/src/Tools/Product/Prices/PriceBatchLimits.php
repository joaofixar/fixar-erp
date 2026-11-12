<?php

declare(strict_types=1);
namespace HardnessMcp\Tools\Product\Prices;

/** One setting controls the published schema, argument budget and HTTP envelope. */
final class PriceBatchLimits
{
    public const DEFAULT_MAX_ITEMS = 1000;

    public static function maxItems(): int
    {
        $value = getenv('MCP_PRODUCT_PRICE_MAX_ITEMS');
        if ($value === false || $value === '') return self::DEFAULT_MAX_ITEMS;
        if (!preg_match('/\A[1-9][0-9]{0,4}\z/', $value) || (int)$value > 10000) {
            throw new \RuntimeException('MCP_PRODUCT_PRICE_MAX_ITEMS must be between 1 and 10000.');
        }
        return (int)$value;
    }

    public static function argumentBytes(): int
    {
        // Includes the worst-case JSON escaping of 255-character codes and all five prices.
        return 4096 + self::maxItems() * 4096;
    }

    public static function payloadBytes(): int { return max(1048576, self::argumentBytes() + 4096); }
}
