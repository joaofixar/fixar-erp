<?php
namespace hardness;
DEFINE("_SALTO_LINHA",CHR(13).CHR(10));


$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from D007 ";
$cClausula .= " left join C004 on C004_Id=D007_C004_Id ";
$cClausula .= " where D007_Id='$D007_Id' ";
$D007 = mysql_query($cClausula);
$mD007=mysql_fetch_array($D007);
log("D007_CNPJ: ".$mD007['D007_CNPJ']);

if (strlen($mD007['D007_Numero_Agencia'])<=0){
   return "Não informado o numero da agência no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Agencia'])>4){
   return "Numero da agência deve ter no máximo 4 caracteres";
}
if (strlen($mD007['D007_Numero_Conta'])<=0){
   return "Não informado o numero da conta no cadastro de contas";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])>2){
   return "Digito verificador da conta deve ter no máximo 2 caracteres";
}

$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from T002 ";
$cClausula .= " left join T117 on T117_Id=T002_T117_Id ";
$cClausula .= " left join D024 on D024_Id=T002_D024_Id ";
$cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
$cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
$cClausula .= " left join C004 on C004_Id=T002_C004_Id ";
$cClausula .= " left join D027 on D027_Id=T002_D027_Id ";
$cClausula .= " where $where ";
$cClausula .= " GROUP BY T117_Id";
//$cClausula .= " and T117_Flag_Enviar_Remessa='S' ";
$T117_validacao=mysql_query($cClausula);
echo mysql_error();

if (mysql_num_rows($T117_validacao)==0){
   return "Nenhum titulo foi selecionado para esta conta";
}
$numRegistros=mysql_num_rows($T117_validacao);


// Verificando se possuem boletos com o bosso número repetido
$cClausula_Nosso_Numero = <<<EOT
		 SELECT 
		 COUNT(*),
		 T117_Nosso_Numero
		 FROM T002 
		 LEFT JOIN T117 ON T117_Id=T002_T117_Id 
		 LEFT JOIN D024 ON D024_Id=T002_D024_Id 
		 LEFT JOIN D020 ON D020_Id=D024_D020_Id 
		 LEFT JOIN D018 ON D018_Id=D020_D018_Id 
		 LEFT JOIN C004 ON C004_Id=T002_C004_Id 
		 LEFT JOIN D027 ON D027_Id=T002_D027_Id 
		 WHERE $where
		 GROUP BY T117_Nosso_Numero
		HAVING COUNT(*) > 1
EOT;
$cClausula_Nosso_Numero = mysql_query($cClausula_Nosso_Numero);
$msg_boletos_duplicados='';
if(mysql_num_rows($cClausula_Nosso_Numero) > 0){
	while($mSql = mysql_fetch_array($cClausula_Nosso_Numero)){
		$msg_boletos_duplicados.=$mSql['T117_Nosso_Numero'].", ";
	}
	$msg_boletos_duplicados = trim($msg_boletos_duplicados, ', ');
}
if($msg_boletos_duplicados != ''){
	return "Os boletos com o 'nosso numero' abaixo estao duplicados, favor verificar: <br /> ".$msg_boletos_duplicados;
}

//-- ELIMINA O ARQUIVO CASO O MESMO EXISTA
$cArquivo='/tmp/remessa_033.txt';
if (file_exists($cArquivo)){
   unlink("$cArquivo");
}

$mErroCadastro=array();

