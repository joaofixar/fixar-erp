<?php
/*
Programador: Evantoni Fuchs
Data.......: 13/03/2008
*/

// Cria ou inicia uma sessão
include_once("../include/sessao.inc");

//--  Conexão com o banco de dados
include_once("../include/conexao.inc");

include_once("../func/funcoes_genericas.inc");

//--  Cria o historico de navegacao
include "../include/voltar.inc";

include_once("../class/super_pesquisa.class.php");
$oSuperPesquisa = new classeSuperPesquisa();
$oSuperPesquisa -> iniciarFuncoesUnicas();

include "../class/calendario.php";
$oCalendario = new Calendario();

echo "<title>Boleto bancário</title>";
echo "<body bottommargin=0 topmargin=0 rightmargin=0 bgcolor='#F2F2EE' leftmargin=0 onload=\"MostrarMensagem('none');\">";
//--  MENSAGEM "CARREGANDO..."
echo "<div id='divLoadingCenter' style='display:inline;'>
          <img src='../imagens/gif/loading.gif' alt='' /><br />
          &nbsp;Aguarde...
      </div>";

echo "
<link rel=stylesheet href='../classes/classe_tamanho_medio_firefox.css' type='text/css'>
<STYLE TYPE='text/css'>
A:link {text-decoration:none; color:000000}
A:visited {text-decoration:none; color:000000}
A:hover {text-decoration:none; color:000000; background-color=none; cursor=hand}
A:active {text-decoration:none; color:000000; background-color=none; cursor=hand}
</STYLE>

<script src='../func/ajax.js'></script>
<script src='../func/funcoes_genericas.js'></script>
";

