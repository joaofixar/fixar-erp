<?php
// Header
$headerHeadTitle = 'Hardness Sistemas - Videos';
include('requisicoes/header.php');

$footerJS = <<<EOT
	$(document).ready(function() {
EOT;
echo <<<EOT
<div id="conheca" class="container conteudo">
	<div class="row">
		<div class="col-md-12" role="main">
			<div class="page-header">
				<h3 style="color:#1F4C74" id="sistema">Videos</h1>
			</div>
			
			<div class="row">
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Introducao_I.mp4" type='video/mp4'>
						</video>
						Introdução ao sistema I
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Introducao_II.mp4" type='video/mp4'>
						</video>
						Introdução ao sistema II
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Introducao_III.mp4" type='video/mp4'>
						</video>
						Introdução ao sistema III
					</center>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Cadastros_Clientes.mp4" type='video/mp4'>
						</video>
						Cadastro de clientes
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Cadastros_Transportadoras.mp4" type='video/mp4'>
						</video>
						Cadastro de transportadoras
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Cadastros_Agenda_de_contatos.mp4" type='video/mp4'>
						</video>
						Cadastro de agenda de contatos
					</center>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Cadastros_Representantes.mp4" type='video/mp4'>
						</video>
						Cadastro de representantes
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Cadastros_Produtos_I.mp4" type='video/mp4'>
						</video>
						Cadastro de produtos I
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Cadastros_Produtos_II.mp4" type='video/mp4'>
						</video>
						Cadastro de produtos II
					</center>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Cadastros_Caixas_Bancos.mp4" type='video/mp4'>
						</video>
						Cadastro de bancos e caixas
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Cadastros_Centro_de_custos.mp4" type='video/mp4'>
						</video>
						Cadastro de centro de custos
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Cadastros_Plano_de_contas_Projecao_orcamentaria.mp4" type='video/mp4'>
						</video>
						Cadastro de plano de contas e projeção orçamentária
					</center>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Vendas_Processo_de_Vendas.mp4" type='video/mp4'>
						</video>
						Processo de vendas
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Vendas_Orcamentos_I.mp4" type='video/mp4'>
						</video>
						Orçamentos
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Vendas_CRM.mp4" type='video/mp4'>
						</video>
						CRM
					</center>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Financeiro_Contas_a_pagar.mp4" type='video/mp4'>
						</video>
						Contas a pagar
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Financeiro_Contas_a_receber_I.mp4" type='video/mp4'>
						</video>
						Contas a receber I
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Financeiro_Contas_a_receber_II.mp4" type='video/mp4'>
						</video>
						Contas a receber II
					</center>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Estoque_Compras_de_emergencia.mp4" type='video/mp4'>
						</video>
						Compras de emergência
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Estoque_Processo_de_compras_I.mp4" type='video/mp4'>
						</video>
						Processo de Compras I
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Estoque_Processo_de_compras_II.mp4" type='video/mp4'>
						</video>
						Processo de Compras II
					</center>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Estoque_Compras_de_estoque.mp4" type='video/mp4'>
						</video>
						Compras de estoque
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Fiscal_Configuracoes_gerais.mp4" type='video/mp4'>
						</video>
						Fiscal - Visão geral
					</center>
				</div>
				<div class="col-md-4" role="main">
					<center>
						<video id="my_video_1" class="video-js vjs-default-skin vjs-big-play-centered" controls preload="auto" width="320" height="180" poster="/static/img/poster.png" data-setup="{}">
							<source src="/static/mp4/Fiscal_Regras_CFOP.mp4" type='video/mp4'>
						</video>
						Regras de CFOP
					</center>
				</div>
			</div>
		</div>
	</div>
</div>
EOT;
// Footer
$footerJS .= <<<EOT
});
EOT;
include('requisicoes/footer.php');