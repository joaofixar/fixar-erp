<?php
namespace hardness;

include('conf.php');

$resposta = array('code' => true, 'data' => '');

switch ($_REQUEST['opcao']) {
	case 'principal':
		$resposta['data'] = <<<EOT
	<div>
    	<input name="scan" id="scan" value="" type="text" style="width: 97%; position: absolute; top: -100px;" />
    	<div class="alerta">LEIA O CÓDIGO DE BARRAS DO PRODUTO</div>
        <div id="content" style="margin: 5px;"></div>
	</div>
    <div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
    	$('#scan').unbind('keypress');
    	$('#scan').bind('keypress', function(e) {
    		if (e.keyCode == 13) {
    			modal('Verificando...');
    			$.getJSON('./buscaProduto.php?opcao=busca&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
    				modalRemove();
    				$('#scan').val('');
					$('#content').empty().html(request.data);
				});
    		}
    	});
    	$('#scan').focus();
    } </script></div>
EOT;
		break;

	case 'busca':
		if (isset($_REQUEST['scan'])) {
			$sql = "SELECT D001_Codigo_Produto, 
			               D001_Descricao_Produto, 
			               D009_Quantidade_Estoque, 
			               D082_Marca 
			          FROM D009 
			     LEFT JOIN D049 ON D049_Id=D009_D049_Id 
			     LEFT JOIN D001 ON D001_Id=D049_D001_Id 
			     LEFT JOIN D082 ON D082_Id=D049_D082_Id 
			         WHERE D049_Codigo_Barras = '{$_REQUEST['scan']}' 
			            OR D001_Codigo_Produto = '{$_REQUEST['scan']}'
			            OR D049_Id = '{$_REQUEST['scan']}';";
			$res = mysql_query($sql);
			if (mysql_numrows($res) == 0) {
				$resposta['data'] = <<<EOT
				    <p align='center' style='color:red'>
					<strong>Código de produto não encontrado.</strong>
					</p>
EOT;
			} else {
				$row = mysql_fetch_assoc($res);
				$resposta['data'] = <<<EOT
				    <p align=center>
					Código:<br /><strong>{$row['D001_Codigo_Produto']}</strong><br />
					Descrição:<br /><strong>{$row['D001_Descricao_Produto']}</strong><br />
					Marca:<br /><strong>{$row['D082_Marca']}</strong><br />
					Estoque:<br /><strong>{$row['D009_Quantidade_Estoque']}</strong><br />
					</p>
EOT;
			}
		} else {
			$resposta['data'] = "<strong>Código de produto não encontrado.</strong>";
		}
		break;
}

$resposta['data'] .= <<<EOT
<button class="bigBtn" id="retornarMenu">Voltar</button>

<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
	$('#retornarMenu').unbind('click').bind('click', function() {
    	 window.location="./menu.php";
    });
		
	$('#scan').unbind('keypress');
	$('#scan').bind('keypress', function(e) {
		if (e.keyCode == 13) {
		    modal('Verificando...');
            $.getJSON('./buscaProduto.php?opcao=busca&scan=' + encodeURIComponent($('#scan').val()) + '&callback=?', function(request) {
		    	modalRemove();
		    	$('#scan').val('');
				$('#contentMenu').empty().html(request.data);
			});
		}
	});
	// (não perder o foco do campo scan)
	$('#scan').focus();
} </script></div>

EOT;

echo $_REQUEST['callback'] . "(" . json_encode($resposta) . ");";