while ($mT117 = mysql_fetch_array($T117_validacao)){
	$mT117['D024_Cpf']  = trim($mT117['D024_Cpf']);
	$mT117['D024_Cpf']  = str_replace(array(".","-","/"," "),"",$mT117['D024_Cpf']);
	
	$mT117['D024_Cnpj'] = trim($mT117['D024_Cnpj']);
	$mT117['D024_Cnpj'] = str_replace(array(".","-","/"," "),"",$mT117['D024_Cnpj']);
	
	$mT117['D024_Cep']  = trim($mT117['D024_Cep']);
	$mT117['D024_Cep']  = str_replace(array(".","-"," "),"",$mT117['D024_Cep']);
	
	if (empty($mT117['D024_Cpf']) and $mT117['D024_Flag_Pessoa_Fisica_Juridica']=='F'){ 
		$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem CPF. Remessa não gerada!"; 
	}
	
	if (empty($mT117['D024_Cnpj']) and $mT117['D024_Flag_Pessoa_Fisica_Juridica']=='J'){ 
		$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem CNPJ. Remessa não gerada!"; 
	}
	
	if (empty($mT117['D024_Endereco'])){ 
		$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Endereço. Remessa não gerada!"; 
	}

	if (empty($mT117['D024_Bairro'])){ 
		$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Bairro. Remessa não gerada!"; 
	}

	if (empty($mT117['D024_Cep'])){ 
		$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Cep. Remessa não gerada!"; 
	}
	    
	if (empty($mT117['D020_Nome_Cidade'])){ 
		$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Cidade. Remessa não gerada!"; 
	}
}

if (count($mErroCadastro)>0){
	return implode("\n",$mErroCadastro);
	exit;
}

$cSql_T117 = mysql_query($cClausula);

//-- INICIA UMA SESSAO NO MYSQL
mysql_query("set autocommit=0");
mysql_query("begin");

$cClausula="select * from T035 where T035_Data_Processamento >= '".date("Y-m-d")." 00:00:00' AND T035_Data_Processamento <='".date("Y-m-d")." 23:59:59'";
$cT035 = mysql_query($cClausula);
$numReg = mysql_num_rows($cT035)+1;
if ($numReg < 10){
	$nomeArquivo=date("dmy").'0'.$numReg;
} else {
	$nomeArquivo=date("dmy").$numReg;
}

$cClausula  = " insert into T035 ( ";
$cClausula .= " T035_D007_Id,  ";
$cClausula .= " T035_C004_Id, ";
$cClausula .= " T035_D062_Id, ";
$cClausula .= " T035_Data_Processamento, ";
$cClausula .= " T035_Nome_Arquivo, ";
$cClausula .= " T035_Numero_Titulos ";
$cClausula .= " ) values (  ";
$cClausula .= " '$D007_Id',  ";
$cClausula .= " '{$mD007['D007_C004_Id']}', ";
$cClausula .= " '{$mD007['D007_D062_Id']}', ";
$cClausula .= " now(), ";
$cClausula .= " '{$nomeArquivo}', ";
$cClausula .= " '{$numRegistros}' ";
$cClausula .= " )  ";
$cT035 = mysql_query($cClausula);
$cErro=mysql_error();
if (!empty($cErro))
{	
    mysql_query("ROLLBACK");
    return "Ocorreu um erro:$cErro/p/p$cClausula";
}
$nT035_Id = $g['mysqlLastId'];
$dadosContaSantander = montarDadosContaSantander($mD007);
$codigoTransmissaoSantander = montarCodigoTransmissaoSantander($mD007, $dadosContaSantander);

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//-- INICIO DA EXPORTACAO DOS DADOS....
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//            HEADER
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$cHoje = date("dmy",time());
$nSeq=1;
$cString="";
//001 001 9(001) Código do registro = 0
$cString  .= prepararCampo("Tipo de Registro","Num",1,0,"0");
 	
//002 002 9(001) Código da remessa = 1
$cString  .= prepararCampo("Operação","Num",1,0,"1");

//003 009 X(007) Literal de transmissão = REMESSA
$cString  .= prepararCampo("Literal de Remessa","Char",7,0,"REMESSA");

//010 011 9(002) Código do serviço = 01
$cString  .= prepararCampo("Código do Serviço","Num",2,0,"01");

//012 026 X(015) Literal de serviço = COBRANÇA
$cString  .= prepararCampo("Literal de serviço","Char",15,0,"COBRANCA");

//027 046 9(020) Código de transmissão / dados da agência-conta conforme nota 2
$cString  .= prepararCampo("Código de Transmissão","Num",20,0,$codigoTransmissaoSantander);

//047 076 X(030) Nome do cedente
$cString  .= prepararCampo("Nome da Empresa","Char",30,0,substr($mD007['C004_Nome_Empresa'],0,30));

