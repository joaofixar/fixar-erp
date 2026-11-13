<?php
namespace hardness;

include('sessao.php');

$alerta = (isset($_REQUEST['incorreto'])) ? '<div style="text-align: center; color: red; font-size: 2em; font-weight: bold;">Valor Incorreto!</div>' : '';

echo <<<EOT
<html>
<head><meta charset="utf-8"><link rel="stylesheet" type="text/css" href="suporte.css"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head> 

<body>
	<div id="banner">			
		<form action="query.php" method="POST" name="form">
			<table width="100%" border="0" cellspacing="0" cellpadding="0" >
				<tr><td><h2><p align="center" font size="7" style="margin-top:200px;"><big><big><big>SCANEAR</big></big></big></p></td></tr>
				<tr><td><h2><p align="center"><big><big><big>CÓDIGO OU</big></big></big></p></td></tr>
				<tr><td><h2><p align="center"><big><big><big>LOCAÇÃO</big></big></big> </p></td></tr>
				<tr><td><h2><p align="center">{$alerta}</td></tr>
				<tr><td><h2><p align="center"><input type="text" name="codigo"  style="height: 100px;  font-size: 50px;"/></p></td></tr><br><br>
				<div id="enviar"><br><tr><td><p align="center"><input type="submit" value="BUSCAR" style="height: 100px; font-weight: bold;  width:300px; font-size:40px; margin-top:45px;"></p></tr></td></div>
			</table>
			<script language="JavaScript" type="text/javascript">
  				document.form.codigo.focus();
			</script>

		</form>
		<form action="logout.php" method="POST">
			<a href="index.php"><input type="submit" value="SAIR" name="SAIR"  style=" margin-left:230px; margin-top:50px; width:300px; height: 100px;   font-weight: bold;  font-size: 50px;  background-color: yellow" ></a></p></td></tr>
		</form>

	</div>
</body>
</html>
EOT;
