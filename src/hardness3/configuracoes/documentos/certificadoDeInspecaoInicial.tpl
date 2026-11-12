<style type="text/css">
	.generalContainer{
		float:left;
		width:100%;
	}
	.centerContainer{
		width:900px;
		margin:0 auto;
	}
	.tableInspecao .alignLeft{
		text-align:left;
	}
	.tableInspecao{
		width:900px;
		margin:5px auto;
		border-collapse:collapse;
	}
	
	.tableInspecao .titulos{
		font-size:16px;
		font-weight:bold;
		text-decoration:underline;
	}
	
	.centerContainer table tr td{
		text-align:center;
	}
	.tableLogo{
		margin:0 auto;
	}
	.centerContainer table tr .alignRight{
		text-align:left;
	}
	.bottomLeft{
		float:left;
		width:300px;
	}
	.bottomLeft table{
		width:300px;
	}
	.bottomRight{
		/* 
		float:left;		
		width:600px;
		height:150px;
		*/
	}
	.bottomRight .bottomCotainerRight{
		float:right;
		margin:130px 10px 0 0;
	}
	.bottomRight .bottomCotainerRight .alignAssCenter{
		margin-left:120px;
	}
	
	.certificadoDeInspecao{
		width:990px;
		text-align:center;
		float:left;
		float:left;
	}
	.certificadoDeInspecao .centerContainer{
		margin:0 auto;
		width:900px;
	}
	.certificadoDeInspecao .center {
		text-align:center; 
		margin-top: 5px;
	}
	.certificadoDeInspecao .center .tableLogo{
		margin:0 auto;
	}
	.certificadoDeInspecao .center .title{
		font-size:18px;
		font-weight:bold;
		text-decoration:underline;
		margin-top:20px;
	}
	.certificadoDeInspecao .paragrafo {
		text-align: justify;
		margin-bottom: 20px;
		margin-top:20px;
		font-size:12px;
	}
	.certificadoDeInspecao .paragrafo .texto{
		margin:0 auto;
	}
	.certificadoDeInspecao .checklist {
		text-align:center;
		margin-top: 10px;
		float:left;
	}
	.certificadoDeInspecao .checklist .containerCheckList{
		width:100%;
		float:left;
	}
	.certificadoDeInspecao .checklist .containerCheckList table{
		width:900px;
	}
	.certificadoDeInspecao .checklist .containerCheckList .first{
		border:1px solid #000;
		border-collapse:collapse;
		font-weight:bold;
	}
	.certificadoDeInspecao .checklist .containerCheckList table tr td{
		border:1px solid #000;
	}
	.certificadoDeInspecao .containerSpecify{
		width:100%;
		float:left;
	}
	.certificadoDeInspecao .containerSpecify .containerTable{
		float:left;
		width:900px;
		margin-bottom:0px;
	}
	.certificadoDeInspecao .containerSpecify .containerTable table{
		text-align:left;
		width:100%;
	}
	.certificadoDeInspecao .containerSpecify .containerTable table td{
		border:1px solid #000;
	}
	.certificadoDeInspecao .containerSpecify .containerTable table .tdRight{
		text-align:right;
		font-weight:bold;
		font-size:12px;
		padding-right:15px;
	}
	.certificadoDeInspecao .containerSpecify .space{
		width:20px;
		float:left;
	}
	.certificadoDeInspecao .containerSpecify .subTitleSpecify{
		font-size:11px;
	}
	.certificadoDeInspecao .containerSpecify .subTitleSpecify table{
		margin:0 auto;
		min-width:600px;
	}
	.certificadoDeInspecao .containerSpecify .subTitleSpecify table .tdRight{
		text-align:right;
		font-weight:bold;
		font-size:13px;
	}
	.certificadoDeInspecao .especificacoes{
		text-align:center;
		margin-top: 10px;
		float:left;
	}
	.certificadoDeInspecao .especificacoes table{
		width:900px;
		margin:0 auto;
	}
	.certificadoDeInspecao .especificacoes table th{
		font-size:16px;
		font-weight:bold;
		text-decoration:underline;
		/*padding-top:20px;*/
	}
	.certificadoDeInspecao .especificacoes table tr td{
		border:1px solid #000;
	}
	.certificadoDeInspecao .importante{
		text-align:left;
		margin-top: 20px;
		float:left;
		font-size:12px;
	}
	.certificadoDeInspecao .importante b{
		font-size:16px;
	}	
</style>

<div class="certificadoDeInspecao">
	<div class="centerContainer">
		<table class="tableInspecao" border='1'>
			<tr>
				<td width=10%><span>{$cabecalho1}</span>
				</td>
				<td width=60%>
					{$logo}
				</td>
				<td width=10%>CERTIFICADO<br />
					<font size="3"><b>{$NumAno}</b></font>
				</td>
			</tr>
		</table>
		<table class="tableInspecao">
			<tr>
				<td>
					<font size="5"><b>{$titleCert}</b></font><br />
					<span>{$subTitle}</span>
				</td>
			</tr>
		</table>
		<span>{$tableInspecao}</span>
		<div class="especificacoes">
			<!-- $CAD040->docGerarChecklist(D125_Id) -->
			{$especificacoes}
		</div>		
		<div class="especificacoes">
			{$cilindrosGas}
		</div>
		<span>{$tableInspecao3}</span>
		<span>{$tableInspecao4}</span>
		<span>{$divFooterLeft}</span>
		<span>{$divFooterRight}</span>
	</div>
</div>
