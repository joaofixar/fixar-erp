<?php
declare(strict_types=1);

/**
 * UFs atendidas pelo Portal Nacional GNRE.
 *
 * This is intentionally independent from GNRE007: snapshots only decide which
 * official configuration can be reused after the user selects a UF.
 */
final class GnreFavoredStateCatalog
{
    /** @return list<string> */
    public static function all(): array
    {
        return [
            'AC', 'AL', 'AM', 'AP', 'BA', 'CE', 'DF', 'GO', 'MA', 'MG',
            'MS', 'MT', 'PA', 'PB', 'PE', 'PI', 'PR', 'RJ', 'RN', 'RO',
            'RR', 'RS', 'SC', 'SE', 'TO',
        ];
    }
}
