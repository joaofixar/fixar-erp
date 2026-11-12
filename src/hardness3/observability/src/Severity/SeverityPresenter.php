<?php
declare(strict_types=1);

namespace Hardness\Observability\Severity;

final class SeverityPresenter
{
    public static function label(string $severity): string
    {
        $labels = array(
            Severity::NORMAL => 'Normal',
            Severity::ATTENTION => 'Atenção',
            Severity::CRITICAL => 'Crítico',
            Severity::UNAVAILABLE => 'Indisponível',
            Severity::UNKNOWN => 'Desconhecido',
        );
        return isset($labels[$severity]) ? $labels[$severity] : $labels[Severity::UNKNOWN];
    }
}
