<?php
/*
   Programador: Evantoni Fuchs
   Objetivo: Fun�o generica para valida�o,inser�o e altera�o de Registros
             Gravar o Log ap� incluir e alterar
             Organizar a sequencia de itens na planilha


   Parametros Recebidos:
   $Gravar_Entidade              => NOME DA ENTIDADE EM QUESTAO
   $Gravar_Operacao              => OPERACAO 1 - INCLUSAO 2 - ALTERA�O
   $Gravar_Entidade_Id           => ID DA ENTIDADE
   $Gravar_Objeto                => TIPO DO OBJETO - FORMULARIO
   $mGravar_Atributos            => MATRIZ[NOMECAMPO] VALOR DOS CAMPOS QUE SER� GRAVADOS
   $Gravar_Entidade_Principal    => ENTIDADE PRINCIPAL, VARIAVEL USADA APENAS PARA CALCULAR A ORDEM DOS ITENS
   $Gravar_Entidade_Principal_Id => ID DA ENTIDADE PRINCIPAL, VARIAVEL USADA APENAS PARA CALCULAR A ORDEM DOS ITENS

*/

// nao expira pagina
set_time_limit(0);

function gravar_entidade($mGravar_Atributos=array(),$Gravar_Entidade,$Gravar_Entidade_Id=0,$mParametros=array(),$Gravar_Entidade_Principal='',$Gravar_Entidade_Principal_Id=0,$Gravar_Objeto='',$Codigo_Consulta='001') {

    Global $Usuario_Atual;
    Global $Pasta_Empresa;
    Global $Empresa_Atual;
    Global $Banco_Dados;
    Global $cBd_Configuracao;
    Global $Nova_Janela;

    $Mensagem_Suporte="";

    if ($Gravar_Entidade_Id>0) {
       $Gravar_Operacao='2';
    } else {
       $Gravar_Operacao='1';
    }
    
    if (!isset($mGravar_Atributos)) {
       msg_alerta('ATENCAO!!!! mGravar_Atributos inexistente!');
       return;
    }

//     if ($Gravar_Objeto!='MANUAL') {

       // PROPRIEDADES DA ENTIDADE
       include DIR_WWW."/empresas/$Pasta_Empresa/comp/$Gravar_Entidade/dados_entidade_001.hardness";

       // configuracoes do formulario do componente
       include DIR_WWW."/empresas/$Pasta_Empresa/comp/$Gravar_Entidade/$Gravar_Entidade"."_F$Codigo_Consulta.hardness";

//     }

    //////////////////////////////////////////////////////////////////////////////
    // ARQUIVO DE REGRAS DE CALCULOS DA ENTIDADE
    //////////////////////////////////////////////////////////////////////////////
    $Arquivo_Regra_Calculos="/empresas/$Pasta_Empresa/comp/$Gravar_Entidade/$Gravar_Entidade"."_regras_calculos.inc";
    if (file_exists($Arquivo_Regra_Calculos)) {
       include $Arquivo_Regra_Calculos;
    }

    // REGRAS DE NEGOCIO ANTES DE EXECUTAR A INCLUS�/ALTERA�O NO BANCO DE DADOS
    $Arquivo_Regra_Antes=DIR_WWW."/empresas/$Pasta_Empresa/comp/$Gravar_Entidade/$Gravar_Entidade"."_$Codigo_Consulta"."_regras_gravar_antes.inc";

    // REGRAS DE NEGOCIO DEPOIS DE EXECUTAR A INCLUS�/ALTERACAO NO BANCO DE DADOS
    $Arquivo_Regra_Depois=DIR_WWW."/empresas/$Pasta_Empresa/comp/$Gravar_Entidade/$Gravar_Entidade"."_$Codigo_Consulta"."_regras_gravar_depois.inc";

    if (file_exists($Arquivo_Regra_Antes)) {
       require $Arquivo_Regra_Antes;
       if ($Retornar=='S') {
          return 0;
       }
    }
    if ($mDados_Entidade[Calcular_Ordem_Planilha]=="S") {
        $Ordem_Operacao="$Gravar_Operacao";
        $Ordem_Entidade="$Gravar_Entidade";
        $Ordem_Chave_Primaria="$Gravar_Entidade_Id";
        $Ordem_Entidade_Principal_Id="$Gravar_Entidade_Principal_Id";
        $Ordem_Entidade_Principal="$Gravar_Entidade_Principal";
        if (!empty($Gravar_Entidade_Principal)) {
          include DIR_WWW."/monta/monta_ordem_planilha.php3";
        }
    }


    //////////////////////////////////////////////////////////////////////////////
    // RETORNA O NOME DE TODOS OS CAMPOS (CHAVES DA MATRIZ) EM OUTRA MATRIZ
    //////////////////////////////////////////////////////////////////////////////
    $mChave_Atributos=array_keys($mGravar_Atributos);

    // RETORNA APENAS AS CHAVES QUE SAO REFERENTES A ENTIDADE
    for($v=0;$v<count($mChave_Atributos);$v++) {
       if (substr($mChave_Atributos[$v],0,4)==$Gravar_Entidade) {
           $mNome_Atributos[]="$mChave_Atributos[$v]";
       }
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////
    // INCLUSAO
    /////////////////////////////////////////////////////////////////////////////////////////////////////

    if ($Gravar_Operacao==1) {
    
        if ($Gravar_Objeto=='MANUAL') {
        
              for ($x=0; $x<count($mNome_Atributos); $x++) {

              	
                   if (!empty($cAcumulaCampos)) {
                      $cAcumulaCampos.=', ';
                   }
                   if (!empty($cAcumulaValor)) {
                      $cAcumulaValor.=', ';
                   }

                   $cAcumulaCampos.=$mNome_Atributos[$x];
                   $cNome_Atributo=$mNome_Atributos[$x];
                   $cValorVariavel=$mGravar_Atributos[$cNome_Atributo];

                   // Tira os espa�s do come� e final do campo
                   $cValorVariavel=trim($cValorVariavel);

                   $cValorVariavel=addslashes($cValorVariavel);
                   $cAcumulaValor.="'$cValorVariavel'";
                   
              }

        } else {
        
              // Tira as Virulas para efetuar alguns calculos que pode estar no arquivo_regra_antes
              for ($x=0; $x<count($mConfCampos); $x++) {
                      $cNome_Atributo=$mConfCampos[$x][Nome_Campo];                      
           	      If (!isset($mGravar_Atributos[$cNome_Atributo])) {
		               continue;
		      }

                   if ($mConfCampos[$x][Formatacao_Campo]=='NUMERO') {
                      // se o Campo for do tipo Valor, troco as virgulas para gravar no banco
                      $Casas_Decimais=$mConfCampos[$x][Casas_Decimais]+1;
                      $cNome_Atributo=$mConfCampos[$x][Nome_Campo];
                      $cComando="$"."mGravar_Atributos[".$cNome_Atributo."]=formata_valor_gravar('".$mGravar_Atributos[$cNome_Atributo]."',$Casas_Decimais);";
                      eval($cComando);
                   }

              }


              // Monta a clausula insert
              $cAcumulaCampos='';
              $cAcumulaValor='';
              for ($x=0; $x<count($mConfCampos); $x++) {

                   $cNome_Atributo=$mConfCampos[$x][Nome_Campo];
                   
                   if (isset($mRegra_Atributo_Formula[$cNome_Atributo])) {
                      continue;
                   }

                   $cValorVariavel=$mGravar_Atributos[$cNome_Atributo];

                   // Tira os espa�s do come� e final do campo
                   $cValorVariavel=trim($cValorVariavel);

                   // Se o campo �de preenchimento obrigatorio, verifica se o mesmo foi preenchido
                   if ($mConfCampos[$x][Campo_Obrigatorio]=="S") {

//                       if (($mConfCampos[$x][Campo_Desabilitado]!='S') and ($mConfCampos[$x][Tipo_Campo]!=2)) {
                       if ($mConfCampos[$x][Campo_Desabilitado]!='X') {
                          if (strlen(trim($cValorVariavel))==0) {
                              if ($Gravar_Objeto=='FORMULARIO') {
                                  If (($mConfCampos[$x][Tipo_Campo]!=2) and ($mConfCampos[$x][Campo_Desabilitado]!='S')) {
                                     echo "<script>parent.document.getElementById('formulario').".$mConfCampos[$x][Nome_Campo].".focus();</script>";
                                  }
                                  msg_alerta('Necessario o preenchimento do campo: '.$mConfCampos[$x][Label_Campo]);
                                  exit;
                              } else {
                                  $Mensagem_Suporte.="<BR>Necessario o preenchimento do campo: ".$mConfCampos[$x][Label_Campo];
                              }
                          }
                          if (($mConfCampos[$x][Formatacao_Campo]=='NUMERO') and ($cValorVariavel==0)) {
                              if ($Gravar_Objeto=='FORMULARIO') {
                                 If (($mConfCampos[$x][Tipo_Campo]!=2) and ($mConfCampos[$x][Campo_Desabilitado]!='S')) {
                                    echo "<script>parent.document.getElementById('formulario').".$mConfCampos[$x][Nome_Campo].".focus();</script>";
                                 }
                                 msg_alerta('O campo: '.$mConfCampos[$x][Label_Campo].' deve ser diferente de ZERO');
                                 exit;
                              } else {
                                  $Mensagem_Suporte.="<BR>O campo: ".$mConfCampos[$x][Label_Campo]." deve ser diferente de ZERO";
                              }
                          }
                       }
                   }

                   // Faz valida�o conforme sua formata�o: CNPJ,CPF,DATA
                   if ($mConfCampos[$x][Validacao_Campo]=='S') {
                      if ($mConfCampos[$x][Formatacao_Campo]=='DATA') {
                         if (($mConfCampos[$x][Campo_Obrigatorio]=="N") and (strlen(trim($cValorVariavel))==0)) {

                         } else if (!validar_data($cValorVariavel)) {
                            if ($Gravar_Objeto=='FORMULARIO') {
                                echo "<script>parent.document.getElementById('formulario').".$cNome_Atributo.".focus();</script>";
                                msg_alerta('Data Invalida: '.$cValorVariavel);
                                exit;
                            } else {
                                $Mensagem_Suporte.="<BR>Data Invalida: ".$cValorVariavel;
                            }
                         }
                      }
                      if (($mConfCampos[$x][Formatacao_Campo]=='CNPJ') and (!Empty($cValorVariavel))) {
                         if (!validar_cnpj($cValorVariavel)) {
                            if ($Gravar_Objeto=='FORMULARIO') {
                               echo "<script>parent.document.getElementById('formulario').".$cNome_Atributo.".focus();</script>";
                               msg_alerta('Cnpj Invalido: '.$cValorVariavel);
                               exit;
                            } else {
                                $Mensagem_Suporte.="<BR>Cnpj Invalido: ".$cValorVariavel;
                            }
                         }
                      }
                      if (($mConfCampos[$x][Formatacao_Campo]=='CPF') and (!Empty($cValorVariavel))) {
                         if (!validar_cpf($cValorVariavel)) {
                            if ($Gravar_Objeto=='FORMULARIO') {
                               echo "<script>parent.document.getElementById('formulario').".$cNome_Atributo.".focus();</script>";
                               msg_alerta('Cpf Invalido: '.$cValorVariavel);
                               exit;
                            } else {
                                $Mensagem_Suporte.="<BR>Cpf Invalido: ".$cValorVariavel;
                            }
                         }
                      }
                   }

                   // Verifica a duplicacao de campos
                   if ($mConfCampos[$x][Validar_Duplicacao]=='S') {
                      // N� HAVER�DUPLICA�ES PARA ESTE CAMPO, A NAO SER ESPA� EM BRANCO
                      if (strlen(trim($cValorVariavel))>0) {

                          $cValorVariavel_Anterior=$cValorVariavel;
                          if ($mConfCampos[$x][Formatacao_Campo]=='DATA') {
                              // se o Campo for do Tipo Data, transformo a data para aaaa-mm-dd para gravar no banco
                              $cValorVariavel=troca_data_amd($cValorVariavel);
                          }
                          if ($mConfCampos[$x][Formatacao_Campo]=='DATAHORA') {
                              // se o Campo for do Tipo datatime, transformo a data para aaaa-mm-dd hh:mm:ss para gravar no banco
                              $cValorVariavel=Troca_DataHora_amd($cValorVariavel);
                          }

                          if ($mConfCampos[$x][Tipo_Campo]!='5') {
                              if (($mConfCampos[$x][Maiusculo_Minusculo]=='1') and ($mConfCampos[$x][Formatacao_Campo]=='TEXTO')) {
                                  // Transforma o conteudo da variavel em maiusculo
                                  $cValorVariavel_Anterior=StrToMaiusculo($cValorVariavel_Anterior);
                              }
                              if (($mConfCampos[$x][Maiusculo_Minusculo]=='2') and ($mConfCampos[$x][Formatacao_Campo]=='TEXTO')) {
                                  // Transforma o conteudo da variavel em minusculo
                                  $cValorVariavel_Anterior=StrToMinusculo($cValorVariavel_Anterior);
                              }
                          }

                          // Busca algum registro que contenha o mesmo valor do campo
                          $cSql="select ".$cNome_Atributo." ";
                          $cSql.="from ".$Gravar_Entidade." ";
                          $cSql.="where ".$cNome_Atributo."='$cValorVariavel'";
                          $cQuery=sql_query($cSql);
                          if (sql_num_rows($cQuery)>0) {
                             if ($Gravar_Objeto=='FORMULARIO') {
                                echo "<script>parent.document.getElementById('formulario').".$cNome_Atributo.".focus();</script>";
                                msg_alerta('Nao permitido duplicidade de registros para o campo: '.$mConfCampos[$x][Label_Campo].'.\n\n'.$cValorVariavel_Anterior.' ja existente!');
                                exit;
                             } else {
                                 $Mensagem_Suporte.="<BR>Nao�permitido duplicidade de registros para o campo: ".$mConfCampos[$x][Label_Campo].". ".$cValorVariavel_Anterior." ja existente!";
                             }
                          }
                      }
                   }

              }

//               if ($mDados_Entidade[Calcular_Ordem_Planilha]=="S") {
//                  $Ordem_Operacao="$Gravar_Operacao";
//                  $Ordem_Entidade="$Gravar_Entidade";
//                  $Ordem_Chave_Primaria="$Gravar_Entidade_Id";
//                  $Ordem_Entidade_Principal_Id="$Gravar_Entidade_Principal_Id";
//                  $Ordem_Entidade_Principal="$Gravar_Entidade_Principal";
//                  if (!empty($Gravar_Entidade_Principal)) {
//                     include DIR_WWW."/monta/monta_ordem_planilha.php3";
//                  }
//               }

              for ($x=0; $x<count($mConfCampos); $x++) {

                   $cNome_Atributo=$mConfCampos[$x][Nome_Campo];

             	   If (!isset($mGravar_Atributos[$cNome_Atributo])) {
                       continue;
              	   }
              	   
              	   if ($mConfCampos[$x][Gravar]!='S' && $Gravar_Objeto == "FORMULARIO") {
                   	 continue;
                   }
                   
                   if (!empty($cAcumulaCampos)) {
                      $cAcumulaCampos.=', ';
                   }
                   if (!empty($cAcumulaValor)) {
                      $cAcumulaValor.=', ';
                   }

                   $cAcumulaCampos.=$cNome_Atributo;
                   $cValorVariavel=$mGravar_Atributos[$cNome_Atributo];

                   // Tira os espa�s do come� e final do campo
                   $cValorVariavel=trim($cValorVariavel);

                   if ($mConfCampos[$x][Tipo_Campo]!='5') {
                      if (($mConfCampos[$x][Maiusculo_Minusculo]=='1') and ($mConfCampos[$x][Formatacao_Campo]=='TEXTO')) {
                          // Transforma o conteudo da variavel em maiusculo
                          $cValorVariavel=StrToMaiusculo($cValorVariavel);
                       }
                       if (($mConfCampos[$x][Maiusculo_Minusculo]=='2') and ($mConfCampos[$x][Formatacao_Campo]=='TEXTO')) {
                          // Transforma o conteudo da variavel em minusculo
                          $cValorVariavel=StrToMinusculo($cValorVariavel);
                       }
                   }

                   if ($mConfCampos[$x][Formatacao_Campo]=='DATA') {
                      // se o Campo for do Tipo Data, transformo a data para aaaa-mm-dd para gravar no banco
                      $cValorVariavel=troca_data_amd($cValorVariavel);
                   }
                   if ($mConfCampos[$x][Formatacao_Campo]=='DATAHORA') {
                      // se o Campo for do Tipo datatime, transformo a data para aaaa-mm-dd hh:mm:ss para gravar no banco
                      $cValorVariavel=Troca_DataHora_amd($cValorVariavel);
                   }
                   if ($mConfCampos[$x][Gravar]!='S' && $Gravar_Objeto == "FORMULARIO") {
                   	 continue;
                   }	 
//                   $cValorVariavel=str_replace("'","\'",$cValorVariavel);
//                   $cValorVariavel=str_replace('"',"\'\'",$cValorVariavel);
                   $cValorVariavel=addslashes($cValorVariavel);

			if($cValorVariavel!="NULL")
			{
	                   $cAcumulaValor.="'$cValorVariavel'";
			}
			else
			{
				 $cAcumulaValor.= 'NULL';
			}
			
                   
              }
              
        }
        
        $cClausula="insert into $Gravar_Entidade ($cAcumulaCampos) values ($cAcumulaValor);";
        
        if (strlen(trim($Mensagem_Suporte))==0) {
           if ($Gravar_Objeto=='FORMULARIO') {
               $nUltimo_Id_Incluso_Funcao=sql_query($cClausula,"S","N","S");
           } else {
               $nUltimo_Id_Incluso_Funcao=sql_query($cClausula,"N","N","S");
           }
        }

        $nRetornar_Id=$nUltimo_Id_Incluso_Funcao;
        
        if (empty($nRetornar_Id)) {
            msg_alerta("Falha na inclusao do registro: insert into $Gravar_Entidade ($cAcumulaCampos) values ($cAcumulaValor);");
        }
              
        // pega o ultimo id incluso dentro da funcao sql_query
        $cComando='$'.$Gravar_Entidade.'_Id="'.$nUltimo_Id_Incluso_Funcao.'";';
        eval("$cComando");

        /////////////////////////////////////////////////////////////////////////////////////
        // ENVIA E-MAIL PARA O SUPORTE
        /////////////////////////////////////////////////////////////////////////////////////
        if (!empty($Mensagem_Suporte)) {
           $Mensagem_Suporte="Entidade: $Gravar_Entidade<BR>".$Mensagem_Suporte;
           enviar_email_erro($Mensagem_Suporte,$cClausula,'VALIDACAO DOS CAMPOS AO GRAVAR A ENTIDADE');
           $Mensagem_Suporte=str_replace("<BR>","/p",$Mensagem_Suporte);
           msg_alerta($Mensagem_Suporte);
        }

        // REGRAS DE NEGOCIO DEPOIS DE EXECUTAR A INCLUS�/ALTERACAO NO BANCO DE DADOS
        if (file_exists($Arquivo_Regra_Depois)) {
           include $Arquivo_Regra_Depois;
        }
              
        /////////////////////////////////////////////////////////////////////////////////////
        // RETORNA O ID
        /////////////////////////////////////////////////////////////////////////////////////
        return $nRetornar_Id;
    }

    /////////////////////////////////////////////////////////////////////////////////////////////////////
    // ALTERA�O
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    if ($Gravar_Operacao==2) {

        if ($Gravar_Objeto=='MANUAL') {

              for ($x=0; $x<count($mNome_Atributos); $x++) {
/*
                   if (!empty($cAcumulaCampos)) {
                      $cAcumulaCampos.=', ';
                   }
                   if (!empty($cAcumulaValor)) {
                      $cAcumulaValor.=', ';
                   }
*/

  //                 $cAcumulaCampos.=$mNome_Atributos[$x];
                   $cNome_Atributo=$mNome_Atributos[$x];
                   $cValorVariavel=$mGravar_Atributos[$cNome_Atributo];

                   // Tira os espa�s do come� e final do campo
                   $cValorVariavel=trim($cValorVariavel);

                   $cValorVariavel=addslashes($cValorVariavel);
                   $cAcumulaValor.="'$cValorVariavel'";

                   if (isset($mRegra_Atributo_Formula[$cNome_Atributo])) {
                      continue;
                   }
                   if (!empty($cAcumulaComando)) {
                      $cAcumulaComando.=', ';
                   }

                   $cAcumulaComando.=$cNome_Atributo."='".$cValorVariavel."'";

              }

        } else {
        
              // Monta a clausula update
              $cAcumulaComando='';

              // Tira as Virulas para efetuar alguns calculos que pode estar no arquivo_regra_antes
              for ($x=0; $x<count($mConfCampos); $x++) {
                      $cNome_Atributo=$mConfCampos[$x][Nome_Campo];
           	          If (!isset($mGravar_Atributos[$cNome_Atributo])) {
		                 continue;
		              }
                      if ($mConfCampos[$x][Formatacao_Campo]=='NUMERO') {
                         // se o Campo for do tipo Valor, troco as virgulas para gravar no banco
                         $Casas_Decimais=$mConfCampos[$x][Casas_Decimais]+1;

                         $cComando="$"."mGravar_Atributos[".$cNome_Atributo."]=formata_valor_gravar('".$mGravar_Atributos[$cNome_Atributo]."',$Casas_Decimais);";
                         eval($cComando);
                   }

              }

              for ($x=0; $x<count($mConfCampos); $x++) {

                   $cNome_Atributo=$mConfCampos[$x][Nome_Campo];
                   
                   $cValorVariavel=$mGravar_Atributos[$cNome_Atributo];

                   $cValorVariavel=trim($cValorVariavel);

                   // Se o campo �de preenchimento obrigatorio, verifica se o mesmo foi preenchido
                   if ($mConfCampos[$x][Campo_Obrigatorio]=="S") {
//                       if (($mConfCampos[$x][Campo_Desabilitado]!='S') and ($mConfCampos[$x][Tipo_Campo]!=2)) {
                       if ($mConfCampos[$x][Campo_Desabilitado]!='X') {
                          if (strlen(trim($cValorVariavel))==0) {
                              if ($Gravar_Objeto=='FORMULARIO') {
                                  If (($mConfCampos[$x][Tipo_Campo]!=2) and ($mConfCampos[$x][Campo_Desabilitado]!='S')) {
                                    echo "<script>parent.document.getElementById('formulario').".$cNome_Atributo.".focus();</script>";
                                 }
                                 msg_alerta('Necessario o preenchimento do campo: '.$mConfCampos[$x][Label_Campo]);
                                 exit;
                              } else {
                                 $Mensagem_Suporte.="<BR>Necessario o preenchimento do campo: ".$mConfCampos[$x][Label_Campo];
                              }
                              
                          }
                          if (($mConfCampos[$x][Formatacao_Campo]=='NUMERO') and ($cValorVariavel==0)) {
                              if ($Gravar_Objeto=='FORMULARIO') {
                                 If (($mConfCampos[$x][Tipo_Campo]!=2) and ($mConfCampos[$x][Campo_Desabilitado]!='S')) {
                                    echo "<script>parent.document.getElementById('formulario').".$cNome_Atributo.".focus();</script>";
                                 }
                                 msg_alerta('O campo: '.$mConfCampos[$x][Label_Campo].' deve ser diferente de ZERO');
                                 exit;
                              } else {
                                $Mensagem_Suporte.="<BR>O campo: ".$mConfCampos[$x][Label_Campo]." deve ser diferente de ZERO";
                              }
                          }
                       }
                   }

                   // Faz valida�o conforme sua formata�o: CNPJ,CPF,DATA
                   if ($mConfCampos[$x][Validacao_Campo]=='S') {
                      if ($mConfCampos[$x][Formatacao_Campo]=='DATA') {
                         if (($mConfCampos[$x][Campo_Obrigatorio]=="N") and (strlen(trim($cValorVariavel))==0)) {

                         } else if (!validar_data($cValorVariavel)) {
                            if ($Gravar_Objeto=='FORMULARIO') {
                               echo "<script>parent.document.getElementById('formulario').".$cNome_Atributo.".focus();</script>";
                               msg_alerta('Data Invalida: '.$cValorVariavel);
                               exit;
                            } else {
                               $Mensagem_Suporte.="<BR>Data Invalida: ".$cValorVariavel;
                            }
                         }
                      }
                      if (($mConfCampos[$x][Formatacao_Campo]=='CNPJ') and (!Empty($cValorVariavel))) {
                         if (!validar_cnpj($cValorVariavel)) {
                            if ($Gravar_Objeto=='FORMULARIO') {
                               echo "<script>parent.document.getElementById('formulario').".$cNome_Atributo.".focus();</script>";
                               msg_alerta('Cnpj Invalido: '.$cValorVariavel);
                               exit;
                            } else {
                               $Mensagem_Suporte.="<BR>Cnpj Invalido: ".$cValorVariavel;
                            }
                         }
                      }
                      if (($mConfCampos[$x][Formatacao_Campo]=='CPF') and (!Empty($cValorVariavel))) {
                         if (!validar_cpf($cValorVariavel)) {
                            if ($Gravar_Objeto=='FORMULARIO') {
                                echo "<script>parent.document.getElementById('formulario').".$cNome_Atributo.".focus();</script>";
                                msg_alerta('Cpf Invalido: '.$cValorVariavel);
                                exit;
                            } else {
                               $Mensagem_Suporte.="<BR>Cpf Invalido: ".$cValorVariavel;
                            }

                         }
                      }
                   }

                   // Verifica a duplicacao de campos
                   if ($mConfCampos[$x][Validar_Duplicacao]=='S') {
                      // N� HAVER�DUPLICA�ES PARA ESTE CAMPO, A NAO SER ESPA� EM BRANCO
                      if (strlen(trim($cValorVariavel))>0) {

                          $cValorVariavel_Anterior=$cValorVariavel;
                          if ($mConfCampos[$x][Formatacao_Campo]=='DATA') {
                              // se o Campo for do Tipo Data, transformo a data para aaaa-mm-dd para gravar no banco
                              $cValorVariavel=troca_data_amd($cValorVariavel);
                          }
                          if ($mConfCampos[$x][Formatacao_Campo]=='DATAHORA') {
                              // se o Campo for do Tipo datatime, transformo a data para aaaa-mm-dd hh:mm:ss para gravar no banco
                              $cValorVariavel=Troca_DataHora_amd($cValorVariavel);
                          }

                          if ($mConfCampos[$x][Tipo_Campo]!='5') {
                              if (($mConfCampos[$x][Maiusculo_Minusculo]=='1') and ($mConfCampos[$x][Formatacao_Campo]=='TEXTO')) {
                                  // Transforma o conteudo da variavel em maiusculo
                                  $cValorVariavel_Anterior=StrToMaiusculo($cValorVariavel_Anterior);
                              }
                              if (($mConfCampos[$x][Maiusculo_Minusculo]=='2') and ($mConfCampos[$x][Formatacao_Campo]=='TEXTO')) {
                                  // Transforma o conteudo da variavel em minusculo
                                  $cValorVariavel_Anterior=StrToMinusculo($cValorVariavel_Anterior);
                              }
                          }

                          // Busca algum registro que contenha o mesmo valor do campo
                          $cSql="select ".$cNome_Atributo." ";
                          $cSql.="from ".$Gravar_Entidade." ";
                          $cSql.="where ".$cNome_Atributo."='$cValorVariavel' ";
                          $cSql.="and ".$Gravar_Entidade."_Id!='".$Gravar_Entidade_Id."'";
                          $cQuery=sql_query($cSql);
                          if (sql_num_rows($cQuery)>0) {
                             if ($Gravar_Objeto=='FORMULARIO') {
                                echo "<script>parent.document.getElementById('formulario').".$cNome_Atributo.".focus();</script>";
                                msg_alerta('Nao permitido duplicidade de registros para o campo: '.$mConfCampos[$x][Label_Campo].'.\n\n'.$cValorVariavel_Anterior.' ja existente!');
                                exit;
                            } else {
                               $Mensagem_Suporte.="<BR>Nao permitido duplicidade de registros para o campo: ".$mConfCampos[$x][Label_Campo]." ".$cValorVariavel_Anterior." ja existente!";
                            }

                          }
                      }
                   }

              }


//               if ($mDados_Entidade[Calcular_Ordem_Planilha]=="S") {
//                  $Ordem_Operacao="$Gravar_Operacao";
//                  $Ordem_Entidade="$Gravar_Entidade";
//                  $Ordem_Chave_Primaria="$Gravar_Entidade_Id";
//                  $Ordem_Entidade_Principal_Id="$Gravar_Entidade_Principal_Id";
//                  $Ordem_Entidade_Principal="$Gravar_Entidade_Principal";
//                  if (!empty($Gravar_Entidade_Principal)) {
//                     include DIR_WWW."/monta/monta_ordem_planilha.php3";
//                  }
//               }


              for ($x=0; $x<count($mConfCampos); $x++) {

                   $cNome_Atributo=$mConfCampos[$x][Nome_Campo];
                   
            	   If (!isset($mGravar_Atributos[$cNome_Atributo])) {
                    	continue;
		           }
                   if ($mConfCampos[$x][Gravar]!='S' && $Gravar_Objeto == "FORMULARIO") {
                   	 continue;
                   }	 


                   if (isset($mRegra_Atributo_Formula[$cNome_Atributo])) {
                      continue;
                   }

                   $cValorVariavel=$mGravar_Atributos[$cNome_Atributo];

                   $cValorVariavel=trim($cValorVariavel);

                   if ($mConfCampos[$x][Tipo_Campo]!='5') {
                      if (($mConfCampos[$x][Maiusculo_Minusculo]=='1') and ($mConfCampos[$x][Formatacao_Campo]=='TEXTO')) {
                         // Transforma o conteudo da variavel em maiusculo
                         $cValorVariavel=StrToMaiusculo($cValorVariavel);
                      }
                      if (($mConfCampos[$x][Maiusculo_Minusculo]=='2') and ($mConfCampos[$x][Formatacao_Campo]=='TEXTO')) {
                         // Transforma o conteudo da variavel em minusculo
                         $cValorVariavel=StrToMinusculo($cValorVariavel);
                      }
                   }

                   if ($mConfCampos[$x][Formatacao_Campo]=='DATA') {
                      // se o Campo for do Tipo Data, transformo a data para aaaa-mm-dd para gravar no banco
                      $cValorVariavel=troca_data_amd($cValorVariavel);
                   }
                   if ($mConfCampos[$x][Formatacao_Campo]=='DATAHORA') {
                      // se o Campo for do Tipo datatime, transformo a data para aaaa-mm-dd hh:mm:ss para gravar no banco
                      $cValorVariavel=Troca_DataHora_amd($cValorVariavel);
                   }
                   if (!empty($cAcumulaComando)) {
                      $cAcumulaComando.=', ';
                   }
//                   $cValorVariavel=str_replace("'","\'",$cValorVariavel);
//                   $cValorVariavel=str_replace('"',"\'\'",$cValorVariavel);
//                   $cValorVariavel=addslashes($cValorVariavel);
			if($cValorVariavel!="NULL")
			{
	                   $cAcumulaComando.=$cNome_Atributo."='".$cValorVariavel."'";
			}
			else
			{
				$cAcumulaComando.=$cNome_Atributo."='NULL'";
			}

              }

        }

        $cClausula="update $Gravar_Entidade set $cAcumulaComando where $Gravar_Entidade"."_Id='$Gravar_Entidade_Id';";
        echo "update $Gravar_Entidade set $cAcumulaComando where $Gravar_Entidade"."_Id='$Gravar_Entidade_Id';";

        if (strlen(trim($Mensagem_Suporte))==0) {
           if ($Gravar_Objeto=='FORMULARIO') {
               $alteracao=sql_query($cClausula,"S","N","S");
           } else {
               $alteracao=sql_query($cClausula,"N","N","S");
           }
        }

        // pega o ultimo id incluso dentro da funcao sql_query
        $cComando='$'.$Gravar_Entidade.'_Id="'.$Gravar_Entidade_Id.'";';
        eval("$cComando");


        /////////////////////////////////////////////////////////////////////////////////////
        // ENVIA E-MAIL PARA O SUPORTE
        /////////////////////////////////////////////////////////////////////////////////////
        if (!empty($Mensagem_Suporte)) {
            $Mensagem_Suporte="Entidade: $Gravar_Entidade<BR>".$Mensagem_Suporte;
            enviar_email_erro($Mensagem_Suporte,$cClausula,'VALIDA�O DOS CAMPOS AO GRAVAR A ENTIDADE');
            $Mensagem_Suporte=str_replace("<BR>","/p",$Mensagem_Suporte);
            msg_alerta($Mensagem_Suporte);
        }

        /////////////////////////////////////////////////////////////////////////////////////
        // REGRAS DE NEGOCIO DEPOIS DE EXECUTAR A INCLUS�/ALTERACAO NO BANCO DE DADOS
        /////////////////////////////////////////////////////////////////////////////////////
        if (file_exists($Arquivo_Regra_Depois)) {
            include $Arquivo_Regra_Depois;
        }

        /////////////////////////////////////////////////////////////////////////////////////
        // RETORNA O ID
        /////////////////////////////////////////////////////////////////////////////////////
        return $Gravar_Entidade_Id;
    }
}
?>
