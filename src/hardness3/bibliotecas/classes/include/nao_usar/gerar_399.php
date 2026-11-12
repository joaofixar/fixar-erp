<?php
namespace hardness;
/*

Programador: Evantoni Fuchs
Data: 13-05-2010

Observa��o Importante!
  * Esse programa foi extraido da p�gina boletophp.com.br, o mesmo foi modificado conforme a necessidade do sistema hardness
  * Esse programa � usado como INCLUDE atrav�s do programa: gravar_formulario_boleto.php;
  * O mesmo include depende do c�digo banc�rio (D062) que ser� selecionado na conta banc�ria (D007);
  * O programa layout_399.php � a IMPRESS�O do boleto e � chamado atrav�s do gerar_boleto.php que tem a classe
    generica com os campos usados no layout;
  * O programa layout_399.php nunca � chamado atrav�s dos programas: funcoes_399.php, gerar_399.php ou gravar_formulario_boleto.php;

*/

//include('../../old/lib/funcoes.php');
//echo 'parte 1<br>';
//echo 'include='."./include/funcoes_".$mD007['D062_Numero_Banco'].".php<br>";
//-- Utiliza as funcoes encontradas na internet (boletophp) para calculos de digito verificador e codigo de barras
require_once("./include/funcoes_".$mD007['D062_Numero_Banco'].".php");
//echo 'parte l<br>';
$codigobanco = "399";
$codigo_banco_com_dv = b399_geraCodigoBanco($codigobanco);
$nummoeda = "9";
//echo 'parte m<br>';
log("Data vencimento: ".$T117_Data_Vencimento);
$fator_vencimento = b399_fator_vencimento(troca_data_dma($T117_Data_Vencimento));
//echo 'parte n<br>'; 
$nValor_Total=number_format($nValor_Total,2,'','');
//echo 'parte o<br>';
log("Valor total: ".$nValor_Total);
$valor = b399_formata_numero($nValor_Total,10,0,"valor");
//echo 'parte p<br>';
if ($mD007['D007_Flag_Tipo_Registro']=='1')
{
    ///////////////////////////////////////////////////////////////////////
    //-- COM REGISTRO
    //-- ******** NAO HOMOLOGADO ***********

    /*
      1 - ESTRUTURA DO N�MERO BANC�RIO (Nosso N�mero)
        E E E E E N N N N N D
        Onde:
        EEEEE = C�digo do Cliente no Banco (N�. do Range)
              C�digo num�rico a ser fornecido pelo Banco por ocasi�o do in�cio dos testes.
              Este c�digo tem amarra��o cadastral com a conta de cobran�a, portanto dever�o ser
              obtidos tantos c�digos quantos forem �s contas de cobran�a a serem utilizadas para a Cobran�a
              Diretiva ou ap�s utilizar todos os n�meros sequenciais do documento.
        NNNNN = N�mero sequencial do documento / t�tulo.
              Uso a crit�rio do cliente e quando utilizar todos, deve solicitar um novo n�mero
              Range.
        D = D�gito verificador de seguran�a.
          � calculado pelo M�dulo 11, com pesos de 2 a 7 da direita para a esquerda.
    */
    
    //-- Cria o "Nosso Numero"
    //echo 'parte q<br>';
    if($g['C031']['atualizarNossoNumeroBoleto'] != 'S' && !empty($mD007['T117_Codigo_Barras'])){
        $nossonumero = $mD007['T117_Nosso_Numero'];
    }else{
        $nossonumero  = STR_PAD($mD007['D007_Numero_Range'],5,"0",STR_PAD_LEFT);
        $nossonumero .= STR_PAD($mD007['D007_Nosso_Numero'],5,"0",STR_PAD_LEFT);
        $nossonumero  = $nossonumero.b399_modulo_11($nossonumero,7);
    }


	//echo 'parte r<br>'; 
    //-- Obtem o codigo da agencia, formatando sempre para 4 carateres
    $agencia_codigo=substr(STR_PAD($mD007['D007_Numero_Agencia'],4,"0",STR_PAD_LEFT),0,4);
    //echo 'parte s<br>'; 
    //-- Obtem o numero da conta, formatando sempre para 5 carateres
    $conta_codigo=SUBSTR(STR_PAD($mD007['D007_Numero_Conta'],5,"0",STR_PAD_LEFT),0,5);
    //echo 'parte t<br>'; 
    //-- Obtem o digito verificado da conta, formatando sempre para 2 carateres
    $dv_conta=SUBSTR(STR_PAD($mD007['D007_Digito_Verificador_Conta'],2,"0",STR_PAD_LEFT),0,2);
	//echo 'parte u<br>'; 
    // 43 numeros para o calculo do digito verificador do codigo de barras
    // 44 com digito verificador
    $barra  = "$codigobanco";           //-- C�digo do HSBC na compensa��o
    $barra .= "$nummoeda";              //-- Tipo de Moeda.
    $barra .= "$fator_vencimento";      //-- Fator de vencimento (obrigat�rio a partir de 03/07/2000)
    $barra .= "$valor";                 //-- Valor do documento ou t�tulo.
    $barra .= "$nossonumero";           //-- N�mero Banc�rio (Nosso N�mero)
    $barra .= "$agencia_codigo";        //-- N�mero da Ag�ncia e Conta Corrente:  4 posi��es ( 31 a 34 ) = C�digo da Ag�ncia.
    $barra .= "$conta_codigo$dv_conta"; //-- Conta de cobran�a 7 posi��es ( 35 a 41 ).
    $barra .= "00";                     //-- C�digo da carteira = �00�
    $barra .= "1";                      //-- C�digo do aplicativo da Cobran�a (COB) = �1�
    //echo 'parte 2<br>';
    if (strlen($barra)!=43)
    {
        log("Código banco: ".$codigobanco." - Tipo moeda: ".$nummoeda." - Fator Vencimento: ".$fator_vencimento." - Valor: ".$valor." - Nosso número: ".$nossonumero." - Agencia e conta: ".$agencia_codigo." - Conta cobrança: ".$conta_codigo." - DV: ".$dv_conta);
        mysql_query("ROLLBACK");
        return "Codigo de barras invalido: ".strlen($barra)." caracteres/pEsse codigo deve ter 43 carateres. ";
    }

    //-- Cria o digito verificado
    $dv = b399_digitoVerificador_barra($barra);
    
    // Insere o digito verificador
    $codigo_barras = substr($barra,0,4) . $dv . substr($barra,4);

    if (strlen($codigo_barras)!=44)
    {
        log("Código banco: ".$codigobanco." - Tipo moeda: ".$nummoeda." - Fator Vencimento: ".$fator_vencimento." - Valor: ".$valor." - Nosso número: ".$nossonumero." - Agencia e conta: ".$agencia_codigo." - Conta cobrança: ".$conta_codigo." - DV: ".$dv_conta);
        mysql_query("ROLLBACK");
        return "Codigo de barras invalido: ".strlen($codigo_barras)." caracteres/pEsse codigo deve ter 44 carateres. ";
    }

    $linha_digitavel = b399_monta_linha_digitavel_com_registro($codigo_barras);

    $cClausula  = " update T117 set ";
    $cClausula .= " T117_Nosso_Numero='$nossonumero', ";
    $cClausula .= " T117_Linha_Digitavel='$linha_digitavel', ";
    $cClausula .= " T117_Codigo_Barras='$codigo_barras' ";
    $cClausula .= " where T117_Id='$nT117_Id' ";
    //echo $cClausula;
    mysql_query($cClausula);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       mysql_query("ROLLBACK");
       return "ERRO: $cErro/p/pClausula: $cClausula";
       exit;
    }
    //echo 'parte 3<br>';
}
else
{
	//echo 'entrou no else<br>';
    ///////////////////////////////////////////////////////////////////////
    //-- SEM REGISTRO
    //-- ******** NAO HOMOLOGADO ***********

    //codigocedente deve possuir 7 caracteres
    $codigocedente = b399_formata_numero($mD007['D007_Codigo_Cedente'],7,0);

    $ndoc = $nT117_Id;

    // n�mero do documento (sem dvs) � 13 digitos
    $nnum = b399_formata_numero($nT117_Id,13,0);
    // nosso n�mero (com dvs) � 16 digitos
    $nossonumero = b399_geraNossoNumero($nnum,$codigocedente,$cT117_Data_Vencimento,'4');

    $vencjuliano = b399_dataJuliano($cT117_Data_Vencimento);
    $app = "2";

    // 43 numeros para o calculo do digito verificador do codigo de barras
    $barra = "$codigobanco$nummoeda$fator_vencimento$valor$codigocedente$nnum$vencjuliano$app";
    $dv = b399_digitoVerificador_barra($barra, 9, 0);
    // Numero para o codigo de barras com 44 digitos
    $codigo_barras = substr($barra,0,4) . $dv . substr($barra,4);

    $agencia_codigo = $codigocedente;

    $linha_digitavel = b399_monta_linha_digitavel_sem_registro($codigo_barras);


    $cClausula  = " update T117 set ";
    $cClausula .= " T117_Nosso_Numero='$nossonumero', ";
    $cClausula .= " T117_Linha_Digitavel='$linha_digitavel', ";
    $cClausula .= " T117_Codigo_Barras='$codigo_barras' ";
    $cClausula .= " where T117_Id='$nT117_Id' ";
    mysql_query($cClausula);
    $cErro = mysql_error();
    if (!empty($cErro))
    {
       mysql_query("ROLLBACK");
       return "ERRO: $cErro/p/pClausula: $cClausula";
    }
}

?>
