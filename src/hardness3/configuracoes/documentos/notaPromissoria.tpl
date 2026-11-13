<style type="text/css">
	.notaPromissoria{
		float:left;
		font-size:12px;
		width:990px;
	}
	.notaPromissoria .center{
		width:700px;
		margin:0 auto;
		border:1px solid #000;
		text-align:center;
		overflow:hidden;
	}
	.notaPromissoria .center .title{
		float:left;
		font-size:14px;
		margin:2px;
		font-weight:bold;
		border:1px solid #000;
		padding:2px;
	}
	.notaPromissoria .center .numeroNota{
		float:right;
		font-size:14px;
		font-weight:bold;
		margin-top:5px;
		margin-right:2px;
		margin-bottom:2px;
	}
	.corFundo{
		background-color:#E8E8E8;
		padding:3px;
		-moz-border-radius:5px;
	}
	.corFundo2{
		background-color:#E8E8E8;
		padding:3px 8px;
		-moz-border-radius:5px;
		font-size:16px;
		border:1px solid #000;
		margin:2px;
	}
	.notaPromissoria .center .clear{
		clear:both;
	}
	.notaPromissoria .center .vencimento{
		float:left;
		margin:2px;
	}
	.notaPromissoria .center .valorNota{
		float:right;
		margin-top:7px;
	}
	.notaPromissoria .center .centerContainer{
		float:left;
		width:100%;
		margin:5px;
		text-align:left;
	}
	.notaPromissoria .center .centerContainer .assinatura{
		text-align:center;
		margin-top:20px;
	}

</style>
<div class="notaPromissoria">
	<div class="center">
		<div class="title">
			NOTA PROMISSÓRIA
		</div>
		<div class="numeroNota">
			{$numeroDuplicata}
		</div>
		<div class="clear"></div>
		<div class="vencimento">
			{$dataVencimento}
		</div>
		<div class="valorNota">
			{$valorPromissoria}
		</div>
		<div class="centerContainer">
			{$conteudoPromissoria}
		</div>
	</div>
</div>
