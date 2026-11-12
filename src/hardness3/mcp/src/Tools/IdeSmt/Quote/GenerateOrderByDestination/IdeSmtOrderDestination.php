<?php

declare(strict_types=1);

namespace HardnessMcp\Tools\IdeSmt\Quote\GenerateOrderByDestination;

use InvalidArgumentException;

/** Maps the public IDE SMT destination names to the legacy VEN001 types. */
final class IdeSmtOrderDestination
{
    /** @var array<string,int> */
    private const TYPES = [
        'point_of_sale' => 1,
        'shipping' => 2,
        'counter_book' => 3,
        'shipping_book' => 4,
    ];

    public static function typeFor(string $destination): int
    {
        if (!isset(self::TYPES[$destination])) {
            throw new InvalidArgumentException('Invalid IDE SMT order destination.');
        }
        return self::TYPES[$destination];
    }

    /** @return string[] */
    public static function all(): array
    {
        return array_keys(self::TYPES);
    }
}
