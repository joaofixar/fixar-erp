<?php
declare(strict_types=1);

return [
    'name' => 'Balanço',
    'base_path' => '/balanco',
    'permission_process_balance' => 206,
    // Ações de Estoque > Locação configuradas no perfil do usuário.
    'location_permissions' => [
        'include' => 262,
        'update' => 263,
    ],
    'page_size' => 30,
    // Modelos Low Code permitidos na impressão auxiliar da contagem.
    // A chave pública nunca é usada como ID de modelo diretamente pelo navegador.
    'label_models' => [
        'produto_100x40' => [
            'name' => 'Produto 100x40',
            'model' => '0b21c4ea16c39ba82439aae2c02b5917',
            'id_type' => 'D001_Id',
            'context_field' => 'D001_Id',
        ],
        'locacao_100x40' => [
            'name' => 'Locação 100x40',
            'model' => '41e7bf859c5cf39ef9a8e14941b556e4',
            'id_type' => 'D004_Id',
            'context_field' => 'T066_D004_Id',
        ],
        'locacao_caixa_100x40' => [
            'name' => 'Locação Caixa 100x40',
            'model' => 'f8c5fa7021ceb9f5b8da5b6ff6ddfbf9',
            'id_type' => 'D001_Id',
            'context_field' => 'D001_Id',
        ],
    ],
];
