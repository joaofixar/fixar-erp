<?php
namespace hardness;
/*
Programador: Aramis
Data: 23/08/2024
*/

DEFINE("_SALTO_LINHA",CHR(13).CHR(10));


$cClausula  = " SELECT ";
$cClausula .= " * ";
$cClausula .= " FROM D007 ";
$cClausula .= " LEFT JOIN C004 ON C004_Id=D007_C004_Id ";
$cClausula .= " WHERE D007_Id='{$D007_Id}' ";

$D007  = mysql_query($cClausula);
$mD007 = mysql_fetch_array($D007);

if (strlen($mD007['D007_Numero_Agencia'])<=0)
{
   return "Não informado o numero da agência no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Agencia'])>4)
{
   return "Numero da agência deve ter no máximo 4 caracteres";
}
if (strlen($mD007['D007_Numero_Conta'])<=0)
{
   return "Não informado o numero da conta no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Conta'])>8)
{
   return "Numero da conta deve ter no máximo 5 caracteres";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])<=0)
{
   return "Não informado o digito verificador da conta no cadastro de contas";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])>2)
{
   return "Digito verificador da conta deve ter no máximo 2 caracteres";
}

$cClausula  = " SELECT ";
$cClausula .= " * ";
$cClausula .= " FROM T002 ";
$cClausula .= " LEFT JOIN T117 ON T117_Id=T002_T117_Id ";
$cClausula .= " LEFT JOIN D024 ON D024_Id=T002_D024_Id ";
$cClausula .= " LEFT JOIN D020 ON D020_Id=D024_D020_Id ";
$cClausula .= " LEFT JOIN D018 ON D018_Id=D020_D018_Id ";
$cClausula .= " LEFT JOIN C004 ON C004_Id=T002_C004_Id ";
$cClausula .= " LEFT JOIN D027 ON D027_Id=T002_D027_Id ";
$cClausula .= " WHERE $where ";
//$cClausula .= " and (T117_T035_Id=0 or T117_T035_Id is null)"; //utilizar apenas T002_T035_Id
$cClausula .= " GROUP BY T117_Id";
//$cClausula .= " and T117_Flag_Enviar_Remessa='S' ";
$T117_validacao=mysql_query($cClausula);

echo mysql_error();


if (mysql_num_rows($T117_validacao)==0)
{
   return "Nenhum titulo foi selecionado para esta conta";
}

$numRegistros=mysql_num_rows($T117_validacao);

//-- ELIMINA O ARQUIVO CASO O MESMO EXISTA
$cArquivo='/tmp/remessa_237.txt';
if (file_exists($cArquivo))
{
   unlink("$cArquivo");
}

$mErroCadastro=array();

