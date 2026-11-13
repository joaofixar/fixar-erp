<?php
namespace hardness;

// confs
require_once("../bibliotecas/confUsuario.php");
//$confUsuario['dbDatabase'] = 'e009';
$g['pathRaiz'] = $confUsuario['pathRaiz'];
chdir($g['pathRaiz']);
$Usuario_Atual = 63;
$Empresa_Atual = 1;

// mysql
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) {
	echo $Error;
	die();
}
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysql_set_charset('utf8');
mysqli_query('SET NAMES utf8;');
$g['conexaoBanco'] = $con;
require_once('bibliotecas/funcoesGerais.php');


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">
<html>
	<head>
	    <title>Hardness Sistemas</title>
	    <meta name="viewport" content="width=device-width" />
	    <script src="/hardness3/static/js/jquery-1.6.2.min.js"></script>
	    <script type="text/javascript">
		    function showAndroidToast(toast) {
		        Hardness.showToast(toast);
		    }
		</script>
		<style>
			body {
				font-size: 20px;
				margin: 0;
				padding: 0;
				background: none repeat scroll 0 0 #B5B59B;
				width: 102%;
				color: #363636;
			}
			.header {
				height: 21px;
				background-color: #1C1C1C;
				color: #F15928;
				padding: 3px 5px;
				font-size: 17px;
			}
			input, button {
				font-size: 24px;
				text-transform: uppercase;
				width: 100%;
				margin-bottom: 5px;
				height: 2em;
			}
			button {
				background: url("/hardness3/static/css/temas/padrao/images/ui-bg_glass_85_dfeffc_1x400.png") repeat-x scroll 50% 50% #DFEFFC;
			    border: 1px solid #C5DBEC;
			    color: #2E6E9E;
			    font-weight: bold;
			}
			a {
				text-decoration: none;
			}
			/* Table S1 */
			.tableS1 {
				width: 100%;
				padding: 0;
				margin: 0;
				background: white;
				clear: both;
			}
			.tableS1 th {
				text-transform: uppercase;
				padding-left: 5px;
				color: white;
				white-space: nowrap;
				background: none repeat scroll 0 0 #70A8D2;
			}
			.tableS1 td {
				text-align: center;
				padding: 20px 1px;
				font-size: 17px;
			}
			/* Table S2 */
			.tableS2 {
				width: 100%;
				padding: 0;
				margin: 0;
				background: white;
				font-size: 16px;
				clear: both;
			}
			.color1 {
				background: none repeat scroll 0 0 #E2EDF3;
			}
			/* Corner radius */
			.ui-corner-all, .ui-corner-top, .ui-corner-left, .ui-corner-tl { -moz-border-radius-topleft: 5px; list-style-type:none; -webkit-border-top-left-radius: 5px; -khtml-border-top-left-radius: 5px; border-top-left-radius: 5px; }
			.ui-corner-all, .ui-corner-top, .ui-corner-right, .ui-corner-tr { -moz-border-radius-topright: 5px; -webkit-border-top-right-radius: 5px; -khtml-border-top-right-radius: 5px; border-top-right-radius: 5px; }
			.ui-corner-all, .ui-corner-bottom, .ui-corner-left, .ui-corner-bl { -moz-border-radius-bottomleft: 5px; -webkit-border-bottom-left-radius: 5px; -khtml-border-bottom-left-radius: 5px; border-bottom-left-radius: 5px; }
			.ui-corner-all, .ui-corner-bottom, .ui-corner-right, .ui-corner-br { -moz-border-radius-bottomright: 5px; -webkit-border-bottom-right-radius: 5px; -khtml-border-bottom-right-radius: 5px; border-bottom-right-radius: 5px; }
		</style>
	</head>
	<body>
	<div class="header">
		<div style="float: left;"><span style="font-weight: bold;">HARDNESS</span> <span style="color: #FFFFFF;">SISTEMAS</span></div>
		<div style="float: right;"><span style="color: #FFFFFF; font-weight: bold;">MOBILE</span></div>
	</div>