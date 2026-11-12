<?php
namespace hardness;
include('inc.header.php');
echo <<<EOT
	<script type="text/javascript">
		Hardness.browserClearHistory();
	</script>
	<div style="padding: 5px; margin-top: 5px; clear: both;">
		<a href="analiseOrcamento.php"><button class="ui-corner-all">Análise Orçamento</button></a>
		<button class="ui-corner-all" onClick="Hardness.exit();">Sair</button>
	</div>
EOT;
include('inc.footer.php');