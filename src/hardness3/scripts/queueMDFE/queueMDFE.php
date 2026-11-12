<?php
namespace hardness;
/**
* Fila de Envio de NFs
*/
set_time_limit(0);
$home = __FILE__;
$home = str_replace('scripts/queueMDFE', '', dirname($home));
date_default_timezone_set("America/Fortaleza");
chdir($home);
require 'scripts/queueMDFE/conf.php';
require 'scripts/queueMDFE/codigos.php';
require 'bibliotecas/funcoesGerais.php';
require 'sistema/apps.php';
require 'sistema/personalizacoes.php';
require 'bibliotecas/phpmailer/class.phpmailer.php';
require 'bibliotecas/classes/VEN003.php';
require 'bibliotecas/classes/VEN012.php';
require 'bibliotecas/classes/EST004.php';
require 'sistema/confSistema.php';
$diretorioNFeClass = '../hardness3/hardness/class/';
global $g;
$g['conf'] = new \hardness\confSistema();
$xml = '';

//ini_set('display_errors', 1);
//error_reporting(E_ALL);
error_reporting(E_ALL ^ E_WARNING ^ E_NOTICE);
set_time_limit(0);   
ini_set('mysql.connect_timeout','0');   
ini_set('max_execution_time', '0'); 
// Conexão com o banco
$con = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error = mysqli_connect_error($con);
if (!empty($Error)) { die($Error); }
$g['conexaoBanco'] = $con;
if (isset($usarUTF8) && $usarUTF8) {
	mysql_set_charset('utf8');
	mysqli_query('SET NAMES utf8;');
}

if (!function_exists('TrocarCaracterEspecial')) {
	require 'old/lib/funcoes_genericas.inc';
}
mysqli_query('SET character_set_connection=utf8'); 
mysqli_query('SET character_set_client=utf8'); 
mysqli_query('SET character_set_results=utf8'); 

// Queue de MDFe para serem transmitidas
$queue = array();
$sql = "SELECT * FROM Q004 WHERE IFNULL(Q004_Recibo,'')='' OR Q004_DataHora < DATE_ADD(NOW(), INTERVAL -12 SECOND) limit 1";
$Q004s = mysqli_query($sql);
while ($Q004 = mysql_fetch_assoc($Q004s)) {
	$queue[] = $Q004;
	$result = mysqli_query("DELETE FROM Q004 WHERE Q004_Id = '{$Q004['Q004_Id']}';");
	if (!$result) {
		echo "ERROR! - 'DELETE FROM Q004 WHERE Q004_Id = '{$Q004['Q004_Id']}';' \n";
		die();
	}
}

$nUltimoMDFe = 0;

