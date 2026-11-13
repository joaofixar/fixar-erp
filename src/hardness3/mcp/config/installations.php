<?php

declare(strict_types=1);

/*
 * MCP-owned installation inventory. This is the only place that binds a
 * customer-specific profile to an ERP installation; confUsuario is not used
 * for feature selection.
 */
return [
    'inecel-production' => [
        'hosts' => [
            'inecel.hardness.com.br',
            'inecel.localhost',
        ],
        'expected_database' => 'e018',
        'profile' => 'inecel-process-analysis-v1',
    ],
];
