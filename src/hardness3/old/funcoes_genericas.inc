<?php
//echo "<script>alert('history.length = '+history.length);</script>";
/*
FUNCOES EM PHP
*/
define( "UC_CHARS", "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ<8c>Ø<8a>ÙÚÛÜÝ<8e>Þ$" );
define( "LC_CHARS", "àáâãäåæçèéêëìíîïðñòóôõö<9c>ø<9a>ùúûüý<9e>þ$" );
define( "ACENTOS" , "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ<8c>Ø<8a>ÙÚÛÜÝ<8e>Þàáâãäåæçèéêëìíîïðñòóôõö<9c>ø<9a>ùúûüý<9e>þº$" );
define( "NORMAL"  , "AAAAAAACEEEEIIIIDNOOOOO   UUUUY  aaaaaaaceeeeiiii nooooo   uuuuy  .$" );

// Busca os dados das configuracoes
$resultado=mysqli_query($con,"select * from C020 where C020_C004_Id='$Empresa_Atual'");
$mConfig=mysqli_fetch_array($resultado);

function TrocarCaracterEspecial($x)
{
      $x=StrToAcentos($x);
      $cCharPermitido="ABCDEFGHIJLKYMNOPQRSTUVXWZ 1234567890()[]{}.,+_-:/=%?;!@|$";
      for ($n = 0;$n<strlen($x);$n++)
      {
           $c=substr($x,$n,1);
           if (strpos($cCharPermitido,strtoupper($c))!==FALSE)
           {
              $r.=$c;
           }
           else
           {
              $r.=" ";
           }
      }
      $x=$r;
      return $x;
}

// Transforma uma String em minusculo, inclusive os caracteres especiais
function StrToAcentos($String) {
   // traduz os caracteres da constante definidas acima
   $S=strtr($String, ACENTOS, NORMAL);
   return ($S);
}

// Transforma uma String em minusculo, inclusive os caracteres especiais
function StrToMinusculo($String) {
   // traduz os caracteres da constante definidas acima
   $S=strtolower(strtr($String, UC_CHARS, LC_CHARS));
   return ($S);
}

// Transforma uma String em maiusculo, inclusive os caracteres especiais
function StrToMaiusculo($String) {
   // traduz os caracteres da constante definidas acima
   $S=strtoupper(strtr($String, LC_CHARS, UC_CHARS));
   return ($S);
}

// Transforma uma String em minusculo, inclusive os caracteres especiais
function StrToMaiusculoMinusculo($String) {
   $mString=explode(" ",$String);
   for ($x=0;$x<=count($mString);$x++) {
       $Letra1=substr($mString[$x],0,1);
       $Letra1=StrToMaiusculo($Letra1);
       $Letra2=substr($mString[$x],1,strlen($mString[$x])-1);
       $Letra2=StrToMinusculo($Letra2);
       $Frase=$Letra1.$Letra2;
       $Total.=' '.$Frase;
   }
   return trim($Total);
}

// FAZ A VALIDAÇÃO DA date E datetime
function validar_data($cData) {
   $mData[0]=substr($cData,6,4);
   $mData[1]=substr($cData,3,2);
   $mData[2]=substr($cData,0,2);
   if (!checkdate($mData[1],$mData[2],$mData[0])){
       return false;
   } else {
      return true;
   }
}

function validar_cnpj($cnpj) {
    if (strlen($cnpj) != 18) {
        return false;
    }

    $soma1 = ($cnpj[0] * 5) +
             ($cnpj[1] * 4) +
             ($cnpj[3] * 3) +
             ($cnpj[4] * 2) +
             ($cnpj[5] * 9) +
             ($cnpj[7] * 8) +
             ($cnpj[8] * 7) +
             ($cnpj[9] * 6) +
             ($cnpj[11] * 5) +
             ($cnpj[12] * 4) +
             ($cnpj[13] * 3) +
             ($cnpj[14] * 2);

    $resto = $soma1 % 11;
    $digito1 = $resto < 2 ? 0 : 11 - $resto;

    $soma2 = ($cnpj[0] * 6) +
            ($cnpj[1] * 5) +
            ($cnpj[3] * 4) +
            ($cnpj[4] * 3) +
            ($cnpj[5] * 2) +
            ($cnpj[7] * 9) +
            ($cnpj[8] * 8) +
            ($cnpj[9] * 7) +
            ($cnpj[11] * 6) +
            ($cnpj[12] * 5) +
            ($cnpj[13] * 4) +
            ($cnpj[14] * 3) +
            ($cnpj[16] * 2);
    $resto = $soma2 % 11;
    $digito2 = $resto < 2 ? 0 : 11 - $resto;
    return (($cnpj[16] == $digito1) && ($cnpj[17] == $digito2));
}

function validar_cpf($cpf) {
    $cpf=str_replace(".","",$cpf);
    $cpf=str_replace("-","",$cpf);
    $cpf=trim($cpf);
    $nulos = array("12345678909","11111111111","22222222222","33333333333",
                   "44444444444","55555555555","66666666666","77777777777",
                   "88888888888","99999999999","00000000000");
    /* Retira todos os caracteres que nao sejam 0-9 */
    $aux="";
    for ($i=1; $i<=strlen($cpf); $i++){
      $ch=substr($cpf,$i-1,1);
      if (ord($ch)>=48 && ord($ch)<=57){
        $aux=$aux.$ch;
      }
    }
    $cpf = $aux;
    /* Retorna falso se o cpf for nulo */
    foreach ( $nulos as $nulo ) {
      if($cpf == $nulo){
        return 0;
      }
    }
    /*Calcula o penúltimo dígito verificador*/
    $acum=0;
    for($i=0; $i<9; $i++) {
      $acum+= $cpf[$i]*(10-$i);
    }

    $x=$acum % 11;
    $acum = ($x>1) ? (11 - $x) : 0;
    /* Retorna falso se o digito calculado eh diferente do passado na string */
    if ($acum != $cpf[9]){
      return 0;
    }
    /*Calcula o último dígito verificador*/
    $acum=0;
    for ($i=0; $i<10; $i++){
      $acum+= $cpf[$i]*(11-$i);
    }

    $x=$acum % 11;
    $acum = ($x > 1) ? (11-$x) : 0;
    /* Retorna falso se o digito calculado eh diferente do passado na string */
    if ( $acum != $cpf[10]){
      return 0;
    }
    /* Retorna verdadeiro se o cpf eh valido */
    return 1;
}

// Formata o Valor Digitado Para Gravar Corretamente no Banco
function formata_valor_gravar($valor,$mil) {

    if (empty($mil)) { $mil=3; }

    if ($mil>0) {
        $Casas_Decimais=$mil-1;
    } else {
        $Casas_Decimais=0;
    }

    $cDecimal=substr($valor,(strlen($valor)-$mil),1);

    if ($cDecimal==',') {
       $cValor=str_replace(',','V',$valor);
       $cValor=str_replace('.','',$cValor);
       $cValor=str_replace(',','',$cValor);
       $cValor=str_replace('V','.',$cValor);
       return $cValor;
    } else if ($cDecimal=='.') {
       $cValor=str_replace(',','',$valor);
       return $cValor;
    } else {
       return $valor;
    }
}


// Retorna a Hora Atual (Servidor)
function hora() {
   $result=strftime('%T');
   return $result;

}


// Função generica, Troca o formado da data para dd-mm-aaaa
function Troca_Data_dma($data,$separador='/') {
    $mdata[0]=substr($data,8,2);
    $mdata[1]=substr($data,5,2);
    $mdata[2]=substr($data,0,4);
    if ((!empty($data)) and ($data!='0000-00-00')) {
       $result=implode($separador,$mdata);
       return $result;
    }
}
// Função generica, Troca o formado da datatime para dd-mm-aaaa hh:mm:ss 00-00-0000 00:00:00
function Troca_DataHora_dma($cData,$cSeparador='/') {
    $mData[0]=substr($cData,8,2); // ano
    $mData[1]=substr($cData,5,2); // mes
    $mData[2]=substr($cData,0,4); // dia
    $cHora=substr($cData,11,8);   // horas
    if ((!empty($cData)) and ($cData!='0000-00-00 00:00:00')) {
       $cResultado=implode($cSeparador,$mData).' '.$cHora;
       return $cResultado;
    }
}

// Função generica, Troca o formado da data para aaaa-mm-dd
function Troca_Data_amd($data) {
   if (empty($data)) {
      return;
   }
   if (substr($data,0,10)=='00-00-0000') {
      return;
   }


   $mdata[0]=substr($data,6,4);
   $mdata[1]=substr($data,3,2);
   $mdata[2]=substr($data,0,2);

   if (!checkdate($mdata[1],$mdata[2],$mdata[0])){
       return "Data Inválida";
       exit;
   }

   $result=implode("-",$mdata);
   return $result;
}

// Função generica, Troca o formado da datatime para aaaa-mm-dd 0000-00-00 00:00:00
function Troca_DataHora_amd($cData) {
   if (empty($cData)) {
      return;
   }
   if ($cData=='0000-00-00 00:00:00') {
      return;
   }

   $mData[0]=substr($cData,6,4);  // Ano
   $mData[1]=substr($cData,3,2);  // Mes
   $mData[2]=substr($cData,0,2);  // Dia
   $cHora=substr($cData,11,8);    // horas


   if (!checkdate($mData[1],$mData[2],$mData[0])){
       msg_alerta('Data-Hora Inválida: '.$cData);
       exit;
   }
   if ((empty($cHora)) or ($cHora=='00:00:00')) {
       msg_alerta('Hora Inválida: '.$cHora);
       exit;
   }
   $cResultado=implode("-",$mData).' '.$cHora;
   return $cResultado;
}



// Retorna a Data no formato ANO/MES/DIA
function data_amd() {
    $data[0]=strftime('%Y');
    $data[1]=strftime('%m');
    $data[2]=strftime('%d');
    $result=implode("-",$data);
    return $result;
}

// Retorna a Data atual no formato DIA/MES/ANO
function hoje($Intervalo_Dias=0) {

    $Dia=date("d",time())+$Intervalo_Dias;
    $cHoje=date("d/m/Y",mktime(0, 0, 0, date("m",time()), $Dia, date("Y",time())));

    return $cHoje;
}

// Retorna a Data e Hora atual no formato DIA/MES/ANO HORA/MINUTOS/SEGUNDOS
function agora() {
    $cHoje=date("d/m/Y h:i:s",time());
    return $cHoje;
}

include "hardness67.php";

function formatar_cnpj($cCnpj) {
  return($cCnpj);
}

///////////////////////////////////////////////////////////////////////////////////////////
// funcoes SQL
///////////////////////////////////////////////////////////////////////////////////////////

function sql_fetch_array($cResultado) {
   return mysql_fetch_array($cResultado);
}

function sql_fetch_field($cResultado) {
   return mysql_fetch_field($cResultado);
}

function sql_num_rows($cResultado) {
   return mysql_num_rows($cResultado);
}

function mostrar($Texto)
{
     if (empty($Texto))
     {
        return "&nbsp;";
     }
     else
     {
        return $Texto;
     }
}

function mostrar1($Texto)
{
     if (empty($Texto))
     {
        return "-";
     }
     else
     {
        return $Texto;
     }
}

