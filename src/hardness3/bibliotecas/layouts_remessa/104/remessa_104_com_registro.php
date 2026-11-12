<?php
namespace hardness;

require_once __DIR__ . '/beneficiario_104.php';
/*
Programador: Evantoni
Data: 01/08/2012
*/


DEFINE("_SALTO_LINHA",CHR(13).CHR(10));


$cClausula  = " select ";
$cClausula .= " * ";
$cClausula .= " from D007 ";
$cClausula .= " left join C004 on C004_Id=D007_C004_Id ";
$cClausula .= " where D007_Id='{$D007_Id}' ";
$D007 = mysql_query($cClausula);
$mD007=mysql_fetch_array($D007);
try {
    $beneficiarioCaixa = b104_separar_beneficiario($mD007['D007_Codigo_Cedente']);
} catch (\InvalidArgumentException $e) {
    return $e->getMessage();
}
$mD007['D007_CNPJ'] = trim($mD007['D007_CNPJ']);
$mD007['D007_CNPJ'] = str_replace(array(".","-","/"," "),"",$mD007['D007_CNPJ']);

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
$T117_validacao=mysql_query($cClausula);
echo mysql_error();

if (mysql_num_rows($T117_validacao)==0)
{
   return "Nenhum titulo foi selecionado para esta conta";
}
$numRegistros=mysql_num_rows($T117_validacao);


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

/* $cClausula="select T035_Id from T035 where T035_D007_Id = '$D007_Id' and T035_Numero_Titulos>0";
$cT035 = mysql_query($cClausula);
$nSeqArquivo = mysql_num_rows($cT035)+1; */

$nSeqArquivo = $mD007['D007_Numero_Sequencial_Remessa'] + 1;

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
$cString  .= prepararCampo("Código do Registro","Num",1,0,"0");
 	
//-- operacao - TIPO DE OPERAÇÃO - REMESSA
//-- posicao 002   002  9(01) 
$cString  .= prepararCampo("Código da Remessa","Num",1,0,"1");

//-- Literal de Remessa -Identificação por extenso DO movimento 
//-- posicao 003   009  X(07) 
$cString  .= prepararCampo("Literal de Remessa","Char",7,0,"REMESSA");

//-- Código do Serviço - Identificação do tipo de serviço
//-- posicao 010   011 9(02)  
$cString  .= prepararCampo("Código do Serviço","Num",2,0,"01");

//-- Literal de serviço Identificação por extenso do tipo de serviço
//-- posicao 012   026 X(15) 
$cString  .= prepararCampo("Literal de serviço","Char",15,0,"COBRANCA");

//-- agência - agência mantenedora da conta
//-- 027   030 9(04)
$cString  .= prepararCampo("Agência","Num",4,0,"$mD007[D007_Numero_Agencia]");

//-- Codigo do Beneficiario sem DV, versao 007 (NE004 A2/B)
//-- posicao: 31   037 9(07)
$cString  .= prepararCampo("Codigo do Beneficiario","Num",7,0,$beneficiarioCaixa['cnab']);

//-- zeros complemento de registro
//-- posicao 038   046 9(02) 
$cString  .= prepararCampo("Brancos","Char",9,0,"");

//-- Nome da Empresa  - Nome por extenso da "empresa mãe" 
//-- posicao 047   076 X(30)  
$cString  .= prepararCampo("Nome da Empresa","Char",30,0,"$mD007[D007_Nome_Conta]");

//-- Código do Banco  Nº do banco na câmara de compensação
//-- posicao: 077   079 9(03)   
$cString  .= prepararCampo("Código do Banco","Num",3,0,"104");

//-- Nome do Banco - Nome por extenso do banco cobrador
//-- posicao: 080   094  X(15) 
$cString  .= prepararCampo("Nome do Banco","Char",15,0,"C ECON FEDERAL");

//-- Data de Geração - Data de geração do arquivo 
//-- posicao: 095   100  9(06)
$cString  .= prepararCampo("Data de Geracao","Num",6,0,$cHoje);

//-- versão do layout
//-- posicao: 101   103  9(06)
/*if(strlen($mD007[D007_Codigo_Cedente])>6){
	$cString  .= prepararCampo("Versão layout","Num",3,0,"007");
} else {
	$cString  .= prepararCampo("Versão layout","char",3,0,"");
}*/
// Em homologação com o banco, solicitou sempre enviar a versao do layout = 007
$cString  .= prepararCampo("Versão layout","Num",3,0,"007");

