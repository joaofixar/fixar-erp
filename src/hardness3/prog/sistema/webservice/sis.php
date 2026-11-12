<?php
namespace hardness;     

switch ($g['r'][3]) {
	case 'send':
		require ('bibliotecas/webservices/sis/sis.class.exportacao.php');
		$exportar = new Exportacao( 'sis' );
		
		echo <<<EOT
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Hardness</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>
	<body>
EOT;

file_put_contents('/tmp/sisUltimoPOST', serialize($_POST));
//$_POST = unserialize(file_get_contents('/tmp/sisUltimoPOST'));
if( $_POST )
	$exportar->comeco( $_POST );
	
echo <<<EOT
	</body>
</html>
EOT;
	break;
}
