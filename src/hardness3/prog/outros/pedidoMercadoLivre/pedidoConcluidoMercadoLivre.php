<?php
namespace hardness;

if(!empty($g['c004']['C004_Logo_Marca'])){
	$pathinfo = pathinfo($g['c004']['C004_Logo_Marca']);
	$caminhoImagem = "{$confUsuario['urlRaiz']}{$g['pathWeb']}{$g['pathDados']}imagens/logo/{$g['empresaAtual']}.{$pathinfo['extension']}";
}else{
	$caminhoImagem="http://tudo.hardness.com.br//hardness3/dados_usuarios/e215/imagens/logo/1.png";
}
$logo ='<img src='.$caminhoImagem.' style="margin-left:25px;margin-top:0px;">';

echo <<<EOT
	<html>
		<head>
			<title>Confirmação pedido</title>
			<script src="/hardness3/static/js/jquery-1.6.2.min.js"></script>
			<script src="/hardness3/static/js/jquery-ui-1.8.16.custom.min.js"></script>
			<script src="/hardness3/static/js/mask.js"></script>
			<style type="text/css">
				*{
					margin:0;
					padding:0;
				}
				body{
					font-family: Arial, Helvetica,sans-serif; 
					background: #F4F4F4;
					size: 5px;
				}
				input {
					text-transform: uppercase;
				}
				textarea {
					text-transform: uppercase;
				}
				.generalContent{
					width:100%;
				}
				.generalContent .centerContent{
					width:850px;
					margin:0 auto;
					border:5px solid #CFCFCF;
				}
				.generalContent .centerContent .header{
					width:100%;
				}
				.generalContent .centerContent .header table{
					width:98%;
					margin:0px auto;
					height:50px;
				}
				.generalContent .centerContent .form{
					width:100%;
				}
				.generalContent .centerContent .form table{
					width:100%;
				}
				.generalContent .centerContent .form table th{
					text-align:left;
					height:50px;
					font-size:20px;
					border-top:1px solid silver;
					border-bottom:1px solid silver;
				}
				.generalContent .centerContent .form table tr td{
					padding:5px;
				}
				.contentProduto{
					width:100%;
					float:left;
				}
				
			</style>
		</head>
		<body style="background: url('http://tudo.hardness.com.br/hardness3/static/css/temas/padrao/images/desktop.png');">
			<div class="generalContent">
				<div class="centerContent" style="background: #F4F4F4;">
					<div class="header">
						<table cellpadding=0 cellspacing=0>
							<tr>
								<td>{$logo}</td>
							</tr>
							<tr>
								<td align="center" style="font-size:28px; border-top:1px solid silver; padding: 80px 0;"><b>Pedido concluido com sucesso!</b></td>
							</tr>
							<tr>
								<td style="border-top:1px solid silver; padding: 10px 0;" colspan="4" align="center">Se tiver alguma dúvida entre em contato com nossa Central de Atendimento pelo email vendas@tudoesportes.com.br ou pelo telefone 41-3042 3939 de segunda a sexta-feira das 9:00 as 18:00hrs</td>
							</tr>
						</table>
					</div>
				</div>
			</div>	
		</body>
	</html>
EOT;

?>