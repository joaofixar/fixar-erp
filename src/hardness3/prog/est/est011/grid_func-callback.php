<?php
namespace hardness;

function retornaStatus($array) {
	global $g;
	if($array['todosCampos']['T182_Status'] == 'P'){
				return "Pendente";
	}
	elseif($array['todosCampos']['T182_Status'] == 'R'){
				return "Em Remessa";
	}
}

function retornaTipo($array) {
	global $g;
	if($array['todosCampos']['T182_Tipo'] == 'A'){
		return "Avaria";
	} elseif ($array['todosCampos']['T182_Tipo'] == 'T'){
		return "Troca";
	} elseif ($array['todosCampos']['T182_Tipo'] == 'DV'){
		return "Devolução";
	} elseif ($array['todosCampos']['T182_Tipo'] == 'DT'){
		return "Desistência";
	} elseif ($array['todosCampos']['T182_Tipo'] == 'DC'){
		return "Desconto";
	}
}

function retornaTipoItemRemessa($array) {
	global $g;
	if($array['todosCampos']['T184_Tipo'] == 'A'){
		return "Avaria";
	} elseif ($array['todosCampos']['T184_Tipo'] == 'T'){
		return "Troca";
	} elseif ($array['todosCampos']['T184_Tipo'] == 'DV'){
		return "Devolução";
	} elseif ($array['todosCampos']['T184_Tipo'] == 'DT'){
		return "Desistência";
	} elseif ($array['todosCampos']['T184_Tipo'] == 'DC'){
		return "Desconto";
	}
}

function validaCampoMarcado($array){
	global $g;	
		if ($array['todosCampos']['T182_Status']!='P'){
			return "OK";	
		}
		$retorno = "<input type='checkbox' id='T182_Marcado' title='Marcar produto' onChange=\"est011CheckboxMarcado(this, '{$array['todosCampos']['T182_Id']}', '{$g['divId']}')\" ";		
		if($array['todosCampos']['T182_Marcado']=='S'){
			$retorno .="checked='checked'";
		}
			return $retorno .= " />";
	
}

function retornaIdRemessa($array){
		$resultado=mysql_query("select * from T184 where T184_T182_Id='{$array['todosCampos']['T182_Id']}'");
    	$row=mysql_fetch_array($resultado);
		return $row['T184_T183_Id'];
}

function retornaFornecedorRemessa($array){
		$resultado=mysql_query("select * from T184 left join T183 on T183_Id=T184_T183_Id left join D024 on D024_Id=T183_D024_Id where T184_T182_Id='{$array['todosCampos']['T182_Id']}'");
    	$row=mysql_fetch_array($resultado);
		return $row['D024_Nome_Empresa'];
}
function campoAbrirDocumentoRMA($array){
			$id = $array['todosCampos']['T271_Id'];
			$retorno = "<a href=\"/est/est011/grid_func-ajax/abreDocumentoRMA/?ajax=true&acaoId={$id}\">Abrir</a>"; 
			return $retorno;
}

?>