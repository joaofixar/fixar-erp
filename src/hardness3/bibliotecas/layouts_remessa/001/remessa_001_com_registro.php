<?php
namespace hardness;
/*
Programador: Evantoni
Data: 01/08/2012
*/

DEFINE("_SALTO_LINHA",CHR(13).CHR(10));


$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from D007 ";
$cClausula .= " left join C004 on C004_Id=D007_C004_Id ";
$cClausula .= " where D007_Id='$D007_Id' ";
$D007 = mysql_query($cClausula);
$mD007=mysql_fetch_array($D007);

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

$cHoje = date("dmy",time());
$nSeq=1;
$cString="";
//-- tipo de Registro - Identificação do registro Header 
//-- posicao 001   001 9(01)
$cString  .= prepararCampo("Tipo de Registro","Num",1,0,"0");
 	
//-- operacao - TIPO DE OPERAÇÃO - REMESSA
//-- posicao 002   002  9(01) 
$cString  .= prepararCampo("Operação","Num",1,0,"1");

//-- Literal de Remessa -Identificação por extenso DO movimento 
//-- posicao 003   009  X(07) 
$cString  .= prepararCampo("Literal de Remessa","Char",7,0,"REMESSA");

//-- Código do Serviço - Identificação do tipo de serviço
//-- posicao 010   011 9(02)  
$cString  .= prepararCampo("Código do Serviço","Num",2,0,"01");

//-- Literal de serviço Identificação por extenso do tipo de serviço
//-- posicao 012   019 X(08) 
$cString  .= prepararCampo("Literal de serviço","Char",8,0,"COBRANCA");

//-- brancos
//-- posicao 020   026 X(07) 
$cString  .= prepararCampo("Brancos","Char",7,0,"");

//-- agência - agência mantenedora da conta
//-- 027   030 9(04)
$cString  .= prepararCampo("Agência","Num",4,0,"{$mD007['D007_Numero_Agencia']}");

//-- DV agencia
//-- posicao 031   031 9(01) 
$cString  .= prepararCampo("DV Agência","Num",1,0,"{$mD007['D007_Digito_Verificador_Agencia']}");

//-- conta número da conta corrente da empresa
//-- posicao: 032   039 9(08)
$cString  .= prepararCampo("Conta","Num",8,0,"{$mD007['D007_Numero_Conta']}");

//-- dac dígito de auto conferência ag/conta empresa
// poiscao: 040   040 9(01)
$cString  .= prepararCampo("Dac","Num",1,0,"{$mD007['D007_Digito_Verificador_Conta']}");

//-- Brancos  Complemento do registro 
//-- posicao: 041   046 X(06)  
$cString  .= prepararCampo("Brancos","Num",6,0,"000000");

//-- nome da empresa
//-- posicao 047  076 9(30)
$mD007['C004_Nome_Empresa'] =\hardness\gLimpaAcentos($mD007['C004_Nome_Empresa']);
$mD007['C004_Nome_Empresa'] =\hardness\gLimpaTexto($mD007['C004_Nome_Empresa']);
$cString  .= prepararCampo("Nome da Empresa","Char",30,0,"{$mD007['C004_Nome_Empresa']}");

//-- Nome do Banco - Nome por extenso do banco cobrador
//-- posicao: 077   094  X(15) 
$cString  .= prepararCampo("Nome do Banco","Char",18,0,"001BANCODOBRASIL");

//-- Data de Geração - Data de geração do arquivo 
//-- posicao: 095   100  9(06)
$cString  .= prepararCampo("Data de Geração","Num",6,0,$cHoje);

//-- Brancos  Complemento do REGISTRO
//-- posicao: 101   107 X(7)
$cString  .= prepararCampo("Sequencial da Remessa (Controle do Cliente)","Num",7,0,$nT035_Id);

//-- Brancos  Complemento do REGISTRO
//-- posicao: 108   129 X(22)
$cString  .= prepararCampo("Brancos","Char",22,0,"");

