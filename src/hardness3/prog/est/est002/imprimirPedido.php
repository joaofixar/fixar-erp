<?php
namespace hardness;
global $g;

$Cor_Normal="#ffffff";

$Linhas_Normais=30;
$Linhas_Ultima_Pagina=25;
$Tamanho_Maximo_Tabela=580;
$Tamanho_Minimo_Tabela=400;
$Width_Relatorio='98%';

$T005_Id = $r_T005_Id;
 
// Verifica se o usuario é um administrador
$cSql_C007=mysql_query("select * from C007 where C007_Id='{$g['usuarioAtual']}'");
$mC007=mysql_fetch_array($cSql_C007);

$cSql_C004=mysql_query("select * from C004 where C004_Id='{$g['empresaAtual']}'");
$mC004=mysql_fetch_array($cSql_C004);
$mC004['C004_Observacao_Promocao']=str_replace("\n","<br>",$mC004['C004_Observacao_Promocao']);

$cSql_T005=mysql_query("select * from T005
                            left join D024 on D024_Id=T005_D024_Id
                            left join D020 on D020_Id=D024_D020_Id
                            left join D018 on D018_Id=D020_D018_Id
                            left join D021 on D021_Id=T005_D021_Id
                            left join D022 on D022_Id=T005_D022_Id
          left join C007 on C007_Id=T005_C007_Id_Vendedor_Interno
                                where T005_Id='$T005_Id'");
$mT005=mysql_fetch_array($cSql_T005);

$mT005['T005_Data_Emissao']=gCorrigeData($mT005['T005_Data_Emissao']);
$mT005['T005_Observacao']=str_replace("\n","<br>",trim($mT005['T005_Observacao']));

$cSql_Prazos=mysql_query("select T005_Prazos('$T005_Id') as Prazos");
$mPrazos=mysql_fetch_array($cSql_Prazos);

if ($g['C020']['C020_Expedicao_Direcionar_Compra']=='S')
{
    $cClausula="select count(*)
                  from T006
             left join T005 on T005_Id=T006_T005_Id
             left join D009 on D009_Id=T006_D009_Id
             left join T066 on T066_D009_Id=D009_Id
             left join D004 on D004_Id=T066_D004_Id
             left join D049 on D049_Id=D009_D049_Id
             left join C004 on C004_Id=T006_C004_Id
             left join D024 on D024_Id=D009_D024_Id
             left join D005 on D005_Id=T006_D005_Id
             left join D082 on D082_Id=D049_D082_Id
                 where T006_T005_Id='$T005_Id'
                   and (T006_T006_Id is null or T006_T006_Id<=0)
                   and (T006_Quantidade>0)
                   and (T006_Flag_Status!=3)
                   and (T006_Flag_Tipo_Produto='AA' or T006_Flag_Tipo_Produto='' or T006_Flag_Tipo_Produto is null)
                   and (T006_Quantidade>T006_Quantidade_Separacao or T006_Quantidade_Separacao is null)
                   and T006_Flag_Divergencia!='S'
                   and T006_Quantidade>T006_Quantidade_Estoque
              group by T006_Id
              order by D004_Local,T006_Id";
    $Resultado=mysql_query($cClausula);
    /*
    if (mysql_num_rows($Resultado)>0)
    {
        msg_alerta("Existem itens sem estoque, execute reprocessar pedidos!");
        exit;
    }
    */
}

$cClausula="select *, T006_Item,
                   T006_Quantidade,
                   if(T006_Codigo_Substituto!='',T006_Codigo_Substituto,T006_Codigo_Produto) as T006_Codigo_Produto_Substituto,
                   T006_Codigo_Produto,
                   T006_Codigo_Substituto,
                   T006_Descricao_Produto,
                   D082_Marca,
                   D009_Quantidade_Estoque,
                   T006_Quantidade_Separacao Separado,
                   T006_Quantidade_Estoque,
                   D009_Quantidade_Estoque_Real,
                   T006_Peso_Unitario,
                   T006_Id,
                   D003_Imagem,
                   group_concat(distinct D004_Local) D004_Local,
                   DATE_FORMAT(D049_Data_Fabricacao,'%d/%m/%Y') AS D049_Data_Fabricacao,
                   DATE_FORMAT(D049_Data_Validade,'%d/%m/%Y') AS D049_Data_Validade,
                   D049_Lote,
                   D037_Unidade
              from T006
         left join T005 on T005_Id=T006_T005_Id
         left join D009 on D009_Id=T006_D009_Id
         left join T066 on T066_D009_Id=D009_Id
         left join D004 on D004_Id=T066_D004_Id
         left join D049 on D049_Id=D009_D049_Id
         left join D001 on D001_Id=D049_D001_Id
         left join D003 on D003_Id=D001_D003_Id
         left join C004 on C004_Id=T006_C004_Id
         left join D024 on D024_Id=D009_D024_Id
         left join D005 on D005_Id=T006_D005_Id
         left join D082 on D082_Id=D049_D082_Id
         left join D037 on D037_Id=D001_D037_Id
             where T006_T005_Id='$T005_Id'
               and (T006_T006_Id is null or T006_T006_Id<=0)
               and (T006_Quantidade>0)
               and (T006_Flag_Status!=3)
          group by T006_Id
          order by D004_Local,T006_Id";
$Resultado=mysql_query($cClausula);

echo mysql_error();

echo "<html>";
echo "<head>";
echo "<title>Pedido venda Nr $T005_Id - {$mT005['D024_Nome_Empresa']}</title>";
echo "<meta http-equiv='Content-Type' content='text/html; charset=ISO-8859-1' />";
echo "<link rel=stylesheet href=\"/hardness/classes/classe_tamanho_medio_firefox.css\" type=\"text/css\">";
echo "<script src='/hardness/func/funcoes_genericas.js'></script>";
echo "<script src='/hardness/func/ajax.js'></script>";

echo "<style>

.fonte
{
font-family:arial,verdana;
font-size:11px;
}

</style>";


echo "</head>";
echo "<body bgcolor=white leftmargin='0' topmargin='0'>";


////////////////////////////////////////////////////////////
// VERIFICA QUANTAS PAGINAS SERÃO IMPRESSAS
////////////////////////////////////////////////////////////
// Encontra o total das linhas a ser impressa
$Total_Linhas=mysql_num_rows($Resultado);
if (!empty($mT005['T005_Mao_Obra_1'])) {
    $Total_Linhas+=4;
}

if ($Total_Linhas==0) {
   //-- SE NAO RETORNAR NADA PARA IMPRIMIR NO CORPO DO RELATORIO, TOTAL_LINHAS É DEFINIDO PARA 1,
   //-- PARA QUE SEJA IMPRESSO O CABECALHO.
   $Total_Linhas=1;
}
$mLimit = 0;
if ($Total_Linhas>=1) {

    if (count($mLimit)==0) {
        // Encontra o numero de paginas cheias
        $Total_Paginas = intval($Total_Linhas / $Linhas_Normais);
        // Encontra o numero de itens restantes
        if ($Total_Paginas>=1) {
          $Resto = ($Total_Linhas - ($Total_Paginas * $Linhas_Normais));
        } else {
          $Resto = $Total_Linhas;
        }

        // Verifica caso o resto seja maior que o número máximo de itens que cabe na última folha
        if ($Resto > $Linhas_Ultima_Pagina) {
          // Sendo maior soma mais duas folhas
          $Total_Paginas += 2 ;
        } else {
          // Sendo menor soma mais uma folha
          $Total_Paginas += 1 ;
        }
    } else {
        $Total_Paginas=count($mLimit);

        $Linhas_Normais=$mLimit[0];
    }
}

//////////////////////////////////////////////////////////////

// Inicia com o tamanho maximo estabelecido
$Tamanho_Tabela_Itens=$Tamanho_Maximo_Tabela;

// Inicializa Variavel utilizada no limit da consulta dos itens
$Inicio=0;
$Pagina_Atual=0;

    $Pagina_Atual++;

       echo "<table align=center border='0' valign=top cellpading=0 cellspacing=0 width='735' bgcolor=white bgcolor=white style='border:0px solid black'>";
         echo "<tr bgcolor=white>";
          echo "<td valign=top>";
           echo "<table align=center border='0' cellspacing=0 cellpading=0 width='100%' height=10 valign=top style='border:0px solid'>";
            echo "<tr>";
             echo "<td bgcolor=white width=80%>";
                $Diretorio_Imagens="/hardness/dados_usuarios/{$confUsuario['dbDatabase']}/imagens/";
                include $g['pathDadosAntigo']."/cabecalho_relatorio.php";
             echo "</td>";
             echo "<td bgcolor=white align=center width=20%>";
                  echo "<font class='fonte' style='font-size:18px;font-weight:bold'>";
               echo "PEDIDO<BR>Nr. $T005_Id";
                  echo "</font>";
               echo "</font>";
               echo "<br><font class=fonte>".agora()."</font>";
             echo "</td>";
           echo "</tr>";
           echo "<tr bgcolor=white>";
             echo "<td colspan=2>";
               echo "<table cellpadding=7 width=100% border=0>";
                 echo "<tr bgcolor=white>";
                   echo "<td colspan=3>";
                   echo "<table width=100% border=0>";
                    echo "<tr><td style='width:50%'>";
                       if ($mT005['D024_Id']>0) {
                           echo "<font class='fonte' style='font-size:12px'>Cliente : <b>{$mT005['D024_Id']}-{$mT005['D024_Nome_Empresa']}</b></font><br>";
                           echo "<font class='fonte' style='font-size:12px'>Fantasia: <b>{$mT005['D024_Nome_Fantasia']}</b></font><br>";
                           echo "<font class='fonte' style='font-size:12px'>Telefone: <b>{$mT005['D024_DDD_Telefone_1']} {$mT005['D024_Telefone_1']}</b></font><br>";
                           echo "<font class='fonte' style='font-size:12px'>CNPJ/CPF: <b>{$mT005['D024_Cnpj']}{$mT005['D024_Cpf']}</b></font><br>";
                           echo "<font class='fonte' style='font-size:12px'>Endereco: <b>{$mT005['D024_Endereco']}, {$mT005['D024_Numero']} {$mT005['D024_Complemento']}</font></b><br>";
                           echo "<font class='fonte' style='font-size:12px'>Cep/Bairro: <b>{$mT005['D024_Cep']} - {$mT005['D024_Bairro']}</font></b><br>";
                           echo "<font class='fonte' style='font-size:12px'>Cidade/UF: <b>{$mT005['D020_Nome_Cidade']} / {$mT005['D018_UF']}</font></b><br>";
                           if (!empty($mT005['T005_Nome_Comprador'])) {
                              echo "<font class='fonte'>Comprador:</font> ";
                              echo "<font class='fonte' style='font-size:12px'>{$mT005['T005_Nome_Comprador']}</font> <font class=fonte>{$mT005['T005_E_mail_Comprador']}</font><br>";
                           }
                       } else {
                           echo "<font class='fonte' style='font-size:12px'><b>{$mT005['T005_Nome_Cliente']}</b></font><br>";
                           echo "<font class='fonte' style='font-size:12px'>TELEFONE: {$mT005['T005_Telefone']} - FAX: {$mT005['T005_Fax']}</font><br>";
                           if (!empty($mT005['T005_Nome_Comprador'])) {
                              echo "<font class='fonte'>Comprador:</font> ";
                              echo "<font class='fonte' style='font-size:12px'>{$mT005['T005_Nome_Comprador']}</font> <font class=fonte>{$mT005['T005_E_mail_Comprador']}</font>";
                           }
                       }
                       //if (!empty($mT005['T005_Observacao'])) {
                          //echo "<br><font class='fonte' style='font-size:12px'><b>$mT005['T005_Observacao']</b></font>";
                       //}
                    echo "</td>";
                    echo "<td align=left valign=top style='width:50%'>";
                              echo "<font class='fonte' style='font-size:12px'>Vendedor: <b>{$mT005['C007_Nome']}</b></font><br>";
                              echo "<font class='fonte' style=\"color: var(--BGVermelho);font-size:18px;font-weight:bold\">{$mT005['D022_Nome_Empresa']}</font>";
                      
                      switch($mT005['T005_Flag_Frete']){
                      case '0':
                        $T005_Frete = "EMITENTE";
                        break;
                      case '1':
                        $T005_Frete = "DESTINATÁRIO";
                        break;
                      case '2':
                        $T005_Frete = "TERCEIROS";
                        break;
                      case '9':
                        $T005_Frete = "SEM FRETE";
                        break;
                      default:
                        $T005_Frete = "NÃO INFORMADO";
                        break;
                      }
                      echo "<br /><font class=fonte style=\"font-size:12px\">Frete: <b>{$T005_Frete}</b></font>";
                      if (!empty($mT005['T005_Prazo_Entrega'])) {
                        echo "<br /><font class=fonte style=\"font-size:12px\">Entrega: <b>{$mT005['T005_Prazo_Entrega']}</b></font>";
                      }
                      if (!empty($mT005['T005_Ordem_Compra'])) {
                        echo "<br /><font class=fonte style=\"font-size:12px\">OC Cliente: <b>{$mT005['T005_Ordem_Compra']}</b></font>";
                      }
                      if (!empty($mT005['T005_Data_Entrega'])) {
                        $mT005['T005_Data_Entrega']=gCorrigeData($mT005['T005_Data_Entrega']);
                        echo "<br /><font class=fonte style=\"font-size:12px;\">Entrega: <b>{$mT005['T005_Data_Entrega']}</b></font>";
                      }
                   echo "</td></tr>";
                    echo "<tr><td colspan=2>";
                       if (!empty($mT005['T005_Observacao_1'])) {
                          echo "<br><font class='fonte' style='font-size:14px;background:yellow;'><b>{$mT005['T005_Observacao_1']}</b></font>";
                       }
                    echo "</td></tr>";
                   echo "</table>";
                 echo "</td>";
               echo "</tr>";
             echo "</table>";
           echo "</td>";
         echo "</tr>";
        echo "</table>";
       echo "</td>";
     echo "</tr>";

     //////////////////////////////////////////////////////////////////////////////////////////////
     // CORPO DO RELATORIO
     //////////////////////////////////////////////////////////////////////////////////////////////
     echo "<tr valign=top height=100% bgcolor=white>";
       echo "<td valign=top>";

         echo "<table align=center cellspacing=1 cellpadding=3 border=0 width=100% bgcolor=black valign=top>";

           echo "<tr bgcolor=#f4f4f4>";
             echo "<td align='center' valign='center' width='30'";
               echo "<font class='fonte'><b>Id</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='30'";
               echo "<font class='fonte'><b>Código</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='30'";
               echo "<font class='fonte'><b>Cód. Cliente</b></font>";
             echo "</td>";

             echo "<td align='left' valign='center'>";
               echo "<font class='fonte'><b>Descrição Produto</b></font>";
             echo "</td>";

             echo "<td align='left' valign='center'>";
               echo "<font class='fonte'><b>Marca</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='20'>";
               echo "<font class='fonte'><b>Un</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>Quant.</b></font>";
             echo "</td>";

             echo "<td align='center' valign='center' width='40'>";
               echo "<font class='fonte'><b>Estoque Atual</b></font>";
             echo "</td>";

             if($mT005['T005_Flag_Status'] == '10'){
                 echo "<td align='center' valign='center' width='40'>";
                   echo "<font class='fonte'><b>Separado</b></font>";
                 echo "</td>";
             }
             
             echo "<td align='center' valign='center' width='100'>";
               echo "<font class='fonte'><b>Local</b></font>";
             echo "</td>";

           echo "</tr>";


         $cClausula_Limit = $cClausula;
         $Resultado_Itens = mysql_query($cClausula_Limit);

         $Sequencial=0;
         $nTotal_Linhas_Impressas=0;

         while ($mResultado=mysql_fetch_array($Resultado_Itens)) {

                $mResultado['T006_Descricao_Produto']=stripslashes($mResultado['T006_Descricao_Produto']);

                echo "<tr bgcolor=white height=25>";

                  echo "<td align='center' valign='center'>";
                    echo "<font class='fonte'>{$mResultado['T006_Id']}</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='30'>";
                    echo "<font class='fonte'>{$mResultado['T006_Codigo_Produto']}</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='30'>";
                    echo "<font class='fonte'>{$mResultado['T006_Codigo_Substituto']}</font>";
                  echo "</td>";

                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'><b>{$mResultado['T006_Descricao_Produto']}</b></font>";
                    $sqlT204 = mysql_query("SELECT * FROM T204 WHERE T204_T006_Id = '{$mResultado['T006_Id']}'");
                    if(mysql_num_rows($sqlT204) > 0){
                    	while($rowT204 = mysql_fetch_array($sqlT204)){
                    		echo "<br><font class='fonte'>{$rowT204['T204_Descricao']} - ".number_format($rowT204['T204_Valor_Unitario'],2,',','.')." - {$rowT204['T204_Observacao']}</font>";
                    	}	
                    }
                    if (!empty($mResultado['D003_Imagem'])) {
                        $mResultado['D003_Imagem'] = explode(".", $mResultado['D003_Imagem']);
                        $dir = "/hardness3/dados_usuarios/{$Banco_Dados}/produtos/linhas/{$mResultado['D003_Imagem'][0]}-100.{$mResultado['D003_Imagem'][1]}";
                        echo "<br /><img src=\"{$dir}\" />";
                    }
                    
                  echo "</td>";

                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>{$mResultado['D082_Marca']}</font>";
                  echo "</td>";

                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>{$mResultado['D037_Unidade']}</font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'><b><big>".number_format($mResultado['T006_Quantidade'],2,',','.')."</big></b></font>";
                  echo "</td>";

                  echo "<td align='center' valign='center' width='40'>";
                    echo "<font class='fonte'>".number_format($mResultado['D009_Quantidade_Estoque_Real'],2,',','.')."</font>";
                  echo "</td>";

                  if($mT005['T005_Flag_Status'] == '10'){
                      echo "<td align='center' valign='center' width='40'>";
                        echo "<font class='fonte'><b><big>".number_format($mResultado['Separado'],2,',','.')."</big></b></font>";
                      echo "</td>";
                  }
                  

                  echo "<td align='left' valign='center'>";
                    echo "<font class='fonte'>{$mResultado['D004_Local']}";
                        if(!empty($mResultado['D049_Data_Validade'])){
                            echo "<br /><i>Validade:{$mResultado['D049_Data_Validade']}</i>";
                        }
                    echo "</font>";
                  echo "</td>";

                echo "</tr>";

                $nTotal_Linhas_Impressas++;

         }


echo "</table></td></tr>";
$Moeda = "R";
if ($Moeda=='D') {
    $mT005['T005_Valor_Total_Produtos']=$mT005['T005_Valor_Total_Produtos']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Frete']=$mT005['T005_Valor_Frete']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Mao_Obra']=$mT005['T005_Valor_Mao_Obra']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Despesas_Acessorias']=$mT005['T005_Valor_Despesas_Acessorias']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Desconto']=$mT005['T005_Valor_Desconto']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Total']=$mT005['T005_Valor_Total']/$mT005['T005_Valor_Dolar'];
    $mT005['T005_Valor_Total_IPI']=$mT005['T005_Valor_Total_IPI']/$mT005['T005_Valor_Dolar'];
}

