<?php
namespace hardness;
/*
	layout do banco
*/
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

if (strlen($mD007['D007_Numero_Agencia'])<=0)
{
   return "Número informado o numero da Agência no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Agencia'])>4)
{
   return "Número da Agência deve ter no máximo 4 caracteres";
}
if (strlen($mD007['D007_Numero_Conta'])<=0)
{
   return "Número informado o numero da conta no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Conta'])>5)
{
   //return "Numero da conta deve ter no máximo 5 caracteres";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])<=0)
{
   return "número informado o digito verificador da conta no cadastro de contas";
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
$cClausula .= " where ifnull(T002_T035_Id,0)<=0 ";
$cClausula .= " and T002_Flag_Cancelada<>'S'";
$cClausula .= " and T002_Flag_Enviar_Banco='S'";
$cClausula .= " and (T117_Flag_Cancelado='N' ";
$cClausula .= " and T117_D007_Id='$D007_Id' ";
// $cClausula .= " and (T117_T035_Id=0 or T117_T035_Id is null))"; //utilizar apenas T002_T035_Id
$cClausula .= "  or (D027_Flag_Tipo='1'))";
$cClausula .= " and T002_C004_Id='{$g['empresaAtual']}'";
$cClausula .= " GROUP BY ifnull(T117_Id,T002_Id)";
//$cClausula .= " and T117_Flag_Enviar_Remessa='S' ";
$T117_validacao=mysql_query($cClausula);
echo mysql_error();

if (mysql_num_rows($T117_validacao)==0)
{
   return "Nenhum titulo foi selecionado para esta conta";
}
$numRegistros=mysql_num_rows($T117_validacao);

//-Elimina o arquivo caso o mesmo exita
$cArquivo='/tmp/remessa_756.txt';
if(file_exists($cArquivo)){
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
    	$mErroCadastro[]="Cliente: {$mT117['D024_Nome_Empresa']} está sem endereço. Remessa não gerada!"; 
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


//obter o id do insert
$nT035_Id = $g['mysqlLastId'];
$cHoje = date("dmy",time());
$nSeq=0000001;
$cString="";

//-- tipo de Registro - Identificação do registro Header 
//-- posicao 001   001 9(001)
$cString  .= prepararCampo("Identificação do Registro","Num",1,0,"0");

//-- operacao - TIPO DE Operação 
//-- posicao 002   002  9(001) 
$cString  .= prepararCampo("Tipo de Operação","Num",1,0,"1");

//-- Literal de Remessa -Identificação por extenso DO movimento 
//-- posicao 003   009  A(007) 
$cString  .= prepararCampo("Identificação por Extenso do Tipo de Operação","Char",7,0,"REMESSA");

//-- Código do serviço - Identificação do tipo de serviço
//-- posicao 010   011 9(002)  
$cString  .= prepararCampo("Tipo do serviço","Num",2,0,"01");

//-- Identificação por extenso do tipo de serviço
//-- posicao 012   019 A(008) 
$cString  .= prepararCampo("Identificação do serviço","Char",9,0,"COBRANÇA");

//-- complemento de registro
//-- posicao 020   026 A(007) 
$cString  .= prepararCampo("Complemento do Registro","Char",7,0,"");

//-- Prefixo da cooperativa
//-- 027   030 9(04)
$cString  .= prepararCampo("Prefixo da Cooperativa","Num",4,0,"$mD007[D007_Numero_Agencia]");

//-- dígito do prefixo
//-- posicao: 031   031 A(001)
$cString  .= prepararCampo("Digito Verificador do Prefixo","Char",1,0,"$mD007[D007_Digito_Verificador_Agencia]");

//-- Obter o digito verificador a partir do Código do cedente
$dvExplode = explode("-", $mD007[D007_Codigo_Cedente]);
$dv = $dvExplode[1];

//-- Concatenando o Código do cedente e o digito verificador do Código caso exista
if($dv!=null){
    $codCedenteDv = trim($mD007[D007_Codigo_Cedente]).trim($dv);
}else{
    $codCedenteDv = trim($mD007[D007_Codigo_Cedente]);
}

//-- Código do cliente/beneficiário e dígito verificador do Código
//-- posicao: 032   040 9(008) 
//voltar aqui
$cString  .= prepararCampo("Código do cliente/beneficiário","Num",9,0, "$codCedenteDv");

//-Número do convênio líder
//--posição: 041 046 9(006)
//-- mudei para char porque se  preenchia com 00"
$cString  .= prepararCampo("Número do convênio líder","Char",6,0,"");

//-- Nome do beneficiário
//-- posicao 047   076 A(30)  
$mD007['C004_Nome_Empresa'] = \hardness\gLimpaAcentos($mD007['C004_Nome_Empresa']);
$mD007['C004_Nome_Empresa'] = \hardness\gLimpaTexto($mD007['C004_Nome_Empresa']);
$cString  .= prepararCampo("Nome da Empresa","Char",30,0,substr($mD007['C004_Nome_Empresa'],0,30));

//--Identificação do campo
//--posição 077 094 A(018)
$cString  .= prepararCampo("Identificação do banco","Char",18,0, "756BANCOOBCED");

//-- Data da gravação da remessa - 
//-- posicao: 095   100  9(006)
$cString  .= prepararCampo("Data da gravação da Remessa","Num",6,0, "$cHoje");

//--Número sequêncial da remessa
//-- posicao: 101  107  9(007) 
$cString  .= prepararCampo("Número sequêncial da Remessa","Num",7,0,"$nSeq");

//--Complemento do registro 
//--posição 108 394
$cString  .= prepararCampo("Complemento do Registro", "Char",287, 0, "");

//-- Número sequencial do registro no arquivo 
//-- posicao 395   400  9(06) 
$cString  .= prepararCampo("Brancos","Num",6,0, "000001");

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

    //-- NUMERO SEQUENCIAL PARA CADA LINHA DO ARQUIVO (CONFORME LAYAUTE)
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
    
    /*$mD007[D007_Instrucao_Protesto_1]
    $mD007[D007_Instrucao_Protesto_2]
    $mD007[D007_Instrucao_Cobranca_2]
    */
    //-- Tipo de registro - Identificação do registro transição
    //-- Posicao 001   001 9(01)
    $cString .= prepararCampo("Identificação do Registro","Num",1,0,"1");
    
    //-- Código de inscrição tipo de inscrição da empresa
    //-- posição: 002   003  9(02) 
    $cString .= prepararCampo("Tipo de inscrição","Num",2,0,"02");

    //-- Número de inscrição Nº de inscrição da empresa (CPF/CNPJ)
    //-- posicao 004   017 9(14)
    $cString .= prepararCampo("Número do CPF/CNPJ do beneficiário","Num",14,0,"$mD007[D007_CNPJ]");

    //-- Prefixo da cooperativa
    //-- posicao: 018   021 9(04)
    $cString .= prepararCampo("Prefixo da Cooperativa","Num",4,0,"$mD007[D007_Numero_Agencia]");

    //-- Digito verificador do Prefixo da cooperativa
    //-- posicao: 022   022 9(01)
    $cString .= prepararCampo("dígito da Cooperativa","Num",1,0,"$mD007[D007_Digito_Verificador_Agencia]");    

    //-- conta - Número da conta corrente da empresa
    //-- posicao: 023   030 9(08)
    $cString .= prepararCampo("Conta","Num",8,0,"$mD007[D007_Numero_Conta]");

    //-- dac - dígito de auto conferência ag/conta empresa
    //-- posicao: 031   031 X(01)
    $cString .= prepararCampo("Digito Conta","Char",1,0,"$mD007[D007_Digito_Verificador_Conta]");

    //-- Número do convenio de Cobrança do beneficiário
    //-- posição 032 037 9(06)
    $cString .= prepararCampo("Número do convênio", "Num", 6, "000000");    

    //-- Nº Controle do Participante
    //-- posicao 038 062 X(25)
    $cString .= prepararCampo("Nº Controle do Participante","Char",25,0,"");
    //$mT117[T117_Id]

    //-- Nosso Número  - Identificação do Título no Banco
    //-- posicao 063   074 9(12) 
    $mT117[T117_Nosso_Numero] = str_replace("-", "", $mT117[T117_Nosso_Numero]);
    $cString .= prepararCampo("Nosso Numero","Num",12,0,"$mT117[T117_Nosso_Numero]");

    //-- Número da parcela 
    //-- posição 075 076 9(02)
    $cString .= prepararCampo("Número da Parcela","Num",2,0,"$mT117[T002_Numero_Parcela]");

    //-- Grupo de valor
    //-- posição 077 078 9(02)
    $cString .= prepararCampo("Grupo de valor","Num",2,0,"00");

    //-- Complemento do registro
    //-- posição 079 081 X(03)
    $cString .= prepararCampo("Complemento do Registro","Char",3,0,"");

    //-- Indicativo de Mensagem ou Sacador/Avalista 
    //-- posição 082 082 X(01)
    $cString .= prepararCampo("Indicativo de mensagem","Char",1,0,"");

    //-- Prefixo de Título 
    //-- posição 083 085 X(03)
    $cString .= prepararCampo("Prefixo de Título","Char",3,0,"");

    //-- Variação da carteira 
    //-- posição 086 088 9(03)
    $cString .= prepararCampo("Variação da carteira","Num",3,0,"000");

    //-- Conta caução
    //-- posição 089 089 9(01) 
    $cString .= prepararCampo("Conta caução","Num",1,0,"0");

    //-- Número do contrato garantia 
    //-- posição 090 094 9(05)
    $cString .= prepararCampo("Número contrato garantia","Num",5,0,"00000");

    //-- DV do contrato
    //-- posição 095 095 X(01)
    $cString .= prepararCampo("DV do contrato","Char",1,0,"0");    

    //-- Número do borderô
    //-- posição 096 101 9(06)
    $cString .= prepararCampo("Número do borderô","Num",6,0,"");

    //-- Complemento do Registro
    //-- posição 102 105 X(04)
    $cString .= prepararCampo("Complemento do registro","Char",4,0,"");
    
    //-- Tipo de emissão 
    //-- posição 106 106 9(01)
    $cString .= prepararCampo("Tipo de emissão","Num",1,0,"2");

    //-- Carteira/Modalidade
    //-- posição 107 108 9(02)
    $cString .= prepararCampo("Carteira/Modalidade","Num",2,0,"01");
 
    //-- Comando/Movimento 
    //-- posição 109 110 9(02)
    $cString .= prepararCampo("Comando/Movimento","Num",2,0,"01");

    //-- Seu Número atribuído pela empresa
    //-- posição 111 120 X(10)
    $cString .= prepararCampo("Seu Número","Char",10,0,"$mT117[T002_Numero_Duplicata]"); 

    //-- Vencimento - Data de vencimento do Título 
    //-- posicao: 121   126  9(06) 
    $cString .= prepararCampo("Vencimento","Date",6,0,"$mT117[T117_Data_Vencimento]"); 

    //-- Valor do Título  - Valor Nominal do Título
    //-- posicao: 127   139 9(11)V9(2)          
    $cString .= prepararCampo("Valor do Titulo","Num",13,2,"$mT117[T117_Valor_Total]");    

    //-- Código do Banco  - Nº do Banco na camera de compensação 
    //-- Posicao 140   142  9(03) 
    $cString .= prepararCampo("Código do Banco","Num",3,0,"756");   

    //-- Agência COBRADORA - Agência onde o título será cobrado
    //-- posicao: 143   146 9(04) 
    $cString .= prepararCampo("Prefixo da Cooperativa","Num",4,0,"$mD007[D007_Numero_Agencia]"); 

    //-- Digito verificador do Prefixo da cooperativa
    //-- posicao: 147   147 X(01)
    $cString .= prepararCampo("Digito da Cooperativa","Char",1,0,"$mD007[D007_Digito_Verificador_Agencia]"); 

    //-- Espécie - Espécie do Título
    //-- posicao: 148   149  9(02)  
    $cString .= prepararCampo("Especie","Num",2,0,"01"); 

    //-- Aceite  - Identificação de Título aceito ou Nº ACEITO
    //-- posição: 150   150 X(01) 0=ACEITE 1=Nºo ACEITE
    if ($mT117[T117_Aceite]=='S') { 
        $aceite='0'; 
    } else { 
        $aceite='1'; 
    }
    $cString .= prepararCampo("Aceite","Char",1,0,"$aceite");

    //-- Data de emissão - Data da emissão do Título
    //-- Posicao: 151   156 9(06) 
    $cString .= prepararCampo("Data de emissão","Date",6,0,"$mT117[T117_Data_Emissao]"); 

    //-- Instrução 1 - 1 Instrução de cobrança 
    //-- posicao 157   158  9(02) 
    $cString .= prepararCampo("Instrução 1","Char",2,0,"$mD007[D007_Instrucao_Protesto_1]");  

    //-- Instrução 2 - 2 Instrução de cobrança
    //-- posicao 159   160 X(02) *Nota 11
    $cString .= prepararCampo("Instrução 2","Num",2,0,"$mD007[D007_Instrucao_Cobranca_2]");  

    //-- Juros de 1 dia - Valor de Mora por dia de atraso
    //-- posicao: 161   166  9(02)V9999 
    $jurosMes = round((($mT117['T117_Percentual_Juros_Dia']*30)/100),4);
    $cString .= prepararCampo("Taxa de mora mês","Num",6,4,$jurosMes);

    //-- Taxa de multa por dia
    //-- posicao 167 172
    $cString .= prepararCampo("Taxa de multa","Num",6,4, "$mT117[T117_Percentual_Multa]");

    //-- Tipo Distribição
    //-- posição 173 173 9(01)
    $cString .= prepararCampo("Tipo de Distribição","Num",1,0,"2");

    //-- Desconto até  - Data limite para conceder de desconto
    //-- posicao: 174   179  9(06) DDMMAA 
    $cString .= prepararCampo("Desconto até","Date",6,0,"");  

    //-- Valor do desconto - Valor do desconto a ser concedido
    //-- posicao: 180   192  9(11)V99 
    $cString .= prepararCampo("Valor do desconto","Num",13,2,"");

    //-- Código da moeda
    //-- posicao: 193 193 9(1)  
    $cString .= prepararCampo("Código da moeda","Num",1,0,"9"); 

    //-- Valor do I.O.F.  - Valor do I.O.F. recolhido p/ Notas Seguro
    //-- posicao: 194 205  9(12)  
    $cString .= prepararCampo("Valor do IOF","Num",12,0,""); 

    //-- Abatimento - Valor do abatimento a ser concedido
    //-- Posicao: 206   218 9(11)V99 
    $cString .= prepararCampo("Abatimento","Num",12,2,"");

    //-- Filer
    //-- posição: 219  219  9(01) 
    $cString .= prepararCampo("Código de inscrição","Char",1,0,"0");
    
    //-- Tipo pessoa do pagador
    //-- posição: 220  220  9(01) 
    $cString .= prepararCampo("Código de inscrição","Num",1,0,"$cCpfCnpjTipo");

    //-- Número de inscrição da empresa (CPF/CNPJ)
    //-- posicao 221   234 9(14)
    $cString .= prepararCampo("CPF/CNPJ","Num",14,0,"$cCpfCnpj");

    //-- Nome - Nome do Pagador
    //-- Posicao: 235   274 X(30) *NOTA 15
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaAcentos($mT117['D024_Nome_Empresa']);
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaTexto($mT117['D024_Nome_Empresa']);
    $cString .= prepararCampo("Nome do pagador","Char",40,0,"{$mT117['D024_Nome_Empresa']}");

    //-- Endereço - Rua, Número e complemento do Sacado
    //-- posição: 275   311 A(37)
    $mT117['D024_Endereco']=\hardness\gLimpaAcentos($mT117['D024_Endereco']);
    $mT117['D024_Endereco']=\hardness\gLimpaTexto($mT117['D024_Endereco']);
    $mT117['D024_Complemento']=\hardness\gLimpaAcentos($mT117['D024_Complemento']);
    $mT117['D024_Complemento']=\hardness\gLimpaTexto($mT117['D024_Complemento']);
    $mT117['D020_Nome_Cidade']=\hardness\gLimpaAcentos($mT117['D020_Nome_Cidade']);
    $mT117['D020_Nome_Cidade']=\hardness\gLimpaTexto($mT117['D020_Nome_Cidade']);
    $mT117['D024_Bairro']=\hardness\gLimpaAcentos($mT117['D024_Bairro']);
    $mT117['D024_Bairro']=\hardness\gLimpaTexto($mT117['D024_Bairro']);
    $cString .= prepararCampo("Endereço do Pagador","Char",37,0,"{$mT117['D024_Endereco']}, {$mT117['D024_Numero']} {$mT117['D024_Complemento']}");

    //-- Bairro - Bairro do Pagador
    //-- Posicoes: 312   326 X(15)
    $cString .= prepararCampo("Bairro do Pagador","Char",15,0,"{$mT117['D024_Bairro']}");

    //-- CEP - CEP do Pagador 
    //-- Posicoes: 327   334 9(08)
    $cString .= prepararCampo("Cep do Pagador","Num",8,0,"$mT117[D024_Cep]");

    //-- Cidade - Cidade do Pagador 
    //-- Posicoes: 335   349  X(15)
    $cString .= prepararCampo("Cidade do Pagador","Char",15,0,"$mT117[D020_Nome_Cidade]");

    //-- Estado - UF do Pagador
    //-- Posicoes: 350   351  X(02)
    $cString .= prepararCampo("UF do Pagador","Char",2,0,"$mT117[D018_UF]");

    //-- Observação Sacador/Avalista 
    //-- Posicoes 352   391 X(40) 
    $cString .= prepararCampo("Observação","Char",40,0,"");

    //-- Prazo - Quantidade de dias
    // posicao: 392   393  9(02) 
    $cString .= prepararCampo("Prazo","Num",2,0,"$mD007[D007_Instrucao_Protesto_2]");

    //-- BRANCOS COMPLEMENTO DO REGISTRO
    //-- posicao 394   394  X(01)
    $cString .= prepararCampo("Brancos","Char",1,0,"");

    //-- Número sequencial do registro no arquivo
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
//-- posicao 002   194 X(193) 
$cString .= prepararCampo("Brancos","Char",193,0,"");

//-- Mensagem responsabilidade beneficiário
//-- posição 195 234 x(40) 
$cString .= prepararCampo("Mensagem responsabilidade beneficiário","Char",40,0,"");

//-- Mensagem responsabilidade beneficiário
//-- posição 235 274 x(40) 
$cString .= prepararCampo("Mensagem responsabilidade beneficiário","Char",40,0,"");

//-- Mensagem responsabilidade beneficiário
//-- posição 275 314 x(40) 
$cString .= prepararCampo("Mensagem responsabilidade beneficiário","Char",40,0,"");

//-- Mensagem responsabilidade beneficiário
//-- posição 315 354 x(40) 
$cString .= prepararCampo("Mensagem responsabilidade beneficiário","Char",40,0,"");

//-- Mensagem responsabilidade beneficiário
//-- posição 195 234 x(40) 
$cString .= prepararCampo("Mensagem responsabilidade beneficiário","Char",40,0,"");

//-- Número sequencial DO REGISTRO NO ARQUIVO
//-- posicao 395   400  9(06) 
$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

$cString = mysql_real_escape_string($cString);

$cClausula  = " update T035 set ";
$cClausula .= " T035_Conteudo_Arquivo='$cString',  ";
$cClausula .= " T035_Total_Remessa='$valorTotalRemessa'  ";  
$cClausula .= " where T035_Id='$nT035_Id' ";

mysql_query($cClausula);

mysql_query("COMMIT");
                         
return $nT035_Id;  

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
        
        /*if (strlen($String)>($Tamanho-$Decimais)) {
            echo "Erro!! Campo:$Campo - Total de caracteres deste campo: $String (".strlen($String).") maior que o tamanho do campo(".($Tamanho-$Decimais).")";
            exit;
        }*/
        
        
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