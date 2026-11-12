<?php
namespace hardness;
/*
Programador: Fernando
Programador: Evantoni
Data: 07/05/2010
*/

DEFINE("_SALTO_LINHA",CHR(13).CHR(10));

$cClausula  = " select ";
$cClausula .= " D007_C004_Id, ";
$cClausula .= " D007_D062_Id, ";
$cClausula .= " C004_Cnpj, ";
$cClausula .= " C004_Nome_Empresa, ";
$cClausula .= " D007_Numero_Agencia, "; 
$cClausula .= " D007_Digito_Verificador_Agencia, ";
$cClausula .= " D007_Numero_Conta, ";
$cClausula .= " D007_Digito_Verificador_Conta, ";
$cClausula .= " D007_Instrucao_Protesto_1, ";
$cClausula .= " D007_Instrucao_Protesto_2 ";
$cClausula .= " from D007 ";
$cClausula .= " left join C004 on C004_Id=D007_C004_Id ";
$cClausula .= " where D007_Id='$D007_Id' ";
$D007 = mysql_query($cClausula);
$mD007=mysql_fetch_array($D007);

if (strlen($mD007['D007_Numero_Agencia'])<=0)
{
   return "Não informado o número da agência no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Agencia'])>4)
{
   return "Número da agência deve ter no máximo 4 caracteres";   
}
if (strlen($mD007['D007_Numero_Conta'])<=0)
{
   return "Não informado o numero da conta no cadastro de contas";
}
if (strlen($mD007['D007_Numero_Conta'])>5)
{
   return "Número da conta deve ter no máximo 5 caracteres";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])<=0)
{
   return "Não informado o dígito verificador da conta no cadastro de contas";
}
if (strlen($mD007['D007_Digito_Verificador_Conta'])>2)
{
   return "Dígito verificador da conta deve ter no máximo 2 caracteres";
}

// seleciona todos os boletos gerados dos títulos selecionados
$cClausula  = " select T002_T117_Id, ";
$cClausula .= " T002_Id ";
$cClausula .= " from T002 ";
$cClausula .= " where T002_Data_Recebimento='0000-00-00' ";
$cClausula .= " and ifnull(T002_T035_Id,0)<=0 ";
$cClausula .= " and T002_Flag_Cancelada<>'S'";
$cClausula .= " and T002_Flag_Enviar_Banco='S'";
$T002=mysql_query($cClausula);

if (!empty($cErro)){
	return mysql_error();
}
if (mysql_num_rows($T002)==0)
{
   return "Nenhum boleto foi gerado para os títulos selecionados...";
}

$cClausula  = " select ";
$cClausula .= " T002_Id, ";
$cClausula .= " D024_Flag_Pessoa_Fisica_Juridica, ";
$cClausula .= " D024_Cpf, ";
$cClausula .= " D024_Cnpj, ";
$cClausula .= " D024_Endereco, ";
$cClausula .= " D024_Numero, ";
$cClausula .= " D024_Bairro, ";
$cClausula .= " D024_Cep, ";
$cClausula .= " D020_Nome_Cidade, ";
$cClausula .= " D018_UF, ";
$cClausula .= " T117_Id, ";
$cClausula .= " D024_Nome_Empresa, ";
$cClausula .= " D024_Flag_Protestar, ";
//$cClausula .= " T117_C004_Id, ";
//$cClausula .= " T117_D024_Id, ";
$cClausula .= " T117_Data_Emissao, ";
$cClausula .= " T117_Data_Vencimento, ";
//$cClausula .= " T117_Data_Pagamento, ";
$cClausula .= " T117_Instrucoes, ";
//$cClausula .= " T117_Codigo_Barras, ";
$cClausula .= " T117_Nosso_Numero, ";
$cClausula .= " T117_Aceite, ";
$cClausula .= " C004_Cnpj, ";
//$cClausula .= " T117_Especie, ";
//$cClausula .= " T117_Especie_Documento, ";
//$cClausula .= " T117_Uso_Banco, ";
$cClausula .= " T117_Percentual_Juros_Dia, ";
$cClausula .= " T117_Percentual_Multa, ";
//$cClausula .= " T117_Linha_Digitavel, ";
//$cClausula .= " T117_Codigo_Cedente, ";
$cClausula .= " T117_Valor_Total ";
$cClausula .= " from T002 ";
$cClausula .= " left join T117 on T117_Id=T002_T117_Id ";
$cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
$cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
$cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
$cClausula .= " left join C004 on C004_Id=T117_C004_Id ";
$cClausula .= " where T117_Flag_Cancelado='N' ";
$cClausula .= " and ifnull(T002_T035_Id,0)<=0 ";
$cClausula .= " and T002_Flag_Cancelada<>'S'";
$cClausula .= " and T002_Flag_Enviar_Banco='S'";
$cClausula .= " and T117_D007_Id='$D007_Id' ";
//$cClausula .= " and (T117_T035_Id=0 or T117_T035_Id is null)";//utilizar apenas T002_T035_Id
//$cClausula .= " and T117_Flag_Enviar_Remessa='S' ";
$T117=mysql_query($cClausula);
if (!empty($cErro)){
	return mysql_error();
}

