<?php
namespace hardness;

echo <<<'EOT'
<html>
	<head><link rel="stylesheet" type="text/css" href="suporte.css"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head> 
		<body>
		
			<div id="logar">

				<table align="center" >
				<form action="validarLoginPorUser.php" method="POST" name="form">
					<tr><td><p align="center" style="font-size:50px; margin-top:200px;">Login<br><input type="text" name="login"  style="height: 80px;  font-size: 50px;"/></td></tr>
					<tr><td><p align="center" style="font-size:50px;">Senha<br><input type="password" name="senha"  style="height: 80px;  font-size: 50px;"/></td></tr>
					<div id="enviar"><br><tr><td><p align="center"><input type="submit" value="ENTRAR" style="height: 100px; width:300px; font-size:40px; margin-top:45px;"></p></tr></td></div>
					<div id="enviar2"><br><tr><td><p align="center"><a href=""><input type="button" onclick="javascript:history.back(-1)" value="VOLTAR" style="height: 100px; width:300px; font-size:40px; margin-top:45px;"></a></p></tr></td></div>
				</form>
				<script language="JavaScript" type="text/javascript">
  				 	document.form.login.focus();
				</script>	
				</table>
			</div>
		</body>
</html>
EOT;
