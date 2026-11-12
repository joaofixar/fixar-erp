<?php
namespace hardness;

function campoPrecoTabelaGridOrcamento($array, $exibirCifrao = false){
	global $g;

	/* T003_Flag_Status_Cotacao - SIGNIFICADO
	* Z - Cotar
	* A - Aguarde
	* C - Cotando
	* T - Não possui estoque, mas possui preço de tabela fornecedor válido
	* P - Retornou preço de cotação/OC
	*/

	if(empty($array['todosCampos']['T003_Flag_Status_Cotacao'])){
		return ($exibirCifrao == true) ? 'R$ '.gCorrigeNumero($array['campoAtual']) : gCorrigeNumero($array['campoAtual']);
	} else if($array['todosCampos']['T003_Flag_Status_Cotacao'] == 'Z'){
		return "<span style='background-color:#FF0000; color: black;'>Cotar</span>&nbsp;";
	} else if($array['todosCampos']['T003_Flag_Status_Cotacao'] == 'A'){
		return "<span style='background-color:#F7F774; color: black;'>Aguarde</span>";
	} else if($array['todosCampos']['T003_Flag_Status_Cotacao'] == 'C'){
		return "<span style='background-color:#8AF774; color: blue;'>Cotando</span>";
	} else if($array['todosCampos']['T003_Flag_Status_Cotacao'] == 'T'){
		return (($exibirCifrao == true) ? 'R$' : '') . "<span style='background-color: #F7F774;'> ".gCorrigeNumero($array['campoAtual'])."</span>";
	} else if($array['todosCampos']['T003_Flag_Status_Cotacao'] == 'P'){
		return (($exibirCifrao == true) ? 'R$ ':'')."<span style='background-color: #BADCFC; color: blue;'> ".gCorrigeNumero($array['campoAtual'])."</span>";
	}
}
