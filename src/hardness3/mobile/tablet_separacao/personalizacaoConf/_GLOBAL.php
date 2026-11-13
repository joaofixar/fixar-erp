<?php
declare(strict_types=1);

return [
    /**
     * Personalização do tablet_separacao:
     * - Este arquivo (_GLOBAL.php) é a base padrão.
     * - O cliente pode sobrescrever somente partes específicas criando UM destes arquivos:
     *   1) personalizacaoConf/<BANCO>.php
     *   2) personalizacaoConf/<BANCO>.tablet_separacao.php
     *   3) personalizacaoConf/tablet_separacao/<BANCO>.php
     * - Opcionalmente, também é possível usar classe em:
     *   bibliotecas/classes/TABLETSEPARACAO.php
     *   com classe TABLETSEPARACAO e método estático overrides($currentConfig).
     * - O merge é recursivo para arrays associativos (sobrescreve apenas as chaves enviadas).
     * - Arrays numéricos (listas) são substituídos por completo.
     */

    // Entrada da locação:
    // false = força scanner/câmera
    // true = permite digitação manual no campo
    'locacao_permite_digitacao' => false,

    // Habilita leitura pela câmera no campo de locação
    'locacao_permite_camera' => true,

    // Campo usado para identificar a locação ao bipar/ler
    // Ex.: D004_Local, T066_Id, etc.
    'locacao_busca_por' => 'D004_Local',

    // Forma de entrada/exibição da locação no item
    // lista = campo de bip + lista visual das locações
    // combo = select com todas as locações do item
    'locacao_exibicao' => 'lista',

    // Ordenação dos itens na tela do pedido
    // locacao = ordena por D004_Local/T006_Id
    'ordenacao_itens' => 'locacao',

    // Filtros da fila
    'mostrar_busca_pedidos' => true,
    'mostrar_filtro_emissao' => true,
    'mostrar_filtro_entrega' => true,
    'mostrar_filtro_separador' => true,

    // Fila "Iniciar separação"
    // true = mostra somente pedidos sem data de entrega ou com entrega ate hoje
    'fila_iniciar_filtrar_entrega_ate_hoje' => false,

    // Campos visíveis nos cards da fila
    'mostrar_vendedor' => true,
    'mostrar_transportadora' => true,
    'mostrar_observacao' => true,
    'mostrar_percentual_separado' => true,
    'mostrar_quem_esta_separando' => true,

    // Permite excluir uma separação já lançada no histórico do item
    'permitir_excluir_separacao' => true,

    // Etiquetas disponíveis nos itens da tela do pedido.
    // Futuramente esta estrutura pode vir de uma configuração web/banco.
    'etiquetas_pedido' => [
        [
            'nome' => 'Produtos pedido 100x40',
            'opcoes' => [
                [
                    'nome' => 'Simples',
                    'modelo' => '5118080fd58fd6020ce08b1de8a0c67d',
                    'id_type' => 'T005_Id',
                    'mostrar' => true,
                ],
                [
                    'nome' => 'Completa',
                    'modelo' => '0e3cf1835a7be44870bd9b43c653bff6',
                    'id_type' => 'T005_Id',
                    'mostrar' => true,
                ],
            ],
        ],
        [
            'nome' => 'Produto Pedido Separação 100x40',
            'opcoes' => [
                [
                    'nome' => 'Simples',
                    'modelo' => '5118080fd58fd6020ce08b1de8a0c67d',
                    'id_type' => 'separacaoT005',
                    'mostrar' => false,
                ],
                [
                    'nome' => 'Completa',
                    'modelo' => '0e3cf1835a7be44870bd9b43c653bff6',
                    'id_type' => 'separacaoT005',
                    'mostrar' => false,
                ],
            ],
        ],
    ],

    // Exibe o botão de impressão de etiqueta do item na listagem de itens do pedido (pedido.php)
    // A impressão no item.php continua independente desta opção
    'mostrar_etiqueta_item_no_pedido' => false,

    'etiquetas_item_pedido' => [
        [
            'nome' => 'Produtos pedido 100x40',
            'opcoes' => [
                [
                    'nome' => 'Simples',
                    'modelo' => '5118080fd58fd6020ce08b1de8a0c67d',
                    'id_type' => 'T006_Id',
                    'mostrar' => true,
                ],
                [
                    'nome' => 'Completa',
                    'modelo' => '0e3cf1835a7be44870bd9b43c653bff6',
                    'id_type' => 'T006_Id',
                    'mostrar' => true,
                ],
            ],
        ],
        [
            'nome' => 'Produtos Pedido Separação 100x40',
            'opcoes' => [
                [
                    'nome' => 'Simples',
                    'modelo' => '5118080fd58fd6020ce08b1de8a0c67d',
                    'id_type' => 'separacaoT006',
                    'mostrar' => false,
                ],
                [
                    'nome' => 'Completa',
                    'modelo' => '0e3cf1835a7be44870bd9b43c653bff6',
                    'id_type' => 'separacaoT006',
                    'mostrar' => false,
                ],
            ],
        ],
    ],
];
