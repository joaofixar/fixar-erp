<?php

// Cria ou inicia uma sessão
include_once("../include/sessao.inc");

//--  Conexão com o banco de dados
include_once("../include/conexao.inc");

// Funções Genéricas em PHP
include "../func/funcoes_genericas.inc";

$nT117_D024_Id=$_POST['T117_D024_Id'];
$nT117_D007_Id=$_POST['T117_D007_Id'];
$cT117_Data_Vencimento=$_POST['T117_Data_Vencimento'];
$cT117_Instrucoes=$_POST['T117_Instrucoes'];
$nT117_Percentual_Juros_Dia=$_POST['T117_Percentual_Juros_Dia'];
$nT117_Percentual_Multa=$_POST['T117_Percentual_Multa'];

if (empty($nT117_D024_Id))
{
	msg_alerta("Selecione um Cliente");
    exit;
}
if (empty($nT117_D007_Id))
{
	msg_alerta("Selecione o Banco");
    exit;
}
if (empty($cT117_Data_Vencimento))
{
	msg_alerta("Digite a data de vencimento");
    exit;
}

$cT117_Data_Vencimento=troca_data_amd($cT117_Data_Vencimento);

$hoje=str_replace("-","",troca_data_amd(hoje()));
$venc=str_replace("-","",$cT117_Data_Vencimento);

if ($venc<$hoje)
{
	msg_alerta("Vencimento inferior a data atual. Verifique!");
    exit;
}



mysql_query("SET AUTOCOMMIT=0");
mysql_query("START TRANSACTION");

$cClausula  = " select ";
$cClausula .= " datediff(T002_Data_Vencimento,current_date()) Dias, ";
$cClausula .= " T002_Id, ";
$cClausula .= " T002_Numero_Duplicata, ";
$cClausula .= " T002_Valor_Total, ";
$cClausula .= " T002_Data_Vencimento, ";
$cClausula .= " T002_Data_Vencimento_1, ";
$cClausula .= " T002_Data_Vencimento_2, ";
$cClausula .= " T002_Data_Vencimento_3, ";
$cClausula .= " T002_Data_Vencimento_4, ";
$cClausula .= " T002_Data_Vencimento_5, ";
$cClausula .= " T002_Data_Recebimento, ";
$cClausula .= " T007_Id, ";
$cClausula .= " T007_Flag_ACP ";
$cClausula .= " from T002 ";
$cClausula .= " left join D024 on D024_Id=T002_D024_Id ";
$cClausula .= " left join C004 on C004_Id=T002_C004_Id ";
$cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
$cClausula .= " left join D018 on D018_Id=D020_D018_Id ";
$cClausula .= " left join D027 on D027_Id=T002_D027_Id ";
$cClausula .= " left join T007 on T007_Id=T002_T007_Id ";
$cClausula .= " left join D007 on D007_Id=T002_D007_Id ";
$cClausula .= " left join D062 on D062_Id=D007_D062_Id ";
$cClausula .= " left join T117 on T002_T117_Id=T117_Id ";
$cClausula .= " where T002_Flag_Cancelada='N' ";
$cClausula .= " and T117_Id is null ";
$cClausula .= " and T002_Data_Recebimento='0000-00-00' ";
$cClausula .= " and T002_D024_Id='$nT117_D024_Id' ";

$cSql_T002 = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
	mysql_query("ROLLBACK");
	msg_alerta("ERRO: $cErro/p/pClausula: $cClausula");
	exit;
}

//-- PEGA O ULTIMO NUMERO DO "NOSSO NUMERO"
$cClausula  = " select ";
$cClausula .= " D062_Numero_Banco, ";
$cClausula .= " D007_Nosso_Numero, ";
$cClausula .= " D007_Aceite, ";
$cClausula .= " D007_Especie, ";
$cClausula .= " D007_Especie_Documento, ";
$cClausula .= " D007_Uso_Banco, ";
$cClausula .= " D007_Numero_Agencia, ";
$cClausula .= " D007_Digito_Verificador_Agencia, ";
$cClausula .= " D007_Numero_Conta, ";
$cClausula .= " D007_Digito_Verificador_Conta, ";
$cClausula .= " D007_Numero_Convenio, ";
$cClausula .= " D007_Numero_Contrato, ";
$cClausula .= " D007_Codigo_Baixa, ";
$cClausula .= " D007_Codigo_Cedente, ";
$cClausula .= " D007_Numero_Range, ";
$cClausula .= " D007_Flag_Layout, ";
$cClausula .= " D007_Flag_Tipo_Registro, ";
$cClausula .= " D007_Tipo_Carteira_Banco_Brasil ";
$cClausula .= " from D007 ";
$cClausula .= " left join D062 on D062_Id=D007_D062_Id ";
$cClausula .= " where D007_Id='$nT117_D007_Id' ";
$cSql_D007 = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
	mysql_query("ROLLBACK");
	msg_alerta("ERRO: $cErro/p/pClausula: $cClausula");
	exit;
}
$mD007 = mysql_fetch_array($cSql_D007);

