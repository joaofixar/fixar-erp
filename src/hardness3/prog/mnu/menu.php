<?php
namespace hardness;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style type="text/css">
a, a:hover, a:active, a:focus {
	outline:0; 
	direction:ltr;
}
 
.wrapper {
	position:relative; height:25px;
}
	
.mainmenu {
	position:absolute; 
	z-index:100;
	font-family:Verdana, Geneva, sans-serif;
	font-weight:normal;
	font-size:90%;
	line-height:25px;
	left:0px; 
	width:606px;
}

ul.menu {
	padding:0; 
	margin:0; 
	list-style:none; 
	width:100px; 
	overflow:hidden; 
	float:left; 
	margin-right:1px;
}

ul.menu a {
	background:#369;
	text-decoration:none; 
	color:#fff; 
	padding-left:5px;
}
 
ul.menu li.list {
	float:left;
	width:250px;
	margin:-32767px -125px 0px 0px;
}

ul.menu li.list a.category {
	position:relative;
	z-index:50;
	display:block;
	float:left;
	width:120px;
	margin-top:32767px;
	color:black;
	background:transparent;
}

ul.menu li.list a.category:hover,
ul.menu li.list a.category:focus,
ul.menu li.list a.category:active {
	margin-right:1px;
	background-repeat:no-repeat;
	background-position:left top;

}

ul.submenu {
	float:left; 
	padding:25px 0px 0px 0px; 
	margin:0; 
	list-style:none; 
	background-repeat:no-repeat;
	background-position:left top;
	margin:-25px 0px 0px 0px;
}
	
ul.submenu li a {
	float:left;
	width:120px;
	background:#369;
	clear:left;
	color:#fff;
}

ul.submenu a:hover,
ul.submenu a:focus,
ul.submenu a:active {
	background:#900;
	margin-right:1px;
	color:#fff;
}
</style>
</head>

<body>

<iframe name='fme_liberacao_credito' id='fme_liberacao_credito' width=100% valign=bottom style='visibility:visible;display:none;' scrolling=no frameborder=0 src='../analise_credito/verificar_mensagens.php'></iframe>
<iframe name='fme_pedidos_faturar' id='fme_pedidos_faturar' width=100% valign=bottom style='visibility:visible;display:none;' scrolling=no frameborder=0 src='../pre_pedido_novo/verificar_mensagens.php'></iframe>

<table class="menuTable" width=100%>
	<tr>
		<td colspan=20 >
			<table cellspacing='0' valign=top cellpadding='0' border='0'
				height=35px width='100%' bgcolor=#0E4A8E
				style='background-repeat: no-repeat; background-position: right top;'
				background='../img/barra1.png'>
				<tr>
					<td width=15 align=right valign=center>&nbsp;&nbsp;EMPRESA:&nbsp;<br>&nbsp;&nbsp;<small>USUÁRIO:</small>&nbsp;
					</td>
					<td align=left valign=center width=400>&nbsp;<?php echo "$mC004[C004_Id] - $mC004[C004_Nome_Abreviado] - $mC004[D020_Nome_Cidade]/$mC004[D018_UF]"; ?>
					<br>&nbsp;<b><small><?php echo "$mC007[C007_Id] - $mC007[C007_Primeiro_Nome] - $mC021[C021_Nome_Perfil]"; ?>
					<?php
					if ( $Usuario_Suporte=='S' ) {
						echo " - $Banco_Dados - $host";
					}
					?> </small> </b> 
					</td>
					<td align=left valign=center><?php echo $Titulo_Tela; ?>
					</td>
					<td align=right>Suporte técnico<br>(41) 3344-2938
					
					</td>
				</tr>
			</table>
		</td>
		<td height=1 rowspan=2 align=right width=184 bgcolor='#F2F2EE'
			style='background-repeat: no-repeat; background-position: right top; padding: 0px 0px 0px 0px'
			background='../img/barra2.png'><a
			href='http://www.hardness.com.br' target='new' tabindex=-1><img
				src='../img/logo_hardness5.png' border=0px> </a>&nbsp;</td>
	</tr>

	<tr>
	<?php
	if (count($mURL_Historico)>1)
	{
		?>
		<td align=center class=fonte width=35
			style='border-top: 1px solid white'>
			<table width=100% border=0 cellpadding=0 cellspacing=0 height=100%>
				<tr>
					<td align=center onClick='voltar();' style="cursor: pointer"><img
						src='../img/voltar.gif' border=0 padding=0
						title='VOLTAR A TELA ANTERIOR'>
					</td>
				</tr>
			</table>
		</td>
		<?php
	}
	else
	{
		?>
		<td align=center class=fonte width=10
			style='border-top: 1px solid white'></td>
		<?php 	 
    }
    ?>
	<td>
		<div class="wrapper">
		
		    <div class="mainmenu">
		        <ul class="menu">
		            <li class="list">
		                <a class="category" href="#about">Cadastros&nbsp;&nbsp;&raquo;</a>
		                <ul class="submenu">
		                    <li><a href="#about1">Clientes</a></li>
		                    <li><a href="#about2">Transportadoras</a></li>
		                    <li><a href="#about3">Representantes</a></li>
		                    <li><a href="#about4">About link 4</a></li>
		                    <li><a class="endlist" href="#about5">About link 5</a></li>
		                </ul>
		            </li>
		        </ul>
		    </div>
		</div>
     </td>		
  </tr>
