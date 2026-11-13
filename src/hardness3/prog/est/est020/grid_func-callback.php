<?php
namespace hardness;

include_once 'bibliotecas\funcoesCallback\abrirJanela.php';
	
function ValidaCampoDacte($array){
	global $g;		
	return "<a href=\"#\" onclick=\"alert('Imprimir DACTE')\" style='cursor:pointer'>DACTE</a>";					
}

function ValidaCampoCancelar($array) {		
	global $g;	

	if($array['todosCampos']['T084_Flag_Cancelado'] == 'S') {
		return "<b>Cancelado</b>";
	} else if($array['todosCampos']['T084_Flag_Denegado'] == 'S')  {
		return "<b>Denegado</b>";
	}

	return "<a href=\"#\" onclick=\"abrirJanela(false, '{$g['divIdRoot']}', '{$g['divId']}', unique(), '', 'Cancelar CT-e', '/est/est020/content/est020ContentCancelarCte/', '&acaoId=' + encodeURIComponent('{$array['todosCampos']['T084_Id']}'), [565,307]); return false;\">Cancelar</a>";
}

function enviarCCe($array){		
	return "<a href=\"#\" onclick=\"alert('Carta de correção')\" style='cursor:pointer'>CC-e</a>";					
}

function validaCampoXML($array){
	global $g;
	$anoMesCte=substr($array['todosCampos']['T084_Data_Emissao'],0,7);		
	$diretorioCte=$g['pathDadosAntigo'] . 'cte';

	if (!empty($array['todosCampos']['T084_Chave_Acesso_Cte']))
	{
		$retorno='';
	   if (!empty($array['todosCampos']['T084_Numero_Protocolo_Cte'])) {

			if (file_exists("$diretorioCte/xml/autorizado/{$anoMesCte}/".$array['todosCampos']['T084_Numero_Protocolo_Cte'].".xml")) {
			   $retorno  = "<a title='Download do arquivo XML' href=\"/est/est020/grid_func-ajax/abreXML/?ajax=true&acaoId={$array['todosCampos']['T084_Id']}\">";    
			   $retorno .= "<font class=fonte><u>XML</u></font>";
			   $retorno .= "</a>";
		   } else {
				  $retorno  = "<a title='Baixar XML autorizado do banco para o disco' target='_blank' href=\"/est/est020/grid_func-ajax/baixaXML/?ajax=true&acaoId={$array['todosCampos']['T084_Id']}\">";    
				$retorno .= "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML referente a esta CTe não existe no disco!'>Erro!</font>";
				$retorno .= "</a>";
		   }
		} else {
			 $retorno = "<a title='Download do arquivo XML' href=\"/est/est020/grid_func-ajax/abreXMLAssinado/?ajax=true&acaoId={$array['todosCampos']['T084_Id']}\">";			 
			if (file_exists("$diretorioCte/xml/assinado/CTe".$array['todosCampos']['T084_Chave_Acesso_Cte'].".xml")) {
				$retorno .= "<font class=fonte style='color: var(--BGVermelho)'><u>XML</u></font></a>";
			} else {
				$retorno .="</a>";
				$retorno .= "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML referente a esta NFe não existe!'>-</font>";
			}
		}
   return $retorno;
   }
}

function campoUnidade($array){		
	$unidade = [
		'00' => 'M3',
		'01' => 'KG',
		'02' => 'TON',
		'03' => 'UNIDADE',
		'04' => 'LITROS',
		'05' => 'MMBTU'
	];

	return  "{$array['todosCampos']['T305_Codigo_Unidade']} -  {$unidade[$array['todosCampos']['T305_Codigo_Unidade']]}";
}

function diaSemana($array) {
	return	gPegaDiaSemana($array['todosCampos']['T307_Data_Vencimento']);
}

function T307_D027_Id_Portador($array) {
	return $array['todosCampos']['D027_Portador'];
}

function validaExcluirRegistro($array, $params) {
	
	if($array['todosCampos']['T084_Flag_Travar'] == 'S') {
		return '&nbsp;';
	}
	return gExcluirLinha($array,$params);
}

function pegaValidadeCertificado($Certificado,$Senha,$String='N') {
	if ($String=='N') {
		$key  = file_get_contents($Certificado);
	} else {
		   $key = $Certificado;
	}		
	$resp = openssl_pkcs12_read ($key,$x509certdata,$Senha);
	$data = openssl_x509_parse($x509certdata['cert']);
	$in=$data['validTo'];

	$year  = substr($in, 0, 2); /* NOTE: Yes, this returns a two digit year */
	$month = substr($in, 2, 2);
	$day   = substr($in, 4, 2);
	$hour  = substr($in, 6, 2);
	$min   = substr($in, 8, 2);
	$sec   = substr($in, 10, 2);
	if (!empty($in)) {
		$cRetorno=mktime($hour, $min, $sec, $month, $day, $year);
	} else {
		$cRetorno='';
	}
	return $cRetorno;
}

function pegaValidadeCertificadoA1($Certificado,$Senha,$String='N') {
	if ($String=='N') {
		$key  = file_get_contents($Certificado);
	} else {
		$key = $Certificado;
	}		
	$resp = openssl_pkcs12_read ($key,$x509certdata,$Senha);
	$data = openssl_x509_parse($x509certdata['cert']);
	$in=$data['validTo'];

	$year  = substr($in, 0, 2); /* NOTE: Yes, this returns a two digit year */
	$month = substr($in, 2, 2);
	$day   = substr($in, 4, 2);
	$hour  = substr($in, 6, 2);
	$min   = substr($in, 8, 2);
	$sec   = substr($in, 10, 2);
	if (!empty($in)) {
		$cRetorno=mktime($hour, $min, $sec, $month, $day, $year);
	} else {
		$cRetorno='';
	}
	return $cRetorno;
}

function excluirCTe($array) {
	global $g;

	  return <<<EOT
			<a href="#" onclick="est020excluirCTe('{$array['todosCampos']['T084_Id']}','{$g['divId']}')">
				<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
			</a>
EOT;
}

function imprimirDACTE($array) {
	global $g;
	if($array['todosCampos']['T084_Numero_Protocolo_Cte'] > 0) {
		return <<<EOT
			<a href="#" onclick="est020imprimirDACTE('{$array['todosCampos']['T084_Id']}','{$g['divId']}')">
				DACTE
			</a>
		EOT;
	}
}
	

function validaCampoEnviar($array) {
	global $g;
	$FlagEnviar = $array['todosCampos']['T084_Flag_Enviar'];
	$T084_Id = $array['todosCampos']['T084_Id'];
	$usuarioMarcou = $array['todosCampos']['T084_C007_Id_Marcou_Enviar'];
	
	if($usuarioMarcou != $g['usuarioAtual']){
		$disabled = "disabled='disabled'";		
		$Marcado = "<br /><font color='gray' style='font-size:8px;'>marcado</font>";
	}
	
	if($array['todosCampos']['T084_Flag_Travar'] == 'N')
		return '';

	$retorno = "<input onclick=\"est020CheckboxEnviarCTe(this, '{$T084_Id}', '{$g['divId']}');\" type='checkbox' id='T084_Flag_Enviar' title='Enviar no Lote'";
	if ($FlagEnviar == 'S'){
		return $retorno .="checked='checked' $disabled/> {$Marcado}";
	} else {
		return $retorno .= " />";
	}	
}

function validaCampoTipoDocumento($array){

	switch($array['todosCampos']['T085_Flag_Tipo_Documento'])
	{
		case '2':
			return 'NF-e';
		case '3':
			return 'Outros';
		default:
			return '-';
	}
}
?>
