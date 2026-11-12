<?php

// Cria ou inicia uma sessão
include "../include/sessao.inc";

//--  Conexão com o banco de dados
include "../include/conexao.inc";

include "../func/funcoes_genericas.inc";

//-- RECUPERA AS VARIAVEIS PASSADAS VIA POST
$cOperacao         = $_POST['Operacao'];
$nT199_Id          = $_POST['T199_Id'];
$nT007_Id          = $_POST['T007_Id'];
$cT199_Correcao    = $_POST['T199_Correcao'];

//-- ELIMINA OS ESPACOS DO INICIO E FINAL DA VARIAVEL
$cT199_Correcao    = trim($cT199_Correcao);

//-- CONVERTE EM MAIUSCULO
$cT199_Correcao    = strToMaiusculo($cT199_Correcao);

if (empty($nT007_Id))
{
    msg_alerta("Não foi passado o T007_Id");
}

if ($cOperacao!='1' && $cOperacao!='2')
{
    msg_alerta("Operacao incorreta: $cOperacao");
    exit;
}

//-- NAO PERMITE SALTO DE LINHA, ASPAS SIMPLES OU DUPLAS
$cT199_Correcao    = str_replace(array("\n","\r","'",'"')," ",$cT199_Correcao);

$cT199_Correcao = strToAcentos($cT199_Correcao);

// NAO PERMITE DUPLO ESPACO ENTRE AS PALAVRAS
while (strpos($cT199_Correcao,"  ")>0) $cT199_Correcao = str_replace("  "," ",$cT199_Correcao);
echo "<script>parent.document.getElementById('T199_Correcao').value='$cT199_Correcao';</script>";

if (strlen($cT199_Correcao)<15)
{
    echo "<script>parent.document.getElementById('T199_Correcao').focus();</script>";
    msg_alerta("Texto de correção deve ter mais de 15 caracteres");
    exit;
}

if (strlen($cT199_Correcao)>1000)
{
    echo "<script>parent.document.getElementById('T199_Correcao').focus();</script>";    
    msg_alerta("Texto de correção ultrapassou 1000 caracteres");
    exit;
}


if ($cOperacao=='1')
{
    //-- Encontra o ultimo numero sequencial desta nota fiscal
    $cClausula  = " select ";
    $cClausula .= " T199_Sequencial_Evento+1 as Seq ";
    $cClausula .= " from T199 ";
    $cClausula .= " where T199_T007_Id='$nT007_Id' ";
    $cClausula .= " order by T199_Sequencial_Evento desc ";        
    $cSql_T199 = mysql_query($cClausula);
    $mT199 = mysql_fetch_array($cSql_T199);
    
    if ($mT199[Seq]<=0) $mT199[Seq]=1;
    
    if ($mT199[Seq]>20)
    {
        msg_alerta("Nota fiscal não pode ter mais de 20 cartas de correções");
        exit;        
    }
    
    $cClausula  = " insert into T199 ( ";
    $cClausula .= " T199_T007_Id, ";
    $cClausula .= " T199_C007_Id, ";
    $cClausula .= " T199_Data_Hora_Inclusao, ";
    $cClausula .= " T199_Sequencial_Evento, ";
    $cClausula .= " T199_Correcao ";
    $cClausula .= " ) values ( ";                        
    $cClausula .= " '$nT007_Id', ";
    $cClausula .= " '$Usuario_Atual', ";    
    $cClausula .= " NOW(), ";
    $cClausula .= " '$mT199[Seq]', ";
    $cClausula .= " '$cT199_Correcao' ";            
    $cClausula .= " ) ";
    mysql_query($cClausula);
    $cErro=mysql_error();
    if (!empty($cErro))
    {
        msg_alerta("Ocorreu um erro ao tentar incluir o registro/p/pErro:$cErro/p/pClausula:$cClausula");
        exit;
    }
    else
    {
        msg_alerta('Registro incluido com sucesso!');
        echo "<script>parent.fecharFormularioCCe();parent.Recarregar()</script>";
        exit;
    }
}

if ($cOperacao=='2')
{
    if (empty($nT199_Id))
    {
        msg_alerta("Não foi passado o T199_Id");
    }
    
    //-- FAZ A ALTERACAO DOS CAMPOS REFERENTE AO FORMULARIO DE FINALIZACAO DO PEDIDO
	$cClausula  = " update T199 set ";
	$cClausula .= " T199_Correcao = '$cT199_Correcao' ";
	$cClausula .= " where T199_Id = '$nT199_Id' ";
	mysql_query($cClausula);
	
    $cErro=mysql_error();
    if (!empty($cErro))
    {
        msg_alerta("Ocorreu um erro ao tentar incluir o registro/p/pErro:$cErro/p/pClausula:$cClausula");
        exit;
    }
    else
    {
        msg_alerta('Registro alterado com sucesso!');
        echo "<script>parent.fecharFormularioCCe();parent.Recarregar()</script>";
        exit;
    }
	
}    
?>