</table>

<?php 

//include "../include/menu_historico_navegacao.inc";

function modulo($modulo,$titulo,$mOpcoes)
{
          $Sequencial++; 
    	  $cNome_Id="Div_".$Sequencial; 
          echo "
              <td>
                <table class='menuOpcoesTable'>
    			     <tr id='opcao_$cNome_Id'>
    			         <td onMouseOver=\"AcessoSubMenu('$cNome_Id','S')\"  onMouseOut=\"AcessoSubMenu('$cNome_Id','N')\" >
                             $titulo
                         </td>
                     </tr>
    			     <tr width=100%>
    			         <td align=center style='position:absolute;border-right:0px solid gray;border-bottom:0px solid gray' onMouseOver=\"AcessoSubMenu('$cNome_Id','S')\"  onMouseOut=\"AcessoSubMenu('$cNome_Id','N')\">
                            <table border=0 ID='$cNome_Id' cellpadding=0 cellspacing=0 valign='top' style='display:none;empty-cells: show; border-collapse: collapse;'>
                               <tr>
    			                 <td rowspan=2 colspan=2>
                                   <table border=0 cellpadding=4 cellspacing=0 bgcolor='#E1E6EC' valign='top' style='border:3px solid lightblue;border-right:3px solid lightblue;border-bottom:3px solid lightblue;'>";
                                    foreach ($mOpcoes as $array) 
                                    {
                                        echo opcao($array[0],$array[1],$array[2]);
                                    }   
	                                
           echo "
                                  </table>
                               </td>
                             </tr>
                         </table>
                      </td>
                  </tr>
                </table>
             </td> ";
}

function opcao($Texto,$Opcao,$Link)
{
   global $Usuario_Atual;
   global $Perfil_Usuario;
   global $Divisao_Menu;

   //-- VERIFICA SE O USUARIO TEM ACESSO A OPCAO DE CANCELAR PEDIDO PENDENTE
   $C030=mysql_query("select C030_Acesso_Liberado from $cBd_Configuracao.C030 where C030_C029_Id='$Opcao' and C030_Id_Perfil='$Perfil_Usuario'");
   $mC030=mysql_fetch_array($C030);
   if (($mC030[C030_Acesso_Liberado]=='S') or (($Opcao=='49' or $Opcao=='86' or $Opcao=='51') and ($Usuario_Atual=='63'))) {
       $retorno="<tr class=cor_opcao width=100%>";
         $retorno.="<td width=150 align=left>";
          if (($mC030[C030_Acesso_Liberado]=='S') or (($Opcao=='49' or $Opcao=='86' or $Opcao=='51') and ($Usuario_Atual=='63')))
          {
              $retorno.="<a href='$Link'>$Texto</a>";
          }
          else
          {
              $retorno.="$Texto";
          }
         $retorno.="</td>";
       $retorno.="</tr>";
       $Divisao_Menu=1;
   }
   return $retorno;
}

function opcao_grupo($Opcao)
{
   global $Usuario_Atual;
   global $Perfil_Usuario;
   global $Divisao_Menu;

   $retorno=true;
   $XOpcao=$Opcao+1000;
   //-- VERIFICA SE O USUARIO TEM ACESSO A OPCAO DE CANCELAR PEDIDO PENDENTE
   $C030=mysql_query("select C030_Acesso_Liberado from $cBd_Configuracao.C030 where C030_C029_Id='$XOpcao' and C030_Id_Perfil='$Perfil_Usuario'");
   if (mysql_num_rows($C030)>=1)
   {
      $retorno = false;
      $mC030=mysql_fetch_array($C030);
      if ($mC030[C030_Acesso_Liberado]=='S') {
         $retorno = true;
      }
   }
   $C030=mysql_query("select C030_Acesso_Liberado from $cBd_Configuracao.C030 left join C029 on C029_Id=C030_C029_Id where C029_Grupo='$Opcao' and C030_Id_Perfil='$Perfil_Usuario' and C030_Acesso_Liberado='S'");
   if (mysql_num_rows($C030)<=0)
   {
      $retorno = false;
   }
   return $retorno;
}

?>

<iframe name='fme_gravar' id='fme_gravar' style='top:0;left:0;display:none;'></iframe>