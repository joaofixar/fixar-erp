<?php
isset($footerJS) or ($footerJS = '');
$htmlMemoriza = <<<EOT
<select id="campoOpcaoSalvarDados" class="form-control input-sm input-group-addon" style="width: 185px;float: left;border: 1px solid #999; margin-bottom: 10px;" onchange="setOpcaoSalvarDados(this); return false;"><option value="login">Memorizar login</option><option value="senha">Memorizar login e senha</option><option value="nunca">Nunca memorizar</option></select><button type="button" class="btn btn-success input-group-addon confirmAction" style="padding-right: 8px; padding-left: 8px;float: left;border: 1px solid #999;background-color: #58BF58;border-left: 0; width: 32px; height: 30px;"><span class="glyphicon glyphicon-ok" style="padding: -10px;"></span></button>
EOT;
$htmlMemoriza = str_replace("\n", " ", $htmlMemoriza);
$accountType = !isset($_COOKIE['accountType']) ? '' : $_COOKIE['accountType'];
$lastId = !isset($_COOKIE['lastId']) ? '' : $_COOKIE['lastId'];
echo <<<EOT
<div class="push"></div>
</div>
<div class="footer">
	<div class="container" style="padding-top: 20px; font-size: 90%;">
		<div class="row">
			<div class="col-md-4">
				<h4>Site</h4>
				<p>
					<a style="color: #999;" href="/">Home</a><br />
					<a style="color: #999;" href="/empresa">Empresa</a><br />
					<a style="color: #999;" href="/conheca">ERP</a><br />
					<!--<a style="color: #999;" href="/cadastro">NF-e / NFC-e</a><br />-->
					<a style="color: #999;" href="/contato">Contato</a><br />
				</p>
			</div>
			<div class="col-md-4" style="text-decoration: none;">
				<h4>Soluções</h4>
				<p>
					<a style="color: #999;" href="/conheca/#nota">Nota fiscal</a><br />
					<a style="color: #999;" href="/conheca/#vendas">Vendas</a><br />
					<a style="color: #999;" href="/conheca/#estoque">Estoque</a><br />
					<a style="color: #999;" href="/conheca/#compras">Compras</a><br />
					<a style="color: #999;" href="/conheca/#financeiro">Financeiro</a><br />
					<a style="color: #999;" href="/conheca/#gerencial">Gerencial</a><br />
				</p>
			</div>
			<div class="col-md-4">
				<h4>Contato</h4>
				<p style="color: #999;">
					<b>suporte@hardness.com.br</b><br /><br />
					41 <font style='font-size:22px'>3344-2938</font><br />
					(Seg à sex 8:30h às 12h e 13h às 18h)
				</p>			
			</div>
		</div>
		<div class="row">
			<div class="col-md-12" style="text-align:center; font-size: 14px; font-weight: bold;">
				© Hardness Sistemas
			</div>
		</div>
	</div>
