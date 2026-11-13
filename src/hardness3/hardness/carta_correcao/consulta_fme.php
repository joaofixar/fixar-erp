<?php

// Cria ou inicia uma sessão
include "../include/sessao.inc";

//--  Conexão com o banco de dados
include "../include/conexao.inc";

include "../func/funcoes_genericas.inc";

$nT007_Id = $_GET['T007_Id'];

echo "<link rel=stylesheet href='../css/geral.css' type='text/css'>";

?>
<link rel=stylesheet href="../classes/classe_tamanho_medio_firefox.css" type="text/css">
<script src='../inc/ajax.js'></script>
<script src='../inc/funcoes_geral.js'></script>
<script src='funcoes.js'></script>
<iframe name='fme_geral'  id='fme_geral'  src='' style='position:absolute;display:none;top:100;width:500;height:500;'></iframe>

<?php

$cClausula  = " SELECT ";
$cClausula .= " T199_Id, ";
$cClausula .= " T199_Data_Hora_Inclusao, ";
$cClausula .= " T199_Sequencial_Evento, ";
$cClausula .= " T199_Correcao, ";
$cClausula .= " T199_Flag_Enviado, ";
$cClausula .= " C007_Nome ";
$cClausula .= " from T199 ";
$cClausula .= " left join C007 on C007_Id=T199_C007_Id ";
$cClausula .= " left join T007 on T007_Id=T199_T007_Id ";
$cClausula .= " where T199_T007_Id='$nT007_Id' ";
$cClausula .= " order by T199_Sequencial_Evento ";
$cSql_Resultado = sql_query($cClausula);

?>
<body bgcolor=white bottommargin=0 topmargin=0 rightmargin=0 leftmargin=0 onLoad="parent.MostrarMensagem('none');" >
<?php 


    echo "<table border=0 cellspacing=0 cellpadding=4 bgcolor=#E0DFE3 style='border:1px solid gray;border-top:1px solid white;border-left:1px solid white;empty-cells:show;'>";
         echo "<tr>";
			echo "<td class='td_titulo' width=150 align=center>";
	   			echo "Inclusão";
			echo "</td>";
			echo "<td class='td_titulo' width=150 align=center>";
			    echo "Ação";
			echo "</td>";		
			echo "<td class='td_titulo' width=60 align=center>";
	   			echo "Sequencial";
			echo "</td>";
			echo "<td class='td_titulo' align=left>";
	   			echo "Correção";
			echo "</td>";
			echo "<td class='td_titulo' align=left>";
	   			echo "Retorno Sefaz";
			echo "</td>";
			echo "<td class='td_titulo' align=center>";
			    echo "Usuário";
			echo "</td>";			
		echo "</tr>";

		while($mSql=mysql_fetch_array($cSql_Resultado))
		{
            $cCor_Letra='black';
            if ($mSql['T199_Flag_Enviado']=='N')
            {
		        $cCor_Letra='red';
            }
		    
            $cClausula  = " select ";
            $cClausula .= " T200_Status, ";
            $cClausula .= " T200_Motivo ";            
            $cClausula .= " from T200 ";            
            $cClausula .= " where T200_T199_Id='$mSql[T199_Id]' ";
            $cClausula .= " order by T200_Id desc ";
            $cClausula .= " limit 1 ";
            $cSql_T200 = sql_query($cClausula);
            $mT200 = mysql_fetch_array($cSql_T200);            
                                                
            $xRetorno_Sefaz = $mT200['T200_Status'].' - '.$mT200['T200_Motivo'];
                        
           	echo "<tr id='L_$mSql[T199_Id]' class='tr_dados' bgcolor='#FFFFFF'  >";
                if ($mSql['T199_Flag_Enviado']=='N') {
             	    echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' align=center onClick=\"parent.abrirFormularioCCe('$mSql[T199_Id]','$nT007_Id','2')\">";
             	        echo troca_datahora_dma($mSql[T199_Data_Hora_Inclusao]);
             	    echo "</td>";
             	    echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' align=center>";             	    
             	        echo "<input type='button' value='Transmitir CCe...' class=botao style='width:120px;background:#80FF80' onclick=\"parent.abreTelaEnvioCCe('$mSql[T199_Id]')\"/>";
             	    echo "</td>";
             	    echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' align=center onClick=\"parent.abrirFormularioCCe('$mSql[T199_Id]','$nT007_Id','2')\">";
             	        echo $mSql[T199_Sequencial_Evento];
             	    echo "</td>";
             	    echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' title='$mSql[T199_Correcao]' align=left onClick=\"parent.abrirFormularioCCe('$mSql[T199_Id]','$nT007_Id','2')\">";
             	        echo substr($mSql[T199_Correcao],0,40);
             	    echo "</td>";
                  echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' align=left onClick=\"parent.abrirFormularioCCe('$mSql[T199_Id]','$nT007_Id','2')\">";
                      if (empty($xRetorno_Sefaz)) echo "&nbsp;";
                    echo $xRetorno_Sefaz;
                  echo "</td>";
                  echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' align=center onClick=\"parent.abrirFormularioCCe('$mSql[T199_Id]','$nT007_Id','2')\">";
                      if (empty($mSql[C007_Nome])) echo "&nbsp;";
                      echo $mSql[C007_Nome];
                  echo "</td>";
                } else {
                  echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' align=center onClick=\"parent.imprimirCCe('$mSql[T199_Id]')\">";
                      echo troca_datahora_dma($mSql[T199_Data_Hora_Inclusao]);
                  echo "</td>";
                  echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' align=center>";                  
                      echo "<input type='button' value='Download XML' class=botao style='width:120px;' onclick=\"parent.downloadXMLCCe('$mSql[T199_Id]')\"/>";
                  echo "</td>";
                  echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' align=center onClick=\"parent.imprimirCCe('$mSql[T199_Id]')\">";
                      echo $mSql[T199_Sequencial_Evento];
                  echo "</td>";
                  echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' title='$mSql[T199_Correcao]' align=left onClick=\"parent.imprimirCCe('$mSql[T199_Id]')\">";
                      echo substr($mSql[T199_Correcao],0,40);
                  echo "</td>";
                  echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' align=left onClick=\"parent.imprimirCCe('$mSql[T199_Id]')\">";
                      if (empty($xRetorno_Sefaz)) echo "&nbsp;";
                    echo $xRetorno_Sefaz;
                  echo "</td>";
                  echo "<td class='td_itens' height=23 style='cursor:pointer;color:$cCor_Letra;' align=center onClick=\"parent.imprimirCCe('$mSql[T199_Id]')\">";
                      if (empty($mSql[C007_Nome])) echo "&nbsp;";
                      echo $mSql[C007_Nome];
                  echo "</td>";
                }
	           	    
				
				
			echo "</tr>";

		}
	echo "</table>";


?>
