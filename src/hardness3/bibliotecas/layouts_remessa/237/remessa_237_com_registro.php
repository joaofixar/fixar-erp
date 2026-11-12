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
$cClausula .= " GROUP BY T117_Id";
//$cClausula .= " and T117_Flag_Enviar_Remessa='S' ";
$T117_validacao=mysql_query($cClausula);
echo mysql_error();


if (mysql_num_rows($T117_validacao)==0)
{
   return "Nenhum titulo foi selecionado para esta conta";
}
$numRegistros=mysql_num_rows($T117_validacao);


// Verificando se possuem boletos com o bosso Nºmero repetido
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
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem CPF. Remessa Não gerada!"; 
    }
    
    if (empty($mT117['D024_Cnpj']) and $mT117['D024_Flag_Pessoa_Fisica_Juridica']=='J')   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem CNPJ. Remessa Não gerada!"; 
    }
    
    if (empty($mT117['D024_Endereco']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Endereço. Remessa Não gerada!"; 
    }

    if (empty($mT117['D024_Bairro']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Bairro. Remessa Não gerada!"; 
    }

    if (empty($mT117['D024_Cep']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Cep. Remessa Não gerada!"; 
    }
        
    if (empty($mT117['D020_Nome_Cidade']))   
    { 
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem Cidade. Remessa Não gerada!"; 
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
 	
//-- operacao - TIPO DE OPERAçãO - REMESSA
//-- posicao 002   002  9(01) 
$cString  .= prepararCampo("Operação","Num",1,0,"1");

//-- Literal de Remessa -Identificação por extenso DO movimento 
//-- posicao 003   009  X(07) 
$cString  .= prepararCampo("Literal de Remessa","Char",7,0,"REMESSA");

//-- Código do serviço - Identificação do tipo de serviço
//-- posicao 010   011 9(02)  
$cString  .= prepararCampo("Código do serviço","Num",2,0,"01");

//-- Literal de serviço Identificação por extenso do tipo de serviço
//-- posicao 012   026 X(15) 
$cString  .= prepararCampo("Literal de serviço","Char",15,0,"COBRANCA");

//-- codigo da empresa - codigo fornecido pelo banco
//-- 027   046 9(20)
$cString  .= prepararCampo("Código da Empresa","Num",20,0,$mD007['D007_Codigo_Cedente']); //4168133

//-- nome da empresa
//-- posicao 047  076 9(30)
$mD007['C004_Nome_Empresa'] = \hardness\gLimpaAcentos($mD007['C004_Nome_Empresa']);
$mD007['C004_Nome_Empresa'] = \hardness\gLimpaTexto($mD007['C004_Nome_Empresa']);
$cString  .= prepararCampo("Nome da Empresa","Char",30,0,substr($mD007['C004_Nome_Empresa'],0,30));

//-- numero do banco
//-- posicao: 077 079 9(03)
$cString  .= prepararCampo("Código do Banco","Num",3,0,"237");

//-- dac dï¿½gito de auto conferï¿½ncia ag/conta empresa
// poiscao: 080  094 9(15)
$cString  .= prepararCampo("Nome do Banco","Char",15,0,"BRADESCO");

//-- Data geraï¿½ï¿½o do arquivo
//-- posicao: 095   100 X(08)  
$cString  .= prepararCampo("Nome do Banco","Num",6,0,$cHoje);

//-- Campo em Branco
//-- posicao 101  108 X(08)  
$cString  .= prepararCampo("Brancos","Char",8,0,"");

//-- Identificação do sistema
//-- posicao: 109 110 9(02)   
$cString  .= prepararCampo("Identificação do Sistema","Char",2,0,"MX"); #ERRO

//-- Nome do Banco - Nome por extenso do banco cobrador
//-- posicao: 111  117  X(07) 
$mD007['D007_Numero_Sequencial_Remessa']++;
$cString  .= prepararCampo("Nº Sequencial de Remessa","Num",7,0,"{$mD007['D007_Numero_Sequencial_Remessa']}"); #ERRO

//-- Campo em Branco
//-- posicao 118  394 X(277)  
$cString  .= prepararCampo("Brancos","Char",277,0,"");

//-- Numero Sequencial do Registro
//-- posicao 395  400 X(6)  
$cString  .= prepararCampo("Numero Sequencial do Registro","Num",6,0,"000001");

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
    
    unset($mT117['Instrucao_Protesto_1']);
    unset($mT117['Instrucao_Protesto_2']);
    if ($mT117['D024_Flag_Protestar'] != 'N') {
		$mT117['Instrucao_Protesto_1']=$mD007['D007_Instrucao_Protesto_1'];
		$mT117['Instrucao_Protesto_2']=$mD007['D007_Instrucao_Protesto_2'];
    }

	//-- Tipo de registro - Identificação do registro Transaï¿½ï¿½o
	//-- Posicao 001   001 9(01)
    $cString .= prepararCampo("Tipo de Registro","Num",1,0,"1");
    #ERRO CAMPOS OPCIONAIS (EXCLUSIVO PARA DEBITO EM CONTA)
	//-- agência mantenedora da conta
	//-- posicao: 002   006 9(05)
    $cString .= prepararCampo("Agencia","Num",5,0,"");
    
    //-- dac - dï¿½gito de auto conferï¿½ncia ag/conta empresa
	//-- posicao: 007   007 9(01)
    $cString .= prepararCampo("Digito Agencia","Num",1,0,"");

	//-- razï¿½o da conta
	//-- posicao: 008   012 9(05)
    $cString .= prepararCampo("Razão da Conta","Num",5,0,"");    
    
	//-- conta - Nºmero da conta corrente da empresa
	//-- posicao: 013   019 9(07)
    $cString .= prepararCampo("Conta","Num",7,0,"");

	//-- dac - dï¿½gito de auto conferï¿½ncia ag/conta empresa
	//-- posicao: 020   020 9(01)
    $cString .= prepararCampo("Digito Conta","Num",1,0,"");
    
    /*Identificação da Empresa Cedente no Banco 021 037 (17)
    021 021 - Zero
    022 024 - Codigo da Carteira
    025 029 - Codigo da Agencia Cedente, sem digito
    030 036 - Conta corrente
    037 037 - Digito da conta
    */

    $cString .= prepararCampo("Ident. da Empresa","Num",1,0,"0");
    $cString .= prepararCampo("Carteira","Num",3,0,"{$mD007['D007_Tipo_Carteira_Banco_Brasil']}");
    $cString .= prepararCampo("Agencia","Num",5,0,"{$mD007['D007_Numero_Agencia']}");
    $cString .= prepararCampo("Conta","Num",7,0,"{$mD007['D007_Numero_Conta']}");
    $cString .= prepararCampo("Digito Conta","Num",1,0,"{$mD007['D007_Digito_Verificador_Conta']}");

    //-- Nº Controle do Participante
    //-- posicao 038 062 (25)
    $cString .= prepararCampo("Nº Controle do Part.","Char",25,0,"");  #ERRO

    //-- numero do banco
	//-- posicao: 063 065 9(03)
	$cString  .= prepararCampo("Código do Banco","Num",3,0,"000");

	//-- indentificacao de multa
	//-- posicao: 066 066 9(01) 0 sem multa / 2 com multa
	$multa = ($mT117['T117_Percentual_Multa'] > 0) ? 2 : 0;
	$cString  .= prepararCampo("Identificação de Multa","Num",1,0,$multa);

	//-- percentual de multa
	//-- posicao: 067 070 9(04) se identificao de multa for 0 preencher com 0
	$mT117['T117_Percentual_Multa'] = number_format($mT117['T117_Percentual_Multa'], 2, '', '');
	$cString  .= prepararCampo("Identificação de Multa","Num",4,0,"{$mT117['T117_Percentual_Multa']}");

	//-- Nosso Nºmero  - Identificação do Tï¿½tulo no Banco
	//-- posicao 071  081 9(11)
    $cString .= prepararCampo("Nosso Numero","Num",11,0,substr($mT117['T117_Nosso_Numero'], 3, 11));
    
    //-- Conferencia numero bancario
	//-- posicao 082  082 9(1)
    $cString .= prepararCampo("Digito Conferencia Numero Bancario","Char",1,0,substr($mT117['T117_Nosso_Numero'], 15, 16)); #ERRO

    //-- Desconto bonificação
	//-- posicao 083  092 9(10)
    $cString .= prepararCampo("Desconto por bonificação","Num",10,0,""); #ERRO
    
    //-- Condição para emissao do boleto
	//-- posicao: 093 093 9(01) 2 = cliente emite o boleto
	$cString  .= prepararCampo("Condição de Emissao","Num",1,0,"2"); #ERRO

	//-- Identifica se o emite boleto para deb. automatico
	//-- posicao: 094 094 9(01)
	$cString  .= prepararCampo("Idenficação para Débito Automatico","Char",1,0,"N"); #ERRO

	//-- Brancos - Complemento de registro
	//-- posicoes: 095   104 X(10)
    $cString .= prepararCampo("Brancos","Char",10,0,"");

    //-- Indicador de Rateio de Credito
	//-- posicao: 105 105 9(01) R se a empresa contratou o serviço de rateio
	$cString  .= prepararCampo("Indicador de Rateio","Char",1,0,""); #ERRO

	//-- Endereçamento para Aviso de Debito Automatico
	//-- posicao: 106 106 9(01)
	$cString  .= prepararCampo("Endereçamento para Aviso","Num",1,0,""); #ERRO	

	//-- Brancos - Complemento de registro
	//-- posicoes: 107   108 X(2)
    $cString .= prepararCampo("Brancos","Char",2,0,"");

	//-- Identificação da Ocorrï¿½ncia 
	//-- posiï¿½ï¿½o: 109 110 9(02)
	$cString .= prepararCampo("Cód. de Ocorrência","Num",2,0,"01"); #ERRO

	//-- Nº do documento - Nº do documento de cobraNºa (Dupl.,NP etc.)
	//-- posicao: 111   120  X(10) *nota 18
	$cString .= prepararCampo("Nº do documento","Char",10,0,"{$mT117['T002_Numero_Duplicata']}");        

	//-- Vencimento - Data de vencimento do tï¿½tulo 
	//-- posicao: 121   126  9(06) *NOTA 7
	$cString .= prepararCampo("Vencimento","Date",6,0,"{$mT117['T117_Data_Vencimento']}");        

	//-- Valor do Tï¿½tulo  - Valor Nominal do Tï¿½TULO
	//-- posicao: 127   139 9(11)V9(2) *Nota 8         
	$cString .= prepararCampo("Valor do Titulo","Num",13,2,"{$mT117['T117_Valor_Total']}");        
        
	//-- Código do Banco  - Nº do Banco na cï¿½mara de compensaï¿½ï¿½o 
	//-- Posicao 140   142  9(03) 
	$cString .= prepararCampo("Código do Banco","Num",3,0,"");    
        
	//-- AGêNCIA COBRADORA - AGêNCIA ONDE O Tï¿½TULO SERï¿½ COBRADO
	//-- posicao: 143   147 9(05) *nota 9
	$cString .= prepararCampo("AGêNCIA COBRADORA","Num",5,0,"");        
        
	//-- Espï¿½cie - Espï¿½cie do Tï¿½TULO
	//-- posicao: 148   149  X(02) *Nota 10 
	$cString .= prepararCampo("Especie","Num",2,0,"01");        
        
	//-- Aceite  - Identificação de tï¿½tulo aceito ou Não ACEITO
	//-- posiï¿½ï¿½o: 150   150 X(01) SEMPRE = N	
	$cString .= prepararCampo("Aceite","Char",1,0,"N");
        
	//-- Data de Emissï¿½o - Data da emissï¿½o do tï¿½tulo
	//-- Posicao: 151   156 9(06) *Nota 31
	$cString .= prepararCampo("Data de Emissão","Date",6,0,"{$mT117['T117_Data_Emissao']}");        
        
	//-- Instrução 1  - 1ï¿½ instrução de cobraNºa 
	//-- posicao 157   158  X(02) *Nota 11
	$cString .= prepararCampo("Instrução 1","Num",2,0,"{$mT117['Instrucao_Protesto_1']}");        
        
	//-- Instrução 2  2ï¿½ instrução de cobraNºa
	//-- posicao 159   160 X(02) *Nota 11
	$cString .= prepararCampo("Instrução 2","Num",2,0,"{$mT117['Instrucao_Protesto_2']}");        

	//-- Juros de 1 dia - Valor de Mora por dia de atraso
	//-- posicao: 161   173  9(11)V9(2) *Nota 12 
	$jurosDia = round((($mT117['T117_Percentual_Juros_Dia']/30)/100)*$mT117['T002_Valor_Total'],2);
	$cString .= prepararCampo("Juros de 1 dia","Num",13,2,"{$jurosDia}");
	    
	//-- Desconto até  - Data limite para concessï¿½o de desconto
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
        
	//-- Código de Inscrição Identificação do tipo de Inscrição/sacado
	//-- Posicao: 219   220  9(02) 01=CPF 02=CNPJ
	$cString .= prepararCampo("Código de Inscrição","Num",2,0,"$cCpfCnpjTipo");        
        
	//-- Nºmero de Inscrição - Nº de Inscrição do Sacado  (CPF/CNPJ)
	//-- Posicao: 221   234 9(14)
	$cString .= prepararCampo("Nºmero de Inscrição","Num",14,0,"$cCpfCnpj");

	//-- Nome - Nome do sacado
	//-- Posicao: 235   274 X(40) *NOTA 15
	$mT117['D024_Nome_Empresa']=\hardness\gLimpaAcentos($mT117['D024_Nome_Empresa']);
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaTexto($mT117['D024_Nome_Empresa']);
	$cString .= prepararCampo("Nome do sacado","Char",40,0,"{$mT117['D024_Nome_Empresa']}");

	//-- Logradouro - Rua, Nºmero e complemento do Sacado
	//-- Posiï¿½ï¿½o: 275   314 X(40)
	$mT117['D024_Endereco']=\hardness\gLimpaAcentos($mT117['D024_Endereco']);
    $mT117['D024_Endereco']=\hardness\gLimpaTexto($mT117['D024_Endereco']);
    $mT117['D020_Nome_Cidade']=\hardness\gLimpaAcentos($mT117['D020_Nome_Cidade']);
	$mT117['D020_Nome_Cidade']=\hardness\gLimpaTexto($mT117['D020_Nome_Cidade']);
	$mT117['D024_Bairro']=\hardness\gLimpaAcentos($mT117['D024_Bairro']);
    $mT117['D024_Bairro']=\hardness\gLimpaTexto($mT117['D024_Bairro']);

    $cString .= prepararCampo("Endereco","Char",40,0,"{$mT117['D024_Endereco']}, {$mT117['D024_Numero']} {$mT117['D020_Nome_Cidade']} {$mT117['D018_UF']}");    	

	//-- Bairro - Bairro do Sacado
	//-- Posicoes: 315   326 X(12)
	$cString .= prepararCampo("Bairro","Char",12,0,"{$mT117['D024_Bairro']}");

	//-- CEP - CEP do sacado 
	//-- Posicoes: 327   331 9(05)
	$cString .= prepararCampo("Cep","Num",5,0,substr($mT117['D024_Cep'], 0, 5));

	//-- CEP - Sufixo CEP do sacado 
	//-- Posicoes: 332   334 9(03)
	$cString .= prepararCampo("Cep Sufixo","Num",3,0,substr($mT117['D024_Cep'], 5, 8));

	//-- Instruï¿½ï¿½es boleto
	//-- Posicoes: 335   394  X(60)
	$mT117['T117_Instrucoes']=\hardness\gLimpaAcentos($mT117['T117_Instrucoes']);
	$mT117['T117_Instrucoes']=\hardness\gLimpaTexto($mT117['T117_Instrucoes']); 
	$cString .= prepararCampo("Cidade","Char",60,0,"{$mT117['T117_Instrucoes']}");

	//-- Nº seqï¿½encial do registro no arquivo
	//-- posicao 395   400 9(06)
	$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

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

//-- NºMERO SEQï¿½ENCIAL DO REGISTRO NO ARQUIVO
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

mysql_query("UPDATE D007 SET D007_Numero_Sequencial_Remessa = '{$mD007['D007_Numero_Sequencial_Remessa']}' WHERE D007_Id = '{$mD007['D007_Id']}'");

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