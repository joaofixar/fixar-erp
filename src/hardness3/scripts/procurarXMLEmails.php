<?php
namespace hardness;
$confUsuario = array(
	'dbDatabase' => 'dev_e229',
	'dbHost' => '127.0.0.1',
	'dbUser' => 'root',
	'dbPass' => 'hardness',
);
$con=@mysqli_connect($confUsuario['dbHost'], $confUsuario['dbUser'], $confUsuario['dbPass'],$confUsuario['dbDatabase']);
$Error=mysqli_connect_error($con);
if (!empty($Error)) { echo $Error; die(); }
// $dbs=mysql_select_db($confUsuario['dbDatabase'], $con) or die (mysql_error());
mysqli_set_charset($con,'utf8');
mysqli_query($con,'SET NAMES utf8;');
error_reporting(E_ALL);
require_once('../sistema/comunicacaoEmail.php');

$lista = array();
$sql = "SELECT T013_Id, T013_Chave_Acesso_Nfe, T013_Numero_Protocolo_Nfe FROM T013";
$res = mysqli_query($con,$sql);
while ($row = mysqli_fetch_assoc($res)) {
	$lista[$row['T013_Chave_Acesso_Nfe']] = $row['T013_Id'];
	$lista[$row['T013_Numero_Protocolo_Nfe']] = $row['T013_Id'];
}

$cache = array();
$sql = <<<EOT
SELECT 
*
FROM T139
WHERE T139_Pasta = 'E' 
AND T139_Flag_Tem_Xml = 'S'
ORDER BY T139_Id DESC
EOT;
$res = mysqli_query($con,$sql);
$tmp = array();
while ($row = mysqli_fetch_assoc($res)) {
	$r_acaoId = $row['T139_Id'];
	$sql = "SELECT * FROM C007 WHERE C007_Id = '{$row['T139_C007_Id']}';";
	$resultado = mysqli_query($con,$sql);
	$g = array();
	$g['c007'] = $C007 = mysqli_fetch_assoc($resultado);
	$mail = new comunicacaoEmail($C007['C007_Email_POP_Servidor'], $C007['C007_Email_SMTP_Usuario'], $C007['C007_Email_SMTP_Senha'], false, $r_acaoId, $C007['C007_Email_POP_Porta']);
	//echo "{$C007['C007_Email_POP_Servidor']}, {$C007['C007_Email_SMTP_Usuario']}, {$C007['C007_Email_SMTP_Senha']}, false, {$r_acaoId}, {$C007['C007_Email_POP_Porta']}\n";
	//$mail->setarLido($r_acaoId);
	//$cabecalho = $mail->pegarMensagemCabecalho($r_acaoId);
	//$body = $mail->pegarMensagemCorpo($r_acaoId);
	$anexos = $mail->listaAnexos($r_acaoId);
	foreach ($anexos as $key => $arquivoXML) {
		if (strpos($arquivoXML, '.xml') === false) { continue; }
		if (empty($arquivoXML)) { continue; }
		foreach ($lista as $CHAVE => $T013_Id) {
			if (empty($CHAVE)) { continue; }
			//echo "{$value} - {$CHAVE}\n";
			$CHAVE = utf8_decode($CHAVE);
			if (strpos($arquivoXML, $CHAVE) !== false) {
				if (in_array($T013_Id, $tmp)) { continue; }
				$tmp[] = $T013_Id;
				$a = mysqli_query($con,"SELECT T013_Numero_Protocolo_Nfe FROM T013 WHERE T013_Id = '{$T013_Id}'");
				$a = mysqli_fetch_assoc($a);
				$a = $a['T013_Numero_Protocolo_Nfe'];
				if (!file_exists("/home/flycker/html/hardness/dados_usuarios/dev_e229/nfe/xml/recebido/{$a}.xml")) {
					echo "{$row['T139_Id']} - {$T013_Id} - {$arquivoXML} - {$CHAVE}\n";
					$conteudo = $mail->downloadAnexo($r_acaoId, $key, true);
					if (empty($conteudo)) {
						echo "\tNao encontrado no email!\n";
					} else {
						file_put_contents("/home/flycker/html/hardness/dados_usuarios/dev_e229/nfe/xml/recebido/{$a}.xml", $conteudo);
						echo "\tRecuperado!\n";
					}

				}
			}
		}
	}
}