$numRegistros=mysql_num_rows($T117);

if ($numRegistros==0)
{
   return "Nenhum titulo foi selecionado para esta conta";
}


//-- ELIMINA O ARQUIVO CASO O MESMO EXISTA
$cArquivo='/tmp/remessa_399.txt';
if (file_exists($cArquivo))
{
   unlink("$cArquivo");
}

//-- Abre o arquivo
//$cArquivo_Aberto = fopen( $cArquivo, 'x' );

/*
//-- Abre a tela de download do arquivo
header('Content-type: application/x-msdownload');
header('Content-Disposition: attachment; filename='.basename($cArquivo));
header('Pragma: no-cache');
header('Expires: 0');
*/
mysql_query("set autocommit=0");
mysql_query("start transaction");

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
$lastId = $nT035_Id;

$cHeader='';
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// LAYOUT PADR�O CNAB 400 ARQUIVO REMESSA
// REGISTRO HEADER


// C�digo do Registro
// Identifica��o do Registro Header
// Posi��o inicial 001 / final 001 /tamanho 001
// Num�rico igual a �0� (zero)
$cHeader.="0";

// C�digo do Arquivo
// Identifica��o do tipo de arquivo
// Posi��o inicial 002 / final 002 /tamanho 001
// Num�rico igual a �1�
$cHeader.="1";

// Literal Arquivo
// Identifica��o por extenso
// Posi��o inicial 003 / final 009 /tamanho 007
// Alfab�tico igual a �REMESSA�
$cHeader.="REMESSA";

// C�digo do Servi�o
// Identifica��o do tipo de servi�o
// Posi��o inicial 010 / final 011 /tamanho 002
// Num�rico igual a �01�
$cHeader.="01";

// Literal Servi�o
// Identifica��o do servi�o por extenso
// Posi��o inicial 012 / final 026 /tamanho 015
// Alfab�tico igual a �COBRANCA�
$cHeader.=str_pad("COBRANCA",15," ");

// Zero
// Posi��o inicial 027 / final 027 /tamanho 001
// Num�rico igual a�0�(zero)
$cHeader.="0";

// Ag�ncia Cedente
// C�digo da ag�ncia onde o Cliente mant�m conta corrente
// Posi��o inicial 028 / final 031 /tamanho 004
// Num�rico
$cVariavel=substr($mD007['D007_Numero_Agencia'],0,4);
$cVariavel=STR_PAD($cVariavel,4,"0",STR_PAD_LEFT);
$cHeader.="$cVariavel";

// Sub-conta
// Sub-conta da conta do Cliente
// Posi��o inicial 032 / final 033 /tamanho 002
// Num�rico igual a �55�
$cHeader.="55";

// Conta Corrente
// N�mero da conta Corrente do Cliente
// Posi��o inicial 034 / final 044 /tamanho 011
// Exemplo: Ag�ncia 4321; Conta 56789-00
// Campo Conta Corrente ent�o ser�: 43215678900
// Num�rico
$cVariavel=STR_PAD($mD007['D007_Numero_Agencia'],4,"0",STR_PAD_LEFT).STR_PAD($mD007['D007_Numero_Conta'],5,"0",STR_PAD_LEFT).STR_PAD($mD007['D007_Digito_Verificador_Conta'],2,"0",STR_PAD_LEFT);
$cVariavel=STR_PAD($cVariavel,11,"0",STR_PAD_LEFT);
$cHeader.="$cVariavel";

