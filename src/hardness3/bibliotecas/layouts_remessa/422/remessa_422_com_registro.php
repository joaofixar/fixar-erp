<?php
/*
Programador: Evantoni
Data: 01/08/2012
*/
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
if (strlen($mD007['D007_Numero_Agencia'])>5)
{
   return "Numero da agência deve ter no máximo 4 caracteres";
}
if (strlen($mD007['D007_Numero_Conta'])<=0)
{
   return "Não informado o número da conta no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Conta'])>9)
{
   return "Número da conta deve ter no máximo 5 caracteres";
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
$cClausula .= " where $where ";
$cClausula .= " GROUP BY ifnull(T117_Id,T002_Id)";

log("Sql remessa 422: ".$cClausula);
 
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
$cArquivo='/tmp/remessa_422.txt';
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
/*$cArquivo_Remessa=$Diretorio_Usuario.'/arq_temp/remessa_422.txt';
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
 	
//-- Identificação Arquivo Remessa
//-- posicao 002   002  9(01) 
$cString  .= prepararCampo("Operação","Num",1,0,"1");

//-- Identificação Arquivo por Extenso 
//-- posicao 003   009  X(07) 
$cString  .= prepararCampo("Literal de Remessa","Char",7,0,"REMESSA");

//-- Código de Identificação do Serviço
//-- posicao 010   011 9(02)  
$cString  .= prepararCampo("Código do Serviço","Num",2,0,"01");

//-- Identificação do Serviço por Extenso
//-- posicao 012   019 X(15) 
$cString  .= prepararCampo("Literal de serviço","Char",8,0,"COBRANCA");

// //-- Campo sem preenchimento
// //-- posicao 020   026 X(15) 
$cString .= prepararCampo("Branco","Char",7,0,"");

//-- Identificação da Empresa no Banco
//-- 027   040 9(04)
$cString  .= prepararCampo("Agência","Num",5,0,"$mD007[D007_Numero_Agencia]$mD007[D007_Digito_Verificador_Agencia]");
$cString  .= prepararCampo("Conta","Num",9,0,"$mD007[D007_Numero_Conta]$mD007[D007_Digito_Verificador_Conta]");

//-- Campos sem preenchimento
//-- posicao 041   046 9(02) 
$cString .= prepararCampo("Branco","Char",6,0,"");

// //-- Nome da empresa cedente
// //-- posicao: 047   076 9(05)
$mD007['D007_Nome_Conta']=gLimpaAcentosPuro($mD007['D007_Nome_Conta']);
$mD007['D007_Nome_Conta']=gLimpaTexto($mD007['D007_Nome_Conta']);
$cString  .= prepararCampo("Nome da Empresa","Char",30,0,"$mD007[D007_Nome_Conta]");

//-- Código de Identificação do Banco
// poiscao: 077   079 9(01)
$cString  .= prepararCampo("Código do Banco","Num",3,0,"422");

//-- Nome do Banco por Extenso
//-- posicao: 080   090 X(08)  
$cString  .= prepararCampo("Nome do Banco","Char",11,0,"BANCO SAFRA");

//--Campo sem Preenchimento 
//-- posicao 091   094 X(30)  
$cString .= prepararCampo("Branco","Char",4,0,"");

//-- Data da Geração do Arquivo Remessa 
//-- posicao: 095   100  9(06)
$cString  .= prepararCampo("Nome do Banco","Num",6,0,$cHoje);

//--Campo sem Preenchimento 
//-- posicao 101   391 X(30)  
$cString .= prepararCampo("Branco","Char",291,0,"");

//-- Número Sequencial de Geração do Arquivo
//-- posicao 392   394  9(06) 
$mD007['D007_Numero_Sequencial_Remessa']++;
$cString  .= prepararCampo("Nº Sequencial da Remessa","Num",3,0,"{$mD007['D007_Numero_Sequencial_Remessa']}"); 

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

	$mT117['T117_Nosso_Numero'] = trim(str_replace(array(".","-","/"," "),"",$mT117['T117_Nosso_Numero']));
	
	//-- Identificação do Registro Transação
	//-- Posicao 001   001 9(01)
    $cString .= prepararCampo("Tipo de Registro","Num",1,0,"1");
    
	//-- Tipo de Inscrição da Empresa 
	//-- Posição: 002   003  9(02) 
    $cString .= prepararCampo("Código de Inscrição","Num",2,0,"$cCpfCnpjTipo");

	//-- Número de Inscrição  (CPF/CNPJ)
	//-- posicao 004   017 9(14)
    $cString .= prepararCampo("CPF/CNPJ","Num",14,0,"$mD007[D007_CNPJ]");
  
	//-- Identificação da Empresa no Banco
	//-- posicao: 018   031 9(04)
    $cString  .= prepararCampo("Agência","Num",5,0,"$mD007[D007_Numero_Agencia]$mD007[D007_Digito_Verificador_Agencia]");
	$cString  .= prepararCampo("Conta","Num",9,0,"$mD007[D007_Numero_Conta]$mD007[D007_Digito_Verificador_Conta]");

	//-- Campo sem Preenchimento
	//-- posicao: 032   037 9(02)
	$cString .= prepararCampo("Branco","Char",6,0,"");
	
	// //-- Uso Exclusivo da Empresa
	// //-- posicao: 038   062 9(05)
	$cString .= prepararCampo("Branco","Char",25,0,"{$mT117['T117_Id']}");
 
	//-- Identificação do Título no Banco
	//-- posicao: 063   071 9(05)
	$cString .= prepararCampo("Nosso Número","Num",9,0,"{$mT117['T117_Nosso_Numero']}");
	
	//-- Campo sem Preenchimento
	//-- posicoes: 072   101 X(04)
	$cString .= prepararCampo("Branco","Char",30,0,"");

	//-- Código IOF Operações de Seguro
	//-- posicoes: 102   102 X(04)
	$cString .= prepararCampo("Valor do IOF","Num",1,0,"0"); 

	//-- Identificação do Tipo de Moeda
	//-- posicoes: 103   104 X(04)    
	$cString .= prepararCampo("Qtde Moeda","Num",2,0,"00");       
	
	//-- Campo sem Preenchimento
	//-- posicoes: 105   105 X(04)
	$cString .= prepararCampo("Branco","Char",1,0,"");

	//-- Terceira Instrução de Cobrança 
	//-- posicoes: 106   107 X(04)
	$cString .= prepararCampo("Instrução 3","Num",2,0,"");

	//-- Identificação Tipo de Carteira
	//-- posicoes: 108   108 X(04)
	$cString .= prepararCampo("Carteira","Num",1,0,"1");  
	
	//-- Identificação do Tipo de Ocorrência 
	//-- posicoes: 109   110 X(04)
	$cString .= prepararCampo("Cód. de Ocorrência","Num",2,0,"01");   

	//-- Identificação do Título na Empresa
	//-- posicoes: 111   120 X(04)
	$cString .= prepararCampo("Número do Título","Num",10,0,"$mT117[T002_Numero_Duplicata]");

	//-- Data de Vencimento do Título
	//-- posicoes: 121   126 X(04)
	$cString .= prepararCampo("Vencimento","Date",6,0,"$mT117[T117_Data_Vencimento]");
	
	//-- Valor Nominal do Título
	//-- posicoes: 127   139 X(04)
	$cString .= prepararCampo("Valor do Titulo","Num",13,2,"$mT117[T117_Valor_Total]");   

	//-- Código do Banco encarregado da
	//-- posicoes: 140   142 X(04)
	$cString .= prepararCampo("Código do Banco","Num",3,0,"422");
	
	//-- Agência Encarregada da Cobrança
	//-- posicoes: 143   147 X(04)
	$cString .= prepararCampo("AGÊNCIA COBRADORA","Num",5,0,""); 

	//-- Espécie do Título
	//-- posicoes: 148   149 X(04)
	$cString .= prepararCampo("Especie","Char",2,0,"01"); 

	//-- Identificação do Aceite do Título
	//-- posicoes: 150   150 X(04)
	if ($mT117['T117_Aceite']=='S') { $mT117['T117_Aceite']='A'; } else { $mT117['T117_Aceite']='N'; }
	$cString .= prepararCampo("Aceite","Char",1,0,"$mT117[T117_Aceite]");

	//-- Data de Emissão do Título
	//-- posicoes: 151   156 X(04)
	$cString .= prepararCampo("Data de Emissão","Date",6,0,"$mT117[T117_Data_Emissao]"); 

	//-- Primeira Instrução de Cobrança
	//-- posicoes: 157   158 X(04)
	$cString .= prepararCampo("Instrução 1","Char",2,0,"{$mD007['D007_Instrucao_Protesto_1']}");

	//-- Segunda Instrução de Cobrança
	//-- posicao 159   160 X(02) *Nota 11
	$cString .= prepararCampo("Instrução 2","Char",2,0,"{$mD007['D007_Instrucao_Protesto_2']}");

	//-- Juros de Mora por dia de atraso
	//-- posicao 161   173 X(02) *Nota 12 
	$jurosDia = round((($mT117['T117_Percentual_Juros_Dia']/30)/100)*$mT117['T117_Valor_Total'],2);
	$cString .= prepararCampo("Juros de 1 dia","Num",13,2,$jurosDia);

	//-- Data Limite para Desconto
	//-- posicao: 174   179  9(06) DDMMAA 
	$cString .= prepararCampo("Desconto até","Date",6,0,"");        

	//-- Valor do Desconto Concedido
	//-- posicao: 180   192  9(11)V9(2) *Nota 13 
	$cString .= prepararCampo("Valor do desconto","Num",13,2,"");

    //-- Valor do IOF Operações de Seguro
	//-- posicao: 193   205  9(11)V9(2) 
	$cString .= prepararCampo("Valor do IOF","Num",13,2,"0");  

	//-- Valor do Abatimento Concedido ou Cancelado / Multa
	//-- Posicao: 206   218 9(11)V9(2) *Nota 13 
	if ($mD007['D007_Percentual_Multa']>0) {
		//-- Data de Inicio da Cobrança da Multa 16
		//-- Posição: 206 a 211 9(006)
		$dataMulta = date("Y-m-d", strtotime("+1 day", strtotime($mT117['T117_Data_Vencimento'])));
		$cString .= prepararCampo("Data de Inicio da Cobrança","Date",6,0,$dataMulta);
		//-- Valor/Percentual da Multa 17
		//-- Posição: 212 a 215 9(004)
		$cString .= prepararCampo("Percentual da Multa","Num",4, 2, $mD007['D007_Percentual_Multa']);
		//-- Complemento do Registro: “Brancos”
		//-- Posição: 216 a 218
		$cString .= prepararCampo("Brancos","Num",3,0,"");
	} else {
		//-- Data de Inicio da Cobrança da Multa 16
		//-- Posição: 206 a 211 9(006)
		$cString .= prepararCampo("Data de Inicio da Cobrança","Date",6,0,"");
		//-- Valor/Percentual da Multa 17
		//-- Posição: 212 a 215 9(004)
		$cString .= prepararCampo("Percentual da Multa","Num",4, 2, "");
		//-- Complemento do Registro: “Brancos”
		//-- Posição: 216 a 218
		$cString .= prepararCampo("Brancos","Num",3,0,"");
	}

	//-- Código de Inscrição do Pagador
	//-- Posicao: 219   220  9(02) 01=CPF 02=CNPJ
	$cString .= prepararCampo("Código de Inscrição","Num",2,0,"$cCpfCnpjTipo");        
        
	//-- Número de Inscrição do Pagador
	//-- Posicao: 221   234 9(14)
	$cString .= prepararCampo("Número de Inscrição","Num",14,0,"$cCpfCnpj");

	//-- Nome - Nome do Pagadpr
	//-- Posicao: 235   274 X(30) *NOTA 15
	$mT117['D024_Nome_Empresa']=gLimpaAcentosPuro($mT117['D024_Nome_Empresa']);
    $mT117['D024_Nome_Empresa']=gLimpaTexto($mT117['D024_Nome_Empresa']);
	$cString .= prepararCampo("Nome do sacado","Char",40,0,"{$mT117['D024_Nome_Empresa']}");


	//-- Endereço do Pagador
	//-- Posição: 275   314 X(40)
	$mT117['D024_Endereco']=gLimpaAcentosPuro($mT117['D024_Endereco']);
    $mT117['D024_Endereco']=gLimpaTexto($mT117['D024_Endereco']);	
	$mT117['D024_Complemento']=gLimpaAcentosPuro($mT117['D024_Complemento']);
    $mT117['D024_Complemento']=gLimpaTexto($mT117['D024_Complemento']);
    $mT117['D020_Nome_Cidade']=gLimpaAcentosPuro($mT117['D020_Nome_Cidade']);
	$mT117['D020_Nome_Cidade']=gLimpaTexto($mT117['D020_Nome_Cidade']);
	$mT117['D024_Bairro']=gLimpaAcentosPuro($mT117['D024_Bairro']);
    $mT117['D024_Bairro']=gLimpaTexto($mT117['D024_Bairro']);
	$cString .= prepararCampo("Endereço","Char",40,0,"{$mT117['D024_Endereco']}, {$mT117['D024_Numero']} {$mT117['D024_Complemento']}");

	//-- Bairro - Bairro do Pagador
	//-- Posicoes: 315   324 X(12)
	$cString .= prepararCampo("Bairro","Char",10,0,"{$mT117['D024_Bairro']}");

	//-- Campo sem Preenchimento
	//-- posicoes: 325   326 X(04)
	$cString .= prepararCampo("Branco","Char",2,0,"");

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
	//-- Posicoes 352   381	X(30) *Nota 16
	$cString .= prepararCampo("Sacador/Avalista","Char",30,0,"");

	//-- Brancos Complemento do registro
	//-- posicao 382   388 X(04) 
	$cString .= prepararCampo("Branco","Char",7,0,"");

	//-- Banco Emitente do Boleto
	//-- posicoes: 389   391 X(04)
	$cString .= prepararCampo("Código do Banco","Num",3,0,"422");

	//-- Número Sequencial Geração do Arquivo Remessa
    //-- posicao 392   394  9(06) 
    $cString  .= prepararCampo("Nº Sequencial da Remessa","Num",3,0,"{$mD007['D007_Numero_Sequencial_Remessa']}"); 
	
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

//-- Campo sem Preenchimento
//-- posicoes: 002   368 X(04)
$cString .= prepararCampo("Branco","Char",367,0,"");

//-- Quantidade de Títulos Existentes no Arquivo
//-- posicoes: 369   376 X(04)37
$cString .= prepararCampo("Quantidade de títulos","Num",8,0,"$SeqGeracao");

//-- Valor Total dos Títulos
//-- posicoes: 377   391 X(04)
$cString .= prepararCampo("Valor total","Num",15,2,"$valorTotalRemessa");   

// // -- Número Sequencial Geração do Arquivo Remessa
// // -- posicoes: 392   394 X(04)
$cString  .= prepararCampo("Nº Sequencial da Remessa","Num",3,0,"{$mD007['D007_Numero_Sequencial_Remessa']}"); 

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