if ($mT005['T005_Valor_Mao_Obra']>0) {

    echo "<tr bgcolor=white><td>";
    echo "<table align=center border='0' valign=top cellpading=0 cellspacing=0 width='$Width_Relatorio' bgcolor='white'>";

    if (!empty($mT005['T005_Mao_Obra_1'])) {
      echo "<tr bgcolor=white>";
          echo "<td valign=top colspan=10>";
            echo "<font class='fonte'>{$mT005['T005_Mao_Obra_1']}<br></font>";
            echo "<font class='fonte'>{$mT005['T005_Mao_Obra_2']}<br></font>";
            echo "<font class='fonte'>{$mT005['T005_Mao_Obra_3']}<br></font>";
            echo "<font class='fonte'>{$mT005['T005_Mao_Obra_4']}</font>";
          echo "</td>";
      echo "</tr>";

    }

    echo "</table>";

    echo "</td></tr>";

}

echo "<tr bgcolor=white><td>";


///////////////////////////////////////////////////////////////////////////////////
// TOTALIZACAO
///////////////////////////////////////////////////////////////////////////////////

$mC004['C004_Observacao_Rodape_Orcamento']=str_replace("\n","<br>",$mC004['C004_Observacao_Rodape_Orcamento']);

echo "<font class=fonte>Sistema Hardness - www.hardness.com.br</font>";

function agora(){
  return date("d/m/Y H:m:s");
}

?>
</body>
</html>
