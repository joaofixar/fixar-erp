<?php
namespace hardness;

switch ($g['r'][3]) {

	case 'processarLayout':
		$resposta = array('code' => true, 'data' => array());

		$sql = "SELECT D150_Nome_Arquivo FROM D150 WHERE D150_Id = '{$r_D150_Id}'";
		$resD150 = mysql_query($sql);
		$rowD150 = mysql_fetch_assoc($resD150);

		$texto = '';
		$sql = "SELECT D151_Id,D151_Id_Pai FROM D151 WHERE D151_D150_Id = '{$r_D150_Id}' AND IFNULL(D151_Id_Pai,0) <= 0 ORDER BY D151_Ordem ASC";
		$res = mysql_query($sql);
		global $contadorGlobal;
		$contadorGlobal = 0;
		while ($row = mysql_fetch_assoc($res)) {
			$texto .= utf8_decode(processarLayout($row['D151_Id']));
		}

		$dir = $g['pathDados'] . 'tmp/';
        is_dir($dir) or mkdir($dir, 0777, true);
        $arquivo = "{$dir}{$rowD150['D150_Nome_Arquivo']}";
		file_put_contents($arquivo, $texto);

		$resposta['data'] = $r_D150_Id;

		echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";
	break;

	case 'download':
		$sql = "SELECT D150_Nome_Arquivo FROM D150 WHERE D150_Id = '{$r_D150_Id}'";
		$resD150 = mysql_query($sql);
		$rowD150 = mysql_fetch_assoc($resD150);
		$dir = $g['pathDados'] . 'tmp/';
		$arquivo = "{$dir}{$rowD150['D150_Nome_Arquivo']}";

		header("Content-Type: application/octet-stream");
		header("Content-Disposition: attachment; filename={$rowD150['D150_Nome_Arquivo']};");

		echo file_get_contents($arquivo);
	break;

	case 'testarSubLayout':
		// Simula vars vindo do form de exportação
		$r_Data_Inicial = '2014-10-01';
		$r_Data_Final = '2014-10-31';

		$D152_Id = isset($r_D152_Id) ? $r_D152_Id : false;
		$testar = isset($r_testar) ? true : false;
		$retorno = processarLayout($r_D151_Id, $D152_Id, true);
		output($retorno);
	break;

}