//077 079 9(003) Código do Banco = 033
$cString  .= prepararCampo("Código do Banco","Num",3,0,"033");

//080 094 X(015) Nome do Banco = SANTANDER
$cString  .= prepararCampo("Nome do Banco","Char",15,0,"SANTANDER");

//095 100 9(006) Data de Gravação
$cString  .= prepararCampo("Data de Gravação","Num",6,0,$cHoje);

//101 116 9(016) Zeros 
$cString  .= prepararCampo("Zeros","Num",16,0,"");

//117 391 X(275) Brancos
$cString  .= prepararCampo("Brancos","Char",275,0,"");

//392 394 9(003) Número da versão da remessa opcional, se informada, será controlada pelo sistema (opcional = 000)   
$cString  .= prepararCampo("Número da versão da remessa opcional","Num",3,0,"");

//395 400 9(006) Número seqüencial do registro no arquivo = 000001
$cString  .= prepararCampo("Numero Sequencial do Registro","Num",6,0,"000001");

$cString .= _SALTO_LINHA;

$valorTotalRemessa=0;

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//            BODY
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

while ($mT117 = mysql_fetch_array($cSql_T117)){

	$valorTotalRemessa+=$mT117['T117_Valor_Total'];

	//utilizar apenas o T002_T035_Id
	// $cClausula  = " update T117 set ";
	// $cClausula .= " T117_T035_Id='$nT035_Id'  ";
	// $cClausula .= " where T117_Id='{$mT117['T117_Id']}' ";
	// mysql_query($cClausula);

	$cClausula  = " update T002 set ";
	$cClausula .= " T002_Flag_Enviar_Banco='N',"; 
	$cClausula .= " T002_T035_Id='$nT035_Id'  ";
	$cClausula .= " where T002_T117_Id='{$mT117['T117_Id']}' ";
	mysqli_query($cClausula);
	
	//-- NUMERO SEQUENCIAL PARA CADA LINHA DO ARQUIVO (CONFORME LEIAUTE)
	$nSeq++;

	//-- ELIMINA CARACATERES INVALIDOS PARA CFP OU CNPJ
	if ($mT117['D024_Flag_Pessoa_Fisica_Juridica']=='F'){
	   $cCpfCnpjTipo='01';
	   $mT117['D024_Cpf'] = trim($mT117['D024_Cpf']);
	   $cCpfCnpj  = str_replace(array(".","-","/"," "),"",$mT117['D024_Cpf']);
	} elseif ($mT117['D024_Flag_Pessoa_Fisica_Juridica']=='J'){
	   $cCpfCnpjTipo='02';
	   $mT117['D024_Cnpj'] = trim($mT117['D024_Cnpj']);
	   $cCpfCnpj  = str_replace(array(".","-","/"," "),"",$mT117['D024_Cnpj']);
	}

	$mT117['D024_Cpf']  = trim($mT117['D024_Cpf']);
	$mT117['D024_Cpf']  = str_replace(array(".","-","/"," "),"",$mT117['D024_Cpf']);
	
	$mT117['D024_Cnpj'] = trim($mT117['D024_Cnpj']);
	$mT117['D024_Cnpj'] = str_replace(array(".","-","/"," "),"",$mT117['D024_Cnpj']);
	
	$mT117['D024_Cep']  = trim($mT117['D024_Cep']);
	$mT117['D024_Cep']  = str_replace(array(".","-"," "),"",$mT117['D024_Cep']);
	
	unset($mT117['Instrucao_Protesto_1']);
	unset($mT117['Instrucao_Protesto_2']);
	if ($mT117['D024_Flag_Protestar'] != 'N') {
		$mT117['Instrucao_Protesto_1']=$mD007['D007_Instrucao_Protesto_1'];
		$mT117['Instrucao_Protesto_2']=$mD007['D007_Instrucao_Protesto_2'];
	}

	//001 001 9(001) Código do registro = 1
	$cString .= prepararCampo("Tipo de Registro","Num",1,0,"1");

	//002 003 9(002) Tipo de inscrição do cedente: 01 = CPF 02 = CGC
	$cString .= prepararCampo("Tipo de inscrição do cedente","Num",2,0,"02");
	
	log("D007_CNPJ: ".$mD007['D007_CNPJ']);
	$mD007['D007_CNPJ'] = str_replace(array(".","-","/"," "),"",$mD007['D007_CNPJ']);
	log("D007_CNPJ: ".$mD007['D007_CNPJ']);
	//004 017 9(014) CGC ou CPF do cedente
	$cString .= prepararCampo("CGC ou CPF do cedente","Num",14,0,$mD007['D007_CNPJ']);

	//018 021 9(004) Código da agência cedente (nota 2)
	$cString .= prepararCampo("Código da Agência Cedente","Num",4,0,$mD007['D007_Numero_Agencia']);    

	//022 029 9(008) Conta movimento cedente (nota 2)
	$cString .= prepararCampo("Conta Movimento Cedente","Num",8,0,$mD007['D007_Codigo_Cedente']);    

	//030 037 9(008) Conta cobrança cedente (nota 2)
	$cString .= prepararCampo("Conta Cobrança Cedente","Num",8,0,$dadosContaSantander['conta_cobranca']);    
	
	//038 062 X(025) Número de controle do participante, para controle por parte do cedente
	$cString .= prepararCampo("Conta","Char",25,0,$mT117['T002_Numero_Duplicata']);

	//063 070 9(008) Nosso número (nota 3)
	$cString .= prepararCampo("Nosso Número","Num",8,0,substr($mT117['T117_Nosso_Numero'], -8));
	
	//071 076 9(006) Data do segundo desconto
	$cString .= prepararCampo("Data do segundo desconto","Num",6,0,"0");

	//077 077 X(001) Branco
	$cString .= prepararCampo("Branco","Char",1,0,"");

	//078 078 9(001) Informação de multa = 4, se não houver informar zero
	$flagMulta = ($mT117['T117_Percentual_Multa'] > 0) ? 4 : 0;
	$cString  .= prepararCampo("Informação de multa","Num",1,0,$flagMulta);

	//079 082 9(004)v99 Percentual multa por atraso %
	$mT117['T117_Percentual_Multa'] = number_format($mT117['T117_Percentual_Multa'], 2, '', '');
	$cString  .= prepararCampo("Indentificação de Multa","Num",4,0,"{$mT117['T117_Percentual_Multa']}");

	//083 084 9(002) Unidade de valor moeda corrente = 00
	$cString .= prepararCampo("Moeda","Num", 2, 0, '00');
	
	//085 097 9(013)v99 Valor do título em outra unidade
	$cString .= prepararCampo("Valor do Titulo em outra unidade","Num",13,0,"0");

	//098 101 X(004) Brancos
	$cString .= prepararCampo("Brancos","Char", 4, 0, "");
	
	//102 107 9(006) Data para cobrança de multa
	$cString  .= prepararCampo("Data para cobrança de multa","Num",6,0,"0");

	//108 108 9(001) Código da carteira
	$cString  .= prepararCampo("Código da carteira","Num",1,0,"5");

	//109 110 9(002) Código da ocorrência
	$cString  .= prepararCampo("Código da ocorrência","Num",2,0,'01');

	//111 120 X(010) Seu número
	$cString .= prepararCampo("Seu Numero","Num",10,0,$mT117['T002_Numero_Duplicata']);        

	//121 126 9(006) Data de vencimento do título
	$cString .= prepararCampo("Vencimento","Date",6,0,"{$mT117['T117_Data_Vencimento']}");        

	//127 139 9(013)v99 Valor do título       
	$cString .= prepararCampo("Valor do Titulo","Num",13,2,"{$mT117['T117_Valor_Total']}");        
	    
	//140 142 9(003) Número do Banco cobrador
	$cString .= prepararCampo("Código do Banco","Num",3,0,"033");    
	    
	//143 147 9(005) Código da agência cobradora
	$cString .= prepararCampo("AGÊNCIA COBRADORA","Num",5,0,"");        
	    
	//148 149 9(002) Espécie de documento
	$cString .= prepararCampo("Especie","Num",2,0,"01");        
	    
	//150 150 X(001) Tipo de aceite = N
	$cString .= prepararCampo("Aceite","Char",1,0,"N");

	//151 156 9(006) Data da emissão do título
	$cString .= prepararCampo("Data de Emissão","Date",6,0,"{$mT117['T117_Data_Emissao']}");        
	    
	//157 158 9(002) Primeira instrução cobrança
	$cString .= prepararCampo("Instrução 1","Num",2,0,"{$mT117['Instrucao_Protesto_1']}");        
	    
	//159 160 9(002) Segunda instrução cobrança
	$cString .= prepararCampo("Instrução 2","Num",2,0,"{$mT117['Instrucao_Protesto_2']}");  

	//161 173 9(013)v99 Valor de mora a ser cobrado por dia de atraso
	$jurosDia = round((($mT117['T117_Percentual_Juros_Dia']/30)/100)*$mT117['T002_Valor_Total'],2);
	$cString .= prepararCampo("Juros de 1 dia","Num",13,2,"{$jurosDia}");
	    
	//174 179 9(006) Data limite para concessão de desconto
	$cString .= prepararCampo("Desconto até","Date",6,0,"");        

	//180 192 9(013)v99 Valor de desconto a ser concedido
	$cString .= prepararCampo("Valor do desconto","Num",13,2,"");        
	    
	//193 205 9(013)v99 Valor do IOF
	$cString .= prepararCampo("Valor do IOF","Num",13,2,"");               

	//206 218 9(013)v99 Valor do abatimento
	$cString .= prepararCampo("Abatimento","Num",13,2,"");        
	    
	//219 220 9(002) Tipo de inscrição do sacado
	$cString .= prepararCampo("Código de Inscrição","Num",2,0,"$cCpfCnpjTipo");        
	    
	//221 234 9(014) CGC ou CPF do sacado
	$cString .= prepararCampo("Número de Inscrição","Num",14,0,"$cCpfCnpj");

	$mT117['D024_Nome_Empresa']=\hardness\gLimpaAcentos($mT117['D024_Nome_Empresa']);
	$mT117['D024_Nome_Empresa']=\hardness\gLimpaTexto($mT117['D024_Nome_Empresa']);
	//235 274 X(040) Nome do sacado
	$cString .= prepararCampo("Nome do sacado","Char",40,0,"{$mT117['D024_Nome_Empresa']}");

	$mT117['D024_Endereco']=\hardness\gLimpaAcentos($mT117['D024_Endereco']);
	$mT117['D024_Endereco']=\hardness\gLimpaTexto($mT117['D024_Endereco']);
	$mT117['D020_Nome_Cidade']=\hardness\gLimpaAcentos($mT117['D020_Nome_Cidade']);
	$mT117['D020_Nome_Cidade']=\hardness\gLimpaTexto($mT117['D020_Nome_Cidade']);
	$mT117['D024_Bairro']=\hardness\gLimpaAcentos($mT117['D024_Bairro']);
	$mT117['D024_Bairro']=\hardness\gLimpaTexto($mT117['D024_Bairro']);

	//275 314 X(040) Endereço do sacado
	$cString .= prepararCampo("Endereco","Char",40,0,"{$mT117['D024_Endereco']}, {$mT117['D024_Numero']} {$mT117['D020_Nome_Cidade']} {$mT117['D018_UF']}");    	

	//315 326 X(012) Bairro do sacado
	$cString .= prepararCampo("Bairro","Char",12,0,"{$mT117['D024_Bairro']}");

	//327 331 9(005) CEP do sacado
	$cString .= prepararCampo("Cep","Num",5,0,substr($mT117['D024_Cep'], 0, 5));

	//332 334 9(003) Complemento do CEP
	$cString .= prepararCampo("Cep Sufixo","Num",3,0,substr($mT117['D024_Cep'], 5, 8));

	//335 349 X015) Município do sacado 
	$cString .= prepararCampo("Cidade","Char",15,0,"{$mT117['D020_Nome_Cidade']}");

	//350 351 X(002) UF Estado do sacado
	$cString .= prepararCampo("UF","Char",2,0,"{$mT117['D018_UF']}");

	//352 381 X(030) Nome do sacador
	$cString .= prepararCampo("Sacador/Avalista","Char",30,0,"");

	//382 382 X(001) Brancos
	$cString .= prepararCampo("Brancos","Char", 1, 0, "");
	
	//383 383 X(001) Identificador do Complemento
	$cString .= prepararCampo("Identificador do Complemento","Char", 1, 0, $dadosContaSantander['identificador']);

	//384 385 9(002) Complemento
	$cString .= prepararCampo("Complemento","Num", 2, 0, $dadosContaSantander['complemento']);

	//386 391 X(006) Brancos
	$cString .= prepararCampo("Brancos","Char", 6, 0, "");

	//392 393 9(002) Número de dias para protesto.
	$diasProtesto = ($mD007['D007_Instrucao_Protesto_1'] == '06') ? $mD007['D007_Instrucao_Protesto_2'] : 0;
	$cString .= prepararCampo("Número de dias para protesto.","Num", 2, 0, $diasProtesto);

	//394 394 X(001) Branco
	$cString .= prepararCampo("Brancos","Char", 1, 0, "");

	//395 400 9(006) Número seqüencial
	$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

	$cString .= _SALTO_LINHA;
	
}
// -- RODAPE
$nSeq++;

