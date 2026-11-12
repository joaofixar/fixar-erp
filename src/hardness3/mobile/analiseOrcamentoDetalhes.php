<?php
namespace hardness;
include('inc.header.php');

$T003_Id = $_REQUEST['T003_Id'];

$C007 = mysqli_query("select * from C007 where C007_Id='$Usuario_Atual'");
$mC007 = mysql_fetch_array($C007);

$cClausula = <<<EOT
	select
    D024_Nome_Empresa, 
    D024_Nome_Fantasia, 
    Vendedor.C007_Primeiro_Nome Vendedor, 
    Supervisor.C007_Primeiro_Nome Supervisor, 
    D024_Id, 
    T003_Id, 
    T003_IPV, 
    T003_Valor_Total, 
    T003_Observacao_Comercial, 
    group_concat(distinct T006_T005_Id) Pedido, 
    group_concat(distinct T007_Numero_Nota_Fiscal) Nota_Fiscal, 
    T003_Data_Emissao, 
    T007_Numero_Nota_Fiscal, 
    T005_Status_Pedido(T005_Flag_Status,1) T005_Status, 
    if(T003_Flag_ACP='1','NF',if(T003_Flag_ACP='2','Romaneio',if(T003_Flag_ACP='3','ACP',if(T003_Flag_ACP='5','D1',if(T003_Flag_ACP='6','NFe','CF'))))) Tipo_Documento 
    from T003 
    left join D024 on D024_Id=T003_D024_Id
    left join T004 on T004_T003_Id=T003_Id
    left join T006 on T006_T004_Id=T004_Id
    left join T005 on T005_Id=T006_T005_Id
    left join T007 on T007_T005_Id=T005_Id
    left join C007 as Vendedor on Vendedor.C007_Id=T003_C007_Id_Vendedor_Interno 
    left join C007 as Supervisor on Supervisor.C007_Id=Vendedor.C007_C007_Id 
    where T003_Id='{$T003_Id}'
    and T003_Flag_Perdido!='S'
    and T003_Flag_Status_Liberacao='1'
    group by T003_Id
EOT;
$T003 = mysqli_query($cClausula);
echo <<<EOT
	<div style="padding: 5px; margin-top: 5px; clear: both;">
		<button class="ui-corner-all" onClick="Hardness.browserBack()" style="width: 48%; float: left;" />Voltar</button>
		<button class="ui-corner-all" onClick="Hardness.exit()" style="width: 48%; float: right;" />Sair</button>
	</div>
	<table class="tableS2">
EOT;
while ($mT003 = mysql_fetch_array($T003)) {
	$dataEmissao = gCorrigeData($mT003['T003_Data_Emissao']);
	$nomeFantasia = substr($mT003['D024_Nome_Fantasia'],0,20);
	$valorTotal = number_format($mT003['T003_Valor_Total'],2,',','.');
	$ipv = number_format($mT003['T003_IPV'],4,',','.');
	echo <<<EOT
		<tr>
			<td style="width:1%;">Emissão:</td>
			<td>{$dataEmissao}</td>
		</tr>
		<tr>
			<td style="width:1%;">Número:</td>
			<td>{$mT003['T003_Id']}</td>
		</tr>
		<tr>
			<td style="width:1%;">Cliente:</td>
			<td>{$nomeFantasia}</td>
		</tr>
		<tr>
			<td style="width:1%;">Doc:</td>
			<td>{$mT003['Tipo_Documento']}</td>
		</tr>
		<tr>
			<td style="width:1%;">Valor:</td>
			<td>{$valorTotal}</td>
		</tr>
		<tr>
			<td style="width:1%;">IPV:</td>
			<td>{$ipv}</td>
		</tr>
		<tr>
			<td style="width:1%;">Vendedor:</td>
			<td>{$mT003['Vendedor']}</td>
		</tr>
		<tr>
			<td style="width:1%;">Supervisor:</td>
			<td>{$mT003['Supervisor']}</td>
		</tr>
		<tr>
			<td style="width:1%;">Observacao:</td>
			<td>{$mT003['T003_Observacao_Comercial']}</td>
		</tr>
EOT;
}
echo <<<EOT
    </table>
    <div style="padding: 5px; margin-top: 5px; clear: both;">
		<button class="ui-corner-all" onClick="Hardness.browserDialogYesNo('analiseOrcamentoAcao.php?acao=A&T003_Id={$T003_Id}');" style="width: 48%; float: left;" />Aprovar</button>
		<button class="ui-corner-all" onClick="Hardness.browserDialogYesNo('analiseOrcamentoAcao.php?acao=N&T003_Id={$T003_Id}');" style="width: 48%; float: right;" />Negar</button>
	</div>
EOT;

echo <<<EOT
	<div class="divHidden"><script type="text/javascript"> if (typeof jQuery != 'undefined') {
		var grid = $('.tableS1');
		grid.find('tr').removeClass('color1');
		grid.find('tr:odd').addClass('color1');
	} </script></div>
EOT;
include('inc.footer.php');