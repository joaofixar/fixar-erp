<?php

// Cria ou inicia uma sessão
include "../include/sessao.inc";

//--  Conexão com o banco de dados
include "../include/conexao.inc";

include "../func/funcoes_genericas.inc";

//-- NOME DA TELA :
//-- SERA CRIADO UM ARQUIVO PHP DENTRO DA PASTA DADOS_USUARIOS COMO O NOME QUE FOR DEFINIDO
//-- SERVE PARA GUARDAR OS FILTROS E CONDICOES QUE O USUARIO ESCOLHER, PORTANTO,
//-- PARA CADA FILTRO, O IDEIAL É QUE TENHA UM NOME DIFERENTE
$cNomeTelaFiltro='boleto';
include "../include/include_filtro.php";

?>

<body bgcolor=white bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0>

<?php

echo "<script src='../func/ajax.js'></script>";
echo "<script src='../func/funcoes_genericas.js'></script>";

echo "<script>
function seleciona_cliente(D024_Id)
{
   parent.document.getElementById('D024_Id').value=D024_Id;
   parent.Atualizar_Tela(0);
}
function abrir_boleto(T117_Id)
{
    nova_janela('../boleto/gerar_boleto.php?T117_Id='+T117_Id,0,0,800,500,'no','no','no','no','yes','yes',0);
}
function alterar_boleto(T117_Id) {
     top.location.replace('../monta/principal_formulario.php?Entidade=T117&Tamanho=500&Nova_Janela=S&operacao=2&Codigo_Consulta=001&T117_Id='+T117_Id+'&Id_Entidade='+T117_Id);
}
function ordem(campo) {

     document.getElementById('fme_geral').src='../include/executar_ordem.php?cOrdem='+campo+'&cNomeTelaFiltro=$cNomeTelaFiltro';

}
function cancelar_boleto(T117_Id)
{
    if (confirm(\"Confirma o cancelamento deste boleto?\"))
    {
       document.getElementById('fme_gravar').src='cancelar_boleto.php?T117_Id='+T117_Id;
    }
}
</script>";

echo "<iframe name='fme_gravar' id='fme_gravar' style='display:none' tabindex=-1></iframe>";

echo "<link rel=stylesheet href='../classes/classe_tamanho_medio_firefox.css' type='text/css'>";
echo "<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 ";
echo "onLoad=\"parent.MostrarMensagem('none')\" ";
echo "bgcolor='#FFFFFF' ";
echo ">";

    // Todos os pedidos da data recebida

    $cClausula  = " select ";
    $cClausula .= " T117_Id, ";
    $cClausula .= " T117_T035_Id, ";
    $cClausula .= " T117_Data_Emissao, ";
    $cClausula .= " T117_Data_Vencimento, ";
    $cClausula .= " T002_Data_Recebimento, ";
    $cClausula .= " group_concat(DISTINCT T002_Numero_Duplicata order by T002_Numero_Duplicata SEPARATOR ',') T002_Numero_Duplicata, ";
    $cClausula .= " T117_Valor_Total, ";
    $cClausula .= " T117_Nosso_Numero,";
    $cClausula .= " T117_Flag_Layout,";
    $cClausula .= " T117_Flag_Enviar_Remessa,";
    $cClausula .= " T117_Data_Envio_Email,";
    $cClausula .= " D007_Nome_Banco,";
    $cClausula .= " T117_Flag_Cancelado,";
    $cClausula .= " D024_Nome_Fantasia, ";
    $cClausula .= " D024_E_Mail ";
    $cClausula .= " from T117 ";
    $cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
    $cClausula .= " left join T002 on T002_T117_Id=T117_Id ";
    $cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
    $cClausula .= " where T117_C004_Id='$Empresa_Atual' ";
    if (!empty($xConsulta_Clausula_Filtro))
    {
       $cClausula .= " and ".$xConsulta_Clausula_Filtro;
    }
    $cClausula .= " group by T117_Id ";
    $cClausula .= " $xConsulta_Order_By ";
    $cClausula .= " limit 500 ";
    $T117 = mysql_query($cClausula);
    
    echo mysql_error();

    $cor='#E2EDF3';

    echo "<table border=0 cellpadding=3 cellspacing=0 style='border:1px solid; border-color:white gray gray white'>";

        echo "<tr>";
             echo "<td width=30 align=center class='td_titulo'>";
                echo "<small>";
                   echo link_ordem("Remessa","T117_Flag_Enviar_Remessa","$xConsulta_Ordem","$xConsulta_Ordem_Desc");
                echo "</small>";
             echo "</td>";
             echo "<td width=100 align=center class='td_titulo'>";
                echo link_ordem("Nosso Número","T117_Nosso_Numero","$xConsulta_Ordem","$xConsulta_Ordem_Desc");
             echo "</td>";
             echo "<td width=100 align=center class='td_titulo'>";
                echo link_ordem("Duplicata","T002_Numero_Duplicata","$xConsulta_Ordem","$xConsulta_Ordem_Desc");
             echo "</td>";
             echo "<td align=left width=100 class='td_titulo'>";
                echo link_ordem("Banco","D007_Nome_Banco","$xConsulta_Ordem","$xConsulta_Ordem_Desc");
             echo "</td>";
             echo "<td align=left width=250 class='td_titulo'>";
                echo link_ordem("Cliente","D024_Nome_Fantasia","$xConsulta_Ordem","$xConsulta_Ordem_Desc");
             echo "</td>";
             echo "<td align=center width=70 class='td_titulo'>";
                echo link_ordem("Emissão","T117_Data_Emissao","$xConsulta_Ordem","$xConsulta_Ordem_Desc");
             echo "</td>";
             echo "<td align=center width=70 class='td_titulo'>";
                echo link_ordem("Vencimento","T117_Data_Vencimento","$xConsulta_Ordem","$xConsulta_Ordem_Desc");
             echo "</td>";
             echo "<td align=center width=70 class='td_titulo'>";
                echo link_ordem("Recebido","T002_Data_Recebimento","$xConsulta_Ordem","$xConsulta_Ordem_Desc");
             echo "</td>";
             echo "<td  align=right width=70 class='td_titulo'>";
                echo link_ordem("Total","T117_Valor_Total","$xConsulta_Ordem","$xConsulta_Ordem_Desc");
             echo "</td>";
             echo "<td  align=right width=70 class='td_titulo'>";
                echo "&nbsp;";
             echo "</td>";
        	echo "<td align=center class='td_titulo'>";
                echo "eMail";
            echo "</td>";
        	echo "<td align=left width=150 class='td_titulo'>";
                echo "E-Mail";
            echo "</td>";
        echo "</tr>";

        while ($mT117 = mysql_fetch_array($T117))
        {

               if ($cor=='#E2EDF3')
               {
                   $cor='white';
               }
               else
               {
                   $cor='#E2EDF3';
               }
               
               if ($mT117[T117_Flag_Cancelado]=='S')
               {
                  $cCorFonte='gray';
               }
               else
               {
                  $cCorFonte='black';
               }

               echo "<tr class=fonte bgcolor='$cor' style='cursor:pointer;color:$cCorFonte'>";
                  if ($mT117[T117_Flag_Layout]=='S' and $mT117[T117_Flag_Cancelado]!='S' and $mT117[T117_T035_Id]<=0)
                  {
                      echo "<td align=center class='td_itens'>";
                          echo "<INPUT TYPE='checkbox' name='T117_Flag_Enviar_Remessa' id='T117_Flag_Enviar_Remessa' onChange=\"parent.GravarEdicaoCampo(this,'$mT117[T117_Id]')\" ";
                          if ($mT117[T117_Flag_Enviar_Remessa]=='S')
                          {
                              echo "checked";
                          }
                          echo ">";
                      echo "</td>";
                  }
                  elseif ($mT117[T117_T035_Id]>0)
                  {
                      echo "<td align=center class='td_itens'>";
                          echo "<a href='../remessa/download_remessa.php?T035_Id=$mT117[T117_T035_Id]' target='fme_gravar'>";
                             echo $mT117[T117_T035_Id];
                          echo "</a>";
                      echo "</td>";
                  }
                  else
                  {
                      echo "<td align=center class='td_itens'>";
                         echo "&nbsp;";
                      echo "</td>";
                  }

                  echo "<td align=center class='td_itens' onclick=\"abrir_boleto('$mT117[T117_Id]');\">";
                     echo $mT117[T117_Nosso_Numero].'&nbsp;';
                  echo "</td>";
                  echo "<td align=center class='td_itens' onclick=\"abrir_boleto('$mT117[T117_Id]');\">";
                     echo substr($mT117[T002_Numero_Duplicata],0,15).'&nbsp;';
                  echo "</td>";
                  echo "<td align=left class='td_itens' onclick=\"abrir_boleto('$mT117[T117_Id]');\">";
                     echo substr($mT117[D007_Nome_Banco],0,15);
                  echo "</td>";
                  echo "<td align=left class='td_itens' onclick=\"abrir_boleto('$mT117[T117_Id]');\">";
                     echo substr($mT117[D024_Nome_Fantasia],0,25);
                  echo "</td>";
                  echo "<td align=center class='td_itens' onclick=\"abrir_boleto('$mT117[T117_Id]');\">";
                     echo troca_data_dma($mT117[T117_Data_Emissao]);
                  echo "</td>";
                  echo "<td align=center class='td_itens' onclick=\"abrir_boleto('$mT117[T117_Id]');\">";
                     echo troca_data_dma($mT117[T117_Data_Vencimento]);
                  echo "</td>";
                  echo "<td align=center class='td_itens' onclick=\"abrir_boleto('$mT117[T117_Id]');\">";
                     echo troca_data_dma($mT117[T002_Data_Recebimento]).'&nbsp;';
                  echo "</td>";
                  echo "<td  align=right class='td_itens' onclick=\"abrir_boleto('$mT117[T117_Id]');\">";
                     echo number_format($mT117[T117_Valor_Total],2,',','.');
                  echo "</td>";
                  echo "<td  align=center class='td_itens'>";
                       if ($mT117[T117_Flag_Cancelado]=='N')
                       {
                          echo "<a href=\"javascript:cancelar_boleto('$mT117[T117_Id]');\" style='color:blue;text-decoration:none;'>";
                          echo "<font class=fonte>";
                          echo "Cancelar";
                          echo "</font>";
                          echo "</a>";
                       }
                       else
                       {
                          echo "&nbsp;";
                       }
                  echo "</td>";
                  echo "<td align=center bgcolor='$cor' class='td_itens' style='cursor:pointer' onclick=\"javascript:parent.AbrirFormularioEmail('$mT117[T117_Id]')\" width=16>";
                         if ($mT117[T117_Flag_Cancelado]=='S')
                         {
                             echo "&nbsp;";
                         }
                         else
                         {
                             if ($mT117[T117_Data_Envio_Email]!='0000-00-00 00:00:00' and $mT117[T117_Data_Envio_Email]!='')
                             {
                                echo "<img src='../imagens/gif/icone_enviado.gif' title='E-mail enviado em: ".troca_datahora_dma($mT117[T117_Data_Envio_Email])."'>";
                             }
                             else
                             {
                                echo "<img src='../imagens/gif/icone_enviar.gif'>";
                             }
                         }

                  echo "</td>";
                  echo "<td align=left class='td_itens' onclick=\"abrir_boleto('$mT117[T117_Id]');\">";
                     echo substr($mT117[D024_E_Mail],0,20);
                  echo "</td>";

             echo "</tr>";

             $Total+=$mT117[T117_Valor_Total];

        }

    echo "</table>";
    
    echo "<iframe name='fme_gravar' id='fme_gravar' style='display:none' tabindex=-1 ></iframe>";
    echo "<iframe src='' name='fme_geral' id='fme_geral' style='display:none;'></iframe>";


    echo mysql_error();


?>
