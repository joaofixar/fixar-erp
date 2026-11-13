<?php

include "sessao.php";
include "conexao.php";
include "tabelas_config.php";
include "funcoes.php";
include "voltar.php";

?>

<html>
<head>
<title>HARDNESS <?php echo $mC004[C004_Nome_Abreviado]; ?></title>
<script src='funcoes.js'></script>
<link rel=stylesheet href="estilos.css" type="text/css">
</head>

<body>

<?php

$Titulo_Tela='Início';

include "atualizabd.php";

?>

<iframe name='fme_pedidos_faturar' id='fme_pedidos_faturar' width=100% valign=bottom style='visibility:visible;display:none;' scrolling=no frameborder=0 src='../pre_pedido_novo/verificar_mensagens.php?Confirma=S'></iframe>

<table border=0 cellspacing=0 cellpadding=0 width=100% bgcolor=#F2F2EE height=100% style='empty-cells:show'>
   <tr align=left valign=top height=1%>
	  <td>
	  <?php 
          include '../mnu/menu.php';
        ?>  
	  </td>
   </tr>
   <tr align=center valign=center height=99% bgcolor='#F2F2EE'>
	 <td style='border-top:1px solid white'>
	 </td>
   </tr>
</table>

</body>
</html>
