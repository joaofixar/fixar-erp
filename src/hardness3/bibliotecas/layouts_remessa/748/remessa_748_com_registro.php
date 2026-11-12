<?php
namespace hardness;
/*
    layout do banco Sicredi
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

//-Elimina o arquivo caso o mesmo exita
$cArquivo='/tmp/remessa_748.txt';
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


$cClausula="select * from T035 where T035_D007_Id='{$D007_Id}' AND T035_Data_Processamento >= '".date("Y-m-d")." 00:00:00' AND T035_Data_Processamento <='".date("Y-m-d")." 23:59:59' AND T035_Numero_Titulos > 0 ORDER BY T035_Id desc";
$cT035 = mysql_query($cClausula);
$numReg = mysql_num_rows($cT035);
$codMes = retornaCodMes(date('m'));
if ($numReg==0){
    $nomeArquivo = $mD007['D007_Codigo_Cedente'] . $codMes . date('d') . ".CRM";
} else {
    $numReg++;
    $nomeArquivo = $mD007['D007_Codigo_Cedente'] . $codMes . date('d') . ".RM{$numReg}";
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

$mD007['C004_Cnpj'] = str_replace(array(".","-","/"," "),"",$mD007['C004_Cnpj']);

//obter o id do insert
$nT035_Id = $g['mysqlLastId'];
$cHoje = date("Ymd",time());
$nSeq=0000001;
$cString="";

//-- tipo de Registro - Identificação do registro Header 
//-- posicao 001   001 9(001)
$cString  .= prepararCampo("Identificação do Header","Num",1,0,"0");

//-- operacao - Identificação do arquivo remessa
//-- posicao 002   002  9(001) 
$cString  .= prepararCampo("Tipo de Operação","char",1,0,"1");

//-- Literal de Remessa -Identificação por extenso da operação
//-- posicao 003   009  A(007) 
$cString  .= prepararCampo("Identificação por Extenso do Tipo de Operação","Char",7,0,"REMESSA");

//-- Código do Serviço de cobrança - Identificação do tipo de serviço
//-- posicao 010   011 9(002)  
$cString  .= prepararCampo("Tipo do Serviço","Num",2,0,"01");

//-- Literal Cobrança - Indentificação por extenso do serviço
//-- posicao 012   026 A(015) 
$cString  .= prepararCampo("Identificação por Extenso do serviço","Char",15,0,"COBRANCA");

//-- Código do beneficiário
//-- posicao 027   031 9(005) 
$cString  .= prepararCampo("Código do cliente/Beneficiário","Num",5,0, "{$mD007['D007_Codigo_Cedente']}");

//-- CPF/CNPJ do beneficiário
//-- 032   045 9(014)
$cString  .= prepararCampo("Número do CPF/CNPJ do Beneficiário","Num",14,0,"{$mD007['C004_Cnpj']}");

//-- Filler
//-- posicao: 046   076 A(031)
$cString  .= prepararCampo("Brancos","Char",31,0,"");

//--Número do Sicredi
//--posição 077 079 9(003)
$cString  .= prepararCampo("Identificação do banco","Num",3,0, "748");

//--Literal Sicredi
//--posição 080 094 A(015)
$cString  .= prepararCampo("Identificação do banco por extenso","Char",15,0, "SICREDI");

//-- Data da Gravação da remessa - 
//-- posicao: 095   102  9(008)
$cString  .= prepararCampo("Data da Gravação da Remessa","Num",8,0, "$cHoje");

//-- Filler
//-- posicao: 103   110 A(008)
$cString  .= prepararCampo("Brancos","Char",8,0,"");

//--Número da remessa
//-- posicao: 111  117  9(007) 
$mD007['D007_Numero_Sequencial_Remessa']++;
$cString  .= prepararCampo("Nº Sequencial da Remessa","Num",7,0,"{$mD007['D007_Numero_Sequencial_Remessa']}"); 

//-- Filler
//-- posicao: 118   390 A(273)
$cString  .= prepararCampo("Brancos","Char",273,0,"");

//-- Versão do sistema
//-- posicao: 391   394 9(3)
$versaoSistema = number_format(2,2); 
$cString  .= "{$versaoSistema}";

//--Número sequencial do registro
//-- posicao: 395  400  9(006) 
$cString  .= prepararCampo("Número Sequencial do Registro","Num",6,0,"$nSeq");

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
    $cClausula .= " where T002_T117_Id='{$mT117['T117_Id']}' or T002_Id='{$mT117['T002_Id']}'";
    mysqli_query($cClausula);

    //-- NUMERO SEQUENCIAL PARA CADA LINHA DO ARQUIVO (CONFORME LAYOUT)
    $nSeq++;

    //-- ELIMINA CARACTERES INVALIDOS PARA CPF OU CNPJ
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
    
    /*$mD007[D007_Instrucao_Protesto_1]
    $mD007[D007_Instrucao_Protesto_2]
    $mD007[D007_Instrucao_Cobranca_2]
    */
    //Detalhe
    //-- Tipo de registro - Identificação do registro Detalhe
    //-- Posicao 001   001 9(01)
    $cString .= prepararCampo("Identificação do Registro","Num",1,0,"1");
    
    //-- Tipo de cobrança - "A" Sicredi com Registro
    //-- Posicao 002   002 A(01)
    $cString .= prepararCampo("Tipo de Cobrança","Char",1,0,"A");

    //-- Tipo de carteira - "A" simples
    //-- Posicao 003   003 A(01)
    $cString .= prepararCampo("Tipo de Carteira","Char",1,0,"A");

    //-- Tipo de Impressão - "A" Normal ; "B" Carnê
    //-- Posicao 004   004 A(01)
    $cString .= prepararCampo("Tipo de Impressão","Char",1,0,"A");

    //-- Filler
    //-- posicao: 005   016 A(012)
    $cString  .= prepararCampo("Brancos","Char",12,0,"");

    //-- Tipo de moeda - "A" Real
    //-- Posicao 017   017 A(01)
    $cString .= prepararCampo("Tipo de Moeda","Char",1,0,"A");

    //-- Tipo de desconto - "A" Valor ; "B" Percentual
    //-- posicao: 018   018 A(01)
    $cString .= prepararCampo("Tipo de Desconto","Char",1,0,"B");    

    //-- Tipo de juros - "A" Valor ; "B" Percentual
    //-- posicao: 019   019 A(01)
    $tipoJuros = ($mD007['D007_Flag_Tipo_Multa'] == 'P') ? 'B': 'A';
    $cString .= prepararCampo("Tipo de juros","Num",1,0, "{$tipoJuros}");

    //-- Filler
    //-- posicao: 020   047 A(028)
    $cString  .= prepararCampo("Brancos","Char",28,0,"");

    //-- Nosso Número  - Identificação do Título no Banco
    //-- posicao 048   056 9(09) 
    $mT117['T117_Nosso_Numero'] = str_replace("/", "", str_replace("-", "", $mT117['T117_Nosso_Numero']));
    $cString .= prepararCampo("Nosso Numero","Num",9,0,"{$mT117['T117_Nosso_Numero']}");   

    //-- Filler
    //-- posicao: 057   062 A(06)
    $cString  .= prepararCampo("Brancos","Char",6,0,"");

    //-- Data da Instrução / Formato AAAAMMDD
    //-- posicao: 063   070  9(08) 
    $cString .= prepararCampo("Data da Instrução","Date",8,0,"$cHoje"); 

    //-- Campo alterado, quando instrução "31"
    //-- posicao 071   071 A(01) 
    $cString .= prepararCampo("Campo alterado","Char",1,0,"");

    //-- Postagem do título 
    //-- posicao 072   072 A(01) 
    $cString .= prepararCampo("Postagem do título","Char",1,0,"N");

    //-- Filler
    //-- posicao: 073   073 A(01)
    $cString  .= prepararCampo("Brancos","Char",1,0,"");

    //-- Emissão do boleto 
    //-- posicao: 074   074 A(01)
    $cString  .= prepararCampo("Emissão do boleto","Char",1,0,"B");

    //-- Número da parcela do carnê 
    //-- posição 075 076 9(02)
    $cString .= prepararCampo("Número da Parcela","Num",2,0,"");

    //-- Número total de parcelas do carnê 
    //-- posição 077 078 9(02)
    $cString .= prepararCampo("Número Total da Parcela","Num",2,0,"");

    //-- Filler
    //-- posicao: 079   082 A(01)
    $cString  .= prepararCampo("Brancos","Char",4,0,"");

    //-- Valor do desconto por dia de antecipação
    //-- posicao: 083   092  9(10)V9(2) 
    $cString .= prepararCampo("Valor do desconto","Num",10,2,"");

    //-- % multa por pagamento em atraso
    //-- posicao 093 096
    $cString .= prepararCampo("Taxa de multa","Num",4,2, "{$mT117['T117_Percentual_Multa']}");

    //-- Filler
    //-- posicao: 097   108 A(12)
    $cString  .= prepararCampo("Brancos","Char",12,0,"");

    //-- Instrução 
    //-- Posição 109 110  9(2)
    $cString .= prepararCampo("Indicativo de mensagem","Num",2,0,"01");

    //-- Seu número/Número Atribuído pela empresa
    //-- posição 111 120 X(10)
    $cString .= prepararCampo("Seu número","Char",10,0,"{$mT117['T002_Numero_Duplicata']}"); 

    //-- Vencimento - Data de vencimento do título DDMMAA
    //-- posicao: 121   126  9(06) 
    $cString .= prepararCampo("Vencimento","Date",6,0,"{$mT117['T117_Data_Vencimento']}"); 

    //-- Valor do Título  - Valor Nominal do TÍTULO
    //-- posicao: 127   139 9(13)V9(2)          
    $cString .= prepararCampo("Valor do Titulo","Num",13,2,"{$mT117['T117_Valor_Total']}");  

    //-- Filler
    //-- posicao: 140   148 A(09)
    $cString  .= prepararCampo("Brancos","Char",9,0,"");

    //-- Espécie - Espécie do documento
    //-- posicao: 149   149  A(01)  
    $cString .= prepararCampo("Especie","Char",1,0,"A");   

    //-- Aceite  - Identificação de título aceito ou não ACEITO
    //-- posição: 150   150 X(01) S=ACEITE N=Não ACEITE
    if ($mT117['T117_Aceite']=='S') { 
        $aceite='S'; 
    } else { 
        $aceite='N'; 
    }
    $cString .= prepararCampo("Aceite","Char",1,0,"$aceite");

    //-- Data de Emissão - Data da emissão do título DDMMAA
    //-- Posicao: 151   156 9(06) 
    $cString .= prepararCampo("Data de Emissão","Date",6,0,"{$mT117['T117_Data_Emissao']}"); 

    $Instrucao_Protesto_1 = '00';
    $Instrucao_Protesto_2 = '00';
    if ($mT117['D024_Flag_Protestar'] != 'N') {
		$Instrucao_Protesto_1 = $mD007['D007_Instrucao_Protesto_1'];
		$Instrucao_Protesto_2 = $mD007['D007_Instrucao_Protesto_2'];
		//$mT117[Instrucao_Cobranca_2]=$mD007[D007_Instrucao_Cobranca_2];  Instr. Cobran�a 1 sera implementado no futuro
    }
    
    ////-- Instrução de protesto automático - "00" Não protestar automaticamente ; "06" Protestar automaticamente
    //-- posicao 157   158  9(02) 
    $cString .= prepararCampo("Instrução de protesto automático","Num",2,0,"{$Instrucao_Protesto_1}"); 

    ////-- Número de dias para protesto automático
    //-- posicao 159   160  9(02) 
    $cString .= prepararCampo("Número de dias para protesto automático","Num",2,0,"{$Instrucao_Protesto_2}"); 

    //-- Valor/%de juros por dia de atraso
    //-- posicao: 161   173  9(13) 
    if($mD007['D007_Flag_Tipo_Multa'] == 'V'){
        $jurosDia = round((($mT117['T117_Percentual_Juros_Dia']/30)/100)*$mT117['T002_Valor_Total'],2);
    } else {
        $jurosDia = ($mT117['T117_Percentual_Juros_Dia'] / 30);
    }
    $cString .= prepararCampo("Valor/% de juros por dia de atraso","Num",13,2,$jurosDia);

    //-- Desconto até  - Data limite para concessão de desconto
    //-- posicao: 174   179  9(06) DDMMAA 
    $cString .= prepararCampo("Desconto até","Date",6,0,""); 

    //-- Valor do desconto - Valor do desconto a ser concedido
    //-- posicao: 180   192  9(13)V99 
    $cString .= prepararCampo("Valor do desconto","Num",13,2,"");

    //-- Filler
    //-- posicao: 193   205 A(13)
    $cString  .= prepararCampo("Brancos","Char",13,0,"0000000000000"); 

    //-- Abatimento - Valor do abatimento a ser concedido
    //-- Posicao: 206   218 9(13)V99 
    $cString .= prepararCampo("Abatimento","Num",13,2,"");

    //-- Tipo de pessoa do pagador :PF ou PJ
    //-- posicao: 219   219  9(01) - "1"PF "2"PJ
    $cString .= prepararCampo("Tipo de pessoa do pagador","Num",1,0,"$cCpfCnpjTipo");

    //-- Filler
    //-- posicao: 220   220 A(01)
    $cString  .= prepararCampo("Brancos","Char",1,0,"0"); 

    //-- CPF/CNPJ do Pagador
    //-- posicao 221   234 9(14)
    $cString .= prepararCampo("CPF/CNPJ","Num",14,0,"$cCpfCnpj");

    //-- Nome - Nome do Pagador
    //-- Posicao: 235   274 A(40)
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaAcentos($mT117['D024_Nome_Empresa']);
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaTexto($mT117['D024_Nome_Empresa']);
    $cString .= prepararCampo("Nome do pagador","Char",40,0,"{$mT117['D024_Nome_Empresa']}");

    //-- Endereço - Rua, Número e complemento do Sacado
    //-- Posição: 275   314 A(40)
    $mT117['D024_Endereco']=\hardness\gLimpaAcentos($mT117['D024_Endereco']);
    $mT117['D024_Endereco']=\hardness\gLimpaTexto($mT117['D024_Endereco']);
    $mT117['D024_Complemento']=\hardness\gLimpaAcentos($mT117['D024_Complemento']);
    $mT117['D024_Complemento']=\hardness\gLimpaTexto($mT117['D024_Complemento']);
    $cString .= prepararCampo("Endereço do Pagador","Char",40,0,"{$mT117['D024_Endereco']}, {$mT117['D024_Numero']} {$mT117['D024_Complemento']}");

    //-- Código do Pagador 
    //-- Posicoes: 315   319 9(05)
    $cString .= prepararCampo("Código do Pagador","Num",5,0,"00000");

    //-- Filler
    //-- posicao: 320   325 9(06)
    $cString  .= prepararCampo("Zeros","Num",6,0,"000000");

    //-- Filler
    //-- posicao: 326   326 A(01)
    $cString  .= prepararCampo("Brancos","Char",1,0,"");  

    //-- CEP - CEP do Pagador 
    //-- Posicoes: 327   334 9(08)
    $cString .= prepararCampo("Cep do Pagador","Num",8,0,"{$mT117['D024_Cep']}");

    //-- Código do Pagador junto ao cliente
    //-- Posicoes: 335   339  9(05)
    $cString .= prepararCampo("Código do Pagador junto ao cliente","Num",5,0,"{$mT117['D024_Id']}");

    //-- CPF/CNPJ do Sacado Avalista
    //-- Posicoes: 340   353  9(14)
    $cString .= prepararCampo("CPF/CNPJ do Sacado Avalista","Num",14,0,"");

    //-- Nome do Sacador Avalista
    //-- Posicoes 354   394 A(41) 
    $cString .= prepararCampo("Nome do Sacador Avalista","Char",41,0,"");

    //-- Nº seqüencial do registro no arquivo
    //-- posicao 395   400 9(06)
    $cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq"); 

    $cString .= _SALTO_LINHA;
}

