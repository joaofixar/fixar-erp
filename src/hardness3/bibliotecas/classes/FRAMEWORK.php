<?php
namespace hardness;

/**
 * Classe utilizada para partes do framework poderem ser personalizadas
 */
definirClasse('FRAMEWORK');
class FRAMEWORK_
{
    /**
     * montaPesquisaGrid
     *
     * @param string $pesquisa texto digitado na pesquisa
     * @param array $chave campos para pesquisa
     * @return array($where, $grid, $explode)
     */
    public function montaPesquisaGrid($pesquisa, $chaves)
    {
        global $r_campo11;
        $where              = '';
        $grid               = array();
        $r_pesquisa_explode = explode('|', $pesquisa);
        $campos11           = explode('||', $r_campo11);
        foreach ($chaves as $id => $campo) {
            $grid[] = array(
                'colunaValor' => $campo,
                'colunaTitulo' => 'Id',
                'chavePrimaria' => true,
                'colunaExibir' => false
            );
            if (!isset($r_pegarExtra)) {
                if (in_array($campo, $campos11)) {
                    continue;
                }
                foreach ($r_pesquisa_explode as $r_pesquisa_key => $r_pesquisa_item) {
                    $logica          = ($r_pesquisa_key == 0) ? 'OR (' : 'AND';
                    $r_pesquisa_item = str_replace(" ", "%", $r_pesquisa_item);
                    $where .= (empty($where)) ? "WHERE (({$campo} LIKE '%{$r_pesquisa_item}%'" : " {$logica} {$campo} LIKE '%{$r_pesquisa_item}%'";
                }
                $where .= ')';
            }
        }
        return array(
            $where,
            $grid,
            $r_pesquisa_explode
        );
    }
}