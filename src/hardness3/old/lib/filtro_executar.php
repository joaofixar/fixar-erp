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

$cNomeTelaFiltro=$cConfigNomeTelaFiltro;

include "../include/include_filtro.php";

unset($mConteudo_Filtro);
unset($mSinal);
unset($Variaveis_Filtro);

$cConfigCampos=base64_decode($cConfigCampos);
$mCampos=explode(',,',$cConfigCampos);

$cConfigCabecalho=base64_decode($cConfigCabecalho);
$mCabecalho=explode(',,',$cConfigCabecalho);

$cConfigFormatacaoCampo=base64_decode($cConfigFormatacaoCampo);
$mFormatacaoCampo=explode(',,',$cConfigFormatacaoCampo);

$cMensagem_Filtro_Ativo='';
for ($nI=0;$nI<count($mCampos);$nI++)
{
   // FILTRO
   // CONTEUDO DOS CAMPOS NO FORMULARIO FILTRO
   if (($mFormatacaoCampo[$nI]=='DATA') or ($mFormatacaoCampo[$nI]=='DATAHORA')) {

        $xxData_Inicial='Conteudo_Filtro_'.$nI.'_Inicial';
        $xxData_Final='Conteudo_Filtro_'.$nI.'_Final';
        $Conteudo_Filtro=$$xxData_Inicial.'|'.$$xxData_Final;
        $mConteudo_Filtro[]=$$xxData_Inicial.'|'.$$xxData_Final;

   } else if (($mFormatacaoCampo[$nI]=='DATAUNICA')) {

        $xxData_Inicial='Conteudo_Filtro_'.$nI.'_Inicial';
        $Conteudo_Filtro=$$xxData_Inicial;
        $mConteudo_Filtro[]=$$xxData_Inicial;

   } else {

        $xConteudo='Conteudo_Filtro_'.$nI;
        $Conteudo_Filtro=trim($$xConteudo);
        $mConteudo_Filtro[]=$Conteudo_Filtro;

        if ((strpos($mCampos[$nI],')')<=0) and (strpos($mCampos[$nI],'.')<=0))
        {
            if (strpos($mCampos[$nI],'||')<=0)
            {
                $Variaveis_Filtro.="@"."$mCampos[$nI]='".$Conteudo_Filtro."';";
            }
            else
            {
                $xmCampos=explode('||',$mCampos[$nI]);
                for ($i=0;$i<count($xmCampos);$i++)
                {
                     $Variaveis_Filtro.="@"."$xmCampos[$i]='".$Conteudo_Filtro."';";
                }
            }
        }
        if ($mFormatacaoCampo[$nI]=='VARIAVEL')
        {
            $xSinal='Sinal_'.$nI;
            $Sinal=trim($$xSinal);
            $mSinal[]=$Sinal;
            continue;
        }
   }

   if ($Conteudo_Filtro=='IGNORAR FILTRO')
   {
       continue;
   }

   $xSinal='Sinal_'.$nI;
   $Sinal=trim($$xSinal);
   $mSinal[]=$Sinal;

   //-- MENSAGEM DO FILTRO ATIVO
   if (!empty($Sinal))
   {
      if (!empty($cMensagem_Filtro_Ativo))
      {
         $cMensagem_Filtro_Ativo.=' e ';
      }
      $mConteudo_Final=explode("|",$Conteudo_Filtro);
      $mConteudo_Final[0]=trim($mConteudo_Final[0]);
      if (!empty($mConteudo_Final[1]))
      {
         $mConteudo_Final[0]=' de '.$mConteudo_Final[0].' até '.$mConteudo_Final[1];
      }
      else if (empty($mConteudo_Final[0]))
      {
         $mConteudo_Final[0]="<b>' '</b>";
      }
      $cSignificado_Sinal='';
      if (empty($mConteudo_Final[1]))
      {
         if ($Sinal=='=')         { $cSignificado_Sinal='igual';
         } else if ($Sinal=='!=') { $cSignificado_Sinal='diferente';
         } else if ($Sinal=='>')  { $cSignificado_Sinal='maior que';
         } else if ($Sinal=='>=') { $cSignificado_Sinal='maior ou igual a';
         } else if ($Sinal=='<')  { $cSignificado_Sinal='menor que';
         } else if ($Sinal=='<=') { $cSignificado_Sinal='menor ou igual a';
         } else if ($Sinal=='%')  { $cSignificado_Sinal='contenha';
         } else if ($Sinal=='!%')  { $cSignificado_Sinal='não contenha';
         }
      }

      $cMensagem_Filtro_Ativo.=$mCabecalho[$nI].' '.$cSignificado_Sinal.' <b>'.$mConteudo_Final[0].'</b> ';
   }



      //-- Acumula a condicao na Variavel
      if ((($mFormatacaoCampo[$nI]=='DATA') or ($mFormatacaoCampo[$nI]=='DATAHORA')) and (!Empty($Sinal))){

              $xxData_Inicial='Conteudo_Filtro_'.$nI.'_Inicial';
              $xxData_Final='Conteudo_Filtro_'.$nI.'_Final';

              If ((strlen(trim($$xxData_Inicial))==0) and (strlen(trim($$xxData_Final))>0)) {
                  echo "<script>";
                  echo "parent.Abre_Menu_Filtro();";
                  echo "parent.document.getElementById('$xxData_Inicial').focus();";
                  echo "</script>";
                  msg_alerta('Digite a Data Inicial');
                  exit;
              } else if ((strlen(trim($$xxData_Inicial))>0) and (strlen(trim($$xxData_Final))>0)) {
                  If (!validar_data($$xxData_Inicial)) {
                    echo "<script>";
                    echo "parent.Abre_Menu_Filtro();";
                    echo "parent.document.getElementById('$xxData_Inicial').focus();";
                    echo "</script>";
                    msg_alerta('DATA INICIAL: '.$$xxData_Inicial.' INVÁLIDA!');
                    exit;
                  }
                  If (!validar_data($$xxData_Final)) {
                    echo "<script>";
                    echo "parent.Abre_Menu_Filtro();";
                    echo "parent.document.getElementById('$xxData_Final').focus();";
                    echo "</script>";
                    msg_alerta('DATA FINAL: '.$$xxData_Final.' INVÁLIDA!');
                    exit;
                  }
                  If ((troca_data_amd($$xxData_Inicial))>(troca_data_amd($$xxData_Final))) {
                    echo "<script>";
                    echo "parent.Abre_Menu_Filtro();";
                    echo "parent.document.getElementById('$xxData_Inicial').focus();";
                    echo "</script>";
                    msg_alerta('DATA INICIAL: '.$$xxData_Inicial.' É MAIOR QUE A DATA FINAL: '.$$xxData_Final);
                    exit;
                  }
                  if ($Sinal!="=") {
                    echo "<script>";
                    echo "parent.document.getElementById('$xxData_Inicial').focus();";
                    echo "</script>";
                    msg_alerta('A condição deve ser: \"Igual a...\"');
                    exit;
                  }
              } else if ((strlen(trim($$xxData_Inicial))==0) and (strlen(trim($$xxData_Final))==0)) {
                  if (($Sinal!="=") and ($Sinal!="<>")) {
                    echo "<script>";
                    echo "parent.document.getElementById('$xxData_Inicial').focus();";
                    echo "</script>";
                    msg_alerta('A condição deve ser: \"Igual a...\" ou \"Diferente de...\"');
                    exit;
                  }
              }

              // -- ACUMULA AS DATAS
              if (!empty($Acumula_Filtro)) {
                  $Acumula_Filtro.=' and ';
              }
              if ($mFormatacaoCampo[$nI]=='DATA') {
                $xxxData_Inicial=troca_data_amd($$xxData_Inicial);
                $xxxData_Final=troca_data_amd($$xxData_Final);
              } else {
                $xxxData_Inicial=troca_datahora_amd($$xxData_Inicial);
                $xxxData_Final=troca_datahora_amd($$xxData_Final);
              }
              if ((strlen(trim($$xxData_Inicial))>0) and (strlen(trim($$xxData_Final))==0)) {
                  if (!validar_data($$xxData_Inicial)) {
                    echo "<script>";
                    echo "parent.Abre_Menu_Filtro();";
                    echo "parent.document.getElementById('$xxData_Inicial').focus();";
                    echo "</script>";
                    msg_alerta('DATA INICIAL: '.$$xxData_Inicial.' INVÁLIDA!');
                    exit;
                  }
                  $Acumula_Filtro.="$mCampos[$nI] $Sinal '$xxxData_Inicial'";

              } else if ((strlen(trim($$xxData_Inicial))==0) and (strlen(trim($$xxData_Final))==0)) {

                  $Acumula_Filtro.="$mCampos[$nI] $Sinal '0000-00-00' ";

              } else {

                  $Acumula_Filtro.="$mCampos[$nI] >= '$xxxData_Inicial' and $mCampos[$nI] <= '$xxxData_Final'";

              }

     }
     else if (($mFormatacaoCampo[$nI]=='DATAUNICA') and (!Empty($Sinal)))
     {

              $xxData_Inicial='Conteudo_Filtro_'.$nI.'_Inicial';

              If ((strlen(trim($$xxData_Inicial))==0)) {
                  echo "<script>";
                  echo "parent.Abre_Menu_Filtro();";
                  echo "parent.document.getElementById('$xxData_Inicial').focus();";
                  echo "</script>";
                  msg_alerta('Digite a Data');
                  exit;
              } else if ((strlen(trim($$xxData_Inicial))>0)) {
                  If (!validar_data($$xxData_Inicial)) {
                    echo "<script>";
                    echo "parent.Abre_Menu_Filtro();";
                    echo "parent.document.getElementById('$xxData_Inicial').focus();";
                    echo "</script>";
                    msg_alerta('DATA : '.$$xxData_Inicial.' INVÁLIDA!');
                    exit;
                  }
              }

              // -- ACUMULA AS DATAS
              if (!empty($Acumula_Filtro)) {
                  $Acumula_Filtro.=' and ';
              }

              $xxxData_Inicial=troca_data_amd($$xxData_Inicial);

              if ((strlen(trim($$xxData_Inicial))>0)) {

                  $Acumula_Filtro.="$mCampos[$nI] $Sinal '$xxxData_Inicial'";

              } else if ((strlen(trim($$xxData_Inicial))==0)) {

                  $Acumula_Filtro.="$mCampos[$nI] $Sinal '0000-00-00' ";

              }

     }
     else if (($mFormatacaoCampo[$nI]=='NUMERO') and (!Empty($Sinal)))
     {

              // Se existir filtro, Acumula a condicao na Variavel
              if (!empty($Acumula_Filtro)) {
                  $Acumula_Filtro.=' and ';
              }
              // Verifica se no campo existe um "IF" agregado
              $mCampos[$nI]=stripslashes($mCampos[$nI]);
              $mCampo_Filtro=explode("||",$mCampos[$nI]);

                 if (count($mCampo_Filtro)>1)
                 {
                    $Acumula_Filtro.=' ( ';
                 }
                 for ($nCC=0;$nCC<count($mCampo_Filtro);$nCC++)
                 {
                     if (count($mCampo_Filtro)>1 and $nCC>0)
                     {
                        $Acumula_Filtro.=' or ';
                     }


                      if ($Conteudo_Filtro!='NAO FILTRAR')
                      {
                          $cCampo_Filtro=$mCampo_Filtro[$nCC];
                          $nPos_Final=strrpos($cCampo_Filtro,')');
                          If ($nPos_Final>0) {
                              $cCampo_Filtro=substr($cCampo_Filtro,0,$nPos_Final+1);
                          }
                          If ($Sinal=='%') {
                              if (strpos($Conteudo_Filtro,'%')==true) {
                                 $Acumula_Filtro.=$cCampo_Filtro." like '$Conteudo_Filtro'";
                              } else {
                                 $Acumula_Filtro.=$cCampo_Filtro." like '%".$Conteudo_Filtro."%'";
                              }
                          } Else If ($Sinal=='!%') {
                              $Acumula_Filtro.=$cCampo_Filtro." not like '%".$Conteudo_Filtro."%'";
                          } Else {
                              $Acumula_Filtro.=$cCampo_Filtro." $Sinal ".$Conteudo_Filtro;
                          }
                      }
                      else
                      {
                           $Acumula_Filtro.=$mCampo_Filtro[$nCC];
                      }
                 }
                 if (count($mCampo_Filtro)>1)
                 {
                    $Acumula_Filtro.=' ) ';
                 }


     }
     else If ($Sinal!='')
     {

                 // Se existir filtro, Acumula a condicao na Variavel
                 if (!empty($Acumula_Filtro)) {
                     $Acumula_Filtro.=' and ';
                 }

                 // Verifica se no campo existe um "IF" agregado
                 $XmConteudo_Filtro=explode(";",$Conteudo_Filtro);

                 if (count($XmConteudo_Filtro)>1)
                 {
                    $Acumula_Filtro.=' ( ';
                 }
                 for ($nCF=0;$nCF<count($XmConteudo_Filtro);$nCF++)
                 {

                     if (count($XmConteudo_Filtro)>1 and $nCF>0)
                     {
                        if (($Sinal=='!%') OR ($Sinal=='<>'))
                        {
                            $Acumula_Filtro.=' AND ';
                        }
                        else
                        {
                            $Acumula_Filtro.=' OR ';
                        }
                     }

                     $Conteudo_Filtro=$XmConteudo_Filtro[$nCF];

                     // Verifica se no campo existe um "IF" agregado
                     $mCampos[$nI]=stripslashes($mCampos[$nI]);
                     $mCampo_Filtro=explode("||",$mCampos[$nI]);

                     if (count($mCampo_Filtro)>1)
                     {
                        $Acumula_Filtro.=' ( ';
                     }
                     for ($nCC=0;$nCC<count($mCampo_Filtro);$nCC++)
                     {
                         if (count($mCampo_Filtro)>1 and $nCC>0)
                         {
                            $Acumula_Filtro.=' or ';
                         }

                          if ($Conteudo_Filtro!='NAO FILTRAR')
                          {
                              $cCampo_Filtro=$mCampo_Filtro[$nCC];
                              $nPos_Final=strrpos($cCampo_Filtro,')');
                              If ($nPos_Final>0) {
                                  $cCampo_Filtro=substr($cCampo_Filtro,0,$nPos_Final+1);
                              }
                              If ($Sinal=='%') {
                                  if (strpos($Conteudo_Filtro,'%')==true) {
                                     $Acumula_Filtro.=$cCampo_Filtro." like '$Conteudo_Filtro'";
                                  } else {
                                    if ($mFormatacaoCampo[$nI]=='PARAFUSO')
                                    {
                                        $Conteudo_Filtro2=Strrchr($Conteudo_Filtro, "-");
                                        $Conteudo_Filtro1=substr($Conteudo_Filtro,0,strlen($Conteudo_Filtro)-strlen($Conteudo_Filtro2));
                                        $Acumula_Filtro.=$cCampo_Filtro." like '".str_replace(array(" ","-"),"%",$Conteudo_Filtro1)."%'";
                                        if ((!empty($Conteudo_Filtro2)) and ($Conteudo_Filtro2!=='-'))
                                        {
                                           $Acumula_Filtro.=" and $cCampo_Filtro like '%$Conteudo_Filtro2'";
                                        }
                                    }
                                    else
                                    {
                                        $Acumula_Filtro.=$cCampo_Filtro." like '%".str_replace(array(" ","-"),"%",$Conteudo_Filtro)."%'";
                                    }

                                  }
                              } Else If ($Sinal=='!%') {
                                  $Acumula_Filtro.=$cCampo_Filtro." not like '%".$Conteudo_Filtro."%'";
                              } Else {
                                  $Acumula_Filtro.="(ifnull(".$cCampo_Filtro.",'') $Sinal '".$Conteudo_Filtro."' or $cCampo_Filtro $Sinal '".$Conteudo_Filtro."')" ;
                              }
                          }
                          else
                          {
                               $Acumula_Filtro.=$mCampo_Filtro[$nCC];
                          }
                     }
                     if (count($mCampo_Filtro)>1)
                     {
                        $Acumula_Filtro.=' ) ';
                     }

                 }
                 if (count($XmConteudo_Filtro)>1)
                 {
                    $Acumula_Filtro.=' ) ';
                 }


     }


}