//-- Brancos  Complemento do registro 
//-- posicao: 104   389 X(08)  
$cString  .= prepararCampo("Brancos","Char",286,0,"");

//-- Número Seqüencial do registro no arquivo 
//-- posicao 390   394  9(05) 
$cString  .= prepararCampo("Numero sequencial arquivo","Num",5,0,$nSeqArquivo);

//-- Número Seqüencial do registro no arquivo 
//-- posicao 395   400  9(06) 
$cString  .= prepararCampo("Numero sequencial linha arquivo","Num",6,0,$nSeq);

/*
//-- dac dígito de auto conferência ag/conta empresa
// poiscao: 038   038 9(01)
$cString  .= prepararCampo("Dac","Num",1,0,"$mD007[D007_Digito_Verificador_Conta]");

//-- Brancos  Complemento do REGISTRO
//-- posicao: 101   394 X(294)
$cString  .= prepararCampo("Brancos","Char",294,0,"");
*/

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
    
    ///-- NUMERO SEQUENCIAL PARA CADA LINHA DO ARQUIVO (CONFORME LEIAUTE)
    $nSeq++;

    //-- ELIMINA CARACATERES INVALIDOS PARA CFP OU CNPJ
    if ($mT117[D024_Flag_Pessoa_Fisica_Juridica]=='F')
    {
       $cCpfCnpjTipo='01';
       $mT117[D024_Cpf] = trim($mT117[D024_Cpf]);
       $cCpfCnpj  = str_replace(array(".","-","/"," "),"",$mT117[D024_Cpf]);
    }
    elseif ($mT117[D024_Flag_Pessoa_Fisica_Juridica]=='J')
    {
       $cCpfCnpjTipo='02';
       $mT117[D024_Cnpj] = trim($mT117[D024_Cnpj]);
       $cCpfCnpj  = str_replace(array(".","-","/"," "),"",$mT117[D024_Cnpj]);
    }

	$mT117[D024_Cpf]  = trim($mT117[D024_Cpf]);
	$mT117[D024_Cpf]  = str_replace(array(".","-","/"," "),"",$mT117[D024_Cpf]);
	
	$mT117[D024_Cnpj] = trim($mT117[D024_Cnpj]);
	$mT117[D024_Cnpj] = str_replace(array(".","-","/"," "),"",$mT117[D024_Cnpj]);
	
    $mT117[D024_Cep]  = trim($mT117[D024_Cep]);
    $mT117[D024_Cep]  = str_replace(array(".","-"," "),"",$mT117[D024_Cep]);
    
    if ($mT117[D024_Flag_Protestar] != 'N') {
		$mT117[Instrucao_Protesto_1]=$mD007[D007_Instrucao_Protesto_1];
		$mT117[Instrucao_Protesto_2]=$mD007[D007_Instrucao_Protesto_2];
		//$mT117[Instrucao_Cobranca_2]=$mD007[D007_Instrucao_Cobranca_2];  Instr. Cobrança 1 sera implementado no futuro
		$mT117[Instrucao_Cobranca_2]=$mD007[D007_Instrucao_Cobranca_2];
    }

    if($mD007[D007_Flag_Tipo_Registro]==1){
    	// Com registro
    	$dvNossoNumero='14';
    	$dvCarteira='01';

    } else if($mD007[D007_Flag_Tipo_Registro]==2){
    	// Sem resgitro
    	$dvNossoNumero='24';
    	$dvCarteira='02';
	}
	//-- Tipo de registro - Identificação do registro Transação
	//-- Posicao 001   001 9(01)
    $cString .= prepararCampo("Tipo de Registro","Num",1,0,"1");
    
	//-- Código de Inscrição tipo de inscrição da empresa
	//-- Posição: 002   003  9(02) 
    $cString .= prepararCampo("Código de Inscrição","Num",2,0,"02");

	//-- Número de Inscrição Nº de inscrição da empresa (CPF/CNPJ)
	//-- posicao 004   017 9(14)
    $cString .= prepararCampo("Número da inscricao","Num",14,0,"$mD007[D007_CNPJ]");
  
	//-- Uso Exclusivo CAIXA
	//-- posicao: 018   020 9(04)
    $cString .= prepararCampo("Agencia","Num",3,0,"");
    
	//-- Codigo do Beneficiario sem DV, versao 007 (NE004 A2/B)
	//-- posicao: 021   027 9(07)
	$cString  .= prepararCampo("Codigo do Beneficiario","Num",7,0,$beneficiarioCaixa['cnab']);

	//-- Identificacao da emissa do boleto
	//-- posicao: 028   028 9(05)
    $cString .= prepararCampo("ID Emissao","Num",1,0,"2");

	//-- Identificação da Entrega/Distribuição do Boleto
	//-- posicao: 029   029 9(01)
    $cString .= prepararCampo("ID Postagem","Num",1,0,"0");

	//-- Identificação da Entrega/Distribuição do Boleto
	//-- posicao: 030   031 9(02)
    $cString .= prepararCampo("Taxa Permanencia","Num",2,0,"00");

	//-- Uso da Empresa - Identificação do Título na empresa
	//-- posicao: 032   056  X(25)
    $cString .= prepararCampo("Identificação do Título na empresa","Num",25,0,"$mT117[T117_Id]");  

    /*
		O Nosso Número tem no total 17 posições, sendo as duas primeiras de utilização pela CAIXA, as
		quinze demais são livres. As duas primeiras posições definem a modalidade e variam de acordo com a
		carteira a ser utilizada, conforme abaixo:
		1) Quando a emissão for feita pelo Beneficiário, obedecer ao seguinte formato:CCNNNNNNNNNNNNNNN, onde:
		CC = 14 (para Nosso Número da Cobrança Registrada)
		CC = 24 (para Nosso Número da Cobrança Sem Registro) NNNNNNNNNNNNNNN = Número livre do cliente / Beneficiário
		2) Quando a emissão for feita pela CAIXA, terá o seguinte formato: CCNNNNNNNNNNNNNNN, onde:
		CC = 11 (para Nosso Número da Cobrança Registrada)
		CC = 21 (para Nosso Número da Cobrança Sem Registro)NNNNNNNNNNNNNNN
*/
    $cString .= prepararCampo("Digito Nosso número","Num",2,0,"$dvNossoNumero");

	//-- Nosso Número  - Identificação do Título no Banco
	//-- posicao 057   073 9(17) ver *nota3
	$mT117[T117_Nosso_Numero] = substr($mT117[T117_Nosso_Numero], 2,15);
    $cString .= prepararCampo("Nosso Numero","Num",15,0,"$mT117[T117_Nosso_Numero]");

	//-- Brancos - Complemento de registro
	//-- posicoes: 074  075 X(02)
    $cString .= prepararCampo("Brancos","Char",2,0,"");

	//-- Brancos - Complemento de registro
	//-- posicoes: 076  076 X(01)
    $cString .= prepararCampo("Brancos","Char",1,0,"");

	//-- Código do tipo de juros e data de início dos juros
	//-- posições: 077 / 078-083. Para juros em valor diário, a CAIXA
	//-- exige o código 1 e a data de início. Sem juros, informa 5 (isento).
	$jurosDia = round(($mT117['T117_Percentual_Juros_Dia']/100)*$mT117['T002_Valor_Total'],2);
	$codigoJuros = $jurosDia > 0 ? '1' : '5';
	$dataJuros = $jurosDia > 0 ? date('Y-m-d', strtotime('+1 day', strtotime($mT117['T117_Data_Vencimento']))) : '';
    $cString .= prepararCampo("Código dos juros","Num",1,0,$codigoJuros);
    $cString .= prepararCampo("Data de Juros","Date",6,0,$dataJuros);

	//-- Código do tipo do desconto
	//-- posicao: 084   084 9(01)
    $cString .= prepararCampo("Código do Desconto","char",1,0,"0");

	//--Campos em branco 
	//-- posicao: 085   106 9(022)
    $cString .= prepararCampo("Brancos","char",22,0,"");

	//-- Código de carteira
	//-- posicao: 107   108 9(02)
    $cString .= prepararCampo("Codigo da Carteira","char",2,0,"{$dvCarteira}");

	//--Identificação Tipo Ocorrência do arquivo remessa
	//-- posicao: 109   110 9(02)
    $cString .= prepararCampo("Codigo da ocorrencia","char",2,0,"01");

    //-- Número do Documento de Cobrança (Seu Número)
	//-- posicao: 111   120  X(10) *nota 18
	$cString .= prepararCampo("numero do documento","Char",10,0,"$mT117[T002_Numero_Duplicata]");        

	//-- Vencimento - Data de vencimento do título 
	//-- posicao: 121   126  9(06) *NOTA 7
	$cString .= prepararCampo("Vencimento","Date",6,0,"$mT117[T117_Data_Vencimento]");     

	//-- Valor do Título  - Valor Nominal do TÍTULO
	//-- posicao: 127   139 9(11)V9(2) *Nota 8         
	$cString .= prepararCampo("Valor do Titulo","Num",13,2,"$mT117[T117_Valor_Total]");        
        
	//-- Código do Banco  - Nº do Banco na câmara de compensação 
	//-- Posicao 140   142  9(03) 
	$cString .= prepararCampo("Código do Banco","Num",3,0,"104");
        
	//-- AGÊNCIA COBRADORA - AGÊNCIA ONDE O TÍTULO SERÁ COBRADO
	//-- posicao: 143   147 9(05) *nota 9
	$cString .= prepararCampo("AGÊNCIA COBRADORA","Num",5,0,"");
        
	//-- Espécie - Espécie do TÍTULO
	//-- posicao: 148   149  X(02) *Nota 10 
	$cString .= prepararCampo("Especie","Char",2,0,"01");    
	
	//-- Aceite  - Identificação de título aceito ou não ACEITO
	//-- posição: 150   150 X(01) A=ACEITE N=Não ACEITE
	if ($mT117[T117_Aceite]=='S') { $mT117[T117_Aceite]='A'; } else { $mT117[T117_Aceite]='N'; }
	$cString .= prepararCampo("Aceite","Char",1,0,"$mT117[T117_Aceite]");

	//-- Data de Emissão - Data da emissão do título
	//-- Posicao: 151   156 9(06) *Nota 31
	$cString .= prepararCampo("Data de Emissão","Date",6,0,"$mT117[T117_Data_Emissao]");  

	//-- Instrução 1  - 1ª instrução de cobrança 
	//-- posicao 157   158  X(02) *Nota 11
	$cString .= prepararCampo("Instrução 1","Char",2,0,"$mT117[Instrucao_Protesto_1]");   
        
	//-- Instrução 2  2ª instrução de cobrança
	//-- posicao 159   160 X(02) *Nota 11
	$cString .= prepararCampo("Instrução 2","Char",2,0,"00");

	//-- Juros de 1 dia - Valor de Mora por dia de atraso
	//-- posicao: 161   173  9(11)V9(2) *Nota 12 
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
	$cString .= prepararCampo("Número de Inscrição","Num",14,0,"{$cCpfCnpj}");

	//-- Nome - Nome do sacado
	//-- Posicao: 235   274 X(40) *NOTA 15
	$mT117['D024_Nome_Empresa']=\hardness\gLimpaAcentos($mT117['D024_Nome_Empresa']);
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaTexto($mT117['D024_Nome_Empresa']);
	$cString .= prepararCampo("Nome do sacado","Char",40,0,"{$mT117['D024_Nome_Empresa']}");

	//-- Logradouro - Rua, Número e complemento do Sacado
	//-- Posição: 275   314 X(40)
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
	$cString .= prepararCampo("Cep","Num",8,0,"$mT117[D024_Cep]");

	//-- Cidade - Cidade do Sacado 
	//-- Posicoes: 335   349  X(15)
	$cString .= prepararCampo("Cidade","Char",15,0,"$mT117[D020_Nome_Cidade]");

	//-- Estado - UF do Sacado
	//-- Posicoes: 350   351  X(02)
	$cString .= prepararCampo("UF","Char",2,0,"$mT117[D018_UF]");

	//-- Data multa
	//-- Posicoes: 352   357 X(06)
	$datamulta = date('dmy', strtotime("+1 days",strtotime($mT117['T117_Data_Vencimento']))); 
	$cString .= prepararCampo("Data Multa","Num",6,0,"$datamulta");

	//-- Vaor multa
	//-- Posicoes: 358   367 X(10)
	$multa = $mT117['T002_Valor_Total']*($mD007['D007_Percentual_Multa']/100);
	$multa = number_format($multa,2,'.', '');
	$cString .= prepararCampo("Valor Multa","Num",10,2,$multa);

	//-- Sacador/Avalista Nome do Sacador ou Avalista
	//-- Posicoes 368   389	X(22) *Nota 16
	$cString .= prepararCampo("Sacador/Avalista","Char",22,0,"");

	//-- Instrução 3  3ª instrução de cobrança
	//-- posicao 390   391 X(2)
	$cString .= prepararCampo("Instrução 3","Num",2,0,"");

	//-- Prazo - Quantidade de dias
	// posicao: 392   393  9(02) *NOTA 11 (A)
	$cString .= prepararCampo("Prazo","Num",2,0,"$mT117[Instrucao_Protesto_2]");

	//-- Código da moeda
	//-- posicao 394   394 X(1)
	$cString .= prepararCampo("Cod moeda","Char",1,0,"1");

	//-- Número Seqüencial Nº seqüencial do registro no arquivo
	//-- posicao 395   400 9(06)
	$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");
	
    
