<?php
include "bibliotecas/pasta_usuario.php";

//-- Cor dos sinais no filtro
$mSinalCor['<>']="#000000";
$mSinalCor['>']="#000000";
$mSinalCor['>=']="#000000";
$mSinalCor['<']="#000000";
$mSinalCor['<=']="#000000";
$mSinalCor['%']="#000000";
$mSinalCor['!%']="#000000";
$mSinalCor['=']="#000000";

//-- Descricao (LABEL) do filtro
$mSinalLabel['<>']="Diferente de";
$mSinalLabel['>']="Maior que";
$mSinalLabel['>=']="Maior ou igual a";
$mSinalLabel['<']="Menor que";
$mSinalLabel['<=']="Menor ou igual a";
$mSinalLabel['%']="Contenha";
$mSinalLabel['!%']="N�o contenha";
$mSinalLabel['=']="Igual a";

?>
<script>

function ReloadTela()
{
    document.location.href="<?php echo $_SERVER['REQUEST_URI']; ?>";
}

function Abre_Menu_Filtro()
{
   if (document.getElementById('menufiltrosimples').style.display=='inline')
   {     
      document.getElementById('menufiltrosimples').style.display = 'none';      
      document.getElementById('menufiltro').style.display = 'inline';
      document.getElementById('menufiltro').style.zIndex = zCount++;
      poe_focus_filtro();
   }
   else
   {
      document.getElementById('menufiltro').style.display = 'none';
      document.getElementById('menufiltrosimples').style.display = 'inline';
      document.getElementById('cPesquisaSimples').focus();                  
   }
}

function Limpar_Campo_Filtro(i,tipo_campo)
{
     document.getElementById('Sinal_'+i).value='';
     document.getElementById('Botao_Sinal_'+i).value='';
     if (tipo_campo=='DATA')
     {
        document.getElementById('Conteudo_Filtro_'+i+'_Inicial').value='';
        document.getElementById('Conteudo_Filtro_'+i+'_Final').value='';
        document.getElementById('Conteudo_Filtro_'+i+'_Inicial').focus();
     }
     else
     {
        document.getElementById('Conteudo_Filtro_'+i).value='';
        document.getElementById('Conteudo_Filtro_'+i).focus();
     }
}
function Sinal_Padrao(i,label,evento) {

     var nome_sinal='Sinal_'+i;
     var nome_botao_sinal='Botao_Sinal_'+i;
     var conteudo_sinal=document.getElementById(nome_sinal).value;

     if (evento.keyCode==9) {
        return;
     }

     if (conteudo_sinal=='') {
        document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['%']; ?>";
        document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['%']; ?>";;
        document.getElementById(nome_botao_sinal).title = 'pesquisa por fragmento de informa��es no campo: ' + label;
        document.getElementById(nome_sinal).value = '%';
     }
}

function Sinal_Padrao_Data(i,label,evento) {
     var nome_sinal='Sinal_'+i;
     var nome_botao_sinal='Botao_Sinal_'+i;
     var conteudo_sinal=document.getElementById(nome_sinal).value;

     if (evento.keyCode==9) {
        return;
     }

     if (conteudo_sinal=='') {
        document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
        document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
        document.getElementById(nome_botao_sinal).title = label + ' igual a...';
        document.getElementById(nome_sinal).value = '=';
     }
}

function Sinal_Padrao_DataUnica(i,label,evento) {
     var nome_sinal='Sinal_'+i;
     var nome_botao_sinal='Botao_Sinal_'+i;
     var conteudo_sinal=document.getElementById(nome_sinal).value;

     if (evento.keyCode==9) {
        return;
     }

     if (conteudo_sinal=='') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['<']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['<']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' menor que...';
      document.getElementById(nome_sinal).value = '<';
     }
}

function Sinal_Padrao_Numero(i,label,evento,objeto) {
     var nome_sinal='Sinal_'+i;
     var nome_botao_sinal='Botao_Sinal_'+i;
     var conteudo_sinal=document.getElementById(nome_sinal).value;
     
     if (evento.keyCode==9) {
        return;
     }
     
     if (objeto.value!='')
     {
        if (conteudo_sinal=='') {
           document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
           document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
           document.getElementById(nome_botao_sinal).title = label + ' igual a...';
           document.getElementById(nome_sinal).value = '=';
        }
     }
     else
     {
        document.getElementById(nome_botao_sinal).style.color = "";
        document.getElementById(nome_botao_sinal).value = "";
        document.getElementById(nome_botao_sinal).title = '';
        document.getElementById(nome_sinal).value = '';
     }
}

function Mudar_Sinal_Data(i,label,campo) {
   var nome_sinal='Sinal_'+i;
   var nome_botao_sinal='Botao_Sinal_'+i;
   var conteudo_sinal=document.getElementById(nome_sinal).value;

   document.getElementById('Conteudo_Filtro_'+i+'_Inicial').focus();

   if (conteudo_sinal=='') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' igual a...';
      document.getElementById(nome_sinal).value = '=';
   } else if (conteudo_sinal=='=') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['<>']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['<>']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' diferente de...';
      document.getElementById(nome_sinal).value = '<>';
   } else if (conteudo_sinal=='<>') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' igual a...';
      document.getElementById(nome_sinal).value = '=';
   }
}

function Mudar_Sinal_DataUnica(i,label,campo) {
   var nome_sinal='Sinal_'+i;
   var nome_botao_sinal='Botao_Sinal_'+i;
   var conteudo_sinal=document.getElementById(nome_sinal).value;

   document.getElementById('Conteudo_Filtro_'+i+'_Inicial').focus();

   if (conteudo_sinal=='') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' igual a...';
      document.getElementById(nome_sinal).value = '=';
   } else if (conteudo_sinal=='=') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['<']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['<']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' menor que...';
      document.getElementById(nome_sinal).value = '<';
   } else if (conteudo_sinal=='<') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['>']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['>']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' maior que...';
      document.getElementById(nome_sinal).value = '>';
   } else if (conteudo_sinal=='>') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' igual a...';
      document.getElementById(nome_sinal).value = '=';
   }
}

