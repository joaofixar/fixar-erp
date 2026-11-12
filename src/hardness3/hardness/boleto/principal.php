<?php

// Cria ou inicia uma sessão
include "../include/sessao.inc";

//--  Conexão com o banco de dados
include "../include/conexao.inc";

include "../func/funcoes_genericas.inc";

include "../class/super_pesquisa.class.php";

//--  Cria o historico de navegacao
include "../include/voltar.inc";

include "../class/calendario.php";
$oCalendario = new Calendario();

$C004=mysql_query("select C004_Casas_Decimais_Preco from C004 where C004_Id='$Empresa_Atual'");
$mC004=mysql_fetch_array($C004);

$oSuperPesquisa = new classeSuperPesquisa();
$oSuperPesquisa -> iniciarFuncoesUnicas();

$cNomeTelaFiltro='boleto';
include "../include/include_filtro.php";

echo "<html>";
echo "<head>";
echo "<title>BOLETO BANCÁRIO</title>";
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


if (empty($Data_Inicio))
{
   $Data_Inicio=hoje();
   $Data_Fim=hoje();
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

function GravarEdicaoCampo(Objeto,T117_Id) {
   if (Objeto.type='checkbox') {
      var Conteudo_Campo_Editado=Objeto.checked;
   } else {
      var Conteudo_Campo_Editado=Objeto.value;
   }
   var Campo_Editado=Objeto.name;
   parent.MostrarMensagem('inline');
   document.getElementById('fme_gravar').src='gravar_campo_editado.php?T117_Id='+T117_Id+'&Campo_Editado='+Campo_Editado+'&Conteudo_Campo_Editado='+Conteudo_Campo_Editado;
}

function Recarrega() {
    MostrarMensagem('inline');
    document.location.href="<?php echo $_SERVER['REQUEST_URI']; ?>";
}

function MostrarMensagem(Visivel)
{
    document.getElementById('divLoadingCenter').style.display=Visivel;
    document.getElementById('divLoadingCenter').style.zIndex = zCount++;
}

function Atualizar_Tela(Intervalo_Dias)
{
   MostrarMensagem('inline');
   var D024_Id=document.getElementById('D024_Id_Principal').value;
   var Data_Inicio=document.getElementById('Data_Inicio').value;
   var Data_Fim=document.getElementById('Data_Fim').value;
   document.location.replace('principal.php?D024_Id='+D024_Id+'&Data_Inicio='+Data_Inicio+'&Data_Fim='+Data_Fim);
}
function Iniciar()
{
   MostrarMensagem('inline');
   document.location.replace('principal.php?D024_Id=0');
}
function CentralizarDiv(Div,Largura,Altura) {
   var W = (Total_Largura/2)-(Largura/2);
   var H = (Total_Altura/2)-(Altura/2);
   document.getElementById(Div).style.top = H;
   document.getElementById(Div).style.left = W;
   document.getElementById(Div).style.width = Largura;
   document.getElementById(Div).style.height = Altura;
}
function imprimir()
{
    nova_janela('imprimir.php?D007_Id=<?php echo $D007_Id; ?>',0,0,820,600,'no','no','no','no','yes','yes',0);
}

function AbreFormularioRemessa()
{
    CentralizarDiv('DIV_formulario_remessa',500,150);
    document.getElementById('DIV_formulario_remessa').style.display='inline';
    var cLink="../remessa/formulario_gerar_remessa.php";
    carregar_ajax(cLink,'POST',false,'DIV_formulario_remessa');
}
function FecharFormularioRemessa()
{
    document.getElementById('DIV_formulario_remessa').style.display='none';
}

var OpcaoFormularioEmail='H';
function AbrirFormularioEmail(T117_Id)
{
   if (OpcaoFormularioEmail=='H')
   {
      document.getElementById('DIV_formulario_email').style.zIndex = zCount++;
      CentralizarDiv('DIV_formulario_email',740,300);
      cLink='../boleto/formulario_enviar_boleto_email.php?T117_Id='+T117_Id;
      carregar_ajax(cLink,'POST',false,'DIV_formulario_email');
      document.getElementById('DIV_formulario_email').style.display = 'inline';
      OpcaoFormularioEmail='V';
   }
}
function FecharFormularioEmail()
{
   if (OpcaoFormularioEmail=='V')
   {
      document.getElementById('DIV_formulario_email').style.display = 'none';
      OpcaoFormularioEmail='H';
   }
}

</script>
<?php

$Titulo_Tela='BOLETO BANCÁRIO';

echo "<table border=0 cellspacing=0 cellpadding=0 width=100% bgcolor=#F2F2EE height=100%>";

    echo "<tr align=left valign=top height=1%>";
		echo "<td>";
          include '../menu/menu.php';
		echo "</td>";
	echo "</tr>";

	echo "<tr align=left valign=top height=1%>";
		echo "<td style='border-bottom:1px solid silver'>";
			echo "<table cellspacing=2 cellpadding=2 border=0 width='100%' height=100% bgcolor='#F2F2EE' style='border-top:1px solid white;'>";
				echo "<tr align=left valign=center>";
					echo "<td align=center width=60>";
                        echo "<input type=button class=resumo value='Resumo' onclick=\"location.replace('principal_total.php')\">";
					echo "</td>";
					echo "<td align=center width=60>";
                        echo "<input type=button class=imprimir value='Imprimir' onclick=\"imprimir()\" tabindex=-1>";
					echo "</td>";
					echo "<td align=center width=60>";
                        echo "<input type=button class=incluir value='Incluir' onclick=\"location.replace('formulario_gerar_boleto.php')\">";
					echo "</td>";
					echo "<td align=center width=60>";
                        echo "<input type=button class=cabecalho value='Remessa' style='width:90px' onclick=\"AbreFormularioRemessa();\">";
					echo "</td>";
					echo "<td align=left>";
					echo "</td>";
				echo "</tr>";
			echo "</table>";
		echo "</td>";
	echo "</tr>";

	echo "<tr align=left valign=top height=1%>";
	  echo "<td width=60% style='border-top:1px solid white'>";

          //////////////////////////////////////////////////////////////////////////////////////////////////////////
          //-- FILTRO
          //////////////////////////////////////////////////////////////////////////////////////////////////////////

          //-- NOME DA TELA :
          //-- SERA CRIADO UM ARQUIVO PHP DENTRO DA PASTA DADOS_USUARIOS COMO O NOME QUE FOR DEFINIDO
          //-- SERVE PARA GUARDAR OS FILTROS E CONDICOES QUE O USUARIO ESCOLHER, PORTANTO,
          //-- PARA CADA FILTRO, O IDEIAL É QUE TENHA UM NOME DIFERENTE
          $cNomeTelaFiltro='boleto';

          //-- OPÇÃO DE FILTRO
          $mCampos[]="D024_Nome_Fantasia||D024_Nome_Empresa";
          $mCabecalho[]="Cliente";
          $mFormatacaoCampo[]=""; // COLOCAR DATA OU DATAHORA

          $mCampos[]="D007_Nome_Banco";
          $mCabecalho[]="Banco";
          $mFormatacaoCampo[]=""; // COLOCAR DATA OU DATAHORA

          $mCampos[]="T117_Data_Emissao";
          $mCabecalho[]="Emissão";
          $mFormatacaoCampo[]="DATA"; // COLOCAR DATA OU DATAHORA

          $mCampos[]="T117_Data_Vencimento";
          $mCabecalho[]="Vencimento";
          $mFormatacaoCampo[]="DATA"; // COLOCAR DATA OU DATAHORA

          $mCampos[]="T002_Data_Recebimento";
          $mCabecalho[]="Recebimento";
          $mFormatacaoCampo[]="DATA"; // COLOCAR DATA OU DATAHORA

          $mCampos[]="T117_Nosso_Numero";
          $mCabecalho[]="Nosso número";
          $mFormatacaoCampo[]=""; // COLOCAR DATA OU DATAHORA

          $mCampos[]="T117_Flag_Cancelado";
          $mCabecalho[]="Cancelado";
          $mFormatacaoCampo[]=""; // COLOCAR DATA OU DATAHORA

          include "../include/filtro1.php";

      echo "</td>";
	echo "</tr>";

	echo "<tr align=left valign=top height=100%>";
		echo "<td colspan=2>";
			echo "<table cellspacing=0 cellpadding=1 border=0 width='100%' height=100% bgcolor='#F2F2EE'>";
   				    echo "<tr align=center valign=top>";
                      echo "<td colspan=3>";
						echo "<iframe id='iFrame_Boletos_Dia' class='Class_IFrame_Padrao' style='width:99.5%' src='boletos_dia.php' tabindex=-1>";
						echo "</iframe>";
    				  echo "</td>";
                    echo "</tr>";

                    // Todos os pedidos da data recebida

                    $cClausula  = " select ";
                    $cClausula .= " sum(T117_Valor_Total) Total, ";
                    $cClausula .= " count(distinct T117_Id) Boletos ";
				    $cClausula .= " from T117 ";
				    $cClausula .= " left join D024 on D024_Id=T117_D024_Id ";
				    $cClausula .= " left join T002 on T002_T117_Id=T117_Id ";
				    $cClausula .= " left join D007 on D007_Id=T117_D007_Id ";
				    $cClausula .= " where T117_C004_Id='$Empresa_Atual' ";
				    if (!empty($xConsulta_Clausula_Filtro))
				    {
				       $cClausula .= " and ".$xConsulta_Clausula_Filtro;
				    }
                    $cClausula .= " and T117_Flag_Cancelado!='S' ";
                    $T117 = mysql_query($cClausula);
                    
                    $mT117=mysql_fetch_array($T117);

  				    echo "<tr align=center valign=center height=40>";
                      echo "<td bgcolor='#F2F2EE' valign=center colspan=3 style='border-bottom:1px solid;border-color: white #909090 #909090 white'>";
                        echo "<font class=fonte>&nbsp; &nbsp; Boletos emitidos &nbsp;&nbsp;</font>";
                        echo "<font class=fonte style='border:1px solid gray;background:white;padding:1px;font-size:18px;font-weight:bold'>&nbsp;".number_format($mT117[Boletos],0,',','.')."&nbsp;</font>";
                        echo "<font class=fonte>&nbsp; &nbsp; Valor total &nbsp;&nbsp;</font>";
                        echo "<font class=fonte style='border:1px solid gray;background:white;padding:1px;font-size:18px;font-weight:bold'>&nbsp;".number_format($mT117[Total],2,',','.')."&nbsp;</font>";
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

echo "<div id='DIV_formulario_remessa' style='display:none;position:absolute;top:0;left:0;overflow:auto;border:2px solid;background:#EBE9E0;border-color: #D0D0D0 #B0B0B0 #B0B0B0 #D0D0D0 '>";
echo "</div>";

echo "<div id='DIV_formulario' style='display:none;position:absolute;top:0;left:0;overflow:auto;border:2px solid;background:#EBE9E0;border-color: #D0D0D0 #B0B0B0 #B0B0B0 #D0D0D0 '>";
echo "</div>";

echo "<iframe name='fme_gravar' id='fme_gravar' style='display:none' tabindex=-1></iframe>";

echo "<div id='DIV_prazos' style='overflow:auto;position: absolute;z-index:auto;clear:both;vertical-align:middle;padding:0px;display:inline;'>";
echo "</div>";

echo "<div id='DIV_vendedor' style='overflow:auto;position: absolute;z-index:auto;clear:both;vertical-align:middle;padding:0px;display:inline;'>";
echo "</div>";

echo "<div id='DIV_formulario_email' style='display:none;position:absolute;top:0;left:0;overflow:auto;border:2px solid;background:#EBE9E0;border-color: #D0D0D0 #B0B0B0 #B0B0B0 #D0D0D0 '>";
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
