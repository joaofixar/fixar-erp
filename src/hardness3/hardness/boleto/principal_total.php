<?php

// Cria ou inicia uma sessão
include "../include/sessao.inc";

//--  Conexão com o banco de dados
include "../include/conexao.inc";

include "../func/funcoes_genericas.inc";

include "../class/super_pesquisa.class.php";

//--  Cria o historico de navegacao
include "../include/voltar.inc";

$cNomeTelaFiltro='boleto';
include "../include/include_filtro.php";


$C004=mysql_query("select C004_Casas_Decimais_Preco from C004 where C004_Id='$Empresa_Atual'");
$mC004=mysql_fetch_array($C004);

$oSuperPesquisa = new classeSuperPesquisa();
$oSuperPesquisa -> iniciarFuncoesUnicas();

echo "<html>";
echo "<head>";
echo "<title>Boleto Bancario</title>";
echo "</head>";

?>

<style>
.Class_IFrame_Sem_Borda {
   visibility:visible;
   border:0px;
   border-bottom:1px solid #909090;
   width:99.6%;
   height:99.5%;
   padding : 0px;
}
.Class_IFrame_Sem_Borda1 {
   visibility:visible;
   border:1px solid #c0c0c0;
   border-bottom:1px solid white;
   width:99.6%;
   height:99.5%;
   padding : 0px;
}
</style>

<?php

echo "<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 ";
echo "onLoad=\"MostrarMensagem('none');";
echo "document.getElementById('Consulta_D024_Id').focus(); ";
echo "\"";
echo "bgcolor='#FFFFFF' ";
echo ">";

//--  MENSAGEM "CARREGANDO..."
echo "<div id='divLoadingCenter' style='display:inline;'>
          <img src='../imagens/gif/loading.gif' alt='' /><br />
          &nbsp;Aguarde...
      </div>";

if (empty($cData))
{
   $cData=hoje();
}

if (!empty($nIntervalo_Dias))
{
    list($cDia_Selecionado,$cMes_Selecionado,$cAno_Selecionado)=explode("/",$cData);
    $cDia_Selecionado+=$nIntervalo_Dias;
    $cData=date("d/m/Y",mktime(0, 0, 0, $cMes_Selecionado, $cDia_Selecionado, $cAno_Selecionado));
}

$cClausula  = " select ";
$cClausula .= " D024_Id, ";
$cClausula .= " D024_Nome_Empresa ";
$cClausula .= " from D024 ";
$cClausula .= " where D024_Id='$D024_Id' ";
$cSql_D024 = mysql_query($cClausula);

$mD024=mysql_fetch_array($cSql_D024);

?>
<link rel=stylesheet href="../classes/classe_tamanho_medio_firefox.css" type="text/css">
<STYLE TYPE="text/css">
A:link {text-decoration:none; color:000000}
A:visited {text-decoration:none; color:000000}
A:hover {text-decoration:none; color:000000; background-color=none; cursor=hand}
A:active {text-decoration:none; color:000000; background-color=none; cursor=hand}

#divLoadingCenter
{
	width:150px;
	height:25px;
	position: absolute;
	top:50%;
	left:48%;
	margin-top:-50px;
	margin-left:-50px;
	z-index:auto;
	background-color: yellow;
	border: 1px solid #BDCBD8;
	clear:both;
	vertical-align:middle;
	color: red;
	padding:12px;
    font-family:arial, tahoma, verdana, helvetica, sans-serif;
	font-size:14px;
	text-align:center;
}
</style>

<script src='../func/ajax.js'></script>
<script src='../func/funcoes_genericas.js'></script>
<script>
var zCount=1;
if (screen.availWidth<1000)
{
   var Total_Largura=789;
   var Total_Altura=527;
}
else
{
   var Total_Largura=screen.availWidth;
   var Total_Altura=screen.availHeight;
}