// -- RODAPE
$nSeq++;

//-- Tipo de registro Identificação do registro TRAILER 
//-- posicao 001   001 9(01)
$cString .= prepararCampo("Tipo de Registro","Num",1,0,"9");

//-- Identificação do arquivo remessa - TIPO DE OPERAÇÃO 
//-- posicao 002   002  9(001) 
$cString  .= prepararCampo("Tipo de Operação","Num",1,0,"1");

//--Número do Sicredi
//--posição 003 005 9(003)
$cString  .= prepararCampo("Identificação do banco","Num",3,0, "748");

//-- Código do beneficiário
//-- posicao: 006   010 9(005) 
$cString  .= prepararCampo("Código do cliente/Beneficiário","Num",5,0, "{$mD007['D007_Codigo_Cedente']}");

//-- Filler
//-- posicao: 011   394 A(384)
$cString  .= prepararCampo("Brancos","Char",384,0,""); 

//-- NÚMERO SEQÜENCIAL DO REGISTRO NO ARQUIVO
//-- posicao 395   400  9(06) 
$cString .= prepararCampo("Sequencial","Num",6,0,"$nSeq");

$cString .= _SALTO_LINHA;

$cString = mysql_real_escape_string($cString);

mysql_query("UPDATE D007 SET D007_Numero_Sequencial_Remessa = '{$mD007['D007_Numero_Sequencial_Remessa']}' WHERE D007_Id = '{$mD007['D007_Id']}'");

$cClausula  = " update T035 set ";
$cClausula .= " T035_Conteudo_Arquivo='$cString',  ";
$cClausula .= " T035_Total_Remessa='$valorTotalRemessa'  ";  
$cClausula .= " where T035_Id='$nT035_Id' ";

mysql_query($cClausula);

mysql_query("COMMIT");
                         
return $nT035_Id;  

function retornaCodMes($mes){
    switch ($mes) {
        case '1':
            return '1';
            break;
        case '2':
            return '2';
            break;
        case '3':
            return '3';
            break;
        case '4':
            return '4';
            break;
        case '5':
            return '5';
            break;
        case '6':
            return '6';
            break;
        case '7':
            return '7';
            break;
        case '8':
            return '8';
            break;
        case '9':
            return '9';
            break;
        case '10':
            return 'O';
            break;
        case '11':
            return 'N';
            break;
        case '12':
            return 'D';
            break;
    }
}

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
            //-- Encontra um ponto no numero e desconsidera o que estiver depois do ponto 
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