function sql_query($cClausulaSql,$cMostra_Msg='S',$cAutoVoltar='S',$Exec_Formula='S') {

   // $cMostra_Msg => "S" - Mostra a mensagem ao incluir / alterar / excluir um registro
   // $cAutoVoltar => "N" - executa o history.back() após mostrar a mensagem.

   global $Usuario_Atual;
   global $Pasta_Empresa;
   global $Empresa_Atual; //-- Id da Empresa Atual

   $cTipo=substr(strtoupper($cClausulaSql),0,6);

   if ($cTipo=='DELETE') {
      /////////////////////////////////////////////////////////////////////////////////////////////////
      //-- DELETE
      /////////////////////////////////////////////////////////////////////////////////////////////////

      $Entidade=substr($cClausulaSql,12,4);

      $Condicao=trim(substr($cClausulaSql,strpos(strtoupper($cClausulaSql),"WHERE"),strlen($cClausulaSql)));

      // OBTEM O ID USADO NO UPDATE
      $cSql_Delete=mysql_query("select * from $Entidade $Condicao");
      $mResultado=mysql_fetch_array($cSql_Delete);

      $cComando='$nUltimo_Id_Incluso_Funcao="$mResultado['.$Entidade.'_Id]";';
      eval("$cComando");
      $cComando='$'.$Entidade.'_Id="$mResultado['.$Entidade.'_Id]";';
      eval("$cComando");

      if (file("../empresas/$Pasta_Empresa/comp/$Entidade/$Entidade"."_regras_excluir_antes.inc")) {
         include "../empresas/$Pasta_Empresa/comp/$Entidade/$Entidade"."_regras_excluir_antes.inc";
      }

      Totalizar_EP($mRegras_Totalizar,$mRegras_Condicao,$nUltimo_Id_Incluso_Funcao,$cClausulaSql,$nUltimo_Id_Incluso_Funcao);

   }

   $Resultado=mysql_query($cClausulaSql);

   // QUANDO FOR UM INSERT PEGA O ID QUE FOI CRIADO
   if ($cTipo=='INSERT') {
      $nUltimo_Id_Incluso_Funcao=mysql_insert_id();
   }

   $erro=mysql_error();
   if (!empty($erro)) {
      $erro=str_replace("'","",$erro);
      $erro=str_replace('"','',$erro);
      $erro=addslashes($erro);
      $cClausulaSql=str_replace("'","",$cClausulaSql);
      $cClausulaSql=str_replace('"','',$cClausulaSql);
      $cClausulaSql=addslashes($cClausulaSql);

      $Assunto="ERRO NA SQL QUERY";
      // envia o e-mail para o suporte
      msg_erro($erro,$cClausulaSql,$Assunto);
      exit;

   } else {

     ////////////////////////////////////////////////////
     // EXECUTA A REGRA DOS ATRIBUTOS CALCULADOS
     ////////////////////////////////////////////////////

     if ((($cTipo=='UPDATE') or ($cTipo=='INSERT'))
         and ($Exec_Formula=='S')) {

        if ($cTipo=='UPDATE') {

           $Entidade=substr($cClausulaSql,7,4);
           $Condicao=trim(substr($cClausulaSql,strpos(strtoupper($cClausulaSql),"WHERE"),strlen($cClausulaSql)));

           // OBTEM O ID USADO NO UPDATE
           $cSql_Update=mysql_query("select $Entidade"."_Id from $Entidade $Condicao");
           $mIdUpdate=mysql_fetch_array($cSql_Update);

           $nUltimo_Id_Incluso_Funcao="$mIdUpdate[0]";


        } else if ($cTipo=='INSERT') {

           // Encontra o Nome da Entidade
           $Entidade=substr($cClausulaSql,12,4);
           $Condicao="where $Entidade"."_Id='$nUltimo_Id_Incluso_Funcao'";


        }

        //////////////////////////////////////////////////////////////////////////////
        // ARQUIVO DE REGRAS DE CALCULOS DA ENTIDADE
        //////////////////////////////////////////////////////////////////////////////
        $Arquivo_Regra_Calculos=DIR_WWW."/empresas/$Pasta_Empresa/comp/$Entidade/$Entidade"."_regras_calculos.inc";

        if (file_exists($Arquivo_Regra_Calculos)) {

           // "COMPONENTE_ATUAL" - VARIAVEL QUE SERA USADA PARA IDENTIFICAR AONDE ESTA SENDO "DADO" O INCLUDE EM REGRAS_CALCULOS
           $Componente_Atual="SQL_QUERY";

           include $Arquivo_Regra_Calculos;

           $cAcumulaComando='';
           for ($u=0;$u<count($mRegra_Grava_Formula);$u++) {
               if (!empty($cAcumulaComando)) {
                  $cAcumulaComando.=', ';
               }
               $cAcumulaComando.=$mRegra_Grava_Formula[$u];
           }


           if (!empty($cAcumulaComando)) {

              $cClausula_Sql_Regra="update $Entidade set $cAcumulaComando $Condicao";

              mysql_query($cClausula_Sql_Regra);

              $erro=mysql_error();
              if (!empty($erro)) {
                 $erro=str_replace("'","",$erro);
                 $erro=str_replace('"','',$erro);
                 $erro=addslashes($erro);
                 $cClausula_Sql_Regra=str_replace("'","",$cClausula_Sql_Regra);
                 $cClausula_Sql_Regra=str_replace('"','',$cClausula_Sql_Regra);
                 $cClausula_Sql_Regra=addslashes($cClausula_Sql_Regra);

                 $Assunto="ERRO NA SQL QUERY DE FORMULA";
                 // envia o e-mail para o suporte
                 msg_erro($erro,$cClausula_Sql_Regra,$Assunto);
                 exit;

              }
           }
           Totalizar_EP($mRegras_Totalizar,$mRegras_Condicao,$nUltimo_Id_Incluso_Funcao,$cClausulaSql);

        }
     }

     /////////////////////////////////////////////////////////////////////
     // GRAVA O LOG
     /////////////////////////////////////////////////////////////////////
     if ($cTipo=='UPDATE') {

        $Log_Entidade=substr($cClausulaSql,7,4);
        $Condicao=trim(substr($cClausulaSql,strpos(strtoupper($cClausulaSql),"WHERE"),strlen($cClausulaSql)));

        // OBTEM O ID USADO NO UPDATE
        $cSql_Log=mysql_query("select $Log_Entidade"."_Id from $Log_Entidade $Condicao");
        //$mIdUpdate=mysql_fetch_array($cSql_Log);

        $Log_Chave_Primaria="$mIdUpdate[0]";
        $Log_Operacao="2";

     } else if ($cTipo=='INSERT') {

        $Log_Entidade=substr($cClausulaSql,12,4);
        $Log_Chave_Primaria="$nUltimo_Id_Incluso_Funcao";
        $Log_Operacao="1";
        //include "../monta/salvar_log.php3";

     } else if ($cTipo=='DELETE') {

        $Log_Entidade=substr($cClausulaSql,12,4);
        $Log_Chave_Primaria="$nUltimo_Id_Incluso_Funcao";
        $Log_Operacao="3";
        //include "../monta/salvar_log.php3";

     }

     if ($cMostra_Msg=='S') {

        if (($cTipo=='INSERT') and ($Resultado>0)) {
            echo '<script>
                    alert("Registro incluido com sucesso!");
                  </script>';
        }
        if (($cTipo=='UPDATE') and ($Resultado>0)) {
            echo '<script>
                    alert("Registro alterado com sucesso!");
                  </script>';
        }
        if (($cTipo=='DELETE') and ($Resultado>0)) {
            echo '<script>
                    alert("Registro excluído com sucesso!");
                  </script>';
        }

     }

     if ($cAutoVoltar=='S') {
        if (($cTipo=='INSERT') and ($Resultado>0)) {
            echo '<script>
                    parent.location.reload();
                  </script>';
        }
        if (($cTipo=='UPDATE') and ($Resultado>0)) {
            echo '<script>
                    parent.voltar();
                  </script>';
        }
        if (($cTipo=='DELETE') and ($Resultado>0)) {
            echo '<script>
                    parent.location.reload();
                  </script>';
        }

     }

   }
   if ($cTipo=='INSERT') {
       return $nUltimo_Id_Incluso_Funcao;
   } else {
       return $Resultado;
   }
}
//////////////////////////////////////////////////////////////////////////////////////////
// TOTALIZA A ENTIDADE PRINCIPAL
//////////////////////////////////////////////////////////////////////////////////////////
function Totalizar_EP($mRegras_Totalizar=array(),$mRegras_Condicao=array(),$Id_Entidade,$cClausulaSql,$Id_Excluido=0) {
   if (!isset($mRegras_Totalizar)) {
       return;
   }

   $mKeys=array_keys($mRegras_Totalizar);

   if ($Id_Excluido>0) {
       $mRegras_Condicao[$mKeys[$x]].=" and $Entidade"."_Id!='".$Id_Excluido."' ";
   }

   for ($x=0; $x<count($mKeys); $x++) {

       // SE O CAMPO A SER TOTALIZADO ESTIVER NA CONSULTAS, FAÇA A TOTALIZACAO DA ENTIDADE PRINCIPAL

       if (strpos($cClausulaSql,$mRegras_Totalizar[$mKeys[$x]])===FALSE) {
          continue;
       }


       // EXTRAIO A ENTIDADE PRINCIPAL
       $Entidade_Principal=substr($mKeys[$x],0,4);

       // EXTRAIO A ENTIDADE
       $Entidade=substr($mRegras_Totalizar[$mKeys[$x]],0,4);

       // FAÇO A CONSULTA NA ENTIDADE BUSCANDO O ID DA ENTIDADE PRINCIPAL
       $cSql_Entidade="select $Entidade"."_$Entidade_Principal"."_Id Id_Entidade_Principal ";
       $cSql_Entidade.="from $Entidade ";
       $cSql_Entidade.="where $Entidade"."_Id='$Id_Entidade' ";

       $cSql_Entidade=sql_query($cSql_Entidade);
       $mEntidade=sql_fetch_array($cSql_Entidade);


       // FAÇO A CONSULTA NA ENTIDADE EFETUANDO A SOMATORIA DO CAMPO
       $cSql_Entidade="select sum(".$mRegras_Totalizar[$mKeys[$x]].") Total, ";
       $cSql_Entidade.="$Entidade"."_$Entidade_Principal"."_Id Id_Entidade_Principal ";
       $cSql_Entidade.="from $Entidade ";
       $cSql_Entidade.="where $Entidade"."_$Entidade_Principal"."_Id='".$mEntidade[Id_Entidade_Principal]."' ";

       $cSql_Entidade.=$mRegras_Condicao[$mKeys[$x]];
       $cSql_Entidade.=" group by Id_Entidade_Principal ";
       $cSql_Entidade=sql_query($cSql_Entidade);


       if (sql_num_rows($cSql_Entidade)>0) {
          $mEntidade=sql_fetch_array($cSql_Entidade);

	  unset($mGravar_Atributos);
          $mGravar_Atributos[$mKeys[$x]]="$mEntidade[Total]";

          if (empty($mEntidade[Id_Entidade_Principal])) {
             msg_erro("Erro ao totalizar a entidade principal: $Entidade_Principal - ID ESTÁ EM BRANCO");
          } else {
             gravar_entidade($mGravar_Atributos,$Entidade_Principal,$mEntidade[Id_Entidade_Principal],'','','','MANUAL');
          }
       }

   }
}

function diasem($data) {
	$ano =  substr("$data", 0, 4);
	$mes =  substr("$data", 5, -3);
	$dia =  substr("$data", 8, 9);

	$diasemana = date("w", mktime(0,0,0,$mes,$dia,$ano) );

 	switch($diasemana) {
		case"0": $diasemana = "Domingo";   break;
		case"1": $diasemana = "Segunda-feira";   break;
		case"2": $diasemana = "Terça-feira";   break;
		case"3": $diasemana = "Quarta-feira";   break;
		case"4": $diasemana = "Quinta-feira";   break;
		case"5": $diasemana = "Sexta-feira";   break;
		case"6": $diasemana = "Sábado";  break;
	}

	return "$diasemana";
}

