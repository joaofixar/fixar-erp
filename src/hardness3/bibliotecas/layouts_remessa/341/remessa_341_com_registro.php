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

if (mysql_error()){
   return mysql_error();
}
if (strlen($mD007['D007_Numero_Agencia'])<=0)
{
   return "N�o informado o numero da ag�ncia no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Agencia'])>4)
{
   return "Numero da ag�ncia deve ter no m�ximo 4 caracteres";
}
if (strlen($mD007['D007_Numero_Conta'])<=0)
{
   return "N�o informado o numero da conta no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Conta'])>5)
{
   return "Numero da conta deve ter no m�ximo 5 caracteres";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])<=0)
{
   return "N�o informado o digito verificador da conta no cadastro de contas";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])>2)
{
   return "Digito verificador da conta deve ter no m�ximo 2 caracteres";
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

log("Sql remessa 341: ".$cClausula);
 
$T117_validacao=mysql_query($cClausula);
echo mysql_error();

if (mysql_num_rows($T117_validacao)==0)
{
   return "Nenhum titulo foi selecionado para esta conta";
}
$numRegistros=mysql_num_rows($T117_validacao);

// Verificando se possuem boletos com o bosso n�mero repetido
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
$cArquivo='/tmp/remessa_341.txt';
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
    	$mErroCadastro[]="Cliente: $mT117[D024_Nome_Empresa] est� sem CPF. Remessa n�o gerada!"; 
    }
    
    if (empty($mT117['D024_Cnpj']) and $mT117['D024_Flag_Pessoa_Fisica_Juridica']=='J')   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} est� sem CNPJ. Remessa n�o gerada!"; 
    }
    
    if (empty($mT117['D024_Endereco']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} est� sem Endere�o. Remessa n�o gerada!"; 
    }

    if (empty($mT117['D024_Bairro']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} est� sem Bairro. Remessa n�o gerada!"; 
    }

    if (empty($mT117['D024_Cep']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} est� sem Cep. Remessa n�o gerada!"; 
    }
        
    if (empty($mT117['D020_Nome_Cidade']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} est� sem Cidade. Remessa n�o gerada!"; 
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
//-- tipo de Registro - Identifica��o do registro Header 
//-- posicao 001   001 9(01)
$cString  .= prepararCampo("Tipo de Registro","Num",1,0,"0");
 	
//-- operacao - TIPO DE OPERA��O - REMESSA
//-- posicao 002   002  9(01) 
$cString  .= prepararCampo("Opera��o","Num",1,0,"1");

//-- Literal de Remessa -Identifica��o por extenso DO movimento 
//-- posicao 003   009  X(07) 
$cString  .= prepararCampo("Literal de Remessa","Char",7,0,"REMESSA");

//-- C�digo do Servi�o - Identifica��o do tipo de servi�o
//-- posicao 010   011 9(02)  
$cString  .= prepararCampo("C�digo do Servi�o","Num",2,0,"1");

//-- Literal de servi�o Identifica��o por extenso do tipo de servi�o
//-- posicao 012   026 X(15) 
$cString  .= prepararCampo("Literal de servi�o","Char",15,0,"COBRANCA");

//-- ag�ncia - ag�ncia mantenedora da conta
//-- 027   030 9(04)
$cString  .= prepararCampo("Ag�ncia","Num",4,0,"$mD007[D007_Numero_Agencia]");

//-- zeros complemento de registro
//-- posicao 031   032 9(02) 
$cString  .= prepararCampo("Zeros","Num",2,0,"");

//-- conta n�mero da conta corrente da empresa
//-- posicao: 033   037 9(05)
$cString  .= prepararCampo("Conta","Num",5,0,"$mD007[D007_Numero_Conta]");

//-- dac d�gito de auto confer�ncia ag/conta empresa
// poiscao: 038   038 9(01)
$cString  .= prepararCampo("Dac","Num",1,0,"$mD007[D007_Digito_Verificador_Conta]");

//-- Brancos  Complemento do registro 
//-- posicao: 039   046 X(08)  
$cString  .= prepararCampo("Brancos","Char",8,0,"");

//-- Nome da Empresa  - Nome por extenso da "empresa m�e" 
//-- posicao 047   076 X(30)  
$cString  .= prepararCampo("Nome da Empresa","Char",30,0,"$mD007[D007_Nome_Conta]");

//-- C�digo do Banco  N� do banco na c�mara de compensa��o
//-- posicao: 077   079 9(03)   
$cString  .= prepararCampo("C�digo do Banco","Num",3,0,"341");

//-- Nome do Banco - Nome por extenso do banco cobrador
//-- posicao: 080   094  X(15) 
$cString  .= prepararCampo("Nome do Banco","Char",15,0,"BANCO ITAU SA");

//-- Data de Gera��o - Data de gera��o do arquivo 
//-- posicao: 095   100  9(06)
$cString  .= prepararCampo("Nome do Banco","Num",6,0,$cHoje);

//-- Brancos  Complemento do REGISTRO
//-- posicao: 101   394 X(294)
$cString  .= prepararCampo("Brancos","Char",294,0,"");

//-- N�mero Seq�encial do registro no arquivo 
//-- posicao 395   400  9(06) 
$cString  .= prepararCampo("Brancos","Num",6,0,$nSeq);

$cString .= _SALTO_LINHA;

$valorTotalRemessa=0;

while ($mT117 = mysql_fetch_array($cSql_T117))
{

	$valorTotalRemessa+=$mT117['T117_Valor_Total'];

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
    
    if ($mT117['D024_Flag_Protestar'] != 'N') {
		$mT117['Instrucao_Protesto_1']=$mD007['D007_Instrucao_Protesto_1'];
		$mT117['Instrucao_Protesto_2']=$mD007['D007_Instrucao_Protesto_2'];
		//$mT117[Instrucao_Cobranca_2]=$mD007[D007_Instrucao_Cobranca_2];  Instr. Cobran�a 1 sera implementado no futuro
    }
	$mT117['Instrucao_Cobranca_2']=$mD007['D007_Instrucao_Cobranca_2'];

	//-- Tipo de registro - Identifica��o do registro Transa��o
	//-- Posicao 001   001 9(01)
    $cString .= prepararCampo("Tipo de Registro","Num",1,0,"1");
    
	//-- C�digo de Inscri��o tipo de inscri��o da empresa
	//-- Posi��o: 002   003  9(02) 
    $cString .= prepararCampo("C�digo de Inscri��o","Num",2,0,"$cCpfCnpjTipo");

	//-- N�mero de Inscri��o N� de inscri��o da empresa (CPF/CNPJ)
	//-- posicao 004   017 9(14)
    $cString .= prepararCampo("CPF/CNPJ","Num",14,0,"$cCpfCnpj");
  
	//-- ag�ncia mantenedora da conta
	//-- posicao: 018   021 9(04)
    $cString .= prepararCampo("Agencia","Num",4,0,"$mD007[D007_Numero_Agencia]");
    
	//-- zeros complemento de registro
	//-- posicao: 022   023 9(02)
    $cString .= prepararCampo("Zeros","Num",2,0,"");
    
	//-- conta - n�mero da conta corrente da empresa
	//-- posicao: 024   028 9(05)
    $cString .= prepararCampo("Conta","Num",5,0,"$mD007[D007_Numero_Conta]");

	//-- dac - d�gito de auto confer�ncia ag/conta empresa
	//-- posicao: 029   029 9(01)
    $cString .= prepararCampo("Digito Conta","Num",1,0,"$mD007[D007_Digito_Verificador_Conta]");
    
	//-- Brancos - Complemento de registro
	//-- posicoes: 030   033 X(04)
    $cString .= prepararCampo("Brancos","Char",4,0,"");
    
	//-- instru��o/alega��o - cod.instru��o/alega��o a ser cancelada
	//-- 034   037 9(04) - 
	//-- NOTA 27	
	//-- Deve ser preenchido na remessa somente quando utilizados, na posicao 109-110, os codigos de ocorrencia 35 -  
	//-- Cancelamento de Instrucao e 38 - Cedente n�o concorda com alegacao do sacado. Para os demais codigos de ocorrencia 
	//-- este campo devera ser preenchido com zeros. 
	//-- Obs.: No arquivo retorno sera informado o mesmo codigo da instrucao cancelada, e para o cancelamento de alegacao de sacado 
	//-- nao ha retorno da informacao.	
    $cString .= prepararCampo("instru��o/alega��o","Num",4,0,"");
        
	//-- Uso da Empresa - Identifica��o do T�tulo na empresa
	//-- posicao: 038   062  X(25)
    $cString .= prepararCampo("Identifica��o do T�tulo na empresa","Char",25,0,"$mT117[T117_Id]");  
        
	//-- Nosso N�mero  - Identifica��o do T�tulo no Banco
	//-- posicao 063   070 9(08) ver *nota3
    $cString .= prepararCampo("Nosso Numero","Num",8,0,"$mT117[T117_Nosso_Numero]");
        
	//-- Qtde de Moeda  Quantidade de moeda vari�vel
	//-- posicao: 071   083 9(08)V9(5) 
	//-- Este campo dever� ser preenchido com zeros caso a moeda seja o Real.     
    $cString .= prepararCampo("Qtde Moeda","Num",13,5,"");        
        
	//-- N� da Carteira  N�mero da Carteira no Banco
	//-- posicao: 084   086 9(03) Nota 5 
    $cString .= prepararCampo("Carteira","Num",3,0,"$mD007[D007_Tipo_Carteira_Banco_Brasil]");        
        
	//-- USO DO BANCO - IDENTIFICA��O DA OPERA��O NO BANCO
	//-- posi��o: 087   107  X(21)
	$cString .= prepararCampo("Operacao no Banco","Char",21,0,"");

	//-- Carteira - C�digo da Carteira 
	//-- posicao 108   108  X(01) *Nota 5  
	$cString .= prepararCampo("Carteira","Char",1,0,"I");        
        
	//-- C�d. de Ocorr�ncia - Identifica��o da Ocorr�ncia 
	//-- posi��o: 109   110 9(02) *Nota 6
	$cString .= prepararCampo("C�d. de Ocorr�ncia","Num",2,0,"01");        
        
	//-- n� do documento - N� do documento de cobran�a (Dupl.,NP etc.)
	//-- posicao: 111   120  X(10) *nota 18
	$cString .= prepararCampo("n� do documento","Char",10,0,"$mT117[T002_Numero_Duplicata]");        

	//-- Vencimento - Data de vencimento do t�tulo 
	//-- posicao: 121   126  9(06) *NOTA 7
	$cString .= prepararCampo("Vencimento","Date",6,0,"$mT117[T117_Data_Vencimento]");        

	//-- Valor do T�tulo  - Valor Nominal do T�TULO
	//-- posicao: 127   139 9(11)V9(2) *Nota 8         
	$cString .= prepararCampo("Valor do Titulo","Num",13,2,"$mT117[T117_Valor_Total]");        
        
	//-- C�digo do Banco  - N� do Banco na c�mara de compensa��o 
	//-- Posicao 140   142  9(03) 
	$cString .= prepararCampo("C�digo do Banco","Num",3,0,"341");        
        
	//-- AG�NCIA COBRADORA - AG�NCIA ONDE O T�TULO SER� COBRADO
	//-- posicao: 143   147 9(05) *nota 9
	$cString .= prepararCampo("AG�NCIA COBRADORA","Num",5,0,"");        
        
	//-- Esp�cie - Esp�cie do T�TULO
	//-- posicao: 148   149  X(02) *Nota 10 
	$cString .= prepararCampo("Especie","Char",2,0,"01");        
        
	//-- Aceite  - Identifica��o de t�tulo aceito ou n�o ACEITO
	//-- posi��o: 150   150 X(01) A=ACEITE N=N�o ACEITE
	if ($mT117['T117_Aceite']=='S') { $mT117['T117_Aceite']='A'; } else { $mT117['T117_Aceite']='N'; }
	$cString .= prepararCampo("Aceite","Char",1,0,"$mT117[T117_Aceite]");
        
	//-- Data de Emiss�o - Data da emiss�o do t�tulo
	//-- Posicao: 151   156 9(06) *Nota 31
	$cString .= prepararCampo("Data de Emiss�o","Date",6,0,"$mT117[T117_Data_Emissao]");        
        
	//-- Instru��o 1  - 1� instru��o de cobran�a 
	//-- posicao 157   158  X(02) *Nota 11
	$cString .= prepararCampo("Instru��o 1","Char",2,0,"{$mT117['Instrucao_Protesto_1']}");        
        
	//-- Instru��o 2  2� instru��o de cobran�a
	//-- posicao 159   160 X(02) *Nota 11
	$cString .= prepararCampo("Instru��o 2","Char",2,0,"$mT117[Instrucao_Cobranca_2]");        

	//-- Juros de 1 dia - Valor de Mora por dia de atraso
	//-- posicao: 161   173  9(11)V9(2) *Nota 12 
	$jurosDia = round((($mT117['T117_Percentual_Juros_Dia']/30)/100)*$mT117['T002_Valor_Total'],2);
	$cString .= prepararCampo("Juros de 1 dia","Num",13,2,$jurosDia);
        
	//-- Desconto at�  - Data limite para concess�o de desconto
	//-- posicao: 174   179  9(06) DDMMAA 
	$cString .= prepararCampo("Desconto at�","Date",6,0,"");        

	//-- Valor do desconto - Valor do desconto a ser concedido
	//-- posicao: 180   192  9(11)V9(2) *Nota 13 
	$cString .= prepararCampo("Valor do desconto","Num",13,2,"");        
        
	//-- Valor do I.O.F.  - Valor do I.O.F. recolhido p/ Notas Seguro
	//-- posicao: 193   205  9(11)V((2) *Nota 14 
	$cString .= prepararCampo("Valor do IOF","Num",13,2,"");               

	//-- Abatimento - Valor do abatimento a ser concedido
	//-- Posicao: 206   218 9(11)V9(2) *Nota 13 
	$cString .= prepararCampo("Abatimento","Num",13,2,"");        
        
	//-- C�digo de Inscri��o Identifica��o do tipo de inscri��o/sacado
	//-- Posicao: 219   220  9(02) 01=CPF 02=CNPJ
	$cString .= prepararCampo("C�digo de Inscri��o","Num",2,0,"$cCpfCnpjTipo");        
        
	//-- N�mero de Inscri��o - N� de inscri��o do Sacado  (CPF/CNPJ)
	//-- Posicao: 221   234 9(14)
	$cString .= prepararCampo("N�mero de Inscri��o","Num",14,0,"$cCpfCnpj");

	//-- Nome - Nome do sacado
	//-- Posicao: 235   264 X(30) *NOTA 15
	$mT117['D024_Nome_Empresa']=\hardness\gLimpaAcentos($mT117['D024_Nome_Empresa']);
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaTexto($mT117['D024_Nome_Empresa']);
	$cString .= prepararCampo("Nome do sacado","Char",30,0,"{$mT117['D024_Nome_Empresa']}");

	//-- Brancos - Complemento de registro
	//-- Posicao: 265   274  X(10) *Nota 15 
	$cString .= prepararCampo("Brancos","Char",10,0,"");

	//-- Logradouro - Rua, N�mero e complemento do Sacado
	//-- Posi��o: 275   314 X(40)
	$mT117['D024_Endereco']=\hardness\gLimpaAcentos($mT117['D024_Endereco']);
    $mT117['D024_Endereco']=\hardness\gLimpaTexto($mT117['D024_Endereco']);
    $mT117['D024_Complemento']=\hardness\gLimpaAcentos($mT117['D024_Complemento']);
    $mT117['D024_Complemento']=\hardness\gLimpaTexto($mT117['D024_Complemento']);
    $mT117['D020_Nome_Cidade']=\hardness\gLimpaAcentos($mT117['D020_Nome_Cidade']);
	$mT117['D020_Nome_Cidade']=\hardness\gLimpaTexto($mT117['D020_Nome_Cidade']);
	$mT117['D024_Bairro']=\hardness\gLimpaAcentos($mT117['D024_Bairro']);
    $mT117['D024_Bairro']=\hardness\gLimpaTexto($mT117['D024_Bairro']);
	$cString .= prepararCampo("Logradouro","Char",40,0,"{$mT117['D024_Endereco']}, {$mT117['D024_Numero']} {$mT117['D024_Complemento']}");
        
	//-- Bairro - Bairro do Sacado
	//-- Posicoes: 315   326 X(12)
	$cString .= prepararCampo("Bairro","Char",12,0,"{$mT117['D024_Bairro']}");

	//-- CEP - CEP do sacado 
	//-- Posicoes: 327   334 9(08)
	$cString .= prepararCampo("Cep","Num",8,0,"{$mT117['D024_Cep']}");

	//-- Cidade - Cidade do Sacado 
	//-- Posicoes: 335   349  X(15)
	$cString .= prepararCampo("Cidade","Char",15,0,"$mT117[D020_Nome_Cidade]");

	//-- Estado - UF do Sacado
	//-- Posicoes: 350   351  X(02)
	$cString .= prepararCampo("Estado","Char",2,0,"$mT117[D018_UF]");

	//-- Sacador/Avalista Nome do Sacador ou Avalista
	//-- Posicoes 352   381	X(30) *Nota 16
	$cString .= prepararCampo("Sacador/Avalista","Char",30,0,"");

	//-- Brancos Complemento do registro
	//-- posicao 382   385 X(04) 
	$cString .= prepararCampo("Branco","Char",4,0,"");

	//-- Data de Mora  
	//-- posicao 386   391 9(06) DDMMAA  
	$cString .= prepararCampo("Data de Mora","Date",6,0,"");

	//-- Prazo - Quantidade de dias
	// posicao: 392   393  9(02) *NOTA 11 (A)
	$cString .= prepararCampo("Prazo","Num",2,0,"$mT117[Instrucao_Protesto_2]");

	//-- BRANCOS COMPLEMENTo DO REGISTRO
	//-- posicao 394   394  X(01)
	$cString .= prepararCampo("Brancos","Char",1,0,"");

	//-- N�mero Seq�encial N� seq�encial do registro no arquivo
	//-- posicao 395   400 9(06)
	$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

	$cString .= _SALTO_LINHA;

	// Caso possua Multa adiciona linha adicional de instru��es de juros, conforme manual itau.
	if ($mT117['T117_Percentual_Multa'] > 0 && $mD007['D007_Flag_Enviar_Multa_Remessa'] == 'S'){

	    //-- NUMERO SEQUENCIAL PARA CADA LINHA DO ARQUIVO (CONFORME LEIAUTE)
	    $nSeq++;

		//-- Tipo de registro - Identifica��o do registro Transa��o
		//-- Posicao 001   001 9(01)
	    $cString .= prepararCampo("Tipo de Registro","Num",1,0,"2");

		//-- C�digo multa - 0 - Não registra multa | 1 - Valor em reais (Fixo) | 2 - Valor em percentual com duas casas decimais conforme estrutura do campo
		//-- Posicao 002   002 9(01) *NOTA 35
		$codigoMulta = ($mD007['D007_Flag_Tipo_Multa'] == 'V') ? 1 : 2;

	    $cString .= prepararCampo("Codigo multa","Num",1,0,$codigoMulta);

		//-- Data da multa - Data limite para pagamento sem multa
		//-- Vamos utilizar o vencimento como padr�o		
		//-- posicao: 003   010  9(08) *NOTA 35
		$cString .= prepararCampo("Vencimento","Date",8,0,"$mT117[T117_Data_Vencimento]");        

		//-- Multa - Valor/Percentual a ser aplicado
		//-- Vamos utilizar valor em reais como padr�o
		//-- posicao: 011   023  9(11)V9(2) *Nota 35 
		//-- $multa = round(($mT117['T117_Percentual_Multa']/100)*$mT117['T002_Valor_Total'],2);

		if($codigoMulta == 1){
			$valorMultaPercentual = round(($mT117['T117_Percentual_Multa']/100)*$mT117['T117_Valor_Total'],2);
		} else {
			$valorMultaPercentual = $mT117['T117_Percentual_Multa'];
		}

		$cString .= prepararCampo("Multa","Num",13,2,$valorMultaPercentual);
	        
		//-- BRANCOS COMPLEMENTo DO REGISTRO
		//-- posicao 394   394  X(01)
		$cString .= prepararCampo("Brancos","Char",371,0,"");

		//-- N�mero Seq�encial N� seq�encial do registro no arquivo
		//-- posicao 395   400 9(06)
		$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

		$cString .= _SALTO_LINHA;
	} 
}

// -- RODAPE
$nSeq++;

//-- Tipo de registro Identifica��o do registro TRAILER 
//-- posicao 001   001 9(01)
$cString .= prepararCampo("Tipo de Registro","Num",1,0,"9");

//-- BRANCOS - COMPLEMENTO DO REGISTRO
//-- posicao 002   394 X(393) 
$cString .= prepararCampo("Brancos","Char",393,0,"");

//-- N�MERO SEQ�ENCIAL DO REGISTRO NO ARQUIVO
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
        echo "Campo:$Campo N�o foi definido tipo<br>";        
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
            echo "Erro!! Campo:$Campo - deve ter $Tamanho caracteres ".strlen($String);
            exit;
        }        
        $cRetorno = STR_PAD($String,$Tamanho,"0",STR_PAD_LEFT);
    }        
    
    return $cRetorno;
}

?>