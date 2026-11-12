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

$cNomeTelaFiltro='carta_correcao';
include "../include/include_filtro.php";

$oSuperPesquisa = new classeSuperPesquisa();
$oSuperPesquisa -> iniciarFuncoesUnicas();

$nT007_Id = $_GET['T007_Id'];

if ($nT007_Id<=0) {
    echo "T007_Id nao foi informado";
    exit;
}

echo "<html>";
echo "<head>";
echo "<title>CC-e - CARTA DE CORREÇÃO ELETRÔNICA</title>";
echo "</head>";

echo "<body bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 ";
echo "bgcolor='#FFFFFF' ";
echo ">";


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
.Class_IFrame_Padrao {
   visibility:visible;
   border: 1px solid gray;
   width:99.4%;
   height:99.5%;
   padding : 0px;
}
</style>

<script src='../func/ajax.js'></script>
<script src='../func/funcoes_genericas.js'></script>
<script src='../func/jquery-1.6.1.js'></script>
<script>
//FUNCOES EM JAVASCRIPT

var zCount=0;
var cLink='';

var Total_Largura=screen.availWidth;
var Total_Altura=screen.availHeight;
var Intervalo_Parcelas=30;
var Tamanho_Total_Menu_Registro=50;

var Menu_T199_Id=0;

function Recarregar()
{
	location.replace('principal.php?T007_Id=<?php echo $nT007_Id; ?>');
}

var OpcaoFormularioCCe='H';
function abrirFormularioCCe(T199_Id,T007_Id,Operacao)
{
	$.post('formulario.php',{T199_Id:T199_Id,T007_Id:T007_Id,Operacao:Operacao},function(data) 
	{
		OpcaoFormularioCCe='H';
		$("#DIV_formulario").html(data);
		document.getElementById('DIV_transparente').style.display='inline';
		document.getElementById('DIV_transparente').style.zIndex = zCount++;
		centralizarDiv("DIV_formulario",700,250);
		ContaCaracter('T199_Correcao',1000);
		document.getElementById('DIV_formulario').style.zIndex = zCount++;
		$("#DIV_formulario").fadeIn(400);
		OpcaoFormularioCCe='V';						
		document.getElementById('T199_Correcao').focus();
	});         
}

function fecharFormularioCCe()
{
   document.getElementById('DIV_transparente').style.display='none';	
   OpcaoFormularioCCe='H';
   $("#DIV_formulario").fadeOut();
}

function abreTelaEnvioCCe(T199_Id)
{
	document.getElementById('DIV_transparente').style.display='inline';
	document.getElementById('DIV_transparente').style.zIndex = zCount++;
    cLink='../carta_correcao/enviar_cce.php?T199_Id='+T199_Id;
    document.getElementById('fme_transmissao').src=cLink;
    centralizarDiv('DIV_tela_envio',600,340);    
    document.getElementById('DIV_tela_envio').style.zIndex = zCount++;    
    document.getElementById('DIV_tela_envio').style.display = 'inline';
}

function fecharTelaEnvioCCe()
{	
	document.getElementById('DIV_transparente').style.display='none';
    document.getElementById('fme_transmissao').src='';
    document.getElementById('DIV_tela_envio').style.display='none';
}

function downloadXMLCCe(T199_Id)
{
	document.getElementById('fme_gravar').display='inline';
	document.getElementById('fme_gravar').src='../carta_correcao/download_xml.php?T199_Id='+T199_Id;
}

function imprimirCCe(T199_Id){
    nova_janela('impressao.php?T199_Id=' + encodeURIComponent(T199_Id),0,0,1000,600,'no','no','no','no','no','no','Carta de Correção Eletrônica');
}

</script>
<?php

$Titulo_Tela='CARTA CORREÇÃO';

echo "<div id='DIV_transparente' class='transparente'>";
echo "</div>";

echo "<div id='DIV_tela_envio' style='position:absolute;display:none;top:10px;left:10px;'>";
echo "<table bgcolor=white width='600' border='0' cellspacing=0 cellpadding=0> ";
    echo "<tr bgcolor=#DDD6C8>";
        echo "<td align=left height=20 background='../imagens/fundo_aba.gif'>";
            echo "<font class='fonte' ><big>Transmissão da Carta de Correção</big>";
        echo "</td>";
        echo "<td align=right background='../imagens/fundo_aba.gif' onclick=\"fecharTelaEnvioCCe()\">";
            echo "<img src='../imagens/gif/fechar.gif' style='border:0px;cursor:pointer;'>";
        echo "</td>";
    echo "</tr>";

    echo "<tr>";
        echo "<td height=5>";
        echo "</td>";
    echo "</tr>";

    echo "<tr>";
        echo" <td colspan=2 width='100%' height=320>";
            echo "<iframe id='fme_transmissao' style='border:0px;height:100%;width:100%;'></iframe>";
        echo "</td>";
    echo "</tr>";