if (empty($mD007[D062_Numero_Banco]))
{
	mysql_query("ROLLBACK");
	msg_alerta("Numero do banco nao foi definido");
	exit;
}

if ($mD007[D062_Numero_Banco]=='409' and strlen($mD007[D007_Nosso_Numero])>10)
{
   mysql_query("ROLLBACK");
   msg_alerta("Campo: Nosso numero definido na conta bancaria não pode ultrapassar 10 caracteres: $mD007[D007_Nosso_Numero]");
   exit;
}
if ($mD007[D062_Numero_Banco]=='104' and strlen($mD007[D007_Nosso_Numero])>9)
{
   mysql_query("ROLLBACK");
   msg_alerta("Campo: Nosso numero definido na conta bancaria não pode ultrapassar 9 caracteres: $mD007[D007_Nosso_Numero]");
   exit;
}

if ($mD007[D062_Numero_Banco]=='399')
{
   if (strlen($mD007[D007_Numero_Range])!=5)
   {
      mysql_query("ROLLBACK");
      msg_alerta("O campo Range definido na conta bancária, não pode ser diferente de 5 caracteres: $mD007[D007_Numero_Range]");
      exit;
   }
   if (strlen($mD007[D007_Nosso_Numero])!=5)
   {
      mysql_query("ROLLBACK");
      msg_alerta("O campo Nosso Numero definido na conta bancaria, não pode ser diferente de 5 caracteres: $mD007[D007_Nosso_Numero]");
      exit;
   }
}

//-- Para qualquer banco:
if (empty($mD007[D007_Nosso_Numero]))
{
   $mD007[D007_Nosso_Numero]=1;
}
else
{
   $mD007[D007_Nosso_Numero]++;
}

if ($mD007[D062_Numero_Banco]=='399')
{
   if (strlen($mD007[D007_Nosso_Numero])>5)
   {
       mysql_query("ROLLBACK");
       msg_alerta("O campo Nosso Numero ultrapassou limite: 99999 entre em contato com o banco e solicite um novo Range + Nosso Numero e grave-os no cadastro de contas.");
       exit;
   }
   $mD007[D007_Nosso_Numero]=STR_PAD($mD007[D007_Nosso_Numero],5,"0",STR_PAD_LEFT);
}


$cClausula  = " update D007 set ";
$cClausula .= " D007_Nosso_Numero='$mD007[D007_Nosso_Numero]' ";
$cClausula .= " where D007_Id='$nT117_D007_Id' ";
$cSql_D007 = mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
	mysql_query("ROLLBACK");
	msg_alerta("ERRO: $cErro/p/pClausula: $cClausula");
	exit;
}

if (!empty($mD007[D007_Codigo_Cedente]))
{
    $xCodigo_Cedente=$mD007[D007_Codigo_Cedente];
}
else
{
    $xCodigo_Cedente=$nT117_D024_Id;
}

$T117_Flag_Layout='N';
if ($mD007[D007_Flag_Layout]!=2)
{
   $T117_Flag_Layout='S';
}

if ($mD007[D007_Flag_Tipo_Registro]=='1')
{
   $D007_Flag_Com_Registro='S';
}
elseif ($mD007[D007_Flag_Tipo_Registro]=='2')
{
   $D007_Flag_Com_Registro='N';
}
else
{
   msg_alerta("Tipo de cobrança deve ser definido como: COM REGISTRO ou SEM REGISTRO no cadatro de contas");
   exit;
}

$cClausula  = " insert into T117 ( ";
$cClausula .= " T117_C004_Id, ";
$cClausula .= " T117_D024_Id, ";
$cClausula .= " T117_D007_Id, ";
$cClausula .= " T117_Data_Emissao, ";
$cClausula .= " T117_Data_Vencimento, ";
$cClausula .= " T117_Valor_Total, ";
$cClausula .= " T117_Percentual_Juros_Dia, ";
$cClausula .= " T117_Percentual_Multa, ";
$cClausula .= " T117_Instrucoes, ";
$cClausula .= " T117_Nosso_Numero, ";
$cClausula .= " T117_Flag_Layout, ";
$cClausula .= " T117_Flag_Com_Registro, ";
$cClausula .= " T117_Codigo_Cedente, ";
$cClausula .= " T117_Aceite, ";
$cClausula .= " T117_Especie, ";
$cClausula .= " T117_Especie_Documento, ";
$cClausula .= " T117_Uso_Banco ";
$cClausula .= " ) values (";
$cClausula .= " '$Empresa_Atual', ";
$cClausula .= " '$nT117_D024_Id', ";
$cClausula .= " '$nT117_D007_Id', ";
$cClausula .= " current_date(), ";
$cClausula .= " '$cT117_Data_Vencimento', ";
$cClausula .= " '0', ";
$cClausula .= " '$nT117_Percentual_Juros_Dia', ";
$cClausula .= " '$nT117_Percentual_Multa', ";
$cClausula .= " '$cT117_Instrucoes', ";
$cClausula .= " '$mD007[D007_Nosso_Numero]', ";
$cClausula .= " '$T117_Flag_Layout', ";
$cClausula .= " '$D007_Flag_Com_Registro', ";
$cClausula .= " '$xCodigo_Cedente', ";
$cClausula .= " '$mD007[D007_Aceite]', ";
$cClausula .= " '$mD007[D007_Especie]', ";
$cClausula .= " '$mD007[D007_Especie_Documento]', ";
$cClausula .= " '$mD007[D007_Uso_Banco]' ";
$cClausula .= " )";
mysql_query($cClausula);
$cErro = mysql_error();
$nT117_Id = mysql_insert_id();
if (!empty($cErro))
{
	mysql_query("ROLLBACK");
	msg_alerta("ERRO: $cErro/p/pClausula: $cClausula");
	exit;
}

