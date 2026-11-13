<?php

namespace hardness;

definirClasse('BALANCO');

/**
 * Personalização central do módulo de Balanço.
 * Retorne apenas as chaves que deseja sobrescrever.
 */
class BALANCO_
{
    /**
     * Permite personalizar a configuração do módulo por empresa, inclusive
     * os modelos de etiqueta disponíveis na tela de contagem.
     *
     * Para adicionar uma etiqueta personalizada na nuvem, retorne:
     *
     * return [
     *     'label_models' => [
     *         'etiqueta_personalizada' => [
     *             // Texto exibido no botão "Imprimir etiqueta".
     *             'name' => 'Etiqueta personalizada',
     *
     *             // Apenas o ID de 32 caracteres do modelo Low Code.
     *             // Ex.: /sistema/personalizacao/tela/f72efe5946b0fb19f2fd66938c989f4d/
     *             'model' => 'f72efe5946b0fb19f2fd66938c989f4d',
     *
     *             // Define o parâmetro enviado ao modelo. O Balanço busca o
     *             // valor automaticamente: D001_Id=produto, D004_Id=locação
     *             // e T066_Id=registro da contagem.
     *             'id_type' => 'D001_Id',
     *         ],
     *     ],
     * ];
     *
     * Não é necessário repetir os modelos padrão: esta configuração é
     * mesclada a eles e a nova etiqueta aparece automaticamente na tela.
     *
     * Para ocultar uma etiqueta já existente apenas da tela de impressão,
     * informe sua chave com mostrar=false. Exemplos:
     *
     * return [
     *     'label_models' => [
     *         'produto_100x40' => ['mostrar' => false],
     *         'locacao_100x40' => ['mostrar' => false],
     *         'locacao_caixa_100x40' => ['mostrar' => false],
     *     ],
     * ];
     *
     * @param array $currentConfig
     * @return array
     */
    public static function overrides($currentConfig)
    {
        return [];
    }
}