//-- Tipo de registro Identificação do registro TRAILER 
//-- posicao 001   001 9(01)
$cString .= prepararCampo("Tipo de Registro","Num",1,0,"9");

//002 007 9(006) Quantidade total de linhas no arquivo
$cString .= prepararCampo("Quantidade total de linhas","Num",6,0,$nSeq);

//008 020 9(013)v99 Valor total dos títulos
$cString .= prepararCampo("Valor total dos títulos","Num",13,2,$valorTotalRemessa);

//-- BRANCOS - COMPLEMENTO DO REGISTRO
//-- posicao 002   394 X(393) 
$cString .= prepararCampo("Brancos","Num",374,0,"");

//-- NÚMERO SEQÜENCIAL DO REGISTRO NO ARQUIVO
//-- posicao 395   400  9(06) 
$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

$cString .= _SALTO_LINHA;

/*
fwrite($cArquivo_Remessa_Aberto, $cString ._SALTO_LINHA);

fclose($cArquivo_Remessa_Aberto);
$Ponteiro=fopen($cArquivo_Remessa, "r");
$cConteudo=fread($Ponteiro, filesize($cArquivo_Remessa));
*/

$cString = mysql_real_escape_string($cString);

$cClausula  = " update T035 set ";
$cClausula .= " T035_Conteudo_Arquivo='$cString',  ";
$cClausula .= " T035_Total_Remessa='$valorTotalRemessa'  ";  
$cClausula .= " where T035_Id='$nT035_Id' ";

