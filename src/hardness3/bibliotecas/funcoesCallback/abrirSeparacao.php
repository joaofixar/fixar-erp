<?php
namespace hardness;

function abrirSeparacao($array){
    global $g;
    $valor = $array['todosCampos']['T006_Quantidade_Separacao'];
    if($g['usuarioAtual'] == 63){
        $retorno = "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Separação', '/est/est002/content/est002SeparacaoPorLocacao/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['T005_Id']}'), [900,550]); return false;\">{$valor}</a>";
    }else{
        $retorno = $valor;
    }
    return $retorno;
}





