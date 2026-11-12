<?php
//Arquivo importado do sistema antigo

// Cria ou inicia uma sess�o
include "sessao.php";

//--  Conex�o com o banco de dados
include "../../../hardness/include/conexao.inc"; // conexao.php

//-- Fun��es Gen�ricas em PHP
include "funcoes_genericas.inc";

set_time_limit(0);

if (empty($T007_Id)) {
   msg_alerta("Falta o Parametro T007_Id");
   exit;
}

      $cSql_T007=sql_query("select * from T007 left join C004 on C004_Id=T007_C004_Id where T007_Id='$T007_Id'");
      $mT007=sql_fetch_array($cSql_T007);

      if (mysql_num_rows($cSql_T007)==0)
      {
         msg_alerta("T007_Id n�o existe: $T007_Id");
         exit;
      }
      if ($mT007[T007_Flag_ACP]!='6')
      {
         msg_alerta("O Tipo do documento n�o � NF-e. Verifique!!!<BR><BR>Tipo: $mT007[T007_Flag_ACP]");
         exit;
      }
      if (!empty($mT007[T007_Numero_Protocolo_Nfe]))
      {
//         msg_alerta("NF-e possui Protocolo de autoriza��o do Sefaz, nao pode ser DENEGADA. Protocolo: $mT007[T007_Numero_Protocolo_Nfe]");
//         exit;
      }


      mysql_query("SET AUTOCOMMIT=0");
      mysql_query("START TRANSACTION");


      //-- Cancelando Nota Fiscal
      sql_query("update T007 
                    set T007_Flag_Cancelada='S',
                        T007_Flag_Classificacao=0,
                        T007_Observacao_Cancelamento='NOTA FISCAL DENEGADA',
                        T007_Flag_Denegado='S',
                        T007_Valor_Total=0
                  where T007_Id='$mT007[T007_Id]'","N","N","N");

      //-- Limpa o Flag de classificacao do item
      $sql=sql_query("update T008 set T008_Flag_Classificacao=0 where T008_T007_Id='$mT007[T007_Id]'","N","N","N");
      
      //-- Cancelando Contas a Receber 
      sql_query("update T002 set T002_Flag_Cancelada='S',
                                                  T002_Data_Recebimento=current_date(),
                                                   T002_Observacao_2='*** NF DENEGADA ***',
                                                   T002_Valor_Duplicata=0,
                                                   T002_Valor_Total=0
                                        where T002_T007_Id='$mT007[T007_Id]'","N","N","N");
      
      //-- Cancelando Contas a Pagar
      sql_query("update T015 set T015_Flag_Cancelada='S',
                                                   T015_Data_Pagamento=current_date(),
                                                   T015_Observacao_2='*** NF DENEGADA ***',
                                                   T015_Valor_Duplicata=0,
                                                   T015_Valor_Total=0
                                         where T015_T007_Id='$mT007[T007_Id]'","N","N","N");
      
     
      $cSql=sql_query("update T001 set T001_Flag_Operacao='C' where T001_T007_Id='$mT007[T007_Id]'","N","N","N");
       
      $cSql_T008=sql_query("select D006_Flag_Estoque,
                                                      T008_D009_Id,
                                                      T008_Quantidade,
						      T007_D024_Id,
						      T008_Id,
						      T007_Id,
						      T008_C004_Id,
                                                      T007_D006_Id,
            					      T008_Codigo_Produto,
						      T007_Numero_Nota_Fiscal,
						      D049_D001_Id 
                                             from T008 
                                         left join T007 on T008_T007_Id=T007_Id 
                                         left join D006 on T007_D006_Id=D006_Id 
					 left join D009 on D009_Id=T008_D009_Id
					 left join D049 on D049_Id=D009_D049_Id
                                            where T008_T007_Id='$mT007[T007_Id]'");

      while ($mT008=sql_fetch_array($cSql_T008)) {

          ///////////////////////////////////////////////////////////////////////////////////////////////////
          //-- INVERSAO DOS FLAG POR SER UM CANCELAMENTO
          ///////////////////////////////////////////////////////////////////////////////////////////////////
    	  if ($mT008[D006_Flag_Estoque]=='D') {

                 $Flag_Operacao_Historico="E";

      	  } elseif($mT008[D006_Flag_Estoque]=='C'){

                 $Flag_Operacao_Historico="S";

      	  } elseif($mT008[D006_Flag_Estoque]=='N'){

                 $Flag_Operacao_Historico="N";

       	  }

          ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // ENCONTRA O ID DO T066
          ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          $cSql_T066=sql_query("select T066_Id from T066 where T066_D048_Id='1' and T066_D009_Id='$mT008[T008_D009_Id]'");
          $mT066=sql_fetch_array($cSql_T066);

          D001_reprocessa_historico($mT008[D049_D001_Id]);
  
          ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
          // HISTORICO DO PRODUTO
          ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

      }

	// PROCESSO DE ATUALIZAR DATA DA PRIMEIRA ULTIMA COMPRA SENDO EXECUDATO PELO CRONTAB
/* 	$sqlUpdD024  = " UPDATE D024 SET";
	$sqlUpdD024 .= " D024_Data_Primeira_Compra = T007_Primeira_Ultima_Compra(1,D024_Id),";
	$sqlUpdD024 .= " D024_Data_Ultima_Compra = T007_Primeira_Ultima_Compra(2,D024_Id),";
	//Function parametro = 3 retorna a empresa da ultima compra
	$sqlUpdD024 .= " D024_C004_Id_Ultima_Compra = T007_Primeira_Ultima_Compra(3,D024_Id)";
	$sqlUpdD024 .= " WHERE D024_Id=$mT007[T007_D024_Id]";
	$cmD204 = sql_query($sqlUpdD024,"N","N","N"); */
	

      mysql_query("COMMIT");
      msg_alerta("NFe denegada com sucesso!");
       echo "<script>
               parent.self.close();
             </script>";



?>
