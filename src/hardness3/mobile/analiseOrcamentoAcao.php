<?php
namespace hardness;
include('inc.header.php');

$T003_Id = $_REQUEST['T003_Id'];
$acao = $_REQUEST['acao'];

if (isset($_REQUEST['motivo'])) {
	$T003_Liberou_Alcada_Observacao_Nao_Liberado = $_REQUEST['motivo'];
	$T003_Liberou_Alcada_Observacao_Nao_Liberado=trim($T003_Liberou_Alcada_Observacao_Nao_Liberado);
	$T003_Liberou_Alcada_Observacao_Nao_Liberado=addslashes($T003_Liberou_Alcada_Observacao_Nao_Liberado);
	$T003_Liberou_Alcada_Observacao_Nao_Liberado=strtoupper($T003_Liberou_Alcada_Observacao_Nao_Liberado);
	$cSql=mysqli_query("update T003 set T003_Liberou_Alcada_C007_Id='$Usuario_Atual',
                                      T003_Liberou_Alcada_Data=now(),
                                      T003_Liberou_Alcada_IPV=0,
                                      T003_Liberou_Alcada_Observacao_Nao_Liberado='$T003_Liberou_Alcada_Observacao_Nao_Liberado',
                                      T003_Flag_Status_Liberacao='3'
                                where T003_Id='$T003_Id'");
    echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			Hardness.showToast('Orçamento Não Liberado!');
			window.location = 'analiseOrcamento.php';
		} </script></div>
EOT;
	die();
} else if ($acao == 'A') {
	// Aprovar
	$cSql = mysqli_query("update T003 set T003_Liberou_Alcada_C007_Id='$Usuario_Atual',
                                      T003_Liberou_Alcada_Data=now(),
                                      T003_Liberou_Alcada_IPV=T003_IPV,
                                      T003_Flag_Status_Liberacao='2'
                                where T003_Id='$T003_Id'");
	echo <<<EOT
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
			Hardness.showToast('Orçamento Liberado!');
			window.location = 'analiseOrcamento.php';
		} </script></div>
EOT;
	die();
} else {
	// Negar
	echo <<<EOT
		<div style="padding: 5px; margin-top: 5px; clear: both;">
			<button class="ui-corner-all" onClick="Hardness.browserBack()" style="width: 48%; float: left;" />Voltar</button>
			<button class="ui-corner-all" onClick="Hardness.exit()" style="width: 48%; float: right;" />Sair</button>
		</div>
		Motivo:<br />
		<textarea name='T003_Liberou_Alcada_Observacao_Nao_Liberado' id='T003_Liberou_Alcada_Observacao_Nao_Liberado' rows="6" style='text-transform:uppercase; width: 100%; margin-left: 3%;'></textarea>
		<div style="padding: 5px; margin-top: 5px; clear: both;">
			<button class="ui-corner-all" id="negarOrcamento">Negar</button>
		</div>
		<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {		
			$('#negarOrcamento').click(function() {
				if ($('#T003_Liberou_Alcada_Observacao_Nao_Liberado').val() == '') {
					Hardness.showToast('Informe o motivo por favor!');
				} else {
					window.location = 'analiseOrcamentoAcao.php?acao=N&T003_Id={$T003_Id}&motivo=' + encodeURIComponent($('#T003_Liberou_Alcada_Observacao_Nao_Liberado').val());
				}
			});
		} </script></div>
EOT;
}
include('inc.footer.php');