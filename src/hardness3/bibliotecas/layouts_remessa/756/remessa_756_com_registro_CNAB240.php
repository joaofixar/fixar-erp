<?php
namespace hardness;
/*
Programador: Lígia Nakazato
Data: 12/07/2019

O arquivo CNAB240 é composto por 3 partes:

Registro header do arquivo;
Lotes do arquivo;
Registro trailer do arquivo.

Onde os lotes são composto por:
- Registro header do lote;
- Segmento P (obrigatório remessa);
- Segmento Q (obrigatório remessa);
- Outros segmentos (opcionais);
- Registro trailer de lote.
Cada lote corresponde a um tipo de operação, nesse caso iremos ter apenas a remessa
*/

// $D007_Id='8';
// $where="T002_Flag_Enviar_Banco='S' AND T002_Flag_Cancelada!='S' AND IFNULL(T002_T035_Id,0)<=0 AND IFNULL(T002_T002_Id_Agrupado,0)<=0 AND T002_Data_Recebimento='0000-00-00' AND IFNULL(T002_T117_Id,0)>0 AND IFNULL(T117_Id,0)>0 AND T117_Flag_Cancelado!='S' AND T002_C004_Id='1' ";

DEFINE("_SALTO_LINHA",CHR(13).CHR(10));

$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from D007 ";
$cClausula .= " left join C004 on C004_Id=D007_C004_Id ";
$cClausula .= " where D007_Id='$D007_Id' ";
$D007 = mysql_query($cClausula);
$mD007=mysql_fetch_array($D007);
$mD007['D007_CNPJ'] = trim($mD007['D007_CNPJ']);
$mD007['D007_CNPJ'] = str_replace(array(".","-","/"," "),"",$mD007['D007_CNPJ']);
$mD007['D007_Numero_Convenio'] = str_pad(trim($mD007['D007_Numero_Convenio']),20);
$mD007['C004_Cnpj'] = str_replace(array(".","-","/"," "),"",$mD007['C004_Cnpj']);

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
if (strlen($mD007['D007_Numero_Conta'])>5)
{
   //return "Numero da conta deve ter no máximo 5 caracteres";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])<=0)
{
   return "Não informado o digito verificador da conta no cadastro de contas";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])>2)
{
   return "Digito verificador da conta deve ter no máximo 2 caracteres";
}

$mD007['D007_Numero_Agencia'] = str_pad(trim($mD007['D007_Numero_Agencia']),5,'0', STR_PAD_LEFT);
$mD007['D007_Numero_Conta'] = str_pad(trim($mD007['D007_Numero_Conta']),12,'0', STR_PAD_LEFT);
$mD007['D007_Numero_Sequencial_Remessa']++;

$cClausula  = " select ";
$cClausula .= " *, ";
$cClausula .= " if(T117_Id is null,T002_Id,T117_Id) T117_Id, ";
$cClausula .= " if(T117_Data_Vencimento is null,T002_Data_Vencimento,T117_Data_Vencimento) T117_Data_Vencimento, ";
$cClausula .= " if(T117_Data_Emissao is null,T002_Data_Emissao,T117_Data_Emissao) T117_Data_Emissao, ";
$cClausula .= " if(T117_Valor_Total is null,T002_Valor_Total,T117_Valor_Total) T117_Valor_Total ";
$cClausula .= " from T002 ";
$cClausula .= " left join T117 on T117_Id=T002_T117_Id ";
$cClausula .= " left join D024 on D024_Id=T002_D024_Id ";
$cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
$cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
$cClausula .= " left join C004 on C004_Id=T002_C004_Id ";
$cClausula .= " left join D027 on D027_Id=T002_D027_Id ";
$cClausula .= " where $where ";
$cClausula .= " GROUP BY ifnull(T117_Id,T002_Id)";

//$cClausula .= " and T117_Flag_Enviar_Remessa='S' ";
// echo "Ligia clausula: ".$cClausula;
$T117_validacao=mysql_query($cClausula);
echo mysql_error();

if (mysql_num_rows($T117_validacao)==0)
{
   return "Nenhum titulo foi selecionado para esta conta";
}
$numRegistros=mysql_num_rows($T117_validacao);


