<?php

// Arquivo de exemplo do fallback de desenvolvimento.
// Em producao, a policy principal deve ficar em:
// dados_usuarios/{dbCliente}/rest_api/access_policy.php
// O fallback de desenvolvimento continua em:
// rest_api/config/policies/clients/{host_normalizado}__{dbCliente}.php

return [

    // Libera todos os campos da tabela.
    'T005' => true,

    // Libera a tabela, mas bloqueia campos especificos.
    'T006' => [
        'block' => [
            'T006_Custo',
            'T006_Margem',
        ],
    ],

    // Libera somente os campos listados.
    'T007' => [
        'allow' => [
            'T007_Id',
            'T007_Descricao',
            'T007_Status',
        ],
    ],
];
