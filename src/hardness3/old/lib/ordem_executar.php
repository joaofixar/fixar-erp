<?php
/*
   Programador: Evantoni Fuchs
   Data: 04/12/2009
*/

//--  Cria ou inicia uma sessão
include "../include/sessao.inc";

//--  Conexão com o banco de dados
include "../include/conexao.inc";

//--  Funções Genéricas em PHP
include "../func/funcoes_genericas.inc";

include "../include/include_filtro.php";

include "../include/pasta_usuario.inc";

if (empty($Usuario_Atual)) msg_alerta("Usuario não esta logado");


//-- DEFININDO ARQUIVO QUE GUARDARÁ OS DADOS
//-- $Diretorio_Usuario é definido no arquivo include/pasta_usuario.inc
$cArquivo=$Diretorio_Usuario."/".$cNomeTelaFiltro.".php";

//--  ABRE O ARQUIVO DE FILTRO DO USUARIO
$ponteiro=fopen($cArquivo, "r");
$conteudo=fread($ponteiro, filesize($cArquivo));

//--  SEPARA O ARQUIVO EM LINHAS E GRAVA NA MATRIZ mLINHA
$mLinha=explode("\n", $conteudo);

//--  GRAVA QUANTAS POSICOES TEM A MATRIZ QUE FOI CRIADA
$nTotalLinhas=sizeof($mLinha);

$nLinha_Declaracao=0;


$mDados[]='<?php';
$mDados[]='$xConsulta_Conteudo_Filtro="'.$xConsulta_Conteudo_Filtro.'";';
$mDados[]='$xConsulta_Sinal_Filtro="'.$xConsulta_Sinal_Filtro.'";';
$mDados[]='$xConsulta_Clausula_Filtro="'.$xConsulta_Clausula_Filtro.'";';
$mDados[]='$xConsulta_Mensagem_Filtro="'.$xConsulta_Mensagem_Filtro.'";';
$mDados[]='$xConsulta_Ordem="'.$cOrdem.'";';
unset($cOrdenar);
if (!empty($cOrdem))
{
    if ((empty($xConsulta_Ordem_Desc)))
    {
       if ($cOrdem=='D001_Codigo_Produto')
       {
           if ($mConfig[C020_Ordenar_Codigo_Numerico]=='S')
           {
               $cOrdem="substring(D001_Codigo_Produto,1,locate('-',concat(D001_Codigo_Produto,'-'))) desc,floor(substring(D001_Codigo_Produto,locate('-',concat(D001_Codigo_Produto,'-'))+1,20))";
           }
       }
       
       $mOrdem=explode(';',$cOrdem);
       
       for ($i=0;$i<count($mOrdem);$i++)
       {
       	     if (!empty($cOrdenar))
       	     {
       	     	$cOrdenar.=",$mOrdem[$i]";
       	     }
       	     else
       	     {
       	     	$cOrdenar.="$mOrdem[$i] desc";
       	     }       	
       }
       
       $mDados[]='$xConsulta_Order_By="order by '.$cOrdenar.'";';
       $mDados[]='$xConsulta_Ordem_Desc="desc";';
    }
    else
    {
       if ($cOrdem=='D001_Codigo_Produto')
       {
           if ($mConfig[C020_Ordenar_Codigo_Numerico]=='S')
           {
               $cOrdem="substring(D001_Codigo_Produto,1,locate('-',concat(D001_Codigo_Produto,'-'))),floor(substring(D001_Codigo_Produto,locate('-',concat(D001_Codigo_Produto,'-'))+1,20))";
           }
       }

       $cOrdem=str_replace(';',',',$cOrdem);
       
       $mDados[]='$xConsulta_Order_By="order by '.$cOrdem.'";';
       $mDados[]='$xConsulta_Ordem_Desc="";';
    }
}
else
{
    $mDados[]='$xConsulta_Order_By="";';
    $mDados[]='$xConsulta_Ordem_Desc="";';
}
if (strpos('...'.$xVariaveis_Filtro,'@')>0)
{
    $mDados[]='$xVariaveis_Filtro="'.$xVariaveis_Filtro.'";';
    $mVariaveis_Filtro=explode(';',$xVariaveis_Filtro);
    for ($i=0;$i<=count($mVariaveis_Filtro);$i++)
     {
         if (!empty($mVariaveis_Filtro[$i]))
         {
             $mDados[]=str_replace('@','$',"$mVariaveis_Filtro[$i];");
         }
    }
}
$mDados[]='?'.'>';

$cDados=implode("\n",$mDados);
 echo "<BR><BR>".$Acumula_Filtro;
// grava o arquivo
if (!file_put_contents($cArquivo,$cDados))
{
   msg_alerta("IMPOSSIVEL CRIAR O ARQUIVO: $cArquivo");
   exit;
}
else
{
   // echo "<script>parent.parent.ReloadTela();</script>";
   echo "<script>
             parent.parent.location.replace(parent.parent.location.href);
         </script>";
}

?>
