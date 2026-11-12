<?php
namespace hardness;

$resposta = array('code' => true, 'data' => array());
/**
 * Ações
 */
switch($g['r'][3]) {
	case 'preencheCampos':
		$T117_D007_Id = isset($r_T117_D007_Id) ? $r_T117_D007_Id : false;
		$valorTotal = isset($r_valorTotal) ? $r_valorTotal : false;

		$sql = "SELECT * 
				 FROM D007 
				WHERE D007_Id = '{$T117_D007_Id}';";
		
		$resultado = mysql_query($sql);
		$row 	   = mysql_fetch_assoc($resultado);

		$mInstrucao        = array();
		$mensagemJurosMultas = '';

		if($row['D007_Flag_Tipo_Multa'] != "P") {
			//Valores exibidos em REAIS
			//-- INSTRUCOES
			$nJuros_Dia 	   = round((($row['D007_Percentual_Juros_Dia']/30)/100)*$valorTotal,2);
			$nMulta_Vencimento = round(($row['D007_Percentual_Multa']/100)*$valorTotal,2);

			if ($nMulta_Vencimento>0){
			   $mInstrucao[] = " Multa de R$ ".number_format($nMulta_Vencimento,2,',','.')." ";
			}

			if ($nJuros_Dia>0){
			   $mInstrucao[] = " Juros de R$ ".number_format($nJuros_Dia,2,',','.')." ao Dia.";
			}

			if ($nJuros_Dia>0 or $nMulta_Vencimento>0){
				$mensagemJurosMultas = 'Após o Vencimento Cobrar '.implode("e",$mInstrucao);
			}

		} else {

			$nJuros_Dia 	   = $row['D007_Percentual_Juros_Dia'];
			$nMulta_Vencimento = $row['D007_Percentual_Multa'];

			if ($nMulta_Vencimento>0){
			   $mInstrucao[] = " Multa de ".$nMulta_Vencimento."% ";
			}

			if ($nJuros_Dia>0){
			   $mInstrucao[] = " Juros de ".$nJuros_Dia."% ao mês.";
			}

			if ($nJuros_Dia>0 or $nMulta_Vencimento>0){
				$mensagemJurosMultas  = 'Após o Vencimento Cobrar '.implode("e",$mInstrucao);
			} 
		}
		
		$resposta['code']							   = true;
		$resposta['data']['T117_Percentual_Multa']     = gCorrigeNumero($row['D007_Percentual_Multa']);
		$resposta['data']['T117_Percentual_Juros_Dia'] = gCorrigeNumero($row['D007_Percentual_Juros_Dia']);
		$resposta['data']['T117_Instrucoes'] 		   = $row['D007_Instrucoes_Boleto'];
		$resposta['data']['mensagemJurosMultas'] 	   = $mensagemJurosMultas;

	break;		
}
echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

?>