while ($mT117 = mysql_fetch_array($T117_validacao))
{

	$mT117['D024_Cpf']  = trim($mT117['D024_Cpf']);
	$mT117['D024_Cpf']  = str_replace(array(".","-","/"," "),"",$mT117['D024_Cpf']);
	
	$mT117['D024_Cnpj'] = trim($mT117['D024_Cnpj']);
	$mT117['D024_Cnpj'] = str_replace(array(".","-","/"," "),"",$mT117['D024_Cnpj']);
	
    $mT117['D024_Cep']  = trim($mT117['D024_Cep']);
    $mT117['D024_Cep']  = str_replace(array(".","-"," "),"",$mT117['D024_Cep']);
    
    if (empty($mT117['D024_Cpf']) and $mT117['D024_Flag_Pessoa_Fisica_Juridica']=='F')   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem CPF. Remessa não gerada!"; 
    }
    
    if (empty($mT117['D024_Cnpj']) and $mT117['D024_Flag_Pessoa_Fisica_Juridica']=='J')   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem CNPJ. Remessa não gerada!"; 
    }
    
    if (empty($mT117['D024_Endereco']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Endereço. Remessa não gerada!"; 
    }

    if (empty($mT117['D024_Bairro']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Bairro. Remessa não gerada!"; 
    }

    if (empty($mT117['D024_Cep']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Cep. Remessa não gerada!"; 
    }
        
    if (empty($mT117['D020_Nome_Cidade']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Cidade. Remessa não gerada!"; 
    }

}

if (count($mErroCadastro)>0)
{
	return implode("\n",$mErroCadastro);
	exit;
}

$cSql_T117 = mysql_query($cClausula);

//-- INICIA UMA SESSAO NO MYSQL
mysql_query("set autocommit=0");
mysql_query("begin");

$cClausula="SELECT * FROM T035 WHERE T035_D007_Id = '{$mD007['D007_Id']}' AND T035_Data_Processamento >= '".date("Y-m-d")." 00:00:00' AND T035_Data_Processamento <='".date("Y-m-d")." 23:59:59'";
$cT035  = mysql_query($cClausula);
$numReg = mysql_num_rows($cT035)+1;

if ($numReg<10){
	$nomeArquivo=date("dmy").'0'.$numReg;
}else{
	$nomeArquivo=date("dmy").$numReg;
}

$cClausula  = " INSERT INTO T035 ( ";
$cClausula .= " T035_D007_Id,  ";
$cClausula .= " T035_C004_Id, ";
$cClausula .= " T035_D062_Id, ";
$cClausula .= " T035_Data_Processamento, ";
$cClausula .= " T035_Nome_Arquivo, ";
$cClausula .= " T035_Numero_Titulos ";
$cClausula .= " ) VALUES (  ";
$cClausula .= " '$D007_Id',  ";
$cClausula .= " '{$mD007['D007_C004_Id']}', ";
$cClausula .= " '{$mD007['D007_D062_Id']}', ";
$cClausula .= " now(), ";
$cClausula .= " '{$nomeArquivo}', ";
$cClausula .= " '{$numRegistros}' ";
$cClausula .= " )  ";
$cT035      = mysql_query($cClausula);
$cErro      = mysql_error();

if (!empty($cErro)){	
    mysql_query("ROLLBACK");
    return "Ocorreu um erro:$cErro/p/p$cClausula";
}

$nT035_Id = $g['mysqlLastId'];

//-- ELIMINA O ARQUIVO CASO O MESMO EXISTA
//-- ELIMINA O ARQUIVO CASO O MESMO EXISTA
/*$cArquivo_Remessa=$Diretorio_Usuario.'/arq_temp/remessa_341.txt';
if (file_exists($cArquivo_Remessa))
{
   unlink("$cArquivo_Remessa");
}
*/

//-- Abre o arquivo
//$cArquivo_Remessa_Aberto = fopen( $cArquivo_Remessa, 'x' );
  
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//-- INICIO DA EXPORTACAO DOS DADOS....
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$cHoje            = date("dmY",time());
$horaAtual        = date("His"); 
$nSeq             = 0;
$cString          = "";

//-----------------------------------------------------------------------------------//
//-- HEADER DO ARQUIVO --------------------------------------------------------------//
//-----------------------------------------------------------------------------------//

$RegistrosArquivo = 1;

//-- Numero do banco - Código do Banco na Compensação
//-- posicao 001   003 9(03)
$cString  .= prepararCampo("Banco","Num",3,0,"341");
 	
//-- Lote - Lote de Serviço
//-- posicao 004   007  9(04) 
$cString  .= prepararCampo("Lote","Num",4,0,"0000");

//-- Registro - tipo de Serviço
//-- posicao 008   008  9(01) 
$cString  .= prepararCampo("Tipo do serviço","Char",1,0,"0");

//-- Uso Exclusivo FEBRABAN/CNAB
//-- posicao 009   017 x(09)  
$cString  .= prepararCampo("Uso Exclusivo FEBRABAN/CNAB","Char",9,0,"");

//-- Inscrição - Tipo - Tipo de Inscrição da Empresa
//-- posicao 018   018  9(01) 
$cString  .= prepararCampo("Tipo de Inscrição da Empresa","Char",1,0,"2");

//-- Inscrição - Numero - Número de Inscrição da Empresa
//-- posicao 019   032 9(14) 
$mD007['D007_CNPJ'] = trim($mD007['D007_CNPJ']);
$mD007['D007_CNPJ'] = str_replace(array(".","-","/"," "),"",$mD007['D007_CNPJ']);
$cString  .= prepararCampo("Número de Inscrição da Empresa","Num",14,0,$mD007['D007_CNPJ']);

//-- posicao 033   052 X(20) 
$cString  .= prepararCampo("Complemento de Registro","Num",20,0,"");

//-- posicao 053   053 9(01)
$cString  .= prepararCampo("Complemento de Registro","Num",1,0,"0");

//-- Dados conta corrente - Agencia
// poiscao: 054   057 9(04)
$cString  .= prepararCampo("Agencia","Num",4,0,"{$mD007['D007_Numero_Agencia']}");


//-- posicao: 058   058 X(01)  
$cString  .= prepararCampo("COMPLEMENTO DE REGISTRO","Num",1,0,"");


//-- posicao 059  065 9(07)
$cString  .= prepararCampo("COMPLEMENTO DE REGISTRO","Num",7,0,"0");

//-- Dados conta corrente -  o Número da Conta Corrente
//-- posicao: 066   070 9(05)  
$cString  .= prepararCampo("NÚMERO DA CONTA CORRENTE DA EMPRESA","Num",5,0,"{$mD007['D007_Numero_Conta']}");

//-- posicao: 071   071 X(01)  
$cString  .= prepararCampo("COMPLEMENTO DE REGISTRO","Num",1,0,"");

//-- Dados conta corrente -  Digito Verificador da Ag/Conta
//-- posicao: 072   072 9(01)  
$cString  .= prepararCampo("Digito Verificador Ag/Conta","Num",1,0,"0");

//-- Empresa - Nome Empresa
//-- posicao: 073   102 9(30)  
$mD007['C004_Nome_Empresa'] =\hardness\gLimpaAcentos($mD007['C004_Nome_Empresa']);
$mD007['C004_Nome_Empresa'] =\hardness\gLimpaTexto($mD007['C004_Nome_Empresa']); 
$cString  .= prepararCampo("Nome da Empresa","Char",30,0,"{$mD007['C004_Nome_Empresa']}");

//-- Banco - Nome Do Banco
//-- posicao: 103   132 9(30)  
$cString  .= prepararCampo("Nome do Banco","Char",30,0,"BANCO ITAU SA");

//-- CNAB - Exclusivo Banco
//-- posicao: 133   142 9(10)  
$cString  .= prepararCampo("Brancos","Char",10,0,"");

//-- Arquivo - Código Remessa (1) / Retorno (2)
//-- posicao: 143   143 9(01)  
$cString  .= prepararCampo("Codigo Remessa","Num",1,0,"1");

//-- Arquivo - Data Geração
//-- posicao: 144   151 x(08)  
$cString  .= prepararCampo("Data Arquivo","Date",8,0,$cHoje);

//-- Arquivo - Hora Geração
//-- posicao: 152   157 9(06)  
$cString  .= prepararCampo("Hora Arquivo","Num",6,0,$horaAtual);

//-- Arquivo - Numero sequencial 
//-- posicao: 158   163 9(06)  
$mD007['D007_Numero_Sequencial_Remessa']++;
$cString  .= prepararCampo("Numero sequencial arquivo","Num",6,0,"{$mD007['D007_Numero_Sequencial_Remessa']}");

//-- Arquivo - Nº Versão do Layout do Arquivo
//-- posicao: 164   166 9(03)  
$cString  .= prepararCampo("Numero Versão do Layout do Arquivo","Num",3,0,"040");

//-- Arquivo - Densidade de Gravação do Arquivo
//-- posicao: 167   171 9(05)  
$cString  .= prepararCampo("Densidade","Num",5,0,"00000");

//-- Reservado Banco - Para Uso Reservado do Banco
//-- posicao: 172   225 x(54)  
$cString  .= prepararCampo("Brancos","Char",54,0,"");

//-- Reservado Empresa - Para Uso Reservado da Empresa
//-- posicao: 226   228 9(03)  
$cString  .= prepararCampo("Brancos","Char",3,0,"0");

// 20/03/2024 - Teve uma atualização no manual particularidades onde teve alteração do header do arquivo
// Essas últimas posíções é char brancos

//-- CNAB - Para Uso Exclusivo FEBRABAN CNAB
//-- posicao: 229   240 x(12)  
$cString  .= prepararCampo("Brancos","Char",12,0,"");


$cString .= _SALTO_LINHA;

//-----------------------------------------------------------------------------------//
//-- FIM HEADER DO ARQUIVO ----------------------------------------------------------//
//-----------------------------------------------------------------------------------//


//-----------------------------------------------------------------------------------//
//-- HEADER DO LOTE -- --------------------------------------------------------------//
//-----------------------------------------------------------------------------------//
$RegistrosLote    = 1;
$RegistrosArquivo++;

//-- posicao 001   003 9(03)
$cString  .= prepararCampo("Banco","Num",3,0,"341");

//-- Lote de Serviço
//-- Posição: 004   007  9(04) 
$cString .= prepararCampo("Lote do servico","Num",4,0,"0001");

//-- Tipo de Registro 
//-- Posição: 008   008  9(01) 
$cString .= prepararCampo("Tipo de Registro","Num",1,0,"1");

//-- Tipo de Operação 
//-- Posição: 009   009  9(01) 
$cString .= prepararCampo("Tipo de Operação","Char",1,0,"R");

//-- Tipo de Serviço 
//-- Posição: 010   011  9(02) 
$cString .= prepararCampo("Tipo de Serviço","Num",2,0,"01");

//-- Uso Exclusivo FEBRABAN/CNAB 
//-- Posição: 012   013  9(02) 
$cString .= prepararCampo("Tipo de Serviço","Char",2,0,"");

//-- Nº da Versão do Layout do Lote 
//-- Posição: 014   016  9(03) 
$cString .= prepararCampo("Nº da Versão do Layout do Lote","Num",3,0,"030");

//-- Uso Exclusivo FEBRABAN/CNAB 
//-- Posição: 017   017  9(1) 
$cString .= prepararCampo("Branco","Char",1,0,"");

//-- Tipo de Inscrição da Empresa 
//-- Posição: 018   018  9(1) 
$cString .= prepararCampo("Tipo de Inscrição da Empresa","Num",1,0,"2");

//-- Nº de Inscrição da Empresa 
//-- Posição: 019   033  9(15) 
$cString .= prepararCampo("Nº de Inscrição da Empresa","Num",15,0,"{$mD007['D007_CNPJ']}");

//-- Posição: 034   053  9(20) 
$cString .= prepararCampo("Código do Convênio no Banco","Char",20,0,"");

//-- Posição: 054   054  9(01) 
$cString .= prepararCampo("Código do Convênio no Banco","Num",1,0,"0");

//-- Dados conta corrente - Agencia
// poiscao: 055   058 9(04)
$cString  .= prepararCampo("Agencia","Num",4,0,"{$mD007['D007_Numero_Agencia']}");

//-- posicao: 059   059 X(01)  
$cString  .= prepararCampo("Digito Verificador Agencia","Char",1,0,"");

//-- posicao 060  066 9(07)
$cString  .= prepararCampo("Numero Conta Corrente","Num",7,0,"0");

//-- Dados conta corrente -  o Número da Conta Corrente
//-- posicao 067  071 9(05)
$cString  .= prepararCampo("Numero Conta Corrente","Num",5,0,"{$mD007['D007_Numero_Conta']}");

//-- Dados conta corrente -  Dígito Verificador da conta
//-- posicao: 072   072 X(01)  
$cString  .= prepararCampo("Digito Verificador Conta Corrente","Char",1,0,"");

//-- Dados conta corrente -  Digito Verificador da Ag/Conta
//-- posicao: 073   073 9(01)  
$cString  .= prepararCampo("Digito Verificador Ag/Conta","Num",1,0,"0");

//-- Nome da Empresa 
//-- posicao: 074   103 9(30)  
$cString  .= prepararCampo("Nome da Empresa","Char",30,0,"{$mD007['C004_Nome_Empresa']}");

//-- Mensagem 1 
//-- posicao: 104   183 9(80)  
$cString  .= prepararCampo("Mensagem 1","Char",80,0,"");

//-- Número Remessa/Retorno 
//-- posicao: 184   191 9(8)  
$cString  .= prepararCampo("Número Remessa","Num",8,0,"1");

//-- Arquivo - Data Geração
//-- posicao: 192   199x(08)  
$cString  .= prepararCampo("Data Arquivo","Date",8,0,$cHoje);

//-- Arquivo - Data Credito
//-- posicao: 200   207 x(08)  
$cString  .= prepararCampo("Data Credito","Num",8,0,"");

//-- Uso Exclusivo FEBRABAN/CNAB
//-- posicao: 208   240 x(33)  
$cString  .= prepararCampo("Brancos","Char",33,0,"");

$cString .= _SALTO_LINHA;

//-----------------------------------------------------------------------------------//
//-- FIM HEADER DO LOTE --------------------------------------------------------------//
//-----------------------------------------------------------------------------------//


$valorTotalRemessa=0;

while ($mT117 = mysql_fetch_array($cSql_T117))
{

	$valorTotalRemessa+=$mT117['T117_Valor_Total'];

	//utilizar apenas T002_T035_Id
    // $cClausula  = " update T117 set ";
    // $cClausula .= " T117_T035_Id='$nT035_Id'  ";
    // $cClausula .= " where T117_Id='{$mT117['T117_Id']}' ";
    // mysql_query($cClausula);

    $cClausula  = " update T002 set ";
    $cClausula .= " T002_Flag_Enviar_Banco='N',"; 
    $cClausula .= " T002_T035_Id='$nT035_Id'  ";
    $cClausula .= " where T002_T117_Id='{$mT117['T117_Id']}' ";
    mysqli_query($cClausula);
    
    //-- ELIMINA CARACATERES INVALIDOS PARA CPF OU CNPJ
    if ($mT117['D024_Flag_Pessoa_Fisica_Juridica']=='F')
    {
       $cCpfCnpjTipo='1';
       $mT117['D024_Cpf'] = trim($mT117['D024_Cpf']);
       $cCpfCnpj  = str_replace(array(".","-","/"," "),"",$mT117['D024_Cpf']);
    }
    elseif ($mT117['D024_Flag_Pessoa_Fisica_Juridica']=='J')
    {
       $cCpfCnpjTipo='2';
       $mT117['D024_Cnpj'] = trim($mT117['D024_Cnpj']);
       $cCpfCnpj  = str_replace(array(".","-","/"," "),"",$mT117['D024_Cnpj']);
    } 

	$mT117['D024_Cpf']  = trim($mT117['D024_Cpf']);
	$mT117['D024_Cpf']  = str_replace(array(".","-","/"," "),"",$mT117['D024_Cpf']);
	
	$mT117['D024_Cnpj'] = trim($mT117['D024_Cnpj']);
	$mT117['D024_Cnpj'] = str_replace(array(".","-","/"," "),"",$mT117['D024_Cnpj']);
	
    $mT117['D024_Cep']  = trim($mT117['D024_Cep']);
    $mT117['D024_Cep']  = str_replace(array(".","-"," "),"",$mT117['D024_Cep']);
      
    if ($mT117['D024_Flag_Protestar'] != 'N') {
		$mT117['Instrucao_Protesto_1']=$mD007['D007_Instrucao_Protesto_1'];
    }
	// NO CASO DO LAYOUT CNAB400 É ENVIADO O CÓDIGO 07 PARA NÃO PROTESTAR
	$mT117['Instrucao_Protesto_2']=$mD007['D007_Instrucao_Protesto_2'];

    $C004_Cnpj = str_replace(array(".","-","/"," "),"",$mD007['C004_Cnpj']);

    //-----------------------------------------------------------------------------------//
    //-- INICIO SEGMENTO P --------------------------------------------------------------//
    //-----------------------------------------------------------------------------------//

    //-- NUMERO SEQUENCIAL PARA CADA SEGMENTO DO ARQUIVO (CONFORME LEIAUTE)
    $nSeq++;
    $RegistrosLote++;
    $RegistrosArquivo++;

	//-- Código do Banco na Compensação
	//-- Posicao 001   003 9(03)
    $cString .= prepararCampo("Codigo do Banco","Num",3,0,"341");

    //-- Lote de Serviço
	//-- Posição: 004   007  9(04) 
    $cString .= prepararCampo("Lote do servico","Num",4,0,"0001");

    //-- Tipo de Registro
	//-- Posição: 008   008  9(01) 
    $cString .= prepararCampo("Tipo de Registro","Num",1,0,"3");

    //-- Nº Sequencial do Registro no Lote
	//-- Posição: 009   013  9(05) 
    $cString .= prepararCampo("Nº Sequencial do Registro no Lote","Num",5,0,$nSeq);

    //-- Cód. Segmento do Registro Detalhe
	//-- Posição: 014   014  x(01) 
    $cString .= prepararCampo("Cód. Segmento do Registro Detalhe","Char",1,0,"P");

    //-- Cód. Segmento do Registro Detalhe
	//-- Posição: 015   015  x(01) 
    $cString .= prepararCampo("Uso Exclusivo FEBRABAN/CNAB","Char",1,0,"");

    //-- Código de Movimento Remessa / '01' = Entrada de Títulos
	//-- Posição: 016   017  x(02) 
    $cString .= prepararCampo("Código de Movimento Remessa","Num",2,0,"01");

    //-- Posição: 018   018  9(01) 
    $cString .= prepararCampo("COMPLEMENTO DE REGISTRO","Num",2,0,"0");

    //-- Agência Mantenedora da Conta
	//-- Posição: 019   022  9(04) 
    $cString .= prepararCampo("Agência Mantenedora da Conta","Num",4,0,"{$mD007['D007_Numero_Agencia']}");


	//-- Posição: 023   023  X(01) 
    $cString .= prepararCampo("COMPLEMENTO DE REGISTRO","Char",1,0,"");

    //-- Posição: 024   030  9(07) 
    $cString .= prepararCampo("COMPLEMENTO DE REGISTRO","Num",7,0,"0");

    //-- Número da Conta Corrente
	//-- Posição: 031   035  9(12) 
    $cString .= prepararCampo("Número da Conta Corrente","Num",5,0,"{$mD007['D007_Numero_Conta']}");

   	//-- Posição: 036   036  X(01) 
    $cString .= prepararCampo("Dígito Verificador da Conta","Char",1,0,"");

 	//-- Posição: 037   037  9(01) 
    $cString .= prepararCampo("Dígito Verificador da Ag/Conta","Num",1,0,"");

    //-- Posição: 038   040  9(03) 
    $cString .= prepararCampo("NÚMERO DA CARTEIRA NO BANCO","Num",3,0,"{$mT117['D007_Tipo_Carteira_Banco_Brasil']}");

    //-- dentificação do Título no Banco
	//-- Posição: 041   048  9(08) 
    $cString .= prepararCampo("Nosso número","Num",8,0,"{$mT117['T117_Nosso_Numero']}");

    //-- Posição: 049   049  9(01) 
    $cString .= prepararCampo("Dígito Verificador da Ag/Conta","Num",1,0,"0");

    //-- Posição: 050   057  X(08) 
    $cString .= prepararCampo("COMPLEMENTO DE REGISTRO","Char",8,0,"");

    //-- Posição: 058   062  9(05) 
    $cString .= prepararCampo("COMPLEMENTO DE REGISTRO","Num",5,0,"0");

    //-- Número do Documento de Cobrança
	//-- Posição: 063   072  x(10) 
    $cString .= prepararCampo("Número do Documento de Cobrança","Char",10,0,"{$mT117['T002_Numero_Duplicata']}");

    //-- Posição: 073   077  X(05) 
    $cString .= prepararCampo("COMPLEMENTO DE REGISTRO","Char",5,0,"");

    //-- Data de Vencimento do Título 
	//-- Posição: 078   085  x(8) 
    // verificar se na remessa vai sair certo
    $cString .= prepararCampo("Data de Vencimento","Date",8,0,"{$mT117['T117_Data_Vencimento']}");

    //-- Valor Nominal do Título
	//-- Posição: 086   100  9(13) 
    $cString .= prepararCampo("Valor Nominal do Título","Num",15,2,"{$mT117['T117_Valor_Total']}");

    //-- Agência Encarregada da Cobrança
	//-- Posição: 101   105  9(5) 
    $cString .= prepararCampo("Agência Encarregada da Cobrança","Num",5,0,"00000");

    //-- Dígito Verificador da Agência
	//-- Posição: 106   106  x(1) 
    $cString .= prepararCampo("Dígito Verificador da Agência","Char",1,0,"");

    //-- Espécie do Título / 01 = DM Duplicata Mercantil
	//-- Posição: 107   108  9(2) 
    $cString .= prepararCampo("Espécie do Título","Num",2,0,"01");

    //-- Identific. de Título Aceito/Não Aceito
	//-- Posição: 109   109  x(1) 
    $cString .= prepararCampo("Identific. de Título Aceito/Não Aceito","Char",1,0,"{$mD007['D007_Aceite']}");

    //-- Data da Emissão do Título
	//-- Posição: 110   117  x(8) 
    $cString .= prepararCampo("Data da Emissão do Título","Date",8,0,"{$mT117['T117_Data_Emissao']}");

    //-- Código do Juros Mora
	//-- Posição: 118   118  9(1) / '1' = Valor por Dia '2' = Taxa Mensal '3' = Isento
    $cString .= prepararCampo("Código do Juros Mora","Num",1,0,"0");

    //-- Data do Juros de Mora
	//-- Posição: 119   126  x(8) 
    $cString .= prepararCampo("Data do Juros de Mora","Num",8,0,"");

    //-- Juros de Mora por Dia/Taxa 
	//-- Posição: 127   141  9(13) 
    $jurosDia = round((($mT117['T117_Percentual_Juros_Dia']/30)/100)*$mT117['T002_Valor_Total'],2);
    $cString .= prepararCampo("Juros de Mora por Dia/Taxa","Num",15,2,$jurosDia);

    //-- Data do Desconto 1 
	//-- Posição: 142   142  9(1) 
    $cString .= prepararCampo("Código do Desconto 1","Num",1,0,"0");

    //-- Data do Desconto 1 
	//-- Posição: 143   150  9(8) 
    $cString .= prepararCampo("Data do Desconto 1","Num",8,0,"");

    //-- Valor/Percentual a ser Concedido 
	//-- Posição: 151   165  9(13) 
    $cString .= prepararCampo("Valor/Percentual a ser Concedido","Num",15,2,"");

    //-- Valor do IOF a ser Recolhido 
	//-- Posição: 166   180  9(13) 
    $cString .= prepararCampo("Valor do IOF a ser Recolhido","Num",15,2,"");

    //-- Valor do Abatimento 
	//-- Posição: 181   195  9(13) 
    $cString .= prepararCampo("Valor do Abatimento","Num",15,2,"");

    //-- Identificação do Título na Empresa 
	//-- Posição: 196   220  x(25) 
    $cString .= prepararCampo("Identificação do Título na Empresa","Char",25,0,"");

    //-- Código para Protesto 
	//-- Posição: 221   221  9(1)
    $instrucaoProtesto = (empty($mT117['Instrucao_Protesto_1'])) ? '3' : $mT117['Instrucao_Protesto_1'];
    error_log("qual a instrucaoProtesto ".$instrucaoProtesto);
    $cString .= prepararCampo("Código para Protesto","Num",1,0,"{$instrucaoProtesto}");

    //-- Número de Dias para Protesto 
	//-- Posição: 222   223  9(2) 
    $cString .= prepararCampo("Número de Dias para Protesto","Num",2,0,"{$mT117['Instrucao_Protesto_2']}");

    //-- Código para Baixa/Devolução 
	//-- Posição: 224   224  9(1) 
    $cString .= prepararCampo("Código para Baixa/Devolução","Num",1,0,"");

    //-- Número de Dias para Baixa/Devolução 
	//-- Posição: 225   226  9(2) 
    $cString .= prepararCampo("Número de Dias para Baixa/Devolução","Num",2,0,"");

    //-- Código da Moeda 
	//-- Posição: 227   239  9(13) 
    $cString .= prepararCampo("COMPLEMENTO DE REGISTRO","Num",13,0,"0");

    //-- Uso livre banco/empresa ou autorização de pagamento parcial / 1 – Não autoriza pagamento parcial  2 – Autoriza pagamentos parciais
	//-- Posição: 240   240  x(1) 
    $cString .= prepararCampo("autorização de pagamento parcial","Char",1,0,"");

    $cString .= _SALTO_LINHA;

    //-----------------------------------------------------------------------------------//
    //-- FIM SEGMENTO P -----------------------------------------------------------------//
    //-----------------------------------------------------------------------------------//


    //-----------------------------------------------------------------------------------//
    //-- INICIO SEGMENTO Q --------------------------------------------------------------//
    //-----------------------------------------------------------------------------------//

    //-- NUMERO SEQUENCIAL PARA CADA SEGMENTO DO ARQUIVO (CONFORME LEIAUTE)
    $nSeq++;
    $RegistrosLote++;
    $RegistrosArquivo++;

    //-- Código do Banco na Compensação
	//-- Posição: 001   003  x(1) 
    $cString .= prepararCampo("Código do Banco na Compensação","Num",3,0,"341");

    //-- Lote - Lote de Serviço
    //-- posicao 004   007  9(04) 
    $cString  .= prepararCampo("Lote","Num",4,0,"0001");

    //-- Tipo de Registro
    //-- posicao 008   008  9(01) 
    $cString  .= prepararCampo("Lote","Num",1,0,"3");

    //-- Nº Sequencial do Registro no Lote
    //-- posicao 009   013  9(05) 
    $cString .= prepararCampo("Nº Sequencial do Registro no Lote","Num",5,0,$nSeq);

    //-- Cód. Segmento do Registro Detalhe
	//-- Posição: 014   014  x(01) 
    $cString .= prepararCampo("Cód. Segmento do Registro Detalhe","Char",1,0,"Q");

    //-- Uso Exclusivo FEBRABAN/CNAB
	//-- Posição: 015   015  x(01) 
    $cString .= prepararCampo("Brancos","Char",1,0,"");

    //-- Código de Movimento Remessa / '01' = Entrada de Títulos
	//-- Posição: 016   017  x(02) 
    $cString .= prepararCampo("Código de Movimento Remessa","Num",2,0,"01");

    //-- Tipo de Inscrição do Sacado
	//-- Posição: 018   018  9(01) 
    $cString .= prepararCampo("Tipo de Inscrição do Sacado","Num",1,0,"{$cCpfCnpjTipo}");

    //-- Número de Inscrição
	//-- Posição: 019   033  9(15) 
    $cString .= prepararCampo("Número de Inscrição","Num",15,0,"{$cCpfCnpj}");

    //-- Nome do Sacado
	//-- Posição: 034   073  x(40) 
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaAcentos($mT117['D024_Nome_Empresa']);
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaTexto($mT117['D024_Nome_Empresa']);
    $cString .= prepararCampo("Nome do Sacado","Char",40,0,"{$mT117['D024_Nome_Empresa']}");


    //-- Endereco do Sacado
	//-- Posição: 074   113  x(40) 
    $mT117['D024_Endereco']=\hardness\gLimpaAcentos($mT117['D024_Endereco']);
    $mT117['D024_Endereco']=\hardness\gLimpaTexto($mT117['D024_Endereco']);   
    $cString .= prepararCampo("Endereco do Sacado","Char",40,0,"{$mT117['D024_Endereco']}");


	//-- Bairro - Bairro do Sacado
	//-- Posicoes: 114   128 X(15)
	$mT117['D024_Bairro']=\hardness\gLimpaAcentos($mT117['D024_Bairro']);
	$mT117['D024_Bairro']=\hardness\gLimpaTexto($mT117['D024_Bairro']);
	$cString .= prepararCampo("Bairro","Char",15,0,"{$mT117['D024_Bairro']}");

	//-- CEP - CEP do sacado 
	//-- Posicoes: 129   133 9(05)
    $CEP = substr($mT117['D024_Cep'], 0, 5); 
	$cString .= prepararCampo("Cep","Num",5,0,"{$CEP}");

    //-- Sufixo CEP - CEP do sacado 
	//-- Posicoes: 134   136 9(03)
    $SufixoCEP = substr($numero, 5); 
	$cString .= prepararCampo("Cep","Num",3,0,"{$SufixoCEP}");

	//-- Cidade do sacado 
	//-- Posicoes: 137   151 x(15)
	$mT117['D020_Nome_Cidade']=\hardness\gLimpaAcentos($mT117['D020_Nome_Cidade']);
	$mT117['D020_Nome_Cidade']=\hardness\gLimpaTexto($mT117['D020_Nome_Cidade']);
	$cString .= prepararCampo("Cidade","Char",15,0,"{$mT117['D020_Nome_Cidade']}");

	//-- UF - Estado do sacado 
	//-- Posicoes: 152   153 x(02)
	$cString .= prepararCampo("UF","Char",2,0,"{$mT117['D018_UF']}");

    //-- Tipo de Inscrição Avalista
	//-- Posicoes: 154   154 x(01)
	$cString .= prepararCampo("Tipo de Inscrição Avalista","Num",1,0,"");
    
    //-- Número de Inscrição Avalista
	//-- Posicoes: 155   169 x(15)
	$cString .= prepararCampo("Número de Inscrição Avalista","Num",15,0,"");

    //-- Nome do Sacador/Avalista Avalista
	//-- Posicoes: 170   199 x(30)
	$cString .= prepararCampo("Nome do Sacador/Avalista Avalista","Char",30,0,"");

    //--  Uso Exclusivo FEBRABAN/CNAB
	//-- Posicoes: 200   209 x(10)
	$cString .= prepararCampo("Brancos","Char",10,0,"");

    //-- Cód. Bco. Corresp. na Compensação
	//-- Posicoes: 210   212 x(3)
	$cString .= prepararCampo("Brancos","Num",3,0,"");

    //--  Nosso Nº no Banco Correspondente
	//-- Posicoes: 213   240 x(28)
	$cString .= prepararCampo("Brancos","Char",28,0,"");

    $cString .= _SALTO_LINHA;


    //-----------------------------------------------------------------------------------//
    //-- FIM SEGMENTO Q------- ----------------------------------------------------------//
    //-----------------------------------------------------------------------------------//


    //-----------------------------------------------------------------------------------//
    //-- INICIO SEGMENTO R --------------------------------------------------------------//
    //-----------------------------------------------------------------------------------//

    //-- NUMERO SEQUENCIAL PARA CADA SEGMENTO DO ARQUIVO (CONFORME LEIAUTE)
    $nSeq++;
    $RegistrosLote++;
    $RegistrosArquivo++;

    //-- Código do Banco na Compensação
	//-- Posição: 001   003  x(1) 
    $cString .= prepararCampo("Código do Banco na Compensação","Num",3,0,"341");

    //-- Lote - Lote de Serviço
    //-- posicao 004   007  9(04) 
    $cString  .= prepararCampo("Lote","Num",4,0,"0001");

    //-- Tipo de Registro
    //-- posicao 008   008  9(01) 
    $cString  .= prepararCampo("Lote","Num",1,0,"3");

    //-- Nº Sequencial do Registro no Lote
    //-- posicao 009   013  9(05) 
    $cString .= prepararCampo("Nº Sequencial do Registro no Lote","Num",5,0,$nSeq);

    //-- Cód. Segmento do Registro Detalhe
	//-- Posição: 014   014  x(01) 
    $cString .= prepararCampo("Cód. Segmento do Registro Detalhe","Char",1,0,"R");

    //-- Uso Exclusivo FEBRABAN/CNAB
	//-- Posição: 015   015  x(01) 
    $cString .= prepararCampo("Brancos","Char",1,0,"");

    //-- Código de Movimento Remessa / '01' = Entrada de Títulos
	//-- Posição: 016   017  x(02) 
    $cString .= prepararCampo("Código de Movimento Remessa","Num",2,0,"01");

    //-- Data do Desconto 2 
	//-- Posição: 018   018  9(1) 
    $cString .= prepararCampo("Código do Desconto 2","Num",1,0,"0");

    //-- Data do Desconto 2 
	//-- Posição: 019   026  9(8) 
    $cString .= prepararCampo("Data do Desconto 2","Num",8,0,"");

    //-- Valor/Percentual a ser Concedido 
	//-- Posição: 027   041  9(13) 
    $cString .= prepararCampo("Valor/Percentual a ser Concedido","Num",15,2,"");

    //-- Data do Desconto 3 
	//-- Posição: 042   042  9(1) 
    $cString .= prepararCampo("Código do Desconto 3","Num",1,0,"0");

    //-- Data do Desconto 3 
	//-- Posição: 043   050  9(8) 
    $cString .= prepararCampo("Data do Desconto 3","Num",8,0,"");

    //-- Valor/Percentual a ser Concedido 
	//-- Posição: 051   065  9(13) 
    $cString .= prepararCampo("Valor/Percentual a ser Concedido","Num",15,2,"");

    //-- Código da Multa 
	//-- Posição: 066   066  9(1) 
    $codigoMulta = ($mD007['D007_Flag_Tipo_Multa'] == 'V') ? 1 : 2;
    $cString .= prepararCampo("Código da Multa","Num",1,0,"{$codigoMulta}");

    //-- Data da Multa 
	//-- Posição: 067   074  9(8) 
    $dataMulta = date("Y-m-d", strtotime("+1 day", strtotime($mT117['T117_Data_Vencimento'])));
    $cString .= prepararCampo("Data da Multa","Date",8,0,"{$dataMulta}");
    
    //-- Valor/Percentual a Ser Aplicado 
	//-- Posição: 075   089  9(13) 
    if($codigoMulta == 1){
        $valorMultaPercentual = round(($mT117['T117_Percentual_Multa']/100)*$mT117['T117_Valor_Total'],2);
    } else {
        $valorMultaPercentual = $mT117['T117_Percentual_Multa'];;
    }
    $cString .= prepararCampo("Valor/Percentual Multa","Num",13,2,$valorMultaPercentual);

    //-- Informação ao Pagador
	//-- Posição: 090   099  x(10) 
    $cString .= prepararCampo("Informação ao Pagador","Char",10,0,"");

    //-- Mensagem 3 
	//-- Posição: 100   139  x(40) 
    $cString .= prepararCampo("Mensagem 3 ","Char",40,0,"");

    //-- Uso Exclusivo FEBRABAN/CNAB
	//-- Posição: 140   199  x(60) 
    $cString .= prepararCampo("Bracos","Char",60,0,"");

    //-- Cód. Ocor. do Pagador
	//-- Posição: 200   207  x(8) 
    $cString .= prepararCampo("Cód. Ocor. do Pagador","Num",8,0,"");

    //-- Cód. do Banco na Conta do Débito
	//-- Posição: 208   215  9(8) 
    $cString .= prepararCampo("Cód. do Banco na Conta do Débito","Num",8,0,"0");

    //-- Dígito Verificador da Agência 
	//-- Posição: 216   216  x(1) 
    $cString .= prepararCampo("Dígito Verificador da Agência","Char",1,0,"");

    //-- Conta Corrente para Débito 
	//-- Posição: 217   228  9(12) 
    $cString .= prepararCampo("Conta Corrente para Débito","Num",12,0,"");

    //-- Dígito Verificador da Conta 
	//-- Posição: 229   230  x(2) 
    $cString .= prepararCampo("Dígito Verificador da Conta","Char",2,0,"");

    //-- Posição: 231   231  x(1) 
    $cString .= prepararCampo("Aviso para Débito Automático","Num",1,0,"0");

    //-- Uso Exclusivo FEBRABAN/CNAB 
    //-- Manual esta falando 9 mas total da linha da 238 , acrescentei mais 2 para fechar as linhas
	//-- Posição: 232   240  x(9) 
    $cString .= prepararCampo("Branco","Char",11,0,"");

    $cString .= _SALTO_LINHA;

    //-----------------------------------------------------------------------------------//
    //-- FIM SEGMENTO R -----------------------------------------------------------------//
    //-----------------------------------------------------------------------------------//


}

//-----------------------------------------------------------------------------------//
//-- TRAILER DO LOTE ----------------------------------------------------------------//
//-----------------------------------------------------------------------------------//

$RegistrosLote++;
$RegistrosArquivo++;
//-- Código do Banco na Compensação
//-- Posição: 001   003  x(1) 
$cString .= prepararCampo("Código do Banco na Compensação","Num",3,0,"341");

//-- Lote - Lote de Serviço
//-- posicao 004   007  9(04) 
$cString  .= prepararCampo("Lote","Num",4,0,"0001");

//-- Tipo de Registro
//-- posicao 008   008  9(01) 
$cString  .= prepararCampo("Lote","Num",1,0,"5");

//-- Uso Exclusivo FENRABAN/CNAB
//-- Posição: 009   017 x(09) 
$cString .= prepararCampo("Brancos","Char",9,0,"");

//-- Quantidade de Registros do Lote
//-- Posição: 018   023  x(6) 
$cString .= prepararCampo("Quantidade de Registros do Lote","Num",6,0,"{$RegistrosLote}");

//-- Somatorio dos valores 
//Somatório dos valores de pagamento de boletos (pos. 153 a 167) dos  registros de detalhe (Registro = '3' / Código de Segmento = 'J').
//Se informado incorretamente, todo o lote será recusado. Reg. Tipos 3
//-- Posição: 024   041  x(18) 
$cString .= prepararCampo("Somatorio dos valores","Num",18,2,"");

//-- Somatória de Quantidade de Moedas 
//-- Posição: 042   059  x(18) 
$cString .= prepararCampo("Somatória de Quantidade de Moedas","Num",18,5,"");

//-- Número Aviso Débito
//-- Posição: 060   065  x(6) 
$cString .= prepararCampo("Número Aviso Débito","Num",6,0,"");

//-- Uso Exclusivo FEBRABAN/CNAB
//-- Posição: 066   230  x(165) 
$cString .= prepararCampo("Brancos","Char",165,0,"");

//-- Códigos das Ocorrências para Retorno
//-- Posição: 231   240  x(10) 
$cString .= prepararCampo("Brancos","Char",10,0,"");

$cString .= _SALTO_LINHA;

//-----------------------------------------------------------------------------------//
//-- FIM TRAILER DO LOTE ------------------------------------------------------------//
//-----------------------------------------------------------------------------------//


//-----------------------------------------------------------------------------------//
//-- TRAILER DO ARQUIVO  ------------------------------------------------------------//
//-----------------------------------------------------------------------------------//

$RegistrosArquivo++;
error_log("qual o o RegistrosArquivo ".$RegistrosArquivo);
//-- Código do Banco na Compensação
//-- Posição: 001   003  x(1) 
$cString .= prepararCampo("Código do Banco na Compensação","Num",3,0,"341");

//-- Lote - Lote de Serviço
//-- posicao 004   007  9(04) 
$cString  .= prepararCampo("Lote","Num",4,0,"9999");

//-- Tipo de Registro
//-- posicao 008   008  9(01) 
$cString  .= prepararCampo("Lote","Num",1,0,"9");

//-- Uso Exclusivo FENRABAN/CNAB
//-- Posição: 009   017 x(09) 
$cString .= prepararCampo("Brancos","Char",9,0,"");

//-- Quantidade de Lotes do Arquivo 
//-- posicao 018   023 9(06)
$cString .= prepararCampo("Quantidade de Lotes do Arquivo ","Num",6,0,"1");

//-- Quantidade de Registros do Arquivo
//-- posicao 024   029 X(6) 
$cString .= prepararCampo("Quantidade de Registros do Arquivo","Num",6,0,"{$RegistrosArquivo}");

//--  Qtde. de Contas p/ Conc. (Lotes)
//-- posicao 030   035 X(6) 
$cString .= prepararCampo("Qtde. de Contas p/ Conc. (Lotes)","Num",6,0,"");

//--  Uso Exclusivo FEBRABAN / CNAB
//-- posicao 036   240 X(205) 
$cString .= prepararCampo("Brancos","Char",205,0,"");

$cString .= _SALTO_LINHA;

//-----------------------------------------------------------------------------------//
//-- FIM TRAILER DO ARQUIVO ---------------------------------------------------------//
//-----------------------------------------------------------------------------------//

$cString = mysql_real_escape_string($cString);

$cClausula  = " update T035 set ";
$cClausula .= " T035_Conteudo_Arquivo='$cString',  ";
$cClausula .= " T035_Total_Remessa='$valorTotalRemessa'  ";  
$cClausula .= " where T035_Id='$nT035_Id' ";

mysql_query($cClausula);

mysql_query("UPDATE D007 SET D007_Numero_Sequencial_Remessa = '{$mD007['D007_Numero_Sequencial_Remessa']}' WHERE D007_Id = '{$mD007['D007_Id']}'");


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

    	if ($Tamanho==8) {
    		list($ano,$mes,$dia)=explode("-",$String);
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

?>