// Banco
// Uso do Banco
// Posi��o inicial 045 / final 046 /tamanho 002
// Uso do Banco
$cHeader.=str_repeat(" ",2);

// Nome do Cliente
// Raz�o Social / Nome do Cliente por extenso
// Posi��o inicial 047 / final 076 /tamanho 030
// Alfanum�rico
$mD007['C004_Nome_Empresa']=\hardness\gLimpaAcentos($mD007['C004_Nome_Empresa']);
$mD007['C004_Nome_Empresa']=\hardness\gLimpaTexto($mD007['C004_Nome_Empresa']);   
$cVariavel=substr($mD007['C004_Nome_Empresa'],0,30);
$cVariavel=STR_PAD($cVariavel,30," ",STR_PAD_RIGHT);
$cHeader.="$cVariavel";

// C�digo do Banco
// N�mero do Banco na Compensa��o
// Posi��o inicial 077 / final 079 /tamanho 003
// Num�rico igual a �399�
$cHeader.="399";

// Nome doBanco
// Nome do Banco por Extenso
// Posi��o inicial 080 / final 094 /tamanho 015
// Alfab�tico igual a �HSBC�
$cHeader.=str_pad("HSBC",15," ");

// Data da Grava��o
// Data da grava��o do Arquivo
// Posi��o inicial 095 / final 100 /tamanho 006
// Num�rico no formato DDMMAA (Dia/M�s/Ano)
$cVariavel=date("dmy",time());
$cHeader.="$cVariavel";

// Densidade
// Densidade de grava��o
// Posi��o inicial 101 / final 105 /tamanho 005
// Num�rico igual a �01600�
$cHeader.="01600";

// Literal Densidade
// Unidade de densidade de grava��o
// Posi��o inicial 106 / final 108 /tamanho 003
// Alfab�tico igual a �BPI�
$cHeader.="BPI";

// Banco
// Uso do Banco
// Posi��o inicial 109 / final 110 /tamanho 002
// Uso do Banco
$cHeader.=str_repeat(" ",2);

// Sigla Layout
// Sigla do Layout t�cnico
// Posi��o inicial 111 / final 117 /tamanho 007
// Alfanum�rico=LANCV08. Campo Obrigat�rio
$cHeader.="LANCV08";

// Banco
// Uso do Banco
// Posi��o inicial 109 / final 110 /tamanho 002
// Uso do Banco
$cHeader.=str_repeat(" ",277);

// N�mero Seq�encial
// N�mero seq�encial do registro no arquivo
// Posi��o inicial 395 / final 400 /tamanho 006
// Num�rico igual a �000001�
$cHeader.="000001";
$cHeader.=_SALTO_LINHA;

//fwrite($cArquivo_Aberto, $cHeader ._SALTO_LINHA);


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// LAYOUT PADR�O CNAB 400 ARQUIVO REMESSA
// REGISTRO DETALHE

