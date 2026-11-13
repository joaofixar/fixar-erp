<?php
namespace hardness;

/**
* qtePedidos
*
* @var $array
* @var $opcoes = array(
* 	abrirLocacoes - abre com foco na segunda aba (Locações)
*	valorMenor - pode ser número ou um campo (se for menor que este valor, ficará em vermelho)
* )
*	
*/
function qtePedidosDI($array){
        global $g;
        $retorno=mysql_query("SELECT sum(T006_Quantidade) as Quantidade FROM T006
                        left join T005 on T005_Id=T006_T005_Id
                        LEFT JOIN T075 ON T075_Id=T006_T075_Id
                        WHERE T005_Flag_Status!=5
                        and T006_Flag_Status!='3'
                        and T005_Flag_Status!='7'
                        and T005_Flag_Status!='8'
                        and T005_Flag_Status!='11'
                        AND T006_T075_Id='{$array['todosCampos']['T075_Id']}'
                        AND T006_D009_Id='{$array['todosCampos']['D009_Id']}'");
        $retorno=mysql_fetch_array($retorno);
        return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}' , unique(), '', 'Pedidos', '/imp/imp003/content/imp003ContentPedidosProduto/', '&acaoId=' + encodeURIComponent({$array['todosCampos']['T055_Id']}), [720,420]);\"> <font style='font-weight:bold'>".$retorno['Quantidade']."</font></a>";
}

