<?php

namespace hardness;

definirClasse('TABLETSEPARACAO');

/**
 * Personalização central do tablet_separacao.
 * Retorne apenas as chaves que deseja sobrescrever.
 */
class TABLETSEPARACAO_
{
    /**
     * @param array $currentConfig
     * @return array
     */
    public static function overrides($currentConfig)
    {
        /**
         * Exemplos de personalizações possíveis:
         *
         * return [
         *     // Exibir ou ocultar botão de etiqueta do item na tela do pedido
         *     'mostrar_etiqueta_item_no_pedido' => false,
         *
         *     // Entrada de locação: permite digitação manual
         *     'locacao_permite_digitacao' => true,
         *
         *     // Leitura por câmera no campo de locação
         *     'locacao_permite_camera' => true,
         *
         *     // Modo de exibição da locação: lista ou combo
         *     'locacao_exibicao' => 'combo',
         *
         *     // Permitir excluir separações do histórico do item
         *     'permitir_excluir_separacao' => false,
         * ];
         */

        // Padrão: sem sobrescrita.
        return [];
    }

    /**
     * Permite que cada cliente acrescente informações aos itens carregados pelo
     * tablet sem alterar a consulta e o comportamento padrão do módulo.
     *
     * @param int   $T005_Id
     * @param array $items
     * @return array
     */
    public static function personalizarItensPedido($T005_Id, $items)
    {
        return $items;
    }

    /**
     * Permite tratar retornos específicos da finalização. Retornar null mantém
     * integralmente o comportamento padrão do tablet.
     *
     * @param mixed $retorno
     * @return array|null
     */
    public static function tratarRetornoFinalizacao($retorno)
    {
        return null;
    }
}
