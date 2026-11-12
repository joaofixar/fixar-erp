<?php
// Header
$headerHeadTitle = 'Hardness Sistemas - Soluções de negócio';
include('requisicoes/header.php');

$footerJS = <<<EOT
	$(document).ready(function() {
EOT;
/*
<!--<div style="text-align: center;">
	<video id="my_video_1" class="video-js vjs-default-skin" controls preload="auto" width="640" height="360" poster="/static/img/poster.png" data-setup="{}">
	<source src="/static/mp4/sistema.mp4" type='video/mp4'>
	</video>
</div>-->
*/
echo <<<EOT
<div id="conheca" class="container conteudo">
	<div class="row">
		<div class="col-md-9" role="main">
			<div class="bs-docs-section">
				<div class="page-header">
					<h3 style="color:#1F4C74" class="modulo" id="sistema">Hardness ERP</h1>
				</div>
				<div class="row">
					<div class="col-md-12">
					<p><ul>
						<li><span class='glyphicon glyphicon-ok'></span> Foco na experiência de uso e conforto do usuário.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Sistema ERP em nuvem (100% web).</li>
						<li><span class='glyphicon glyphicon-ok'></span> Melhor custo benefício e menor curva de aprendizado. </li>
						<li><span class='glyphicon glyphicon-ok'></span> Simplifica atividades complexas garantindo maior qualidade de trabalho aos usuários.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Adaptável ao nível de complexidade do seu negócio: Baixa, Média ou Alta complexidade. </li>
						<li><span class='glyphicon glyphicon-ok'></span> Garantia de sucesso da implantação, ágil e personalizada.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Integra todos os seus departamentos e processos.</li>
					</ul></p>
					</div>
				</div>
				<p style="text-align: justify; color: black;">
				</p>
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-4">
						<a href="/static/img/tela_erp3.png" data-lightbox="erp" title="Tela de login"><img src="/static/img/tela_erp3.png" style="width: 100%;" class=screen onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_erp2.png" data-lightbox="erp" title="Tela de alertas"><img src="/static/img/tela_erp2.png" style="width: 100%;" class=screen onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_erp1.png" data-lightbox="erp" title="Catálogo de produtos"><img src="/static/img/tela_erp1.png" class=screen style="width: 100%;" onclick></a>
					</div>
				</div>
				<!--<p style="text-align: center;">
					<img src="/static/img/fluxograma.png" style="width: 100%;"/>
				</p>-->
			</div>
			<div class="bs-docs-section">
				<div class="page-header">
					<h3 style="color:#1F4C74" class="modulo" id="nota">Nota Fiscal Eletrônica</h1>
				</div>
				<div class="row">
					<div class="col-md-12">
					<p><ul>
						<li><span class='glyphicon glyphicon-ok'></span> Emissão de NF-e (Nota Fiscal eletrônica) e NFC-e (Nota Fiscal de Consumidor eletrônica).</li>
						<li><span class='glyphicon glyphicon-ok'></span> Controle de Notas Fiscais emitidas (Envio por e-mail, inutilização e cancelamento).</li>
						<li><span class='glyphicon glyphicon-ok'></span> Carta de Correção Eletrônica (CC-e).</li>
						<li><span class='glyphicon glyphicon-ok'></span> Importação e exportação de XML's.</li>
					</ul></p>
					</div>
				</div>
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-4">
						<a href="/static/img/tela_notas_fiscais_2.png" data-lightbox="erp" title="Lista de notas fiscais"><img src="/static/img/tela_notas_fiscais_2.png" class=screen style="width: 100%;" onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_notas_fiscais_3.png" data-lightbox="erp" title="Inclusão nota fiscal"><img src="/static/img/tela_notas_fiscais_3.png" style="width: 100%;" class=screen onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_notas_fiscais_4.png" data-lightbox="erp" title="Tela de transmissão nota fiscal"><img src="/static/img/tela_notas_fiscais_4.png" style="width: 100%;" class=screen onclick></a>
					</div>
				</div>
			</div>
			<div class="bs-docs-section">
				<div class="page-header">
					<h3 style="color:#1F4C74" class="modulo" id="vendas">Vendas</h1>
				</div>
				<div class="row">
					<div class="col-md-12">
					<p><ul>
						<li><span class='glyphicon glyphicon-ok'></span> CRM.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Carteira de clientes ativos e inativos.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Controle de contatos telefônicos e por e-mail.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Agendamento de contato.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Orçamentos.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Pedidos.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Geração automática de Nota Fiscal.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Controle de alçadas (descontos, prazo médio, frete, etc...).</li>
						<li><span class='glyphicon glyphicon-ok'></span> Controle de comissões.</li>
					</ul></p>
					</div>
				</div> 
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-4">
						<a href="/static/img/tela_vendas.png" data-lightbox="erp" title="CRM - Lista de pedidos de venda"><img src="/static/img/tela_vendas.png" style="width: 100%;" class=screen onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_vendas_2.png" data-lightbox="erp" title="Pedido de venda"><img src="/static/img/tela_vendas_2.png" style="width: 100%;" class=screen onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_vendas_3.png" data-lightbox="erp" title="CRM cliente - Resumo vendas"><img src="/static/img/tela_vendas_3.png" style="width: 100%;" class=screen onclick></a>
					</div>
				</div> 
			</div>
			<div class="bs-docs-section">
				<div class="page-header">
					<h3 style="color:#1F4C74" class="modulo" id="estoque">Estoque</h1>
				</div>
				<div class="row">
					<div class="col-md-12">
					<p><ul>
						<li><span class='glyphicon glyphicon-ok'></span> Controle de estoque.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Lançamento de Conhecimentos de Transporte.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Lançamento de Notas Fiscais recebidas (XML).</li>
						<li><span class='glyphicon glyphicon-ok'></span> Recebimento de mercadorias.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Expedição de mercadorias.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Divergências e balanços.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Produtos montados, transformados e produzidos.</li>
					</ul></p>
					</div>
				</div>	
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-4">
						<a href="/static/img/tela_estoque.png" data-lightbox="erp" title="Estoque - Balanço / Invetário"><img src="/static/img/tela_estoque.png" style="width: 100%;" class=screen onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_estoque_2.png" data-lightbox="erp" title="Reposição estoque - itens com estoque insuficiente"><img src="/static/img/tela_estoque_2.png" style="width: 100%;" class=screen onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_estoque_3.png" data-lightbox="erp" title="Entrada NF-e - confronto ordem de compra"><img src="/static/img/tela_estoque_3.png" style="width: 100%;" class=screen onclick></a>
					</div>
				</div> 
			</div>
			<div class="bs-docs-section">
				<div class="page-header">
					<h3 style="color:#1F4C74" class="modulo" id="compras">Compras</h1>
				</div>
				<div class="row">
					<div class="col-md-12">
					<p><ul>
						<li><span class='glyphicon glyphicon-ok'></span> SRM.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Sugestão de compras para estoque.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Compras de emergência.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Cotações de preços.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Ordens de compra.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Entrada de NF-e e NFC-e.</li>
						<li><span class='glyphicon glyphicon-ok'></span> DI - Declaração de Importação.</li>
					</ul></p>
					</div>
				</div>	
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-4">
						<a href="/static/img/tela_compras.png" data-lightbox="erp" title="Sugestão e análise de compras para estoque"><img src="/static/img/tela_compras.png" style="width: 100%;" class=screen onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_compras_2.png" data-lightbox="erp" title="Sugestão e análise de reposição de estoque"><img src="/static/img/tela_compras_2.png" style="width: 100%;" class=screen onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_compras_3.png" data-lightbox="erp" title="Formação de custo e preço de venda em tempo real"><img src="/static/img/tela_compras_3.png" style="width: 100%;" class="screen" onclick></a>
					</div>
				</div>
			</div>
			<div class="bs-docs-section">
				<div class="page-header">
					<h3 style="color:#1F4C74" class="modulo" id="financeiro">Financeiro</h1>
				</div>
				<div class="row">
					<div class="col-md-12">
					<p><ul>
						<li><span class='glyphicon glyphicon-ok'></span> Controle de contas a receber e a pagar.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Emissão de boletos (Impressão e envio por e-mail).</li>
						<li><span class='glyphicon glyphicon-ok'></span> Integração Bancária.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Conciliação bancária.</li>					
						<li><span class='glyphicon glyphicon-ok'></span> Plano de contas e centros de custos.</li>	
						<li><span class='glyphicon glyphicon-ok'></span> Projeção orçamentária.</li>	
					</ul></p>
					</div>
				</div>
				<div class="row" style="margin-top: 25px;">
					<div class="col-md-4">
						<a href="/static/img/tela_pagar.png" data-lightbox="erp" title="Contas a receber"><img src="/static/img/tela_pagar.png" style="width: 100%;" class="screen" onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_pagar_2.png" data-lightbox="erp" title="Projeção orçamentária"><img src="/static/img/tela_pagar_2.png" style="width: 100%;" class="screen" onclick></a>
					</div>
					<div class="col-md-4">
						<a href="/static/img/tela_pagar_3.png" data-lightbox="erp" title="Controle caixa / banco"><img src="/static/img/tela_pagar_3.png" style="width: 100%;" class="screen" onclick></a>
					</div>
				</div> 
			</div>
			<div class="bs-docs-section">
				<div class="page-header">
					<h3 style="color:#1F4C74" class="modulo" id="gerencial">Gerencial</h1>
				</div>
				<div class="row">
					<div class="col-md-12">
					<p><ul>
						<li><span class='glyphicon glyphicon-ok'></span> BI.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Fluxo de Caixa.</li>
						<li><span class='glyphicon glyphicon-ok'></span> DRE.</li>
						<li><span class='glyphicon glyphicon-ok'></span> Relatórios.</li>					
					</ul></p>
					</div>
				</div>
				<!--<div class="row" style="margin-top: 25px;">
					<div class="col-md-3">
						<a href="/static/img/tela_pagar.png" data-lightbox="pagar" title="Títulos a Pagar Emitidos / Gerados"><img src="/static/img/tela_pagar.png" style="width: 100%;" class="screen" onclick></a>
					</div>
					<div class="col-md-3">
						<a href="/static/img/tela_pagar_2.png" data-lightbox="pagar" title="Baixa do Título a Pagar"><img src="/static/img/tela_pagar_2.png" style="width: 100%;" class="screen" onclick></a>
					</div>
					<div class="col-md-3">
						<a href="/static/img/tela_pagar_3.png" data-lightbox="pagar" title="Reparcelamento do Título a Pagar"><img src="/static/img/tela_pagar_3.png" style="width: 100%;" class="screen" onclick></a>
					</div>
					<div class="col-md-3">
						<a href="/static/img/tela_pagar_4.png" data-lightbox="pagar" title="Detalhes do Título a Pagar"><img src="/static/img/tela_pagar_4.png" style="width: 100%;" class="screen" onclick></a>
					</div>
				</div> -->
			</div>
		</div>
		<div class="col-md-3">
			<div class="bs-sidebar hidden-print affix" role="complementary">
			<ul class="nav bs-sidenav">    
				<li class="active">
					<a href="#sistema">Hardness ERP</a>
				</li>
				<li>
					<a href="#nota">Nota Fiscal Eletrônica</a>
				</li>
				<li>
					<a href="#vendas">Vendas</a>
				</li>
				<li>
					<a href="#estoque">Estoque</a>
				</li>
				<li>
					<a href="#compras">Compras</a>
				</li>
				<li>
					<a href="#financeiro">Financeiro</a>
				</li>
				<li>
					<a href="#gerencial">Gerencial</a>
				</li>
			</ul>
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