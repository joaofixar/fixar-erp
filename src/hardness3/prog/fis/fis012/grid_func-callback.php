<?php
namespace hardness;
	require_once('prog/fis/fis012/grid_func-js.php');

	function tipoTransporte($array){
		$tipoTransporte = $array['todosCampos']['T281_Flag_Unidade_Transporte'];
		$retorno = '';

		if ($tipoTransporte == 1) {
			$retorno = "RODOVIÁRIO TRAÇÃO";
		}
		if ($tipoTransporte == 2) {
			$retorno = "RODOVIÁRIO REBOQUE";
		}
		if ($tipoTransporte == 3) {
			$retorno = "NAVIO";
		}
		if ($tipoTransporte == 4) {
			$retorno = "BALSA";
		}
		if ($tipoTransporte == 5) {
			$retorno = "AERONAVE";
		}
		if ($tipoTransporte == 6) {
			$retorno = "VAGÃO";
		} 
		if ($tipoTransporte == 7) {
			$retorno = "OUTROS";
		}
		
		return $retorno;
	}	

	function tipoCarga($array){
		$tipoCarga = $array['todosCampos']['T282_Flag_Unidade_Carga'];
		$retorno = '';

		if ($tipoCarga == 1) {
			$retorno = "CONTAINER";
		}
		if ($tipoCarga == 2) {
			$retorno = "ULD";
		}
		if ($tipoCarga == 3) {
			$retorno = "PALLET";
		} 
		if ($tipoCarga == 4) {
			$retorno = "OUTROS";
		}
		
		return $retorno;
	}

	function validaTipo($array){
		$mDocumento[1] = 'CT-e';
		$mDocumento[2] = 'NF-e';
		if (empty($array['todosCampos']['T284_Flag_Tipo_Documento'])) { 
			$array['todosCampos']['T284_Flag_Tipo_Documento'] = 2; 
		}
		$retorno = $mDocumento[$array['todosCampos']['T284_Flag_Tipo_Documento']]; 
		return $retorno;
	}

	function validaCarroProprio($array){		
		if($array['todosCampos']['T283_Flag_Carro_Proprio']=='S'){
			return "SIM";
		}else{
			return "NÃO";
		}
	}

	function validaCampoTipoRodado($array){
		if ($array['todosCampos']['T283_Flag_Rodado'] == 01){
			return "TRUCK";
		} elseif ($array['todosCampos']['T283_Flag_Rodado'] == 02) {
			return "TOCO";
		} elseif ($array['todosCampos']['T283_Flag_Rodado'] == 03) {
			return "CAVALO MECÂNICO";
		} elseif ($array['todosCampos']['T283_Flag_Rodado'] == 04) {
			return "VAN";
		} elseif ($array['todosCampos']['T283_Flag_Rodado'] == 05) {
			return "UTILITÁRIO";
		} else {
			return "OUTROS";
		}
	}

	function validaCampoCarroceria($array){
		if ($array['todosCampos']['T283_Flag_Carroceria'] == 00) {
			return "NÃO APLICÁVEL";
		} elseif ($array['todosCampos']['T283_Flag_Carroceria'] == 01) {
			return "ABERTA";
		} elseif ($array['todosCampos']['T283_Flag_Carroceria'] == 02) {
			return "FECHADA/BAÚ";
		} elseif ($array['todosCampos']['T283_Flag_Carroceria'] == 03) {
			return "GRANELEIRA";
		} elseif ($array['todosCampos']['T283_Flag_Carroceria'] == 04) {
			return "PORTA-CONTEINER";
		} else {
			return "SIDER";
		}
	}

	function validaCampoCategoria($array){
		if ($array['todosCampos']['T283_Categoria_Veiculo'] == 1){
			return "REBOQUE";
		} elseif ($array['todosCampos']['T283_Categoria_Veiculo'] == 2) {
			return "TRAÇÃO";
		} else {
			return '-';
		}
	}	

	function colunaMotoristas($array){
		global $g;
		
		$retorno = '';
		$motoristas = explode("<br>", $array['todosCampos']['colunaMotoristas']);
		foreach ($motoristas as $motorista) {
			$dados = explode("|", $motorista);
			$nomeMotorista = $dados[0];
			$retorno .= "<font color='#413a3a'>{$nomeMotorista}</font><br>";
		}
		//$retorno .= "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Cadastrar Motorista MDF-e', '/fis/fis012/content/fis012ContentCadastrarMotoristaMdfe/', '&T276_Id=' + encodeURIComponent('{$array['todosCampos']['T276_Id']}'), [650,550]); return false;\"><font color='#413a3a'>INCLUIR</font></a>";
		return $retorno;
	}

	function colunaCargas($array){
		global $g;
		$T276_Id = (isset($_REQUEST['T276_Id'])) ? $_REQUEST['T276_Id'] : false;

		$retorno = '';
		$array['todosCampos']['colunaCargas'] = str_replace(",", "", $array['todosCampos']['colunaCargas']);
		$cargas = explode("<br>", $array['todosCampos']['colunaCargas']);
		foreach ($cargas as $carga) {
			$dados = explode("|", $carga);
			$identCarga = $dados[0];
			$retorno .= "<font color='#413a3a'>{$identCarga}</font><br>";
		}
		$retorno .= "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Cadastrar Tipo Transporte Carga MDF-e', '/fis/fis012/content/fis012ContentCadastrarTipoTransporteCargas/', '&T281_Id=' + encodeURIComponent('{$array['todosCampos']['T281_Id']}') + '&T276_Id=' + encodeURIComponent('{$T276_Id}'), [650,550]); return false;\"><font color='#413a3a'>INCLUIR</font></a>";
		return $retorno;
	}

	function colunaAverbacao($array){
		global $g;
		log("callback: T279:".$array['todosCampos']['T279_Id']);
		$retorno = '';
		$array['todosCampos']['colunaSeguradora'] = str_replace(",", "", $array['todosCampos']['colunaSeguradora']);
		$seguradoras = explode("<br>", $array['todosCampos']['colunaSeguradora']);
		foreach ($seguradoras as $seguradora) {
			$dados = explode("|", $seguradora);
			$identSeguradora = $dados[0];
			$retorno .= "<font color='#413a3a'>{$identSeguradora}</font><br>";
		}
		$retorno .= "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Cadastrar Averbação', '/fis/fis012/content/fis012ContentCadastrarAverbacao/', '&T279_Id=' + encodeURIComponent('{$array['todosCampos']['T279_Id']}'), [650,550]); return false;\"><font color='#413a3a'>INCLUIR</font></a>";
		return $retorno;
	}

	function colunaTipoTransporteCargaLacres($array){
		global $g;

		$retorno = '';
		$cargaLacres = explode("<br>", $array['todosCampos']['colunaCargaLacres']);
		foreach ($cargaLacres as $cargaLacre) {
			$dados = explode("|", $cargaLacre);
			$numLacre = $dados[0];
			$retorno .= "<font color='#413a3a'>{$numLacre}</font><br>";
		}
		$retorno .= "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Cadastrar Tipo Transporte Carga Lacre MDF-e', '/fis/fis012/content/fis012ContentCadastrarTipoTransporteCargaLacre/', '&T282_Id=' + encodeURIComponent('{$array['todosCampos']['T282_Id']}') + '&T276_Id=' + encodeURIComponent('{$_REQUEST['T276_Id']}'), [650,550]); return false;\"><font color='#413a3a'>INCLUIR</font></a>";

		return $retorno;
	}

	function colunaTipoTransporteLacres($array){
		global $g;

		$retorno = '';
		$transporteLacres = explode("<br>", $array['todosCampos']['colunaTransporteLacres']);
		foreach ($transporteLacres as $transporteLacre) {
			$dados = explode("|", $transporteLacre);
			$numLacre = $dados[0];
			$retorno .= "<font color='#413a3a'>{$numLacre}</font><br>";
		}
		$retorno .= "<a href=\"#\" onclick=\"abrirJanela(false, divIdRootAberto(), gridPegarDivId(\$(this)), unique(), '', 'Cadastrar Tipo Transporte Lacre MDF-e', '/fis/fis012/content/fis012ContentCadastrarTipoTransporteLacre/', '&T281_Id=' + encodeURIComponent('{$array['todosCampos']['T281_Id']}') + '&T276_Id=' + encodeURIComponent('{$_REQUEST['T276_Id']}'), [650,550]); return false;\"><font color='#413a3a'>INCLUIR</font></a>";

		return $retorno;
	}

	function ValidaCampoDamdfe($array){
		global $g;
		$protocoloMDFe = $array['todosCampos']['T276_Numero_Protocolo_MDFe'];
		$T276_Id = $array['todosCampos']['T276_Id'];
		$T276_C004_Id = $array['todosCampos']['T276_C004_Id'];
		$Query = md5($T276_C004_Id.'|'.$T276_Id);
		if(!empty($protocoloMDFe)){
			$retorno = "<a href=\"#\" onclick=\"window.open('/hardness3/hardness/damdfe/imprimir_damdfe.php?Query={$Query}&T276_Id={$T276_Id}&T276_C004_Id={$T276_C004_Id}','DAMDFE{$Query}','toolbar=yes,menubar=yes,location=no,scrollbars=yes,status=yes,resizable=yes,width=990,height=542,top=0,left=0');\" style='cursor:pointer'>DAMDFE</a>";
		}else{
			$retorno = "<a href=\"#\" onclick=\"javascript:abre_doc('/hardness3/hardness/nota_emitida2/gerar_pre_damdfe.php?T276_Id={$array['todosCampos']['T276_Id']}&T276_C004_Id={$T276_C004_Id}','MDFe {$array['todosCampos']['T276_Id']}');\" style='cursor:pointer'>DAMDFE</a>";	
		}			

		return $retorno;
	}

	function validaCampoXML($array){
		global $g;
		$cAno_Mes_MDFe = substr($array['todosCampos']['T276_Data_Emissao'],0,7);
		$Diretorio_MDFe=$g['pathDadosAntigo'] . 'mdfe';
		if (!empty($array['todosCampos']['T276_Chave_Acesso_MDFe'])) {
			$retorno='';
			if (!empty($array['todosCampos']['T276_Numero_Protocolo_MDFe'])) {
				$retorno = "<a title='Download do arquivo XML' href=\"/fis/fis012/grid_func-ajax/abreXML/?ajax=true&acaoId={$array['todosCampos']['T276_Id']}\">";				
				if (file_exists("$Diretorio_MDFe/xml/autorizado/{$cAno_Mes_MDFe}/".$array['todosCampos']['T276_Numero_Protocolo_MDFe'].".xml")) {
					$retorno .= "<font class=fonte><u>XML {$array['todosCampos']['T276_Versao_MDFe']}</u></font></a>";
				} else {
					$retorno .="</a>";
					$retorno .= "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML referente a este MDFe não existe!'>Erro!</font>";
				}
			} else {
				$retorno = "<a title='Download do arquivo XML' href=\"/fis/fis012/grid_func-ajax/abreXMLAssinado/?ajax=true&acaoId={$array['todosCampos']['T276_Id']}\">";			
				if (file_exists("$Diretorio_MDFe/xml/assinado/MDFe".$array['todosCampos']['T276_Chave_Acesso_MDFe'].".xml")) {
					$retorno .= "<font class=fonte style='color: var(--BGVermelho)'><u>XML {$array['todosCampos']['T276_Versao_MDFe']}</u></font></a>";
				} else if (file_exists("$Diretorio_MDFe/xml/assinado/".$array['todosCampos']['T276_Numero_Nota_Fiscal'].'_'.$array['todosCampos']['T276_C004_Id'].".xml")) {
                     $retorno .= "<font class=fonte style='color: var(--BGVermelho)'><u>XML {$array['todosCampos']['T276_Versao_MDFe']}</u></font></a>";
				} else {
					$retorno .="</a>";
					$retorno .= "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML referente a este MDFe não existe!'>-</font>";
				}
			}
		return $retorno;
		}
	}

	function validaCampoCancelar($array){
		global $g;
		$T276_Id = $array['todosCampos']['T276_Id'];
		$sqlT285 = mysql_query("SELECT T285_Codigo_Retorno FROM T285 WHERE T285_T276_Id = '{$T276_Id}' AND T285_Modelo = 58 ORDER BY T285_Id DESC LIMIT 1");
		$T285 = mysql_fetch_array($sqlT285);
		$T285_Codigo_Retorno = $T285['T285_Codigo_Retorno'];

		if($array['todosCampos']['T276_Flag_Cancelada'] == 'S'){
			if($T285_Codigo_Retorno == '301' || $T285_Codigo_Retorno == '302' || $T285_Codigo_Retorno == '110'){ //Necessários ajustes de acordo com o MDF-e
				$retorno = "<b>Denegada</b>";
			} else {
				// die($array['todosCampos']['T276_Observacao_Cancelamento']);
				$retorno = "<b>Cancelado</b>";					
			}
			return $retorno;
		} else {
			if($array['todosCampos']['T276_Flag_Encerrada'] == 'S'){
				$retorno = "<b>Encerrado</b>";
			}else{
				$retorno="<a href=\"#\" onclick=\"fis012CancelarMfde('{$array['todosCampos']['T276_Id']}', '{$g['divId']}', '{$g['divIdRoot']}'); return false;\">Cancelar</a>";
			}
			return $retorno;
		}
	}

	function chamaCampo($array) {
		$cClausula  = " SELECT ";
		$cClausula .= " T285_Codigo_Retorno, ";
		$cClausula .= " T285_Retorno_Sefaz";
		$cClausula .= " FROM T285";
		$cClausula .= " WHERE T285_T276_Id = '{$array['todosCampos']['T276_Id']}' ";
		$cClausula .= " ORDER BY T285_Data_Transmicao DESC  ";
		$cClausula .= " LIMIT 1  ";
		$cSql_T285 = mysql_query($cClausula);
		$mT285 = mysql_fetch_array($cSql_T285);
		return $mT285['T285_Retorno_Sefaz'];   
	}

	function validaCampoXMLCancelado($array){
		global $g;
		$cAno_Mes_MDFe=substr($array['todosCampos']['T276_Data_Emissao'],0,7);
		$Diretorio_MDFe=$g['pathDadosAntigo'] . 'mdfe';
		if (!empty($array['todosCampos']['T276_Chave_Acesso_MDFe'])) {
			$retorno='';
			if (!empty($array['todosCampos']['T276_Numero_Protocolo_Cancelamento_MDFe'])) {
				$retorno = "<a title='Download do arquivo XML' href=\"/fis/fis012/grid_func-ajax/abreXMLCancelamento/?ajax=true&acaoId={$array['todosCampos']['T276_Id']}\">";
				if (file_exists("$Diretorio_NFe/xml/cancelado/ID110111".$array['todosCampos']['T276_Chave_Acesso_MDFe']."01.xml")) {
					$retorno .= "<font class=fonte><u>{$array['todosCampos']['T276_Numero_Protocolo_Cancelamento_MDFe']}</u></font></a>";
				} else {
					$retorno .="</a>";
					$retorno .= "<font class=fonte style='color: var(--BGVermelho)' title='ATENÇÃO! O arquivo XML de cancelamento referente a este MDFe não existe!'>{$array['todosCampos']['T276_Numero_Protocolo_Cancelamento_MDFe']}</font>";
				}
			}
			return $retorno;
		}
	}

	function ValidaChaveAcesso($array){
		return $array['todosCampos']['T276_Chave_Acesso_MDFe'];
		/*$retorno = "";
		if($array['todosCampos']['T007_Flag_Inutilizado']=='S' and empty($array['todosCampos']['T007_Protocolo_Inutilizacao'])){
			$retorno = "<u>Inutilize essa numeração</u>";
			return $retorno;
		}elseif($array['todosCampos']['T007_Flag_Inutilizado']=='S' and !empty($array['todosCampos']['T007_Protocolo_Inutilizacao'])){
			$retorno = "<u>Protocolo de inutilização: ".$array['todosCampos']['T007_Protocolo_Inutilizacao']."</u>";
			return $retorno;
		}else{
			$retorno = $array['todosCampos']['T007_Chave_Acesso_Nfe'];
			return $retorno;
		}*/
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

	function validaCampoStatus($array){
		if($array['todosCampos']['D109_Flag_Ativo']=="S"){
			return "Ativo";
		}else{
			return "Inativo";
		}
	}

	function mostrarUF ($array) {
		global $g;
		return $array['todosCampos']['T276_UF_Descarregamento'];
	}

	function mostrarCidade ($array) {	  
		if(empty($array['todosCampos']['descarregamentoD020.D020_Nome_Cidade']))
			return $array['todosCampos']['descarregamentoD020.D020_Nome_Cidade'];
	}

	function excluirMDFe($array) {
		global $g;

	  	return <<<EOT
	    		<a href="#" onclick="excluirMDFe('{$array['todosCampos']['T276_Id']}','{$g['divId']}')">
	    			<span class="ui-icon ui-icon-circle-close" title="Excluir"></span>
				</a>
EOT;
		
	}

	function retornaNumeroPelaChaveAcesso($array, $params = null){
		global $g;

		$campoChaveAcesso = $params[0] ?: '';
		$chaveAcesso = $array['todosCampos'][$campoChaveAcesso];

		return ltrim(substr($chaveAcesso,25,9), '0');
	}


?>