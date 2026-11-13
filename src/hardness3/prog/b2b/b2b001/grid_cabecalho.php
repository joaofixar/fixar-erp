<?php

$buscaProduto = $_GET['buscaProduto'];
$D001_Id = $_GET['idProd'];
$carrinho = $_GET['carrinho'];
$excluir = $_GET['excluir'];

$T269 = mysql_query("select T269_Id,T269_Quantidade,D001_Codigo_Produto,replace(D001_Descricao_Produto,D003_Nome_Linha,'') as D001_Descricao_Produto,D003_Nome_Linha,D003_Imagem, D001_Id from T269 left join D001 on D001_Id=T269_D001_Id left join D003 on D003_Id=D001_D003_Id where T269_D024_Id='{$g['clienteAtual']}'");
$T269_Itens = mysql_num_rows($T269);

if (!empty($buscaProduto)){
	$filtro = "and D001_Descricao_Produto like '%".str_replace(' ','%',$buscaProduto)."%'";
}

$D001 = mysql_query("SELECT D001_Id,
							D001_Codigo_Produto,
							replace(D001_Descricao_Produto,D003_Nome_Linha,'') as D001_Descricao_Produto,
							D003_Imagem,
							D003_Nome_Linha,
							sum(D009_Quantidade_Estoque) as estoque,
							T269_Id
					   FROM D001 
				  left join D049 on D049_D001_Id=D001_Id 
				  left join D009 on D009_D049_Id=D049_Id 
                  left join D003 on D003_Id=D001_D003_Id 
                  left join T269 on T269_D001_Id=D001_Id
                      where D001_C008_Id=7 
                        and D001_Flag_Ativo='S' 
                        and ifnull(D003_Imagem,'')!='' 
                        and D003_Nome_Linha!='' 
                            {$filtro}
                   group by D001_Id 
                   order by replace(D001_Descricao_Produto,D003_Nome_Linha,'')");
$D001_Itens = mysql_num_rows($D001);

echo <<<EOT

	<head>
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
	</head>

	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="utf-8">
	    <meta name="viewport" content="width=device-width, initial-scale=1" />
		<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">

		<style type="text/css">  

		body{
			margin: 10px 0px 10px 0px;
			pdding: 10px 10px 10px 10px;
			align: center;
		}

		div.container{
			font-family: "Arial";
			max-width:1000px;
			float: center;
		}

		div.row{
			float: left;
		    margin: 5px;
		    padding: 15px;
		    max-width: 290px;
		    min-width: 290px;
		    height: 290px;
			text-align:center;
			background: white;
			border-bottom:1px dashed silver;
		}	

		div.row1{
			float: left;
		    margin: 5px;
		    padding: 15px;
		    max-width: 100%;
		    min-width: 100%;
		    height: 130px;
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

	<div class='container' style='margin:0 auto'>
		<form action='/b2b/b2b001/grid/gridB2BPrincipal/' method='get'>
			<div style='width:10%;float:left;padding-right:5px;'>
				<button id="Home" class="btn btn-success" type="button" data-placement="bottom" onclick="javascript:location.replace('/b2b/b2b001/grid/gridB2BPrincipal/?ajax=true');"">Home</button>
			</div>
			<div style='width:60%;float:left;'>			
			   	<input type='hidden' name='ajax' id='ajax' value='true'>
			   	<input class='form-control' name='buscaProduto' id='buscaProduto' placeholder='Procurar produto...' value='{$buscaProduto}'>
			</div>
			<div style='width:10%;float:left;padding-left:5px;'>
				<button id="Procurar" class="btn btn-success" type="submit" data-placement="bottom">Ok</button>
			</div>
			<div style='width:20%;float:right'>
				<button id="Carrinho" class="btn btn-info" type="button" data-placement="bottom" onclick="javascript:location.replace('/b2b/b2b001/grid/gridB2BCarrinho?ajax=true&buscaProduto={$buscaProduto}');"">{$T269_Itens} Itens</button>
				<button id="Home" class="btn btn-danger" type="button" data-placement="bottom" onclick="javascript:location.replace('/hardness3/outros/loginb2b/?ajax=true');"">Logout</button>
			</div>
		
		</form>
	</div>


EOT;
