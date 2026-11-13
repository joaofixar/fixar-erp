<?php
namespace hardness;

function campoPrecoTabelaGridPedido($array, $exibirCifrao = false){
	global $g;

	/* T005_Flag_Status_Cotacao - SIGNIFICADO
	* A - Aguarde
	* C - Cotando
	* T - Não possui estoque, mas possui preço de tabela fornecedor válido
	* P - Retornou preço de cotação/OC
	*/

	if(empty($array['todosCampos']['T005_Flag_Status_Cotacao'])){
		return (($exibirCifrao == true) ? 'R$ ' : '') . gCorrigeNumero($array['campoAtual']);
	} else if($array['todosCampos']['T005_Flag_Status_Cotacao'] == 'A') {
		return "<span style='background-color:#F7F774; color: black;'>Aguarde</span>";
	} else if($array['todosCampos']['T005_Flag_Status_Cotacao'] == 'C'){
		return "<span style='background-color:#8AF774; color: blue;'>Cotando</span>";
	} else if($array['todosCampos']['T005_Flag_Status_Cotacao'] == 'T'){
		return (($exibirCifrao == true) ? 'R$' : '') . "<span style='background-color: #F7F774;'> ".gCorrigeNumero($array['campoAtual'])."</span>";
	} else if($array['todosCampos']['T005_Flag_Status_Cotacao'] == 'P'){
		return (($exibirCifrao == true) ? 'R$' : '') . "<span style='background-color: #BADCFC; color: blue;'> ".gCorrigeNumero($array['campoAtual'])."</span>";
	}
}