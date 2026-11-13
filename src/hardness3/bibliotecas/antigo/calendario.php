<?php
namespace hardness;
/*

	Programador:	Evantoni Fuchs
	Obejtivo:       Criar um canlendário ao lado do campo data
	Data:			14/11/2009

*/


class Calendario
{
	public function setNomeCampo($x)
	{
		$this->nome_campo=$x;
	}

	public function setNomeDiv($x)
	{
		$this->nome_div=$x;
	}

	public function setDataSelecionada($x)
	{
		$this->data_selecionada=$x;
	}

	public function setExecutarFuncao($x)
	{
		$this->funcao=base64_encode($x);
	}

	function AdicionarCalendario()
	{
		echo "<table cellspacing=0 cellpadding=0>";
	    	echo "<tr>";
    			echo "<td>";
					echo "<a onclick=\"AbreDivCalendarioClasse(event,'".$this->nome_campo."','".$this->nome_div."','".$this->funcao."');\"  align=center style='cursor:pointer'>";
						echo "<img src='../imagens/gif/calendario.gif' border=0>";
			    	echo "</a>";
		    	echo "</td>";
	    	echo "</tr>";
	    	echo "<tr>";
    			echo "<td>";
    			    echo "<span>";
					echo "<div id='".$this->nome_div."' style='display:none; border-left-width: 2px;   border-right-width: 2px;   border-top-width : 2px;   border-bottom-width : 1px;   border-color : white;   border-top : 2px outset #D0D0D0;   border-right : 2px outset white;   border-bottom : 2px outset white;   border-left : 2px outset #D0D0D0; position:absolute;   z-index:auto;   clear:both;   vertical-align:middle;   padding:0px;'>";
					echo "</div>";
    			    echo "</span>";
		    	echo "</td>";
	    	echo "</tr>";
    	echo "</table>";

	}
	
	function CriarCalendario($cFuncaoCalendario)
	{

		$cDia_Corrente=date("d",time());
		$cMes_Corrente=date("m",time());
		$cAno_Corrente=date("Y",time());

		list($cDia_Selecionado,$cMes_Selecionado,$cAno_Selecionado)=explode("/",$this->data_selecionada);

		if (empty($cDia_Selecionado)) $cDia_Selecionado=$cDia_Corrente;
		if (empty($cMes_Selecionado)) $cMes_Selecionado=$cMes_Corrente;
		if (empty($cAno_Selecionado)) $cAno_Selecionado=$cAno_Corrente;


		$nUltimo_Dia_Mes=date("t", mktime(0, 0, 0, $cMes_Selecionado, 1, $cAno_Selecionado));
		$cArquivo_Historico = '';
		$nMes_Selecionado=intval($cMes_Selecionado);

		$mMes[1]='Janeiro';
		$mMes[2]='Fevereiro';
		$mMes[3]='Marco';
		$mMes[4]='Abril';
		$mMes[5]='Maio';
		$mMes[6]='Junho';
		$mMes[7]='Julho';
		$mMes[8]='Agosto';
		$mMes[9]='Setembro';
		$mMes[10]='Outubro';
		$mMes[11]='Novembro';
		$mMes[12]='Dezembro';



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
		echo "<table border=0 bgcolor=white cellspacing=0 cellpadding=1 width=200 height=15>";
			echo "<tr bgcolor='#d66417'>";
				echo "<td align=right width=20>";
                    echo "<input type='button' class=botao_fechar value='<<' ";
	     	    		echo " onclick=\"AtualizaDivCalendario('".str_pad($cDia_Selecionado, 2, "0", STR_PAD_LEFT)."/".str_pad($cMesAnterior, 2, "0", STR_PAD_LEFT)."/$cAnoAnterior','".$this->nome_campo."','".$this->nome_div."');\" style='cursor:pointer;' ";
		            echo "/>";
				echo "</td>";
				echo "<td align=center colspan=4>";
					echo "<font class='fonte' style='color:#FFFFFF;font-family:arial,verdana;font-size:11px'><b>".$mMes[$nMes_Selecionado]." / $cAno_Selecionado</b>";
				echo "</td>";
				echo "<td align=left width=20>";
  			    echo "<input type='button' class=botao_fechar value='>>' ";
	     		    echo " onclick=\"AtualizaDivCalendario('".str_pad($cDia_Selecionado, 2, "0", STR_PAD_LEFT)."/".str_pad($cMesPosterior, 2, "0", STR_PAD_LEFT)."/$cAnoPosterior','".$this->nome_campo."','".$this->nome_div."');\" style='cursor:pointer;' ";
	            	echo "/>";
				echo "</td>";
				echo "<td align=right>";
  			        echo "<input type='button' value='X' class=botao_fechar onclick=\"FechaDivCalendarioClasse('".$this->nome_div."')\">";
				echo "</td>";
			echo "</tr>";
		echo "</table>";
		
		echo "<table border=0 bgcolor=white cellspacing=0 cellpadding=4 width=200 height=150>";
			echo "<tr bgcolor='lightyellow' height=15>";
				echo "<td align=center style='font-family:arial,verdana;font-size:10px'>";
					echo "<b>Seg</b>";
				echo "</td>";
				echo "<td align=center style='font-family:arial,verdana;font-size:10px'>";
					echo "<b>Ter</b>";
				echo "</td>";
				echo "<td align=center style='font-family:arial,verdana;font-size:10px'>";
					echo "<b>Qua</b>";
				echo "</td>";
				echo "<td align=center style='font-family:arial,verdana;font-size:10px'>";
					echo "<b>Qui</b>";
				echo "</td>";
				echo "<td align=center style='font-family:arial,verdana;font-size:10px'>";
					echo "<b>Sex</b>";
				echo "</td>";
				echo "<td align=center style='font-family:arial,verdana;font-size:10px'>";
					echo "<b>Sab</b>";
				echo "</td>";
				echo "<td align=center style='font-family:arial,verdana;font-size:10px'>";
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
				echo "<td align=right style='font-family:arial,verdana;font-size:10px'>";
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


			if ($nColuna==1) echo "<tr bgcolor='white' >";

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

	  	    echo " onclick=\"document.getElementById('".$this->nome_campo."').value='".str_pad($nDia, 2, "0", STR_PAD_LEFT)."/".str_pad($nMes_Selecionado, 2, "0", STR_PAD_LEFT)."/$cAno_Selecionado';";
			if (!empty($cFuncaoCalendario))
			{
			    echo base64_decode($cFuncaoCalendario).";";
			}
            echo "FechaDivCalendarioClasse('".$this->nome_div."');\" style='cursor:pointer;font-family:arial,verdana;font-size:10px' ";

			echo " >";
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

	}
	

}

?>