$nIncluido=0;
while ($mT002 = mysql_fetch_array($cSql_T002))
{
	eval("$"."T002_$mT002[T002_Id]=$"."_POST['T002_$mT002[T002_Id]'];");
    $v="T002_$mT002[T002_Id]";

    if ($$v=='on')
    {
       $nValor_Total+=$mT002[T002_Valor_Total];
       $nIncluido++;
	   $cClausula  = " update T002 set ";
	   if (empty($mT002[T002_Data_Vencimento_1]))
	   {
  	      $cClausula .= " T002_Data_Vencimento_1 = T002_Data_Vencimento, ";
	   }
	   elseif (empty($mT002[T002_Data_Vencimento_2]))
	   {
  	      $cClausula .= " T002_Data_Vencimento_2 = T002_Data_Vencimento, ";
	   }
	   elseif (empty($mT002[T002_Data_Vencimento_3]))
	   {
  	      $cClausula .= " T002_Data_Vencimento_3 = T002_Data_Vencimento, ";
	   }
	   elseif (empty($mT002[T002_Data_Vencimento_4]))
	   {
  	      $cClausula .= " T002_Data_Vencimento_4 = T002_Data_Vencimento, ";
	   }
	   elseif (empty($mT002[T002_Data_Vencimento_5]))
	   {
  	      $cClausula .= " T002_Data_Vencimento_6 = T002_Data_Vencimento, ";
	   }
//	   $cClausula .= " T002_Data_Vencimento='$cT117_Data_Vencimento', ";
	   $cClausula .= " T002_T117_Id='$nT117_Id', ";
	   $cClausula .= " T002_D007_Id='$nT117_D007_Id' ";
	   $cClausula .= " where T002_Id='$mT002[T002_Id]' ";
	   mysql_query($cClausula);
	   $cErro = mysql_error();
	   if (!empty($cErro))
	   {
		   mysql_query("ROLLBACK");
		   msg_alerta("ERRO: $cErro/p/pClausula: $cClausula");
		   exit;
	   }
    }
}

$cClausula  = " update T117 set ";
$cClausula .= " T117_Valor_Total='$nValor_Total' ";
$cClausula .= " where T117_Id='$nT117_Id' ";
mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
   mysql_query("ROLLBACK");
   msg_alerta("ERRO: $cErro/p/pClausula: $cClausula");
   exit;
}

/*
$cClausula  = " insert into D085 (";
$cClausula .= " D085_D024_Id, ";
$cClausula .= " D085_C007_Id, ";
$cClausula .= " D085_Data, ";
$cClausula .= " D085_Mensagem  ";
$cClausula .= " ) values ( ";
$cClausula .= " '$D024_Id', ";
$cClausula .= " '$Usuario_Atual', ";
$cClausula .= " current_date(), ";
$cClausula .= " 'Boleto R$ ".number_format($nValor_Total,2,',','.')."'";
$cClausula .= " )";
$cSql=mysql_query($cClausula);
$cErro = mysql_error();
if (!empty($cErro))
{
	echo "Clausula: $cClausula\n\n";
	echo "ERRO: $cErro";
	mysql_query("ROLL BACK");
 echo "</script>"; exit;
}
*/

//-- ESTE ARQUIVO IRÁ GERAR O CODIGO DE BARRAS A LINHA DIGITAVEL E O NOSSO NUMERO COM DIGITO VERIFICADOR
$cArquivo_Gerar="./include/gerar_".$mD007[D062_Numero_Banco].".php";
if (file_exists($cArquivo_Gerar))
{
   include($cArquivo_Gerar);
}
else
{
   mysql_query("ROLLBACK");
   msg_alerta("Arquivo inválido: $cArquivo_Gerar");
   exit;
}


if ($nIncluido>0)
{
   mysql_query("COMMIT");
   msg_alerta("Pronto");
   echo "<script>parent.Recarregar();</script>";
}
else
{
   mysql_query("ROLLBACK");
   msg_alerta("Selecione um titulo para gerar o boleto");
   exit;
}


?>