/*

	//-- dac - dígito de auto conferência ag/conta empresa
	//-- posicao: 029   029 9(01)

	//-- dac - dígito de auto conferência ag/conta empresa
	//-- posicao: 029   029 9(01)
    $cString .= prepararCampo("Digito Conta","Num",1,0,"$mD007[D007_Digito_Verificador_Conta]");
    
	//-- Brancos - Complemento de registro
	//-- posicoes: 030   033 X(04)
    $cString .= prepararCampo("Brancos","Char",4,0,"");
    
	//-- instrução/alegação - cod.instrução/alegação a ser cancelada
	//-- 034   037 9(04) - 
	//-- NOTA 27	
	//-- Deve ser preenchido na remessa somente quando utilizados, na posicao 109-110, os codigos de ocorrencia 35 -  
	//-- Cancelamento de Instrucao e 38 - Cedente não concorda com alegacao do sacado. Para os demais codigos de ocorrencia 
	//-- este campo devera ser preenchido com zeros. 
	//-- Obs.: No arquivo retorno sera informado o mesmo codigo da instrucao cancelada, e para o cancelamento de alegacao de sacado 
	//-- nao ha retorno da informacao.	
    $cString .= prepararCampo("instrução/alegação","Num",4,0,"");

	//-- Qtde de Moeda  Quantidade de moeda variável
	//-- posicao: 071   083 9(08)V9(5) 
	//-- Este campo deverá ser preenchido com zeros caso a moeda seja o Real.     
    $cString .= prepararCampo("Qtde Moeda","Num",13,5,"");        
        
	//-- Nº da Carteira  Número da Carteira no Banco
	//-- posicao: 084   086 9(03) Nota 5 
    $cString .= prepararCampo("Carteira","Num",3,0,"$mD007[D007_Tipo_Carteira_Banco_Brasil]");        
        
	//-- USO DO BANCO - IDENTIFICAÇÃO DA OPERAÇÃO NO BANCO
	//-- posição: 087   107  X(21)
	$cString .= prepararCampo("Operacao no Banco","Char",21,0,"");

	//-- Carteira - Código da Carteira 
	//-- posicao 108   108  X(01) *Nota 5  
	$cString .= prepararCampo("Carteira","Char",1,0,"I");        


	//-- Brancos - Complemento de registro
	//-- Posicao: 265   274  X(10) *Nota 15 
	$cString .= prepararCampo("Brancos","Char",10,0,"");


	

	//-- Brancos Complemento do registro
	//-- posicao 382   385 X(04) 
	$cString .= prepararCampo("Branco","Char",4,0,"");

	//-- Data de Mora  
	//-- posicao 386   391 9(06) DDMMAA  
	$cString .= prepararCampo("Data de Mora","Date",6,0,"");


	//-- BRANCOS COMPLEMENTo DO REGISTRO
	//-- posicao 394   394  X(01)
	$cString .= prepararCampo("Brancos","Char",1,0,"");
	*/


	$cString .= _SALTO_LINHA;
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

mysql_query("UPDATE D007 SET D007_Numero_Sequencial_Remessa = '{$nSeqArquivo}' WHERE D007_Id = '{$D007_Id}'");


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
