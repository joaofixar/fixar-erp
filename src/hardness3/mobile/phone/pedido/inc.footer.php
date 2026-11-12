<?php
namespace hardness;
include('inc.conf.php');

$T005_Data_Emissao = empty($T005['T005_Data_Emissao']) ? '' : gCorrigeData($T005['T005_Data_Emissao']);

echo <<<EOT
	<div data-role="footer" data-position="fixed">
		<h3 style="margin: 4px 4px 4px 0;">
			<span>
				Pedido: {$T005_Id} - {$T005_Data_Emissao} - {$C007['C007_Primeiro_Nome']}<br>
				{$T005['D024_Nome_Fantasia']}
			</span>
		</h3>
	</div>
	    </body>
	</html>
EOT;
