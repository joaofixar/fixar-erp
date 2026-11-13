<?php
namespace hardness;
include('inc.conf.php');

$T005_Data_Emissao = empty($T005['T005_Data_Emissao']) ? '' : gCorrigeData($T005['T005_Data_Emissao']);

echo <<<EOT
	<div data-role="footer" data-position="fixed" style="font-size: 110%;">
		<h3 style="margin: 0.3em 40px 0.3em;">
EOT;
if (!isset($confCaixaLista)) {
	echo <<<EOT
		<span style="float: left;">
		Pedido: {$T005_Id}&nbsp;&nbsp;&nbsp;&nbsp;Data: {$T005_Data_Emissao}&nbsp;&nbsp;&nbsp;&nbsp;Cliente: {$T005['D024_Nome_Empresa']}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		</span>
EOT;
}else{
	echo <<<EOT
		<span style="float:left;">
			<a href="caixaLista.php?statusServicos=X" style="color:#FFF; text-decoration:none;">Todos&nbsp;&nbsp;</a>
            <a href="caixaLista.php?statusServicos=1" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/white-dot.png">&nbsp;Pendente&nbsp;&nbsp;</a>
            <a href="caixaLista.php?statusServicos=R" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/blue-dot.png">&nbsp;Retornado&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
			<a href="caixaLista.php?statusServicos=3" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/red-dot.png">&nbsp;Cancelado&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
			<a href="caixaLista.php?statusServicos=2" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/gray-dot.png">&nbsp;Concluído&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
			<a href="caixaLista.php?statusServicos=0" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/orange-dot.png">&nbsp;Ítens Pendentes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>
		</span>
EOT;
}
$tipoUsuario = ($C007['C007_Flag_Vendedor'] == 'D') ? 'Diretor' : "Vendedor";

echo <<<EOT
		<span style="float:right;">{$tipoUsuario}: {$C007['C007_Primeiro_Nome']}</span></h3> 
	</div> 
	    </body>
	</html>
EOT;