function AbreDivCalendario(e,cAno_Selecionado,cMes_Selecionado,Campo_Data)
{
    var PosicaoX=0;
    var PosicaoY=0;
    var Posicao_FinalX_Div=0;
    var Posicao_FinalY_Div=0;
    var mouseX=0;
    var mouseY=0;
    var mouse2X=0;
    var mouse2Y=0;
    var Tamanho_Total_Menu_Registro=200;
	document.getElementById('DIV_calendario').style.display='inline';
    if (ffox)
    {
    	mouseX = window.pageXOffset + e.clientX
        mouseY = window.pageYOffset + e.clientY
    	mouse2X = parent.window.pageXOffset + e.clientX
        mouse2Y = parent.window.pageYOffset + e.clientY
    }
    else
    {
        mouseX = window.event.x + document.body.scrollLeft;
    	mouseY = window.event.y + document.body.scrollTop;
        mouse2X = parent.window.event.x + document.body.scrollLeft;
    	mouse2Y = parent.window.event.y + document.body.scrollTop;

    }
    mouseOut = false;
    //-- POSICAO X
    Posicao_FinalX_Div=eval(Tamanho_Total_Menu_Registro)+eval(mouse2X);
    if (Posicao_FinalX_Div<0) {
        Posicao_FinalX_Div=Posicao_FinalX_Div*-1;
    }
    if (Posicao_FinalX_Div>(screen.availWidth-350)) {
        PosicaoX = mouseX - 220;
    } else {
        PosicaoX = mouseX + 15;
    }
    if (PosicaoX<=0) {
        PosicaoX=10;
    }

    //-- POSICAO Y
    Posicao_FinalY_Div=eval(Tamanho_Total_Menu_Registro)+eval(mouse2Y);
    if (Posicao_FinalY_Div<0) {
        Posicao_FinalY_Div=Posicao_FinalY_Div*-1;
    }
    if (Posicao_FinalY_Div>(screen.availHeight-300)) {
        PosicaoY = mouseY - Tamanho_Total_Menu_Registro;
    } else {
        PosicaoY = mouseY + 7;
    }
    if (PosicaoY<=0) {
        PosicaoY=10;
    }
    var Data=document.getElementById(Campo_Data).value;
	cLink='calendario.php?Data='+Data+'&Campo_Data='+Campo_Data;
	document.getElementById('DIV_calendario').style.left=PosicaoX;
	document.getElementById('DIV_calendario').style.top=PosicaoY;
	carregar_ajax(cLink,'POST',false,'DIV_calendario');
}

function FechaDivCalendario()
{
	document.getElementById('DIV_calendario').style.display='none';
}

function AtualizaDivCalendario(Data,Campo_Data)
{
	cLink='calendario.php?Data='+Data+'&Campo_Data='+Campo_Data;
	carregar_ajax(cLink,'POST',false,'DIV_calendario');
}


function MostrarMensagem(Visivel)
{
    document.getElementById('divLoadingCenter').style.display=Visivel;
    document.getElementById('divLoadingCenter').style.zIndex = zCount++;
}

function CentralizarDiv(Div,Largura,Altura)
{
   var W = (Total_Largura/2)-(Largura/2);
   var H = (Total_Altura/2)-(Altura/2);
   document.getElementById(Div).style.top = H;
   document.getElementById(Div).style.left = W;
   document.getElementById(Div).style.width = Largura;
   document.getElementById(Div).style.height = Altura;
}
function Atualizar_Iframes()
{
   MostrarMensagem('inline');
   var D024_Id=document.getElementById('D024_Id').value;
   var Data=document.getElementById('Data').value;
   var Tipo=document.getElementById('Tipo').value;
   var Receber=document.getElementById('Receber').checked;
   var Recebido=document.getElementById('Recebido').checked;
   document.getElementById('iFrame_Total_Dia').src='ultimas_vendas.php?D024_Id='+D024_Id+'&Data='+Data+'&Tipo='+Tipo+'&Receber='+Receber+'&Recebido='+Recebido;
   document.getElementById('iFrame_Contas_Dia').src='pedido_dia.php?D024_Id='+D024_Id+'&Data='+Data+'&Tipo='+Tipo+'&Receber='+Receber+'&Recebido='+Recebido;
   document.getElementById('iFrame_Clientes').src='clientes.php?Data='+Data+'&Tipo='+Tipo+'&Receber='+Receber+'&Recebido='+Recebido;
   document.getElementById('iFrame_Total_Mes').src='pedidos.php?Data='+Data+'&Tipo='+Tipo+'&Receber='+Receber+'&Recebido='+Recebido;
}
function Atualizar_Tela(Intervalo_Dias)
{
   MostrarMensagem('inline');
   var D024_Id=document.getElementById('D024_Id').value;
   var Data_Inicio=document.getElementById('Data_Inicio').value;
   var Data_Fim=document.getElementById('Data_Fim').value;
   var D006_Flag_Venda_Compra_Outros=document.getElementById('D006_Flag_Venda_Compra_Outros').value;
   document.location.replace('principal_total.php?D024_Id='+D024_Id+'&Data_Inicio='+Data_Inicio+'&Data_Fim='+Data_Fim+'&D006_Flag_Venda_Compra_Outros='+D006_Flag_Venda_Compra_Outros);
}
function Atualizar_Tela_Data(Data)
{
   MostrarMensagem('inline');
   var D024_Id=document.getElementById('D024_Id').value;
   document.location.replace('principal_total.php?D024_Id='+D024_Id+'&Data='+Data);
}
function Iniciar()
{
   MostrarMensagem('inline');
   document.location.replace('principal_total.php?D024_Id=0');
}
function CentralizarDiv(Div,Largura,Altura) {
   var W = (Total_Largura/2)-(Largura/2);
   var H = (Total_Altura/2)-(Altura/2);
   document.getElementById(Div).style.top = H;
   document.getElementById(Div).style.left = W;
   document.getElementById(Div).style.width = Largura;
   document.getElementById(Div).style.height = Altura;
}
</script>
<?php

