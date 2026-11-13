<?php
namespace hardness;

while($row = mysql_fetch_array($resultado)){
	echo <<<EOT
<html><body><head><meta charset="utf-8"><link rel="stylesheet" type="text/css" href="suporte.css"></head>
<table bgcolor="#C0C0C0" border="0" width="780px" height="1024px" cellspacing="0" cellpadding="0" >

	<tr>
		<td><p align="center" ><font color="#0102ff"><b>CÓDIGO DO PRODUTO</b></p></td>
	</tr>
	<tr>
		<td><p align="center">{$row["D001_Codigo_Produto"]}</p></td>
	</tr>
	<tr>
		<td><p align="center"><font color="#0102ff"><b>DESCRIÇÃO DO PRODUTO</b></p></td>
	</tr>
	<tr>
		<td><p align="center">{$row["D001_Descricao_Produto"]}</p></td>
	</tr>
	<tr>
		<td><p align="center"><font color="#0102ff"><b>QUANTIDADE EM ESTOQUE</b></p></td>
	</tr>
	<tr>
		<td><p align="center">{$row["D009_Quantidade_Estoque"]}</p></td>
	</tr>
	<tr>
		<td><p align="center"><font color="#0102ff"><b>LOCAL</b></p></td>
	</tr>
	<tr>
		<td><p align="center">{$row["D004_Local"]}</p></td>
	</tr>

	<form action="balanco.php" method="POST" name="form">
		<tr>
			<td><p align="center"><input type="text" name="quantidade"  style="height: 100px; font-size: 50px;"/></td>
		</tr> 

	<form action="balanco.php" method="POST" >
		<input type="hidden" name="codProduto" value="{$row["D001_Codigo_Produto"]}" />
		<tr>
			<td>
						<p align="center"><input type="submit" value="ENTRADA" name="somar" bgcolor="red" style="margin-left:10px; height: 120px;  font-weight: bold; font-size: 50px;  background-color: #009900">
						<input type="submit" value="BALANÇO" name="balanco"  style="margin-left:10px; height: 120px; font-weight: bold;  font-size: 50px;  background-color: blue">
						<input type="submit" value="SAÍDA" name="subtrair" nm style="margin-left:10px; height: 120px; font-weight: bold;  font-size: 50px;  background-color: red">
			</td>
		</tr>
	<script language="JavaScript" type="text/javascript">
			document.form.quantidade.focus();
	</script>			
	</form> 
	</form>

	<form method="POST" action=""> 
		<tr>
			<td>
				<p align="left"><a href=""><input type="button" value="VOLTAR" onclick="javascript:history.back(-1).reload()" name="voltar"  style="margin-left:70px; height: 120px; width:250px; font-weight: bold;  font-size: 50px;  background-color: yellow;"></a>
		
	</form>

	<form action="logout.php" method="POST">
				<p align="center"><a href="prin.php"><input type="submit" value="SAIR" name="SAIR"  style=" margin-left:300px; margin-top:-140px;  width:250px; height: 120px;  font-weight: bold;  font-size: 50px;  background-color: yellow" ></p>
			</td>	
		</tr>
	</form>



</table>
EOT;

}
