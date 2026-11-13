<?php
set_time_limit(0);
$conteudo = '';
if (file_exists('tiago.txt')) {
	$conteudo = file_get_contents('tiago.txt');
}
if (isset($_REQUEST['editor'])) {
	$conteudo = $_REQUEST['editor'];
	file_put_contents('tiago.txt', $conteudo);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Interface Admin - Hardnes Sistemas</title>

	<!-- Le styles -->
	<link href="/hardness3/static/css/temas/empresa/jqueryui.css" rel="stylesheet">
	<link href="/hardness3/static/bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link href="/hardness3/bibliotecas/codeMirror-3.02/lib/codemirror.css" rel="stylesheet">
	<style type="text/css">
	body {
		padding-top: 40px;
	}
	.diva {
		background-color: #FFFFFF;
		border: 1px solid #DDDDDD;
		border-radius: 4px 4px 4px 4px;
		/*padding: 27px 6px;*/
		position: relative;
	}
	</style>
	<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
</head>

<body>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="#">Admin Hardness</a>
				<div class="nav-collapse collapse">
					<p class="navbar-text pull-right">
						Hardness Sistemas
					</p>
					<ul class="nav">
						<li><a href="../">Empresas</a></li>
						<li><a href="../status/">Status</a></li>
						<li><a href="../nfadmin/">NF Admin</a></li>
						<li><a href="../pendencias/">Pendências</a></li>
						<li><a href="../mural/">Mural</a></li>
						<li><a href="../agenda/">Agenda</a></li>
						<li><a href="../comercial/">Comercial</a></li>
						<li><a href="../marcelo/">Marcelo</a></li>
						<li class="active"><a href="../tiago/">Tiago</a></li>
						<!--<li><a href="#about">NF</a></li>-->
					</ul>
				</div><!--/.nav-collapse -->
			</div>
		</div>
	</div>

	<div class="container-fluid" style="margin: 0; padding: 0;">
		<div class="row-fluid">
			<form method="POST">
			<div id="agenda" class="span6 diva" style="margin-bottom: 8px; overflow: hidden; border: 1px solid #DDDDDD; box-shadow: 1px 1px 4px 1px #C5C5C5; margin: 10px; margin-right: 0; width: 100px; height: 100px;">
				<textarea name="editor"><?php echo $conteudo; ?></textarea>
			</div>
			<p style="text-align: center;">
				<button class="btn btn-success" type="submit">Salvar</button>
			</p>
			</form>
		</div>
	</div>

	<!-- Placed at the end of the document so the pages load faster -->
	<script src="/hardness3/static/js/jquery-1.7.2.min.js"></script>
	<script src="/hardness3/static/js/jquery-ui-1.8.16.custom.min.js"></script>
	<script src="/hardness3/static/bootstrap/js/bootstrap.min.js"></script>
	<script src="/hardness3/scripts/admin/js.js"></script>
	<script src="/hardness3/bibliotecas/codeMirror-3.02/lib/codemirror.js"></script>
	<script src="/hardness3/bibliotecas/codeMirror-3.02/mode/mysql/mysql.js"></script>
	<script>
		var editor = false;
		$(document).ready(function() {
			var height = ($(window).height() - 25 - 40 - 50);
			var width = ($(window).width() - 22);
			$('.diva').height(height).width(width);
			$('.diva textarea').height(height - 29).width(width - 3);
			bkLib.onDomLoaded(nicEditors.allTextAreas({maxHeight : height - 29}));
			//nic = new nicEditor({maxHeight : height - 37}).panelInstance('textarea'); 
		});
	</script>
</body>
</html>