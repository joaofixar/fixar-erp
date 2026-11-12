<?php
namespace hardness;

function campoPrecoTabelaOrcamento($array, $param){
	global $g;

	$T004_Id                   = $array['todosCampos']['T004_Id'];
	$T004_T219_Id              = $array['todosCampos']['T004_T219_Id'];
	$T219_Id                   = $array['todosCampos']['T219_Id'];
	$T004_Flag_Comprar         = $array['todosCampos']['T004_Flag_Comprar'];
	$D009_Quantidade_OC        = $array['todosCampos']['D009_Quantidade_OC'];
	$D009_Quantidade_Estoque   = $array['todosCampos']['D009_Quantidade_Estoque'];
	$D009_Valor_Custo_Tabela   = $array['todosCampos']['D009_Valor_Custo_Tabela'];
	$T004_Quantidade           = $array['todosCampos']['T004_Quantidade'];
	$D049_Flag_Tipo            = $array['todosCampos']['D049_Flag_Tipo'];
	$D070_Data_Vencimento      = $array['todosCampos']['D070_Data_Vencimento'];
	$T004_Flag_Transferencia   = $array['todosCampos']['T004_Flag_Transferencia'];
	$T004A_D184_Id 			   = $array['todosCampos']['T004A_D184_Id'];
	$D184_Quantidade_Conversao = $array['todosCampos']['D184_Quantidade_Conversao'];
	$D184_Tipo_Operacao        = $array['todosCampos']['D184_Tipo_Operacao'];

	if($T004A_D184_Id > 0){
		// Valida se tem unidade de conversão e faz o calculo
		if($D184_Tipo_Operacao == 'D'){
			$array['campoAtual'] = $array['campoAtual'] / $D184_Quantidade_Conversao;
		}elseif($D184_Tipo_Operacao == 'M'){
			$array['campoAtual'] = $array['campoAtual'] * $D184_Quantidade_Conversao;
		}
	}


	$T219 = mysql_query("SELECT T219_Valor_Preco_Venda FROM T219 WHERE T219_Id = '{$T004_T219_Id}'");
	$T219 = mysql_fetch_array($T219);

	require_once('bibliotecas/funcoesCallback/abrirNotificacao.php');
	$precoTabela = abrirNotificacao($array, $param);

	if($D049_Flag_Tipo == 'CP' && $g['C031']['desabilitarCotacaoEmergencia'] == 'N'){
		if(($D009_Quantidade_Estoque+$D009_Quantidade_OC) < $T004_Quantidade && $g['C031']['exibirMsgCotarOrcamento'] == 'S' && $T219_Id <= 0 && $T004_T219_Id <=0 && $T004_Flag_Comprar != 'S' && ($D009_Valor_Custo_Tabela <= 0 || strtotime($D070_Data_Vencimento) < strtotime(date('Y-m-d'))) && $T004_Flag_Transferencia != 'S'){
			return "<span style='background-color:#FF0000; color: black;'>Cotar</span>&nbsp;";
		} else if($T004_T219_Id > 0){ //Preço de venda foi atualizado atraves da cotação
			$precoTabela = str_replace("<a ", "<a style='background-color: #BADCFC; color: blue; font-weight: bold;' ", $precoTabela);
			if(empty($param) || !isset($g['c029Ids'][208])){
				return "<span style='background-color: #BADCFC; color: blue; font-weight: bold;'>".gCorrigeNumero($array['campoAtual'],4)."</span>";
			} else {
				return $precoTabela;
			}
		} else if($T219_Id <= 0 && $T004_Flag_Comprar == 'S'){
			if($D009_Valor_Custo_Tabela > 0 && strtotime($D070_Data_Vencimento) > strtotime(date('Y-m-d'))){
				if(empty($param) || !isset($g['c029Ids'][208])){
					return "<span style='background-color: #F7F774; font-weight: bold;'>".gCorrigeNumero($array['campoAtual'],4)."</span>";
				} else {
					$precoTabela = str_replace("<a ", "<a style='background-color: #F7F774; font-weight: bold;' ", $precoTabela);
					return $precoTabela;
				}
			} else {
				return "<span style='background-color:#F7F774; color: black;'>Aguarde</span>";
			}
		} else if($T219_Id > 0 && $T004_T219_Id <= 0){ //Existem cotações vinculadas a este item (T219_T004_Id > 0)
			return "<span style='background-color:#8AF774; color: blue;'>Cotando</span>";
		} else {
			if(empty($param) || !isset($g['c029Ids'][208])){
				$retorno = gCorrigeNumero($array['campoAtual'],4);
			} else {
				$retorno = $precoTabela;
			}
		}
	} else if($D049_Flag_Tipo == 'PD' && $T004_Flag_Comprar == 'S'){
		return "<span style='background-color:#F7F774; color: black;'>&nbsp;P.C.P&nbsp;</span>";
	} else {
		if(empty($param) || !isset($g['c029Ids'][208])){
			$retorno = gCorrigeNumero($array['campoAtual'],4);
		} else {
			$retorno = $precoTabela;
		}
	}
	if($T004_Flag_Transferencia == 'S'){
		return "<span style='background-color:#DAA5E5;'>".$retorno."</span>";;
	} else {
		return $retorno;
	}

	//Aguarde, Cotando, Preço Retornado em Cor Diferente
}
