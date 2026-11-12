<?php
namespace hardness;

function validaCampoMensagensSuporte($array) {
	$boxShadow = "-webkit-box-shadow: -1px 1px 3px 0px rgba(50, 50, 50, 0.50); -moz-box-shadow: -1px 1px 3px 0px rgba(50, 50, 50, 0.50); box-shadow: -1px 1px 3px 0px rgba(50, 50, 50, 0.50);";
	$retorno = '<div style="width:100%; white-space:normal; margin-bottom: 15px; font-family: Helvetica; font-weight: normal; color: #000;">';


	$interacoes = array();
	$sqlT252 = mysql_query("SELECT CONCAT(T252_Descricao, '[|]', IF(T252_C007_Id_Cliente > 0, 0, 1), '[|]', T252_Usuario, '[|]', T252_Data_Hora, '[|]', T234_D024_Id, '[|]', SUBSTRING(D024_Nome_Empresa,1,20), '[|]', T252_Flag_Finalizacao, '[|]', T252_Flag_Lido) Texto FROM T252 LEFT JOIN T234 ON T234_Id=T252_T234_Id LEFT JOIN D024 ON D024_Id=T234_D024_Id WHERE T252_T234_Id = '{$array['todosCampos']['T234_Id']}' ORDER BY T252_Data_Hora");
	while($rowT252 = mysql_fetch_array($sqlT252)){
		$texto = $rowT252['Texto'];
		$texto = str_replace("&NBSP;", "&nbsp;", $texto);
		$texto = str_replace(array("<div><br></div>", "<DIV><BR></DIV>"), " ", $texto);
		$texto = strip_tags($texto);
		$interacoes[] = $texto;
	}

	$arrayInteracoes = $interacoes;
	//Verifica a posição da última interação do suporte e do cliente
	foreach($arrayInteracoes as $id => $interacao){
		if($id == 0) { continue; }
		$dados = explode('[|]', $interacao);
		if($dados[1] == 0) {
			$ultimaInteracaoCliente = $id;
		}
		if($dados[1] == 1 && $dados[6] == 'N') {
			$ultimaInteracaoSuporte = $id;
		}
		if($dados[1] == 1 && $dados[6] == 'S') {
			$finalizacaoSuporte = $id;
		}
	}
	foreach ($interacoes as $id => $interacao) {
		$dados = explode('[|]', $interacao);
		$data = date("d/m/Y H:i:s", strtotime($dados[3]));
		$dados[5] = utf8_encode($dados[5]);
		if($id == 0){
			if($dados[4] == '520') {
				$cor = '#FFF';
				$ladoMargin = 'left';
			} else {
				$cor = '#C1FFB6';
				$ladoMargin = 'right';
			}
			$retorno .= <<<EOT
				<div style="box-sizing:border-box; background-color: {$cor}; border-radius: 3px; min-height: 23px; padding: 3px; 2px; color: #222; margin-{$ladoMargin}: 20px; {$boxShadow}">
					<div style="font-size: 11px; margin-bottom: 3px;">{$data} - {$dados[2]} - {$dados[5]}</div>
					{$dados[0]}
				</div>
EOT;
			continue;
		}
		//Última interação do suporte
		if($dados[1] == 1 && $id == $ultimaInteracaoSuporte){
			log("Msg lida = ".$dados[7]);
			if($dados[7] == 'S'){
				$msglida = <<<EOT
				<div style= "color: #3333ff">Lida &#10004;</div>
EOT;
		} else {
			$msglida =<<<EOT
			<div style= "color: #e60000">Não Lida &#x2716;</div>
EOT;
		}
			$respostaSuporte = true;
			$retorno .= <<<EOT
				<div style="box-sizing:border-box; margin: 5px 1px 1px 20px; background-color: #FFF; border-radius: 3px; min-height: 23px; padding: 3px; 2px; {$boxShadow}">
					<div style="font-size: 11px; font-family: Helvetica; color: #222; font-weight: normal; margin-bottom: 3px;">{$data} - {$dados[2]}</div>
					{$dados[0]}
					{$msglida}
				</div>
EOT;
		}
		//Mensagem de finalização
		if($dados[1] == 1 && $id == $finalizacaoSuporte){
			$respostaSuporte = true;
			$retorno .= <<<EOT
				<div style="box-sizing:border-box; margin: 5px 1px 1px 20px; background-color: #FFF; border-radius: 3px; min-height: 23px; padding: 3px; 2px; {$boxShadow}">
					<div style="font-size: 11px; font-family: Helvetica; color: #222; font-weight: normal; margin-bottom: 3px;">{$data} - {$dados[2]}</div>
					{$dados[0]}
				</div>
EOT;
		}
		//Última interação do cliente
		if($dados[1] == 0 && $id == $ultimaInteracaoCliente){
			$respostaCliente = true;
			$retorno .= <<<EOT
				<div style="box-sizing:border-box; margin: 5px 20px 1px 1px; background-color: #C1FFB6; border-radius: 3px; min-height: 23px; padding: 3px; 2px; {$boxShadow}">
					<div style="font-size: 11px; font-family: Helvetica; color: #222; font-weight: normal; margin-bottom: 3px;">{$data} - {$dados[2]} - {$dados[5]}</div>
					{$dados[0]}
				</div>
EOT;
		}
	}
	$retorno .= '</div>';
	return $retorno;
}