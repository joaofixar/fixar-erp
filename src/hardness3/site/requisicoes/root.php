<?php
// Header
$headerHeadTitle = 'Hardness Sistemas';
include('requisicoes/header.php');

// Body
echo <<<EOT
<div class="board">
	<img src="/static/img/intro.jpg" class="card img-responsive" />
	<div class="intro">
		<div class="container">
			<div class="row">
				<div id="introtext" class="col-md-5">
					<h1 style="margin: 0;">Gerencie a sua empresa!</h1>
					<h3 style="margin-top: 2px;">ERP completo, simples e fácil.</h3>
					<a href="/conheca/"><button type="button" class="btn btn-lg btn-success">Saiba Mais</button></a>
			<!--		<br /><br /><br />
					<h3 style="margin: 0;">Precisando apenas emitir suas notas?</h3>
					<h3 style="margin-top: 2px;">Emissor NF-e / NFC-e</h3>
					<a href="/cadastro/"><button type="button" class="btn btn-lg btn-success">Comece já!</button></a> -->
				</div>
			</div>
		</div>
	</div>
</div>
<div class="endboard"></div>
<div style="padding-bottom: 20px;"> <!-- border-bottom: 1px solid #DDDDDD; -->
	<div class="container" style="margin-top: 20px;">
		<div style="width: 100%; float: left;">
			<p style="font-size: 24px;">Alguns dos nossos clientes:</p>
			<div class="col-md-1" style="position: relative; height: 100px;"></div>
			<div class="col-md-2" style="position: relative; height: 100px;">
				<img src="/static/img/logos/logo_incesal.jpg" style="width: 120px; height: 50px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0;" />
			</div>
			<div class="col-md-2" style="position: relative; height: 100px;">
				<img src="/static/img/logos/logo_incavel.jpg" style="width: 150px; height: 60px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0" />
			</div>
			<div class="col-md-2" style="position: relative; height: 100px;">
				<img src="/static/img/logos/logo_sulmatel.jpg" style="width: 100px; height: 70px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0; margin-left: 17%" />
			</div>
			<div class="col-md-2" style="position: relative; height: 100px;">
				<img src="/static/img/logos/logo_royal.jpg" style="width: 120px; height: 100px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0" />
			</div>
			<div class="col-md-2" style="position: relative; height: 100px;">
				<img src="/static/img/logos/logo_crv.jpg" style="width: 150px; height: 70px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0" />
			</div>
			<div class="col-md-1" style="position: relative; height: 100px;"></div>
		</div>
		<div style="width: 100%; float: left;">
			<div class="col-md-1" style="position: relative; height: 100px;"></div>
			<div class="col-md-2" style="position: relative; height: 100px;">
				<img src="/static/img/logos/logo_alumifix.jpg" style="width: 120px; height: 50px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0;" />
			</div>
			<div class="col-md-2" style="position: relative; height: 100px;">
				<img src="/static/img/logos/logo_bigvel.jpg" style="width: 150px; height: 80px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0" />
			</div>
			<div class="col-md-2" style="position: relative; height: 100px;">
				<img src="/static/img/logos/logo_lacombe.jpg" style="width: 100px; height: 70px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0; margin-left: 17%" />
			</div>
			<div class="col-md-2" style="position: relative; height: 100px;">
				<img src="/static/img/logos/logo_nucleodot.jpg" style="width: 120px; height: 90px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0" />
			</div>
			<div class="col-md-2" style="position: relative; height: 100px;">
				<img src="/static/img/logos/logo_alumisistem.jpg" style="width: 150px; height: 80px; background:#eee; -webkit-box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); box-shadow:0 0px 4px rgba(0, 0, 0, 0.2); position: absolute; bottom: 0" />
			</div>
			<div class="col-md-1" style="position: relative; height: 100px;"></div>
		</div>
	</div>
	<div style="clear:both;"></div>
</div>
EOT;

// Footer
$footerJS = <<<EOT
EOT;
include('requisicoes/footer.php');