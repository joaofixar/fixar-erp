<style type="text/css">
	.notaPromissoria{
		float:left;
		font-size:14px;
		width:990px;
		font-family:Arial, Helvetica, sans-serif;
	}
	.notaPromissoria .center{
		width:700px;
		margin:0 auto;
		border:1px solid #000;
		text-align:center;
		overflow:hidden;
		padding:3px;
	}
	.notaPromissoria .center .header{
		float:left;
		width:100%;
		font-size:14px;
	}
	.notaPromissoria .center .header table{
		width:100%;
		height:100px;
		border:0;
	}
	.notaPromissoria .center .header table tr td img{
		float:left;
	}
	.color{
		padding:0 10px;
		border:1px solid #000;
		-moz-border-radius:5px;
		font-size:20px;
	}
	.flutuarPreco{
		float:right;
	}
	.notaPromissoria .center .containerTop{
		width:100%;
		float:left;
		text-align:left;
	}
	.notaPromissoria .center .containerTop .recibo{
		font-size:24px;
		font-weight:bold;
	}
	.valorReais{
		float:right;
		font-size:18px;
		margin-top:-10px;
	}
	.notaPromissoria .center .centerContainer{
		float:left;
		width:100%;
		margin-top:10px;
	}
	.notaPromissoria .center .centerContainer table{
		width:100%;
	}
	.notaPromissoria .center .centerContainer .containerFooter{
		width:100%;
		margin-top:20px;
	}
	.notaPromissoria .center .centerContainer .containerFooter .data{
		float:left;
		width:49%;
		text-align:left;
		margin-top:15px;
	}
	.notaPromissoria .center .centerContainer .containerFooter .assinatura{
		float:left;
		width:49%;
		text-align:center;
	}
</style>
<div class="notaPromissoria">
	<div class="center">
		<div class="header">
			{$header}
		</div>
		<div class="containerTop">
			{$containerTop}
		</div>
		<div class="centerContainer">
			{$containerCenter}
		</div>
	</div>
</div>