function msg_erro($cErro="",$cSql="",$cAssunto="MENSAGEM DE ERRO",$cTipo_Erro="",$cComponente="",$nLinha="") {
  global $Usuario_Suporte;
  if (empty($cComponente)) {
     // Pega o nome do Componente Atual
     $cComponente=$_SERVER['HTTP_REFERER'];
  }

  $enviou=enviar_email_erro($cErro,$cSql,$cAssunto,$cTipo_Erro,$cComponente,$nLinha);
  $enviou=1;

  echo '<script>
          alert("Desculpe, o sistema gerou um erro inesperado e sua operação não foi executada.\n';

          if (($Usuario_Suporte!='S') and ($enviou)) {
             echo 'Este erro foi enviado aos administradores do sistema, em breve será corrigido, se necessário entre em contato com o Departamento de Informática.\n';
          } else {
             echo '\nESTE ERRO NãO FOI ENVIADO AOS ADMINISTRADORES DO SISTEMA\n\n';
          }
          if (!empty($cErro)) {
             echo '\nMensagem do erro: '.$cErro;
          }
          if (!empty($cSql)) {
             echo '\nClausula: '.$cSql;
          }
          if (!empty($cTipo_Erro)) {
             echo '\nTipo do Erro: '.$cTipo_Erro;
          }
          if (!empty($cComponente)) {
             echo '\nComponente: '.$cComponente;
          }
          if (!empty($nLinha)) {
             echo '\nLinha: '.$nLinha;
          }

          echo '");
        </script>';
  echo "<font style='font-family:courier new; font-size:12;color:red'>Desculpe, o sistema gerou um erro inesperado e sua operação não foi executada.<br>";
  if (($Usuario_Suporte!='S') and ($enviou)) {
     echo "Este erro foi enviado aos administradores do sistema, em breve será corrigido, se necessário entre em contato com o Departamento de Informática.<BR><BR></font>";
  } else {
     echo 'ESTE ERRO NãO FOI ENVIADO AOS ADMINISTRADORES DO SISTEMA<br><br></font>';
  }

  if (!empty($cErro)) {
     echo "<font style='font-family:courier new; font-size:12;color:black'>ERRO........: $cErro</font><BR>";
  }
  if (!empty($cSql)) {
     echo "<font style='font-family:courier new; font-size:12;color:black'>CLAUSULA....: $cSql</font><BR>";
  }
  if (!empty($cTipo_Erro)) {
     echo "<font style='font-family:courier new; font-size:12;color:black'>TIPO DO ERRO: $cTipo_Erro</font><BR>";
  }
  if (!empty($cComponente)) {
     echo "<font style='font-family:courier new; font-size:12;color:black'>COMPONENTE..: $cComponente</font><BR>";
  }
  if (!empty($nLinha)) {
     echo "<font style='font-family:courier new; font-size:12;color:black'>LINHA.......: $nLinha</font><BR>";
  }

}

function enviar_email_erro($Erro="",$Sql="",$Assunto="",$Tipo_Erro="",$Componente="",$Linha="") {
      global $Nome_Usuario;
      global $Usuario_Suporte;
      global $Banco_Dados;
      if (StrtoUpper($Tipo_Erro)=='WARNING') {
         return false;
      }
      if (empty($cComponente)) {
         // Pega o nome do Componente Atual
         $cComponente=$_SERVER['PHP_SELF'];
      }

      $Data=date("d/m/Y",time());
      $Hora_Inclusao=date("H:i",time());

      $cAssunto.=' - '.$Data.' - '.$Hora_Inclusao;

      // envia o e-mail para o suporte
      $Erro=str_replace(trim("\\ "),"",$Erro);

      $Mensagem="<html>";
      $Mensagem.="<head>";
        $Mensagem.="<title>$Assunto</title>";
      $Mensagem.="</head>";
      $Mensagem.="<body>";
      $Mensagem.="<table width=100% bgcolor=f4f4f4 cellspacing=0 cellpadding=2>";
        $Mensagem.="<tr>";
          $Mensagem.="<td colspan=2>";
            $Mensagem.="<font style='font-family:Arial;font-size:20;color:gray;'>SUPORTE HARDNESS</font>";
          $Mensagem.="</td>";
        $Mensagem.="</tr>";
        $Mensagem.="<tr bgcolor=white>";
          $Mensagem.="<td align=center colspan=2><hr></td>";
        $Mensagem.="</tr>";
        if (!empty($Banco_Dados)) {
           $Mensagem.="<tr bgcolor=white>";
             $Mensagem.="<td align=right><i>Banco de dados:</i></font></td>";
             $Mensagem.="<td align=left>$Banco_Dados</font></td>";
          $Mensagem.="</tr>";
        }
        if (!empty($Nome_Usuario)) {
           $Mensagem.="<tr bgcolor=white>";
             $Mensagem.="<td align=right><i>Usuário:</i></font></td>";
             $Mensagem.="<td align=left>$Nome_Usuario</font></td>";
          $Mensagem.="</tr>";
        }
        if (!empty($Erro)) {
           $Mensagem.="<tr bgcolor=white>";
             $Mensagem.="<td align=right><i>Mensagem do Erro:</i></font></td>";
             $Mensagem.="<td align=left>$Erro</font></td>";
           $Mensagem.="</tr>";
        }
        if (!empty($Tipo_Erro)) {
           $Mensagem.="<tr bgcolor=white>";
             $Mensagem.="<td align=right><i>Tipo do Erro:</i></font></td>";
             $Mensagem.="<td align=left>$Tipo_Erro</font></td>";
           $Mensagem.="</tr>";
        }
        if (!empty($Componente)) {
           $Mensagem.="<tr bgcolor=white>";
             $Mensagem.="<td align=right><i>Componente:</i></font></td>";
             $Mensagem.="<td align=left>$Componente</font></td>";
           $Mensagem.="</tr>";
        }
        if ($Linha>0) {
           $Mensagem.="<tr bgcolor=white>";
             $Mensagem.="<td align=right><i>Linha:</i></font></td>";
             $Mensagem.="<td align=left>$Linha</font></td>";
           $Mensagem.="</tr>";
        }
        if (!empty($Sql)) {
           $Sql=str_replace(",",",<BR>",$Sql);
           $Sql=str_replace("from","<BR>from",$Sql);
           $Sql=str_replace("left join","<BR>left join",$Sql);
           $Sql=str_replace("where","<BR>where",$Sql);
           $Sql=str_replace("order","<BR>order",$Sql);
           $Sql=str_replace("group","<BR>group",$Sql);
           $Sql=str_replace(trim("\\ "),"",$Sql);
           $Mensagem.="<tr bgcolor=white>";
             $Mensagem.="<td align=center colspan=2><hr></td>";
           $Mensagem.="</tr>";
           $Mensagem.="<tr bgcolor=white>";
             $Mensagem.="<td align=left colspan=2><i>CLAUSULA SQL</i></font></td>";
           $Mensagem.="</tr>";
           $Mensagem.="<tr bgcolor=white>";
             $Mensagem.="<td align=left colspan=2>$Sql</font></td>";
           $Mensagem.="</tr>";
        }
      $Mensagem.="</table>";
      $Mensagem.="</body>";
      $Mensagem.="</html>";

      $Mensagem=str_replace("'","",$Mensagem);
      //if ($Usuario_Suporte!='S')
      //{

           echo "<form name='formulario_error' id='formulario_error' target='_self' method='post' action='http://www.hardness.com.br/enviar_email_erro.php'>
                   <input type='hidden' name='Para' id='Para'  value='evantoni@gmail.com,marcelo@hardness.com.br'>
                   <input type='hidden' name='Mensagem' id='Mensagem' value='".htmlspecialchars($Mensagem)."'>
                   <input type='hidden' name='Assunto' id='Assunto' value='$Assunto'>
                 </form>";

           echo "<script>
                   document.getElementById('formulario_error').submit();
                 </script>";
      //}
      return $enviou;

}

function msg_alerta($Msg,$Tipo='S') {
      $Msg2=$Msg;
      $Msg=str_replace('/p','\n',$Msg);
      ?>
        <script>
        alert("ATENÇÃO!!\n\n<?php echo $Msg; ?>");
        </script>
      <?php
      if ($Tipo=='S') {
         $Msg2=str_replace('/p','<BR>',$Msg2);
         $Msg2=str_replace('\t','',$Msg2);
//         echo "<font color=red>$Msg2</font>";
      }
}

function msg_alerta1($Msg,$Tipo='S') 
{
      $Msg2=$Msg;
      echo "<script>
               alert('ATENÇÃO!!\n\n$Msg');
            </script>";
}

//////////////////////////////////////////////////////////////////////////////////////
// BUSCA O NOME DA ENTIDADE EM DADOS ENTIDADE
//////////////////////////////////////////////////////////////////////////////////////
function Pega_Nome_Entidade($cEntidade,$Codigo_Consulta='001') {
      Global $Pasta_Empresa;
      $cArquivo_Entidade="../empresas/$Pasta_Empresa/comp/$cEntidade/dados_entidade_$Codigo_Consulta.hardness";
      if (file_exists($cArquivo_Entidade)) {
         // ABRE O ARQUIVO EXTERNO
         $cPonteiro=fopen($cArquivo_Entidade, "r");
         $cConteudo=fread($cPonteiro, filesize($cArquivo_Entidade));

         // SEPARA O ARQUIVO EM LINHAS E GRAVA NA MATRIZ mLINHA
         $mLinha=explode("\n", $cConteudo);

         // GRAVA QUANTAS POSICOES TEM A MATRIZ QUE FOI CRIADA
         $nTotalLinhas=sizeof($mLinha);

         $cEntidade='';
         for($i = 0; $i <= $nTotalLinhas; $i++) {
             if (strpos(trim($mLinha[$i]),'$mDados_Entidade[Entidade_Formulario]=')!==FALSE) {
                $nPos_Inicial=strpos($mLinha[$i],'"');
                $nPos_Final=strpos($mLinha[$i],'";');
                $cNome_Entidade=substr($mLinha[$i],$nPos_Inicial+1,$nPos_Final-$nPos_Inicial-1);
                return $cNome_Entidade;
                break;
             }
         }
      } Else {
        return ;
      }
}

//////////////////////////////////////////////////////////////////////////////////////////
// VERIFICA SE A VARIAVEL EXISTE
//////////////////////////////////////////////////////////////////////////////////////////
function hisset($variavel) {
   if (!isset($variavel)) {
      return NULL;
   }
   return $variavel;
}

function Busca_D008($D008_Codigo_Original) {
   return sql_query("select D008_Id from D008
               where replace(replace(replace(replace(D008_Codigo_Original,'/',''),'-',''),'.',''),' ','')
                    =replace(replace(replace(replace('$D008_Codigo_Original','/',''),'-',''),'.',''),' ','')");
}

function Pega_Navegador() {
   $curos=strtolower($_SERVER['HTTP_USER_AGENT']);
   if (strstr($curos,"gecko")) {
      if (strstr($curos,"safari")) {
         $bos="Safari";
      } else if (strstr($curos,"camino")) {
         $bos="Camino";
      } else if (strstr($curos,"firefox")) {
         $bos="Firefox";
      } else if (strstr($curos,"netscape")) {
         $bos="Netscape";
      } else {
         $bos="Mozilla";
      }
   } else if (strstr($curos,"opera")) {
      $bos="Opera";
   } else if (strstr($curos,"msie")) {
      $bos="Internet Exploder";
   } else if (strstr($curos,"voyager")) {
      $bos="Voyager";
   } else if (strstr($curos,"lynx")) {
      $bos="Lynx";
   } else {
      $bos="[?]EgzoticalBrowser";
   }
   return ($bos);
}


function Inicio_Semana($cDia,$cMes,$cAno) {
   // -- Encontra a data da Segunda Feira anterior
   For ($nS=0;$nS<8;$nS++) {
       If (date("w",mktime(0, 0, 0, $cMes, $cDia, $cAno))=='1') {
          break;
       }
       $cDia--;
   }
   return date("Y-m-d",mktime(0, 0, 0, $cMes, $cDia, $cAno));
}

function SemanaMes($cData) {

   list($cAno_Atual , $cMes_Atual,  $cDia_Atual )=explode("-",$cData);
   list($cAno_Inicio, $cMes_Inicio,  $cDia_Inicio )=explode("-",$cData);

   $cDia_Inicio=1;

   //-- Monta Matriz com a data de inicio e data do final da semana

   For ($nS=0;$nS<8;$nS++) {

       $mData_Inicio[$nS]=Inicio_Semana($cDia_Inicio,$cMes_Inicio,$cAno_Inicio);
       list($cAno_Inicio, $cMes_Inicio,  $cDia_Inicio )=explode("-",$mData_Inicio[$nS]);

       $cDia_Inicio=$cDia_Inicio+7;
       $mData_Final[$nS]=date("Y-m-d",mktime(0, 0, 0, $cMes_Inicio, $cDia_Inicio, $cAno_Inicio));
       If (mktime(0, 0, 0, $cMes_Inicio, $cDia_Inicio, $cAno_Inicio)>mktime(0, 0, 0, $cMes_Atual, $cDia_Atual, $cAno_Atual)) {
           break;
       }


   }
   $cData=str_replace("-","",$cData);
   For ($nS=0;$nS<count($mData_Inicio);$nS++) {
        $mData_Inicio[$nS]=str_replace("-","",$mData_Inicio[$nS]);
        $mData_Final[$nS]=str_replace("-","",$mData_Final[$nS]);
        If (($cData>=$mData_Inicio[$nS]) and ($cData<$mData_Final[$nS])) {
           return $nS+1;
        }
   }

}


function Pega_Dados_Entidade_Principal($Nome_Arquivo) {
     include $Nome_Arquivo;
     return $mConfConsulta;
}

if (strpos($_SERVER['REQUEST_URI'],"monta_exportar_excel.php3")===false && empty($Pular_Script)) {
   $mPasta=explode("/",$_SERVER['REQUEST_URI']);
   ///////////////////////////////////////////////////////////////////////////////////
   // -- VOLTAR
   ///////////////////////////////////////////////////////////////////////////////////
   echo "<iframe id='Fme_Voltar' style='display:none;'></iframe>";
   echo "<script>
           function voltar(nUrl_Anterior,nNivel_Parent) {
                  document.getElementById('Fme_Voltar').src='/$mPasta[1]/principal/voltar.php3?nUrl_Anterior='+nUrl_Anterior+'&nNivel_Parent='+nNivel_Parent;
           }
         </script>";
}
function gravar_linha($Arquivo,$String) {
   if (is_writable($Arquivo)) {
      // Abrindo $filename em modo de append (acréscimo).
      // O ponteiro do arquivo estará no final do arquivo
      if (!$handle = fopen($Arquivo, 'a')) {
         msg_alerta("Erro abrindo arquivo: $Arquivo");
         exit;
      }
      // Escrevendo para o arquivo aberto.
      if (!fwrite($handle, $String)) {
          msg_alerta("Erro escrevendo no arquivo $Arquivo");
          exit;
      }
      fclose($handle);
   } else {
      msg_alerta("O arquivo $Arquivo não tem permissão para escrita");
      exit;
   }
}

/////////////////////////////////////////////////////////////////////////////////////////////////
//-- Função QUE RETORNA O NUMERO POR EXTENSO
/////////////////////////////////////////////////////////////////////////////////////////////////
// EXEMPLOS:
// echo extenso("12428.12"); //retorna: doze mil, quatrocentos e vinte e oito reais e doze centavos
//
// ou use:
//    echo extenso("12428.12", true); //esta linha retorna: Doze Mil, Quatrocentos E Vinte E Oito Reais E Doze Centavos
//    sa�da..: string com $valor por extenso em reais e pode ser com iniciais em mai�sculas (true) ou não.
//
//    tamb�m � poss�vel passar o valor para a funcao com a v�irgula decimal.
//    exemplo: echo extenso("12428,12"); // o retorna � o mesmo que a passagem com ponto decimal
//
//    Colocando a op��o do resultado ser em CAIXA ALTA. Setar 2 em $maiusculas.
//    exemplo: echo extenso("12428,12", 2); // retorna o texto em caixa alta


function extenso($valor=0, $maiusculas=false) {
    // verifica se tem virgula decimal
    if (strpos($valor,",") > 0) {
      // retira o ponto de milhar, se tiver
      $valor = str_replace(".","",$valor);

      // troca a virgula decimal por ponto decimal
      $valor = str_replace(",",".",$valor);
    }

      $singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
      $plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");

      $c = array("", "cem", "duzentos", "trezentos", "quatrocentos","quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
      $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta","sessenta", "setenta", "oitenta", "noventa");
      $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze","dezesseis", "dezesete", "dezoito", "dezenove");
      $u = array("", "um", "dois", "três", "quatro", "cinco", "seis","sete", "oito", "nove");

      $z=0;

      $valor = number_format($valor, 2, ".", ".");
      $inteiro = explode(".", $valor);
      for($i=0;$i<count($inteiro);$i++)
              for($ii=strlen($inteiro[$i]);$ii<3;$ii++)
                      $inteiro[$i] = "0".$inteiro[$i];

      $fim = count($inteiro) - ($inteiro[count($inteiro)-1] > 0 ? 1 : 2);
      for ($i=0;$i<count($inteiro);$i++) {
              $valor = $inteiro[$i];
              $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
              $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
              $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

              $r = $rc.(($rc && ($rd || $ru)) ? " e " : "").$rd.(($rd &&$ru) ? " e " : "").$ru;
              $t = count($inteiro)-1-$i;
              $r .= $r ? " ".($valor > 1 ? $plural[$t] : $singular[$t]) : "";
              if ($valor == "000")$z++; elseif ($z > 0) $z--;
              if (($t==1) && ($z>0) && ($inteiro[0] > 0)) $r .= (($z>1) ? " de " : "").$plural[$t];
              if ($r) $rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
      }

        if(!$maiusculas){
            return($rt ? $rt : "zero");
        } elseif($maiusculas == "2") {
            return (strtoupper($rt) ? strtoupper($rt) : "Zero");
        } else {
            return (ucwords($rt) ? ucwords($rt) : "Zero");
        }

}

function dateDiff($interval,$dateTimeBegin,$dateTimeEnd) {

    $dateTimeBegin=troca_data_amd($dateTimeBegin);
    $dateTimeEnd=troca_data_amd($dateTimeEnd);

    $dateTimeBegin=strtotime($dateTimeBegin);
    if($dateTimeBegin === -1) {
      return("..data inicial invalida");
    }

    $dateTimeEnd=strtotime($dateTimeEnd);
    if($dateTimeEnd === -1) {
      return("..Data final invalida");
    }

    $dif=$dateTimeEnd - $dateTimeBegin;

    switch($interval) {
      case "s":
          //seconds
          return($dif);

      case "n":
          //minutes
          return(floor($dif/60));
          //60s=1m

      case "h":
          //hours
          return(floor($dif/3600));
          //3600s=1h

      case "d":
          //days
          return(floor($dif/86400));
          //86400s=1d

      case "ww":
          //Week
          return(floor($dif/604800));
          //604800s=1week=1semana

      case "m":
          //similar result "m" dateDiff Microsoft
          $monthBegin=(date("Y",$dateTimeBegin)*12)+
            date("n",$dateTimeBegin);
          $monthEnd=(date("Y",$dateTimeEnd)*12)+
            date("n",$dateTimeEnd);
          $monthDiff=$monthEnd-$monthBegin;
          return($monthDiff);


      case "yyyy":
            //similar result "yyyy" dateDiff Microsoft
          return(date("Y",$dateTimeEnd) - date("Y",$dateTimeBegin));

      default:
          return(floor($dif/86400));
          //86400s=1d
    }

}

function D001_reprocessa_historico($D001_Id=0,$D009_Id=0) 
{

    global $Empresa_Atual;
                     
    $cSql_D009=mysql_query("select D009_Id,
                                   D009_Quantidade_Estoque,
                                   D009_Valor_Custo_Medio_Unitario,
                                   D009_Valor_Custo_Compra,
                                   D009_Valor_Melhor_Custo,
                                   D049_Flag_Nacional_Importado,
                                   D049_Flag_Tipo,
                                   D049_Valor_Preco_Tabela,
                                   D049_Percentual_Desconto_Tabela,
                                   D082_Percentual_Acrescimo,
                                   D049_Aliquota_ICMS_Tabela,
                                   D049_Id,
                                   D049_D001_Id,
                                   D049_ICF,
                                   D049_IPV,
                                   D009_C004_Id,
                                   D009_D049_Id,
                                   C008_Flag_Custo
                              from D009 as T1
                          left join D049 on D049_Id=D009_D049_Id
                          left join D001 on D001_Id=D049_D001_Id
                          left join C008 on C008_Id=D001_C008_Id
                          left join D082 on D082_Id=D049_D082_Id
                              where (D049_D001_Id='$D001_Id'
                                 or D009_Id='$D009_Id')
                                and D009_C004_Id='$Empresa_Atual'");
                                
    $C020=mysql_query("select * from C020 where C020_C004_Id='$Empresa_Atual'");
    $mC020=mysql_fetch_array($C020);
    
    if ($mC020[C020_IPV_Custo]<=0)
    {
        $mC020[C020_IPV_Custo]=1;
    }
    
    $C004_Matriz=mysql_query("select C004_Id from C004 where C004_Matriz='1'");
    $mC004_Matriz=mysql_fetch_array($C004_Matriz);

    if (mysql_num_rows($cSql_D009)<=0)
    {
    	echo "<script>alert('O produto não foi encontrado: $D001_Id / $D009_Id');</script>";
    	return NULL;
    }

    while ($mD009=mysql_fetch_array($cSql_D009)) 
    {

            $Total_Produtos++;
            $cSql_T001=mysql_query("select T001_Quantidade,
                                           T001_Flag_Operacao,
                                           T001_Flag_Atualiza_Custo,
                                           T001_Quantidade_Saldo,
                                           T001_Valor_Custo_Unitario,
                                           T001_Valor_Custo_Medio_Unitario,
                                           T001_Id,
                                           T001_T066_Id,
                                           T001_Flag_MMF,
                                           T001_T008_Id,
                                           T001_Descricao_Operacao,
                                           T008_Valor_Custo_Unitario,
                                           T008_Valor_Preco_Unitario,
                                           T008_IPV,
                                           T007_D006_Id,
                                           T055_Quantidade,
                                           T001_D024_Id,
                                           T001_Data_Lancamento,
                                           if(T001_Valor_Custo_Unitario>0,T001_Valor_Custo_Unitario,T001_Valor_Preco_Unitario) T001_Valor_Preco_Unitario,
                                           T008_Quantidade,
                                           D006_Flag_Venda_Compra_Outros
                                     from T001
                                left join T008 on T008_Id=T001_T008_Id
                                left join T007 on T007_Id=T008_T007_Id
                                left join T055 on T055_Id=T001_T055_Id
                                left join D006 on D006_Id=T001_D006_Id
                                    where T001_D009_Id='$mD009[D009_Id]'
                                 order by T001_Data_Lancamento,T001_Id");
            $Saldo=0;
            $Custo=0;
            $Custo_Medio=0;
            while ($mT001=mysql_fetch_array($cSql_T001)) {

                  if ($mT001[T001_Flag_Operacao]!='C')
                  {

/*                      if (($mT001[T007_D006_Id]==19) and ($Empresa_Atual==1)) {
                          if ($mT001[T001_Quantidade]<=0) {
                              $Custo_Medio=((($Saldo-$mT001[T008_Quantidade])*$Custo_Medio)+($mT001[T001_Valor_Preco_Unitario]*$mT001[T008_Quantidade]))/($Saldo);
                          } else {
                              $Custo_Medio=(($Saldo*$Custo_Medio)+($mT001[T001_Valor_Preco_Unitario]*$mT001[T001_Quantidade]))/($Saldo+$mT001[T001_Quantidade]);
                          }
                      } */

                      if (($mT001[T001_Flag_Atualiza_Custo]=='S') and ($mT001[T001_Flag_Operacao]!='S')) {
                      	  if ($Custo_Medio<=0)
                      	  {
                              $Custo_Medio=$mT001[T001_Valor_Preco_Unitario];
                      	  }
                      	  if (($Saldo+$mT001[T001_Quantidade])>0)
                      	  {
                              $Custo_Medio=(($Saldo*$Custo_Medio)+($mT001[T001_Valor_Preco_Unitario]*$mT001[T001_Quantidade]))/($Saldo+$mT001[T001_Quantidade]);
						  }
                          $Custo=$mT001[T001_Valor_Preco_Unitario];

                          $D024_Id=$mT001[T001_D024_Id];
                          $D024_Id_Fornecedor=$mT001[T001_D024_Id];
                          $mFD024[$D024_Id]=$D024_Id;
                      }

                      $T066_Id=$mT001[T001_T066_Id];
                      if ($mT001[T001_Flag_Operacao]=='S') {
                          $Saldo-=$mT001[T001_Quantidade];
                          $mSaldo_T066[$T066_Id]-=$mT001[T001_Quantidade];
                          if ($mT001[D006_Flag_Venda_Compra_Outros]=='V')
                          {
                             $D024_Id=$mT001[T001_D024_Id];
                             $mCD024[$D024_Id]=$D024_Id;
                          }
                      } else if ($mT001[T001_Flag_Operacao]=='E') {
                          $Saldo+=$mT001[T001_Quantidade];
                          $mSaldo_T066[$T066_Id]+=$mT001[T001_Quantidade];
                      } else if ($mT001[T001_Flag_Operacao]=='B') {
                          $Saldo=$mT001[T001_Quantidade];
                          $mSaldo_T066[$T066_Id]=$mT001[T001_Quantidade];
                      }
                  }

                  if (($mT001[T001_Flag_MMF]=='S') and ($mD009[D049_Flag_Nacional_Importado]=='X')) {
                      $D016=mysql_query("select D016_Valor_Engepecas from D016 where D016_Data<='$mT001[T001_Data_Lancamento]' order by D016_Data desc limit 1");
                      $mD016=mysql_fetch_array($D016);
                      $Custo_Reais=$Custo*$mD016[0];
                  } else {
                      $Custo_Reais=$Custo;
                  }
                  
                  if ((number_format($Saldo,2)!=number_format($mT001[T001_Quantidade_Saldo],2)) or
                      (number_format($Custo_Medio,2)!=number_format($mT001[T001_Valor_Custo_Medio_Unitario],2)) or
                      ((number_format($mT001[T001_Valor_Custo_Unitario],2)!=number_format($Custo_Reais,2)) and ($mT001[T001_Flag_MMF]=='S')) or
                      (($mT001[T001_Valor_Custo_Unitario]<=0) and ($Custo_Reais>0)))
                  {
                      mysql_query("update T001 set T001_Quantidade_Saldo='$Saldo',
                                                   T001_Valor_Custo_Unitario='$Custo_Reais',
                                                   T001_Valor_Custo_Medio_Unitario='$Custo_Medio'
                                             where T001_Id='$mT001[T001_Id]'");
                  }

                  if ($Saldo<=0)
                  {
                      $Custo=0;
                      $Custo_Reais=0;
                      $Custo_Medio=0;
                  }

/*                  if (($mT001[T001_T008_Id]>0) and
                      ((number_format($mT001[T008_Valor_Custo_Unitario],2)!=number_format($Custo_Reais,2)) or ($mT001[T008_IPV]<=0)) and
                      ($mT001[T001_Flag_MMF]=='S'))
                  {
                      mysql_query("update T008 set T008_Valor_Custo_Unitario='$Custo_Reais',
                                                   T008_IPV=(T008_Valor_Preco_Unitario-round(T008_Valor_ICMS/T008_Quantidade,2)-T008_Valor_Desconto_Unitario)/round(T008_Valor_Custo_Unitario,2),
                                                   T008_Valor_Total_Custo=T008_Valor_Custo_Unitario*T008_Quantidade where T008_Id='$mT001[T001_T008_Id]'");
                  } */
             }
             
             $Custo_Compra=$Custo;
             
             $D069=mysql_query("select (D069_Preco_Unitario-(D069_Preco_Unitario*(D069_Percentual_Desconto/100))) D069_Preco_Unitario,
                                       D069_D070_Id
                                  from D069
                             left join D070 on D070_Id=D069_D070_Id
                                 where D069_D009_Id='$mD009[D009_Id]'
                                   and D070_Flag_Ativo='S'
                                 order by (D069_Preco_Unitario-(D069_Preco_Unitario*(D069_Percentual_Desconto/100)))");
             
             $mD069=mysql_fetch_array($D069);

             if ($mD009[D049_Flag_Tipo]=='MD')
             {
             	$D049=mysql_query("select D009_Valor_Custo_Compra,
                                          D009_Valor_Custo_Medio_Unitario,
                                          D049_ICF,
                                          D049_Valor_Preco_Tabela,
                                          D049_Percentual_Desconto_Tabela,
                                          D049_Aliquota_ICMS_Tabela,
                                          C008_Flag_Custo
                                          from D009
                                     left join D049 on D049_Id=D009_D049_Id
                                     left join D001 on D001_Id=D049_D001_Id
                                     left join C008 on C008_Id=D001_C008_Id
                                         where D049_D001_Id='$mD009[D049_D001_Id]' and D049_Flag_Tipo='CP' and D009_C004_Id='$Empresa_Atual'");
             	$mD049=mysql_fetch_array($D049);
             	$Custo_Tabela=0;
                if ($mD049[D049_Valor_Preco_Tabela]>0)
                {
             	   	$Custo_Tabela=$mD049[D049_Valor_Preco_Tabela]-($mD049[D049_Valor_Preco_Tabela]*($mD049[D049_Percentual_Desconto_Tabela]/100));
             	    $Custo_Tabela=$Custo_Tabela-($Custo_Tabela*($mD049[D049_Aliquota_ICMS_Tabela]/100));
                }
             	$Custo=$mD049[D009_Valor_Custo_Compra];
             	if (($mD049[C008_Flag_Custo]=='TABELA' and $Custo_Tabela>0) or ($Custo<=0))
             	{
             		$Custo=$Custo_Tabela;
             	}
             	if ($mD009[D082_Percentual_Acrescimo]>0)
             	{
             		$Custo = $Custo*(1+($mD009[D082_Percentual_Acrescimo]/100));
				}
             	if ($Custo_Medio<=0)
             	{
             	    $Custo_Medio=$mD049[D009_Valor_Custo_Medio_Unitario];
                	if ($mD009[D082_Percentual_Acrescimo]>0)
                	{
    	    			$Custo_Medio = $Custo_Medio*(1+($mD009[D082_Percentual_Acrescimo]/100));
					}
				}
             }
             else
             {
                if ($mD009[D049_Valor_Preco_Tabela]>0)
                {
             	   	$Custo_Tabela=$mD009[D049_Valor_Preco_Tabela]-($mD009[D049_Valor_Preco_Tabela]*($mD009[D049_Percentual_Desconto_Tabela]/100));
             	    $Custo_Tabela=$Custo_Tabela-($Custo_Tabela*($mD009[D049_Aliquota_ICMS_Tabela]/100));
             	    if (($mD009[C008_Flag_Custo]=='TABELA' and $Custo_Tabela>0) or ($Custo<=0))
             	    {
             		    $Custo=$Custo_Tabela;
             	    }
                }
                else if ($mD009[D049_Flag_Tipo]=='MT')
                {
                    $D042=mysql_query("select D042_Id
                                        from D042 left join D049 on D049_Id=D042_D049_Id_Itens
                                                  left join D082 on D049_D082_Id=D082_Id
                                                  left join D001 on D001_Id=D049_D001_Id
                                                  left join D009 on D009_D049_Id=D042_D049_Id_Itens
                                            where D042_D049_Id='$mD009[D049_Id]'
                                              and D042_C004_Id='$Empresa_Atual' 
                                              and D009_C004_Id='$Empresa_Atual'
                                              and (D009_Valor_Custo_Compra*D042_Quantidade)<=0");
                    if (mysql_num_rows($D042)<=0)
                    {
	                	$D042=mysql_query("select sum(D009_Valor_Custo_Compra*D042_Quantidade)
	                                        from D042 left join D049 on D049_Id=D042_D049_Id_Itens
	                                                  left join D082 on D049_D082_Id=D082_Id
	                                                  left join D001 on D001_Id=D049_D001_Id
	                                                  left join D009 on D009_D049_Id=D042_D049_Id_Itens
	                                            where D042_D049_Id='$mD009[D049_Id]'
	                                              and D042_C004_Id='$Empresa_Atual' 
	                                              and D009_C004_Id='$Empresa_Atual'");
	                	$mD042=mysql_fetch_array($D042);
	                	$Custo=$mD042[0];
                    }
                }
             }
             if ($Custo<0)
             {
             	$Custo=0;
             }
              
             if ($mD009[D049_ICF]<=0)
             {
                 $mD009[D049_ICF]=0;
             }
             
             $C004=mysql_query("select C004_Matriz from C004 where C004_Id='$mD009[D009_C004_Id]'");
             $mC004=mysql_fetch_array($C004);                      
             
             if ($mC004[C004_Matriz]=='1')
             {
	             mysql_query("update D009
	                       left join D049 on D049_Id=D009_D049_Id
	                       left join D082 on D082_Id=D049_D082_Id
	                       left join D001 on D001_Id=D049_D001_Id
	                       left join C008 on C008_Id=D001_C008_Id
	                             set D009_Valor_Custo_Medio_Unitario='$Custo_Medio',
	                                 D009_Valor_Custo_Compra='$Custo',
	                                 D009_Valor_Custo_Ultima_Compra='$Custo_Compra',
	                                 D009_Valor_Melhor_Custo='$mD069[D069_Preco_Unitario]',
	                                 D009_D070_Id='$mD069[D069_D070_Id]',
	                                 D009_Valor_Custo_Unitario=D009_Valor_Custo_Compra*if($mD009[D049_ICF]>0,$mD009[D049_ICF],if(D082_ICF>0,D082_ICF,if(C008_ICF>0,C008_ICF,$mC020[C020_IPV_Custo]))),
	                                 D009_Quantidade_Estoque='$Saldo'
	                           where D009_Id='$mD009[D009_Id]'");
	                           
	             mysql_query("update D049 set D049_D024_Id='$D024_Id_Fornecedor' where D049_Id='$mD009[D049_Id]'");


	             if (count($mSaldo_T066)>0)
	             {
	                 $mT066_Ids=array_keys($mSaldo_T066);
	                 for ($i=0;$i<=count($mT066_Ids);$i++)
	                 {
	                      $T066_Id=$mT066_Ids[$i];
	                      if ($T066_Id>0)
	                      {
	                          mysql_query("update T066 set T066_Quantidade_Estoque='$mSaldo_T066[$T066_Id]' where T066_Id='$T066_Id'");
	                      }
	                 }
	             }
		         if ($mD009[D049_IPV]>0)
	             {
		             mysql_query("update D041
		                       left join D049 on D049_Id=D041_D049_Id
		                       left join D009 on D009_D049_Id=D049_Id
		                       left join D082 on D082_Id=D049_D082_Id
		                             set D041_Valor_Preco_Revenda   =D009_Valor_Custo_Unitario*$mD009[D049_IPV],
		                                 D041_Valor_Base            =D009_Valor_Custo_Unitario*$mD009[D049_IPV],
		                                 D041_Valor_Preco_Consumidor=D009_Valor_Custo_Unitario*$mD009[D049_IPV]
		                           where D009_Id='$mD009[D009_Id]'
		                             and ifnull(D009_Flag_Preco_Tabelado,'N')!='S'");
	             }
	             else if ($mC020[C020_IPV_Revenda]>0)
	             {
		             mysql_query("update D041
		                       left join D049 on D049_Id=D041_D049_Id
		                       left join D009 on D009_D049_Id=D049_Id
		                       left join D082 on D082_Id=D049_D082_Id
		                             set D041_Valor_Preco_Revenda   =D009_Valor_Custo_Unitario*$mC020[C020_IPV_Revenda],
		                                 D041_Valor_Base            =D009_Valor_Custo_Unitario*$mC020[C020_IPV_Atacado],
		                                 D041_Valor_Preco_Consumidor=D009_Valor_Custo_Unitario*$mC020[C020_IPV_Varejo]
		                           where D009_Id='$mD009[D009_Id]'
		                             and ifnull(D009_Flag_Preco_Tabelado,'N')!='S'");
	             }

			 } 
			 else
			 {
			 	 $D009_Custo_Matriz=mysql_query("select D009_Valor_Custo_Unitario,D009_Valor_Custo_Compra,D009_Valor_Custo_Medio_Unitario,D009_Valor_Custo_Importado,D009_Valor_Melhor_Custo,D009_Valor_Custo_Ultima_Compra from D009 where D009_D049_Id='$mD009[D009_D049_Id]' and D009_C004_Id='$mC004_Matriz[C004_Id]'");
			 	 $mD009_Custo_Matriz=mysql_fetch_array($D009_Custo_Matriz);			 	 
			 	 
			 	 // faz a c�pia de todos os custos da matriz para a filial
			 	 
				 mysql_query("update D049 left join D009 on D009_D049_Id=D049_Id set D049_Marca='$mD009_Custo_Matriz[D009_Valor_Custo_Unitario]' where D009_Id=$mD009[D009_Id]");
				 mysql_query("update D009 left join D049 on D049_Id=D009_D049_Id set D009_Valor_Custo_Unitario=D049_Marca where D009_Id=$mD009[D009_Id]");						 	 
			 	 				 
				 mysql_query("update D049 left join D009 on D009_D049_Id=D049_Id set D049_Marca='$mD009_Custo_Matriz[D009_Valor_Custo_Compra]' where D009_Id=$mD009[D009_Id]");
				 mysql_query("update D009 left join D049 on D049_Id=D009_D049_Id set D009_Valor_Custo_Compra=D049_Marca where D009_Id=$mD009[D009_Id]");						 	 
			 	 				 
				 mysql_query("update D049 left join D009 on D009_D049_Id=D049_Id set D049_Marca='$mD009_Custo_Matriz[D009_Valor_Custo_Medio_Unitario]' where D009_Id=$mD009[D009_Id]");
				 mysql_query("update D009 left join D049 on D049_Id=D009_D049_Id set D009_Valor_Custo_Medio_Unitario=D049_Marca where D009_Id=$mD009[D009_Id]");						 	 

				 mysql_query("update D049 left join D009 on D009_D049_Id=D049_Id set D049_Marca='$mD009_Custo_Matriz[D009_Valor_Custo_Importado]' where D009_Id=$mD009[D009_Id]");
				 mysql_query("update D009 left join D049 on D049_Id=D009_D049_Id set D009_Valor_Custo_Importado=D049_Marca where D009_Id=$mD009[D009_Id]");						 	 

				 mysql_query("update D049 left join D009 on D009_D049_Id=D049_Id set D049_Marca='$mD009_Custo_Matriz[D009_Valor_Melhor_Custo]' where D009_Id=$mD009[D009_Id]");
				 mysql_query("update D009 left join D049 on D049_Id=D009_D049_Id set D009_Valor_Melhor_Custo=D049_Marca where D009_Id=$mD009[D009_Id]");						 	 

				 mysql_query("update D049 left join D009 on D009_D049_Id=D049_Id set D049_Marca='$mD009_Custo_Matriz[D009_Valor_Custo_Ultima_Compra]' where D009_Id=$mD009[D009_Id]");
				 mysql_query("update D009 left join D049 on D049_Id=D009_D049_Id set D009_Valor_Custo_Ultima_Compra=D049_Marca where D009_Id=$mD009[D009_Id]");						 	 
			 	 				 
			 	 				 
			 }
			      
             // Cadastra os fornecedores do produto
             if (count($mFD024)>0)
             {
                 $mD024_Ids=array_keys($mFD024);
                 for ($i=0;$i<=count($mD024_Ids);$i++)
                 {
                      $D024_Id=$mD024_Ids[$i];
                      if ($D024_Id>0)
                      {
                          $D083=mysql_query("select D083_Id,D083_Flag_Fornecedor from D083 where D083_D001_Id='$mD009[D049_D001_Id]' and D083_D049_Id='$mD009[D049_Id]' and D083_D024_Id='$D024_Id'");
                          if (mysql_num_rows($D083)<=0)
                          {
                              mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Fornecedor) values ('$mD009[D049_Id]','$mD009[D049_D001_Id]','$D024_Id',curdate(),'S')");
                          }
                          else
                          {
                              $mD083=mysql_fetch_array($D083);
                              if ($mD083[D083_Flag_Fornecedor]!='S')
                              {
                                  mysql_query("update D083 set D083_Flag_Fornecedor='S' where D083_Id='$mD083[D083_Id]'");
                              }
                          }
                      }
                 }
             }
             
             // Cadastra os fornecedores do produto
             if (count($mCD024)>0)
             {
                 $mD024_Ids=array_keys($mCD024);
                 for ($i=0;$i<=count($mD024_Ids);$i++)
                 {
                      $D024_Id=$mD024_Ids[$i];
                      if ($D024_Id>0)
                      {
                          $D083=mysql_query("select D083_Id,D083_Flag_Cliente from D083 where D083_D001_Id='$mD009[D049_D001_Id]' and D083_D049_Id='$mD009[D049_Id]' and D083_D024_Id='$D024_Id'");
                          if (mysql_num_rows($D083)<=0)
                          {
                              mysql_query("insert into D083 (D083_D049_Id,D083_D001_Id,D083_D024_Id,D083_Data_Cadastro,D083_Flag_Cliente) values ('$mD009[D049_Id]','$mD009[D049_D001_Id]','$D024_Id',curdate(),'S')");
                          }
                          else
                          {
                              $mD083=mysql_fetch_array($D083);
                              if ($mD083[D083_Flag_Cliente]!='S')
                              {
                                  mysql_query("update D083 set D083_Flag_Cliente='S' where D083_Id='$mD083[D083_Id]'");
                              }
                          }
                      }
                 }
              }
              
            // Atualiza T186 (� a T001 por loca��o)
    		$sqlT001 = <<<EOT
    			SELECT 
    				* 
    			FROM T186 
    				LEFT JOIN T001 ON T001_Id=T186_T001_Id 
    			WHERE 
    				T001_D009_Id = '{$mD009['D009_Id']}';
EOT;
			$Saldo = 0;
			
			// T066 deste produto
			$sqlT066 = "SELECT * FROM T066 LEFT JOIN D004 ON D004_Id=T066_D004_Id WHERE T066_D009_Id = '{$mD009['D009_Id']}' and D004_Flag_Tipo is not null;";
			$resT066 = mysql_query($sqlT066);
			$mSaldo_T066 = array();
			while ($rowT066 = mysql_fetch_assoc($resT066)) {
				// estoque por loca��o, somente nas loca��es (zera tudo)
				// // todo saldo come�a em 0 (loca��es novas com estoque)
				// if (!empty($rowT066['D004_Id']) && !empty($rowT066['D004_Flag_Tipo'])) {
					$mSaldo_T066[$rowT066['T066_Id']] = 0;
				// }
			}
			
			$resT001 = mysql_query($sqlT001);
			while ($rowT001 = mysql_fetch_assoc($resT001)) {
				if ($rowT001['T001_Flag_Operacao']!='C')
                {
					$T066_Id = $rowT001['T186_T066_Id'];
					
					if ($rowT001['T001_Flag_Operacao']=='S') {
						$Saldo-=$rowT001['T186_Quantidade'];
						$mSaldo_T066[$T066_Id]-=$rowT001['T186_Quantidade'];
					} else if ($rowT001['T001_Flag_Operacao']=='E') {
						$Saldo+=$rowT001['T186_Quantidade'];
						$mSaldo_T066[$T066_Id]+=$rowT001['T186_Quantidade'];
					} else if ($rowT001['T001_Flag_Operacao']=='B') {
						$Saldo=$rowT001['T186_Quantidade'];
						$mSaldo_T066[$T066_Id]=$rowT001['T186_Quantidade'];
					}
				}
			}
			
			// atualize T066
			foreach ($mSaldo_T066 as $key => $value) {
				$sql = "UPDATE T066 SET T066_Quantidade_Estoque = '{$value}' WHERE T066_Id = '{$key}';";
				mysql_query($sql);
				$sql = <<<EOT
					UPDATE T066 SET 
  						T066_Quantidade_Estoque_Real=T066_Quantidade_Estoque-T066_Quantidade_Separado(T066_Id),
  						T066_Quantidade_Estoque_Liquido=T066_Quantidade_Estoque-T066_Quantidade_Liquido(T066_Id)
  					WHERE
  						T066_Id='{$key}';
EOT;
				mysql_query($sql);
			}
    }
    
    

/*    mysql_query("update D009 left join D049 on D049_Id=D009_D049_Id
                             left join D001 on D001_Id=D049_D001_Id
                             set D009_Quantidade_Minima_Estoque=D009_Minimo_Maximo(D009_Id,1),
                                 D009_Quantidade_Maxima_Estoque=D009_Minimo_Maximo(D009_Id,2),
                                 D009_Frequencia_Venda=D009_Frequencia(D009_Id),
                                 D009_Data_Ultima_Venda=(select T001_Data_Lancamento
                                                           from T001
                                                          where T001_Flag_MMF='S'
                                                            and T001_D009_Id=D009_Id
                                                       order by T001_Data_Lancamento desc limit 1),
                                 D009_Data_Ultimo_Movimento=(select T001_Data_Lancamento
                                                           from T001
                                                          where (T001_Flag_MMF='S'
                                                             or T001_Flag_Atualiza_Custo='S')
                                                            and T001_D009_Id=D009_Id
                                                       order by T001_Data_Lancamento desc limit 1),
                                 D009_Data_Ultima_Entrada=(select T001_Data_Lancamento
                                                           from T001
                                                          where T001_Flag_Atualiza_Custo='S'
                                                            and T001_D009_Id=D009_Id
                                                       order by T001_Data_Lancamento desc limit 1)
                          where (D001_Id='$D001_Id'
                             or D009_Id='$D009_Id')
                            and D009_C004_Id='$Empresa_Atual'");



    if (!empty($D001_Id)) {
       mysql_query("update D049
                       set D049_Data_Ultima_Venda=(select D009_Data_Ultima_Venda
                                                  from D009
                                                 where D009_D049_Id=D049_Id
                                                   and D009_Data_Ultima_Venda!='0000-00-00'
                                              order by D009_Data_Ultima_Venda limit 1)
                     where D049_D001_Id='$D001_Id'");
    } */

}

function D001_reprocessa_historico_rapido($D001_Id=0,$D009_Id=0) 
{


    global $Empresa_Atual;

    $cSql_D009=mysql_query("select D009_Id,
                                   D009_Quantidade_Estoque,
                                   D009_Valor_Custo_Medio_Unitario,
                                   D009_Valor_Custo_Compra,
                                   D009_Valor_Melhor_Custo,
                                   D049_Flag_Nacional_Importado,
                                   D049_Flag_Tipo,
                                   D049_Valor_Preco_Tabela,
                                   D049_Percentual_Desconto_Tabela,
                                   D082_Percentual_Acrescimo,
                                   D049_Aliquota_ICMS_Tabela,
                                   D049_Id,
                                   D049_D001_Id,
                                   D049_ICF,
                                   D049_IPV,
                                   C008_Flag_Custo
                              from D009 as T1
                          left join D049 on D049_Id=D009_D049_Id
                          left join D001 on D001_Id=D049_D001_Id
                          left join C008 on C008_Id=D001_C008_Id
                          left join D082 on D082_Id=D049_D082_Id
                              where (D049_D001_Id='$D001_Id'
                                 or D009_Id='$D009_Id')
                                and D009_C004_Id='$Empresa_Atual'");
                                
    $C020=mysql_query("select * from C020 where C020_C004_Id='$Empresa_Atual'");
    $mC020=mysql_fetch_array($C020);
    
    if ($mC020[C020_IPV_Custo]<=0)
    {
        $mC020[C020_IPV_Custo]=1;
    }
    
    if (mysql_num_rows($cSql_D009)<=0)
    {
    	echo "<script>alert('O produto não foi encontrado: $D001_Id / $D009_Id');</script>";
    	return NULL;
    }

    while ($mD009=mysql_fetch_array($cSql_D009)) 
    {
    	
            $cSql_T001=mysql_query("select T001_Valor_Custo_Unitario,
                                           T001_Valor_Custo_Medio_Unitario,
                                           if(T001_Valor_Custo_Unitario>0,T001_Valor_Custo_Unitario,T001_Valor_Preco_Unitario) T001_Valor_Preco_Unitario
                                     from T001
                                left join T008 on T008_Id=T001_T008_Id
                                left join T007 on T007_Id=T008_T007_Id
                                left join T055 on T055_Id=T001_T055_Id
                                left join D006 on D006_Id=T001_D006_Id
                                    where T001_D009_Id='$mD009[D009_Id]'
                                      and T001_Flag_Atualiza_Custo='S'
                                 order by T001_Data_Lancamento,T001_Id
                                    limit 1");
             $mT001=mysql_fetch_array($cSql_T001);
             
             $Custo=$mT001[T001_Valor_Preco_Unitario];
             
             if ($mD009[D049_Flag_Tipo]=='MD')
             {
             	$D049=mysql_query("select D009_Valor_Custo_Compra,
                                          D049_ICF,
                                          D049_Valor_Preco_Tabela,
                                          D049_Percentual_Desconto_Tabela,
                                          D049_Aliquota_ICMS_Tabela,
                                          C008_Flag_Custo
                                          from D009
                                     left join D049 on D049_Id=D009_D049_Id
                                     left join D001 on D001_Id=D049_D001_Id
                                     left join C008 on C008_Id=D001_C008_Id
                                         where D049_D001_Id='$mD009[D049_D001_Id]' and D049_Flag_Tipo='CP' and D009_C004_Id='$Empresa_Atual'");
             	$mD049=mysql_fetch_array($D049);
             	$Custo_Tabela=0;
                if ($mD049[D049_Valor_Preco_Tabela]>0)
                {
             	   	$Custo_Tabela=$mD049[D049_Valor_Preco_Tabela]-($mD049[D049_Valor_Preco_Tabela]*($mD049[D049_Percentual_Desconto_Tabela]/100));
             	    $Custo_Tabela=$Custo_Tabela-($Custo_Tabela*($mD049[D049_Aliquota_ICMS_Tabela]/100));
                }
             	$Custo=$mD049[D009_Valor_Custo_Compra];
             	if (($mD049[C008_Flag_Custo]=='TABELA' and $Custo_Tabela>0) or ($Custo<=0))
             	{
             		$Custo=$Custo_Tabela;
             	}
             	if ($mD009[D082_Percentual_Acrescimo]>0)
             	{
             		$Custo = $Custo*(1+($mD009[D082_Percentual_Acrescimo]/100));
             	}
             }
             else
             {
                if ($mD009[D049_Valor_Preco_Tabela]>0)
                {
             	   	$Custo_Tabela=$mD009[D049_Valor_Preco_Tabela]-($mD009[D049_Valor_Preco_Tabela]*($mD009[D049_Percentual_Desconto_Tabela]/100));
             	    $Custo_Tabela=$Custo_Tabela-($Custo_Tabela*($mD009[D049_Aliquota_ICMS_Tabela]/100));
             	    if (($mD009[C008_Flag_Custo]=='TABELA' and $Custo_Tabela>0) or ($Custo<=0))
             	    {
             		    $Custo=$Custo_Tabela;
             	    }
                }
                else if ($mD009[D049_Flag_Tipo]=='MT')
                {
                    $D042=mysql_query("select D042_Id
                                        from D042 left join D049 on D049_Id=D042_D049_Id_Itens
                                                  left join D082 on D049_D082_Id=D082_Id
                                                  left join D001 on D001_Id=D049_D001_Id
                                                  left join D009 on D009_D049_Id=D042_D049_Id_Itens
                                            where D042_D049_Id='$mD009[D049_Id]'
                                              and D042_C004_Id='$Empresa_Atual' 
                                              and D009_C004_Id='$Empresa_Atual'
                                              and (D009_Valor_Custo_Compra*D042_Quantidade)<=0");
                    if (mysql_num_rows($D042)<=0)
                    {
	                	$D042=mysql_query("select sum(D009_Valor_Custo_Compra*D042_Quantidade)
	                                        from D042 left join D049 on D049_Id=D042_D049_Id_Itens
	                                                  left join D082 on D049_D082_Id=D082_Id
	                                                  left join D001 on D001_Id=D049_D001_Id
	                                                  left join D009 on D009_D049_Id=D042_D049_Id_Itens
	                                            where D042_D049_Id='$mD009[D049_Id]'
	                                              and D042_C004_Id='$Empresa_Atual' 
	                                              and D009_C004_Id='$Empresa_Atual'");
	                	$mD042=mysql_fetch_array($D042);
	                	$Custo=$mD042[0];
                    }
                }
             }
             if ($Custo<0)
             {
             	$Custo=0;
             }
              
             if ($mD009[D049_ICF]<=0)
             {
                 $mD009[D049_ICF]=0;
             }

             mysql_query("update D009
                       left join D049 on D049_Id=D009_D049_Id
                       left join D082 on D082_Id=D049_D082_Id
                       left join D001 on D001_Id=D049_D001_Id
                       left join C008 on C008_Id=D001_C008_Id
                             set D009_Valor_Custo_Compra='$Custo',
                                 D009_Valor_Custo_Unitario=D009_Valor_Custo_Compra*if($mD009[D049_ICF]>0,$mD009[D049_ICF],if(D082_ICF>0,D082_ICF,if(C008_ICF>0,C008_ICF,$mC020[C020_IPV_Custo])))
                           where D009_Id='$mD009[D009_Id]'");
                           
	         if ($mD009[D049_IPV]>0)
             {
	             mysql_query("update D041
	                       left join D049 on D049_Id=D041_D049_Id
	                       left join D009 on D009_D049_Id=D049_Id
	                       left join D082 on D082_Id=D049_D082_Id
	                             set D041_Valor_Preco_Revenda   =D009_Valor_Custo_Unitario*$mD009[D049_IPV],
	                                 D041_Valor_Base            =D009_Valor_Custo_Unitario*$mD009[D049_IPV],
	                                 D041_Valor_Preco_Consumidor=D009_Valor_Custo_Unitario*$mD009[D049_IPV]
	                           where D009_Id='$mD009[D009_Id]'
	                             and ifnull(D009_Flag_Preco_Tabelado,'N')!='S'");
             }
             else if ($mC020[C020_IPV_Revenda]>0)
             {
	             mysql_query("update D041
	                       left join D049 on D049_Id=D041_D049_Id
	                       left join D009 on D009_D049_Id=D049_Id
	                       left join D082 on D082_Id=D049_D082_Id
	                             set D041_Valor_Preco_Revenda   =D009_Valor_Custo_Unitario*$mC020[C020_IPV_Revenda],
	                                 D041_Valor_Base            =D009_Valor_Custo_Unitario*$mC020[C020_IPV_Atacado],
	                                 D041_Valor_Preco_Consumidor=D009_Valor_Custo_Unitario*$mC020[C020_IPV_Varejo]
	                           where D009_Id='$mD009[D009_Id]'
	                             and ifnull(D009_Flag_Preco_Tabelado,'N')!='S'");
             }
    }

	
}

function D001_reprocessa_historico_geral() {

    global $Empresa_Atual;

    $cSql_D009=mysql_query("select D009_Id,
                                   D009_Quantidade_Estoque,
                                   D009_Valor_Custo_Medio_Unitario,
                                   D009_Valor_Custo_Compra,
                                   D009_Valor_Melhor_Custo,
                                   D049_Flag_Nacional_Importado,
                                   D049_Flag_Tipo,
                                   D049_Valor_Preco_Tabela,
                                   D049_Percentual_Desconto_Tabela,
                                   D082_Percentual_Acrescimo,
                                   D049_D001_Id
                               from D009 as T1
                          left join D049 on D049_Id=D009_D049_Id
                          left join D082 on D082_Id=D049_D082_Id
                              where D009_C004_Id='$Empresa_Atual'");

    $C020=mysql_query("select * from C020 where C020_C004_Id='$Empresa_Atual'");
    $mC020=mysql_fetch_array($C020);

    if ($mC020[C020_IPV_Custo]<=0)
    {
        $mC020[C020_IPV_Custo]=1;
    }

    while ($mD009=mysql_fetch_array($cSql_D009)) {

            $Total_Produtos++;
            $cSql_T001=mysql_query("select T001_Quantidade,
                                           T001_Flag_Operacao,
                                           T001_Flag_Atualiza_Custo,
                                           T001_Quantidade_Saldo,
                                           T001_Valor_Custo_Unitario,
                                           T001_Valor_Custo_Medio_Unitario,
                                           T001_Id,
                                           T001_T066_Id,
                                           T001_Flag_MMF,
                                           T001_T008_Id,
                                           T001_Descricao_Operacao,
                                           T008_Valor_Custo_Unitario,
                                           T008_Valor_Preco_Unitario,
                                           T008_IPV,
                                           T007_D006_Id,
                                           T055_Quantidade,
                                           T001_Data_Lancamento,
                                           if(T001_Valor_Custo_Unitario>0,T001_Valor_Custo_Unitario,T001_Valor_Preco_Unitario) T001_Valor_Preco_Unitario,
                                           T008_Quantidade
                                     from T001
                                left join T008 on T008_Id=T001_T008_Id
                                left join T007 on T007_Id=T008_T007_Id
                                left join T055 on T055_Id=T001_T055_Id
                                    where T001_D009_Id='$mD009[D009_Id]'
                                 order by T001_Data_Lancamento,T001_Id");
            $Saldo=0;
            $Custo=0;
            $Custo_Medio=0;
            while ($mT001=mysql_fetch_array($cSql_T001)) {

                  if ($mT001[T001_Flag_Operacao]!='C')
                  {

                      if (($mT001[T007_D006_Id]==19) and ($Empresa_Atual==1)) {
                          if ($mT001[T001_Quantidade]<=0) {
                              $Custo_Medio=((($Saldo-$mT001[T008_Quantidade])*$Custo_Medio)+($mT001[T001_Valor_Preco_Unitario]*$mT001[T008_Quantidade]))/($Saldo);
                          } else {
                              $Custo_Medio=(($Saldo*$Custo_Medio)+($mT001[T001_Valor_Preco_Unitario]*$mT001[T001_Quantidade]))/($Saldo+$mT001[T001_Quantidade]);
                          }
                      }

                      if ($mT001[T001_Flag_Atualiza_Custo]=='S') {
                          if ($mT001[T001_Valor_Custo_Medio_Unitario]<=0) {
                              if (($Saldo+$mT001[T001_Quantidade])>0) {
                                 $Custo_Medio=(($Saldo*$Custo_Medio)+($mT001[T001_Valor_Preco_Unitario]*$mT001[T001_Quantidade]))/($Saldo+$mT001[T001_Quantidade]);
                              } else {
                                 $Custo_Medio=$mT001[T001_Valor_Preco_Unitario];
                              }
                          } else {
                              $Custo_Medio=$mT001[T001_Valor_Custo_Medio_Unitario];
                          }
                          $Custo=$mT001[T001_Valor_Preco_Unitario];
                      }

                      $T066_Id=$mT001[T001_T066_Id];
                      if ($mT001[T001_Flag_Operacao]=='S') {
                          $Saldo-=$mT001[T001_Quantidade];
                          $mSaldo_T066[$T066_Id]-=$mT001[T001_Quantidade];
                      } else if ($mT001[T001_Flag_Operacao]=='E') {
                          $Saldo+=$mT001[T001_Quantidade];
                          $mSaldo_T066[$T066_Id]+=$mT001[T001_Quantidade];
                      } else if ($mT001[T001_Flag_Operacao]=='B') {
                          $Saldo=$mT001[T001_Quantidade];
                          $mSaldo_T066[$T066_Id]=$mT001[T001_Quantidade];
                      }
                  }

                  if (($mT001[T001_Flag_MMF]=='S') and ($mD009[D049_Flag_Nacional_Importado]=='I')) {
                      $D016=mysql_query("select D016_Valor_Engepecas from D016 where D016_Data<='$mT001[T001_Data_Lancamento]' order by D016_Data desc limit 1");
                      $mD016=mysql_fetch_array($D016);
                      $Custo_Reais=$Custo*$mD016[0];
                  } else {
                      $Custo_Reais=$Custo;
                  }

                  if ((number_format($Saldo,2)!=number_format($mT001[T001_Quantidade_Saldo],2)) or
                      (number_format($Custo_Medio,2)!=number_format($mT001[T001_Valor_Custo_Medio_Unitario],2)) or
                      ((number_format($mT001[T001_Valor_Custo_Unitario],2)!=number_format($Custo_Reais,2)) and ($mT001[T001_Flag_MMF]=='S')) or
                      (($mT001[T001_Valor_Custo_Unitario]<=0) and ($Custo_Reais>0)))
                  {
                      mysql_query("update T001 set T001_Quantidade_Saldo='$Saldo',
                                                   T001 set T001_Valor_Custo_Unitario='$Custo_Reais',
                                                   T001_Valor_Custo_Medio_Unitario='$Custo_Medio'
                                             where T001_Id='$mT001[T001_Id]'");
                  }
/*  	   	  		  if (($mT001[T001_T008_Id]>0) and
                      ((number_format($mT001[T008_Valor_Custo_Unitario],2)!=number_format($Custo_Reais,2)) or ($mT001[T008_IPV]<=0)) and
                      ($mT001[T001_Flag_MMF]=='S'))
                  {
                      mysql_query("update T008 set T008_Valor_Custo_Unitario='$Custo_Reais',
                                                   T008_IPV=(T008_Valor_Preco_Unitario-round(T008_Valor_ICMS/T008_Quantidade,2)-T008_Valor_Desconto_Unitario)/round(T008_Valor_Custo_Unitario,2),
                                                   T008_Valor_Total_Custo=T008_Valor_Custo_Unitario*T008_Quantidade where T008_Id='$mT001[T001_T008_Id]'");
                  } */
             }

             if ($Custo<=0)
             {
                 if (($mD009[D049_Flag_Tipo]=='MD'))
                 {
                     $D049=mysql_query("select D009_Valor_Custo_Compra
                                          from D009
                                     left join D049 on D049_Id=D009_D049_Id
                                         where D049_D001_Id='$mD009[D049_D001_Id]' and D049_Flag_Tipo='CP' and D009_C004_Id='$Empresa_Atual'");

                     $mD049=mysql_fetch_array($D049);
                     $Custo=$mD049[D009_Valor_Custo_Compra];
                 }
                 else
                 {
                    $Custo=$mD009[D049_Valor_Preco_Tabela]-($mD009[D049_Valor_Preco_Tabela]*($mD009[D049_Percentual_Desconto_Tabela]/100));
                 }
                 if ($mD009[D082_Percentual_Acrescimo]>0)
                 {
                     $Custo = $Custo * (1+($mD009[D082_Percentual_Acrescimo]/100));
                 }
                 if ($Custo<0)
                 {
                    $Custo=0;
                 }

             }

             $Custo_Unitario=$Custo*$mC020[C020_IPV_Custo];
             mysql_query("update D009 set D009_Valor_Custo_Medio_Unitario='$Custo_Medio',
                                          D009_Valor_Custo_Compra='$Custo',
                                          D009_Valor_Melhor_Custo='$mD009[Melhor_Custo]',
                                          D009_Valor_Custo_Unitario='$Custo_Unitario',
                                          D009_Quantidade_Estoque='$Saldo'
                                    where D009_Id='$mD009[D009_Id]'");


             $mT066_Ids=array_keys($mSaldo_T066);
             for ($i=0;$i<=count($mT066_Ids);$i++)
             {
                  $T066_Id=$mT066_Ids[$i];
                  if ($T066_Id>0)
                  {
                      mysql_query("update T066 set T066_Quantidade_Estoque='$mSaldo_T066[$T066_Id]' where T066_Id='$T066_Id'");
                  }
             }
    }

    mysql_query("update D009 left join D049 on D049_Id=D009_D049_Id
                             left join D001 on D001_Id=D049_D001_Id
                             set D009_Quantidade_Minima_Estoque=D009_Minimo_Maximo(D009_Id,1),
                                 D009_Quantidade_Maxima_Estoque=D009_Minimo_Maximo(D009_Id,2),
                                 D009_Frequencia_Venda=D009_Frequencia(D009_Id),
                                 D009_Data_Ultima_Venda=(select T001_Data_Lancamento
                                                           from T001
                                                          where T001_Flag_MMF='S'
                                                            and T001_D009_Id=D009_Id
                                                       order by T001_Data_Lancamento desc limit 1),
                                 D009_Data_Ultimo_Movimento=(select T001_Data_Lancamento
                                                           from T001
                                                          where (T001_Flag_MMF='S'
                                                             or T001_Flag_Atualiza_Custo='S')
                                                            and T001_D009_Id=D009_Id
                                                       order by T001_Data_Lancamento desc limit 1),
                                 D009_Data_Ultima_Entrada=(select T001_Data_Lancamento
                                                           from T001
                                                          where T001_Flag_Atualiza_Custo='S'
                                                            and T001_D009_Id=D009_Id
                                                       order by T001_Data_Lancamento desc limit 1)
                          where D009_C004_Id='$Empresa_Atual'");

    if (!empty($D001_Id)) {
       mysql_query("update D049
                       set D049_Data_Ultima_Venda=(select D009_Data_Ultima_Venda
                                                  from D009
                                                 where D009_D049_Id=D049_Id
                                                   and D009_Data_Ultima_Venda!='0000-00-00'
                                                   and D009_C004_Id='$Empresa_Atual'
                                              order by D009_Data_Ultima_Venda limit 1)");
    }

}


function XD001_reprocessa_historico_geral() {

    global $Empresa_Atual;

    $cSql_D009=mysql_query("select D009_Id,
                                   D009_Quantidade_Estoque,
                                   D009_Valor_Custo_Medio_Unitario,
                                   D001_Codigo_Produto,
                                   D049_Flag_Nacional_Importado,
                                   D009_Valor_Custo_Compra,
                                   D009_Valor_Melhor_Custo
                              from D009 as T1
                         left join D049 on D049_Id=D009_D049_Id
   	  		             left join D001 on D001_Id=D049_D001_Id
                             where D009_C004_Id='$Empresa_Atual'
                          order by D001_Codigo_Produto");

    $Itens=mysql_num_rows($cSql_D009);
    $Count=1;

    while ($mD009=mysql_fetch_array($cSql_D009)) {
            echo "Procesando item $Count de $Itens itens: $mD009[D001_Codigo_Produto] ";

            $Count++;

            $cSql_T001=mysql_query("select T001_Quantidade,
                                           T001_Flag_Operacao,
                                           T001_Flag_Atualiza_Custo,
                                           T001_Quantidade_Saldo,
                                           T001_Valor_Custo_Unitario,
                                           T001_Valor_Custo_Medio_Unitario,
                                           T001_Id,
                                           T001_Flag_MMF,
                                           T001_T008_Id,
                                           T001_Descricao_Operacao,
                                           T008_Valor_Custo_Unitario,
                                           T008_Valor_Preco_Unitario,
                                           T008_IPV,
                                           T007_D006_Id,
                                           T055_Quantidade,
                                           T001_Data_Lancamento,
                                           if(T001_Valor_Custo_Unitario>0,T001_Valor_Custo_Unitario,T001_Valor_Preco_Unitario) T001_Valor_Preco_Unitario,
                                           T008_Quantidade
                                     from T001
                                left join T008 on T008_Id=T001_T008_Id
                                left join T007 on T007_Id=T008_T007_Id
                                left join T055 on T055_Id=T001_T055_Id
                                     where T001_D009_Id='$mD009[D009_Id]'
                                 order by T001_Data_Lancamento,T001_Id");
            $Saldo=0;
            $Custo=0;
            $Custo_Medio=0;
            while ($mT001=mysql_fetch_array($cSql_T001)) {

                  if ($mT001[T001_Flag_Operacao]!='C') {

                      if ($mT001[T001_Flag_Atualiza_Custo]=='S') {
                          if ($mT001[T001_Valor_Custo_Medio_Unitario]<=0) {
                              if (($mT001[T001_Quantidade])>0) {
                                 $Custo_Medio=(($Saldo*$Custo_Medio)+($mT001[T001_Valor_Preco_Unitario]*$mT001[T001_Quantidade]))/($Saldo+$mT001[T001_Quantidade]);
                              } else {
                                 $Custo_Medio=$mT001[T001_Valor_Preco_Unitario];
                              }
                              mysql_query("update T001 set T001_Valor_Custo_Medio_Unitario='$Custo_Medio' where T001_Id='$mT001[T001_Id]'");
                          } else {
                              $Custo_Medio=$mT001[T001_Valor_Custo_Medio_Unitario];
                          }
                          $Custo=$mT001[T001_Valor_Preco_Unitario];
                      }

                      if ($mT001[T001_Flag_Operacao]=='S') {
                          $Saldo-=$mT001[T001_Quantidade];
                      } else if ($mT001[T001_Flag_Operacao]=='E') {
                          $Saldo+=$mT001[T001_Quantidade];
                      } else if ($mT001[T001_Flag_Operacao]=='B') {
                          $Saldo=$mT001[T001_Quantidade];
                      }
                  }

                  if (($mT001[T001_Flag_MMF]=='S') and ($mD009[D049_Flag_Nacional_Importado]=='I')) {
                      $D016=mysql_query("select D016_Valor_Engepecas from D016 where D016_Data<='$mT001[T001_Data_Lancamento]' order by D016_Data desc limit 1");
                      $mD016=mysql_fetch_array($D016);
                      $Custo_Reais=$Custo*$mD016[0];
                  } else {
                      $Custo_Reais=$Custo;
                  }

                  if ($Saldo!=$mT001[T001_Quantidade_Saldo]) {
                      mysql_query("update T001 set T001_Quantidade_Saldo='$Saldo' where T001_Id='$mT001[T001_Id]'");
                  }

                  if (((number_format($mT001[T001_Valor_Custo_Unitario],2)!=number_format($Custo_Reais,2)) and ($mT001[T001_Flag_MMF]=='S')) or ($mT001[T001_Valor_Custo_Unitario]<=0)) {
                      mysql_query("update T001 set T001_Valor_Custo_Unitario='$Custo_Reais' where T001_Id='$mT001[T001_Id]'");
                  }
/*                  if (((number_format($mT001[T008_Valor_Custo_Unitario],2)!=number_format($Custo_Reais,2)) or ($mT001[T008_IPV]<=0)) and ($mT001[T001_Flag_MMF]=='S')) {
                      mysql_query("update T008 set T008_Valor_Custo_Unitario='$Custo_Reais',
                                                   T008_IPV=(T008_Valor_Preco_Unitario-round(T008_Valor_ICMS/T008_Quantidade,2)-T008_Valor_Desconto_Unitario)/round(T008_Valor_Custo_Unitario,2),
                                                   T008_Valor_Total_Custo=T008_Valor_Custo_Unitario*T008_Quantidade where T008_Id='$mT001[T001_T008_Id]'");
                  } */
                  if (($mT001[T001_Valor_Custo_Medio_Unitario]<=0)) { // and ($mT001[T001_Flag_MMF]=='S')) {
                      mysql_query("update T001 set T001_Valor_Custo_Medio_Unitario='$Custo_Medio' where T001_Id='$mT001[T001_Id]'");
                  }

            }

            if ((number_format($Custo_Medio,2)!=number_format($mD009[D009_Valor_Custo_Medio_Unitario],2)) or (number_format($Custo,2)!=number_format($mD009[D009_Valor_Custo_Compra],2))) {
                mysql_query("update D009 set
                                    D009_Valor_Custo_Medio_Unitario='$Custo_Medio',
                                    D009_Valor_Custo_Compra='$Custo'
                              where D009_Id='$mD009[D009_Id]'");
            }

           if ($Saldo!=$mD009[D009_Quantidade_Estoque]) {
                mysql_query("update D009 set D009_Quantidade_Estoque='$Saldo',D009_Quantidade_Estoque_Real=D009_Quantidade_Estoque-D009_Quantidade_Pedido(D009_Id,2)-D009_Quantidade_Pendencia(D009_Id) where D009_Id='$mD009[D009_Id]'");
                echo " Estoque atual: $mD009[D009_Quantidade_Estoque] Estoque Correto: $Saldo";
                $cSql_T066=mysql_query("select
                                   (select sum(1) from T066 where T066_D009_Id=D009_Id) as Total_Estoque,
                                   (select T066_Quantidade_Estoque from T066 where T066_D009_Id=D009_Id and T066_D048_Id=1) as Total_Estoque_1,
                                   (select T066_Quantidade_Estoque from T066 where T066_D009_Id=D009_Id and T066_D048_Id=2) as Total_Estoque_2
                              from D009
                             where D009_Id='$mD009[D009_Id]'");

                $mT066=mysql_fetch_array($cSql_T066);

                if ($mT066[Total_Estoque]>1) {
                  $Saldo_Estoque_1=$Saldo-$mT066[Total_Estoque_2];
                  mysql_query("update T066 set T066_Quantidade_Estoque='$Saldo_Estoque_1' where T066_D009_Id='$mD009[D009_Id]' and T066_D048_Id=1");
                } else {
                  mysql_query("update T066 set T066_Quantidade_Estoque='$Saldo' where T066_D009_Id='$mD009[D009_Id]' and T066_D048_Id=1");
                }
            }
            mysql_query("update D009 set D009_Quantidade_Estoque_Real=D009_Quantidade_Estoque-D009_Quantidade_Pedido(D009_Id,2)-D009_Quantidade_Pendencia(D009_Id) where D009_Id='$mD009[D009_Id]'");

            echo "...<br>";

    }


    mysql_query("update D009 left join D049 on D049_Id=D009_D049_Id
                             left join D001 on D001_Id=D049_D001_Id
                             set D009_Quantidade_Minima_Estoque=D009_Minimo_Maximo(D009_Id,1),
                                 D009_Quantidade_Maxima_Estoque=D009_Minimo_Maximo(D009_Id,2),
                                 D009_Frequencia_Venda=D009_Frequencia(D009_Id),
                                 D009_Data_Ultima_Venda=(select T001_Data_Lancamento
                                                           from T001
                                                          where T001_Flag_MMF='S'
                                                            and T001_D009_Id=D009_Id
                                                       order by T001_Data_Lancamento desc limit 1),
                                 D009_Data_Ultimo_Movimento=(select T001_Data_Lancamento
                                                           from T001
                                                          where (T001_Flag_MMF='S'
                                                             or T001_Flag_Atualiza_Custo='S')
                                                            and T001_D009_Id=D009_Id
                                                       order by T001_Data_Lancamento desc limit 1),
                                 D009_Data_Ultima_Entrada=(select T001_Data_Lancamento
                                                           from T001
                                                          where T001_Flag_Atualiza_Custo='S'
                                                            and T001_D009_Id=D009_Id
                                                       order by T001_Data_Lancamento desc limit 1)
                          where D009_C004_Id='$Empresa_Atual'");

}

/**
 * A FUNCAO ABAIXO RETORNA UM ARRAY QUE SERVIRA PARA SUGERIR AO USUARIO EM QUAIS LOCAIS ALOCAR AS QUANTIDADES
 * VARIAVEIS:
 * 		 - $nD009_Id CHAVE PRIMARIA DA TABELA D009
 * 		 - $nQuantidade QUANTIDADE TOTAL A SER DISTRIBUIDA
 * 		 - $nId_Padrao EH A CHAVE ESTRANGEIRA DA TABELA EM QUESTAO POR EXEMPLO: PARA A NOTA FISCAL RECEBIDA EH O CAMPO T014_Id
 */
function entradaProdutoLocal($nD009_Id,$nQuantidade,$nId_Padrao)
{
	$mDistribuir            = array();
	$nQuantidadeRestante    = $nQuantidade;
	$nQuantidadeDistribuida = 0;
	
	// SQL PADRAO
	$cClausula  = " select ";
	$cClausula .= " D004_Id, ";
	$cClausula .= " D004_Flag_Tipo, ";
	$cClausula .= " D004_Local, ";
	$cClausula .= " T066_Id, ";
	$cClausula .= " T066_Quantidade_Estoque_Liquido, ";
	$cClausula .= " T066_Maximo, ";
	$cClausula .= " T066_Quantidade_Estoque, ";	
	$cClausula .= " T066_Agrupamento ";
	$cClausula .= " from D009 ";
	$cClausula .= " LEFT JOIN T066 ON T066_D009_Id=D009_Id ";
	$cClausula .= " LEFT JOIN D004 ON D004_Id=T066_D004_Id ";
	$cClausula .= " WHERE D009_Id = '$nD009_Id' ";
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// VERIFICACAO 1
	// PEGA TODOS OS LOCAIS RESPEITANDO A QUANTIDADE MAXIMA E ESTOQUE DISPONIVEL
	$cSql_D009 = mysql_query($cClausula." and T066_Maximo>0 and (T066_Maximo-T066_Quantidade_Estoque)>0 order by T066_Maximo asc");
	
	if (mysql_error()) {
		echo 'Erro: '.mysql_error();
		exit;
	}
	
	while($mD009 = mysql_fetch_array($cSql_D009)) {	
	
		// Verifica se estamos em uma locacao antigo (onde o estoque nao era definido por locacao)
		if (empty($mD009['D004_Id']) || empty($mD009['D004_Flag_Tipo'])) {
	
			if (!isset($mRetiradas['antigo'])) {
				$mDistribuir['antigo'] = array($nId_Padrao, $mD009['T066_Id'], 0);
			}
			continue;
		}		
		
		$nQuantidadeDistribuida = $mD009['T066_Maximo']-$mD009['T066_Quantidade_Estoque'];
 
		
		if ($nQuantidadeRestante>0) {
			
			if ($nQuantidadeDistribuida>$nQuantidadeRestante) {
				$nQuantidadeDistribuida = $nQuantidadeRestante;				
			}
		
			$nQuantidadeRestante-=$nQuantidadeDistribuida;
			
			
		}
		$mDistribuir[] = array($nId_Padrao, $mD009['T066_Id'], $nQuantidadeDistribuida);
		
	}
	
	
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// VERIFICACAO 2
	// PEGA TODOS OS LOCAIS QUE NAO TEM QUANTIDADE MAXIMA
	$cSql_D009 = mysql_query($cClausula." and T066_Maximo=0 order by T066_Quantidade_Estoque asc");
	
	if (mysql_error()) {
		echo 'Erro: '.mysql_error();
		exit;
	}
	
	while($mD009 = mysql_fetch_array($cSql_D009)) {
	
		// Verifica se estamos em uma locacao antigo (onde o estoque nao era definido por locacao)
		if (empty($mD009['D004_Id']) || empty($mD009['D004_Flag_Tipo'])) {
	
			if (!isset($mRetiradas['antigo'])) {
				$mDistribuir['antigo'] = array($nId_Padrao, $mD009['T066_Id'], 0);
			}
			continue;
		}
	
		$mDistribuir[] = array($nId_Padrao, $mD009['T066_Id'], $nQuantidadeRestante);
	
	}	
		
	/*
	// A DIVISAO POR PACOTE NAO SERA UTILIZADA POR ENQUANTO
	// MANTIVE AQUI COMENTADO PARA NAO PERDER A LOGICA, AFINAL, QUEBREI A CABECA PARA KARALHO PARA FAZER HEHEHE
	// QUANDO HOUVER A NECESSIDADE O MESMO DEVE SER REANALISADO PARA FUNCIONAR EM CONJUNTO COM O SCRIPT ACIMA
 
	$nQuantidadeSeparada = 0;
	if ($nQuantidadeRestante>0 && $mD009['T066_Agrupamento']>0)
	{
		$nQuantidadeSeparada = $mD009['T066_Agrupamento']*intval($nQuantidadeRestante/$mD009['T066_Agrupamento']);
	}
	
	$nQuantidadeRestante-=$nQuantidadeSeparada;
	$mRetiradas[] = array($nId_Padrao, $mD009['T066_Id'], $nQuantidadeSeparada);
	*/
	
	return 	$mDistribuir;
	
}


/**
 * A FUNCAO ABAIXO RETORNA UM ARRAY QUE SERVIRA PARA IDENTIFICAR OS LOCAIS SE AS QUANTIDADES QUE SERAO SEPARADAS
 * VARIAVEIS:
 * 		 - $nD009_Id CHAVE PRIMARIA DA TABELA D009
 * 		 - $nQuantidade QUANTIDADE TOTAL A SER SEPARADA
 * 		 - $nId_Padrao EH A CHAVE ESTRANGEIRA DA TABELA EM QUESTAO POR EXEMPLO: PARA O PEDIDO EH O CAMPO T006_Id
 */
function saidaProdutoLocal($nD009_Id,$nQuantidade,$nId_Padrao)
{
	$mRetiradas            = array();
	$nQuantidadeRestante   = $nQuantidade;
	$nQuantidadeSeparar    = 0;
	$nQuantidadeNecessaria = 0;

	// SQL PADRAO
	$cClausula  = " select ";
	$cClausula .= " D004_Id, ";
	$cClausula .= " D004_Flag_Tipo, ";
	$cClausula .= " D004_Local, ";
	$cClausula .= " T066_Id, ";
	$cClausula .= " T066_Quantidade_Estoque_Liquido, ";
	$cClausula .= " T066_Maxima_Separacao, ";
	$cClausula .= " T066_Agrupamento ";
	$cClausula .= " from D009 ";
	$cClausula .= " LEFT JOIN T066 ON T066_D009_Id=D009_Id ";
	$cClausula .= " LEFT JOIN D004 ON D004_Id=T066_D004_Id ";
	$cClausula .= " WHERE D009_Id = '$nD009_Id' ";


	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// VERIFICACAO 1
	// PEGA TODOS OS LOCAIS RESPEITANDO A QUANTIDADE MAXIMA DE SEPARACAO
	$cSql_D009 = mysql_query($cClausula." and T066_Maxima_Separacao>0 order by T066_Maxima_Separacao asc");

	if (mysql_error()) {
		msg_alerta('Erro: '.mysql_error());
		exit;
	}

	while($mD009 = mysql_fetch_array($cSql_D009)) {


		// Verifica se estamos em uma locacao antigo (onde o estoque nao era definido por locacao)
		if (empty($mD009['D004_Id']) || empty($mD009['D004_Flag_Tipo'])) {

			if (!isset($mRetiradas['antigo'])) {
				$mRetiradas['antigo'] = array($nId_Padrao, $mD009['T066_Id'], 0);
			}
			continue;
		}

		//echo "<hr>";
		//echo "Local: {$mD009['D004_Local']}<br>";
		//echo "T066_Id: {$mD009['T066_Id']}<br>";
		//echo "Estoque: {$mD009['T066_Quantidade_Estoque_Liquido']}<br>";
		//echo "Quant rest.$nQuantidadeRestante<br>Max: $mD009[T066_Maxima_Separacao]<br>";

		// NO CASO, AQUI ELE FAZ A SEPARACAO CONFORME A QUANTIDADE MAXIMA DEFINIDA NO LOCAL
		if ($nQuantidadeRestante>0) {

			$nQuantidadeSeparar = $nQuantidadeRestante;

			if ($mD009['T066_Maxima_Separacao']<$nQuantidadeRestante) continue;

			if ($mD009['T066_Quantidade_Estoque_Liquido']<=$nQuantidadeRestante) {
				$nQuantidadeSeparar=$mD009['T066_Quantidade_Estoque_Liquido'];
			}

			if ($nQuantidadeSeparar>0) {
				$nQuantidadeRestante-=$nQuantidadeSeparar;
				//echo "Separar: $nQuantidadeSeparar<br>";
				//echo "Restante: $nQuantidadeRestante<br>";
				$mRetiradas[] = array($nId_Padrao, $mD009['T066_Id'], $nQuantidadeSeparar);
			}
		}
	}

	if ($nQuantidadeRestante>0) {

		//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		// VERIFICACAO 2:
		// PEGA OS LOCAIS QUE NAO TEM LIMITE PARA DE SEPARACAO, OU SEJA, SE AINDA EXISTE QUANTIDADE PARA SEPARAR AS MESMAS SERAO TIRADAS DESTA LOCACAO
		$cSql_D009 = mysql_query($cClausula." and T066_Maxima_Separacao<=0");

		if (mysql_error()) {
			msg_alerta('Erro: '.mysql_error());
			exit;
		}

		while($mD009 = mysql_fetch_array($cSql_D009)) {

		    // Verifica se estamos em uma locacao antigo (onde o estoque nao era definido por locacao)
		    if (empty($mD009['D004_Id']) || empty($mD009['D004_Flag_Tipo'])) {
		    
		        if (!isset($mRetiradas['antigo'])) {
		            $mRetiradas['antigo'] = array($nId_Padrao, $mD009['T066_Id'], 0);
		        }
		        continue;
		    }
		    
			//echo "<hr>";
			//echo "Local: {$mD009['D004_Local']}<br>";
			//echo "T066_Id: {$mD009['T066_Id']}<br>";
			//echo "Estoque: {$mD009['T066_Quantidade_Estoque_Liquido']}<br>";
			//echo "Quant rest.$nQuantidadeRestante<br>Max: $mD009[T066_Maxima_Separacao]<br>";

			$nQuantidadeSeparar = $nQuantidadeRestante;
			
			if ($mD009['T066_Quantidade_Estoque_Liquido']<=$nQuantidadeRestante) {
				$nQuantidadeSeparar=$mD009['T066_Quantidade_Estoque_Liquido'];
			}

			if ($nQuantidadeSeparar>0) {

				$nQuantidadeRestante-=$nQuantidadeSeparar;
				//echo "Separar: $nQuantidadeSeparar<br>";
				//echo "Restante: $nQuantidadeRestante<br>";
				$mRetiradas[] = array($nId_Padrao, $mD009['T066_Id'], $nQuantidadeSeparar);

			}

		}

	}

	/*
	// A DIVISAO POR PACOTE NAO SERA UTILIZADA POR ENQUANTO
	// MANTIVE AQUI COMENTADO PARA NAO PERDER A LOGICA, AFINAL, QUEBREI A CABECA PARA KARALHO PARA FAZER HEHEHE
	// QUANDO HOUVER A NECESSIDADE O MESMO DEVE SER REANALISADO PARA FUNCIONAR EM CONJUNTO COM O SCRIPT ACIMA
	if ($nQuantidadeRestante>0 && $mD009['T066_Agrupamento']>0 && $mD009['T066_Quantidade_Estoque_Liquido']>=$mD009['T066_Agrupamento']) {

		$nQuantidadeNecessaria = intval($nQuantidadeRestante/$mD009['T066_Agrupamento']);
		$nQuantidadeDisponivel = intval($mD009['T066_Quantidade_Estoque_Liquido']/$mD009['T066_Agrupamento']);
	
		if ($nQuantidadeDisponivel<$nQuantidadeNecessaria) {
			$nQuantidadeNecessaria=$nQuantidadeDisponivel;
		}
		$nQuantidadeSeparar = $mD009['T066_Agrupamento']*$nQuantidadeNecessaria;
	
		if ($nQuantidadeSeparar>0) {
			$nQuantidadeRestante-=$nQuantidadeSeparar;
			$mRetiradas[] = array($nId, $mD009['T066_Id'], $nQuantidadeSeparar);
		}
	}
	*/
	
	// Separamos e sobrou quantidades para separar....
	if ($nQuantidadeRestante > 0) {
		// se tiver antigo, jogamos tudo para la
		if (isset($mRetiradas['antigo'])) {
			$mRetiradas['antigo'][2] += $nQuantidadeRestante;
		} else {
			// senao... colocamos em algum lugar mesmo que nao tenha estoque
			$mRetiradas[0][2] += $nQuantidadeRestante;
		}
	}
	
	return $mRetiradas;
}

function retornaStrMes($numero)
{
	switch($numero)
	{
		case 1:
		{
			return "Janeiro";
			break;
		}
		case 2:
		{
			return "Fevereiro";
			break;
		}
		case 3:
		{
			return "Março";
			break;
		}
		case 4:
		{
			return "Abril";
			break;
		}
		case 5:
		{
			return "Maio";
			break;
		}
		case 6:
		{
			return "Junho";
			break;
		}
		case 7:
		{
			return "Julho";
			break;
		}
		case 8:
		{
			return "Agosto";
			break;
		}
		case 9:
		{
			return "Setembro";
			break;
		}
		case 10:
		{
			return "Outubro";
			break;
		}
		case 11:
		{
			return "Novembro";
			break;
		}
		case 12:
		{
			return "Dezembro";
			break;
		}
		default:
		{
			break;
		}
	}
}

function validarCFOP($xCFOP)
{
    $xCFOP = str_replace(array(".",","," "),"",$xCFOP);

    $CFOP = "1101, 1102, 1111, 1113, 1116, 1117, 1118, 1120, 1121, 1122, 1124, 1125, 1126, 1151, 1152, 1153, 1154, 1201, 1202, 1203, 1204, 1205, 1206, 1207, 1208, 1209, 1251, 1252, 1253, 1254, 1255, 1256, 1257, 1301, 1302, 1303, 1304, 1305, 1306, 1351, 1352, 1353, 1354, 1355,
1356, 1360, 1401, 1403, 1406, 1407, 1408, 1409, 1410, 1411, 1414, 1415, 1451, 1452, 1501, 1503, 1504, 1505, 1506, 1551, 1552, 1553, 1554, 1555, 1556, 1557, 1601, 1602, 1603, 1604, 1605, 1651, 1652, 1653, 1658, 1659, 1660, 1661, 1662, 1663, 1664, 1901, 1902, 1903,
1904, 1905, 1906, 1907, 1908, 1909, 1910, 1911, 1912, 1913, 1914, 1915, 1916, 1917, 1918, 1919, 1920, 1921, 1922, 1923, 1924, 1925, 1926, 1931, 1932, 1933, 1949, 2101, 2102, 2111, 2113, 2116, 2117, 2118, 2120, 2121, 2122, 2124, 2125, 2126, 2151, 2152, 2153, 2154,
2201, 2202, 2203, 2204, 2205, 2206, 2207, 2208, 2209, 2251, 2252, 2253, 2254, 2255, 2256, 2257, 2301, 2302, 2303,
2304, 2305, 2306, 2351, 2352, 2353, 2354, 2355, 2356, 2401, 2403, 2406, 2407, 2408, 2409, 2410, 2411, 2414, 2415, 2501, 2503, 2504, 2505, 2506, 2551, 2552, 2553, 2554, 2555, 2556, 2557, 2603, 2651, 2652, 2653, 2658, 2659, 2660, 2661, 2662, 2663, 2664, 2901, 2902,
2903, 2904, 2905, 2906, 2907, 2908, 2909, 2910, 2911, 2912, 2913, 2914, 2915, 2916, 2917, 2918, 2919, 2920, 2921, 2922, 2923, 2924, 2925, 2931, 2932, 2933, 2949, 3101, 3102, 3126, 3127, 3201, 3202, 3205, 3206, 3207, 3211, 3251, 3301, 3351, 3352, 3353, 3354, 3355,
3356, 3503, 3551, 3553, 3556, 3651, 3652, 3653, 3930, 3949, 5101, 5102, 5103, 5104, 5105, 5106, 5109, 5110, 5111, 5112, 5113, 5114, 5115, 5116, 5117, 5118, 5119, 5120, 5122, 5123, 5124, 5125, 5151, 5152, 5153, 5155, 5156, 5201, 5202, 5205, 5206, 5207, 5208, 5209,
5210, 5251, 5252, 5253, 5254, 5255, 5256, 5257, 5258, 5301, 5302, 5303, 5304, 5305, 5306, 5307, 5351, 5352, 5353, 5354, 5355, 5356, 5357, 5359, 5360, 5401, 5402, 5403, 5405, 5408, 5409, 5410, 5411, 5412, 5413, 5414, 5415, 5451, 5501, 5502, 5503, 5504, 5505, 5551,
5552, 5553, 5554, 5555, 5556, 5557, 5601, 5602, 5603, 5605, 5606, 5651, 5652, 5653, 5654, 5655, 5656, 5657, 5658, 5659, 5660, 5661, 5662, 5663, 5664, 5665, 5666, 5901, 5902, 5903, 5904, 5905, 5906, 5907, 5908, 5909, 5910, 5911, 5912, 5913, 5914, 5915, 5916, 5917,
5918, 5919, 5920, 5921, 5922, 5923, 5924, 5925, 5926, 5927, 5928, 5929, 5931, 5932, 5933, 5949, 6101, 6102, 6103,
6104, 6105, 6106, 6107, 6108, 6109, 6110, 6111, 6112, 6113, 6114, 6115, 6116, 6117, 6118, 6119, 6120, 6122, 6123, 6124, 6125, 6151, 6152, 6153, 6155, 6156, 6201, 6202, 6205, 6206, 6207, 6208, 6209, 6210, 6251, 6252, 6253, 6254, 6255, 6256, 6257, 6258, 6301, 6302,
6303, 6304, 6305, 6306, 6307, 6351, 6352, 6353, 6354, 6355, 6356, 6357, 6359, 6401, 6402, 6403, 6404, 6408, 6409, 6410, 6411, 6412, 6413, 6414, 6415, 6501, 6502, 6503, 6504, 6505, 6551, 6552, 6553, 6554, 6555, 6556, 6557, 6603, 6651, 6652, 6653, 6654, 6655, 6656,
6657, 6658, 6659, 6660, 6661, 6662, 6663, 6664, 6665, 6666, 6901, 6902, 6903, 6904, 6905, 6906, 6907, 6908, 6909, 6910, 6911, 6912, 6913, 6914, 6915, 6916, 6917, 6918, 6919, 6920, 6921, 6922, 6923, 6924, 6925, 6929, 6931, 6932, 6933, 6949, 7101, 7102, 7105, 7106,
7127, 7201, 7202, 7205, 7206, 7207, 7210, 7211, 7251, 7301, 7358, 7501, 7551, 7553, 7556, 7651, 7654, 7930, 7949, 7911";

    if (strpos("$CFOP","$xCFOP")===false)
    {
       return false;
    }
    else
    {
       return true;
    }
}


function PegaRelacionamento($Entidade,$Id_Entidade,$Excecao='')
{
    if ($Id_Entidade<=0)
    {
       msg_alerta("Não foi definida a variavel Entidade_Id");
       exit;
    }
    else
    {

        $cSql=sql_query("show tables");
        while ($mTabela=sql_fetch_array($cSql)) {
             $mTabela[0]=strtoupper($mTabela[0]);
             if ($mTabela[0]==$Entidade) {
                continue;
             }
             $cSql2=sql_query("desc $mTabela[0] '%$Entidade%'");
             while ($mCampos=sql_fetch_array($cSql2)) {
                   //$mCampos[0]=strtoupper($mCampos[0]);
                   if (strpos($mCampos[0],"$Entidade")!==false) {
                       $mT[]=$mTabela[0];
                       $mTC[]=$mCampos[0];

                   }
             }
        }

        for ($x=0;$x<count($mT);$x++) {
            $cSql=sql_query("show create table $mT[$x]");
            $mSql=mysql_fetch_array($cSql);
            $cSql="select $mTC[$x] from $mT[$x] where $mTC[$x]=$Id_Entidade limit 0,1";
            //echo $cSql."<BR>";
            $resultado=sql_query("$cSql");
            if (sql_num_rows($resultado)>0) {
               if (strpos($mSql[1],'ON DELETE CASCADE')!=0) {
               } else {
                  if (strpos(" ".$Excecao,$mT[$x])<=0)
                  {
                      return $mT[$x];
                      break;
                  }
               }
            }
        }
    }
}

function link_ordem($Titulo,$Campo,$Ordem,$Desc)
{
   if ($Ordem==$Campo)
   {
       If ($Desc!='desc')
       {
          $imagem="<img src='../imagens/gif/ordem_decrescente.gif' title='Coluna classificada em ordem crescente ' height=7 hspace=3 width=7 border=0>";
       } else
       {
          $imagem="<img src='../imagens/gif/ordem_crescente.gif' title='Coluna classificada em ordem decrescente ' height=7 hspace=3 width=7 border=0>";
       }
       return "<font onclick=\"ordem('$Campo');\" style='cursor:pointer;font-weight:bold'><b>$Titulo</b></font>$imagem";
   }
   else
   {
       return "<font onclick=\"ordem('$Campo');\" style='cursor:pointer'>$Titulo</font>";
   }
}

function Criar_Imagem_Redimencionada($cArquivo_Original,$cArquivo_Destino,$nLargura,$nAltura)
{

 	//-- VERIFICA QUAL É A EXTENSAO DO ARQUIVO PARA CRIAR UMA NOVA IMAGEM
	$cExtensao = substr($cArquivo_Original,strrpos($cArquivo_Original,".")+1,4);
	$cExtensao = StrToLower($cExtensao);

	if (($cExtensao=='jpg') or ($cExtensao == 'jpeg'))
	{
		if (!$Img = imagecreatefromjpeg($cArquivo_Original))
		{
		   msg_alerta("Essa imagem não é jpg");
		   exit;
		}
	}
	else if ($cExtensao == 'png')
	{
		if (!$Img = imagecreatefrompng($cArquivo_Original))
		{
		   msg_alerta("Essa imagem não é png");
		   exit;
		}
	}
	elseif ($cExtensao == 'gif')
	{
		if (!$Img = imagecreatefromgif($cArquivo_Original))
		{
		   msg_alerta("Essa imagem não é gif");
		   exit;
		}
	}

    // PEGA O TAMANHO DA IMAGEM ORIGINAL
    $nLargura_Imagem_Original = imagesx($Img);
    $nAltura_Imagem_Original  = imagesy($Img);

    $nEscala = min($nLargura/$nLargura_Imagem_Original, $nAltura/$nAltura_Imagem_Original);

    // Se a imagem e maior que o permitido, encolhe ela!
    if ($nEscala < 1)
	{
        $nNovo_Width = round($nEscala * $nLargura_Imagem_Original);
        $nNovo_Height = round($nEscala * $nAltura_Imagem_Original);
    }
    else
    {
        $nNovo_Width = $nLargura;
        $nNovo_Height = $nAltura;
    }

    $Img_Tmp = imagecreatetruecolor($nNovo_Width, $nNovo_Height);

	//-- CRIA A IMAGEM
	imagecopyresampled($Img_Tmp,
						$Img,
						0,
						0,
						0,
						0,
						$nNovo_Width,
						$nNovo_Height,
						$nLargura_Imagem_Original,
						$nAltura_Imagem_Original);

    imagejpeg($Img_Tmp,$cArquivo_Destino,90);
    imagedestroy($Img);
    imagedestroy($Img_Tmp);

    RETURN TRUE;
}



function gdVersion($user_ver = 0)
{
    if (! extension_loaded('gd')) { return; }
    static $gd_ver = 0;
    // Just accept the specified setting if it's 1.
    if ($user_ver == 1) { $gd_ver = 1; return 1; }
    // Use the static variable if function was called previously.
    if ($user_ver !=2 && $gd_ver > 0 ) { return $gd_ver; }
    // Use the gd_info() function if possible.
    if (function_exists('gd_info')) {
        $ver_info = gd_info();
        preg_match('/\d/', $ver_info['GD Version'], $match);
        $gd_ver = $match[0];
        return $match[0];
    }
    // If phpinfo() is disabled use a specified / fail-safe choice...
    if (preg_match('/phpinfo/', ini_get('disable_functions'))) {
        if ($user_ver == 2) {
            $gd_ver = 2;
            return 2;
        } else {
            $gd_ver = 1;
            return 1;
        }
    }
    // ...otherwise use phpinfo().
    ob_start();
    phpinfo(8);
    $info = ob_get_contents();
    ob_end_clean();
    $info = stristr($info, 'gd version');
    preg_match('/\d/', $info, $match);
    $gd_ver = $match[0];
    return $match[0];
}


function svn_info($host="")
{
    eval("$"."Revisao_SVN=`svn info $host ` ;");

    $PosInicial=strpos($Revisao_SVN,"Revisão:");
    if ($PosInicial==0)
    {
       $PosInicial=strpos($Revisao_SVN,"Revisão:");
    }
    if ($PosInicial==0)
    {
       $PosInicial=strpos($Revisao_SVN,"Revision:");
    }
    $Revisao_SVN=substr($Revisao_SVN,$PosInicial+9,strlen($Revisao_SVN));
    $PosFinal=strpos($Revisao_SVN,"Tipo");
    if ($PosFinal==0)
    {
       $PosFinal=strpos($Revisao_SVN,"Node");
    }
    $Revisao_SVN=trim(substr($Revisao_SVN,0,$PosFinal));
    return $Revisao_SVN;
}

function pegaValidadeCertificadoA1($Certificado,$Senha,$String='N')
{
   if ($String=='N')
   {
   		$key  = file_get_contents($Certificado);
   }
   else
   {
   		$key = $Certificado;
   }		
   $resp = openssl_pkcs12_read ($key,$x509certdata,$Senha);
   $data = openssl_x509_parse($x509certdata['cert']);
   $in=$data['validTo'];

   $year  = substr($in, 0, 2); /* NOTE: Yes, this returns a two digit year */
   $month = substr($in, 2, 2);
   $day   = substr($in, 4, 2);
   $hour  = substr($in, 6, 2);
   $min   = substr($in, 8, 2);
   $sec   = substr($in, 10, 2);
   if (!empty($in))
   {
       $cRetorno=mktime($hour, $min, $sec, $month, $day, $year);
   }
   else
   {
       $cRetorno='';
   }
   return $cRetorno;
}

function validarAcesso($nC009_Id)
{
    global $Usuario_Atual;
    global $Empresa_Atual;
        
    $cSql_C010=mysql_query("select C010_Id from C010,C019 where C019_C021_Id=C010_C021_Id and C019_C004_Id='$Empresa_Atual' and C010_C009_Id='$nC009_Id' and C010_Acesso_Liberado='S' and C019_C007_Id='$Usuario_Atual'");

    if (sql_num_rows($cSql_C010)>0) {
        return true;
    } else {
        return false;
    }        
}

function pedidoSeguirOrdemSeparacao($nT005_Id) 
{
    
    if (validarAcesso('908')) {
        return false;        
    }
    
    while (true) {
        
        $cClausula  = " select ";
        $cClausula .= " T005_Id ";
        $cClausula .= " from T005 ";
        $cClausula .= " where T005_Id>'$nT005_Id' ";
        $cClausula .= " and T005_Flag_Almoxarifado='S' ";
        $cClausula .= " and T005_Flag_Status=1 ";
        $cSql = mysql_query($cClausula);
        
        if (mysql_num_rows($cSql)>0) {
        
            $mSql = mysql_fetch_array($cSql);
            
            $isTravado = pedidoItemTravadoBalanco($mSql['T005_Id']);
            
            if ($isTravado) {
                
                //-- Se o pedido nao pode ser mandado pra frente pega o anterior a ele, (se existir) 
                $nT005_Id = $mSql['T005_Id'];
                continue;
                
            } else {
                
                return true;
            }
            
        } else {
            
            return false;
            
        }
        $n++;
        if ($n>100) break;
                
    }
    return false;
}

function pedidoItemTravadoBalanco($nT005_Id)
{
    //-- Verifica se o item esta no balanco e nao permite que seja emitida a nota ou separado.
     
    $cClausula  = " select ";
    $cClausula .= " T029_Id ";
    $cClausula .= " from T005,T006,T030,T029 ";
    $cClausula .= " where T005_Id='$nT005_Id' ";
    $cClausula .= " and T006_T005_Id=T005_Id ";
    $cClausula .= " and T030_D009_Id=T006_D009_Id ";
    $cClausula .= " and T030_T029_Id=T029_Id ";
    $cClausula .= " and T029_Flag_Coletor='S' ";
    $cClausula .= " and T029_Flag_Ok!='S' ";
    $cSql = mysql_query($cClausula);
    
    if (mysql_num_rows($cSql)>0) {
        return true;
    } else {
        return false;
    }            
}

function notaEntradaItemTravadoBalanco($nT013_Id)
{
    //-- Verifica se o item esta no balanco e nao permite que seja emitida a nota ou separado.

    $cClausula  = " select ";
    $cClausula .= " T029_Id ";
    $cClausula .= " from T013,T014,T030,T029 ";
    $cClausula .= " where T013_Id='$nT013_Id' ";
    $cClausula .= " and T014_T013_Id=T013_Id ";
    $cClausula .= " and T030_D009_Id=T014_D009_Id ";
    $cClausula .= " and T030_T029_Id=T029_Id ";
    $cClausula .= " and T029_Flag_Coletor='S' ";
    $cClausula .= " and T029_Flag_Ok!='S' ";
    $cSql = mysql_query($cClausula);

    if (mysql_num_rows($cSql)>0) {
        return true;
    } else {
        return false;
    }
}
?>