// Processa MDFe
foreach ($queue as $Q004) {
	// Globais do sistema desktop
	$g['usuarioAtual'] = $Q004['Q004_C007_Id'];
	$g['empresaAtual'] = $Q004['Q004_C004_Id'];
	$g['pathDados'] = $Q004['Q004_Path'] . "dados_usuarios/{$Q004['Q004_Database']}/";
	$g['pathDadosUsuario'] = $Q004['Q004_Path'] . $g['pathDados'] . 'usuarios/u_' . str_repeat('0', 6-strlen($g['usuarioAtual'])) . $g['usuarioAtual'] . '/';
	$g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $g['pathDados']);
	$g['pathDadosUsuarioAntigo'] = str_replace('hardness3', 'hardness', $g['pathDadosUsuario']);
	$confUsuario['pathRaiz'] = $Q004['Q004_Path'];
	$confUsuario['urlRaiz'] = $Q004['Q004_Url'];
	$confUsuario['dbDatabase'] = $Q004['Q004_Database'];
	$g['mysqlLastId'] = false;
	$etapaAtual = 0;
	$diretorioMDFe = $Q004['Q004_Path'] . $diretorioMDFe_;	
	// $g['urlRaiz'] = $confUsuario['urlRaiz'];

	/**
	* Base / Empresa
	*/
	echo "{$Q004['Q004_Database']} - {$Q004['Q004_C004_Id']} - {$Q004['Q004_T276_Id']} ...\n";
	$result = mysqli_query("USE {$Q004['Q004_Database']};");
	if (!$result) {
		echo "ERROR! - 'USE {$Q004['Q004_Database']};' \n";
		continue;
	}

	// $nT276_Id
	$nT276_Id = $Q004['Q004_T276_Id'];
	
	// $mConfig
	$resultado = mysqli_query("select * from C020 where C020_C004_Id = '{$Q004['Q004_C004_Id']}'");
	$mConfig = mysql_fetch_array($resultado);
	
	// $Diretorio_MDFe

	$Diretorio_MDFe = str_replace('[[DB]]', $Q004['Q004_Database'], $diretorioMDFe);
	is_dir($Diretorio_MDFe) || mkdir($Diretorio_MDFe, 0777);
	
	// $Senha
	$Senha = '';
	
	// C031
	$sqlC031 = mysqli_query("SELECT * FROM C031 WHERE C031_C004_Id = '{$g['empresaAtual']}';");
	while($rowC031 = mysql_fetch_assoc($sqlC031)){
		$g['c031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
		$g['C031'][$rowC031['C031_Campo']] = $rowC031['C031_Valor'];
	}

	$C004=mysqli_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
	$mC004=mysql_fetch_array($C004);

	$T276=mysqli_query("select * from T276 where T276_Id='$nT276_Id'");
	$mT276=mysql_fetch_array($T276);

	chdir($diretorioMDFeClass);
	if($mT276['T276_Versao_MDFe'] == '3.00'){	
		//require '/www/hardness3/hardness/class/mdfe3.class.php';
		require 'mdfe3.class.php';
		echo "Versão: 3.00\n";
	}
	chdir($home);
	if ($nUltimoMDFe!=$mT276['T276_Numero_MDFe']){

		echo "\n";
		echo "*****************************************\n";
		echo "***********************************************************\n";
		echo "*****************************************************************************\n";
		echo "**************************************************************************************\n";
		echo "**\n";
		echo "**              >>>>>  INICIO PROCESSO <<<<<\n";
		echo "** \n";
		echo "**              Banco  : {$Q004['Q004_Database']}        \n";
		echo "**              Empresa: {$mC004['C004_Nome_Empresa']}        \n";
		echo "**              MDFe     : {$mT276['T276_Numero_MDFe']}        \n";
		echo "**\n";
		echo "**************************************************************************************\n";
		echo "*****************************************************************************\n";
		echo "************************************************************\n";
		echo "******************************************\n";
		echo "\n";
		$nUltimoMDFe = $mT276['T276_Numero_MDFe'];

	}

	/*
	* Refazer / Via Scan
	*/
	if ($Q004['Q004_Flag_Refazer'] == 'S' || (empty($mT276['T276_Numero_Recibo_MDFe']) and empty($mT276['T276_Numero_Protocolo_MDFe']) and empty($mT276['T276_Chave_Acesso_MDFe']))) {
		echo "**********************************************************************************\n";
		echo "********************** ENTROU REFAZER ********************************************\n";
		echo "**********************************************************************************\n";
		if ($Q004['Q004_Flag_Scan'] == 'S') {
			msg_erro_mdfe(11);
		} else {
			msg_erro_mdfe(10);
		}

		$Scan = $Q004['Q004_Flag_Scan'];			
		if ($mT276['T276_Flag_Scan']!='S' && $Scan=='S')
		{
			echo "**********************************************************************************\n";
			echo "********************** ENTROU SCAN ***********************************************\n";
			echo "**********************************************************************************\n";
			if (!empty($mT276['T276_Data_Hora_Protocolo_MDFe']))
			{
				echo "**********************************************************************************\n";
				echo "********************** ENTROU SCAN CONTINUE **************************************\n";
				echo "**********************************************************************************\n";
				msg_erro_mdfe(118);
				continue;
			}

			mysqli_query("update T276 set T276_Flag_SCAN='S',T276_Chave_Acesso_MDFe='',T276_Numero_Recibo_MDFe='' where T276_Id='{$mT276['T276_Id']}'");

			echo "**********************************************************************************\n";
			echo "********************** EXECUTOU UPDATE SCAN **************************************\n";
			ECHO "UPDAE: update T276 set T276_Flag_SCAN='S',T276_Chave_Acesso_MDFe='',T276_Numero_Recibo_MDFe='' where T276_Id='{$mT276['T276_Id']}'\n";
			echo "**********************************************************************************\n";
		}
		else if ($mT276['T276_Flag_SCAN']=='S' and $Scan!='S')
		{
			echo "**********************************************************************************\n";
			echo "********************** ENTROU ELSEIF T276_Flag_Scan='S' AND scan != S ************\n";
			echo "**********************************************************************************\n";
			if (!empty($mT276['T276_Data_Hora_Protocolo_MDFe']))
			{
				msg_erro_mdfe(118);
				continue; 
			}

			mysqli_query("update T276 set T276_Flag_SCAN='N',T276_Chave_Acesso_Nfe='',T276_Numero_Recibo_Nfe='' where T276_Id='{$mT276['T276_Id']}'");
			echo "**********************************************************************************\n";
			echo "********************** EXECUTOU UPDATE SCAN **************************************\n";
			ECHO "UPDAE: update T276 set T276_Flag_SCAN='N',T276_Chave_Acesso_Nfe='',T276_Numero_Recibo_Nfe='' where T276_Id='{$mT276['T276_Id']}'\n";
			echo "**********************************************************************************\n";
			
		}
		
		$cClausula  = " select ";
		$cClausula .= " C004_Nome_Empresa, ";
		$cClausula .= " C004_Nome_Abreviado, ";
		$cClausula .= " C004_Logradouro, ";
		$cClausula .= " C004_Numero, ";
		$cClausula .= " C004_Bairro, ";
		$cClausula .= " C004_Cep, ";
		$cClausula .= " c4d20.D020_Nome_Cidade as Empresa_Cidade, ";
		$cClausula .= " c4d20.D020_Codigo_IBGE as Empresa_Cidade_IBGE, ";
		$cClausula .= " c4d18.D018_UF as Empresa_UF, ";
		$cClausula .= " c4d18.D018_Estado as Empresa_Estado, ";
		$cClausula .= " c4d18.D018_Codigo_IBGE as Empresa_UF_IBGE, ";
		$cClausula .= " C004_DDD_Telefone, ";
		$cClausula .= " C004_Telefone, ";
		$cClausula .= " C004_Fax, ";
		$cClausula .= " C004_Cnpj, ";
		$cClausula .= " C004_Inscricao_Estadual, ";
		$cClausula .= " C004_CNAE_Fiscal ";
		$cClausula .= " from T276 ";
		$cClausula .= " left join C004 on C004_Id=T276_C004_Id ";
		$cClausula .= " left join D020 as c4d20 on c4d20.D020_Id=C004_D020_Id ";
		$cClausula .= " left join D018 as c4d18 on c4d18.D018_Id=c4d20.D020_D018_Id ";
		$cClausula .= " where T276_Id='$nT276_Id' ";
		
		$cSql_T276=mysqli_query($cClausula);
		$mT276=mysql_fetch_array($cSql_T276);

		if (!empty($mT276['T276_Numero_Recibo_MDFe']) && !empty($mT276['T276_Numero_Protocolo_MDFe']))
		{
		    echo "Numero do recido e protocolo não vazio";
		    recolocarNaFila();		// denovo, sem refazer
		    continue;
		}
		
		if (!empty($mT276['T276_Numero_Recibo_MDFe']) and (!empty($mT276['T276_Chave_Acesso_MDFe']))) {
			echo "Numero do recido e chave de acesso não vazio";
			recolocarNaFila();		// denovo, sem refazer
		   	continue;
		}
		
		if (!empty($mT276['T276_Numero_Protocolo_MDFe']))
		{
			echo "Numero do protocolo não está vazio";
			msg_erro_mdfe(119);
		   	continue;
		}

		$mT276['C004_Inscricao_Estadual']=str_replace(array("-"," ","/"),"",$mT276['C004_Inscricao_Estadual']);
		$mT276['C004_Nome_Empresa']=addslashes($mT276['C004_Nome_Empresa']);
		$mT276['Cliente_Cidade']=addslashes($mT276['Cliente_Cidade']);

		$cClausula  = " update T276 set ";

		$cClausula .= " T276_Emitente_Nome='".mysql_real_escape_string($mT276['C004_Nome_Empresa'])."', ";
		$cClausula .= " T276_Emitente_Nome_Fantasia='".mysql_real_escape_string($mT276['C004_Nome_Abreviado'])."', ";
		$cClausula .= " T276_Emitente_Rua='".mysql_real_escape_string($mT276['C004_Logradouro'])."', ";
		$cClausula .= " T276_Emitente_Numero='".mysql_real_escape_string($mT276['C004_Numero'])."', ";
		$cClausula .= " T276_Emitente_Bairro='".mysql_real_escape_string($mT276['C004_Bairro'])."', ";
		$cClausula .= " T276_Emitente_CEP='".mysql_real_escape_string($mT276['C004_Cep'])."', ";
		$cClausula .= " T276_Emitente_Cidade='".mysql_real_escape_string($mT276['Empresa_Cidade'])."', ";
		$cClausula .= " T276_Emitente_Cidade_IBGE='".mysql_real_escape_string($mT276['Empresa_Cidade_IBGE'])."', ";
		$cClausula .= " T276_Emitente_UF='".mysql_real_escape_string($mT276['Empresa_UF'])."', ";
		$cClausula .= " T276_Emitente_UF_IBGE='".mysql_real_escape_string($mT276['Empresa_UF_IBGE'])."', ";
		$cClausula .= " T276_Emitente_CNPJ='".mysql_real_escape_string($mT276['C004_Cnpj'])."', ";
		$cClausula .= " T276_Emitente_Inscricao_Estadual='".mysql_real_escape_string($mT276['C004_Inscricao_Estadual'])."', ";
		$cClausula .= " T276_Flag_Contingencia='N',";
		$cClausula .= " T276_Chave_Acesso_MDFe='',";
		$cClausula .= " T276_Numero_Recibo_MDFe='',";
		$cClausula .= " T276_Data_Hora_Recebimento_MDFe='0000-00-00 00:00:00' ";
		$cClausula .= " where T276_Id='$nT276_Id'";
		mysqli_query($cClausula);
		$cErro = mysql_error();
		log("Erro: ".$cErro);
		if (!empty($cErro))
		{
		   	msg_erro_mdfe(120);
		   	continue;
		}
		if ($Q004['Q004_Flag_Refazer'] == 'S') {
			recolocarNaFila();
			continue;
		}
	}
	
	
	/**
	* Verificações Gerais que são feitas em todas as etapas
	*/
	$cClausula = <<<EOT
SELECT 
	*
FROM
	T276
left join C004 on C004_Id=T276_C004_Id
left join C007 on C007_Id=T276_C007_Id
left join D018 on D018_Id=C004_D018_Id
where T276_Id='{$nT276_Id}';
EOT;
	$cSql_T276=mysqli_query($cClausula);
	$mT276=mysql_fetch_array($cSql_T276);
	
	if (empty($mT276)) {
		msg_erro_mdfe(100);
		continue;
	}
	
	if ($mT276['T276_Versao_MDFe']=='3.00')
	{
		// OK para continuar!
	}
	else
	{
		msg_erro_mdfe(102);
		continue;
	}
	
	if ($mT276['T276_Flag_Modalidade_Transporte']!='1' && $mT276['T276_Flag_Modalidade_Transporte']!='2' && $mT276['T276_Flag_Modalidade_Transporte']!='3' && $mT276['T276_Flag_Modalidade_Transporte']!='4')
	{
		msg_erro_mdfe(105, "Tipo de Transporte: {$mT276['T276_Flag_Modalidade_Transporte']}");
		continue;
	}
	
	if ($mT276['T276_Flag_Cancelada']=='S' and (empty($mT276['T276_Numero_Recibo_MDFe']) or empty($mT276['T276_Numero_Protocolo_MDFe'])))
	{
		msg_erro_mdfe(106);
		continue;
	}
	
	// SMARTNESS
	// require 'hardness/class/mdfe3.class.php';
	// echo "Versão: 3.00\n";
	// SMARTNESS

	// Classe da MDFe
	$oMDFe = new classeMDFe();
	$oMDFe->setT276Id($nT276_Id);
	$oMDFe->setPastaArquivoXML("{$Diretorio_MDFe}/xml");
	$oMDFe->setTipoDocAuxiliar(1);
	
	$Origem="{$mT276['T276_Numero_MDFe']}_{$mT276['T276_Serie']}.xml";
	$Destino="MDFe{$mT276['T276_Chave_Acesso_MDFe']}.xml";
	
	// Descontinuado modo assincrono, ajustando para modo sincrono
	//if(!empty($mT276['T276_Numero_Protocolo_MDFe']) and !empty($mT276['T276_Numero_Recibo_MDFe']) and !empty($mT276['T276_Chave_Acesso_MDFe'])){
	if(!empty($mT276['T276_Numero_Protocolo_MDFe']) and !empty($mT276['T276_Chave_Acesso_MDFe'])){
		echo date("d/m/Y H:i:s") . " Etapa 4: Encerrando XML\n";
		include ('scripts/queueMDFE/etapa4-encerraXml.php');
	} else if ((file_exists("$Diretorio_MDFe/xml/assinado/MDFe{$mT276['T276_Chave_Acesso_MDFe']}.xml") and (!empty($mT276['T276_Chave_Acesso_MDFe']))) || (!empty($mT276['T276_Numero_Recibo_MDFe']) and (!empty($mT276['T276_Chave_Acesso_MDFe'])))){	
		$xml = file_get_contents("$Diretorio_MDFe/xml/assinado/MDFe{$mT276['T276_Chave_Acesso_MDFe']}.xml");
		//-- SE O ARQUIVO DE ASSINATURA JA EXISTIR, PASSA DIRETO PRO ENVIO
		$etapaAtual = 3;
		echo date("d/m/Y H:i:s") . " Etapa 3: Enviando XML\n";
		//include ('scripts/queueMDFE/etapa3-enviarXml.php');
		include ('scripts/queueMDFE/etapa3-enviarXml-sincrona.php');
	} elseif (empty($mT276['T276_Numero_Recibo_MDFe']) and empty($mT276['T276_Numero_Protocolo_MDFe']) and !empty($mT276['T276_Chave_Acesso_MDFe'])){
		$etapaAtual = 2;
		echo date("d/m/Y H:i:s") . " Etapa 2: Assinando XML\n";
		include ('scripts/queueMDFE/etapa2-assinarXml.php');
	}else {
		$etapaAtual = 1;
		echo date("d/m/Y H:i:s") . " Etapa 1: Gerando XML\n";
		include ('scripts/queueMDFE/etapa1-gerarXml.php');
	}

	$cClausula = <<<EOT
SELECT 
	*
FROM
	T276
left join C004 on C004_Id=T276_C004_Id
left join C007 on C007_Id=T276_C007_Id
left join D018 on D018_Id=C004_D018_Id
where T276_Id='{$nT276_Id}';
EOT;
	$cSql_T276=mysqli_query($cClausula);
	$mT276=mysql_fetch_array($cSql_T276);
	
	if (empty($mT276)) {
		msg_erro_mdfe(100);
		continue;
	}

	echo "\n";
	echo "**************************************************************************************\n";
	echo "**              Final de processo\n";
	echo "** \n";
	echo "**              Banco  : {$Q004['Q004_Database']}   MDFe : {$mT276['T276_Numero_MDFe']}        \n";
	echo "**              Empresa: {$mC004['C004_Nome_Empresa']}        \n";
	echo "**              Chave  : {$mT276['T276_Chave_Acesso_MDFe']}        \n";
	echo "**              Recibo : {$mT276['T276_Numero_Recibo_MDFe']}      \n";
	echo "**              Protocl: {$mT276['T276_Numero_Protocolo_MDFe']} {$mT276['T276_Data_Hora_Protocolo_MDFe']}      \n";
	echo "**************************************************************************************\n";
	echo "\n";

}


