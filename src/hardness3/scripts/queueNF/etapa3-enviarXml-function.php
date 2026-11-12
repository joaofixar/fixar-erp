<?php
namespace hardness;

/**
 * Retorna a chave efetivamente autorizada quando a SEFAZ informa duplicidade.
 *
 * No cStat 204 a chave persistida na nota e a mesma chave autorizada. No 539
 * a SEFAZ devolve, na mensagem, a chave da NF-e que ja havia sido autorizada.
 */
function obterChaveAutorizadaDuplicidadeNFe($codigoRetorno, $retornoSefaz, $chaveAtual)
{
    if (!preg_match('/^\d{44}$/', $chaveAtual)) {
        return false;
    }

    if ((string) $codigoRetorno === '204') {
        return $chaveAtual;
    }

    if ((string) $codigoRetorno !== '539') {
        return false;
    }

    // Formato usual: "... [chNFe: 352... ]".
    if (preg_match('/(?:chNFe|chave\s+de\s+acesso)\D*(\d{44})/i', $retornoSefaz, $chave)) {
        return $chave[1];
    }

    // Mantem compatibilidade com UFs que retornam somente os 44 digitos.
    if (preg_match('/(?<!\d)(\d{44})(?!\d)/', $retornoSefaz, $chave)) {
        return $chave[1];
    }

    return false;
}