$Titulo_Tela='Boleto Bancario';

echo "<table border=0 cellspacing=0 cellpadding=0 width=100% bgcolor=#F2F2EE height=100%>";

    echo "<tr align=left valign=top height=1%>";
		echo "<td>";
          include '../menu/menu.php';
		echo "</td>";
	echo "</tr>";
	echo "<tr align=left valign=top height=100%>";
		echo "<td colspan=2>";
			echo "<table cellspacing=0 cellpadding=1 border=0 width='100%' height=100% bgcolor='#F2F2EE'>";

           if ($Tela<2)
           
           {
            echo "<tr align=center valign=center height=10>";
                      echo "<td bgcolor='#F2F2EE' style='border-top:1px solid;border-color: white #F2F2EE #F2F2EE silver'>";
                        echo "<font class=fonte>Total Mensal
                        </font>";
    				  echo "</td>";
                      echo "<td bgcolor='#F2F2EE' style='border:1px solid;border-color: white #F2F2EE #F2F2EE silver'>";
                        echo "<font class=fonte>Total Dia.</font>";
    				  echo "</td>";
  			   	    echo "</tr>";

				    echo "<tr align=center valign=top>";
                        echo "<td>";
 						echo "<iframe id='iFrame_Resumo' class='Class_IFrame_Sem_Borda1' src='nota_mes_total.php' tabindex=-1>";
						echo "</iframe>";
					    echo "</td>";
                        echo "<td>";
 						echo "<iframe id='iFrame_Total_Mes' class='Class_IFrame_Sem_Borda1' src='nota_Dia_total.php' tabindex=-1>";
						echo "</iframe>";
  				        echo "</td>";
				    echo "</tr>";

				    echo "<tr align=center valign=center height=10>";
                      echo "<td bgcolor='#F2F2EE' style='border-top:1px solid;border-color: white #F2F2EE #F2F2EE silver'>";
                        echo "<font class=fonte>Total Banco</font>";
    				  echo "</td>";
                      echo "<td bgcolor='#F2F2EE' style='border:1px solid;border-color: white #F2F2EE #F2F2EE silver'>";
                        echo "<font class=fonte>Total Cliente</font>";
    				  echo "</td>";
                    echo "</tr>";

  			   	    echo "<tr align=center valign=top>";
                        echo "<td>";
 						echo "<iframe id='iFrame_Resumo' class='Class_IFrame_Sem_Borda1' src='nota_banco_total.php' tabindex=-1>";
						echo "</iframe>";
					    echo "</td>";
                        echo "<td>";
 						echo "<iframe id='iFrame_Total_Mes' class='Class_IFrame_Sem_Borda1' src='nota_Cliente_total.php' tabindex=-1>";
						echo "</iframe>";
  				        echo "</td>";
                    echo "</tr>";
           

			}
			
         else
         
           {
                 echo "<tr align=center valign=center height=10>";
                      echo "<td bgcolor='#F2F2EE' style='border-top:1px solid;border-color: white #F2F2EE #F2F2EE silver'>";
                        echo "<font class=fonte></font>";
    				  echo "</td>";
                      echo "<td bgcolor='#F2F2EE' style='border:1px solid;border-color: white #F2F2EE #F2F2EE silver'>";
                        echo "<font class=fonte></font>";
    				  echo "</td>";
  			   	    echo "</tr>";

				    echo "<tr align=center valign=top>";
                        echo "<td>";
 						echo "<iframe id='iFrame_Resumo' class='Class_IFrame_Sem_Borda1' src='nota_branco_total.php' tabindex=-1>";
						echo "</iframe>";
					    echo "</td>";
                        echo "<td>";
 						echo "<iframe id='iFrame_Total_Mes' class='Class_IFrame_Sem_Borda1' src='nota_branco_total.php' tabindex=-1>";
						echo "</iframe>";
  				        echo "</td>";
  				    echo "</tr>";

				    echo "<tr align=center valign=center height=10>";
                      echo "<td bgcolor='#F2F2EE' style='border-top:1px solid;border-color: white #F2F2EE #F2F2EE silver'>";
                        echo "<font class=fonte></font>";
    				  echo "</td>";
                      echo "<td bgcolor='#F2F2EE' style='border:1px solid;border-color: white #F2F2EE #F2F2EE silver'>";
                        echo "<font class=fonte></font>";
    				  echo "</td>";
  			   	    echo "</tr>";

  			   	    echo "<tr align=center valign=top>";
                        echo "<td>";
 						echo "<iframe id='iFrame_Resumo' class='Class_IFrame_Sem_Borda1' src='nota_branco_total.php' tabindex=-1>";
						echo "</iframe>";
					    echo "</td>";
                        echo "<td>";
 						echo "<iframe id='iFrame_Total_Mes' class='Class_IFrame_Sem_Borda1' src='nota_branco_total.php' tabindex=-1>";
						echo "</iframe>";
  				        echo "</td>";
				    echo "</tr>";
			}
				    
				    $cClausula  = " select ";
                    $cClausula .= " sum(T117_Valor_Total) Total, ";
                    $cClausula .= " count(*) Boletos, ";
                    $cClausula .= " count(*) Qte_Notas ";
                    $cClausula .= " from T117 ";
                    $cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
                    $cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
                    $cClausula .= " where T117_C004_Id='$Empresa_Atual' ";
                    if (!empty($xConsulta_Clausula_Filtro))
                    {
                        $cClausula .= " and ".$xConsulta_Clausula_Filtro;
                    }
                    $T117 = mysql_query($cClausula);

                    $mT117=mysql_fetch_array($T117);

         echo "<tr align=center valign=center height=35>";
                      echo "<td bgcolor='#F2F2EE' valign=center colspan=3 style='border-bottom:1px solid;border-color: white #909090 #909090 white'>";
       			 	    echo "<table border=0 cellspacing=0 cellpadding=0>";
                          echo "<tr align=center valign=center height=35>";
                              echo "<td>";
                                 unset($Disabled);
                                if ($Tela<=1)
                                {
                                    $Disabled='disabled';
                                }
                                echo "<input type='button' $Disabled class='anterior' $Style onclick=\"location.replace('principal_total.php?Tela=1')\">";
            				  echo "</td>";
                              echo "<td>";
                        echo "<font class=fonte>&nbsp; &nbsp; Qte. &nbsp;&nbsp;</font>";
                        echo "<font class=fonte style='border:1px solid silver;background:white;padding:1px;font-size:18px;font-weight:bold'>&nbsp;".$mT117[Qte_Notas]."&nbsp;</font>";
                        echo "<font class=fonte>&nbsp; &nbsp; Valor total &nbsp;&nbsp;</font>";
                        echo "<font class=fonte style='border:1px solid silver;background:white;padding:1px;font-size:18px;font-weight:bold'>&nbsp;".number_format($mT117[Total],2,',','.')."&nbsp;</font>&nbsp;&nbsp;";
    				  echo "</td>";
    				  echo "<td>";
                                unset($Disabled);
                                if ($Tela>1)
                                {
                                    $Disabled='disabled';
                                }
                                echo "<input type='button' $Disabled class='proxima' $Style onclick=\"location.replace('principal_total.php?Tela=2')\">";
            				  echo "</td>";
                    echo "</tr>";

			echo "</table>";
		echo "</td>";
	echo "</tr>";