echo "</table>";
echo "</div>";


echo "<table border=0 cellspacing=0 cellpadding=0 width=100% bgcolor=#F2F2EE height=100%>";

    if ($Nova_Janela!='S') {
	    echo "<tr align=left valign=top height=1%>";
			echo "<td>";
	           include '../menu/menu.php';
			echo "</td>";
		echo "</tr>";
	} else {
	    echo "<tr align=left valign=top height=1%>";
			echo "<td style='border-bottom:1px solid silver'>";
	           echo "<font class='fonte' style='font-size:18px;font-weight:bold'>CC-e Carta de Correção Eletrônica</font>";
			echo "</td>";
		echo "</tr>";
	}
	
	echo "<tr align=left valign=top height=1%>";
		echo "<td width=60% style='border-bottom:1px solid silver'>";
			echo "<table cellspacing=0 cellpadding=2 border=0 width='100%' height=100% bgcolor='#F2F2EE' style='border-top:1px solid white;'>";
				echo "<tr align=left valign=center>";
                    echo "<td align=left colspan=5>";
                        echo "<input type=button class=incluir value='Incluir' onclick=\"javascript:abrirFormularioCCe(0,$nT007_Id,'1');\">&nbsp;";
                    echo "</td>";
                    
                    echo "<td>";
                    $cClausula  = " select ";
                    $cClausula .= " D024_Nome_Empresa, ";
                    $cClausula .= " T007_Ambiente_NFe, ";
                    $cClausula .= " T007_Numero_Nota_Fiscal, ";
                    $cClausula .= " T007_Chave_Acesso_Nfe ";
                    $cClausula .= " from T007 ";
                    $cClausula .= " left join D024 on D024_Id=T007_D024_Id ";
                    $cClausula .= " where T007_Id='$nT007_Id' ";
                    $cSql_T007 = sql_query($cClausula);
                    $mT007 = mysql_fetch_array($cSql_T007);
                    
                    echo "<table border=0 cellspacing=2 cellpadding=2>";
                    echo "<tr>";
                    echo "<td class=fonte>";
                    echo "Nota Fiscal:";
                    echo "</td>";
                    echo "<td class=fonte>";
                    echo $mT007['T007_Numero_Nota_Fiscal'];
                    echo "</td>";
                    
                    echo "<td class=fonte width=100>";
                    echo "</td>";
                    
                    echo "<td class=fonte>";
                    echo "Ambiente:";
                    echo "</td>";
                    
                    echo "<td class=fonte>";
                    if ($mT007['T007_Ambiente_NFe']==1)
                    {
                        echo "<font color=green><b>Produção</b></font>";
                    }
                    else if ($mT007['T007_Ambiente_NFe']==2)
                    {
                        echo "<font color=red><b>Homologação</b></font>";
                    }
                    echo "</td>";
                    
                    echo "</tr>";
                    
                    echo "<tr>";
                    echo "<td class=fonte>";
                    echo "Cliente:";
                    echo "</td>";
                    echo "<td class=fonte>";
                    echo $mT007['D024_Nome_Empresa'];
                    echo "</td>";
                    
                    echo "<td class=fonte width=100>";
                    echo "</td>";
                    
                    
                    echo "<td class=fonte>";
                    echo "Chave de Acesso:";
                    echo "</td>";
                    echo "<td class=fonte>";
                    echo $mT007['T007_Chave_Acesso_Nfe'];
                    echo "</td>";
                    echo "</tr>";
                    
                    echo "</table>";                    
                    echo "</td>";
                    
   				echo "</tr>";
			echo "</table>";
		echo "</td>";
	echo "</tr>";

	echo "<tr align=left valign=top height=100%>";
		echo "<td colspan=2>";
			echo "<table cellspacing=0 cellpadding=1 border=0 width='100%' height=100% bgcolor='#F2F2EE'>";

   				    echo "<tr align=center valign=top>";
                      echo "<td colspan=3>";
 						echo "<iframe id='iFrame_Notas_Dia' class='Class_IFrame_Padrao' src='consulta_fme.php?T007_Id=$T007_Id' tabindex=-1>";
						echo "</iframe>";
    				  echo "</td>";
                    echo "</tr>";
    		echo "</table>";
		echo "</td>";
	echo "</tr>";
echo "</table>";

echo "<div id='DIV_transparente' class='transparente'>";
echo "</div>";

echo "<div id='DIV_formulario' style='display:none;position:absolute;top:0;left:0;overflow:auto;border:0px solid;background:#EBE9E0;border-color: #D0D0D0 #B0B0B0 #B0B0B0 #D0D0D0 '>";
echo "</div>";

echo "<iframe name='fme_gravar' id='fme_gravar' style='display:none' tabindex=-1></iframe>";

?>