function mostrarMsgErro($nNumeroErro,$cArquivo_Assinado="",$cRetornoSefaz="")
{
	global $nT007_Id;
	global $Usuario_Atual;
	global $oNFE;
  global $xml;
	
    $cClausula = " select * from global.T136 where T136_Numero_Erro='$nNumeroErro'";
    $cSql = mysqli_query($cClausula);
    $mT136 = mysql_fetch_array($cSql);

    if (empty($mT136))
    {
    	msg_erro_nfe(200, $nNumeroErro, false, $cRetornoSefaz);
    	/*$mT136['T136_Numero_Erro']='0';
    	$mT136['T136_Descricao_Erro']='N�O FOI POSS�VEL CONEX�O COM A SEFAZ';
    	$mT136['T136_Procedimento']='O SERVI�O NFE SEFAZ EST� COM PROBLEMAS, NESTES CASOS NOSSO SUPORTE J� DEVE ESTAR EM CONTATO COM O SUPORTE SEFAZ.<BR>PEDIMOS QUE VOC� AGUARDE E TENTE ENVIAR A NFE DENTRO DE ALGUNS MINUTOS.';*/
    }
    $cClausula  = " select ";
    $cClausula .= " T007_Numero_Protocolo_Nfe, ";
    $cClausula .= " T007_Flag_ACP, ";
    $cClausula .= " T007_C004_Id, ";
    $cClausula .= " T007_D024_Id, ";
    $cClausula .= " T007_Chave_Acesso_Nfe, ";
    $cClausula .= " T007_Numero_Nota_Fiscal, ";
    $cClausula .= " C020_NFe_SVC_Ativado, ";
    $cClausula .= " C020_NFCe_SVC_Ativado, ";
    $cClausula .= " T007A_Flag_Contingencia_Offline ";
    $cClausula .= " from T007 ";
    $cClausula .= " LEFT JOIN C020 ON C020_C004_Id = T007_C004_Id ";
    $cClausula .= " LEFT JOIN T007A ON T007A_T007_Id = T007_Id ";
    $cClausula .= " where T007_Id='$nT007_Id' ";
    $cSql_T007=mysqli_query($cClausula);
    $mT007=mysql_fetch_array($cSql_T007);
    
    // $cRetornoSefazs

    
    /*
    Ir� para o grid
   if (empty($mT136[T136_Procedimento]))
   {
       echo "Caso n�o saiba como solucionar o problema, entre em contato com o suporte e informe o erro. Suporte Hardness 41 3344-2938";
   }
   else
   {
       echo $mT136[T136_Procedimento].". Suporte Hardness 41 3344-2938";
   }
   */
  $apagaXMLAssinado = false;
	if ($mT136['T136_Flag_Refazer_Chave_Acesso']=='S' and $nT007_Id>0)
	{
		if (!empty($mT007['T007_Numero_Protocolo_Nfe']))
		{
			msg_erro_nfe(201, $nNumeroErro, true, $cRetornoSefaz);
			return false;
		}

    if($mT007['T007_Flag_ACP'] == '6' || ($mT007['T007_Flag_ACP'] == '8' && ($mT007['C020_NFCe_SVC_Ativado'] == 'S' || $mT007['T007A_Flag_Contingencia_Offline'] == 'S'))){
      // So apaga se for NFe
      $apagaXMLAssinado = true;
    }
		
		$cClausula  = " update T007 set ";
		$cClausula .= " T007_Chave_Acesso_Nfe='',";
		$cClausula .= " T007_Numero_Recibo_Nfe=NULL,";
		$cClausula .= " T007_Data_Hora_Recebimento_Nfe='0000-00-00 00:00:00' ";
		$cClausula .= " where T007_Id='$nT007_Id'";
		mysqli_query($cClausula);
		$cErro = mysql_error();
		if (!empty($cErro))
		{
			msg_erro_nfe(117);
		   	return false;
    }
    
    $modelo = ($mT007['T007_Flag_ACP'] == '6') ? '55' : '65';
    mysqli_query("insert into T127 (T127_C004_Id,
                                   T127_D024_Id,
                                   T127_Chave_Acesso_Nfe,
                                   T127_Numero_Nota_Fiscal,
                                   T127_Codigo_Retorno,
                                   T127_Numero_Recibo_Nfe,
                                   T127_Data_Transmicao,
                                   T127_Retorno_Sefaz,
                                   T127_Xml_Retorno,
                                   T127_Modelo,
                                   T127_T007_Id
                                   ) values (
                                   '{$mT007['T007_C004_Id']}',
                                   '{$mT007['T007_D024_Id']}',
                                   '{$mT007['T007_Chave_Acesso_Nfe']}',
                                   '{$mT007['T007_Numero_Nota_Fiscal']}',
                                   '{$nNumeroErro}',
                                   '{$oNFE->nRec}',
                                   now(),
                                   'Limpou chave de acesso tabela T136 (Id: {$mT136['T136_Id']})',
                                   '{$cArquivo_Assinado}',
                                   '{$modelo}',
                                   '{$nT007_Id}')");

    $cErro = mysql_error();
    if (!empty($cErro))
    {
        msg_erro_nfe(111);
        return false;
    }
	}
									    
  // $cErro=$oNFE->getErro();
	/*
	Vai para o grid
   if (($mT136['T136_Flag_Refazer_Chave_Acesso']=='S' or $Usuario_Atual=='63') and $nT007_Id>0)
   {
     echo "<tr>";
       echo "<td align=center>";
         echo "<input type='button' value='Corrija o erro e clique aqui para tentar novamente' class='baixar' style='width:305' onclick=\"location.replace('refazer_envio_nfe.php?T007_Id=$nT007_Id');\"; />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
         if ($mT007[T007_Flag_SCAN]=='S')
         {  
             echo "<input type='button' value='Enviar normal' class='baixar' style='width:120' onclick=\"location.replace('refazer_envio_nfe.php?T007_Id=$nT007_Id&Scan=N');\"; />";
         }
         else 
         {
            echo "<input type='button' value='Enviar por scan' class='atencao' style='width:125' onclick=\"location.replace('refazer_envio_nfe.php?T007_Id=$nT007_Id&Scan=S');\"; />";
         }
       echo "</td>";
     echo "</tr>";
   }
   else if ($nT007_Id>0)
   {
     echo "<tr>";
        echo "<td align=center>";
            echo "<font class='fonte' style='color:red;font-size:12px'>ATEN��O! S� utilize esta op��o SCAN caso a SEFAZ do seu estado esteja indispon�vel!</font><br><input type='button' value='Enviar por scan' class='atencao' style='width:125' onclick=\"location.replace('refazer_envio_nfe.php?T007_Id=$nT007_Id&Scan=S');\"; />";
       echo "</td>";
     echo "</tr>";
   }
   */
                     
                     
     if (empty($cArquivo_Assinado))
     {
      $cClausula  = " select ";
		  $cClausula .= " T007_Chave_Acesso_Nfe ";
		  $cClausula .= " from T007 ";
		  $cClausula .= " where T007_Id='$nT007_Id' ";
		  $cSql_T007=mysqli_query($cClausula);
      $mT007=mysql_fetch_array($cSql_T007);
      
      $cArquivo_Assinado=$oNFE->getPastaArquivoXML()."assinado/NFe".$mT007['T007_Chave_Acesso_Nfe'].'.xml';
     }
                     
     /*if (!empty($cArquivo_Assinado))
     {
	     echo "<tr>";                                                                          
	         echo "<td align=left>";                                               
	            echo "<font style='font-size:12px;font-family:arial,verdana;color:gray;font-weight:normal;'>Arquivo XML: <a href='".$cArquivo_Assinado."'><font color=#0094CE>".basename($cArquivo_Assinado)."</a>";
	         echo "</td>";	
	     echo "</tr>";
     }*/
     
     $xml = file_get_contents($cArquivo_Assinado);

     msg_erro_nfe(202, $nNumeroErro, true, $cRetornoSefaz);

     // Limpa o XML da pasta
    if ($apagaXMLAssinado && file_exists($cArquivo_Assinado)){
      unlink($cArquivo_Assinado);
    }
    
     /*
                     if ($Usuario_Atual!='63')
                     {
                                 echo "<tr>";
                                        echo "<td align=right colspan=10>";

                                            $cClausula  = " select ";
                                            $cClausula .= " T127_Id, ";                                            
                                            $cClausula .= " T127_Data_Transmicao, ";
                                            $cClausula .= " T127_Codigo_Retorno, ";
                                            $cClausula .= " T127_Chave_Acesso_Nfe, ";
                                            $cClausula .= " T127_Numero_Recibo_Nfe, ";
                                            $cClausula .= " T127_Retorno_Sefaz ";
                                            $cClausula .= " from T127 ";
                                            $cClausula .= " where T127_Numero_Nota_Fiscal='$mT007[T007_Numero_Nota_Fiscal]' ";
                                            $cClausula .= " and T127_C004_Id='$mT007[T007_C004_Id]' ";
                                            $cClausula .= " order by T127_Id desc,T127_Data_Transmicao desc  ";
                                            $cSql_T127=mysqli_query($cClausula);
                                            
                                            echo "<table width=100% cellspacing=0 style='empty-cells:show'>";
                                            
                                              echo "<tr>";
                                                 echo "<td class='td_titulo' width=105>";
                                                     echo "Data";
                                                 echo "</td>";
                                                 echo "<td class='td_titulo'>";
                                                     echo "Log";
                                                 echo "</td>";
                                                 echo "<td class='td_titulo'>";
                                                     echo "Chave Acesso";
                                                 echo "</td>";
                                                 echo "<td class='td_titulo'>";
                                                     echo "Recibo";
                                                 echo "</td>";
                                              echo "</tr>";
                                                
                                            while ($mT127=mysql_fetch_array($cSql_T127))
                                            {
                                            	  echo "<tr>";
                                            	    echo "<td class='td_itens'>";
                                                      echo Troca_DataHora_dma($mT127[T127_Data_Transmicao],'/');
                                                    echo "</td>";
                                            	    echo "<td class='td_itens'>";
                                                      echo "$mT127[T127_Retorno_Sefaz]";
                                                    echo "</td>";
                                            	    echo "<td class='td_itens'>";
                                                      echo "<a href=\"javascript:location.replace('refazer_envio_chave_antiga.php?T127_Id=".md5($mT127[T127_Id])."');\" title='Refaz o envio usando esta chave de acesso' alt='Refaz o envio usando esta chave de acesso'  />$mT127[T127_Chave_Acesso_Nfe]</a>";
                                                    echo "</td>";
                                            	    echo "<td class='td_itens'>";
                                                      echo "$mT127[T127_Numero_Recibo_Nfe]";
                                                    echo "</td>";
                                                 echo "</tr>";  
                                            }
                                            
                                            echo "</table>";
                                            
                                        echo "</td>";
                                 echo "</tr>";
                     	
                     }
                     else
                     {
	                     echo "<tr>";                                                                          
	                       echo "<td align=left>";
	                       

//-- EMPRESA EMITENTE
$C004 = mysqli_query("select *
                       from $cBd_Configuracao.C004
                  left join D020 on D020_Id=C004_D020_Id
                  left join D018 on D018_Id=C004_D018_Id
                      where C004_Id = '$Empresa_Atual'");
$mC004 =mysql_fetch_array($C004);


    //-- CONSULTA DA NOTA FISCAL
    $cClausula  = " select ";
    $cClausula .= " * ";
    $cClausula .= " from T007 ";
    $cClausula .= " left join D024 on D024_Id=T007_D024_Id ";
    $cClausula .= " left join D021 on D021_Id=T007_D021_Id ";
    $cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
    $cClausula .= " where T007_Id='$nT007_Id' ";
    $cSql_T007=mysqli_query($cClausula);
    echo mysql_error();
    $mT007=mysql_fetch_array($cSql_T007);


$cC004=mysqli_query("select * from C004 left join D020 on D020_Id=C004_D020_Id left join D018 on D018_Id=D020_D018_Id where C004_Id='$mT007[T007_C004_Id]'");
$mC004=mysql_fetch_array($cC004);

echo "<form name='formulario_cabecalho' id='formulario_cabecalho' method='post' target='fme_gravar' action='../nota_emitida2/gravar_cabecalho_tecnico.php' style='margin:0px;padding:0px;'>";

echo "<input type='hidden' name='T007_Id' id='T007_Id' value='$nT007_Id'>";

echo "<table border=0 cellspacing=0 cellpadding=0 width=900 height=500 bgcolor=#F2F2EE style='border:3px solid #000096' height=100%>";
     echo "<tr valign=center bgcolor='#000096' height=20>";
		echo "<td align=left>";
            echo "<table border=0 cellspacing=0 cellpadding=0 height=20 width=100%>";
               echo "<tr>";
                    echo "<td width=99%>";
			             echo "<font class='fonte' style='color:#FFFFFF;font-size:14px;'><b>Cabe�alho da Nota Fiscal</b>";
	                echo "</td>";
		            echo "<td align=right >";
			             echo "<input type='button' value='X' class=botao_fechar style='cursor:pointer' ";
                         echo " onclick=\"FecharCabecalhoNota();";
                         if ($Operacao=='A')
                         {
                            echo "Ativar_Botoes();";
                         }
                         echo "\" ";
                         echo ">";
                    echo "</td>";
               echo "</tr>";
            echo "</table>";
        echo "</td>";
    echo "</tr>";

	echo "<tr align=left valign=top>";
		echo "<td colspan=2 style='border-top:1px solid #969696' bgcolor=#F2F2EE>";
			echo "<table cellspacing=0 cellpadding=1 border=0 width='100%' height=100% >";

	$nTabIndex=200;

	echo "<tr>";
		echo "<td valign=top>";

                  echo "<table cellspacing=0 cellpadding=0 border=0 width=100%>";
                       echo "<tr>";
                         echo "<td align=center class='fonte'>";
                              echo "<table cellspacing=0 cellpadding=3 border=0 width=100%>";
                                    echo "<tr>";
                                         echo "<td align=left colspan=10>";
                                            echo "<font class=fonte style='color:red;font-size:12px'>ATEN��O! Cuidado na altera��o dos dados abaixo, certifique-se de estar na nota correta e que a situa��o atual desta nota realmente permita a altera��o desejada!</font>";
                                          echo "</td>";
                                    echo "</tr>";
                               
                                    echo "<tr>";
                                         echo "<td align=right width=100>";
                                            echo "<font class='fonte'><small><font color=red>*</font></small></font>&nbsp;";
                                            echo "<font class=fonte>Chave Acesso</font>";
                                          echo "</td>";
                                          echo "<td colspan=3>";
                                            echo "<input type='text' name='T007_Chave_Acesso_Nfe' id='T007_Chave_Acesso_Nfe' value='$mT007[T007_Chave_Acesso_Nfe]' class='campo' style='width:300px'>";
                                         echo "</td>";
                                         echo "<td align=right width=100>";
                                            echo "<font class='fonte'><small><font color=red>*</font></small></font>&nbsp;";
                                            echo "<font class=fonte>Flag Almoxarifado</font>";
                                          echo "</td>";
                                          echo "<td colspan=3>";
                                            echo "<input type='text' name='T007_Flag_Almoxarifado' id='T007_Flag_Almoxarifado' value='$mT007[T007_Flag_Almoxarifado]' class='campo' style='text-align:center;width:50px'>";
                                         echo "</td>";
                                     echo "</tr>";

                                   echo "<tr>";
                                         echo "<td align=right>";
                                            echo "<font class='fonte'><small><font color=red>*</font></small></font>&nbsp;";
                                            echo "<font class=fonte>N�mero Recibo</font>";
                                          echo "</td>";
                                          echo "<td colspan=3>";
                                            echo "<input type='text' name='T007_Numero_Recibo_Nfe' id='T007_Numero_Recibo_Nfe' value='$mT007[T007_Numero_Recibo_Nfe]' class='campo' style='width:300px'>";
                                         echo "</td>";
                                         echo "<td align=right width=100>";
                                            echo "<font class='fonte'><small><font color=red>*</font></small></font>&nbsp;";
                                            echo "<font class=fonte>Flag Impresso</font>";
                                          echo "</td>";
                                          echo "<td colspan=3>";
                                            echo "<input type='text' name='T007_Flag_Impresso' id='T007_Flag_Impresso' value='$mT007[T007_Flag_Impresso]' class='campo' style='text-align:center;width:50px'>";
                                         echo "</td>";
                                   echo "</tr>";
                                   
                                   echo "<tr>";
                                         echo "<td align=right>";
                                            echo "<font class='fonte'><small><font color=red>*</font></small></font>&nbsp;";
                                            echo "<font class=fonte>Numero Nota</font>";
                                          echo "</td>";
                                          echo "<td colspan=3>";
                                            echo "<input type='text' name='T007_Numero_Nota_Fiscal' id='T007_Numero_Nota_Fiscal' value='$mT007[T007_Numero_Nota_Fiscal]' class='campo' style='width:300px'>";
                                         echo "</td>";
                                   echo "</tr>";
                                   
                                   echo "<tr>";
                                        echo "<td align=right>";
                                            echo "<font class=fonte>Vers�o NF-e</font>";
                                        echo "</td>";
                                        echo "<td>";
                                          $mVersao[]="1.10,,1.10";
                                          $mVersao[]="2.00,,2.00";
                                          echo "<select id='T007_Versao_Nfe' name='T007_Versao_Nfe' $cClasseCampo>";
                                          for ($x=0;$x<count($mVersao);$x++)
                                          {
                                               list($Id,$Desc)=explode(",,",$mVersao[$x]);
                                               echo "<option value='$Id' ";
                                               if ($Id==$mT007[T007_Versao_NFe])
                                               {
                                                   echo "SELECTED";
                                               }
                                               echo ">$Desc</option>";
                                        
                                          }
                                          echo "</select>";
                                        echo "</td>";
                                 echo "</tr>";
                                 echo "<tr>";
                                        echo "<td align=right colspan=10>";

                                            $cClausula  = " select ";
                                            $cClausula .= " T127_Data_Transmicao, ";
                                            $cClausula .= " T127_Codigo_Retorno, ";
                                            $cClausula .= " T127_Chave_Acesso_Nfe, ";
                                            $cClausula .= " T127_Numero_Recibo_Nfe, ";
                                            $cClausula .= " T127_Retorno_Sefaz ";
                                            $cClausula .= " from T127 ";
                                            $cClausula .= " where T127_Numero_Nota_Fiscal='$mT007[T007_Numero_Nota_Fiscal]' ";
                                            $cClausula .= " and T127_C004_Id='$mT007[T007_C004_Id]' ";
                                            $cClausula .= " order by T127_Id desc,T127_Data_Transmicao desc  ";
                                            $cSql_T127=mysqli_query($cClausula);
                                            
                                            echo "<table width=100% cellspacing=0 style='empty-cells:show'>";
                                            
                                              echo "<tr>";
                                                 echo "<td class='td_titulo' width=105>";
                                                     echo "Data";
                                                 echo "</td>";
                                                 echo "<td class='td_titulo'>";
                                                     echo "Log";
                                                 echo "</td>";
                                                 echo "<td class='td_titulo'>";
                                                     echo "Chave Acesso";
                                                 echo "</td>";
                                                 echo "<td class='td_titulo'>";
                                                     echo "Recibo";
                                                 echo "</td>";
                                              echo "</tr>";
                                                
                                            while ($mT127=mysql_fetch_array($cSql_T127))
                                            {
                                            	  echo "<tr>";
                                            	    echo "<td class='td_itens'>";
                                                      echo Troca_DataHora_dma($mT127[T127_Data_Transmicao],'/');
                                                    echo "</td>";
                                            	    echo "<td class='td_itens'>";
                                                      echo "$mT127[T127_Retorno_Sefaz]";
                                                    echo "</td>";
                                            	    echo "<td class='td_itens'>";
                                                      echo "$mT127[T127_Chave_Acesso_Nfe]";
                                                    echo "</td>";
                                            	    echo "<td class='td_itens'>";
                                                      echo "$mT127[T127_Numero_Recibo_Nfe]";
                                                    echo "</td>";
                                                 echo "</tr>";  
                                            }
                                            
                                            echo "</table>";
                                            
                                        echo "</td>";
                                 echo "</tr>";
                                            
                            echo "</table>";
        				echo "</td>";
                     echo "</tr>";
                  echo "</table>";

	echo "<tr>";
		echo "<td align=center valign=bottom colspan=2>";
			echo "<table border=0 cellspacing=0 cellpadding=2>";
				echo "<tr align=center>";
					echo "<td>";
                        echo "<input type='submit' value='Gravar' class=enviar>";
   					echo "</td>";

					echo "<td>";

						$nTabIndex++;
						echo "<input type='button' value='Cancelar' class=cancelar  ";
                        echo " onclick=\"FecharCabecalhoNota();";
                        if ($Operacao=='A')
                        {
                            echo "Ativar_Botoes();";
                        }
                        echo "\" ";
                        echo " tabindex='$nTabIndex'/>";

					echo "</td>";
				echo "</tr>";
			echo "</table>";
		echo "</td>";
	echo "</tr>";
	echo "<tr>";
		echo "<td height=10>";
		echo "</td>";
	echo "</tr>";

	echo "</table>";
echo "</form>";
	                       
	                       
	                       echo "</td>";	
	                     echo "</tr>";
                     }                                          
                echo "</table>";
             echo "</td>";
         echo "</tr>";
    echo "</table>";
    		    */
}