/**
* Atualiza status da T276 e transmite para o servidor de LOG
* Nem sempre pode ser um erro, o nome msg_erro_mdfe() foi utilizado porque já existia
* 
* @param int $codigo
* @param string $extra
*/
function msg_erro_mdfe($codigo, $extra = '', $updateT276 = true, $retornoSefaz = '') {
	global $servidorId, $errosLogUrl, $etapaAtual, $Q004, $oMDFe, $nT276_Id, $xml;
	
	$cClausula = <<<EOT
SELECT 
	T276_TX_Flag_Erro,
	T276_Numero_MDFe,
	T276_Serie,
	T276_Data_Emissao,
	C004_Nome_Abreviado,
	C004_Telefone,
	T276_Valor_Carga
FROM
	T276
left join C004 on C004_Id=T276_C004_Id
left join C007 on C007_Id=T276_C007_Id
where T276_Id='{$nT276_Id}';
EOT;
	$cSql_T276=mysqli_query($cClausula);
	$mT276=mysql_fetch_array($cSql_T276);
	
	echo date("d/m/Y H:i:s") . " Codigo: {$codigo} - {$extra} - {$retornoSefaz}\n";
	// Grava erro na T276
	if ($updateT276) {
		$codigo_ = mysql_real_escape_string($codigo);
		$extra_ = mysql_real_escape_string($extra);
		$retornoSefaz_ = mysql_real_escape_string($retornoSefaz);
		$xml_ = mysql_real_escape_string($xml);
		mysqli_query("UPDATE T276 SET T276_TX_Resposta_Codigo = '{$codigo_}', T276_TX_Resposta_Extra = '{$extra_}', T276_TX_Retorno_Sefaz = '{$retornoSefaz_}', T276_Arquivo_XML_Temp = '{$xml_}' WHERE T276_Id = '{$nT276_Id}';");
	}

	// Envia erro para o servidor de log
	if($codigo > 99 || ($mT276['T276_TX_Flag_Erro'] == '1' && $codigo > 8)){
		//Marca a nota com erro
		mysqli_query("UPDATE T276 SET T276_TX_Flag_Erro = '1' WHERE T276_Id = '{$nT276_Id}';");

		/*	echo "\n";
			echo "**************************************************************************************\n";
			echo "**              ENVIANDO ERRO PARA O SERVIDOR DE LOG\n";
			echo "** \n";
			echo "**  ERROSlOGUIRL: {$errosLogUrl}";	
			echo "** ";
			echo "**************************************************************************************\n";
			echo "\n";

		$log = array(
			'Servidor_Id' => $servidorId,
			'Database' => $Q004['Q004_Database'],
			'C004_Id' => $Q004['Q004_C004_Id'],
			'T276_Id' => $Q004['Q004_T276_Id'],
			'C007_Id' => $Q004['Q004_C007_Id'],
			'Etapa' => $etapaAtual,
			'Codigo_Resposta' => $codigo,
			'Resposta_Extra' => $extra,
			'Soap_Debug' => @$oMDFe->soapDebug,
			'Numero_MDFe' => $mT276['T276_Numero_MDFe'],
			'Retorno_Sefaz' => $retornoSefaz,
			'T276_Serie' => $mT276['T276_Serie'],
			'T276_Data_Emissao' => $mT276['T276_Data_Emissao'],
			'C004_Nome_Abreviado' => $mT276['C004_Nome_Abreviado'],
			'C004_Telefone' => $mT276['C004_Telefone'],
			'Url' => $Q004['Q004_Url'],
			'T276_Valor_Carga' => $mT276['T276_Valor_Carga'],
			'XML' => $xml,
		);

		// Servidor de log
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $errosLogUrl);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, 'log=' . urlencode(serialize($log)));
		curl_exec($ch);
		curl_close($ch);*/
	}
}

