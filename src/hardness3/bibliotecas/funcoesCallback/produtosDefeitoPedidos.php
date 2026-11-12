<?php
namespace hardness;
function produtosDefeitoPedidos($array){
        global $g;
        $T206_Id = $array['todosCampos']['T206_Id'];
        $T006=mysql_query("SELECT SUM(T006_Quantidade) as Pedidos FROM T206 LEFT JOIN T006 ON T206_Id=t006_t206_id  LEFT JOIN D006 ON D006_Id=T006_D006_Id LEFT JOIN D169 ON D169_Id=T206_D169_Id WHERE T006_T206_Id = '{$T206_Id}' and D169_Sigla_Tipo_Produto='{$array['todosCampos']['D169_Sigla_Tipo_Produto']}';");
        $pedidos = mysql_fetch_array($T006);

        if($pedidos['Pedidos'] > 0){
                $retorno = gCorrigeNumero($pedidos['Pedidos']);
        } else {
                $retorno = '0,00';
        }
        return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Pedidos', '/ven/ven002/content/ven002ContentProdutosDefeitos/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['T206_Id']}'), [700,400]); return false;\">{$retorno}</a>";
}
