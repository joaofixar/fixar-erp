<?php
declare(strict_types=1);

namespace Hardness\Observability\Context;

/**
 * Etapa 3.1 somente: substitua este provider na etapa de autenticação real.
 * A variável é destinada apenas ao ambiente de desenvolvimento/teste e não é
 * exposta por rota, query string, cookie ou interface.
 */
final class MockObservationContextProvider implements ObservationContextProvider
{
    public function current(): ObservationContext
    {
        $requested = getenv('OBSERVABILITY_MOCK_CONTEXT');

        if ($requested === 'hardness_internal') {
            return new ObservationContext(
                'hardness_internal',
                'Servidor simulado atual',
                array('view' => true, 'full_sql' => true, 'connection_identity' => true),
                array('Tenant A', 'Tenant B', 'Tenant C')
            );
        }

        return new ObservationContext(
            'client',
            'Tenant atual simulado',
            array('view' => true, 'full_sql' => false, 'connection_identity' => false),
            array('Tenant atual simulado')
        );
    }
}