$nSequencial=1;
$cDetalhe="";
// array com os titulos desta remessa 
$titulos = array();
$valorTotalRemessa=0;
while ($mT117=mysql_fetch_array($T117))
{
			
	$valorTotalRemessa+=$mT117['T117_Valor_Total'];
	//echo $mT117['T117_Id'].'<br>';
	// só gera remessa dos boletos gerados para os títulos selecionados
	$cClausula  = " select T002_T117_Id, ";
	$cClausula .= " T002_Id ";
	$cClausula .= " from T002 ";
	$cClausula .= " where T002_Data_Recebimento='0000-00-00' ";
    $cClausula .= " and T002_T117_Id='$mT117[T117_Id]' ";
    $cClausula .= " and ifnull(T002_T035_Id,0)<=0 ";
	$cClausula .= " and T002_Flag_Cancelada<>'S'";
	$cClausula .= " and T002_Flag_Enviar_Banco='S'";
	$T002=mysql_query($cClausula);	
	$continua=false;
	while ($mT002=mysql_fetch_array($T002))
	{				
		   $continua=true;		
		   array_push($titulos, $mT002['T002_Id']);
	}	
	if (!$continua)
		continue;
    
    //utilizar apenas T002_T035_Id	
    // $cClausula  = " update T117 set ";
    // $cClausula .= " T117_T035_Id='$nT035_Id'  ";
    // $cClausula .= " where T117_Id='{$mT117['T117_Id']}' ";
    // mysql_query($cClausula);
    // $cErro=mysql_error();
    // if (!empty($cErro))
    // {
    //     mysql_query("ROLLBACK");
    //     return "Ocorreu um erro ao tentar alterar o registro na tabela T117";
    // }
    
    // C�digo do Registro
    // Identifica��o do Registro Detalhe
    // Posi��o inicial 001 / final 001 /tamanho 001
    // Num�rico igual a �1�
    $cDetalhe.="1";

    // C�digo de Inscri��o
    // Identifica��o do c�digo de inscri��o do Cliente
    // Posi��o inicial 002 / final 003 /tamanho 002
    // 01 - CPF
    // 02 - CNPJ
    // 98 - N�o tem
    // 99 - Outros
    // Num�rico
    $cDetalhe.="02";

    // N�mero de Inscri��o
    // N�mero de inscri��o do Cliente (CPF/CNPJ)
    // Posi��o inicial 004 / final 017 /tamanho 014
    // Num�rico
    //$cVariavel=$mT117['D024_Cnpj'];
    $cVariavel=$mT117['C004_Cnpj'];
    $cVariavel=str_replace(array("/"," ",".","-"),"",$cVariavel);
    $cVariavel=STR_PAD($cVariavel,14,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Zero
    // Zero
    // Posi��o inicial 018 / final 018 /tamanho 001
    // Num�rico igual a �0� (zero)
    $cDetalhe.="0";

    // Ag�ncia Cedente
    // C�digo da ag�ncia onde o Cliente mant�m conta
    // Posi��o inicial 019 / final 022 /tamanho 004
    // Num�rico
    $cVariavel=substr($mD007['D007_Numero_Agencia'],0,4);
    $cVariavel=STR_PAD($cVariavel,4,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Sub-conta
    // Sub-conta da conta do Cliente
    // Posi��o inicial 023 / final 024 /tamanho 002
    // Num�rico igual a �55�
    $cDetalhe.="55";

    // Conta Corrente
    // N�mero da conta corrente do Cliente
    // Posi��o inicial 025 / final 035 /tamanho 011
    // Exemplo: Ag�ncia 4321; Conta 56789-00
    // Campo Conta Corrente ent�o ser�: 43215678900
    // Num�rico
    $cVariavel=STR_PAD($mD007['D007_Numero_Agencia'],4,"0",STR_PAD_LEFT).STR_PAD($mD007['D007_Numero_Conta'],5,"0",STR_PAD_LEFT).STR_PAD($mD007['D007_Digito_Verificador_Conta'],2,"0",STR_PAD_LEFT);
    $cVariavel=STR_PAD($cVariavel,11,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Brancos
    // Uso do Banco
    // Posi��o inicial 036 / final 037 /tamanho 002
    // Uso do Banco
    $cDetalhe.="  ";

    // Controle do Participante
    // Identifica��o do t�tulo no sistema do Cliente
    // Posi��o inicial 038 / final 062 /tamanho 025
    // Alfanum�rico
    $cVariavel=STR_PAD($mT117['T117_Id'],25,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Nosso N�mero
    // Identifica��o do t�tulo no Banco
    // Posi��o inicial 063 / final 073 /tamanho 011
    // Num�rico
    $mT117['T117_Nosso_Numero']=substr($mT117['T117_Nosso_Numero'],0,11);
    $cVariavel=STR_PAD($mT117['T117_Nosso_Numero'],11,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Desconto Data-(2)
    // Data limite para o desconto-(2)
    // Posi��o inicial 074 / final 079 /tamanho 006
    // Num�rico no formato DDMMAA (Dia/M�s/Ano)
    $cVariavel=str_repeat("0",6);
    $cDetalhe.="$cVariavel";

    // Valor do Desconto-(2)
    // Valor do desconto a conceder-(2)
    // Posi��o inicial 080 / final 090 /tamanho 011 /decimais 002
    // Apenas para moeda �Real� Num�rico
    $cVariavel=STR_PAD('',11,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Desconto Data-(3)
    // Data limite para o desconto-(3)
    // Posi��o inicial 091 / final 096 /tamanho 006
    // Num�rico no formato DDMMAA (Dia/M�s/Ano)
    $cVariavel=str_repeat("0",6);
    $cDetalhe.="$cVariavel";

    // Valor do Desconto-(3)
    // Valor do desconto a conceder-(3)
    // Posi��o inicial 097 / final 107 /tamanho 011 /decimais 002
    // Num�rico Apenas para moeda �Real�
    $cVariavel=STR_PAD('',11,"0",STR_PAD_LEFT);
    $cDetalhe.=$cVariavel;

    // Carteira
    // Identifica o tipo da carteira de cobran�a
    // Posi��o inicial 108 / final 108 /tamanho 001
    // Num�rico
    $cDetalhe.="1";

    // C�digo da Ocorr�ncia
    // Identifica��o da ocorr�ncia
    // Posi��o inicial 109 / final 110 /tamanho 002
    // Num�rico
    //$cVariavel=STR_PAD('',2,"0",STR_PAD_LEFT);
    $cVariavel="01";
    $cDetalhe.="$cVariavel";

    // Seu N�mero
    // N�mero da duplicata, nota promiss�ria, etc.
    // Posi��o inicial 111 / final 120 /tamanho 010
    // Alfanum�rico.
    // Para os clientes que vierem a operar com a cobran�a Cash Flow Forecasting, este campo � obrigat�rio e ser� entendido somente como num�rico.
    //$cVariavel=STR_PAD($mT117['T117_Id'],10,"0",STR_PAD_LEFT);
    $T002=mysql_query("Select * from T002 where T002_T117_Id=".$mT117['T117_Id']);
    $T002=mysql_fetch_array($T002);
    $T002['T002_Numero_Duplicata'] = str_replace(' ', '', substr($T002['T002_Numero_Duplicata'],0,10));
    $cVariavel=STR_PAD($T002['T002_Numero_Duplicata'],10,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Vencimento
    // Data do vencimento do t�tulo
    // Posi��o inicial 121 / final 126 /tamanho 006
    // Num�rico no formato DDMMAA (Dia/M�s/Ano)
    list($ano,$mes,$dia)=explode("-",$mT117['T117_Data_Vencimento']);
    $ano=substr($ano,-2);
    $cDetalhe.="$dia$mes$ano";

    // Valor do T�tulo
    // Valor nominal do t�tulo
    // Posi��o inicial 127 / final 139 /tamanho 013
    // Num�rico Para moeda �REAL�, com 02 (duas) decimais.Para �moeda vari�vel�, quantidade de moeda com 05 (cinco) decimais.
    $cVariavel=number_format($mT117['T117_Valor_Total'],2);
    $cVariavel=str_replace(array(".",","),"",$cVariavel);
    $cVariavel=STR_PAD($cVariavel,13,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Banco Cobrador
    // N�mero do banco cobrador
    // Posi��o inicial 140 / final 142 /tamanho 003
    // Num�rico igual a �399�
    $cDetalhe.="399";

    // Ag�ncia Deposit�ria
    // Ag�ncia encarregada da cobran�a
    // Posi��o inicial 143 / final 147 /tamanho 005
    // Num�rico Preencher com �zeros�
    $cVariavel=STR_PAD('',5,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Esp�cie
    // Esp�cie do t�tulo
    // Posi��o inicial 148 / final 149 /tamanho 002
    // Num�rico
    $cDetalhe.="98";

    // Aceite
    // Identifica��o de aceito/ n�o - aceito
    // Posi��o inicial 150 / final 150 /tamanho 001
    // Alfanum�rico �A� para aceito �N� para n�o - aceito
    $cVariavel=STR_PAD($mT117['T117_Aceite'],1," ",STR_PAD_RIGHT);
    $cDetalhe.="$cVariavel";

    // Data de Emiss�o
    // Data de emiss�o do t�tulo
    // Posi��o inicial 151 / final 156 /tamanho 006
    // Num�rico no formato DDMMAA (Dia/M�s/Ano)
    list($ano,$mes,$dia)=explode("-",$mT117['T117_Data_Emissao']);
    $ano=substr($ano,2,2);
    $cDetalhe.="$dia$mes$ano";

    // Instru��o 01
    // Primeira instru��o de cobran�a
    // Posi��o inicial 157 / final 158 /tamanho 002
    // Num�rico
    if(!empty($mT117['T117_Percentual_Multa'])){
        $cDetalhe.="24";
    } else {
        $cDetalhe.="00";
    }
    unset($D007_Instrucao_Protesto_1);
    unset($D007_Instrucao_Protesto_2);
    if($mT117['D024_Flag_Protestar'] != 'N'){        
        $D007_Instrucao_Protesto_1 = $mD007['D007_Instrucao_Protesto_1'];
        $D007_Instrucao_Protesto_2 = $mD007['D007_Instrucao_Protesto_2'];
    }

    // Instru��o 02
    // Segunda instru��o de cobran�a
    // Posi��o inicial 159 / final 160 /tamanho 002
    $cVariavel=STR_PAD($D007_Instrucao_Protesto_1,2,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Juros de Mora
    // Valor dos juros a ser cobrado por dia de atraso
    // Posi��o inicial 161 / final 173 /tamanho 013
    // Alfanum�rico
    $JurosDia = round((($mT117['T117_Percentual_Juros_Dia']/30)/100)*$mT117['T117_Valor_Total'],2);
    $JurosDia=number_format($JurosDia,2);
    $JurosDia=str_replace(array(".",","),"",$JurosDia);

    $cVariavel=STR_PAD($JurosDia,13,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Desconto Data
    // Data limite para o desconto
    // Posi��o inicial 174 / final 179 /tamanho 006
    // Num�rico no formato DDMMAA (Dia/M�s/Ano)
    $cVariavel=STR_PAD('',6,"9",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Valor do Desconto
    // Valor do desconto a ser concedido
    // Posi��o inicial 180 / final 192 /tamanho 013 /decimais 002
    // Num�rico Apenas para moeda �Real�
    //$cVariavel=number_format($var,2);
    $cVariavel=STR_PAD('',13,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Valor do IOF
    // Valor do IOF a ser recolhido pelo banco
    // Posi��o inicial 193 / final 205 /tamanho 013 /decimais 002
    // Num�rico Apenas para moeda �Real�
    $cVariavel=STR_PAD('',13,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

     //Valor do Abatimento
     //Valor do abatimento concedido
     //Posi��o inicial 206 / final 218 /tamanho 013 /decimais 002
     //Somente moeda �Real� e quando o c�digo de ocorr�ncia for igual a �04� ou �05� Obs.: Quando utilizar as instru��es 15, 19,16,22, 24, 29,73 e 74, neste campo, arquivo remessa de entrada de t�tulos.
    $Multa = round(($mT117['T117_Percentual_Multa']/100)*$mT117['T117_Valor_Total'],2);
    $Multa=number_format($Multa,2);
    $Multa=str_replace(array(".",","),"",$Multa);
    $cVariavel=STR_PAD($Multa,10,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    $cVariavel=STR_PAD('',3,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";


    // C�digo de Inscri��o
    // Identifica o tipo de inscri��o do sacado
    // Posi��o inicial 219 / final 220 /tamanho 002
    // Num�rico
    if ($mT117['D024_Flag_Pessoa_Fisica_Juridica']=='F')
    {
       $cDetalhe.="01";
    }
    elseif ($mT117['D024_Flag_Pessoa_Fisica_Juridica']=='J')
    {
       $cDetalhe.="02";
    }
    else
    {
       $cDetalhe.="98";
    }

    // N�mero de Inscri��o
    // N�mero de inscri��o do sacado (CPF/CNPJ)
    // Posi��o inicial 221 / final 234 /tamanho 014
    // Num�rico
    $cVariavel="";
    if ($mT117['D024_Flag_Pessoa_Fisica_Juridica']=='F')
    {
       $cVariavel=$mT117['D024_Cpf'];
    }
    elseif ($mT117['D024_Flag_Pessoa_Fisica_Juridica']=='J')
    {
       $cVariavel=$mT117['D024_Cnpj'];
    }
    //$cVariavel=substr($cVariavel,0,14);
    $cVariavel=str_replace(array("/"," ",".","-"),"",$cVariavel);
    $cVariavel=STR_PAD($cVariavel,14,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Raz�o Social / Nome do sacado
    // Posi��o inicial 235 / final 274 /tamanho 040
    // Alfanum�rico
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaAcentos($mT117['D024_Nome_Empresa']);
    $mT117['D024_Nome_Empresa']=\hardness\gLimpaTexto($mT117['D024_Nome_Empresa']);       
    $cVariavel=substr($mT117['D024_Nome_Empresa'],0,40);
    $cVariavel=STR_PAD($cVariavel,40," ",STR_PAD_RIGHT);
    $cDetalhe.="$cVariavel";

    // Endere�o do Sacado
    // Logradouro, n�mero, complemento, etc.
    // Posi��o inicial 275 / final 312 /tamanho 038
    // Alfanum�rico
    // o tamanho maximo do endereço é 38 menos o numero -1 (espaço entre endereço e numero)
    $tamEnd=37-strlen($mT117['D024_Numero']);
    $mT117['D024_Numero']=\hardness\gLimpaAcentos($mT117['D024_Numero']);
    $mT117['D024_Numero']=\hardness\gLimpaTexto($mT117['D024_Numero']);
    
    //$cVariavel=substr($mT117['D024_Endereco']." ".$mT117['D024_Numero'],0,$tamEnd);
    $mT117[D024_Endereco]=\hardness\gLimpaAcentos($mT117[D024_Endereco]);
    $mT117[D024_Endereco]=\hardness\gLimpaTexto($mT117[D024_Endereco]);
    $cVariavel=substr($mT117['D024_Endereco'],0,$tamEnd);
    $cVariavel=STR_PAD($cVariavel." ".$mT117['D024_Numero'],38," ",STR_PAD_RIGHT);
    $cDetalhe.="$cVariavel";   

    // Instru��o de n�o recebimento do bloqueto
    // Instru��o de n�o recebimento do bloqueto.
    // Posi��o inicial 313 / final 314 /tamanho 002
    // Num�rico se utilizada e Brancos se n�o utilizada.( ver nota explicativa 9 ) Somente para as instru��es �71 e 72�.
    //$cDetalhe.="72";
    $cDetalhe.="  ";

    // Bairro
    // Bairro do sacado
    // Posi��o inicial 315 / final 326 /tamanho 012
    // Alfanum�rico
    $mT117[D024_Bairro]=\hardness\gLimpaAcentos($mT117[D024_Bairro]);
    $mT117[D024_Bairro]=\hardness\gLimpaTexto($mT117[D024_Bairro]);      
    $mT117['D024_Bairro']=substr($mT117['D024_Bairro'],0,12);
    $cVariavel=STR_PAD($mT117['D024_Bairro'],12," ",STR_PAD_RIGHT);
    $cDetalhe.="$cVariavel";

    // CEP do Sacado
    // C�digo de endere�o Postal
    // Posi��o inicial 327 / final 331 /tamanho 005
    // Num�rico
    $cVariavel=substr($mT117['D024_Cep'],0,5);
    $cVariavel=STR_PAD($cVariavel,5,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Sufixo do CEP
    // Complemento do CEP
    // Posi��o inicial 332 / final 334 /tamanho 003
    // Alfanum�rico
    $cVariavel=substr($mT117['D024_Cep'],-3);
    $cVariavel=STR_PAD($cVariavel,3," ",STR_PAD_RIGHT);
    $cDetalhe.="$cVariavel";

    // Cidade do Sacado
    // Cidade do sacado Pra�a de pagamento
    // Posi��o inicial 335 / final 349 /tamanho 015
    // Alfanum�rico
    $cVariavel=substr($mT117['D020_Nome_Cidade'],0,15);
    $cVariavel=STR_PAD($cVariavel,15," ",STR_PAD_RIGHT);
    $cDetalhe.="$cVariavel";

    // Sigla da UF
    // Estado do sacado
    // Posi��o inicial 350 / final 351 /tamanho 002
    // Alfab�tico
    $cVariavel=substr($mT117['D018_UF'],0,2);
    $cVariavel=STR_PAD($cVariavel,2," ",STR_PAD_RIGHT);
    $cDetalhe.="$cVariavel";

    // Sacador / Avalista
    // Nome do sacador ou avalista
    // Posi��o inicial 352 / final 390 /tamanho 039
    // Alfanum�rico
    // $cVariavel=substr($mT117['D024_Nome_Empresa'],0,39);
    $cVariavel="";
    $cVariavel=STR_PAD($cVariavel,39," ",STR_PAD_RIGHT);
    $cDetalhe.="$cVariavel";

    // Tipo de Bloqueto
    // Tipo de Bloqueto Utilizado
    // Posi��o inicial 391 / final 391 /tamanho 001
    // Alfab�tico se utilizado.
    // $cDetalhe.="S";
    // tá "E" nos exemplos do cliente 
    $cDetalhe.="E";
    
    // Prazo de Protesto
    // N�mero de dias para protesto ap�s o vencimento, se n�o pago. (Protesto autom�tico)
    // Posi��o inicial 392 / final 393 /tamanho 002
    // Num�rico se utilizada e Brancos se n�o utilizada.
    $cVariavel=STR_PAD($D007_Instrucao_Protesto_2,2,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";

    // Moeda
    // Tipo de Moeda
    // Posi��o inicial 394 / final 394 /tamanho 001
    // Num�rico
    $cDetalhe.="9";

    // N�mero Seq�encial
    // N�mero seq�encial do registro no arquivo
    // Posi��o inicial 395 / final 400 /tamanho 006
    // Num�rico
    $nSequencial++;
    $cVariavel=STR_PAD($nSequencial,6,"0",STR_PAD_LEFT);
    $cDetalhe.="$cVariavel";
    
    $cDetalhe.=_SALTO_LINHA;
    //fwrite($cArquivo_Aberto, $cDetalhe ._SALTO_LINHA);
}

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// LAYOUT PADR�O CNAB 400 ARQUIVO REMESSA
// REGISTRO TRAILER


// C�digo do Registro
// N�mero de dias para protesto ap�s o vencimento, se n�o pago. (Protesto autom�tico)
// Posi��o inicial 001 / final 001 /tamanho 001
// Num�rico igual a �9�
$cTrailer="";
$cTrailer.="9";

// Banco
// Uso do Banco
// Posi��o inicial 002 / final 394 /tamanho 393
// Uso do Banco
$cVariavel=STR_PAD('',393," ",STR_PAD_LEFT);
$cTrailer.="$cVariavel";

// N�mero Seq�encial
// N�mero seq�encial do registro no arquivo
// Posi��o inicial 395 / final 400 /tamanho 006
// Num�rico
$nSequencial++;
$cVariavel=STR_PAD($nSequencial,6,"0",STR_PAD_LEFT);
$cTrailer.="$cVariavel";

$cTrailer.=_SALTO_LINHA;
//fwrite($cArquivo_Aberto, $cTrailer);

//fclose($cArquivo_Aberto);
//$Ponteiro=fopen($cArquivo, "r");
//$Conteudo=fread($Ponteiro, filesize($cArquivo));

$Conteudo=$cHeader.$cDetalhe.$cTrailer;

$Conteudo = mysql_real_escape_string($Conteudo);

$cClausula  = " update T035 set ";
$cClausula .= " T035_Conteudo_Arquivo='$Conteudo',  ";
$cClausula .= " T035_Total_Remessa='$valorTotalRemessa'  ";
$cClausula .= " where T035_Id='$nT035_Id' ";

mysql_query($cClausula);

$cErro=mysql_error();
if (!empty($cErro))
{	
    mysql_query("ROLLBACK");
    return "Ocorreu um erro ao tentar alterar o registro na tabela T035";
}

	// indica que as contas a receber desta remessa já tiveram remessa gerada
	foreach ($titulos as $titulo){	
    	$cClausula  = " update T002 set ";
        $cClausula .= " T002_Flag_Enviar_Banco='N',"; 
    	$cClausula .= " T002_T035_Id='$nT035_Id'";
    	$cClausula .= " where T002_Id=".$titulo;
		mysqli_query($cClausula);
	    $cErro=mysql_error();
	    if (!empty($cErro))
	    {
	        mysql_query("ROLLBACK");
	        return "Ocorreu um erro:$cErro/p/p$cClausula";
	    }
	}		

mysql_query("COMMIT");

return $lastId;
//echo $Conteudo;



?>