function Mudar_Sinal(i,label,campo) {
   var nome_sinal='Sinal_'+i;
   var nome_botao_sinal='Botao_Sinal_'+i;
   var conteudo_sinal=document.getElementById(nome_sinal).value;
   document.getElementById('Conteudo_Filtro_'+i).focus();

   if (conteudo_sinal=='') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' igual a...';
      document.getElementById(nome_sinal).value = '=';
   } else if (conteudo_sinal=='=') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['<>']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['<>']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' diferente de...';
      document.getElementById(nome_sinal).value = '<>';
   } else if (conteudo_sinal=='<>') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['>']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['>']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' maior que...';
      document.getElementById(nome_sinal).value = '>';
   } else if (conteudo_sinal=='>') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['>=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['>=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' maior ou igual a...';
      document.getElementById(nome_sinal).value = '>=';
   } else if (conteudo_sinal=='>=') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['<']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['<']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' menor que...';
      document.getElementById(nome_sinal).value = '<';
   } else if (conteudo_sinal=='<') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['<=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['<=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' menor ou igual a...';
      document.getElementById(nome_sinal).value = '<=';
   } else if (conteudo_sinal=='<=') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['%']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['%']; ?>";
      document.getElementById(nome_botao_sinal).title = 'Pesquisa por fragmento de informa��es no campo: ' + label;
      document.getElementById(nome_sinal).value = '%';
   } else if (conteudo_sinal=='%') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['!%']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['!%']; ?>";
      document.getElementById(nome_botao_sinal).title = 'Pesquisa por fragmento de informa��es que nao existem no campo: ' + label;
      document.getElementById(nome_sinal).value = '!%';
   } else if (conteudo_sinal=='!%') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' igual a...';
      document.getElementById(nome_sinal).value = '=';
   }
}

function Mudar_Sinal_Numero(i,label,campo) {
   var nome_sinal='Sinal_'+i;
   var nome_botao_sinal='Botao_Sinal_'+i;
   var conteudo_sinal=document.getElementById(nome_sinal).value;
   document.getElementById('Conteudo_Filtro_'+i).focus();

   if (conteudo_sinal=='') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' igual a...';
      document.getElementById(nome_sinal).value = '=';
   } else if (conteudo_sinal=='=') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['>']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['>']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' maior que...';
      document.getElementById(nome_sinal).value = '>';
   } else if (conteudo_sinal=='>') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['>=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['>=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' maior ou igual a...';
      document.getElementById(nome_sinal).value = '>=';
   } else if (conteudo_sinal=='>=') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['<']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['<']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' menor que...';
      document.getElementById(nome_sinal).value = '<';
   } else if (conteudo_sinal=='<') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['<=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['<=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' menor ou igual a...';
      document.getElementById(nome_sinal).value = '<=';
   } else if (conteudo_sinal=='<=') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' igual a...';
      document.getElementById(nome_sinal).value = '=';
   }
}

function Mudar_Sinal_Combo(i,label,campo) {
   var nome_sinal='Sinal_'+i;
   var nome_botao_sinal='Botao_Sinal_'+i;
   var conteudo_sinal=document.getElementById(nome_sinal).value;
   document.getElementById('Conteudo_Filtro_'+i).focus();

   if (conteudo_sinal=='') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' igual a...';
      document.getElementById(nome_sinal).value = '=';
   } else if (conteudo_sinal=='=') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['<>']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['<>']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' diferente de...';
      document.getElementById(nome_sinal).value = '<>';
   } else if (conteudo_sinal=='<>') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' igual a...';
      document.getElementById(nome_sinal).value = '=';
   }
}

function Mudar_Sinal_Variavel(i,label,campo) {
   var nome_sinal='Sinal_'+i;
   var nome_botao_sinal='Botao_Sinal_'+i;
   var conteudo_sinal=document.getElementById(nome_sinal).value;
   document.getElementById('Conteudo_Filtro_'+i).focus();

   if (conteudo_sinal=='') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' igual a...';
      document.getElementById(nome_sinal).value = '=';
   } else if (conteudo_sinal=='=') {
      document.getElementById(nome_botao_sinal).style.color = "<?php echo $mSinalCor['=']; ?>";
      document.getElementById(nome_botao_sinal).value = "<?php echo $mSinalLabel['=']; ?>";
      document.getElementById(nome_botao_sinal).title = label + ' maior que...';
      document.getElementById(nome_sinal).value = '=';
   }
}