//-- Número Seqüencial do registro no arquivo 
//-- posicao 130   136  9(07) 
$cString  .= prepararCampo("Codigo do Convenio","Num",7,0,"{$mD007['D007_Numero_Convenio']}");

//-- Brancos  Complemento do REGISTRO
//-- posicao: 137   394 X(258)
$cString  .= prepararCampo("Brancos","Char",258,0,"");

//-- Número Seqüencial do registro no arquivo 
//-- posicao 395   400  9(06) 
$cString  .= prepararCampo("Sequencial","Num",6,0,$nSeq);

$cString .= _SALTO_LINHA;

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
    
    //-- NUMERO SEQUENCIAL PARA CADA LINHA DO ARQUIVO (CONFORME LEIAUTE)
    $nSeq++;

    //-- ELIMINA CARACATERES INVALIDOS PARA CPF OU CNPJ
    if ($mT117['D024_Flag_Pessoa_Fisica_Juridica']=='F')
    {
       $cCpfCnpjTipo='01';
       $mT117['D024_Cpf'] = trim($mT117['D024_Cpf']);
       $cCpfCnpj  = str_replace(array(".","-","/"," "),"",$mT117['D024_Cpf']);
    }
    elseif ($mT117['D024_Flag_Pessoa_Fisica_Juridica']=='J')
    {
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
      
    if ($mT117['D024_Flag_Protestar'] != 'N') {
		$mT117['Instrucao_Protesto_1']=$mD007['D007_Instrucao_Protesto_1'];
    }
	// NO CASO DO LAYOUT CNAB400 É ENVIADO O CÓDIGO 07 PARA NÃO PROTESTAR
	$mT117['Instrucao_Protesto_2']=$mD007['D007_Instrucao_Protesto_2'];

    $C004_Cnpj = str_replace(array(".","-","/"," "),"",$mD007['C004_Cnpj']);

	//-- Tipo de registro - Identificação do registro Transação
	//-- Posicao 001   001 9(01)
    $cString .= prepararCampo("Tipo de Registro","Num",1,0,"7");
    //-- Código de Inscrição tipo de inscrição da empresa
	//-- Posição: 002   003  9(02) 
    $cString .= prepararCampo("Código de Inscrição","Num",2,0,"$cCpfCnpjTipo");

	//-- Número de Inscrição Nº de inscrição da empresa (CPF/CNPJ)
	//-- posicao 004   017 9(14)
    $cString .= prepararCampo("CPF/CNPJ","Num",14,0,$C004_Cnpj);
  
	//-- agência mantenedora da conta
	//-- posicao: 018   021 9(04)
    $cString .= prepararCampo("Agencia","Num",4,0,"{$mD007['D007_Numero_Agencia']}");
    
	//-- zeros complemento de registro
	//-- posicao: 022   022 9(01)
    $cString .= prepararCampo("DV Agencia","Num",1,0,"{$mD007['D007_Digito_Verificador_Agencia']}");
    
	//-- conta - número da conta corrente da empresa
	//-- posicao: 023   030 9(08)
    $cString .= prepararCampo("Conta","Num",8,0,"{$mD007['D007_Numero_Conta']}");

	//-- dac - dígito de auto conferência ag/conta empresa
	//-- posicao: 031   031 9(01)
    $cString .= prepararCampo("Digito Conta","Num",1,0,"{$mD007['D007_Digito_Verificador_Conta']}");
    
	//-- Brancos - Complemento de registro
	//-- posicoes: 032   038 9(07)
    $cString .= prepararCampo("Codigo do Convenio","Num",7,0,"{$mD007['D007_Numero_Convenio']}");

    //-- Nº Controle do Participante
    //-- posicao 039 063 (25)
    $cString .= prepararCampo("Nº Controle do Part.","Char",25,0,"");  #ERRO

    //-- numero do banco
	//-- posicao: 064 080 9(17)
	$cString  .= prepararCampo("Nosso Numero","Num",17,0,$mT117['T117_Nosso_Numero']);

	//-- Numero da Prestação
	//-- posicoes: 081   082 9(02)
    $cString .= prepararCampo("Numero da Prestação","Num",2,0,"");

    //-- Grupo de Valor
	//-- posicoes: 083   084 9(02)
    $cString .= prepararCampo("Grupo de Valor","Num",2,0,"");

    //-- Brancos - Complemento de registro
	//-- posicoes: 085   087 9(03)
	$cString .= prepararCampo("Brancos","Char",3,0,"");

	//-- Brancos - Indicativo de Mensagem
	//-- posicoes: 088   088 9(01)
	$cString .= prepararCampo("Indicativo de Mensagem","Char",1,0,"");

	//-- Brancos - Prefixo do Titulo
	//-- posicoes: 089   091 9(03)
	$cString .= prepararCampo("Brancos","Char",3,0,"");

	//-- Carteira
	//-- posicoes: 092   094 9(03)
	$cString .= prepararCampo("Carteira","Num",3,0, $mD007['D007_Variacao_Carteira']);

	//-- Brancos - Conta Caução
	//-- posicoes: 095   095 9(01)
	$cString .= prepararCampo("Conta Caução","Num",1,0,"0");

	//-- Numero do Borderô
	//-- posicoes: 096   101 9(06)
	$cString .= prepararCampo("Numero do Borderô","Num",6,0,"000000");

	//-- Tipo de Cobrança
	//-- posicoes: 102   106 9(05)
	//$tipoCobranca = (!empty($mD007['D007_Tipo_Cobranca']) && $mD007['D007_Tipo_Cobranca'] != '') ? $mD007['D007_Tipo_Cobranca'] : '02VIN';
	$tipoCobranca =  $mD007['D007_Tipo_Cobranca'];
	$cString .= prepararCampo("Tipo de Cobrança","Char",5,0,"{$tipoCobranca}");

	//-- Carteira de Cobrança
	//-- posicoes: 107   108 9(02)
	$cString .= prepararCampo("Carteira de Cobrança","Num",2,0,"{$mD007['D007_Tipo_Carteira_Banco_Brasil']}");

	//-- Comando
	//-- posicoes: 109   110 9(02)
	$cString .= prepararCampo("Comando","Num",2,0,"01");

	//-- Número do Titulo (Campo Extra)
	//-- posicoes: 111   120 9(10)
	$cString .= prepararCampo("Número do Título","Char",10,0,$mT117['T002_Numero_Duplicata']);

	//-- Vencimento - Data de vencimento do título 
	//-- posicao: 121   126  9(06)
	$cString .= prepararCampo("Vencimento","Date",6,0,"{$mT117['T117_Data_Vencimento']}");        

	//-- Valor do Título  - Valor Nominal do TÍTULO
	//-- posicao: 127   139 9(11)V9(2) *Nota 8         
	$cString .= prepararCampo("Valor do Titulo","Num",13,2,"{$mT117['T117_Valor_Total']}");        
    
	//-- Código do Banco  - Nº do Banco na câmara de compensação 
	//-- Posicao 140   142  9(03) 
	$cString .= prepararCampo("Código do Banco","Num",3,0,"001");  

	//-- AGÊNCIA COBRADORA - AGÊNCIA ONDE O TÍTULO SERÁ COBRADO
	//-- posicao: 143   146 9(04) *nota 9
	$cString .= prepararCampo("AGÊNCIA COBRADORA","Num",4,0,"");

	//-- DV AGÊNCIA COBRADORA
	//-- posicao: 147   147 9(01) *nota 9
	$cString .= prepararCampo("DV AGÊNCIA COBRADORA","Char",1,0,"");

	//-- Espécie - Espécie do TÍTULO
	//-- posicao: 148   149  X(02) *Nota 10 
	$cString .= prepararCampo("Especie","Num",2,0,"01");        
        
	//-- Aceite  - Identificação de título aceito ou não ACEITO
	//-- posição: 150   150 X(01) SEMPRE = N	
	$cString .= prepararCampo("Aceite","Char",1,0,"N");

	//-- Data de Emissão - Data da emissão do título
	//-- Posicao: 151   156 9(06) 
	$cString .= prepararCampo("Data de Emissão","Date",6,0,"{$mT117['T117_Data_Emissao']}");

	//-- Instrução 1  - 1ª instrução de cobrança 
	//-- posicao 157   158  X(02) *Nota 11
	$cString .= prepararCampo("Instrução 1","Num",2,0,"{$mT117['Instrucao_Protesto_1']}");        
        
	//-- Instrução 2  2ª instrução de cobrança
	//-- posicao 159   160 X(02) *Nota 11
	$cString .= prepararCampo("Instrução 2","Num",2,0,"{$mT117['Instrucao_Protesto_2']}");        

	//-- Juros de 1 dia - Valor de Mora por dia de atraso
	//-- posicao: 161   173  9(11)V9(2) *Nota 12 
	$jurosDia = round((($mT117['T117_Percentual_Juros_Dia']/30)/100)*$mT117['T002_Valor_Total'],2);
	$cString .= prepararCampo("Juros de 1 dia","Num",13,2,$jurosDia);
        
	//-- Desconto até  - Data limite para concessão de desconto
	//-- posicao: 174   179  9(06) DDMMAA 
	$cString .= prepararCampo("Desconto até","Date",6,0,"");        

	//-- Valor do desconto - Valor do desconto a ser concedido
	//-- posicao: 180   192  9(11)V9(2) *Nota 13 
	$cString .= prepararCampo("Valor do desconto","Num",13,2,"");        
        
	//-- Valor do I.O.F.  - Valor do I.O.F. recolhido p/ Notas Seguro
	//-- posicao: 193   205  9(11)V((2) *Nota 14 
	$cString .= prepararCampo("Valor do IOF","Num",13,2,"");               

	//-- Abatimento - Valor do abatimento a ser concedido
	//-- Posicao: 206   218 9(11)V9(2) *Nota 13 
	$cString .= prepararCampo("Abatimento","Num",13,2,"");        
        
	//-- Código de Inscrição Identificação do tipo de inscrição/sacado
	//-- Posicao: 219   220  9(02) 01=CPF 02=CNPJ
	$cString .= prepararCampo("Código de Inscrição","Num",2,0,"$cCpfCnpjTipo");        
        
	//-- Número de Inscrição - Nº de inscrição do Sacado  (CPF/CNPJ)
	//-- Posicao: 221   234 9(14)
	$cString .= prepararCampo("Número de Inscrição","Num",14,0,"$cCpfCnpj");

	//-- Nome - Nome do sacado
	//-- Posicao: 235   271 X(37) *NOTA 15
	$mT117['D024_Nome_Empresa']=\hardness\gLimpaAcentos($mT117['D024_Nome_Empresa']);
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaTexto($mT117['D024_Nome_Empresa']);
	$cString .= prepararCampo("Nome do sacado","Char",37,0,"{$mT117['D024_Nome_Empresa']}");

	//-- Brancos - Complemento do registro
	//-- Posição: 272   274 X(3)
	$cString .= prepararCampo("Brancos","Char",3,0,"");


	//-- Endereço do Sacado
	//-- Posição: 275   314 X(3)
	$mT117['D024_Endereco']=\hardness\gLimpaAcentos($mT117['D024_Endereco']);
    $mT117['D024_Endereco']=\hardness\gLimpaTexto($mT117['D024_Endereco']);    
    $cString .= prepararCampo("Endereco","Char",40,0,"{$mT117['D024_Endereco']}, {$mT117['D024_Numero']}");    	

	//-- Bairro - Bairro do Sacado
	//-- Posicoes: 315   326 X(12)
	$mT117['D024_Bairro']=\hardness\gLimpaAcentos($mT117['D024_Bairro']);
	$mT117['D024_Bairro']=\hardness\gLimpaTexto($mT117['D024_Bairro']);
	$cString .= prepararCampo("Bairro","Char",12,0,"{$mT117['D024_Bairro']}");

	//-- CEP - CEP do sacado 
	//-- Posicoes: 327   334 9(08)
	$cString .= prepararCampo("Cep","Num",8,0,"{$mT117['D024_Cep']}");

	//-- Cidade do sacado 
	//-- Posicoes: 335   349 9(15)
	$mT117['D020_Nome_Cidade']=\hardness\gLimpaAcentos($mT117['D020_Nome_Cidade']);
	$mT117['D020_Nome_Cidade']=\hardness\gLimpaTexto($mT117['D020_Nome_Cidade']);
	$cString .= prepararCampo("Cidade","Char",15,0,"{$mT117['D020_Nome_Cidade']}");

	//-- UF - Estado do sacado 
	//-- Posicoes: 350   351 X(02)
	$cString .= prepararCampo("UF","Char",2,0,"{$mT117['D018_UF']}");

	//-- Instruções boleto
	//-- Posicoes: 352   391  X(40)
	$mT117['T117_Instrucoes']=\hardness\gLimpaAcentos($mT117['T117_Instrucoes']);
	$mT117['T117_Instrucoes']=\hardness\gLimpaTexto($mT117['T117_Instrucoes']); 
	$cString .= prepararCampo("Cidade","Char",40,0,"{$mT117['T117_Instrucoes']}");

	//-- Número de Dias para Protesto
	//-- Posição: 392   393 X(02)
	$cString .= prepararCampo("Dias para Protesto","Char",2,0,"");

	//-- Brancos - Complemento do registro
	//-- Posição: 392   394 X(01)
	$cString .= prepararCampo("Brancos","Char",1,0,"");

	//-- Nº seqüencial do registro no arquivo
	//-- posicao 395   400 9(06)
	$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

	$cString .= _SALTO_LINHA;

	if($mD007['D007_Percentual_Multa'] > 0 && !empty($mD007['D007_Percentual_Multa'])){
		$nSeq++;		
		//-- Identificação do Registro Transação: “5” 14
		//-- Posição: 001 a 001 9(001)
		$cString .= prepararCampo("Identificação do Registro","Num",1,0,"5");
		//-- Tipo de Serviço: “99” (Cobrança de Multa)
		//-- Posição: 002 a 003 X(002)
		$cString .= prepararCampo("Tipo de Serviço","Char",2,0,"99");
		//-- Código de Multa 15
		//-- Posição: 004 a 004 9(001)
		$cString .= prepararCampo("Tipo da Multa","Num",1,0,"2");
		//-- Data de Inicio da Cobrança da Multa 16
		//-- Posição: 005 a 010 9(006)
		$dataMulta = date("Y-m-d", strtotime("+1 day", strtotime($mT117['T117_Data_Vencimento'])));
		$cString .= prepararCampo("Data de Inicio da Cobrança","Date",6,0,$dataMulta);
		//-- Valor/Percentual da Multa 17
		//-- Posição: 011 a 022 9(012)
		$cString .= prepararCampo("Percentual da Multa","Num",12, 2, $mD007['D007_Percentual_Multa']);
		//-- Quantidade de dias para recebimento após vencimento
		//-- Posição: 023 a 025
		$cString .= prepararCampo("Brancos","Num",3,0,"90");
		//-- Complemento do Registro: “Brancos”
		//-- Posição: 026 a 394
		$cString .= prepararCampo("Brancos","Char",369,0,"");
		//-- Número Seqüencial do Registro no Arquivo
		//-- Posição: 395 a 400 9(006)
		$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

		$cString .= _SALTO_LINHA;
	}
	
}

// -- RODAPE
$nSeq++;

//-- Tipo de registro Identificação do registro TRAILER 
//-- posicao 001   001 9(01)
$cString .= prepararCampo("Tipo de Registro","Num",1,0,"9");

//-- BRANCOS - COMPLEMENTO DO REGISTRO
//-- posicao 002   394 X(393) 
$cString .= prepararCampo("Brancos","Char",393,0,"");

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

?>