echo "
<script>
function Recarregar()
{
    location.replace('".$_SERVER['REQUEST_URI']."');
}
function Confirmacao_Gerar_Boleto()
{
    if (confirm(\"Ao gerar o boleto todas as contas serão alteradas para cobrança bancária e para o banco informado. Continuar?\"))
    {
        return true;
    }
    else
    {
        return false;
    }
}
function AlterarPercentual(D007_Id)
{
    document.getElementById('fme_geral').src='alterar_percentual.php?D007_Id='+D007_Id;
}
function abrir_boleto(T117_Id)
{
    nova_janela('../boleto/gerar_boleto.php?T117_Id='+T117_Id,0,0,800,500,'no','no','no','no','yes','yes',0);
}
function MostrarMensagem(Visivel)
{
    document.getElementById('divLoadingCenter').style.display=Visivel;
    document.getElementById('divLoadingCenter').style.zIndex=zCount++;
}
</script>";

/*
if (empty($T007_Id) && empty($T002_Id) && empty($D024_Id))
{
	msg_erro_boleto("Não foi passado o T007_Id ou T002_Id");
}
*/

$cClausula  = " select ";
$cClausula .= " T007_D024_Id, ";
$cClausula .= " T002_D024_Id, ";
$cClausula .= " D024_Nome_Empresa, ";
$cClausula .= " D020_Nome_Cidade, ";
$cClausula .= " D018_UF, ";
$cClausula .= " D024_CEP, ";
$cClausula .= " D024_Cnpj, ";
$cClausula .= " D024_Endereco ";
$cClausula .= " from D024 ";
$cClausula .= " left join T002 on T002_D024_Id=D024_Id ";
$cClausula .= " left join T007 on T007_D024_Id=D024_Id ";
$cClausula .= " left join D018 on D018_Id=D024_D018_Id ";
$cClausula .= " left join D020 on D020_Id=D024_D020_Id ";
$cClausula .= " where D024_Id='$D024_Id' ";

$cSql_D024=mysql_query($cClausula);
echo mysql_error();
$mD024=mysql_fetch_array($cSql_D024);

echo "<iframe name='fme_geral' id='fme_geral' style='display:none;width:300;height:100;'></iframe>";
echo "<form name='formulario_bolet' id='formulario_bolet' method='post' target='fme_geral' action='gravar_formulario_boleto.php' onsubmit='return Confirmacao_Gerar_Boleto()' style='margin:0px'>";
echo "<input type='hidden' name='T117_D024_Id' id='T117_D024_Id' value='$D024_Id'>";

echo "<table align=left border=0 cellpadding=0 cellspacing=0 width=100%>";

     echo "<tr>";
          echo "<td colspan=2>";
            include '../menu/menu.php';
          echo "</td>";
     echo "</tr>";
     echo "<tr>";
          echo "<td style='border-top:1px solid white' colspan=2>";
            echo "<table width=700 border=0 cellpadding=2>";
                 echo "<tr>";
					  echo "<td align=right width=50>";
					    	echo "<font class='titulo'><b>Cliente:</b>";
   					  echo "</td>";
                      echo "<td>";
                         $oCliente = new classeSuperPesquisa();
                         $oCliente -> setNomeCampo('D024_Id');
                         $oCliente -> setTamanhoCampo('50');
                         $oCliente -> setTabelaSql("D024");
                         $oCliente -> setCamposSql("D024_Nome_Empresa,,D024_Nome_Fantasia,,D024_Id");
                         $oCliente -> setFiltroFixoSql("");
                         $oCliente -> setRelacionamentosSql("");
                         $oCliente -> setAgruparSql("");
                         $oCliente -> setOrdenarSql("order by D024_Nome_Empresa");

                         $oCliente -> setTituloConsulta("CONSULTA RÁPIDA DE CLIENTES/FORNECEDORES");
                         $oCliente -> setTitulosColunas("Razão Social,,Nome Fantasia,,Código");
                         $oCliente -> setColunasVisualizadas("D024_Nome_Empresa,,D024_Nome_Fantasia,,D024_Id");

                         $oCliente -> setTamanhoColunas("300,,300,,50");
                         $oCliente -> setAlinhamentoColunas("left,,left,,center");
                         $oCliente -> setTipoDadosColunas("str,,str,,str"); //-- str, date, int

                         $oCliente -> setVisualizarCampoPK("S");
                         $oCliente -> setValorPadraoId("$D024_Id");
                         $oCliente -> setValorPadraoTexto("$mD024[D024_Nome_Empresa]");

                         $TabIndex++;
                         $oCliente -> setTabIndex($TabIndex);
                         $TabIndex++;

                         $oCliente -> criarCampo();

                      echo "</td>";
                      //echo "<td align=center>";
                      //   echo "<input type='button' value='Incluir Boleto' class=botao onclick=location.replace('../monta/monta_formularios_new.php?Entidade=T117&operacao=1&Codigo_Consulta=')>&nbsp;&nbsp;";
                      //echo "</td>";
                 echo "</tr>";

                 if (!empty($D024_Id)) {
                     echo "<tr>";
                          echo "<td style='font-weight:bold' colspan=2>";
                             include "../receber/dados_cliente.php";
                          echo "</td>";
                     echo "</tr>";
                 }

             echo "</table>";
          echo "</td>";

     echo "</tr>";
     echo "<tr>";
          echo "<td colspan=2>";
            echo "<table width=700 border=0 cellpadding=2>";
                 echo "<tr>";
                      echo "<td>";
                         echo "<font class=fonte>Banco</font><BR>";
                         $cSql_D007 = mysql_query("select * from D007 left join D062 on D062_Id=D007_D062_Id where D062_Id is not null and D007_C004_Id='$Empresa_Atual' order by D007_Id");
                         echo "<select name='T117_D007_Id' id='T117_D007_Id' class=campo onchange='AlterarPercentual(this.value)'>";
                         echo "<option value=''>[...]</option>";
                         while ($mD007 = mysql_fetch_array($cSql_D007))
                         {
                               echo "<option value='$mD007[D007_Id]'>$mD007[D007_Nome_Banco]</option>";
                         }
                         echo "</select>";
                      echo "</td>";
                      echo "<td>";
                         echo "<font class=fonte>Multa Atraso</font><BR>";
                         echo "<input type='text' name='T117_Percentual_Multa' id='T117_Percentual_Multa' value='0.00' class=campo size=7  style='text-align:center;'>";
                         echo "<font class=fonte>%</font>";
                      echo "</td>";
                      echo "<td>";
                         echo "<font class=fonte>Juros mês</font><BR>";
                         echo "<input type='text' name='T117_Percentual_Juros_Dia' id='T117_Percentual_Juros_Dia' value='0.00' class=campo size=6  style='text-align:center;'>";
                         echo "<font class=fonte>%</font>";
                      echo "</td>";
                      echo "<td>";
                         echo "<font class=fonte>Vencimento</font><BR>";
                         echo "<table border=0 cellpadding=0 cellspacing=0>";
                            	echo "<tr>";
                               		echo "<td>";
				                         echo "<input type='text' name='T117_Data_Vencimento' id='T117_Data_Vencimento' value='' class=campo size=10  style='text-align:center;' onKeyPress=\"return(Formatar_Data('0',this,event,'/'));\">";
                      				echo "</td>";
                               		echo "<td>";
                               			$oCalendario->setNomeCampo("T117_Data_Vencimento");
                       		 			$oCalendario->setNomeDiv("Div_Calendario_T117_Data_Vencimento");
                       	 	 			$oCalendario->AdicionarCalendario();
                               		echo "</td>";
                              	echo "</tr>";
                           echo "</table>";
                      echo "</td>";

                      
                      echo "<td rowspan=2 align=center>";
                         echo "<input type='submit' value='Gerar Boleto' style='height:50px' class=botao>";
                      echo "</td>";
                 echo "</tr>";
                 echo "<tr>";
                      echo "<td colspan=4>";
                         echo "<font class=fonte>Instruções</font><BR>";
                         echo "<textarea style='width:100%' name='T117_Instrucoes' id='T117_Instrucoes' rows=3></textarea>";
                      echo "</td>";
                 echo "</tr>";
             echo "</table>";
          echo "</td>";
     echo "</tr>";
     echo "<tr>";
          echo "<td valign=top>";

            $cClausula  = " select ";
            $cClausula .= " datediff(T002_Data_Vencimento,current_date()) Dias, ";
            $cClausula .= " T002_Id, ";
            $cClausula .= " T002_Numero_Duplicata, ";
            $cClausula .= " T002_Valor_Total, ";
            $cClausula .= " T002_Data_Vencimento, ";
            $cClausula .= " T002_Data_Recebimento, ";
            $cClausula .= " T007_Id, ";
            $cClausula .= " T007_Flag_ACP, ";
            $cClausula .= " D062_Numero_Banco, ";
            $cClausula .= " D027_Portador ";
            $cClausula .= " from T002 ";
            $cClausula .= " left join D027 on D027_Id=T002_D027_Id ";
            $cClausula .= " left join T007 on T007_Id=T002_T007_Id ";
            $cClausula .= " left join D007 on D007_Id=T002_D007_Id ";
            $cClausula .= " left join D062 on D062_Id=D007_D062_Id ";
            $cClausula .= " left join T117 on T117_Id=T002_T117_Id ";
            $cClausula .= " where T002_Flag_Cancelada!='S' ";
            $cClausula .= " and T002_C004_Id='$Empresa_Atual' ";
            $cClausula .= " and T117_Id is null ";
            $cClausula .= " and T002_Data_Recebimento='0000-00-00' ";
           	$cClausula .= " and T002_D024_Id='$D024_Id' ";
            $cClausula .= "order by T002_Data_Vencimento";

            $cSql_T002=mysql_query($cClausula);
            echo mysql_error();

  	        echo "<fieldset>";
	        echo "<legend class='fonte'>Selecione os titulos abaixo para gerar um boleto</legend>";
					    
            echo "<table width=100% border=0 >";
                 echo "<tr class=fonte bgcolor=lightblue>";
                      echo "<td>";
                         echo "Gerar";
                      echo "</td>";
                      echo "<td align=center>";
                         echo "Titulo";
                      echo "</td>";
                      echo "<td align=center width=70>";
                         echo "Vencimento";
                      echo "</td>";
                      echo "<td align=left width=150>";
                         echo "Portador";
                      echo "</td>";
                      echo "<td align=center>";
                         echo "Dias";
                      echo "</td>";
                      echo "<td  align=right>";
                         echo "Total";
                      echo "</td>";
                 echo "</tr>";

                    while ($mT002 = mysql_fetch_array($cSql_T002))
                    {
                         echo "<tr class=fonte bgcolor=white>";
                              echo "<td width=30 align=center>";
                                 echo "<input type='checkbox' id='T002_$mT002[T002_Id]' name='T002_$mT002[T002_Id]'>";
                              echo "</td>";
                              echo "<td align=center>";
                                 echo $mT002[T002_Numero_Duplicata];
                              echo "</td>";
                              echo "<td align=center>";
                                 echo troca_data_dma($mT002[T002_Data_Vencimento]);
                              echo "</td>";
                              echo "<td align=left>";
                                 echo $mT002[D027_Portador];
                              echo "</td>";
                              echo "<td align=center>";
                                 echo $mT002[Dias];
                              echo "</td>";
                              echo "<td  align=right>";
                                 echo number_format($mT002[T002_Valor_Total],2,',','.');
                              echo "</td>";
                         echo "</tr>";
                         
                         $Total+=$mT002[T002_Valor_Total];
                         
                    }
                     echo "<tr class=fonte bgcolor=white>";
                          echo "<td colspan=5>";
                             echo "<font class=fonte><b>Total</b></font>";
                          echo "</td>";
                          echo "<td  align=right>";
                             echo "<b>".number_format($Total,2,',','.')."</b>";
                          echo "</td>";
                     echo "</tr>";

            echo "</table>";
            echo "</fielset>";
            
        echo "</td>";
          echo "<td valign=top width=50%>";

            unset($total);

            $cClausula  = " select ";
            $cClausula .= " T117_Id, ";
            $cClausula .= " T117_Data_Emissao, ";
            $cClausula .= " T117_Data_Vencimento, ";
            $cClausula .= " T117_Valor_Total, ";
            $cClausula .= " T117_Nosso_Numero,";
            $cClausula .= " T117_Flag_Cancelado,";
            $cClausula .= " D007_Nome_Banco";
            $cClausula .= " from T117 ";
            $cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
            $cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
            $cClausula .= " where T117_D024_Id='$D024_Id' ";
            $cClausula .= "   and T117_C004_Id='$Empresa_Atual' ";
            $cClausula .= " order by T117_Id desc";
            $cClausula .= " limit 10";

            $T117=mysql_query($cClausula);

            echo mysql_error();

	        echo "<fieldset>";
	        echo "<legend class='fonte'>Últimos boletos emitidos para este Cliente</legend>";

            echo "<table width=100% border=0 >";
                 echo "<tr class=fonte bgcolor=lightblue>";
                      echo "<td align=center>";
                         echo "Nosso número";
                      echo "</td>";
                      echo "<td align=left width=120>";
                         echo "Banco";
                      echo "</td>";
                      echo "<td align=center width=70>";
                         echo "Emissão";
                      echo "</td>";
                      echo "<td align=center width=70>";
                         echo "Vencimento";
                      echo "</td>";
                      echo "<td  align=right width=70>";
                         echo "Total";
                      echo "</td>";
                 echo "</tr>";

                    while ($mT117 = mysql_fetch_array($T117))
                    {
                           if ($mT117[T117_Flag_Cancelado]=='S')
                           {
                              $cCorFonte='gray';
                           }
                           else
                           {
                              $cCorFonte='black';
                           }
                           echo "<tr class=fonte bgcolor=white style='cursor:pointer;color:$cCorFonte' onclick=\"abrir_boleto('$mT117[T117_Id]');\">";
                              echo "<td align=center>";
                                 echo $mT117[T117_Nosso_Numero];
                              echo "</td>";
                              echo "<td align=left>";
                                 echo $mT117[D007_Nome_Banco];
                              echo "</td>";
                              echo "<td align=center>";
                                 echo troca_data_dma($mT117[T117_Data_Emissao]);
                              echo "</td>";
                              echo "<td align=center>";
                                 echo troca_data_dma($mT117[T117_Data_Vencimento]);
                              echo "</td>";
                              echo "<td  align=right>";
                                 echo number_format($mT117[T117_Valor_Total],2,',','.');
                              echo "</td>";
                         echo "</tr>";

                         $Total+=$mT117[T117_Valor_Total];

                    }

            echo "</table>";
            echo "</fielset>";

        echo "</td>";
    echo "</tr>";
echo "</table>";

echo "</form>";

?>
