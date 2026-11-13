<?php
namespace hardness;
/**
* Fila de Envio de NFs
*/
set_time_limit(0);
$home = __FILE__;
$home = str_replace('scripts/queueNF', '', dirname($home));
date_default_timezone_set("America/Fortaleza");
ini_set('default_charset', 'utf-8');
setlocale(LC_CTYPE, 'pt_BR.UTF-8');
chdir($home);
$diretorioNFeClass = '../hardness3/hardness/class/';
//ini_set('display_errors', 1);
//error_reporting(E_ALL ^ E_DEPRECATED ^ E_NOTICE ^ E_WARNING);
require 'scripts/queueNF/conf.php';
require 'scripts/queueNF/codigos.php';
require 'bibliotecas/funcoesGerais.php';
require 'sistema/apps.php';
require 'sistema/personalizacoes.php';
require 'bibliotecas/phpmailer/class.phpmailer.php';
require 'sistema/comunicacaoEmail.php';
require 'bibliotecas/classes/VEN003.php';
require 'bibliotecas/classes/VEN012.php';
require 'bibliotecas/classes/EST004.php';
require 'sistema/confSistema.php';
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

// Queue de NFs para serem transmitidas
$queue = array();
$sql = "SELECT * FROM Q001 WHERE IFNULL(Q001_Recibo,'')='' OR Q001_DataHora < DATE_ADD(NOW(), INTERVAL -12 SECOND) limit 1";
$Q001s = mysqli_query($sql);
while ($Q001 = mysql_fetch_assoc($Q001s)) {
	$queue[] = $Q001;
	$result = mysqli_query("DELETE FROM Q001 WHERE Q001_Id = '{$Q001['Q001_Id']}';");
	if (!$result) {
		echo "ERROR! - 'DELETE FROM Q001 WHERE Q001_Id = '{$Q001['Q001_Id']}';' \n";
		die();
	}
}

$nUltimaNF = 0;