//-- ELIMINA O ARQUIVO CASO O MESMO EXISTA
$cArquivo='/tmp/remessa_756.txt';
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
	
	//-- ELIMINA CARACATERES INVALIDOS PARA CFP OU CNPJ
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

    if (empty($mT117['D024_Cpf']) and $mT117['D024_Flag_Pessoa_Fisica_Juridica']=='F')   
    { 
    	$mErroCadastro[]="Cliente: $mT117[D024_Nome_Empresa] está sem CPF. Remessa não gerada!"; 
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

$cClausula="select T035_Id from T035 where T035_D007_Id = '$D007_Id' and T035_Numero_Titulos>0";
$cT035 = mysql_query($cClausula);
$nSeqArquivo = mysql_num_rows($cT035)+1;

$cClausula="select * from T035 where T035_Data_Processamento >= '".date("Y-m-d")." 00:00:00' AND T035_Data_Processamento <='".date("Y-m-d")." 23:59:59'";
$cT035 = mysql_query($cClausula);
$numReg = mysql_num_rows($cT035)+1;
if ($numReg<10)
	$nomeArquivo=date("dmy").'0'.$numReg;
else
	$nomeArquivo=date("dmy").$numReg;

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

$cHoje = date("dmY",time());
$cHorario = date("His",time());
$nSeq=0;
$cString="";
// REGISTRO HEADER DO ARQUIVO

$RegistrosArquivo = 1;

// Código do banco
//-- posicao 001   003 (03)
$cString  .= prepararCampo("Código do Banco na Compensação","Num",3,0,"756");
 	
//-- Lote do serviço
//-- posicao 004   007  (04) 
$cString  .= prepararCampo("Lote de Serviço","Num",4,0,"0000");

//-- Tipo de Registro - 0 (Header de arquivo)
//-- posicao 008   008  (01) 
$cString  .= prepararCampo("Tipo de Registro","Num",1,0,"0");

//-- Uso exclusivo FEBRABAN/CNAB
//-- posicao 009   017  (09)  
$cString  .= prepararCampo("Brancos","Char",9,0,"");

//-- Tipo de Inscrição da Empresa
//-- posicao 018   018  (01) 
$cString  .= prepararCampo("Tipo de Inscrição da Empresa","Num",1,0,"2");

//-- Número de inscrição da empresa
//-- posicao 019   032  (14)
$cString  .= prepararCampo("Número de inscrição da empresa","Num",14,0,"{$mD007['C004_Cnpj']}");

//-- Código de convênio na cooperativa
//-- posicao: 033  052  (20)
$cString  .= prepararCampo("Código de convênio na cooperativa","Char",20,0,"");

//-- Agência da sede da cooperativa
//-- posicao 053   057  (5) 
$cString  .= prepararCampo("Agência","Num",5,0,"{$mD007['D007_Numero_Agencia']}");

//-- Dígito verificador da agência
//-- posicao 058   058  (01)  
$cString  .= prepararCampo("DV Agência","Char",1,0,"{$mD007['D007_Digito_Verificador_Agencia']}");

//-- Número da conta corrente
//-- posicao: 059   070 (12)   
$cString  .= prepararCampo("Conta corrente","Num",12,0,"{$mD007['D007_Numero_Conta']}");

//-- Dígito verificador da conta corrente
//-- posicao: 071   071  (01) 
$cString  .= prepararCampo("DV Conta","Char",1,0,"{$mD007['D007_Digito_Verificador_Conta']}");

//-- Dígito verificador da Ag/Conta: Preencher com zeros
//-- posicao: 072   072  (01)
$cString  .= prepararCampo("DV da Ag/Conta","Char",1,0,"0");

//-- Nome da empresa 
//-- posicao: 073   102  (30) 
//$mD007['C004_Nome_Empresa'] = \hardness\gLimpaAcentos($mD007['C004_Nome_Empresa']);
//$mD007['C004_Nome_Empresa'] = \hardness\gLimpaTexto($mD007['C004_Nome_Empresa']); 
$mD007['C004_Nome_Empresa'] = \hardness\gRemoveCaracteresEspeciais($mD007['C004_Nome_Empresa']);
$cString  .= prepararCampo("Nome da Empresa","Char",30,0,substr($mD007['C004_Nome_Empresa'],0,30));

//-- Nome da Cooperativa
//-- posicao 103   132  (30) 
$cString  .= prepararCampo("Nome da cooperativa","Char",30,0,"SICOOB");
// $cString  .= prepararCampo("Nome da cooperativa","Char",30,0,$nSeqArquivo);

//-- Uso exclusivo FEBRABAN/CNAB
//-- posicao 133   142  (10) 
$cString  .= prepararCampo("Brancos","Char",10,0,"");
// $cString  .= prepararCampo("Brancos","Num",10,0,$nSeq);

//-- Código Remessa/Retorno
//-- posicao 143   143  (1)
$cString  .= prepararCampo("Código Remessa","Num",1,0,"1");

//-- Data de Geração do Arquivo
//-- posicao 144   151  (8)
$cString  .= prepararCampo("Data de geração do arquivo","Num",8,0,$cHoje);

//-- Hora de geração do arquivo
//-- posicao 152   157  (6)
$cString  .= prepararCampo("Hora de geração do arquivo","Num",6,0,$cHorario);

//-- Número sequencial do arquivo
//-- posicao 158   163  (6)
$cString  .= prepararCampo("Número sequencial do arquivo","Num",6,0,"{$mD007['D007_Numero_Sequencial_Remessa']}");

//-- Número da versão do layout do arquivo
//-- posicao 164   166  (3)
$cString  .= prepararCampo("Versão do layout","Num",3,0,"081");

//-- Densidade de Gravação do Arquivo
//-- posicao 167   171  (5)
$cString  .= prepararCampo("Densidade de gravação","Num",5,0,"");

//-- Para uso reservado da coooperativa
//-- posicao 172   191  (20)
$cString  .= prepararCampo("Brancos","Char",20,0,"");

//-- Para uso reservado da empresa
//-- posicao 192   211  (20)
$cString  .= prepararCampo("Brancos","Char",20,0,"");

//-- Uso exclusivo FEBRABAN/CNAB
//-- posicao 212   240  (29)
$cString  .= prepararCampo("Brancos","Char",29,0,"");

$cString .= _SALTO_LINHA;

//HEADER DO LOTE
// No CNAB240 é possível ter vários lotes; mas foi criado apenas o lote de remessa
$RegistrosArquivo++;
//-- Código do Banco
//-- Posicao 001   003 (03)
$cString .= prepararCampo("Código do Banco","Num",3,0,"756");

//-- Lote de Serviço
//-- Posição: 004   007 (04) 
$cString .= prepararCampo("Lote de serviço","Num",4,0,"0001");

//-- Tipo de Registro - 1 (Header de Lote)
//-- posicao 008   008 (01)
$cString .= prepararCampo("Tipo de Registro","Num",1,0,"1");

//-- Tipo de Operação
//-- posicao: 009   009 (01)
$cString .= prepararCampo("Operação","Char",1,0,"R");

//-- Tipo de Serviço
//-- posicao: 010   011 (02)
$cString .= prepararCampo("Tipo de Serviço","Num",2,0,"01");

//-- Uso Exclusivo FEBRARA/CNAB
//-- posicao: 012   013 (02)
$cString .= prepararCampo("Brancos","Char",2,0,"");

//-- Número da versão do layout do lote
//-- posicao: 014   016 (03)
$cString .= prepararCampo("Versão lote","Num",3,0,"040");

//-- Uso Exclusivo FEBRABAN/CNAB
//-- posicao: 017   017 (01)
$cString .= prepararCampo("Brancos","Char",1,0,"");

//-- Tipo de inscrição da empresa
//-- posicao: 018   018 (01)
$cString .= prepararCampo("Tipo de inscrição da empresa","Num",1,0,"2");

//-- Número de inscrição da empresa
//-- posicao: 019   033 (15)
$cString .= prepararCampo("Número de Inscrição","Num",15,0,"{$mD007['C004_Cnpj']}");

//-- Código de convênio
//-- posicao: 034   053 (20)
$cString  .= prepararCampo("Código de convênio na cooperativa","Char",20,0,"");

//-- Agência da sede da Cooperativa
//-- posicao: 054   058 (05)
$cString  .= prepararCampo("Agência","Num",5,0,"{$mD007['D007_Numero_Agencia']}");

//-- Dígito verificador da agência
//-- posicao 059   059  (01)  
$cString  .= prepararCampo("DV Agência","Char",1,0,"{$mD007['D007_Digito_Verificador_Agencia']}");

//-- Número da conta corrente
//-- posicao: 060   071 (12)   
$cString  .= prepararCampo("Conta corrente","Num",12,0,"{$mD007['D007_Numero_Conta']}");

//-- Dígito verificador da conta corrente
//-- posicao: 072   072  (01) 
$cString  .= prepararCampo("DV Conta","Char",1,0,"{$mD007['D007_Digito_Verificador_Conta']}");

//-- Dígito verificador da Ag/Conta
//-- posicao: 073   073  (01)
$cString  .= prepararCampo("DV da Ag/Conta","Char",1,0,"");

//-- Nome da empresa 
//-- posicao: 074   103  (30) 
// $mD007['C004_Nome_Empresa'] = \hardness\gLimpaAcentos($mD007['C004_Nome_Empresa']);
// $mD007['C004_Nome_Empresa'] = \hardness\gLimpaTexto($mD007['C004_Nome_Empresa']); 
$mD007['C004_Nome_Empresa'] = \hardness\gRemoveCaracteresEspeciais($mD007['C004_Nome_Empresa']);
$cString  .= prepararCampo("Nome da Empresa","Char",30,0,substr($mD007['C004_Nome_Empresa'],0,30));

//-- Mensagem a ser impressa no boleto
//-- posicao: 104   143 (40)
$cString .= prepararCampo("Mensagem 1","Char",40,0,"");

//-- Mensagem a ser impressa no boleto
//-- posicao: 144   183 (40)
$cString .= prepararCampo("Mensagem 2","Char",40,0,"");

//-- Número da remessa
//-- posicao: 184  191  (08) 
$cString  .= prepararCampo("Nº Sequencial de Remessa","Num",8,0,"{$mD007['D007_Numero_Sequencial_Remessa']}");

//-- Data de Geração do Arquivo
//-- posicao 192   199  (08)
$cString  .= prepararCampo("Data de gravação da remessa","Num",8,0,$cHoje);

//-- Data do Crédito -- enviado apenas no arquivo de retorno
//-- posicao 200   207  (08)
$cString  .= prepararCampo("Data do Crédito","Num",8,0,"");

//-- Uso exclusivo FEBRABAN/CNAB
//-- posicao 208   240  (33) 
$cString  .= prepararCampo("Brancos","Char",33,0,"");

$cString .= _SALTO_LINHA;

$valorTotalRemessa=0;


while ($mT117 = mysql_fetch_array($cSql_T117))
{
	$cCpfCnpj = '';

	$valorTotalRemessa += $mT117['T117_Valor_Total'];

    $cClausula  = " update T002 set ";
    $cClausula .= " T002_Flag_Enviar_Banco='N',"; 
    $cClausula .= " T002_T035_Id='$nT035_Id'  ";
    $cClausula .= " where T002_T117_Id='$mT117[T117_Id]' or T002_Id='$mT117[T002_Id]'";
    mysqli_query($cClausula);
    
    ///-- NUMERO SEQUENCIAL PARA CADA LINHA DO ARQUIVO (CONFORME LEIAUTE)
    $nSeq++;

	$mT117['D024_Cpf']  = trim($mT117['D024_Cpf']);
	$mT117['D024_Cpf']  = str_replace(array(".","-","/"," "),"",$mT117['D024_Cpf']);
	
	$mT117['D024_Cnpj'] = trim($mT117['D024_Cnpj']);
	$mT117['D024_Cnpj'] = str_replace(array(".","-","/"," "),"",$mT117['D024_Cnpj']);

	//-- ELIMINA CARACATERES INVALIDOS PARA CFP OU CNPJ
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
	
    $mT117['D024_Cep']  = trim($mT117['D024_Cep']);
    $mT117['D024_Cep']  = str_replace(array(".","-"," "),"",$mT117['D024_Cep']);
	
	$mT117['Instrucao_Protesto_1']=$mD007['D007_Instrucao_Protesto_1'];

    if ($mT117['D024_Flag_Protestar'] != 'N') {
		$mT117['Instrucao_Protesto_1']=$mD007['D007_Instrucao_Protesto_1'];
		$mT117['Instrucao_Protesto_2']=$mD007['D007_Instrucao_Protesto_2'];
		$mT117['Instrucao_Cobranca_2']=$mD007['D007_Instrucao_Cobranca_2'];
    }

    if($mD007['D007_Flag_Tipo_Registro']==1){
    	// Com registro
    	$dvNossoNumero='14';
    	$dvCarteira='1';

    } else if($mD007['D007_Flag_Tipo_Registro']==2){
    	// Sem resgitro
    	$dvNossoNumero='24';
    	$dvCarteira='2';
	}

	
	// SEGMENTO P  - Obrigatório Remessa ----------------------------------------------------------
	//-- NUMERO SEQUENCIAL PARA CADA SEGMENTO DO ARQUIVO (CONFORME LEIAUTE)
	$RegistrosArquivo++;
	//-- Código do Banco
	//-- Posicao 001   003 (03)
	$cString .= prepararCampo("Código do Banco","Num",3,0,"756");

	//-- Lote de Serviço
	//-- Posição: 004   007 (04) 
	$cString .= prepararCampo("Lote de serviço","Num",4,0,"0001");

	//-- Tipo de Registro - 3 (Detalhe)
	//-- posicao 008   008 (01)
	$cString .= prepararCampo("Tipo de Registro","Num",1,0,"3");

	//-- Número Seqüencial Nº seqüencial do registro no arquivo
	//-- posicao 009   013 (5)
	$cString .= prepararCampo("Sequencial","Num",5,0,$nSeq);

	//-- Código segmento do registro detalhe
	//-- posicao 014   014 (1)
	$cString .= prepararCampo("Código segmento","Char",1,0,"P");

	//-- Uso exclusivo FEBRABAN/CNAB
	//-- posicao 015   015  (1)  
	$cString  .= prepararCampo("Brancos","Char",1,0,"");

	//-- Código de Movimento Remessa
	//-- posicao 016   017  (2)  
	$cString  .= prepararCampo("Movimento Remessa","Num",2,0,"01");

	//-- Agência Mantenedora da conta
	//-- posicao 018   022  (5) 
	$cString  .= prepararCampo("Agência","Num",5,0,"{$mD007['D007_Numero_Agencia']}");

	//-- Dígito verificador da agência
	//-- posicao 023   023  (01)  
	$cString  .= prepararCampo("DV Agência","Char",1,0,"{$mD007['D007_Digito_Verificador_Agencia']}");

	//-- Número da conta corrente
	//-- posicao: 024   035 (12)   
	$cString  .= prepararCampo("Conta corrente","Num",12,0,"{$mD007['D007_Numero_Conta']}");

	//-- Dígito verificador da conta corrente
	//-- posicao: 036   036  (01) 
	$cString  .= prepararCampo("DV Conta","Char",1,0,"{$mD007['D007_Digito_Verificador_Conta']}");

	//-- Dígito verificador da Ag/Conta
	//-- posicao: 037   037  (01)
	$cString  .= prepararCampo("DV da Ag/Conta","Char",1,0,"");
	
	//-- Nosso Número  - Identificação do Título no Banco
	//-- posicao 038   047 (10) 
	$mT117['T117_Nosso_Numero'] = str_replace(array(".","-","/"," "),"",$mT117['T117_Nosso_Numero']);
    $cString .= prepararCampo("Nosso Numero1","Num",10,0,"$mT117[T117_Nosso_Numero]");

	//-- Nosso Número  - Identificação do Título no Banco
	//-- posicao 048   049 (2) 
    $cString .= prepararCampo("Nosso Numero2","Char",2,0,"01");

	//-- Nosso Número  - Identificação do Título no Banco
	//-- posicao 050   051 (2) 
    $cString .= prepararCampo("Nosso Numero3","Char",2,0,"01");

	//-- Nosso Número  - Identificação do Título no Banco
	//-- posicao 052   052 (1) 
    $cString .= prepararCampo("Nosso Numero4","Char",1,0,"4");

	//-- Nosso Número  - Identificação do Título no Banco
	//-- posicao 053   057 (5) 
    $cString .= prepararCampo("Nosso Numero5","Char",5,0,"");

	//-- Código de carteira
	//-- posicao: 058   058 (01)
    $cString .= prepararCampo("Codigo da Carteira","Num",1,0,"{$dvCarteira}");

	//--Título com registro
	//-- posicao: 059   059 (01)
    $cString .= prepararCampo("Título com registro","Num",1,0,"0");

    //-- Tipo de documento
	//-- posicao: 060   060  (01) 
	$cString .= prepararCampo("Tipo do documento","Char",1,0,"");      
	
	//-- Identificação da emissão do boleto - 1(Cooperativa emite) ; 2 (Cooperado emite)
	//-- posicao: 061   061  (01) 
	$cString .= prepararCampo("Identificação da emissão do boleto","Num",1,0,"2");    

	//-- Identificação da distribuição - 1(Cooperativa distribui) ; 2(Cooperado distribui) ; 3(Banco envia e-mail) ; 4(Banco envia sms)
	//-- posicao: 062   062  (01) 
	$cString .= prepararCampo("Identificação da distribuição","Char",1,0,"2");    

	//-- Número do documento de cobrança
	//-- posicao: 063   077  (15) 
	$cString .= prepararCampo("Nº do documento","Char",15,0,"{$mT117['T002_Numero_Duplicata']}");  

	//-- Vencimento - Data de vencimento do título 
	//-- posicao: 078   085  (8) 
	$dataVencimento = date('dmY', strtotime($mT117['T117_Data_Vencimento']));
	$cString .= prepararCampo("Vencimento","Num",8,0,"$dataVencimento");     

	//-- Valor do Título  - Valor Nominal do TÍTULO
	//-- posicao: 086   100 (13)(2)          
	$cString .= prepararCampo("Valor do Titulo","Num",15,2,"$mT117[T117_Valor_Total]");   
       
	//-- AGÊNCIA COBRADORA - AGÊNCIA ONDE O TÍTULO SERÁ COBRADO
	//-- posicao: 101   105 (5) 
	$cString .= prepararCampo("AGÊNCIA COBRADORA","Num",5,0,"");

	//-- DV AGÊNCIA COBRADORA - DV AGÊNCIA ONDE O TÍTULO SERÁ COBRADO
	//-- posicao: 106   106 (1) 
	$cString .= prepararCampo("DV AGÊNCIA COBRADORA","Char",1,0,"");
        
	//-- Espécie - Espécie do TÍTULO
	//-- posicao: 107   108  (02)  
	$cString .= prepararCampo("Especie","Num",2,0,"03");
	
	//-- Aceite  - Identificação de título aceito ou não ACEITO
	//-- posição: 109   109 (01) A=ACEITE N=Não ACEITE
	if ($mT117[T117_Aceite]=='S') { $mT117[T117_Aceite]='A'; } else { $mT117[T117_Aceite]='N'; }
	$cString .= prepararCampo("Aceite","Char",1,0,"$mT117[T117_Aceite]");

	//-- Data de Emissão - Data da emissão do título
	//-- Posicao: 110   117 (08) 
	$dataEmissao = date('dmY', strtotime($mT117['T117_Data_Emissao']));
	$cString .= prepararCampo("Data de Emissão","Num",8,0,"$dataEmissao");  

	$cobraJuros = ($mT117['T117_Percentual_Juros_Dia'] > 0 ) ? "1" : "0";
	
	//-- Código de Juros de Mora - 1(Valor por dia) ; 2(Taxa mensal) ;
	//-- Posicao: 118   118 (01) 
	$cString .= prepararCampo("Código de juros mora","Num",1,0, $cobraJuros);  

	//-- Data multa
	//-- Posicoes: 119   126 (08)
	$datamulta = ($cobraJuros === '1') ? date('dmY', strtotime("+1 days",strtotime($mT117['T117_Data_Vencimento']))) : "00000000"; 
	$cString .= prepararCampo("Data Multa","Num",8,0,$datamulta);

	// -- Juros de Mora por Dia/Taxa
	// -- posicao: 127  141(13)(2)
	$jurosDia = round((($mT117['T117_Percentual_Juros_Dia']/30)/100)*$mT117['T002_Valor_Total'],2);
    $cString .= prepararCampo("Juros de Mora por Dia/Taxa","Num",15,2,$jurosDia);
	
	//-- Código de Desconto - 0(Não assumirá desconto) ; 1(Valor fixo até a data informada)
	//-- Posicao: 142   142 (01) 
	$cString .= prepararCampo("Código de desconto","Num",1,0,"0");  

	//-- Desconto até  - Data limite para concessão de desconto
	//-- posicao: 143   150  (8) DDMMAAAA 
	$cString .= prepararCampo("Desconto até","Num",8,0,"00000000");  

	//-- Valor do desconto - Valor do desconto a ser concedido
	//-- posicao: 151   165  (13)(2) 
	$cString .= prepararCampo("Valor do desconto","Num",15,2,"");     

	//-- Valor do I.O.F.  - Valor do I.O.F. recolhido p/ Notas Seguro
	//-- posicao: 166   180  (13)(2) 
	$cString .= prepararCampo("Valor do IOF","Num",15,2,"");   

	//-- Abatimento - Valor do abatimento a ser concedido
	//-- Posicao: 181   195 (13)(2) 
	$cString .= prepararCampo("Abatimento","Num",15,2,"");        

	//-- Identificação do título na empresa
	//-- posicao: 196   220  (25) 
	$cString .= prepararCampo("Nº do documento","Char",25,0,"{$mT117['T002_Numero_Duplicata']}");  

	//-- Código para negativação ou protesto - 1(Protestar dias corridos) ; 2(Negativação de boletos via Serasa) ; 3(Não protestar)
	//-- posicao: 221   221  (01) 
	$codigoProtesto = $mD007['D007_Instrucao_Protesto_1'];
	if (!in_array($codigoProtesto, array('1', '2', '3', '8', '9'), true)) {
		$codigoProtesto = '3';
	}
	$cString .= prepararCampo("Código para negativação ou protesto","Num",1,0,$codigoProtesto);

	//-- Número de dias para negativação - Prazo CECRED de 5 a 15 dias corridos
	//-- posicao: 222   223  (2) 
	$diasNeg = ($mD007['D007_Instrucao_Protesto_2'] <= 0 ) ? $mD007['D007_Instrucao_Protesto_2'] : 00;
		
	$cString .= prepararCampo("Dias para negativação","Num",2,0,$diasNeg);

	//-- Código para Baixa/Devolução - 1(Baixar/Devolver) ; 2(Não Baixar/não Devolver)
	//-- posicao: 224   224  (1) 
	$cString .= prepararCampo("Código para baixa/devolução","Num",1,0,"0");  

	//-- Número de dias para devolução
	//-- posicao: 225   227  (3) 
	$cString .= prepararCampo("Dias para devolução","Char",3,0,"");  

	//-- Código da moeda
	//-- posicao 228   229 (2)
	$cString .= prepararCampo("Cod moeda","Num",2,0,"09");

	//-- Nº do contrato da operação de créd
	//-- posicao 230   239 (10)
	$cString .= prepararCampo("Contrato da operação","Num",10,0,"");

	//-- Uso livre banco/empresa
	//-- posicao 240   240 (1)
	$cString .= prepararCampo("Brancos","Char",1,0,"");


	$cString .= _SALTO_LINHA;

	//SEGMENTO Q (Obrigatório Remessa)----------------------------------------------------------
	//-- NUMERO SEQUENCIAL PARA CADA SEGMENTO DO ARQUIVO (CONFORME LEIAUTE)
    $nSeq++;
	$RegistrosArquivo++;

	//-- Código do Banco
	//-- Posicao 001   003 (03)
	$cString .= prepararCampo("Código do Banco","Num",3,0,"756");

	//-- Lote de Serviço
	//-- Posição: 004   007 (04) 
	$cString .= prepararCampo("Lote de serviço","Num",4,0,"0001");

	//-- Tipo de Registro - 3 (Detalhe)
	//-- posicao 008   008 (01)
	$cString .= prepararCampo("Tipo de Registro","Num",1,0,"3");

	//-- Número Seqüencial Nº seqüencial do registro no arquivo
	//-- posicao 009   013 (5)
	$cString .= prepararCampo("Sequencial","Num",5,0,$nSeq);

	//-- Código segmento do registro detalhe
	//-- posicao 014   014 (1)
	$cString .= prepararCampo("Código segmento","Char",1,0,"Q");

	//-- Uso exclusivo FEBRABAN/CNAB
	//-- posicao 015   015  (1)  
	$cString  .= prepararCampo("Brancos","Char",1,0,"");

	//-- Código de Movimento Remessa
	//-- posicao 016   017  (2)  
	$cString  .= prepararCampo("Movimento Remessa","Num",2,0,"01");
	
	//-- Tipo de Inscrição Identificação do tipo de inscrição/sacado
	//-- Posicao: 018   018  (01) 1=CPF 2=CNPJ
	$cString .= prepararCampo("Código de Inscrição","Num",1,0,"$cCpfCnpjTipo");        
        
	//-- Número de Inscrição - Nº de inscrição do Sacado  (CPF/CNPJ)
	//-- Posicao: 019   033 (15)
	$cString .= prepararCampo("Número de Inscrição","Num",15,0,"$cCpfCnpj");

	//-- Nome - Nome do sacado
	//-- Posicao: 034   073 (40) 
	//$mT117['D024_Nome_Empresa']=\hardness\gLimpaAcentos($mT117['D024_Nome_Empresa']);
	//$mT117['D024_Nome_Empresa']=\hardness\gLimpaTexto($mT117['D024_Nome_Empresa']);
	$mT117['D024_Nome_Empresa']=\hardness\gRemoveCaracteresEspeciais($mT117['D024_Nome_Empresa']);
	$cString .= prepararCampo("Nome do sacado","Char",40,0,"{$mT117['D024_Nome_Empresa']}");

	//-- Endereço - Rua, Número e complemento do Sacado
	//-- Posição: 074   113 (40)
	//$mT117['D024_Endereco']=\hardness\gLimpaAcentos($mT117['D024_Endereco']);
	//$mT117['D024_Endereco']=\hardness\gLimpaTexto($mT117['D024_Endereco']);
	$mT117['D024_Endereco']=\hardness\gRemoveCaracteresEspeciais($mT117['D024_Endereco']);
    //$mT117['D024_Complemento']=\hardness\gLimpaAcentos($mT117['D024_Complemento']);
	//$mT117['D024_Complemento']=\hardness\gLimpaTexto($mT117['D024_Complemento']);
	$mT117['D024_Complemento']=\hardness\gRemoveCaracteresEspeciais($mT117['D024_Complemento']);
    //$mT117['D020_Nome_Cidade']=\hardness\gLimpaAcentos($mT117['D020_Nome_Cidade']);
	//$mT117['D020_Nome_Cidade']=\hardness\gLimpaTexto($mT117['D020_Nome_Cidade']);
	$mT117['D020_Nome_Cidade']=\hardness\gRemoveCaracteresEspeciais($mT117['D020_Nome_Cidade']);
	//$mT117['D024_Bairro']=\hardness\gLimpaAcentos($mT117['D024_Bairro']);
    //$mT117['D024_Bairro']=\hardness\gLimpaTexto($mT117['D024_Bairro']);
    $mT117['D024_Bairro']=\hardness\gRemoveCaracteresEspeciais($mT117['D024_Bairro']);
	$cString .= prepararCampo("Endereço","Char",40,0,"{$mT117['D024_Endereco']}, {$mT117['D024_Numero']} {$mT117['D024_Complemento']}");
        
	//-- Bairro - Bairro do Sacado
	//-- Posicoes: 114   128 (15)
	$cString .= prepararCampo("Bairro","Char",15,0,"{$mT117['D024_Bairro']}");

	//-- CEP - CEP do sacado 
	//-- Posicoes: 129   136 (08)
	$cString .= prepararCampo("Cep","Num",8,0,"$mT117[D024_Cep]");

	//-- Cidade - Cidade do Sacado 
	//-- Posicoes: 137   151  (15)
	$cString .= prepararCampo("Cidade","Char",15,0,"$mT117[D020_Nome_Cidade]");

	//-- Estado - UF do Sacado
	//-- Posicoes: 152   153  (2)
	$cString .= prepararCampo("UF","Char",2,0,"$mT117[D018_UF]");

	//-- Tipo de Inscrição Identificação do tipo de inscrição/sacado
	//-- Posicao: 154   154  (01) 1=CPF 2=CNPJ
	$cString .= prepararCampo("Código de Inscrição","Num",1,0,"0");        
        
	//-- Número de Inscrição - Nº de inscrição do Sacado  (CPF/CNPJ)
	//-- Posicao: 155   169 (15)
	$cString .= prepararCampo("Número de Inscrição","Num",15,0,"0");

	//-- Sacador/Avalista Nome do Sacador ou Avalista
	//-- Posicoes 170   209	(40) 
	$cString .= prepararCampo("Sacador/Avalista","Char",40,0,"");

	//-- Código do Banco
	//-- Posicao 210   212 (03)
	$cString .= prepararCampo("Código do Banco","Num",3,0,"0");

	//-- Nosso Número  - Identificação do Título no Banco
	//-- posicao 213   232 (20) 
	$cString .= prepararCampo("Nosso Numero","Char",20,0,"");
	
	//-- Uso excluisov FEBRABAN/CNAB
	//-- posicao 233   240 (08) 
    $cString .= prepararCampo("Brancos","Char",8,0,"");

	$cString .= _SALTO_LINHA;


	//SEGMENTO R (Obrigatório Remessa)----------------------------------------------------------
	//-- NUMERO SEQUENCIAL PARA CADA SEGMENTO DO ARQUIVO (CONFORME LEIAUTE)
    $nSeq++;
	$RegistrosArquivo++;

	//-- Código do Banco
	//-- Posicao 001   003 (03)
	$cString .= prepararCampo("Código do Banco","Num",3,0,"756");

	//-- Lote de Serviço
	//-- Posição: 004   007 (04) 
	$cString .= prepararCampo("Lote de serviço","Num",4,0,"0001");

	//-- Tipo de Registro - 3 (Detalhe)
	//-- posicao 008   008 (01)
	$cString .= prepararCampo("Tipo de Registro","Num",1,0,"3");

	//-- Número Seqüencial Nº seqüencial do registro no arquivo
	//-- posicao 009   013 (5)
	$cString .= prepararCampo("Sequencial","Num",5,0,$nSeq);

	//-- Código segmento do registro detalhe
	//-- posicao 014   014 (1)
	$cString .= prepararCampo("Código segmento","Char",1,0,"R");

	//-- Uso exclusivo FEBRABAN/CNAB
	//-- posicao 015   015  (1)  
	$cString  .= prepararCampo("Brancos","Char",1,0,"");

	//-- Código de Movimento Remessa
	//-- posicao 016   017  (2)  
	$cString  .= prepararCampo("Movimento Remessa","Num",2,0,"01");
	
	
	//-- Posicao: 018   018  (01) Desconto
	$cString .= prepararCampo("Código de desconto","Num",1,0,"0");      
        
	//-- Posicao: 019   026 (08) Data Desconto
	$cString .= prepararCampo("Desconto até","Num",8,0,"00000000");  

	//-- Nome - Nome do sacado//-- Valor do desconto - Valor do desconto a ser concedido
	//-- Posicao: 027   041 (13) 
	$cString .= prepararCampo("Valor do desconto","Num",15,2,""); 

	//-- Código de Desconto - 0(Não assumirá desconto) ; 1(Valor fixo até a data informada)
	//-- Posicao: 042   042 (01) 
	$cString .= prepararCampo("Código de desconto","Num",1,0,"0");  
	
	//-- Posicao: 043   050 (08) Data Desconto
	$cString .= prepararCampo("Desconto até","Num",8,0,"00000000");  

	//-- Nome - Nome do sacado//-- Valor do desconto - Valor do desconto a ser concedido
	//-- Posicao: 051   065 (13) 
	$cString .= prepararCampo("Valor do desconto","Num",15,2,""); 

	$codigoMulta = ($mT117['T117_Percentual_Multa'] > 0 ) ? (($mD007['D007_Flag_Tipo_Multa'] == 'V') ? "1" : "2") : "0";
	//-- Código Multa
	//-- Posicao: 066   066 (01)
	$cString .= prepararCampo("Código de multa","Num",1,0, $codigoMulta);
		
	//-- Data multa
	//-- Posicoes: 067   074 (08)
	$datamulta = ($codigoMulta !== '0') ? date('dmY', strtotime("+1 days",strtotime($mT117['T117_Data_Vencimento']))) : "00000000";
	$cString .= prepararCampo("Data multa","Num",8,0,$datamulta); 

    // Valor Multa
	//-- Posicao: 075   089 (13) 
	$valorMulta = 0;
	if ($codigoMulta === '1') {
		$valorMulta = round(($mT117['T117_Percentual_Multa']/100) * $mT117['T002_Valor_Total'],2);
	} elseif ($codigoMulta === '2') {
		$valorMulta = $mT117['T117_Percentual_Multa'];
	}
    $cString .= prepararCampo("Valor Multa","Num",15,2,$valorMulta);


	//-- Informacao ao Pagador
	//-- posicao 090   099 (10) 
    $cString .= prepararCampo("Brancos","Char",10,0,"");


	//-- Mensagem 3
	//-- posicao 100   139 (40) 
    $cString .= prepararCampo("Brancos","Char",40,0,"");

	//-- Mensagem 4
	//-- posicao 140   179 (40) 
    $cString .= prepararCampo("Brancos","Char",40,0,"");

	//--  Uso exclusivo FEBRABAN
	//-- posicao 180   199 (20) 
    $cString .= prepararCampo("Brancos","Char",20,0,"");

	//-- Data Limite
	//-- Posicoes: 200   207 (08)
	$cString .= prepararCampo("Limite até","Num",8,0,"00000000"); 

	//Codigo Banco da Conta do Debito
	//-- posicao 208   210 (3) 
    $cString .= prepararCampo("Brancos","Num",3,0,"");

	//Codigo Agencia da Conta do Debito
	//-- posicao 211   215 (5) 
    $cString .= prepararCampo("Brancos","Num",5,0,"");

	//DV Agencia da Conta do Debito
	//-- posicao 216   216 (1) 
    $cString .= prepararCampo("Brancos","Char",1,0,"");


	//Codigo Conta Corrente do Debito
	//-- posicao 217   228 (12) 
    $cString .= prepararCampo("Brancos","Num",12,0,"");

	//DV Conta Corrente do Debito
	//-- posicao 229   229 (1) 
    $cString .= prepararCampo("Brancos","Char",1,0,"");

	//DV Agencia/Conta Corrente do Debito
	//-- posicao 230   230 (1) 
    $cString .= prepararCampo("Brancos","Char",1,0,"");

	//Aviso para Debito Automatico
	//-- posicao 231   231 (1) 
    $cString .= prepararCampo("Brancos","Num",1,0,"");


	//Uso exclusivo FEBRABAN
	//-- posicao 232   240 (9) 
    $cString .= prepararCampo("Brancos","Char",9,0,"");


	$cString .= _SALTO_LINHA;
}

// -- RODAPE
$nSeq++;

//-- TRAILER DO LOTE
$RegistrosArquivo++;
//-- Código do banco

//-- posicao 001   003 (03)
$cString .= prepararCampo("Código do Banco","Num",3,0,"756");

//-- Lote de Serviço
//-- posicao 004   007 (04)
$cString .= prepararCampo("Lote de Serviço","Num",4,0,"0001");

//-- Tipo de Registro - 5(Trailer dO LOTE)
//-- posicao 008   008 (01)
$cString .= prepararCampo("Tipo de Registro","Num",1,0,"5");

//-- BRANCOS - COMPLEMENTO DO REGISTRO
//-- posicao 009   017 (09) 
$cString .= prepararCampo("Brancos","Char",9,0,"");


//-- Quantidade de registros no lote
//-- posicao 018   023 (06) 
$cString .= prepararCampo("Quantidade de registros lotes","Num",6,0,"$nSeq");

//COBRANÇA SIMPLES -----------------------------------------------------------------------------
//-- Quantidade de títulos em cobrança
//-- posicao 024   029 (06) 
$cString .= prepararCampo("Quantidade de títulos em cobrança","Num",6,0,"$nSeq");

//-- Valor total dos títulos em carteiras
//-- posicao 030   046 (15)(2) 
$cString .= prepararCampo("Valor total dos títulos em carteiras","Num",17,2,"$valorTotalRemessa");
//COBRANÇA SIMPLES -----------------------------------------------------------------------------

//COBRANÇA VINCULADA -----------------------------------------------------------------------------
//-- Quantidade de títulos em cobrança
//-- posicao 047   052 (06) 
$cString .= prepararCampo("Quantidade de títulos em cobrança","Num",6,0,"");

//-- Valor total dos títulos em carteiras
//-- posicao 053   069 (15)(2) 
$cString .= prepararCampo("Valor total dos títulos em carteiras","Num",17,0,"");
//COBRANÇA VINCULADA -----------------------------------------------------------------------------

//COBRANÇA CAUCIONADA -----------------------------------------------------------------------------
//-- Quantidade de títulos em cobrança
//-- posicao 070   075 (06) 
$cString .= prepararCampo("Quantidade de títulos em cobrança","Num",6,0,"");

//-- Valor total dos títulos em carteiras
//-- posicao 076   092 (15)(2) 
$cString .= prepararCampo("Valor total dos títulos em carteiras","Num",17,0,"");
//COBRANÇA CAUCIONADA -----------------------------------------------------------------------------

//COBRANÇA DESCONTADA -----------------------------------------------------------------------------
//-- Quantidade de títulos em cobrança
//-- posicao 093   098 (6) 
$cString .= prepararCampo("Quantidade de títulos em cobranã","Num",6,0,"");

//-- Valor total dos títulos em carteiras
//-- posicao 099   115 (15)(02) 
$cString .= prepararCampo("Valor total dos títulos em carteiras","Num",17,0,"");
//COBRANÇA DESCONTADA -----------------------------------------------------------------------------

//-- Nº do aviso do lançamento
//-- posicao 116   123 (08) 
$cString .= prepararCampo("Nº do aviso do lançamento","Char",8,0,"");

//-- BRANCOS - COMPLEMENTO DO REGISTRO
//-- posicao 124   240 (117) 
$cString .= prepararCampo("Brancos","Char",117,0,"");

$cString .= _SALTO_LINHA;


//-- TRAILER DO ARQUIVO
$RegistrosArquivo++;
//-- Código do banco
//-- posicao 001   003 (03)
$cString .= prepararCampo("Código do Banco","Num",3,0,"756");

//-- Lote de Serviço
//-- posicao 004   007 (04)
$cString .= prepararCampo("Lote de Serviço","Num",4,0,"9999");

//-- Tipo de Registro - 9(Trailer de arquivo)
//-- posicao 008   008 (01)
$cString .= prepararCampo("Tipo de Registro","Num",1,0,"9");

//-- BRANCOS - COMPLEMENTO DO REGISTRO
//-- posicao 009   017 (09) 
$cString .= prepararCampo("Brancos","Char",9,0,"");

//-- Quantidade de lotes do arquivo
//-- posicao 018   023 (06) 
$cString .= prepararCampo("Quantidade de lotes","Num",6,0,"{$RegistrosArquivo}");

//-- Quantidade de registros do arquivo
//-- posicao 024   029 (06) 
$cString .= prepararCampo("Quantidade de registros","Num",6,0,"$nSeq");

//-- Quantidade de contas p/ Conc. (Lotes)
//-- posicao 030   035 (06) 
$cString .= prepararCampo("Quantidade de contas","Num",6,0,"");

//-- BRANCOS - COMPLEMENTO DO REGISTRO
//-- posicao 036   240 (205) 
$cString .= prepararCampo("Brancos","Char",205,0,"");

$cString .= _SALTO_LINHA;

/*
fwrite($cArquivo_Remessa_Aberto, $cString ._SALTO_LINHA);

fclose($cArquivo_Remessa_Aberto);
$Ponteiro=fopen($cArquivo_Remessa, "r");
$cConteudo=fread($Ponteiro, filesize($cArquivo_Remessa));
*/

mysql_query("UPDATE D007 SET D007_Numero_Sequencial_Remessa = '{$mD007['D007_Numero_Sequencial_Remessa']}' WHERE D007_Id = '{$D007_Id}'");

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
            echo "Erro!! Campo:$Campo - deve ter $Tamanho caracteres ".strlen($String);
            exit;
        }        
        $cRetorno = STR_PAD($String,$Tamanho,"0",STR_PAD_LEFT);
    }        
    
    return $cRetorno;
}

?>
