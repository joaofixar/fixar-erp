<?php
/*
-- Cria o historico de navegacao
-- Sempre colocar esse include logo apos o include da sessao

-- Variaveis de Sessao:
   ===================

 - URL_Historico           -> Registra um historico de o Enderecos
 - URL_Descricao_Historico -> Registra um historico de nomes lógicos das páginas acessadas
 - URL_Entidade_Principal  -> Registra um historico com as Entidades Principais
 - URL_Tipo_Historico      -> Registra um historico de Tipos de Telas
 - URL_Descricao_Historico -> Registra um historico de nomes lógicos das páginas acessadas
 - URL_Entidade_Principal  -> Registra um historico com as Entidades Principais

*/

//-- Define o primeiro link como padrao - pag_inicial.php3
If ((Empty($mURL_Historico[0])) and (empty($Cliente_Atual))) {
   $mPasta=explode("/",$_SERVER['REQUEST_URI']);
   $mURL_Historico[0]="/$mPasta[1]/principal/hardness_new.php";
   $mURL_Tipo_Historico[0]="Página Inicial";
   $mURL_Descricao_Historico[0]="Menu";
   $mURL_Entidade_Principal[0]="";
}

$nUrl_Atual=count($mURL_Historico)-1;

$cUrl_Atual=$_SERVER['REQUEST_URI'];
$cURL_Comparar=$mURL_Historico[$nUrl_Atual];

if (strpos($cUrl_Atual,'monta_formulario')===FALSE and
    strpos($cUrl_Atual,'/monta/principal.php')===FALSE)
{
    //-- SE NAO FOR O MONTA FORMULARIO ou MONTA_CONSULTAS, GRAVA NO HISTORICO APENAS O NOME DO PROGRAMA SEM OS PARAMETROS VIA GET
    $nPosFinal=strpos($cUrl_Atual,".php3");
    if ($nPosFinal)
    {
       $cUrl_Atual=substr($cUrl_Atual,0,$nPosFinal+4);
    }
    else
    {
       $nPosFinal=strpos($cUrl_Atual,".php");
       if ($nPosFinal)
       {
           $cUrl_Atual=substr($cUrl_Atual,0,$nPosFinal+4);
       }
    }

    $nPosFinal=strpos($cURL_Comparar,".php3");
    if ($nPosFinal)
    {
       $cURL_Comparar=substr($cURL_Comparar,0,$nPosFinal+4);
    }
    else
    {
       $nPosFinal=strpos($cURL_Comparar,".php");
       if ($nPosFinal)
       {
           $cURL_Comparar=substr($cURL_Comparar,0,$nPosFinal+4);
       }
    }

}

