<?php
namespace hardness;
function validaFlagBalanco($array){
	require_once('prog/est/est003/grid_func-js.php');
	global $g;
	$T066_Flag_Balanco = $array['todosCampos']['T066_Flag_Balanco'];
	$T066_Id = $array['todosCampos']['T066_Id'];
	$T066=mysql_query("SELECT T066_Id,T066_C007_Id_Marcou_Balanco FROM T066 WHERE T066_Id='{$T066_Id}'");;
	$mT066=mysql_fetch_array($T066);
	$disabled='';
	if($T066_Flag_Balanco=='S'){
		$C007 = mysql_query("SELECT C007_Primeiro_Nome from C007 WHERE C007_Id='{$mT066['T066_C007_Id_Marcou_Balanco']}'");
		$mC007=mysql_fetch_array($C007);
		if($mT066['T066_C007_Id_Marcou_Balanco'] != $g['usuarioAtual']){
			$disabled = "disabled='disabled'";
            $nome = substr($mC007['C007_Primeiro_Nome'], 0,8);
            $C007_Nome = <<<EOT
                <br /><font color='gray' style='font-size:8px;'>{$nome}</font>
EOT;
		}
	}

	$retorno = "<input onclick=\"est003CheckboxBalanco(this, '{$T066_Id}', '{$g['divId']}');\" type='checkbox' id='T066_Flag_Balanco' title='Balanço?'";
	if ($T066_Flag_Balanco == 'S'){
		return $retorno .="checked='checked' {$disabled} /> {$C007_Nome}";
	} else {
		return $retorno .= " />";
	}
}