mysql_query($cClausula);

mysql_query("COMMIT");
                         
return $nT035_Id;  

/*
//-- Abre a tela de download do arquivo
header('Content-type: application/x-msdownload');
header('Content-Disposition: attachment; filename='.basename($cArquivo_Remessa));
header('Pragma: no-cache');
header('Expires: 0');
echo $cConteudo;
*/

function prepararCampo($Campo,$Tipo="Char",$Tamanho,$Decimais=0,$String="")
{
    if (empty($Tipo))
    {
        echo "Campo:$Campo Não foi definido tipo<br>";        
    }

    $cRetorno="";

    $String=trim($String);

    if (strtoupper($Tipo)=="NUM") {
        
        if (empty($String)) {
            $String=0;
        }
        
        if (strlen($String)>($Tamanho-$Decimais)) {
            echo "Erro!! Campo:$Campo - Total de caracteres deste campo: $String (".strlen($String).") maior que o tamanho do campo(".($Tamanho-$Decimais).")";
            exit;
        }
        
        
        if ($Decimais>=$Tamanho) {
            echo "Erro!! Campo:$Campo - Decimal ($Decimais) maior que o tamanho do campo($Tamanho)";
            exit;
        }
        
        if ($Decimais>0) {
            $String=number_format($String,$Decimais,"","");
        } else {
            //-- Ecnontra um ponto no numero e desconsidera o que estiver depois do ponto 
            if ($posicao=strpos($String,".")!==FALSE) {
                $String=substr($String,0,$posicao+1);
            }
        }
        $cRetorno = STR_PAD($String,$Tamanho,"0",STR_PAD_LEFT);
    }
    
    if (strtoupper($Tipo)=="CHAR") {
        $String = substr($String,0,$Tamanho);
        $cRetorno = STR_PAD($String,$Tamanho," ",STR_PAD_RIGHT);
    }    
    
    if (strtoupper($Tipo)=="DATE") {
    	
    	if ($Tamanho==6) {
    		list($ano,$mes,$dia)=explode("-",$String);
    		$ano=substr($ano,-2);
    		$String = "$dia$mes$ano";
    	}
        if (!empty($String) and strlen($String)!=$Tamanho) {
            msg_alerta("Erro!! Campo:$Campo - deve ter $Tamanho caracteres ".strlen($String));
            exit;
        }        
        $cRetorno = STR_PAD($String,$Tamanho,"0",STR_PAD_LEFT);
    }        
    
    return $cRetorno;
}

