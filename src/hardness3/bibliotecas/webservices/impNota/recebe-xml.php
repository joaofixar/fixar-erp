<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<title>Hardness</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
	</head>
	<body>
<?php
require('./impNota.class.exportacao.php');
$exportar = new Exportacao( 'impNota' );

if( $_POST )
	$exportar->comeco( $_POST );
?>
	</body>
</html>