function processarLayout($D151_Id = false, $D152_Id = false, $teste = false, $registroPai = false) {
	global $g, $r_Data_Inicial, $r_Data_Final, $contadorGlobal;
	$retorno = '';
	//
	$sql = "SELECT * FROM D151 WHERE D151_Id = '{$D151_Id}'";
	$res = mysql_query($sql);
	$rowD151 = mysql_fetch_assoc($res);
	if ($teste) {
		$retorno .= "Iniciando <strong>{$rowD151['D151_Nome']}</strong> ... <br/><br/>";
	}
	$D151 = mysql_query("SELECT * FROM D151 WHERE D151_Id='{$D151_Id}'");
	$mD151 = mysql_fetch_array($D151);
	//
	if (empty($rowD151['D151_Select'])) {
		return '';
	}
	$limit = $teste ? ' LIMIT 500' : '';
	//$limit = ' LIMIT 500';
	$GROUPBY = explode("GROUP BY", $rowD151['D151_Select']);
	$extra_Editado='';
	if(!empty($rowD151['D151_Campo_Datas'])){
		$and='';
		if($registroPai > 0){
			$and="AND ".$mD151['D151_Where']."='".$registroPai."'";
		}
		$extra_Editado = $and." AND ".$rowD151['D151_Campo_Datas'] . " >= '" . $r_Data_Inicial . "' AND " . $rowD151['D151_Campo_Datas'] . " <= '" . $r_Data_Final."'"." GROUP BY ".$GROUPBY[1]. " ORDER BY ".$GROUPBY[1]." DESC";

		$rowD151['D151_Select'] = $GROUPBY[0];
	}


	eval("\$sql = \"{$rowD151['D151_Select']} {$extra_Editado} {$limit}\";");

	if($D151_Id=='3'){
		//die($sql);
	}
	$res = mysql_query($sql);
	if (!$res) {
		if ($teste) {
	    	$retorno .= 'Invalid query: ' . mysql_error() . '<br/>';
	    }
	    output($retorno);
	}
	$total = 0;
	while ($row = mysql_fetch_assoc($res)) {
		$linha = '';
		$total++;
		//$linha .= "{$total}: ";
		if ($D152_Id) {
			$sql = "SELECT * FROM D152 WHERE D152_Id = '{$D152_Id}' ORDER BY D152_Posicao_Inicio ASC";
		} else {
			$sql = "SELECT * FROM D152 WHERE D152_D151_Id = '{$rowD151['D151_Id']}' ORDER BY D152_Posicao_Inicio ASC";
		}
		$resD152 = mysql_query($sql);
		$contadorGlobal++;
		while ($rowD152 = mysql_fetch_assoc($resD152)) {
			// Fixo ou Select?
			if ($rowD152['D152_Valor_Fixo'] != '') {
				$valor = $rowD152['D152_Valor_Fixo'];
			} else if (!empty($rowD152['D152_Campo_Select'])) {
				if (!is_null($row[$rowD152['D152_Campo_Select']]) && !isset($row[$rowD152['D152_Campo_Select']])) {
					if ($teste) {
						$retorno .= "Campo <strong>{$rowD152['D152_Campo']}</strong>: <strong>{$rowD152['D152_Campo_Select']}</strong> não encontrado no select!";
					}
					output($retorno);
				}
				$valor = $row[$rowD152['D152_Campo_Select']];
			} else {
				$valor = '';
			}
			// Callbacks
			if (!empty($rowD152['D152_D153_Id']) || !empty($rowD152['D152_D153_2_Id'])) {
				$array = array(
					'campoAtual' => $valor,
					'todosCampos' => array_merge($rowD152, $row),
					'contadorGlobal' => $contadorGlobal
				);
			}
			// Executar Callback 1
			if (!empty($rowD152['D152_D153_Id'])) {
				$callback = mysql_fetch_assoc(mysql_query("SELECT D153_Callback FROM D153 WHERE D153_Id = '{$rowD152['D152_D153_Id']}'"));
				$code = <<<EOT
namespace hardness;
return call_user_func(function(\$array) {
{$callback['D153_Callback']}
}, \$array);
EOT;
				$valor = eval($code);

			}
			$array['campoAtual'] = $valor;
			// Executar Callback 2

			if (!empty($rowD152['D152_D153_2_Id'])) {
				$callback = mysql_fetch_assoc(mysql_query("SELECT D153_Callback FROM D153 WHERE D153_Id = '{$rowD152['D152_D153_2_Id']}'"));
				$code = <<<EOT
namespace hardness;
return call_user_func(function(\$array) {
{$callback['D153_Callback']}
}, \$array);
EOT;
				$valor = eval($code);
			}
			// 'Callback padrão' executado para todos os campos
			if ($rowD152['D152_Formato'] == 'X') {
				$valor = mb_str_pad($valor, $rowD152['D152_Tamanho'], ' ', STR_PAD_RIGHT);
			} else {
				if ($rowD152['D152_Decimal'] > 0) {
					$tmp = explode('.', $valor);
					if (count($tmp) > 1) {
						$decimal = $tmp[1];
						$decimal = mb_str_pad($decimal, $rowD152['D152_Decimal'], '0', STR_PAD_RIGHT);
						$decimal = mb_substr($decimal, 0, $rowD152['D152_Decimal']);
						if($decimal>0){
							//echo "tmp: ".$tmp[0]. " decimal: ".$decimal." Outro decimal: ".$rowD152['D152_Decimal']."<br /><br />";
						}
						$valor = $tmp[0] . $decimal;
					}
				}
				$valor = mb_str_pad($valor, $rowD152['D152_Tamanho']+$rowD152['D152_Decimal'], '0', STR_PAD_LEFT);
				if($decimal>0){
					//echo " Depois de STR_PAD->VALOR: ".$valor."<br /><br />";
				}
			}
			$valor = mb_substr($valor, 0, $rowD152['D152_Tamanho']+$rowD152['D152_Decimal']);
			if($decimal>0){
				//die(" Depois de mb-str->VALOR: ".$valor);
			}
			// 
			$linha .= $valor;
		}
		$len = mb_strlen($linha);
		if ($teste) {			
			$linha = str_replace(' ', '&nbsp;', $linha);
			$retorno .= "-> {$linha} <- (".$len.")<br/>";
		} else {
			$retorno .= "{$linha}\n";
		}
		if(!empty($mD151['D151_Where']) && (empty($mD151['D151_Id_Pai']) || $mD151['D151_Id_Pai']==0)) {
			$resSubLayout = mysql_query("SELECT D151_Id, D151_Id_Pai FROM D151 WHERE D151_Id_Pai = '{$mD151['D151_Id']}' AND D151_D150_Id = '{$mD151['D151_D150_Id']}' ORDER BY D151_Ordem ASC");
			while ($rowSubLayout = mysql_fetch_assoc($resSubLayout)) {
				
				$retorno .= utf8_decode(processarLayout($rowSubLayout['D151_Id'], false, false, $row[$mD151['D151_Where']]));
			}
		}
	}


	return $retorno;
}

function output($retorno) {
	global $r_D150_Id;
	if (isset($r_D150_Id)) {
		return "Erro gerando os dados! Por favor, entre em contato com o suporte técnico.\n";
	}
	echo <<<EOT
<div style="height: 100%; width: 100%; overflow: scroll; font-family: monospace; white-space: nowrap;">
	{$retorno}
</div>
EOT;
	die();
}
