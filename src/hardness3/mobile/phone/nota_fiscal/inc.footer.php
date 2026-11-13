<?php
namespace hardness;

echo <<<EOT
	<div data-role="footer" data-position="fixed" style="font-size: 110%;">
		<h3 style="margin: 0.3em 40px 0.3em;">
		<span style="float:left;">
			<a href="nota_fiscalLista.php?statusNota_Fiscal=X" style="color:#FFF; text-decoration:none;">Todos&nbsp;&nbsp;</a>
			<a href="nota_fiscalLista.php?statusNota_Fiscal=N" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/white-dot.png">&nbsp;Emitida&nbsp;&nbsp;</a>
			<a href="nota_fiscalLista.php?statusNota_Fiscal=S" style="color:#FFF; text-decoration:none;"><img src="/hardness3/mobile/tablet/jquery.mobile-1.0.1/images/red-dot.png">&nbsp;Cancelada&nbsp;&nbsp;</a>
			
		</span>
EOT;
$tipoUsuario = ($C007['C007_Flag_Vendedor'] == 'D') ? 'Diretor' : "Vendedor";

echo <<<EOT
		<span style="float:right;">{$tipoUsuario}: {$C007['C007_Primeiro_Nome']}</span></h3> 
	</div> 
	    </body>
	</html>
EOT;
