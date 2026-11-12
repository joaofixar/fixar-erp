<?php

// Cria ou inicia uma sessão
include "../include/sessao.inc";

//--  Conexão com o banco de dados
include "../include/conexao.inc";

include "../func/funcoes_genericas.inc";

$cNomeTelaFiltro='boleto';
include "../include/include_filtro.php";


?>

<body bgcolor=white bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0>

<?php

echo "<script>
function muda_pagina(opcao)
{
   location.replace('pedidos.php?opcao='+opcao+'&D024_Id=$D024_Id&Data=$cData');
}
function seleciona_cliente(D024_Id)
{
   parent.document.getElementById('D024_Id').value=D024_Id;
   parent.Atualizar_Tela(0);
}
function abre_danfe(parametro,nome_janela) {
  var param='';
  param+='menubar=no,';
  param+='toolbar=no,';
  param+='location=no,';
  param+='top=0,';
  param+='left=0,';
  param+='status=no,';
  param+='scrollbars=yes,';
  param+='resizable=yes,';
  var Total_Largura=950;
  var Total_Altura=600;
  param+='width='+Total_Largura+',';
  param+='height='+Total_Altura+'';
  new_window=window.open(parametro,nome_janela,param);
  new_window.focus();
}

function cancela_nfe(parametro,nome_janela) {
  var param='';
  param+='menubar=no,';
  param+='toolbar=no,';
  param+='location=no,';
  param+='top=0,';
  param+='left=0,';
  param+='status=no,';
  param+='scrollbars=yes,';
  param+='resizable=yes,';
  var Total_Largura=650;
  var Total_Altura=500;
  param+='width='+Total_Largura+',';
  param+='height='+Total_Altura+'';
  new_window=window.open(parametro,nome_janela,param);
  new_window.focus();
}

</script>";

echo "<link rel=stylesheet href='../classes/classe_tamanho_medio_firefox.css' type='text/css'>";

    // Todos os pedidos da data recebida

    $cClausula  = " select ";
    $cClausula .= " T117_Data_Emissao, ";
    $cClausula .= " sum(T117_Valor_Total) Total, ";
    $cClausula .= " count(*) Qte_Notas ";
    $cClausula .= " from T117 ";
    $cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
    $cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
    $cClausula .= " where T117_C004_Id='$Empresa_Atual' ";
    if (!empty($xConsulta_Clausula_Filtro))
    {
       $cClausula .= " and ".$xConsulta_Clausula_Filtro;
    }
    $cClausula .= " group by T117_Data_Emissao";
    $cClausula .= " order by T117_Data_Emissao";
    $T117 = mysql_query($cClausula);

    echo mysql_error();

    echo "<table border=0 cellspacing=0  cellpadding=3 align=left >";

      echo "<tr>";
    	echo "<td align=center   width=80 class='td_titulo'>";
            echo "Dia";
        echo "</td>";
    	echo "<td align=center width=30 class='td_titulo'>";
            echo "Qte";
        echo "</td>";
    	echo "<td align=right  width=80 class='td_titulo'>";
            echo "Valor total";
        echo "</td>";
      echo "</tr>";

      while ($mT117 = mysql_fetch_array($T117))
      {

           if ($cor!='white')
           {
               $cor='white';
           }
           else
           {
               $cor='#E2EDF3';
           }

      	   echo "<tr class='tr_dados' bgcolor='$cor'>";
              echo "<td align=center bgcolor='$cor' class='td_itens' style=''>";
                 echo "<font class=fonte>".troca_data_dma($mT117[T117_Data_Emissao])."</font>";
              echo "</td>";
              echo "<td align=center bgcolor='$cor' class='td_itens' style=''>";
                 echo "<font class=fonte>$mT117[Qte_Notas]</font>";
              echo "</td>";
              echo "<td align=right bgcolor='$cor' class='td_itens' style=''>";
                 echo "<font class=fonte>".number_format($mT117[Total],2,',','.')."</font>";
              echo "</td>";
           echo "</tr>";
      }
    echo "</table>";


?>
