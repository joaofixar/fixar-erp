<style type="text/css">
	.certificadoDeConformidade{
		width:990px;
		text-align:center;
		float:left;
		float:left;
	}
	.certificadoDeConformidade .centerContainer{
		margin:0 auto;
		width:900px;
	}
	.certificadoDeConformidade .center {
		text-align:center; 
		margin-top: 5px;
	}
	.certificadoDeConformidade .center .tableLogo{
		margin:0 auto;
	}
	.certificadoDeConformidade .center .title{
		font-size:18px;
		font-weight:bold;
		text-decoration:underline;
		margin-top:20px;
	}
	.certificadoDeConformidade .paragrafo {
		text-align: justify;
		margin-bottom: 20px;
		margin-top:20px;
		font-size:12px;
	}
	.certificadoDeConformidade .paragrafo .texto{
		margin:0 auto;
	}
	.certificadoDeConformidade .checklist {
		text-align:center;
		margin-top: 10px;
		float:left;
	}
	.certificadoDeConformidade .checklist .containerCheckList{
		width:100%;
		float:left;
	}
	.certificadoDeConformidade .checklist .containerCheckList table{
		width:900px;
	}
	.certificadoDeConformidade .checklist .containerCheckList .first{
		border:1px solid #000;
		border-collapse:collapse;
		font-weight:bold;
	}
	.certificadoDeConformidade .checklist .containerCheckList table tr td{
		border:1px solid #000;
	}
	.certificadoDeConformidade .containerSpecify{
		width:100%;
		float:left;
	}
	.certificadoDeConformidade .containerSpecify .containerTable{
		float:left;
		width:900px;
		margin-bottom:10px;
	}
	.certificadoDeConformidade .containerSpecify .containerTable table{
		text-align:left;
		width:100%;
	}
	.certificadoDeConformidade .containerSpecify .containerTable table td{
		border:1px solid #000;
	}
	.certificadoDeConformidade .containerSpecify .containerTable table .tdRight{
		text-align:right;
		font-weight:bold;
		font-size:12px;
	}
	.certificadoDeConformidade .containerSpecify .space{
		width:20px;
		float:left;
	}
	.certificadoDeConformidade .containerSpecify .subTitleSpecify{
		font-size:11px;
	}
	.certificadoDeConformidade .containerSpecify .subTitleSpecify table{
		margin:0 auto;
		min-width:600px;
	}
	.certificadoDeConformidade .containerSpecify .subTitleSpecify table .tdRight{
		text-align:right;
		font-weight:bold;
		font-size:13px;
	}
	.certificadoDeConformidade .especificacoes{
		text-align:center;
		margin-top: 10px;
		float:left;
	}
	.certificadoDeConformidade .especificacoes table{
		width:900px;
		margin:0 auto;
	}
	.certificadoDeConformidade .especificacoes table th{
		font-size:16px;
		font-weight:bold;
		text-decoration:underline;
		padding-top:0px;
	}
	.certificadoDeConformidade .especificacoes table tr td{
		border:1px solid #000;
	}
	.certificadoDeConformidade .importante{
		text-align:left;
		margin-top: 20px;
		float:left;
		font-size:12px;
	}
	.certificadoDeConformidade .importante b{
		font-size:16px;
	}
</style>
<div class="certificadoDeConformidade">
	<div class="centerContainer">
		<div class="center">
			<!-- $CAD044->getVar('CABECALHO') -->
			<span>{$cabecalho}</span>
		</div>
		<div class="center">
			<!-- $CAD044->getVar('CERT_CONFORMIDADE_TITULO') -->
			{$titulo}
		</div>
		<div class="paragrafo">
			<!-- $CAD044->getVar('CERT_CONFORMIDADE_TEXTO_BALSA') -->
			{$texto}
		</div>
		<div>
			{$especificacoes}
		</div>
		<div class="checklist">
			<!-- $CAD040->docGerarChecklist(D125_Id) -->
			{$checklist}
		</div>
		<div class="especificacoes">
			{$cilindrosGas}
		</div>
		<div class="importante">
			{$importante}
		</div>
	</div>
</div>
