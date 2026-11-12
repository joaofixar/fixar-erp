<?php


// Cria ou inicia uma sessão
include "../include/sessao.inc";

//--  Conexão com o banco de dados
include "../include/conexao.inc";

include "../func/funcoes_genericas.inc";

$nT199_Id  = $_POST['T199_Id'];
$nT007_Id  = $_POST['T007_Id'];
$cOperacao = $_POST['Operacao'];

if ($cOperacao=='2')
{
    $cClausula  = " select ";
    $cClausula .= " T199_Correcao, ";
    $cClausula .= " T199_Flag_Enviado ";
    $cClausula .= " from T199 ";
    $cClausula .= " where T199_Id='$nT199_Id' ";
    $cSql_T199 = sql_query($cClausula);
    $mT199 = mysql_fetch_array($cSql_T199);
}

$nTabIndex=0;

if ($mT199['T199_Flag_Enviado']!='S')
{
    echo "<form name='formulario' id='formulario' method='post' target='fme_gravar' action='gravar_formulario.php'>";
    $cClassCampo='class="formulario_campo"';
}
else
{
    $cClassCampo='class="campo_desabilitado" disabled';
}
echo "<input type='hidden' name='Operacao' id='Operacao' value='$cOperacao'>";
echo "<input type='hidden' name='T199_Id' id='T199_Id' value='$nT199_Id'>";
echo "<input type='hidden' name='T007_Id' id='T007_Id' value='$nT007_Id'>";


echo "<table border=0 gcolor=#F2F2EE cellpadding=3 cellspacing=0 align=left width=100% style='border: 0px solid;border-color:  #f2f2ee #B0B0B0 #B0B0B0 #f2f2ee;'>";
	echo "<tr  bgcolor=#DDD6C8>";
		echo "<td align=left>";
			echo "<font class='fonte' style='font-size:11px;color:#404040;padding:2' ><big>Carta de Correção</big></font>";
		echo "</td>";
		echo "<td align=right onclick=\"fecharFormularioCCe();\">";
			echo "<img src='../imagens/gif/fechar.gif' style='border:0px;cursor:pointer;'>";
		echo "</td>";
	echo "</tr>";

	echo "<tr>";
		echo "<td height=10>";
		echo "</td>";
	echo "</tr>";

	echo "<tr width=100%>";
	    echo "<td align=center colspan=2>";
				echo "<table border=0 width=98% cellspacing=0 cellpadding=2>";
					echo "<tr>";
						echo "<td align=left>";
							echo "<font class='fonte'>Texto da Carta de Correção</font>";
						echo "</td>";
					echo "</tr>";
					echo "<tr>";
						echo "<td align=right>";
							$nTabIndex++;
							echo "<textarea name='T199_Correcao' id='T199_Correcao' cols='30' rows='7' $cClassCampo style='text-transform: uppercase;width:100%;' tabindex='$nTabIndex' onKeyUp=\"ContaCaracter('T199_Correcao',1000)\">".htmlspecialchars($mT199[T199_Correcao],ENT_QUOTES)."</textarea>";
						echo "</td>";
					echo "</tr>";
					echo "<tr>";
					echo "<td align=right>";
					echo "<div id='Total_T199_Correcao'>";
					echo "</div>";
					echo "</td>";
					echo "</tr>";					
				echo "</table>";

		echo "</td>";
	echo "</tr>";
	
	$nTabIndex++;
	echo "<tr>";
		echo "<td align=center colspan=3>";
		    if ($mT199['T199_Flag_Enviado']!='S')
		    {		
		        echo "<input type='submit' value='Gravar' tabindex='$nTabIndex' class='enviar'>&nbsp;";
		    }    
			echo "<input type='button' value='Cancelar' class=cancelar onclick=\"fecharFormularioCCe()\" >";
		echo "</td>";
	echo "</tr>";

echo "</table>";

echo "</form>";
?>
