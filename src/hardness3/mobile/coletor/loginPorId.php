<?php
namespace hardness;

echo <<<'EOT'
	<html>
	<head><meta charset="utf-8"><link rel="stylesheet" type="text/css" href="suporte.css"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /></head> 
		<body>
			<div id="logar">
				<table>
					<form action="validaLoginPorId.php" method="POST" name="form">
						<div id="texto">
						<tr style="font-size:60px; font:bold; ">
							<td><p align="center" valign="middle" style="margin-top:300px;">LEIA O CÓDIGO PARA ENTRAR <br>OU FAÇA LOGIN</p></td>
						</tr>
						<tr>
							<td><input type="password" name="idUsuario" style="height:100px; width:400px; margin-top:20px; margin-left:180px; font-size:55px; "></td>
									<script language="JavaScript" type="text/javascript">
  										document.form.idUsuario.focus();
									</script>
						</tr>
						</div>
					</form>
					<form action="loginPorUser.php">
						<tr>
							<td><input type="submit" name="login" value="FAZER LOGIN" style="height:100px; width:400px; margin-top:20px; margin-left:180px; font-size:55px; "></td>
						</tr>	
					</form>				
				</table>

			</div>
		</body>
	</html>
EOT;