function montarDadosContaSantander($mD007)
{
    $conta = preg_replace('/\D+/', '', (string)$mD007['D007_Numero_Conta']);
    $digito = preg_replace('/\D+/', '', (string)$mD007['D007_Digito_Verificador_Conta']);

    $dados = array(
        'conta_cobranca' => str_pad(substr($conta, -8), 8, '0', STR_PAD_LEFT),
        'identificador' => '',
        'complemento' => '',
    );

    // Quando a conta já está cadastrada com 9 posições, o layout usa as 8
    // primeiras no campo principal e o restante no complemento.
    if (strlen($conta) > 8 && $digito !== '') {
        $contaBaseNova = str_pad(substr($conta, -9), 9, '0', STR_PAD_LEFT);
        $dados['conta_cobranca'] = substr($contaBaseNova, 0, 8);
        $dados['identificador'] = 'I';
        $dados['complemento'] = substr($contaBaseNova, -1) . substr($digito, 0, 1);
        return $dados;
    }

    // Para contas cadastradas no sistema com 8 posições + DV, mantemos a
    // conta exatamente como cadastrada no campo principal e geramos o
    // complemento conforme a composição usada pelo Santander.
    if ($digito !== '') {
        $dados['identificador'] = 'I';
        $dados['complemento'] = calcularComplementoContaSantander($dados['conta_cobranca']) . substr($digito, 0, 1);
    }

    return $dados;
}

function montarCodigoTransmissaoSantander($mD007, $dadosContaSantander)
{
    $codigoTransmissao = preg_replace('/\D+/', '', (string)$mD007['D007_Codigo_Cedente']);

    if (strlen($codigoTransmissao) === 20) {
        return $codigoTransmissao;
    }

    $agencia = str_pad(preg_replace('/\D+/', '', (string)$mD007['D007_Numero_Agencia']), 4, '0', STR_PAD_LEFT);
    $contaMovimento = str_pad(substr($codigoTransmissao, -8), 8, '0', STR_PAD_LEFT);

    return $agencia . $contaMovimento . $dadosContaSantander['conta_cobranca'];
}

function calcularComplementoContaSantander($contaCobranca)
{
    $contaCobranca = str_pad(preg_replace('/\D+/', '', (string)$contaCobranca), 8, '0', STR_PAD_LEFT);
    $soma = 0;
    $fator = 2;

    for ($i = strlen($contaCobranca) - 1; $i >= 0; $i--) {
        $soma += ((int) substr($contaCobranca, $i, 1)) * $fator;
        $fator = ($fator == 7) ? 2 : $fator + 1;
    }

    $resto = $soma % 11;

    return ($resto > 9) ? 0 : $resto;
}

?>