function Limpar_Filtro() {
//     poe_focus_filtro();
<?php
     for ($nI=0;$nI<count($mCampos);$nI++) {
        // SE EXISTIR CAMPOS DEFINIDOS COMO PRINCIPAL PARA O FILTRO,
        // MOSTRA OS CAMPOS QUE FORAM DEFINIDOS COMO PRINCIPAL, SENAO
        // MOSTRA OS CAMPOS QUE ESTAO DEFINIDOS PARA SEREM VISUALIZADOS NA CONSULTA
          If (($mFormatacaoCampo[$nI]=='DATA') or ($mFormatacaoCampo[$nI]=='DATAHORA')) {
             echo "document.getElementById('Conteudo_Filtro_$nI"."_Inicial').value = '';";
             echo "document.getElementById('Conteudo_Filtro_$nI"."_Final').value = '';";
          } Else If (($mFormatacaoCampo[$nI]=='DATAUNICA')) {
             echo "document.getElementById('Conteudo_Filtro_$nI"."_Inicial').value = '';";
          } Else {
             echo "document.getElementById('Conteudo_Filtro_$nI').value = '';";
          }
          echo "document.getElementById('Botao_Sinal_$nI').value = '';";
          echo "document.getElementById('Botao_Sinal_$nI').title = 'Clique aqui para selecionar esse campo para o filtro';";
          echo "document.getElementById('Sinal_$nI').value = '';";
     }
     ?>
}
</script>
<style>
.campo_filtro
{
border:1px solid silver;
font-size:12px;
fomt-family:arial,verdana;
</style>
<?php

echo "<iframe name='fme_filtro' id='fme_filtro' style='display:none;'></iframe>";

if (empty($cNomeTelaFiltro))
{
	echo "<p>N�o foi definida a variavel: cNomeTelaFiltro</p>";
}
else
{
	//-- $Diretorio_Usuario � definido no arquivo include/pasta_usuario.inc
	$cArquivoTelaFiltro=$Diretorio_Usuario."/".$cNomeTelaFiltro.".php";
	if (file_exists($cArquivoTelaFiltro))
	{
		include "$cArquivoTelaFiltro";
		$xConsulta_Conteudo_Filtro = (isset($xConsulta_Conteudo_Filtro)) ? $xConsulta_Conteudo_Filtro : false;
		$xConsulta_Sinal_Filtro = (isset($xConsulta_Sinal_Filtro)) ? $xConsulta_Sinal_Filtro : false;
		$mConteudo_Filtro=explode(',,',$xConsulta_Conteudo_Filtro);
		$mSinal=explode(',,',$xConsulta_Sinal_Filtro);
	}
}

$cCampos=implode(",,",$mCampos);
$cCampos=base64_encode($cCampos);

$cFormatacaoCampo=implode(",,",$mFormatacaoCampo);
$cFormatacaoCampo=base64_encode($cFormatacaoCampo);

$cCabecalho=implode(",,",$mCabecalho);
$cCabecalho=base64_encode($cCabecalho);

$xConsulta_Conteudo_Filtro = (isset($xConsulta_Conteudo_Filtro)) ? $xConsulta_Conteudo_Filtro : '';
$xFiltroAvancado=str_replace(array(" ","-",",","|","%","$","@"),"",$xConsulta_Conteudo_Filtro);

echo "<div id='menufiltrosimples' ";
echo " style='border: 0px solid silver;";
if (empty($xFiltroAvancado))
{
	echo "display:inline; ";	
}	
else 
{
	echo "display:none; ";				
}	
echo "'>";
	  echo "<form name='formFiltroSimples' id='formFiltroSimples' action='/hardness3/bibliotecas/filtroGridPesquisar.php' method='post' target='fme_filtro' style='border:0px;margin:0px;'>";

		echo "<input type='hidden' name='cConfigCampos' id='cConfigCampos' value='$cCampos'>";
        echo "<input type='hidden' name='cConfigFormatacaoCampo' id='cConfigFormatacaoCampo' value='$cFormatacaoCampo'>";        		
        echo "<input type='hidden' name='cConfigNomeTelaFiltro' id='cConfigNomeTelaFiltro' value='$cNomeTelaFiltro'>";	  
        // Parametros necessário para o sistema novo (09/2012)
        echo "<input type='hidden' name='divId' id='divId' value='{$g['divId']}'>";
        echo "<input type='hidden' name='pathRaiz' id='pathRaiz' value='{$confUsuario['pathRaiz']}'>";
        echo "<input type='hidden' name='r' id='r' value='{$g['r_']}'>";
        echo "<input type='hidden' name='divIdRoot' id='divIdRoot' value='{$g['divIdRoot']}'>";
        
	echo "<table border=0 cellpadding=3 cellspacing=0 background='../imagens/gif/fundo_opcoes_grid.gif' width=100%>";
	
	 echo "<tr>";
        
	  echo "<td class=fonte style='font-size:12px' width=470>";
  	  		$xConsulta_Conteudo_Filtro_Simples = (isset($xConsulta_Conteudo_Filtro_Simples)) ? $xConsulta_Conteudo_Filtro_Simples : '';
		 echo "&nbsp;Pesquisar: <input type='text' name='cPesquisaSimples' id='cPesquisaSimples' value='$xConsulta_Conteudo_Filtro_Simples' size=60 class='campo' style='zIndex:0;zCount:0;font-size:12px;font-weight:bold;padding:4px;border:1px solid silver;background:white'>";
		 echo "<script>document.getElementById('cPesquisaSimples').focus();
		               document.getElementById('cPesquisaSimples').select()</script>";
		 
	   echo "</td>";

	   echo "<td width=10>";
		 echo "<input type='submit' name='cBotaoAvancado' id='cBotaoAvancado' value='Pesquisar' class='botao'>";
	   echo "</td>";
	     
	   echo "<td width=20>";
	   echo "</td>";  
	   
	   echo "<td align=right width=10>";
		 echo "<input type='button' name='cBotaoAvancado' id='cBotaoAvancado' value='Filtro Avançado' class='filtrar_avancado' style='width:110px' onclick='Abre_Menu_Filtro()'>";
	   echo "</td>";  
	   echo "<td></td>";
	 echo "</tr>";
	echo "</table>";
  echo "</form>";
	
echo "</div>";
  
echo "<div id='menufiltro' ";
echo " style='border: 0px solid silver;";
if (empty($xFiltroAvancado))
{
	echo "display:none; ";
}	
else 
{
	echo "display:inline; ";	
}	
echo "'>";

if (count($mCampos)<=8)
{
	$fundo='../imagens/gif/fundo_opcoes_grid_pq.gif';
}
else if (count($mCampos)<=11)
{
	$fundo='../imagens/gif/fundo_opcoes_grid_md.gif';
}
else
{
	$fundo='../imagens/gif/fundo_opcoes_grid_gd.gif';
	
}
echo ("<table border=0 width=100% cellpadding=0 cellspacing=0>");
 echo "<tr>";
  echo "<td>";
	echo ("<table border=0 cellpadding=0 cellspacing=0 width=100% background='$fundo' height=100%>");
		echo "<tr>";
//			echo "<td bgcolor=#E7E0DA style='border-right:1px solid #E4E4E4;padding:0px;border-top:2px solid white;border-left:2px solid white;border-bottom:2px solid silver'>";
//               echo "<img src='../imagens/gif/filtro.gif'>";
//			echo "</td>";
			echo "<td style='border-bottom:0px solid #E6E6E6;border-top:0px solid #F5F5F5' width=900 align=center  background='$fundo'>";
		    	echo "<table border=0 cellpadding=0 align=center height=100% cellspacing=3  background='$fundo'>";

					echo "<tr>";
						echo "<td>";
						
						   echo "<form name='formulario' id='formulario' action='executar_filtro.php' method='post' target='fme_filtro' style='border:0px;margin:0px;'>";

                             echo "<input type='hidden' name='cConfigURL' id='cConfigURL' value='$cURLFiltro'>";
                           
						     echo "<input type='hidden' name='cConfigCampos' id='cConfigCampos' value='$cCampos'>";
						     echo "<input type='hidden' name='cConfigCabecalho' id='cConfigCabecalho' value='$cCabecalho'>";
						     echo "<input type='hidden' name='cConfigFormatacaoCampo' id='cConfigFormatacaoCampo' value='$cFormatacaoCampo'>";
						     
						     echo "<input type='hidden' name='cConfigNomeTelaFiltro' id='cConfigNomeTelaFiltro' value='$cNomeTelaFiltro'>";

						    $nConta_Campos=0;
						    $nTem_Campo_Escondido=0;
						    $CriuFocus=FALSE;
						    $Quebralinha=TRUE;
						    
						    for ($nI=0;$nI<count($mCampos);$nI++) {
						    
                               if ($Quebralinha==TRUE)
                               {
                                  echo "<tr>";
                               }

						       if ($CriuFocus==FALSE) {
						          If ($nTotal_Principal_Campo_Filtro>0) {
						            If ($mPrincipal_Campo_Filtro[$nI]==1) {
						                echo "<script>";
						                echo "function poe_focus_filtro() {";
						                if (($mFormatacaoCampo[$nI]=='DATA') or ($mFormatacaoCampo[$nI]=='DATAHORA')) {
						                  echo "document.getElementById('Conteudo_Filtro_".$nI."_Inicial').focus();";
						                } else {
						                  echo "document.getElementById('Conteudo_Filtro_".$nI."').focus();";
						                }
						                echo "}";
						                echo "</script>";
						                $CriuFocus=TRUE;
						            }
						          } Else {
						                echo "<script>";
						                echo "function poe_focus_filtro() {";
						                if (($mFormatacaoCampo[$nI]=='DATA') or ($mFormatacaoCampo[$nI]=='DATAHORA')) {
						                    echo "document.getElementById('Conteudo_Filtro_".$nI."_Inicial').focus();";
						                } else {
						                    echo "document.getElementById('Conteudo_Filtro_".$nI."').focus();";
						                }
						                echo "}";
						                echo "</script>";
						                $CriuFocus=TRUE;
						          }
						       }


						       $cSinal=$mSinal[$nI];
						       $cCorCampoSelecionado='';
						       if (!empty($cSinal))
						       {
						       		$cCorCampoSelecionado="background-color : lightyellow; font-weight:bold;";       	
						       }
						       
						       
						       //--  TIRA OS <BR> DOS LABES
						       $mCabecalho[$nI]=str_replace("<BR>","",$mCabecalho[$nI]);
						       $mCabecalho[$nI]=str_replace("<br>","",$mCabecalho[$nI]);

						       $cNomeLinhaFiltro="Filtro_Linha_$nI";

						      //--  SE FOR UM CAMPO TIPO DATA, ABRIR� UM TEXT PARA DIGITAR O INICIO E FIM DA DATA DESEJADA

						      if ($mFormatacaoCampo[$nI]=='DATA') {
						            $mDatas=explode("|",$mConteudo_Filtro[$nI]);
						            

						              echo "<td align=right>
						                      <font class=fonte>$mCabecalho[$nI]</font>
						                    </td>";
						              // -- SINAL PARA O CAMPO DATA
						              echo "<td align=center width='80'>";

                                     echo "<table border=0 cellpadding=0 cellspacing=0>";
                                      echo "<tr>";
                                      echo "<td>";


						                If ($cSinal=='=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' igual a...';
						                } Else If ($cSinal=='<>') {
						                   $cMens_Sinal=$mCabecalho[$nI].' diferente de...';
						                } Else If ($cSinal=='') {
						                   $cMens_Sinal='Clique aqui para selecionar esse campo para o filtro';
						                }

						                echo "<input type='hidden' name='Sinal_$nI' id='Sinal_$nI' value='$cSinal'>";
						                echo "<input type='button' name='Botao_Sinal_$nI' id='Botao_Sinal_$nI' tabindex='-1' align=left ";
						                echo "title='$cMens_Sinal' ";
						                echo "value='$mSinalLabel[$cSinal]' ";
						                echo "onclick=\"Mudar_Sinal_Data('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\" style='border:1px solid silver;background-color: #f4f4f4; font-size: 10; font-family: arial; color:".$mSinalCor[$cSinal]."; height: 15px; width: 92px;'>";

						              echo "</td>";
						              echo "<td align=center width=10>";
	 								    echo "<img src='../imagens/gif/dropdown.gif'/ border=0 onclick=\"Mudar_Sinal_Data('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\">";
						              echo "</td>";

                                      echo "</tr>";
                                     echo "</table>";
   								    echo "</td>";

						              echo "<td align=left>";
                                           echo "<table cellspacing=0 cellpadding=0>";
                                              echo "<tr>";
						                          $nConta_Campos++;

                                                  echo "<td align=left>";
                                                     echo "<table border=0 cellpadding=0 cellspacing=0>";
                                                     	echo "<tr>";
                                                     		echo "<td>";
                                                               echo "<input type=text class='campo_filtro' style='font-weight:bold;$cCorCampoSelecionado' tabindex='$nConta_Campos' name='Conteudo_Filtro_".$nI."_Inicial' id='Conteudo_Filtro_".$nI."_Inicial' value='$mDatas[0]' size=6 class=campo onKeyPress=\"return(Formatar_Data('$nConta_Campos',this,event,'/'));\"  onKeyDown=\"Sinal_Padrao_Data('$nI','".$mCabecalho[$nI]."',event);\">";
                                            				echo "</td>";
                                                     		echo "<td>";
                                                     			$oCalendario->setNomeCampo("Conteudo_Filtro_".$nI."_Inicial");
                                            		 			$oCalendario->setNomeDiv("Div_Calendario_Data_Inicial_".$nI);
                           		 				 				$oCalendario->setExecutarFuncao("Sinal_Padrao_Data('$nI','',event);");
                                            	 	 			$oCalendario->AdicionarCalendario();
                                                     		echo "</td>";
                                                     	echo "</tr>";
                                                     echo "</table>";
                                                  echo "</td>";

                                                  echo "<td align=center width=25>";
						                               echo "<font class=fonte>at�</font>";
                                                  echo "</td>";

						                          $nConta_Campos++;
                                                  echo "<td align=left>";
                                                     echo "<table border=0 cellpadding=0 cellspacing=0>";
                                                     	echo "<tr>";
                                                     		echo "<td>";
                                                               echo "<input type=text class='campo_filtro' style='font-weight:bold;$cCorCampoSelecionado' tabindex='$nConta_Campos' name='Conteudo_Filtro_".$nI."_Final' id='Conteudo_Filtro_".$nI."_Final' value='$mDatas[1]' size=6 class=campo onKeyPress=\"return(Formatar_Data('$nConta_Campos',this,event,'/'));\"  onKeyDown=\"Sinal_Padrao_Data('$nI','".$mCabecalho[$nI]."',event);\">";
                                            				echo "</td>";
                                                     		echo "<td>";
                                                     			$oCalendario->setNomeCampo("Conteudo_Filtro_".$nI."_Final");
                                            		 			$oCalendario->setNomeDiv("Div_Calendario_Data_Final_".$nI); //$mCabecalho[$nI].
                           		 				 				$oCalendario->setExecutarFuncao("Sinal_Padrao_Data('$nI','',event);");
                                            	 	 			$oCalendario->AdicionarCalendario();
                                                     		echo "</td>";
                                                     	echo "</tr>";
                                                     echo "</table>";
                                                  echo "</td>";

                                              echo "</tr>";
                                           echo "</table>";
						              echo "</td>";

						              echo "<td align=left width=30>";
						                echo "<img src='../imagens/gif/borracha.gif' onClick=\"Limpar_Campo_Filtro('$nI','DATA');\" style='cursor:pointer;'>";
						              echo "</td>";

						      } else if ($mFormatacaoCampo[$nI]=='DATAUNICA') {

						            $mConteudo_Filtro[$nI];						            

						              echo "<td align=right>
						                      <font class=fonte>$mCabecalho[$nI]</font>
						                    </td>";
						              // -- SINAL PARA O CAMPO DATA
						              echo "<td align=center width='80'>";

                                     echo "<table border=0 cellpadding=0 cellspacing=0>";
                                      echo "<tr>";
                                      echo "<td>";


						                If ($cSinal=='=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' igual a...';
						                } Else If ($cSinal=='>') {
						                   $cMens_Sinal=$mCabecalho[$nI].' maior que...';
						                } Else If ($cSinal=='<') {
						                   $cMens_Sinal=$mCabecalho[$nI].' menor que...';
						                } Else If ($cSinal=='') {
						                   $cMens_Sinal='Clique aqui para selecionar esse campo para o filtro';
						                }

						                echo "<input type='hidden' name='Sinal_$nI' id='Sinal_$nI' value='$cSinal'>";
						                echo "<input type='button' name='Botao_Sinal_$nI' id='Botao_Sinal_$nI' tabindex='-1' align=left ";
						                echo "title='$cMens_Sinal' ";
						                echo "value='$mSinalLabel[$cSinal]' ";
						                echo "onclick=\"Mudar_Sinal_DataUnica('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\" style='border:1px solid silver;background-color: #f4f4f4; font-size: 10; font-family: arial; color:".$mSinalCor[$cSinal]."; height: 15px; width: 92px;'>";

						              echo "</td>";
						              echo "<td align=center width=10>";
	 								    echo "<img src='../imagens/gif/dropdown.gif'/ border=0 onclick=\"Mudar_Sinal_DataUnica('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\">";
						              echo "</td>";

                                      echo "</tr>";
                                     echo "</table>";
   								    echo "</td>";

						              echo "<td align=left>";
                                           echo "<table cellspacing=0 cellpadding=0>";
                                              echo "<tr>";
						                          $nConta_Campos++;

                                                  echo "<td align=left>";
                                                     echo "<table border=0 cellpadding=0 cellspacing=0>";
                                                     	echo "<tr>";
                                                     		echo "<td>";
                                                               echo "<input type=text class='campo_filtro' style='font-weight:bold;$cCorCampoSelecionado' tabindex='$nConta_Campos' name='Conteudo_Filtro_".$nI."_Inicial' id='Conteudo_Filtro_".$nI."_Inicial' value='$mConteudo_Filtro[$nI]' size=9 class=campo onKeyPress=\"return(Formatar_Data('$nConta_Campos',this,event,'/'));\"  onKeyDown=\"Sinal_Padrao_DataUnica('$nI','".$mCabecalho[$nI]."',event);\">";
                                            				echo "</td>";
                                                     		echo "<td>";
                                                     			$oCalendario->setNomeCampo("Conteudo_Filtro_".$nI."_Inicial");
                                            		 			$oCalendario->setNomeDiv("Div_Calendario_Data_Inicial_".$nI);
                           		 				 				$oCalendario->setExecutarFuncao("Sinal_Padrao_DataUnica('$nI','',event);");
                                            	 	 			$oCalendario->AdicionarCalendario();
                                                     		echo "</td>";
                                                     	echo "</tr>";
                                                     echo "</table>";
                                                  echo "</td>";

                                              echo "</tr>";
                                           echo "</table>";
						              echo "</td>";

						              echo "<td align=left width=30>";
						                echo "<img src='../imagens/gif/borracha.gif' onClick=\"Limpar_Campo_Filtro('$nI','DATA');\" style='cursor:pointer;'>";
						              echo "</td>";

						       } else if ($mFormatacaoCampo[$nI]=='DATAHORA') {

						            $mDatas=explode("|",$mConteudo_Filtro[$nI]);

						              echo "<td align=right>
						                      <font class=fonte>$mCabecalho[$nI]</font>
						                    </td>";

						              // -- SINAL PARA O CAMPO DATA-HORA
						              echo "<td align=center width=80>";

                                      echo "<table border=0 cellpadding=0 cellspacing=0>";
                                      echo "<tr>";
                                      echo "<td>";

						                If ($cSinal=='=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' igual a...';
						                } Else If ($cSinal=='<>') {
						                   $cMens_Sinal=$mCabecalho[$nI].' diferente de...';
						                } Else If ($cSinal=='') {
						                   $cMens_Sinal='Clique aqui para selecionar esse campo para o filtro';
						                }

						                echo "<input type='hidden' name='Sinal_$nI' id='Sinal_$nI' value='$cSinal'>";
						                echo "<input type='button' name='Botao_Sinal_$nI' id='Botao_Sinal_$nI' tabindex='-1' align=left ";
						                echo "title='$cMens_Sinal' ";
						                echo "value='$mSinalLabel[$cSinal]' ";
						                echo "onclick=\"Mudar_Sinal_Data('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\" style='border:1px solid silver;background-color: #f4f4f4; font-size: 10; font-family: arial; color:".$mSinalCor[$cSinal]."; height: 15px; width: 92px;'>";

						              echo "</td>";
						              echo "<td align=center width=10>";
	 								    echo "<img src='../imagens/gif/dropdown.gif'/ border=0 onclick=\"Mudar_Sinal_Data('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\">";
						              echo "</td>";

                                      echo "</tr>";
                                     echo "</table>";
   								    echo "</td>";

						              echo "<td align=left>";

						                echo "<input type=text class='campo_filtro' style='font-weight:bold;$cCorCampoSelecionado' name='Conteudo_Filtro_".$nI."_Inicial' id='Conteudo_Filtro_".$nI."_Inicial' value='$mDatas[0]' size=16 class=campo onKeyPress=\"return(Formatar_Data_Hora('$nConta_Campos',this,event,'/'));\" onKeyDown=\"Sinal_Padrao_Data('$nI','".$mCabecalho[$nI]."',event);\">";
						                $nConta_Campos++;
						                echo "<font class=fonte>&nbsp;at�&nbsp;&nbsp;</font>";

						                echo "<input type=text class='campo_filtro' style='font-weight:bold;$cCorCampoSelecionado' name='Conteudo_Filtro_".$nI."_Final' id='Conteudo_Filtro_".$nI."_Final' value='$mDatas[1]' size=16 class=campo onKeyPress=\"return(Formatar_Data_Hora('$nConta_Campos',this,event,'/'));\"  onKeyDown=\"Sinal_Padrao_Data('$nI','".$mCabecalho[$nI]."',event);\">";
						                $nConta_Campos++;

						              echo "</td>";

						              echo "<td align=left width=30>";
						                echo "<img src='../imagens/gif/borracha.gif' onClick=\"Limpar_Campo_Filtro('$nI','DATA');\" style='cursor:pointer;'>";
						              echo "</td>";

                               } else if (($mFormatacaoCampo[$nI]=='COMBO') or ((!empty($mConteudoCombo[$nI])) and ($mConteudoCombo[$nI]!='N'))){
                               
						            $mCabecalho[$nI]=stripslashes($mCabecalho[$nI]);
						            
						              echo "<td align=right>";
						                echo "<font class=fonte>$mCabecalho[$nI]&nbsp;</font>";
						              echo "</td>";

						              // -- SINAL
						              echo "<td align=center width=80>";

                                      echo "<table border=0 cellpadding=0 cellspacing=0>";
                                      echo "<tr>";
                                      echo "<td>";

						                If ($cSinal=='=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' igual a...';
						                } Else If ($cSinal=='<>') {
						                   $cMens_Sinal=$mCabecalho[$nI].' diferente de...';
						                } Else If ($cSinal=='>') {
						                   $cMens_Sinal=$mCabecalho[$nI].' maior que...';
						                } Else If ($cSinal=='>=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' maior ou igual a...';
						                } Else If ($cSinal=='<') {
						                   $cMens_Sinal=$mCabecalho[$nI].' menor que...';
						                } Else If ($cSinal=='<=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' menor ou igual a...';
						                } Else If ($cSinal=='%') {
						                   $cMens_Sinal='Pesquisa por fragmento de informa��es no campo: '.$mCabecalho[$nI];
						                } Else If ($cSinal=='!%') {
						                   $cMens_Sinal='Pesquisa por fragmento de informa��es que nao existem no campo: '.$mCabecalho[$nI];
						                } Else If ($cSinal=='') {
						                   $cMens_Sinal='Clique aqui para selecionar esse campo para o filtro';
						                }
						                echo "<input type='hidden' name='Sinal_$nI' id='Sinal_$nI' value='$cSinal'>";
						                echo "<input type='button' name='Botao_Sinal_$nI' id='Botao_Sinal_$nI' tabindex='-1' align=left ";
						                echo "title='$cMens_Sinal' ";
						                echo "value='$mSinalLabel[$cSinal]' ";
						                echo "onclick=\"Mudar_Sinal_Combo('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\" style='border:1px solid silver;background-color: #f4f4f4; font-size: 10; font-family: arial; color:".$mSinalCor[$cSinal]."; height: 15px; width: 92px;'>";
						              echo "</td>";
						              echo "<td align=center width=10>";
	 								    echo "<img src='../imagens/gif/dropdown.gif'/ border=0 onclick=\"Mudar_Sinal_Combo('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\">";
						              echo "</td>";

                                      echo "</tr>";
                                     echo "</table>";
   								    echo "</td>";

                                    if (empty($mConteudoCombo[$nI]))
                                    {
						              echo "<td align=left colspan=3>";
						                echo "<font class=fonte>N�o foi definido matriz: $"."mConteudoCombo[]</font>";
						              echo "</td>";
                                    }
                                    else
                                    {

						              echo "<td align=left>";
						                $cComando='echo "<select class=\"campo_filtro\" style=\'width:195;'.$cCorCampoSelecionado.'\' name=\"Conteudo_Filtro_'.$nI.'\" id=\"Conteudo_Filtro_'.$nI.'\" value=\"'.htmlspecialchars($mConteudo_Filtro[$nI]).'\"  onchange=\"Sinal_Padrao_Numero(\''.$nI.'\',\''.$mCabecalho[$nI].'\',event,this);\">";';
						                eval($cComando);
						                echo "<option value=''></option>";
						                $mDadosCombo=explode("||",$mConteudoCombo[$nI]);
						                for($nJ=0;$nJ<count($mDadosCombo);$nJ++)
						                {
                                           list($cId,$cOpcao)=explode(",,",$mDadosCombo[$nJ]);
						                   echo "<option value='$cId' ";
						                   if ($mConteudo_Filtro[$nI]==$cId)
						                   {
                                              echo " selected ";
						                   }
                                           echo ">$cOpcao</option>";
						                }
						                echo "</select>";
						                $nConta_Campos++;
						              echo "</td>";
						              echo "<td align=left width=30>";
						                echo "<img src='../imagens/gif/borracha.gif' onClick=\"Limpar_Campo_Filtro('$nI','TEXTO');\" style='cursor:pointer;'>";
						              echo "</td>";
                                     }

                               } else if ($mFormatacaoCampo[$nI]=='NUMERO') {

						            $mCabecalho[$nI]=stripslashes($mCabecalho[$nI]);

						              echo "<td align=right>";
						                echo "<font class=fonte>$mCabecalho[$nI]&nbsp;</font>";
						              echo "</td>";

						              // -- SINAL
						              echo "<td align=center width=80>";

                                      echo "<table border=0 cellpadding=0 cellspacing=0>";
                                      echo "<tr>";
                                      echo "<td>";

						                If ($cSinal=='=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' igual a...';
						                } Else If ($cSinal=='>') {
						                   $cMens_Sinal=$mCabecalho[$nI].' maior que...';
						                } Else If ($cSinal=='>=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' maior ou igual a...';
						                } Else If ($cSinal=='<') {
						                   $cMens_Sinal=$mCabecalho[$nI].' menor que...';
						                } Else If ($cSinal=='<=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' menor ou igual a...';
						                } Else If ($cSinal=='') {
						                   $cMens_Sinal='Clique aqui para selecionar esse campo para o filtro';
						                }

						                echo "<input type='hidden' name='Sinal_$nI' id='Sinal_$nI' value='$cSinal'>";
						                echo "<input type='button' name='Botao_Sinal_$nI' id='Botao_Sinal_$nI' tabindex='-1' align=left ";
						                echo "title='$cMens_Sinal' ";
						                echo "value='$mSinalLabel[$cSinal]' ";
						                echo "onclick=\"Mudar_Sinal_Numero('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\" style='border:1px solid silver;background-color: #f4f4f4; font-size: 10; font-family: arial; color:".$mSinalCor[$cSinal]."; height: 15px; width: 92px;'>";
						              echo "</td>";
						              echo "<td align=center width=10>";
	 								    echo "<img src='../imagens/gif/dropdown.gif'/ border=0 onclick=\"Mudar_Sinal_Numero('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\">";
						              echo "</td>";
                                      echo "</tr>";
                                     echo "</table>";
   								    echo "</td>";

						              echo "<td align=left>";
						                $cComando='echo "<input type=text size=31 style=\'width:195;'.$cCorCampoSelecionado.'\' class=\"campo_filtro\" name=\"Conteudo_Filtro_'.$nI.'\" id=\"Conteudo_Filtro_'.$nI.'\" value=\"'.htmlspecialchars($mConteudo_Filtro[$nI]).'\"  onKeyPress=\"Sinal_Padrao_Numero(\''.$nI.'\',\''.$mCabecalho[$nI].'\',event,this);\">";'; //onKeyPress=\"Skip_Campo(\''.$nConta_Campos.'\',event);\">";';
						                eval($cComando);
						                $nConta_Campos++;
						              echo "</td>";
						              echo "<td align=left width=30>";
						                echo "<img src='../imagens/gif/borracha.gif' onClick=\"Limpar_Campo_Filtro('$nI','TEXTO');\" style='cursor:pointer;'>";
						              echo "</td>";


                               } else if ($mFormatacaoCampo[$nI]=='VARIAVEL') {

						            $mCabecalho[$nI]=stripslashes($mCabecalho[$nI]);

						              echo "<td align=right>";
						                echo "<font class=fonte>$mCabecalho[$nI]&nbsp;</font>";
						              echo "</td>";

						              // -- SINAL
						              echo "<td align=center width=80>";

                                      echo "<table border=0 cellpadding=0 cellspacing=0>";
                                      echo "<tr>";
                                      echo "<td>";

						                If ($cSinal=='=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' igual a...';
						                } Else If ($cSinal=='') {
						                   $cMens_Sinal='Clique aqui para selecionar esse campo para o filtro';
						                }

						                echo "<input type='hidden' name='Sinal_$nI' id='Sinal_$nI' value='$cSinal'>";
						                echo "<input type='button' name='Botao_Sinal_$nI' id='Botao_Sinal_$nI' tabindex='-1' align=left ";
						                echo "title='$cMens_Sinal' ";
						                echo "value='$mSinalLabel[$cSinal]' ";
						                echo "onclick=\"Mudar_Sinal_Variavel('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\" style='border:1px solid silver;background-color: #f4f4f4; font-size: 10; font-family: arial; color:".$mSinalCor[$cSinal]."; height: 15px; width: 92px;'>";
						              echo "</td>";
						              echo "<td align=center width=10>";
	 								    echo "<img src='../imagens/gif/dropdown.gif'/ border=0 onclick=\"Mudar_Sinal_Variavel('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\">";
						              echo "</td>";
                                      echo "</tr>";
                                     echo "</table>";
   								    echo "</td>";

						              echo "<td align=left>";
						                $cComando='echo "<input type=text size=31 style=\'width:195;'.$cCorCampoSelecionado.'\' class=\"campo_filtro\" name=\"Conteudo_Filtro_'.$nI.'\" id=\"Conteudo_Filtro_'.$nI.'\" value=\"'.htmlspecialchars($mConteudo_Filtro[$nI]).'\"  onKeyPress=\"Sinal_Padrao_Numero(\''.$nI.'\',\''.$mCabecalho[$nI].'\',event,this);\">";'; //onKeyPress=\"Skip_Campo(\''.$nConta_Campos.'\',event);\">";';
						                eval($cComando);
						                $nConta_Campos++;
						              echo "</td>";
						              echo "<td align=left width=30>";
						                echo "<img src='../imagens/gif/borracha.gif' onClick=\"Limpar_Campo_Filtro('$nI','TEXTO');\" style='cursor:pointer;'>";
						              echo "</td>";


						       } else {

						            $mCabecalho[$nI]=stripslashes($mCabecalho[$nI]);

						              echo "<td align=right>";
						                echo "<font class=fonte>$mCabecalho[$nI]&nbsp;</font>";
						              echo "</td>";

						              // -- SINAL
						              echo "<td align=center width=80>";

                                      echo "<table border=0 cellpadding=0 cellspacing=0>";
                                      echo "<tr>";
                                      echo "<td>";

						                If ($cSinal=='=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' igual a...';
						                } Else If ($cSinal=='<>') {
						                   $cMens_Sinal=$mCabecalho[$nI].' diferente de...';
						                } Else If ($cSinal=='>') {
						                   $cMens_Sinal=$mCabecalho[$nI].' maior que...';
						                } Else If ($cSinal=='>=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' maior ou igual a...';
						                } Else If ($cSinal=='<') {
						                   $cMens_Sinal=$mCabecalho[$nI].' menor que...';
						                } Else If ($cSinal=='<=') {
						                   $cMens_Sinal=$mCabecalho[$nI].' menor ou igual a...';
						                } Else If ($cSinal=='%') {
						                   $cMens_Sinal='Pesquisa por fragmento de informa��es no campo: '.$mCabecalho[$nI];
						                } Else If ($cSinal=='!%') {
						                   $cMens_Sinal='Pesquisa por fragmento de informa��es que nao existem no campo: '.$mCabecalho[$nI];
						                } Else If ($cSinal=='') {
						                   $cMens_Sinal='Clique aqui para selecionar esse campo para o filtro';
						                }
						                echo "<input type='hidden' name='Sinal_$nI' id='Sinal_$nI' value='$cSinal'>";
						                echo "<input type='button' name='Botao_Sinal_$nI' id='Botao_Sinal_$nI' tabindex='-1' align=left ";
						                echo "title='$cMens_Sinal' ";
						                echo "value='$mSinalLabel[$cSinal]' ";
						                echo "onclick=\"Mudar_Sinal('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\" style='border:1px solid silver;background-color: #f4f4f4; font-size: 10; font-family: arial; color:".$mSinalCor[$cSinal]."; height: 15px; width: 92px;'>";
						              echo "</td>";
						              echo "<td align=center width=10>";
	 								    echo "<img src='../imagens/gif/dropdown.gif'/ border=0 onclick=\"Mudar_Sinal('".$nI."','".$mCabecalho[$nI]."','$cCampo_Filtro');\">";
						              echo "</td>";
                                      echo "</tr>";
                                     echo "</table>";
   								    echo "</td>";

   								      								    
						              echo "<td align=left>";
						                $cComando='echo "<input type=text size=31 style=\'width:195;'.$cCorCampoSelecionado.'\' class=\"campo_filtro\" name=\"Conteudo_Filtro_'.$nI.'\" id=\"Conteudo_Filtro_'.$nI.'\" value=\"'.htmlspecialchars($mConteudo_Filtro[$nI]).'\"  onKeyPress=\"Sinal_Padrao(\''.$nI.'\',\''.$mCabecalho[$nI].'\',event);\">";'; //onKeyPress=\"Skip_Campo(\''.$nConta_Campos.'\',event);\">";';
						                eval($cComando);
						                $nConta_Campos++;
						              echo "</td>";
						              echo "<td align=left width=30>";
						                echo "<img src='../imagens/gif/borracha.gif' onClick=\"Limpar_Campo_Filtro('$nI','TEXTO');\" style='cursor:pointer;'>";
						              echo "</td>";


						       }

                               if ($Quebralinha==TRUE)
                               {
                                  $Quebralinha=FALSE;
                               }
                               else
                               {
                                  echo "</tr>";
                                  $Quebralinha=TRUE;
                               }

						    }
						    echo "</td>";
					    echo "</tr>";
					    echo "<tr>";
					      echo "<td height=100%>";
					      echo "</td>";
					    echo "</tr>";
     			    echo "</table>";
				 echo "</td>";
  	             echo "<td valign=bottom background='$fundo' width=50>";
  	                if ($Usuario_Atual=='63')
  	                {
			            echo "<input type=button value='Sql' onclick=\"javascript:nova_janela('../config/showcode.php?arquivo=$cNomeTelaFiltro',50,50,900,500,'N','N','S','N','S','S','Sql');\">";
  	                }
			        echo "<input type=button value='Fechar' onclick='javascript:Abre_Menu_Filtro();'><br>";
			        echo "<input type=button value='Limpar' onclick='javascript:Limpar_Filtro();'>";
			        echo "<input type=submit value='Filtrar'><br>";
			        echo "</td>";
  	             echo "<td valign=bottom background='$fundo'>";
  	                echo "&nbsp;";
 		         echo "</td>";
         echo "</tr>";
   	echo "</table>";
   echo "</td>";
 echo "</tr>";
echo "</table>";
echo "</form>";
echo "</div>";

?>
