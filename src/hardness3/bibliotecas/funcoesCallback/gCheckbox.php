<?php
namespace hardness;
/* $array (array) todos os campos da linha
* $param (array)[0] - nome do campo
*				[1] - N|S ou 0|1 - Define os valores booleanos aceitos
* 				[2] - C007_Id - Campo preenchido com o $g['usuarioAtual']
				[3] - Restricao Tabela
				[4] - Refresh no grid: 'full' recarrega todo o grid, 'no' nao recarrega;
				      vazio ou nao informado recarrega somente a linha do registro
*/
function gCheckbox($array, $param){
	global $g;
	$campoUpdate = $param[0];
    $tabela = substr($campoUpdate, 0, strpos($campoUpdate, '_'));
	$tabelaPk = $array['todosCampos'][$tabela.'_Id'];
	$valoresValidos = $param[1];
	$campo = $array['campoAtual'];
	$uniq = uniqid();
	$campoC007 = !empty($param[2]) ? $param[2] : '';
	$checkRestricao = !empty($param[3]) ? true : false;
	$refresh = (isset($param[4]) && is_string($param[4]) && in_array(strtolower($param[4]), array('full', 'no'))) ? strtolower($param[4]) : '';

	if($checkRestricao){
		$restricao = $g['conf']->restricaoTravar('alterar', $tabela.'_Id', $tabelaPk);
		if ($restricao !== true) {
			return '';
		}
	}

	$retorno = "<input onclick=\"globalCheckbox(this, '{$tabelaPk}', '{$campoUpdate}', '{$valoresValidos}', '{$g['divId']}', '{$campoC007}', '{$refresh}');\" type='checkbox' id='{$uniq}' title='Alterar registro'";
	$valoresValidos = explode('|', $valoresValidos);
	if ($campo == $valoresValidos[1]){
		if($array['todosCampos'][$campoC007] != $g['usuarioAtual'] && $array['todosCampos'][$campoC007] > 0){
            $C007 = mysql_fetch_array(mysql_query("SELECT C007_Primeiro_Nome FROM C007 WHERE C007_Id='{$array['todosCampos'][$campoC007]}'"));
            $disabled = "disabled='disabled'";
            $nome = substr($C007['C007_Primeiro_Nome'], 0,8);
            $nome = "<font color='gray' style='font-size:8px;'>{$nome}</font>";
        }
        $retorno .="checked='checked'";
        return $retorno .= " {$disabled}/><br />{$nome}";
	} else {
		return $retorno .= " />";
	}
}