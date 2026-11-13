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
$mD007['D007_CNPJ'] = trim($mD007['D007_CNPJ']);
$mD007['D007_CNPJ'] = str_replace(array(".","-","/"," "),"",$mD007['D007_CNPJ']);

if (mysql_error()){
   return mysql_error();
}
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
   return "Não informado o número da conta no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Conta'])>8)
{
   return "Número da conta deve ter no máximo 6 caracteres";
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
$cClausula .= " left join T007 on T007_Id=T002_T007_Id ";
$cClausula .= " where $where ";
$cClausula .= " GROUP BY ifnull(T117_Id,T002_Id)";

log("Sql remessa 707: ".$cClausula);
 
$T117_validacao=mysql_query($cClausula);
echo mysql_error();

if (mysql_num_rows($T117_validacao)==0)
{
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
$cArquivo='/tmp/remessa_707.txt';
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
	
	$mT117['T117_Nosso_Numero'] = trim(str_replace(array(".","-","/"," "),"",$mT117['T117_Nosso_Numero']));

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

$cClausula="select * from T035 where T035_Data_Processamento >= '".date("Y-m-d")." 00:00:00' AND T035_Data_Processamento <='".date("Y-m-d")." 23:59:59'";
$cT035 = mysql_query($cClausula);
$numReg = mysql_num_rows($cT035)+1;

//$nomeArquivo="UHF".date("dm").$numReg;
$nomeArquivo="OBX".date("dm").$numReg;


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
/*$cArquivo_Remessa=$Diretorio_Usuario.'/arq_temp/remessa_707.txt';
if (file_exists($cArquivo_Remessa))
{
   unlink("$cArquivo_Remessa");
}
*/

//-- Abre o arquivo
//$cArquivo_Remessa_Aberto = fopen( $cArquivo_Remessa, 'x' );
  
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//-- INICIO DA EXPORTACAO DOS DADOS....																							  //
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$cHoje = date("dmy",time());
$nSeq=1;
$cString="";
//-- Identificação Registro Header 
//-- posicao 001   001 9(01)
$cString  .= prepararCampo("Tipo de Registro","Num",1,0,"0");
 	
//-- Código da Remessa
//-- posicao 002   002  9(01) 
$cString  .= prepararCampo("Operação","Num",1,0,"1");

//-- Identificação Arquivo por Extenso 
//-- posicao 003   009  X(07) 
$cString  .= prepararCampo("Literal de Remessa","Char",7,0,"REMESSA");

//-- Código de Identificação do Serviço
//-- posicao 010   011 9(02)  
$cString  .= prepararCampo("Código do Serviço","Num",2,0,"01");

//-- Identificação do Serviço por Extenso
//-- posicao 012   026 X(15) 
$cString  .= prepararCampo("Literal de serviço","Char",15,0,"COBRANCA");

// //-- Identificação da Empresa no Banco
// //-- posicao 027   046 X(20) 
$cString .= prepararCampo("Identificação empresa","Char",20,0,"$mD007[D007_Codigo_Cedente]");

// //-- Nome por extenso da Empresa
// //-- posicao 047   076 X(30)
$cString  .= prepararCampo("Nome Empresa","Char",30,0,"$mD007[D007_Nome_Conta]");

// //-- Código do Banco
// //-- posicao 077   079 9(03) 
$cString .= prepararCampo("Código banco","Num",3,0,"707");

// //-- Nome por extenso do Banco
// //-- posicao: 080   094 X(15)
$cString  .= prepararCampo("Nome do banco","Char",15,0,"BANCO DAYCOVAL");

// //-- Data da Gravação da fita
// //-- poiscao: 095   100 9(06)
$cString  .= prepararCampo("Data da gravação","Num",6,0,"$cHoje");

//-- Complementação do Registro
//-- posicao: 101   394 X(294)  
$cString  .= prepararCampo("Branco","Char",294,0,"");

//-- Número sequencial do Registro no arquivo
//-- posicao 395   400  9(06) 
$cString  .= prepararCampo("Informar sempre o número:","Num",6,0,"000001");

$cString .= _SALTO_LINHA;

$valorTotalRemessa=0;
$SeqGeracao=0;

while ($mT117 = mysql_fetch_array($cSql_T117))
{

	$valorTotalRemessa+=$mT117['T117_Valor_Total'];

	$SeqGeracao++;


	//utilizar apenas T002_T035_Id
    // $cClausula  = " update T117 set ";
    // $cClausula .= " T117_T035_Id='$nT035_Id'  ";
    // $cClausula .= " where T117_Id='$mT117[T117_Id]' ";
    // mysql_query($cClausula);

    $cClausula  = " update T002 set ";
    $cClausula .= " T002_Flag_Enviar_Banco='N',"; 
    $cClausula .= " T002_T035_Id='$nT035_Id'  ";
    $cClausula .= " where T002_T117_Id='$mT117[T117_Id]' or T002_Id='$mT117[T002_Id]'";
    mysqli_query($cClausula);
    
    //-- NUMERO SEQUENCIAL PARA CADA LINHA DO ARQUIVO (CONFORME LEIAUTE)
	$nSeq++;

    //-- ELIMINA CARACATERES INVALIDOS PARA CFP OU CNPJ
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

	$mT117['T117_Nosso_Numero'] = trim(str_replace(array(".","-","/"," "),"",substr($mT117['T117_Nosso_Numero'],2,8)));
	
	//-- Identificação do Registro Transação
	//-- Posicao 001   001 9(01)
    $cString .= prepararCampo("Tipo de Registro","Num",1,0,"1");
    
	//-- Tipo de Inscrição da Empresa 
	//-- Posição: 002   003  9(02) 
    $cString .= prepararCampo("Código de Inscrição","Num",2,0,"02");

	//-- Número de Inscrição  (CPF/CNPJ)
	//-- posicao 004   017 9(14)
    $cString .= prepararCampo("CPF/CNPJ","Num",14,0,"$mD007[D007_CNPJ]");
  
	//-- Identificação da Empresa no Banco
	//-- posicao: 018   037 X(20)
    $cString  .= prepararCampo("Identificação empresa","Char",20,0,"$mD007[D007_Codigo_Cedente]");

	//-- Identificação do título na empresa
	//-- posicao: 038   062 X(25)
	$cString .= prepararCampo("Identificação do titulo","Char",25,0,"");
	
	// //-- Identificação do título no Banco
	// //-- posicao: 063   070 9(8)
	$cString .= prepararCampo("Nosso Número","Num",8,0,"{$mT117['T117_Nosso_Numero']}");
 
	//-- Brancos
	//-- posicao: 071   083 X(13)
	$cString .= prepararCampo("Branco","Char",13,0,"");
	
	//-- Uso do banco
	//-- posicoes: 071   083 X(24)
	$cString .= prepararCampo("Branco","Char",24,0,"");

	//-- Código da remessa
	//-- posicoes: 108   108 X(01)
	$cString .= prepararCampo("Código remessa","Num",1,0,"6"); 

	//-- Identificação da Ocorrência
	//-- posicoes: 109   110 9(02)    
	$cString .= prepararCampo("Identificação ocorrência","Num",2,0,"01");       
	
	//-- Nº. Documento de cobrança (duplicata, promissória etc.)
	//-- posicoes: 111   120 X(10)
	$cString .= prepararCampo("Número do documento","Num",10,0,"$mT117[T002_Numero_Duplicata]");

	//-- Data de Vencimento do Título
	//-- posicoes: 121   126 9(6)
	$cString .= prepararCampo("Vencimento","Date",6,0,"$mT117[T117_Data_Vencimento]");
	
	//-- Valor nominal do título 
	//-- posicoes: 127   139 9(11) V9(2)
	$cString .= prepararCampo("Valor do Titulo","Num",13,2,"$mT117[T117_Valor_Total]");   

	//-- Código do Banco
	//-- posicoes: 140   142 9(3)
	$cString .= prepararCampo("Numero banco","Num",3,0,"707");  
	
	//-- Agência encarregada da cobrança
	//-- posicoes: 143   146 X(04)
	$cString .= prepararCampo("Branco","Num",4,0,"");   
	
	//-- Dígito de auto-conferência da ag. cobradora
	//-- posicoes: 147   147 9(01)
	$cString .= prepararCampo("Branco","Num",1,0,"");   

	//-- Espécie do título
	//-- posicoes: 148   149 X(02)
	$cString .= prepararCampo("Especie","Char",2,0,"01");

	//-- Identificação do título aceito ou não aceito
	//-- posicoes: 150   150 X(01)
	if ($mT117['T117_Aceite']=='S') { $mT117['T117_Aceite']='A'; } else { $mT117['T117_Aceite']='N'; }
	$cString .= prepararCampo("Aceite","Char",1,0,"$mT117[T117_Aceite]");

	//-- Data da emissão do título
	//-- posicoes: 151   156 9(06)
	$cString .= prepararCampo("Data de Emissão","Date",6,0,"$mT117[T117_Data_Emissao]"); 

	//-- 
	//-- posicoes: 157   158 9(02)
	$cString .= prepararCampo("Branco","Num",2,0,"");

	//-- 
	//-- posicoes: 159   160 9(02)
	$cString .= prepararCampo("Branco","Num",2,0,"");

	//-- Valor de mora por dia de atraso
	//-- posicoes: 161   173 9(11) V9(2)
	$cString .= prepararCampo("Branco","Num",13,2,"");

	//-- Data limite para concessão de desconto
	//-- posicao 174   179 9(06) 
	$cString .= prepararCampo("Desconto até","Date",6,0,"");  

	//-- Valor do desconto a ser concedido
	//-- posicao 180   192 9(11) V9(2)  
	$cString .= prepararCampo("Valor do desconto","Num",13,2,"");

    //-- Uso do Banco
	//-- posicao: 193   205  9(13) 
	$cString .= prepararCampo("Branco","Num",13,2,"");  

	//-- Valor do Abatimento Utilizar este campo somente para comando de instrução de abatimento (nas posições 109 a 110) 
	//-- Para ocorrência 01: Manter zeros - Para ocorrência 04: Informar valor a ser concedido para abatimento.
	//-- Posicao: 206   218 9(11)V9(2) 
	$cString .= prepararCampo("Branco","Num",13,0,"");

	//-- Identificação do tipo de inscrição do sacado
	//-- Posicao: 219   220  9(02) 01=CPF 02=CNPJ
	$cString .= prepararCampo("Código de Inscrição","Num",2,0,"$cCpfCnpjTipo");        
        
	//-- Número de Inscrição do Pagador
	//-- Posicao: 221   234 9(14)
	$cString .= prepararCampo("Número de Inscrição","Num",14,0,"$cCpfCnpj");

	//-- Nome - Nome do Pagador
	//-- Posicao: 235   264 X(30)
	$mT117['D024_Nome_Empresa']=gLimpaAcentosPuro($mT117['D024_Nome_Empresa']);
    $mT117['D024_Nome_Empresa']=gLimpaTexto($mT117['D024_Nome_Empresa']);
	$cString .= prepararCampo("Nome do sacado","Char",30,0,"{$mT117['D024_Nome_Empresa']}");

	//-- Complementação do Registro
	//-- Posicao: 265   274 X(10) 
	$cString .= prepararCampo("Branco","Char",10,0,"");

	//-- Endereço do Pagador
	$mT117['D024_Endereco']=gLimpaAcentosPuro($mT117['D024_Endereco']);
    $mT117['D024_Endereco']=gLimpaTexto($mT117['D024_Endereco']);	
	$mT117['D024_Complemento']=gLimpaAcentosPuro($mT117['D024_Complemento']);
    $mT117['D024_Complemento']=gLimpaTexto($mT117['D024_Complemento']);
    $mT117['D020_Nome_Cidade']=gLimpaAcentosPuro($mT117['D020_Nome_Cidade']);
	$mT117['D020_Nome_Cidade']=gLimpaTexto($mT117['D020_Nome_Cidade']);
	$mT117['D024_Bairro']=gLimpaAcentosPuro($mT117['D024_Bairro']);
    $mT117['D024_Bairro']=gLimpaTexto($mT117['D024_Bairro']);

	//-- Posição: 275   314 X(40)
	$cString .= prepararCampo("Endereço","Char",40,0,"{$mT117['D024_Endereco']}, {$mT117['D024_Numero']} {$mT117['D024_Complemento']}");

	//-- Bairro - Bairro do Pagador
	//-- Posicoes: 315   326 X(12)
	$cString .= prepararCampo("Bairro","Char",12,0,"{$mT117['D024_Bairro']}");

	//-- Código de Endereçamento Postal 
	//-- Posicoes: 327   334 9(08)
	$cString .= prepararCampo("Cep","Num",8,0,"{$mT117['D024_Cep']}");

	//-- Cidade do Pagador 
	//-- Posicoes: 335   349  X(15)
	$cString .= prepararCampo("Cidade","Char",15,0,"$mT117[D020_Nome_Cidade]");

	//-- Estado do Pagador
	//-- Posicoes: 350   351  X(02)
	$cString .= prepararCampo("Estado","Char",2,0,"$mT117[D018_UF]");

	//-- Nome do Sacador Avalista
	//-- Posicoes 352   381	X(30) 
	$cString .= prepararCampo("Sacador/Avalista","Char",30,0,"$mD007[D007_Nome_Conta]");

	//-- Brancos Complemento do registro
	//-- posicao 382   385 X(04) 
	$cString .= prepararCampo("Branco","Char",4,0,"");

	//-- Brancos
	//-- posicoes: 386   391 X(06)
	$cString .= prepararCampo("Branco","Char",6,0,"");

	//-- Quantidade de dias p/ início da ação de protesto
    //-- posicao 392   393  9(02) 
	$cString .= prepararCampo("Branco","Num",2,0,"");	

	//-- Moeda
    //-- posicao 394   394  X(01) 
	$cString .= prepararCampo("Moeda","Num",1,0,"0");	

	//-- Número Sequencial de Registro de Arquivo
	//-- posicao 395   400 9(06)
	$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

	$cString .= _SALTO_LINHA;	

	//-- REGISTRO DA NOTA FISCAL ELETRONICA
	$nSeq++;

	//-- Identificação do Registro
	//-- posicao 001   001 9(01)
	$cString .= prepararCampo("Identificação do registro","Num",1,0,"4");
	
	//-- Número da Nota Fiscal 1
	//-- posicao 002   016 X(15)
	$cString .= prepararCampo("Numero nota fiscal","Num",15,0,"$mT117[T007_Numero_Nota_Fiscal]");

	//-- Valor da Nota Fiscal 1
	//-- posicao 017   029 9(11) V(02)
	$cString .= prepararCampo("Valor nota fiscal","Num",13,2,"$mT117[T007_Valor_Total]");

	//-- Data de Emissão da Nota Fiscal 1
	//-- posicao 030   037 9(8)
	$cString .= prepararCampo("Data emissão","Date",8,0,"$mT117[T007_Data_Emissao]");

	//-- Chave de Acesso DANFE NF 1
	//-- posicao 038   081 9(44)
	$cString .= prepararCampo("Chave de acesso","Num",44,0,"$mT117[T007_Chave_Acesso_Nfe]");

	//-- Brancos
	//-- posicao 082   394 X(313)
	$cString .= prepararCampo("Branco","Char",313,0,"");

	//-- Número Sequencial de Registro de Arquivo
	//-- posicao 395   400 9(06)
	$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

	$cString .= _SALTO_LINHA;

}

// -- RODAPE
$nSeq++;

//-- Identificação Registro Trailler
//-- posicao 001   001 9(01)
$cString .= prepararCampo("Tipo de Registro","Num",1,0,"9");

//-- Complementação do registro
//-- posicoes: 002   394 X(393)
$cString .= prepararCampo("Branco","Char",393,0,"");


//-- NÚMERO SEQUENCIAL DO REGISTRO NO ARQUIVO
//-- posicao 395   400  9(06) 
$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

$cString .= _SALTO_LINHA;

mysql_query("UPDATE D007 SET D007_Numero_Sequencial_Remessa = '{$mD007['D007_Numero_Sequencial_Remessa']}' WHERE D007_Id = '{$mD007['D007_Id']}'");

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
        
        if (strlen($String)>$Tamanho) {
            echo "Erro!! Campo:$Campo - Total de caracteres deste campo: $String (".strlen($String).") maior que o tamanho do campo(".$Tamanho.")";
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
            echo "Erro!! Campo:$Campo - deve ter $Tamanho caracteres ".strlen($String);
            exit;
        }        
        $cRetorno = STR_PAD($String,$Tamanho,"0",STR_PAD_LEFT);
    }        
    
    return $cRetorno;
}

?>