<?php
namespace hardness;

   function formataPrecoB2B($array){
        global $g;
        return "<span> R$ ".gCorrigeNumero($array['todosCampos']['D009_Preco_1'])."</span>";
    }
    function precoProdutoB2B($array){
        global $g;
        return "<span> R$ ".gCorrigeNumero($array['todosCampos']['D009_Preco_1']*$array['todosCampos']['T269_Quantidade'])."</span>";
    }

    function valorTotalPedidoB2B($array){
        global $g;
        return "<span> R$ ".gCorrigeNumero($array['todosCampos']['T005_Valor_Total'])."</span>";
    }
    function valorTotalOrcamentoB2B($array){
        global $g;
        return "<span> R$ ".gCorrigeNumero($array['todosCampos']['T003_Valor_Total'])."</span>";
    }
    function valorTotalNotasB2B($array){
        global $g;
        return "<span> R$ ".gCorrigeNumero($array['todosCampos']['T007_Valor_Total'])."</span>";
    }
    function valorTotalBoletosB2B($array){
        global $g;
        return "<span> R$ ".gCorrigeNumero($array['todosCampos']['T002_Valor_Duplicata'])."</span>";
    }

?>
