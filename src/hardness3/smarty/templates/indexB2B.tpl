<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1" />
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">

		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<script src="/hardness3/static/js/jquery-1.6.2.min.js"></script>
		<script src="/hardness3/static/js/jquery-ui-1.8.16.custom.min.js"></script>
		<script src="/hardness3/static/js/sistema_2.js"></script>
		<script src="/hardness3/static/js/jquery.showLoading.js"></script>
		<script src="/hardness3/static/js/jquery.form.js"></script>
		<script src="/hardness3/static/js/swfobject.js"></script>
		<script src="/hardness3/static/js/fileuploader.js"></script>
		<script src="/hardness3/static/js/nicEdit.js"></script>
		<script src="/hardness3/static/js/mask.js"></script>
		<script src="/hardness3/static/js/json2.min.js"></script>
		<script src="/hardness3/static/js/jquery.ui.multidraggable-1.8.8.js"></script>
		<script src="/hardness3/static/js/shortcut.js"></script>
		<script src="/hardness3/static/js/flotr2/lib/yepnope.js"></script>
		<script src="/hardness3/static/js/flotr2/flotr2.min.js"></script>
		<script src="/hardness3/bibliotecas/codemirror-3.20/lib/codemirror.js"></script>
		<script src="/hardness3/bibliotecas/codemirror-3.20/mode/xml/xml.js"></script>
		<script src="/hardness3/bibliotecas/codemirror-3.20/mode/javascript/javascript.js"></script>
		<script src="/hardness3/bibliotecas/codemirror-3.20/mode/css/css.js"></script>
		<script src="/hardness3/bibliotecas/codemirror-3.20/mode/clike/clike.js"></script>
		<script src="/hardness3/bibliotecas/codemirror-3.20/mode/php/php.js"></script>
		<script src="/hardness3/static/bootstrapErp/js/bootstrap.js"></script>
		<script src="/hardness3/static/js/go.js"></script>
		<script src="/hardness3/static/js/jquery.zclip.min.js"></script>
		<script src="/hardness3/static/js/html2canvas.js"></script>

		<style type="text/css">  

		body{
			margin: 10px 0px 10px 0px;
			pdding: 10px 10px 10px 10px;
			align: center;
		}

		div.container{
			font-family: "Arial";
			max-width:1100px;
			float: center;
		}

		div.row{
			float: left;
		    margin: 5px;
		    padding: 15px;
		    max-width: 255px;
		    min-width: 255px;
		    height: 290px;
			text-align:center;
			background: white;
			border-bottom:1px dashed silver;
		}	

		div.row-carrinho{
			float: left;
		    margin: 5px;
		    padding: 15px;
		    max-width: 255px;
		    min-width: 255px;
		    height: 290px;
			text-align:center;
			background: white;
			border-bottom:1px dashed silver;
		}	

		div.cab{
			float: left;
		    margin: 5px;
		    padding: 15px;
		    max-width: 1075px;
		    min-width: 1075px;
			text-align:center;
			background: white;
			border-bottom:1px dashed silver;
		}	

		p.descricao{
		    height: 60px;
		}	

		p.foto{
		    height: 150px;
			max-width:110px;
			margin: 0 auto;
		}	

		#input{
			font-size:16px;
		}
		

	</style>

	</head>

	<head>
		<title>{$titulo}</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		{$cssJs}
		<style type="text/css">
		{$cssExtra}
		</style>
	</head>

	<body style="overflow: hidden;background: white">
		<div class="main">
			<div id="desktop">
			</div>
			<div id="footer">
			</div>
		</div>

	</body>
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		$('#selecionaEmpresa').hide();
		$('#selecionaUsuario').hide();
		
		$('#campoEmpresa').unbind('click').bind('click', function() {
			$('#campoEmpresa').hide();
			$('#selecionaEmpresa').show();
			fixMenuExtraHtml();
		});	
		
		$('#campoUsuario').unbind('click').bind('click', function() {
			$('#campoUsuario').hide();
			$('#selecionaUsuario').show();
			fixMenuExtraHtml();
		});

		function esconderEmpresa() {
			$('#selecionaEmpresa').hide();
			$('#campoEmpresa').show();
			fixMenuExtraHtml();
		}

		function esconderUsuario() {
			$('#selecionaUsuario').hide();
			$('#campoUsuario').show();
			fixMenuExtraHtml();
		}

		function fixMenuExtraHtml() {
			var menu1Size = $('#footer > div').outerWidth() + 14
			var menu2Size = 0;
			$('#topoMenu li').each(function() {
				menu2Size += $(this).outerWidth();
			});
			var size = (menu2Size > menu1Size) ? menu2Size : menu1Size;
			$('#menuextrahtml')
				.css({
					'left': size - 10
				})
				.show();
		}
		
		// Globais
		var g = {
			'empresaAtual': '{$empresaAtual}',
			'usuarioAtual': '{$usuarioAtual}',
			'personalizacaoConf': {$personalizacaoConf},
			'alertaEmailRecebido' : {$alertaEmailRecebido},
			'menuExtraEmpresas': {$menuExtraEmpresas},
			'tamanhosJanelas': {$tamanhosJanelas},
			'janelasIgnorarESC': {$janelasIgnorarESC},
			'oneWindow': '{$oneWindow}',
			'nomeEmpresaAbreviado' : '{$empresaAtualNomeAbreviado}',
			'D024_Id_Hardness' : '{$D024_Id_Hardness}'
		};
		$(document).ready(function() {
			{$teclasAtalho}
			fixMenuExtraHtml();
		});
	}</script></div>
</html>