/*	echo "<tr>";
  	     echo "<td height=100% colspan=2>";

			echo "<table cellspacing=1 cellpadding=0 border=0 width='100%' height=100% bgcolor='#F2F2EE' style='border:1px solid;border-color: white #909090 #909090 white'>";
				echo "<tr align=left valign=center>";
				
					echo "<td align=right>";
						echo "<input type=button value='Atualizar' onclick=\"Atualizar_Iframes()\" tabindex=-1>";
					echo "</td>";

					echo "<td align=left width=100%>";
					echo "</td>";
				echo "</tr>";
			echo "</table>";
		echo "</td>";
	echo "</tr>"; */
echo "</table>";

echo "<div id='DIV_calendario' style='display:none;position:absolute;top:0;left:0;overflow:auto;border:2px solid;background:#EBE9E0;border-color: #D0D0D0 #B0B0B0 #B0B0B0 #D0D0D0 '>";
echo "</div>";
echo "<div id='DIV_formulario' style='display:none;position:absolute;top:0;left:0;overflow:auto;border:2px solid;background:#EBE9E0;border-color: #D0D0D0 #B0B0B0 #B0B0B0 #D0D0D0 '>";
echo "</div>";

echo "<iframe name='fme_gravar' id='fme_gravar' style='display:none' tabindex=-1></iframe>";

echo "<div id='DIV_prazos' style='overflow:auto;position: absolute;z-index:auto;clear:both;vertical-align:middle;padding:0px;display:inline;'>";
echo "</div>";

echo "<div id='DIV_vendedor' style='overflow:auto;position: absolute;z-index:auto;clear:both;vertical-align:middle;padding:0px;display:inline;'>";
echo "</div>";

function diasemana($data) {
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

	echo "$diasemana";
}

?>
