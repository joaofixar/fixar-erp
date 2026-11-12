<?php
namespace hardness;

   function formataPrecoB2B($array){
        global $g;
        return gCorrigeNumero($array['todosCampos']['D009_Preco_1']);
    }
    function precoProdutoB2B($array){
        global $g;
        return gCorrigeNumero($array['todosCampos']['D009_Preco_1']*$array['todosCampos']['T269_Quantidade']);
    }

    function valorTotalPedidoB2B($array){
        global $g;
        return "R$".gCorrigeNumero($array['todosCampos']['T005_Valor_Total']);
    }
    function valorTotalOrcamentoB2B($array){
        global $g;
        return gCorrigeNumero($array['todosCampos']['T003_Valor_Total']);
    }
    function valorTotalNotasB2B($array){
        global $g;
        return "R$".gCorrigeNumero($array['todosCampos']['T007_Valor_Total']);
    }
    function valorTotalBoletosB2B($array){
        global $g;
        return "R$".gCorrigeNumero($array['todosCampos']['T002_Valor_Duplicata']);
    }

?>
