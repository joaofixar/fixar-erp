<?php
namespace hardness;
include('inc.conf.php');

$T003_Data_Emissao = empty($T003['T003_Data_Emissao']) ? '' : gCorrigeData($T003['T003_Data_Emissao']);

if($T003_Id > 0){
	echo <<<EOT
		<div data-role="footer" data-position="fixed">
			<h3 style="margin: 4px 4px 4px 0;">
				<span>
					Orç.: {$T003_Id} - {$T003_Data_Emissao} - {$C007['C007_Primeiro_Nome']}<br>
					{$T003['D024_Nome_Fantasia']}
				</span>
			</h3>
		</div>
	    </body>
	</html>
EOT;
}
