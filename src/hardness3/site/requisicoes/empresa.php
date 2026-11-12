<?php
// Header
$headerHeadTitle = 'Hardness Sistemas - Empresa';
include('requisicoes/header.php');

$footerJS = <<<EOT
	$(document).ready(function() {
EOT;
echo <<<EOT
<div id="conheca" class="container conteudo">
<div class="row">
	<div class="col-md-8" role="main">
		<div class="page-header">
			<h3 style="color:#1F4C74" class="contact">Empresa</h3>
		</div>
		<div>
		A Hardness Sistemas se diferencia pelo foco na experiência de uso e conforto do usuário. A boa utilização do sistema trará resultados rápidos e precisos! Oferecemos sistemas de gestão empresarial em nuvem, com o melhor custo benefício e menor curva de aprendizado. Nosso sistema simplifica atividades complexas garantindo maior qualidade de trabalho aos usuários.
Desde 1993 atuamos diariamente no crescimento de nossos clientes, temos orgulho de participar de tantos casos de sucesso!
		</div>
		<div  class="col-md-20">
			<div style="width: 100%; float: left;">
				<div class="page-header">
					<h3 style="color:#1F4C74" class="contact">Clientes</h3>
				</div>
				<div class="col-md-3" style="position: relative; height: 100px;">
					<img src="/static/img/logos/logo_crv.jpg" style="width: 150px; height: 70px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0" />
				</div>
				<div class="col-md-3" style="position: relative; height: 100px;">
					<img src="/static/img/logos/logo_incavel.jpg" style="width: 150px; height: 60px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0" />
				</div>
				<div class="col-md-3" style="position: relative; height: 100px;">
					<img src="/static/img/logos/logo_alumifix.jpg" style="width: 120px; height: 50px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0;" />
				</div>
				<div class="col-md-3" style="position: relative; height: 100px;">
					<img src="/static/img/logos/logo_alumisistem.jpg" style="width: 150px; height: 80px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0" />
				</div>
				<div class="col-md-3" style="position: relative; height: 100px;">
					<img src="/static/img/logos/logo_sulmatel.jpg" style="width: 100px; height: 70px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0; margin-left: 17%" />
				</div>
				<div class="col-md-3" style="position: relative; height: 100px;">
					<img src="/static/img/logos/logo_bigvel.jpg" style="width: 150px; height: 80px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0" />
				</div>
				<div class="col-md-3" style="position: relative; height: 100px;">
					<img src="/static/img/logos/logo_lacombe.jpg" style="width: 100px; height: 70px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0; margin-left: 17%" />
				</div>
				<div class="col-md-3" style="position: relative; height: 100px;">
					<img src="/static/img/logos/logo_incesal.jpg" style="width: 120px; height: 50px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0;" />
				</div>
			</div>
		</div>
		<div style="clear:both;"></div>
	</div>
	<div class="col-md-4" role="main">
		<div class="page-header">
			<h3 style="color:#1F4C74">Localização</h1>
		</div>
		<div style="text-align: justify;">
			Rua Cel. Dulcídio, 588 - Batel - OrbitCity Coworking - Sala 3<br>
			Curitiba - PR - 80420-170
		</div>
		<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3602.9802659300663!2d-49.28587408449964!3d-25.438923283784302!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x94dce475ee27164d%3A0x27be4d1a832013f5!2sOrbitCity+Coworking!5e0!3m2!1sen!2sbr!4v1494609250896" width="350" height="350" frameborder="0" style="border:0" allowfullscreen></iframe>
	</div>
	</div>
</div>
EOT;
// Footer
$footerJS .= <<<EOT
});
EOT;
include('requisicoes/footer.php');