<?php

// Cria ou inicia uma sessão
include "../include/sessao.inc";

$cDia_Corrente=date("d",time());
$cMes_Corrente=date("m",time());
$cAno_Corrente=date("Y",time());

$cData = $_GET[Data];
list($cDia_Selecionado,$cMes_Selecionado,$cAno_Selecionado)=explode("/",$cData);

if (empty($cDia_Selecionado)) $cDia_Selecionado=$cDia_Corrente;
if (empty($cMes_Selecionado)) $cMes_Selecionado=$cMes_Corrente;
if (empty($cAno_Selecionado)) $cAno_Selecionado=$cAno_Corrente;


$nUltimo_Dia_Mes=date("t", mktime(0, 0, 0, $cMes_Selecionado, 1, $cAno_Selecionado));
$cArquivo_Historico = '';
$cDiretorio = "../fechamento/balancete/";
$nMes_Selecionado=intval($cMes_Selecionado);

$mMes[1]='Janeiro';
$mMes[2]='Fevereiro';
$mMes[3]='Março';
$mMes[4]='Abril';
$mMes[5]='Maio';
$mMes[6]='Junho';
$mMes[7]='Julho';
$mMes[8]='Agosto';
$mMes[9]='Setembro';
$mMes[10]='Outubro';
$mMes[11]='Novembro';
$mMes[12]='Dezembro';


echo "<table border=0 bgcolor=white cellspacing=0 cellpadding=4 width=200>";

    $cMesAnterior=$cMes_Selecionado-1;
    $cAnoAnterior=$cAno_Selecionado;
    if ($cMesAnterior<1)
    {
        $cMesAnterior=12;
        $cAnoAnterior=$cAno_Selecionado-1;
    }
    
    $cMesPosterior=$cMes_Selecionado+1;
    $cAnoPosterior=$cAno_Selecionado;
    if ($cMesPosterior>12)
    {
        $cMesPosterior=1;
        $cAnoPosterior=$cAno_Selecionado+1;
    }

	echo "<tr bgcolor='#d66417'>";
		echo "<td align=right>";
			echo "<input type='button' class=botao_fechar value='<<' ";
     	    echo " onclick=\"AtualizaDivCalendario('".str_pad($cDia_Selecionado, 2, "0", STR_PAD_LEFT)."/".str_pad($cMesAnterior, 2, "0", STR_PAD_LEFT)."/$cAnoAnterior','$Campo_Data');\" style='cursor:pointer;' ";
            echo ">";
		echo "</td>";
		echo "<td align=left colspan=4>";
			echo "<font class='fonte' style='color:#FFFFFF'><b>".$mMes[$nMes_Selecionado]." / $cAno_Selecionado</b>";
		echo "</td>";
		echo "<td align=right>";
			echo "<input type='button' class=botao_fechar value='>>' ";
     	    echo " onclick=\"AtualizaDivCalendario('".str_pad($cDia_Selecionado, 2, "0", STR_PAD_LEFT)."/".str_pad($cMesPosterior, 2, "0", STR_PAD_LEFT)."/$cAnoPosterior','$Campo_Data');\" style='cursor:pointer;' ";
            echo ">";
		echo "</td>";
		echo "<td align=right>";
			echo "<input type='button' value='X' class=botao_fechar onclick='FechaDivCalendario()'>";
		echo "</td>";	
	echo "</tr>";
	echo "<tr bgcolor='lightyellow'>";
		echo "<td align=center class=fonte>";
			echo "<b>Seg</b>";
		echo "</td>";
		echo "<td align=center class=fonte>";
			echo "<b>Ter</b>";
		echo "</td>";
		echo "<td align=center class=fonte>";
			echo "<b>Qua</b>";
		echo "</td>";
		echo "<td align=center class=fonte>";
			echo "<b>Qui</b>";
		echo "</td>";
		echo "<td align=center class=fonte>";
			echo "<b>Sex</b>";
		echo "</td>";
		echo "<td align=center class=fonte>";
			echo "<b>Sáb</b>";
		echo "</td>";		
		echo "<td align=center class=fonte>";
			echo "<b>Dom</b>";
		echo "</td>";		
	echo "</tr>";
	
echo "<tr bgcolor='white'>";
$nColuna=1;

$nDia_Semana = date("N", mktime(0, 0, 0, $cMes_Selecionado, 1, $cAno_Selecionado));
for ($nDia=1;$nDia<=7;$nDia++)
{

		if ($nDia_Semana!=$nDia)
		{
			echo "<td align=right class=fonte>";
				echo "&nbsp;";
			echo "</td>";
		} 
		else 
		{
			break;			
		}
		$nColuna++;				

}
for ($nDia=1;$nDia<=$nUltimo_Dia_Mes;$nDia++)
{
		$cFonte='';


		if ($nColuna==1) echo "<tr bgcolor='white'>";
					
		$nDia_Semana = date("N", mktime(0, 0, 0, $cMes_Selecionado, $nDia, $cAno_Selecionado));
				
		echo "<td align=center ";
		
		if ($cAno_Selecionado==$cAno_Selecionado && $cMes_Selecionado==$cMes_Selecionado && $nDia==$cDia_Selecionado)
		{
			echo " bgcolor=red ";
			$cFonte="<font style='color:white;font-weight:bold;'>";
		}
		elseif ($cAno_Selecionado==$cAno_Corrente && $cMes_Selecionado==$cMes_Corrente && $nDia==$cDia_Corrente)
		{
			$cFonte="<font style='color:RoyalBlue;font-weight:bold;'>";
		}
		else if ($nDia_Semana==7)
		{
			echo " bgcolor='lightyellow' ";
		}

  	    echo " onclick=\"document.getElementById('$Campo_Data').value='".str_pad($nDia, 2, "0", STR_PAD_LEFT)."/".str_pad($nMes_Selecionado, 2, "0", STR_PAD_LEFT)."/$cAno_Selecionado';Atualizar_Tela();\" style='cursor:pointer;' ";
		
		echo "class=fonte>";
			echo $cFonte.$nDia."</font>";
		echo "</td>";
		
		$nColuna++;		
		if ($nColuna==8)
		{
			$nColuna=1;
			echo "</tr>";
		}

}
echo "</table>";


?>