// Processa NFs
foreach ($queue as $Q001) {
	// Globais do sistema desktop
	$g['usuarioAtual'] = $Q001['Q001_C007_Id'];
	$g['empresaAtual'] = $Q001['Q001_C004_Id'];
	$g['pathDados'] = $Q001['Q001_Path'] . "dados_usuarios/{$Q001['Q001_Database']}/";
	$g['pathDadosUsuario'] = $Q001['Q001_Path'] . $g['pathDados'] . 'usuarios/u_' . str_repeat('0', 6-strlen($g['usuarioAtual'])) . $g['usuarioAtual'] . '/';
	$g['pathDadosAntigo'] = str_replace('hardness3', 'hardness', $g['pathDados']);
	$g['pathDadosUsuarioAntigo'] = str_replace('hardness3', 'hardness', $g['pathDadosUsuario']);
	$confUsuario['pathRaiz'] = $Q001['Q001_Path'];
	$confUsuario['urlRaiz'] = $Q001['Q001_Url'];
	$confUsuario['dbDatabase'] = $Q001['Q001_Database'];
	$g['mysqlLastId'] = false;
	$g['urlRaiz'] = $confUsuario['urlRaiz'];
	
	/**
	* -1 - Refazer / Via Scan
	* 0 - Validação
	* 1 - Gerar XML
	* 2 - Assinar XML
	* 3 - Enviar XML
	* 4 - Gerar Danfe
	* @var int
	*/
	$etapaAtual = 0;
	
	$diretorioNFe = $Q001['Q001_Path'] . $diretorioNFe_;
	
	/**
	* Base / Empresa
	*/
	echo "{$Q001['Q001_Database']} - {$Q001['Q001_C004_Id']} - {$Q001['Q001_T007_Id']} ...\n";
	$result = mysqli_query("USE {$Q001['Q001_Database']};");
	if (!$result) {
		echo "ERROR! - 'USE {$Q001['Q001_Database']};' \n";
		continue;
	}
	// $nT007_Id
	$nT007_Id = $Q001['Q001_T007_Id'];
	// $mConfig
	$resultado = mysqli_query("select * from C020 where C020_C004_Id = '{$Q001['Q001_C004_Id']}'");
	$mConfig = mysql_fetch_array($resultado);
	// $Diretorio_NFe
	$Diretorio_NFe = str_replace('[[DB]]', $Q001['Q001_Database'], $diretorioNFe);
	if(!is_dir($Diretorio_NFe)) {
		mkdir($Diretorio_NFe, 0777);
		chmod($Diretorio_NFe, 0777);
	}
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

	// Buscando dados da nota fiscal
	$T007=mysqli_query("SELECT T007_Numero_Nota_Fiscal,
							  T007_Versao_NFe,
							  D024_Nome_Empresa,
							  T007_Flag_ACP,
							  T007_Numero_Recibo_Nfe,
							  T007_Chave_Acesso_Nfe,
							  T007_Id,
							  T007_Numero_Protocolo_Nfe,
							  T007_C004_Id,
							  T007_D024_Id,
							  T007_Flag_SCAN,
							  T007_Data_Hora_Protocolo_Nfe 
						 FROM T007 
			    	LEFT JOIN D024 ON D024_Id=T007_D024_Id 
						WHERE T007_Id='$nT007_Id'");
						
	$mT007=mysql_fetch_array($T007);

	//Criar arquivo texto log nota fiscal
	$arquivoNome = "/tmp/QueueNF-{$Q001['Q001_Database']}-{$Q001['Q001_C004_Id']}-{$mT007['T007_Numero_Nota_Fiscal']}.txt";
	$h = fopen($arquivoNome, "a");
	// chmod("/tmp/QueueNF-{$Q001['Q001_Database']}-{$Q001['Q001_C004_Id']}-{$mT007['T007_Numero_Nota_Fiscal']}.txt", 777);

	$logTxt = "Transmissão feita no horario - ".date("d/m/Y H:i:s")."\r\n";
	$r = fwrite($h, $logTxt);
	
	if(chdir($diretorioNFeClass)){
		echo "\n\n Mudei de diretório, diretório atual: " . getcwd();
	}else{
		echo "\n\n NÃO de diretório, diretório atual: " . getcwd();
	}

	if($mT007['T007_Versao_NFe'] == '3.10'){
		require 'nfe3.class.php';
		echo "Versão: 3.10\n";
		$logTxt = "Include Versão: 3.10\r\n";
        $r = fwrite($h, $logTxt);
	} else if($mT007['T007_Versao_NFe'] == '4.00'){
		require 'nfe4.class.php';
		echo "Versão: 4.00\n";
		$logTxt = "Include Versão: 4.00\r\n";
        $r = fwrite($h, $logTxt);
	} else {
		require 'nfe2.class.php';
		echo "Versâo: 2.00\n";
		$logTxt = "Include Versão: 2.00 \r\n";
        $r = fwrite($h, $logTxt);
	}
	chdir($home);

	if ($nUltimaNF!=$mT007['T007_Numero_Nota_Fiscal']){
		echo "\n";
		echo "*****************************************\n";
		echo "***********************************************************\n";
		echo "*****************************************************************************\n";
		echo "**************************************************************************************\n";
		echo "**\n";
		echo "**              >>>>>  INICIO PROCESSO <<<<<\n";
		echo "** \n";
		echo "**              Banco  : {$Q001['Q001_Database']}        \n";
		echo "**              Empresa: {$mC004['C004_Nome_Empresa']}        \n";
		echo "**              Cliente: {$mT007['D024_Nome_Empresa']}        \n";
		echo "**              NF     : {$mT007['T007_Numero_Nota_Fiscal']}        \n";
		echo "**              Tipo   : {$mT007['T007_Flag_ACP']}        \n";
		echo "**\n";
		echo "**************************************************************************************\n";
		echo "*****************************************************************************\n";
		echo "************************************************************\n";
		echo "******************************************\n";
		echo "\n";
		$nUltimaNF = $mT007['T007_Numero_Nota_Fiscal'];

		$logTxt = "\r\n QueueNF - Inicio do processo \r\n";
		$logTxt .= "Banco  : {$Q001['Q001_Database']} \r\n";
		$logTxt .= "Cliente: {$mT007['D024_Nome_Empresa']} \r\n";
		$logTxt .= "NF     : {$mT007['T007_Numero_Nota_Fiscal']} \r\n";
		$logTxt .= "Tipo   : {$mT007['T007_Flag_ACP']} \r\n";
        $r = fwrite($h, $logTxt);
	}

	// Caso tenha número do recibo e não tenha chave de acesso limpar número do recibo
	if(!empty($mT007['T007_Numero_Recibo_Nfe']) AND empty($mT007['T007_Chave_Acesso_Nfe'])){
		mysqli_query("UPDATE T007 
						SET T007_Numero_Recibo_Nfe='' 
					  WHERE T007_Id='{$mT007['T007_Id']}'");
		$erro = mysql_error();
		if(!empty($erro)){
			msg_erro_nfe(122);
			$logTxt = "\r\n Erro 122 mysql update T007 - UPDATE T007 SET T007_Numero_Recibo_Nfe='' WHERE T007_Id='{$mT007['T007_Id']}' - {$erro}\r\n";
        	$r = fwrite($h, $logTxt);
		}

		$logTxt = "Encontrou numero do recibo e nao encontrou chave de acesso - Limpou número do recibo. \r\n";
		$r = fwrite($h, $logTxt);
		
		echo "**********************************************************************************\n";
		echo "* LIMPOU NUMERO DO RECIBO\n";
		echo "**********************************************************************************\n";
	}

	/*
	* Refazer / Via Scan
	*/

	if ($Q001['Q001_Flag_Refazer'] == 'S' || (empty($mT007['T007_Numero_Recibo_Nfe']) and empty($mT007['T007_Numero_Protocolo_Nfe']) and empty($mT007['T007_Chave_Acesso_Nfe']))) {
		echo "**********************************************************************************\n";
		echo "********************** ENTROU REFAZER ********************************************\n";
		echo "**********************************************************************************\n";

		$logTxt = "Entrou no refazer. \r\n";
		$r = fwrite($h, $logTxt);
		
		$modelo = ($mT007['T007_Flag_ACP'] == '6') ? '55' : '65';

		if ($Q001['Q001_Flag_Scan'] == 'S') {
			msg_erro_nfe(11);
		} else {
			msg_erro_nfe(10);
		}
		
		$Scan = $Q001['Q001_Flag_Scan'];	
		
		if ($mT007['T007_Flag_SCAN']!='S' && $Scan=='S')
		{
			echo "**********************************************************************************\n";
			echo "********************** ENTROU SCAN ***********************************************\n";
			echo "**********************************************************************************\n";

			$logTxt = "Entrou no scan. \r\n";
			$r = fwrite($h, $logTxt);

			if (!empty($mT007['T007_Data_Hora_Protocolo_Nfe']))
			{
				echo "**********************************************************************************\n";
				echo "********************** ENTROU SCAN CONTINUE **************************************\n";
				echo "**********************************************************************************\n";

				$logTxt = "Entrou no scan continue, data hora do protocolo não está vazia. \r\n";
				$r = fwrite($h, $logTxt);
				fclose($h);

				msg_erro_nfe(118);
				continue;
			}

			// SVC - Não modifica mais a numeração
			/*
			$notaf=mysqli_query("select T007_Numero_Nota_Fiscal
    									from T007
     		 				           where T007_Flag_ACP='6'
    									 and T007_C004_Id='{$mT007['T007_C004_Id']}'
    									 and T007_Serie='900'
    								order by year(T007_Data_Emissao) desc,round(T007_Numero_Nota_Fiscal) desc limit 0,1;");
			$mT007_ultimo=mysql_fetch_array($notaf);
			if ($mT007_ultimo[0]>0)
			{
				$mT007_ultimo[0]++;
			}
			else
			{
				$mT007_ultimo[0]='1';
			}
			mysqli_query("update T007 set T007_Numero_Controle=T007_Numero_Nota_Fiscal,T007_Numero_Nota_Fiscal='{$mT007_ultimo[0]}',T007_Serie='900',T007_Flag_SCAN='S',T007_Chave_Acesso_Nfe='',T007_Numero_Recibo_Nfe='' where T007_Id='{$mT007['T007_Id']}'");
			mysqli_query("update T002 set T002_Numero_Duplicata=REPLACE(T002_Numero_Duplicata,'{$mT007['T007_Numero_Nota_Fiscal']}','{$mT007_ultimo[0]}'),T002_Numero_Documento='{$mT007_ultimo[0]}' where T002_T007_Id='{$mT007['T007_Id']}'");
			*/
			mysqli_query("update T007 set T007_Flag_SCAN='S',T007_Chave_Acesso_Nfe='',T007_Numero_Recibo_Nfe='' where T007_Id='{$mT007['T007_Id']}'");

			echo "**********************************************************************************\n";
			echo "********************** EXECUTOU UPDATE SCAN **************************************\n";
			ECHO "UPDAE: update T007 set T007_Flag_SCAN='S',T007_Chave_Acesso_Nfe='',T007_Numero_Recibo_Nfe='' where T007_Id='{$mT007['T007_Id']}'\n";
			echo "**********************************************************************************\n";

			$logTxt = "Executou o update scan: update T007 set T007_Flag_SCAN='S',T007_Chave_Acesso_Nfe='',T007_Numero_Recibo_Nfe='' where T007_Id='{$mT007['T007_Id']}'. \r\n";
			$r = fwrite($h, $logTxt);

			echo "\n\n\n**********************************************************************************\n";
			echo "************************ INSERE LOG T127 *****************************************\n";
			echo "**********************************************************************************\n\n\n";

			$logTxt = "INSERE LOG T127 - Limpou chave de acesso envio via SVC\r\n";
			$r = fwrite($h, $logTxt);

			mysqli_query("insert into T127 (T127_C004_Id,
											T127_D024_Id,
											T127_Chave_Acesso_Nfe,
											T127_Numero_Nota_Fiscal,
											T127_Data_Transmicao,
											T127_Retorno_Sefaz,
											T127_Modelo,
											T127_T007_Id
											) values (
											'{$mT007['T007_C004_Id']}',
											'{$mT007['T007_D024_Id']}',
											'{$mT007['T007_Chave_Acesso_Nfe']}',
											'{$mT007['T007_Numero_Nota_Fiscal']}',
											now(),
											'Limpou chave de acesso envio via SVC',
											'{$modelo}',
											'{$nT007_Id}')");

			$cErro = mysql_error();
			
			if (!empty($cErro))
			{
				msg_erro_nfe(111);
				$logTxt = "Erro 111 mysql ao inserir log T127 - Limpou chave de acesso envio via SVC - {$erro}\r\n";
				$logTxt = "Clausula: insert into T127 (T127_C004_Id,
														T127_D024_Id,
														T127_Chave_Acesso_Nfe,
														T127_Numero_Nota_Fiscal,
														T127_Data_Transmicao,
														T127_Retorno_Sefaz,
														T127_Modelo,
														T127_T007_Id
														) values (
														'{$mT007['T007_C004_Id']}',
														'{$mT007['T007_D024_Id']}',
														'{$mT007['T007_Chave_Acesso_Nfe']}',
														'{$mT007['T007_Numero_Nota_Fiscal']}',
														now(),
														'Limpou chave de acesso envio via SVC',
														'{$modelo}',
														'{$nT007_Id}')\r\n";
				$r = fwrite($h, $logTxt);
				fclose($h);
				return false;
			}
		}
		else if ($mT007['T007_Flag_SCAN']=='S' and $Scan!='S')
		{
			echo "**********************************************************************************\n";
			echo "********************** ENTROU ELSEIF T007_Flag_Scan='S' AND scan != S ************\n";
			echo "**********************************************************************************\n";

			$logTxt = "ENTROU ELSE IF T007_Flag_Scan='S' AND scan != S. \r\n";
			$r = fwrite($h, $logTxt);

			if (!empty($mT007['T007_Data_Hora_Protocolo_Nfe']))
			{
				msg_erro_nfe(118);
				$logTxt = "Erro 118 T007_Data_Hora_Protocolo_Nfe não está vazio. \r\n";
				$r = fwrite($h, $logTxt);
				fclose($h);
				continue; 
			}

			// SVC - Não modifica mais a numeração
			/*
			$notaf=mysqli_query("select T007_Numero_Nota_Fiscal
    									from T007
     		 				           where T007_Flag_ACP='6'
    									 and T007_C004_Id='{$mT007['T007_C004_Id']}'
    									 and T007_Serie='1'
    								order by year(T007_Data_Emissao) desc,round(T007_Numero_Nota_Fiscal) desc limit 0,1;");
			$mT007_ultimo=mysql_fetch_array($notaf);
			if ($mT007_ultimo[0]>0)
			{
				$mT007_ultimo[0]++;
			}
			else
			{
				$mT007_ultimo[0]='1';
			}
			if (!empty($mT007['T007_Numero_Controle']))
			{
				$mT007_ultimo[0]=$mT007['T007_Numero_Controle'];
			}
			mysqli_query("update T007 set T007_Numero_Controle='',T007_Numero_Nota_Fiscal='{$mT007_ultimo[0]}',T007_Serie='1',T007_Flag_SCAN='N',T007_Chave_Acesso_Nfe='',T007_Numero_Recibo_Nfe='' where T007_Id='{$mT007['T007_Id']}'");
			mysqli_query("update T002 set T002_Numero_Duplicata=REPLACE(T002_Numero_Duplicata,'{$mT007['T007_Numero_Nota_Fiscal']}','{$mT007_ultimo[0]}'),T002_Numero_Documento='{$mT007_ultimo[0]}' where T002_T007_Id='{$mT007['T007_Id']}'");
			*/
			mysqli_query("update T007 set T007_Flag_SCAN='N',T007_Chave_Acesso_Nfe='',T007_Numero_Recibo_Nfe='' where T007_Id='{$mT007['T007_Id']}'");
			echo "**********************************************************************************\n";
			echo "********************** EXECUTOU UPDATE SCAN **************************************\n";
			ECHO "UPDAE: update T007 set T007_Flag_SCAN='N',T007_Chave_Acesso_Nfe='',T007_Numero_Recibo_Nfe='' where T007_Id='{$mT007['T007_Id']}'\n";
			echo "**********************************************************************************\n";

			$logTxt = "EXECUTOU UPDATE SCAN: update T007 set T007_Flag_SCAN='N',T007_Chave_Acesso_Nfe='',T007_Numero_Recibo_Nfe='' where T007_Id='{$mT007['T007_Id']}' \r\n";
			$r = fwrite($h, $logTxt);

			mysqli_query("insert into T127 (T127_C004_Id,
											T127_D024_Id,
											T127_Chave_Acesso_Nfe,
											T127_Numero_Nota_Fiscal,
											T127_Data_Transmicao,
											T127_Retorno_Sefaz,
											T127_Modelo,
											T127_T007_Id
											) values (
											'{$mT007['T007_C004_Id']}',
											'{$mT007['T007_D024_Id']}',
											'{$mT007['T007_Chave_Acesso_Nfe']}',
											'{$mT007['T007_Numero_Nota_Fiscal']}',
											now(),
											'Limpou chave de acesso tentativa SVC e retorno tentativa normal.',
											'{$modelo}',
											'{$nT007_Id}')");

			$cErro = mysql_error();
			
			if (!empty($cErro))
			{
				msg_erro_nfe(111);
				$logTxt = "Erro 111 mysql insert T127 - Limpou chave de acesso tentativa SVC e retorno tentativa normal. - {$erro}  \r\n";
				$logTxt .= "Clausula: insert into T127 (T127_C004_Id,
														T127_D024_Id,
														T127_Chave_Acesso_Nfe,
														T127_Numero_Nota_Fiscal,
														T127_Data_Transmicao,
														T127_Retorno_Sefaz,
														T127_Modelo,
														T127_T007_Id
														) values (
														'{$mT007['T007_C004_Id']}',
														'{$mT007['T007_D024_Id']}',
														'{$mT007['T007_Chave_Acesso_Nfe']}',
														'{$mT007['T007_Numero_Nota_Fiscal']}',
														now(),
														'Limpou chave de acesso tentativa SVC e retorno tentativa normal.',
														'{$modelo}',
														'{$nT007_Id}') \r\n";
				$r = fwrite($h, $logTxt);
				fclose($h);
				
				return false;
			}
			
		}
		
		$cClausula  = " select ";

		$cClausula .= " D024_Nome_Empresa, ";
		$cClausula .= " D024_Numero_Suframa, ";
		$cClausula .= " d24d20.D020_Nome_Cidade as Cliente_Cidade, ";
		$cClausula .= " d24d20.D020_Codigo_IBGE as Cliente_Cidade_IBGE, ";
		$cClausula .= " d24d18.D018_UF as Cliente_Estado, ";
		$cClausula .= " d24d18.D018_Codigo_IBGE as Cliente_UF_IBGE, ";
		$cClausula .= " D024_DDD_Telefone_1, ";
		$cClausula .= " D024_Telefone_1, ";
		$cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";
		$cClausula .= " D024_Cpf, ";
		$cClausula .= " D024_Cnpj, ";
		$cClausula .= " D024_Inscricao_Estadual,";
		$cClausula .= " D022_Inscricao_Estadual,";
		$cClausula .= " D024_Bairro,";
		$cClausula .= " D024_Cep,";
		$cClausula .= " D024_Endereco,";
		$cClausula .= " D024_Numero,";
		$cClausula .= " D024_Complemento,";

		$cClausula .= " D030_Nome_Pais, ";
		$cClausula .= " D030_Codigo_Pais, ";

		$cClausula .= " T007_Flag_ACP, ";
		$cClausula .= " T007_Numero_Recibo_Nfe, ";
		$cClausula .= " T007_Numero_Protocolo_Nfe, ";
		$cClausula .= " T007_Data_Hora_Protocolo_Nfe, ";
		$cClausula .= " T007_Chave_Acesso_Nfe, ";

		$cClausula .= " C004_Nome_Empresa, ";
		$cClausula .= " C004_Logradouro, ";
		$cClausula .= " C004_Numero, ";
		$cClausula .= " C004_Bairro, ";
		$cClausula .= " C004_Cep, ";
		$cClausula .= " c4d20.D020_Nome_Cidade as Empresa_Cidade, ";
		$cClausula .= " c4d20.D020_Codigo_IBGE as Empresa_Cidade_IBGE, ";
		$cClausula .= " c4d18.D018_UF as Empresa_UF, ";
		$cClausula .= " c4d18.D018_Estado as Empresa_Estado, ";
		$cClausula .= " c4d18.D018_Codigo_IBGE as Empresa_UF_IBGE, ";

		$cClausula .= " d22d20.D020_Nome_Cidade as Transportadora_Cidade, ";
		$cClausula .= " d22d18.D018_UF as Transportadora_Estado, ";

		$cClausula .= " C004_DDD_Telefone, ";
		$cClausula .= " C004_Telefone, ";
		$cClausula .= " C004_Fax, ";
		$cClausula .= " C004_Cnpj, ";
		$cClausula .= " C004_Inscricao_Estadual, ";
		$cClausula .= " C004_CNAE_Fiscal ";

		$cClausula .= " from T007 ";
		$cClausula .= " left join D024 on D024_Id=T007_D024_Id ";
		$cClausula .= " left join D022 on D022_Id=T007_D022_Id ";
		$cClausula .= " left join C004 on C004_Id=T007_C004_Id ";
		$cClausula .= " left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno ";
		$cClausula .= " left join D020 as c4d20 on c4d20.D020_Id=C004_D020_Id ";
		$cClausula .= " left join D018 as c4d18 on c4d18.D018_Id=c4d20.D020_D018_Id ";
		$cClausula .= " left join D020 as d24d20 on d24d20.D020_Id=D024_D020_Id ";
		$cClausula .= " left join D018 as d24d18 on d24d18.D018_Id=D024_D018_Id ";
		$cClausula .= " left join D020 as d22d20 on d22d20.D020_Id=D022_D020_Id ";
		$cClausula .= " left join D018 as d22d18 on d22d18.D018_Id=D022_D018_Id ";
		$cClausula .= " left join D030 on D030_Id=D024_D030_Id ";
		$cClausula .= " where T007_Id='$nT007_Id' ";
		$cSql_T007=mysqli_query($cClausula);
		$mT007=mysql_fetch_array($cSql_T007);

		if ($mT007['T007_Flag_ACP']!='6' && $mT007['T007_Flag_ACP']!='8')
		{
		  	msg_erro_nfe(105, "Tipo: {$mT007['T007_Flag_ACP']}");
		   	$logTxt = "T007_Flag_ACP é diferente de 6 e 8. - T007_Flag_ACP:{$mT007['T007_Flag_ACP']} \r\n";
			$r = fwrite($h, $logTxt);
			fclose($h);
		   	continue;
		}

		if (!empty($mT007['T007_Numero_Recibo_Nfe']) && !empty($mT007['T007_Numero_Protocolo_Nfe']))
		{
			$logTxt = "T007_Numero_Recibo_Nfe e T007_Numero_Protocolo_Nfe não estão vazios recolocando T007 na fila Q001  \r\n";
			$r = fwrite($h, $logTxt);
			fclose($h);

		    recolocarNaFila();		// denovo, sem refazer
		    continue;
		}
		
		if (!empty($mT007['T007_Numero_Recibo_Nfe']) and (!empty($mT007['T007_Chave_Acesso_Nfe']))) {
			$logTxt = "T007_Numero_Recibo_Nfe e T007_Chave_Acesso_Nfe não estão vazios recolocando T007 na fila Q001  \r\n";
			$r = fwrite($h, $logTxt);
			fclose($h);

			recolocarNaFila();		// denovo, sem refazer
		   	continue;
		}
		
		if (!empty($mT007['T007_Numero_Protocolo_Nfe']))
		{
			$logTxt = "T007_Numero_Protocolo_Nfe não está vazio.\r\n";
			$r = fwrite($h, $logTxt);
			fclose($h);

			msg_erro_nfe(119);
		   	continue;
		}

		if ($mT007['T007_Flag_ACP'] != 8){
			
			if ($mT007['D024_Flag_Pessoa_Fisica_Juridica']=='J')
			{
			   $Cnpj_Cpf=$mT007['D024_Cnpj'];
			}
			elseif ($mT007['D024_Flag_Pessoa_Fisica_Juridica']=='F')
			{
			   $Cnpj_Cpf=$mT007['D024_Cpf'];
			}
		}

		$mT007['D024_Inscricao_Estadual']=str_replace(array("-"," ","/"),"",$mT007['D024_Inscricao_Estadual']);

		$mT007['C004_Inscricao_Estadual']=str_replace(array("-"," ","/"),"",$mT007['C004_Inscricao_Estadual']);

		$mT007['D024_DDD_Telefone_1']=str_replace(array("-"," ","(",")","/"),"",$mT007['D024_DDD_Telefone_1']);
		$mT007['D024_Telefone_1']=str_replace(array("-"," ","(",")","/"),"",$mT007['D024_Telefone_1']);

		$mT007['C004_Nome_Empresa']=addslashes($mT007['C004_Nome_Empresa']);
		$mT007['Cliente_Cidade']=addslashes($mT007['Cliente_Cidade']);
		$mT007['D024_Bairro']=addslashes($mT007['D024_Bairro']);
		$mT007['D024_Nome_Empresa']=addslashes($mT007['D024_Nome_Empresa']);
		$mT007['D024_Endereco']=addslashes($mT007['D024_Endereco']);

		$cClausula  = " update T007 set ";

		$cClausula .= " T007_Empresa_Usuaria_Nome='".mysql_real_escape_string($mT007['C004_Nome_Empresa'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Endereco='".mysql_real_escape_string($mT007['C004_Logradouro'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Numero='".mysql_real_escape_string($mT007['C004_Numero'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Bairro='".mysql_real_escape_string($mT007['C004_Bairro'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Cep='".mysql_real_escape_string($mT007['C004_Cep'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Cidade='".mysql_real_escape_string($mT007['Empresa_Cidade'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Codigo_Cidade_IBGE='".mysql_real_escape_string($mT007['Empresa_Cidade_IBGE'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Uf='".mysql_real_escape_string($mT007['Empresa_UF'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Estado='".mysql_real_escape_string($mT007['Empresa_Estado'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Codigo_Estado_IBGE='".mysql_real_escape_string($mT007['Empresa_UF_IBGE'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Pais='BR', ";
		$cClausula .= " T007_Empresa_Usuaria_Telefone='".mysql_real_escape_string($mT007['C004_DDD_Telefone'])."".mysql_real_escape_string($mT007['C004_Telefone'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Fax='".mysql_real_escape_string($mT007['C004_Fax'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Cnpj='".mysql_real_escape_string($mT007['C004_Cnpj'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_Inscricao_Estadual='".mysql_real_escape_string($mT007['C004_Inscricao_Estadual'])."', ";
		$cClausula .= " T007_Empresa_Usuaria_CNAE_Fiscal='".mysql_real_escape_string($mT007['C004_CNAE_Fiscal'])."', ";
		$cClausula .= " T007_Transportadora_Inscricao_Estadual='".mysql_real_escape_string($mT007['D022_Inscricao_Estadual'])."', ";

		$cClausula .= " T007_Cliente_Razao='".mysql_real_escape_string($mT007['D024_Nome_Empresa'])."', ";
		if (!empty($Cnpj_Cpf))
		{
		   $cClausula .= " T007_Cliente_CNPJ_CPF='".mysql_real_escape_string($Cnpj_Cpf)."',";
		}
		$cClausula .= " T007_Cliente_Inscricao_Suframa='".mysql_real_escape_string($mT007['D024_Numero_Suframa'])."', ";
		$cClausula .= " T007_Cliente_Inscricao_Estadual='".mysql_real_escape_string($mT007['D024_Inscricao_Estadual'])."',";
		$cClausula .= " T007_Cliente_Endereco='".mysql_real_escape_string($mT007['D024_Endereco'])."', ";
		$cClausula .= " T007_Cliente_Endereco_Numero='".mysql_real_escape_string($mT007['D024_Numero'])."', ";
		$cClausula .= " T007_Cliente_Complemento='".mysql_real_escape_string($mT007['D024_Complemento'])."', ";
		$cClausula .= " T007_Cliente_Bairro='".mysql_real_escape_string($mT007['D024_Bairro'])."',";
		$cClausula .= " T007_Cliente_CEP='".mysql_real_escape_string($mT007['D024_Cep'])."', ";
		$cClausula .= " T007_Cliente_Cidade='".mysql_real_escape_string($mT007['Cliente_Cidade'])."', ";
		$cClausula .= " T007_Cliente_Codigo_Cidade_IBGE='".mysql_real_escape_string($mT007['Cliente_Cidade_IBGE'])."', ";
		$cClausula .= " T007_Cliente_Telefone='".mysql_real_escape_string($mT007['D024_DDD_Telefone_1'])."".mysql_real_escape_string($mT007['D024_Telefone_1'])."', ";
		$cClausula .= " T007_Cliente_Estado='".mysql_real_escape_string($mT007['Cliente_Estado'])."', ";
		$cClausula .= " T007_Cliente_Codigo_Estado_IBGE='".mysql_real_escape_string($mT007['Cliente_UF_IBGE'])."', ";

		$cClausula .= " T007_Cliente_Pais='".mysql_real_escape_string($mT007['D030_Nome_Pais'])."', ";
		$cClausula .= " T007_Cliente_Codigo_Pais='".mysql_real_escape_string($mT007['D030_Codigo_Pais'])."', ";
		
		$cClausula .= " T007_Transportadora_Cidade='".mysql_real_escape_string($mT007['Transportadora_Cidade'])."', ";
		$cClausula .= " T007_Transportadora_Estado='".mysql_real_escape_string($mT007['Transportadora_Estado'])."', ";

		$cClausula .= " T007_Flag_Contingencia='N',";
		$cClausula .= " T007_Chave_Acesso_Nfe='',";
		$cClausula .= " T007_Numero_Recibo_Nfe='',";
		$cClausula .= " T007_Data_Hora_Recebimento_Nfe='0000-00-00 00:00:00' ";
		$cClausula .= " where T007_Id='$nT007_Id'";
		mysqli_query($cClausula);
		$cErro = mysql_error();
		
		if (!empty($cErro))
		{
			$logTxt = "Erro 120 mysql select T007 - {$erro}\r\n";
			$logTxt .= "Clausula: {$cClausula}\r\n";
			$r = fwrite($h, $logTxt);
			fclose($h);

			echo "\n\nSQL Erro: " .$cErro . "\n\n"; 
		   	msg_erro_nfe(120);
		   	continue;
		}
		if ($Q001['Q001_Flag_Refazer'] == 'S') {
			$logTxt = "Q001_Flag_Refazer = S recolocando t007 na fila Q001\r\n";
			$r = fwrite($h, $logTxt);
			fclose($h);

			recolocarNaFila();
			continue;
		}
	}
	
	/**
	* Verificações Gerais que são feitas em todas as etapas
	*/
	$cClausula = <<<EOT
		 SELECT T007_Id,
				T007_Versao_NFe,
				T007_Ambiente_NFe,
				T007_Flag_ACP,
				T007_Flag_Cancelada,
				T007_Numero_Recibo_Nfe,
				T007_Numero_Protocolo_Nfe,
				C004_Senha_Certificados,
				T007_Flag_SCAN,
				D018_UF,
				T007_Numero_Nota_Fiscal,
				T007_Serie,
				T007_Chave_Acesso_Nfe,
				T007_Empresa_Usuaria_Uf,
				T007_Empresa_Usuaria_Codigo_Estado_IBGE,
				C004_Localizacao_Certificados,
				T007_C004_Id,
				T007_Data_Emissao,
				T007_Empresa_Usuaria_Cnpj,
				T007_D024_Id
		   FROM T007
	  LEFT JOIN D006 on D006_Id=T007_D006_Id
	  LEFT JOIN C004 on C004_Id=T007_C004_Id
	  LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
	  LEFT JOIN D018 on D018_Id=C004_D018_Id
	  LEFT JOIN D024 ON D024_Id=T007_D024_Id
	      WHERE T007_Id='{$nT007_Id}';
EOT;
	$cSql_T007=mysqli_query($cClausula);
	$mT007=mysql_fetch_array($cSql_T007);
	
	if (empty($mT007)) {
		$logTxt = "Erro 100 select mysql T007 - Não encontrou T007 - {$cClausula}\r\n";
		$r = fwrite($h, $logTxt);
		fclose($h);
		msg_erro_nfe(100);
		continue;
	}

	// Verificações
	if ($mT007['T007_Versao_NFe']=='1.10')
	{
		$logTxt = "Versao da NF - T007_Versao_NFe = 1.10 \r\n";
		$r = fwrite($h, $logTxt);
		fclose($h);

		msg_erro_nfe(101);
		continue;
	}
	elseif ($mT007['T007_Versao_NFe']=='2.00' || $mT007['T007_Versao_NFe']=='3.10' || $mT007['T007_Versao_NFe']=='4.00')
	{
		// OK para continuar!
	}
	else
	{
		$logTxt = "Versao da NF - T007_Versao_NFe é diferente das versões: 1.10, 2.00, 3.10 e 4.00 \r\n";
		$r = fwrite($h, $logTxt);
		fclose($h);

		msg_erro_nfe(102);
		continue;
	}
	
	if ($mConfig['C020_NFe_Treinamento']=='S' and $mT007['T007_Ambiente_NFe']!='2')
	{
		$logTxt = "Campo C020_NFe_Treinamento=S e T007_Ambiente_NFe diferente de 2 \r\n";
		$r = fwrite($h, $logTxt);
		fclose($h);

		msg_erro_nfe(103);
		continue;
	}
	
	if ($mConfig['C020_NFe_Tipo_Certificado']=='A3') {
		$logTxt = "Campo C020_NFe_Tipo_Certificado=A3. \r\n";
		$r = fwrite($h, $logTxt);
		fclose($h);

		msg_erro_nfe(104);
		continue;
	}
	
	if ($mT007['T007_Flag_ACP']!='6' && $mT007['T007_Flag_ACP']!='8')
	{
		$logTxt = "T007_Flag_ACP é diferente de 6 e 8. - T007_Flag_ACP={$mT007['T007_Flag_ACP']} \r\n";
		$r = fwrite($h, $logTxt);
		fclose($h);

		msg_erro_nfe(105, "Tipo: {$mT007['T007_Flag_ACP']}");
		continue;
	}
	
	if ($mT007['T007_Flag_Cancelada']=='S' and (empty($mT007['T007_Numero_Recibo_Nfe']) or empty($mT007['T007_Numero_Protocolo_Nfe'])))
	{
		$logTxt = "T007 está cancelada e campos T007_Numero_Recibo_Nfe ou T007_Numero_Protocolo_Nfe estão vazios. \r\n";
		$r = fwrite($h, $logTxt);
		fclose($h);

		msg_erro_nfe(106);
		continue;
	}
	
	/*if ($mT007[T007_Ambiente_NFe]=='2')
	{
		echo "<div style='position:absolute;top:5%;left:40%;'><font class=fonte_atencao>Ambiente de Homologação</font></div>";
	}*/
	
	/*
	if ($mT007[T007_Flag_SCAN]=='S')
	{
		echo "<div style='position:absolute;top:10%;left:85%;'><font class=fonte_atencao><blink>SCAN ATIVADO</blink></font></div>";
	}
	*/
	
	if ($mConfig['C020_NFe_Solicitar_Senha_Certificado']=='S')
	{
		$logTxt = "Campo C020_NFe_Solicitar_Senha_Certificado está como s \r\n";
		$r = fwrite($h, $logTxt);

		if (!empty($Senha) and $Senha!=$mT007['C004_Senha_Certificados'])
	    {
			$logTxt = "Campo C004_Senha_Certificados está diferente da senha: C004_Senha_Certificados:{$mT007['C004_Senha_Certificados']} e senha:{$Senha}\r\n";
			$r = fwrite($h, $logTxt);
			fclose($h);

			msg_erro_nfe(107);
			continue;
		}
	    if (empty($Senha))
	    {
			$logTxt = "Senha está vazia: {$Senha}\r\n";
			$r = fwrite($h, $logTxt);
			fclose($h);

	    	msg_erro_nfe(108);
	    	continue;
		}
	}
	
	/*if($cContingencia=='S')
	{
		$cClausula  = " update T007 set ";
		$cClausula .= " T007_Flag_Contingencia = 'S' ";
		$cClausula .= " where T007_Id='$nT007_Id' ";
		mysqli_query($cClausula);
	}*/

	// SMARTNESS
	// require 'hardness/class/nfe4.class.php';
	// echo "Versão: 4.00\n";
	// $logTxt = "Include Versão: 4.00\r\n";
	// $r = fwrite($h, $logTxt);
	// SMARTNESS
	
	// Classe da NFe
	$oNFE = new classeNFe($mT007['T007_Flag_ACP']);
	$oNFE->setT007Id($nT007_Id);
	$oNFE->setPastaArquivoXML("{$Diretorio_NFe}/xml");
	
	if ($mT007['T007_Flag_SCAN'] == 'S')
	{
		//$oNFE->setTipoDocAuxiliar(3);
		/*
		I - pela Sefaz Virtual do Ambiente Nacional, disponibilizada pela Secretaria da Receita Federal do 
		Brasil, para os Estados do Acre, Alagoas, Amapá, Minas Gerais, Paraíba, Rio de Janeiro, Rio Grande do 
		Sul, Rondônia, Roraima, Santa Catarina, Sergipe, São Paulo e Tocantins e para o Distrito Federal; e
		II - pela Sefaz Virtual do Rio Grande do Sul, disponibilizada pelo Estado do Rio Grande do Sul, 
		para os estados do Amazonas, Bahia, Ceará, Espírito Santo, Goiás, Maranhão, Mato Grosso, Mato 
		Grosso do Sul, Pará, Pernambuco, Piauí, Paraná e Rio Grande do Norte. 
		*/
		if ($mT007['D018_UF'] == 'AC' || $mT007['D018_UF'] == 'AL' || $mT007['D018_UF'] == 'AP' || $mT007['D018_UF'] == 'MG' || $mT007['D018_UF'] == 'PB' || $mT007['D018_UF'] == 'RJ' || $mT007['D018_UF'] == 'RS' || $mT007['D018_UF'] == 'RO' || $mT007['D018_UF'] == 'RR' || $mT007['D018_UF'] == 'SC' || $mT007['D018_UF'] == 'SE' || $mT007['D018_UF'] == 'SP' || $mT007['D018_UF'] == 'TO' || $mT007['D018_UF'] == 'DF') {
			$oNFE->setTipoDocAuxiliar(6);	// SVC-AN
		} else {
			$oNFE->setTipoDocAuxiliar(7);	// SVC-RS
		}

		//SE FOR  NFC-E
		if($mT007['T007_Flag_ACP']=='8'){
			$oNFE->setTipoDocAuxiliar(9);	// SCAN
		}
	}
	elseif ($mConfig['C020_NFe_SCAN_Ativado']=='S')
	{
		$oNFE->setTipoDocAuxiliar(3);
	}
	else
	{
		$oNFE->setTipoDocAuxiliar(1);
	}
	
	$Origem="{$mT007['T007_Numero_Nota_Fiscal']}_{$mT007['T007_Serie']}.xml";
	$Destino="NFe{$mT007['T007_Chave_Acesso_Nfe']}.xml";
	
	if (!empty($mT007['T007_Numero_Protocolo_Nfe']))
	{
		$etapaAtual = 4;
		echo date("d/m/Y H:i:s") . " Etapa 4: OK!\n";
		//include ('scripts/queueNF/etapa4-enviarEmail.php');
		msg_erro_nfe(9);
		unlink("/tmp/QueueNF-{$Q001['Q001_Database']}-{$Q001['Q001_C004_Id']}-{$mT007['T007_Numero_Nota_Fiscal']}.txt");
		
		//include ('scripts/queueNF/etapa4-gerarDanfe.php');
		//-- SE TEM PROTOCOLO, ABRE A DANFE
		/*echo "&nbsp;Aguarde . . .&nbsp;&nbsp;&nbsp;&nbsp;abrindo Danfe $mT007[T007_Numero_Nota_Fiscal] . . .";
		echo "</div>";
		echo "<script>";
		echo "location.replace('../almoxarifado/gerar_danfe.php?T007_Id=$nT007_Id');";
		echo "</script>";
		exit;*/
	}
	elseif ((file_exists("$Diretorio_NFe/xml/assinado/NFe{$mT007['T007_Chave_Acesso_Nfe']}.xml") and (!empty($mT007['T007_Chave_Acesso_Nfe']))) || (!empty($mT007['T007_Numero_Recibo_Nfe']) and (!empty($mT007['T007_Chave_Acesso_Nfe']))))
	{
		$xml = file_get_contents("$Diretorio_NFe/xml/assinado/NFe{$mT007['T007_Chave_Acesso_Nfe']}.xml");
		//-- SE O ARQUIVO DE ASSINATURA JA EXISTIR, PASSA DIRETO PRO ENVIO
		$etapaAtual = 3;
		$logTxt = date("d/m/Y H:i:s")." - Include Etapa 3: Enviando XML \r\n";
		$r = fwrite($h, $logTxt);

		echo date("d/m/Y H:i:s") . " Etapa 3: Enviando XML\n";

		/*if ($mT007['T007_Flag_ACP']=='6') {
			include ('scripts/queueNF/etapa3-enviarXml.php');
		} else {
			include ('scripts/queueNF/etapa3-enviarXml-sincrona.php');
		} */

		// A partir de 01/09/2025 o envio de uma NF precisa ser sincrona, hoje não tem a opção para enviar em lote
		include ('scripts/queueNF/etapa3-enviarXml-sincrona.php');
	}
	elseif (empty($mT007['T007_Numero_Recibo_Nfe']) and empty($mT007['T007_Numero_Protocolo_Nfe']) and !empty($mT007['T007_Chave_Acesso_Nfe']))
	{
		$etapaAtual = 2;
		$logTxt = date("d/m/Y H:i:s")." - Include Etapa 2: Assinando XML \r\n";
		$r = fwrite($h, $logTxt);
		echo date("d/m/Y H:i:s") . " Etapa 2: Assinando XML\n";
		include ('scripts/queueNF/etapa2-assinarXml.php');
	}
	else
	{
		$etapaAtual = 1;
		$logTxt = date("d/m/Y H:i:s")." - Include Etapa 1: Gerando XML \r\n";
		$r = fwrite($h, $logTxt);
		echo date("d/m/Y H:i:s") . " Etapa 1: Gerando XML\n";
		include ('scripts/queueNF/etapa1-gerarXml.php');

		$validaArquivoXML = mysql_query("select T007_Chave_Acesso_Nfe from T007 where T007_Id={$mT007['T007_Id']}");
		$mValidaArquivoXML = mysql_fetch_array($validaArquivoXML);

		if (!empty($mValidaArquivoXML['T007_Chave_Acesso_Nfe'])) {
			if (!file_exists("$Diretorio_NFe/xml/assinado/{$mT007['T007_Numero_Nota_Fiscal']}_{$mT007['T007_Serie']}.xml")){
				msg_erro_nfe(109);
				continue;
			}	
		}			

	}

	$cClausula = <<<EOT
			SELECT D024_Nome_Empresa,
				   T007_Numero_Nota_Fiscal,
				   T007_Flag_ACP,
				   T007_Valor_Total,
				   T007_Chave_Acesso_Nfe,
				   T007_Numero_Recibo_Nfe,
				   T007_Numero_Protocolo_Nfe,
				   T007_Data_Hora_Protocolo_Nfe
			  FROM T007
	     LEFT JOIN D006 on D006_Id=T007_D006_Id
	     LEFT JOIN C004 on C004_Id=T007_C004_Id
	     LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
	     LEFT JOIN D018 on D018_Id=C004_D018_Id
	     LEFT JOIN D024 ON D024_Id=T007_D024_Id
	  	     WHERE T007_Id='{$nT007_Id}';
EOT;
	$cSql_T007=mysqli_query($cClausula);
	$mT007=mysql_fetch_array($cSql_T007);

	$cErro = mysql_error();
	if (!empty($cErro))	{
		$cSql = mysqli_query("SELECT DATABASE();");
		$cSqlDb = mysql_fetch_assoc($cSql);

		$logTxt = "(1)Banco de dados atual:".json_encode($cSqlDb)."\r\n";
		$r = fwrite($h, $logTxt);

		$logTxt = "Erro 100 select T007 - {$cErro}\r\n";
		$r = fwrite($h, $logTxt);
		fclose($h);
		msg_erro_nfe(100);
		continue;
	}

	if (empty($mT007)) {
		$cSql = mysqli_query("SELECT DATABASE();");
		$cSqlDb = mysql_fetch_assoc($cSql);

		$logTxt = "(2)Banco de dados atual:".json_encode($cSqlDb)."\r\n";
		$r = fwrite($h, $logTxt);

		$logTxt = "Erro 100(2) - Não encontrou nenhum T007 - SELECT D024_Nome_Empresa,
							T007_Numero_Nota_Fiscal,
							T007_Flag_ACP,
							T007_Valor_Total,
							T007_Chave_Acesso_Nfe,
							T007_Numero_Recibo_Nfe,
							T007_Numero_Protocolo_Nfe,
							T007_Data_Hora_Protocolo_Nfe
					FROM T007
					LEFT JOIN D006 on D006_Id=T007_D006_Id
					LEFT JOIN C004 on C004_Id=T007_C004_Id
					LEFT JOIN C007 on C007_Id=T007_C007_Id_Vendedor_Interno
					LEFT JOIN D018 on D018_Id=C004_D018_Id
					LEFT JOIN D024 ON D024_Id=T007_D024_Id
						WHERE T007_Id='{$nT007_Id}'\r\n";
		$r = fwrite($h, $logTxt);
		fclose($h);

		msg_erro_nfe(100);
		continue;
	}

	$logTxt = "Final de processo \r\n";
	$logTxt .="Banco  : {$Q001['Q001_Database']}   NF : {$mT007['T007_Numero_Nota_Fiscal']}  Tipo : {$mT007['T007_Flag_ACP']}      \n";
	$logTxt .="Empresa: {$mC004['C004_Nome_Empresa']}        \n";
	$logTxt .="Cliente: {$mT007['D024_Nome_Empresa']} Valor : {$mT007['T007_Valor_Total']}       \n";
	$logTxt .="Chave  : {$mT007['T007_Chave_Acesso_Nfe']}        \n";
	$logTxt .="Recibo : {$mT007['T007_Numero_Recibo_Nfe']}      \n";
	$logTxt .="Protocl: {$mT007['T007_Numero_Protocolo_Nfe']} {$mT007['T007_Data_Hora_Protocolo_Nfe']}      \n";
	$r = fwrite($h, $logTxt);
	fclose($h);

	echo "\n";
	echo "**************************************************************************************\n";
	echo "**              Final de processo\n";
	echo "** \n";
	echo "**              Banco  : {$Q001['Q001_Database']}   NF : {$mT007['T007_Numero_Nota_Fiscal']}  Tipo : {$mT007['T007_Flag_ACP']}      \n";
	echo "**              Empresa: {$mC004['C004_Nome_Empresa']}        \n";
	echo "**              Cliente: {$mT007['D024_Nome_Empresa']} Valor : {$mT007['T007_Valor_Total']}       \n";
	echo "**              Chave  : {$mT007['T007_Chave_Acesso_Nfe']}        \n";
	echo "**              Recibo : {$mT007['T007_Numero_Recibo_Nfe']}      \n";
	echo "**              Protocl: {$mT007['T007_Numero_Protocolo_Nfe']} {$mT007['T007_Data_Hora_Protocolo_Nfe']}      \n";
	echo "**************************************************************************************\n";
	echo "\n";
	
}


/**
* Atualiza status da T007 e transmite para o servidor de LOG
* Nem sempre pode ser um erro, o nome msg_erro_nfe() foi utilizado porque já existia
* 
* @param int $codigo
* @param string $extra
*/
function msg_erro_nfe($codigo, $extra = '', $updateT007 = true, $retornoSefaz = '') {
	global $servidorId, $errosLogUrl, $etapaAtual, $Q001, $oNFE, $nT007_Id, $xml, $g;
	$cClausula = <<<EOT
					SELECT 
						T007_TX_Flag_Erro,
						T007_Numero_Nota_Fiscal,
						T007_Codigo_CFOP,
						T007_Serie,
						D024_Nome_Empresa,
						T007_Data_Emissao,
						C004_Nome_Abreviado,
						C004_Telefone,
						T007_Valor_Total
					FROM
						T007
					left join D006 on D006_Id=T007_D006_Id
					left join C004 on C004_Id=T007_C004_Id
					left join C007 on C007_Id=T007_C007_Id_Vendedor_Interno
					left join D024 on D024_Id=T007_D024_Id
					where T007_Id='{$nT007_Id}';
EOT;
	$cSql_T007=mysqli_query($cClausula);
	$mT007=mysql_fetch_array($cSql_T007);
	
	echo date("d/m/Y H:i:s") . " Codigo: {$codigo} - {$extra} - {$retornoSefaz}\n";
	// Grava erro na T007
	if ($updateT007) {
		$codigo_ = mysql_real_escape_string($codigo);
		$extra_ = mysql_real_escape_string($extra);
		$retornoSefaz_ = mysql_real_escape_string($retornoSefaz);
		$xml_ = mysql_real_escape_string($xml);
		mysqli_query("UPDATE T007 SET T007_TX_Resposta_Codigo = '{$codigo_}', T007_TX_Resposta_Extra = '{$extra_}', T007_TX_Retorno_Sefaz = '{$retornoSefaz_}', T007_Arquivo_XML_Temp = '{$xml_}' WHERE T007_Id = '{$nT007_Id}';");
		//mysqli_query("UPDATE T007 SET T007_TX_Resposta_Codigo = '{$codigo_}', T007_TX_Resposta_Extra = '{$extra_}', T007_TX_Retorno_Sefaz = '{$retornoSefaz_}' WHERE T007_Id = '{$nT007_Id}';");
		
		/*if (!mysqli_ping($g['conexaoBanco'])) {
			reconectarBanco($mT007['T007_Numero_Nota_Fiscal']);	
		}*/
	}

	// Envia erro para o servidor de log
	if($codigo > 99 || ($mT007['T007_TX_Flag_Erro'] == '1' && $codigo > 8)){
		//Marca a nota com erro
		mysqli_query("UPDATE T007 SET T007_TX_Flag_Erro = '1' WHERE T007_Id = '{$nT007_Id}';");

			echo "\n";
			echo "**************************************************************************************\n";
			echo "**              ENVIANDO ERRO PARA O SERVIDOR DE LOG\n";
			echo "** \n";
			echo "**  ERROSlOGUIRL: {$errosLogUrl}";	
			echo "** ";
			echo "**************************************************************************************\n";
			echo "\n";

		$log = array(
			'Servidor_Id' => $servidorId,
			'Database' => $Q001['Q001_Database'],
			'C004_Id' => $Q001['Q001_C004_Id'],
			'T007_Id' => $Q001['Q001_T007_Id'],
			'C007_Id' => $Q001['Q001_C007_Id'],
			'Etapa' => $etapaAtual,
			'Codigo_Resposta' => $codigo,
			'Resposta_Extra' => $extra,
			'Soap_Debug' => @$oNFE->soapDebug,
			'Numero_NF' => $mT007['T007_Numero_Nota_Fiscal'],
			'Retorno_Sefaz' => $retornoSefaz,
			'T007_Codigo_CFOP' => $mT007['T007_Codigo_CFOP'],
			'T007_Serie' => $mT007['T007_Serie'],
			'T007_D024_Nome_Empresa' => $mT007['D024_Nome_Empresa'],
			'T007_Data_Emissao' => $mT007['T007_Data_Emissao'],
			'C004_Nome_Abreviado' => $mT007['C004_Nome_Abreviado'],
			'C004_Telefone' => $mT007['C004_Telefone'],
			'Url' => $Q001['Q001_Url'],
			'T007_Valor_Total' => $mT007['T007_Valor_Total'],
			'XML' => $xml,
		);

		// Servidor de log
		$ch = curl_init();
		curl_setopt ($ch, CURLOPT_URL, $errosLogUrl);
		curl_setopt ($ch, CURLOPT_POST, 1);
		curl_setopt ($ch, CURLOPT_POSTFIELDS, 'log=' . urlencode(serialize($log)));
		curl_exec($ch);
		curl_close($ch);
	}
}

/**
* Recoloca a NF na fila para transmissão (na próxima irá identificar a próxima etapa, e assim pode diante)
* 
*/
function recolocarNaFila($Recibo = NULL) {
	global $Q001;

	$sql = "SELECT Q001_Id FROM `global`.Q001 WHERE Q001_Database = '{$Q001['Q001_Database']}' AND Q001_T007_Id = '{$Q001['Q001_T007_Id']}' AND '{$Q001['Q001_C004_Id']}'";
	$res = mysqli_query($sql);
	if (mysql_num_rows($res) > 0) {
/*		$sql = "UPDATE `global`.Q001 SET Q001_Flag_Refazer = 'N' WHERE Q001_Database = '{$Q001['Q001_Database']}' AND Q001_T007_Id = '{$Q001['Q001_T007_Id']}' AND '{$Q001['Q001_C004_Id']}'";
		echo "Forçando Refazer: '{$Q001['Q001_Database']}', '{$Q001['Q001_T007_Id']}', '{$Q001['Q001_C004_Id']}', '{$Q001['Q001_Url']}', '{$Q001['Q001_Path']}'\n";
		mysqli_query($sql); */
	} else {
		$sql = <<<EOT
INSERT INTO `global`.Q001
(`Q001_Database`,`Q001_T007_Id`,`Q001_C004_Id`,`Q001_Url`,`Q001_Path`,`Q001_C007_Id`,`Q001_DataHora`,`Q001_Recibo`,`Q001_Flag_Scan`)
VALUES
('{$Q001['Q001_Database']}', '{$Q001['Q001_T007_Id']}', '{$Q001['Q001_C004_Id']}', '{$Q001['Q001_Url']}', '{$Q001['Q001_Path']}', '{$Q001['Q001_C007_Id']}', now(), '{$Recibo}','{$Q001['Q001_Flag_Scan']}');
EOT;
		echo "Recolocando na fila: '{$Q001['Q001_Database']}', '{$Q001['Q001_T007_Id']}', '{$Q001['Q001_C004_Id']}', '{$Q001['Q001_Url']}', '{$Q001['Q001_Path']}, {$Q001['Q001_Flag_Scan']}'\n";
		mysqli_query($sql);
	}
}

function reconectarBanco($T007_Numero_Nota_Fiscal){
	global $confUsuario, $Q001;
	$arquivoNome =  "/tmp/QueueNF-{$Q001['Q001_Database']}-{$Q001['Q001_C004_Id']}-{$T007_Numero_Nota_Fiscal}.txt";
	$h = fopen($arquivoNome, "a");

	echo '\n\n Tentando reconectar Banco de Dados.';
	$logTxt = "Tentando reconectar Banco de Dados. \r\n";
	$r = fwrite($h, $logTxt);
	
	$con = @mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
	$Error = mysql_error();
	if (!empty($Error)) {
		echo '\n\n Erro 9.1 Nao foi possivel se reconectar Banco de Dados.';
		$logTxt = "Erro 9.1 Nao foi possivel se reconectar Banco de Dados. \r\n";
		fclose($h);
	}
	
	$dbs = mysqli_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
	if (isset($usarUTF8) && $usarUTF8) {
		mysql_set_charset('utf8');
		mysqli_query('SET NAMES utf8;');
	}
	fclose($h);
}