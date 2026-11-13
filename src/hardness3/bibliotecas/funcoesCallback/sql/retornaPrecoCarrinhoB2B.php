<?php
namespace hardness;
/**
* Este código PHP irá substituir o callback: retornaPrecoVenda()
*/

// Preço e demonstrativo
if($g['C031']['HabilitarPreco']=="N"){
    $row['carrinho_produto_preco'] = "Indisponível";
    $row['carrinho_preco_total'] = "Indisponível";
}


