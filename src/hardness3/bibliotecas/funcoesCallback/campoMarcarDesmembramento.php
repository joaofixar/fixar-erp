<?php
namespace hardness;
function campoMarcarDesmembramento($array) {
	global $g;
	if($array['todosCampos']['T216_Id'] > 0){
		if($array['todosCampos']['T015_Data_Pagamento'] != '0000-00-00'){
			return $array['todosCampos']['T216_T215_Id'];
		} else {
			$T015_Id=$array['todosCampos']['T015_Id'];
			$T216=mysql_query("SELECT * FROM T216 WHERE T216_T015_Id='{$T015_Id}'");
			if(mysql_num_rows($T216) > 1){
				$retorno = '';
				while($mT216=mysql_fetch_array($T216)){
					$retorno .= "<a href=\"#\" onclick=\"removerVinculoTransacao('{$mT216['T216_Id']}', '{$g['divId']}'); return false;\">{$mT216['T216_T215_Id']}</a> ";
				}
				return $retorno;
			}else{
				return "<a href=\"#\" onclick=\"removerVinculoTransacao('{$array['todosCampos']['T216_Id']}', '{$g['divId']}'); return false;\">{$array['todosCampos']['T216_T215_Id']}</a>";
			}
		}
	} else if($array['todosCampos']['T015_Data_Pagamento'] != '0000-00-00'){
		return '';
	}
	$T015_Id = $array['todosCampos']['T015_Id'];
	$T015_Flag_Marcar_Lancamento = $array['todosCampos']['T015_Flag_Marcar_Lancamento'];
	$retorno = "<span><input onclick=\"campoMarcarDesmembramento(this, '{$T015_Id}', '{$g['divId']}')\" type='checkbox' id='T015_Flag_Marcar_Lancamento' title='Marcar Título a Pagar'";
	if ($array['todosCampos']['T015_Flag_Marcar_Lancamento'] == 'S'){
	    $retorno .="checked='checked'";
	}
	return $retorno .= " /></span>";
}