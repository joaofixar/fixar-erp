<?php
namespace hardness;
		
	global $g;

	$sqlC007 = mysql_query("SELECT C007_Id,C007_Key_Trello,C007_Token_Trello,C007_Nome from C007 where C007_Id={$g['usuarioAtual']}");
        $rC007 = mysql_fetch_array($sqlC007);
        $token = $rC007["C007_Token_Trello"];
        $key = $rC007["C007_Key_Trello"];

?>