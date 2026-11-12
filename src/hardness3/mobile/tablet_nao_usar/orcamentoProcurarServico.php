<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$pesquisa = isset($_REQUEST['pesquisa']) ? $_REQUEST['pesquisa'] : '';

include('inc.header.php');
echo <<<EOT
	<div data-role="page" id="pageOrcamentoProcurarServico">
EOT;
include('inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px;">
                	<div class="ui-block-a">
                		<a data-role="button" data-inline="true" data-transition="slide" data-direction="reverse" href="./orcamento.php?T003_Id={$T003_Id}" data-ajax="false">
                            Voltar
                        </a>
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarServico2" data-ajax="false" onclick="$.mobile.changePage( './orcamentoProcurarServico.php?T003_Id={$T003_Id}&pesquisa=' + encodeURIComponent($('#procurarServico2').val()), { transition: 'slide'} )">
			            		Procurar
				            </a>
				        </div>
                    	<div style="width: 70%; float:right; margin-top: 14px;">
                    		<input placeholder="Procurar Serviço" data-type="search" class="ui-input-text ui-body-c" id="procurarServico2">
                    	</div>
                    </div>
                </div>
                <ul data-role="listview" data-divider-theme="b" data-inset="true" style="margin-top: 1px;">
                    <li data-role="list-divider" role="heading">
	    				<table style="width: 100%";>
	    					<tr>
	    						<td style="width: 5%"; align="center">Código</td>
	    						<td style="width: 25%";>Titulo</td>
	    						<td style="width: 40%";>Descrição serviço</td>
	    						<td style="width: 15%";>Tipo</td>
	    						<td style="width: 5%"; align="center">Tempo</td>
	    						<td style="width: 10%"; align="right">Preço R$</td>
	    					</tr>
	    				</table>
                    </li>
EOT;

// Serviços
$pesquisa_ = explode(' ', $pesquisa);
if (count($pesquisa_) == 0) {
	$extra = <<<EOT
		(
		(D112_Id LIKE '%{$pesquisa}%') OR 
		(D112_Titulo LIKE '%{$pesquisa}%') OR 
		(D112_Descricao LIKE '%{$pesquisa}%')
		)
EOT;
} else {
	$tmp = '(';
	foreach ($pesquisa_ as $word) {
		if ($tmp == '(') {
			$tmp .= "[%] LIKE '%{$word}%'";
		} else {
			$tmp .= " AND [%] LIKE '%{$word}%'";
		}
	}
	$tmp .= ')';
	$extra = '(';
	$extra .= str_replace('[%]', 'D112_Id', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D112_Titulo', $tmp) . ' OR';
	$extra .= str_replace('[%]', 'D112_Descricao', $tmp);
	$extra .= ')';
}

$T212_Id = isset($_REQUEST['T212_Id']) ? 'AND D112_T212_Id = '.$_REQUEST['T212_Id'] : '';
$sql = <<<EOT
	SELECT * FROM D112 LEFT JOIN T212 ON T212_Id = D112_T212_Id WHERE {$extra} {$T212_Id} ORDER BY D112_Titulo LIMIT 0,100
EOT;

$res = mysql_query($sql);
if(mysql_num_rows($res) == '0'){
	echo "<span style='margin-left:40%; font-weight:bold; font-size:20px;'>Serviço não encontrado</span>";
}
while ($row = mysql_fetch_assoc($res)) {
	$row['D112_Valor_Unitario'] = gCorrigeNumero($row['D112_Valor_Unitario']);
	$row['D112_Tempo_Estimado'] = gCorrigeTempo($row['D112_Tempo_Estimado']);
	echo <<<EOT
		<li data-theme="c" data-icon="false">
			<a href="./orcamentoAddServico.php?T003_Id={$T003_Id}&D112_Id={$row['D112_Id']}" data-ajax="false" data-transition="slide">
	    		<table style="width: 100%";>
	    			<tr>
	    				<td style="width: 5%"; align="center">{$row['D112_Id']}</td>
	    				<td style="width: 25%";>{$row['D112_Titulo']}</td>
	    				<td style="width: 40%";>{$row['D112_Descricao']}</td>
	    				<td style="width: 15%";>{$row['T212_Nome']}</td>
	    				<td style="width: 5%"; align="center">{$row['D112_Tempo_Estimado']}</td>
	    				<td style="width: 10%"; align="right">{$row['D112_Valor_Unitario']}</td>
	    			</tr>
	    		</table>
		    </a>
	    </li>
EOT;
}

echo <<<EOT
                </ul>
            </div>
EOT;
include('inc.footer.php');
echo <<<EOT
    </div>
    <script>
    	$( document ).undelegate("#pageOrcamentoProcurarServico", "pageinit");
    	$( document ).delegate("#pageOrcamentoProcurarServico", "pageinit", function() {
EOT;
include('inc.swipe.events.php');
echo <<<EOT
    	});
    </script>
EOT;
