<?php
namespace hardness;

include('conf.php');
include('inc.conf.php');

// Vars
$pesquisa = isset($_REQUEST['pesquisa']) ? $_REQUEST['pesquisa'] : '';

include('mobile/tablet/inc.header.php');
echo <<<EOT
	<div data-role="page" id="pagepedidoProcurarServico">
EOT;
include('mobile/tablet/pedido/inc.header.bar.php');
echo <<<EOT
        	<div data-role="content">
                <div class="ui-grid-a" style="margin-top: 7px; margin-bottom: 5px;">
                	<div class="ui-block-a">
                		<a data-role="button" data-inline="true" data-transition="slide" data-direction="reverse" href="./pedido.php?T005_Id={$T005_Id}{$acao}" data-ajax="false">
                            Voltar
                        </a>
                	</div>
                    <div class="ui-block-b">
                    	<div style="float:right;">
                    		<a data-role="button" data-transition="slide" data-icon="plus" data-iconpos="left" id="adicionarServico2" data-ajax="false" onclick="$.mobile.changePage( './pedidoProcurarTipoServico.php?T005_Id={$T005_Id}{$acao}&pesquisa=' + encodeURIComponent($('#procurarServico2').val()), { transition: 'slide'} )">
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
	    						<td style="width: 100%">Tipo do Serviço</td>
	    					</tr>
	    				</table>
                    </li>
EOT;

// Serviços
$pesquisa_ = explode(' ', $pesquisa);
if (count($pesquisa_) == 0) {
	$extra = <<<EOT
		((T212_Nome LIKE '%{$pesquisa}%'))
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
	$extra .= str_replace('[%]', 'T212_Nome', $tmp);
	$extra .= ')';
}
$sql = <<<EOT
	SELECT * FROM T212 WHERE {$extra} ORDER BY T212_Nome LIMIT 0,100
EOT;
$res = mysql_query($sql);
if(mysql_num_rows($res) == '0'){
	echo "<span style='margin-left:40%; font-weight:bold; font-size:20px;'>Serviço não encontrado</span>";
}
while ($row = mysql_fetch_assoc($res)) {
	echo <<<EOT
		<li data-theme="c" data-icon="false">
			<a href="./pedidoProcurarServico.php?T005_Id={$T005_Id}&T212_Id={$row['T212_Id']}{$acao}" data-ajax="false" data-transition="slide">
	    		<table style="width: 100%";>
	    			<tr>
	    				<td style="width: 100%">{$row['T212_Nome']}</td>
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
    	$( document ).undelegate("#pagepedidoProcurarServico", "pageinit");
    	$( document ).delegate("#pagepedidoProcurarServico", "pageinit", function() {
EOT;

echo <<<EOT
    	});
    </script>
EOT;
