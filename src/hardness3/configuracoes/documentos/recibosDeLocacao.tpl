<style type="text/css">
.generalContainerTable{
	float:left;
	width:100%;
}
.tableLocacaoBase{
	border:1px solid #000;
	width:900px; 
	margin:5px auto;
	border-collapse:collapse;
}
.tableLocacaoBase td {
	border:1px solid #000;
	padding: 3px;
}
.tableLocacaoBase .D007Banco{
	text-align:center;
	width:100%;
	height:45px;
	font-size:14px;
}
.tableLocacaoBase .D024ClienteFornecedor{
	margin-top:10px;
	text-align:center;
	width:100%;
	height:40px;
	font-size:11px;
}
.tableLocacaoBase .logo{
	margin-left:23px;
}
.tableLocacaoBase .logo table tr td{
	border:none;
}
.titleTh{
	margin:0 auto;
	font-size:22px;
	padding:5px;
}
.alignRight{
	text-align:right;
}
.alignRight label{
	font-size:20px; 
	font-weight:bold;
}
.prefixoTable3{
	text-align:right;
	font-size:14px;
}
.sufixoTable3{
	font-size:18px;
	margin-left:20px;
}
.titleTotal{
	text-align:right;
	font-weight:bold;
	font-size:16px;
}
.valorTotal{
	text-align:right;
	font-weight:bold;
	font-size:16px;
}
.alignCenter{
	text-align:center;
}
.alignRight{
	text-align:right;
}
.footerGrid{
	margin:0 auto;
}
.footerDataAssinatura label{
	margin-top:5px;
}
</style>
<div class="recibosDeLocacao">
	<div class="generalContainerTable">
				<div class="contentTableLocacao">
					<table class="tableLocacaoBase">
						<tr height="110px">
							<td width="400px">
								<div class="logo">
									<span>{$cabecalho}</span>
								</div>
							</td>
							<td valign="top">
								<span>{$banco}</span>
								<span>{$dadosEmpresaCabecalho}</span>
							</td>
						</tr>
					</table>
				</div>
				<div class="contentTableLocacao">
					<span>{$emissaoANDNum}</span>
				</div>
				<div class="contentTableLocacao">
					<table class="tableLocacaoBase">
						<th class="titleTh" colspan=3>
							<span>{$textoTitle}</span>
						</th>
						<span>{$tableEmpresa}</span>
					<span>{$corpoDcto}</span>
					<hr / width="900">
					<span>{$rodape}</span>
				</div>
			</div>
</div>