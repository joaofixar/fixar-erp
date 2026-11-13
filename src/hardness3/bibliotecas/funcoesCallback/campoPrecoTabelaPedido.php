<?php
namespace hardness;

function campoPrecoTabelaPedido($array, $param){
	global $g;
	$T006_Id 				   = $array['todosCampos']['T006_Id'];
	$T006_T006_Id_Dividir 	   = $array['todosCampos']['T006_T006_Id_Dividir'];
	$T004_Id 				   = $array['todosCampos']['T004_Id'];
	$T006_T219_Id 			   = $array['todosCampos']['T006_T219_Id'];
	$T006_T225_Id 			   = $array['todosCampos']['T006_T225_Id'];
	$T004_T219_Id 			   = $array['todosCampos']['T004_T219_Id'];
	$T006_Quantidade_Estoque   = $array['todosCampos']['T006_Quantidade_Estoque'];
	$T006_Quantidade_Estoque   = ($T006_Quantidade_Estoque > 0) ? $T006_Quantidade_Estoque : 0;
	$T006_Quantidade 		   = $array['todosCampos']['T006_Quantidade'];
	$T006_Quantidade_OC 	   = $array['todosCampos']['T006_Quantidade_OC'];
	$D049_Flag_Tipo 		   = $array['todosCampos']['D049_Flag_Tipo'];
	$T006_Flag_Transferencia   = $array['todosCampos']['T006_Flag_Transferencia'];
	$D009_Valor_Custo_Tabela   = $array['todosCampos']['D009_Valor_Custo_Tabela'];
	$D070_Data_Vencimento 	   = $array['todosCampos']['D070_Data_Vencimento'];
	$T006A_D184_Id 			   = $array['todosCampos']['T006A_D184_Id'];
	$D184_Quantidade_Conversao = $array['todosCampos']['D184_Quantidade_Conversao'];
	$D184_Tipo_Operacao        = $array['todosCampos']['D184_Tipo_Operacao'];

	if($T006A_D184_Id > 0){
		// Valida se tem unidade de conversão e faz o calculo
		if($D184_Tipo_Operacao == 'D'){
			$array['campoAtual'] = $array['campoAtual'] / $D184_Quantidade_Conversao;
		}elseif($D184_Tipo_Operacao == 'M'){
			$array['campoAtual'] = $array['campoAtual'] * $D184_Quantidade_Conversao;
		}
	}

	$T219 = mysql_query("SELECT T219_Valor_Preco_Venda, IFNULL(T219_Id, 0) AS T219_Id FROM T219 WHERE (T219_T006_Id = '{$T006_Id}') AND IF('{$T006_T219_Id}' > 0, T219_Id='{$T006_T219_Id}', 1=1)");
	$T219 = mysql_fetch_array($T219);

	$T225 = mysql_query("SELECT T225_Valor_Preco_Venda, IFNULL(T225_Id, 0) AS T225_Id FROM T225 WHERE (T225_T006_Id = '{$T006_Id}') AND IF('{$T006_T225_Id}' > 0, T225_Id='{$T006_T225_Id}', 1=1)");
	$T225 = mysql_fetch_array($T225);

	require_once('bibliotecas/funcoesCallback/abrirNotificacao.php');
	$precoTabela = abrirNotificacao($array, $param);

	if($D049_Flag_Tipo == 'CP' && $g['C031']['desabilitarCompraEmergencia'] == 'N') {
		if(($T006_Quantidade_Estoque+$T006_Quantidade_OC) < $T006_Quantidade && $T219['T219_Id'] <= 0 && $T225['T225_Id'] <= 0 && $T006_Flag_Transferencia != 'S'){
			//Quando o estoque for insuficiente e não houver cotação nem ordem de compra vinculados ao pedido, exibimos aguarde
			if($D009_Valor_Custo_Tabela > 0 && strtotime($D070_Data_Vencimento) >= strtotime(date('Y-m-d'))) {
				if(empty($param) || !isset($g['c029Ids'][208])){
					return "<span style='background-color: #F7F774;'>".gCorrigeNumero($array['campoAtual'],4)."</span>";
				} else {
					$precoTabela = str_replace("<a ", "<a style='background-color: #F7F774; font-weight: bold;' ", $precoTabela);
					return $precoTabela;
				}
			} else {
				return "<span style='background-color:#F7F774; color: black;'>Aguarde</span>";
			}
		} else if(($T219['T219_Id'] > 0 || $T225['T225_Id'] > 0) && ($T006_T219_Id <= 0 && $T006_T225_Id <= 0)) {
			return "<span style='background-color:#8AF774; color: blue;'>Cotando</span>";
		} else if($T006_T219_Id > 0 || $T006_T225_Id > 0){
			$precoTabela = str_replace("<a ", "<a style='background-color: #BADCFC; color: blue; font-weight: bold;' ", $precoTabela);
			if(empty($param) || !isset($g['c029Ids'][208])){
				return "<span style='color: blue;background-color: #BADCFC;'>".gCorrigeNumero($array['campoAtual'],4)."</span>";
			} else {
				return $precoTabela;
			}
		} else {
			if(empty($param) || !isset($g['c029Ids'][208])){
				$retorno = gCorrigeNumero($array['campoAtual'],4);
			} else {
				$retorno = $precoTabela;
			}
		}

	} else {
		if(empty($param) || !isset($g['c029Ids'][208])){
			$retorno = gCorrigeNumero($array['campoAtual'],4);
		} else {
			$retorno = $precoTabela;
		}
	}
	if($T006_Flag_Transferencia == 'S'){
		return "<span style='background-color:#DAA5E5;'>".$retorno."</span>";;
	} else {
		return $retorno;
	}
}