</div>
<!-- Placed at the end of the document so the pages load faster -->
<script src="/static/jquery/jquery-1.10.2.min.js"></script>
<script src="/static/js/site.js"></script>
<script src="/static/bootstrap/js/scrollspy_mod.js"></script>
<script src="/static/bootstrap/js/bootstrap.js"></script>
<script src="/static/js/lightbox-2.6.min.js"></script>
<script src="/static/video-js/video.js"></script>
<script src="/static/js/md5.js"></script>
<script type="text/javascript">
	{$footerJS}
	function loginNOTA() {
		$('#form-login #entrar').attr('disabled', 'disabled');
		if('{$_COOKIE['opcaoSalvarDados']}' == '' && $('.inputLogin').val() != ''){
			var combo = '{$htmlMemoriza}';

			$('.opcaoSalvarDados').val('login');
			$('#entrar').popover({
			    html: true,
			    placement: 'bottom',
			    title: 'Deseja memorizar os dados?',
			    content: combo
			});
			$('#entrar').popover('show');
			$('.popover').css('width', '252px');
			$('.popover').addClass('input-group');
			
			var screenWidth = parseFloat($('body').css('width'));
			var popoverLeft = $('.popover').offset().left;
			var popoverLeftRel = parseFloat($('.popover').position().left); 
			var popoverWidth = parseFloat($('.popover').css('width'));
			
			if(popoverLeft+popoverWidth > screenWidth){
				var arrowLeft = $('.popover').find('.arrow').position().left;
				$('.popover').css('left', popoverLeftRel - ((popoverLeft+popoverWidth)-screenWidth+10));
				$('.popover').find('.arrow').css('left', arrowLeft)
			}

			$('body').on('click', function (event) {
		    	if (!$(event.target).closest('.popover').size() && $(event.target).attr('id') != 'entrar') {
					$('.popover').prev().popover('destroy').data('clicked', false).data('show', false);
					$('#form-login #entrar').removeAttr('disabled').html('Entrar');
				}
			});			
			//$('#entrar').popover('show');
			if($('.tipoConta').val() == 'novo'){
				$("#campoOpcaoSalvarDados option[value=senha]").hide();
			} else {
				$("#campoOpcaoSalvarDados option[value=senha]").show();
			}
		} else {
			requestLogin();
		}
	}

	function requestLogin(){
		$.ajax({
			url: '/login/',
			dataType: 'jsonp',
			type: 'POST',
			cache: false,
			data: $("#form-login").serialize(),
			success: function(request) {
				if (request.code == 'restricao') {
					eval(request.data);
				} else if (request.code) {
					$('<div class="requestData"></div>').html(request.data).appendTo('body');
					$('#form-login #entrar').removeAttr('disabled').html('Entrar');
					$('#form_acesso').submit();
				} else {
					$('#form-login #entrar').removeAttr('disabled').html('Entrar');
					$('.alertaLogin').remove();
					$('body').append('<div class="alert alert-danger alertaLogin" role="alert" style="position: fixed; top:0; bottom: 0; left: 0; right: 0; margin: auto; margin-top: 70px; width: 300px; overflow: auto; display: table; z-index: 10000;"><button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>' + request.data + '</div>');
				}
			}
		});
	}

	function verificaTipoConta(elem) {
		var login = window.md5($(elem).val());
		if('{$accountType}' == '0' && '{$lastId}' == login){
			$('.campoSenha').css('display', 'inline').find('input');
			$('.tipoConta').val('antigo');
		} else if('{$accountType}' == '1' && '{$lastId}' == login){
			$('.campoSenha').css('display', 'none');
			$('.tipoConta').val('novo');
		} else if($(elem).val().length == 14 || $(elem).val().length == 11){
			$.ajax({
				url: '/ajax/?funcao=verificaTipoConta',
				dataType: 'jsonp',
				type: 'POST',
				cache: false,
				data: {Cnpj: $(elem).val() },
				success: function(request) {
					if(request.code) {
						$('.campoSenha').css('display', 'none');
						$('.tipoConta').val('novo');
					} else {
						$('.campoSenha').css('display', 'inline').find('input');
						$('.tipoConta').val('antigo');
					}
				}
			});
		} else {
			$('.campoSenha').css('display', 'none');
			$('.tipoConta').val('novo');
		}
	}

	function setOpcaoSalvarDados(elem){
		$('#opcaoSalvarDados').val($(elem).val());
	}

	$(document).on("click", ".confirmAction", function() {
	    requestLogin();
	});

	function limparLoginSenha(){
		$.ajax({
			url: '/ajax/?funcao=limparLoginSenha',
			dataType: 'jsonp',
			type: 'POST',
			cache: false,
			data: $("#form-login").serialize(),
			success: function(request) {
				if (request.code) {
					$('body').append('<div class="alert alert-success" role="alert" style="position: fixed; top:0; bottom: 0; left: 0; right: 0; margin: auto; margin-top: 100px; width: 200px; height: 50px; z-index: 9999;"><button type="button" class="close" data-dismiss="alert" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>Login e senha limpos!</div>');
					setTimeout(function(){
						window.location = '/';
					}, 3000);
				}
			}
		});
	}
</script>
</body>
</html>
EOT;