If ($cURL_Comparar!=$cUrl_Atual) {
//-- Grava o historico se a pagina mudar

    If (($nTipoTela==9) and (strpos($cUrl_Atual,'monta_consultas.php3')!==FALSE)) {
       //-- Se nTipo_Tela for 3, não gravará historico
       
       If ($Entidade_Principal!=$mURL_Entidade_Principal[$nUrl_Atual]) {

          //-- Caso a pagina acionada seja um sub-nivel da consulta, grava o historico

          $nUrl_Posterior=$nUrl_Atual+1;
          $mURL_Historico[$nUrl_Posterior]=$_SERVER['REQUEST_URI'];
          $mURL_Entidade_Principal[$nUrl_Posterior]=$Entidade_Principal;
          $nPonteiro_Descricao=$nUrl_Posterior;
          
       } Else {
       
          //-- Sobrepoe o historico da posicao atual
          $mURL_Historico[$nUrl_Atual]=$_SERVER['REQUEST_URI'];
          $nPonteiro_Descricao=$nUrl_Atual;
       }
    } Else {
    
       //-- Grava o historico normal das telas que sao acessadas

       $nUrl_Posterior=$nUrl_Atual+1;
       $mURL_Historico[$nUrl_Posterior]=$_SERVER['REQUEST_URI'];
       $mURL_Entidade_Principal[$nUrl_Posterior]="";
       $nPonteiro_Descricao=$nUrl_Posterior;
       
    }

    $Arquivo_Regras_Inicio_Consulta="../empresas/$Pasta_Empresa/comp/$Entidade/dados_entidade_001.hardness";

    if (file_exists($Arquivo_Regras_Inicio_Consulta)) {
       include $Arquivo_Regras_Inicio_Consulta;
    }
    
    //-- Cria o historico com os nomes logicos das paginas acessadas
    if (!empty($Titulo_Tela))
    {
       $mURL_Descricao_Historico[$nPonteiro_Descricao]=$Titulo_Tela;
    } ElseIf (strpos($mURL_Historico[$nPonteiro_Descricao],'monta_consultas.php3')!==FALSE) {

       $mURL_Tipo_Historico[$nPonteiro_Descricao]="Consulta";
       $mURL_Descricao_Historico[$nPonteiro_Descricao]="$mDados_Entidade[Entidade_Formulario] - $cTela";

    } ElseIf (strpos($mURL_Historico[$nPonteiro_Descricao],'monta_formularios.php3')!==FALSE) {

       $mURL_Tipo_Historico[$nPonteiro_Descricao]="Formulário";
       $mURL_Descricao_Historico[$nPonteiro_Descricao]="$mDados_Entidade[Entidade_Formulario] - $cTela";

    } ElseIf (strpos($mURL_Historico[$nPonteiro_Descricao],'monta_impressao_controle.php3')!==FALSE) {

       $mURL_Tipo_Historico[$nPonteiro_Descricao]="Impressão";
       $mURL_Descricao_Historico[$nPonteiro_Descricao]="$mDados_Entidade[Entidade_Formulario] - $cTela";

    } ElseIf (strpos($mURL_Historico[$nPonteiro_Descricao],'cad_configuracoes_consultas.php3')!==FALSE) {

       $mURL_Tipo_Historico[$nPonteiro_Descricao]="Configuração Consulta";
       $mURL_Descricao_Historico[$nPonteiro_Descricao]="$mDados_Entidade[Entidade_Formulario] - $cTela";

    } ElseIf (strpos($mURL_Historico[$nPonteiro_Descricao],'monta_conf_relatorios.php3')!==FALSE) {

       $mURL_Tipo_Historico[$nPonteiro_Descricao]="Configuração Impressão";
       $mURL_Descricao_Historico[$nPonteiro_Descricao]="$mDados_Entidade[Entidade_Formulario] - $cTela";

    } ElseIf (strpos($mURL_Historico[$nPonteiro_Descricao],'monta_graficos.php3')!==FALSE) {

       $mURL_Tipo_Historico[$nPonteiro_Descricao]="Gráficos";
       $mURL_Descricao_Historico[$nPonteiro_Descricao]="$mDados_Entidade[Entidade_Formulario] - $cTela";

    } ElseIf (strpos($mURL_Historico[$nPonteiro_Descricao],'monta_curva_abc.php3')!==FALSE) {

       $mURL_Tipo_Historico[$nPonteiro_Descricao]="Curva ABC";
       $mURL_Descricao_Historico[$nPonteiro_Descricao]="$mDados_Entidade[Entidade_Formulario] - $cTela";

    } ElseIf (strpos($mURL_Historico[$nPonteiro_Descricao],'editar_arquivo.php3')!==FALSE) {

       $mURL_Tipo_Historico[$nPonteiro_Descricao]="Edição Arq.Config.";
       $mURL_Descricao_Historico[$nPonteiro_Descricao]="$mConfConsulta[Entidade] - $cTela";

    } else {

       $mURL_Tipo_Historico[$nPonteiro_Descricao]="Url";
       $mURL_Descricao_Historico[$nPonteiro_Descricao]=$_SERVER['SCRIPT_NAME'];

    }


}
/*
   for ($x=0; $x<count($mURL_Historico); $x++) {
       echo "$x  -  ";
       echo $mURL_Historico[$x];
       echo "<BR>";
   }
*/

   session_start();
   $_SESSION['Usuario_Atual'];
   $_SESSION['Nome_Usuario'];
   $_SESSION['Cargo_Usuario'];
   $_SESSION['Empresa_Atual'];
   $_SESSION['Nome_Empresa_Atual'];
   $_SESSION['Banco_Dados'];
   $_SESSION['nWidth_Padrao'];
   $_SESSION['nHeight_Padrao'];
   $_SESSION['Pasta_Empresa']; //-- pasta onde estao localizados os dados da empresa
   $_SESSION['cBd_Configuracao'];
   $_SESSION['Usuario_Suporte'];
   if (count($mURL_Historico)>0) {
      $_SESSION['URL_Historico']=implode(",,",$mURL_Historico);
      $_SESSION['URL_Tipo_Historico']=implode(",,",$mURL_Tipo_Historico);
      $_SESSION['URL_Descricao_Historico']=implode(",,",$mURL_Descricao_Historico);
      $_SESSION['URL_Entidade_Principal']=implode(",,",$mURL_Entidade_Principal);
   } else {
      $_SESSION['URL_Historico'];
      $_SESSION['URL_Tipo_Historico'];
      $_SESSION['URL_Descricao_Historico'];
      $_SESSION['URL_Entidade_Principal'];
   }
   $_SESSION['Pagina_Monta_Consulta'];
   $_SESSION['Chave_Pagina_Monta_Consulta'];


?>