/**
* Recoloca a NF na fila para transmissão (na próxima irá identificar a próxima etapa, e assim pode diante)
* 
*/
function recolocarNaFila($Recibo = NULL) {
	global $Q004;

	$sql = "SELECT Q004_Id FROM `global`.Q004 WHERE Q004_Database = '{$Q004['Q004_Database']}' AND Q004_T276_Id = '{$Q004['Q004_T276_Id']}' AND '{$Q004['Q004_C004_Id']}'";
	log("SELECT Q004_Id FROM `global`.Q004 WHERE Q004_Database = '{$Q004['Q004_Database']}' AND Q004_T276_Id = '{$Q004['Q004_T276_Id']}' AND '{$Q004['Q004_C004_Id']}'");
	$res = mysqli_query($sql);
	if (mysql_num_rows($res) > 0) {
/*		$sql = "UPDATE `global`.Q004 SET Q004_Flag_Refazer = 'N' WHERE Q004_Database = '{$Q004['Q004_Database']}' AND Q004_T276_Id = '{$Q004['Q004_T276_Id']}' AND '{$Q004['Q004_C004_Id']}'";
		echo "Forçando Refazer: '{$Q004['Q004_Database']}', '{$Q004['Q004_T276_Id']}', '{$Q004['Q004_C004_Id']}', '{$Q004['Q004_Url']}', '{$Q004['Q004_Path']}'\n";
		mysqli_query($sql); */
	} else {
		$sql = <<<EOT
INSERT INTO `global`.Q004
(`Q004_Database`,`Q004_T276_Id`,`Q004_C004_Id`,`Q004_Url`,`Q004_Path`,`Q004_C007_Id`,`Q004_DataHora`,`Q004_Recibo`,`Q004_Flag_Scan`)
VALUES
('{$Q004['Q004_Database']}', '{$Q004['Q004_T276_Id']}', '{$Q004['Q004_C004_Id']}', '{$Q004['Q004_Url']}', '{$Q004['Q004_Path']}', '{$Q004['Q004_C007_Id']}', now(), '{$Recibo}','{$Q004['Q004_Flag_Scan']}');
EOT;
		echo "Recolocando na fila: '{$Q004['Q004_Database']}', '{$Q004['Q004_T276_Id']}', '{$Q004['Q004_C004_Id']}', '{$Q004['Q004_Url']}', '{$Q004['Q004_Path']}, {$Q004['Q004_Flag_Scan']}'\n";
		mysqli_query($sql);
		echo mysql_error();
	}
}