include "../include/pasta_usuario.inc";

//-- DEFININDO ARQUIVO QUE GUARDARÁ OS DADOS
//-- $Diretorio_Usuario é definido no arquivo include/pasta_usuario.inc
$cArquivo=$Diretorio_Usuario."/".$cConfigNomeTelaFiltro.".php";

//--  ABRE O ARQUIVO DE FILTRO DO USUARIO
$ponteiro=fopen($cArquivo, "r");
$conteudo=fread($ponteiro, filesize($cArquivo));

//--  SEPARA O ARQUIVO EM LINHAS E GRAVA NA MATRIZ mLINHA
$mLinha=explode("\n", $conteudo);

//--  GRAVA QUANTAS POSICOES TEM A MATRIZ QUE FOI CRIADA
$nTotalLinhas=sizeof($mLinha);

$nLinha_Declaracao=0;

$cConteudo_Filtro=implode(",,",$mConteudo_Filtro);
$cSinal=implode(",,",$mSinal);

$mDados[]='<?php';
$mDados[]='$xConsulta_Conteudo_Filtro="'.$cConteudo_Filtro.'";';
$mDados[]='$xConsulta_Sinal_Filtro="'.$cSinal.'";';
$mDados[]='$xConsulta_Clausula_Filtro="'.$Acumula_Filtro.'";';
$mDados[]='$xConsulta_Mensagem_Filtro="'.$cMensagem_Filtro_Ativo.'";';
$mDados[]='$xConsulta_Ordem="'.$xConsulta_Ordem.'";';
$mDados[]='$xConsulta_Ordem_Desc="'.$xConsulta_Ordem_Desc.'";';
$mDados[]='$xConsulta_Order_By="'.$xConsulta_Order_By.'";';
$mDados[]='$xVariaveis_Filtro="'.$Variaveis_Filtro.'";';
$mVariaveis_Filtro=explode(';',$Variaveis_Filtro);
for ($i=0;$i<=count($mVariaveis_Filtro);$i++)
 {
     if (!empty($mVariaveis_Filtro[$i]))
     {
         $mDados[]=str_replace('@','$',"$mVariaveis_Filtro[$i];");
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
   if (!empty($cConfigURL))
   {
      echo "<script>parent.location.replace('$cConfigURL');</script>";
   }
   else
   {
      echo "<script>parent.ReloadTela();</script>";
   }
}

?>
