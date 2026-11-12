<?php 

echo "<table border=0 width=100% height=60%>";
	echo "<tr>";
		echo "<td align=center>";
			echo "<table border=0 width=500 height=100 bgcolor=#EBE9E0 cellpadding=0 cellspacing=1  style='border: 1px solid;border-color:  white #B0B0B0 #B0B0B0 white;'>";
				echo "<tr>";
					echo "<td align=center>";
						echo "<img src='../imagens/gif/proibido.gif'>";
					echo "</td>";
					echo "<td align=left>";
						echo "<font style='font-family: arial, verdana, helvetica, sans-serif;font-size: 24;font-weight:bold;'>Acesso negado para o usuário:$servidor - $host,$user,$pass);</font>";
						echo "<br>";
						echo "<br>";
						echo "<font style='font-family: arial, verdana, helvetica, sans-serif;font-size: 16;font-weight:bold;'>$Nome_Usuario</font>";
					echo "</td>";
				echo "</tr>";
			echo "</table>";
		echo "</td>";
	echo "</tr>";
echo "</table>";
exit;

?>